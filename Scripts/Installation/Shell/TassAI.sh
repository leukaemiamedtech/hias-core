#!/bin/bash

read -p "? This script will install TassAI on your HIAS Server. The script assumes you have downloaded l_openvino_toolkit_p_2020.3.194 (2020 3 LTS) to your home directory. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
	echo "- Installing TassAI...."
	sudo mkdir -p /fserver/models/TassAI
	sudo chown -R $USER:$USER /fserver/models/TassAI
	read -p "! Enter your full domain name including https://: " domain
	read -p "! Enter your HIAS Blockchain user address: " haddress
	read -p "! Enter your HIAS Blockchain user pass: " hpass
	read -p "! Enter your HIAS public key: " user
	read -p "! Enter your HIAS private key: " pass
	read -p "! Enter your zone name (No spaces or special characters). This field represents the zone that this device is installed in, ie: Office, Study, Lounge, Kitchen etc: " zone
	read -p "! Enter local IP address of the HIAS Server device (IE: 192.168.1.98): " ip
	read -p "! Enter MAC address of HIAS Server device: " mac
	php Scripts/Installation/PHP/TassAI.php "$zone" "$ip" "$mac" "$domain" "$user" "$pass" "$haddress" "$hpass"
	read -p "! Enter your TassAI Device Zone ID: " zid
	read -p "! Enter your TassAI Device ID: " did
	read -p "! Enter your TassAI Device name: " dn
	read -p "! Enter your TassAI Device MQTT username: " gun
	read -p "! Enter your TassAI Device MQTT password: " gpw
	sudo sed -i "s/\"zid\":.*/\"zid\": \"$zid\",/g" "confs.json"
	sudo sed -i "s/\"did\":.*/\"did\": \"$did\",/g" "confs.json"
	sudo sed -i "s/\"dn\":.*/\"dn\": \"$dn\",/g" "confs.json"
	sudo sed -i "s/\"gun\":.*/\"gun\": \"$gun\",/g" "confs.json"
	sudo sed -i "s/\"gpw\":.*/\"gpw\": \"${gpw//&/\\&}\",/g" "confs.json"
	sudo sed -i "s/YourCameraApiIP/$ip/g" "confs.json"
	wget https://download.01.org/opencv/2020/openvinotoolkit/2020.3/open_model_zoo/models_bin/1/face-detection-retail-0004/FP16/face-detection-retail-0004.bin -P /fserver/models/TassAI/
	wget https://download.01.org/opencv/2020/openvinotoolkit/2020.3/open_model_zoo/models_bin/1/face-detection-retail-0004/FP16/face-detection-retail-0004.xml -P /fserver/models/TassAI/
	wget https://download.01.org/opencv/2020/openvinotoolkit/2020.3/open_model_zoo/models_bin/1/face-reidentification-retail-0095/FP16/face-reidentification-retail-0095.bin -P /fserver/models/TassAI/
	wget https://download.01.org/opencv/2020/openvinotoolkit/2020.3/open_model_zoo/models_bin/1/face-reidentification-retail-0095/FP16/face-reidentification-retail-0095.xml -P /fserver/models/TassAI/
	wget https://download.01.org/opencv/2020/openvinotoolkit/2020.3/open_model_zoo/models_bin/1/landmarks-regression-retail-0009/FP16/landmarks-regression-retail-0009.bin -P /fserver/models/TassAI/
	wget https://download.01.org/opencv/2020/openvinotoolkit/2020.3/open_model_zoo/models_bin/1/landmarks-regression-retail-0009/FP16/landmarks-regression-retail-0009.xml -P /fserver/models/TassAI/
	echo "- TassAI iotJumpWay device installation complete!";
	exit 0
else
	echo "- TassAI installation terminated!";
	exit 1
fi