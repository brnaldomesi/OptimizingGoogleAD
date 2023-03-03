# coding: utf-8
from datetime import datetime, timedelta

import calendar
import numpy as np
import pandas as pd
import pytz
import yaml

from common.Database import Database
from common.Settings import Settings


class LocalDates(object):
    """Dates in the account's local timezone"""

    def __init__(self, account_id):
        self.account_id = account_id
        self.setDates()

    def getTimezone(self):
        """Returns an account's tzinfo timezone"""
        query = 'SELECT timezone FROM accounts where id = "%s"' % (self.account_id)
        timezone = pd.read_sql_query(query, Database().createEngine())["timezone"].values[0]
        return pytz.timezone(timezone)

    def getLocalTime(self):
        """Returns the local datetime for a specified account"""
        tz = self.getTimezone()
        local_time = datetime.now(tz)
        return local_time

    def toString(self, time):
        return datetime.strftime(time, "%Y-%m-%d %H:%M:%S")

    def setDates(self):
        """Set local account times to variables"""
        self.today = self.getLocalTime()
        self.yesterday = self.today - timedelta(days=1)
        self.first_date_of_this_month = self.today.replace(day=1).date()

        self.last_date_of_last_month = self.today.replace(day=1) - timedelta(days=1)
        self.first_date_of_last_month = self.last_date_of_last_month.replace(day=1)

        self.seven_days_ago = self.yesterday - timedelta(days=6)
        self.twenty_eight_days_ago = self.yesterday - timedelta(days=27)

        self.is_first_of_month = self.today.date().day == 1

        self.days_in_this_month = calendar.mdays[self.today.month]

        self.days_remaining_in_this_month =  (self.days_in_this_month - self.today.day) + 1  # +1 to include today






