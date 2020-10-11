######################################################################################################
#
# Organization:  Peter Moss Leukemia AI Research
# Repository:    HIAS: Hospital Intelligent Automation System
#
# Author:        Adam Milton-Barker (AdamMiltonBarker.com)
#
# Title:         MySQL Class
# Description:   MySQL functions for the Hospital Intelligent Automation System.
# License:       MIT License
# Last Modified: 2020-09-20
#
######################################################################################################

import bcrypt
import MySQLdb
import sys
import time

from datetime import datetime
from datetime import timedelta

from Classes.Helpers import Helpers


class MySQL():
	""" MySQL Class

	MySQL functions for the Hospital Intelligent Automation System.
	"""

	def __init__(self):
		""" Initializes the class. """

		self.Helpers = Helpers("MySQL")
		self.Helpers.logger.info("MySQL Class initialization complete.")

	def startMySQL(self):
		""" Connects to MySQL database. """

		self.mysqlConn = MySQLdb.connect(host=self.Helpers.confs["iotJumpWay"]["ip"],
                                   user=self.Helpers.confs["iotJumpWay"]["dbuser"],
                                   passwd=self.Helpers.confs["iotJumpWay"]["dbpass"],
                                   db=self.Helpers.confs["iotJumpWay"]["dbname"])
		self.Helpers.logger.info("MySQL connection started")

	def getApplication(self, app):
		""" Get application details """

		try:
			cur = self.mysqlConn.cursor()
			cur.execute("""
					SELECT *
					FROM mqtta
					WHERE id=%s
				""", (int(app),))
			appDetails = cur.fetchone()
			cur.close()
			self.Helpers.logger.info("App details select OK!")
			return appDetails
		except:
			e = sys.exc_info()
			self.Helpers.logger.info("App details select failed!")
			self.Helpers.logger.info(str(e))
			return ""

	def updateApplicationStatus(self, payload, splitTopic):
		""" Updates the status of an application """

		try:
			cur = self.mysqlConn.cursor()
			cur.execute("""
					UPDATE mqtta
					SET status=%s
					WHERE id=%s
				""", (str(payload), splitTopic[2]))
			self.mysqlConn.commit()
			cur.close()
			self.Helpers.logger.info("Mysql Application status updated OK")
		except:
			e = sys.exc_info()
			self.mysqlConn.rollback()
			self.Helpers.logger.info("Mysql Application status update FAILED!")
			self.Helpers.logger.info(str(e))

	def updateApplication(self, typeof, data, splitTopic):
		""" Updates an application """

		try:
			cur = self.mysqlConn.cursor()
			cur.execute("""
							UPDATE mqtta
							SET cpu=%s,
								mem=%s,
								hdd=%s,
								tempr=%s,
								lt=%s,
								lg=%s
							WHERE id=%s
						""", (data["CPU"], data["Memory"], data["Diskspace"], data["Temperature"], data["Latitude"], data["Longitude"], splitTopic[2]))
			self.mysqlConn.commit()
			cur.close()
			self.Helpers.logger.info("Mysql " + typeof + " application updated OK")
		except:
			e = sys.exc_info()
			self.mysqlConn.rollback()
			self.Helpers.logger.info("Mysql " + typeof + " application updated FAILED ")
			self.Helpers.logger.info(str(e))

	def getDevice(self, device):
		""" Get application details """

		try:
			cur = self.mysqlConn.cursor()
			cur.execute("""
							SELECT *
							FROM mqttld
							WHERE id=%s
						""", (int(device),))
			dvcDetails = cur.fetchone()
			cur.close()
			self.Helpers.logger.info("Device details select OK!")
			return dvcDetails
		except:
			e = sys.exc_info()
			self.Helpers.logger.info("Device details select failed!")
			self.Helpers.logger.info(str(e))
			return ""

	def updateDeviceStatus(self, payload, device):
		""" Updates the status of a device """

		try:
			cur = self.mysqlConn.cursor()
			cur.execute("""
							UPDATE mqttld
							SET status=%s
							WHERE id=%s
						""", (str(payload), device))
			self.mysqlConn.commit()
			cur.close()
			self.Helpers.logger.info("Mysql Device status updated OK")
		except:
			e = sys.exc_info()
			self.mysqlConn.rollback()
			self.Helpers.logger.info("Mysql Device status update FAILED")
			self.Helpers.logger.info(str(e))

	def updateDevice(self, typeof, data, device):
		""" Updates a device """

		try:
			cur = self.mysqlConn.cursor()
			cur.execute("""
							UPDATE mqttld
							SET cpu=%s,
								mem=%s,
								hdd=%s,
								tempr=%s,
								lt=%s,
								lg=%s
							WHERE id=%s
						""", (data["CPU"], data["Memory"], data["Diskspace"], data["Temperature"], data["Latitude"], data["Longitude"], device))
			self.mysqlConn.commit()
			cur.close()
			self.Helpers.logger.info("Mysql " + typeof + " device updated OK")
		except:
			e = sys.exc_info()
			self.mysqlConn.rollback()
			self.Helpers.logger.info("Mysql " + typeof + " device update FAILED!")
			self.Helpers.logger.info(str(e))

	def getNLU(self, splitTopic):
		""" Get NLU device """

		try:
			cur = self.mysqlConn.cursor()
			cur.execute("""
							SELECT genisysainlu.did
							FROM genisysainlu
							INNER JOIN mqttld
							ON genisysainlu.did = mqttld.id
							WHERE mqttld.zid = %s
								&& mqttld.status=%s
						""", (splitTopic[2], "ONLINE"))
			nlu = cur.fetchone()
			cur.close()
			self.Helpers.logger.info("Camera NLU details: " + str(nlu))
			return nlu
		except:
			e = sys.exc_info()
			self.Helpers.logger.info("Camera NLU details select failed!")
			self.Helpers.logger.info(str(e))
			return ""
		pass

	def updateUserLocation(self, splitTopic, data):
		""" Get NLU device """

		try:
			cur = self.mysqlConn.cursor()
			cur.execute("""
							UPDATE users
							SET cz=%s,
								czt=%s
							WHERE id=%s
						""", (splitTopic[2], time.time(), int(data["Value"])))
			self.mysqlConn.commit()
			cur.close()
			self.Helpers.logger.info("Mysql user location data updated OK")
		except:
			e = sys.exc_info()
			self.mysqlConn.rollback()
			self.Helpers.logger.info("Mysql user location update FAILED")
			self.Helpers.logger.info(str(e))

	def getUser(self, data):
		""" Get user details """

		cTime = datetime.now()
		hb = cTime - timedelta(hours=1)
		hbe = int(hb.timestamp())

		try:
			cur = self.mysqlConn.cursor()
			cur.execute("""
							SELECT users.name,
								users.aid,
								mqtta.status
							FROM users
							INNER JOIN mqtta
							ON users.aid = mqtta.id
							WHERE users.id=%s
								&& (users.welcomed = 0 || users.welcomed <= %s)
						""", (int(data["Value"]), hbe))
			userDetails = cur.fetchone()
			cur.close()
			self.Helpers.logger.info("User details: " + str(userDetails))
			return userDetails
		except:
			e = sys.exc_info()
			self.Helpers.logger.info("User details select failed ")
			self.Helpers.logger.info(str(e))
			return ""

	def getUserNFC(self, uid):
		""" Checks user NFC UID """

		try:
			cur = self.mysqlConn.cursor()
			cur.execute("""
							SELECT nfc
							FROM users
							WHERE users.nfc=%s
						""", (uid,))
			uuid = cur.fetchone()
			cur.close()
			if uuid[0] is not None:
				self.Helpers.logger.info("NFC UID OK!")
				return True
			else:
				self.Helpers.logger.info("NFC UID Not Authorized!")
				return False
		except:
			e = sys.exc_info()
			self.Helpers.logger.info("NFC UID select failed ")
			self.Helpers.logger.info(str(e))
			return ""

	def updateUser(self, data):
		""" Get user details """

		try:
			cur = self.mysqlConn.cursor()
			cur.execute("""
							UPDATE users
							SET welcomed=%s
							WHERE id=%s
						""", (time.time(), int(data["Value"])))
			self.mysqlConn.commit()
			cur.close()
			self.Helpers.logger.info("Mysql user welcome updated OK")
		except:
			e = sys.exc_info()
			self.mysqlConn.rollback()
			self.Helpers.logger.info("Mysql user welcome updated FAILED!")
			self.Helpers.logger.info(str(e))

	def insertDataTransaction(self, aid, did, action, thash):
		""" Get user details """

		try:
			cur = self.mysqlConn.cursor()
			cur.execute("""
							INSERT INTO transactions (aid, did, action, hash, time)
							VALUES (%s, %s, %s, %s, %s)
						""", (aid, did, action, thash, time.time()))
			self.mysqlConn.commit()
			hashid = cur.lastrowid
			self.Helpers.logger.info("Transaction stored in database!")

			cur = self.mysqlConn.cursor()
			cur.execute("""
							INSERT INTO history (taid, tdid, action, hash, time)
							VALUES (%s, %s, %s, %s, %s)
						""", (aid, did, action, hashid, time.time()))
			self.mysqlConn.commit()
			cur.close()
			self.Helpers.logger.info("History stored in database!")
		except:
			e = sys.exc_info()
			self.mysqlConn.rollback()
			self.Helpers.logger.info("Transaction history FAILED!")
			self.Helpers.logger.info(str(e))

	def getContracts(self):
		""" Get all smart contracts """

		try:
			cur = self.mysqlConn.cursor()
			cur.execute("""
							SELECT *
							FROM contracts
						""")
			contracts = cur.fetchall()
			cur.close()
			self.Helpers.logger.info("Got contracts: " + str(len(contracts)))
			return contracts
		except:
			e = sys.exc_info()
			self.Helpers.logger.info("Contracts select failed ")
			self.Helpers.logger.info(str(e))
			return ""
