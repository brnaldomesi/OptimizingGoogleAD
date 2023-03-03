# coding: utf-8
from api.mutations_queue.Update import Update


class BidStrategies(object):
    """Methods to allow:
    Getting current bid strategies and settings
    Setting new bid strategies from the mutations queue
    """

    # TODO: This is a work in progress, mainly stashing code here

    def __init__(self):
        pass

    def getStrategies(self):
        google_service_type = "BiddingStrategyService"
        client = Update(account_id, google_service_type).getClient()
        service = client.GetService(google_service_type, version='v201809')

        PAGE_SIZE = 100
        offset = 0

        selector = {
            'fields': [
                'BiddingScheme', 'Name', 'Id', 'Status', 'Type'
            ],
            'paging': {
                'startIndex': str(offset),
                'numberResults': str(PAGE_SIZE)
            }
        }

        results = []

        more_pages = True
        while more_pages:
            page = service.get(selector)

            # Display results.
            if 'entries' in page:
                for result_set in page['entries']:
                    results.append(result_set)
                else:
                    print('No results were found.')
                    offset += PAGE_SIZE

            selector['paging']['startIndex'] = str(offset)
            more_pages = offset < int(page['totalNumEntries'])

    def getKeys(self):
        """Get keys from an AdWords API Selector response
        Use this to create tables full of information e.g bid strategies
        """

        type_list = [str, long, float, int, bool]
        keys = []
        for key in results[0]:
            keys.append(key)
            if type(results[0][key]) in type_list:
                continue

            for second_key in results[0][key]:
                keys.append(second_key)
                if type(results[0][key][second_key]) in type_list:
                    continue

                for third_key in results[0][key][second_key]:
                    keys.append(third_key)

        return keys
