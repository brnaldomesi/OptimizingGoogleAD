from __future__ import division

import traceback
import warnings
from datetime import datetime

import numpy as np
import pandas as pd

import common.functions as functions
from common.Database import Database
from common.Log import Log
from common.Settings import Settings
from data_processing.Confidence import Confidence

warnings.filterwarnings("ignore")


class AdvertProcessing:
    """Write to advert_performance table with calculated metrics (like cpa) and statistical significance"""

    def __init__(self, account_id):
        # use this to skip processing once a winning ad is found (we'll only display 1 date range)
        self.ids_of_adgroups_with_winning_ads = []
        self.account_id = account_id
        self.column_map = {
            "adgroup_google_id": "adgroup_google_id", "campaign_google_id": "campaign_google_id",
            "advert_google_id": "google_id",
            "advert_status": "status", "headline_1": "headline_1", "headline_2": "headline_2",
            "headline_3": "headline_3", "description": "description",
            "description_2": "description_2", "path_1": "path_1", "path_2": "path_2", "final_urls": "final_urls",
            'ad_type': 'ad_type'
        }

    def main(self):
        """Populate the advert_performance table"""
        Log("info", "populating the advert_performance table",
            "", self.account_id)

        settings = Settings()
        for date_range in settings.date_ranges:

            df_chunks = self.dataFrameFromAdPerformanceReports(date_range)
            self.deleteExisting(date_range)

            while True:

                try:
                    df = next(df_chunks)
                except StopIteration:
                    break

                if functions.dfIsEmpty(df):
                    continue

                try:
                    df = self.processDf(df, date_range)
                except Exception as exception:
                    Log("error", str(exception), traceback.format_exc())
                    raise

                df = self.addInfo(df)

                self.writeToAdvertPerformanceTable(df, date_range)

    def addInfo(self, df):
        df["created_at"] = datetime.now()
        df["updated_at"] = datetime.now()
        df = self.addDomainName(df)

        return df

    def writeToAdvertPerformanceTable(self, df, date_range):

        table_name = "advert_performance"

        columns = list(pd.read_sql("select * from %s where clicks > 999999999" %
                                   (table_name), Database().createEngine()).columns)
        for col in columns:
            if col not in df.columns:
                df[col] = None

        functions.append_df_to_sql_table(df[columns], table_name)

    def deleteExisting(self, date_range):
        """Initially remove all advert data for this account - we'll overwrite it"""

        deleteQuery = "delete from advert_performance where account_id = '%s' and date_range = '%s' " % (
            self.account_id, date_range)

        Database().createEngine().execute(deleteQuery)

    # add cpa, ctr, etc.
    def processDf(self, df, date_range):

        if functions.dfIsEmpty(df):
            return

        df = functions.addCalculatedMetricsToDataFrame(df)

        df = self.addSignificance(df)

        return df

    def adsAreTheSame(self, winning_ad, losing_ad):
        if winning_ad['headline_1'] != losing_ad['headline_1']:
            return False
        if winning_ad['headline_2'] != losing_ad['headline_2']:
            return False
        if winning_ad['headline_3'] != losing_ad['headline_3']:
            return False
        if winning_ad['description'] != losing_ad['description']:
            return False
        if winning_ad['description_2'] != losing_ad['description_2']:
            return False
        if winning_ad['path_1'] != losing_ad['path_1']:
            return False
        if winning_ad['path_2'] != losing_ad['path_2']:
            return False
        return True

    def addSignificance(self, df):
        df["ctr_significance"] = 0
        df["ctr_message"] = ''

        minimum_significance = 90

        df = df.sort_values("ctr", ascending=False)

        processed_adgroup_ids = []

        for adgroup_id in df[df.clicks > 10].adgroup_id.drop_duplicates().values:

            if adgroup_id in self.ids_of_adgroups_with_winning_ads:
                continue

            this_df = df[(df.adgroup_id == adgroup_id) & (df.ad_type == 'Expanded text ad')]

            if this_df[this_df.clicks > 9].shape[0] < 2:
                continue

            winning_ad = this_df.iloc[0]
            # the losing ad is the lowest ctr ad which has 10 or more clicks
            losing_ad = this_df[this_df.clicks > 9].iloc[this_df[this_df.clicks > 9].shape[0] - 1]

            if not self.adsAreTheSame(winning_ad, losing_ad):

                ctr_significance = Confidence([losing_ad.impressions, losing_ad.clicks],
                                              [winning_ad.impressions, winning_ad.clicks]).calculate()
                df["ctr_significance"] = np.where(df['id'] == winning_ad.id, ctr_significance, df["ctr_significance"])
                df["ctr_significance"] = np.where(df['id'] == losing_ad.id, ctr_significance, df["ctr_significance"])

                if ctr_significance > minimum_significance:
                    df["ctr_message"] = np.where(df['id'] == winning_ad.id, 'winning', df["ctr_message"])
                    df["ctr_message"] = np.where(df['id'] == losing_ad.id, 'losing', df["ctr_message"])
                    processed_adgroup_ids.append(adgroup_id)

                    self.ids_of_adgroups_with_winning_ads.append(adgroup_id)

        df["conversion_rate_significance"] = 0
        df["conversion_rate_message"] = ''

        df = df.sort_values("conversion_rate", ascending=False)

        for adgroup_id in df[df.clicks > 10].adgroup_id.drop_duplicates().values:

            if adgroup_id in self.ids_of_adgroups_with_winning_ads:
                continue

            if adgroup_id in processed_adgroup_ids:
                continue

            this_df = df[(df.adgroup_id == adgroup_id) &
                         (df.ad_type == 'Expanded text ad')]

            if this_df[this_df.clicks > 9].shape[0] < 2:
                continue

            winning_ad = this_df.iloc[0]
            # the losing ad is the lowest ctr ad which has 10 or more clicks
            losing_ad = this_df[this_df.clicks >
                                9].iloc[this_df[this_df.clicks > 9].shape[0] - 1]

            conversion_rate_significance = Confidence([losing_ad.clicks, losing_ad.conversions], [
                winning_ad.clicks, winning_ad.conversions]).calculate()
            df["conversion_rate_significance"] = np.where(
                df['id'] == winning_ad.id, conversion_rate_significance, df["conversion_rate_significance"])
            df["conversion_rate_significance"] = np.where(
                df['id'] == losing_ad.id, conversion_rate_significance, df["conversion_rate_significance"])

            if conversion_rate_significance > minimum_significance:
                df["conversion_rate_message"] = np.where(
                    df['id'] == winning_ad.id, 'winning', df["conversion_rate_message"])
                df["conversion_rate_message"] = np.where(
                    df['id'] == losing_ad.id, 'losing', df["conversion_rate_message"])

                self.ids_of_adgroups_with_winning_ads.append(adgroup_id)

        return df

    # grabs the necessary information from the ad performance reports table
    def dataFrameFromAdPerformanceReports(self, date_range):
        database = Database()
        table_name = 'advert_performance'
        columns = list(pd.read_sql("select * from %s where id = '999999999'" %
                                   (table_name), database.createEngine()).columns)

        columns = ['advert_performance.' + column for column in columns]

        query = """
        select adverts.ad_type,adverts.adgroup_id,adverts.final_urls, %s,
        adverts.headline_1,
        adverts.headline_2,
        adverts.headline_3,
        adverts.description,
        adverts.description_2,
        adverts.path_1,
        adverts.path_2
        from advert_performance
        join adverts
        on adverts.id = advert_performance.advert_id
        where adverts.account_id = '%s'
        and advert_performance.date_range = '%s'
        """ % (','.join(columns), self.account_id, date_range)

        ad_performance_df_chunks = pd.read_sql_query(
            query, Database().createEngine(), chunksize=2000)

        return ad_performance_df_chunks

    def addDomainName(self, df):
        # add the domain name
        import ast
        try:
            from urllib.parse import urlparse
        except ImportError:
            from urlparse import urlparse

        def getDomain(row):
            row = ast.literal_eval(row)
            parsed_uri = urlparse(row[0])
            domain = '{uri.scheme}://{uri.netloc}/'.format(uri=parsed_uri)
            return domain

        df["domain"] = df["final_urls"].apply(lambda row: getDomain(row))
        return df
