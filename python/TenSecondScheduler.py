import time

from tendo import singleton

import common.schedule as schedule  # timezone version
from common.Log import Log
from common.Process import Process as myProcess


class TenSecondScheduler:
    """Run every 10 seconds
    Runs every_ten_seconds.py which in-turn works the mutations queue
    """

    def every_ten_seconds(self):
        """Adding a middle man so we can log if the schedule worked"""
        myProcess().main('every_ten_seconds.py')

    def main(self):
        schedule.every(10).seconds.do(self.every_ten_seconds)
        while True:
            schedule.run_pending()
            time.sleep(1)


if __name__ == "__main__":
    me = singleton.SingleInstance()  # will sys.exit(-1) if other instance is running
    TenSecondScheduler().main()
