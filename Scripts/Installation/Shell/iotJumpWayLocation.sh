#!/bin/bash

read -p "? This script will install your iotJumpWay location and core application on your server. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
    echo "- Installing iotJumpWay location...."
    read -p "! Enter your default location name (No spaces or special characters). This field represents the physical location that your server is installed in, ie: Home, Office, Hospital, Center etc: " location
    read -p "! Enter your default application name (No spaces or special characters). This field represents the server application that will provide websocket services to the UI allowing it to communicate with the network via the UI, ie: WebSockets, Server etc: " application
    read -p "! Enter local IP address of the device that the application will run on (IE: 192.168.1.98): " ip
    read -p "! Enter MAC address of the device that the application will run on: " mac
    php Scripts/Installation/PHP/Location.php "$location" "$application" "$ip" "$mac"
    echo "- Installed iotJumpWay location, application and devices!";
    exit 0
else
    echo "- iotJumpWay location and application installation terminated!";
    exit 1
fi
