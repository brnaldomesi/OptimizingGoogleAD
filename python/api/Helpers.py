# coding: utf-8
import numpy as np
import pandas as pd
import pytz
import yaml

from common.Database import Database
from common.Settings import Settings


class Helpers(object):

    def __init__(self):
        pass

    def getClientCustomerID(self, settings, account_id):
        query = "select google_id from accounts where id = '%s'" % (account_id)
        result = Database().createEngine().execute(query)
        for row in result:
            return row[0]

    def getYamlData(self):
        """Read in yaml data from the googleads.yaml file"""

        settings = Settings()
        with open(settings.yaml_path, 'r') as stream:
            try:
                yaml_data = (yaml.safe_load(stream))
            except yaml.YAMLError as exc:
                print(exc)

        return yaml_data


    def getAccountNameFromId(self, account_id):
        results = Database().executeQuery('select name from accounts where id = "%s"' %(account_id))
        for result in results:
            return result[0]

    def getAccountGoogleIdFromId(self, account_id):
        results = Database().executeQuery('select google_id from accounts where id = "%s"' %(account_id))
        for result in results:
            return result[0]

    def getUserIdFromAccountId(self, account_id):
        query = "select user_id from accounts where id = '%s'" % (account_id)

        result = Database().executeQuery(query)

        for row in result:
            # print row
            return row[0]

    def getRefreshToken(self, account_id):
        
        user_id = self.getUserIdFromAccountId(account_id)

        if user_id is None:
            raise Exception("no user_id found under account_id '%s'" % (account_id))

        query = "select refresh_token from users where id = '%s'" % (user_id)

        result = Database().executeQuery(query)
        for row in result:
            refresh_token = row[0]
            break
        return refresh_token

    def getUserEmail(self, account_id):
        query = """
        SELECT u.email FROM users as u
        join accounts as a 
        on a.user_id = u.id
        where a.id = "%s"
        """ % (account_id)
        result = (Database()).createEngine().execute(query)

        for row in result:
            return row[0]

    def getUsername(self, account_id):
        query = """
        SELECT u.name FROM users as u
        join accounts as a 
        on a.user_id = u.id
        where a.id = "%s"
        """ % (account_id)
        result = (Database()).createEngine().execute(query)

        for row in result:
            return row[0]

    def isActiveAccount(self,account_id):
        """Returns true if the account exists, is_synced is true and the user has a refresh token"""
        query = """
        SELECT accounts.id FROM accounts
        join users
        on users.id = accounts.user_id
        where accounts.is_synced = 1
        and users.refresh_token is not null
        and accounts.id = '%s'
        """ %(account_id)
        results = Database().executeQuery(query)
        if(results.rowcount>0):
            return True
        return False

    def isActiveUser(self,user_id):
        """Returns true if user exists and has a refresh token"""
        query = """
        SELECT id from users 
        where refresh_token is not null 
        and id = '%s'
        """ %(user_id)
        results = Database().executeQuery(query)
        if(results.rowcount>0):
            return True
        return False

    def getRefreshTokenFromUserId(self, user_id):

        query = "select refresh_token from users where id = '%s'" % (user_id)

        result = Database().executeQuery(query)
        for row in result:
            if row[0] is None:
                return
            return row[0]

    # append dataframe to a table. The table will be created if it doesn't exist
    # replacing inf with 0 to avoid this error explained here: https://github.com/PyMySQL/mysqlclient-python/issues/246
    def append_df_to_sql_table(self, df, table_name, engine):
        df = df.replace([np.inf, -np.inf], 0)
        df.to_sql(table_name, engine, if_exists='append', index=False)

    def errors_to_message(self, error_list):
        """ transform errors object to one string message to save to Database

        :param error_list: errors return by Batch Job Service when mutation failed
        :return: error message
        :rtype: str
        """
        errors = error_list['errors']
        if not type(error_list['errors']) == list:
            errors = [error_list['errors']]

        # Only return the first error in list
        for error in errors:
            error_message = 'Error type: {0}, trigger: {1}, error string: {2}, field path: {3} and reason: {4}'.format(
                error['ApiError.Type'],
                error['trigger'],
                error['errorString'],
                error['fieldPath'],
                error['reason'],
            )

            return error_message

    @classmethod
    def normalize_batch_job_response(cls, response):
        if 'rval' in response['mutateResponse']:
            if type(response['mutateResponse']['rval']) == list:
                batch_job_response = response['mutateResponse']['rval']
            else:
                batch_job_response = [response['mutateResponse']['rval']]

            return batch_job_response

        return None

    def print_batch_job_response(self, response):
        """ Prints the BatchJobService response.

        :param response: a string containing a response from the BatchJobService.
        :return:
        """
        if 'rval' in response['mutateResponse']:
            job_response = response['mutateResponse']['rval']
            if not type(response['mutateResponse']['rval']) == list:
                job_response = [response['mutateResponse']['rval']]

            for data in job_response:
                if 'errorList' in data:
                    for error in data['errorList']['errors']:
                        print('Operation {0} - FAILURE:'.format(data['index']))
                        print('\terrorType={0}'.format(error['ApiError.Type']))
                        print('\ttrigger={0}'.format(error['trigger']))
                        print('\terrorString={0}'.format(error['errorString']))
                        print('\tfieldPath={0}'.format(error['fieldPath']))
                        print('\treason={0}'.format(error['reason']))

                if 'result' in data:
                    print('Operation {0} - SUCCESS.'.format(data['index']))
