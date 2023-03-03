import argparse
import traceback

import requests

from api.features.budget_commander.pause_enable_campaigns.EmergencyStop import \
    EmergencyStop
from api.Helpers import Helpers
from common.Log import Log

def main():
    """Runs emergency stop"""
    Log("info", "run_emergency_stop running", "Output to run_emergency_stop table")

    parser = argparse.ArgumentParser()
    parser.add_argument("-a")
    args = parser.parse_args()

    try:
        account_id = args.a
        if not account_id:
            print('Please specify an account id with -a')
            return
        if not Helpers().isActiveAccount(account_id):
            Log("info", "this account isn't active. Exiting", '', account_id)
            return
        EmergencyStop(account_id)
    except Exception as exception:
        Log("error", str(exception), traceback.format_exc(), account_id)
        raise


if __name__ == '__main__':
    requests.get('http://beats.envoyer.io/heartbeat/7nDk2k28qaQVJqk')
    main()
