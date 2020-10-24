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
	read -p "! Enter your HIAS iotJumpWay Location ID: " lid
	read -p "! Enter your HIAS iotJumpWay Location Entity: " lie
	read -p "! Enter your zone name (No spaces or special characters). This field represents the zone that this device is installed in, ie: Office, Study, Lounge, Kitchen etc: " zone
	read -p "! Enter local IP address of the HIAS Server device (IE: 192.168.1.98): " ip
	read -p "! Enter MAC address of HIAS Server device: " mac
	php Scripts/Installation/PHP/TassAI.php "$lid"  "$lie" "$zone" "$ip" "$mac" "$domain" "$user" "$pass" "$haddress" "$hpass"
	read -p "! Enter your TassAI Device Zone ID: " zid
	read -p "! Enter your TassAI Device ID: " did
	read -p "! Enter your TassAI Device MQTT username: " un
	read -p "! Enter your TassAI Device MQTT password: " pw
	sudo sed -i -- "s/YourTassAIZoneID/$zid/g" "confs.json"
	sudo sed -i -- "s/YourTassAIDeviceID/$did/g" "confs.json"
	sudo sed -i -- "s/YourTassAIMqttUsername/$un/g" "confs.json"
	sudo sed -i -- "s/YourTassAIMqttPassword/$pw/g" "confs.json"
	cd ~/
	 tar -xvzf l_openvino_toolkit_p_2020.3.194.tgz
	cd l_openvino_toolkit_p_2020.3.194
	sudo ./install.sh
	cd /opt/intel/openvino/deployment_tools/model_optimizer/install_prerequisites
	sudo ./install_prerequisites.sh
	echo "# OpenVINO" | tee -a ~/.bashrc
	echo "source /opt/intel/openvino/bin/setupvars.sh" | tee -a ~/.bashrc
	source ~/.bashrc
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