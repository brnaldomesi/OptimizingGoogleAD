import unittest

from common.Database import Database

class TestDatabase(unittest.TestCase):

    def test_connection(self):

        try:
            query = "select id from accounts"
            (Database()).createEngine().execute(query)
        except Exception as e:
            print(e)
            self.fail("test_connection failed")
