#!/usr/bin/env python
from __future__ import division

import os
import traceback
import warnings
from datetime import datetime

from jinja2 import Template

from api import (account_performance_report, account_performance_reports,
                 ad_performance_report, adgroup_performance_reports,
                 campaign_performance_reports, filter_out_draft_campaigns,
                 keyword_performance_report, sqr_performance_report)
from api.features.budget_commander.BudgetCommander import BudgetCommander
from api.features.budget_commander.ControlSpend import ControlSpend
from common.Database import Database
from common.notifications.NotificationController import NotificationController
from common.helpers.LocalDates import LocalDates
from common.Log import Log
from common.Settings import Settings
from data_processing.main import processAccount

settings = Settings()

warnings.filterwarnings("ignore")
# currentdir = os.path.dirname(os.path.abspath(inspect.getfile(inspect.currentframe())))
# parentdir = os.path.dirname(currentdir)
# common_dir = os.path.abspath(os.path.join(parentdir, "common"))
# sys.path.insert(0,common_dir)


runAllAccounts = False  # process all accounts regardless of completed_at date

downloadData = True

delete_existing_data = False  # delete existing data before downloading?


def downloadAccount(account_id):
    if delete_existing_data:
        deleteExistingData(account_id)

    if downloadData or settings.envvars["APP_ENV"] == "production":

        account_performance_report.downloadAccountPerformanceReport(account_id)

        account_performance_reports.main(account_id)

        campaign_performance_reports.main(account_id)

        adgroup_performance_reports.main(account_id)

        # sqr_performance_report.main(account_id)

        ad_performance_report.main(account_id)

        #before downloading keywords and replacing their bids, revert back to original bids in the account
        ControlSpend(account_id).revertToOriginalBids()
        
        keyword_performance_report.main(account_id)

        filter_out_draft_campaigns.main(account_id)

    else:
        Log("warning", "downloadData set to false", "", account_id)

    Log("info", "API reports downloded and stored successfully", "", account_id)

    # runs main.py function
    processAccount(account_id)

    Log("info", "finished downloading and processing the account", "", account_id)


def deleteExistingData(account_id):
    reports = [
        "ad_performance_reports",

    ]
    for report in reports:
        query = "delete from %s where account_id = '%s'" % (report, account_id)
        Database().createEngine().execute(query)


def main(account_id):
    """Sparks the downloads and data processing
    returns (stops) if the account_id has already run in this window (once per day)
    """
    Log("info", "downloading and processing", "", account_id)
    start_time = datetime.now()

    try:
        downloadAccount(account_id)
    except Exception:
        msg = 'Failed to download and process the account %s' % (account_id)
        Log("error", msg, traceback.format_exc(), account_id)
        if Settings().isFirstRun(account_id):
            NotificationController(account_id).accountError()
        return

    createNotification(account_id)

    addProcessedAtTimestamp(account_id)
    addUpdatedAtTimestamp(account_id)

    Log("info", "download.py completed in " +
        str(datetime.now() - start_time), "", account_id)

def addProcessedAtTimestamp(account_id):
    local_dates = LocalDates(account_id)
    today = local_dates.toString(local_dates.today)
    Log('info', "storing local time: %s"%(today), '', account_id)
    query = "update accounts set ad_performance_report_processed_at = '%s' where id = '%s'" % (
        today, account_id)
    Database().executeQuery(query)

def addUpdatedAtTimestamp(account_id):
    """Update the updated_at timestamp to the current, server time"""
    query = "update accounts set updated_at = '%s' where id = '%s'" % (
        datetime.now().strftime('%Y-%m-%d %H:%M:%S'), account_id)
    Database().executeQuery(query)

def createNotification(account_id):

    if not settings.isFirstRun(account_id):
        Log('info', "the notification won't be created as this isn't the first run", '', account_id)
        return

    Log('info', "creating successful sync notification", '', account_id)

    NotificationController(account_id).accountSyncSuccess()

if __name__ == '__main__':
    account_id = ""
    main(account_id)
