#!/bin/bash

read -p "? This script will install your iotJumpWay location and core application on your HIAS Server. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
	echo "- Installing iotJumpWay location...."
	sudo apt install apache2-utils
	sudo mkdir -p /etc/nginx/security
	sudo touch /etc/nginx/security/htpasswd
	sudo chmod -R 777 /etc/nginx/security
	read -p "! Enter your default location name. This field represents the physical location that your server is installed in, ie: Home, Office, Hospital, Center etc: " location
	read -p "! Enter your full domain name including https://: " domain
	read -p "! Enter your HIAS Blockchain user address: " haddress
	read -p "! Enter your HIAS Blockchain user pass: " hpass
	read -p "! Enter the IP of your HIAS Server: " ip
	read -p "! Enter the MAC address of your HIAS Server: " mac
	read -p "! Enter your HIAS iotJumpWay Blockchain user address: " iaddress
	php Scripts/Installation/PHP/Location.php "$location" "HIAS" "$haddress" "$hpass" "$ip" "$mac" "iotJumpWay" "$iaddress" "$domain"
	read -p "! Enter your iotJumpWay Application Location ID (1): " lid
	read -p "! Enter your iotJumpWay Application Application ID: " aid
	read -p "! Enter your iotJumpWay Application Application name: " an
	read -p "! Enter your iotJumpWay Application Application MQTT username: " un
	read -p "! Enter your iotJumpWay Application Application MQTT password: " pw
	sudo sed -i "s/\"lid\":.*/\"lid\": \"$lid\",/g" "confs.json"
	sudo sed -i "s/\"paid\":.*/\"paid\": \"$aid\",/g" "confs.json"
	sudo sed -i "s/\"pan\":.*/\"pan\": \"$an\",/g" "confs.json"
	sudo sed -i "s/\"pun\":.*/\"pun\": \"$un\",/g" "confs.json"
	sudo sed -i "s/\"ppw\":.*/\"ppw\": \"${pw//&/\\&}\",/g" "confs.json"
	echo "- Installed iotJumpWay location and applications!";
	exit 0
else
	echo "- iotJumpWay location and applications installation terminated!";
	exit 1
fi
