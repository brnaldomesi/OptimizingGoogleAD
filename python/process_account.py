# -*- coding: utf-8 -*-
import argparse
import os
import traceback

import requests

from api import download
from api.Helpers import Helpers
from common.Log import Log
from common.Process import Process as myProcess
from common.Settings import Settings


def main():
    """Download data from the api
    Process the data ready for the app"""
    
    Log("info", "process_account running", "from process_account.py")

    parser = argparse.ArgumentParser()
    parser.add_argument("-a")
    args = parser.parse_args()

    try:
        account_id = args.a
        if(not account_id):
            Log('info', 'Please specify an account id with -a')
            return
        if not Helpers().isActiveAccount(account_id):
            Log("info", "this account isn't active. Exiting", '', account_id)
            return
        download.main(account_id)
    except:
        Log("error", "error starting process_account from command line", traceback.format_exc())
        raise
    
    Log("info", "firing run_budget_commander command", '', account_id)
    myProcess().main("run_budget_commander.py", account_id)


if __name__ == "__main__":
    requests.get('http://beats.envoyer.io/heartbeat/LXvuOsCsIWaDFSp')
    main()
