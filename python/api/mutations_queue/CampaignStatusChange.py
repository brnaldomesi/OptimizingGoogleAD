# coding: utf-8
from common.Database import Database


class CampaignStatusChange(object):
    """
    Updates the mutations queue
    Affecting campaign status (PAUSED or ENABLED)
    """

    def __init__(self, account_id, google_campaign_ids, status):
        self.account_id = account_id
        self.google_campaign_ids = google_campaign_ids
        self.status = status
        self.main()

    def main(self):
        rows = {}

        mutation_type = "campaign"
        action = "set"
        attribute = "status"
        value = self.status

        for i, campaign_id in enumerate(self.google_campaign_ids):
            entity_google_id = campaign_id[0]
            entity_id = campaign_id[1]
            i = str(i)
            rows[i] = {}
            rows[i]["account_id"] = self.account_id
            rows[i]["type"] = mutation_type
            rows[i]["entity_id"] = entity_id
            rows[i]["entity_google_id"] = entity_google_id
            rows[i]["action"] = action
            rows[i]["attribute"] = attribute
            rows[i]["value"] = value

        (Database()).appendRows("mutations", rows)
