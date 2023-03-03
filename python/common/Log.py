import datetime
import logging
import uuid

import pandas as pd
# from slack import WebClient
# from slack.errors import SlackApiError

from common.Database import Database
from common.notifications.Email import Email
from common.Settings import Settings

FORMAT = "%(asctime)-15s %(message)s"
logging.basicConfig(
    format=FORMAT, level=logging.INFO, datefmt='%Y-%m-%d %H:%M:%S', filename="python.log"
)
log = logging.getLogger(__name__)
logging.getLogger('oauth2client').setLevel(logging.WARNING)



class Log(object):
    """Handle logging throughout the app
    Stores to python.log
    Stores to the log.db within the python dir
    Errors can then be picked up later and sent in an email    """

    def __init__(self, category, short_message, message="", account_id=None):

        self.account_id = account_id
        self.category = category
        self.message = message
        self.short_message = short_message

        self.debug_mode = self.isDebugMode()
        if self.category == "debug" and self.debug_mode == False:
            return
        self.getData()
        self.logToFile()
        self.logToDatabase()

        if(not self.isInProduction()):
            return

        if self.category== "error" or self.category== "warning":
            html_content = "Time: %s, Category:  %s, account_id: %s, Short msg: %s, More info: %s" % (
                self.data["created_at"], self.category, self.account_id, self.short_message, self.message)
            self.sendEmail(html_content)
            # self.sendSlackMessage(html_content)

    def sendEmail(self, html_content):
        Email().send("app@adevolver.com", "charlesbannister@gmail.com", "AdEvolver %s" %(self.category), html_content)
        Email().send("app@adevolver.com", "ed@adevolver.com", "AdEvolver %s" %(self.category), html_content)

    def isDebugMode(self):
        return Settings().envvars["APP_DEBUG"] == "true"

    def isInProduction(self):
        return Settings().envvars["APP_ENV"] == "production"

    def getData(self):

        self.name = None
        self.row_num = None

        self.data = {
            "id": str(uuid.uuid4()),
            "created_at": datetime.datetime.now(),
            "category": self.category,
            "account_id": self.account_id,
            "short_message": str(self.short_message),
            "message": str(self.message),
        }

    def logToFile(self):
        log_text = "Time: %s, Category:  %s, account_id: %s, Short msg: %s, More info: %s" % (
            self.data["created_at"], self.category, self.account_id, self.short_message, self.message)

        print(log_text)

        if self.category.lower() == "error":
            log.error(log_text)
        else:
            log.info(log_text)

    def logToDatabase(self):
        df = pd.DataFrame(self.data, index=[0])
        df.to_sql("python_log", Database().createEngine(), index=False, if_exists="append")

    # def sendSlackMessage(self, text):
    #     slack_token = 'xoxp-50344447441-228913361216-1160751556337-d06604c7e2bf20ea61828a220fafed52'
    #     client = WebClient(token=slack_token)
    #     try:
    #         response = client.chat_postMessage(
    #             channel="G013LQGMNPP",
    #             text=text
    #         )
    #     except SlackApiError as e:
    #         # You will get a SlackApiError if "ok" is False
    #         print(e)
    #         assert e.response["error"]  # str like 'invalid_auth', 'channel_not_found'
