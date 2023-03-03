# coding: utf-8
from forex_python.converter import CurrencyCodes

from common.Database import Database


class Currency:
    def getSymbol(self, account_id):
        code = self.getCode(account_id)
        return self.getSymbolFromCode(code)

    def getCode(self, account_id):
        return (Database()).getValueFromTable("currency_code", "accounts", "id = '%s'" % (account_id))

    def getSymbolFromCode(self, code):
        c = CurrencyCodes()
        return c.get_symbol(code)
