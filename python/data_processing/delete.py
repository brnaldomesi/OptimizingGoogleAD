import pandas as pd

from common.Log import Log


def main(settings, account_id):
    Log("info", "running deletions", "", account_id)

    # get the ids
    # settings.campaign_ids = getCampaignIds(settings, account_id)
    # settings.adgroup_ids = getAdGroupIds(settings)
    # settings.advert_ids = getAdvertIds(settings)

    # delete from the tables
    # deleteFromAccountTables(settings, account_id)
    # deleteFromCampaignTables(settings, account_id)
    # deleteFromAdGroupTables(account_id, settings)
    # deleteFromAdvertTables(settings)
    # deleteFromPotentialGains(settings, account_id)
    # deleteFromAdNGrams(settings, account_id)

    # just for now to fix a bug
    deleteFromRecents(settings, account_id)


def deleteFromAdNGrams(settings, account_id):
    table_name = "ad_n_gram_performance"
    deleteFromTableByIds(settings, table_name, "account_id", [account_id])


def deleteFromRecents(settings, account_id):
    # get the user id
    # delete where user id is the user id
    query = "select id from users where current_account_id = '%s'" % (account_id)
    result = Database().createEngine().execute(query)
    try:
        user_id = result.fetchall()[0][0]
    except IndexError:
        # no results, usually because it's the first run and the
        # current_account_id hasn't been added yet
        return
    deleteFromTableByIds(settings, "recents", "id", [user_id])


def deleteFromPotentialGains(settings, account_id):
    deleteFromTableByIds(settings, "potential_gains", "account_id", [account_id])


def deleteFromTableByIds(settings, table_name, target_id, ids):
    if type(ids) != list:
        raise ValueError('deleteFromTableByIds expected a list')
    if len(ids) == 0:
        return
    if len(ids) == 1:
        query = "delete from %s where %s = '%s'" % (table_name, target_id, ids[0])
    else:
        ids = ['"' + i + '"' for i in ids]
        query = "delete from %s where %s in (%s)" % (table_name, target_id, ",".join(ids))

    try:
        Database().createEngine().execute(query)
    except:
        print("There was a problem deleting data from %s" % (table_name,))


def deleteFromAccountTables(settings, account_id):
    # never want to delete from the actual accounts table
    deleteFromTableByIds(settings, "account_winning_elements", "account_id", [account_id])
    deleteFromTableByIds(settings, "account_performance_changes", "account_id", [account_id])
    deleteFromTableByIds(settings, "best_performers", "account_id", [account_id])
    deleteFromTableByIds(settings, "worst_performers", "account_id", [account_id])


def deleteFromCampaignTables(settings, account_id):
    deleteFromTableByIds(settings, "campaigns", "account_id", [account_id])
    deleteFromTableByIds(settings, "campaign_performance", "campaign_id", settings.campaign_ids)
    deleteFromTableByIds(settings, "campaign_winning_elements", "campaign_id", settings.campaign_ids)


def deleteFromAdGroupTables(account_id, settings):
    # get the ids from the adgroups table
    # delete where adgroup_id in (adgroup_performance table)
    # delete where adgroup_od in adgroup_winning_elements
    query = "select id from adgroups where account_id = '%s'" % (account_id)
    result = Database().createEngine().execute(query)
    ids = result.fetchall()
    ids = [i[0] for i in ids]
    deleteFromTableByIds(settings, "adgroup_performance", "adgroup_id", ids)
    deleteFromTableByIds(settings, "adgroup_winning_elements", "adgroup_id", ids)

    query = "delete from adgroups where account_id = '%s'" % (account_id)
    Database().createEngine().execute(query)


def deleteFromAdvertTables(settings):
    deleteFromTableByIds(settings, "adverts", "adgroup_id", settings.adgroup_ids)
    deleteFromTableByIds(settings, "advert_performance", "advert_id", settings.advert_ids)


def getCampaignIds(settings, account_id):
    query = "SELECT id FROM campaigns where account_id = '%s'" % (account_id)
    return [i[0] for i in pd.read_sql_query(query, Database().createEngine()).values]


def getAdGroupIds(settings):
    if len(settings.campaign_ids) == 0:
        return []
    campaign_ids = ['"' + i + '"' for i in settings.campaign_ids]
    query = "SELECT id FROM adgroups where campaign_id in (%s)" % (",".join(campaign_ids))
    return [i[0] for i in pd.read_sql_query(query, Database().createEngine()).values]


def getAdvertIds(settings):
    if len(settings.adgroup_ids) == 0:
        return []
    adgroup_ids = ['"' + i + '"' for i in settings.adgroup_ids]
    query = "SELECT id FROM adverts where adgroup_id in (%s)" % (",".join(adgroup_ids))
    return [i[0] for i in pd.read_sql_query(query, Database().createEngine()).values]
