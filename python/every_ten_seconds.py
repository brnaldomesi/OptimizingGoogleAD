from time import sleep

import requests

from api.mutations_queue_workers.Worker import Worker
from common.Log import Log


def main():
    """This runs every 10 second via TenSecondScheduler.py
    Work the mutations queue (make changes via the AdWords API)
    """
    # Log("info", "every_ten_seconds running")
    Worker().work()
    sleep(5)
    Worker().check()

if __name__ == '__main__':
    requests.get('http://beats.envoyer.io/heartbeat/cBL7zHIMti09AGV')
    main()
