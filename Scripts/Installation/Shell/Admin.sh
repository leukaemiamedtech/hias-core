#!/bin/bash

read -p "? This script will create a new user account for your COVID-19 Medical Support System Server. You will need to copy and save the credentials provided to you at the end of this script. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
    echo "- Creating GeniSysAI admin user"
    read -p "! Enter your desired username (No spaces or special characters): " username
    read -p "! Enter your location ID: " lid
    php Scripts/Installation/PHP/Admin.php "$username" "$lid"
    #python Scripts/Installation/Python/Admin.py "$username"
else
    echo "- GeniSysAI admin user creation terminated";
    exit
fi
