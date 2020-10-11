#!/usr/bin/env python3
######################################################################################################
#
# Organization:  Peter Moss Leukemia AI Research
# Repository:    HIAS: Hospital Intelligent Automation System
#
# Author:        Adam Milton-Barker (AdamMiltonBarker.com)
#
# Title:         Replenish Class
# Description:   The replenish module is used to replenish HIAS Smart Contracts with Ether so that they
#                can function.
# License:       MIT License
# Last Modified: 2020-09-27
#
######################################################################################################

import time

from Classes.Helpers import Helpers
from Classes.Blockchain import Blockchain
from Classes.MySQL import MySQL

class Replenish():
	""" Replenish Class

	The replenish module is used to replenish HIAS Smart Contracts
	with Ether so that they can function.
	"""

	def __init__(self):
		""" Initializes the class. """

		self.Helpers = Helpers("Replenish")

		self.MySQL = MySQL()
		self.MySQL.startMySQL()

		self.Blockchain = Blockchain()
		self.Blockchain.startBlockchain()
		self.Blockchain.w3.geth.personal.unlockAccount(
			self.Helpers.confs["ethereum"]["haddress"], self.Helpers.confs["ethereum"]["hpass"], 0)

		self.Helpers.logger.info("Replenish Class initialization complete.")

Replenish = Replenish()

while True:
	abalance = Replenish.Blockchain.getBalance(Replenish.Blockchain.authContract)
	Replenish.Helpers.logger.info(
		"Auth Contract (" + Replenish.Helpers.confs["ethereum"]["authContract"] + ") has a balance of " + str(abalance) + " HIAS Ether")

	if abalance < Replenish.Blockchain.contractBalance:
		replenishment = Replenish.Blockchain.contractBalance - abalance
		if Replenish.Blockchain.replenish(
				Replenish.Blockchain.authContract, Replenish.Helpers.confs["ethereum"]["authContract"], replenishment):
					Replenish.Helpers.logger.info(
						"Auth Contract (" + Replenish.Helpers.confs["ethereum"]["authContract"] + ") balanced replenished to " + str(Replenish.Blockchain.contractBalance) + " HIAS Ether")

	ibalance = Replenish.Blockchain.getBalance(Replenish.Blockchain.iotContract)
	Replenish.Helpers.logger.info(
		"iotJumpWay Contract (" + Replenish.Helpers.confs["ethereum"]["iotContract"] + ") has a balance of " + str(ibalance) + " HIAS Ether")

	if ibalance < Replenish.Blockchain.contractBalance:
		replenishment = Replenish.Blockchain.contractBalance - ibalance
		if Replenish.Blockchain.replenish(
				Replenish.Blockchain.iotContract, Replenish.Helpers.confs["ethereum"]["iotContract"], replenishment):
					Replenish.Helpers.logger.info(
						"iotJumpWay Contract (" + Replenish.Helpers.confs["ethereum"]["iotContract"] + ") balanced replenished to " + str(Replenish.Blockchain.contractBalance) + " HIAS Ether")

	pbalance = Replenish.Blockchain.getBalance(Replenish.Blockchain.patientsContract)
	Replenish.Helpers.logger.info(
		"Patients Contract (" + Replenish.Helpers.confs["ethereum"]["patientsContract"] + ") has a balance of " + str(pbalance) + " HIAS Ether")

	if pbalance < Replenish.Blockchain.contractBalance:
		replenishment = Replenish.Blockchain.contractBalance - pbalance
		if Replenish.Blockchain.replenish(
				Replenish.Blockchain.patientsContract, Replenish.Helpers.confs["ethereum"]["patientsContract"], replenishment):
					Replenish.Helpers.logger.info(
						"Patients Contract (" + Replenish.Helpers.confs["ethereum"]["patientsContract"] + ") balanced replenished to " + str(Replenish.Blockchain.contractBalance) + " HIAS Ether")

	time.sleep(300)
