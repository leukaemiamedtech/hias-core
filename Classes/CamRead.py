############################################################################################
#
# Project:       Peter Moss Leukemia AI Research
# Repository:    HIAS: Hospital Intelligent Automation System
# Project:       GeniSysAI
#
# Author:        Adam Milton-Barker (AdamMiltonBarker.com)
# Contributors:
# Title:         CamRead Class
# Description:   The CamRead Class processes the frames from the local camera and
#                identifies known users and intruders.
# License:       MIT License
# Last Modified: 2020-06-04
#
############################################################################################

import base64, cv2, sys, time

from datetime import datetime
from imutils import face_utils
from threading import Thread

from Classes.Helpers import Helpers
from Classes.GeniSysAI import GeniSysAI
from Classes.Socket import Socket

class CamRead(Thread):
    """ CamRead Class

    The CamRead Class processes the frames from the local camera and
    identifies known users and intruders.
    """

    def __init__(self):
        """ Initializes the class. """

        self.Helpers = Helpers("CamRead")
        super(CamRead, self).__init__()

        self.Helpers.logger.info("CamRead Class initialization complete.")

    def run(self):
        """ Runs the module. """

        fps = ""
        framecount = 0
        time1 = 0
        time2 = 0

        self.font = cv2.FONT_HERSHEY_SIMPLEX
        self.color = (0,0,0)

        # Starts the GeniSysAI module
        self.GeniSysAI = GeniSysAI()
        # Connects to the camera
        self.GeniSysAI.connect()
        # Encodes known user database
        self.GeniSysAI.preprocess()
        self.publishes = [None] * (len(self.GeniSysAI.encoded) + 1)
        # Starts the socket module
        self.Socket = Socket("CamRead")
        # Starts the socket server
        soc = self.Socket.connect(self.Helpers.confs["tass"]["socket"]["ip"],
                                  self.Helpers.confs["tass"]["socket"]["port"])

        while True:
            try:
                t1 = time.perf_counter()
                # Reads the current frame
                _, frame = self.GeniSysAI.lcv.read()
                # Processes the frame
                raw, frame, gray = self.GeniSysAI.processim(frame)
                width = frame.shape[1]

                # Gets faces and coordinates
                faces, coords = self.GeniSysAI.faces(frame)

                # Writes header to frame
                cv2.putText(frame, "Server Camera 1", (10, 30), self.font,
                            0.7, self.color, 2, cv2.LINE_AA)

                # Writes date to frame
                cv2.putText(frame, str(datetime.now()), (10, 80), self.font,
                            0.5, self.color, 2, cv2.LINE_AA)

                if len(coords):
                    i = 0
                    # Loops through coordinates
                    for face in coords:
                        # Gets facial landmarks coordinates
                        coordsi = face_utils.shape_to_np(face)
                        # Looks for matches/intruders
                        person, msg = self.GeniSysAI.match(raw, [coords])
                        # If iotJumpWay publish for user is in past
                        if (self.publishes[person] is None or (self.publishes[person] + (1 * 60)) < time.time()):
                            # Update publish time for user
                            self.publishes[person] = time.time()

                        # Send iotJumpWay notification
                        self.iot.channelPub("Sensors", {
                            "Type": "GeniSysAI",
                            "Sensor": "USB Camera",
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

                cv2.putText(frame, fps, (width-170, 30), cv2.FONT_HERSHEY_SIMPLEX,
                            0.5, self.color, 1, cv2.LINE_AA)

                # Streams the modified frame to the socket server
                encoded, buffer = cv2.imencode('.jpg', frame)
                soc.send(base64.b64encode(buffer))

                # FPS calculation
                framecount += 1
                if framecount >= 15:
                    fps = "Stream: {:.1f} FPS".format(time1/15)
                    framecount = 0
                    time1 = 0
                    time2 = 0
                t2 = time.perf_counter()
                elapsedTime = t2-t1
                time1 += 1/elapsedTime
                time2 += elapsedTime

            except KeyboardInterrupt:
                self.GeniSysAI.lcv.release()
                break
