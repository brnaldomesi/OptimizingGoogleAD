# coding: utf-8
import uuid
from datetime import datetime

import numpy as np
import pandas as pd
from sqlalchemy import create_engine, MetaData, Table
from sqlalchemy.sql import insert

from common.Env import Env

class Database(object):

    def __init__(self):
        self.env_vars = Env().vars

    def createEngine(self):
        # 192.168.10.10
        connection_data = ('{connection}+pymysql://{username}:{password}@{host}:{port}/{database}?charset=utf8'.format(
            connection=self.env_vars["DB_CONNECTION"],
            username=self.env_vars["DB_USERNAME"],
            password=self.env_vars["DB_PASSWORD"],
            host=self.env_vars["DB_HOST"],
            port=self.env_vars["DB_PORT"],
            database=self.env_vars["DB_DATABASE"]
        ))

        # print "Connecting to database: %s" %(env_vars["DB_DATABASE"],)
        return create_engine(connection_data)

    def getValueFromTable(self, value, table, where):
        query = "select %s from %s where %s " % (value, table, where)
        result = self.createEngine().execute(query)
        for row in result:
            return row[0]

    def executeQuery(self, query):
        return self.createEngine().execute(query)

    def insertRow(self, table_name, data):
        engine = self.createEngine()
        metadata = MetaData(bind=engine)
        table = Table(table_name, metadata, autoload=True)
        i = insert(table)
        i = i.values(data)
        engine.connect().execute(i)

    def setValue(self, table_name, column, value, where_string):
        """Set a single value in a given table"""

        query = """
        UPDATE %s
        SET %s = '%s'
        %s
        """ % (table_name, column, value, where_string)
        self.executeQuery(query)

    def getValue(self, table_name, column, where_string):
        """Get a single value in a given table"""

        query = """
        SELECT %s from %s
        %s
        """ % (column, table_name, where_string)
        result = self.executeQuery(query)
        for row in result:
            return row[0]

    def getValues(self, table_name, columns, where_string=""):
        """Get multiple values from a given table"""

        query = """SELECT %s from %s %s""" % (columns, table_name, where_string)
        # print(query)
        rows = []
        result = self.executeQuery(query)
        for row in result:
            rows.append(row)
        return rows

    def appendRows(self, table_name, dictionary):
        """Appends rows (a dict) to a table."""

        df = pd.DataFrame(data=dictionary).T

        df["created_at"] = datetime.now()
        df["updated_at"] = datetime.now()

        columns = self.getColumns(table_name)
        for column in columns:
            if column not in df.columns:
                df[column] = None

        df = df.reset_index(drop=True)
        df["id"] = pd.Series([uuid.uuid1() for i in range(len(df))]).astype(str)

        self.appendDataframe(table_name, df)

    def appendDataframe(self, table_name, df):
        """Write (append) a dataframe to the table specified"""
        # replacing inf with 0 to avoid this error explained here: https://github.com/PyMySQL/mysqlclient-python/issues/246
        df = df.replace([np.inf, -np.inf], 0)
        df.to_sql(table_name, self.createEngine(), if_exists='append', index=False)

    def getColumns(self, table_name):
        """Get the column names from a table. Returns a list"""

        columns = list(pd.read_sql("select * from %s where id = '999999999'" % (table_name), self.createEngine()).columns)

        if not columns:
            raise Exception("Could not determine columns from table %s" % (table_name))

        return columns
