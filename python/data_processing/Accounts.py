import uuid
from datetime import datetime

import numpy as np
import pandas as pd

from common.Database import Database


# No longer in use, this is now a download
# Populate the Account and Account_performance tables
def Account(account_id, settings):
    # then we'll start creating the df which we'll use to populate the db
    engine = Database().createEngine()
    query = 'SELECT * FROM ad_performance_reports where account_id = "%s"' % (account_id)

    # Note: just add the date range manually for now, but they can be looped through at a later date
    # for date_range in settings.date_ranges:
    date_range = "last_30_days"
    query = query + " and (date BETWEEN '%s' AND '%s')" % (
        settings.date_ranges[date_range]["from_date"], settings.date_ranges[date_range]["to_date"])
    # need to update this (and all) queries to match the date_range we've set
    ad_performance_df = pd.read_sql_query(query, engine)

    if ad_performance_df.shape[0] == 0:
        print("Account: no data found.")
        return

    # names of the columns in the ad_performance_report : names in the account reports
    column_map = {"account_id": "account_id"}
    # the rest of the columns (where the column header is the same)
    cols = ["date"] + settings.metrics

    # Note: just add the date range manually for now, but they can be looped through at a later date
    # for date_range in settings.date_ranges:
    date_range = "last_30_days"

    df = ad_performance_df[list(column_map.keys()) + cols]
    df = df.rename(columns=column_map)

    # convert the date to datetime
    df.date = pd.to_datetime(df.date)

    # impression weight calculated fields e.g. avg pos
    impressionMultiples = []
    for field in settings.calculatedFields:
        newField = "%sxImpressions" % (field)
        impressionMultiples.append(newField)
        df[newField] = df[field] * df.impressions

    # pivot, removing date
    df_cols = list(df.columns)
    values = cols + impressionMultiples

    for value in values:
        df_cols.remove(value)

    # df containing stats by day - used for the kpi and budget calculations
    by_day_df = df.groupby("date").sum()[["impressions", "clicks", "conversions", "cost", "conversion_value"]]
    by_day_df["cpa"] = by_day_df["cost"] / by_day_df["conversions"]
    by_day_df["ctr"] = by_day_df["clicks"] / by_day_df["impressions"]
    by_day_df["conversion_rate"] = by_day_df["conversions"] / by_day_df["clicks"]
    by_day_df["roas"] = by_day_df["conversion_value"] / by_day_df["cost"]

    df = pd.pivot_table(df, index=df_cols, values=values, aggfunc=np.sum)
    df.reset_index(inplace=True)

    # add the calculated fields (e.g. average position) back in
    for i, multiple in enumerate(impressionMultiples):
        df[settings.calculatedFields[i]] = df[multiple] / df["impressions"]
        del df[multiple]

    # #add the calculated metrics (ctr, etc)
    for metric in settings.calculatedMetrics:
        if settings.calculatedMetrics[metric][1] == "/":
            df[metric] = df[settings.calculatedMetrics[metric][0]] / \
                         df[settings.calculatedMetrics[metric][2]]
        if settings.calculatedMetrics[metric][3]:
            df[metric] = df[metric] * 100

    # add the unique id field
    df["id"] = pd.Series([uuid.uuid1() for i in range(len(df))]).astype(str)

    # #now let's add created_at and updated_at as today
    df["created_at"] = datetime.now()
    df["updated_at"] = datetime.now()
    df["date_range"] = date_range

    df = df.replace([np.inf, -np.inf], 0)
    df = df.fillna(0).round(2)

    return by_day_df
