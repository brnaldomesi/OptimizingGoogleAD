from __future__ import division

import uuid
from datetime import datetime

import numpy as np
import pandas as pd

import common.functions as functions
from common.Settings import Settings

settings = Settings()


# NO LONGER IN USE: We've moved this functionality to a download
def populateAdGroupsAndAdGroupPerformance(account_id):
    column_map = {"campaign_google_id": "campaign_id", "adgroup_name": "name", "adgroup_google_id": "google_id",
                  "adgroup_status": "status"}

    # all data = both performance and adgroups
    all_data_df = getAllData(account_id, column_map)
    if functions.dfIsEmpty(all_data_df):
        print("df is empty")
        return

    # Split into adgroups and adgroup_performance and add to the db
    # adgroups
    adgroups_cols = [column_map[i] for i in column_map] + ["created_at", "updated_at", "id"]
    adgroups = all_data_df[adgroups_cols]
    adgroups["account_id"] = account_id

    deleteAndWriteToAdGroupsTable(adgroups, account_id, "adgroups", Database().createEngine())

    # adgroup_performance
    adgroup_performance_cols = list(all_data_df.columns)
    for k in column_map:
        adgroup_performance_cols.remove(column_map[k])
    adgroup_performance = all_data_df[adgroup_performance_cols]
    # Note: just add the date range manually for now, but they can be looped through at a later date
    # for date_range in settings.date_ranges:
    date_range = "last_30_days"
    adgroup_performance["date_range"] = date_range
    adgroup_performance["adgroup_id"] = adgroup_performance["id"]
    del adgroup_performance["id"]
    adgroup_performance["account_id"] = account_id

    adgroup_performance["id"] = pd.Series([uuid.uuid1() for i in range(len(adgroup_performance))]).astype(str)

    deleteAndWriteToAdGroupsTable(adgroup_performance, account_id, "adgroup_performance", Database().createEngine())


def getAllData(account_id, column_map, date_range):
    # then we'll start creating the df which we'll use to populate the db
    engine = Database().createEngine()

    query = 'SELECT * FROM ad_performance_reports where account_id = "%s" and date_range = %s ' % (
        account_id, date_range
    )
    # Note: just add the date range manually for now, but they can be looped through at a later date

    # need to update this (and all) queries to match the date_range we've set
    ad_performance_df = pd.read_sql_query(query, engine)

    if ad_performance_df.shape[0] == 0:
        return

    # names of the columns in the ad_performance_report : names in the campaign_performance report

    # the rest of the columns (where the column header is the same)
    cols = ["date"] + settings.metrics

    df = ad_performance_df[list(column_map.keys()) + cols]
    del ad_performance_df
    df = df.rename(columns=column_map)

    df["account_id"] = account_id

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

    # START campaign_id = id column from the campaigns table
    engine = Database().createEngine()
    query = 'SELECT id,google_id FROM %s where account_id = "%s"' % ("campaigns", account_id)
    campaigns = pd.read_sql_query(query, engine)
    if campaigns.shape[0] == 0:
        return

    campaigns.rename(columns={"google_id": "campaign_id"}, inplace=True)

    df = df.merge(campaigns, on="campaign_id")
    del df["campaign_id"]
    df.rename(columns={"id": "campaign_id"}, inplace=True)
    del campaigns
    # END campaign_id = id column from the campaigns table

    # add the unique id field
    df["id"] = pd.Series([uuid.uuid1() for i in range(len(df))]).astype(str)

    # #now let's add created_at and updated_at as today
    df["created_at"] = datetime.now()
    df["updated_at"] = datetime.now()

    df = df.replace([np.inf, -np.inf], 0)
    df = df.fillna(0).round(2)
    df = df.drop_duplicates()

    return df


def deleteAndWriteToAdGroupsTable(df, account_id, table_name, engine):
    functions.deleteThisAccountsDataFromTable(table_name, account_id, engine)
    functions.append_df_to_sql_table(df, table_name)


if __name__ == '__main__':
    account_id = "1205f866-2eaa-4447-8fbd-8e440e768b59"
    populateAdGroupsAndAdGroupPerformance(account_id)
