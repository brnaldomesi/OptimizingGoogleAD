# coding: utf-8
# !/usr/bin/env python
from __future__ import division

import uuid
from datetime import datetime

import pandas as pd

from common.Log import Log
from common.Settings import Settings

settings = Settings()
import common.functions as functions


def main(account_id):
    Log("info", "processing keyword feed", "", account_id)

    target_cpa = functions.getTargetCpa(account_id)

    table_name = "keyword_feed"

    deleteAccountDataFromTable(account_id, table_name, settings)

    feed = None
    for date_range in settings.date_ranges:
        df = getKeywords(account_id, date_range)
        df = df.loc[:, ~df.columns.duplicated()]
        # print date_range
        # print df.shape

        # to check for duplicates
        df["keyword_and_type"] = df["keyword_text"] + df["keyword_match_type"]

        if functions.dfIsEmpty(df):
            continue

        if not functions.dfIsEmpty(feed):
            feed["keyword_and_type"] = feed["keyword_text"] + feed["keyword_match_type"]
            df = df[~df["keyword_and_type"].isin(feed["keyword_and_type"].values)]

        feed = getWorstAndBestPerformers(df, feed, target_cpa)

        if functions.dfIsEmpty(feed):
            continue

        # drop duplicate columns
        # from here: https://stackoverflow.com/questions/14984119/python-pandas-remove-duplicate-columns
        feed = feed.loc[:, ~feed.columns.duplicated()]

    if functions.dfIsEmpty(feed):
        return

    if "keyword_and_type" in feed.columns:
        del feed["keyword_and_type"]

    feed['display_from_date'] = datetime.now()

    functions.append_df_to_sql_table(feed, table_name)


def deleteAccountDataFromTable(account_id, table_name, settings):
    deleteQuery = "delete from %s where account_id = '%s'" % (table_name, account_id)
    Database().createEngine().execute(deleteQuery)


def getWorstAndBestPerformers(df, feed, target_cpa):
    if target_cpa is None:
        return
    worst_performer = getWorstPerformer(df, target_cpa)
    best_performer = getBestPerformer(df, target_cpa)

    this_feed = worst_performer.append(best_performer)

    if functions.dfIsEmpty(feed):
        return this_feed

    this_feed["created_at"] = datetime.now()
    this_feed["updated_at"] = datetime.now()
    this_feed["display_from_date"] = datetime.now()

    cols = ["id", "created_at", "updated_at", "account_id", "priority",
            "headline", "message", "suggestion", "display_from_date",
            "keyword_id", "keyword_text", "keyword_match_type", "date_range"]
    this_feed = this_feed[cols]

    return feed.append(this_feed)


def getBestPerformer(keyword_df, target_cpa):
    if target_cpa is None:
        return
    best_performer = keyword_df[keyword_df.cpa < (target_cpa * .8)].sort_values("cost", ascending=False).head(1)
    best_performer["message"] = "The keyword '" + best_performer["keyword_text"] + "' (" + best_performer[
        "keyword_match_type"] + ") well below the CPA target"
    best_performer["headline"] = "High Performing Keyword"
    best_performer["suggestion"] = "Review and possibly increase this keyword's spend"
    best_performer["priority"] = "success"
    best_performer["id"] = str(uuid.uuid1())
    return best_performer


def getWorstPerformer(keyword_df, target_cpa):
    # find poor performers
    if target_cpa is None:
        return
    worst_performer = keyword_df[keyword_df.cpa > (target_cpa * 1.2)].sort_values("cost", ascending=False).head(1)
    worst_performer["message"] = "The keyword '" + worst_performer["keyword_text"] + "' (" + worst_performer[
        "keyword_match_type"] + ") is well above CPA target"
    worst_performer["headline"] = "Low Performing Keyword"
    worst_performer["suggestion"] = "Review and possibly reduce this keyword's spend"
    worst_performer["priority"] = "danger"
    worst_performer["id"] = str(uuid.uuid1())
    return worst_performer


def getKeywords(account_id, date_range):
    engine = Database().createEngine()

    query = """
    SELECT * FROM keyword_performance
    join keywords
    on keyword_performance.keyword_id = keywords.id
    where date_range = "%s"
    and keywords.account_id = "%s"
    and keyword_text != "AutomaticContent"
    and clicks > 50
    """ % (date_range, account_id)

    df = pd.read_sql_query(query, engine)

    return df
