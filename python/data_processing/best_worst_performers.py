from __future__ import division

from common.Log import Log
from common.Database import Database
from common.functions import *
from data_processing.winning_elements.WinningElements import WinningElements


def bestWorstPerformers(account_id, settings):
    Log("info", "processing best and worst performers (ads)", "", account_id)

    advert_performance = WinningElements(account_id).getAdvertsDataframe()

    if advert_performance is None:
        return
    if len(advert_performance) == 0:
        return

    # return advert_performance df used for winning elements
    cols = ["id", "cost", "date_range", "ctr", "cpa", "roas", "conversion_rate"]
    advert_performance = advert_performance[cols + ["impressions"]]

    # order by cost desc then trim to the top 20% of rows
    advert_performance = advert_performance.sort_values("cost", ascending=False)
    numRows = int(advert_performance.shape[0] / 5)
    advert_performance = advert_performance.iloc[0:numRows, :]

    advert_performance["advert_id"] = advert_performance["id"]
    advert_performance["account_id"] = account_id
    # #now let's add created_at and updated_at as today
    advert_performance["created_at"] = datetime.now()
    advert_performance["updated_at"] = datetime.now()

    medianCost = advert_performance.cost.median()

    quantileImpressions = advert_performance.impressions.quantile()

    best_performance = advert_performance[
        (advert_performance.cost > medianCost) & (advert_performance.impressions > quantileImpressions)].sort_values(
        "ctr", ascending=False).head(1).reset_index(drop=True)
    best_performance["id"] = pd.Series([uuid.uuid1() for i in range(len(best_performance))]).astype(str)
    del best_performance["impressions"]
    del best_performance["cost"]

    worst_performance = advert_performance[
        (advert_performance.cost > medianCost) & (advert_performance.impressions > quantileImpressions)].sort_values(
        "ctr", ascending=True).head(1).reset_index(drop=True)
    worst_performance["id"] = pd.Series([uuid.uuid1() for i in range(len(worst_performance))]).astype(str)
    del worst_performance["impressions"]
    del worst_performance["cost"]

    append_df_to_sql_table(best_performance, "best_performers", Database().createEngine())
    append_df_to_sql_table(worst_performance, "worst_performers", Database().createEngine())
