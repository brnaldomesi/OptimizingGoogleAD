# coding: utf-8
from __future__ import division

import calendar
import uuid
from datetime import datetime, timedelta

import pandas as pd

from api.Helpers import Helpers
from common.Database import Database
from common.helpers.Currency import Currency
from common.helpers.LocalDates import LocalDates
from common.Log import Log
from common.Settings import Settings
from common.Env import Env


class BudgetCommander(object):

    def __init__(self, account_id, budget_group_id=None):
        self.account_id = account_id
        self.budget_group_id = budget_group_id
        self.google_id = self.account_info["google_id"]
        self.is_master_budget_group = self.budget_group_id is None
        self.name = self.account_info["name"]
        self.google_id = self.account_info["google_id"]    
        self.original_budget = self.getOriginalBudget()
        self.budget = self.getBudget()
        self.this_month_spend = self.getThisMonthSpend()
        self.pause_campaigns = self.user_settings["pause_campaigns"]
        self.enable_campaigns = self.user_settings["enable_campaigns"]
        self.email_sent = self.user_settings["email_sent"]
        self.notify_via_email = self.user_settings["notify_via_email"]
        self.campaigns_paused_month = self.campaignsArePausedMonth()

    @property
    def local_dates(self):
        return LocalDates(self.account_id)

    @property
    def envvars(self):
        return (Settings()).getEnv()

    @property
    def currency_symbol(self):
        return (Currency()).getSymbol(self.account_id)

    @property
    def styled_google_id(self):
        return '%s-%s-%s' %(self.google_id[0:3], self.google_id[3:6], self.google_id[6:10])

    def getOriginalBudget(self):
        if self.is_master_budget_group:
            return self.account_info["budget"]
        
        return self.user_settings["budget"]

    def getBudget(self):
        """Get the budget including any excess from the rollover"""
        budget = self.original_budget

        if budget == "" or budget is None:
            print("No budget is set in the account. Can't run budget commander features")
            return None

        if self.user_settings["rollover_spend"] and self.user_settings["excess_budget"]:
            budget = budget + self.user_settings["excess_budget"]

        return float(budget)

    @property
    def budget_group_name(self):
        if self.is_master_budget_group:
            return None
        else:
            return self.user_settings['budget_group_name']

    @property
    def user_settings(self):
        """
        Returns a dict of the budget commander settings
        """

        query = "select * from budget_commander where account_id = '%s'" % (self.account_id)
        if self.is_master_budget_group:
            query += "and budget_group_name = 'master'"
        else:
            query += "and id = '%s'" %(self.budget_group_id)

        data = pd.read_sql(query, (Database()).createEngine()).to_dict()

        user_settings = {}
        for key in data:
            try:
                user_settings[key] = data[key][0]
            except KeyError as e:
                if self.is_master_budget_group:
                    return self.addDefaultBudgetCommanderSettings()
                else:
                    Log('error', 'Budget group not found', '', self.account_id)
                    raise

        return user_settings

    

    @property
    def username(self):
        """
        Returns a dict of the user settings
        """

        query = """
        SELECT u.name FROM users as u
        join accounts as a
        on a.user_id = u.id
        where a.id = "%s"
        """ % (self.account_id)
        result = (Database()).createEngine().execute(query)

        for row in result:
            username = row[0]

        return username


    def addDefaultBudgetCommanderSettings(self):
        """If budget commander settings aren't available add the defaults
        This will only run for the master budget group
        """

        if not self.is_master_budget_group:
            #only create the default row in the table for the master budget group
            return

        settings = {"1": {
            "id": uuid.uuid4(),
            "created_at": datetime.now(),
            "updated_at": datetime.now(),
            "account_id": self.account_id,
            "notify_via_email": 0,
            "pause_campaigns": 0,
            "enable_campaigns": 0,
            "rollover_spend": 0,
            "control_spend": 0,
            "email_sent": 0,
            "emergency_stop": 0,
            "email_addresses": None,
            "day_paused_campaigns": None,
            "month_paused_campaigns": None,
            "excess_budget": None,
            "budget_group_name": 'master',
            "budget": None,
            "kpi_name": None,
            "kpi_value": None,
        }}

        # write to the db
        Database().appendRows("budget_commander", settings)

        return settings["1"]

    def campaignsAreEnabledDay(self):
        """Returns True if campaigns are enabled self.now i.e. under the Emergency Stop feature."""
        if not self.user_settings['day_paused_campaigns']:
            return True

        return False

    def campaignsArePausedDay(self):
        """Returns True if campaigns have been paused self.now i.e. under the Emergency Stop feature."""
        if self.user_settings['day_paused_campaigns']:
            return True

        return False

    def campaignsArePausedMonth(self):
        """Returns True if campaigns have been paused this month."""
        if self.user_settings['month_paused_campaigns']:
            return True

        return False

    def getBudgetVsSpend(self, budget, this_month_spend):
        difference = this_month_spend - budget
        return difference

    def getBudgetVsSpendPercentage(self, budget, this_month_spend):
        difference = this_month_spend - budget
        percentage = difference / budget
        return percentage

    @property
    def account_info(self):
        query = "select budget, name,google_id,currency_code from accounts where id = '%s' " % (self.account_id)
        data = pd.read_sql(query, (Database()).createEngine()).to_dict()
        user_info = {}
        for key in data:
            user_info[key] = data[key][0]

        return user_info

    def getThisMonthSpend(self):
        
        if self.local_dates.today.date().day == 1:
            return 0

        if self.budget_group_id:
            query = """
            select sum(campaign_performance.cost) from campaign_performance 
            join campaigns on campaigns.id = campaign_performance.campaign_id
            where campaigns.account_id = '%s'
            and campaigns.budget_group_id = '%s'
            """ %(self.account_id, self.budget_group_id)
            query += " and campaign_performance.date >= '%s' and campaign_performance.date <= '%s' " % (self.local_dates.first_date_of_this_month, self.local_dates.yesterday)

            rows = Database().executeQuery(query)
            for row in rows:
                cost = row[0]
            if cost is None:
                return 0
            return cost

        query = "select sum(cost) from account_performance_reports where account_id = '%s' " % (self.account_id)
        query += " and date >= '%s' and date <= '%s' " % (self.local_dates.first_date_of_this_month, self.local_dates.yesterday)
        result = (Database()).createEngine().execute(query)
        for row in result:
            this_month_cost = row[0]
        
        if this_month_cost is None:
            return 0

        return this_month_cost
    
    @property
    def last_month_spend(self):
        start_date = datetime.strftime(self.local_dates.first_date_of_last_month, '%Y-%m-%d')
        end_date = datetime.strftime(self.local_dates.last_date_of_last_month, '%Y-%m-%d')
        query = "select sum(cost) from account_performance_reports where account_id = '%s' " % (self.account_id)
        query += " and date >= '%s' and date <= '%s' " % (start_date, end_date)
        result = (Database()).createEngine().execute(query)
        for row in result:
            return row[0]

    def getSevenDaySpend(self):
        """Get cost from the last 7 days"""

        if self.local_dates.is_first_of_month:
            return 0

        query = "select cost from account_performance_reports where account_id = '%s' " % (self.account_id)
        query += " and date >= '%s' and date <= '%s' " % (self.local_dates.seven_days_ago, self.local_dates.yesterday)
        result = (Database()).createEngine().execute(query)
        cost = 0
        for row in result:
            cost += row[0]

        return cost

    @property
    def under_budget(self):
        if self.budget is None:
            return True

        if self.this_month_spend is None or self.this_month_spend > self.budget:
            return False

        return True

    def getEmailAddresses(self):
        email_addresses = self.user_settings["email_addresses"]

        if email_addresses:
            email_addresses = email_addresses.strip('[]').replace('"', '').replace(' ', '').split(',')
        else:
            email_addresses = []

        query = """
        SELECT u.email FROM users as u
        join accounts as a 
        on a.user_id = u.id
        where a.id = "%s"
        """ % (self.account_id)
        result = (Database()).createEngine().execute(query)

        for row in result:
            user_email_address = row[0]
            if user_email_address != "":
                email_addresses.append(user_email_address)

        return list(set(email_addresses))  # return unique values only
