# coding: utf-8
from __future__ import division

import warnings

from api.Report import Report

warnings.filterwarnings("ignore")
from common.Log import Log
import common.functions as functions

where_string = """
 where Status in [ENABLED,PAUSED] and CampaignStatus = ENABLED and AdGroupStatus = ENABLED
"""

options = {
    "report_name": "KEYWORDS_PERFORMANCE_REPORT",
    "performance_table_name": "keyword_performance",
    "entity_table_name": "keywords",
    "entity_id_name": "keyword_id",

    "where_string": where_string,

    "queryColumnsToTableColumns": {
        'CampaignId': "adgroup_google_id",
        'CampaignName': "adgroup_name",
        'CampaignStatus': "adgroup_status",
        'AdGroupId': "adgroup_id",
        'AdGroupStatus': "adgroup_status",
        'Cost': "cost",
        'Impressions': "impressions",
        'Conversions': "conversions",
        'Clicks': "clicks",
        'ConversionValue': "conversion_value",
        'AdGroupName': "adgroup_name",
        'Criteria': "keyword_text",
        'KeywordMatchType': "keyword_match_type",
        'Id': "google_id",
        'Status': "status",
        'BounceRate': "bounce_rate",
        'SearchImpressionShare': "search_impression_share",
        'BiddingStrategyId': "bidding_strategy_id",
        'BiddingStrategyName': "bidding_strategy_name",
        'BiddingStrategySource': "bidding_strategy_source",
        'BiddingStrategyType': "bidding_strategy_type",
        'CpcBidSource': "cpc_bid_source",
        'CpcBid': "cpc_bid",
        'CpmBid': "cpm_bid",
        'FirstPageCpc': "first_page_cpc",
        'FirstPositionCpc': "first_position_cpc",
        'TopOfPageCpc': "top_of_page_cpc",
    },

    # map query column name to downloaded column names
    "queryColumnsToDownloadColumns": {
        'CampaignId': "Campaign ID",
        'CampaignName': "Campaign",
        'CampaignStatus': "Campaign state",
        'AdGroupId': "Ad group ID",
        'AdGroupStatus': "Ad group state",
        'Cost': "Cost",
        'Impressions': "Impressions",
        'Conversions': "Conversions",
        'Clicks': "Clicks",
        'ConversionValue': "Total conv. value",
        'Criteria': "Keyword",
        'KeywordMatchType': "Match type",
        'Id': "Keyword ID",
        'Status': "Keyword state",
        'BounceRate': "Bounce rate",
        'SearchImpressionShare': "Search Impr. share",
        'BiddingStrategyId': "Bid Strategy ID",
        'BiddingStrategyName': "Bid Strategy Name",
        'BiddingStrategySource': "Bidding Strategy Source",
        'BiddingStrategyType': "Bid Strategy Type",
        'CpcBidSource': "Max CPC source",
        'CpcBid': "Max. CPC",
        'CpmBid': "Max. CPM",
        'FirstPageCpc': "First page CPC",
        'FirstPositionCpc': "First position CPC",
        'TopOfPageCpc': "Top of page CPC",
    }

}


def main(account_id):
    Log("info", "getting keyword performance from the api", "", account_id)

    report = Report(account_id, "", options)

    df = report.createDfWithAllDateRanges(account_id)

    if functions.dfIsEmpty(df):
        Log("warning","This user's account doesn't have enough data to process","",account_id)
        
        return

    # print df[df.google_id=="309491001346"].cpc_bid

    # remember column headers are as per the download here
    df["keyword_id"] = df.apply(lambda row: functions.addIdToDf(account_id, row["Keyword ID"], row["Ad group ID"]), axis=1)
    df["id"] = df["keyword_id"]

    df = addParentId(df, account_id)  # our UUID from the keywords table

    df = report.basicProcessing(df)

    df = reportSpecificProcessing(df)

    report.writeToEntitiesTable(df, report, account_id)

    report.deleteAllPerformance()
    report.writeToPerformanceTable(df, report, account_id)


# hex of id (if there is one) and parent id
def addParentId(df, account_id):
    df["adgroup_id"] = df.apply(lambda row: functions.addIdToDf(account_id, row["Ad group ID"], row["Campaign ID"]), axis=1)
    return df


def reportSpecificProcessing(df):
    def moneyToValue(value):
        """Convert Google's Money format to the actual value (divide by 1000000).
        """
        try:
            return float(value) / 1000000
        except ValueError:
            return 0

    df["cpm_bid"] = df["cpm_bid"].apply(lambda value: moneyToValue(value))
    df["cpc_bid"] = df["cpc_bid"].apply(lambda value: moneyToValue(value))

    df["original_cpc_bid"] = df["cpc_bid"]

    def convertToFloat(bounce_rate):
        try:
            return float(bounce_rate.replace("%", ""))
        except ValueError:
            # already a float
            return bounce_rate

    df["bounce_rate"] = df["bounce_rate"].apply(lambda bounce_rate: convertToFloat(bounce_rate))
    df["bounce_rate"] = df["bounce_rate"].astype("float")

    return df
