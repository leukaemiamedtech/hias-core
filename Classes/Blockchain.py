######################################################################################################
#
# Organization:  Peter Moss Leukemia AI Research
# Repository:    HIAS: Hospital Intelligent Automation System
#
# Author:        Adam Milton-Barker (AdamMiltonBarker.com)
#
# Title:         Blockchain Class
# Description:   Handles communication with the HIAS Blockchain.
# License:       MIT License
# Last Modified: 2020-09-20
#
######################################################################################################

import bcrypt
import json
import sys
import time

from requests.auth import HTTPBasicAuth
from web3 import Web3

from Classes.Helpers import Helpers
from Classes.MySQL import MySQL


class Blockchain():
	""" Blockchain Class

	Handles communication with the HIAS Blockchain.
	"""

	def __init__(self):
		""" Initializes the class. """

		self.Helpers = Helpers("Blockchain")

		self.contractBalance = 5000

		self.Helpers.logger.info("Blockchain Class initialization complete.")

	def startBlockchain(self):
		""" Connects to MySQL database. """

		self.w3 = Web3(Web3.HTTPProvider(self.Helpers.confs["ethereum"]["bchost"], request_kwargs={
						'auth': HTTPBasicAuth(self.Helpers.confs["ethereum"]["user"], self.Helpers.confs["ethereum"]["pass"])}))

		self.authContract = self.w3.eth.contract(self.w3.toChecksumAddress(
			self.Helpers.confs["ethereum"]["authContract"]), abi=json.dumps(self.Helpers.confs["ethereum"]["authAbi"]))
		self.iotContract = self.w3.eth.contract(self.w3.toChecksumAddress(
			self.Helpers.confs["ethereum"]["iotContract"]), abi=json.dumps(self.Helpers.confs["ethereum"]["iotAbi"]))
		self.patientsContract = self.w3.eth.contract(self.w3.toChecksumAddress(
			self.Helpers.confs["ethereum"]["patientsContract"]), abi=json.dumps(self.Helpers.confs["ethereum"]["patientsAbi"]))
		self.Helpers.logger.info("Blockchain connections started")

	def hiasAccessCheck(self, typeof, identifier):
		""" Checks sender is allowed access via HIAS Smart Contract """

		if not self.authContract.functions.identifierAllowed(typeof, identifier).call({'from': self.w3.toChecksumAddress(self.Helpers.confs["ethereum"]["iaddress"])}):
			return False
		else:
			return True

	def iotJumpWayAccessCheck(self, address):
		""" Checks sender is allowed access to the iotJumpWay Smart Contract """

		if not self.iotContract.functions.accessAllowed(self.w3.toChecksumAddress(address)).call({'from': self.w3.toChecksumAddress(self.Helpers.confs["ethereum"]["iaddress"])}):
			return False
		else:
			return True

	def getBalance(self, contract):
		""" Gets smart contract balance """

		try:
			balance = contract.functions.getBalance().call({"from": self.w3.toChecksumAddress(self.Helpers.confs["ethereum"]["haddress"])})
			balance = self.w3.fromWei(balance, "ether")
			self.Helpers.logger.info("Get Balance OK!")
			return balance
		except:
			e = sys.exc_info()
			self.Helpers.logger.info("Get Balance Failed!")
			self.Helpers.logger.info(str(e))
			return False

	def hashCommand(self, data):
		""" Hashes Command data for data integrity. """

		hasher = str(data["From"]) + str(data["Type"]) + \
					str(data["Value"]) + str(data["Message"])

		return bcrypt.hashpw(hasher.encode(), bcrypt.gensalt())

	def hashNfc(self, data):
		""" Hashes the NFC UID for data integrity. """

		hasher = str(data["Sensor"]) + str(data["Value"]) + str(data["Message"])

		return bcrypt.hashpw(hasher.encode(), bcrypt.gensalt())

	def hashStatus(self, hasher):
		""" Hashes the status for data integrity. """

		return bcrypt.hashpw(hasher.encode(), bcrypt.gensalt())

	def hashLifeData(self, data):
		""" Hashes the data for data integrity. """

		hasher = str(data["CPU"]) + str(data["Memory"]) + str(data["Diskspace"]) + \
					str(data["Temperature"]) + \
					str(data["Latitude"]) + str(data["Longitude"])

		return bcrypt.hashpw(hasher.encode(), bcrypt.gensalt())

	def hashSensorData(self, data):
		""" Hashes the data for data integrity. """

		hasher = str(data["Sensor"]) + str(data["Type"]) + str(data["Value"]) + str(data["Message"])

		return bcrypt.hashpw(hasher.encode(), bcrypt.gensalt())

	def replenish(self, contract, to, replenish):
		""" Replenishes the iotJumpWay smart contract """

		try:
			tx_hash = contract.functions.deposit(self.w3.toWei(replenish, "ether")).transact({
													"to": self.w3.toChecksumAddress(to),
													"from": self.w3.toChecksumAddress(self.Helpers.confs["ethereum"]["haddress"]),
													"gas": 1000000,
													"value": self.w3.toWei(replenish, "ether")})
			self.Helpers.logger.info("HIAS Blockchain Replenish Transaction OK! ")
			self.Helpers.logger.info(tx_hash)
			tx_receipt = self.w3.eth.waitForTransactionReceipt(tx_hash)
			self.Helpers.logger.info("HIAS Blockchain Replenish OK!")
			self.Helpers.logger.info(str(tx_receipt))
			return True
		except:
			e = sys.exc_info()
			self.Helpers.logger.info("HIAS Blockchain Replenish Failed! ")
			self.Helpers.logger.info(str(e))
			return False

	def storeHash(self, dbid, hashed, at, inserter, identifier, to, typeof):
		""" Stores data hash in the iotJumpWay smart contract """

		try:
			txh = self.iotContract.functions.registerHash(dbid, hashed, at, int(inserter), identifier, self.w3.toChecksumAddress(to)).transact({
															"from": self.w3.toChecksumAddress(self.Helpers.confs["ethereum"]["iaddress"]),
															"gas": 1000000})
			self.Helpers.logger.info("HIAS Blockchain Data Transaction OK!")
			self.Helpers.logger.info(txh)
			txr = self.w3.eth.waitForTransactionReceipt(txh)
			self.Helpers.logger.info("HIAS Blockchain Data Hash OK!")
			self.Helpers.logger.info(str(txr))
		except:
			e = sys.exc_info()
			self.Helpers.logger.info("HIAS Blockchain Data Hash Failed!")
			self.Helpers.logger.info(str(e))
			self.Helpers.logger.info(str(e))

