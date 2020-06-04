############################################################################################
#
# Project:       Peter Moss Leukemia AI Research
# Repository:    HIAS: Hospital Intelligent Automation System
# Project:       GeniSysAI
#
# Author:        Adam Milton-Barker (AdamMiltonBarker.com)
# Contributors:
# Title:         MySQL Class
# Description:   MySQL functions for the Hospital Intelligent Automation System.
# License:       MIT License
# Last Modified: 2020-06-04
#
############################################################################################

import json, logging, MySQLdb, sys

from datetime import datetime
from pymongo import MongoClient

from Classes.Helpers import Helpers


class MySQL():
    """ MySQL Class
    
    MySQL functions for the Hospital Intelligent Automation System.
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