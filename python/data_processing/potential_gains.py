from __future__ import division

from common.functions import *


def potentialGains(advert_performance, adgroup_performance, account_id, settings):
    if advert_performance is None:
        return
    if len(advert_performance) == 0:
        return

    potential_gains = calculatePotentialGains(advert_performance, adgroup_performance)

    # updates needed:
    # the script isn't taking into account how long each ad has been running
    # we need to therefore first create a df of weighted stats (e.g. clicks/days available * 30)

    df = pd.DataFrame(potential_gains, index=[0])
    df["account_id"] = account_id
    df["date_range"] = "last_30_days"
    # #now let's add created_at and updated_at as today
    df["created_at"] = datetime.now()
    df["updated_at"] = datetime.now()
    df.rename(columns={'cost': 'cost_change'}, inplace=True)

    df["id"] = pd.Series([uuid.uuid1() for i in range(len(df))]).astype(str)
    append_df_to_sql_table(df, "potential_gains", Database().createEngine())


def calculatePotentialGains(advert_performance, adgroup_performance):
    # metrics we'll be calculating for potential gains
    metrics = ["clicks", "cost", "conversions"]

    # trim down so we're only working with what we need
    advert_performance_cols = ["id", "adgroup_id", "ctr_message", "impressions"] + metrics
    advert_performance = advert_performance[advert_performance_cols]

    adgroup_performance_cols = ["adgroup_id", "message"]
    adgroup_performance = adgroup_performance[adgroup_performance_cols]

    has_winners_adgroup_ids = adgroup_performance[adgroup_performance["message"] == "has_winners"][
        "adgroup_id"].drop_duplicates().values
    # won't be needing this anymore
    del adgroup_performance

    potential_gains = {
        "winners": len(has_winners_adgroup_ids),
        "clicks": 0,
        "conversions": 0,
        "cpa": 0,
        "cost": 0
    }

    # totals for calculating CPA change
    totals = {
        "conversions": {"actual": 0, "would_be": 0},
        "cost": {"actual": 0, "would_be": 0},
    }

    if potential_gains["winners"] == 0:
        # no winners found, return
        return potential_gains

    # calculate the would-be stats, minus the actual stats to get the would be increase

    for adgroup_id in has_winners_adgroup_ids:
        br = False
        adgroup = advert_performance[advert_performance.adgroup_id == adgroup_id]
        adgroup_ex_losing = adgroup[adgroup.ctr_message != "losing"]
        num_ads = adgroup.shape[0]

        for metric in metrics:
            total = np.sum(adgroup[metric])
            if metric == "conversions":
                winning_rate = np.sum(adgroup_ex_losing[metric] / adgroup_ex_losing["clicks"])
                would_be_total = np.sum(winning_rate * adgroup["clicks"])
                totals[metric]["actual"] = totals[metric]["actual"] + total
                totals[metric]["would_be"] = totals[metric]["would_be"] + would_be_total
            elif metric == "cost":
                winning_rate = np.sum(adgroup_ex_losing[metric] / adgroup_ex_losing["clicks"])
                would_be_total = np.sum(winning_rate * adgroup["clicks"])
                totals[metric]["actual"] = totals[metric]["actual"] + total
                totals[metric]["would_be"] = totals[metric]["would_be"] + would_be_total
            elif metric == "clicks":
                winning_rate = np.sum(adgroup_ex_losing[metric] / adgroup_ex_losing["impressions"])
                would_be_total = np.sum(winning_rate * adgroup["impressions"])
            difference = would_be_total - total
            potential_gains[metric] = potential_gains[metric] + difference

    actual_cpa = totals["cost"]["actual"] / totals["conversions"]["actual"]
    would_be_cpa = totals["cost"]["would_be"] / totals["conversions"]["would_be"]
    potential_gains["cpa"] = would_be_cpa - actual_cpa

    # round the numbers to 2 places
    for metric in potential_gains:
        if metric == "winners":
            continue
        potential_gains[metric] = round(potential_gains[metric], 2)

    return potential_gains
