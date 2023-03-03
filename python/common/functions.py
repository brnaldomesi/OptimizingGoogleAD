# coding: utf-8
import hashlib
import traceback
import uuid
from datetime import datetime, timedelta

import numpy as np
import pandas as pd

from common.Database import Database
from common.Settings import Settings
from common.helpers.LocalDates import LocalDates
#
# Helper functions
#

# append dataframe to a table
# replacing inf with 0 to avoid this error explained here: https://github.com/PyMySQL/mysqlclient-python/issues/246
def append_df_to_sql_table(df, table_name, engine=None):
    if dfIsEmpty(df):
        return
    df = df.replace([np.inf, -np.inf], 0)
    # df = df.replace(r'\s+', np.nan, regex=True)
    df = trimDfToTableColumns(df, table_name)
    df.to_sql(table_name, Database().createEngine(), if_exists='append', index=False)


def create_hash_id(self, text):
    m = hashlib.md5()
    m.update("".join(list(text.values)))
    return str(int(m.hexdigest(), 16))[0:12]


def deleteThisAccountsDataFromTable(table_name, account_id, engine):
    deleteQuery = "delete from %s where account_id = '%s'" % (table_name, account_id)
    engine.execute(deleteQuery)


def getNumDaysBetweenTwoDates(fromDate, toDate):
    delta = toDate - fromDate
    daysInRange = delta.days + 1
    return daysInRange


def dateRangeFromDays(date_range, account_id):
    if date_range.find(',')>0:
        return date_range#if it has a comma it's already been formatted

    if date_range.lower() == "today":
        return datetime.strftime(LocalDates(account_id).today, '%Y%m%d') + ", " + datetime.strftime(LocalDates(account_id).today, '%Y%m%d')

    if date_range=="THIS_MONTH":
        days = LocalDates(account_id).today.date().day-1
        assert days != 0 #we can't get this month on the 1st
    else:
        days = int(date_range.split("_")[1])

    start_date = datetime.strftime(LocalDates(account_id).today - timedelta(days), '%Y%m%d')
    end_date = datetime.strftime(LocalDates(account_id).yesterday, '%Y%m%d')
    return start_date + ", " + end_date


def getNumDaysFromDateRangeString(date_range_string):
    supported_strings = ["THIS_MONTH"]
    if date_range_string in supported_strings:
        return date_range_string

    return int(date_range_string.split("_")[1])


def dfIsEmpty(df):
    if df is None:
        return True
    try:
        return df.empty
    except AttributeError:
        # if there's no empty att it must not exist
        return True

    if df.shape[0] == 0:
        return True


# todo: move this to it's own class
def getTargetCpa(account_id):
    default_cpa = 50

    settings = Settings()
    # account_id = "71211f98-45e6-475e-942f-e637289f6cce"
    query = "select kpi_name,kpi_value from accounts where id = '%s'" % (account_id)
    df = pd.read_sql_query(query, Database().createEngine())
    if dfIsEmpty(df):
        print("Can't find the target cpa for this account, using the default (%s)") % (default_cpa)
        return default_cpa

    df = df.head(1)

    kpi_name = list(df["kpi_name"].values)[0]
    kpi_value = list(df["kpi_value"].values)[0]
    if kpi_name is None:
        kpi_name = "cpa"  # default, #todo grab a default based on performance
        kpi_value = 50

    if kpi_name == "cpa":
        target_cpa = kpi_value
    else:
        target_cpa = 50

    return target_cpa


def createUuid(df):
    df["id"] = [str(uuid.uuid1()) for _ in range(len(df.index))]
    if len(df["id"]) != len(df["id"].drop_duplicates()):
        print("Ids aren't unique: %s ids Vs %s ids" % (len(df["id"]), len(df["id"].drop_duplicates())))
        df.to_csv("error.csv")
        raise Exception("Error: IDs aren't unique!")
    return df


def createId(string1="", string2="", string3=""):
    import hashlib
    m = hashlib.md5()
    m.update(str(string1).encode('utf-8') + str(string2).encode('utf-8') + str(string3).encode('utf-8'))
    return str(m.hexdigest())[0:36]


def addIdToDf(string1="", string2="", string3=""):
    assert string1 is not None
    assert string2 is not None
    assert string3 is not None
    return createId(string1, string2, string3)


# get the columns from the table
# trim to those columns
# add any missing columns
def trimDfToTableColumns(df, table_name):
    if dfIsEmpty(df):
        raise Exception("Error: can't trim an empty dataframe!")
    settings = Settings()
    columns = list(
        pd.read_sql("select * from %s where id = '999999999'" % (table_name), Database().createEngine()).columns)
    for col in columns:
        if col not in df.columns:
            df[col] = None
    df = df[columns]
    return df


def addCalculatedMetricsToDataFrame(df):
    # #add the calculated metrics (ctr, etc)
    settings = Settings()
    for metric in settings.calculatedMetrics:
        if settings.calculatedMetrics[metric][1] == "/":
            df[metric] = df[settings.calculatedMetrics[metric][0]] / \
                         df[settings.calculatedMetrics[metric][2]]
        if settings.calculatedMetrics[metric][3]:
            df[metric] = df[metric] * 100

    return df


def deleteAllFromTable(table_name, account_id, engine):
    deleteQuery = "delete from %s where account_id = '%s' " % (table_name, account_id)

    try:
        engine.execute(deleteQuery)

    except Exception as e:
        if "1146" in traceback.format_exc():
            print("Table doesn't exist, suppressing the error")
        else:
            # it's some other error
            raise Exception(e)
