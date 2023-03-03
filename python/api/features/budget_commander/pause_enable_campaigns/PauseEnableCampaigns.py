# coding: utf-8
from api.features.budget_commander.BudgetCommander import BudgetCommander
from api.features.budget_commander.pause_enable_campaigns.PauseCampaigns import PauseCampaigns
from api.mutations_queue.CampaignStatusChange import CampaignStatusChange
from common.Database import Database
from common.Settings import Settings
from common.Env import Env

class PauseEnableCampaigns(object):
    """
    * Accepts whether it's the day or month pause
    * Pauses or Enables campaigns
    """

    def __init__(self, account_id, budget_group_id=None):
        self.account_id = account_id
        self.envvars = Env().vars
        self.budget_commander = BudgetCommander(account_id, budget_group_id)
        self.budget_group_id = budget_group_id
        self.user_settings = self.budget_commander.user_settings

    def pauseForToday(self):
        """Pause all campaigns and store their IDs."""
        campaign_ids = self.getActiveCampaignIds()
        if len(campaign_ids)==0:
            print("There are no campaigns in this budget group. Nothing will be paused")
            return
        CampaignStatusChange(self.account_id, campaign_ids, 'PAUSED')
        self.setDayPausedCampaigns(campaign_ids)

    def pauseForMonth(self):
        """Pause all campaigns and store their IDs."""
        campaign_ids = self.getActiveCampaignIds()
        if len(campaign_ids)==0:
            print("There are no campaigns in this budget group. Nothing will be paused")
            return
        CampaignStatusChange(self.account_id, campaign_ids, 'PAUSED')
        self.setMonthPausedCampaigns(campaign_ids)

    def getActiveCampaignIds(self):
        query = """
        SELECT google_id,id from campaigns
        WHERE account_id = '%s'
        AND status = 'enabled'
        """ % (self.account_id)

        if not self.budget_commander.is_master_budget_group:
            query += " AND budget_group_id = '%s' " %(self.budget_group_id)

        result = (Database()).executeQuery(query)
        campaign_ids = []
        for row in result:
            campaign_ids.append(row)

        return campaign_ids

    @property
    def budget_group_where_string(self):
        """Get the sql query where string
        Include budget group id if it's a budget group
        Name is master if not"""
        where_string = 'where account_id = "%s"' % (self.account_id)

        if(self.budget_commander.is_master_budget_group):
            where_string+= ' and budget_group_name = "master" '
        else:
            where_string+= ' and id = "%s" ' %(self.budget_group_id)

        return where_string
    
    def setDayPausedCampaigns(self, campaign_ids):
        """Stores campaign ids under the day_paused_campaigns column.
        
        Arguments:
        campaign_ids -- list of campaign ids which have been paused
        """

        table_name = "budget_commander"
        column = "day_paused_campaigns"

        campaign_ids = ['|'.join(campaign_id) for campaign_id in campaign_ids]
        (Database()).setValue(table_name, column, ",".join(campaign_ids), self.budget_group_where_string)

    def setMonthPausedCampaigns(self, campaign_ids):
        """Stores campaign ids under the month_paused_campaigns column.
        Arguments:
        campaign_ids -- list of campaign ids which have been paused. Tuple containing (google_id, entity_id)
        """

        table_name = "budget_commander"
        column = "month_paused_campaigns"
        campaign_ids = ['|'.join(campaign_id) for campaign_id in campaign_ids]
        (Database()).setValue(table_name, column, ",".join(campaign_ids), self.budget_group_where_string)

    def setDayPausedCampaignsToNull(self):
        """Sets the day_paused_campaigns value to null for this account"""
        table_name = "budget_commander"
        column = "day_paused_campaigns"
        (Database()).setValue(table_name, column, "", self.budget_group_where_string)

    def setMonthPausedCampaignsToNull(self):
        """Sets the month_paused_campaigns value to null for this account"""
        table_name = "budget_commander"
        column = "month_paused_campaigns"
        (Database()).setValue(table_name, column, "", self.budget_group_where_string)

    def enableForToday(self):
        """Enable campaigns based on stored IDs. Remove the IDs."""
        campaign_ids = self.user_settings['day_paused_campaigns']
        campaign_ids = [campaign_id.split('|') for campaign_id in campaign_ids.split(',')]
        if len(campaign_ids)==0:
            print("There are no campaigns in this budget group. Nothing will be enabled")
            return
        CampaignStatusChange(self.account_id, campaign_ids, 'ENABLED')
        self.setDayPausedCampaignsToNull()

    def enableForMonth(self):
        """Enable campaigns based on stored IDs. Remove the IDs."""
        campaign_ids = self.user_settings['month_paused_campaigns']
        campaign_ids = [campaign_id.split('|') for campaign_id in campaign_ids.split(',')]
        if len(campaign_ids)==0:
            print("There are no campaigns in this budget group. Nothing will be enabled")
            return
        CampaignStatusChange(self.account_id, campaign_ids, 'ENABLED')
        self.setMonthPausedCampaignsToNull()

    def campaignsPaused(self):
        result = self.user_settings["paused_campaign_ids"]
        if result == "" or result is None:
            return False

        return True
