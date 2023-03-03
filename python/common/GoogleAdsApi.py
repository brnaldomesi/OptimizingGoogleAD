# coding: utf-8
from googleads import adwords
from googleads import oauth2

from api.Helpers import Helpers
from common.Log import Log
from common.Settings import Settings


class GoogleAdsApi:

    @staticmethod
    def getClient(user_id=None, account_id=None, client_customer_id=None):
        """Returns the adwords client
        Manager level if a user_id is provided
        Client (account) level if account_id and client_customer_id are provided
        """
        helpers = Helpers()
        settings = Settings()

        if user_id is not None:
            refresh_token = helpers.getRefreshTokenFromUserId(user_id)
        else:
            refresh_token = helpers.getRefreshToken(account_id)

        if not refresh_token:
            Log("info", "Can't determine refresh_token for user %s " %(user_id),'', account_id)
            return

        yaml_data = helpers.getYamlData()["adwords"]
        client_id = yaml_data["client_id"]
        client_secret = yaml_data["client_secret"]
        developer_token = yaml_data["developer_token"]
        oauth_client = oauth2.GoogleRefreshTokenClient(
            client_id=client_id, client_secret=client_secret, refresh_token=refresh_token
        )

        if client_customer_id is not None:
            adwords_client = adwords.AdWordsClient(developer_token, oauth_client, client_customer_id=client_customer_id)
        else:
            adwords_client = adwords.AdWordsClient(developer_token, oauth_client)

        return adwords_client
