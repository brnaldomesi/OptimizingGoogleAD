#!/usr/bin/env python
#
# Copyright 2016 Google Inc. All Rights Reserved.
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#      http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.

"""This example gets all campaigns.

To add a campaign, run add_campaign.py.

The LoadFromStorage method is pulling credentials and properties from a
"googleads.yaml" file. By default, it looks for this file in your home
directory. For more information, see the "Caching authentication information"
section of our README.

"""
from __future__ import division

import inspect
import os
import sys

import pandas as pd
from googleads import adwords

currentdir = os.path.dirname(os.path.abspath(inspect.getfile(inspect.currentframe())))
parentdir = os.path.dirname(currentdir)
sys.path.insert(0, parentdir)

from common.Settings import Settings
from common.Database import Database

PAGE_SIZE = 100


def main(client):
    # Initialize appropriate service.
    campaign_service = client.GetService('CampaignService', version='v201809')

    # Construct selector and get all campaigns.
    offset = 0
    selector = {
        'fields': ['Id', 'Name', 'Status'],
        'paging': {
            'startIndex': str(offset),
            'numberResults': str(PAGE_SIZE)
        }
    }

    more_pages = True
    while more_pages:
        page = campaign_service.get(selector)

        # Display results.
        if 'entries' in page:
            for campaign in page['entries']:
                print('Campaign with id "%s", name "%s", and status "%s" was '
                      'found.' % (campaign['id'], campaign['name'],
                                  campaign['status']))
        else:
            print('No campaigns were found.')

        offset += PAGE_SIZE
        selector['paging']['startIndex'] = str(offset)
        more_pages = offset < int(page['totalNumEntries'])


# grab the client id and refresh token from the db


if __name__ == '__main__':
    this_folder = os.path.dirname(os.path.abspath(__file__))
    yaml_path = os.path.abspath(os.path.join(this_folder, 'googleads.yaml'))
    adwords_client = adwords.AdWordsClient.LoadFromStorage(yaml_path)

    user_id = "3a556899-947e-48b7-868b-8db49000ffc8"
    query = 'SELECT temp_google_customer_id FROM users where id = "' + user_id + '"'
    print(query)
    df = pd.read_sql_query(query, Database().createEngine())
    # print df
    if df.shape[0] == 0:
        # note the error in the exemption will trigger in this case
        raise ValueError("There was a problem downloading the API data. Client id not found.")

    print(df)
    customer_id = df.temp_google_customer_id.values[0]
    print(customer_id)

    adwords_client.SetClientCustomerId(customer_id)
    main(adwords_client)
