import unittest

from api.features.budget_commander.BudgetCommander import BudgetCommander
from TestAccount import TestAccount
from api.features.budget_commander.pause_enable_campaigns.EmergencyStop import EmergencyStop


class TestEmergencyStopEnabling(unittest.TestCase):

    def __init__(self, *args, **kwargs):
        super(TestEmergencyStopEnabling, self).__init__(*args, **kwargs)
        self.account_id = TestAccount().account_id

    def test_should_enable(self):

        emergency_stop = EmergencyStop(self.account_id)

        #feature enabled
        emergency_stop.emergency_stop=True

        #under day budget/limit
        emergency_stop.today_cost = 10
        emergency_stop.day_limit = 100

        #campaigns are paused (so can be enabled)
        emergency_stop.campaigns_are_paused_day = True

        self.assertTrue(emergency_stop.shouldEnable())

    def test_should_not_enable_disabled(self):

        emergency_stop = EmergencyStop(self.account_id)
        
        #feature enabled
        emergency_stop.emergency_stop=False

        #under day budget/limit
        emergency_stop.today_cost = 10
        emergency_stop.day_limit = 100

        #campaigns are paused (so can be enabled)
        emergency_stop.campaigns_are_paused_day = True

        self.assertFalse(emergency_stop.shouldEnable())

    def test_should_not_enable_over_day_limit(self):

        emergency_stop = EmergencyStop(self.account_id)
        
        ##feature enabled
        emergency_stop.emergency_stop=True

        #under day budget/limit
        emergency_stop.today_cost = 100
        emergency_stop.day_limit = 10

        #campaigns are paused (so can be enabled)
        emergency_stop.campaigns_are_paused_day = True

        self.assertFalse(emergency_stop.shouldEnable())

    def test_should_enable_equal_to_day_limit(self):

        emergency_stop = EmergencyStop(self.account_id)
        
        #feature enabled
        emergency_stop.emergency_stop=True

        #under day budget/limit
        emergency_stop.today_cost = 100
        emergency_stop.day_limit = 100

        #campaigns are paused (so can be enabled)
        emergency_stop.campaigns_are_paused_day = True

        self.assertTrue(emergency_stop.shouldEnable())
