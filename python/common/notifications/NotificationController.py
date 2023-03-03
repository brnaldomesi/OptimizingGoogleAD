# -*- coding: utf-8 -*-
from common.helpers.Currency import Currency
from common.notifications.Email import Email
from common.notifications.Notification import Notification
from common.notifications.NotificationTemplate import NotificationTemplate
from common.Pusher import Pusher
from common.Settings import Settings


class NotificationController:
    """From an account/user id grab templates and send emails"""

    def __init__(self, account_id):
        self.account_id = account_id
        self.account_data = Settings().getAccountData(account_id)
        self.google_id = self.getPrettyGoogleId()
        self.currency_symbol = Currency().getSymbol(account_id)
        self.user_data = Settings().getUserData(account_id)
        self.username = self.user_data['name']

    def getPrettyGoogleId(self):
        _id = self.account_data['google_id']
        return "%s-%s-%s" %(_id[0:3],_id[3:6],_id[6:10])

    def emptyAccount(self):
        """Alert the user if their account is empty/doesn't have enough data
        Only send the alert once, the first time they try and sync"""

        subject = "Account '%s' (%s) Doesn't have Enough Data" % (self.account_data['name'], self.google_id)
        Notification(self.account_id).user(subject)
        
        html_content = NotificationTemplate().emptyAccount(self.username, self.account_data['name'], self.google_id)

        Email().send(("support@adevolver.com", "AdEvolver"), str(self.user_data['email']), subject, html_content)
        Email().send(("support@adevolver.com", "AdEvolver"), "support@adevolver.com", "Support copy: %s"%(subject), html_content)
        Email().send(("support@adevolver.com", "AdEvolver"), "charlesbannister@gmail.com", "Support copy: %s"%(subject), html_content)
    
    def accountError(self):
        """Alert the user if there is an error processing their account
        Only send the alert once, the first time they try and sync
        Note this is a back up, generic error
        """

        subject = "Account '%s' (%s) Failed to Sync" % (self.account_data['name'], self.google_id)
        Notification(self.account_id).user(subject)

        html_content = NotificationTemplate().accountError(self.username, self.account_data['name'], self.google_id)

        Email().send(("support@adevolver.com", "AdEvolver"), str(self.user_data['email']), subject, html_content)
        Email().send(("support@adevolver.com", "AdEvolver"), "support@adevolver.com", "Support copy: %s"%(subject), html_content)
        Email().send(("support@adevolver.com", "AdEvolver"), "charlesbannister@gmail.com", "Support copy: %s"%(subject), html_content)
        
    def accountSyncSuccess(self):
        """Alert the user if an account syncs successfully
        Only send the alert once, the first time they try and sync
        """

        Pusher().accountSyncSuccess(self.account_id)

        subject = "Account '%s' (%s) Synced Successfully" % (self.account_data['name'], self.google_id)
        
        Notification(self.account_id).user(subject)

        html_content = NotificationTemplate().accountSyncSuccess(self.username, self.account_data['name'])

        Email().send(("support@adevolver.com", "AdEvolver"), str(self.user_data['email']), subject, html_content)


    def emergencyStopPaused(self, day_limit, today_cost, budget_group_name=None):
        """Email when emergency stop pauses campaigns for the day
        This could be the whole account or a budget group
        """

        subject = "Account '%s' (%s) | Campaigns were paused for the rest of the day" % (self.account_data['name'], self.google_id)
        Notification(self.account_id).user(subject)

        template_data = {
            'username' : self.username,
            'name' : self.account_data['name'],
            'account_id' : self.account_id,
            'google_id' : self.google_id,
            'day_limit' : day_limit,
            'today_cost' : today_cost,
            'currency_symbol' : self.currency_symbol,
            'budget_group_name' : budget_group_name,
        }
        html_content = NotificationTemplate().emergencyStopPaused(template_data)

        Email().send(("app@adevolver.com", "AdEvolver Budget Commander"), str(self.user_data['email']), subject, html_content)

    def monthlyStop(self, budget, this_month_spend, new_status,budget_group_name=None):
        """Email when monthly stop pauses campaigns for the month
        This could be the whole account or a budget group
        """

        subject = "Account '%s' (%s) | Campaigns were %s" % (self.account_data['name'], self.google_id, new_status)
        Notification(self.account_id).user(subject)

        template_data = {
            'username' : self.username,
            'name' : self.account_data['name'],
            'account_id' : self.account_id,
            'google_id' : self.google_id,
             'budget' : float(budget),
            'spend' : float(this_month_spend),
            'currency_symbol' : self.currency_symbol,
            'budget_group_name' : budget_group_name,
            'new_status' : new_status
        }

        html_content = NotificationTemplate().monthlyStop(template_data, new_status)

        Email().send(("app@adevolver.com", "AdEvolver Budget Commander"), str(self.user_data['email']), subject, html_content)
