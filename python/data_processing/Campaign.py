from __future__ import division

import uuid
from datetime import datetime

import numpy as np
import pandas as pd


# No longer in use, this is now a download
def Campaign(account_id, settings):
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
        return
    # names of the columns in the ad_performance_report : names in the df report
    column_map = {"account_id": "account_id", "campaign_name": "name", "campaign_google_id": "google_id",
                  "campaign_status": "status"}

    # the rest of the columns (where the column header is the same)
    cols = ["date"] + settings.metrics

    # Note: just add the date range manually for now, but they can be looped through at a later date
    # for date_range in settings.date_ranges:
    date_range = "last_30_days"

    df = ad_performance_df[list(column_map.keys()) + cols]
    df = df.rename(columns=column_map)

    # convert the date to datetime
    df.date = pd.to_datetime(df.date)

    # trim to this date range
    df = df[
        (df.date >= settings.date_ranges[date_range]["from_date"]) & (
                df.date <= settings.date_ranges[date_range]["to_date"])]
    df["date_range"] = date_range

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

    # #now let's add created_at and updated_at as today
    df["created_at"] = datetime.now()
    df["updated_at"] = datetime.now()

    # add the unique id field
    df["id"] = pd.Series([uuid.uuid1() for i in range(len(df))]).astype(str)

    df = df.replace([np.inf, -np.inf], 0)
    df = df.fillna(0).round(2)

    # Split into campaigns and campaign_performance and add to the db
    # campaigns

    campaigns_cols = [column_map[i] for i in column_map] + ["created_at", "updated_at", "id"]
    campaigns = df[campaigns_cols]
    campaigns.to_sql("campaigns", Database().createEngine(), if_exists='append', index=False)
    # campaign_performance
    campaign_performance_cols = list(df.columns)
    for k in column_map:
        campaign_performance_cols.remove(column_map[k])
    campaign_performance = df[campaign_performance_cols]
    del df
    campaign_performance["campaign_id"] = campaign_performance["id"]
    campaign_performance["id"] = pd.Series([uuid.uuid1() for i in range(len(campaign_performance))]).astype(str)
    campaign_performance.to_sql("campaign_performance", Database().createEngine(), if_exists='append', index=False)
