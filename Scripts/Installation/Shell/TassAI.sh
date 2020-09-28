#!/bin/bash

read -p "? This script will install TassAI on your HIAS Server. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
    echo "- Installing TassAI...."
    sudo apt install python3-pip
    sudo apt install cmake
    sudo apt install python3-opencv
    sudo mkdir -p /fserver/models/TassAI
    pip3 install flask
    pip3 install requests
    pip3 install jsonpickle
    wget https://download.01.org/opencv/2020/openvinotoolkit/2020.3/open_model_zoo/models_bin/1/face-detection-retail-0004/FP16/face-detection-retail-0004.bin -P /fserver/models/TassAI/
    wget https://download.01.org/opencv/2020/openvinotoolkit/2020.3/open_model_zoo/models_bin/1/face-detection-retail-0004/FP16/face-detection-retail-0004.xml -P /fserver/models/TassAI/
    wget https://download.01.org/opencv/2020/openvinotoolkit/2020.3/open_model_zoo/models_bin/1/face-reidentification-retail-0095/FP16/face-reidentification-retail-0095.bin -P /fserver/models/TassAI/
    wget https://download.01.org/opencv/2020/openvinotoolkit/2020.3/open_model_zoo/models_bin/1/face-reidentification-retail-0095/FP16/face-reidentification-retail-0095.xml -P /fserver/models/TassAI/
    wget https://download.01.org/opencv/2020/openvinotoolkit/2020.3/open_model_zoo/models_bin/1/landmarks-regression-retail-0009/FP16/landmarks-regression-retail-0009.bin -P /fserver/models/TassAI/
    wget https://download.01.org/opencv/2020/openvinotoolkit/2020.3/open_model_zoo/models_bin/1/landmarks-regression-retail-0009/FP16/landmarks-regression-retail-0009.xml -P /fserver/models/TassAI/
    read -p "! Enter your zone name (No spaces or special characters). This field represents the zone that this device is installed in, ie: Office, Study, Lounge, Kitchen etc: " zone
    read -p "! Enter local IP address of the HIAS Server device (IE: 192.168.1.98): " ip
    read -p "! Enter MAC address of HIAS Server device: " mac
    php Scripts/Installation/PHP/TassAI.php "$zone" "$ip" "$mac"
    echo "- TassAI iotJumpWay device installation complete!";
    echo "- Installed TassAI!";
    exit 0
else
    echo "- TassAI installation terminated!";
    exit 1
fi