#!/usr/bin/env python
from __future__ import division

from datetime import datetime

import pandas as pd

from common.Log import Log
from common.Settings import Settings
settings = Settings()
from common.Database import Database

import common.functions as functions


def main(account_id):
    Log("info", "processing ad n grams feed", "", account_id)

    target_cpa = functions.getTargetCpa(account_id)

    engine = Database().createEngine()
    table_name = "ad_n_gram_feed"
    functions.deleteAllFromTable(table_name, account_id, engine)

    feed = None
    for date_range in settings.date_ranges:
        df = getNGrams(account_id, date_range)
        df = df.loc[:, ~df.columns.duplicated()]

        if functions.dfIsEmpty(df):
            print("%s is empty" % (date_range))
            continue

        if not functions.dfIsEmpty(feed):
            df = df[~df["n_gram_id"].isin(feed["n_gram_id"].values)]

        feed = getWorstAndBestPerformers(df, feed, target_cpa)

        if functions.dfIsEmpty(feed):
            continue

        # drop duplicate columns
        # from here: https://stackoverflow.com/questions/14984119/python-pandas-remove-duplicate-columns
        feed = feed.loc[:, ~feed.columns.duplicated()]

    if functions.dfIsEmpty(feed):
        print("ad_n_feed is empty")
        return

    print("ad_n_feed length: %s" % (feed.shape[0]))

    feed = functions.createUuid(feed)
    feed = functions.trimDfToTableColumns(feed, table_name)

    feed['display_from_date'] = datetime.now()

    writeToAdvertFeed(feed, table_name, engine)


def writeToAdvertFeed(df, table_name, engine):
    columns = list(
        pd.read_sql("select * from %s where id = '999999999'" % (table_name), Database().createEngine()).columns)
    for col in columns:
        if col not in df.columns:
            df[col] = None

    functions.append_df_to_sql_table(df[columns], table_name)


def getWorstAndBestPerformers(df, feed, target_cpa):
    if target_cpa is None:
        return
    worst_performer = getWorstPerformer(df, target_cpa)
    best_performer = getBestPerformer(df, target_cpa)

    this_feed = worst_performer.append(best_performer)

    this_feed["created_at"] = datetime.now()
    this_feed["updated_at"] = datetime.now()
    this_feed["display_from_date"] = datetime.now()

    cols = ["id", "created_at", "updated_at", "account_id", "priority",
            "headline", "message", "suggestion", "display_from_date", "n_gram_id", "date_range"]

    this_feed = this_feed[cols]

    if functions.dfIsEmpty(feed):
        return this_feed

    return feed.append(this_feed)


def getBestPerformer(df, target_cpa):
    if target_cpa is None:
        return
    best_performer = df[(df.cpa < (target_cpa * .8)) & (df.conversions > 0)].sort_values("cost", ascending=False).head(
        1)
    best_performer["message"] = "The Ad n-gram '" + best_performer["n_gram"] + "' is below CPA target"
    best_performer["headline"] = "High Performing Ad n-gram"
    best_performer["suggestion"] = "Consider further utilising this phrase in ad copy."
    best_performer["priority"] = "success"

    return best_performer


def getWorstPerformer(df, target_cpa):
    # find poor performers
    if target_cpa is None:
        return
    worst_performer = df[df.cpa > (target_cpa * 1.2)].sort_values("cost", ascending=False).head(1)
    if worst_performer.shape[0] == 0:
        worst_performer = df[(df.cpa > (target_cpa * 1.2)) | ((df.cpa == 0) & (df.cost > target_cpa))].sort_values(
            "cost", ascending=False).head(1)
    worst_performer["message"] = "The Ad n-gram '" + worst_performer["n_gram"] + "' is well above CPA target"
    worst_performer["headline"] = "Low Performing Ad n-gram"
    worst_performer["suggestion"] = "You may want to avoid using this phrase in ad copy"
    worst_performer["priority"] = "danger"

    return worst_performer


def getNGrams(account_id, date_range):
    table_name = "ad_n_gram_performance"
    engine = Database().createEngine()
    query = 'SELECT * FROM %s where account_id = "%s"' % (table_name, account_id)
    query += " and clicks > 10 and cost > 10 "
    query += " and date_range = '%s' " % (date_range)
    # need to update this (and all) queries to match the date_range we've set
    df = pd.read_sql_query(query, engine)
    df["n_gram_id"] = df["id"]
    return df


if __name__ == '__main__':
    account_id = "1205f866-2eaa-4447-8fbd-8e440e768b59"
    main(account_id)
