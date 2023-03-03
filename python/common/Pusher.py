
from common.Env import Env
import pusher

class Pusher:
    """Handle pusher.com data"""

    def __init__(self):
        self.pusher_client = pusher.Pusher(
            app_id=Env().vars['PUSHER_APP_ID'], 
            key=Env().vars['PUSHER_APP_KEY'], 
            secret=Env().vars['PUSHER_APP_SECRET'], 
            cluster=Env().vars['PUSHER_APP_CLUSTER']
            )

    def accountSyncSuccess(self, account_id):
        self.pusher_client.trigger(u'private-account-syncs.%s'%(account_id), u'App\Events\AccountSyncedSuccessfully', {u'message': u'Account synced successfully'})
