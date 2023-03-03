# coding: utf-8
import uuid

import pandas as pd

from api.Helpers import Helpers
from api.mutations_queue.Update import Update
from api.mutations_queue_workers.Operation import Operation
from common.Database import Database
from common.Settings import Settings
settings = Settings()


class Worker(object):
    """Read from the mutations queue and make API requests
    Designed with multi-processing in mind."""

    def __init__(self):
        self.services_map = {
            "campaign": "CampaignService",
            "keyword": "AdGroupCriterionService",
            "advert": "AdGroupAdService"
        }
        self.helpers = Helpers()

    def work(self):
        for service_type in self.services_map:
            self.process_service_type(service_type, "set", "bid")
            self.process_service_type(service_type, "set", "status")
            self.process_service_type(service_type, "add")

    def process_service_type(self, service_type, action, attribute=None):
        """ Grab rows from the queue filtered by service type, action and attribute
        Process the rows (apply mutations to the API)
        Run each chunk once. The next process will pick up the next chunk.
        """
        # Query data from mutations table
        query = self.mutations_query(service_type, action, attribute)
        mutations_data_frame_chunks = pd.read_sql(query, Database().createEngine(), chunksize=2000)

        # Iterate over chunks
        while True:
            try:
                mutations_data_frame = next(mutations_data_frame_chunks)
            except StopIteration:
                return

            # Mark the chunk data as is_processing equal to True
            self.set_data_frame_to_processing(mutations_data_frame)

            account_ids = mutations_data_frame.account_id.drop_duplicates().values

            for account_id in account_ids:
                account_data_frame = mutations_data_frame[mutations_data_frame.account_id == account_id].drop_duplicates()
                # Send mutations to google ads API
                result = self.process_account_mutations(account_data_frame, service_type)

                # Write data to DB
                self.handle_mutations_result(result, account_data_frame)

    def check(self):
        """
        1. Get batch jobs with status != Done
        2. Check status of batch job
        3. If status = Done, Get mutations with batch job id = current batch job id (order by created)
        4. Update mutations with results
        """
        batch_jobs_query = self.batch_jobs_query()
        batch_jobs_chunks = pd.read_sql(batch_jobs_query, Database().createEngine(), chunksize=10)

        # Iterate over chunks
        try:
            batch_jobs_data_frame = next(batch_jobs_chunks)
        except StopIteration:
            return

        # Mark the batch jobs data as is_checking equal to True
        self.set_batch_jobs_to_processing(batch_jobs_data_frame)

        for index, batch_job_data_frame in batch_jobs_data_frame.iterrows():
            response = Update(
                batch_job_data_frame['account_id'], ''
            ).check_batch_job_result(batch_job_data_frame['google_id'])

            if response:
                mutations_query = self.mutations_query_by_batch_job(batch_job_data_frame['id'])
                mutations_data_frame_chunks = pd.read_sql(mutations_query, Database().createEngine(), chunksize=2000)

                # Iterate over chunks
                try:
                    mutations_data_frame = next(mutations_data_frame_chunks)
                except StopIteration:
                    continue

                mutations_data_frame["is_processing"] = 0
                self.update_batch_job_mutations(mutations_data_frame, response)

                # TODO: support all status for batch job
                batch_jobs_data_frame.at[index, "status"] = 'DONE'

        batch_jobs_data_frame["is_checking"] = 0
        self.update_batch_jobs(batch_jobs_data_frame)

    def set_data_frame_to_processing(self, mutations_data_frame):
        """ Set is_processing to true so that other processes won't work on the same rows
        """
        mutations_data_frame["is_processing"] = 1
        self.update_mutations(mutations_data_frame, "", 0)

    def set_batch_jobs_to_processing(self, batch_jobs_data_frame):
        """ Set is_processing to true so that other processes won't work on the same rows
        """
        batch_jobs_data_frame["is_checking"] = 1
        self.update_batch_jobs(batch_jobs_data_frame)

    def process_account_mutations(self, account_data_frame, service_type):
        """ Process mutations for up to 2000 rows. One account_id and one type at a time.
        """
        google_service_type = self.services_map[service_type]
        account_id = account_data_frame.account_id.values[0]

        operations = []
        for index, row in account_data_frame.iterrows():
            operation = Operation(
                row, service_type, row["action"], row["attribute"]
            ).get()
            operations.append(operation)

        try:
            print("attempting {0} mutation...".format(google_service_type))
            result = Update(account_id, google_service_type, operations).send_request()
        except Exception as exception:
            print("An exception has occurred: {0}".format(exception))
            return [False, str(exception)]

        return True, result

    def handle_mutations_result(self, result, account_data_frame):
        account_data_frame["is_processing"] = 0

        if result[1] == "debug mode":
            self.update_mutations(account_data_frame, result[1], 1)
            return

        if result[0] is False:  # handle error
            msg = '{0}'.format(result[1])
            is_success = 0
            self.update_mutations(account_data_frame, msg, is_success)
            return

        if settings.is_batch_job_processing:
            if result[0] is True:
                if settings.is_processing_batch_job_async:
                    batch_job_uuid_string = self.insert_batch_job(
                        result[1]['batch_job_status'], result[1]['batch_job_id']
                    )

                    account_data_frame["is_processing"] = 1
                    account_data_frame["batch_job_id"] = batch_job_uuid_string

                    self.update_mutations(account_data_frame, '', False)
                else:
                    self.update_batch_job_mutations(account_data_frame, result[1])

        else:
            if result[0] is True:
                message = ''
                is_success = 1
                self.update_mutations(account_data_frame, message, is_success)
                return

            if result[1]["partialFailureErrors"]:
                message = '{0}'.format(result["partialFailureErrors"])
                is_success = 0
                self.update_mutations(account_data_frame, message, is_success)
                return

    def mutations_query(self, service_type, action, attribute=None):
        """ Return a query from request data from DB

        :param service_type:
        :param action:
        :param attribute:
        :return:
        """
        query = """
            select * from mutations
            where (is_success != 1 or isnull(is_success))
            and (is_processing != 1 or isnull(is_processing))
            and type = "{0}"
            and action = "{1}"
        """.format(
            service_type, action
        )

        if attribute:
            query += " and attribute = '{0}'".format(attribute)

        query += " order by account_id, created_at"

        return query

    def mutations_query_by_batch_job(self, batch_job_id):
        query = """
            select * from mutations
            where (is_success != 1 or isnull(is_success))
            and batch_job_id = '{0}'
        """.format(
            batch_job_id
        )

        query += " order by account_id, created_at"

        return query

    def batch_jobs_query(self):
        query = """
            SELECT DISTINCT(batch_job.id), batch_job.is_checking, batch_job.google_id,
            status, mut.account_id
            FROM batch_job INNER JOIN mutations as mut on batch_job.id = mut.batch_job_id
            WHERE batch_job.status != 'DONE' and batch_job.is_checking is False;
        """

        return query

    def insert_batch_job(self, batch_job_status, batch_job_google_id):
        # Create batch job
        uuid_string = uuid.uuid1().__str__()
        query = "INSERT INTO batch_job (`id`, `status`, `google_id`) VALUES ('{0}','{1}', '{2}')".format(
            uuid_string, batch_job_status, batch_job_google_id
        )
        Database().createEngine().execute(query)

        return uuid_string

    def update_batch_jobs(self, batch_jobs_data_frame):
        for index, row in batch_jobs_data_frame.iterrows():
            query = "update batch_job set is_checking={0}, status='{1}' where id='{2}'".format(
                row["is_checking"], row["status"], row["id"]
            )

            Database().executeQuery(query)

    def update_mutations(self, account_data_frame, message, is_success):
        for i, row in account_data_frame.iterrows():
            if row['batch_job_id'] is None:
                row['batch_job_id'] = 'null'
            else:
                row['batch_job_id'] = "'%s'" %(row['batch_job_id'])
            query = "update mutations set response = '{0}', executed_at=now(), is_success={1}, is_processing={2}, batch_job_id={3} where id = '{4}'".format(
                message, is_success, row["is_processing"],row['batch_job_id'] , row["id"],
            )

            Database().executeQuery(query)

    def update_batch_job_mutations(self, account_data_frame, response):
        batch_job_response = Helpers.normalize_batch_job_response(response)

        if batch_job_response:
            is_success = False
            message = ''
            query_string = "update mutations set response = '{0}', executed_at = now(), is_success = {1}, is_processing={2}  where id = '{3}'"

            for data in batch_job_response:
                index = int(data['index'])

                if 'result' in data:
                    is_success = True
                    message = ''
                elif 'errorList' in data:
                    is_success = False
                    message = self.helpers.errors_to_message(data['errorList'])

                # Query to DB
                query = query_string.format(
                    message, is_success, account_data_frame.iloc[index, :]['is_processing'],
                    account_data_frame.iloc[index, :]['id'],
                )
                Database().executeQuery(query)
