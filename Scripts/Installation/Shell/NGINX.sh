
#!/bin/bash

FMSG="- NGINX installation terminated"

read -p "? This script will install NGINX on your HIAS Server. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
    echo "- Installing NGINX"
    sudo apt-get install nginx
    sudo cp /etc/nginx/sites-available/default /etc/nginx/sites-available/default.backup
    sudo mkdir -p /fserver/var
    sudo cp -a Root/var/www/ /fserver/var/
    sudo chown -R www-data:www-data /fserver/var/www/html
    sudo usermod -a -G www-data $USER
    echo ""
    read -p "? Please provide the full domain name of your server, including subdomain: " domain
    read -p "? Please provide the IP of your HIAS server: " ip
    if [ "$domain" != "" ]; then
        sudo sed -i -- "s/server_name _;/server_name $domain;/g" /etc/nginx/sites-available/default
        sudo sed -i -- "s/HiasServerIp/$ip;/g" /etc/nginx/sites-available/default
        sudo nginx -t
        sudo systemctl reload nginx
        echo "- Installed NGINX";
        exit 0
    else
        echo $FMSG;
        exit 1
    fi
else
    echo $FMSG;
    exit 1
fi
