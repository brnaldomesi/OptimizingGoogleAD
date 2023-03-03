from __future__ import division

import os

from jinja2 import Template

import api.campaign_performance_reports as campaign_performance_reports
from api.features.budget_commander.BudgetCommander import BudgetCommander
from api.features.budget_commander.pause_enable_campaigns.GetTodayCost import \
    GetTodayCost
from api.features.budget_commander.pause_enable_campaigns.PauseEnableCampaigns import \
    PauseEnableCampaigns
from api.Helpers import Helpers
from common.notifications.NotificationController import NotificationController
from common.helpers.LocalDates import LocalDates
from common.Log import Log
from common.Settings import Settings


class EmergencyStop(BudgetCommander):
    """
    * Will run hourly
    * Decides whether today's spend has spiked enough to warrant pausing campaigns
    * Re-enables campaigns if spend is under the limit
    """

    def __init__(self, account_id, budget_group_id=None):
        BudgetCommander.__init__(self, account_id, budget_group_id)
        self.account_id = account_id
        self.emergency_stop = self.user_settings['emergency_stop']
        self.campaigns_are_enabled_day = self.campaignsAreEnabledDay()
        self.campaigns_are_paused_day = self.campaignsArePausedDay()
    
    def main(self):
        
        campaign_performance_reports.main(self.account_id, True)#Download today's stats

        self.costs = GetTodayCost(self.account_id, self.budget_group_id)
        if self.costs.today_cost is None:
            return
        self.today_cost = round(self.costs.today_cost,2)
        self.day_budget_percentage = self.costs.day_budget_percentage
        self.day_limit = self.getDayLimit()

        Log("info", "today_cost: %s, day_limit: %s, budget: %s" %(self.today_cost, self.day_limit, self.budget), "", self.account_id)
        
        if self.shouldStop():
            (PauseEnableCampaigns(self.account_id, self.budget_group_id)).pauseForToday()
            NotificationController(self.account_id).emergencyStopPaused(self.day_limit, self.today_cost, self.budget_group_name)
            return

        if self.shouldEnable():
            (PauseEnableCampaigns(self.account_id, self.budget_group_id)).enableForToday()
            return


    def shouldStop(self):
        """Returns True if the campaigns should be paused for the day"""
        if not self.emergency_stop:
            return False

        if not self.budget or self.budget==0:
            return False

        #If the overall month budget is over then the monthly setting will handle things.
        #Don't run emergency stop
        if not self.under_budget:
            return False

        if not self.spendIsOverEmergencyLimit():
            return False

        if not self.campaigns_are_enabled_day:
            return False

        return True

    def shouldEnable(self):
        """Returns True if the campaigns should be re-enabled"""
        if not self.emergency_stop:
            return False

        if not self.budget or self.budget==0:
            return False

        if self.spendIsOverEmergencyLimit():
            return False
        
        if not self.campaigns_are_paused_day:
            return False

        return True

    def spendIsOverEmergencyLimit(self):
        if self.today_cost is None:
            return False
            
        if self.today_cost > self.day_limit:
            return True

        return False

    def getDayLimit(self):
        """Get the day limit
        * The day budget based on day of the week phasing
        * Then multiply the phasing based on forecast Vs budget
        """

        if self.budget is None:
            return 0

        if self.local_dates.is_first_of_month:
            vs_budget_multiplier = 1
        else:
            forecast = (self.this_month_spend/(self.local_dates.today.date().day-1))*self.local_dates.days_in_this_month
            vs_budget_multiplier = self.get_vs_budget_multiplier(self.this_month_spend, forecast)
            
        day_limit = self.budget * (self.day_budget_percentage*vs_budget_multiplier)
        minimum = self.budget/30.4
        if day_limit < minimum:
            day_limit = minimum

        return round(day_limit, 2)

    def get_vs_budget_multiplier(self, this_month_spend, forecast):
        if self.budget is None:
            return 0

        if this_month_spend >= self.budget:
            return 0
        
        if forecast==0:
            return 1

        if self.budget==0:
            return 1

        vs_budget = self.budget/float(forecast)
        vs_budget_multiplier = vs_budget
        if vs_budget_multiplier > 3:
            vs_budget_multiplier = 3
        if vs_budget_multiplier < 0:
            vs_budget_multiplier = 0

        return vs_budget_multiplier