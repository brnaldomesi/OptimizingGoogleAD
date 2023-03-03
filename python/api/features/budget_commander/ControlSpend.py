# coding: utf-8
from __future__ import division

import calendar
import uuid
from datetime import datetime

import pandas as pd

import common.functions as functions
from api.features.budget_commander.BudgetCommander import BudgetCommander
from common.Database import Database
from common.Log import Log
from common.Settings import Settings
from common.helpers.LocalDates import LocalDates
from common.Env import Env

class ControlSpend:
    """If an account is forecasting over budget reduce bids
    Note if pause_campaigns is enabled and the account is over budget don't update bids"""

    def __init__(self, account_id):
        self.account_id = account_id
        self.local_dates = LocalDates(account_id)
        self.envvars = Env().vars
        self.budget_commander = BudgetCommander(account_id)
        self.user_settings = self.budget_commander.user_settings

    def main(self):
        print("running spend controller")

        self.revertToOriginalBids()

        if self.user_settings["control_spend"]:
            return self.controlSpend()

    def controlSpend(self):
        self.storeOriginalBids()
        self.getTotalSpendForecast()

        Log('info',"Forecast: %s" % (self.total_spend_forecast,),'',self.account_id)
        Log('info',"Spend Vs limit: %s" % (self.getForecastOverBudgetPercentage() * 100),'',self.account_id)

        if self.forecastIsOverLimit() and self.spendIsUnderBudget():
            Log('info',"Reducing bids...", '', self.account_id)
            df = self.reduceBids()
            self.updateBids(df)  # push to mutations queue
            self.updateKeywordsTable(df)  # reflect the new bids in the keywords table
            return df

    def storeOriginalBids(self):
        """Store original bids so we can revert back tomorrow"""
        query = "UPDATE keywords SET original_cpc_bid = cpc_bid"
        Database().createEngine().execute(query)

    def revertToOriginalBids(self):
        """Before the update bids we'll store the originals
        Change back to the original bids before we re-update them (if we do)
        """
        if not self.user_settings["control_spend"]:
            return

        df = self.getKeywordsOriginalBidsDataframe()
        df["new_bid"] = df["original_cpc_bid"]
        Log("info", "Reverting back %s bids" % (df.shape[0]), "", self.account_id)
        self.updateBids(df)

        keywords_query = "select original_cpc_bid, id from keywords where original_cpc_bid > cpc_bid and account_id = '%s'" % (
            self.account_id)
        engine = Database().createEngine()
        df = pd.read_sql(keywords_query, engine)
        for i, row in df.iterrows():
            update_query = "update keywords set cpc_bid = %s where id = '%s'" % (row["original_cpc_bid"], row["id"])
            engine.execute(update_query)

    def getTotalSpendForecast(self):
        seven_day_spend = self.budget_commander.getSevenDaySpend()
        self.this_month_spend = self.budget_commander.getThisMonthSpend()
        
        self.total_spend_forecast = float((self.this_month_spend) + ((seven_day_spend) / 7) * self.local_dates.days_remaining_in_this_month)

    def getForecastOverBudgetPercentage(self):
        self.remaining_budget = (self.budget_commander.budget / self.local_dates.days_in_this_month) * self.local_dates.days_remaining_in_this_month
        return (self.total_spend_forecast - self.budget_commander.budget) / self.budget_commander.budget

    def forecastIsOverLimit(self):
        if self.getForecastOverBudgetPercentage() * 100 > 5:
            return True
        return False

    def spendIsUnderBudget(self):
        """Is the current total spend for the month under the budget"""
        if self.this_month_spend < self.budget_commander.budget:
            return True
        return False

    def reduceBids(self):
        """New bid decision making
        Returns a df with the new bids"""

        df = self.getKeywordsDataframe()
        if functions.dfIsEmpty(df):
            Log('info', "no keywords found. Can't change bids", '', self.account_id)
            return

        remaining_spend_forecast = self.total_spend_forecast - float(self.this_month_spend)
        spend_vs_remaining_budget_percentage = self.remaining_budget / remaining_spend_forecast

        def updateBid(cpc_bid, reduction_percentage, min_bid=0.1):
            """Df lambda function
            Reduce bid by percentage.
            Accepts reduction_percentage as whole number e.g. 98.
            Checks a min bid limit (optional)
            """
            try:
                cpc_bid = float(cpc_bid)
                cpc_bid = cpc_bid * ((100 - reduction_percentage) / 100)
                if cpc_bid < min_bid:
                    cpc_bid = min_bid
                return cpc_bid
            except ValueError:
                return cpc_bid

        def updateForecast(row):
            """Df lambda function"""
            try:
                reduction = row["new_bid"] / row["cpc_bid"]
            except TypeError:
                return float(row["cpc_bid"])
            return float(((row["cost"] * reduction) / 7) * self.local_dates.days_remaining_in_this_month)

        start_reduction = 10 - int(spend_vs_remaining_budget_percentage * 10)
        for i in range(start_reduction, 10):
            reduction_percentage = i * 5
            # print reduction_percentage
            df["new_bid"] = df["cpc_bid"].apply(lambda cpc_bid: updateBid(cpc_bid, reduction_percentage))
            df["forecast"] = df[["cpc_bid", "new_bid", "cost"]].apply(lambda row: updateForecast(row), axis=1)
            # print "Forecast: %s" %(df.forecast.sum())
            if df.forecast.sum() <= self.remaining_budget:
                break
        return df

    def getKeywordsOriginalBidsDataframe(self):
        query = """
        SELECT keywords.id as entity_id,adgroups.google_id as adgroup_google_id, keywords.google_id, keywords.keyword_text,
        keywords.keyword_match_type, keywords.cpc_bid, keywords.original_cpc_bid
        FROM keywords
        join adgroups on adgroups.id = keywords.adgroup_id
        where keywords.account_id = "%s"
        and keywords.google_id != "3000006"
        and keywords.google_id != "3000000"
        and keywords.bidding_strategy_type = "cpc" 
        and keywords.cpc_bid != keywords.original_cpc_bid
        """ % (self.account_id)
        df = pd.read_sql(query, (Database()).createEngine())
        df.cpc_bid = df.cpc_bid.astype("str")
        df.cpc_bid = df.cpc_bid.str.replace("--", "0")
        df.cpc_bid = df.cpc_bid.astype("float")
        return df

    def getKeywordsDataframe(self):
        query = """
        SELECT keywords.id as entity_id,adgroups.google_id as adgroup_google_id, keywords.google_id,keyword_performance.clicks, keyword_performance.conversions,keyword_performance.search_impression_share,
        keyword_performance.cost,keyword_performance.conversion_value,keywords.keyword_text
        ,keywords.keyword_match_type, keywords.cpc_bid, keywords.original_cpc_bid
        FROM keyword_performance
        join keywords on keywords.id = keyword_performance.keyword_id
        join adgroups on adgroups.id = keywords.adgroup_id
        where date_range = "last_30_days"
        and keywords.account_id = "%s"
        and keywords.status = "enabled"
        and keyword_performance.conversions = 0
        and keyword_performance.clicks > 0
        and keywords.google_id != "3000006"
        and keywords.google_id != "3000000"
        and keywords.bidding_strategy_type = "cpc" 
        order by cost desc
        """ % (self.account_id)
        df = pd.read_sql(query, (Database()).createEngine())
        df.cpc_bid = df.cpc_bid.astype("str")
        df.cpc_bid = df.cpc_bid.str.replace("--", "0")
        df.cpc_bid = df.cpc_bid.astype("float")
        df['forecast'] = (df.cost / 7) * self.local_dates.days_remaining_in_this_month
        df.forecast = df.forecast.astype("str")
        df.forecast = df.forecast.str.replace("--", "0")
        df.forecast = df.forecast.astype("float")
        return df

    def updateBids(self, df):
        if functions.dfIsEmpty(df):
            return
        mutations = df.copy()
        mutations["entity_google_id"] = mutations["adgroup_google_id"] + "," + mutations["google_id"]
        mutations["account_id"] = self.account_id
        mutations["type"] = "keyword"
        mutations["action"] = "set"
        mutations["attribute"] = "bid"
        mutations["value"] = mutations["new_bid"]
        mutations["created_at"] = datetime.now()
        mutations["updated_at"] = datetime.now()
        mutations = mutations[
            ["entity_google_id", "entity_id", "account_id", "type", "action", "attribute", "value", "created_at",
             "updated_at"]]
        mutations = mutations.reset_index(drop=True)
        mutations["id"] = pd.Series([uuid.uuid1() for i in range(len(mutations))]).astype(str)
        print("updating %s bids" % mutations.shape[0])
        # print mutations["entity_id"]
        Database().appendDataframe("mutations", mutations)

    def updateKeywordsTable(self, df):
        df["id"] = df["entity_id"]
        for i, row in df[["id", "new_bid"]].iterrows():
            query = "UPDATE keywords SET cpc_bid = %s WHERE id = '%s'" % (row["new_bid"], row["id"])
            Database().createEngine().execute(query)
