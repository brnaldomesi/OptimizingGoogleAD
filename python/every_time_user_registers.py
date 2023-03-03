from api.AccountMetaData import AccountMetaData
from common.Log import Log

"""Runs when the user registers. Not part of a schedule"""


def main():
    Log('info', 'running every time user registers')
    AccountMetaData().addNew()


if __name__ == "__main__":
    main()