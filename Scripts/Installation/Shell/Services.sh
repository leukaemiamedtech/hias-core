#!/bin/bash

read -p "? This script will install your server services. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
    echo "- Installing server services...."
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

	sudo sed -i -- "s#YourUser#$username#g" Scripts/System/Camera.sh
	chmod u+x Scripts/System/Camera.sh
	sudo gpasswd -d $usernam video

	sudo touch /lib/systemd/system/GeniSysAI.service
	echo "[Unit]" | sudo tee -a /lib/systemd/system/GeniSysAI.service
	echo "Description=GeniSysAI Service" | sudo tee -a /lib/systemd/system/GeniSysAI.service
	echo "After=multi-user.target" | sudo tee -a /lib/systemd/system/GeniSysAI.service
	echo "" | sudo tee -a /lib/systemd/system/GeniSysAI.service
	echo "[Service]" | sudo tee -a /lib/systemd/system/GeniSysAI.service
	echo "User=$username" | sudo tee -a /lib/systemd/system/GeniSysAI.service
	echo "Type=simple" | sudo tee -a /lib/systemd/system/GeniSysAI.service
	echo "ExecStart=/home/$username/HIAS/Scripts/System/Camera.sh" | sudo tee -a /lib/systemd/system/GeniSysAI.service
	echo "" | sudo tee -a /lib/systemd/system/GeniSysAI.service
	echo "[Install]" | sudo tee -a /lib/systemd/system/GeniSysAI.service
	echo "WantedBy=multi-user.target" | sudo tee -a /lib/systemd/system/GeniSysAI.service
    echo "- Installed server services!";
    exit 0
else
    echo "- Server services installation terminated!";
    exit 1
fi
