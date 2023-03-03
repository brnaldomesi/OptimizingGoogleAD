from common.Database import Database

class TestAccount:
    """Get a random account from a test user (charlesbannister@gmail.com) for testing purposes"""
    
    @property
    def user_id(self):
        return Database().getValue('users','id','where email = "charlesbannister@gmail.com"')

    @property
    def account_id(self): 
        return Database().getValue('accounts', 'id', 'where user_id = "%s" and is_synced=1' %(self.user_id))