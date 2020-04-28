#!/bin/bash

read -p "? This script will install Let's Encypt for NGINX on your device. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
    echo "- Installing Let's Encrypt NGINX"
    sudo add-apt-repository ppa:certbot/certbot
    sudo apt-get update
    sudo apt-get install python-certbot-nginx
    echo "- Follow commands to setup secure server"
    sudo certbot --nginx
    echo "- Installed Let's Encrypt NGINX"
    exit 0
else
    echo "- Let's Encypt NGINX installation terminated";
    exit 1
fi