#!/bin/bash

read -p "? This script will install phpMyAdmin on your device. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
    echo "- Installing phpMyAdmin"
    echo "! tab -> enter -> yes -> password "
    sudo apt-get install phpmyadmin
    sudo ln -s /usr/share/phpmyadmin /fserver/var/www/html
    sudo sed -i "s/|\s*\((count(\$analyzed_sql_results\['select_expr'\]\)/| (\1)/g" /usr/share/phpmyadmin/libraries/sql.lib.php
    echo "- Installed phpMyAdmin";
    exit 0
else
    echo "- phpMyAdmin installation terminated";
    exit 1
fi
