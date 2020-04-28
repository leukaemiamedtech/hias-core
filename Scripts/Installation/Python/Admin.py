############################################################################################
#
# Project:       Peter Moss COVID-19 AI Research Project
# Repository:    COVID-19 Medical Support System Server
# Module:        Admins
#
# Author:        Adam Milton-Barker (AdamMiltonBarker.com)
# Contributors:
# Title:         Admin
# Description:   Creates a new Medical Support System Server admin account.
# License:       MIT License
# Last Modified: 2020-03-12
#
############################################################################################

import sys

from Classes.Encryption import Encryption

class Admin():
    """ COVID-19 Medical Support System Server Admins Class

    Creates a new Medical Support System Server admin account.
    """

    def __init__(self):
        """ Initializes the class. """

        self.encryption = Encryption()

    def encrypt(self, data):

        return self.encryption.encrypt(data)

    def decrypt(self, data):

        return self.encryption.decrypt(data)

Admin = Admin()

def main():

    if len(sys.argv) < 2:
        print("You must provide an argument")
        exit()

    print(sys.argv[1])
    username = Admin.encrypt(sys.argv[1])
    print(username)
    username = Admin.decrypt(username)
    print(username)

if __name__ == "__main__":
    main()