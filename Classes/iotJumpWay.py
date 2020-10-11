######################################################################################################
#
# Organization:  Peter Moss Leukemia AI Research
# Repository:    HIAS: Hospital Intelligent Automation System
#
# Author:        Adam Milton-Barker (AdamMiltonBarker.com)
# Contributors:
# Title:         iotJumpWay Class
# Description:   The iotJumpWay Class provides the Hospital Intelligent Automation System with it's
#                IoT functionality.
# License:       MIT License
# Last Modified: 2020-06-04
#
######################################################################################################

import inspect, json, os

import paho.mqtt.client as mqtt

from Classes.Helpers import Helpers

class Application():
    """ iotJumpWay Class

    The iotJumpWay Class provides the Hospital Intelligent Automation System with
    it's IoT functionality.
    """

    def __init__(self, configs):
        """ Initializes the class. """

        self.Helpers = Helpers("iotJumpWay")
        self.confs = configs

        self.Helpers.logger.info("Initiating Local iotJumpWay Application.")

        if self.confs['host'] == None:
            raise ConfigurationException("** Host (host) property is required")
        elif self.confs['port'] == None:
            raise ConfigurationException("** Port (port) property is required")
        elif self.confs['lid'] == None:
            raise ConfigurationException("** Location ID (lid) property is required")
        elif self.confs['aid'] == None:
            raise ConfigurationException("** Application ID (aid) property is required")
        elif self.confs['an'] == None:
            raise ConfigurationException("** Application Name (an) property is required")
        elif self.confs['un'] == None:
            raise ConfigurationException("** MQTT UserName (un) property is required")
        elif self.confs['pw'] == None:
            raise ConfigurationException("** MQTT Password (pw) property is required")

        self.mqttClient = None
        self.mqttTLS = "/etc/ssl/certs/DST_Root_CA_X3.pem"
        self.mqttHost = self.confs['host']
        self.mqttPort = self.confs['port']

        self.appCommandsCallback = None
        self.appSensorCallback = None
        self.appStatusCallback = None
        self.appTriggerCallback = None
        self.appLifeCallback = None
        self.deviceCameraCallback = None
        self.deviceCommandsCallback = None
        self.deviceLifeCallback = None
        self.deviceNfcCallback = None
        self.deviceSensorCallback = None
        self.deviceStatusCallback = None
        self.deviceTriggerCallback = None

        self.Helpers.logger.info("JumpWayMQTT Application Initiated.")

    def connect(self):

        self.Helpers.logger.info("Initiating Local iotJumpWay Application Connection.")

        self.mqttClient = mqtt.Client(client_id = self.confs['an'], clean_session = True)
        applicationStatusTopic = '%s/Applications/%s/Status' % (self.confs['lid'], self.confs['aid'])
        self.mqttClient.will_set(applicationStatusTopic, "OFFLINE", 0, False)
        self.mqttClient.tls_set(self.mqttTLS, certfile=None, keyfile=None)
        self.mqttClient.on_connect = self.on_connect
        self.mqttClient.on_message = self.on_message
        self.mqttClient.on_publish = self.on_publish
        self.mqttClient.on_subscribe = self.on_subscribe
        self.mqttClient.username_pw_set(str(self.confs['un']), str(self.confs['pw']))
        self.mqttClient.connect(self.mqttHost, self.mqttPort, 10)
        self.mqttClient.loop_start()

        self.Helpers.logger.info("Local iotJumpWay Application Connection Initiated.")

    def on_connect(self, client, obj, flags, rc):

        self.Helpers.logger.info("Local iotJumpWay Application Connection Successful.")
        self.Helpers.logger.info("rc: " + str(rc))

        self.appStatusPub("ONLINE")

    def appStatusPub(self, data):

        deviceStatusTopic = '%s/Applications/%s/Status' % (self.confs['lid'], self.confs['aid'])
        self.mqttClient.publish(deviceStatusTopic, data)
        self.Helpers.logger.info("Published to Application Status " + deviceStatusTopic)

    def on_subscribe(self, client, obj, mid, granted_qos):

        self.Helpers.logger.info("JumpWayMQTT Subscription: "+str(self.confs['an']))

    def on_message(self, client, obj, msg):

        self.Helpers.logger.info("JumpWayMQTT Message Received")
        splitTopic=msg.topic.split("/")

        if splitTopic[1]=='Applications':
            if splitTopic[3]=='Status':
                if self.appStatusCallback == None:
                    self.Helpers.logger.info("** Application Status Callback Required (appStatusCallback)")
                else:
                    self.appStatusCallback(msg.topic,msg.payload)
            elif splitTopic[3]=='Command':
                if self.cameraCallback == None:
                    self.Helpers.logger.info("** Application Camera Callback Required (cameraCallback)")
                else:
                    self.cameraCallback(msg.topic,msg.payload)
            elif splitTopic[3]=='Life':
                if self.appLifeCallback == None:
                    self.Helpers.logger.info("** Application Life Callback Required (appLifeCallback)")
                else:
                    self.appLifeCallback(msg.topic,msg.payload)
        elif splitTopic[1] == 'Devices':
            if splitTopic[4] == 'Actuators':
                if self.deviceActuatorCallback == None:
                    self.Helpers.logger.info("** Device Actuator Callback Required (deviceActuatorCallback)")
                else:
                    self.deviceActuatorCallback(msg.topic, msg.payload)
            elif splitTopic[4]=='Commands':
                if self.deviceCommandsCallback == None:
                    self.Helpers.logger.info("** Device Commands Callback Required (deviceCommandsCallback)")
                else:
                    self.deviceCommandsCallback(msg.topic,msg.payload)
            elif splitTopic[4]=='Life':
                if self.deviceLifeCallback == None:
                    self.Helpers.logger.info("** Device Life Callback Required (deviceLifeCallback)")
                else:
                    self.deviceLifeCallback(msg.topic, msg.payload)
            elif splitTopic[4]=='NFC':
                if self.deviceNfcCallback == None:
                    self.Helpers.logger.info("** Device NFC Callback Required (deviceNfcCallback)")
                else:
                    self.deviceNfcCallback(msg.topic, msg.payload)
            elif splitTopic[4] == 'Status':
                if self.deviceStatusCallback == None:
                    self.Helpers.logger.info("** Device Status Callback Required (deviceStatusCallback)")
                else:
                    self.deviceStatusCallback(msg.topic, msg.payload)
            elif splitTopic[4]=='Sensors':
                if self.deviceSensorCallback == None:
                    self.Helpers.logger.info("** Device Sensors Callback Required (deviceSensorCallback)")
                else:
                    self.deviceSensorCallback(msg.topic,msg.payload)
            elif splitTopic[4]=='Notifications':
                if self.deviceNotificationsCallback == None:
                    self.Helpers.logger.info("** Device Notifications Callback Required (deviceNotificationsCallback)")
                else:
                    self.deviceNotificationsCallback(msg.topic,msg.payload)
            elif splitTopic[4]=='Cameras':
                if self.deviceCameraCallback == None:
                    self.Helpers.logger.info("** Device Camera Callback Required (cameraCallback)")
                else:
                    self.deviceCameraCallback(msg.topic,msg.payload)

    def appChannelPub(self, channel, application, data):

        applicationChannel = '%s/Applications/%s/%s' % (self.confs['lid'], application, channel)
        self.mqttClient.publish(applicationChannel,json.dumps(data))
        print("Published to Application "+channel+" Channel")

    def appChannelSub(self, application, channelID, qos=0):

        if application == "#":
            applicationChannel = '%s/Applications/#' % (self.confs['lid'])
            self.mqttClient.subscribe(applicationChannel, qos=qos)
            self.Helpers.logger.info("-- Subscribed to all Application Channels")
            return True
        else:
            applicationChannel = '%s/Applications/%s/%s' % (self.confs['lid'], application, channelID)
            self.mqttClient.subscribe(applicationChannel, qos=qos)
            self.Helpers.logger.info("-- Subscribed to Application " + channelID + " Channel")
            return True

    def appDeviceChannelPub(self, channel, zone, device, data):

        deviceChannel = '%s/Devices/%s/%s/%s' % (self.confs['lid'], zone, device, channel)
        self.mqttClient.publish(deviceChannel, json.dumps(data))
        self.Helpers.logger.info("-- Published to Device "+channel+" Channel")

    def appDeviceChannelSub(self, zone, device, channel, qos=0):

        if zone == None:
            print("** Zone ID (zoneID) is required!")
            return False
        elif device == None:
            print("** Device ID (device) is required!")
            return False
        elif channel == None:
            print("** Channel ID (channel) is required!")
            return False
        else:
            if device == "#":
                deviceChannel = '%s/Devices/#' % (self.confs['lid'])
                self.mqttClient.subscribe(deviceChannel, qos=qos)
                self.Helpers.logger.info("-- Subscribed to all devices")
            else:
                deviceChannel = '%s/Devices/%s/%s/%s' % (self.confs['lid'], zone, device, channel)
                self.mqttClient.subscribe(deviceChannel, qos=qos)
                self.Helpers.logger.info("-- Subscribed to Device "+channel+" Channel")

            return True

    def on_publish(self, client, obj, mid):

            print("-- Published: "+str(mid))

    def on_log(self, client, obj, level, string):

            print(string)

    def appDisconnect(self):
        self.appStatusPub("OFFLINE")
        self.mqttClient.disconnect()
        self.mqttClient.loop_stop()

class Device():
	""" iotJumpWay Class

	The iotJumpWay Class provides the EMAR device with  it's IoT functionality.
	"""

	def __init__(self):
		""" Initializes the class. """

		self.Helpers = Helpers("iotJumpWay")

		self.Helpers.logger.info("Initiating Local iotJumpWay Device.")

		self.mqttClient = None
		self.mqttTLS = "/etc/ssl/certs/DST_Root_CA_X3.pem"
		self.mqttHost = self.Helpers.confs["iotJumpWay"]['host']
		self.mqttPort = self.Helpers.confs["iotJumpWay"]['port']

		self.commandsCallback = None

		self.Helpers.logger.info("JumpWayMQTT Device Initiated.")

	def connect(self):

		self.Helpers.logger.info("Initiating Local iotJumpWay Device Connection.")

		self.mqttClient = mqtt.Client(client_id = self.Helpers.confs["iotJumpWay"]['dn'], clean_session = True)
		deviceStatusTopic = '%s/Device/%s/%s/Status' % (self.Helpers.confs["iotJumpWay"]['lid'], self.Helpers.confs["iotJumpWay"]['zid'], self.Helpers.confs["iotJumpWay"]['did'])
		self.mqttClient.will_set(deviceStatusTopic, "OFFLINE", 0, False)
		self.mqttClient.tls_set(self.mqttTLS, certfile=None, keyfile=None)
		self.mqttClient.on_connect = self.on_connect
		self.mqttClient.on_message = self.on_message
		self.mqttClient.on_publish = self.on_publish
		self.mqttClient.on_subscribe = self.on_subscribe
		self.mqttClient.username_pw_set(str(self.Helpers.confs["iotJumpWay"]['gun']), str(self.Helpers.confs["iotJumpWay"]['gpw']))
		self.mqttClient.connect(self.mqttHost, self.mqttPort, 10)
		self.mqttClient.loop_start()

		self.Helpers.logger.info("Local iotJumpWay Device Connection Initiated.")

	def on_connect(self, client, obj, flags, rc):

		self.Helpers.logger.info("Local iotJumpWay Device Connection Successful.")
		self.Helpers.logger.info("rc: " + str(rc))

		self.statusPub("ONLINE")

	def on_subscribe(self, client, obj, mid, granted_qos):

		self.Helpers.logger.info("JumpWayMQTT Subscription: "+str(mid))

	def on_message(self, client, obj, msg):

		print("JumpWayMQTT Message Received")
		splitTopic=msg.topic.split("/")

		if splitTopic[1]=='Devices':
			if splitTopic[4]=='Commands':
				if self.commandsCallback == None:
					print("** Device Commands Callback Required (commandsCallback)")
				else:
					self.commandsCallback(msg.topic, msg.payload)
			elif splitTopic[4]=='Triggers':
				if self.triggersCallback == None:
					print("** Device Notifications Callback Required (deviceNotificationsCallback)")
				else:
					self.triggersCallback(msg.topic, msg.payload)

	def statusPub(self, data):

		deviceStatusTopic = '%s/Devices/%s/%s/Status' % (self.Helpers.confs["iotJumpWay"]['lid'], self.Helpers.confs["iotJumpWay"]['zid'], self.Helpers.confs["iotJumpWay"]['did'])
		self.mqttClient.publish(deviceStatusTopic, data)
		self.Helpers.logger.info("Published to Device Status " + deviceStatusTopic)

	def channelPub(self, channel, data):

		deviceChannel = '%s/Devices/%s/%s/%s' % (self.Helpers.confs["iotJumpWay"]['lid'], self.Helpers.confs["iotJumpWay"]['zid'], self.Helpers.confs["iotJumpWay"]['did'], channel)
		self.mqttClient.publish(deviceChannel, json.dumps(data))

	def channelSub(self, channel, qos=0):

		if channel == None:
			self.Helpers.logger.info("** Channel (channel) is required!")
			return False
		else:
			deviceChannel = '%s/Devices/%s/%s/%s' % (self.Helpers.confs["iotJumpWay"]['lid'], self.Helpers.confs["iotJumpWay"]['zid'], self.Helpers.confs["iotJumpWay"]['did'], channel)
			self.mqttClient.subscribe(deviceChannel, qos=qos)
			self.Helpers.logger.info("-- Subscribed to Device "+channel+" Channel")

	def on_publish(self, client, obj, mid):

		self.Helpers.logger.info("-- Published to Device channel")

	def on_log(self, client, obj, level, string):

			print(string)

	def disconnect(self):
		self.statusPub("OFFLINE")
		self.mqttClient.disconnect()
		self.mqttClient.loop_stop()
