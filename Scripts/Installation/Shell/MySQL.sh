#!/bin/bash

FMSG="- MySQL installation terminated"

read -p "? This script will install MySQL on your HIAS Server. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
    echo "- Installing MySQL"
    echo "! Make sure you keep note of all passwords etc you create."
    sudo apt-get install python3-pip
    sudo apt install mysql-server
    sudo mysql_secure_installation
    sudo apt-get install libmysqlclient-dev
    pip3 install pymysql
    pip3 install mysqlclient
    read -p "! Enter your mysql root password specified during set up: " rpassword
    read -p "! Enter a new phpMyAdmin database user: " dbusername
    read -p "! Enter a new phpMyAdmin database password: " dbpassword
    echo "- creating phpMyAdmin password"
    sudo mysql -uroot -p$rpassword -e "GRANT ALL PRIVILEGES ON *.* TO  $dbusername@localhost IDENTIFIED BY '$dbpassword'";
    sudo mysql -uroot -p$rpassword -e "SELECT host, user from mysql.user";
    read -p "! Enter a new application database user: " adbusername
    read -p "! Enter a new application database password: " adbpassword
    sudo mysql -uroot -p$rpassword -e "GRANT SELECT, INSERT, DELETE  ON *.* TO $adbusername@localhost IDENTIFIED BY '$adbpassword'";
    sudo mysql -uroot -p$rpassword -e "SELECT host, user from mysql.user";
    read -p "! Enter a new database name: " dbname
    sudo mysql -uroot -p$rpassword -e "CREATE DATABASE $dbname";
    sudo mysql -uroot -p$rpassword -e 'show databases;'
    sudo mysql -uroot -p$rpassword -e "use $dbname;"
    sudo mysql -uroot  -p$rpassword $dbname < Scripts/Installation/SQL.sql;
    sudo sed -i "s/\"dbname\":.*/\"dbname\": \"$dbname\",/g" "confs.json"
    sudo sed -i "s/\"dbuser\":.*/\"dbuser\": \"$adbusername\",/g" "confs.json"
    sudo sed -i "s/\"dbpass\":.*/\"dbpass\": \"${adbpassword//&/\\&}\"/g" "confs.json"
    sudo sed -i "s/\"dbname\":.*/\"dbname\": \"$dbname\",/g" "/fserver/var/www/Classes/Core/confs.json"
    sudo sed -i "s/\"dbusername\":.*/\"dbusername\": \"$adbusername\",/g" "/fserver/var/www/Classes/Core/confs.json"
    sudo sed -i "s/\"dbpassword\":.*/\"dbpassword\": \"${adbpassword//&/\\&}\",/g" "/fserver/var/www/Classes/Core/confs.json"
    read -p "! Enter a new encryption key, this key should be 32 characters: " ekey
    sudo sed -i "s/\"key\":.*/\"key\": \"$ekey\"/g" "/fserver/var/www/Classes/Core/confs.json"
    echo "! Updated MySql configuration."
    echo "! Moving MySql to hard-drive."
    sudo systemctl stop mysql
    sudo systemctl status mysql
    sudo rsync -av /var/lib/mysql /fserver
    sudo sed -i -- "s#/var/lib/mysql#/fserver/mysql#g" /etc/mysql/mysql.conf.d/mysqld.cnf
    sudo sed -i -- "s+# alias /var/lib/mysql/ -> /home/mysql/,+alias /var/lib/mysql/ -> /fserver/mysql/,+g" /etc/apparmor.d/tunables/alias
    sudo systemctl restart apparmor
    sudo mkdir /var/lib/mysql/mysql -p
    sudo systemctl start mysql
    sudo systemctl status mysql
    sudo rm -Rf /var/lib/mysql
    sudo systemctl start mysql
    sudo systemctl status mysql
    sudo systemctl reload nginx
    echo "! Moved MySql to hard-drive."
    exit 0
else
    echo $FMSG;
    exit 1
fi