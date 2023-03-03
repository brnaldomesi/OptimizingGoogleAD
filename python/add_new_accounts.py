from api.AccountMetaData import AccountMetaData
from common.Log import Log

"""Download user accounts. Run each night.
Created specificially to grab new accounts should users add them within Google Ads
"""


def main():
    Log('info', 'running add_new_accounts')
    AccountMetaData().addAll()


if __name__ == "__main__":
    main()