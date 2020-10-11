#!/bin/bash

FMSG="- HIAS Blockchain installation terminated"

read -p "? This script will install the HIAS Blockchain on your HIAS Server. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
	echo "- Installing HIAS Blockchain"
	sudo apt-get install software-properties-common
	sudo add-apt-repository -y ppa:ethereum/ethereum
	sudo apt-get update
	sudo apt-get install ethereum
	sudo cp -a Root/fserver/ethereum/ /fserver/
	echo "- You will now create the first of the 3 required HIAS Blockchain accounts. Follow the instructions given to create the account for your core HIAS Blockchain user. Make sure you save the information given to you and keep it safe. You will need this information for configuring your HIAS Blockchain and if you lose these details you will have to create a new installation."
	geth account new --datadir /fserver/ethereum/HIAS
	echo "- You will now create the second of the 3 required HIAS Blockchain accounts. Follow the instructions given to create the account for your iotJumpWay HIAS Blockchain user. Make sure you save the information given to you and keep it safe. You will need this information for configuring your HIAS Blockchain and if you lose these details you will have to create a new installation."
	geth account new --datadir /fserver/ethereum/HIAS
	echo "- You will now create the third of the 3 required HIAS Blockchain accounts. Follow the instructions given to create the account for your personal HIAS Blockchain user. Make sure you save the information given to you and keep it safe. You will need this information for configuring your HIAS Blockchain and if you lose these details you will have to create a new installation."
	geth account new --datadir /fserver/ethereum/HIAS
	echo "- You will now install Solidity and configure/compile the HIAS Blockhain Smart Contracts."
	sudo apt-get install solc
	echo "- You now need to update the smart contracts with your HIAS Blockchain user account address."
	read -p "! Enter your HIAS Blockchain user account address: " haddress
	sudo sed -i -- "s/address haccount = YourHiasApplicationAddress;/address haccount = $haddress;/g" /fserver/ethereum/Contracts/HIAS.sol
	sudo sed -i -- "s/address haccount = YourHiasApplicationAddress;/address haccount = $haddress;/g" /fserver/ethereum/Contracts/iotJumpWay.sol
	sudo sed -i -- "s/address haccount = YourHiasApplicationAddress;/address haccount = $haddress;/g" /fserver/ethereum/Contracts/HIASPatients.sol
	echo "- You now need to compile the smart contracts, the overwrite parameter is provided by default incase you need to recompile."
	solc --abi /fserver/ethereum/Contracts/HIAS.sol -o /fserver/ethereum/Contracts/build --overwrite
	solc --bin /fserver/ethereum/Contracts/HIAS.sol -o /fserver/ethereum/Contracts/build --overwrite
	solc --abi /fserver/ethereum/Contracts/iotJumpWay.sol -o /fserver/ethereum/Contracts/build --overwrite
	solc --bin /fserver/ethereum/Contracts/iotJumpWay.sol -o /fserver/ethereum/Contracts/build --overwrite
	solc --abi /fserver/ethereum/Contracts/HIASPatients.sol -o /fserver/ethereum/Contracts/build --overwrite
	solc --bin /fserver/ethereum/Contracts/HIASPatients.sol -o /fserver/ethereum/Contracts/build --overwrite
	echo "- Now you will update HIAS configuration file."
	read -p "! Enter your HIAS domain name: " domain
	sudo sed -i 's/\"bchost\":.*/\"bchost\": \"https:\/\/'$domain'\/Blockchain\/API\/\",/g' "confs.json"
	habi=$(cat /fserver/ethereum/Contracts/build/HIAS.abi)
	sudo sed -i "s/\"authAbi\":.*/\"authAbi\": $habi,/g" "confs.json"
	iabi=$(cat /fserver/ethereum/Contracts/build/iotJumpWay.abi)
	sudo sed -i "s/\"iotAbi\":.*/\"iotAbi\": $iabi,/g" "confs.json"
	pabi=$(cat /fserver/ethereum/Contracts/build/HIASPatients.abi)
	sudo sed -i "s/\"patientsAbi\":.*/\"patientsAbi\": $pabi,/g" "confs.json"
	read -p "! Enter your HIAS Blockchain user account address: " haddress
	read -p "! Enter your HIAS Blockchain user account password: " hpass
	sudo sed -i 's/\"haddress\":.*/\"haddress\": \"'$haddress'\",/g' "confs.json"
	sudo sed -i 's/\"hpass\":.*/\"hpass\": \"'${hpass//&/\\&}'\",/g' "confs.json"
	read -p "! Enter your iotJumpWay HIAS Blockchain user account address: " iaddress
	read -p "! Enter your iotJumpWay HIAS Blockchain user account password: " ipass
	sudo sed -i 's/\"iaddress\":.*/\"iaddress\": \"'$iaddress'\",/g' "confs.json"
	sudo sed -i 's/\"ipass\":.*/\"ipass\": \"'${ipass//&/\\&}'\",/g' "confs.json"
	read -p "! Enter your personal HIAS Blockchain user account address: " paddress
	echo "- Now you will update the Genesis file."
	read -p "! Enter your HIAS Blockchain chain ID: " chainid
	sudo sed -i 's/\"chainId\":.*/\"chainId\": '$chainid',/g' "/fserver/ethereum/genesis.json"
	sudo sed -i 's/\"YourHiasApplicationAddress\"/\"'$haddress'\"/g' "/fserver/ethereum/genesis.json"
	sudo sed -i 's/\"YourIoTJumpWayApplicationAddress\"/\"'$iaddress'\"/g' "/fserver/ethereum/genesis.json"
	sudo sed -i 's/\"YourUserAddress\"/\"'$paddress'\"/g' "/fserver/ethereum/genesis.json"
	echo "- Now you will start your blockchain server so that you can deploy your contracts and complete the configuration."
	geth -datadir /fserver/ethereum/HIAS init /fserver/ethereum/genesis.json
	read -p "! Connect to your HIAS Blockchain then follow the Deploy Smart Contracts With Geth instructions in the readme for deploying the contracts once the blockchain is running. You must start the miner (miner.start()) and wait for the DAG to be generated before deploying your contracts. Are you ready (y/n)? " cmsg
	if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
		echo "- Connect to your HIAS Blockchain then follow the geth instructions in the readme for deploying the contracts once the blockchain is running."
		read -p "! Enter your HIAS Blockchain user account address: " haddress
		read -p "! Enter your HIAS Blockchain chain ID: " chainid
		read -p "! Enter your HIAS Server IP: " ip
		geth --mine --http --networkid $chainid -datadir /fserver/ethereum/HIAS --http.addr $ip --http.corsdomain "*" --miner.etherbase $haddress --http.api "eth,net,web3,personal" --allow-insecure-unlock console
		echo "- Now you will update the configuration and store the contract information in the HIAS MySQL database."
		read -p "! Enter your HIAS Smart Contract address: " hcaddress
		read -p "! Enter your HIAS Smart Contract transaction: " hctransaction
		read -p "! Enter your HIAS iotJumpWay Smart Contract address: " icaddress
		read -p "! Enter your HIAS iotJumpWay Smart Contract transaction: " ictransaction
		read -p "! Enter your HIAS Patients Smart Contract address: " pcaddress
		read -p "! Enter your HIAS Patients Smart Contract transaction: " pctransaction
		habi=$(cat /fserver/ethereum/Contracts/build/HIAS.abi)
		iabi=$(cat /fserver/ethereum/Contracts/build/iotJumpWay.abi)
		pabi=$(cat /fserver/ethereum/Contracts/build/HIASPatients.abi)
		sudo sed -i 's/\"authContract\":.*/\"authContract\": \"'$hcaddress'\",/g' "confs.json"
		sudo sed -i 's/\"iotContract\":.*/\"iotContract\": \"'$icaddress'\",/g' "confs.json"
		sudo sed -i 's/\"patientsContract\":.*/\"patientsContract\": \"'$pcaddress'\"/g' "confs.json"
		php Scripts/Installation/PHP/Blockchain.php "Contract" "$hcaddress" "HIAS" "$haddress" "$hctransaction" "$habi"
		php Scripts/Installation/PHP/Blockchain.php "Contract" "$icaddress" "HIAS" "$haddress" "$ictransaction" "$iabi"
		php Scripts/Installation/PHP/Blockchain.php "Contract" "$pcaddress" "HIAS" "$haddress" "$pctransaction" "$pabi"
		php Scripts/Installation/PHP/Blockchain.php "Config"
		echo "- Now you will install composer and the web3 PHP and Python libraries."
		pip3 install web3
		pip3 install bcrypt
		sudo apt-get install composer
		cd /fserver/var/www
		composer require sc0vu/web3.php dev-master
		cd ~/HIAS
		cp Scripts/Installation/PHP/Web3PHP/Web3.php /fserver/var/www/vendor/sc0vu/web3.php/src
		cp Scripts/Installation/PHP/Web3PHP/RequestManager.php /fserver/var/www/vendor/sc0vu/web3.php/src/RequestManagers
		cp Scripts/Installation/PHP/Web3PHP/HttpRequestManager.php /fserver/var/www/vendor/sc0vu/web3.php/src/RequestManagers
		echo "Installed HIAS Blockchain. You now need to follow the last steps in the tutorial to start the HIAS Blockchain and start mining."
		exit 0
	else
		echo $FMSG;
		exit 1
	fi
else
	echo $FMSG;
	exit 1
fi