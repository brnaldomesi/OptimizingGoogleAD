import unittest

from api.features.budget_commander.BudgetCommander import BudgetCommander
from TestAccount import TestAccount
from api.features.budget_commander.Rollover import Rollover


class TestRollover(unittest.TestCase):

    """Excess budget is stored on the first of the month
    It's the budget minus last month's spend"""

    def __init__(self, *args, **kwargs):
        super(TestRollover, self).__init__(*args, **kwargs)
        self.account_id = TestAccount().account_id

    def test_should_store_excess(self):
        """Store excess if it's the 1st of the month
        and rollover_budget is enabled"""

        rollover_spend = True
        budget = 1000
        last_month_spend = 500
        rollover = Rollover(self.account_id, rollover_spend, budget, last_month_spend)
        rollover.is_first_of_month = True

        self.assertTrue(rollover.should_store_excess_budget)

    def test_should_not_store_excess(self):
        """Store excess if it's the 1st of the month
        and rollover_budget is enabled"""

        rollover_spend = True
        budget = 1000
        last_month_spend = 500
        rollover = Rollover(self.account_id, rollover_spend, budget, last_month_spend)
        rollover.is_first_of_month = False

        self.assertFalse(rollover.should_store_excess_budget)

    def test_should_not_store_excess_disabled(self):
        """Store excess if it's the 1st of the month
        and rollover_budget is enabled"""

        rollover_spend = False
        budget = 1000
        last_month_spend = 500
        rollover = Rollover(self.account_id, rollover_spend, budget, last_month_spend)
        rollover.is_first_of_month = True

        self.assertFalse(rollover.should_store_excess_budget)

    def test_excess_budget_calculation(self):
        """Test the calculation (budget - last month's spend)
        Should be 0 if there's no excess"""
        rollover_spend = True
        budget = 1000
        last_month_spend = 2000
        rollover = Rollover(self.account_id, rollover_spend, budget, last_month_spend)
        self.assertEquals(rollover.calculate_excess_budget(budget, last_month_spend), 0)

    def test_excess_budget_calculation_2(self):
        """Test the calculation (budget - last month's spend)
        Should be 0 if there's no excess"""
        rollover_spend = True
        budget = 2000
        last_month_spend = 1000
        rollover = Rollover(self.account_id, rollover_spend, budget, last_month_spend)
        self.assertTrue(rollover.calculate_excess_budget(budget, last_month_spend)==1000)

    def test_excess_budget_calculation_budget_none(self):
        """Test the calculation (budget - last month's spend)
        Should be 0 if there's no excess"""
        rollover_spend = True
        budget = None
        last_month_spend = 1000
        rollover = Rollover(self.account_id, rollover_spend, budget, last_month_spend)
        self.assertTrue(rollover.calculate_excess_budget(budget, last_month_spend)==0)

    def test_excess_budget_calculation_last_month_spend_none(self):
        """Test the calculation (budget - last month's spend)
        Should be 0 if there's no excess"""
        rollover_spend = True
        budget = 10
        last_month_spend = None
        rollover = Rollover(self.account_id, rollover_spend, budget, last_month_spend)
        self.assertTrue(rollover.calculate_excess_budget(budget, last_month_spend)==0)

