import inspect
import os
import sys

import pandas as pd

currentdir = os.path.dirname(os.path.abspath(inspect.getfile(inspect.currentframe())))
parentdir = os.path.dirname(currentdir)
sys.path.insert(0, parentdir)

from common.Settings import Settings


def fileSplit():
    settings = Settings()

    fileName = "Search keyword.csv"
    keywordReportLocation = os.path.join(settings.this_dir, "scorer", fileName)
    keywordReportLocation
    df = pd.read_csv(keywordReportLocation, encoding="utf-8", skiprows=2)

    for account in df["Account"].drop_duplicates().values:
        this_df = df[df.Account == account]
        writeLocation = os.path.join(settings.this_dir, "scorer", "reports", account + ".csv")
        try:
            this_df.to_csv(writeLocation, index=False, encoding="utf-8")
        except:
            pass


if __name__ == '__main__':
    fileSplit()
