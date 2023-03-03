#!/usr/bin/env python
from __future__ import division

import uuid

import pandas as pd

import common.functions as functions
from api.Report import Report
from common.Log import Log
from common.Settings import Settings
from common.Database import Database

options = {
    "report_name": "ACCOUNT_PERFORMANCE_REPORT",
    "performance_table_name": "account_performance",
    "entity_table_name": "accounts",
    "entity_id_name": "account_id",
    # after the initial run we'll only want to add yesterday's data
    "date_range": "LAST_30_DAYS",

    "where_string": " ",

    "queryColumnsToTableColumns": {
        'Cost': "cost",
        'Impressions': "impressions",
        'Conversions': "conversions",
        'Clicks': "clicks",
        'ConversionValue': "conversion_value",

    },

    # map query column name to downloaded column names
    "queryColumnsToDownloadColumns": {
        'Cost': "Cost",
        'Impressions': "Impressions",
        'Conversions': "Conversions",
        'Clicks': "Clicks",
        'ConversionValue': "Total conv. value",
    }

}


def main(account_id):
    Log("info", "getting account performance from the api", "", account_id)
    report = Report(account_id, "last_30_days", options)

    report.createAccountDirectory()

    report.createReportDirectory()

    report.downloadReport(account_id, options["where_string"])

    df = report.convertCsvToDataframe()

    if functions.dfIsEmpty(df):
        return

    df = report.basicProcessing(df)

    df = reportSpecificProcessing(df, account_id)

    deleteExitingData(account_id, options["performance_table_name"])

    report.writeDataframeToTable(df, options["performance_table_name"])


def deleteExitingData(account_id, table_name):
    query = "delete from %s where account_id = '%s'" % (table_name, account_id)
    Database().executeQuery(query)


def reportSpecificProcessing(df, account_id):
    df["date_range"] = "last_30_days"
    df["account_id"] = account_id
    df.reset_index(inplace=True)
    df["id"] = pd.Series([uuid.uuid1() for i in range(len(df))]).astype(str)

    # #add the calculated metrics (ctr, etc)
    settings = Settings()
    for metric in settings.calculatedMetrics:
        if settings.calculatedMetrics[metric][1] == "/":
            df[metric] = df[settings.calculatedMetrics[metric][0]] / \
                         df[settings.calculatedMetrics[metric][2]]
        if settings.calculatedMetrics[metric][3]:
            df[metric] = df[metric] * 100

    return df


def downloadAccountPerformanceReport(account_id):
    main(account_id)


if __name__ == '__main__':
    account_id = "1205f866-2eaa-4447-8fbd-8e440e768b59"
    downloadAccountPerformanceReport(account_id)
