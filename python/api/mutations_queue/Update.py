# coding: utf-8
# !/usr/bin/env python
from __future__ import division

import time
import random
try:
    from urllib.request import urlopen
except ImportError:
    from urllib2 import urlopen

from api.Helpers import Helpers

from common.Log import Log
from common.Settings import Settings
from common.GoogleAdsApi import GoogleAdsApi

MAX_POLL_ATTEMPTS = 5
PENDING_STATUSES = ('ACTIVE', 'AWAITING_FILE', 'CANCELING')


class Update:

    def __init__(self, account_id, service_type, operations=None):
        self.settings = Settings()
        self.helpers = Helpers()
        self.debug_mode = self.settings.envvars["APP_DEBUG"] == "true"
        self.account_id = account_id
        self.service_type = service_type
        self.operations = operations
        self.refresh_token = self.helpers.getRefreshToken(account_id)
        self.client_customer_id = self.helpers.getClientCustomerID(self.settings, account_id)

    def getClient(self):
        return GoogleAdsApi.getClient(None, self.account_id, self.client_customer_id)

    def getService(self):
        client = self.getClient()
        service = client.GetService(self.service_type, version=Settings.api_version())

        return service

    def send_request(self):
        if self.debug_mode:
            Log(
                "debug",
                "Mutations will not run in debug mode.",
                "There are {0} attempted operations".format(len(self.operations))
            )

            return "debug mode"  # IMPORTANT: needs to be this exact text

        if self.settings.is_batch_job_processing:
            if self.settings.is_processing_batch_job_async:
                return self.send_batch_job_request_async()
            else:
                return self.send_batch_job_request_sync()

        return self.send_mutation_request()

    def check_batch_job_result(self, batch_job_id):
        download_url = self.check_batch_job(batch_job_id)

        response = None
        if download_url:
            batch_job_helper = self.getClient().GetBatchJobHelper(version=Settings.api_version())
            response = batch_job_helper.ParseResponse(urlopen(download_url).read())

        return response

    def send_mutation_request(self):
        criteria = self.getService().mutate(self.operations)

        return criteria

    def send_batch_job_request_sync(self):
        batch_job_helper = self.getClient().GetBatchJobHelper(version=Settings.api_version())
        batch_job = self.add_batch_job()

        print('Created BatchJob with ID "{0}", status "{1}"'.format(
            batch_job['id'], batch_job['status']
        ))

        upload_url = batch_job['uploadUrl']['url']
        batch_job_helper.UploadOperations(
            upload_url, self.operations,
        )

        download_url = self.get_batch_job_download_url_when_ready(batch_job['id'])
        response = batch_job_helper.ParseResponse(urlopen(download_url).read())

        self.helpers.print_batch_job_response(response)

        return response

    def send_batch_job_request_async(self):
        batch_job_helper = self.getClient().GetBatchJobHelper(version=Settings.api_version())
        batch_job = self.add_batch_job()

        print('Created BatchJob with ID "{0}", status "{1}"'.format(
            batch_job['id'], batch_job['status']
        ))

        upload_url = batch_job['uploadUrl']['url']
        batch_job_helper.UploadOperations(
            upload_url, self.operations,
        )

        return {'batch_job_id': batch_job['id'], 'batch_job_status': batch_job['status']}

    def add_batch_job(self):
        """ Add a new BatchJob to upload operations to.

        :return: The new BatchJob created by the request.
        """
        # Initialize appropriate service.
        client = self.getClient()
        batch_job_service = client.GetService('BatchJobService', version=Settings.api_version())

        # Create a BatchJob.
        batch_job_operations = [{
            'operand': {},
            'operator': 'ADD'
        }]

        return batch_job_service.mutate(batch_job_operations)['value'][0]

    def get_batch_job(self, client, batch_job_id):
        """ Retrieves the BatchJob with the given id.

        :param client: an instantiated AdWordsClient used to retrieve the BatchJob.
        :param batch_job_id: a long identifying the BatchJob to be retrieved.
        :return: The BatchJob associated with the given id.
        """
        batch_job_service = client.GetService('BatchJobService', Settings.api_version())

        selector = {
            'fields': ['Id', 'Status', 'DownloadUrl'],
            'predicates': [
                {
                    'field': 'Id',
                    'operator': 'EQUALS',
                    'values': [batch_job_id]
                }
            ]
        }

        return batch_job_service.get(selector)['entries'][0]

    def check_batch_job(self, batch_job_id):
        batch_job = self.get_batch_job(self.getClient(), batch_job_id)

        if batch_job['status'] in PENDING_STATUSES:
            return None

        elif 'downloadUrl' in batch_job and batch_job['downloadUrl'] is not None:
            return batch_job['downloadUrl']['url']

    def get_batch_job_download_url_when_ready(self, batch_job_id, max_poll_attempts=MAX_POLL_ATTEMPTS):
        """ Retrieves the downloadUrl when the BatchJob is complete.

        :param batch_job_id: a long identifying the BatchJob to be polled.
        :param max_poll_attempts: an int defining the number of times the BatchJob will be
            checked to determine whether it has completed.
        :return: A str containing the downloadUrl of the completed BatchJob.
        :rtype: str
        :raise: Exception: If the BatchJob hasn't finished after the maximum poll attempts
            have been made.
        """
        batch_job = self.get_batch_job(self.getClient(), batch_job_id)
        poll_attempt = 0

        while poll_attempt in range(max_poll_attempts) and batch_job['status'] in PENDING_STATUSES:

            sleep_interval = (30 * (2 ** poll_attempt) + (random.randint(0, 10000) / 1000))
            print('Batch Job not ready, sleeping for %s seconds.' % sleep_interval)
            time.sleep(sleep_interval)

            batch_job = self.get_batch_job(self.getClient(), batch_job_id)
            poll_attempt += 1

            if 'downloadUrl' in batch_job and batch_job['downloadUrl'] is not None:
                url = batch_job['downloadUrl']['url']
                print('Batch Job with Id "{0}", Status "{1}" ready.'.format(
                    batch_job['id'], batch_job['status']
                ))

                return url

        raise Exception('Batch Job not finished downloading. Try checking later.')
