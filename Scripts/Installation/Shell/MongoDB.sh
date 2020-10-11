
#!/bin/bash
FMSG="- MongoDB installation terminated"

read -p "? This script will install MongoDB on your HIAS Server. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
	echo "- Installing MongoDB"
	wget -qO - https://www.mongodb.org/static/pgp/server-4.2.asc | sudo apt-key add -
	sudo apt install gnupg
	echo "deb [ arch=amd64,arm64 ] https://repo.mongodb.org/apt/ubuntu bionic/mongodb-org/4.2 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-4.2.list
	sudo apt update
	sudo apt install -y mongodb-org
	sudo apt install php-mongodb
	pip3 install pymongo
	sudo systemctl enable mongod.service
	sudo systemctl start mongod
	sudo systemctl status mongod
	sudo systemctl restart php7.2-fpm
	read -p "! The MongoDB console will now open, you will need to follow the steps in the Mongo Database section of the installation file to create your database credentials. Are you ready (y/n)? " cmsg
	if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
		mongo
		read -p "! Enter your MongoDB database name: " dbn
		read -p "! Enter your MongoDB database username: " dbu
		read -p "! Enter your MongoDB database password: " dbp
		sudo sed -i "s/\"mdb\":.*/\"mdb\": \"$dbn\",/g" "confs.json"
		sudo sed -i "s/\"mdbu\":.*/\"mdbu\": \"$dbu\",/g" "confs.json"
		sudo sed -i "s/\"mdbp\":.*/\"mdbp\": \"${dbp//&/\\&}\",/g" "confs.json"
		sudo sed -i "s/\"mdbname\":.*/\"mdbname\": \"$dbn\",/g" "/fserver/var/www/Classes/Core/confs.json"
		sudo sed -i "s/\"mdbusername\":.*/\"mdbusername\": \"$dbu\",/g" "/fserver/var/www/Classes/Core/confs.json"
		sudo sed -i "s/\"mdbpassword\":.*/\"mdbpassword\": \"${dbn//&/\\&}\",/g" "/fserver/var/www/Classes/Core/confs.json"
		echo "- Installed MongoDB and configured database";
		exit 0
	else
		echo $FMSG;
		exit 1
	fi
else
	echo $FMSG;
	exit 1
fi