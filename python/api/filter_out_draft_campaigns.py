# coding: utf-8
from common.Database import Database

"""
Take only non draft (base) campaigns from the db
Use to filter out adgroups, keywords or ads
Take the campaign ids then
Remove adgroups which don't have a matching campaign id,
then keywords and ads without a matching adgroup id
"""


def main(account_id):
    adgroups(account_id)
    keywords(account_id)
    adverts(account_id)


def adgroups(account_id):
    """Delete ad groups
    Get the campigns we want to remove
    Remove adgroups with matching campaigns
    """

    trial_campaign_ids = Database().getValues('campaigns', 'id', "where campaign_trial_type = 'trial campaign' and account_id = '%s'" %(account_id))
    trial_campaign_ids = ['"%s"' % (campaign_id[0]) for campaign_id in trial_campaign_ids]

    query = """
    delete from adgroups
    where account_id = '%s'
    and campaign_id
    in (%s)
    """ % (account_id, ','.join(trial_campaign_ids))

    Database().executeQuery(query)


def adverts(account_id):
    """Delete ad adverts"""
    adgroup_ids = Database().getValues('adgroups', 'id', "where account_id = '%s'" %(account_id))
    adgroup_ids = ['"%s"' % (adgroup_id[0]) for adgroup_id in adgroup_ids]

    query = """
    delete from adverts
    where account_id = '%s'
    and adgroup_id
    not in (%s)
    """ % (account_id, ','.join(adgroup_ids))
    Database().executeQuery(query)


def keywords(account_id):
    """Delete keywords"""
    adgroup_ids = Database().getValues('adgroups', 'id',"where account_id = '%s'" %(account_id))
    adgroup_ids = ['"%s"' % (adgroup_id[0]) for adgroup_id in adgroup_ids]

    query = """
    delete from keywords
    where account_id = '%s'
    and adgroup_id
    not in (%s)
    """ % (account_id, ','.join(adgroup_ids))
    Database().executeQuery(query)
