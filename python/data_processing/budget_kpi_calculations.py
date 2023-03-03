from __future__ import division

import calendar
from datetime import datetime, date

import numpy as np
import pandas as pd

from common.Database import Database


# Populate the adgroups and adgroup_performance tables
def budget_kpi_calculations(account_performance_by_day, account_id, settings):
    # Last 30 days - may need this later
    # today = date.today()
    # num_days = 30
    # days = [str(today - timedelta(x)) for x in range(1, num_days + 1)]

    now = datetime.now()
    year = now.year
    month = now.month
    num_days = calendar.monthrange(year, month)[1]
    days = [str(date(now.year, now.month, day)) for day in range(1, num_days + 1)]
    naughts = [0 for i in range(1, num_days + 1)]
    df = pd.DataFrame(
        {'date': days,
         'cost': naughts
         })

    by_day_df = account_performance_by_day
    by_day_df = by_day_df[by_day_df.index.isin(days)].reset_index()
    by_day_df.date = by_day_df.date.astype(datetime)

    def dateToString(row):
        return str(row.date())

    by_day_df.date = by_day_df.date.apply(lambda row: dateToString(row))
    df = df.merge(by_day_df, how="outer").fillna(0).groupby("date").sum().reset_index()
    days_gone_by = datetime.now().day - 1
    db_vars = {}
    metrics = ["cost", "impressions", "clicks", "conversions", "conversion_value"]
    for metric in metrics:
        average = np.mean(df[metric][df[metric] > 0])
        df[metric] = np.where(df[metric] == 0, average, df[metric])

    # add running total columns (_cum columns)
    # then add the calculated metrics (ctr, etc) based off them
    for metric in metrics:
        df[metric + "_cum"] = df[metric].cumsum()
    for metric in settings.calculatedMetrics:
        if settings.calculatedMetrics[metric][1] == "/":
            df[metric] = df[settings.calculatedMetrics[metric][0] + "_cum"] / \
                         df[settings.calculatedMetrics[metric][2] + "_cum"]
        if settings.calculatedMetrics[metric][3]:
            df[metric] = df[metric] * 100
    # get the budget and kpi target (and type) from the db
    query = 'SELECT budget,kpi_value,kpi_name FROM accounts where id = "%s"' % (account_id)
    accounts_table = pd.read_sql_query(query, Database().createEngine())
    budget = accounts_table["budget"].values[0]
    kpi_value = accounts_table["kpi_value"].values[0]
    kpi_name = accounts_table["kpi_name"].values[0]

    if budget is None: budget = 0
    if kpi_value is None: kpi_value = 0
    if kpi_name is None: kpi_name = "impressions"

    df = df.fillna(0)

    # have all of the data
    # now need to calculate the forecast for the budget and kpi then push to the db

    # budget Vs cost list
    # cost list
    daily_forecast_cost = []
    for i, cost in enumerate(df['cost']):
        cost = round(cost, 2)
        if i == 0:
            daily_forecast_cost.append(cost)
            continue
        daily_forecast_cost.append(daily_forecast_cost[len(daily_forecast_cost) - 1] + cost)

    # budget list
    daily_budget = []
    daily_budget_amount = budget / len(daily_forecast_cost)
    for i, cost in enumerate(df['cost']):
        if i == 0:
            daily_budget.append(daily_budget_amount)
            continue
        daily_budget.append(daily_budget[len(daily_budget) - 1] + daily_budget_amount)
    db_vars['budget_target_graph_data'] = '"' + str(daily_budget) + '"'
    db_vars['budget_actual_graph_data'] = '"' + str(daily_forecast_cost) + '"'

    # get the budget percentages for the accounts page
    actual = daily_forecast_cost[days_gone_by - 1]
    db_vars['budget_actual_vs_target'] = getVsPercentage(actual, budget)
    actual = daily_forecast_cost[len(daily_forecast_cost) - 1]
    db_vars['budget_forecast_vs_target'] = getVsPercentage(actual, budget)

    # kpi
    daily_forecast_kpi = []
    for i, kpi in enumerate(df[kpi_name]):
        if kpi_name in ['roas', 'conversion_rate', 'ctr', 'cpa']:
            daily_forecast_kpi.append(kpi)
        else:
            if i == 0:
                daily_forecast_kpi.append(kpi)
                continue
            daily_forecast_kpi.append(daily_forecast_kpi[len(daily_forecast_kpi) - 1] + kpi)

    daily_kpi_target = []
    for i, cost in enumerate(df[kpi_name]):
        if kpi_name in ['roas', 'conversion_rate', 'ctr', 'cpa']:
            daily_kpi_target.append(kpi_value)
            continue
        else:
            daily_kpi_target_value = kpi_value / len(daily_forecast_kpi)
        if i == 0:
            daily_kpi_target.append(daily_kpi_target_value)
            continue
        daily_kpi_target.append(daily_kpi_target[len(daily_kpi_target) - 1] + daily_kpi_target_value)

    db_vars['kpi_target_graph_data'] = '"' + str(daily_kpi_target) + '"'
    db_vars['kpi_actual_graph_data'] = '"' + str(daily_forecast_kpi) + '"'

    # get the budget percentages for the accounts page
    actual = daily_forecast_kpi[days_gone_by - 1]
    db_vars['kpi_actual_vs_target'] = getVsPercentage(actual, kpi_value)
    actual = daily_forecast_kpi[len(daily_forecast_kpi) - 1]
    db_vars['kpi_forecast_vs_target'] = getVsPercentage(actual, kpi_value)

    # update the db
    for key in db_vars:
        value = db_vars[key]
        query = "update accounts set %s = '%s' where id = '%s'" % (key, value, account_id)
        Database().createEngine().execute(query)


def getVsPercentage(actual, target):
    if target == 0 and actual > 0:
        return 100
    if target == 0:
        return 0
    return round((actual / target) * 100, 0)
