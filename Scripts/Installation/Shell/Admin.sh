#!/bin/bash

read -p "? This script will create a new user account for your HIAS Server. You will need to copy and save the credentials provided to you at the end of this script. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
    echo "- Creating HIAS Server admin user"
    read -p "! Enter your name: " name
    read -p "! Enter your email: " email
    read -p "! Enter your desired username (No spaces or special characters): " username
    read -p "! Enter your desired personal HIAS Blockchain account address: " paddress
    read -p "! Enter your desired personal HIAS Blockchain account password: " ppass
    read -p "! Enter local IP address of the device that the application will run on (IE: 192.168.1.98): " ip
    read -p "! Enter MAC address of the device that the application will run on: " mac
    sudo apt install apache2-utils
    sudo touch /etc/nginx/security/htpasswd
    sudo chown www-data:www-data /etc/nginx/security/htpasswd
    sudo touch /etc/nginx/security/patients
    sudo chown www-data:www-data /etc/nginx/security/patients
    sudo touch /etc/nginx/security/beds
    sudo chown www-data:www-data /etc/nginx/security/beds
    php Scripts/Installation/PHP/Admin.php "$name" "$email" "$username" "$paddress" "$ppass" "$ip" "$mac"
else
    echo "- HIAS Server admin user creation terminated";
    exit
fi
