from __future__ import division

import uuid
from datetime import datetime, timedelta, date

import pandas as pd
import numpy as np

import common.functions as functions
from common.Log import Log
from common.Settings import Settings
from common.Database import Database

settings = Settings()


# this table corresponds to the data at the top of the dash

def main(account_id):
    Log("info", "processing account performance changes", "", account_id)

    ad_performance_df = dataFrameFromAdPerformanceReports(settings, account_id)

    if functions.dfIsEmpty(ad_performance_df):
        return

    # returns a df with the past x (60 at present) days data
    df = createXDaysDataFrame(ad_performance_df, settings, account_id)

    # add the graph data to a dict
    # first add the the calculated metrics (ctr, etc) - these will make up the graph data
    df = addCalculatedMetrics(df, settings)
    df = df.replace([np.inf, -np.inf], 0)

    previousDf = df.iloc[:30].sort_index().fillna(0).round(2)
    currentDf = df.iloc[30:].sort_index().fillna(0).round(2)

    # we'll store everything in this dict
    mets = getGraphDataFromDf(previousDf, currentDf, settings)

    # add the comparisons - the baseline amount and the -/+ amount
    # compare previousDf to currentDf to get the +/- numbers
    # was, now, diff

    # sum the dfs, combining the dates
    previousDf = previousDf.sum()
    currentDf = currentDf.sum()
    previousDf = addCalculatedMetrics(previousDf, settings).fillna(0)
    currentDf = addCalculatedMetrics(currentDf, settings).fillna(0)

    del previousDf["account_id"]
    del currentDf["account_id"]

    # add the comparisons (i.e. previous Vs current performance)...
    comp = pd.concat([currentDf, previousDf], axis=1)
    comp["vs"] = comp.apply(lambda row: returnComparison(row), axis=1)
    comp = comp.T.reset_index()
    comp = comp[comp["index"] == "vs"].reset_index(drop=True)

    # bring it all together!
    metrics = ["cpa", "ctr", "roas", "conversion_rate"]
    valueNames = list(settings.metrics)
    account_performance_changes = df.groupby("account_id").sum().reset_index()[["account_id"] + metrics + valueNames]
    # add the vs metrics
    for metric in metrics:
        account_performance_changes[metric] = comp[metric].values[0]
    # add the graph data
    for graph in mets:
        account_performance_changes[graph] = str(mets[graph])

    # add the baseline (overall performance)
    for metric in settings.calculatedMetrics:
        if metric == "average_cpc":
            continue
        baseline_metric = metric + "_baseline"
        if settings.calculatedMetrics[metric][1] == "/":
            account_performance_changes[baseline_metric] = account_performance_changes[
                                                               settings.calculatedMetrics[metric][0]] / \
                                                           account_performance_changes[
                                                               settings.calculatedMetrics[metric][2]]
        if settings.calculatedMetrics[metric][3]:
            account_performance_changes[baseline_metric] = account_performance_changes[baseline_metric] * 100

    account_performance_changes = account_performance_changes.fillna(0).round(2)
    basline_metrics = [i + "_baseline" for i in metrics]
    graph_metrics = [i + "_graph_data" for i in metrics]
    account_performance_changes = account_performance_changes[
        ["account_id"] + metrics + basline_metrics + graph_metrics]
    account_performance_changes["date_range"] = "last_30_days"
    account_performance_changes["id"] = pd.Series(
        [uuid.uuid1() for i in range(len(account_performance_changes))]).astype(str)
    account_performance_changes["created_at"] = datetime.now()
    account_performance_changes["updated_at"] = datetime.now()

    for metric in graph_metrics:
        account_performance_changes[metric] = account_performance_changes[metric].str.replace("'", '"')

    account_performance_changes = account_performance_changes.replace([np.inf, -np.inf], 0)

    account_performance_changes.to_sql(
        "account_performance_changes", Database().createEngine(), if_exists='append', index=False
    )

    return account_performance_changes


# grabs the necessary information from the ad performance reports table
# grabs the necessary information from the ad performance reports table
def dataFrameFromAdPerformanceReports(settings, account_id):
    engine = Database().createEngine()
    sql_cols = ["date", "impressions", "conversion_value", "clicks", "conversions", "account_id", "cost"]
    query = 'SELECT ' + ",".join(sql_cols) + ' FROM ad_performance_reports where account_id = "%s"' % (account_id)

    # Note: just add the date range manually for now, but they can be looped through at a later date
    # for date_range in settings.date_ranges:
    # as this is a comparison, we need to double whatever N days we're getting
    date_range = "last_30_days"

    # first get the number of days we're looking at and double it
    dates = functions.dateRangeFromDays(date_range, account_id).split(",")
    fromDate = dates[0].strip()
    toDate = dates[1].strip()

    query = query + " and (date >= '%s' <= '%s')" % (
        fromDate, toDate)
    query = query + " and impressions > 0"
    # need to update this (and all) queries to match the date_range we've set
    ad_performance_df = pd.read_sql_query(query, engine)
    ad_performance_df = ad_performance_df.sort_values("date")

    if ad_performance_df.shape[0] == 0:
        # not throwing an error because an account may have data but no clicks
        msg = "Account performance changes: ad_performance_reports is empty"
        print(msg)
        return

    return ad_performance_df


def returnComparison(row):
    try:
        before = row[0]
        after = row[1]
        if before == 0 or after == 0:
            return 0
        return round(((before - after) / after) * 100, 2)
    except:
        return 0


def createXDaysDataFrame(ad_performance_df, settings, account_id):
    # get the dates of the last X days
    lookBackDays = 60
    dateFormat = '%Y-%m-%d'
    yesterday = date.today() - timedelta(1)
    dates = [(yesterday - timedelta(days=i)).strftime(dateFormat) for i in range(0, 60)]

    metrics = list(settings.metrics)
    df = ad_performance_df[["date", "account_id"] + metrics].groupby(["date", "account_id"]).sum()
    df = df.reset_index(level=1)
    df = df.reset_index()
    df.date = pd.to_datetime(df.date, format=dateFormat)
    df = df[df.date.isin(dates)]

    # create an empty df last 60 days which we can merge
    yesterday = date.today() - timedelta(1)
    empty_data = [[(yesterday - timedelta(days=i)), account_id, 0, 0, 0, 0, 0] for i in range(0, 60)]
    empty_df = pd.DataFrame(columns=df.columns, data=empty_data)
    empty_df.date = pd.to_datetime(empty_df.date, format=dateFormat)

    df = empty_df.append(df)
    df.date = pd.to_datetime(df.date, format=dateFormat)
    df.date = df.date.astype(str)
    df = df.groupby(["date", "account_id"]).sum().reset_index().sort_values("date")
    return df


def addCalculatedMetrics(df, settings):
    for metric in settings.calculatedMetrics:
        if metric == "average_cpc":
            continue
        try:
            if settings.calculatedMetrics[metric][1] == "/":
                df[metric] = df[settings.calculatedMetrics[metric][0]] / \
                             df[settings.calculatedMetrics[metric][2]]
        except ZeroDivisionError:
            df[metric] = 0
        if settings.calculatedMetrics[metric][3]:
            df[metric] = df[metric] * 100

    return df


def getGraphDataFromDf(previousDf, currentDf, settings):
    mets = {}
    # add the graph data
    for metric in settings.calculatedMetrics:
        if metric == "average_cpc":
            continue
        current = list(currentDf[metric].values)
        previous = list(previousDf[metric].values)
        metric = metric + "_graph_data"
        mets[metric] = {"current": current, "previous": previous}
    return mets


if __name__ == '__main__':
    account_id = "1205f866-2eaa-4447-8fbd-8e440e768b59"
    main(account_id)
