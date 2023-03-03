import os

import pandas as pd
from sqlalchemy import create_engine

from common.Database import Database
from common.Env import Env


class Settings(object):

    def __init__(self):
        self.common_dir = os.path.dirname(os.path.abspath(__file__))
        self.python_dir = os.path.abspath(os.path.join(self.common_dir, os.pardir))
        self.root_dir = os.path.abspath(os.path.join(self.python_dir, os.pardir))
        self.api_dir = os.path.abspath(os.path.join(self.python_dir, "api"))
        self.yaml_path = os.path.abspath(os.path.join(self.api_dir, "googleads.yaml"))
        self.storage_dir = os.path.abspath(os.path.join(self.api_dir, "storage"))
        self.logs_dir = os.path.abspath(os.path.join(self.storage_dir, "logs"))

        self.date_ranges = [
            "TODAY",
            "THIS_MONTH",
            "last_7_days",
            "last_14_days",
            "last_28_days",
            "last_30_days",
            "last_56_days",
            "last_112_days",
        ]

        self.metrics = ["impressions", "clicks", "conversions", "cost", "conversion_value"]
        self.calculatedFields = []
        self.calculatedMetrics = {
            # numerator, operator, denominator, is percentage (needs to be multiplied by 100)
            "ctr": ["clicks", "/", "impressions", True],
            "average_cpc": ["cost", "/", "clicks", False],
            "cpa": ["cost", "/", "conversions", False],
            "roas": ["conversion_value", "/", "cost", False],
            "conversion_rate": ["conversions", "/", "clicks", True]
        }
        self.winning_significance_threshold = 90
        self.is_batch_job_processing = True
        self.is_processing_batch_job_async = True

        # where there are no conversions, this may be used to calc things like cpa for scores, etc.
        self.temp_conversions = .2

        self.envvars = Env().vars

    # TODO: remove duplicate method from settings
    def createEngine(self):
        # 192.168.10.10
        envvars = self.envvars
        values = (envvars["DB_CONNECTION"], envvars["DB_USERNAME"], envvars["DB_PASSWORD"], envvars["DB_HOST"],
                  envvars["DB_PORT"], envvars["DB_DATABASE"],)
        conn_string = ('%s+pymysql://%s:%s@%s:%s/%s' + '?charset=utf8') % values
        # print "Connecting to database: %s" %(envvars["DB_DATABASE"],)
        return create_engine(conn_string)

    def getLogPath(self, fileName):
        if self.envvars["APP_ENV"] == "local":
            path = fileName
        else:
            # production, place in storage
            path = os.path.abspath(os.path.join(self.logs_dir, fileName))

        return path

    def isFirstRun(self,account_id):
        query = "select ad_performance_report_processed_at from accounts where id = '%s'" % (account_id)
        results = Database().executeQuery(query)
        for result in results:
            return result[0] is None

    def getAccountData(self, account_id):
        """dict of the account data (name, etc.)"""
        query = "select * from accounts where id = '%s' " % (account_id)
        data = pd.read_sql(query, (Database()).createEngine()).to_dict()
        user_info = {}
        for key in data:
            user_info[key] = data[key][0]

        return user_info

    def getUserData(self, account_id):
        """
        Returns the account's username
        """

        query = """
        SELECT u.* FROM users as u
        join accounts as a
        on a.user_id = u.id
        where a.id = "%s"
        """ % (account_id)
        data = pd.read_sql(query, (Database()).createEngine()).to_dict()
        user_info = {}
        for key in data:
            user_info[key] = data[key][0]

        return user_info

    @classmethod
    def api_version(cls):
        return 'v201809'

# s = Settings()
# print s.data
