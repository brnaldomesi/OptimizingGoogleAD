from __future__ import division

import uuid
from datetime import datetime

import pandas as pd

import common.functions as functions
import common.Database as Database
from common.Log import Log


def main(df, account_id, settings):
    Log("info", "processing campaign winning element", "", account_id)

    if functions.dfIsEmpty(df):
        return

    # add the campaign id, add path_1_path_2
    df = processDf(df, account_id, settings)

    lines = ["headline_1", "headline_2", "description", "path_1_path_2"]

    ldf = None
    # start lines loop
    for line in lines:
        this_df = df[["clicks", "impressions", "campaign_id", line]].groupby(["campaign_id", line]).sum()
        this_df["ctr"] = (this_df.clicks / this_df.impressions) * 100
        if functions.dfIsEmpty(this_df):
            break
        campaignIds = list(this_df.index.levels[0])
        this_df = this_df.reset_index()

        # start campaign ids loop
        for i, campaign_id in enumerate(campaignIds):
            # if i!=5:continue
            tdf = this_df.copy()
            quantile = tdf[(tdf.campaign_id == campaign_id)].impressions.quantile()
            tdf = tdf[(tdf.campaign_id == campaign_id) & (tdf.impressions > quantile) & (tdf[line] != "/")].sort_values(
                "ctr", ascending=False).head(3).reset_index(drop=True)

            if tdf.shape[0] == 0:
                continue

            tdf["order"] = tdf.index + 1
            tdf["type"] = line
            tdf.rename(columns={line: "value"}, inplace=True)
            tdf = tdf[["campaign_id", "value", "order", "type"]]

            try:
                ldf = ldf.append(tdf)
            except:
                ldf = tdf.copy()

    if functions.dfIsEmpty(ldf):
        return

    ldf = ldf.reset_index(drop=True)
    ldf.value = ldf.value.str.replace(" --", "")
    ldf["id"] = pd.Series([uuid.uuid1() for i in range(len(ldf))]).astype(str)
    # #now let's add created_at and updated_at as today
    ldf["created_at"] = datetime.now()
    ldf["updated_at"] = datetime.now()
    # for date_range in settings.date_ranges:
    date_range = "last_30_days"
    ldf["date_range"] = date_range
    ldf.to_sql("campaign_winning_elements", Database().createEngine(), if_exists='append', index=False)
    return df


def processDf(df, account_id, settings):
    # add the campaign id to the df
    query = "select campaign_id,id from adgroups where account_id = '%s'" % (account_id)
    adgroups = pd.read_sql_query(query, Database().createEngine())
    adgroups.rename(columns={"id": "adgroup_id"}, inplace=True)
    df = df.merge(adgroups, left_on="adgroup_id", right_on="adgroup_id")
    # empty paths should be just that, empty
    df.path_1 = df.path_1.str.replace(" --", "")
    df.path_2 = df.path_2.str.replace(" --", "")
    # campaign level top 3
    df["path_1_path_2"] = df["path_1"] + "/" + df["path_2"]
    return df
