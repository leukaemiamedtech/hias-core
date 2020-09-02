#!/usr/bin/env python3
# ###########################################################################################
#
# Project:       Peter Moss Leukemia AI Research
# Repository:    HIAS: Hospital Intelligent Automation System
# Project:       GeniSysAI
#
# Author:         Adam Milton-Barker (AdamMiltonBarker.com)
# Contributors:
# Title:         GeniSysAI Class
# Description:   The GeniSysAI Class provides the Hospital Intelligent Automation System with
#                it's intelligent functionality.
# License:         MIT License
# Last Modified: 2020-06-04
#
############################################################################################


import json
import psutil
import requests
import signal
import sys
import threading

from threading import Thread

from Classes.Helpers import Helpers
from Classes.iotJumpWay import Device as iot
from Classes.CamRead import CamRead
from Classes.CamStream import CamStream
from Classes.Socket import Socket

class Camera():
	""" GeniSysAI Class

	The GeniSysAI Class provides the Hospital Intelligent Automation System with
	it's intelligent functionality.
	"""

	def __init__(self):
		""" Initializes the class. """

		self.Helpers = Helpers("Camera")

		# Initiates the iotJumpWay connection class
		self.iot = iot()
		self.iot.connect()

		self.Sockets = Socket()

		self.Helpers.logger.info("Camera Class initialization complete.")

	def life(self):
		""" Sends vital statistics to HIAS """

		cpu = psutil.cpu_percent()
		mem = psutil.virtual_memory()[2]
		hdd = psutil.disk_usage('/').percent
		tmp = psutil.sensors_temperatures()['coretemp'][0].current
		r = requests.get('http://ipinfo.io/json?token=15062dec38bfc3')
		data = r.json()
		location = data["loc"].split(',')

		self.Helpers.logger.info(
			"GeniSysAI Life (TEMPERATURE): " + str(tmp) + "\u00b0")
		self.Helpers.logger.info("GeniSysAI Life (CPU): " + str(cpu) + "%")
		self.Helpers.logger.info("GeniSysAI Life (Memory): " + str(mem) + "%")
		self.Helpers.logger.info("GeniSysAI Life (HDD): " + str(hdd) + "%")
		self.Helpers.logger.info("GeniSysAI Life (LAT): " + str(location[0]))
		self.Helpers.logger.info("GeniSysAI Life (LNG): " + str(location[1]))

		# Send iotJumpWay notification
		self.iot.channelPub("Life", {
			"CPU": cpu,
			"Memory": mem,
			"Diskspace": hdd,
			"Temperature": tmp,
			"Latitude": location[0],
			"Longitude": location[1]
		})

		threading.Timer(60.0, self.life).start()

	def threading(self):
		""" Creates required module threads. """

		# Life thread
		Thread(target=self.life, args=(), daemon=True).start()
		threading.Timer(60.0, self.life).start()

		# Camera read and stream
		Thread(target=CamRead.run, args=(self, ), daemon=True).start()
		Thread(target=CamStream.run, args=(self,), daemon=True).start()

	def signal_handler(self, signal, frame):
		self.Helpers.logger.info("Disconnecting")
		self.iot.disconnect()
		sys.exit(1)

Camera = Camera()

def main():
	# Starts threading
	signal.signal(signal.SIGINT, Camera.signal_handler)
	signal.signal(signal.SIGTERM, Camera.signal_handler)
	Camera.threading()

if __name__ == "__main__":

	main()
