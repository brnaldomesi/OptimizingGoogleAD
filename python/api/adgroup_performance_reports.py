# coding: utf-8
from __future__ import division

import warnings

import common.functions as functions
from api.Report import Report
from common.Log import Log

warnings.filterwarnings("ignore")

options = {
    "report_name": "ADGROUP_PERFORMANCE_REPORT",
    "performance_table_name": "adgroup_performance",
    "entity_table_name": "adgroups",
    "entity_id_name": "adgroup_id",

    "where_string": " where CampaignStatus = ENABLED and AdGroupStatus = ENABLED and AdGroupType = 'SEARCH_STANDARD' ",

    "queryColumnsToTableColumns": {
        'AdGroupId': "google_id",
        'CampaignId': "",  # won't be writing this
        'Cost': "cost",
        'Impressions': "impressions",
        'Conversions': "conversions",
        'Clicks': "clicks",
        'ConversionValue': "conversion_value",
        'AdGroupName': 'name',
        'AdGroupStatus': 'status',
    },

    # map query column name to downloaded column names
    "queryColumnsToDownloadColumns": {
        'AdGroupId': "Ad group ID",
        'CampaignId': "Campaign ID",
        'Cost': "Cost",
        'Impressions': "Impressions",
        'Conversions': "Conversions",
        'Clicks': "Clicks",
        'ConversionValue': "Total conv. value",
        'AdGroupName': "Ad group",
        'AdGroupStatus': "Ad group state",
    }

}


def main(account_id):
    Log("info", "getting adgroup performance from the api", "", account_id)

    report = Report(account_id, "", options)

    df = report.createDfWithAllDateRanges(account_id)

    if functions.dfIsEmpty(df):
        return

    # remember column headers are as per the download here
    df["adgroup_id"] = df.apply(lambda row: functions.addIdToDf(account_id, row["Ad group ID"], row["Campaign ID"]), axis=1)
    df["id"] = df["adgroup_id"]

    df = addParentId(df, account_id)  # our UUID from the campaigns table

    df = report.basicProcessing(df)

    df = filterExperimentCampaigns(df)

    report.writeToEntitiesTable(df, report, account_id)

    report.deleteAllPerformance()

    report.writeToPerformanceTable(df, report, account_id)


def addParentId(df, account_id):
    """hex of id (if there is one) and parent id
    the parent (campaign) used the same method so they match up"""
    df["campaign_id"] = df.apply(lambda row: functions.addIdToDf(account_id,row["Campaign ID"]), axis=1)
    return df


def filterExperimentCampaigns(df):
    """It isn't possble to filter experiments/drafts from the adgroup report
    Instead we'll remove anything which does not have a matching campaign in the db"""

    return df
