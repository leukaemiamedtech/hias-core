#!/usr/bin/env python3
######################################################################################################
#
# Organization:  Peter Moss Leukemia AI Research
# Repository:    HIAS: Hospital Intelligent Automation System
#
# Author:        Adam Milton-Barker (AdamMiltonBarker.com)
#
# Title:         CamAPI Class
# Description:   The CamAPI module is used by the HIAS Sercurity Service to provide an API endpoint
#                allowing devices on the HIAS network to identify known and unknown users via
#                HTTP request.
# License:       MIT License
# Last Modified: 2020-09-23
#
######################################################################################################

import cv2
import json
import jsonpickle
import psutil
import requests
import os
import signal
import sys
import threading

import numpy as np

from flask import Flask, request, Response
from threading import Thread

from Classes.Helpers import Helpers
from Classes.iotJumpWay import Device as iot
from Classes.TassAI import TassAI

from Classes.OpenVINO.ie_module import InferenceContext
from Classes.OpenVINO.landmarks_detector import LandmarksDetector
from Classes.OpenVINO.face_detector import FaceDetector
from Classes.OpenVINO.faces_database import FacesDatabase
from Classes.OpenVINO.face_identifier import FaceIdentifier

os.environ["OPENBLAS_NUM_THREADS"] = "1"
os.environ["MKL_NUM_THREADS"] = "1"

class CamAPI():
	""" CamAPI Class

	The CamAPI module is used by the HIAS Sercurity Service to provide an API endpoint
	allowing devices on the HIAS network to identify known and unknown users via
	HTTP request.
	"""

	def __init__(self):
		""" Initializes the class. """

		self.Helpers = Helpers("Camera")

		# Initiates the iotJumpWay connection class
		self.iot = iot()
		self.iot.connect()

		# Starts the TassAI module
		self.TassAI = TassAI()
		# Loads the required models
		self.TassAI.load_models()
		# Loads known images
		self.TassAI.load_known()

		self.Helpers.logger.info("Camera API Class initialization complete.")

	def life(self):
		""" Sends vital statistics to HIAS """

		cpu = psutil.cpu_percent()
		mem = psutil.virtual_memory()[2]
		hdd = psutil.disk_usage('/').percent
		tmp = psutil.sensors_temperatures()['coretemp'][0].current
		r = requests.get('http://ipinfo.io/json?token=' +
		                 self.Helpers.confs["iotJumpWay"]["ipinfo"])
		data = r.json()
		location = data["loc"].split(',')

		self.Helpers.logger.info("TassAI Life (TEMPERATURE): " + str(tmp) + "\u00b0")
		self.Helpers.logger.info("TassAI Life (CPU): " + str(cpu) + "%")
		self.Helpers.logger.info("TassAI Life (Memory): " + str(mem) + "%")
		self.Helpers.logger.info("TassAI Life (HDD): " + str(hdd) + "%")
		self.Helpers.logger.info("TassAI Life (LAT): " + str(location[0]))
		self.Helpers.logger.info("TassAI Life (LNG): " + str(location[1]))

		# Send iotJumpWay notification
		self.iot.channelPub("Life", {
			"CPU": str(cpu),
			"Memory": str(mem),
			"Diskspace": str(hdd),
			"Temperature": str(tmp),
			"Latitude": float(location[0]),
			"Longitude": float(location[1])
		})

		threading.Timer(300.0, self.life).start()

	def threading(self):
		""" Creates required module threads. """

		# Life thread
		Thread(target=self.life, args=(), daemon=True).start()

	def signal_handler(self, signal, frame):
		self.Helpers.logger.info("Disconnecting")
		self.iot.disconnect()
		sys.exit(1)


app = Flask(__name__)
CamAPI = CamAPI()

@app.route('/Encode', methods=['POST'])
def Encode():
	""" Responds to POST requests sent to the /Encode API endpoint. """


@app.route('/Inference', methods=['POST'])
def Inference():
	""" Responds to POST requests sent to the /Inference API endpoint. """

	detected = []
	idd = 0
	intruders = 0

	if len(request.files) != 0:
		img = np.fromstring(request.files['file'].read(), np.uint8)
	else:
		img = np.fromstring(request.data, np.uint8)

	img = cv2.imdecode(img, cv2.IMREAD_UNCHANGED)

	detections = CamAPI.TassAI.process(img)

	if len(detections):
		for roi, landmarks, identity in zip(*detections):
			frame, label = CamAPI.TassAI.draw_detection_roi(img, roi, identity)

			if label is "Unknown":
				label = 0
				intruders += 1
				mesg = "TassAI identified intruder"
			else:
				idd += 1
				mesg = "TassAI identified User #" + str(label)

			# Send iotJumpWay notification
			CamAPI.iot.channelPub("Sensors", {
				"Type": "TassAI",
				"Sensor": "Camera API",
				"Value": label,
				"Message": mesg
			})

			# Send iotJumpWay notification
			CamAPI.iot.channelPub("Cameras", {
				"Type": "TassAI",
				"Sensor": "Camera API",
				"Value": label,
				"Message": mesg
			})

			detected.append((label, mesg))

		resp = jsonpickle.encode({
			"Response": "OK",
			"Detections": detected
		})

		CamAPI.Helpers.logger.info("GeniSys detected " + str(idd) +
									" known humans and " + str(intruders) + " intruders.")

		return Response(response=resp, status=200, mimetype="application/json")

	else:

		CamAPI.Helpers.logger.info("GeniSys detected 0 known humans and 0 intruders.")

		resp = jsonpickle.encode({
			"Response": "FAILED",
			"Detections": []
		})

		return Response(response=resp, status=200, mimetype="application/json")

def main():
	# Starts threading
	signal.signal(signal.SIGINT, CamAPI.signal_handler)
	signal.signal(signal.SIGTERM, CamAPI.signal_handler)
	CamAPI.threading()

	app.run(host=CamAPI.Helpers.confs["TassAI"]["ip"],
			port=CamAPI.Helpers.confs["TassAI"]["port"])

if __name__ == "__main__":
	main()
