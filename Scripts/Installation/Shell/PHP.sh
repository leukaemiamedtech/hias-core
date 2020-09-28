#!/bin/bash

FMSG="- PHP installation terminated"

read -p "? This script will install PHP on your HIAS Server. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
    echo "- Installing PHP"
    sudo apt-get install php-fpm php-mysql
    sudo sed -i -- 's/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/g' /etc/php/7.2/fpm/php.ini
    sudo systemctl restart php7.2-fpm
    read -p "? Please provide the full domain name of your server, including subdomain: " domain
    read -p "? Please provide the IP of your server: " ip
    read -p "? Please provide the port port you will use for your Facial Recognition Security API: " port
    if [ "$domain" != "" ]; then
        sudo cp Root/etc/nginx/sites-available/default /etc/nginx/sites-available/default
        sudo sed -i -- "s#root /var/www/html;#root /fserver/var/www/html;#g" /etc/nginx/sites-available/default
        sudo sed -i -- "s/YourSubdomain.YourDomain.TLD/$domain/g" /etc/nginx/sites-available/default
        sudo sed -i -- "s/proxy_pass http://YourSecurityApiIP:YourSecurityApiPort/$1;/proxy_pass http://$ip:$port/g" /etc/nginx/sites-available/default
        sudo cp /etc/nginx/nginx.conf /etc/nginx/nginx.tls-old
        sudo sed -i -- "s/ssl_protocols TLSv1 TLSv1.1 TLSv1.2;/ssl_protocols TLSv1.2 TLSv1.3;/g" /etc/nginx/nginx.conf
        sudo sed -i -- "s/ssl_protocols TLSv1 TLSv1.1 TLSv1.2;/ssl_protocols TLSv1.2 TLSv1.3;/g" /etc/letsencrypt/options-ssl-nginx.conf
        sudo nginx -t
        sudo systemctl reload nginx
        echo "- You can now view your PHP configuration at https://www.YourDomain.com/info";
        echo "- Installed PHP";
        exit 0
    else
        echo $FMSG;
        exit 1
    fi
else
    echo $FMSG;
    exit 1
fi
