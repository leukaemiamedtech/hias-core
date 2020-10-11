######################################################################################################
#
# Organization:  Peter Moss Leukemia AI Research
# Repository:    HIAS: Hospital Intelligent Automation System
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

		connection = MongoClient(self.Helpers.confs["iotJumpWay"]["ip"])
		self.mongoConn = connection[self.Helpers.confs["iotJumpWay"]["mdb"]]
		self.mongoConn.authenticate(self.Helpers.confs["iotJumpWay"]["mdbu"],
                              self.Helpers.confs["iotJumpWay"]["mdbp"])
		self.Helpers.logger.info("Mongo connection started")

	def insertData(self, collection, doc):
		""" Connects to MongoDB database. """

		try:
			_id = collection.insert(doc)
			self.Helpers.logger.info("Mongo data inserted OK")
			return _id
		except:
			e = sys.exc_info()
			self.Helpers.logger.info("Mongo data inserted FAILED!")
			self.Helpers.logger.info(str(e))
			return False
