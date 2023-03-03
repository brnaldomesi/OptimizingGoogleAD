from __future__ import division

import inspect
import os
import sys

import numpy as np
import pandas as pd

from common.Settings import Settings

currentdir = os.path.dirname(os.path.abspath(inspect.getfile(inspect.currentframe())))
parentdir = os.path.dirname(currentdir)
sys.path.insert(0, parentdir)


def keywordScore(account_name):
    settings = Settings()

    targets = getTargets()

    df = readKeywordReport(settings, account_name)

    df = renameColumns(df)

    df = dfMetricsToFloat(settings.metrics, df)

    df = addCalculatedMetrics(df, settings)

    df = addFilters(df, targets)

    df = addScore(df, targets)

    df = addMessages(df)

    writeToCsv(df, settings)


def renameColumns(df):
    return df.rename(columns={
        'Account': 'account',
        'Campaign': 'campaign',
        'Ad group': 'ad_group',
        'Search keyword': 'keyword',
        'Currency': 'currency',
        'Clicks': 'clicks',
        'Impressions': 'impressions',
        'Cost': 'cost',
        'Conversions': 'conversions',
        'Conv. value': 'conversion_value',
        'Search Impr. share': 'search_impression_share',
        'Search Lost IS (rank)': 'search_lost_impression_share_rank',
        'Search Exact match IS': 'search_exact_match_impression_share'
    })


def writeToCsv(df, settings):
    cols = [
        'account',
        'campaign',
        'ad_group',
        'keyword',
        'clicks',
        'impressions',
        'ctr',
        'ctr_standard_error',
        'cost',
        'conversions',
        'conversion_rate',
        'conversion_rate_standard_error',
        'cpa',
        'conversion_value',
        'search_impression_share',
        'search_lost_impression_share_rank',
        'search_exact_match_impression_share',
        'score',
        'messages'
    ]

    df = df[cols]

    fileName = "output.csv"

    outputLocation = os.path.join(settings.this_dir, "scorer", fileName)

    df.to_csv(outputLocation, index=False, encoding="utf-8")


def addCtrStandardError(row):
    std = np.sqrt(row.clicks * (1 - row.ctr) ** 2 + (row.impressions - row.clicks) * row.ctr ** 2) / (
                row.impressions - 1)
    return round((std / np.sqrt(row.impressions)) * 100, 2)


def addConversionStandardError(row):
    std = np.sqrt(row.conversions * (1 - row.conversion_rate) ** 2 + (
                row.clicks - row.conversions) * row.conversion_rate ** 2) / (row.clicks - 1)
    return round((std / np.sqrt(row.clicks)) * 100, 2)


def readKeywordReport(settings, account_name):
    fileName = account_name + ".csv"
    keywordReportLocation = os.path.join(settings.this_dir, "scorer", "reports", fileName)
    return pd.read_csv(keywordReportLocation, encoding="utf-8", skiprows=0)


def addFilters(df, targets):
    min_clicks = (df.sum().conversions / df.sum().clicks) * 10
    print("min clicks: %s" % (min_clicks))
    df = df[df.clicks > min_clicks]

    if targets["type"] == "cpa":
        df = df[df.cost > targets["target"]]

    return df


def getTargets():
    # hard code these for now, but they'll come from the db
    targets = {}

    targets["type"] = "cpa"

    targets["target"] = 100

    targets["cost"] = 2500  # i.e. the budget

    return targets


# #add the calculated metrics (ctr, etc)
def addCalculatedMetrics(df, settings):
    for metric in settings.calculatedMetrics:

        # they're all divisions
        df[metric] = df[settings.calculatedMetrics[metric][0]] / df[settings.calculatedMetrics[metric][2]]

        # multiply the percentages by 100
        if settings.calculatedMetrics[metric][3]:
            df[metric] = df[metric] * 100

    df = df.replace([np.inf, -np.inf], 0)
    df = df.fillna(0).round(2)

    return df


def dfMetricsToFloat(metrics, df):
    for metric in metrics:
        try:
            df[metric] = df[metric].str.replace(",", "").astype("float")
        except Exception as e:
            pass
            # if there's an error it just means it's already a float
    return df


def addMessages(df):
    df["messages"] = ""

    max_percent_of_conversions = df.max().percent_of_conversions
    # get the index of the record we want to set a message to
    mostImportantIndex = \
    df[df.percent_of_conversions == max_percent_of_conversions].sort_values("cost", ascending=False).iloc[0].name
    df.loc[df.index == mostImportantIndex, 'messages'] = df.messages + "," + "most_important_keyword"

    max_score = df.max().score
    # get the index of the record we want to set a message to
    bestPerformerIndex = df[df.score == max_score].sort_values("cost", ascending=False).iloc[0].name
    df.loc[df.index == bestPerformerIndex, 'messages'] = df.messages + "," + "best_performing_keyword"

    min_score = df.min().score
    # get the index of the record we want to set a message to
    worstPerformerIndex = df[df.score == min_score].sort_values("cost", ascending=False).iloc[0].name
    df.loc[df.index == worstPerformerIndex, 'messages'] = df.messages + "," + "worst_performing_keyword"

    return df


def addScore(df, targets):
    # add temp values where conversions are zero
    df.loc[df['conversions'] == 0, 'temp_conversions'] = .2
    df.loc[df['conversions'] == 0, 'temp_cpa'] = df.cost / df.temp_conversions

    total_conversions = df.conversions.sum()
    total_cost = df.cost.sum()

    if targets["type"] == "cpa":
        df["target_kpi_percentage"] = ((targets["target"] - df.cpa) / targets["target"])
        df.loc[df['conversions'] == 0, 'target_kpi_percentage'] = (
                    (targets["target"] - df.temp_cpa) / targets["target"])

    df["percent_of_conversions"] = (df.conversions / total_conversions)
    df.loc[df['conversions'] == 0, 'percent_of_conversions'] = (df.temp_conversions / total_conversions)

    df["percent_of_cost"] = (df.cost / total_cost)

    df["kpi_performance"] = (df["percent_of_conversions"] - df["percent_of_cost"]) / df["percent_of_conversions"]

    df = df.replace([np.inf, -np.inf], 0)
    df = df.fillna(0).round(2)

    def addScoreToDf(df, targets):
        if targets["type"] == "cpa":
            if df.conversions == 0:
                return ((df.temp_conversions / df.temp_cpa) + df.kpi_performance) + df.target_kpi_percentage

            else:
                return ((df.conversions / df.cpa) + df.kpi_performance) + df.target_kpi_percentage

    df["score"] = df.apply(lambda row: addScoreToDf(row, targets), axis=1)

    df["ctr_standard_error"] = df.apply(lambda row: addCtrStandardError(row), axis=1)
    df["conversion_rate_standard_error"] = df.apply(lambda row: addConversionStandardError(row), axis=1)

    return df


if __name__ == '__main__':
    keywordScore("Aimondo")
