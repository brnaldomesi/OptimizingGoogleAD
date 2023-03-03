# coding: utf-8
from __future__ import division

import warnings

import pandas as pd

import common.functions as functions
from api.Report import Report
from common.Database import Database
from common.Log import Log

warnings.filterwarnings("ignore")

options = {
    "report_name": "CAMPAIGN_PERFORMANCE_REPORT",
    "performance_table_name": "campaign_performance",
    "entity_table_name": "campaigns",
    "entity_id_name": "campaign_id",

    "where_string": " ",

    "queryColumnsToTableColumns": {
        'CampaignId': "google_id",
        'Cost': "cost",
        'Impressions': "impressions",
        'Conversions': "conversions",
        'Clicks': "clicks",
        'ConversionValue': "conversion_value",
        'CampaignName': 'name',
        'CampaignStatus': 'status',
        'AdvertisingChannelSubType': 'advertising_channel_sub_type',
        'AdvertisingChannelType': 'advertising_channel_type',
        'CampaignTrialType': 'campaign_trial_type',
        'BiddingStrategyType': 'bidding_strategy_type',
        'BiddingStrategyName': 'bidding_strategy_name',
        'BiddingStrategyId': 'bidding_strategy_id',
        'BudgetId': 'budget_id',
        'IsBudgetExplicitlyShared': 'is_budget_explicitly_shared',
        'TotalAmount': 'total_amount',
        'CampaignDesktopBidModifier': 'campaign_desktop_bid_modifier',
        'CampaignMobileBidModifier': 'campaign_mobile_bid_modifier',
        'CampaignTabletBidModifier': 'campaign_tablet_bid_modifier',
        'StartDate': 'start_date',
        'EndDate': 'end_date',
        'EnhancedCpcEnabled': 'enhanced_cpc_enabled',
        'FinalUrlSuffix': 'final_url_suffix',
        'HasRecommendedBudget': 'has_recommended_budget',
        'ServingStatus': 'serving_status',
        'UrlCustomParameters': 'url_custom_parameters',
        'TrackingUrlTemplate': 'tracking_url_template',
        'Date': 'date',
    },

    # map query column name to downloaded column names
    "queryColumnsToDownloadColumns": {
        'CampaignId': "Campaign ID",
        'Cost': "Cost",
        'Impressions': "Impressions",
        'Conversions': "Conversions",
        'Clicks': "Clicks",
        'ConversionValue': "Total conv. value",
        'CampaignName': "Campaign",
        'CampaignStatus': "Campaign state",
        'AdvertisingChannelSubType': 	'Advertising Sub Channel',
        'AdvertisingChannelType': 	'Advertising Channel',
        'CampaignTrialType': 	'Campaign Trial Type',
        'BiddingStrategyType': 	'Bid Strategy Type',
        'BiddingStrategyName': 	'Bid Strategy Name',
        'BiddingStrategyId': 	'Bid Strategy ID',
        'BudgetId': 	'Budget ID',
        'IsBudgetExplicitlyShared': 	'Budget explicitly shared',
        'TotalAmount': 	'Total Budget amount',
        'CampaignDesktopBidModifier': 	'Desktop bid adj.',
        'CampaignMobileBidModifier': 	'Mobile bid adj.',
        'CampaignTabletBidModifier': 	'Tablet bid adj.',
        'StartDate': 	'Start date',
        'EndDate': 	'End date',
        'EnhancedCpcEnabled': 	'Enhanced CPC enabled',
        'FinalUrlSuffix': 	'Final URL suffix',
        'HasRecommendedBudget': 	'Has recommended Budget',
        'ServingStatus': 	'Campaign serving status',
        'UrlCustomParameters': 	'Custom parameter',
        'TrackingUrlTemplate': 	'Tracking template',
        'Date': 	'Day',
    }

}


def main(account_id, get_today_alone=False):
    """get_today_alone is whether only download today's data
    If True this will ONLY download today's data and entities (campaigns table data) won't be stored 
    if False this will get all date ranges"""

    Log("info", "getting campaign performance from the api", "", account_id)

    if not get_today_alone:
        createReport('last_112_days', account_id)

    createReport('TODAY', account_id)

def createReport(date_range, account_id):

    report = Report(account_id, date_range, options)

    report.createAccountDirectory()

    report.createReportDirectory()

    report.downloadReport(account_id, options["where_string"])

    df = report.convertCsvToDataframe()

    if(functions.dfIsEmpty(df)):
        return
        

    df["campaign_id"] = df.apply(lambda row: functions.addIdToDf(account_id, row["Campaign ID"]), axis=1)
    df['date_range'] = date_range

    df = report.basicProcessing(df)

    report.deleteDateRangePerformance(date_range)
    report.writeToPerformanceTable(df, report, account_id)

    if date_range == 'last_112_days':
        writeToEntitiesTable(df, report, account_id, 'campaigns')


def writeToEntitiesTable(df, report, account_id, entity_table_name):

    budget_group_ids = pd.read_sql("select id, budget_group_id from campaigns where account_id = '%s'" %(account_id),Database().createEngine())
    df['id'] = df["campaign_id"]
    df = df.merge(budget_group_ids, on="id", how="outer")
    
    delete_query = "delete from %s where account_id = '%s'" % (entity_table_name, account_id)
    Database().executeQuery(delete_query)

    # we only need to write the keyword data in once
    # for the longest range to cover all keywords
    df = functions.trimDfToTableColumns(df, 'campaigns')
    df = df.reset_index(drop=True).drop_duplicates('id')
    
    functions.append_df_to_sql_table(df, entity_table_name)
