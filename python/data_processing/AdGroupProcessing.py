from __future__ import division

import pandas as pd

from common.Database import Database
from common.Log import Log


class AdGroupProcessing():
    """Set the priority, message and ad count in the adgroups table"""
    def __init__(self, account_id):
        self.account_id = account_id
        self.date_range = "last_112_days"

    def main(self):
        Log("info", "Adding message, ad_count and priority to the adgroups table")
        self.updateAdGroups()


    def getMessage(self, eta_ad_count, has_winners, ad_count):
        if eta_ad_count == 0:
            return "no_expanded_text_ads"  # we can only show ad groups which at least 1 ETA (for the placeholder text)

        if has_winners:
            return "has_winners"

        if ad_count < 2:
            return "too_few_ads"

    def updateAdGroups(self):
        """Determine message, ad count and priority then update the adgroups one at a time"""
        adgroup_ids = self.getAdGroupIds()
        for adgroup_id in adgroup_ids:
            message = ""
            df = self.getAdvertPerformanceDf(adgroup_id)

            ad_count = df.shape[0]

            if ad_count == 0:
                self.writeToDatabase(0, self.getPriority(0, False), "no_ads", adgroup_id)
                continue

            eta_ad_count = df[df.ad_type == 'Expanded text ad'].shape[0]

            has_winners = "winning" in df.ctr_message.values or "winning" in df.conversion_rate_message.values

            self.writeToDatabase(ad_count, self.getPriority(df.shape[0], has_winners), self.getMessage(eta_ad_count,has_winners,ad_count), adgroup_id)

    def getAdGroupIds(self):
        """Get active ad group ids"""
        query = """
        select adgroups.id as adgroup_id from adgroups 
        join campaigns on campaigns.id = adgroups.campaign_id
        where adgroups.account_id = '%s'
        and campaigns.status = 'enabled'
        and adgroups.status = 'enabled'
        
        """ % (self.account_id)

        df = pd.read_sql(query, Database().createEngine())
        ids = list(df.adgroup_id.values)
        return ids

    def writeToDatabase(self, ad_count, priority, message, adgroup_id):
        Database().executeQuery("update adgroups set ad_count = %s where id = '%s' and account_id = '%s'" % (
        ad_count, adgroup_id, self.account_id))
        Database().executeQuery("update adgroups set priority = %s where id = '%s' and account_id = '%s'" % (
        priority, adgroup_id, self.account_id))
        Database().executeQuery("update adgroups set message = '%s' where id = '%s' and account_id = '%s'" % (
        message, adgroup_id, self.account_id))

    def getAdvertPerformanceDf(self, adgroup_id):
        """We need this to count ads and generate messages"""
        query = """
        SELECT advert_performance.advert_id,adverts.ad_type,adverts.adgroup_id,advert_performance.clicks, advert_performance.conversion_rate_message,advert_performance.ctr_message
        FROM advert_performance
        join adverts
        on adverts.id = advert_performance.advert_id
        where advert_performance.account_id = '%s'
        and advert_performance.date_range = '%s' 
        and adgroup_id = '%s'
        and adverts.status = 'enabled'
        """ % (self.account_id, self.date_range, adgroup_id)

        # need to update this (and all) queries to match the date_range we've set
        return pd.read_sql_query(query, Database().createEngine())

    def getPriority(self, ad_count, has_winners):
        """Return the priority as an integer. Higher number = higher priority
        TODO: Incorporate clicks or cost"""
        if ad_count == 0:
            return 5000

        if ad_count == 1:
            return 2000

        if has_winners:
            return 1500

        if ad_count == 2:
            return 1000

        return 0
