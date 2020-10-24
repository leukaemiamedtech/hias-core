#!/usr/bin/env python3
######################################################################################################
#
# Organization:  Peter Moss Leukemia AI Research
# Repository:    HIAS: Hospital Intelligent Automation System
#
# Author:        Adam Milton-Barker (AdamMiltonBarker.com)
#
# Title:         HIAS Context Broker Helpers
# Description:   Helper functions that allow the HIAS iotAgents to communicate with the Context
#                Broker.
# License:       MIT License
# Last Modified: 2020-10-18
#
######################################################################################################

import json
import requests

from Classes.Helpers import Helpers


class ContextBroker():
	""" HIAS Context Broker Helpers

	Helper functions that allow the HIAS iotAgents
	to communicate with the Context Broker.
	"""

	def __init__(self):
		""" Initializes the class. """

		self.Helpers = Helpers("ContextBroker")
		self.headers = {"content-type": 'application/json'}
		self.Helpers.logger.info("Context Broker initialization complete.")

	def getRequiredAttributes(self, _id, typeof):
		""" Gets required attributes. """

		if typeof is "Application":
			params = "&attrs = blockchain.address, lid.value, lid.entity"
		else:
			params = "&attrs = blockchain.address, lid.value, lid.entity, zid.entity"

		apiUrl = "https://" + self.Helpers.confs["iotJumpWay"]["host"] + "/" +  self.Helpers.confs["iotJumpWay"]["ContextBroker"]["address"] + "/entities/" + _id + "?type=" + typeof + "&attrs=blockchain.address,lid.value,lid.entity"

		response = requests.get(apiUrl, headers=self.headers, auth=(
			self.Helpers.confs["iotJumpWay"]["identifier"], self.Helpers.confs["iotJumpWay"]["auth"]))

		return json.loads(response.text)

	def getNFC(self, nfc):
		""" Gets required attributes. """

		apiUrl = "https://" + self.Helpers.confs["iotJumpWay"]["host"] + "/" + \
					self.Helpers.confs["iotJumpWay"]["ContextBroker"]["address"] + \
					"/entities?type=Staff&values=nfc.value|nfc"

		response = requests.get(apiUrl, headers=self.headers, auth=(
			self.Helpers.confs["iotJumpWay"]["identifier"], self.Helpers.confs["iotJumpWay"]["auth"]))

		return json.loads(response.text)

	def getNLU(self, zone):
		""" Gets required attributes. """

		apiUrl = "https://" + self.Helpers.confs["iotJumpWay"]["host"] + "/" + \
					self.Helpers.confs["iotJumpWay"]["ContextBroker"]["address"] + \
					"/entities?type=Device&category.value=GeniSysAI&values=zid.value|" + zone + ",status|ONLINE"

		response = requests.get(apiUrl, headers=self.headers, auth=(
			self.Helpers.confs["iotJumpWay"]["identifier"], self.Helpers.confs["iotJumpWay"]["auth"]))

		return json.loads(response.text)

	def updateEntity(self, _id, typeof, data):
		""" Updates an entity. """

		apiUrl = "https://" + self.Helpers.confs["iotJumpWay"]["host"] + "/" + \
			self.Helpers.confs["iotJumpWay"]["ContextBroker"]["address"] + \
			"/entities/" + _id + "/attrs?type=" + typeof

		response = requests.patch(apiUrl, data=json.dumps(data), headers=self.headers, auth=(
			self.Helpers.confs["iotJumpWay"]["identifier"], self.Helpers.confs["iotJumpWay"]["auth"]))

		return json.loads(response.text)
