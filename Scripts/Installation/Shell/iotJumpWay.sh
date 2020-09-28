read -p "? This script will install the iotJumpWay on your HIAS Server. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
    echo "- Installing iotJumpWay...."
    pip3 install paho-mqtt
    sudo apt install mosquitto
    sudo apt install libmosquitto-dev
    sudo mkdir -p /fserver/libraries/mosquitto
    cd /fserver/libraries/mosquittomys/
    sudo git clone https://github.com/jpmens/mosquitto-auth-plug.git
    cd mosquitto-auth-plug
    sudo cp config.mk.in config.mk
    sudo sed -i -- "s#MOSQUITTO_SRC =#MOSQUITTO_SRC = /usr/inlude#g" config.mk
    sudo sed -i -- "s#OPENSSLDIR = /usr#OPENSSLDIR = /usr/include/openssl#g" config.mk
    sudo nano config.mk
    sudo make
    sudo cp auth-plug.so /etc/mosquitto/auth-plug.so
    sudo rm /etc/mosquitto/mosquitto.conf
    sudo touch /etc/mosquitto/mosquitto.conf
    sudo mkdir -p /fserver/mosquitto/
    echo "allow_anonymous false" | sudo tee -a /etc/mosquitto/mosquitto.conf
    echo "autosave_interval 1800" | sudo tee -a /etc/mosquitto/mosquitto.conf
    echo "connection_messages true" | sudo tee -a /etc/mosquitto/mosquitto.conf
    echo "log_timestamp true" | sudo tee -a /etc/mosquitto/mosquitto.conf
    echo "persistence_file mosquitto.db" | sudo tee -a /etc/mosquitto/mosquitto.conf
    echo "persistent_client_expiration 1m" | sudo tee -a /etc/mosquitto/mosquitto.conf
    echo "retained_persistence true" | sudo tee -a /etc/mosquitto/mosquitto.conf
    echo "require_certificate false" | sudo tee -a /etc/mosquitto/mosquitto.conf
    echo "listener 1883" | sudo tee -a /etc/mosquitto/mosquitto.conf
    echo "user mosquitto" | sudo tee -a /etc/mosquitto/mosquitto.conf
    echo "protocol mqtt" | sudo tee -a /etc/mosquitto/mosquitto.conf
    echo "auth_plugin /etc/mosquitto/auth-plug.so" | sudo tee -a /etc/mosquitto/mosquitto.conf
    echo "auth_opt_host localhost" | sudo tee -a /etc/mosquitto/mosquitto.conf
    echo "auth_opt_port 3306" | sudo tee -a /etc/mosquitto/mosquitto.conf
    read -p "! Enter your server database name: " sdn
    read -p "! Enter your server database user name: " sdu
    read -p "! Enter your server user password: " sdup
    echo "auth_opt_dbname $sdn" | sudo tee -a /etc/mosquitto/mosquitto.conf
    echo "auth_opt_user $sdu" | sudo tee -a /etc/mosquitto/mosquitto.conf
    echo "auth_opt_pass $sdup" | sudo tee -a /etc/mosquitto/mosquitto.conf
    echo "auth_opt_userquery SELECT pw FROM mqttu WHERE uname = '%s'" | sudo tee -a /etc/mosquitto/mosquitto.conf
    echo "auth_opt_superquery SELECT COUNT(*) FROM mqttu WHERE uname = '%s' AND super = 1" | sudo tee -a /etc/mosquitto/mosquitto.conf
    echo "auth_opt_aclquery SELECT topic FROM mqttua WHERE (uname = '%s') AND (rw >= %d)" | sudo tee -a /etc/mosquitto/mosquitto.conf
    echo "listener 9001" | sudo tee -a /etc/mosquitto/mosquitto.conf
    echo "protocol websockets" | sudo tee -a /etc/mosquitto/mosquitto.conf
    nano /etc/mosquitto/mosquitto.conf
    cd ../
    sudo systemctl status mosquitto.service
    echo "- Installed iotJumpWay!";
    exit 0
else
    echo "- iotJumpWay installation terminated!";
    exit 1