#!/bin/bash

read -p "? This script will install your iotJumpWay location and core application on your HIAS Server. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
	echo "- Installing iotJumpWay location...."
	sudo apt install apache2-utils
	sudo mkdir -p /etc/nginx/security
	sudo touch /etc/nginx/security/htpasswd
	sudo chown -R $USER:$USER /etc/nginx/security
	read -p "! Enter your default location name. This field represents the physical location that your server is installed in, ie: Home, Office, Hospital, Center etc: " location
	read -p "! Enter your full domain name including https://: " domain
	read -p "! Enter your HIAS Blockchain user address: " haddress
	read -p "! Enter your HIAS Blockchain user pass: " hpass
	read -p "! Enter your HIAS iotJumpWay MQTT Blockchain user address: " iaddress
	read -p "! Enter your HIAS iotJumpWay AMQP Blockchain user address: " aaddress
	read -p "! Enter the IP of your HIAS Server: " ip
	read -p "! Enter the MAC address of your HIAS Server: " mac
	php Scripts/Installation/PHP/Location.php "$location" "Context Broker" "$haddress" "$hpass" "$ip" "$mac" "MQTT IoT Agent" "$iaddress" "AMQP IoT Agent" "$aaddress" "$domain"
	read -p "! Enter your HIAS Location Identifier (1): " lid
	read -p "! Enter your Context Broker Application Public Key: " haid
	read -p "! Enter your Context Broker Application name: " han
	read -p "! Enter your Context Broker Application MQTT username: " hun
	read -p "! Enter your Context Broker Application MQTT password: " hpw
	read -p "! Enter your iotJumpWay MQTT Application Public Key: " iaid
	read -p "! Enter your iotJumpWay MQTT Application Private Key: " iapk
	read -p "! Enter your iotJumpWay MQTT Application name: " ian
	read -p "! Enter your iotJumpWay MQTT Application MQTT username: " iun
	read -p "! Enter your iotJumpWay MQTT Application MQTT password: " ipw
	read -p "! Enter your iotJumpWay AMQP Application Public Key: " aaid
	read -p "! Enter your iotJumpWay AMQP Application MQTT username: " aun
	read -p "! Enter your iotJumpWay AMQP Application MQTT password: " apw
	hpw=$(printf '%s\n' "$hpw" | sed -e 's/[\/&]/\\&/g');
	ipw=$(printf '%s\n' "$ipw" | sed -e 's/[\/&]/\\&/g');
	apw=$(printf '%s\n' "$apw" | sed -e 's/[\/&]/\\&/g');
	iapk=$(printf '%s\n' "$iapk" | sed -e 's/[\/&]/\\&/g');
	sudo sed -i -- "s/YourIotJumpWayLocationID/$lid/g" "confs.json"
	sudo sed -i -- "s/YourIotJumpWayApplicationIdentifier/$iaid/g" "confs.json"
	sudo sed -i -- "s/YourIotJumpWayApplicationAuthKey/$iapk/g" "confs.json"
	sudo sed -i -- "s/YourContextBrokerApplicationID/$haid/g" "confs.json"
	sudo sed -i -- "s/YourContextBrokerApplicationName/$han/g" "confs.json"
	sudo sed -i -- "s/YourContextBrokerApplicationMqttUsername/$hun/g" "confs.json"
	sudo sed -i -- "s/YourContextBrokerApplicationMqttPassword/$hpw/g" "confs.json"
	sudo sed -i -- "s/YourIotJumpWayApplicationID/$iaid/g" "confs.json"
	sudo sed -i -- "s/YourIotJumpWayApplicationName/$ian/g" "confs.json"
	sudo sed -i -- "s/YourIotJumpWayApplicationUsername/$iun/g" "confs.json"
	sudo sed -i -- "s/YourIotJumpWayApplicationPassword/$ipw/g" "confs.json"
	sudo sed -i -- "s/YourAmqpApplicationIdentifier/$aaid/g" "confs.json"
	sudo sed -i -- "s/YourAmqpApplicationUsername/$aun/g" "confs.json"
	sudo sed -i -- "s/YourAmqpApplicationPasword/$apw/g" "confs.json"
	echo "- Installed iotJumpWay location and applications!";
	exit 0
else
	echo "- iotJumpWay location and applications installation terminated!";
	exit 1
fi