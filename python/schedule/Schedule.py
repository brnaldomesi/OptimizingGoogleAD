# coding: utf-8
from datetime import datetime

import pandas as pd

from api.Helpers import Helpers
from common.Database import Database
from common.Settings import Settings
from common.helpers.LocalDates import LocalDates

class Schedule(object):
    """Logic around daily and hourly scheduling"""

    def __init__(self):
        self.run_all_active_accounts = Settings().envvars["RUN_ALL_ACTIVE_ACCOUNTS"]=='true'

    def getSyncedAccountIds(self):
        """Return a list of synced account ids"""
        query = "select id from accounts where is_synced = 1"
        result = Database().createEngine().execute(query)
        account_ids = []
        for row in result:
            account_id = row[0]
            account_ids.append(account_id)
        return account_ids

    def accountShouldRun(self, account_id):
        """
        For the nightly shedule
        Whether the account should run
         - Don't run before 3am because the data may not be ready
         - Run if the account hasn't ran today already
         """
        if self.run_all_active_accounts:
            return True
        
        if LocalDates(account_id).today.hour < 3:
            return False

        if self.accountRanToday(account_id):
            return False

        return True

    def accountRanToday(self,account_id):
        """Returns True if the account ran today"""
        query = 'SELECT ad_performance_report_processed_at FROM accounts where id = "%s"' % (account_id)
        last_run_time = pd.read_sql_query(query, Database().createEngine())["ad_performance_report_processed_at"].values[0]
        if not last_run_time:
            return False
            
        return LocalDates(account_id).today.date() == datetime.utcfromtimestamp(last_run_time.tolist()/1e9).date()
