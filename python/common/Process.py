import subprocess
import traceback

from common.Log import Log
from common.Settings import Settings
import os

class Process:

    def __init__(self):
        pass

    def main(self, file, account_id=None):

        if os.name=='nt':
            #running on windows
            path = os.path.join(Settings().python_dir, file)
        else:
            path = file

        command = 'python3 ' + path
        if account_id:
            command = command + " -a %s" %(account_id)

        print(command)

        try:
            # Log("info", "Attempting to run process.", command)
            subprocess.Popen(command, shell=True)
        except Exception as e:
            subprocess.Popen(command, shell=False)
            Log("info", "command: %s" % (command), "", None)
            Log("error", e, traceback.format_exc(), None)
            print(e)
            exit(1)
