def clearDatabase(settings):
    sure = input("Are you sure you want to clear all databases? (y/n)")
    if sure == "y":
        tables = [
            "account_performance", "adverts", "advert_performance", "campaigns", "campaign_performance",
            "adgroups", "adgroup_performance"
        ]
        for table in tables:
            query = "delete from %s" % (table)
            Database().createEngine().execute(query)
