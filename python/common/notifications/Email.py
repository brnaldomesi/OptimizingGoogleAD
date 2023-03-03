# -*- coding: utf-8 -*-
# using SendGrid's Python Library
# https://github.com/sendgrid/sendgrid-python
import traceback

from sendgrid import SendGridAPIClient
from sendgrid.helpers.mail import Mail

from common.Settings import Settings
from common.Env import Env

class Email:
    @staticmethod
    def send(from_email, to_emails, subject, html_content):
        SENDGRID_API_KEY = Env().vars["SENDGRID_API_KEY"]

        try:
            message = Mail(
                from_email=from_email,
                to_emails=to_emails,
                subject=subject,
                html_content=html_content
            )
        except Exception as e:
            print(e)
            print(traceback.format_exc())

        try:
            sg = SendGridAPIClient(SENDGRID_API_KEY)
            response = sg.send(message)

            # print(response.status_code)
            # print(response.body)
            # print(response.headers)
        except Exception as exception:
            print(exception)
