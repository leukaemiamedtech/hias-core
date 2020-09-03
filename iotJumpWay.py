#!/usr/bin/env python3
############################################################################################
#
# Project:       Peter Moss Leukemia AI Research
# Repository:    HIAS: Hospital Intelligent Automation System
# Project:       GeniSysAI
#
# Author:        Adam Milton-Barker (AdamMiltonBarker.com)
# Contributors:
# Title:         GeniSysAI Class
# Description:   The GeniSysAI Class provides the Hospital Intelligent Automation System with
#                it's intelligent functionality.
# License:       MIT License
# Last Modified: 2020-06-04
#
############################################################################################

import json
import logging
import MySQLdb
import psutil
import requests
import signal
import sys
import time
import threading

from threading import Thread

from datetime import datetime
from datetime import timedelta
from pymongo import MongoClient

from Classes.Helpers import Helpers
from Classes.iotJumpWay import Application

class iotJumpWay():
	""" iotJumpWay Class

	The iotJumpWay Class listens for data from the network and
	stores it in the Mongo db.
	"""

	def __init__(self):
		""" Initializes the class. """

		self.Helpers = Helpers("GeniSysAI")
		self.Helpers.logger.info("GeniSysAI Class initialization complete.")

	def startIoT(self):
		# Initiates the iotJumpWay connection class

		self.Application = Application({
			"host": self.Helpers.confs["iotJumpWay"]["host"],
			"port": self.Helpers.confs["iotJumpWay"]["port"],
			"lid": self.Helpers.confs["iotJumpWay"]["lid"],
			"aid": self.Helpers.confs["iotJumpWay"]["paid"],
			"an": self.Helpers.confs["iotJumpWay"]["pan"],
			"un": self.Helpers.confs["iotJumpWay"]["pun"],
			"pw": self.Helpers.confs["iotJumpWay"]["ppw"]
		})
		self.Application.connect()

		self.Application.appChannelSub("#","#")
		self.Application.appDeviceChannelSub("#", "#", "#")

		self.Application.appCommandsCallback = self.appCommandsCallback
		self.Application.appSensorCallback = self.appSensorCallback
		self.Application.appStatusCallback = self.appStatusCallback
		self.Application.appTriggerCallback = self.appTriggerCallback
		self.Application.deviceCommandsCallback = self.deviceCommandsCallback
		self.Application.deviceSensorCallback = self.deviceSensorCallback
		self.Application.deviceStatusCallback = self.deviceStatusCallback
		self.Application.deviceTriggerCallback = self.deviceTriggerCallback
		self.Application.deviceCameraCallback = self.deviceCameraCallback
		self.Application.deviceLifeCallback = self.deviceLifeCallback
		self.Application.appLifeCallback = self.appLifeCallback

		self.Helpers.logger.info("GeniSysAI Class initialization complete.")

	def startMysql(self):

		self.mysqlConn = MySQLdb.connect(host=self.Helpers.confs["iotJumpWay"]["ip"],
											user=self.Helpers.confs["iotJumpWay"]["dbuser"],
											passwd=self.Helpers.confs["iotJumpWay"]["dbpass"],
											db=self.Helpers.confs["iotJumpWay"]["dbname"])
		self.Helpers.logger.info("MySQL connection started")

	def startMongo(self):

		connection = MongoClient(self.Helpers.confs["iotJumpWay"]["ip"])
		self.mongoConn = connection[self.Helpers.confs["iotJumpWay"]["mdb"]]
		self.mongoConn.authenticate(self.Helpers.confs["iotJumpWay"]["mdbu"],
									self.Helpers.confs["iotJumpWay"]["mdbp"])
		self.Helpers.logger.info("Mongo connection started")

	def life(self):
		""" Sends vital statistics to HIAS """

		cpu = psutil.cpu_percent()
		mem = psutil.virtual_memory()[2]
		hdd = psutil.disk_usage('/').percent
		tmp = psutil.sensors_temperatures()['coretemp'][0].current
		r = requests.get('http://ipinfo.io/json?token=15062dec38bfc3')
		data = r.json()
		location = data["loc"].split(',')

		self.Helpers.logger.info("GeniSysAI Life (TEMPERATURE): " + str(tmp) + "\u00b0")
		self.Helpers.logger.info("GeniSysAI Life (CPU): " + str(cpu) + "%")
		self.Helpers.logger.info("GeniSysAI Life (Memory): " + str(mem) + "%")
		self.Helpers.logger.info("GeniSysAI Life (HDD): " + str(hdd) + "%")
		self.Helpers.logger.info("GeniSysAI Life (LAT): " + str(location[0]))
		self.Helpers.logger.info("GeniSysAI Life (LNG): " + str(location[1]))

		# Send iotJumpWay notification
		self.Application.appChannelPub("Life", self.Helpers.confs["iotJumpWay"]["paid"], {
			"CPU": cpu,
			"Memory": mem,
			"Diskspace": hdd,
			"Temperature": tmp,
			"Latitude": location[0],
			"Longitude": location[1]
		})

		threading.Timer(60.0, self.life).start()

	def appStatusCallback(self, topic, payload):
		"""
		iotJumpWay Application Status Callback

		The callback function that is triggerend in the event of an application
		status communication from the iotJumpWay.
		"""

		self.Helpers.logger.info("Recieved iotJumpWay Application Status : " + payload.decode())

		splitTopic=topic.split("/")

		try:
			cur = self.mysqlConn.cursor()
			cur.execute ("""
				UPDATE mqtta
				SET status=%s
				WHERE id=%s
			""", (str(payload.decode()), splitTopic[2]))
			self.mysqlConn.commit()
			self.Helpers.logger.info("Mysql data updated OK")
		except:
			self.mysqlConn.rollback()
			e = sys.exc_info()[0]
			self.Helpers.logger.info("Mysql data updated FAILED " + str(e))

		try:
			collection = self.mongoConn.Statuses
			doc = {
				"Use": "Application",
				"Location": splitTopic[0],
				"Zone": 0,
				"Application": splitTopic[2],
				"Device": 0,
				"Status": payload.decode(),
				"Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
			}
			collection.insert(doc)
			self.Helpers.logger.info("Mongo data inserted OK")
		except:
			e = sys.exc_info()[0]
			self.Helpers.logger.info("Mongo data inserted FAILED")

	def appLifeCallback(self, topic, payload):
		"""
		iotJumpWay Application Life Callback

		The callback function that is triggerend in the event of a application
		life communication from the iotJumpWay.
		"""

		self.Helpers.logger.info("Recieved iotJumpWay Application Life Data : " + payload.decode())
		data = json.loads(payload.decode("utf-8"))
		splitTopic=topic.split("/")

		try:
			cur = self.mysqlConn.cursor()
			cur.execute ("""
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
			self.Helpers.logger.info("Mysql LIFE data updated OK")
		except:
			self.mysqlConn.rollback()
			e = sys.exc_info()[0]
			self.Helpers.logger.info("Mysql LIFE data updated FAILED " + str(e))

		try:
			collection = self.mongoConn.Life
			doc = {
				"Use": "Application",
				"Location": splitTopic[0],
				"Zone": 0,
				"Application": splitTopic[2],
				"Device": 0,
				"Data": data,
				"Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
			}
			collection.insert(doc)
			self.Helpers.logger.info("Mongo data inserted OK")
		except:
			e = sys.exc_info()[0]
			self.Helpers.logger.info("Mongo data inserted FAILED" + str(e))

	def appCommandsCallback(self, topic, payload):
		"""
		iotJumpWay Application Commands Callback

		The callback function that is triggerend in the event of an application
		command communication from the iotJumpWay.
		"""

		self.Helpers.logger.info("Recieved iotJumpWay Application Command Data: " + payload.decode())
		command = json.loads(payload.decode("utf-8"))

		splitTopic=topic.split("/")

		try:
			collection = self.mongoConn.Commands
			doc = {
				"Use": "Application",
				"Location": splitTopic[0],
				"Zone": 0,
				"From": command["From"],
				"To": splitTopic[3],
				"Type": command["Type"],
				"Value": command["Value"],
				"Message": command["Message"],
				"Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
			}
			collection.insert(doc)
			self.Helpers.logger.info("Mongo data inserted OK")
		except:
			e = sys.exc_info()[0]
			self.Helpers.logger.info("Mongo data inserted FAILED")

	def appSensorCallback(self, topic, payload):
		"""
		iotJumpWay Application Sensors Callback

		The callback function that is triggerend in the event of an application
		sensor communication from the iotJumpWay.
		"""

		self.Helpers.logger.info("Recieved iotJumpWay Application Sensors Data : " + payload.decode())
		command = json.loads(payload.decode("utf-8"))

		splitTopic=topic.split("/")

		try:
			collection = self.mongoConn.Sensors
			doc = {
				"Use": "Application",
				"Location": splitTopic[0],
				"Zone": 0,
				"Application": splitTopic[2],
				"Device": 0,
				"Sensor": command["Sensor"],
				"Type": command["Type"],
				"Value": command["Value"],
				"Message": command["Message"],
				"Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
			}
			collection.insert(doc)
			self.Helpers.logger.info("Mongo data inserted OK")
		except:
			e = sys.exc_info()[0]
			self.Helpers.logger.info("Mongo data inserted FAILED")

	def appTriggerCallback(self, topic, payload):
		"""
		iotJumpWay Application Trigger Callback

		The callback function that is triggerend in the event of an application
		trigger communication from the iotJumpWay.
		"""

		self.Helpers.logger.info("Recieved iotJumpWay Application Trigger Data : " + payload.decode())
		command = json.loads(payload.decode("utf-8"))

	def deviceStatusCallback(self, topic, payload):
		"""
		iotJumpWay Device Status Callback

		The callback function that is triggerend in the event of an device
		status communication from the iotJumpWay.
		"""

		self.Helpers.logger.info("Recieved iotJumpWay Device Status Data : " + payload.decode())
		splitTopic=topic.split("/")

		try:
			cur = self.mysqlConn.cursor()
			cur.execute ("""
							UPDATE mqttld
							SET status=%s
							WHERE id=%s
					""", (str(payload.decode()), splitTopic[3]))
			self.mysqlConn.commit()
			self.Helpers.logger.info("Mysql data updated OK")
		except:
			self.mysqlConn.rollback()
			e = sys.exc_info()[0]
			self.Helpers.logger.info("Mysql data updated FAILED " + str(e))

		try:
			collection = self.mongoConn.Statuses
			doc = {
				"Use": "Device",
				"Location": splitTopic[0],
				"Zone": splitTopic[2],
				"Application": 0,
				"Device": splitTopic[3],
				"Status": payload.decode(),
				"Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
			}
			collection.insert(doc)
			self.Helpers.logger.info("Mongo data inserted OK")
		except:
			e = sys.exc_info()[0]
			self.Helpers.logger.info("Mongo data inserted FAILED" + str(e))

	def deviceLifeCallback(self, topic, payload):
		"""
		iotJumpWay Device Life Callback

		The callback function that is triggerend in the event of a device
		life communication from the iotJumpWay.
		"""

		self.Helpers.logger.info("Recieved iotJumpWay Device Life Data : " + payload.decode())
		data = json.loads(payload.decode("utf-8"))
		splitTopic=topic.split("/")

		try:
			cur = self.mysqlConn.cursor()
			cur.execute ("""
							UPDATE mqttld
							SET cpu=%s,
								mem=%s,
								hdd=%s,
								tempr=%s,
								lt=%s,
								lg=%s
							WHERE id=%s
					""", (data["CPU"], data["Memory"], data["Diskspace"], data["Temperature"], data["Latitude"], data["Longitude"], splitTopic[3]))
			self.mysqlConn.commit()
			self.Helpers.logger.info("Mysql LIFE data updated OK")
		except:
			self.mysqlConn.rollback()
			e = sys.exc_info()[0]
			self.Helpers.logger.info("Mysql LIFE data updated FAILED " + str(e))

		try:
			collection = self.mongoConn.Life
			doc = {
				"Use": "Device",
				"Location": splitTopic[0],
				"Zone": splitTopic[2],
				"Application": 0,
				"Device": splitTopic[3],
				"Data": data,
				"Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
			}
			collection.insert(doc)
			self.Helpers.logger.info("Mongo data inserted OK")
		except:
			e = sys.exc_info()[0]
			self.Helpers.logger.info("Mongo data inserted FAILED" + str(e))

	def deviceCommandsCallback(self, topic, payload):
		"""
		iotJumpWay Application Commands Callback

		The callback function that is triggerend in the event of an device
		command communication from the iotJumpWay.
		"""

		self.Helpers.logger.info("Recieved iotJumpWay Device Command Data: " + payload.decode())
		command = json.loads(payload.decode("utf-8"))

		splitTopic=topic.split("/")

		try:
			collection = self.mongoConn.Commands
			doc = {
				"Use": "Device",
				"Location": splitTopic[0],
				"Zone": splitTopic[2],
				"From": command["From"],
				"To": splitTopic[3],
				"Type": command["Type"],
				"Value": command["Value"],
				"Message": command["Message"],
				"Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
			}
			collection.insert(doc)
			self.Helpers.logger.info("Mongo data inserted OK")
		except:
			e = sys.exc_info()[0]
			self.Helpers.logger.info("Mongo data inserted FAILED")

	def deviceSensorCallback(self, topic, payload):
		"""
		iotJumpWay Application Sensors Callback

		The callback function that is triggerend in the event of an device
		sensor communication from the iotJumpWay.
		"""

		self.Helpers.logger.info("Recieved iotJumpWay Device Sensors Data : " + payload.decode())
		command = json.loads(payload.decode("utf-8"))

		splitTopic=topic.split("/")

		try:
			collection = self.mongoConn.Sensors
			doc = {
				"Use": "Device",
				"Location": splitTopic[0],
				"Zone": splitTopic[2],
				"Application": 0,
				"Device": splitTopic[3],
				"Sensor": command["Sensor"],
				"Type": command["Type"],
				"Value": command["Value"],
				"Message": command["Message"],
				"Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
			}
			collection.insert(doc)
			self.Helpers.logger.info("Mongo data inserted OK")
		except:
			e = sys.exc_info()[0]
			self.Helpers.logger.info("Mongo data inserted FAILED")

	def deviceTriggerCallback(self, topic, payload):
		"""
		iotJumpWay Application Trigger Callback

		The callback function that is triggerend in the event of an device
		trigger communication from the iotJumpWay.
		"""

		self.Helpers.logger.info("Recieved iotJumpWay Device Trigger Data : " + payload.decode())
		command = json.loads(payload.decode("utf-8"))

	def deviceCameraCallback(self, topic, payload):
		"""
		iotJumpWay Device Camera Callback

		The callback function that is triggerend in the event of a camera detecting a
		known user or intruder.
		"""

		self.Helpers.logger.info("Recieved iotJumpWay Device Camera Data: " + payload.decode())
		data = json.loads(payload.decode("utf-8"))

		splitTopic=topic.split("/")

		try:
			cur = self.mysqlConn.cursor()
			cur.execute ("""
							SELECT genisysainlu.did
							FROM genisysainlu
							INNER JOIN mqttld
							ON genisysainlu.did = mqttld.id
							WHERE mqttld.zid = %s
								&& mqttld.status=%s
					""", (splitTopic[2], "ONLINE"))
			nlu = cur.fetchone()
			self.Helpers.logger.info("Camera NLU details: " + str(nlu))
		except:
			e = sys.exc_info()[0]
			self.Helpers.logger.info("Camera NLU details select failed " + str(e))

		if data["Value"] is not 0:

			try:
				cur = self.mysqlConn.cursor()
				cur.execute ("""
								UPDATE users
								SET cz=%s,
									czt=%s
								WHERE id=%s
						""", (splitTopic[2], time.time(), int(data["Value"])))
				self.mysqlConn.commit()
				self.Helpers.logger.info("Mysql camera data updated OK")
			except:
				self.mysqlConn.rollback()
				e = sys.exc_info()[0]
				self.Helpers.logger.info("Mysql camera data updated FAILED " + str(e))

			try:
				collection = self.mongoConn.Users
				doc = {
					"User": int(data["Value"]),
					"Location": splitTopic[0],
					"Zone": splitTopic[2],
					"Device": splitTopic[3],
					"Time": datetime.now().strftime('%Y-%m-%d %H:%M:%S')
				}
				collection.insert(doc)
				self.Helpers.logger.info("Mongo camera data inserted OK")
			except:
				e = sys.exc_info()[0]
				self.Helpers.logger.info("Mongo camera data inserted FAILED" + str(e))

			cTime = datetime.now()
			hb = cTime - timedelta(hours=1)
			hbe = int(hb.timestamp())
			print(hbe)

			try:
				cur = self.mysqlConn.cursor()
				cur.execute ("""
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
				self.Helpers.logger.info("Camera user details: " + str(userDetails))
			except:
				e = sys.exc_info()[0]
				self.Helpers.logger.info("Camera user details select failed " + str(e))

			if userDetails[1] and nlu[0]:

				self.Application.appDeviceChannelPub("Commands", splitTopic[2], nlu[0], {"Command": "Welcome", "Value": userDetails[0]})

				try:
					cur = self.mysqlConn.cursor()
					cur.execute ("""
									UPDATE users
									SET welcomed=%s
									WHERE id=%s
							""", (time.time(), int(data["Value"])))
					self.mysqlConn.commit()
					self.Helpers.logger.info("Mysql camera welcome updated OK")
				except:
					self.mysqlConn.rollback()
					e = sys.exc_info()[0]
					self.Helpers.logger.info("Mysql camera welcome updated FAILED " + str(e))

			elif userDetails[1] and userDetails[2] is "ONLINE":
				print("SEND USER APP NOTIFICATION")

	def signal_handler(self, signal, frame):
		self.Helpers.logger.info("Disconnecting")
		self.Application.appDisconnect()
		sys.exit(1)

iotJumpWay = iotJumpWay()

def main():

	signal.signal(signal.SIGINT, iotJumpWay.signal_handler)
	signal.signal(signal.SIGTERM, iotJumpWay.signal_handler)

	# Starts the application

	iotJumpWay.startIoT()
	iotJumpWay.startMysql()
	iotJumpWay.startMongo()

	Thread(target=iotJumpWay.life, args=(), daemon=True).start()
	while True:
		continue
	exit()

if __name__ == "__main__":
	main()
