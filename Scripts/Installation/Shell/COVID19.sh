#!/bin/bash

read -p "? This script will install the COVID-19 data analysis system on your HIAS Server. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
    echo "Installing COVID-19 data analysis system"
    sudo chmod -R 777 /fserver/var/www/html/Data-Analysis/COVID-19/Data/
    php Scripts/Installation/PHP/COVID19.php
    echo "Installed COVID-19 data analysis system"
    exit 0
else
    echo "- COVID-19 data analysis system installation terminated";
    exit 1
fi