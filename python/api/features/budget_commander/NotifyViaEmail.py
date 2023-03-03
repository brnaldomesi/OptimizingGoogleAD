# -*- coding: utf-8 -*-
from __future__ import division

import os

from jinja2 import Template

from api.features.budget_commander.BudgetCommander import BudgetCommander
from common.Database import Database
from common.notifications.Email import Email
from common.Log import Log
from common.Settings import Settings


class NotifyViaEmail(BudgetCommander):

    def __init__(self, account_id, budget_group_id=None):
        BudgetCommander.__init__(self, account_id, budget_group_id)
        self.account_id = account_id

    def main(self):

        if self.under_budget and self.user_settings["email_sent"]:
            self.markAsNotSent()
            return

        if self.shouldSendEmail():
            self.sendEmail()
        
    def shouldSendEmail(self):
        if self.pause_campaigns:
            Log("info","notify via email won't run because pause_campaigns is enabled" , '', self.account_id)
            return False# they'll get an email when the campaigns pause so there's no need to send one here

        if self.email_sent:
            return False

        if self.budget is None:
            return False

        if self.under_budget:
            return False

        if not self.notify_via_email:
            return False

        return True

        

    def markAsSent(self):
        # update the budget_commander table mark the account's email as sent
        # email_sent field
        print("marking as sent")
        query = "update budget_commander set email_sent = 1 where account_id = '%s' " % (self.account_id)
        (Database()).createEngine().execute(query)

    def markAsNotSent(self):
        print("marking as not sent...")
        query = "update budget_commander set email_sent = 0 where account_id = '%s' " % (self.account_id)
        (Database()).createEngine().execute(query)

    def getHtmlContent(self, subject):
        template_path = os.path.abspath(os.path.join(Settings().python_dir,"email_templates", "budget_commander_email_notification.html"))
        template = Template(open(template_path).read())

        html_content = template.render(
            subject=subject,
            username=self.username,
            account_name=self.name,
            account_id=self.account_id,
            google_account_id=self.styled_google_id,
            budget=float(self.budget),
            spend=float(self.this_month_spend),
            currency_symbol=self.currency_symbol,
            )

        return html_content

    def sendEmail(self):

        subject = "Account '%s' (%s) has gone over budget" % (self.name, self.styled_google_id)
        
        if self.budget_group_id:
            subject = "The '%s' Budget Group in "%(self.budget_group_info['budget_group_name']) + subject

        html_content = self.getHtmlContent(subject)

        email_addresses = self.getEmailAddresses()

        Log("info", "Sending email(s)", "%s - send to: %s" % (subject, ",".join(email_addresses),), self.account_id)
        assert len(email_addresses) > 0

        for email_address in email_addresses:
            # print email_address
            Email.send(("app@adevolver.com", "AdEvolver Budget Commander"), str(email_address), subject, html_content)

        self.markAsSent()
