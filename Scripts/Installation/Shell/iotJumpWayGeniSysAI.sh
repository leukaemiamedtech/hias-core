#!/bin/bash

read -p "? This script will install your GeniSysAI iotJumpWay device on your server. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
    echo "- Installing GeniSysAI iotJumpWay device...."
    read -p "! Enter the location ID for this device: " location
    read -p "! Enter your zone name (No spaces or special characters). This field represents the zone that this device is installed in, ie: Office, Study, Lounge, Kitchen etc: " zone
    read -p "! Enter local IP address of this device (IE: 192.168.1.98): " ip
    read -p "! Enter MAC address of this device: " mac
    php Scripts/Installation/PHP/GeniSysAI.php "$location" "$zone" "$ip" "$mac"
    echo "- GeniSysAI iotJumpWay device installation complete!";
    exit 0
else
    echo "- GeniSysAI iotJumpWay device installation terminated!";
    exit 1
fi
