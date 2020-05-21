############################################################################################
#
# Project:       Peter Moss COVID-19 AI Research Project
# Repository:    COVID-19 Medical Support System Server
# Project:       GeniSysAI
#
# Author:        Adam Milton-Barker (AdamMiltonBarker.com)
# Contributors:
# Title:         CamRead Class
# Description:   The CamRead Class processes the frames from the local camera and 
#                identifies known users and intruders.
# License:       MIT License
# Last Modified: 2020-04-25
#
############################################################################################

import base64, cv2, sys, time

from datetime import datetime
from imutils import face_utils
from threading import Thread

from Classes.Helpers import Helpers
from Classes.TASS import TASS
from Classes.Socket import Socket

class CamRead(Thread):
    """ CamRead Class

    The CamRead Class processes the frames from the local camera and 
    identifies known users and intruders.
    """

    def __init__(self):
        """ Initializes the class. """

        self.Helpers = Helpers("CamRead")
        
        self.Helpers.logger.info("CamRead Class initialization complete.")

    def run(self):
        """ Runs the module. """
        
        self.font = cv2.FONT_HERSHEY_SIMPLEX
        self.color = (0,0,0)
        
        # Starts the TASS module
        self.TASS = TASS()
        # Connects to the camera
        self.TASS.connect()
        # Encodes known user database
        self.TASS.preprocess()
        self.publishes = [None] * (len(self.TASS.encoded) + 1)
        # Starts the socket module
        self.Socket = Socket("CamRead")
        # Starts the socket server
        soc = self.Socket.connect(self.Helpers.confs["tass"]["socket"]["ip"], 
                                  self.Helpers.confs["tass"]["socket"]["port"])

        while True:
            try:
                # Reads the current frame
                _, frame = self.TASS.lcv.read()
                # Processes the frame
                raw, frame, gray = self.TASS.processim(frame)
                # Gets faces and coordinates
                faces, coords = self.TASS.faces(frame)
                
                # Writes header to frame
                cv2.putText(frame, "Office Camera 1", (10,50), self.font,
                            0.7, self.color, 2, cv2.LINE_AA)
    
                # Writes date to frame
                cv2.putText(frame, str(datetime.now()), (10,80), self.font,
                            0.5, self.color, 2, cv2.LINE_AA)
                
                if len(coords):
                    i = 0
                    # Loops through coordinates
                    for face in coords:
                        # Gets facial landmarks coordinates
                        coordsi = face_utils.shape_to_np(face)
                        # Looks for matches/intruders
                        person, msg = self.TASS.match(raw, [coords])
                        # If iotJumpWay publish for user is in past
                        if (self.publishes[person] is None or (self.publishes[person] + (1 * 60)) < time.time()):
                            # Update publish time for user
                            self.publishes[person] = time.time()
                            # Send iotJumpWay notification
                            self.iotJumpWay.appDeviceChannelPub("Sensors", self.Helpers.confs["tass"]["zid"], self.Helpers.confs["tass"]["did"],{
                                "Sensor": self.Helpers.confs["tass"]["sid"],
                                "Type": "TASS",
                                "Value": person,
                                "Message": msg
                            })
                        # Draws facial landmarks
                        for (x, y) in coordsi:
                            cv2.circle(frame, (x, y), 2, (0, 255, 0), -1)
                        # Adds user name to frame 
                        if person == 0:
                            string = "Unknown"
                        else:
                            string = "User #" + str(person)
                        cv2.putText(frame, string, (x + 75, y), self.font,
                                    1, (0, 255, 0), 2, cv2.LINE_AA)
                        i+=1
                # Streams the modified frame to the socket server
                encoded, buffer = cv2.imencode('.jpg', frame)
                soc.send(base64.b64encode(buffer))
                
            except KeyboardInterrupt:
                self.TASS.lcv.release()
                break