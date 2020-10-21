#!/usr/bin/env python3
######################################################################################################
#
# Organization:  Peter Moss Leukemia AI Research
# Repository:    HIAS: Hospital Intelligent Automation System
#
# Author:        Adam Milton-Barker (AdamMiltonBarker.com)
#
# Title:         iotJumpWay MQTT IoT Agent
# Description:   The MQTT IoT Agent listens for all traffic coming from devices connected to the HIAS
#                HIAS network using the MQTT & Websocket protocols, translates them into a format
#                compatible with the HIAS iotJumpWay Context Broker and sends the data to the broker
#                for processing and storage.
# License:       MIT License
# Last Modified: 2020-10-13
#
######################################################################################################

import json
import os
import psutil
import requests
import signal
import sys
import time
import threading

sys.path.insert(0, os.path.abspath(
	os.path.join(os.path.dirname(__file__), '..')))

from datetime import datetime
from datetime import timedelta

from flask import Flask, request, Response
from threading import Thread

from web3 import Web3

from Classes.Helpers import Helpers
from Classes.ContextBroker import ContextBroker
from Classes.Blockchain import Blockchain
from Classes.MQTT import Application
from Classes.MongoDB import MongoDB


class MQTT():
	""" iotJumpWay MQTT IoT Agent

	The MQTT IoT Agent listens for all traffic coming from devices
	connected to the HIAS network using the MQTT protocol.
	"""

	def __init__(self):
		""" Initializes the class. """

		self.Helpers = Helpers("MQTT")
		self.Helpers.logger.info("MQTT Agent initialization complete.")

	def startIoT(self):
		""" Initiates the iotJumpWay connection. """

		self.Application = Application({
			"host": self.Helpers.confs["iotJumpWay"]["host"],
			"port": self.Helpers.confs["iotJumpWay"]["MQTT"]["port"],
			"lid": self.Helpers.confs["iotJumpWay"]["MQTT"]["Agent"]["lid"],
			"aid": self.Helpers.confs["iotJumpWay"]["MQTT"]["Agent"]["aid"],
			"an": self.Helpers.confs["iotJumpWay"]["MQTT"]["Agent"]["an"],
			"un": self.Helpers.confs["iotJumpWay"]["MQTT"]["Agent"]["un"],
			"pw": self.Helpers.confs["iotJumpWay"]["MQTT"]["Agent"]["pw"]
		})
		self.Application.connect()

		self.Application.appChannelSub("#", "#")
		self.Application.appDeviceChannelSub("#", "#", "#")

		self.Application.appLifeCallback = self.appLifeCallback
		self.Application.appSensorCallback = self.appSensorCallback
		self.Application.appStatusCallback = self.appStatusCallback
		self.Application.deviceCommandsCallback = self.deviceCommandsCallback
		self.Application.deviceNfcCallback = self.deviceNfcCallback
		self.Application.deviceSensorCallback = self.deviceSensorCallback
		self.Application.deviceStatusCallback = self.deviceStatusCallback
		self.Application.deviceLifeCallback = self.deviceLifeCallback

		self.Helpers.logger.info("iotJumpWay connection initiated.")

	def contextConn(self):
		""" Initiates the Context Broker class. """

		self.ContextBroker = ContextBroker()

	def mongoDbConn(self):
		""" Initiates the MongoDB connection. """

		self.MongoDB = MongoDB()
		self.MongoDB.startMongoDB()

	def blockchainConn(self):
		""" Initiates the Blockchain connection. """

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

		self.Application.appChannelPub("Life", self.Helpers.confs["iotJumpWay"]["MQTT"]["Agent"]["aid"], {
			"CPU": str(cpu),
			"Memory": str(mem),
			"Diskspace": str(hdd),
			"Temperature": str(tmp),
			"Latitude": float(location[0]),
			"Longitude": float(location[1])
		})

		self.Helpers.logger.info("Agent life statistics published.")
		threading.Timer(300.0, self.life).start()

	def appStatusCallback(self, topic, payload):
		"""
		iotJumpWay Application Status Callback

		The callback function that is triggered in the event of status
		communication via MQTT from an iotJumpWay application.
		"""

		self.Helpers.logger.info(
			"Recieved iotJumpWay Application Status: " + payload.decode())

		splitTopic = topic.split("/")
		status = payload.decode()

		location = splitTopic[0]
		application = splitTopic[2]

		requiredAttributes = self.ContextBroker.getRequiredAttributes(
			application, "Application")

		locationID = int(requiredAttributes["Data"]["lid"]["value"])
		bcAddress = requiredAttributes["Data"]["blockchain"]["address"]

		if not self.Blockchain.iotJumpWayAccessCheck(bcAddress):
			return

		updateResponse = self.ContextBroker.updateEntity(
			application, "Application", {
			"status": {
				"value": status,
				"timestamp": datetime.now().isoformat()
			}
		})

		if updateResponse["Response"] == "OK":
			self.Helpers.logger.info("Application " + application + " status update OK")
			_id = self.MongoDB.insertData(self.MongoDB.mongoConn.Statuses, {
				"Use": "Application",
				"Location": location,
				"Zone": "NA",
				"Application": application,
				"Device": "NA",
				"Status": status,
				"Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
			}, None)
			Thread(target=self.Blockchain.storeHash, args=(str(_id), self.Blockchain.hashStatus(status), int(time.time()),
															locationID, application, bcAddress, "App"), daemon=True).start()
		else:
			self.Helpers.logger.error("Application " + application + " status update KO")

	def appLifeCallback(self, topic, payload):
		"""
		iotJumpWay Application Life Callback

		The callback function that is triggered in the event of life
		communication via MQTT from an iotJumpWay application.
		"""

		self.Helpers.logger.info(
			"Recieved iotJumpWay Application Life Data: " + payload.decode())

		data = json.loads(payload.decode("utf-8"))
		splitTopic = topic.split("/")

		location = splitTopic[0]
		application = splitTopic[2]

		requiredAttributes = self.ContextBroker.getRequiredAttributes(
			application, "Application")

		locationID = int(requiredAttributes["Data"]["lid"]["value"])
		bcAddress = requiredAttributes["Data"]["blockchain"]["address"]

		if not self.Blockchain.iotJumpWayAccessCheck(bcAddress):
			return

		updateResponse = self.ContextBroker.updateEntity(
			application, "Application", {
				"status": {
					"value": "ONLINE",
					"timestamp": datetime.now().isoformat()
				},
				"cpuUsage": {
					"value": data["CPU"]
				},
				"memoryUsage": {
					"value": data["Memory"]
				},
				"hddUsage": {
					"value": data["Diskspace"]
				},
				"temperature": {
					"value": data["Temperature"]
				},
				"location": {
					"type": "geo:json",
					"value": {
						"type": "Point",
						"coordinates": [float(data["Latitude"]), float(data["Longitude"])]
					}
				}
			})

		if updateResponse["Response"] == "OK":
			_id = self.MongoDB.insertData(self.MongoDB.mongoConn.Life, {
				"Use": "Application",
				"Location": location,
				"Zone": "NA",
				"Application": application,
				"Device": "NA",
				"Data": data,
				"Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
			}, None)
			Thread(target=self.Blockchain.storeHash, args=(str(_id), self.Blockchain.hashLifeData(data), int(time.time()),
															locationID, application, bcAddress, "App"), daemon=True).start()
			self.Helpers.logger.info("Application " + application + " status update OK")
		else:
			self.Helpers.logger.error("Application " + application + " life update KO")

	def appSensorCallback(self, topic, payload):
		"""
		iotJumpWay Application Sensors Callback

		The callback function that is triggered in the event of sensor
		communication via MQTT from an iotJumpWay application.
		"""

		self.Helpers.logger.info(
			"Recieved iotJumpWay Application Sensors Data: " + payload.decode())

		data = json.loads(payload.decode("utf-8"))
		splitTopic = topic.split("/")

		location = splitTopic[0]
		application = splitTopic[2]

		requiredAttributes = self.ContextBroker.getRequiredAttributes(
			application, "Application")

		locationID = int(requiredAttributes["Data"]["lid"]["value"])
		bcAddress = requiredAttributes["Data"]["blockchain"]["address"]

		if not self.Blockchain.iotJumpWayAccessCheck(bcAddress):
			return

		updateResponse = self.ContextBroker.updateEntity(
			application, "Application", {
				"status": {
					"value": "ONLINE",
					"timestamp": datetime.now().isoformat()
				},
				"cpuUsage": {
					"value": data["CPU"]
				},
				"memoryUsage": {
					"value": data["Memory"]
				},
				"hddUsage": {
					"value": data["Diskspace"]
				},
				"temperature": {
					"value": data["Temperature"]
				},
				"location": {
					"type": "geo:json",
					"value": {
						"type": "Point",
						"coordinates": [float(data["Latitude"]), float(data["Longitude"])]
					}
				}
			})

		if updateResponse["Response"] == "OK":
			_id = self.MongoDB.insertData(self.MongoDB.mongoConn.Life, {
				"Use": "Application",
				"Location": location,
				"Zone": "NA",
				"Application": application,
				"Device": "NA",
				"Data": data,
				"Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
			}, None)
			Thread(target=self.Blockchain.storeHash, args=(str(_id), self.Blockchain.hashLifeData(data), int(time.time()),
															locationID, application, bcAddress, "App"), daemon=True).start()
			self.Helpers.logger.info("Application " + application + " status update OK")
		else:
			self.Helpers.logger.error("Application " + application + " life update KO")

		data = json.loads(payload.decode("utf-8"))
		splitTopic = topic.split("/")

		location = splitTopic[0]
		application = splitTopic[2]

		requiredAttributes = self.ContextBroker.getRequiredAttributes(
			application, "Application")

		locationID = int(requiredAttributes["Data"]["lid"]["value"])
		bcAddress = requiredAttributes["Data"]["blockchain"]["address"]

		if not self.Blockchain.iotJumpWayAccessCheck(bcAddress):
			return

		self.MongoDB.insertData(self.MongoDB.mongoConn.Sensors, {
			"Use": "Application",
			"Location": splitTopic[0],
			"Zone": 0,
			"Application": splitTopic[2],
			"Device": 0,
			"Sensor": data["Sensor"],
			"Type": data["Type"],
			"Value": data["Value"],
			"Message": data["Message"],
			"Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
		}, None)

	def deviceStatusCallback(self, topic, payload):
		"""
		iotJumpWay Device Status Callback

		The callback function that is triggered in the event of status
		communication via MQTT from an iotJumpWay device.
		"""

		self.Helpers.logger.info(
			"Recieved iotJumpWay Device Status: " + payload.decode())

		splitTopic = topic.split("/")
		status = payload.decode()

		location = splitTopic[0]
		zone = splitTopic[2]
		device = splitTopic[3]

		requiredAttributes = self.ContextBroker.getRequiredAttributes(
			device, "Device")

		locationID = int(requiredAttributes["Data"]["lid"]["value"])
		bcAddress = requiredAttributes["Data"]["blockchain"]["address"]

		if not self.Blockchain.iotJumpWayAccessCheck(bcAddress):
			return

		updateResponse = self.ContextBroker.updateEntity(
			device, "Device", {
			"status": {
				"value": status,
				"timestamp": datetime.now().isoformat()
			}
		})

		if updateResponse["Response"] == "OK":
			self.Helpers.logger.info("Device " + device + " status update OK")
			_id = self.MongoDB.insertData(self.MongoDB.mongoConn.Statuses, {
				"Use": "Device",
				"Location": location,
				"Zone": zone,
				"Application": "NA",
				"Device": device,
				"Status": status,
				"Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
			}, None)
			Thread(target=self.Blockchain.storeHash, args=(str(_id), self.Blockchain.hashStatus(status), int(time.time()),
															locationID, device, bcAddress, "Device"), daemon=True).start()
		else:
			self.Helpers.logger.error("Device " + device + " status update KO")

	def deviceLifeCallback(self, topic, payload):
		"""
		iotJumpWay Device Life Callback

		The callback function that is triggered in the event of life
		communication via MQTT from an iotJumpWay device.
		"""

		self.Helpers.logger.info(
			"Recieved iotJumpWay Device Life Data: " + payload.decode())

		data = json.loads(payload.decode("utf-8"))
		splitTopic = topic.split("/")

		location = splitTopic[0]
		zone = splitTopic[2]
		device = splitTopic[3]

		requiredAttributes = self.ContextBroker.getRequiredAttributes(
			device, "Device")

		locationID = int(requiredAttributes["Data"]["lid"]["value"])
		bcAddress = requiredAttributes["Data"]["blockchain"]["address"]

		if not self.Blockchain.iotJumpWayAccessCheck(bcAddress):
			return

		updateResponse = self.ContextBroker.updateEntity(
			device, "Device", {
				"status": {
					"value": "ONLINE",
					"timestamp": datetime.now().isoformat()
				},
				"cpuUsage": {
					"value": data["CPU"]
				},
				"memoryUsage": {
					"value": data["Memory"]
				},
				"hddUsage": {
					"value": data["Diskspace"]
				},
				"temperature": {
					"value": data["Temperature"]
				},
				"location": {
					"type": "geo:json",
					"value": {
						"type": "Point",
						"coordinates": [float(data["Latitude"]), float(data["Longitude"])]
					}
				}
			})

		if updateResponse["Response"] == "OK":
			_id = self.MongoDB.insertData(self.MongoDB.mongoConn.Life, {
				"Use": "Application",
				"Location": location,
				"Zone": zone,
				"Application": "NA",
				"Device": device,
				"Data": data,
				"Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
			}, None)
			Thread(target=self.Blockchain.storeHash, args=(str(_id), self.Blockchain.hashLifeData(data), int(time.time()),
															locationID, device, bcAddress, "Device"), daemon=True).start()
			self.Helpers.logger.info("Device " + device + " status update OK")
		else:
			self.Helpers.logger.error("Device " + device + " life update KO")

	def deviceCommandsCallback(self, topic, payload):
		"""
		iotJumpWay Device Commands Callback

		The callback function that is triggerend in the event of an device
		command communication from the iotJumpWay.
		"""

		self.Helpers.logger.info(
			"Recieved iotJumpWay Device Command Data: " + payload.decode())

		command = json.loads(payload.decode("utf-8"))
		splitTopic = topic.split("/")

		location = splitTopic[0]
		zone = splitTopic[2]
		device = splitTopic[3]

		requiredAttributes = self.ContextBroker.getRequiredAttributes(
			device, "Device")

		locationID = int(requiredAttributes["Data"]["lid"]["value"])
		bcAddress = requiredAttributes["Data"]["blockchain"]["address"]

		if not self.Blockchain.iotJumpWayAccessCheck(bcAddress):
			return

		_id = self.MongoDB.insertData(self.MongoDB.mongoConn.Commands, {
			"Use": "Device",
			"Location": location,
			"Zone": zone,
			"From": command["From"],
			"To": device,
			"Type": command["Type"],
			"Value": command["Value"],
			"Message": command["Message"],
			"Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
		}, None)

		Thread(target=self.Blockchain.storeHash, args=(str(_id), self.Blockchain.hashCommand(command), int(time.time()),
														locationID, device, bcAddress, "Device"), daemon=True).start()
		self.Helpers.logger.info("Device " + device + " command update OK")

	def deviceNfcCallback(self, topic, payload):
		"""
		iotJumpWay Device NFC Callback

		The callback function that is triggered in the event of a device
		NFC communication from the iotJumpWay.
		"""

		self.Helpers.logger.info(
			"Recieved iotJumpWay Device NFC Data: " + payload.decode())

		data = json.loads(payload.decode("utf-8"))
		splitTopic = topic.split("/")

		location = splitTopic[0]
		zone = splitTopic[2]
		device = splitTopic[3]

		requiredAttributes = self.ContextBroker.getRequiredAttributes(
			device, "Device")

		locationID = int(requiredAttributes["Data"]["lid"]["value"])
		bcAddress = requiredAttributes["Data"]["blockchain"]["address"]

		if not self.Blockchain.iotJumpWayAccessCheck(bcAddress):
			return

		check = self.ContextBroker.getNFC(data["Value"])
		if check["Response"] is "OK" and len(check["Data"]):
			self.Application.appDeviceChannelPub("Commands", zone, device, {
				"From": str(self.Helpers.confs["iotJumpWay"]["MQTT"]["Agent"]["aid"]),
				"Type": "NFC",
				"Value": "Not Authorized",
				"Message": "NFC Chip Not Authorized"
			})
			self.Helpers.logger.info("Device " + device + " NFC Not Allowed KO")
			return

		_id = self.MongoDB.insertData(self.MongoDB.mongoConn.NFC, {
			"Use": "Device",
			"Location": location,
			"Zone": zone,
			"Application": 0,
			"Device": device,
			"Sensor": data["Sensor"],
			"Value": data["Value"],
			"Message": data["Message"],
			"Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
		}, None)

		self.Application.appDeviceChannelPub("Commands", zone, device, {
			"From": str(self.Helpers.confs["iotJumpWay"]["MQTT"]["Agent"]["aid"]),
			"Type": "NFC",
			"Value": "Authorized",
			"Message": "NFC Chip Authorized"
		})

		Thread(target=self.Blockchain.storeHash, args=(str(_id), self.Blockchain.hashNfc(data), int(time.time()),
														locationID, device, bcAddress, "Device"), daemon=True).start()
		self.Helpers.logger.info("Device " + device + " NFC Allowed OK")

	def deviceSensorCallback(self, topic, payload):
		"""
		iotJumpWay Application Sensors Callback

		The callback function that is triggered in the event of an device
		sensor communication from the iotJumpWay.
		"""

		self.Helpers.logger.info(
			"Recieved iotJumpWay Device Sensors Data : " + payload.decode())

		data = json.loads(payload.decode("utf-8"))
		splitTopic = topic.split("/")

		location = splitTopic[0]
		zone = splitTopic[2]
		device = splitTopic[3]

		requiredAttributes = self.ContextBroker.getRequiredAttributes(
			device, "Device")

		locationID = int(requiredAttributes["Data"]["lid"]["value"])
		bcAddress = requiredAttributes["Data"]["blockchain"]["address"]

		if not self.Blockchain.iotJumpWayAccessCheck(bcAddress):
			return

		_id = self.MongoDB.insertData(self.MongoDB.mongoConn.Sensors, {
			"Use": "Device",
			"Location": location,
			"Zone": zone,
			"Application": 0,
			"Device": device,
			"Sensor": data["Sensor"],
			"Type": data["Type"],
			"Value": data["Value"],
			"Message": data["Message"],
			"Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
		}, None)

		Thread(target=self.Blockchain.storeHash, args=(str(_id), self.Blockchain.hashSensorData(data), int(time.time()),
														locationID, device, bcAddress, "Device"), daemon=True).start()
		self.Helpers.logger.info("Device " + device + " NFC Allowed OK")

	def respond(self, responseCode, response):
		""" Builds the request repsonse """

		return Response(response=json.dumps(response, indent=4), status=responseCode,
						mimetype="application/json")

	def signal_handler(self, signal, frame):
		self.Helpers.logger.info("Disconnecting")
		self.Application.appDisconnect()
		sys.exit(1)

app = Flask(__name__)
MQTT = MQTT()

@app.route('/About', methods=['GET'])
def about():
	""" Responds to POST requests sent to the North Port About API endpoint. """

	return MQTT.respond(200, {
		"Identifier": MQTT.Helpers.confs["iotJumpWay"]["MQTT"]["Agent"]["identifier"],
		"Host": MQTT.Helpers.confs["iotJumpWay"]["ip"],
		"NorthPort": MQTT.Helpers.confs["iotJumpWay"]["MQTT"]["Agent"]["northPort"],
		"CPU": psutil.cpu_percent(),
		"Memory": psutil.virtual_memory()[2],
		"Diskspace": psutil.disk_usage('/').percent,
		"Temperature": psutil.sensors_temperatures()['coretemp'][0].current
	})

@app.route('/Commands', methods=['POST'])
def commands():
	""" Responds to POST requests sent to the North Port Commands API endpoint. """

	if request.headers["Content-Type"] == "application/json":
		command = request.json
		if command["ToType"] is "Device":
			MQTT.Application.appDeviceChannelPub("Commands", command["ToLocation"], command["ToDevice"], {
				"From": command["From"],
				"Type": command["FromType"],
				"Value": command["Value"],
				"Message": command["Message"]
			})
		elif command["ToType"] is "Application":
			MQTT.Application.appChannelPub("Commands", command["ToApplication"], {
				"From": command["From"],
				"Type": command["FromType"],
				"Value": command["Value"],
				"Message": command["Message"]
			})
		else:
			return MQTT.respond(400, {
				"Response": "Failed",
				"Error": "BadRequest",
				"Description": "Command type not supported!"
			})
	else:
		return MQTT.respond(405, {
			"Response": "Failed",
			"Error": "MethodNotAlowed",
			"Description": "Method not allowed!"
		})

def main():

	signal.signal(signal.SIGINT, MQTT.signal_handler)
	signal.signal(signal.SIGTERM, MQTT.signal_handler)

	# Starts the application
	MQTT.contextConn()
	MQTT.mongoDbConn()
	MQTT.blockchainConn()
	MQTT.startIoT()

	Thread(target=MQTT.life, args=(), daemon=True).start()

	app.run(host=MQTT.Helpers.confs["iotJumpWay"]["ip"],
			port=MQTT.Helpers.confs["iotJumpWay"]["MQTT"]["Agent"]["northPort"])

if __name__ == "__main__":
	main()
