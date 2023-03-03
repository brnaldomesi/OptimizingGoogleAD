# coding: utf-8
from __future__ import division

import traceback
## my files/classes
import warnings

from common.Log import Log
from common.Settings import Settings
from data_processing import delete, search_query_feed, search_query_n_grams, search_query_n_grams_feed, \
    budgetCommanderReporting, account_performance_changes, advert_feed, ad_n_grams, keyword_feed, adgroups_feed
from data_processing.AdGroupProcessing import AdGroupProcessing
from data_processing.AdvertProcessing import AdvertProcessing
from data_processing.winning_elements.WinningElements import WinningElements

settings = Settings()

warnings.filterwarnings("ignore")

#runs after api/download.py

def processAccount(account_id):
    Log("info", "processing report data", "", account_id)
    # --delete existing entries and populate settings with ids

    # try:
    #     search_query_feed.main(account_id)
    # except Exception as exception:
    #     Log("error", str(exception), traceback.format_exc(), account_id)
        
    # try:
    #     search_query_n_grams.main(account_id)
    # except Exception as exception:
    #     Log("error", str(exception), traceback.format_exc(), account_id)

        
    # try:
    #     search_query_n_grams_feed.main(account_id)
    # except Exception as exception:
    #     msg = "failed to process search_query_n_grams_feed"
    #     Log("error", msg, traceback.format_exc(), account_id)

    try:
        budgetCommanderReporting.main(account_id)
    except Exception as exception:
        Log("error", str(exception), traceback.format_exc(), account_id)
        
    # try:
    #     account_performance_changes.main(account_id)
    # except Exception as exception:
    #     Log("error", str(exception), traceback.format_exc(), account_id)
        
    try:
        AdvertProcessing(account_id).main()
    except Exception as exception:
        Log("error", str(exception), traceback.format_exc(), account_id)
        
    # try:
    #     advert_feed.main(account_id)
    # except Exception as exception:
    #     Log("error", str(exception), traceback.format_exc(), account_id)
        
    try:
        AdGroupProcessing(account_id).main()
    except Exception as exception:
        Log("error", str(exception), traceback.format_exc(), account_id)
        
    try:
        ad_n_grams.main(account_id)
    except Exception as exception:
        Log("error", str(exception), traceback.format_exc(), account_id)
        
    try:
        WinningElements(account_id).main()
    except Exception as exception:
        Log("error", str(exception), traceback.format_exc(), account_id)
        
    # try:
    #     keyword_feed.main(account_id)
    # except Exception as exception:
    #     Log("error", str(exception), traceback.format_exc(), account_id)
        
    # try:
    #     adgroups_feed.main(account_id)
    # except Exception as exception:
    #     Log("error", str(exception), traceback.format_exc(), account_id)
