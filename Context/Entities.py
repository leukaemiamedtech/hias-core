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


class Entities():
	""" Entities Class

	Handles Entity functionality for the HIAS HDSI Context Broker.
	"""

	def __init__(self, mongoConnection):
		""" Initializes the class. """

		self.Helpers = Helpers("Entities")
		self.MongoDB = mongoConnection
		self.Helpers.logger.info("Entities Class initialization complete.")

	def createEntity(self, data):
		""" Creates a new NDSI Entity """

		if data["type"] == "Location":
			collection = self.MongoDB.locationsCollection
		elif data["type"] == "Zone":
			collection = self.MongoDB.zonesCollection
		elif data["type"] == "Device":
			collection = self.MongoDB.devicesCollection
		elif data["type"] == "Application":
			collection = self.MongoDB.applicationsCollection
		elif data["type"] == "Patient":
			collection = self.MongoDB.patientsCollection
		elif data["type"] == "Staff":
			collection = self.MongoDB.staffCollection
		elif data["type"] == "Thing":
			collection = self.MongoDB.thingsCollection
		elif data["type"] == "Model":
			collection = self.MongoDB.modelsCollection
		else:
			return self.respond(400, {
				"Response": "Failed",
				"Error": "BadRequest",
				"Description": "Entity type not supported!"
			})

		_id = self.MongoDB.insertData(collection, data, data["type"])

		resp = {
			"Response": "OK",
			"ID": str(_id),
			"Entity": json.loads(json_util.dumps(data))
		}

		if str(_id) is not False:
			return self.respond(201, resp, "v1/entities/" + data["id"] + "?type=" + data["type"])
		else:
			return self.respond(400, {
				"Response": "Failed",
				"Error": "BadRequest",
				"Description": "Entity storage failed!"
			})

	def getEntities(self, typeof, limit=0, category=None, values=None):
		""" Gets all NDSI Entities """

		if typeof == "Location":
			collection = self.MongoDB.locationsCollection
		elif typeof == "Zone":
			collection = self.MongoDB.zonesCollection
		elif typeof == "Device":
			collection = self.MongoDB.devicesCollection
		elif typeof == "Application":
			collection = self.MongoDB.applicationsCollection
		elif typeof == "Patient":
			collection = self.MongoDB.patientsCollection
		elif typeof == "Staff":
			collection = self.MongoDB.staffCollection
		elif typeof == "Thing":
			collection = self.MongoDB.thingsCollection
		elif typeof == "Model":
			collection = self.MongoDB.modelsCollection
		else:
			return self.respond(400, {
				"Response": "Failed",
				"Error": "BadRequest",
				"Description": "Entity type not supported!"
			})

		entities = self.MongoDB.getData(collection, limit, category, values)

		if not entities:
			return self.respond(404, {
				"Response": "Failed",
				"Error": "NotFound",
				"Description": "No entities exist for this type!"
			})
		else:
			resp = {
				"Response": "OK",
				"Data": json.loads(json_util.dumps(entities))
			}
			return self.respond(200, resp)

	def getEntity(self, typeof, _id, attrs):
		""" Gets a specific NDSI Entity """

		if typeof == "Location":
			collection = self.MongoDB.locationsCollection
		elif typeof == "Zone":
			collection = self.MongoDB.zonesCollection
		elif typeof == "Device":
			collection = self.MongoDB.devicesCollection
		elif typeof == "Application":
			collection = self.MongoDB.applicationsCollection
		elif typeof == "Patient":
			collection = self.MongoDB.patientsCollection
		elif typeof == "Staff":
			collection = self.MongoDB.staffCollection
		elif typeof == "Thing":
			collection = self.MongoDB.thingsCollection
		elif typeof == "Model":
			collection = self.MongoDB.modelsCollection
		else:
			return self.respond(400, {
				"Response": "Failed",
				"Error": "BadRequest",
				"Description": "Entity type not supported!"
			})

		entity = self.MongoDB.getDataById(collection, _id, attrs)

		if not entity:
			return self.respond(404, {
				"Response": "Failed",
				"Error": "NotFound",
				"Description": "Entity does not exist!"
			})
		else:
			resp = {
				"Response": "OK",
				"Data": json.loads(json_util.dumps(entity[0]))
			}
			return self.respond(200, resp)

	def updateEntity(self, _id, typeof, data):
		""" Updates an NDSI Entity """

		if typeof == "Location":
			collection = self.MongoDB.locationsCollection
		elif typeof == "Zone":
			collection = self.MongoDB.zonesCollection
		elif typeof == "Device":
			collection = self.MongoDB.devicesCollection
		elif typeof == "Application":
			collection = self.MongoDB.applicationsCollection
		elif typeof == "Patient":
			collection = self.MongoDB.patientsCollection
		elif typeof == "Staff":
			collection = self.MongoDB.staffCollection
		elif typeof == "Thing":
			collection = self.MongoDB.thingsCollection
		elif typeof == "Model":
			collection = self.MongoDB.modelsCollection
		else:
			return self.respond(400, {
				"Response": "Failed",
				"Error": "BadRequest",
				"Description": "Entity type not supported!"
			})

		updated = self.MongoDB.updateData(_id, collection, data)

		if updated is True:
			return self.respond(200, {
				"Response": "OK"
			})
		else:
			return self.respond(400, {
				"Response": "OK",
				"Error": "BadRequest",
				"Description": "Entity update failed!"
			})

	def deleteEntity(self, typeof, _id):
		""" Deletes an NDSI Entity """

		if typeof == "Location":
			collection = self.MongoDB.locationsCollection
		elif typeof == "Zone":
			collection = self.MongoDB.zonesCollection
		elif typeof == "Device":
			collection = self.MongoDB.devicesCollection
		elif typeof == "Application":
			collection = self.MongoDB.applicationsCollection
		elif typeof == "Patient":
			collection = self.MongoDB.patientsCollection
		elif typeof == "Staff":
			collection = self.MongoDB.staffCollection
		elif typeof == "Thing":
			collection = self.MongoDB.thingsCollection
		elif typeof == "Model":
			collection = self.MongoDB.modelsCollection
		else:
			return self.respond(400, {
				"Response": "Failed",
				"Error": "BadRequest",
				"Description": "Entity type not supported!"
			})

		updated = self.MongoDB.deleteData(_id, collection)

		if updated is True:
			return self.respond(200, {
				"Response": "OK"
			})
		else:
			return self.respond(400, {
				"Response": "OK",
				"Error": "BadRequest",
				"Description": "Entity update failed!"
			})

	def respond(self, responseCode, response, location=None):
		""" Builds the request repsonse """

		return Response(response=json.dumps(response, indent=4), status=responseCode,
						mimetype="application/json")
