#!/usr/bin/env python
from __future__ import division

import traceback
from datetime import datetime

import pandas as pd

from common.Log import Log
from common.Settings import Settings
import common.functions as functions

settings = Settings()


def main(account_id):
    Log("info", "processing search query n-grams feed", "", account_id)

    target_cpa = functions.getTargetCpa(account_id)

    engine = Database().createEngine()

    deleteAllFromTable("search_query_n_gram_feed", account_id, engine)

    feed = None
    for date_range in settings.date_ranges:
        print(date_range)
        df = getQueryPerformance(account_id, date_range)
        df = df.loc[:, ~df.columns.duplicated()]

        print(date_range)
        print(df.shape)

        if functions.dfIsEmpty(df):
            continue

        if not functions.dfIsEmpty(feed):
            df = df[~df["n_gram"].isin(feed["n_gram"].values)]

        feed = getWorstAndBestPerformers(df, feed, target_cpa)

        if feed is None:
            continue

        # drop duplicate columns
        # from here: https://stackoverflow.com/questions/14984119/python-pandas-remove-duplicate-columns
        feed = feed.loc[:, ~feed.columns.duplicated()]

    if functions.dfIsEmpty(feed):
        return

    table_name = "search_query_n_gram_feed"

    feed = functions.createUuid(feed)
    feed = functions.trimDfToTableColumns(feed, table_name)

    feed['display_from_date'] = datetime.now()

    functions.append_df_to_sql_table(feed, table_name)


def getWorstAndBestPerformers(df, feed, target_cpa):
    worst_performer = getWorstPerformer(df, target_cpa)
    best_performer = getBestPerformer(df, target_cpa)

    this_feed = worst_performer.append(best_performer)

    if functions.dfIsEmpty(this_feed):
        return

    this_feed["created_at"] = datetime.now()
    this_feed["updated_at"] = datetime.now()
    this_feed["display_from_date"] = datetime.now()
    this_feed["search_query_n_gram_id"] = this_feed["id"]

    cols = ["id", "created_at", "updated_at", "account_id", "priority",
            "headline", "message", "suggestion", "display_from_date", "n_gram", "date_range", "search_query_n_gram_id"]
    this_feed = this_feed[cols]

    if functions.dfIsEmpty(feed):
        return this_feed

    return feed.append(this_feed)


def deleteAllFromTable(table_name, account_id, engine):
    deleteQuery = "delete from %s where account_id = '%s'" % (table_name, account_id)
    print(deleteQuery)
    try:
        engine.execute(deleteQuery)

    except Exception as e:
        if "1146" in traceback.format_exc():
            print("Table doesn't exist, supressing the error")
        else:
            # it's some other error
            raise Exception(e)


def getBestPerformer(df, target_cpa):
    # find poor performers
    best_performer = df[(df.cpa < (target_cpa * .8)) & (df.conversions > 0)].sort_values("cost", ascending=False).head(
        1)
    if best_performer.shape[0] == 0:
        return best_performer
    best_performer["message"] = "The search query n-gram '" + best_performer["n_gram"] + "' is below CPA target"
    best_performer["headline"] = "High Performing Search Query N-gram"
    best_performer["suggestion"] = "Review and possibly increase this search query n-gram's spend"
    best_performer["priority"] = "success"
    return best_performer


def getWorstPerformer(df, target_cpa):
    # find poor performers
    worst_performer = df[df.cpa > (target_cpa * 1.2)].sort_values("cost", ascending=False).head(1)
    if worst_performer.shape[0] == 0:
        worst_performer = df[(df.cpa > (target_cpa * 1.2)) | ((df.cpa == 0) & (df.cost > target_cpa))].sort_values(
            "cost", ascending=False).head(1)

    if worst_performer.shape[0] == 0:
        return worst_performer
    worst_performer["message"] = "The search query n-gram '" + worst_performer["n_gram"] + "' is well above CPA target"
    worst_performer["headline"] = "Low Performing Search Query N-gram"
    worst_performer["suggestion"] = "Review and possibly reduce this search query n-gram's spend"
    worst_performer["priority"] = "danger"
    return worst_performer


def getQueryPerformance(account_id, date_range):
    table_name = "search_query_n_gram_performance"
    engine = Database().createEngine()
    query = 'SELECT id,n_gram,cpa,cost,conversions,account_id FROM %s where account_id = "%s"' % (
    table_name, account_id)
    query += " and clicks > 10 "
    query += " and date_range = '%s' " % (date_range)

    # need to update this (and all) queries to match the date_range we've set
    df = pd.read_sql_query(query, engine)
    df["search_query_n_gram_id"] = df["id"]
    df["date_range"] = date_range

    return df


def createSearchQueryFeed(account_id):
    main(account_id)


if __name__ == '__main__':
    account_id = "1205f866-2eaa-4447-8fbd-8e440e768b59"
    createSearchQueryFeed(account_id)
