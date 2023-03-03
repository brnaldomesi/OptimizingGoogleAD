# -*- coding: utf-8 -*-
import traceback
import uuid
from datetime import datetime

from api.Helpers import Helpers
from common.Database import Database
from common.Log import Log


class Notification:
    """Create a notification in the app. Accessed via the bell"""

    def __init__(self, account_id):
        self.account_id = account_id

    def user(self, message):
        r"""User notification (App\User)"""

        user_id = Helpers().getUserIdFromAccountId(self.account_id)

        message = '{"message":"%s"}'%(message)

        time = datetime.now()
        data = {'id':str(uuid.uuid4()),
                'type':'App\\Notifications\\AccountSynced',
                'notifiable_id':user_id,
                'notifiable_type':'App\\User',
                'data':message,
                'updated_at':time,
                'created_at':time,
                }
        try:
            Database().insertRow('notifications', data)
        except Exception:
            Log('error', 'There was a problem sending the account synced notification', traceback.format_exc(), self.account_id)
