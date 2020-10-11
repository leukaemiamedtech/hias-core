#!/bin/bash
read -p "? This script will install the iotJumpWay on your HIAS Server. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
	echo "- Installing iotJumpWay...."
	sudo apt install python3-pip
	sudo apt install build-essential libc-ares-dev uuid-dev libssl-dev openssl libssl-dev libcurl4-openssl-dev
	sudo apt install libmosquitto-dev
	sudo apt install default-libmysqlclient-dev
	sudo apt install libssl-dev
	sudo apt install libcurl4-openssl-dev
	sudo apt install perl-doc uuid-dev bsdutils gcc g++ git make dialog libssl-dev libc-ares-dev libcurl4-openssl-dev libmysqlclient-dev libwrap0 libwrap0-dev libwebsockets-dev uthash-dev
	pip3 install paho-mqtt
	read -p "! Enter your HIAS domain name: " domain
	read -p "! Enter your server login user: " user
	sudo mkdir -p /fserver/libraries/mosquitto
	sudo mkdir -p /fserver/libraries/mosquitto/certs
	sudo mkdir -p /fserver/var/log/mosquitto
	sudo touch /fserver/var/log/mosquitto/mosquitto.log
	sudo chown -R $user:$user /fserver/var/log/mosquitto/mosquitto.log
	sudo cp /etc/letsencrypt/live/$domain/fullchain.pem /fserver/libraries/mosquitto/certs/
	sudo cp /etc/letsencrypt/live/$domain/cert.pem /fserver/libraries/mosquitto/certs/
	sudo cp /etc/letsencrypt/live/$domain/privkey.pem /fserver/libraries/mosquitto/certs/
	sudo chown -R $user:$user /fserver/libraries/mosquitto -R
	cd /fserver/libraries/mosquitto
	wget http://mosquitto.org/files/source/mosquitto-1.5.5.tar.gz
	tar -xvzf mosquitto-1.5.5.tar.gz
	cd mosquitto-1.5.5
	sudo sed -i -- "s/WITH_WEBSOCKETS:=no/WITH_WEBSOCKETS:=yes/g" config.mk
	sudo sed -i -- "s/WITH_UUID:=yes/WITH_UUID:=no/g" config.mk
	sudo make binary
	sudo make install
	sudo rm /etc/mosquitto/mosquitto.conf
	sudo touch /etc/mosquitto/mosquitto.conf
	echo "user mosquitto" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "allow_anonymous false" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "autosave_interval 1800" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "connection_messages true" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "log_type all" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "log_timestamp true" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "log_dest file /fserver/var/log/mosquitto/mosquitto.log" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "persistence_location /fserver/var/lib/mosquitto" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "retained_persistence true" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "persistent_client_expiration 1m" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "listener 8883" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "protocol mqtt" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "tls_version tlsv1.2" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "cafile /fserver/libraries/mosquitto/certs/fullchain.pem" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "certfile /fserver/libraries/mosquitto/certs/cert.pem" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "keyfile /fserver/libraries/mosquitto/certs/privkey.pem" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "require_certificate false" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "listener 9001" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "protocol websockets" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "cafile /fserver/libraries/mosquitto/certs/fullchain.pem" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "certfile /fserver/libraries/mosquitto/certs/cert.pem" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "keyfile /fserver/libraries/mosquitto/certs/privkey.pem" | sudo tee -a /etc/mosquitto/mosquitto.conf
	cd ../
	sudo git clone https://github.com/jpmens/mosquitto-auth-plug.git
	cd mosquitto-auth-plug
	sudo cp config.mk.in config.mk
	sudo sed -i -- "s#MOSQUITTO_SRC =#MOSQUITTO_SRC = /fserver/libraries/mosquitto/mosquitto-1.5.5#g" config.mk
	sudo sed -i -- "s#OPENSSLDIR = /usr#OPENSSLDIR = /usr/include/openssl#g" config.mk
	sudo make
	sudo cp auth-plug.so /etc/mosquitto/auth-plug.so
	echo "auth_plugin /etc/mosquitto/auth-plug.so" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "auth_opt_backends mysql" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "auth_opt_host localhost" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "auth_opt_port 3306" | sudo tee -a /etc/mosquitto/mosquitto.conf
	read -p "! Enter your server database name: " sdn
	read -p "! Enter your server database user name: " sdu
	read -p "! Enter your server user password: " sdup
	echo "auth_opt_dbname $sdn" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "auth_opt_user $sdu" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "auth_opt_pass ${sdup//&/\\&}" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "auth_opt_userquery SELECT pw FROM mqttu WHERE uname = '%s'" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "auth_opt_superquery SELECT COUNT(*) FROM mqttu WHERE uname = '%s' AND super = 1" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "auth_opt_aclquery SELECT topic FROM mqttua WHERE (username = '%s') AND (rw >= %d)" | sudo tee -a /etc/mosquitto/mosquitto.conf
	cd ~/HIAS
	sudo sed -i "s/\"host\":.*/\"host\": \"$domain\",/g" "confs.json"
	sudo sed -i "s/host:.*/host: \"$domain\",/g" "/fserver/var/www/html/iotJumpWay/Classes/iotJumpWay.js"
	sudo touch /lib/systemd/system/mosquitto.service
	echo "[Unit]" | sudo tee -a /lib/systemd/system/mosquitto.service
	echo "Description=Mosquitto MQTT Broker" | sudo tee -a /lib/systemd/system/mosquitto.service
	echo "Documentation=man:mosquitto.conf(5) man:mosquitto(8)" | sudo tee -a /lib/systemd/system/mosquitto.service
	echo "After=network.target" | sudo tee -a /lib/systemd/system/mosquitto.service
	echo "Wants=network.target" | sudo tee -a /lib/systemd/system/mosquitto.service
	echo "" | sudo tee -a /lib/systemd/system/mosquitto.service
	echo "[Service]" | sudo tee -a /lib/systemd/system/mosquitto.service
	echo "User=$user" | sudo tee -a /lib/systemd/system/mosquitto.service
	echo "ExecStart=/usr/local/sbin/mosquitto -c /etc/mosquitto/mosquitto.conf" | sudo tee -a /lib/systemd/system/mosquitto.service
	echo "ExecReload=/bin/kill -HUP $MAINPID" | sudo tee -a /lib/systemd/system/mosquitto.service
	echo "Restart=on-failure" | sudo tee -a /lib/systemd/system/mosquitto.service
	echo "" | sudo tee -a /lib/systemd/system/mosquitto.service
	echo "[Install]" | sudo tee -a /lib/systemd/system/mosquitto.service
	echo "WantedBy=multi-user.target" | sudo tee -a /lib/systemd/system/mosquitto.service
	sudo sed -i -- "s/ExecStart=\/usr\/bin\/certbot -q renew/ExecStart=\/usr\/bin\/certbot -q renew --deploy-hook \"\/home\/$user\/HIAS\/Scripts\/System\/Certbot.sh\"/g" /lib/systemd/system/certbot.service
	sudo sed -i -- "s/user:user/$user:$user/g" Scripts/System/Certbot.sh
	chmod u+x Scripts/System/Certbot.sh
	sudo systemctl daemon-reload
	sudo systemctl enable mosquitto.service
	sudo systemctl start mosquitto.service
	sudo systemctl status mosquitto.service
	sudo systemctl restart certbot.service
	echo "- Installed iotJumpWay!";
	exit 0
else
	echo "- iotJumpWay installation terminated!";
	exit 1
fi