############################################################################################
#
# Project:       Peter Moss COVID-19 AI Research Project
# Repository:    COVID-19 Medical Support System Server
# Project:       EMAR, Emergency Assistance Robot
#
# Author:        Adam Milton-Barker (AdamMiltonBarker.com)
# Contributors:
# Title:         MySQL Class
# Description:   MySQL functions for the HIAS intelligent network.
# License:       MIT License
# Last Modified: 2020-04-17
#
############################################################################################

import json, logging, MySQLdb, sys

from datetime import datetime
from pymongo import MongoClient

from Classes.Helpers import Helpers


class MySQL():
    """ MySQL Class
    
    MySQL functions for the HIAS intelligent network.
    """
    
    def __init__(self):
        """ Initializes the class. """

        self.Helpers = Helpers("iotJumpWay")

        self.Helpers.logger.info("MySQL Class initialization complete.")

    def startMysql(self):

        self.mongoConn = MongoClient()
        self.mysqlConn = MySQLdb.connect(host=self.Helpers.confs["iotJumpWay"]["ip"], 
                                            user=self.Helpers.confs["iotJumpWay"]["dbuser"],
                                            passwd=self.Helpers.confs["iotJumpWay"]["dbpass"], 
                                            db=self.Helpers.confs["iotJumpWay"]["dbname"])
        self.Helpers.logger.info("MySQL connection started")