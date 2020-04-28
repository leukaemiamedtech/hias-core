#!/bin/bash

read -p "? This script will install UFW Firewall on your server. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
    echo "Installing UFW"
    sudo apt-get install ufw
    echo "Testing UFW"
    sudo ufw enable
    sudo ufw disable
    echo "GeniSysAI opening default ports"
    sudo ufw allow 22
    sudo ufw allow 80
    sudo ufw allow 443
    sudo ufw allow 1883
    sudo ufw allow 9001
    sudo ufw allow OpenSSH
    sudo ufw allow Samba
    echo "Enabling UFW"
    sudo ufw enable
    echo "Checking UFW"
    sudo ufw status
    echo "Installed UFW"
    exit 0
else
    echo "- UFW Firewall installation terminated";
    exit 1
fi