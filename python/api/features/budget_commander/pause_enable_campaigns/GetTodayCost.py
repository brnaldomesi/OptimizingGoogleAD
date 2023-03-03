#!/usr/bin/env python
from __future__ import division

from datetime import datetime, timedelta

import pandas as pd

from api.Helpers import Helpers
from common.Database import Database
from common.helpers.LocalDates import LocalDates
from common.Settings import Settings


class GetTodayCost(object):
    """
        Get cost data from the db for both today and the previous 28 days (28 because we need 4 whole weeks)
        Used in emergency stop budget calculations
    """

    def __init__(self, account_id, budget_group_id=None):
        self.account_id = account_id
        self.budget_group_id = budget_group_id
        self.local_dates = LocalDates(account_id)
        self.today_cost = self.getTodayCost()
        if self.today_cost is None:
            return
        self.day_budget_percentage = self.getBudgetPercentage()

        
    def getBudgetPercentage(self):
        """Get today's budget percentage based on today's previous spend Vs average"""
        def weekday(date):
            date = date.strftime("%Y-%m-%d")
            date = datetime.strptime(date, '%Y-%m-%d')
            return date.weekday()

        last_four_weeks_df = self.getFourWeekDf(self.local_dates.twenty_eight_days_ago.strftime("%Y-%m-%d"), self.local_dates.yesterday.strftime("%Y-%m-%d"))
        last_four_weeks_df['weekday'] = last_four_weeks_df['date'].apply(lambda date: weekday(date))

        last_four_weeks_df = last_four_weeks_df.groupby('weekday').sum()
        last_four_weeks_df['average_day'] = last_four_weeks_df['cost']/4
        last_four_weeks_df['day_percentage_of_total'] = last_four_weeks_df['average_day']/last_four_weeks_df['cost'].sum()
        
        return last_four_weeks_df[last_four_weeks_df.index==self.local_dates.today.weekday()].day_percentage_of_total.values[0]

    def getFourWeekDf(self, start_date, end_date):

        query = """
            SELECT * from campaign_performance 
            join campaigns
            on campaigns.id = campaign_performance.campaign_id
            where campaigns.account_id = '%s'
            and campaign_performance.date >= '%s'
            and campaign_performance.date <= '%s'
        """ %(self.account_id, start_date, end_date)

        if self.budget_group_id:
            query +=  " and campaigns.budget_group_id = '%s'" %(self.budget_group_id)

        return pd.read_sql(query, Database().createEngine())
    
    def getTodayCost(self):
        query = """
            SELECT sum(cost) from campaign_performance 
            join campaigns
            on campaigns.id = campaign_performance.campaign_id
            where campaigns.account_id = '%s'
            and campaign_performance.date_range = "TODAY"
        """ %(self.account_id)
        if self.budget_group_id:
            query +=  " and campaigns.budget_group_id = '%s'" %(self.budget_group_id)
        result = Database().createEngine().execute(query)
        for row in result:
            return row[0]

    def getDateRange(self):
        start_date = str((self.local_dates.today - timedelta(28)).strftime('%Y%m%d'))
        end_date= str(self.local_dates.today.strftime('%Y%m%d'))
        return '%s,%s'%(start_date, end_date)
