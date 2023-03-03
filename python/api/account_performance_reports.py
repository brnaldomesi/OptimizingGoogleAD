#!/usr/bin/env python
from __future__ import division

import inspect
import os
import sys
import uuid

import pandas as pd

import common.functions as functions
from api.Report import Report
from common.Log import Log
from common.Settings import Settings
from common.Database import Database
from common.notifications.NotificationController import NotificationController

currentdir = os.path.dirname(os.path.abspath(inspect.getfile(inspect.currentframe())))
parentdir = os.path.dirname(currentdir)
sys.path.insert(0, parentdir)

settings = Settings()

options = {
    "report_name": "ACCOUNT_PERFORMANCE_REPORT",
    "performance_table_name": "account_performance_reports",
    "entity_table_name": "",
    "entity_id_name": "",
    "date_range": "last_112_days",

    "where_string": " ",

    "queryColumnsToTableColumns": {
        'Cost': "cost",
        'Impressions': "impressions",
        'Conversions': "conversions",
        'Clicks': "clicks",
        'ConversionValue': "conversion_value",
        'Date': "date",
    },

    # map query column name to downloaded column names
    "queryColumnsToDownloadColumns": {
        'Cost': "Cost",
        'Impressions': "Impressions",
        'Conversions': "Conversions",
        'Clicks': "Clicks",
        'ConversionValue': "Total conv. value",
        'Date': 'Day'
    }
}


def main(account_id):
    Log("info", "getting account performance reports from the api", "", account_id)

    report = Report(account_id, options["date_range"], options)

    report.createAccountDirectory()

    report.createReportDirectory()

    report.downloadReport(account_id, options["where_string"])

    df = report.convertCsvToDataframe()

    if(functions.dfIsEmpty(df)):
        if Settings().isFirstRun(account_id):
            NotificationController(account_id).emptyAccount()
        return
    
    df = report.basicProcessing(df)

    df = reportSpecificProcessing(df, account_id)


    deleteExitingData(account_id, options["performance_table_name"])

    report.writeDataframeToTable(df, options["performance_table_name"])


def deleteExitingData(account_id, database_table_name):
    query = "delete from %s where account_id = '%s'" % (database_table_name, account_id)
    Database().executeQuery(query)


def reportSpecificProcessing(df, account_id):
    df["date_range"] = options["date_range"]
    df["account_id"] = account_id
    df.reset_index(inplace=True)
    df["id"] = pd.Series([uuid.uuid1() for i in range(len(df))]).astype(str)
    return df
