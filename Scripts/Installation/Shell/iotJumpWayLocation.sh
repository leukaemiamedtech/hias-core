#!/bin/bash

read -p "? This script will install your iotJumpWay location and core application on your HIAS Server. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
    echo "- Installing iotJumpWay location...."
    read -p "! Enter your default location name. This field represents the physical location that your server is installed in, ie: Home, Office, Hospital, Center etc: " location
    read -p "! Enter your HIAS Blockchain user address: " haddress
    read -p "! Enter the IP of your HIAS Server: " ip
    read -p "! Enter the MAC address of your HIAS Server: " mac
    read -p "! Enter your HIAS iotJumpWay Blockchain user address: " iaddress
    php Scripts/Installation/PHP/Location.php "$location" "HIAS" "$haddress" "$ip" "$mac" "iotJumpWay" "$iaddress" "$ip" "$mac"
    echo "- Installed iotJumpWay location and applications!";
    exit 0
else
    echo "- iotJumpWay location and applications installation terminated!";
    exit 1
fi
