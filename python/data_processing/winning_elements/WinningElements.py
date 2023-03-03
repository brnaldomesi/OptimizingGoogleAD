from __future__ import division

import uuid
from datetime import datetime

import pandas as pd

import common.functions as functions
from common.Database import Database
from common.Log import Log


class WinningElements:
    """Generates the winning elements - headlines and decriptions - for use on the Ad Testing page"""

    def __init__(self, account_id):
        self.date_range = "last_112_days"
        self.account_id = account_id

    def main(self):
        self.adgroupLevel()
        self.campaignLevel()
        self.accountLevel()

    def campaignLevel(self):
        Log("info", "processing campaign winning elements", "", self.account_id)
        self.table_name = "campaign_winning_elements"
        self.lines = ["headline_1", "headline_2", "headline_3", "description", "description_2"]
        self.group_by_column = "campaign_id"
        self.process()

    def accountLevel(self):
        Log("info", "processing account winning elements", "", self.account_id)
        self.table_name = "account_winning_elements"
        self.lines = ["headline_1", "headline_2", "headline_3", "description", "description_2"]
        self.group_by_column = "account_id"
        self.process()

    def adgroupLevel(self):
        Log("info", "processing ad group winning elements", "", self.account_id)
        self.table_name = "adgroup_winning_elements"
        self.lines = ["headline_1", "headline_2", "headline_3", "description", "description_2"]
        self.group_by_column = "adgroup_id"
        self.process()

    def process(self):
        """A place to group the core actions. Delete existing data, process and replace"""

        self.deleteExisting()
        df_chunks = self.getAdvertsDataframe()
        ldf = None
        while True:

            try:
                df = next(df_chunks)
            except StopIteration:
                return

            for line in self.lines:
                this_df = df[["clicks", "impressions", self.group_by_column, line]].groupby(
                    [self.group_by_column, line]).sum()
                this_df["ctr"] = (this_df.clicks / this_df.impressions) * 100
                adgroup_ids = list(this_df.index.levels[0])
                this_df = this_df.reset_index()

                if this_df.shape[0] == 0:
                    continue

                # start campaign ids loop
                for i, adgroup_id in enumerate(adgroup_ids):
                    # if i!=5:continue
                    tdf = this_df.copy()
                    quantile = tdf[(tdf[self.group_by_column] == adgroup_id)].impressions.quantile()
                    tdf = tdf[(tdf[self.group_by_column] == adgroup_id) & (tdf.impressions > quantile) & (
                                tdf[line] != "/")].sort_values(
                        "ctr", ascending=False).head(3).reset_index(drop=True)

                    if tdf.shape[0] == 0:
                        continue

                    tdf["order"] = tdf.index + 1
                    tdf["type"] = line
                    tdf.rename(columns={line: "value"}, inplace=True)
                    tdf = tdf[[self.group_by_column, "value", "order", "type"]]

                    try:
                        ldf = ldf.append(tdf)
                    except:
                        ldf = tdf.copy()

            if functions.dfIsEmpty(ldf):
                return

            ldf["account_id"] = self.account_id
            ldf = ldf.reset_index(drop=True)
            ldf.value = ldf.value.str.replace(" --", "")
            ldf["id"] = pd.Series([uuid.uuid1() for i in range(len(ldf))]).astype(str)
            ldf["created_at"] = datetime.now()
            ldf["updated_at"] = datetime.now()
            ldf["date_range"] = self.date_range
            ldf.to_sql(self.table_name, Database().createEngine(), if_exists='append', index=False)

    def deleteExisting(self):
        Database().executeQuery("delete from %s where account_id = '%s'" % (self.table_name, self.account_id))

    def getAdvertsDataframe(self):
        """Pulls advert, advert_performance and adgroup data
        Returns chunks of 2000"""

        query = """
        SELECT 
        adverts.google_id,
        adverts.final_urls,
        adverts.headline_1,
        adverts.status,
        adverts.description,
        adverts.path_2,
        adverts.path_1,
        adverts.headline_2,
        adverts.id,
        adverts.adgroup_id,
        adverts.domain,
        adverts.ctr_significance,
        adverts.conversion_rate_significance,
        adverts.account_id,
        adverts.loser,
        adverts.potential_savings,
        adverts.headline_3,
        adverts.description_2,
        advert_performance.impressions,
        advert_performance.clicks,
        advert_performance.conversions,
        advert_performance.cost,
        advert_performance.conversion_value,
        advert_performance.date_range,
        advert_performance.id,
        advert_performance.cpa,
        advert_performance.conversion_rate,
        advert_performance.average_cpc,
        advert_performance.ctr,
        advert_performance.roas,
        advert_performance.ctr_significance,
        advert_performance.conversion_rate_significance,
        advert_performance.ctr_message,
        advert_performance.conversion_rate_message,
        advert_performance.advert_id,
        advert_performance.average_position,
        advert_performance.impression_share,
        adgroups.name as adgroup_name,
        adgroups.campaign_id
        from adverts join advert_performance
        on adverts.id = advert_performance.advert_id
        join adgroups
        on adgroups.id = adverts.adgroup_id
        where adverts.account_id = '%s'
        and date_range = '%s'
        """ % (self.account_id, self.date_range)

        return pd.read_sql(query, Database().createEngine(), chunksize=2000)
