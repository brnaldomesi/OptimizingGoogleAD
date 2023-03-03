# coding: utf-8
from common.Database import Database


class PauseCampaigns:

    def __init__(self, account_id):
        self.account_id = account_id
        self.pause()

    def pause(self):
        print("pausing campaigns")
        self.getCampaignIdsToPause()
        self.storeCampaignIds()
        self.pauseCampaigns()

    def getCampaignIdsToPause(self):
        query = "select google_id from campaigns where account_id = '%s' " % (self.account_id)

        result = (Database()).createEngine().execute(query)
        campaign_ids = []

        for row in result:
            campaign_ids.append(row[0])

        self.campaign_ids_string = ",".join(campaign_ids)

    def storeCampaignIds(self):
        # print "storing %s" %(self.campaign_ids_string)
        query = "update budget_commander set paused_campaign_ids = '%s' where account_id = '%s' " % (
        self.campaign_ids_string, self.account_id)
        (Database()).createEngine().execute(query)

    def pauseCampaigns(self):
        # add to the pause campaigns queue

        pass
