import unittest

from api.features.budget_commander.BudgetCommander import BudgetCommander
from TestAccount import TestAccount
from api.features.budget_commander.pause_enable_campaigns.MonthlyStop import MonthlyStop


class TestMonthlyStop(unittest.TestCase):

    def __init__(self, *args, **kwargs):
        super(TestMonthlyStop, self).__init__(*args, **kwargs)
        self.account_id = TestAccount().account_id

    def test_should_pause_campaigns(self):

        monthly_stop = MonthlyStop(self.account_id)
        
        monthly_stop.this_month_spend=30000
        monthly_stop.budget=20000

        monthly_stop.pause_campaigns = True
        monthly_stop.campaigns_paused_month = False

        self.assertTrue(monthly_stop.shouldPauseCampaigns())

    def test_should_not_pause_campaigns_already_paused(self):

        monthly_stop = MonthlyStop(self.account_id)
        
        monthly_stop.this_month_spend=30000
        monthly_stop.budget=20000

        monthly_stop.pause_campaigns = True
        monthly_stop.campaigns_paused_month = True #already paused

        self.assertFalse(monthly_stop.shouldPauseCampaigns())

    def test_should_not_pause_campaigns_feature_disabled(self):

        monthly_stop = MonthlyStop(self.account_id)
        
        monthly_stop.this_month_spend=30000
        monthly_stop.budget=20000

        monthly_stop.pause_campaigns = False  #disabled
        monthly_stop.campaigns_paused_month = False

        self.assertFalse(monthly_stop.shouldPauseCampaigns())

    def test_should_enable_campaigns(self):

        monthly_stop = MonthlyStop(self.account_id)
        
        monthly_stop.this_month_spend=10000
        monthly_stop.budget=20000

        monthly_stop.enable_campaigns = True
        monthly_stop.campaigns_paused_month = True

        self.assertTrue(monthly_stop.shouldEnableCampaigns())

    def test_should_not_enable_campaigns_already_enabled(self):

        monthly_stop = MonthlyStop(self.account_id)
        
        monthly_stop.this_month_spend=10000
        monthly_stop.budget=20000

        monthly_stop.enable_campaigns = True
        monthly_stop.campaigns_paused_month = False #already_enabled

        self.assertFalse(monthly_stop.shouldEnableCampaigns())

    def test_should_not_enable_campaigns_feature_disabled(self):

        monthly_stop = MonthlyStop(self.account_id)
        
        monthly_stop.this_month_spend=10000
        monthly_stop.budget=20000

        monthly_stop.enable_campaigns = False#feature_disabled
        monthly_stop.campaigns_paused_month = True 

        self.assertFalse(monthly_stop.shouldEnableCampaigns())