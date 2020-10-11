######################################################################################################
#
# Organization:  Peter Moss Leukemia AI Research
# Repository:    HIAS: Hospital Intelligent Automation System
#
# Author:        Adam Milton-Barker (AdamMiltonBarker.com)
#
# Title:         Client Class
# Description:   The Client module is used to test the HIAS Sercurity Service.
# License:       MIT License
# Last Modified: 2020-09-23
#
######################################################################################################

import cv2
import json
import os
import requests
import sys
import time

from Classes.Helpers import Helpers

class Client():
	""" Client Class
	The Client module is used to test the HIAS Sercurity Service.
	"""

	def __init__(self):
		""" Initializes the class. """

		self.Helpers = Helpers("Client")

		self.addr = "http://"+self.Helpers.confs["TassAI"]["ip"] + \
			':'+str(self.Helpers.confs["TassAI"]["port"]) + '/Inference'
		self.headers = {'content-type': 'image/jpeg'}

		self.Helpers.logger.info("Client class initialized.")

	def send(self, imagePath):
		""" Sends image to the Inference API endpoint. """

		img = cv2.imread(imagePath)
		_, img_encoded = cv2.imencode('.jpg', img)

		response = requests.post(self.addr, data=img_encoded.tostring(),
                           headers=self.headers)

		response = json.loads(response.text)

	def test(self):
		""" Loops through all images in the testing directory and sends them to the Inference API endpoint. """

		testingDir = self.Helpers.confs["TassAI"]["test"]

		for test in os.listdir(testingDir):
			if os.path.splitext(test)[1] in self.Helpers.confs["TassAI"]["core"]["allowed"]:
				testPath = testingDir+test
				self.Helpers.logger.info("Sending " + testPath)
				self.send(testingDir+test)
				time.sleep(5)


Client = Client()

if __name__ == "__main__":

	if sys.argv[1] == "Test":
		""" Sends all images in the test directory. """

		Client.test()

	elif sys.argv[1] == "Send":
		""" Sends a single image, path for image is sent as argument 2. """

		Client.send(sys.argv[2])
