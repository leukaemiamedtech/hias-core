#!/bin/bash

FMSG="HIAS Core installation terminated!"

printf -- 'This script will install HIAS Core on your machine.\n';
printf -- '\033[33m WARNING: Before running this script you must add new credentials to the install.config file. \033[0m\n';
printf -- '\033[33m WARNING: This script assumes Ubuntu 20.04. \033[0m\n';
printf -- '\033[33m WARNING: This is an inteteractive installation, please follow instructions provided. \033[0m\n';

sed -i 's/\r//' scripts/install.config
. scripts/install.config

read -p "Proceed (y/n)? " proceed
if [ "$proceed" = "Y" -o "$proceed" = "y" ]; then

    printf -- 'First you will install UFW Firewall.\n';
    read -p "Proceed (y/n)? " proceed
    if [ "$proceed" = "Y" -o "$proceed" = "y" ]; then
        printf -- 'Installing UFW Firewall...\n';
        sudo apt install ufw
        printf -- 'Testing UFW Firewall...\n';
        sudo ufw enable
        sudo ufw disable
        printf -- 'Opening required ports...\n';
        sudo ufw allow 22 # SSH
        sudo ufw allow 80 # HTTP
        sudo ufw allow 443 # HTTPS
        sudo ufw allow 636 # LDAP
        sudo ufw allow 1027 # HIASBCH
        sudo ufw allow 3524 # HIASCDI
        sudo ufw allow 3525 # HIASHDI
        sudo ufw allow 5671 # AMQP
        sudo ufw allow 8883 # MQTT
        sudo ufw allow 8545 # HIASBCH
        sudo ufw allow 9001 # Websockets
        sudo ufw allow 15671 # AMQP Manager
        sudo ufw allow 27017 # MongoDB
        sudo ufw allow 30303/udp # HIASBCH
        sudo ufw allow 30303/tcp # HIASBCH
        sudo ufw allow ldap # LDAP
        sudo ufw allow OpenSSH # OpenSSH
        printf -- 'Enabling UFW Firewall...\n';
        sudo ufw enable
        printf -- 'Checking UFW Firewall...\n';
        sudo ufw status
        printf -- '\033[32m SUCCESS: Installed UFW Firewall! \033[0m\n';
    else
        echo $FMSG;
        exit 1
    fi

    printf -- 'Now you will install Fail2Ban.\n';
    read -p "Proceed (y/n)? " proceed
    if [ "$proceed" = "Y" -o "$proceed" = "y" ]; then
        printf -- 'Installing Fail2Ban...\n';
        sudo apt install fail2ban
        sudo mv /etc/fail2ban/jail.conf /etc/fail2ban/jail.local
        sudo rm /etc/fail2ban/action.d/ufw.conf
        sudo touch /etc/fail2ban/action.d/ufw.conf
        echo "[Definition]" | sudo tee -a /etc/fail2ban/action.d/ufw.conf
        echo "  enabled  = true" | sudo tee -a /etc/fail2ban/action.d/ufw.conf
        echo "  actionstart =" | sudo tee -a /etc/fail2ban/action.d/ufw.conf
        echo "  actionstop =" | sudo tee -a /etc/fail2ban/action.d/ufw.conf
        echo "  actioncheck =" | sudo tee -a /etc/fail2ban/action.d/ufw.conf
        echo "  actionban = ufw insert 1 deny from <ip> to any" | sudo tee -a /etc/fail2ban/action.d/ufw.conf
        echo "  actionunban = ufw delete deny from <ip> to any" | sudo tee -a /etc/fail2ban/action.d/ufw.conf
        sudo sed -i -- "s#banaction = iptables-multiport#banaction = ufw#g" /etc/fail2ban/jail.local
        sudo fail2ban-client restart
        sudo fail2ban-client status
        printf -- '\033[32m SUCCESS: Fail2Ban installed! \033[0m\n';
    else
        echo $FMSG;
        exit 1
    fi

    printf -- 'Now you will create a group and certificates directory and add your current user.\n';
    read -p "Proceed (y/n)? " proceed
    if [ "$proceed" = "Y" -o "$proceed" = "y" ]; then
        printf -- 'Creating hiascore group.\n';
        sudo groupadd hiascore
        sudo usermod -a -G hiascore $USER
        sudo mkdir /certs
        sudo chown -R :hiascore /certs
        sudo chmod -R g+w /certs
        printf -- '\033[32m SUCCESS: hiascore group created! \033[0m\n';
    else
        echo $FMSG;
        exit 1
    fi

    printf -- 'Now you install and set up the Samba server.\n';
    read -p "Proceed (y/n)? " proceed
    if [ "$proceed" = "Y" -o "$proceed" = "y" ]; then
        printf -- 'Installing Samba...\n';
        sudo apt install samba
        sudo cp /etc/samba/smb.conf /etc/samba/smb.conf.backup
        sudo sed -i -- "s/;   bind interfaces only = yes/bind interfaces only = yes/g" /etc/samba/smb.conf
        testparm
        sudo systemctl restart smbd
        sudo systemctl status smbd
        printf -- '\033[32m SUCCESS: Samba installed! \033[0m\n';
        printf -- 'Creating Samba groups...\n';
        sudo groupadd sambashare
        sudo groupadd smborg
        sudo groupadd smbdev
        printf -- '\033[32m SUCCESS: Created Samba groups! \033[0m\n';
        printf -- 'Creating Samba directory...\n';
        sudo mkdir -p /hias/samba
        sudo chgrp sambashare /hias/samba
        printf -- '\033[32m SUCCESS: Samba directory created! \033[0m\n';
        printf -- 'Creating Samba organization directory...\n';
        sudo mkdir -p /hias/samba/organization
        sudo chgrp smborg /hias/samba/organization
        sudo chmod -R 2770 /hias/samba/organization
        printf -- '\033[32m SUCCESS: Samba organization directory created! \033[0m\n';
        printf -- 'Creating Samba organization user...\n';
        sudo useradd -M -d /hias/samba/organization -s /usr/sbin/nologin -G sambashare,smborg,smbdev "$sambaorguser"
        sudo smbpasswd -a "$sambaorguser"
        sudo smbpasswd -e "$sambaorguser"
        printf -- '\033[32m SUCCESS: Samba organization user created! \033[0m\n';
        printf -- 'Creating Samba developers directory...\n';
        sudo mkdir -p /hias/samba/developers
        sudo chgrp smbdev /hias/samba/developers
        sudo chmod -R 2770 /hias/samba/developers
        printf -- '\033[32m SUCCESS: Samba developers directory created! \033[0m\n'
        printf -- 'Creating Samba developers user...\n';
        sudo useradd -M -d /hias/samba/developers -s /usr/sbin/nologin -G sambashare,smbdev "$sambadevuser"
        sudo smbpasswd -a "$sambadevuser"
        sudo smbpasswd -e "$sambadevuser"
        printf -- '\033[32m SUCCESS: Samba developers user created! \033[0m\n';
        printf -- 'Creating Samba personal user...\n';
        sudo useradd -M -d /hias/samba/"$sambapersonaluser" -s /usr/sbin/nologin -G sambashare "$sambapersonaluser"
        sudo mkdir -p /hias/samba/"$sambapersonaluser"
        sudo chown "$sambapersonaluser":sambashare /hias/samba/"$sambapersonaluser"
        sudo chmod 2770 /hias/samba/"$sambapersonaluser"
        sudo smbpasswd -a "$sambapersonaluser"
        sudo smbpasswd -e "$sambapersonaluser"
        printf -- '\033[32m SUCCESS: Samba developers user created! \033[0m\n';
        printf -- 'Updating Samba configuration...\n';
        echo "" | sudo tee -a /etc/samba/smb.conf
        echo "[organization]" | sudo tee -a /etc/samba/smb.conf
        echo "  path = /hias/samba/organization" | sudo tee -a /etc/samba/smb.conf
        echo "  browseable = yes" | sudo tee -a /etc/samba/smb.conf
        echo "  read only = no" | sudo tee -a /etc/samba/smb.conf
        echo "  force create mode = 0660" | sudo tee -a /etc/samba/smb.conf
        echo "  force directory mode = 2770" | sudo tee -a /etc/samba/smb.conf
        echo "  valid users = @smborg" | sudo tee -a /etc/samba/smb.conf
        echo "" | sudo tee -a /etc/samba/smb.conf
        echo "[developers]" | sudo tee -a /etc/samba/smb.conf
        echo "  path = /hias/samba/developers" | sudo tee -a /etc/samba/smb.conf
        echo "  browseable = yes" | sudo tee -a /etc/samba/smb.conf
        echo "  read only = no" | sudo tee -a /etc/samba/smb.conf
        echo "  force create mode = 0660" | sudo tee -a /etc/samba/smb.conf
        echo "  force directory mode = 2770" | sudo tee -a /etc/samba/smb.conf
        echo "  valid users = @smborg @smbdev" | sudo tee -a /etc/samba/smb.conf
        echo "" | sudo tee -a /etc/samba/smb.conf
        echo "[$sambapersonaluser]" | sudo tee -a /etc/samba/smb.conf
        echo "  path = /hias/samba/$sambapersonaluser" | sudo tee -a /etc/samba/smb.conf
        echo "  browseable = no" | sudo tee -a /etc/samba/smb.conf
        echo "  read only = no" | sudo tee -a /etc/samba/smb.conf
        echo "  force create mode = 0660" | sudo tee -a /etc/samba/smb.conf
        echo "  force directory mode = 2770" | sudo tee -a /etc/samba/smb.conf
        echo "  valid users = $sambapersonaluser @smborg" | sudo tee -a /etc/samba/smb.conf
        printf -- '\033[32m SUCCESS: Samba configuration updated! \033[0m\n';
        printf -- 'Reloading Samba and checking status...\n';
        sudo ufw allow Samba
        sudo systemctl restart smbd
        sudo systemctl status smbd
        printf -- '\033[32m SUCCESS: Samba installed and configured! \033[0m\n';
    else
        echo $FMSG;
        exit 1
    fi

    printf -- 'Now you will install NGINX.\n';
    read -p "Proceed (y/n)? " proceed
    if [ "$proceed" = "Y" -o "$proceed" = "y" ]; then
        printf -- 'Installing NGINX...\n';
        sudo apt install nginx
        sudo cp /etc/nginx/sites-available/default /etc/nginx/sites-available/default.backup
        sudo mkdir -p /hias/var
        sudo cp -a src/var/www/ /hias/var/
        sudo chown -R $USER /hias/var/www/html
        sudo sed -i -- "s/server_name _;/server_name $domain;/g" /etc/nginx/sites-available/default
        sudo nginx -t
        sudo systemctl reload nginx
        printf -- '\033[32m SUCCESS: NGINX installed! \033[0m\n';
    else
        echo $FMSG;
        exit 1
    fi

    printf -- 'Now you will install apache2-utils.\n';
    read -p "Proceed (y/n)? " proceed
    if [ "$proceed" = "Y" -o "$proceed" = "y" ]; then
        sudo apt install apache2-utils
        sudo mkdir -p /etc/nginx/security
        sudo touch /etc/nginx/security/htpasswd
        sudo chown -R www-data:www-data /etc/nginx/security
        printf -- '\033[32m SUCCESS: apache2-utils installed! \033[0m\n';
    else
        echo $FMSG;
        exit 1
    fi

    printf -- 'Now you will install Certbot.\n';
    printf -- '\033[33m HINT: When asked, select 2 to redirect insecure requests to 443. \033[0m\n';
    read -p "Proceed (y/n)? " proceed
    if [ "$proceed" = "Y" -o "$proceed" = "y" ]; then
        printf -- 'Installing Certbot...\n';
        sudo apt install python3-certbot-nginx
        printf -- 'Follow instructions to setup secure server...\n';
        sudo certbot --nginx
        printf -- '\033[32m SUCCESS: Certbot installed! \033[0m\n';
        printf -- 'Copying certs to HIAS certs directory...\n';
        sudo cp /etc/letsencrypt/live/$domain/cert.pem /certs/cert.pem
        sudo cp /etc/letsencrypt/live/$domain/fullchain.pem /certs/fullchain.pem
        sudo cp /etc/letsencrypt/live/$domain/privkey.pem /certs/privkey.pem
        sudo chown :hiascore /certs/*.pem
        sudo chmod 644 /certs/privkey.pem
        printf -- '\033[32m SUCCESS: Copied certs to HIAS certs directory! \033[0m\n';
        printf -- 'Preparing cert renew script...\n';
        sudo chmod u+x /home/$USER/HIAS-Core/scripts/certs.sh
        printf -- 'Opening certbot cron...\n';
        printf -- '\033[33m HINT: add --post-hook "systemctl restart nginx && /home/$USER/HIAS-Core/scripts/certs.sh" after certbot -q renew  \033[0m\n';
        read -p "Proceed (y/n)? " proceed
        if [ "$proceed" = "Y" -o "$proceed" = "y" ]; then
            sudo nano /etc/cron.d/certbot
        else
            echo $FMSG;
            exit 1
        fi
    else
        echo $FMSG;
        exit 1
    fi

    printf -- 'Now you will install PHP.\n';
    read -p "Proceed (y/n)? " proceed
    if [ "$proceed" = "Y" -o "$proceed" = "y" ]; then
        printf -- 'Installing PHP...\n';
        sudo apt install php-fpm php-mysql
        sudo sed -i -- 's/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/g' /etc/php/7.4/fpm/php.ini
        sudo sed -i -- 's/upload_max_filesize = 20M/upload_max_filesize = 100M/g' /etc/php/7.4/fpm/php.ini
        sudo sed -i -- 's/post_max_size = 20M/post_max_size = 100M/g' /etc/php/7.4/fpm/php.ini
        sudo systemctl restart php7.4-fpm
        sudo cp src/etc/nginx/sites-available/default /etc/nginx/sites-available/default
        sudo sed -i -- "s/YourHiasDomainName/$domain/g" /etc/nginx/sites-available/default
        sudo sed -i -- "s/YourHiasServerIp/$ip/g" /etc/nginx/sites-available/default
        sudo nginx -t
        sudo systemctl reload nginx
        printf -- 'You can now view your PHP configuration at https://www.YourDomain.com/info\n';
        printf -- '\033[32m SUCCESS: PHP installed! \033[0m\n';
    else
        echo $FMSG;
        exit 1
    fi

    printf -- 'Now you will install MySQL.\n';
    printf -- '\033[33m HINT: Do not set up VALIDATE PASSWORD plugin. \033[0m\n';
    printf -- '\033[33m HINT: Remove anonymous users. \033[0m\n';
    printf -- '\033[33m HINT: Restrict root to local host. \033[0m\n';
    printf -- '\033[33m HINT: Remove test database. \033[0m\n';
    printf -- '\033[33m HINT: Use the credentials you added in install.config. \033[0m\n';
    read -p "Proceed (y/n)? " proceed
    if [ "$proceed" = "Y" -o "$proceed" = "y" ]; then
        printf -- 'Installing MySQL...\n';
        sudo apt install python3-pip
        sudo apt install mysql-server
        sudo mysql_secure_installation
        sudo apt install libmysqlclient-dev
        conda install -c anaconda pymysql
        conda install -c carta mysqlclient
        printf -- '\033[32m SUCCESS: Installed MySQL! \033[0m\n';
        printf -- 'Creating phpMyAdmin user...\n';
        sudo mysql -uroot -p$mysqlrootpass -e "CREATE USER $mysqlphpmyuser@localhost IDENTIFIED BY '$mysqlphpmypass'";
        sudo mysql -uroot -p$mysqlrootpass -e "GRANT ALL PRIVILEGES ON *.* TO $mysqlphpmyuser@localhost WITH GRANT OPTION;";
        sudo mysql -uroot -p$mysqlrootpass -e "SELECT host, user from mysql.user";
        printf -- '\033[32m SUCCESS: Created phpMyAdmin user! \033[0m\n';
        printf -- 'Creating local user...\n';
        sudo mysql -uroot -p$mysqlrootpass -e "CREATE USER $mysqldbuser@localhost IDENTIFIED BY '$mysqldbpass'";
        sudo mysql -uroot -p$mysqlrootpass -e "GRANT ALL PRIVILEGES ON *.* TO $mysqldbuser@localhost WITH GRANT OPTION;";
        sudo mysql -uroot -p$mysqlrootpass -e "SELECT host, user from mysql.user";
        printf -- '\033[32m SUCCESS: Created local user! \033[0m\n';
        printf -- 'Creating MySQL database...\n';
        sudo mysql -uroot -p$mysqlrootpass -e "CREATE DATABASE $mysqldbname";
        sudo mysql -uroot -p$mysqlrootpass -e 'show databases;'
        printf -- '\033[32m SUCCESS: Created MySQL database! \033[0m\n';
        printf -- 'Populating MySQL database...\n';
        sudo mysql -uroot -p$mysqlrootpass -e "use $mysqldbname;"
        sudo mysql -uroot  -p$mysqlrootpass $mysqldbname < scripts/install.sql;
        printf -- '\033[32m SUCCESS: Populated MySQL database! \033[0m\n';
        printf -- 'Updating UI config...\n';
        sudo sed -i "s/\"dbname\":.*/\"dbname\": \"$mysqldbname\",/g" "/hias/var/www/Classes/Core/confs.json"
        sudo sed -i "s/\"dbusername\":.*/\"dbusername\": \"$mysqldbuser\",/g" "/hias/var/www/Classes/Core/confs.json"
        escaped=$(printf '%s\n' "$mysqldbpass" | sed -e 's/[\/&]/\\&/g');
        sudo sed -i "s/\"dbpassword\":.*/\"dbpassword\": \"$escaped\",/g" "/hias/var/www/Classes/Core/confs.json"
        escaped=$(printf '%s\n' "$enckey" | sed -e 's/[\/&]/\\&/g');
        sudo sed -i "s/\"key\":.*/\"key\": \"$escaped\"/g" "/hias/var/www/Classes/Core/confs.json"
        printf -- '\033[32m SUCCESS: UI config updated! \033[0m\n';
        printf -- 'Moving MySql to HDD...\n';
        sudo systemctl stop mysql
        sudo systemctl status mysql
        sudo rsync -av /var/lib/mysql /hias
        sudo sed -i -- "s#/var/lib/mysql#/hias/mysql#g" /etc/mysql/mysql.conf.d/mysqld.cnf
        sudo sed -i -- "s+# datadir+datadir+g" /etc/mysql/mysql.conf.d/mysqld.cnf
        sudo sed -i -- "s+# alias /var/lib/mysql/ -> /home/mysql/,+alias /var/lib/mysql/ -> /hias/mysql/,+g" /etc/apparmor.d/tunables/alias
        sudo systemctl restart apparmor
        sudo mkdir /var/lib/mysql/mysql -p
        sudo systemctl start mysql
        sudo systemctl status mysql
        sudo rm -Rf /var/lib/mysql
        sudo systemctl start mysql
        sudo systemctl status mysql
        sudo systemctl reload nginx
        printf -- '\033[32m SUCCESS: MySql moved to HDD! \033[0m\n';
        printf -- '\033[32m SUCCESS: MySql installed! \033[0m\n';
    else
        echo $FMSG;
        exit 1
    fi

    printf -- 'Now you will install phpMyAdmin.\n';
    printf -- '\033[33m HINT: tab -> enter -> yes -> password \033[0m\n';
    read -p "Proceed (y/n)? " proceed
    if [ "$proceed" = "Y" -o "$proceed" = "y" ]; then
        printf -- 'Installing phpMyAdmin...\n';
        sudo apt install phpmyadmin
        sudo ln -s /usr/share/phpmyadmin /hias/var/www/html
        sudo sed -i "s/|\s*\((count(\$analyzed_sql_results\['select_expr'\]\)/| (\1)/g" /usr/share/phpmyadmin/libraries/sql.lib.php
        printf -- '\033[32m SUCCESS: Installed phpMyAdmin! \033[0m\n';
    else
        echo $FMSG;
        exit 1
    fi

    printf -- 'Now you will install and set up your LDAP server.\n';
    read -p "Proceed (y/n)? " proceed
    if [ "$proceed" = "Y" -o "$proceed" = "y" ]; then
        printf -- 'Installing LDAP server...\n';
        sudo apt install slapd ldap-utils
        printf -- 'The LDAP installation console will now open.\n';
        printf -- '\033[33m HINT: Do not omit OpenLDAP server configuration \033[0m\n';
        printf -- '\033[33m HINT: Use domain value from install.config for your DNS domain name  \033[0m\n';
        printf -- '\033[33m HINT: Use ldapdc2 value from install.config for your organization name  \033[0m\n';
        printf -- '\033[33m HINT: Use ldapadminpass value from install.config for your admin password  \033[0m\n';
        printf -- '\033[33m HINT: If asked, use MDB as database backend  \033[0m\n';
        printf -- '\033[33m HINT: Do not remove database when slapd is purged  \033[0m\n';
        printf -- '\033[33m HINT: Do move old database  \033[0m\n';
        read -p "Proceed (y/n)? " proceed
        if [ "$proceed" = "Y" -o "$proceed" = "y" ]; then
            sudo dpkg-reconfigure slapd
        else
            echo $FMSG;
            exit 1
        fi
        printf -- 'Now you will install phpldapadmin.\n';
        sudo apt install phpldapadmin
        sudo usermod -a -G hiascore openldap
        printf -- '\033[32m SUCCESS: phpldapadmin installed! \033[0m\n';
        printf -- 'Updating phpldapadmin config...\n';
        sudo sed -i -- "s/My LDAP Server/HIAS LDAP Server/g" /etc/phpldapadmin/config.php
        sudo sed -i -- "s/dc=example,dc=com/dc=$ldapdc1,dc=$ldapdc2,dc=$ldapdc3/g" /etc/phpldapadmin/config.php
        sudo sed -i -- "s/\$servers->setValue('login','bind_id'/#\$servers->setValue('login','bind_id'/g" /etc/phpldapadmin/config.php
        sudo sed -i -- "s/\/\/ \$servers->setValue('login','anon_bind',true);/\$servers->setValue('login','anon_bind',false);/g" /etc/phpldapadmin/config.php
        sudo sed -i -- "s/\/\/ \$config->custom->appearance\['hide_template_warning'\] = false;/\$config->custom->appearance\['hide_template_warning'\] = true;/g" /etc/phpldapadmin/config.php
        printf -- '\033[32m SUCCESS: y config updated! \033[0m\n';
        printf -- 'Updating UI config...\n';
        sudo sed -i "s/\"ldapdc1\":.*/\"ldapdc1\": \"$ldapdc1\",/g" "/hias/var/www/Classes/Core/confs.json"
        sudo sed -i "s/\"ldapdc2\":.*/\"ldapdc2\": \"$ldapdc2\",/g" "/hias/var/www/Classes/Core/confs.json"
        sudo sed -i "s/\"ldapdc3\":.*/\"ldapdc3\": \"$ldapdc3\",/g" "/hias/var/www/Classes/Core/confs.json"
        escaped=$(printf '%s\n' "$ldapadminpass" | sed -e 's/[\/&]/\\&/g');
        sudo sed -i "s/\"ldaps\":.*/\"ldaps\": \"$escaped\",/g" "/hias/var/www/Classes/Core/confs.json"
        printf -- '\033[32m SUCCESS: UI config updated! \033[0m\n';
        printf -- 'Updating phpldapadmin security...\n';
        sudo sed -i '/#include <local\/usr.sbin.slapd>/a \/certs\/* r,' /etc/apparmor.d/usr.sbin.slapd
        sudo systemctl restart apparmor
        sudo touch /certs/ssl.ldif
        echo "dn: cn=config" | sudo tee -a /certs/ssl.ldif
        echo "changetype: modify" | sudo tee -a /certs/ssl.ldif
        echo "add: olcTLSCACertificateFile" | sudo tee -a /certs/ssl.ldif
        echo "olcTLSCACertificateFile: /certs/fullchain.pem" | sudo tee -a /certs/ssl.ldif
        echo "-" | sudo tee -a /certs/ssl.ldif
        echo "add: olcTLSCertificateFile" | sudo tee -a /certs/ssl.ldif
        echo "olcTLSCertificateFile: /certs/cert.pem" | sudo tee -a /certs/ssl.ldif
        echo "-" | sudo tee -a /certs/ssl.ldif
        echo "add: olcTLSCertificateKeyFile" | sudo tee -a /certs/ssl.ldif
        echo "olcTLSCertificateKeyFile: /certs/privkey.pem" | sudo tee -a /certs/ssl.ldif
        sudo ldapmodify -H ldapi:// -Y EXTERNAL -f /certs/ssl.ldif
        printf -- '\033[32m SUCCESS: phpldapadmin security set up! \033[0m\n';
        printf -- '\033[32m SUCCESS: LDAP server installed and set up! \033[0m\n';
    else
        echo $FMSG;
        exit 1
    fi

    printf -- 'Now you will install MongoDB.\n';
    read -p "Proceed (y/n)? " proceed
    if [ "$proceed" = "Y" -o "$proceed" = "y" ]; then
        printf -- 'Installing MongoDB...\n';
        wget -qO - https://www.mongodb.org/static/pgp/server-4.2.asc | sudo apt-key add -
        sudo apt install gnupg
        echo "deb [ arch=amd64,arm64 ] https://repo.mongodb.org/apt/ubuntu bionic/mongodb-org/4.2 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-4.2.list
        sudo apt update
        sudo apt install -y mongodb-org
        sudo apt install php-mongodb
        conda install pymongo
        sudo systemctl enable mongod.service
        sudo systemctl start mongod
        sudo systemctl status mongod
        sudo systemctl restart php7.2-fpm
        printf -- '\033[32m SUCCESS: Installed MongoDB! \033[0m\n';
        printf -- 'Now you will create your MongoDB user.\n';
        printf -- '\033[33m HINT: The MongoDB console will now open. Follow the steps in the Mongo Database section of the installation file to create your database credentials. \033[0m\n';
        printf -- '\033[33m HINT: Use the credentials stored in install.config. \033[0m\n';
        read -p "Proceed (y/n)? " proceed
        if [ "$proceed" = "Y" -o "$proceed" = "y" ]; then
            mongo
            escaped=$(printf '%s\n' "$mongodbpass" | sed -e 's/[\/&]/\\&/g');
            sudo sed -i "s/\"mdbname\":.*/\"mdbname\": \"$mongodbname\",/g" "/hias/var/www/Classes/Core/confs.json"
            sudo sed -i "s/\"mdbusername\":.*/\"mdbusername\": \"$mongodbuser\",/g" "/hias/var/www/Classes/Core/confs.json"
            sudo sed -i "s/\"mdbpassword\":.*/\"mdbpassword\": \"$escaped\",/g" "/hias/var/www/Classes/Core/confs.json"
            printf -- '\033[32m SUCCESS: MongoDB user created! \033[0m\n';
        else
            echo $FMSG;
            exit 1
        fi
        printf -- '\033[32m SUCCESS: MongoDB installed! \033[0m\n';
    else
        echo $FMSG;
        exit 1
    fi

    printf -- 'Now you will update the server SSL security.\n';
    read -p "Proceed (y/n)? " proceed
    if [ "$proceed" = "Y" -o "$proceed" = "y" ]; then
        printf -- 'Updating server SSL security...\n';
        sudo cp /etc/nginx/nginx.conf /etc/nginx/nginx.tls-old
        sudo sed -i -- "s/ssl_protocols TLSv1 TLSv1.1 TLSv1.2;/ssl_protocols TLSv1.2 TLSv1.3;/g" /etc/nginx/nginx.conf
        sudo sed -i -- "s/ssl_protocols TLSv1 TLSv1.1 TLSv1.2;/ssl_protocols TLSv1.2 TLSv1.3;/g" /etc/letsencrypt/options-ssl-nginx.conf
        sudo nginx -t
        sudo service nginx reload
        printf -- '\033[32m SUCCESS: Updated SSL security! \033[0m\n';
    else
        echo $FMSG;
        exit 1
    fi

    printf -- 'Now you will install the HIAS iotJumpWay MQTT Broker.\n';
    read -p "Proceed (y/n)? " proceed
    if [ "$proceed" = "Y" -o "$proceed" = "y" ]; then
        printf -- 'Installing HIAS iotJumpWay MQTT Broker.\n';
        sudo apt install build-essential libc-ares-dev uuid-dev libssl-dev openssl libssl-dev libcurl4-openssl-dev
        sudo apt install libmosquitto-dev
        sudo apt install default-libmysqlclient-dev
        sudo apt install libssl-dev
        sudo apt install libcurl4-openssl-dev
        sudo apt install perl-doc uuid-dev bsdutils gcc g++ git make dialog libssl-dev libc-ares-dev libcurl4-openssl-dev libmysqlclient-dev libwrap0 libwrap0-dev uthash-dev
        conda install -c conda-forge paho-mqtt
        conda install -c conda-forge jsonpickle
        conda install flask
        sudo mkdir -p /hias/libraries/mosquitto
        sudo chown -R $USER:$USER /hias/libraries/mosquitto
        sudo mkdir -p /hias/var/lib/mosquitto
        sudo mkdir -p /hias/var/log/mosquitto
        sudo touch /hias/var/log/mosquitto/mosquitto.log
        sudo chown -R $USER:$USER /hias/var/log/mosquitto/mosquitto.log
        cd /hias/libraries/mosquitto
        wget https://github.com/warmcat/libwebsockets/archive/refs/tags/v2.4.2.zip
        sudo apt install unzip
        unzip v2.4.2.zip
        cd libwebsockets-2.4.2
        mkdir build && cd build
        cmake ..
        make && sudo make install
        sudo ldconfig
        cd ../
        wget http://mosquitto.org/files/source/mosquitto-1.5.5.tar.gz
        tar -xvzf mosquitto-1.5.5.tar.gz
        cd mosquitto-1.5.5
        sudo sed -i -- "s/WITH_WEBSOCKETS:=no/WITH_WEBSOCKETS:=yes/g" config.mk
        sudo sed -i -- "s/WITH_UUID:=yes/WITH_UUID:=no/g" config.mk
        sudo make binary
        sudo make install
        sudo adduser --system --no-create-home --disabled-login --disabled-password --group mosquitto
        sudo usermod -a -G hiascore mosquitto
        sudo rm /etc/mosquitto/mosquitto.conf
        sudo touch /etc/mosquitto/mosquitto.conf
        echo "user mosquitto" | sudo tee -a /etc/mosquitto/mosquitto.conf
        echo "allow_anonymous false" | sudo tee -a /etc/mosquitto/mosquitto.conf
        echo "autosave_interval 1800" | sudo tee -a /etc/mosquitto/mosquitto.conf
        echo "connection_messages true" | sudo tee -a /etc/mosquitto/mosquitto.conf
        echo "log_type all" | sudo tee -a /etc/mosquitto/mosquitto.conf
        echo "log_timestamp true" | sudo tee -a /etc/mosquitto/mosquitto.conf
        echo "log_dest file /hias/var/log/mosquitto/mosquitto.log" | sudo tee -a /etc/mosquitto/mosquitto.conf
        echo "persistence_location /hias/var/lib/mosquitto" | sudo tee -a /etc/mosquitto/mosquitto.conf
        echo "retained_persistence true" | sudo tee -a /etc/mosquitto/mosquitto.conf
        echo "persistent_client_expiration 1m" | sudo tee -a /etc/mosquitto/mosquitto.conf
        echo "listener 8883" | sudo tee -a /etc/mosquitto/mosquitto.conf
        echo "protocol mqtt" | sudo tee -a /etc/mosquitto/mosquitto.conf
        echo "tls_version tlsv1.2" | sudo tee -a /etc/mosquitto/mosquitto.conf
        echo "cafile /certs/fullchain.pem" | sudo tee -a /etc/mosquitto/mosquitto.conf
        echo "certfile /certs/cert.pem" | sudo tee -a /etc/mosquitto/mosquitto.conf
        echo "keyfile /certs/privkey.pem" | sudo tee -a /etc/mosquitto/mosquitto.conf
        echo "require_certificate false" | sudo tee -a /etc/mosquitto/mosquitto.conf
        echo "listener 9001" | sudo tee -a /etc/mosquitto/mosquitto.conf
        echo "protocol websockets" | sudo tee -a /etc/mosquitto/mosquitto.conf
        echo "cafile /certs/fullchain.pem" | sudo tee -a /etc/mosquitto/mosquitto.conf
        echo "certfile /certs/cert.pem" | sudo tee -a /etc/mosquitto/mosquitto.conf
        echo "keyfile /certs/privkey.pem" | sudo tee -a /etc/mosquitto/mosquitto.conf
        cd ../
        sudo git clone https://github.com/jpmens/mosquitto-auth-plug.git
        cd mosquitto-auth-plug
        sudo sed -i -- "s#my_bool#bool#g" be-mysql.c
        sudo cp config.mk.in config.mk
        sudo sed -i -- "s#MOSQUITTO_SRC =#MOSQUITTO_SRC = /hias/libraries/mosquitto/mosquitto-1.5.5#g" config.mk
        sudo sed -i -- "s#OPENSSLDIR = /usr#OPENSSLDIR = /usr/include/openssl#g" config.mk
        sudo make
        sudo cp auth-plug.so /etc/mosquitto/auth-plug.so
        echo "auth_plugin /etc/mosquitto/auth-plug.so" | sudo tee -a /etc/mosquitto/mosquitto.conf
        echo "auth_opt_backends mysql" | sudo tee -a /etc/mosquitto/mosquitto.conf
        echo "auth_opt_host localhost" | sudo tee -a /etc/mosquitto/mosquitto.conf
        echo "auth_opt_port 3306" | sudo tee -a /etc/mosquitto/mosquitto.conf
        echo "auth_opt_dbname $mysqldbname" | sudo tee -a /etc/mosquitto/mosquitto.conf
        echo "auth_opt_user $mysqldbuser" | sudo tee -a /etc/mosquitto/mosquitto.conf
        echo "auth_opt_pass ${mysqldbpass}" | sudo tee -a /etc/mosquitto/mosquitto.conf
        echo "auth_opt_userquery SELECT pw FROM mqttu WHERE uname = '%s'" | sudo tee -a /etc/mosquitto/mosquitto.conf
        echo "auth_opt_superquery SELECT COUNT(*) FROM mqttu WHERE uname = '%s' AND super = 1" | sudo tee -a /etc/mosquitto/mosquitto.conf
        echo "auth_opt_aclquery SELECT topic FROM mqttua WHERE (username = '%s') AND (rw >= %d)" | sudo tee -a /etc/mosquitto/mosquitto.conf
        cd ~/HIAS-Core
        sudo sed -i "s/host:.*/host: \"$domain\",/g" "/hias/var/www/html/iotJumpWay/Classes/iotJumpWay.js"
        sudo touch /lib/systemd/system/mosquitto.service
        echo "[Unit]" | sudo tee -a /lib/systemd/system/mosquitto.service
        echo "Description=Mosquitto MQTT Broker" | sudo tee -a /lib/systemd/system/mosquitto.service
        echo "Documentation=man:mosquitto.conf(5) man:mosquitto(8)" | sudo tee -a /lib/systemd/system/mosquitto.service
        echo "After=network.target" | sudo tee -a /lib/systemd/system/mosquitto.service
        echo "Wants=network.target" | sudo tee -a /lib/systemd/system/mosquitto.service
        echo "" | sudo tee -a /lib/systemd/system/mosquitto.service
        echo "[Service]" | sudo tee -a /lib/systemd/system/mosquitto.service
        echo "User=$USER" | sudo tee -a /lib/systemd/system/mosquitto.service
        echo "ExecStart=/usr/local/sbin/mosquitto -c /etc/mosquitto/mosquitto.conf" | sudo tee -a /lib/systemd/system/mosquitto.service
        echo "ExecReload=/bin/kill -HUP $MAINPID" | sudo tee -a /lib/systemd/system/mosquitto.service
        echo "Restart=on-failure" | sudo tee -a /lib/systemd/system/mosquitto.service
        echo "" | sudo tee -a /lib/systemd/system/mosquitto.service
        echo "[Install]" | sudo tee -a /lib/systemd/system/mosquitto.service
        echo "WantedBy=multi-user.target" | sudo tee -a /lib/systemd/system/mosquitto.service
        sudo systemctl daemon-reload
        sudo systemctl enable mosquitto.service
        sudo systemctl start mosquitto.service
        sudo systemctl status mosquitto.service
        printf -- '\033[32m SUCCESS: HIAS iotJumpWay MQTT Broker installed! \033[0m\n';
    else
        echo $FMSG;
        exit 1
    fi

    printf -- 'Now you will install the HIAS iotJumpWay AMQP Broker.\n';
    read -p "Proceed (y/n)? " proceed
    if [ "$proceed" = "Y" -o "$proceed" = "y" ]; then
        printf -- 'Installing HIAS iotJumpWay AMQP Broker.\n';
            sudo apt update -y
            sudo apt install -y rabbitmq-server
            sudo touch /etc/rabbitmq/rabbitmq.config
            conda install -c conda-forge pika
            sudo wget https://dl.bintray.com/rabbitmq/community-plugins/rabbitmq_auth_backend_http-3.6.x-61ed0a93.ez -P /usr/lib/rabbitmq/lib/rabbitmq_server-3.6.10/plugins
            sudo wget http://www.rabbitmq.com/releases/plugins/v2.4.1/mochiweb-2.4.1.ez -P /usr/lib/rabbitmq/lib/rabbitmq_server-3.6.10/plugins
            sudo rabbitmq-plugins enable rabbitmq_auth_backend_http
            sudo rabbitmq-plugins enable rabbitmq_management
            conda install -c anaconda gevent
            sudo touch /etc/rabbitmq/rabbitmq.config
            echo "%% -*- mode: erlang -*-" | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "%% ----------------------------------------------------------------------------" | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "%% Classic RabbitMQ configuration format example." | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "%% This format should be considered DEPRECATED." | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "%%" | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "%% Users of RabbitMQ 3.7.x" | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "%% or later should prefer the new style format (rabbitmq.conf)" | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "%% in combination with an advanced.config file (as needed)." | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "%%" | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "%% Related doc guide: https://www.rabbitmq.com/configure.html. See" | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "%% https://rabbitmq.com/documentation.html for documentation ToC." | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "%% ----------------------------------------------------------------------------" | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "[" | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo " {rabbit," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "  [" | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "   {tcp_listeners, []}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "   {ssl_listeners, [5671]}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "   {ssl_options, [{cacertfile,\"/certs/fullchain.pem\"}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "                  {certfile,\"/certs/cert.pem\"}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "                  {keyfile,\"/certs/privkey.pem\"}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "                  {verify,verify_peer}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "                  {fail_if_no_peer_cert,false}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "                  {versions,['tlsv1.2']}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "                  {server_name_indication, \"$domain\"}]}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "   {auth_backends, [rabbit_auth_backend_http]}]}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "    {rabbitmq_auth_backend_http," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "     [{http_method,   post}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "      {user_path,     \"https://$domain/iotJumpWay/AMQP/API/User\"}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "      {vhost_path,     \"https://$domain/iotJumpWay/AMQP/API/Vhost\"}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "      {resource_path,     \"https://$domain/iotJumpWay/AMQP/API/Resource\"}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "      {topic_path,     \"https://$domain/iotJumpWay/AMQP/API/Topic\"}" | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "  ]}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "  {rabbitmq_management," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "   [" | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "    {listener, [{port,     15671}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "     {ssl,      true}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "     {ssl_opts, [{cacertfile, \"/certs/fullchain.pem\"}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "                 {certfile, \"/certs/cert.pem\"}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "                 {keyfile, \"/certs/privkey.pem\"}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "                 {verify, verify_peer}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "                 {fail_if_no_peer_cert, false}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "                 {client_renegotiation, false}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "                 {secure_renegotiate,   true}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "                 {honor_ecc_order,      true}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "                 {honor_cipher_order,   true}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "                 {versions,['tlsv1.1', 'tlsv1.2']}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "                 {ciphers, [\"ECDHE-ECDSA-AES256-GCM-SHA384\"," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "                            \"ECDHE-RSA-AES256-GCM-SHA384\"," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "                            \"ECDH-ECDSA-AES256-GCM-SHA384\"," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "                            \"ECDH-RSA-AES256-GCM-SHA384\"," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "                            \"ECDH-RSA-AES256-SHA384\"," | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "                            \"DHE-RSA-AES256-GCM-SHA384\"" | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "                            ]}" | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "               ]}" | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "             ]}" | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "    ]}" | sudo tee -a /etc/rabbitmq/rabbitmq.config
            echo "]." | sudo tee -a /etc/rabbitmq/rabbitmq.config
            sudo systemctl restart rabbitmq-server
        printf -- '\033[32m SUCCESS: HIAS iotJumpWay AMQP Broker installed! \033[0m\n';
    else
        echo $FMSG;
        exit 1
    fi

    printf -- 'Now you will install HIASBCH.\n';
    read -p "Proceed (y/n)? " proceed
    if [ "$proceed" = "Y" -o "$proceed" = "y" ]; then
        git clone https://github.com/AIIAL/HIASBCH.git
        mkdir components/hiasbch
        mv HIASBCH/* components/hiasbch
        rm -rf HIASBCH
        sh components/hiasbch/scripts/install.sh
    else
        echo $FMSG;
        exit 1
    fi

    printf -- 'Now you will install HIASCDI.\n';
    read -p "Proceed (y/n)? " proceed
    if [ "$proceed" = "Y" -o "$proceed" = "y" ]; then
        git clone https://github.com/AIIAL/HIASCDI.git
        mkdir components/hiascdi
        mv HIASCDI/* components/hiascdi
        rm -rf HIASCDI
        sh components/hiascdi/scripts/install.sh
    else
        echo $FMSG;
        exit 1
    fi

    printf -- 'Now you will install HIASHDI.\n';
    read -p "Proceed (y/n)? " proceed
    if [ "$proceed" = "Y" -o "$proceed" = "y" ]; then
        git clone https://github.com/AIIAL/HIASHDI.git
        mkdir components/hiashdi
        mv HIASHDI/* components/hiashdi
        rm -rf HIASHDI
        sh components/hiashdi/scripts/install.sh
    else
        echo $FMSG;
        exit 1
    fi

    printf -- '\033[32m SUCCESS: Congratulations! HIAS Core installed successfully! \033[0m\n';

else
    echo $FMSG;
    exit 1
fi