#!/usr/bin/env python


# go through the ad_creation_queue table and create the ads one by one


"""This example adds expanded text ads to a given ad group.

To get ad_group_id, run get_ad_groups.py.

The LoadFromStorage method is pulling credentials and properties from a
"googleads.yaml" file. By default, it looks for this file in your home
directory. For more information, see the "Caching authentication information"
section of our README.

"""

from __future__ import division

import inspect
import os
import sys

from api.features.UpdateKeywordBids import UpdateKeywordBids

currentdir = os.path.dirname(os.path.abspath(inspect.getfile(inspect.currentframe())))
parentdir = os.path.dirname(currentdir)
sys.path.insert(0, parentdir)

AD_GROUP_ID = '7591698947'
ACCOUNT_ID = "f24c250e-b71a-4f48-acdb-a02637ab76f6"
CRITERION_ID = '5925142433'


def main():
    # Initialize appropriate service.
    new_bid = 8
    new_micro_bid = int(new_bid * 10) * 100000

    account_id = ACCOUNT_ID
    ad_group_id = AD_GROUP_ID

    criterion_ids = [CRITERION_ID, "24612163"]

    changes = {}
    for i, criterion_id in enumerate(criterion_ids):
        changes[str(i)] = {
            "criterion_id": criterion_id,
            "ad_group_id": ad_group_id,
            "new_micro_bid": new_micro_bid,
        }

    UpdateKeywordBids(account_id, changes)


if __name__ == '__main__':
    # Initialize client object.

    main()
