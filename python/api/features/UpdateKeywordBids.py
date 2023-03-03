#!/usr/bin/env python
from __future__ import division

import inspect
import os
import sys

from api.mutations_queue.Update import Update

currentdir = os.path.dirname(os.path.abspath(inspect.getfile(inspect.currentframe())))
parentdir = os.path.dirname(currentdir)
sys.path.insert(0, parentdir)


class UpdateKeywordBids(Update):

    # Arg 1 is the account id
    # Arg 2 is a dict of bid_updates with each sub-dict containing an adgroup_id, criterion_id (keyword id) and new_bid

    def __init__(self, account_id, bid_updates):
        self.account_id = account_id
        self.bid_updates = bid_updates
        self.service_type = "AdGroupCriterionService"
        self.operations = self.getOperations()
        self.update = Update(self.account_id, self.service_type, self.operations)
        self.sendRequest()

    def getOperations(self):
        operations = [
            {
                'operator': 'SET',
                'operand': {
                    'xsi_type': 'BiddableAdGroupCriterion',
                    'adGroupId': self.bid_updates[unique_id]["ad_group_id"],
                    'criterion': {
                        'id': self.bid_updates[unique_id]["criterion_id"],
                    },
                    'biddingStrategyConfiguration': {
                        'bids': [
                            {
                                'xsi_type': 'CpcBid',
                                'bid': {
                                    'microAmount': self.convertToMicros(self.bid_updates[unique_id]["bid"])
                                }
                            }
                        ]
                    }
                }
            } for unique_id in self.bid_updates
        ]

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
