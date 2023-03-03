#!/usr/bin/env python
import traceback
import uuid
from datetime import datetime

import numpy as np
import pandas as pd

from api.Helpers import Helpers
from common.Database import Database
from common.GoogleAdsApi import GoogleAdsApi
from common.Log import Log
from common.Settings import Settings
import common.functions as functions


class AccountMetaData:

    def __init__(self):
        self.settings = Settings()
        self.user_id = None

    def addNew(self):
        """Add accounts for new users - users without any accounts"""
        first_run = True
        self.user_ids = self.getUserIdsToProcess(first_run)
        if not self.user_ids:
            Log('info', 'no new users to process')
        for user_id in self.user_ids:
            self.addUserAccounts(user_id, first_run)

    def addAll(self):
        """Add accounts for all users - to run daily. Used for getting new accounts"""
        first_run = False
        self.user_ids = self.getUserIdsToProcess(first_run)
        for user_id in self.user_ids:
            self.addUserAccounts(user_id, first_run)

        Log("info", "finished adding account meta data")


    def addUserAccounts(self, user_id, first_run):

        if not Helpers().isActiveUser(user_id):
            Log("info", "this user isn't active. Exiting", 'user_id: %s'%(user_id))
            return

        try:

            Log("info", "adding accounts for user id '%s'" % user_id)
            self.user_id = user_id
            accounts_df = self.getAccountsDf()
            if functions.dfIsEmpty(accounts_df):
                return
            accounts_df = accounts_df.drop_duplicates('google_id')
            accounts_df = self.dropDuplicates(accounts_df, first_run)
            if(accounts_df.shape[0]==0 and first_run):
                Log('warning', "no unique google accounts were found for this user",  "user_id (%s)" %(user_id), "")
            accounts_df.to_sql("accounts", Database().createEngine(), index=False, if_exists="append")
        except Exception as exception:
            Log("error", str(exception) + " (User id: %s)" % (user_id), traceback.format_exc())

        Log("info", "finished adding account meta data")

    def dropDuplicates(self, accounts_df, first_run):
        original_size = accounts_df.shape[0]
        google_ids = accounts_df.google_id.values
        google_ids = [str(google_id) for google_id in google_ids]
        existing_google_ids = Database().getValues('accounts', 'google_id', "where google_id in (%s) " %(','.join(google_ids)))
        existing_google_ids = [int(existing_google_id[0]) for existing_google_id in existing_google_ids]
        accounts_df['is_duplicate'] = accounts_df.google_id.isin(existing_google_ids)
        accounts_df = accounts_df[~accounts_df.is_duplicate]

        if original_size > accounts_df.shape[0] and first_run:
            Log('warning','the user attempted to add duplicate accounts but they will not be added', "user_id (%s)" %(self.user_id), "")

        del accounts_df['is_duplicate']

        return accounts_df

    def getUserIdsToProcess(self, first_run):
        """Get user_ids which need accounts based on whether accounts already exist.
            If first_run is false return all users.
        """
        user_ids = Database().getValues("users", "id")
        if not first_run:
            return [user_id[0] for user_id in user_ids]

        user_ids_to_process = []
        for user_id in user_ids:

            query = """
            SELECT users.id FROM users
            join accounts on accounts.user_id = users.id
            where users.id = '%s'
            """ % (user_id[0])

            result = Database().executeQuery(query)

            has_accounts = False
            for row in result:
                has_accounts = True
                break

            if has_accounts is False:
                user_ids_to_process.append(user_id[0])

        return user_ids_to_process

    def removeAccessToken(self):
        query = """
        UPDATE users SET refresh_token = NULL WHERE (id = '%s');
        """ %(self.user_id)
        Database().executeQuery(query)

    def getAccountsDf(self):

        self.report_column_map = {
            "customerId": "google_id",
            "currencyCode": "currency_code",
            "dateTimeZone": "timezone",
            "descriptiveName": "name",
            "canManageClients": "can_manage_clients",
            "testAccount": "is_test_account",
        }

        def getCustomers():
            assert self.user_id is not None

            manager_client = GoogleAdsApi.getClient(self.user_id, None, None)

            if not manager_client:
                return

            customer_service = manager_client.GetService(
                'CustomerService', version="v201809",
            )

            try:
                customer_data = customer_service.getCustomers()
            except Exception as exception:
                if str(exception).find("Token has been expired or revoked") > -1:
                    Log('warning', "user %s has revoked access. They'll need to re-authorise next time they login" %(self.user_id), '', '')
                    self.removeAccessToken()
                    return
                elif str(exception).find("CUSTOMER_NOT_FOUND") > -1:
                        return
                else:
                    raise

            if not customer_data:
                return

            accounts_data = {}
            for customer in customer_data:
                accounts_data[customer["customerId"]] = {}
                for api_column in self.report_column_map:
                    accounts_data[customer["customerId"]][self.report_column_map[api_column]] = customer[api_column]

            return accounts_data

        
        def addChildAccounts(accounts_data):
            for account in accounts_data:
                accounts_data[account]["children"] = []
                if accounts_data[account]["can_manage_clients"] is True:
                    accounts_data[account]["children"].append(
                        self.getManagedCustomer(accounts_data[account]["google_id"]))

            return accounts_data

        accounts_data = getCustomers()
        if not accounts_data:
            return

        accounts_data = addChildAccounts(accounts_data)

        # we now have the initial, raw data
        # next, convert to a dataframe

        return self.dataToDataframe(accounts_data)

    def getManagedCustomer(self, google_id, is_mcc=False):
        identifications = None
        client = GoogleAdsApi.getClient(self.user_id, None, google_id)
        managed_customer_service = client.GetService(
            'ManagedCustomerService', version="v201809",
        )

        selector = {
            'fields': [
                'CustomerId', 'Name', 'DateTimeZone', 'CurrencyCode', 'CanManageClients'
            ],
            'predicates': [
                {
                    'field': 'CanManageClients',
                    'operator': 'EQUALS',
                    'values': [is_mcc]
                },
            ],
        }

        if identifications:
            selector.update({
                'predicates': [{
                    'field': 'CustomerId',
                    'operator': 'IN',
                    'values': identifications
                }]
            })

        accounts_from_api = managed_customer_service.get(selector)

        if 'entries' in accounts_from_api:
            return accounts_from_api.entries
        else:
            return accounts_from_api

    def dataToDataframe(self, accounts_data):
        # build the parent accounts up first then loop through them if they have children
        columns = ["is_test_account", "name", "google_id", "can_manage_clients", "timezone", "currency_code",
                   "parent_google_id"]
        parent_accounts = {}
        for parent_account_id in accounts_data:
            parent_account = accounts_data[parent_account_id]
            account_id = str(uuid.uuid4())

            parent_accounts[account_id] = {}
            for column in columns:
                try:
                    parent_accounts[account_id][column] = parent_account[column]
                except KeyError:
                    continue

        def addChildAccounts(parent_account_google_id, child_accounts, child_account_data):
            for child_account in child_account_data[0]:
                account_id = str(uuid.uuid4())
                child_accounts[account_id] = {}
                child_accounts[account_id]["parent_google_id"] = parent_account_google_id
                #         print child_account

                # turn the managed customer into a dict
                this_child_account_data = {}
                for column in child_account:
                    this_child_account_data[column] = child_account[column]

                for column in self.report_column_map:
                    db_column = self.report_column_map[column]
                    try:
                        child_accounts[account_id][db_column] = this_child_account_data[column]
                    except KeyError:
                        if column == "descriptiveName":
                            child_accounts[account_id]["name"] = this_child_account_data["name"]
                        else:
                            print("Can't find %s in %s" % (column, ",".join(this_child_account_data.keys()),))

            return child_accounts

        child_accounts = {}
        for parent_account_id in parent_accounts:
            account_id = str(uuid.uuid4())
            child_accounts[account_id] = {}
            if parent_accounts[parent_account_id]["can_manage_clients"] is False:
                continue
            #     print accounts_data[parent_account_google_id]["name"]
            parent_account_google_id = parent_accounts[parent_account_id]["google_id"]
            #     print parent_account_google_id
            child_account_data = accounts_data[parent_account_google_id]['children']
            #     print child_account_data
            child_accounts = addChildAccounts(parent_account_google_id, child_accounts, child_account_data)

        accounts_df = pd.DataFrame(parent_accounts).T.append(pd.DataFrame(child_accounts).T)
        accounts_df = accounts_df.reset_index()
        accounts_df = accounts_df.rename(columns={"index": "id"})
        # where the name can't be determined set the name as the google account id
        accounts_df["name"] = accounts_df["name"].fillna("none")
        accounts_df["name"] = np.where(accounts_df['name'] == "none", accounts_df["google_id"], accounts_df["name"])
        accounts_df["user_id"] = self.user_id
        accounts_df["created_at"] = datetime.now()
        accounts_df["updated_at"] = datetime.now()
        accounts_df["is_synced"] = 0
        accounts_df["is_active"] = 0
        accounts_df["kpi_name"] = 'cpa'

        accounts_df = accounts_df[~accounts_df["google_id"].isna()]

        return accounts_df
