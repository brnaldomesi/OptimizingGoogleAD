# coding: utf-8
from __future__ import division

import uuid
from datetime import datetime

import numpy as np
from nltk import bigrams, trigrams, word_tokenize

import common.functions as functions
from common.Settings import Settings
from data_processing.delete import *


def main(account_id):
    settings = Settings()

    deleteFromSearchQueryNGrams(settings, account_id)

    columns = ["id", "created_at", "updated_at", "account_id", "date_range", "n_gram", "n_gram_count", "show_on_graph",
               "ctr", "ctr_significance", "impressions", "clicks", "conversions", "cost", "average_cpc",
               "conversion_value", "cpa", "roas", "conversion_rate", "graph_order"]

    settings = Settings()

    all_df = None
    for date_range in settings.date_ranges:

        df = dataFrameFromSearchQueryPerformanceReports(settings, account_id, date_range)
        df = addNGrams(df, columns)

        if functions.dfIsEmpty(df):
            continue

        df = filterAndAddColumns(df, date_range, account_id)

        # print date_range
        # print df.shape

        if functions.dfIsEmpty(df):
            continue
        # drop duplicate columns
        # from here: https://stackoverflow.com/questions/14984119/python-pandas-remove-duplicate-columns
        df = df.loc[:, ~df.columns.duplicated()]

        if functions.dfIsEmpty(df):
            continue

        if functions.dfIsEmpty(all_df):
            all_df = df.copy()
        else:
            all_df = all_df.append(df.copy())

    if functions.dfIsEmpty(all_df):
        return

    # print search_query_performance_df.head()

    # serving too many rows will cause php memory issues (and issues with the client)
    # just serve the first X rows, ordered by cost
    rowsToServe = 2000
    all_df = all_df.sort_values("cost", ascending=False).head(rowsToServe)

    # then sort by ctr and search_queryd the graph elements (means the n-grams must be in the top 1000 spenders to be in the graph)
    all_df = all_df.sort_values(["ctr", "conversions", "clicks", "cost"], ascending=False).reset_index(drop=True)
    all_df.loc[all_df.index == 0, 'show_on_graph'] = True
    all_df.loc[all_df.index == 1, 'show_on_graph'] = True
    all_df.loc[all_df.index == 2, 'show_on_graph'] = True
    all_df.loc[all_df.index == 0, 'graph_order'] = 1
    all_df.loc[all_df.index == 1, 'graph_order'] = 2
    all_df.loc[all_df.index == 2, 'graph_order'] = 3

    # finally fill NAs and append to the table
    all_df = all_df[columns].fillna(0)

    functions.append_df_to_sql_table(all_df, "search_query_n_gram_performance")


def filterAndAddColumns(df, date_range, account_id):
    # multiply percentages by 100
    df.ctr = df.ctr * 100
    df.conversion_rate = df.conversion_rate * 100
    df.roas = df.roas * 100

    # filter down. Must have at least 10 clicks
    df = df[df.clicks > 10]

    df = df.reset_index().fillna("")

    if functions.dfIsEmpty(df):
        return

    df["n_gram"] = df.level_0 + " " + df.level_1 + " " + df.level_2

    df["n_gram"] = df["n_gram"].str.strip()

    df = df.reset_index(drop=True)

    df["account_id"] = account_id
    df["date_range"] = date_range
    df["id"] = pd.Series([uuid.uuid1() for i in range(len(df))]).astype(str)
    # #now let's search_queryd created_at and updated_at as today
    df["created_at"] = datetime.now()
    df["updated_at"] = datetime.now()

    return df


def addNGrams(search_query_performance_df, columns):
    if functions.dfIsEmpty(search_query_performance_df):
        return

    n_gram_dict = {}

    for i, row in search_query_performance_df.fillna(0).iterrows():
        impressions = float(row['impressions'])
        clicks = float(row['clicks'])

        conversions = float(row['conversions'])
        conversion_value = float(row['conversion_value'])
        cost = float(row['cost'])
        text = (row['query'])

        # tidy up
        puncts = [",", ".", "!", "?", ":"]
        for punct in puncts:
            text = text.replace(punct, "")
        text = word_tokenize(text)

        bigram = bigrams(text)
        bigram_vec = []
        for gram in bigram:
            bigram_vec.append(gram)
        trigram = trigrams(text)
        trigram_vec = []
        for gram in trigram:
            trigram_vec.append(gram)
        total_gram_vec = bigram_vec + trigram_vec
        for gram in total_gram_vec:
            if gram not in n_gram_dict.keys():
                n_gram_dict[gram] = {'impressions': impressions, \
                                     #  'avg_pos_mult': impressions * avg_pos, \
                                     'gram_count': 1, 'clicks': clicks, \
                                     'cost': cost, 'conversions': conversions, 'conversion_value': conversion_value}
            else:
                n_gram_dict[gram]['impressions'] += impressions
                # n_gram_dict[gram]['avg_pos_mult'] += impressions * avg_pos
                n_gram_dict[gram]['gram_count'] += 1
                n_gram_dict[gram]['clicks'] += clicks
                n_gram_dict[gram]['cost'] += cost
                n_gram_dict[gram]['conversions'] += conversions
                n_gram_dict[gram]['conversion_value'] += conversion_value

    ### compute average position
    ### and statistic data

    n_gram_df_data = {}

    for gram in n_gram_dict.keys():
        impressions = n_gram_dict[gram]['impressions']
        count = n_gram_dict[gram]['gram_count']
        # avg_pos = n_gram_dict[gram]['avg_pos_mult'] / count
        clicks = n_gram_dict[gram]['clicks']
        conversions = n_gram_dict[gram]['conversions']
        cost = n_gram_dict[gram]['cost']
        conversion_value = n_gram_dict[gram]['conversion_value']

        try:
            cpa = cost / conversions
        except ZeroDivisionError:
            cpa = 0

        try:
            roas = conversion_value / cost
        except ZeroDivisionError:
            roas = 0

        try:
            ctr = clicks / impressions
        except ZeroDivisionError:
            ctr = 0
        try:
            conversion_rate = conversions / clicks
        except ZeroDivisionError:
            conversion_rate = 0
        try:
            average_cpc = cost / clicks
        except ZeroDivisionError:
            average_cpc = 0
        if clicks != 0 and clicks != 1:
            std = np.sqrt(clicks * (1 - ctr) ** 2 + \
                          (impressions - clicks) * ctr ** 2) / (impressions - 1)
            standard_error = std / np.sqrt(impressions)
        else:
            standard_error = 0
        min_result = ctr - standard_error * 2
        max_result = ctr + standard_error * 2
        n_gram_df_data[gram] = {'n_gram_count': count, 'impressions': impressions,
                                'ctr': ctr, 'conversion_rate': conversion_rate, \
                                'average_cpc': average_cpc, 'ctr_significance': standard_error,
                                'conversions': conversions,
                                'cost': cost, 'conversion_value': conversion_value, 'cpa': cpa, 'roas': roas,
                                'clicks': clicks}

    df = pd.DataFrame(n_gram_df_data)
    df = df.T

    df["ctr_significance"] = df["ctr_significance"].replace(r'^\s*$', 0, regex=True).astype("float")

    return df


# grabs the necessary information from the search_query performance reports table
def dataFrameFromSearchQueryPerformanceReports(settings, account_id, date_range):
    engine = Database().createEngine()

    query = """
    SELECT search_queries.query,
    search_query_performance.impressions,
    search_query_performance.clicks,
    search_query_performance.cost,
    search_query_performance.conversions,
    search_query_performance.conversion_value
    from search_queries
    join search_query_performance
    where search_query_performance.search_query_id = search_queries.id
    and search_queries.account_id = "%s"
    and search_query_performance.date_range = "%s"
    """ % (account_id, date_range)

    search_query_performance_df = pd.read_sql_query(query, engine)
    # print query

    if search_query_performance_df.shape[0] == 0:
        print("search_query performance df is empty")
        return
    return search_query_performance_df


def deleteFromSearchQueryNGrams(settings, account_id):
    engine = Database().createEngine()

    query = """
    delete from search_query_n_gram_performance
    where account_id = "%s"
    """ % (account_id)

    engine.execute(query)


if __name__ == "__main__":
    # testing
    settings = Settings()

    account_id = "f24c250e-b71a-4f48-acdb-a02637ab76f6"

    startTime = datetime.now()

    main(account_id, settings)
    endTime = datetime.now()
    print("search_query_n_grams.py completed in " + str(datetime.now() - startTime))
