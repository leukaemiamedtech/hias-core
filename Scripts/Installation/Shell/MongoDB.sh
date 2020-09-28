
FMSG="- MongoDB installation terminated"

read -p "? This script will install MongoDB on your HIAS Server. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
	echo "- Installing MongoDB"
	wget -qO - https://www.mongodb.org/static/pgp/server-4.2.asc | sudo apt-key add -
	sudo apt-get install gnupg (If required)
	echo "deb [ arch=amd64,arm64 ] https://repo.mongodb.org/apt/ubuntu bionic/mongodb-org/4.2 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-4.2.list
	sudo apt-get update
	sudo apt-get install -y mongodb-org
	sudo apt-get install php-mongodb
	sudo systemctl enable mongod.service
	sudo systemctl start mongod
	sudo systemctl status mongod
	echo "- The MongoDB console will now open, you will need to follow the steps in the Mongo Database section of the installation file to create your database credentials."
	mongo
	echo "- Now you need to update the configuration files."
	sudo nano confs.json
	sudo nano /fserver/var/www/Classes/Core/confs.json
	exit 0
else
	echo $FMSG;
	exit 1
fi