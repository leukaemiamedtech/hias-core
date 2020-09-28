#!/bin/bash

read -p "? This script will update the SSL security on your HIAS Server. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
    echo "- Updating SSL security...."
    sudo cp /etc/nginx/nginx.conf /etc/nginx/nginx.tls-old
    sudo sed -i -- "s/ssl_protocols TLSv1 TLSv1.1 TLSv1.2;/ssl_protocols TLSv1.2 TLSv1.3;/g" /etc/nginx/nginx.conf
    sudo sed -i -- "s/ssl_protocols TLSv1 TLSv1.1 TLSv1.2;/ssl_protocols TLSv1.2 TLSv1.3;/g" /etc/letsencrypt/options-ssl-nginx.conf
    sudo nginx -t
    sudo service nginx reload
    echo "- Updated SSL security!";
    exit 0
else
    echo "- SSL security updating terminated!";
    exit 1
fi
