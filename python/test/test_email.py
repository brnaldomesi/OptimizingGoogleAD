import unittest

from common.Email import Email
from TestAccount import TestAccount

class TestEmail(unittest.TestCase):

    pass

    # def test_send(self):
    #     try:
    #         subject = "Test Email"
    #         html_content = "Hi,<br><br>"
    #         html_content += "Test email"
    #         email_address = "charlesbannister@gmail.com"
    #         (Email()).send(
    #             ("app@adevolver.com", "AdEvolver Budget Commander"), str(email_address), subject, html_content
    #         )

    #     except Exception as e:
    #         self.fail("send email failed")
