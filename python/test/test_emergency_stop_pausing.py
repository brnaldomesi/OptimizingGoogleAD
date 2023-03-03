import unittest

from api.features.budget_commander.BudgetCommander import BudgetCommander
from TestAccount import TestAccount
from api.features.budget_commander.pause_enable_campaigns.EmergencyStop import EmergencyStop


class TestEmergencyStopPausing(unittest.TestCase):

    def __init__(self, *args, **kwargs):
        super(TestEmergencyStopPausing, self).__init__(*args, **kwargs)
        self.account_id = TestAccount().account_id

    def test_should_stop(self):

        emergency_stop = EmergencyStop(self.account_id)
        
        #under over all budget
        emergency_stop.this_month_spend=10000
        emergency_stop.budget=20000

        #feature enabled
        emergency_stop.emergency_stop=True

        #over day budget/limit
        emergency_stop.today_cost = 100
        emergency_stop.day_limit = 10

        #campaigns are enabled (so can be paused)
        emergency_stop.campaigns_are_enabled_day = True

        self.assertTrue(emergency_stop.shouldStop())

    def test_should_not_stop_over_month_budget(self):

        emergency_stop = EmergencyStop(self.account_id)
        
        #under over all budget
        emergency_stop.this_month_spend=30000
        emergency_stop.budget=20000

        #feature enabled
        emergency_stop.emergency_stop=True

        #over day budget/limit
        emergency_stop.today_cost = 100
        emergency_stop.day_limit = 10

        #campaigns are enabled (so can be paused)
        emergency_stop.campaigns_are_enabled_day = True

        self.assertFalse(emergency_stop.shouldStop())

    def test_should_not_stop_disabled(self):

        emergency_stop = EmergencyStop(self.account_id)
        
        #under over all budget
        emergency_stop.this_month_spend=10000
        emergency_stop.budget=20000

        #feature enabled
        emergency_stop.emergency_stop=False

        #over day budget/limit
        emergency_stop.today_cost = 100
        emergency_stop.day_limit = 10

        #campaigns are enabled (so can be paused)
        emergency_stop.campaigns_are_enabled_day = True

        self.assertFalse(emergency_stop.shouldStop())

    def test_should_not_stop_under_day_limit(self):

        emergency_stop = EmergencyStop(self.account_id)
        
        #under over all budget
        emergency_stop.this_month_spend=10000
        emergency_stop.budget=20000

        #feature enabled
        emergency_stop.emergency_stop=True

        #over day budget/limit
        emergency_stop.today_cost = 10
        emergency_stop.day_limit = 100

        #campaigns are enabled (so can be paused)
        emergency_stop.campaigns_are_enabled_day = True

        self.assertFalse(emergency_stop.shouldStop())

    def test_should_not_stop_equal_to_day_limit(self):

        emergency_stop = EmergencyStop(self.account_id)
        
        #under over all budget
        emergency_stop.this_month_spend=10000
        emergency_stop.budget=20000

        #feature enabled
        emergency_stop.emergency_stop=True

        #over day budget/limit
        emergency_stop.today_cost = 100
        emergency_stop.day_limit = 100

        #campaigns are enabled (so can be paused)
        emergency_stop.campaigns_are_enabled_day = True

        self.assertFalse(emergency_stop.shouldStop())
