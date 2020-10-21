#!/usr/bin/env python3
######################################################################################################
#
# Organization:  Peter Moss Leukemia AI Research
# Repository:    HIAS: Hospital Intelligent Automation System
#
# Author:        Adam Milton-Barker (AdamMiltonBarker.com)
#
# Title:         HIAS HDSI Context Broker
# Description:   The HIAS HDSI Context Broker handles contextual data for all iotJumpWay
#                devices/applications and IoT Agents. The Context Broker uses the HDSI (HIAS
#                Data Services Interface) API V1, based on Open Mobile Alliance's NGSI V2.
# License:       MIT License
# Last Modified: 2020-10-18
#
######################################################################################################

import json
import jsonpickle
import psutil
import requests
import os
import signal
import sys
import threading

sys.path.insert(0, os.path.abspath(
	os.path.join(os.path.dirname(__file__), '..')))

from bson import json_util, ObjectId
from flask import Flask, request, Response
from threading import Thread

from Classes.Helpers import Helpers
from Classes.MongoDB import MongoDB
from Classes.MQTT import Application

from Agents import Agents
from Entities import Entities

class ContextBroker():
	""" HIAS HDSI Context Broker

	The HIAS HDSI Context Broker handles contextual data
	for all iotJumpWay devices/applications and IoT Agents.
	"""

	def __init__(self):
		""" Initializes the class. """

		self.Helpers = Helpers("ContextBroker")
		self.Helpers.logger.info(
			"HIAS iotJumpWay Context Broker initialization complete.")

	def iotConnection(self):
		""" Initiates the iotJumpWay connection. """

		self.Application = Application({
			"host": self.Helpers.confs["iotJumpWay"]["host"],
			"port": self.Helpers.confs["iotJumpWay"]["ContextBroker"]["iport"],
			"lid": self.Helpers.confs["iotJumpWay"]["ContextBroker"]["lid"],
			"aid": self.Helpers.confs["iotJumpWay"]["ContextBroker"]["aid"],
			"an": self.Helpers.confs["iotJumpWay"]["ContextBroker"]["an"],
			"un": self.Helpers.confs["iotJumpWay"]["ContextBroker"]["un"],
			"pw": self.Helpers.confs["iotJumpWay"]["ContextBroker"]["pw"]
		})
		self.Application.connect()

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

		# Send iotJumpWay notification
		self.Application.appChannelPub("Life", self.Helpers.confs["iotJumpWay"]["MQTT"]["Agent"]["aid"], {
			"CPU": str(cpu),
			"Memory": str(mem),
			"Diskspace": str(hdd),
			"Temperature": str(tmp),
			"Latitude": float(location[0]),
			"Longitude": float(location[1])
		})

		self.Helpers.logger.info("Broker life statistics published.")
		threading.Timer(300.0, self.life).start()

	def mongoDbConnection(self):
		""" Initiates the MongoDB connection class. """

		self.MongoDB = MongoDB()
		self.MongoDB.startMongoDB()

	def configureBroker(self):
		""" Configures the Context Broker. """

		self.Entities = Entities(self.MongoDB)
		self.Agents = Agents(self.MongoDB)

	def getBroker(self):

		return {
			"Broker": {
				"Version": self.Helpers.confs["iotJumpWay"]["ContextBroker"]["version"],
				"Host": self.Helpers.confs["iotJumpWay"]["host"],
				"IP": self.Helpers.confs["iotJumpWay"]["ip"],
				"Port": self.Helpers.confs["iotJumpWay"]["ContextBroker"]["port"],
				"Endpoint": self.Helpers.confs["iotJumpWay"]["ContextBroker"]["address"],
				"Locations": self.MongoDB.locationsCollection.count_documents({"type": "Location"}),
				"Zones": self.MongoDB.locationsCollection.count_documents({"type": "Zone"}),
			},
			"Entities": {
				"Applications": {
					"Count": self.MongoDB.applicationsCollection.count_documents({"type": "Application"}),
					"IotAgents": self.MongoDB.applicationsCollection.count_documents({"type": "Application", "category.value.0": "IoT Agent"}),
					"AiAgents": self.MongoDB.applicationsCollection.count_documents({"type": "Application", "category.value.0": "AI Agent"}),
					"Staff": {
						"Administration": self.MongoDB.applicationsCollection.count_documents({"type": "Application", "category.value.0": "Management"}),
						"Director": self.MongoDB.applicationsCollection.count_documents({"type": "Application", "category.value.0": "Director"}),
						"Developer": self.MongoDB.applicationsCollection.count_documents({"type": "Application", "category.value.0": "Developer"}),
						"Doctor": self.MongoDB.applicationsCollection.count_documents({"type": "Application", "category.value.0": "Doctor"}),
						"Management": self.MongoDB.applicationsCollection.count_documents({"type": "Application", "category.value.0": "Management"}),
						"Network Security": self.MongoDB.applicationsCollection.count_documents({"type": "Application", "category.value.0": "Network Security"}),
						"Nurse": self.MongoDB.applicationsCollection.count_documents({"type": "Application", "category.value.0": "Nurse"}),
						"Security": self.MongoDB.applicationsCollection.count_documents({"type": "Application", "category.value.0": "Security"}),
						"Supervisor": self.MongoDB.applicationsCollection.count_documents({"type": "Application", "category.value.0": "Supervisor"}),
						"Cancelled": {
							"Administration": self.MongoDB.applicationsCollection.count_documents({"type": "Application", "category.value.0": "Management", "cancelled.value": "1"}),
							"Director": self.MongoDB.applicationsCollection.count_documents({"type": "Application", "category.value.0": "Director", "cancelled.value": "1"}),
							"Developer": self.MongoDB.applicationsCollection.count_documents({"type": "Application", "category.value.0": "Developer", "cancelled.value": "1"}),
							"Doctor": self.MongoDB.applicationsCollection.count_documents({"type": "Application", "category.value.0": "Doctor", "cancelled.value": "1"}),
							"Management": self.MongoDB.applicationsCollection.count_documents({"type": "Application", "category.value.0": "Management", "cancelled.value": "1"}),
							"Network Security": self.MongoDB.applicationsCollection.count_documents({"type": "Application", "category.value.0": "Network Security", "cancelled.value": "1"}),
							"Nurse": self.MongoDB.applicationsCollection.count_documents({"type": "Application", "category.value.0": "Nurse", "cancelled.value": "1"}),
							"Security": self.MongoDB.applicationsCollection.count_documents({"type": "Application", "category.value.0": "Security", "cancelled.value": "1"}),
							"Supervisor": self.MongoDB.applicationsCollection.count_documents({"type": "Application", "category.value.0": "Supervisor", "cancelled.value": "1"})
						}
					}
				},
				"Devices": {
					"Count": self.MongoDB.devicesCollection.count_documents({}),
					"Server": self.MongoDB.devicesCollection.count_documents({"type": "Device", "category.value.0": "Server"}),
					"Camera": self.MongoDB.devicesCollection.count_documents({"type": "Device", "category.value.0": "Camera"}),
					"Scanner": self.MongoDB.devicesCollection.count_documents({"type": "Device", "category.value.0": "Scanner"}),
					"Virtual Reality": self.MongoDB.devicesCollection.count_documents({"type": "Device", "category.value.0": "Virtual Reality"}),
					"Mixed Reality": self.MongoDB.devicesCollection.count_documents({"type": "Device", "category.value.0": "Mixed Reality"}),
					"Robotics": {
						"EMAR": self.MongoDB.devicesCollection.count_documents({"type": "Device", "category.value.0": "EMAR"})
					},
					"AI": {
						"GeniSysAI": self.MongoDB.devicesCollection.count_documents({"type": "Device", "category.value.0": "GeniSysAI"}),
						"TassAI": self.MongoDB.devicesCollection.count_documents({"type": "Device", "category.value.0": "TassAI"}),
						"AML": self.MongoDB.devicesCollection.count_documents({"type": "Device", "category.value.0": "AMLClassifier"}),
						"ALL": self.MongoDB.devicesCollection.count_documents({"type": "Device", "category.value.0": "ALLClassifier"}),
						"COVID": self.MongoDB.devicesCollection.count_documents({"type": "Device", "category.value.0": "COVIDClassifier"}),
						"Skin": self.MongoDB.devicesCollection.count_documents({"type": "Device", "category.value.0": "SkinCancerClassifier"}),
					}
				}
			}
		}

	def respond(self, responseCode, response, location=None):
		""" Builds the request repsonse """

		return Response(response=json.dumps(response, indent=4), status=responseCode,
						mimetype="application/json")

	def signal_handler(self, signal, frame):
		self.Helpers.logger.info("Disconnecting")
		sys.exit(1)


app = Flask(__name__)
ContextBroker = ContextBroker()

@app.route('/about', methods=['GET'])
def about():
	""" Responds to GET requests sent to the /v1/about API endpoint. """

	return ContextBroker.respond(200, {
		"Response": "OK",
		"Data": ContextBroker.getBroker()
	})

@app.route('/entities', methods=['POST'])
def entitiesPost():
	""" Responds to POST requests sent to the /v1/entities API endpoint. """

	if request.headers["Content-Type"] == "application/json":
		query = request.json
		if query["id"] is None:
			return ContextBroker.Entities.respond(400, {
				"Response": "Failed",
				"Error": "BadRequest",
				"Description": "Entity ID required!"
			})
		return ContextBroker.Entities.createEntity(query)
	else:
		return ContextBroker.Entities.respond(405, {
			"Response": "Failed",
			"Error": "MethodNotAlowed",
			"Description": "Method not allowed!"
		})

@app.route('/entities', methods=['GET'])
def entitiesGet():
	""" Responds to GET requests sent to the /v1/entities API endpoint. """

	if request.args.get('type') is None:
		return ContextBroker.Entities.respond(400, {
			"Response": "Failed",
			"Error": "BadRequest",
			"Description": "Entity type required!"
		})
	if request.args.get('limit') is None:
		limit = 0;
	else:
		limit = int(request.args.get('limit'))
	if request.args.get('values') is None:
		values = None
	else:
		values = request.args.get('values')
	return ContextBroker.Entities.getEntities(request.args.get('type'),
												limit, request.args.get('category'), values)

@app.route('/entities/<_id>', methods=['GET'])
def entityGet(_id):
	""" Responds to GET requests sent to the /v1/entities/<_id> API endpoint. """

	if request.args.get('type') is None:
		return ContextBroker.Entities.respond(400, {
			"Response": "Failed",
			"Error": "BadRequest",
			"Description": "Entity type required!"
		})
	if request.args.get('attrs') is None:
		attrs = None
	else:
		attrs = request.args.get('attrs')
	return ContextBroker.Entities.getEntity(request.args.get('type'), _id, attrs)

@app.route('/entities/<_id>/attrs', methods=['PATCH'])
def entitiesUpdate(_id):
	""" Responds to PATCH requests sent to the /v1/entities/<_id>/attrs API endpoint. """

	if request.headers["Content-Type"] == "application/json":
		query = request.json
		if request.args.get('type') is None:
			return ContextBroker.Entities.respond(400, {
				"Response": "Failed",
				"Error": "BadRequest",
				"Description": "Entity ID required!"
			})
		return ContextBroker.Entities.updateEntity(_id, request.args.get('type'), query)
	else:
		return ContextBroker.Entities.respond(405, {
			"Response": "Failed",
			"Error": "MethodNotAlowed",
			"Description": "Method not allowed!"
		})

@app.route('/entities/<_id>', methods=['DELETE'])
def entityDelete(_id):
	""" Responds to DELETE requests sent to the /v1/entities/<_id> API endpoint. """

	if _id is None:
		return ContextBroker.Entities.respond(400, {
			"Response": "Failed",
			"Error": "BadRequest",
			"Description": "Entity ID required!"
		})
	if request.args.get('type') is None:
		return ContextBroker.Entities.respond(400, {
			"Response": "Failed",
			"Error": "BadRequest",
			"Description": "Entity type required!"
		})
	return ContextBroker.Entities.deleteEntity(request.args.get('type'), _id)

@app.route('/agents', methods=['POST'])
def agentsPost():
	""" Responds to POST requests sent to the /v1/agents API endpoint. """

	if request.headers["Content-Type"] == "application/json":
		query = request.json
		if query["id"] is None:
			return ContextBroker.Agents.respond(400, {
				"Response": "Failed",
				"Error": "BadRequest",
				"Description": "Agent ID required!"
			})
		return ContextBroker.Agents.createAgent(query)
	else:
		return ContextBroker.Agents.respond(405, {
			"Response": "Failed",
			"Error": "MethodNotAlowed",
			"Description": "Method not allowed!"
		})

@app.route('/agents', methods=['GET'])
def agentsGet():
	""" Responds to GET requests sent to the /v1/agents API endpoint. """

	if request.args.get('limit') is None:
		limit = 0;
	else:
		limit = int(request.args.get('limit'))
	return ContextBroker.Agents.getAgents(limit)

@app.route('/agents/<_id>', methods=['GET'])
def agentGet(_id):
	""" Responds to GET requests sent to the /v1/agents/<_id> API endpoint. """

	if request.args.get('attrs') is None:
		attrs = None
	else:
		attrs = request.args.get('attrs')
	return ContextBroker.Agents.getAgent(_id, attrs)

@app.route('/agents/<_id>/attrs', methods=['PATCH'])
def agentUpdate(_id):
	""" Responds to PATCH requests sent to the /v1/agents/<_id>/attrs API endpoint. """

	if request.headers["Content-Type"] == "application/json":
		data = request.json
		return ContextBroker.Agents.updateAgent(_id, data)
	else:
		return ContextBroker.Agents.respond(405, {
			"Response": "Failed",
			"Error": "MethodNotAlowed",
			"Description": "Method not allowed!"
		})

@app.route('/agents/<_id>', methods=['DELETE'])
def agentDelete(_id):
	""" Responds to DELETE requests sent to the /v1/agents/<_id> API endpoint. """

	if _id is None:
		return ContextBroker.Agents.respond(400, {
			"Response": "Failed",
			"Error": "BadRequest",
			"Description": "Agent ID required!"
		})
	return ContextBroker.Agents.deleteAgent(_id)

def main():
	signal.signal(signal.SIGINT, ContextBroker.signal_handler)
	signal.signal(signal.SIGTERM, ContextBroker.signal_handler)

	ContextBroker.mongoDbConnection()
	ContextBroker.iotConnection()
	ContextBroker.configureBroker()

	Thread(target=ContextBroker.life, args=(), daemon=True).start()

	app.run(host=ContextBroker.Helpers.confs["iotJumpWay"]["ip"],
			port=ContextBroker.Helpers.confs["iotJumpWay"]["ContextBroker"]["port"])

if __name__ == "__main__":
	main()
