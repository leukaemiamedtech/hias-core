#!/usr/bin/env python3
######################################################################################################
#
# Organization:  Peter Moss Leukemia AI Research
# Repository:    HIAS: Hospital Intelligent Automation System
#
# Author:        Adam Milton-Barker (AdamMiltonBarker.com)
#
# Title:         iotJumpWay AMQP IoT Agent
# Description:   The AMQP IoT Agent listens for all traffic coming from devices connected to the HIAS
#                HIAS network using the AMQP protocol, translates them into a format compatible with
#                the HIAS iotJumpWay Context Broker and sends the data to the broker for processing
#                and storage.
# License:       MIT License
# Last Modified: 2020-10-18
#
######################################################################################################


import os
import sys
import time

sys.path.insert(0, os.path.abspath(
	os.path.join(os.path.dirname(__file__), '..')))

from gevent import monkey
monkey.patch_all()

import json
import pika
import psutil
import requests
import signal
import ssl
import threading

from datetime import timedelta
from datetime import datetime
from flask import Flask, request, Response
from threading import Thread

from Classes.Helpers import Helpers
from Classes.Blockchain import Blockchain
from Classes.ContextBroker import ContextBroker
from Classes.MongoDB import MongoDB


class AMQP():
	""" iotJumpWay AMQP IoT Agent

	The AMQP IoT Agent listens for all traffic coming from devices
	connected to the HIAS network using the AMQP protocol.
	"""

	def __init__(self):
		""" Initializes the class. """

		self.Helpers = Helpers("AMQP")
		self.Helpers.logger.info("AMQP Agent initialization complete.")

	def amqpConnect(self):
		""" Initiates the AMQP connection. """

		credentials = self.Helpers.confs["iotJumpWay"]["AMQP"]["un"] + \
			':' + self.Helpers.confs["iotJumpWay"]["AMQP"]["pw"]
		parameters = pika.URLParameters(
			'amqps://' + credentials + '@' + self.Helpers.confs["iotJumpWay"]["host"] + '/' + self.Helpers.confs["iotJumpWay"]["AMQP"]["vhost"])
		context = ssl.SSLContext(ssl.PROTOCOL_TLSv1_2)
		context.load_verify_locations(self.Helpers.confs["iotJumpWay"]["cert"])
		parameters.ssl_options = pika.SSLOptions(context)

		self.connection = pika.BlockingConnection(parameters)
		self.channel = self.connection.channel()
		self.amqpPublish(json.dumps({"Application": self.Helpers.confs["iotJumpWay"]["AMQP"]["identifier"],
										"Status": "ONLINE"}), "Statuses")
		self.Helpers.logger.info("AMQP connection established!")

	def blockchainConnect(self):
		""" Initiates the Blockchain connection. """

		self.Blockchain = Blockchain()
		self.Blockchain.startBlockchain()
		self.Blockchain.w3.geth.personal.unlockAccount(
			self.Helpers.confs["ethereum"]["haddress"], self.Helpers.confs["ethereum"]["hpass"], 0)
		self.Blockchain.w3.geth.personal.unlockAccount(
			self.Helpers.confs["ethereum"]["iaddress"], self.Helpers.confs["ethereum"]["ipass"], 0)

	def mongoDbConn(self):
		""" Initiates the MongoDB connection. """

		self.MongoDB = MongoDB()
		self.MongoDB.startMongoDB()

	def amqpConsumeSet(self):
		""" Sets up the AMQP queue subscriptions. """

		self.channel.basic_consume('Life',
							self.lifeCallback,
							auto_ack=True)
		self.channel.basic_consume('Statuses',
							self.statusesCallback,
							auto_ack=True)
		self.Helpers.logger.info("AMQP consume setup!")

	def amqpConsumeStart(self):
		""" Starts consuming. """

		self.Helpers.logger.info("AMQP consume starting!")
		self.channel.start_consuming()

	def amqpPublish(self, data, routing_key):
		""" Publishes to an AMQP broker queue. """

		self.channel.basic_publish(
			exchange=self.Helpers.confs["iotJumpWay"]["AMQP"]["exchange"], routing_key=routing_key, body=data)
		self.Helpers.logger.info("AMQP consume setup!")

	def life(self):
		""" Sends vital statistics to HIAS. """

		cpu = psutil.cpu_percent()
		mem = psutil.virtual_memory()[2]
		hdd = psutil.disk_usage('/').percent
		tmp = psutil.sensors_temperatures()['coretemp'][0].current
		r = requests.get('http://ipinfo.io/json?token=' +
							self.Helpers.confs["iotJumpWay"]["ipinfo"])
		data = r.json()
		location = data["loc"].split(',')

		self.amqpPublish(json.dumps({
			"Application": self.Helpers.confs["iotJumpWay"]["AMQP"]["identifier"],
			"CPU": str(cpu),
			"Memory": str(mem),
			"Diskspace": str(hdd),
			"Temperature": str(tmp),
			"Latitude": float(location[0]),
			"Longitude": float(location[1])
		}), "Life")

		self.Helpers.logger.info("Agent life statistics published.")
		threading.Timer(300.0, self.life).start()

	def contextConn(self):
		""" Initiates the Context Broker class. """

		self.ContextBroker = ContextBroker()

	def statusesCallback(self, ch, method, properties, body):
		""" Processes status messages. """
		Thread(target=self.statusesWorker, args=(body,), daemon=True).start()

	def statusesWorker(self, body):
		""" Processes status messages. """

		self.Helpers.logger.info("Life data callback")
		data = json.loads(body)

		if "Application" in data:
			entityType = "Application"
			entity = data["Application"]
			application = entity
			zone = "NA"
			device = "NA"
			short = "App"
			del data['Application']
		elif "Device" in data:
			entityType = "Device"
			entity = data["Device"]
			application = "NA"
			device = entity
			short = "Device"
			del data['Device']

		status = data['Status']

		requiredAttributes = self.ContextBroker.getRequiredAttributes(
			entity, entityType)

		locationID = int(requiredAttributes["Data"]["lid"]["value"])
		location = requiredAttributes["Data"]["lid"]["entity"]
		if entityType is "Device":
			zone = requiredAttributes["Data"]["zid"]["entity"]
		bcAddress = requiredAttributes["Data"]["blockchain"]["address"]

		if not self.Blockchain.iotJumpWayAccessCheck(bcAddress):
			return

		updateResponse = self.ContextBroker.updateEntity(
			entity, entityType, {
				"status": {
					"value": status,
					"timestamp": datetime.now().isoformat()
			}
		})

		if updateResponse["Response"] == "OK":
			self.Helpers.logger.info(entityType + " " + entity + " status update OK")
			_id = self.MongoDB.insertData(self.MongoDB.mongoConn.Statuses, {
				"Use": entityType,
				"Location": location,
				"Zone": zone,
				"Application": application,
				"Device": device,
				"Status": status,
				"Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
			}, None)
			Thread(target=self.Blockchain.storeHash, args=(str(_id), self.Blockchain.hashStatus(status), int(time.time()),
					locationID, entity, bcAddress, short), daemon=True).start()
		else:
			self.Helpers.logger.error(
				entityType + " " + entity + " status update KO")

	def lifeCallback(self, ch, method, properties, body):
		""" Processes life messages. """
		Thread(target=self.lifeWorker, args=(body,), daemon=True).start()

	def lifeWorker(self, body):

		self.Helpers.logger.info("Life data callback")
		data = json.loads(body)

		if "Application" in data:
			entityType = "Application"
			entity = data["Application"]
			application = entity
			zone = "NA"
			device = "NA"
			short = "App"
			del data['Application']
		elif "Device" in data:
			entityType = "Device"
			entity = data["Device"]
			application = "NA"
			device = entity
			short = "Device"
			del data['Device']

		requiredAttributes = self.ContextBroker.getRequiredAttributes(
			entity, entityType)

		locationID = int(requiredAttributes["Data"]["lid"]["value"])
		location = requiredAttributes["Data"]["lid"]["entity"]
		if entityType is "Device":
			zone = requiredAttributes["Data"]["zid"]["entity"]
		bcAddress = requiredAttributes["Data"]["blockchain"]["address"]

		if not self.Blockchain.iotJumpWayAccessCheck(bcAddress):
			return

		updateResponse = self.ContextBroker.updateEntity(
			entity, entityType, {
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
			self.Helpers.logger.info(entityType + " " + entity + " status update OK")
			_id = self.MongoDB.insertData(self.MongoDB.mongoConn.Life, {
				"Use": "Application",
				"Location": location,
				"Zone": zone,
				"Application": application,
				"Device": device,
				"Data": data,
				"Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
			}, None)
			Thread(target=self.Blockchain.storeHash, args=(str(_id), self.Blockchain.hashLifeData(data), int(time.time()),
					locationID, entity, bcAddress, short), daemon=True).start()
		else:
			self.Helpers.logger.error(
				entityType + " " + entity + " status update KO")

	def agentThreads(self):
		""" Processes status messages. """

		Thread(target=AMQP.life, args=(), daemon=True).start()
		Thread(target=AMQP.amqpConsumeStart, args=(), daemon=True).start()

	def respond(self, responseCode, response):
		""" Builds the request repsonse """

		return Response(response=json.dumps(response, indent=4), status=responseCode,
						mimetype="application/json")

	def signal_handler(self, signal, frame):
		self.Helpers.logger.info("Disconnecting")
		self.amqpPublish(json.dumps({"Application": self.Helpers.confs["iotJumpWay"]["AMQP"]["identifier"],
										"Status": "OFFLINE"}), "Statuses")
		self.connection.close()
		sys.exit(1)


app = Flask(__name__)
AMQP = AMQP()


@app.route('/About', methods=['GET'])
def about():
	""" Responds to POST requests sent to the North Port About API endpoint. """

	return AMQP.respond(200, {
		"Identifier": AMQP.Helpers.confs["iotJumpWay"]["MQTT"]["Agent"]["identifier"],
		"Host": AMQP.Helpers.confs["iotJumpWay"]["ip"],
		"NorthPort": AMQP.Helpers.confs["iotJumpWay"]["MQTT"]["Agent"]["northPort"],
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
			print("Device Command")
		elif command["ToType"] is "Application":
			print("Application Command")
		else:
			return AMQP.respond(400, {
				"Response": "Failed",
				"Error": "BadRequest",
				"Description": "Command type not supported!"
			})
	else:
		return AMQP.respond(405, {
			"Response": "Failed",
			"Error": "MethodNotAlowed",
			"Description": "Method not allowed!"
		})

def main():

	signal.signal(signal.SIGINT, AMQP.signal_handler)
	signal.signal(signal.SIGTERM, AMQP.signal_handler)

	# Starts the IoT Agent
	AMQP.contextConn()
	AMQP.mongoDbConn()
	AMQP.blockchainConnect()
	AMQP.amqpConnect()
	AMQP.amqpConsumeSet()
	AMQP.agentThreads()

	app.run(host=AMQP.Helpers.confs["iotJumpWay"]["ip"],
			port=AMQP.Helpers.confs["iotJumpWay"]["AMQP"]["northPort"])

if __name__ == "__main__":
	main()
