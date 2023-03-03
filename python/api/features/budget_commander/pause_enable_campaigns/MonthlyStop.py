from __future__ import division

import os

from jinja2 import Template

from api.features.budget_commander.BudgetCommander import BudgetCommander
from api.features.budget_commander.pause_enable_campaigns.PauseEnableCampaigns import \
    PauseEnableCampaigns
from api.features.budget_commander.Rollover import Rollover
from common.Database import Database
from common.Log import Log
from common.Settings import Settings
from common.notifications.NotificationController import NotificationController


class MonthlyStop(BudgetCommander):
    """
    * Runs daily
    * Pauses campaigns if spend is over budget
    * Re-enables campaigns if spend is under the limit (e.g. new month)
    """

    def __init__(self, account_id,budget_group_id=None):
        BudgetCommander.__init__(self, account_id, budget_group_id)
        self.account_id = account_id
        self.budget_group_id = budget_group_id
        self.rollover_spend = self.user_settings['rollover_spend']


    def main(self):
        Rollover(self.account_id, self.rollover_spend, self.original_budget, self.last_month_spend, self.budget_group_id).main()

        if self.shouldPauseCampaigns():
            (PauseEnableCampaigns(self.account_id, self.budget_group_id)).pauseForMonth()
            Log("info", "Budget commander monthly stop: campaigns paused for the month", "", self.account_id)
            NotificationController(self.account_id).monthlyStop(self.budget, self.this_month_spend,'Paused', self.budget_group_name)
            return

        if  self.shouldEnableCampaigns():
            (PauseEnableCampaigns(self.account_id, self.budget_group_id)).enableForMonth()
            Log("info", "Budget commander monthly stop: campaigns enabled for the month", "", self.account_id)
            NotificationController(self.account_id).monthlyStop(self.budget, self.this_month_spend,'Enabled', self.budget_group_name)
            return

        Log("info", "Budget commander monthly stop: no actions", "", self.account_id)

    def shouldEnableCampaigns(self):
        """Returns a bool for whether or not to pause the campaigns for the month"""
        if not self.campaigns_paused_month:
            return False

        if not self.under_budget:
            return False

        if not self.enable_campaigns:
            return False

        return True

    def shouldPauseCampaigns(self):
        """Returns a bool for whether or not to pause the campaigns for the month"""
        if self.campaigns_paused_month:
            return False

        if self.under_budget:
            return False

        if not self.pause_campaigns:
            return False

        return True

        