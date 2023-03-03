#!/usr/bin/env python
from __future__ import division

import inspect
import os
import sys

from api.mutations_queue.Update import Update

currentdir = os.path.dirname(os.path.abspath(inspect.getfile(inspect.currentframe())))
parentdir = os.path.dirname(currentdir)
sys.path.insert(0, parentdir)


class CreateAds(Update):

    # Arg 1 is the account id
    # Arg 2 is a dict of new_ads with each sub-dict containing an adgroup_id, criterion_id (keyword id) and new_bid

    def __init__(self, account_id, new_ads):
        self.account_id = account_id
        self.new_ads = new_ads
        self.service_type = "AdGroupAdService"
        self.operations = self.getOperations()
        self.update = Update(self.account_id, self.service_type, self.operations)
        self.sendRequest()

    def getOperations(self):
        # TODO Chunk into 2000 or fewer changes

        operations = [
            {
                'operator': 'ADD',
                'operand': {
                    'xsi_type': 'AdGroupAd',
                    'adGroupId': self.new_ads[unique_id]["ad_group_id"],
                    'ad': {
                        'xsi_type': 'ExpandedTextAd',
                        'headlinePart1': self.new_ads[unique_id]["headlinePart1"],
                        'headlinePart2': self.new_ads[unique_id]["headlinePart2"],
                        'headlinePart3': self.new_ads[unique_id]["headlinePart3"],
                        'description': self.new_ads[unique_id]["description"],
                        'description2': self.new_ads[unique_id]["description2"],
                        'path1': self.new_ads[unique_id]["path1"],
                        'path2': self.new_ads[unique_id]["path2"],
                        'finalUrls': self.new_ads[unique_id]["finalUrls"],
                    },
                    # Optional fields.
                    'status': 'ENABLED'
                }
            } for unique_id in self.new_ads]

        return operations

    def checkOperations(self):
        # keyword specific checks here

        pass

    def convertToMicros(self, bid):
        return int(bid * 10) * 100000

    def sendRequest(self):
        keyword_response = self.update.send_request()
        # print "Keyword response:"
        # print keyword_response
        # add the response to the database here


if __name__ == '__main__':
    # Initialize client object.
    pass

#   updateKeyword = Update.UpdateKeyword(account_id, service_type, operations)
