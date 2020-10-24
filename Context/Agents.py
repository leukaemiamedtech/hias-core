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
import os
import sys

from bson import json_util, ObjectId
from flask import Response

from Classes.Helpers import Helpers


class Agents():
	""" Agents Class

	Handles IoT Agent functionality for the HIAS HDSI Context Broker.
	"""

	def __init__(self, mongoConnection):
		""" Initializes the class. """

		self.Helpers = Helpers("Agents")
		self.MongoDB = mongoConnection
		self.Helpers.logger.info("Agents Class initialization complete.")

	def createAgent(self, data):
		""" Creates a new NDSI IoT Agent """

		_id = self.MongoDB.insertData(
			self.MongoDB.applicationsCollection, data, data["type"])

		resp = {
			"Response": "OK",
			"ID": str(_id),
			"Agent": json.loads(json_util.dumps(data))
		}

		if str(_id) is not False:
			return self.respond(201, resp, "v1/agents/" + data["id"])
		else:
			return self.respond(400, {
				"Response": "Failed",
				"Error": "BadRequest",
				"Description": "Entity storage failed!"
			})

	def getAgents(self, limit=0):
		""" Gets all NDSI IoT Agents """

		agents = self.MongoDB.getData(self.MongoDB.applicationsCollection, limit, "IoT Agent")

		if not agents:
			return self.respond(404, {
				"Response": "Failed",
				"Error": "NotFound",
				"Description": "No agents exist!"
			})
		else:
			resp = {
				"Response": "OK",
				"Data": json.loads(json_util.dumps(agents))
			}
			return self.respond(200, resp)

	def getAgent(self, _id, attrs):
		""" Gets a specific NDSI IoT Agent """

		agent = self.MongoDB.getDataById(
			self.MongoDB.applicationsCollection, _id, attrs)

		if not agent:
			return self.respond(404, {
				"Response": "Failed",
				"Error": "NotFound",
				"Description": "Agent does not exist!"
			})
		else:
			resp = {
				"Response": "OK",
				"Data": json.loads(json_util.dumps(agent[0]))
			}
			return self.respond(200, resp)

	def updateAgent(self, _id, data):
		""" Updates an NDSI IoT Agent """

		updated = self.MongoDB.updateData(
			_id, self.MongoDB.applicationsCollection, data)

		if updated is True:
			return self.respond(200, {
				"Response": "OK"
			})
		else:
			return self.respond(400, {
				"Response": "OK",
				"Error": "BadRequest",
				"Description": "Agent update failed!"
			})

	def respond(self, responseCode, response, location=None):
		""" Builds the request repsonse """

		return Response(response=json.dumps(response, indent=4), status=responseCode,
                  mimetype="application/json")
