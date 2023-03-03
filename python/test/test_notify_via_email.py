import unittest

from api.features.budget_commander.BudgetCommander import BudgetCommander
from TestAccount import TestAccount
from api.features.budget_commander.NotifyViaEmail import NotifyViaEmail


class TestNotifyViaEmail(unittest.TestCase):

    def __init__(self, *args, **kwargs):
        super(TestNotifyViaEmail, self).__init__(*args, **kwargs)
        self.account_id = TestAccount().account_id

    def test_should_send_email(self):

        notify_via_email = NotifyViaEmail(self.account_id)
        
        notify_via_email.this_month_spend=30000
        notify_via_email.budget=20000
        notify_via_email.email_sent=False
        notify_via_email.notify_via_email=True
        notify_via_email.pause_campaigns=False

        self.assertTrue(notify_via_email.shouldSendEmail())

    def test_should_not_send_email(self):

        notify_via_email = NotifyViaEmail(self.account_id)
        
        notify_via_email.this_month_spend=30000
        notify_via_email.budget=20000
        notify_via_email.email_sent=False
        notify_via_email.notify_via_email=False
        notify_via_email.pause_campaigns=False

        self.assertFalse(notify_via_email.shouldSendEmail())

    def test_should_not_send_email_email_sent_test(self):

        notify_via_email = NotifyViaEmail(self.account_id)
        
        notify_via_email.this_month_spend=30000
        notify_via_email.budget=20000
        notify_via_email.email_sent=True
        notify_via_email.notify_via_email=True
        notify_via_email.pause_campaigns=False

        self.assertFalse(notify_via_email.shouldSendEmail())

    def test_should_not_send_email_no_budget_test(self):

        notify_via_email = NotifyViaEmail(self.account_id)
        
        notify_via_email.this_month_spend=30000
        notify_via_email.budget=None
        notify_via_email.email_sent=False
        notify_via_email.notify_via_email=True
        notify_via_email.pause_campaigns=False

        self.assertFalse(notify_via_email.shouldSendEmail())

    def test_should_not_send_email_pause_campaigns_enabled(self):
        """if pause_campaigns is enabled don't send an email"""
        notify_via_email = NotifyViaEmail(self.account_id)
        
        notify_via_email.this_month_spend=30000
        notify_via_email.budget=20000
        notify_via_email.email_sent=False
        notify_via_email.notify_via_email=True
        notify_via_email.pause_campaigns=True

        self.assertFalse(notify_via_email.shouldSendEmail())

  