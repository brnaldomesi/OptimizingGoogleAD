from common.Database import Database
from common.helpers.LocalDates import LocalDates
from common.Log import Log


class Rollover:
    """Logic for managing the account's excess budget"""

    def __init__(self, account_id, rollover_spend, original_budget, last_month_spend, budget_group_id):
        self.account_id = account_id
        self.rollover_spend = rollover_spend
        self.original_budget = original_budget
        self.last_month_spend = last_month_spend
        self.budget_group_id = budget_group_id
        self.is_first_of_month = LocalDates(account_id).is_first_of_month

    def main(self):
        if self.should_store_excess_budget:
            excess_budget = self.calculate_excess_budget(self.original_budget, self.last_month_spend)
            self.update_excess_budget(excess_budget)

        if not self.rollover_spend:
            self.update_excess_budget(0)

    def update_excess_budget(self,excess_budget):
        Log('info', 'Storing excess budget', "excess_budget: %s" %(excess_budget), self.account_id)
        if self.budget_group_id:
            Database().setValue('budget_commander', 'excess_budget', excess_budget, 'where id = "%s" '%(self.budget_group_id))
        else:
            Database().setValue('budget_commander', 'excess_budget', excess_budget, 'where account_id = "%s" and budget_group_name = "master" '%(self.account_id))
    
    @property
    def should_store_excess_budget(self):
        
        if not self.rollover_spend:
            return False

        if not self.is_first_of_month:
            return False

        return True

    def calculate_excess_budget(self, budget, last_month_spend):
        """Calculate the excess budget
        budget - last month's spend
        """
        if budget is None:
            return 0
        if last_month_spend is None:
            return 0

        if budget <= last_month_spend:
            return 0

        return float(budget) - float(last_month_spend)
