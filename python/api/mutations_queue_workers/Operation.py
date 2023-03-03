# coding: utf-8
from __future__ import division

from common.Settings import Settings

settings = Settings()


class Operation(object):
    """ Returns an operation (a dictionary of options) for use with AdWords API mutations
    Supports:
     - Keyword status
     - Keyword bid
     - Ad status
     - Campaign status
     - Expanded Text Ad creation
    """

    def __init__(self, row, service_type, action, attribute=None):
        """

        :param row: the row of data from the table (the class handles one row at a time)
        :param service_type: one of keyword, advert, campaign
        :param action: add or set (create something new or update the entity)
        :param attribute: the attribute we're updating (optional as this isn't required if the entity is being created)
        """
        self.row = row
        self.service_type = service_type
        self.action = action
        self.attribute = attribute

    def get(self):
        if self.action == "set":
            if self.service_type == "keyword" and self.attribute == "status":
                return self.keyword_status_operation()

            if self.service_type == "keyword" and self.attribute == "bid":
                return self.keyword_bid_operation()

            if self.service_type == "advert":
                return self.advert_operation()

            if self.service_type == "campaign" and self.attribute == "status":
                return self.campaign_status_operation()

        if self.action == "add":
            if self.service_type == "advert":
                return self.advert_creation_operation()

    def keyword_status_operation(self):
        """Keyword Operation for Updating (Setting) Statuses"""

        criterion_id, adgroup_id = self.split_entity_google_id(self.row["entity_google_id"])

        operation = {
            'operator': self.row["action"].upper(),
            'operand': {
                'xsi_type': 'BiddableAdGroupCriterion',
                'adGroupId': adgroup_id,
                'criterion': {
                    'id': criterion_id,
                },
                "userStatus": self.row["value"].upper(),
            },
        }

        # TODO: DRY
        if settings.is_batch_job_processing:
            operation.update({'xsi_type': 'AdGroupCriterionOperation'})

        return operation

    def keyword_bid_operation(self):
        """Keyword Operation for Updating (Setting) Bids"""
        row = self.row

        criterion_id, adgroup_id = self.split_entity_google_id(row["entity_google_id"])

        operation = {
            'operator': row["action"].upper(),
            'operand': {
                'xsi_type': 'BiddableAdGroupCriterion',
                'adGroupId': adgroup_id,
                'criterion': {
                    'id': criterion_id,
                },
                'biddingStrategyConfiguration': {
                    'bids': [
                        {
                            'xsi_type': 'CpcBid',
                            'bid': {
                                'microAmount': int((float(row["value"])) * 100) * 10000
                            }
                        }
                    ]
                }
            },
        }

        if settings.is_batch_job_processing:
            operation.update({'xsi_type': 'AdGroupCriterionOperation'})

        return operation

    def advert_operation(self):
        row = self.row
        ad_id, adgroup_id = self.split_entity_google_id(row["entity_google_id"])

        operation = {
            'operator': row["action"].upper(),
            'operand': {
                'adGroupId': adgroup_id,
                'ad': {
                    'id': ad_id,
                },
                row["attribute"]: row["value"].upper(),
            },
        }

        if settings.is_batch_job_processing:
            operation.update({'xsi_type': 'AdGroupAdOperation'})

        return operation

    def advert_creation_operation(self):
        """Operation for a new expanded text ad"""
        row = self.row

        adgroup_id = row["destination_google_id"]

        row["value"] = self.ad_text_to_dict(row["value"])

        operation = {
            'operator': row["action"].upper(),
            'operand': {
                'xsi_type': 'AdGroupAd',
                'adGroupId': adgroup_id,
                'ad': {
                    'xsi_type': 'ExpandedTextAd',
                    'headlinePart1': row["value"]["headlinePart1"],
                    'headlinePart2': row["value"]["headlinePart2"],
                    'headlinePart3': row["value"]["headlinePart3"],
                    'path1': row["value"]["path1"],
                    'path2': row["value"]["path2"],
                    'description': row["value"]["description"],
                    'description2': row["value"]["description2"],
                    'finalUrls': [row["value"]["finalUrls"]],
                },
                'status': 'ENABLED'
            },
        }

        if settings.is_batch_job_processing:
            operation.update({'xsi_type': 'AdGroupAdOperation'})

        return operation

    def campaign_status_operation(self):
        """Campaign Operation for updating statuses"""
        row = self.row

        campaign_id = row["entity_google_id"]

        operation = {
            'operator': row["action"].upper(),
            'operand': {
                'id': campaign_id,
                row["attribute"]: row["value"].upper(),
            },
        }

        if settings.is_batch_job_processing:
            operation.update({'xsi_type': 'CampaignOperation'})

        return operation

    def ad_text_to_dict(self, ad_text):

        ad_text = ad_text.replace("\n", "").replace("\t", "").strip()
        ad = {}
        ad_text = ad_text.replace(" :", ":").replace(": ", ":")
        for row in ad_text.split("`,"):
            if not row.strip():
                continue
            key_value = row.split(":`")
            key = key_value[0].replace("`", "").strip()
            value = key_value[1].replace("`", "").strip()
            ad[key] = value

        return ad



    def split_entity_google_id(self, entity_google_ids):
        ids = entity_google_ids.split(",")

        return ids[1], ids[0]
