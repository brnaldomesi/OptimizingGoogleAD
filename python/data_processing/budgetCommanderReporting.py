#!/usr/bin/env python
from __future__ import division

import pandas as pd

from common.Log import Log
from common.Settings import Settings
from common.Database import Database

import calendar
import common.functions as functions
from datetime import datetime, timedelta, date

settings = Settings()
date_range = "last_112_days"
write_to_table = "accounts"


def main(account_id):
    Log("info", "processing budget commander data", "", account_id)

    account_performance_by_day = getAccountPerformanceByDay("account_performance_reports", account_id, date_range)

    if functions.dfIsEmpty(account_performance_by_day):
        return

    addBudgetActualGraphDataToDb(account_performance_by_day, account_id)


def addBudgetActualGraphDataToDb(account_performance_by_day, account_id):
    budget_actual_graph_data = "[" + ','.join(map(str, account_performance_by_day.running_total.values)) + "]"
    query = "update %s set %s = '%s' where id = '%s'" % (
    write_to_table, "budget_actual_graph_data", budget_actual_graph_data, account_id)
    Database().createEngine().execute(query)


def forecastCost(account_performance_by_day):
    days_ago = 30
    for i in range(0, days_ago):

        now = datetime.now()
        n_days_ago = (now - timedelta(days=days_ago)).date()
        todays_date = (now + timedelta(days=i)).date()  # today relative to the loop
        seven_days_ahead = (now + timedelta(days=(6))).date()  # 7 days ahead of todays real world date. 6 because it includes today

        if todays_date.day == 1:
            break  # break when we reach next month

        if days_ago < 24:  # after calculating 7 days based on averages, extend the 8th day linear to the end of the month
            average = account_performance_by_day[account_performance_by_day.date == pd.to_datetime(seven_days_ahead)].cost.values[0]
        else:
            average = account_performance_by_day[(account_performance_by_day.date >= pd.to_datetime(n_days_ago)) & (
                        account_performance_by_day.date < pd.to_datetime(todays_date))].cost.mean()

        account_performance_by_day.loc[account_performance_by_day['date'] == pd.to_datetime(todays_date), 'cost'] = average

        days_ago = days_ago - 1

    # just return this month
    first_date_of_this_month = now.replace(day=1).date()
    last_date_of_this_month = now.replace(day=getDaysInMonth()).date()
    return account_performance_by_day[(account_performance_by_day.date >= pd.to_datetime(first_date_of_this_month)) & (account_performance_by_day.date <= pd.to_datetime(last_date_of_this_month))]


def createEmptyDataFrameForThisMonth():
    now = datetime.now()
    days_gone_by_this_month = now.date().day
    num_days = getDaysInMonth()
    days = [str(date(now.year, now.month, day)) for day in range(days_gone_by_this_month, num_days + 1)]
    naughts = [0 for i in range(days_gone_by_this_month, num_days + 1)]
    df = pd.DataFrame(
        {'date': days,
         'cost': naughts
         })

    # df['date'] = df['date'].astype('datetime64[ns]')

    return df


def getAccountPerformanceByDay(table_name, account_id, date_range):
    engine = Database().createEngine()
    query = "select cost,clicks,conversion_value,conversions,date from %s where account_id = '%s' and date_range = '%s'" % (
    table_name, account_id, date_range)
    df = pd.read_sql_query(query, engine)
    # df['date'] = df['date'].astype('datetime64[ns]')

    this_month_df = createEmptyDataFrameForThisMonth()
    # df['date'] = pd.datetime(df['date']).dt.date
    df = df.append(this_month_df)
    df["date"] = pd.to_datetime(df["date"])

    df = df.sort_values("date")
    df = forecastCost(df)
    df["day_budget"] = getDailyBudget(account_id)
    df["running_budget"] = df["day_budget"].cumsum()
    df["running_total"] = df["cost"].cumsum()
    df = df.round(2)

    return df


def getDailyBudget(account_id):
    query = 'SELECT budget FROM accounts where id = "%s"' % (account_id)
    accounts_table = pd.read_sql_query(query, Database().createEngine())
    budget = accounts_table["budget"].values[0]
    if budget is None: budget = 0
    daily_budget = budget / getDaysInMonth()
    return daily_budget


def getDaysInMonth():
    now = datetime.now()
    year = now.year
    month = now.month
    num_days = calendar.monthrange(year, month)[1]
    return num_days
