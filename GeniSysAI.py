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

import json, sys

from threading import Thread

from Classes.Helpers import Helpers
from Classes.iotJumpWay import Application as iotJumpWay
from Classes.CamRead import CamRead
from Classes.CamStream import CamStream

class GeniSysAI():
    """ GeniSysAI Class
    
    The GeniSysAI Class provides the Medical Support System Server with 
    it's intelligent functionality.
    """
    
    def __init__(self):
        """ Initializes the class. """

        self.Helpers = Helpers("GeniSysAI")
        
        # Initiates the iotJumpWay connection class
        self.iotJumpWay = iotJumpWay({
            "host": self.Helpers.confs["iotJumpWay"]["host"],
            "port": self.Helpers.confs["iotJumpWay"]["port"],
            "lid": self.Helpers.confs["iotJumpWay"]["lid"],
            "aid": self.Helpers.confs["iotJumpWay"]["aid"],
            "an": self.Helpers.confs["iotJumpWay"]["an"],
            "un": self.Helpers.confs["iotJumpWay"]["un"],
            "pw": self.Helpers.confs["iotJumpWay"]["pw"]
        })
        self.iotJumpWay.appConnect()
        
        self.iotJumpWay.appChannelSub(self.Helpers.confs["iotJumpWay"]["channels"]["commands"])
        self.iotJumpWay.deviceCommandsCallback = self.commands

        self.Helpers.logger.info("GeniSysAI Class initialization complete.")
        
    def threading(self):
        """ Creates required module threads. """
        
        Thread(target=CamRead.run, args=(self, )).start()
        Thread(target=CamStream.run, args=(self,)).start()
            
    def commands(self, topic, payload):
        """ 
        iotJumpWay Commands Callback
        
        The callback function that is triggerend in the event of a
        command communication from the iotJumpWay.
        """
        
        self.Helpers.logger.info("Recieved iotJumpWay Command Data : " + str(payload))
        command = json.loads(payload.decode("utf-8"))
        
GeniSysAI = GeniSysAI()

def main():
    # Starts threading
    GeniSysAI.threading()
    exit()

if __name__ == "__main__":
    main()