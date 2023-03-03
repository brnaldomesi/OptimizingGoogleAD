# coding: utf-8
from __future__ import division

import uuid

import pandas as pd

import common.functions as functions
from api.Report import Report
from common.Database import Database
from common.Log import Log
from common.Settings import Settings

options = {
    "report_name": "AD_PERFORMANCE_REPORT",
    "performance_table_name": "advert_performance",
    "entity_table_name": "adverts",
    "entity_id_name": "advert_id",
    # after the initial run we'll only want to add yesterday's data

    "where_string": " where Status in [ENABLED,PAUSED] and CampaignStatus = ENABLED and AdGroupStatus = ENABLED and AdType in [EXPANDED_TEXT_AD, RESPONSIVE_SEARCH_AD] ",

    "queryColumnsToTableColumns": {
        # 'Date':"date",
        'CampaignId': "campaign_google_id",
        'CampaignName': "campaign_name",
        'CampaignStatus': "campaign_status",
        'AdGroupId': "adgroup_google_id",
        'AdGroupStatus': "adgroup_status",
        'Cost': "cost",
        'Impressions': "impressions",
        'Conversions': "conversions",
        'Clicks': "clicks",
        'ConversionValue': "conversion_value",
        'AdGroupName': "adgroup_name",
        'HeadlinePart1': "headline_1",
        'HeadlinePart2': "headline_2",
        'ExpandedTextAdHeadlinePart3': "headline_3",
        'Description': "description",
        'ExpandedTextAdDescription2': "description_2",
        'Path1': "path_1",
        'Path2': "path_2",
        'CreativeFinalUrls': "final_urls",
        'Id': "advert_google_id",
        'Status': "advert_status",
        'AdType': 'ad_type',
        'ResponsiveSearchAdDescriptions': 'responsive_search_ad_descriptions',
        'ResponsiveSearchAdHeadlines': 'responsive_search_ad_headlines',
        'ResponsiveSearchAdPath1': 'responsive_search_ad_path_1',
        'ResponsiveSearchAdPath2': 'responsive_search_ad_path_2',
        'ShortHeadline': 'short_headline',
        'AdStrengthInfo': 'ad_strength_info',
        'Automated': 'automated',
        'CombinedApprovalStatus': 'combined_approval_status',
        'CreativeFinalMobileUrls': 'creative_final_mobile_urls',
        'CreativeFinalUrlSuffix': 'creative_final_url_suffix',
        'DevicePreference': 'device_preference',
        'PolicySummary': 'policy_summary',

    },

    # map query column name to downloaded column names
    "queryColumnsToDownloadColumns": {
        # 'Date':"Day",
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
        'AdGroupName': "Ad group",
        'HeadlinePart1': "Headline 1",
        'HeadlinePart2': "Headline 2",
        'ExpandedTextAdHeadlinePart3': "Expanded Text Ad Headline 3",
        'Description': "Description",
        'ExpandedTextAdDescription2': "Expanded Text Ad Description 2",
        'Path1': "Path 1",
        'Path2': "Path 2",
        'CreativeFinalUrls': "Final URL",
        'Id': "Ad ID",
        'Status': "Ad state",
        'AdType': 'Ad type',
        'ResponsiveSearchAdDescriptions': 'Responsive Search Ad descriptions',
        'ResponsiveSearchAdHeadlines': 'Responsive Search Ad headlines',
        'ResponsiveSearchAdPath1': 'Responsive Search Ad path 1',
        'ResponsiveSearchAdPath2': '	Responsive Search Ad path 2',
        'ShortHeadline': 'Short headline',
        'AdStrengthInfo': 'Ad strength',
        'Automated': '	Auto-applied ad suggestion',
        'CombinedApprovalStatus': 'Approval status',
        'CreativeFinalMobileUrls': 'Mobile final URL',
        'CreativeFinalUrlSuffix': 'Final URL suffix',
        'DevicePreference': 'Device preference',
        'PolicySummary': 'Policy',
    }

}


def main(account_id):
    Log("info", "getting ad performance from the api", "", account_id)

    settings = Settings()
    for date_range in settings.date_ranges:

        report = Report(account_id, date_range, options)

        report.createAccountDirectory()

        report.createReportDirectory()

        report.downloadReport(account_id, options["where_string"])

        df = report.convertCsvToDataframe()

        if functions.dfIsEmpty(df):
            continue

        df = report.basicProcessing(df)

        df = reportSpecificProcessing(df, date_range, account_id)

        deleteExitingData(account_id, date_range, 'ad_performance_reports')

        report.writeDataframeToTable(df, 'ad_performance_reports')

        deleteExitingData(account_id, date_range, 'advert_performance')
        report.writeDataframeToTable(df, 'advert_performance')

    if functions.dfIsEmpty(df):
        return

    if 'advert_id' not in df.columns:
        Log('error',  'advert_id not in df columns', df.columns, account_id)
        return

    df["id"] = df["advert_id"]
    df['google_id'] = df['advert_google_id']
    df['status'] = df['advert_status']
    df = addParentId(df, account_id)  # our UUID from the adverts table
    report.writeToEntitiesTable(df, report, account_id)  # add the final date range data to adverts


def addParentId(df, account_id):
    df["adgroup_id"] = df.apply(lambda row: functions.addIdToDf(account_id, row["adgroup_google_id"], row["campaign_google_id"]), axis=1)
    return df


def deleteExitingData(account_id, date_range, database_table_name):
    query = "delete from %s where account_id = '%s' and date_range = '%s'" % (
    database_table_name, account_id, date_range)
    Database().executeQuery(query)


def reportSpecificProcessing(df, date_range, account_id):
    df.reset_index(inplace=True)
    df["id"] = pd.Series([uuid.uuid1() for i in range(len(df))]).astype(str)
    df["date_range"] = date_range

    df["advert_id"] = df.apply(lambda row: functions.addIdToDf(account_id, row["advert_google_id"], row["adgroup_google_id"]), axis=1)

    return df
