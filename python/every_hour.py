import argparse
import time
import traceback
from datetime import datetime

import pandas as pd
import pytz
import requests

from api import download
from api.features.budget_commander.BudgetGroups import BudgetGroups

from api.Helpers import Helpers
from common.Database import Database
from common.Log import Log
from schedule.Schedule import Schedule

def main():
    """Runs every hour via cron
    In turn:
    Once per night:
     - Processes accounts
     - Runs Budget Commander features (Email, Monthly Pause, Control Spend)
    Every hour:
     - Runs Emergency stop
    """
    Log("info", "every_hour running", "Output to python_log table")

    account_ids = Schedule().getSyncedAccountIds()

    for account_id in account_ids:
        runProcessAccountAndBudgetCommander(account_id)
        runEmergencyStop(account_id)


def runProcessAccountAndBudgetCommander(account_id):
    """Run the account once the time is after 3am (local time) but only run once per day
    Run Budget Commander features after processing because they use the data from the download
    """

    if not Schedule().accountShouldRun(account_id):
        return

    if Helpers().isActiveAccount(account_id):
        try:
            download.main(account_id)
            BudgetGroups(account_id).monthlyStop()
            BudgetGroups(account_id).notifyViaEmail()
        except Exception as exception:
            Log("error", str(exception), traceback.format_exc(), account_id)


def runEmergencyStop(account_id):
    if Helpers().isActiveAccount(account_id):
        try:
            BudgetGroups(account_id).emergencyStop()
        except Exception as exception:
            Log("error", str(exception), traceback.format_exc(), account_id)



if __name__ == '__main__':
    requests.get('http://beats.envoyer.io/heartbeat/N7IkNTSDXM0tH3P')
    main()  
