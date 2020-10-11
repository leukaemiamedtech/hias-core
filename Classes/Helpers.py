######################################################################################################
#
# Organization:  Peter Moss Leukemia AI Research
# Repository:    HIAS: Hospital Intelligent Automation System
#
# Author:        Adam Milton-Barker (AdamMiltonBarker.com)
# Contributors:
# Title:         Helper Class
# Description:   Helper functions for the Hospital Intelligent Automation System.
# License:       MIT License
# Last Modified: 2020-06-04
#
######################################################################################################

import logging
import logging.handlers as handlers
import json
import os
import sys
import time

from datetime import datetime


class Helpers():
	""" Helper Class

	Helper functions for the Hospital Intelligent Automation System.
	"""

	def __init__(self, ltype, log=True):
		""" Initializes the Helpers Class. """

		# Loads system configs
		self.confs = {}
		self.loadConfs()

		# Sets system logging
		self.logger = logging.getLogger(ltype)
		self.logger.setLevel(logging.INFO)

		formatter = logging.Formatter(
			'%(asctime)s - %(name)s - %(levelname)s - %(message)s')

		allLogHandler = handlers.TimedRotatingFileHandler(
			os.path.dirname(os.path.abspath(__file__)) + '/../Logs/all.log', when='H', interval=1, backupCount=0)
		allLogHandler.setLevel(logging.INFO)
		allLogHandler.setFormatter(formatter)

		errorLogHandler = handlers.TimedRotatingFileHandler(
			os.path.dirname(os.path.abspath(__file__)) + '/../Logs/error.log', when='H', interval=1, backupCount=0)
		errorLogHandler.setLevel(logging.ERROR)
		errorLogHandler.setFormatter(formatter)

		warningLogHandler = handlers.TimedRotatingFileHandler(
			os.path.dirname(os.path.abspath(__file__)) + '/../Logs/warning.log', when='H', interval=1, backupCount=0)
		warningLogHandler.setLevel(logging.WARNING)
		warningLogHandler.setFormatter(formatter)

		consoleHandler = logging.StreamHandler(sys.stdout)
		consoleHandler.setFormatter(formatter)

		self.logger.addHandler(allLogHandler)
		self.logger.addHandler(errorLogHandler)
		self.logger.addHandler(warningLogHandler)
		self.logger.addHandler(consoleHandler)

		if log is True:
			self.logger.info("Helpers class initialization complete.")

	def loadConfs(self):
		""" Load the program configuration. """

		with open(os.path.dirname(os.path.abspath(__file__)) + '/../confs.json') as confs:
			self.confs = json.loads(confs.read())
