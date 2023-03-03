from api.features.budget_commander.ControlSpend import ControlSpend
from api.features.budget_commander.NotifyViaEmail import NotifyViaEmail
from api.features.budget_commander.pause_enable_campaigns.EmergencyStop import \
    EmergencyStop
from api.features.budget_commander.pause_enable_campaigns.MonthlyStop import \
    MonthlyStop
from api.Helpers import Helpers
from common.Database import Database
from common.Log import Log
from common.Settings import Settings


class BudgetGroups(object):

    """A wrapper to run all Budget Commander features once per budget group"""

    def __init__(self, account_id):
        self.account_id = account_id
        self.budget_group_ids = self.getBudgetGroupIds()

    def notifyViaEmail(self):
        self.runBudgetGroups(NotifyViaEmail)

    def monthlyStop(self):
        self.runBudgetGroups(MonthlyStop)

    def emergencyStop(self):
        self.runBudgetGroups(EmergencyStop)

    def controlSpend(self):
        self.runBudgetGroups(ControlSpend)

    def runBudgetGroups(self,feature):
        """Run once for the account (master) then once for each budget group"""
        feature(self.account_id).main()
        for budget_group_id in self.budget_group_ids:
            feature(self.account_id, budget_group_id).main()

    def getBudgetGroupIds(self):
        """Get budget group ids excluding master"""
        budget_group_ids = Database().getValues('budget_commander', 'id', "where account_id = '%s' and budget_group_name != 'master'" %(self.account_id))
        budget_group_ids = [budget_group_id[0] for budget_group_id in budget_group_ids]
        return budget_group_ids
