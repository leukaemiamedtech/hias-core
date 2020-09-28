
FMSG="- Services installation terminated"

read -p "? This script will install MongoDB on your HIAS Server. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
	echo "- Installing Services"
	read -p "! Enter the username you use to login to your device: " username
	sudo touch /lib/systemd/system/iotJumpWay.service
	echo "[Unit]" | sudo tee -a /lib/systemd/system/iotJumpWay.service
	echo "Description=iotJumpWay Service" | sudo tee -a /lib/systemd/system/iotJumpWay.service
	echo "After=multi-user.target" | sudo tee -a /lib/systemd/system/iotJumpWay.service
	echo "" | sudo tee -a /lib/systemd/system/iotJumpWay.service
	echo "[Service]" | sudo tee -a /lib/systemd/system/iotJumpWay.service
	echo "User=$username" | sudo tee -a /lib/systemd/system/iotJumpWay.service
	echo "Type=simple" | sudo tee -a /lib/systemd/system/iotJumpWay.service
	echo "ExecStart=/usr/bin/python3 /home/$username/HIAS/iotJumpWay.py" | sudo tee -a /lib/systemd/system/iotJumpWay.service
	echo "" | sudo tee -a /lib/systemd/system/iotJumpWay.service
	echo "[Install]" | sudo tee -a /lib/systemd/system/iotJumpWay.service
	echo "WantedBy=multi-user.target" | sudo tee -a /lib/systemd/system/iotJumpWay.service

	sudo sed -i -- "s#YourUser#$username#g" Scripts/System/Security.sh
	chmod u+x Scripts/System/Security.sh

	sudo touch /lib/systemd/system/Security.service
	echo "[Unit]" | sudo tee -a /lib/systemd/system/Security.service
	echo "Description=Security Service" | sudo tee -a /lib/systemd/system/Security.service
	echo "After=multi-user.target" | sudo tee -a /lib/systemd/system/Security.service
	echo "" | sudo tee -a /lib/systemd/system/Security.service
	echo "[Service]" | sudo tee -a /lib/systemd/system/Security.service
	echo "User=$username" | sudo tee -a /lib/systemd/system/Security.service
	echo "Type=simple" | sudo tee -a /lib/systemd/system/Security.service
	echo "ExecStart=/home/$username/HIAS/Scripts/System/Security.sh" | sudo tee -a /lib/systemd/system/Security.service
	echo "" | sudo tee -a /lib/systemd/system/Security.service
	echo "[Install]" | sudo tee -a /lib/systemd/system/Security.service
	echo "WantedBy=multi-user.target" | sudo tee -a /lib/systemd/system/Security.service

	sudo touch /lib/systemd/system/Replenish.service
	echo "[Unit]" | sudo tee -a /lib/systemd/system/Replenish.service
	echo "Description=Replenish Service" | sudo tee -a /lib/systemd/system/Replenish.service
	echo "After=multi-user.target" | sudo tee -a /lib/systemd/system/Replenish.service
	echo "" | sudo tee -a /lib/systemd/system/Replenish.service
	echo "[Service]" | sudo tee -a /lib/systemd/system/Replenish.service
	echo "User=$username" | sudo tee -a /lib/systemd/system/Replenish.service
	echo "Type=simple" | sudo tee -a /lib/systemd/system/Replenish.service
	echo "ExecStart=/usr/bin/python3 /home/$username/HIAS/Replenish.py" | sudo tee -a /lib/systemd/system/Replenish.service
	echo "" | sudo tee -a /lib/systemd/system/Replenish.service
	echo "[Install]" | sudo tee -a /lib/systemd/system/Replenish.service
	echo "WantedBy=multi-user.target" | sudo tee -a /lib/systemd/system/Replenish.service
	exit 0
else
	echo $FMSG;
	exit 1
fi