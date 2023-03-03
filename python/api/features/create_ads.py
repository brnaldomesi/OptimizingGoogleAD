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

from api.features.CreateAds import CreateAds

currentdir = os.path.dirname(os.path.abspath(inspect.getfile(inspect.currentframe())))
parentdir = os.path.dirname(currentdir)
sys.path.insert(0, parentdir)

AD_GROUP_ID = '7591698947'
NUMBER_OF_ADS = 1

ACCOUNT_ID = "f24c250e-b71a-4f48-acdb-a02637ab76f6"


def main():
    # This will read from the queue in reality
    # Setup a Queue class which will read from the queue and create the new_ads dict
    # #

    account_id = ACCOUNT_ID
    ad_group_id = AD_GROUP_ID

    new_ads = {
        "0": {
            'headlinePart1': '24/7 Plumbing Services',
            'headlinePart2': 'You Name It, We Can Fix It',
            'headlinePart3': 'Free On-Site Estimate',
            'description': 'Fast, Affordable, Licenced Commercial Plumbing Services Available in 24 Hours',
            'description2': '0% Finance Options Available - Call Now for a Free Estimate!',
            'path1': 'Plumbing',
            'path2': 'Services',
            'finalUrls': ['http://boydrogersplumbing.com/'],
            'ad_group_id': ad_group_id,
        }
    }

    CreateAds(ACCOUNT_ID, new_ads)


if __name__ == '__main__':
    # Initialize client object.

    main()
