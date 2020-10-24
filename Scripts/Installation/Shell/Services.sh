
#!/bin/bash
FMSG="- Services installation terminated"

read -p "? This script will install the system services on your HIAS Server. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
	echo "- Installing Services"
	sudo touch /lib/systemd/system/ContextBroker.service
	echo "[Unit]" | sudo tee -a /lib/systemd/system/ContextBroker.service
	echo "Description=iotJumpWay Context Broker Service" | sudo tee -a /lib/systemd/system/ContextBroker.service
	echo "After=multi-user.target" | sudo tee -a /lib/systemd/system/ContextBroker.service
	echo "" | sudo tee -a /lib/systemd/system/ContextBroker.service
	echo "[Service]" | sudo tee -a /lib/systemd/system/ContextBroker.service
	echo "User=$USER" | sudo tee -a /lib/systemd/system/ContextBroker.service
	echo "Type=simple" | sudo tee -a /lib/systemd/system/ContextBroker.service
	echo "ExecStart=/usr/bin/python3 /home/$USER/HIAS/Context/Broker.py" | sudo tee -a /lib/systemd/system/ContextBroker.service
	echo "" | sudo tee -a /lib/systemd/system/ContextBroker.service
	echo "[Install]" | sudo tee -a /lib/systemd/system/ContextBroker.service
	echo "WantedBy=multi-user.target" | sudo tee -a /lib/systemd/system/ContextBroker.service
	echo "" | sudo tee -a /lib/systemd/system/ContextBroker.service

	sudo touch /lib/systemd/system/MQTT.service
	echo "[Unit]" | sudo tee -a /lib/systemd/system/MQTT.service
	echo "Description=iotJumpWay MQTT Service" | sudo tee -a /lib/systemd/system/MQTT.service
	echo "After=multi-user.target" | sudo tee -a /lib/systemd/system/MQTT.service
	echo "" | sudo tee -a /lib/systemd/system/MQTT.service
	echo "[Service]" | sudo tee -a /lib/systemd/system/MQTT.service
	echo "User=$USER" | sudo tee -a /lib/systemd/system/MQTT.service
	echo "Type=simple" | sudo tee -a /lib/systemd/system/MQTT.service
	echo "ExecStart=/usr/bin/python3 /home/$USER/HIAS/Agents/MQTT.py" | sudo tee -a /lib/systemd/system/MQTT.service
	echo "" | sudo tee -a /lib/systemd/system/MQTT.service
	echo "[Install]" | sudo tee -a /lib/systemd/system/MQTT.service
	echo "WantedBy=multi-user.target" | sudo tee -a /lib/systemd/system/MQTT.service
	echo "" | sudo tee -a /lib/systemd/system/MQTT.service

	sudo touch /lib/systemd/system/AMQP.service
	echo "[Unit]" | sudo tee -a /lib/systemd/system/AMQP.service
	echo "Description=iotJumpWay AMQP Service" | sudo tee -a /lib/systemd/system/AMQP.service
	echo "After=multi-user.target" | sudo tee -a /lib/systemd/system/AMQP.service
	echo "" | sudo tee -a /lib/systemd/system/AMQP.service
	echo "[Service]" | sudo tee -a /lib/systemd/system/AMQP.service
	echo "User=$USER" | sudo tee -a /lib/systemd/system/AMQP.service
	echo "Type=simple" | sudo tee -a /lib/systemd/system/AMQP.service
	echo "ExecStart=/usr/bin/python3 /home/$USER/HIAS/Agents/AMQP.py" | sudo tee -a /lib/systemd/system/AMQP.service
	echo "" | sudo tee -a /lib/systemd/system/AMQP.service
	echo "[Install]" | sudo tee -a /lib/systemd/system/AMQP.service
	echo "WantedBy=multi-user.target" | sudo tee -a /lib/systemd/system/AMQP.service
	echo "" | sudo tee -a /lib/systemd/system/AMQP.service

	chmod u+x Scripts/System/Security.sh
	sudo touch /lib/systemd/system/Security.service
	echo "[Unit]" | sudo tee -a /lib/systemd/system/Security.service
	echo "Description=Security Service" | sudo tee -a /lib/systemd/system/Security.service
	echo "After=multi-user.target" | sudo tee -a /lib/systemd/system/Security.service
	echo "" | sudo tee -a /lib/systemd/system/Security.service
	echo "[Service]" | sudo tee -a /lib/systemd/system/Security.service
	echo "User=$USER" | sudo tee -a /lib/systemd/system/Security.service
	echo "Type=simple" | sudo tee -a /lib/systemd/system/Security.service
	echo "ExecStart=/home/$USER/HIAS/Scripts/System/Security.sh" | sudo tee -a /lib/systemd/system/Security.service
	echo "" | sudo tee -a /lib/systemd/system/Security.service
	echo "[Install]" | sudo tee -a /lib/systemd/system/Security.service
	echo "WantedBy=multi-user.target" | sudo tee -a /lib/systemd/system/Security.service
	echo "" | sudo tee -a /lib/systemd/system/Security.service

	sudo touch /lib/systemd/system/Replenish.service
	echo "[Unit]" | sudo tee -a /lib/systemd/system/Replenish.service
	echo "Description=Replenish Service" | sudo tee -a /lib/systemd/system/Replenish.service
	echo "After=multi-user.target" | sudo tee -a /lib/systemd/system/Replenish.service
	echo "" | sudo tee -a /lib/systemd/system/Replenish.service
	echo "[Service]" | sudo tee -a /lib/systemd/system/Replenish.service
	echo "User=$USER" | sudo tee -a /lib/systemd/system/Replenish.service
	echo "Type=simple" | sudo tee -a /lib/systemd/system/Replenish.service
	echo "ExecStart=/usr/bin/python3 /home/$USER/HIAS/Services/Replenish.py" | sudo tee -a /lib/systemd/system/Replenish.service
	echo "" | sudo tee -a /lib/systemd/system/Replenish.service
	echo "[Install]" | sudo tee -a /lib/systemd/system/Replenish.service
	echo "WantedBy=multi-user.target" | sudo tee -a /lib/systemd/system/Replenish.service
	echo "" | sudo tee -a /lib/systemd/system/Replenish.service

	sudo systemctl enable ContextBroker.service
	sudo systemctl start ContextBroker.service
	sudo systemctl enable MQTT.service
	sudo systemctl start MQTT.service
	sudo systemctl enable AMQP.service
	sudo systemctl start AMQP.service
	sudo systemctl enable Security.service
	sudo systemctl start Security.service
	sudo systemctl enable Replenish.service
	sudo systemctl start Replenish.service

	echo "- Installed services!";
	exit 0
else
	echo $FMSG;
	exit 1
fi