#!/usr/bin/env python3
######################################################################################################
#
# Organization:  Peter Moss Leukemia AI Research
# Repository:    HIAS: Hospital Intelligent Automation System
#
# Author:        Adam Milton-Barker (AdamMiltonBarker.com)
#
# Title:         iotJumpWay Class
# Description:   The iotJumpWay module is used by the HIAS iotJumpWay service to collect
#                and store all data from the iotJumpWay devices on the HIAS network.
# License:       MIT License
# Last Modified: 2020-09-27
#
######################################################################################################

import base64
import json
import logging
import psutil
import requests
import signal
import sys
import time
import threading
import hmac

from threading import Thread

from datetime import datetime
from datetime import timedelta

from requests.auth import HTTPBasicAuth
from web3 import Web3

from Classes.Helpers import Helpers
from Classes.Blockchain import Blockchain
from Classes.iotJumpWay import Application
from Classes.MongoDB import MongoDB
from Classes.MySQL import MySQL

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
		""" Initiates the iotJumpWay connection class. """

		self.Application = Application({
			"host": self.Helpers.confs["iotJumpWay"]["host"],
			"port": self.Helpers.confs["iotJumpWay"]["port"],
			"lid": self.Helpers.confs["iotJumpWay"]["lid"],
			"aid": self.Helpers.confs["iotJumpWay"]["paid"],
			"an": self.Helpers.confs["iotJumpWay"]["pan"],
			"un": self.Helpers.confs["iotJumpWay"]["pun"],
			"pw": self.Helpers.confs["iotJumpWay"]["ppw"]
		})
		self.Application.connect()

		self.Application.appChannelSub("#","#")
		self.Application.appDeviceChannelSub("#", "#", "#")

		self.Application.appCommandsCallback = self.appCommandsCallback
		self.Application.appLifeCallback = self.appLifeCallback
		self.Application.appSensorCallback = self.appSensorCallback
		self.Application.appStatusCallback = self.appStatusCallback
		self.Application.appTriggerCallback = self.appTriggerCallback
		self.Application.deviceCameraCallback = self.deviceCameraCallback
		self.Application.deviceCommandsCallback = self.deviceCommandsCallback
		self.Application.deviceNfcCallback = self.deviceNfcCallback
		self.Application.deviceSensorCallback = self.deviceSensorCallback
		self.Application.deviceStatusCallback = self.deviceStatusCallback
		self.Application.deviceTriggerCallback = self.deviceTriggerCallback
		self.Application.deviceLifeCallback = self.deviceLifeCallback

		self.Helpers.logger.info("GeniSysAI Class initialization complete.")

	def mySqlConn(self):
		""" Initiates the MySQL connection class. """

		self.MySQL = MySQL()
		self.MySQL.startMySQL()

	def mongoDbConn(self):
		""" Initiates the MongoDB connection class. """

		self.MongoDB = MongoDB()
		self.MongoDB.startMongoDB()

	def blockchainConn(self):
		""" Initiates the Blockchain connection class. """

		self.Blockchain = Blockchain()
		self.Blockchain.startBlockchain()
		self.Blockchain.w3.geth.personal.unlockAccount(
			self.Helpers.confs["ethereum"]["haddress"], self.Helpers.confs["ethereum"]["hpass"], 0)
		self.Blockchain.w3.geth.personal.unlockAccount(
			self.Helpers.confs["ethereum"]["iaddress"], self.Helpers.confs["ethereum"]["ipass"], 0)

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

		self.Helpers.logger.info("GeniSysAI Life (TEMPERATURE): " + str(tmp) + "\u00b0")
		self.Helpers.logger.info("GeniSysAI Life (CPU): " + str(cpu) + "%")
		self.Helpers.logger.info("GeniSysAI Life (Memory): " + str(mem) + "%")
		self.Helpers.logger.info("GeniSysAI Life (HDD): " + str(hdd) + "%")
		self.Helpers.logger.info("GeniSysAI Life (LAT): " + str(location[0]))
		self.Helpers.logger.info("GeniSysAI Life (LNG): " + str(location[1]))

		# Send iotJumpWay notification
		self.Application.appChannelPub("Life", self.Helpers.confs["iotJumpWay"]["paid"], {
			"CPU": str(cpu),
			"Memory": str(mem),
			"Diskspace": str(hdd),
			"Temperature": str(tmp),
			"Latitude": float(location[0]),
			"Longitude": float(location[1])
		})

		threading.Timer(300.0, self.life).start()

	def appStatusCallback(self, topic, payload):
		"""
		iotJumpWay Application Status Callback

		The callback function that is triggerend in the event of an application
		status communication from the iotJumpWay.
		"""

		self.Helpers.logger.info("Recieved iotJumpWay Application Status: " + payload.decode())

		splitTopic=topic.split("/")
		status = payload.decode()

		application = self.MySQL.getApplication(splitTopic[2])

		if not self.Blockchain.iotJumpWayAccessCheck(application[12]):
			return

		self.MySQL.updateApplicationStatus(status, splitTopic)

		_id = self.MongoDB.insertData(self.MongoDB.mongoConn.Statuses, {
			"Use": "Application",
			"Location": splitTopic[0],
			"Zone": 0,
			"Application": splitTopic[2],
			"Device": 0,
			"Status": status,
			"Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
		})

		Thread(target=self.Blockchain.storeHash, args=(str(_id), self.Blockchain.hashStatus(status), int(time.time()), int(splitTopic[2]),
														application[10], application[12], "App"),
														daemon=True).start()

	def appLifeCallback(self, topic, payload):
		"""
		iotJumpWay Application Life Callback

		The callback function that is triggerend in the event of a application
		life communication from the iotJumpWay.
		"""

		self.Helpers.logger.info("Recieved iotJumpWay Application Life Data: " + payload.decode())

		data = json.loads(payload.decode("utf-8"))
		splitTopic = topic.split("/")

		application = self.MySQL.getApplication(splitTopic[2])

		if not self.Blockchain.iotJumpWayAccessCheck(application[12]):
			return

		self.MySQL.updateApplication("Life", data, splitTopic)

		_id = self.MongoDB.insertData(self.MongoDB.mongoConn.Life, {
			"Use": "Application",
			"Location": splitTopic[0],
			"Zone": 0,
			"Application": splitTopic[2],
			"Device": 0,
			"Data": data,
			"Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
		})

		Thread(target=self.Blockchain.storeHash, args=(str(_id), self.Blockchain.hashLifeData(data), int(time.time()), int(splitTopic[2]),
														application[10], application[12], "App"),
														daemon=True).start()

	def appCommandsCallback(self, topic, payload):
		"""
		iotJumpWay Application Commands Callback

		The callback function that is triggerend in the event of an application
		command communication from the iotJumpWay.
		"""

		self.Helpers.logger.info("Recieved iotJumpWay Application Command Data: " + payload.decode())

		command = json.loads(payload.decode("utf-8"))
		splitTopic=topic.split("/")

		self.MongoDB.insertData(self.MongoDB.mongoConn.Commands, {
			"Use": "Application",
			"Location": splitTopic[0],
			"Zone": 0,
			"From": command["From"],
			"To": splitTopic[3],
			"Type": command["Type"],
			"Value": command["Value"],
			"Message": command["Message"],
			"Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
		})

	def appSensorCallback(self, topic, payload):
		"""
		iotJumpWay Application Sensors Callback

		The callback function that is triggerend in the event of an application
		sensor communication from the iotJumpWay.
		"""

		self.Helpers.logger.info("Recieved iotJumpWay Application Sensors Data: " + payload.decode())
		command = json.loads(payload.decode("utf-8"))

		splitTopic = topic.split("/")

		self.MongoDB.insertData(self.MongoDB.mongoConn.Sensors, {
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
		})

	def appTriggerCallback(self, topic, payload):
		"""
		iotJumpWay Application Trigger Callback

		The callback function that is triggerend in the event of an application
		trigger communication from the iotJumpWay.
		"""

		self.Helpers.logger.info("Recieved iotJumpWay Application Trigger Data: " + payload.decode())
		command = json.loads(payload.decode("utf-8"))

	def deviceStatusCallback(self, topic, payload):
		"""
		iotJumpWay Device Status Callback

		The callback function that is triggerend in the event of an device
		status communication from the iotJumpWay.
		"""

		self.Helpers.logger.info("Recieved iotJumpWay Device Status Data: " + payload.decode())

		splitTopic = topic.split("/")
		status = payload.decode()

		device = self.MySQL.getDevice(splitTopic[3])

		if not self.Blockchain.iotJumpWayAccessCheck(device[8]):
			return

		self.MySQL.updateDeviceStatus(status, splitTopic[3])

		_id = self.MongoDB.insertData(self.MongoDB.mongoConn.Statuses, {
			"Use": "Device",
			"Location": splitTopic[0],
			"Zone": splitTopic[2],
			"Application": 0,
			"Device": splitTopic[3],
			"Status": status,
			"Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
		})

		Thread(target=self.Blockchain.storeHash, args=(str(_id), self.Blockchain.hashStatus(status), int(time.time()), int(splitTopic[3]),
														device[10], device[8], "Device"),
														daemon=True).start()

	def deviceLifeCallback(self, topic, payload):
		"""
		iotJumpWay Device Life Callback

		The callback function that is triggerend in the event of a device
		life communication from the iotJumpWay.
		"""

		self.Helpers.logger.info("Recieved iotJumpWay Device Life Data : " + payload.decode())

		data = json.loads(payload.decode("utf-8"))
		splitTopic = topic.split("/")

		device = self.MySQL.getDevice(splitTopic[3])

		if not self.Blockchain.iotJumpWayAccessCheck(device[8]):
			return

		self.MySQL.updateDevice("Life", data, splitTopic[3])

		_id = self.MongoDB.insertData(self.MongoDB.mongoConn.Life, {
			"Use": "Device",
			"Location": splitTopic[0],
			"Zone": splitTopic[2],
			"Application": 0,
			"Device": splitTopic[3],
			"Data": data,
			"Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
		})

		Thread(target=self.Blockchain.storeHash, args=(str(_id), self.Blockchain.hashLifeData(data), int(time.time()), int(splitTopic[3]),
														device[10], device[8], "Device"),
														daemon=True).start()

	def deviceCommandsCallback(self, topic, payload):
		"""
		iotJumpWay Application Commands Callback

		The callback function that is triggerend in the event of an device
		command communication from the iotJumpWay.
		"""

		self.Helpers.logger.info("Recieved iotJumpWay Device Command Data: " + payload.decode())

		command = json.loads(payload.decode("utf-8"))
		splitTopic = topic.split("/")

		device = self.MySQL.getDevice(splitTopic[3])

		if not self.Blockchain.iotJumpWayAccessCheck(device[8]):
			return

		_id = self.MongoDB.insertData(self.MongoDB.mongoConn.Commands, {
			"Use": "Device",
			"Location": splitTopic[0],
			"Zone": splitTopic[2],
			"From": command["From"],
			"To": splitTopic[3],
			"Type": command["Type"],
			"Value": command["Value"],
			"Message": command["Message"],
			"Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
		})

		Thread(target=self.Blockchain.storeHash, args=(str(_id), self.Blockchain.hashCommand(command), int(time.time()), int(splitTopic[3]),
														device[10], device[8], "Device"),
														daemon=True).start()

	def deviceNfcCallback(self, topic, payload):
		"""
		iotJumpWay Device NFC Callback

		The callback function that is triggerend in the event of a device
		NFC communication from the iotJumpWay.
		"""

		self.Helpers.logger.info("Recieved iotJumpWay Device NFC Data: " + payload.decode())
		data = json.loads(payload.decode("utf-8"))
		splitTopic = topic.split("/")

		if not self.MySQL.getUserNFC(data["Value"]):
			# Send iotJumpWay command
			self.Application.appDeviceChannelPub("Commands", splitTopic[2], splitTopic[3], {
				"From": str(self.Helpers.confs["iotJumpWay"]["paid"]),
				"Type": "NFC",
				"Value": "Not Authorized",
				"Message": "NFC Chip Not Authorized"
			})
			return

		device = self.MySQL.getDevice(splitTopic[3])

		if not self.Blockchain.iotJumpWayAccessCheck(device[8]):
			return

		_id = self.MongoDB.insertData(self.MongoDB.mongoConn.NFC, {
			"Use": "Device",
			"Location": splitTopic[0],
			"Zone": splitTopic[2],
			"Application": 0,
			"Device": splitTopic[3],
			"Sensor": data["Sensor"],
			"Value": data["Value"],
			"Message": data["Message"],
			"Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
		})

		self.Application.appDeviceChannelPub("Commands", splitTopic[2], splitTopic[3], {
			"From": str(splitTopic[3]),
			"Type": "NFC",
			"Value": "Authorized",
			"Message": "NFC Chip Authorized"
		})

		Thread(target=self.Blockchain.storeHash, args=(str(_id), self.Blockchain.hashNfc(data), int(time.time()), int(splitTopic[3]),
														device[10], device[8], "Device"),
														daemon=True).start()

	def deviceSensorCallback(self, topic, payload):
		"""
		iotJumpWay Application Sensors Callback

		The callback function that is triggerend in the event of an device
		sensor communication from the iotJumpWay.
		"""

		self.Helpers.logger.info("Recieved iotJumpWay Device Sensors Data : " + payload.decode())
		data = json.loads(payload.decode("utf-8"))
		splitTopic = topic.split("/")

		device = self.MySQL.getDevice(splitTopic[3])

		if not self.Blockchain.iotJumpWayAccessCheck(device[8]):
			return

		_id = self.MongoDB.insertData(self.MongoDB.mongoConn.Sensors, {
			"Use": "Device",
			"Location": splitTopic[0],
			"Zone": splitTopic[2],
			"Application": 0,
			"Device": splitTopic[3],
			"Sensor": data["Sensor"],
			"Type": data["Type"],
			"Value": data["Value"],
			"Message": data["Message"],
			"Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
		})

		Thread(target=self.Blockchain.storeHash, args=(str(_id), self.Blockchain.hashSensorData(data), int(time.time()), int(splitTopic[3]),
														device[10], device[8], "Device"),
														daemon=True).start()

	def deviceTriggerCallback(self, topic, payload):
		"""
		iotJumpWay Application Trigger Callback

		The callback function that is triggerend in the event of an device
		trigger communication from the iotJumpWay.
		"""

		self.Helpers.logger.info("Recieved iotJumpWay Device Trigger Data: " + payload.decode())
		command = json.loads(payload.decode("utf-8"))

	def deviceCameraCallback(self, topic, payload):
		"""
		iotJumpWay Device Camera Callback

		The callback function that is trigge105rend in the event of a camera detecting a
		known user or intruder.
		"""

		self.Helpers.logger.info("Recieved iotJumpWay Device Camera Data: " + payload.decode())

		data = json.loads(payload.decode("utf-8"))
		splitTopic = topic.split("/")

		nlu = self.MySQL.getNLU(splitTopic)

		if nlu is not "":

			if data["Value"] is not 0:

				self.MySQL.updateUserLocation(splitTopic, data)

				self.MongoDB.insertData(self.MongoDB.mongoConn.Users, {
					"User": int(data["Value"]),
					"Location": splitTopic[0],
					"Zone": splitTopic[2],
					"Device": splitTopic[3],
					"Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
				})

				userDetails = self.MySQL.getUser(data)

				if userDetails[1] and nlu[0]:
					self.Application.appDeviceChannelPub("Commands", splitTopic[2], nlu[0], {
										"From": str(splitTopic[3]),
										"Type": "Welcome",
										"Message": "Welcome " + userDetails[0],
										"Value": userDetails[0]})
					self.MySQL.updateUser(data)

				elif userDetails[1] and userDetails[2] is "ONLINE":
					print("SEND USER APP NOTIFICATION")

	def signal_handler(self, signal, frame):
		self.Helpers.logger.info("Disconnecting")
		self.Application.appDisconnect()
		sys.exit(1)

iotJumpWay = iotJumpWay()

def main():

	signal.signal(signal.SIGINT, iotJumpWay.signal_handler)
	signal.signal(signal.SIGTERM, iotJumpWay.signal_handler)

	# Starts the application
	iotJumpWay.mySqlConn()
	iotJumpWay.mongoDbConn()
	iotJumpWay.blockchainConn()
	iotJumpWay.startIoT()

	Thread(target=iotJumpWay.life, args=(), daemon=True).start()

	while True:
		time.sleep(1)
	exit()

if __name__ == "__main__":
	main()
