from __future__ import division

import numpy as np
import pandas as pd
from data_processing.Confidence import Confidence


class AdPerformanceReport(object):

    def __init__(self):
        self.test = "Working!"

    def createAdPerformanceReport(self, raw_df):
        raw_df["UniqueAdGroupId"] = (raw_df["CampaignId"].map(str) + "/" + raw_df["AdGroupId"].map(str))
        raw_df["UniqueAdId"] = (
                    raw_df["CampaignId"].map(str) + "/" + raw_df["AdGroupId"].map(str) + "/" + raw_df["Id"].map(str))

        # add the first ad impression stamp
        raw_df = self.addFirstImpressionDate(raw_df)

    def addFirstImpressionDate(self, raw_df):
        print("addFirstImpressionDate")
        t_df = raw_df[["Date", "Id", "Impressions"]][raw_df.Impressions > 0]
        adIds = []
        for row in t_df.itertuples():
            if row[2] not in adIds:
                adIds.append(row[2])
        for ad in adIds:
            t_df.loc[t_df['Id'] == ad, 'ad_first_impression'] = min(t_df[t_df.Id == ad].Date)

        raw_df = raw_df.merge(
            t_df[["Id", "ad_first_impression"]].groupby(["Id", "ad_first_impression"]).count().reset_index())
        return raw_df

    def pivotByDate(self, df):
        # add impression multiples
        # calculated fields need to pull in from somewhere
        calculatedFields = ["AveragePosition"]
        impressionMultiples = []
        for field in calculatedFields:
            newField = "%sxImpressions" % (field)
            impressionMultiples.append(newField)
            df[newField] = df[field] * df.Impressions

        # pivot, removing date
        cols = list(df.columns)
        # these values need to pull in from somewhere
        values = ["Impressions", "Clicks", "Cost", "AveragePosition", "Conversions",
                  "ConversionValue"] + impressionMultiples
        for value in values:
            cols.remove(value)
        cols.remove("Date")
        df = pd.pivot_table(df, index=cols, values=values, aggfunc=np.sum)
        df.reset_index(inplace=True)

        # add the calculated fields back in
        for i, field in enumerate(calculatedFields):
            df[field] = df[impressionMultiples[i]] / df.Impressions
            del df[impressionMultiples[i]]

        return df

    def addAdCount(self, df):
        # add the ad count
        ad_count = pd.DataFrame(df.groupby('UniqueAdGroupId')['Id'].nunique()).sort_values("Id", ascending=False)
        ad_count.reset_index(inplace=True)
        ad_count.rename(columns={'Id': 'ad_count'}, inplace=True)
        df = df.merge(ad_count, how="outer")
        return df

    def addMessages(self, df):
        adf = df[["UniqueAdGroupId", "Id", "Clicks", "Impressions", "ad_count"]]
        adf["adNumber"] = adf.groupby("UniqueAdGroupId").cumcount() + 1
        adf = adf.groupby(["UniqueAdGroupId", "adNumber"]).sum().unstack().fillna(0)
        # move down
        # create metricwithnumber
        # move back up
        adf = adf.T.reset_index()
        adf["metricNum"] = adf["level_0"] + adf["adNumber"].map(str)
        maxAds = max(adf.adNumber.values)
        del adf["level_0"]
        del adf["adNumber"]
        adf = adf.set_index(["metricNum"]).T.reset_index()

        for ad in range(1, maxAds + 1):
            adf["Ctr%s" % (ad,)] = (adf["Clicks%s" % (ad,)] / adf["Impressions%s" % (ad,)]) * 100

        # df[['C','D']] = df['B'].apply(lambda x: pd.Series([f(x)[0],f(x)[1]]))
        adf["winning_ad"] = adf.apply(lambda row: getSignificance(row, maxAds, True), axis=1)
        adf["significance"] = adf.apply(lambda row: getSignificance(row, maxAds, False), axis=1)
        adf["losing_ad"] = adf.apply(lambda row: getLosingAd(row, maxAds), axis=1)

        bdf = ""
        del bdf
        for i in range(1, maxAds + 1):
            tdf = adf[
                ["UniqueAdGroupId", "significance", "winning_ad", "losing_ad", "Id%s" % (i,), "ad_count%s" % (i,)]]
            tdf = tdf.rename(columns={"Id%s" % (i,): "Id", "ad_count%s" % (i,): "ad_count"})
            tdf["ad_number"] = i
            try:
                bdf = bdf.append(tdf)
            except:
                bdf = tdf.copy()
        del tdf

        bdf.loc[bdf['ad_count'] == 1, 'message'] = 'Too few ads'
        bdf.loc[bdf['winning_ad'] == bdf['ad_number'], 'message'] = 'winning'
        bdf.loc[bdf['losing_ad'] == bdf['ad_number'], 'message'] = 'losing'
        bdf.loc[bdf['winning_ad'] != bdf['ad_number'], 'significance'] = 0
        del bdf["ad_number"]
        del bdf["winning_ad"]
        del bdf["losing_ad"]
        bdf.reset_index(drop=True)

        mdf = df.merge(bdf).sort_values("UniqueAdGroupId")
        mdf["Ctr"] = mdf.Clicks / mdf.Impressions
        return mdf


# helper functions
def getSignificance(row, maxAds, returnWinner):
    # set the winner to zero as a fallback
    winningNumber = 0
    # need to push the highest ctr values frist, then the 2nd best 2nd
    if row.ad_count1 == 1:
        return 0
    # get the highest ctr ad
    ctrs = []
    for i in range(1, maxAds + 1):
        ctrs.append(row["Ctr%s" % (i,)])
    winningCtr = max(ctrs)
    winningNumber = ctrs.index(winningCtr) + 1
    # get the 2nd highest ctr ad
    ctrs.remove(winningCtr)
    secondCtr = max(ctrs)
    secondNumber = ctrs.index(secondCtr) + 1
    if min(winningCtr, secondCtr) == 0:
        return 0
    if returnWinner:
        return winningNumber
    if min(row["Clicks%s" % (winningNumber)], row["Clicks%s" % (secondNumber)]) == 0:
        return 0
    if min(row["Impressions%s" % (winningNumber)], row["Impressions%s" % (secondNumber)]) < 2:
        return 0
    # numbers = [row["Impressions%s"%(winningNumber)],row["Clicks%s"%(winningNumber)],row["Impressions%s"%(secondNumber)],row["Clicks%s"%(secondNumber)]]
    calc = Confidence()
    return calc.calculate(row["Impressions%s" % (secondNumber)], row["Clicks%s" % (secondNumber)],
                          row["Impressions%s" % (winningNumber)], row["Clicks%s" % (winningNumber)])


def getLosingAd(row, maxAds):
    ctrs = []
    for i in range(1, maxAds + 1):
        ctrs.append(row["Ctr%s" % (i,)])
    losingCtr = min(ctrs)
    losingNumber = ctrs.index(losingCtr) + 1
    if losingCtr == 0:
        return 0
    return losingNumber
