
import os

class Env:
    """.env variables"""

    def __init__(self):
        self.vars = self.getEnv()

    def getEnv(self):
        common_dir = os.path.dirname(os.path.abspath(__file__))
        python_dir = os.path.abspath(os.path.join(common_dir, os.pardir))
        root_dir = os.path.abspath(os.path.join(python_dir, os.pardir))
        env_path = os.path.abspath(os.path.join(root_dir, '.env'))

        envvars = {}
        with open(env_path) as myfile:
            for line in myfile:
                name, var = line.partition("=")[::2]
                envvars[name.strip()] = var.strip('\n')
        return envvars
