from jinja2 import Template
import os
from common.Settings import Settings

class NotificationTemplate:
    """Common email templates
    Return html content from text"""

    def getTemplate(self, html_template_filename):        
        template_path = os.path.abspath(os.path.join(Settings().python_dir,"email_templates", html_template_filename))
        return Template(open(template_path).read())

    def emptyAccount(self,username, account_name, google_account_id):
        """Inform the user their account doesn't have enough data"""
        html_template_filename = "not_enough_account_data.html"
        template = self.getTemplate(html_template_filename)

        html_content = template.render(
            username = username,
            account_name = account_name,
            google_account_id = google_account_id,
        )

        return html_content

    def accountError(self,username, account_name, google_account_id):
        """Inform the user their account failed to process (generic)"""
        html_template_filename = "account_error_generic.html"
        template = self.getTemplate(html_template_filename)

        html_content = template.render(
            username = username,
            account_name = account_name,
            google_account_id = google_account_id,
        )

        return html_content

    def accountSyncSuccess(self,username, account_name):
        """Inform the user their account synced"""
        html_template_filename = "account_synced_successfully.html"
        template = self.getTemplate(html_template_filename)

        html_content = template.render(
            username = username,
            account_name = account_name,
        )

        return html_content

    def emergencyStopPaused(self, template_data):
        """Email when emergency stop pauses campaigns for the day
        This could be the whole account or a budget group
        """
        html_template_filename = "budget_commander_emergency_stop_paused.html"

        template = self.getTemplate(html_template_filename)

        html_content = template.render(
            username=template_data['username'],
            account_name=template_data['name'],
            account_id=template_data['account_id'],
            google_account_id=template_data['google_id'],
            day_limit=round(template_data['day_limit'], 2),
            today_cost=round(template_data['today_cost'], 2),
            currency_symbol=template_data['currency_symbol'],
            budget_group_name=template_data['budget_group_name'],
            )

        return html_content
    
    def monthlyStop(self, template_data, new_status):
        """Email when monthly stop pauses campaigns for the month
        This could be the whole account or a budget group
        """

        if new_status=='Paused':
            html_template_filename = "budget_commander_monthly_campaign_status_update_paused.html"
        if new_status=='Enabled':
            html_template_filename = "budget_commander_monthly_campaign_status_update_enabled.html"

        template = self.getTemplate(html_template_filename)

        html_content = template.render(
            username=template_data['username'],
            account_name=template_data['name'],
            account_id=template_data['account_id'],
            google_account_id=template_data['google_id'],
            budget=round(template_data['budget'], 2),
            spend=round(template_data['spend'], 2),
            currency_symbol=template_data['currency_symbol'],
            budget_group_name=template_data['budget_group_name'],
            )

        return html_content
    