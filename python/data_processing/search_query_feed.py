# coding: utf-8
# !/usr/bin/env python
from __future__ import division

import traceback
from datetime import datetime

import pandas as pd

from common.Log import Log
from common.Settings import Settings
import common.functions as functions

settings = Settings()


def main(account_id):
    Log("info", "processing search query feed", "", account_id)

    target_cpa = functions.getTargetCpa(account_id)

    # settings = Settings()
    engine = Database().createEngine()
    deleteAllFromTable("search_query_feed", account_id, engine)

    feed = None
    for date_range in settings.date_ranges:
        df = getQueryPerformance(account_id, date_range)
        df = df.loc[:, ~df.columns.duplicated()]
        if functions.dfIsEmpty(df):
            continue

        # print date_range
        # print df.shape

        if not functions.dfIsEmpty(feed):
            # print len(feed.id.values)
            df = df[~df["query"].isin(feed["query"].values)]

        feed = getWorstAndBestPerformers(df, feed, target_cpa)

        # drop duplicate columns
        # from here: https://stackoverflow.com/questions/14984119/python-pandas-remove-duplicate-columns
        feed = feed.loc[:, ~feed.columns.duplicated()]

    if functions.dfIsEmpty(feed):
        return

    feed = functions.createUuid(feed)

    feed = functions.trimDfToTableColumns(feed, "search_query_feed")

    feed['display_from_date'] = datetime.now()

    functions.append_df_to_sql_table(feed, "search_query_feed")


def getWorstAndBestPerformers(df, feed, target_cpa):
    worst_performer = getWorstPerformer(df, target_cpa)
    best_performer = getBestPerformer(df, target_cpa)

    this_feed = worst_performer.append(best_performer)

    if functions.dfIsEmpty(feed):
        return this_feed

    this_feed["created_at"] = datetime.now()
    this_feed["updated_at"] = datetime.now()
    this_feed["display_from_date"] = datetime.now()

    return feed.append(this_feed)


def deleteAllFromTable(table_name, account_id, engine):
    deleteQuery = "delete from %s where account_id = '%s'" % ("search_query_feed", account_id)
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
    if target_cpa is None:
        return
    best_performer = df[(df.cpa < (target_cpa * .8)) & (df.conversions > 0)].sort_values(
        "cost", ascending=False
    ).head(1)

    if best_performer.shape[0] == 0:
        print("best performer is empty")
        return best_performer

    best_performer["message"] = "The query '" + best_performer["query"] + "' is below CPA target"
    best_performer["headline"] = "High Performing Query"
    best_performer["suggestion"] = "Review and possibly increase this query's spend"
    best_performer["priority"] = "success"

    return best_performer


def getWorstPerformer(df, target_cpa):
    # find poor performers
    if target_cpa is None:
        return
    worst_performer = df[df.cpa > (target_cpa * 1.2)].sort_values("cost", ascending=False).head(1)
    if worst_performer.shape[0] == 0:
        worst_performer = df[(df.cpa > (target_cpa * 1.2)) | ((df.cpa == 0) & (df.cost > target_cpa))].sort_values(
            "cost", ascending=False
        ).head(1)

    if worst_performer.shape[0] == 0:
        print("worst performer is empty")
        return worst_performer

    worst_performer["message"] = "The query '" + worst_performer["query"] + "' is well above CPA target"
    worst_performer["headline"] = "Low Performing Query"
    worst_performer["suggestion"] = "Review and possibly reduce this query's spend"
    worst_performer["priority"] = "danger"

    return worst_performer


def getQueryPerformance(account_id, date_range):
    table_name = "search_query_performance"
    engine = Database().createEngine()

    query = """
    SELECT * FROM search_query_performance
    join search_queries
    on search_query_performance.search_query_id = search_queries.id
    where date_range = "%s"
    and search_queries.account_id = "%s"
    and clicks > 50
    """ % (date_range, account_id)

    df = pd.read_sql_query(query, engine)

    df = df[~df["query"].isnull()]  # for if there are rows in the perf table which aren't in the queries table

    return df


def generateKpiTarget(account_id):
    print("Generating kpi target (CPA)")
    kpi_name = "cpa"  # default, #todo grab a default based on performance
    query = "select cpa from account_performance where account_id = '%s'" % (account_id)
    kpi_value = pd.read_sql_query(query, Database().createEngine()).head(1)["cpa"].values[0]

    query = "update accounts set kpi_name = '%s', kpi_value = %s" % (kpi_name, kpi_value)
    Database().createEngine().execute(query)


if __name__ == '__main__':
    account_id = "1205f866-2eaa-4447-8fbd-8e440e768b59"
    main(account_id)
