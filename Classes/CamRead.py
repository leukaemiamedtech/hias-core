############################################################################################
#
# Project:	   Peter Moss Leukemia AI Research
# Repository:	HIAS: Hospital Intelligent Automation System
# Project:	   GeniSysAI
#
# Author:		Adam Milton-Barker (AdamMiltonBarker.com)
# Contributors:
# Title:		 CamRead Class
# Description:   The CamRead Class processes the frames from the local camera and
#				identifies known users and intruders.
# License:	   MIT License
# Last Modified: 2020-06-04
#
############################################################################################

import base64, cv2, sys, time

from datetime import datetime
from imutils import face_utils
from threading import Thread

from Classes.Helpers import Helpers
from Classes.GeniSysAI import GeniSysAI

from Classes.OpenVINO.ie_module import InferenceContext
from Classes.OpenVINO.landmarks_detector import LandmarksDetector
from Classes.OpenVINO.face_detector import FaceDetector
from Classes.OpenVINO.faces_database import FacesDatabase
from Classes.OpenVINO.face_identifier import FaceIdentifier

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
		mesg = ""

		self.font = cv2.FONT_HERSHEY_SIMPLEX
		self.color = (0,0,0)

		# Starts the GeniSysAI module
		self.GeniSysAI = GeniSysAI()
		# Connects to the camera
		self.GeniSysAI.connect()
		# Loads the required models
		self.GeniSysAI.load_models()
		# Loads known images
		self.GeniSysAI.load_known()
		self.publishes = [None] * (len(self.GeniSysAI.faces_database) + 1)

		# Starts the socket server
		soc = self.Sockets.connect(self.Helpers.confs["genisysai"]["socket"]["ip"],
								  self.Helpers.confs["genisysai"]["socket"]["port"])

		while True:
			try:
				t1 = time.perf_counter()
				# Reads the current frame
				_, frame = self.GeniSysAI.lcv.read()
				width = frame.shape[1]
				# Processes the frame
				detections = self.GeniSysAI.process(frame)

				# Writes header to frame
				cv2.putText(frame, "Server Camera", (10, 30), self.font,
							0.7, self.color, 2, cv2.LINE_AA)

				# Writes date to frame
				cv2.putText(frame, str(datetime.now()), (10, 50), self.font,
							0.5, self.color, 2, cv2.LINE_AA)

				if len(detections):
					for roi, landmarks, identity in zip(*detections):
						frame, label = self.GeniSysAI.draw_detection_roi(frame, roi, identity)
						#frame = self.GeniSysAI.draw_detection_keypoints(frame, roi, landmarks)

						if label is "Unknown":
							label = 0
							mesg = "GeniSysAI identified intruder"
						else:
							mesg = "GeniSysAI identified User #" + str(label)

						# If iotJumpWay publish for user is in past
						if (self.publishes[int(label)] is None or (self.publishes[int(label)] + (1 * 20)) < time.time()):
							# Update publish time for user
							self.publishes[int(label)] = time.time()

							# Send iotJumpWay notification
							self.iot.channelPub("Sensors", {
								"Type": "GeniSysAI",
								"Sensor": "USB Camera",
								"Value": label,
								"Message": mesg
							})

							# Send iotJumpWay notification
							self.iot.channelPub("Cameras", {
								"Type": "GeniSysAI",
								"Sensor": "USB Camera",
								"Value": label,
								"Message": mesg
							})

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
