######################################################################################################
#
# Organization:  Asociacion De Investigacion En Inteligencia Artificial Para La Leucemia Peter Moss
# Repository:    HIAS: Hospital Intelligent Automation System
# Module:        MongoDB
#
# Author:        Adam Milton-Barker (AdamMiltonBarker.com)
#
# Title:         MongoDB Class
# Description:   MongoDB functions for the Hospital Intelligent Automation System.
# License:       MIT License
# Last Modified: 2020-09-20
#
######################################################################################################

import sys

from pymongo import MongoClient

from Classes.Helpers import Helpers


class MongoDB():
	""" MongoDB Class

	MongoDB functions for the Hospital Intelligent Automation System.
	"""

	def __init__(self):
		""" Initializes the class. """

		self.Helpers = Helpers("MongoDB")
		self.Helpers.logger.info("MongoDB Class initialization complete.")

	def startMongoDB(self):
		""" Connects to MongoDB database. """

		connection = MongoClient(self.Helpers.confs["iotJumpWay"]["databases"]["mongo"]["ip"])
		self.mongoConn = connection[self.Helpers.confs["iotJumpWay"]["databases"]["mongo"]["db"]]
		self.mongoConn.authenticate(self.Helpers.confs["iotJumpWay"]["databases"]["mongo"]["dbu"],
									self.Helpers.confs["iotJumpWay"]["databases"]["mongo"]["dbp"])
		self.agentsCollection = self.mongoConn.Agents
		self.locationsCollection = self.mongoConn.Locations
		self.zonesCollection = self.mongoConn.Zones
		self.devicesCollection = self.mongoConn.Devices
		self.applicationsCollection = self.mongoConn.Applications
		self.usersCollection = self.mongoConn.Users
		self.patientsCollection = self.mongoConn.Patients
		self.staffCollection = self.mongoConn.Staff
		self.thingsCollection = self.mongoConn.Things
		self.modelsCollection = self.mongoConn.Models
		self.Helpers.logger.info("Mongo connection started")

	def getData(self, collection, limit, category=None,  values=None):
		""" Connects to MongoDB database. """

		query = {}

		if category is not None:
			query = {"category.value.0": category}

		if values is not None:
			valuesArr = values.split(",")
			for value in valuesArr:
				pair = value.split("|")
				query.update({pair[0]: pair[1]})

		try:
			entities = list(collection.find(query, {'_id': False}).limit(limit))
			self.Helpers.logger.info("Mongo data found OK")
			return entities
		except:
			e = sys.exc_info()
			self.Helpers.logger.info("Mongo data find FAILED!")
			self.Helpers.logger.info(str(e))
			return False

	def getDataById(self, collection, _id, attrs):
		""" Connects to MongoDB database. """

		fields = {'_id': False}
		if attrs is not None:
			attribs = attrs.split(",")
			for attr in attribs:
				fields.update({attr: True})

		try:
			entity = list(collection.find({'id': _id}, fields))
			self.Helpers.logger.info("Mongo data found OK")
			return entity
		except:
			e = sys.exc_info()
			self.Helpers.logger.info("Mongo data find FAILED!")
			self.Helpers.logger.info(str(e))
			return False

	def insertData(self, collection, doc, typeof):
		""" Connects to MongoDB database. """

		try:
			_id = collection.insert(doc)
			self.Helpers.logger.info("Mongo data inserted OK")
			if typeof is "Device":
				self.locationsCollection.find_one_and_update(
						{"id": doc.lid.entity},
						{'$inc': {'devices.value': 1}}
					)
				self.zonesCollection.find_one_and_update(
						{"id": doc.zid.entity},
						{'$inc': {'devices.value': 1}}
					)
			if typeof is "Application":
				self.locationsCollection.find_one_and_update(
						{"id": doc.lid.entity},
						{'$inc': {'applications.value': 1}}
					)
				self.Helpers.logger.info("Mongo data update OK")
			return _id
		except:
			e = sys.exc_info()
			self.Helpers.logger.info("Mongo data inserted FAILED!")
			self.Helpers.logger.info(str(e))
			return False

	def updateData(self, _id, collection, doc):
		""" Connects to MongoDB database. """

		try:
			collection.update_one({"id" : _id}, {"$set": doc});
			self.Helpers.logger.info("Mongo data update OK")
			return True
		except:
			e = sys.exc_info()
			self.Helpers.logger.info("Mongo data update FAILED!")
			self.Helpers.logger.info(str(e))
			return False

	def deleteData(self, _id, collection):
		""" Connects to MongoDB database. """

		try:
			collection.delete_one({"id": _id});
			self.Helpers.logger.info("Mongo data update OK")
			return True
		except:
			e = sys.exc_info()
			self.Helpers.logger.info("Mongo data update FAILED!")
			self.Helpers.logger.info(str(e))
			return False
