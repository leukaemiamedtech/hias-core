############################################################################################
#
# Project:       Peter Moss COVID-19 AI Research Project
# Repository:    COVID-19 Medical Support System Server
# Project:       GeniSysAI
#
# Author:        Adam Milton-Barker (AdamMiltonBarker.com)
# Contributors:
# Title:         GeniSysAI Class
# Description:   The GeniSysAI Class provides the Medical Support System Server with it's
#                intelligent functionality.
# License:       MIT License
# Last Modified: 2020-04-25
#
############################################################################################

import json, logging, MySQLdb, sys

from datetime import datetime
from pymongo import MongoClient

from Classes.Helpers import Helpers
from Classes.iotJumpWay import Application

logging.basicConfig(filename='iotCore.log',level=logging.DEBUG)


class iotJumpWay():
    """ iotJumpWay Class
    
    The iotJumpWay Class listens for data from the network and 
    stores it in the Mongo db.
    """
    
    def __init__(self):
        """ Initializes the class. """

        self.Helpers = Helpers("GeniSysAI")
        self.Helpers.logger.info("GeniSysAI Class initialization complete.")

    def startIoT(self):        
        # Initiates the iotJumpWay connection class
        
        self.Application = Application({
            "host": self.Helpers.confs["iotJumpWay"]["host"],
            "port": self.Helpers.confs["iotJumpWay"]["port"],
            "lid": self.Helpers.confs["iotJumpWay"]["lid"],
            "aid": self.Helpers.confs["iotJumpWay"]["paid"],
            "an": self.Helpers.confs["iotJumpWay"]["pan"],
            "un": self.Helpers.confs["iotJumpWay"]["pun"],
            "pw": self.Helpers.confs["iotJumpWay"]["ppw"]
        })
        self.Application.appConnect()
        
        self.Application.appChannelSub("#","#")
        self.Application.appDeviceChannelSub("#", "#", "#")

        self.Application.appCommandsCallback = self.appCommandsCallback
        self.Application.appSensorCallback = self.appSensorCallback
        self.Application.appStatusCallback = self.appStatusCallback    
        self.Application.appTriggerCallback = self.appTriggerCallback
        self.Application.deviceCommandsCallback = self.deviceCommandsCallback
        self.Application.deviceSensorCallback = self.deviceSensorCallback
        self.Application.deviceStatusCallback = self.deviceStatusCallback    
        self.Application.deviceTriggerCallback = self.deviceTriggerCallback

        self.Helpers.logger.info("GeniSysAI Class initialization complete.")

    def startMysql(self):

        self.mongoConn = MongoClient()
        self.mysqlConn = MySQLdb.connect(host=self.Helpers.confs["iotJumpWay"]["ip"], 
                                            user=self.Helpers.confs["iotJumpWay"]["dbuser"],
                                            passwd=self.Helpers.confs["iotJumpWay"]["dbpass"], 
                                            db=self.Helpers.confs["iotJumpWay"]["dbname"])
        self.Helpers.logger.info("MySQL connection started")

    def startMongo(self):

        connection = MongoClient(self.Helpers.confs["iotJumpWay"]["ip"])
        self.mongoConn = connection[self.Helpers.confs["iotJumpWay"]["mdb"]]
        self.mongoConn.authenticate(self.Helpers.confs["iotJumpWay"]["mdbu"], self.Helpers.confs["iotJumpWay"]["mdbp"])
        self.Helpers.logger.info("Mongo connection started")
            
    def appStatusCallback(self, topic, payload):
        """ 
        iotJumpWay Application Status Callback
        
        The callback function that is triggerend in the event of an application
        status communication from the iotJumpWay.
        """
        
        self.Helpers.logger.info("Recieved iotJumpWay Application Status : " + payload.decode())
        
        splitTopic=topic.split("/")

        try:
            collection = self.mongoConn.Statuses
            doc = {
                "Use": "Application",
                "Location": splitTopic[0],
                "Zone": 0,
                "Application": splitTopic[2],
                "Device": 0,
                "Status": payload.decode(),
                "Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
            }
            collection.insert(doc)
            self.Helpers.logger.info("Mongo data inserted OK")
        except:
            e = sys.exc_info()[0]
            self.Helpers.logger.info("Mongo data inserted FAILED")
            
    def appCommandsCallback(self, topic, payload):
        """ 
        iotJumpWay Application Commands Callback
        
        The callback function that is triggerend in the event of an application
        command communication from the iotJumpWay.
        """
        
        self.Helpers.logger.info("Recieved iotJumpWay Application Command Data: " + payload.decode())
        command = json.loads(payload.decode("utf-8"))
        
        splitTopic=topic.split("/")

        try:
            collection = self.mongoConn.Commands
            doc = {
                "Use": "Application",
                "Location": splitTopic[0],
                "Zone": 0,
                "From": command["From"],
                "To": splitTopic[3],
                "Type": command["Type"],
                "Value": command["Value"],
                "Message": command["Message"],
                "Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
            }
            collection.insert(doc)
            self.Helpers.logger.info("Mongo data inserted OK")
        except:
            e = sys.exc_info()[0]
            self.Helpers.logger.info("Mongo data inserted FAILED")
            
    def appSensorCallback(self, topic, payload):
        """ 
        iotJumpWay Application Sensors Callback
        
        The callback function that is triggerend in the event of an application
        sensor communication from the iotJumpWay.
        """
        
        self.Helpers.logger.info("Recieved iotJumpWay Application Sensors Data : " + payload.decode())
        command = json.loads(payload.decode("utf-8"))
        
        splitTopic=topic.split("/")

        try:
            collection = self.mongoConn.Sensors
            doc = {
                "Use": "Application",
                "Location": splitTopic[0],
                "Zone": 0,
                "Application": splitTopic[2],
                "Device": 0,
                "Sensor": command["Sensor"],
                "Type": command["Type"],
                "Value": command["Value"],
                "Message": command["Message"],
                "Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
            }
            collection.insert(doc)
            self.Helpers.logger.info("Mongo data inserted OK")
        except:
            e = sys.exc_info()[0]
            self.Helpers.logger.info("Mongo data inserted FAILED")
            
    def appTriggerCallback(self, topic, payload):
        """ 
        iotJumpWay Application Trigger Callback
        
        The callback function that is triggerend in the event of an application
        trigger communication from the iotJumpWay.
        """
        
        self.Helpers.logger.info("Recieved iotJumpWay Application Trigger Data : " + payload.decode())
        command = json.loads(payload.decode("utf-8"))
            
    def deviceStatusCallback(self, topic, payload):
        """ 
        iotJumpWay Application Status Callback
        
        The callback function that is triggerend in the event of an device
        status communication from the iotJumpWay.
        """
        
        self.Helpers.logger.info("Recieved iotJumpWay Application Sensors Data : " + payload.decode())
        command = json.loads(payload.decode("utf-8"))
        
        splitTopic=topic.split("/")

        try:
            collection = self.mongoConn.Statuses
            doc = {
                "Use": "Device",
                "Location": splitTopic[0],
                "Zone": splitTopic[2],
                "From": command["From"],
                "To": splitTopic[3],
                "Type": command["Type"],
                "Value": command["Value"],
                "Message": command["Message"],
                "Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
            }
            collection.insert(doc)
            self.Helpers.logger.info("Mongo data inserted OK")
        except:
            e = sys.exc_info()[0]
            self.Helpers.logger.info("Mongo data inserted FAILED")
            
    def deviceCommandsCallback(self, topic, payload):
        """ 
        iotJumpWay Application Commands Callback
        
        The callback function that is triggerend in the event of an device
        command communication from the iotJumpWay.
        """
        
        self.Helpers.logger.info("Recieved iotJumpWay Device Command Data: " + payload.decode())
        command = json.loads(payload.decode("utf-8"))
        
        splitTopic=topic.split("/")

        try:
            collection = self.mongoConn.Commands
            doc = {
                "Use": "Device",
                "Location": splitTopic[0],
                "Zone": splitTopic[2],
                "From": command["From"],
                "To": splitTopic[3],
                "Type": command["Type"],
                "Value": command["Value"],
                "Message": command["Message"],
                "Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
            }
            collection.insert(doc)
            self.Helpers.logger.info("Mongo data inserted OK")
        except:
            e = sys.exc_info()[0]
            self.Helpers.logger.info("Mongo data inserted FAILED")
            
    def deviceSensorCallback(self, topic, payload):
        """ 
        iotJumpWay Application Sensors Callback
        
        The callback function that is triggerend in the event of an device
        sensor communication from the iotJumpWay.
        """
        
        self.Helpers.logger.info("Recieved iotJumpWay Device Sensors Data : " + payload.decode())
        command = json.loads(payload.decode("utf-8"))
        
        splitTopic=topic.split("/")

        try:
            collection = self.mongoConn.Sensors
            doc = {
                "Use": "Device",
                "Location": splitTopic[0],
                "Zone": splitTopic[2],
                "Application": 0,
                "Device": splitTopic[3],
                "Sensor": command["Sensor"],
                "Type": command["Type"],
                "Value": command["Value"],
                "Message": command["Message"],
                "Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
            }
            collection.insert(doc)
            self.Helpers.logger.info("Mongo data inserted OK")
        except:
            e = sys.exc_info()[0]
            self.Helpers.logger.info("Mongo data inserted FAILED")
            
    def deviceTriggerCallback(self, topic, payload):
        """ 
        iotJumpWay Application Trigger Callback
        
        The callback function that is triggerend in the event of an device
        trigger communication from the iotJumpWay.
        """
        
        self.Helpers.logger.info("Recieved iotJumpWay Device Trigger Data : " + payload.decode())
        command = json.loads(payload.decode("utf-8"))
        
iotJumpWay = iotJumpWay()

def main():
    # Starts the application
    iotJumpWay.startIoT()
    iotJumpWay.startMysql()
    iotJumpWay.startMongo()
    while True:
        continue
    exit()

if __name__ == "__main__":
    main()