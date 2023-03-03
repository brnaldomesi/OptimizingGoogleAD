#!/usr/bin/env python
from __future__ import division

import uuid
from datetime import datetime

import numpy as np
import pandas as pd

from common.Log import Log
from common.Settings import Settings
from common.Database import Database

settings = Settings()
import common.functions as functions


def main(account_id):
    Log("info", "processing adgroup feed", "", account_id)

    table_name = "adgroup_feed"

    deleteFromTable(table_name, account_id)

    df = getFeedInfoAllDateRanges(account_id)

    if functions.dfIsEmpty(df):
        print("df is empty")
        deleteFromTable(table_name, account_id)
        return

    df = functions.createUuid(df)
    df = functions.trimDfToTableColumns(df, table_name)

    df['display_from_date'] = datetime.now()

    functions.append_df_to_sql_table(df, table_name)


def deleteFromTable(table_name, account_id):
    delete_query = "delete from %s where account_id = '%s'" % (table_name, account_id)
    Database().executeQuery(delete_query)

def getFeedInfoAllDateRanges(account_id):
    all_df = None

    for date_range in settings.date_ranges:

        df = getAdGroups(account_id, date_range)

        if functions.dfIsEmpty(df):
            print("%s is empty" % (date_range))
            continue

        df = addFeedInfo(df)

        if functions.dfIsEmpty(all_df):
            all_df = df.copy()
        else:
            all_df = all_df.append(df.copy())

    return all_df


def addFeedInfo(df):
    df['headline'] = ""
    df['suggestion'] = ""

    df['headline'] = np.where(df['message'] == "has_winners", "We Have a Winner!", df.headline)
    df['headline'] = np.where(df['message'] == "too_few_ads", "More Ads Required", df.headline)
    df['headline'] = np.where(df['message'] == "too_many_ads", "Too Many Ads", df.headline)

    df['suggestion'] = np.where(df['message'] == "has_winners", "Pause the losing ad and create more", df.suggestion)
    df['suggestion'] = np.where(df['message'] == "too_few_ads", "Create more ads", df.suggestion)
    df['suggestion'] = np.where(df['message'] == "too_many_ads", "Pause low performing ads", df.suggestion)

    df['message'] = np.where(df['message'] == "has_winners", "Ad Group '" + df.name + "' has winning ads.", df.message)
    df['message'] = np.where(df['message'] == "too_few_ads", "Ad Group '" + df.name + "' needs more ads.", df.message)
    df['message'] = np.where(df['message'] == "too_many_ads", "Ad Group '" + df.name + "' has too many ads", df.message)

    df["id"] = pd.Series([uuid.uuid1() for i in range(len(df))]).astype(str)
    df["created_at"] = datetime.now()
    df["updated_at"] = datetime.now()
    df["display_from_date"] = datetime.now()
    df["priority"] = "warning"

    # this just looks at anything with a message, but we might not have given every message a headline, etc
    # filter those out
    df = df[df.headline != ""]

    return df


def getAdGroups(account_id, date_range):
    query = """SELECT adgroups.id,adgroups.name,message
    FROM adgroups
    join adgroup_performance
    where adgroups.id = adgroup_performance.adgroup_id
    and message != ""
    and clicks > 10
    and cost > 10
    and adgroups.account_id = '%s'
    and adgroup_performance.date_range = '%s'
    """ % (account_id, date_range)

    engine = Database().createEngine()
    # print query
    # need to update this (and all) queries to match the date_range we've set
    df = pd.read_sql_query(query, engine)
    df["adgroup_id"] = df["id"]
    df["account_id"] = account_id
    return df


if __name__ == '__main__':
    account_id = "1205f866-2eaa-4447-8fbd-8e440e768b59"
    main(account_id)
