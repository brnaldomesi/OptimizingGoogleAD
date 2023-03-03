#!/usr/bin/env python

from __future__ import division

import os
import time
import uuid
from datetime import datetime

import pandas as pd

import common.functions as functions
from api.Helpers import Helpers
from common.Database import Database
from common.GoogleAdsApi import GoogleAdsApi
from common.Log import Log
from common.Settings import Settings
from common.helpers.LocalDates import LocalDates


class Report:

    def __init__(self, account_id, date_range_string, options):
        self.options = options
        self.account_id = account_id
        self.date_range_string = date_range_string
        self.save_report_as_name = self.date_range_string + ".csv"
        self.report_name = options["report_name"]
        self.performance_table_name = options["performance_table_name"]
        self.entity_table_name = options["entity_table_name"]
        self.entity_id_name = options["entity_id_name"]
        self.where_string = options["where_string"]
        self.queryColumnsToTableColumns = options["queryColumnsToTableColumns"]
        self.queryColumnsToDownloadColumns = options["queryColumnsToDownloadColumns"]
        self.settings = Settings()
        self.helpers = Helpers()
        self.moneyFields = ["cost"]
        self.rate_errors = 0

    def createAccountDirectory(self):
        write_directory = os.path.abspath(os.path.join(self.settings.storage_dir, self.account_id))
        if not os.path.exists(write_directory):
            os.makedirs(write_directory)

    def createReportDirectory(self):
        report_directory = os.path.abspath(os.path.join(self.settings.storage_dir, self.account_id, self.report_name))
        if not os.path.exists(report_directory):
            os.makedirs(report_directory)

    def downloadReport(self, account_id, where_string):

        settings = Settings()
        if settings.envvars["SKIP_DOWNLOADS"] == "true":
            print("SKIP_DOWNLOADS set to true in the env, skipping the download")
            return

        columns = ",".join(self.queryColumnsToTableColumns.keys())

        client_customer_id = self.helpers.getClientCustomerID(self.settings, account_id)

        client = GoogleAdsApi.getClient(None, account_id, client_customer_id)

        if not client:
            Log('warning', "tried running Report but there's no client. Exiting", "This is probably due to a missing data such as refresh token", account_id)
            return

        # Initialize appropriate service.
        report_downloader = client.GetReportDownloader(version='v201809')

        report_query = "select %s from %s" % (columns, self.report_name)
        report_query += " " + where_string
        report_query += " during %s" % (functions.dateRangeFromDays(self.date_range_string, account_id))
        write_path = os.path.abspath(
            os.path.join(self.settings.storage_dir, self.account_id, self.report_name, self.save_report_as_name))

        with open(write_path, 'w', encoding='utf-8') as output_file:
            try:
                report_downloader.DownloadReportWithAwql(
                    report_query, 'CSV', output_file, skip_report_header=True,
                    skip_column_header=False, skip_report_summary=True,
                    include_zero_impressions=True, client_customer_id=client_customer_id)

            except Exception as exception:
                if str(exception).find("ZERO_IMPRESSIONS_REQUEST_NOT_SUPPORTED") > -1:
                    report_downloader.DownloadReportWithAwql(
                        report_query, 'CSV', output_file, skip_report_header=True,
                        skip_column_header=False, skip_report_summary=True, client_customer_id=client_customer_id)
                elif str(exception).lower().find("rate") > -1:
                    # rate exceeded error exponential backoff - the 10th error will be 55 minutes
                    Log("error", str(exception), "tries: %s" % (self.rate_errors), self.account_id)

                    time.sleep((self.rate_errors * (self.rate_errors + 1)) * 30)
                    self.downloadReport(account_id, where_string)
                    self.rate_errors += 1

                else:
                    raise

    def convertCsvToDataframe(self):

        write_directory = os.path.abspath(os.path.join(self.settings.storage_dir, self.account_id, self.report_name))
        write_path = os.path.abspath(os.path.join(write_directory, self.save_report_as_name))

        df = pd.read_csv(write_path)

        return df

    def basicProcessing(self, df):
        downloadColumnsToTableColumns = {}
        for col in self.queryColumnsToDownloadColumns:
            downloadColumn = self.queryColumnsToDownloadColumns[col]
            tableColumn = self.queryColumnsToTableColumns[col]
            downloadColumnsToTableColumns[downloadColumn] = tableColumn

        df.reset_index(inplace=True)
        df["created_at"] = datetime.now()
        df["updated_at"] = datetime.now()
        df["account_id"] = self.account_id

        df = df.rename(columns=downloadColumnsToTableColumns)

        decimal_columns = [
            'campaign_desktop_bid_modifier',
            'campaign_mobile_bid_modifier', 
            'campaign_tablet_bid_modifier', 
            'total_amount']
        for decimal_column in decimal_columns:
            if decimal_column in df.columns:
                df[decimal_column] = df[decimal_column].str.replace(' --', '0')
                df[decimal_column] = df[decimal_column].str.replace('%', '')

        for field in self.moneyFields:
            if field in list(df.columns):
                df[field] = df[field] / 1000000

        # #add the calculated metrics (ctr, etc)
        settings = Settings()
        for metric in settings.calculatedMetrics:
            operator = settings.calculatedMetrics[metric][1]
            first_metric = settings.calculatedMetrics[metric][0]
            second_metric = settings.calculatedMetrics[metric][2]
            if first_metric not in df.columns:
                continue
            if second_metric not in df.columns:
                continue

            if operator == "/":
                df[metric] = df[first_metric] / \
                             df[second_metric]
                continue 
            if settings.calculatedMetrics[metric][3]:
                df[metric] = df[metric] * 100

        return df

    # entities table such as adverts, keywords i.e. not performance
    def writeToEntitiesTable(self, df, report, account_id):
        settings = Settings()

        delete_query = "delete from %s where account_id = '%s'" % (self.entity_table_name, account_id)
        Database().executeQuery(delete_query)

        # we only need to write the keyword data in once
        # for the longest range to cover all keywords
        final_date_range = settings.date_ranges[len(settings.date_ranges) - 1]
        df = df[df.date_range == final_date_range]
        df['account_id'] = account_id
        
        df = df.reset_index(drop=True).drop_duplicates()

        report.writeDataframeToTable(df, self.entity_table_name)

    def deleteAllPerformance(self):
        """Remove all performance (ready to be overwritten)"""
        delete_query = "delete from %s where account_id = '%s' " % (self.performance_table_name, self.account_id)
        Database().executeQuery(delete_query)

    def deleteDateRangePerformance(self, date_range):
        """Remove all performance for a specific date range (ready to be overwritten)"""
        delete_query = "delete from %s where account_id = '%s' " % (self.performance_table_name, self.account_id)
        delete_query += "and date_range = '%s'" %(date_range)
        Database().executeQuery(delete_query)
 
    def writeToPerformanceTable(self, df, report, account_id):
        df["id"] = pd.Series([uuid.uuid1() for i in range(len(df))]).astype(str)
        report.writeDataframeToTable(df, self.performance_table_name)

    def writeDataframeToTable(self, df, database_table_name):
        df = functions.trimDfToTableColumns(df, database_table_name)
        functions.append_df_to_sql_table(df, database_table_name)

    def stringifyIds(self, df):
        for column in df.columns:
            if "id" in column.lower():
                df[column] = df[column].astype(str)

        return df

    def createDfWithAllDateRanges(self, account_id, get_today_alone=False):
        all_df = None

        settings = Settings()
        for date_range in settings.date_ranges:

            if date_range=="THIS_MONTH" and LocalDates(account_id).is_first_of_month:
                continue

            if get_today_alone and date_range!="TODAY":
                continue

            report = Report(account_id, date_range, self.options)
            report.createAccountDirectory()
            report.createReportDirectory()
            report.downloadReport(account_id, report.where_string)

            df = report.convertCsvToDataframe()

            df["date_range"] = date_range

            if df.shape[0] == 0:
                print("%s df is empty" % (date_range))
                continue

            if functions.dfIsEmpty(all_df):
                all_df = df.copy()
            else:
                all_df = all_df.append(df.copy())

        if functions.dfIsEmpty(all_df):
            Log("info", "%s report is empty" % (self.report_name), "", self.account_id)
            return

        all_df = report.stringifyIds(all_df)

        return all_df.reset_index()
