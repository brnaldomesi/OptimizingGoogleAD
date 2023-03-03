#!/usr/bin/env python
from __future__ import division

from datetime import datetime

import pandas as pd

from common.Log import Log
from common.Settings import Settings
from common.Database import Database

settings = Settings()
import common.functions as functions


def main(account_id):
    Log("info", "processing advert feed", "", account_id)
    target_cpa = functions.getTargetCpa(account_id)

    engine = Database().createEngine()
    functions.deleteAllFromTable("advert_feed", account_id, engine)

    feed = None
    for date_range in settings.date_ranges:
        df = getAdvertPerformance(account_id, date_range)
        df = df.loc[:, ~df.columns.duplicated()]

        if functions.dfIsEmpty(df):
            print("%s is empty" % (date_range))
            continue

        if not functions.dfIsEmpty(feed):
            df = df[~df["advert_id"].isin(feed["advert_id"].values)]

        feed = getWorstAndBestPerformers(df, feed, target_cpa)

        if functions.dfIsEmpty(feed):
            continue

        # drop duplicate columns
        # from here: https://stackoverflow.com/questions/14984119/python-pandas-remove-duplicate-columns
        feed = feed.loc[:, ~feed.columns.duplicated()]

    if functions.dfIsEmpty(feed):
        print("advert_feed is empty")
        return

    print("advert_feed length: %s" % (feed.shape[0]))

    feed = functions.createUuid(feed)
    feed = functions.trimDfToTableColumns(feed, "advert_feed")

    feed['display_from_date'] = datetime.now()

    writeToAdvertFeed(feed, engine)


def writeToAdvertFeed(df, engine):
    table_name = "advert_feed"

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

    if worst_performer is None and worst_performer is None:
        return
    if worst_performer is None:
        this_feed = best_performer
    else:
        this_feed = worst_performer.append(best_performer)

    if functions.dfIsEmpty(this_feed):
        return

    if functions.dfIsEmpty(feed):
        return

    this_feed["created_at"] = datetime.now()
    this_feed["updated_at"] = datetime.now()
    this_feed["display_from_date"] = datetime.now()

    cols = ["id", "created_at", "updated_at", "account_id", "priority",
            "headline", "message", "suggestion", "display_from_date", "advert_id", "date_range"]

    this_feed = this_feed[cols]

    return feed.append(this_feed)


def getBestPerformer(df, target_cpa):
    # find poor performers
    if target_cpa is None:
        return
    best_performer = df[(df.cpa < (target_cpa * .8)) & (df.conversions > 0)].sort_values("cost", ascending=False).head(
        1)
    if best_performer.shape[0] == 0:
        return best_performer
    best_performer["message"] = "This advert is below CPA target"
    best_performer["headline"] = "High Performing Advert"
    best_performer["suggestion"] = "Review and possibly increase this advert's spend"
    best_performer["priority"] = "success"

    return best_performer


def getWorstPerformer(df, target_cpa):
    # find poor performers
    if target_cpa is None:
        return
    df["cpa"] = df["cost"] / df["conversions"]
    worst_performer = df[df.cpa > (target_cpa * 1.2)].sort_values("cost", ascending=False).head(1)
    if worst_performer.shape[0] == 0:
        worst_performer = df[(df.cpa > (target_cpa * 1.2)) | ((df.cpa == 0) & (df.cost > target_cpa))].sort_values(
            "cost", ascending=False).head(1)

    if worst_performer.shape[0] == 0:
        return worst_performer
    worst_performer["message"] = "The Advert is well above CPA target"
    worst_performer["headline"] = "Low Performing Advert"
    worst_performer["suggestion"] = "Review and possibly reduce this Advert's spend"
    worst_performer["priority"] = "danger"

    return worst_performer


def getAdvertPerformance(account_id, date_range):
    engine = Database().createEngine()

    query = """
    SELECT * FROM advert_performance
    join adverts
    on advert_performance.advert_id = adverts.id
    where date_range = "%s"
    and advert_performance.account_id = "%s"
    and ad_type = 'Expanded text ad'
    and clicks > 20
    """ % (date_range, account_id)

    df = pd.read_sql_query(query, engine)

    df["date_range"] = date_range

    return df


if __name__ == '__main__':
    account_id = "f24c250e-b71a-4f48-acdb-a02637ab76f6"
    main(account_id)
