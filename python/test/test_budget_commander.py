import unittest

from api.features.budget_commander.BudgetCommander import BudgetCommander
from TestAccount import TestAccount

class TestBudgetCommander(unittest.TestCase):

    def __init__(self, *args, **kwargs):
        super(TestBudgetCommander, self).__init__(*args, **kwargs)
        self.account_id = TestAccount().account_id

    def test_account_under_budget_equal_values(self):
        
        budget_commander = BudgetCommander(self.account_id)
        budget_commander.this_month_spend=20000
        budget_commander.budget=20000

        self.assertTrue(budget_commander.under_budget)

    def test_account_under_budget(self):
        
        budget_commander = BudgetCommander(self.account_id)
        budget_commander.this_month_spend=10000
        budget_commander.budget=20000

        self.assertTrue(budget_commander.under_budget)

    def test_account_over_budget(self):
        
        budget_commander = BudgetCommander(self.account_id)
        budget_commander.this_month_spend=30000
        budget_commander.budget=20000

        self.assertFalse(budget_commander.under_budget)

    def test_account_over_budget_budget_is_none(self):
        
        budget_commander = BudgetCommander(self.account_id)
        budget_commander.this_month_spend=30000
        budget_commander.budget=None

        self.assertTrue(budget_commander.under_budget)

    # def test_account_under_budget_budget_under(self):
    #     budget = 9000
    #     this_month_spend = 10000
    #
    #     budget_commander = BudgetCommander(self.account_id)
    #     result = (budget_commander).accountIsUnderBudget(budget, this_month_spend)
    #     self.assertFalse(result)

    # def test_account_under_budget_budget_over(self):
    #     budget = 10000
    #     this_month_spend = 9999
    #
    #     budget_commander = BudgetCommander(self.account_id)
    #     result = (budget_commander).accountIsUnderBudget(budget, this_month_spend)
    #     self.assertTrue(result)

    # def test_budget_vs_spend_equal_spend(self):
    #     budget = 10000
    #     this_month_spend = 10000
    #
    #     budget_commander = BudgetCommander(self.account_id)
    #
    #     result = budget_commander.getBudgetVsSpend(budget, this_month_spend)
    #     self.assertEquals(result, 0)

    # def test_budget_vs_spend_budget_higher(self):
    #     budget = 10001
    #     this_month_spend = 10000
    #
    #     budget_commander = BudgetCommander(self.account_id)
    #
    #     result = budget_commander.getBudgetVsSpend(budget, this_month_spend)
    #     self.assertEquals(result, -1)

    # def test_budget_vs_spend_percentage_budget_higher(self):
    #     budget = 10
    #     this_month_spend = 9
    #
    #     budget_commander = BudgetCommander(self.account_id)
    #
    #     result = budget_commander.getBudgetVsSpendPercentage(budget, this_month_spend)
    #     self.assertEquals(result, -.1)
