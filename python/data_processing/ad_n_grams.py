# coding: utf-8
from __future__ import division

import uuid
from datetime import datetime

import pandas as pd
from nltk import bigrams, trigrams, word_tokenize

import common.functions as functions
from common.Log import Log
from common.Settings import Settings
from common.Database import Database


def main(account_id):
    settings = Settings()
    Log("info", "processing ad n-grams", "", account_id)

    df = createFullDataFrame(account_id, settings)

    if functions.dfIsEmpty(df):
        return

    df = df.drop_duplicates()

    table_name = "ad_n_gram_performance"

    deleteFromTable(table_name, account_id, Database().createEngine())

    functions.append_df_to_sql_table(df, table_name)


def createFullDataFrame(account_id, settings):
    df = None
    for date_range in settings.date_ranges:

        this_df = processDateRange(date_range, account_id, settings)

        if functions.dfIsEmpty(this_df):
            continue

        this_df = this_df.drop_duplicates('n_gram')
        if functions.dfIsEmpty(df):
            df = this_df.copy()
        else:
            df = df.append(this_df.copy())

    return df


def processDateRange(date_range, account_id, settings):
    ad_performance_df = dataFrameFromAdPerformanceReports(settings, account_id, date_range)

    if ad_performance_df.shape[0] == 0:
        print("ad performance df is empty")
        return

    adgroup_ids = ad_performance_df.adgroup_id.drop_duplicates().values
    n_gram_df_data = []
    for adgroup_id in adgroup_ids:
        for i, row in ad_performance_df[ad_performance_df.adgroup_id == adgroup_id].fillna(0).iterrows():
            n_gram_df_data = createNGramDict(row, n_gram_df_data)

    df = pd.DataFrame(n_gram_df_data)

    df.ctr = df.ctr * 100
    df.conversion_rate = df.conversion_rate * 100
    df.roas = df.roas * 100

    df.n_gram = df.n_gram.apply(lambda n_gram: ' '.join(n_gram).strip())

    df["account_id"] = account_id
    df["date_range"] = date_range
    df["id"] = pd.Series([uuid.uuid1() for i in range(len(df))]).astype(str)
    df["created_at"] = datetime.now()
    df["updated_at"] = datetime.now()

    # serving too many rows will cause php memory issues (and issues with the client)
    # just serve the first X rows, ordered by cost
    # rowsToServe = 10000
    df = df.sort_values("cost", ascending=False)  # .head(rowsToServe)

    # then sort by ctr and add the graph elements (means the n-grams must be in the top 1000 spenders to be in the graph)
    df = df.sort_values(["ctr", "conversions", "clicks", "cost"], ascending=False).reset_index(drop=True)
    df.loc[df.index == 0, 'show_on_graph'] = True
    df.loc[df.index == 1, 'show_on_graph'] = True
    df.loc[df.index == 2, 'show_on_graph'] = True
    df.loc[df.index == 0, 'graph_order'] = 1
    df.loc[df.index == 1, 'graph_order'] = 2
    df.loc[df.index == 2, 'graph_order'] = 3

    df['ctr_significance'] = 0

    columns = ['word_count', "campaign_id", "adgroup_id", "campaign_name", "adgroup_name", "id", "created_at",
               "updated_at", "account_id", "date_range", "n_gram", "n_gram_count", "show_on_graph", "ctr",
               "ctr_significance", "impressions", "clicks", "conversions", "cost", "average_cpc", "conversion_value",
               "cpa", "roas", "conversion_rate", "graph_order"]

    df = df[columns].fillna(0)

    df["ctr_significance"] = df["ctr_significance"].replace(r'^\s*$', 0, regex=True).astype("float")

    return df


def deleteFromTable(table_name, account_id, engine):
    query = "delete from %s where account_id = '%s'" % (table_name, account_id)
    engine.execute(query)


# grabs the necessary information from the ad performance reports table
def dataFrameFromAdPerformanceReports(settings, account_id, date_range):
    engine = Database().createEngine()

    query = """
        SELECT adverts.headline_1, adverts.headline_2,adverts.headline_3, adverts.description,adverts.description_2,adverts.adgroup_id,
        advert_performance.impressions,advert_performance.conversion_value, advert_performance.clicks,advert_performance.conversions, advert_performance.cost,
        adgroups.name as adgroup_name,adgroups.campaign_id,
        campaigns.name as campaign_name
        FROM adverts
        join advert_performance on advert_performance.advert_id = adverts.id
        join adgroups on adgroups.id = adverts.adgroup_id
        join campaigns on campaigns.id = adgroups.campaign_id
        where adverts.account_id = '%s'
        and advert_performance.clicks > 0
        and advert_performance.date_range = '%s'
    """ % (account_id, date_range)

    ad_performance_df = pd.read_sql_query(query, engine)

    return ad_performance_df


def createNGramDict(row, n_gram_df_data):
    n_gram_dict = {}
    impressions = float(row['impressions'])
    clicks = float(row['clicks'])
    conversions = float(row['conversions'])
    conversion_value = float(row['conversion_value'])
    cost = float(row['cost'])

    campaign_name = row['campaign_name']
    adgroup_name = row['adgroup_name']
    campaign_id = row['campaign_id']
    adgroup_id = row['adgroup_id']

    text = (row['headline_1'] + ' ' + row['headline_2'] + ' ' + row['headline_3'] + ' ' + row['description'] + ' ' +
            row['description_2'])

    # tidy up
    text = text.replace('--', '').strip()
    puncts = [",", ".", "!", "?", ":", '-']
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
            n_gram_dict[gram] = {
                'impressions': impressions, 'gram_count': 1, 'clicks': clicks,
                'cost': cost, 'conversions': conversions, 'conversion_value': conversion_value
            }
        else:
            n_gram_dict[gram]['impressions'] += impressions
            n_gram_dict[gram]['gram_count'] += 1
            n_gram_dict[gram]['clicks'] += clicks
            n_gram_dict[gram]['cost'] += cost
            n_gram_dict[gram]['conversions'] += conversions
            n_gram_dict[gram]['conversion_value'] += conversion_value

    for gram in n_gram_dict.keys():
        impressions = n_gram_dict[gram]['impressions']
        count = n_gram_dict[gram]['gram_count']
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

        df_ready_dict = {
            'n_gram': gram, 'word_count': len(gram),
            'n_gram_count': count, 'impressions': impressions,
            'ctr': ctr, 'conversion_rate': conversion_rate,
            'average_cpc': average_cpc,
            'conversions': conversions,
            'cost': cost, 'conversion_value': conversion_value, 'cpa': cpa, 'roas': roas,
            'clicks': clicks,
            'campaign_name': campaign_name,
            'adgroup_name': adgroup_name,
            'campaign_id': campaign_id,
            'adgroup_id': adgroup_id,
        }

        n_gram_df_data.append(df_ready_dict)

    return n_gram_df_data
