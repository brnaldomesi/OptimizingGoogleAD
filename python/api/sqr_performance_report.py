# coding: utf-8
from __future__ import division

import warnings

import common.functions as functions
from api.Report import Report
from common.Log import Log

warnings.filterwarnings("ignore")

options = {
    "report_name": "SEARCH_QUERY_PERFORMANCE_REPORT",
    "performance_table_name": "search_query_performance",
    "entity_table_name": "search_queries",
    "entity_id_name": "search_query_id",

    "where_string": " where CampaignStatus = ENABLED and AdGroupStatus = ENABLED and Clicks > 0 ",

    "queryColumnsToTableColumns": {
        # 'CampaignId':"campaign_google_id",
        # 'AdGroupId':"adgroup_google_id",
        # 'KeywordId':"keyword_google_id",
        'Cost': "cost",
        'Impressions': "impressions",
        'Conversions': "conversions",
        'Clicks': "clicks",
        'ConversionValue': "conversion_value",
        'Query': 'query',
    },

    # map query column name to downloaded column names
    "queryColumnsToDownloadColumns": {
        # 'CampaignId':"Campaign ID",
        # 'AdGroupId':"Ad group ID",
        # 'KeywordId':"Keyword ID",
        'Cost': "Cost",
        'Impressions': "Impressions",
        'Conversions': "Conversions",
        'Clicks': "Clicks",
        'ConversionValue': "Total conv. value",
        'Query': "Search term"
    }

}


def main(account_id):
    Log("info", "getting search query performance from the api", "", account_id)

    report = Report(account_id, "", options)

    df = report.createDfWithAllDateRanges(account_id)

    if functions.dfIsEmpty(df):
        return

    df = df.groupby(['Search term', 'date_range'], as_index=False).sum()

    df["search_query_id"] = df.apply(lambda row: functions.addIdToDf(account_id, row["Search term"]), axis=1)
    df["id"] = df["search_query_id"]

    df = report.basicProcessing(df)

    report.writeToEntitiesTable(df, report, account_id)
    report.writeToPerformanceTable(df, report, account_id)

    return df
