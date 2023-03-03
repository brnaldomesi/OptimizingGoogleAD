from __future__ import division

import uuid
from datetime import datetime

import pandas as pd

from common.Log import Log
from common.Database import Database


def accountWinningElements(df, account_id, settings):
    Log("info", "processing account winning elements", "", account_id)

    if df is None:
        return
    if len(df) == 0:
        return
    # account level top 3
    account_winning_elements = ""
    df["path_1_path_2"] = df["path_1"] + "/" + df["path_2"]
    lines = ["headline_1", "headline_2", "description", "path_1_path_2"]
    for line in lines:
        print("running line " + line)
        this_df = df[["clicks", "impressions", line]].groupby([line]).sum()
        this_df["type"] = line
        this_df["ctr"] = (this_df.clicks / this_df.impressions) * 100
        median = this_df.impressions.median()
        this_df = this_df[(this_df.impressions >= median) & (this_df.index != "/")].sort_values("ctr",
                                                                                                ascending=False).ix[
                  0:3].reset_index()
        this_df.rename(columns={line: "value"}, inplace=True)
        # add "order" column
        this_df = this_df.sort_values("ctr", ascending=False)
        this_df["order"] = this_df.index + 1
        # then we just need value, type and order
        this_df = this_df[["value", "type", "order"]]
        try:
            account_winning_elements = account_winning_elements.append(this_df.copy())
        except:
            account_winning_elements = this_df.copy()

    account_winning_elements["account_id"] = account_id

    account_winning_elements.value = account_winning_elements.value.str.replace(" --", "")
    # #now let's add created_at and updated_at as today
    account_winning_elements["created_at"] = datetime.now()
    account_winning_elements["updated_at"] = datetime.now()
    # Note: just add the date range manually for now, but they can be looped through at a later date
    # for date_range in settings.date_ranges:
    date_range = "last_30_days"
    account_winning_elements["date_range"] = date_range
    # add the unique id field
    account_winning_elements.reset_index(inplace=True, drop=True)
    account_winning_elements["id"] = pd.Series([uuid.uuid1() for i in range(len(account_winning_elements))]).astype(str)
    account_winning_elements.to_sql(
        "account_winning_elements", Database().createEngine(), if_exists='append', index=False
    )
