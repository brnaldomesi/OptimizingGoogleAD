# coding: utf-8
import unittest

from common.helpers.Currency import Currency
from TestAccount import TestAccount

class TestCurrency(unittest.TestCase):

    def __init__(self, *args, **kwargs):
        super(TestCurrency, self).__init__(*args, **kwargs)
        self.account_id = TestAccount().account_id
        
    def test_get_symbol_from_code(self):
        code = "GBP"

        result = (Currency()).getSymbolFromCode(code)

        expected = chr(163)  # £

        self.assertEqual(result, expected)
