# Peter Moss Leukemia AI Research
## Hospital Intelligent Automation System
### Installation Guide
[![Hospital Intelligent Automation System](../Media/Images/HIAS-Hospital-Intelligent-Automation-System.png)](https://github.com/LeukemiaAiResearch/HIAS)

# Table Of Contents

- [Introduction](#introduction)
- [Required Hardware](#required-hardware)
- [Prerequisites](#prerequisites)
  - [Ubuntu Server 18.04.4 LTS](#ubuntu-server-18044-lts)
  - [VirtualBox](#virtualbox)
  - [Domain Name](#domain-name)
  - [Port Forwarding](#port-forwarding)
  - [Server Security](#server-security)
    - [Remote User](#remote-user)
    - [SSH Access](#ssh-access)
      - [Tips](#tips)
  - [Attach Hard-Drive](#attach-hard-drive)
  - [Clone The Repository](#clone-the-repository)
    - [Developer Forks](#developer-forks)
- [Installation](#installation)
  - [Easy Install (Recommended)](#easy-install-recommended)
  - [Manual Install](#manual-install)
    - [UFW Firewall](#ufw-firewall)
    - [Fail2Ban](#fail2ban)
    - [NGINX](#nginx)
    - [Let's Encrypt](#lets-encrypt)
    - [PHP](#php)
    - [MySql](#mysql)
    - [phpMyAdmin](#phpmyadmin)
    - [Mongo Database](#mongo-database)
    - [SSL Security](#ssl-security)
    - [File Server](#file-server)
    - [Private Ethereum Blockchain](#private-ethereum-blockchain)
      - [Deploy Smart Contracts With Geth](#deploy-smart-contracts-with-geth)
    - [iotJumpWay MQTT Broker](#iotjumpway-mqtt-broker)
    - [iotJumpWay AMQP Broker](#iotjumpway-amqp-broker)
    - [iotJumpWay Location and Applications](#iotjumpway-location-and-applications)
    - [Create Admin User](#create-admin-user)
    - [HIAS Server Services](#hias-server-services)
    - [Finalize Server Settings](#finalize-server-settings)
    - [TassAI (Computer Vision)](#tassai-computer-vision)
      - [OpenVINO 2020.3](openvino-20203)
    - [Install COVID-19 Data Analysis System](#install-covid-19-data-analysis-system)
- [Login To Your Server UI](#login-to-server-ui)
- [HIAS IoT Network](hias-iot-network)
- [Contributing](#contributing)
    - [Contributors](#contributors)
- [Versioning](#versioning)
- [License](#license)
- [Bugs/Issues](#bugs-issues)

# Introduction
The following guide will take you through setting up and installing the  [Hospital Intelligent Automation System](https://github.com/LeukemiaAiResearch/HIAS " Hospital Intelligent Automation System").

&nbsp;

# Required Hardware
For this tutorial I am using a [UP2 AI Vision Devkit](https://up-board.org/upkits/up-squared-ai-vision-kit/ "UP2 AI Vision Devkit") and a 1.5TB hard-drive for the core server hardware, but you can use any linux machine and hard-drive. For real-world usage in medical centers and hospitals it is suggested to use a device with more resources.

![Required Hardware](../Media/Images/HIAS-Hardware.png)

- 1 x Linux machine (Server)
- 1 x 1TB (Or more) HDD
- 1 x Webcam

&nbsp;

# Prerequisites
Before you can continue with this tutorial. Please ensure you have completed all of the following prerequisites.

## Ubuntu Server 18.04.4 LTS
For this project, the operating system of choice is [Ubuntu Server 18.04.4 LTS](https://ubuntu.com/download/server "Ubuntu Server 18.04.4 LTS"). To get your operating system installed you can follow the [Create a bootable USB stick on Ubuntu](https://tutorials.ubuntu.com/tutorial/tutorial-create-a-usb-stick-on-ubuntu#0 "Create a bootable USB stick on Ubuntu") tutorial.

**__The server installation can be run on an existing installation of Ubuntu, however we recommend using a fresh installation.__**

## VirtualBox
If you would like to install your HIAS server on a Virtual Machine, you can use the [VirtualBox](VirtualBox.md) installation guide.

## Domain Name
Now is as good a time as any to sort out and configure a domain name. You need to have your domain already hosted on a hosting account, from there edit the DNS zone by adding an A record to your public IP, for this you need a static IP or IP software that will update the IP in the DNZ Zone each time it changes. You add your IP as an A record and save your DNS Zone.

## Port Forwarding
Now you have your domain pointing to your public IP, it is time to add a port forward, traffic to your network will be coming from port 80 (insecure) and secure. Although Nginx will bounce the insecure traffic to port 443, you still need to add a port forward for port 80 as well as 443.

How you will do this will vary, but you need to find the area of your router that allows you to add port forwards. Then add one port forward for incoming insecure traffic (port 80) to port 80 on your server's IP, and one for secure traffic (port 443) to port 443 on server's IP. Both incoming ports should be forwarded to the same port on your server.

This will open the HTTP/HTTPS ports on your router and forward the traffic to your server. In the case someone tries to access using insecure protocol (http - port 80) they will be automatically be sent to the secure port of the server (https - 443)

## Server Security
First you will harden your server security.

### Remote User
You will create a new user for accessing your server remotely. Use the following commands to set up a new user for your machine. Follow the instructions provided and make sure you use a secure password.
```
sudo adduser YourUsername
```
Now grant sudo priveleges to the user:
```
usermod -aG sudo YourUsername
```
Now open a new terminal and login to your server using the new credentials you set up.
```
ssh YourNewUser@YourServerIP
```

### SSH Access
Now let's beef up server secuirty. Use the following command to set up your public and private keys. Make sure you carry out this step on your development machine, **not** on your server.

#### Tips
- Hit enter to confirm the default file.
- Hit enter twice to skip the password (Optionalm, you can use a password if you like).
```
ssh-keygen
```
You should end up with a screen like this:
```
Generating public/private rsa key pair.
Enter file in which to save the key (/home/genisys/.ssh/id_rsa):
Enter passphrase (empty for no passphrase):
Enter same passphrase again:
Your identification has been saved in /home/genisys/.ssh/id_rsa.
Your public key has been saved in /home/genisys/.ssh/id_rsa.pub.
The key fingerprint is:
SHA256:5BYJMomxATmanduT3/d1CPKaFm+pGEIqpJJ5Z3zXCPM genisys@genisyslprt
The key's randomart image is:
+---[RSA 2048]----+
|.oooo..          |
|o .o.o . .       |
|.+..    +        |
|o o    o .       |
|  .o .+ S . .    |
| =..+o = o.o . . |
|= o =oo.E .o..o .|
|.. + ..o.ooo+. . |
|        .o++.    |
+----[SHA256]-----+
```
Now you are going to copy your key to the server:
```
ssh-copy-id YourNewUser@YourServerIP
```
Once you enter your password for the new user account, your key will be saved on the server. Now try and login to the server again in a new terminal, you should log straight in without having to enter a password.
```
ssh YourNewUser@YourServerIP
```
Finally you will turn off password authentication for login. Use the following command to edit the ssh configuration.
```
sudo nano /etc/ssh/sshd_config
```
Change the following:
```
#PasswordAuthentication yes
```
To:
```
PasswordAuthentication no
```
Then restart ssh:
```
sudo systemctl restart ssh
```
_If you are using ssh to do the above steps keep your current terminal connected._ Open a new terminal, attempt to login to your server. If you can login then the above steps were successful.

The remainder of this tutorial assumes you are logged into your server. From your development machine, connect to your server using ssh or open your local terminal if working directly on your server machine.

```
ssh YourUser@YourServerIP
```

## Attach Hard-Drive
Now you will attach the hard-drive to your server so that you can use it for the database and file system for your server.

Use the following command to create the **fserver** directory which will be the core directory for your HIAS installation.

```
sudo mkdir /fserver
```

**IF YOU ARE NOT GOING TO USE AN EXTERNAL HDD YOU CAN SKIP TO THE [Clone the repository](#clone-the-repository) STEP**

First off, make sure you have plugged your hard-drive into the server machine, then use the following commands:
```
sudo fdisk -l
```
For me this gives the following output:
```
Disk /dev/loop1: 93.8 MiB, 98336768 bytes, 192064 sectors
Units: sectors of 1 * 512 = 512 bytes
Sector size (logical/physical): 512 bytes / 512 bytes
I/O size (minimum/optimal): 512 bytes / 512 bytes


Disk /dev/loop2: 93.9 MiB, 98484224 bytes, 192352 sectors
Units: sectors of 1 * 512 = 512 bytes
Sector size (logical/physical): 512 bytes / 512 bytes
I/O size (minimum/optimal): 512 bytes / 512 bytes


Disk /dev/mmcblk0: 58.2 GiB, 62537072640 bytes, 122142720 sectors
Units: sectors of 1 * 512 = 512 bytes
Sector size (logical/physical): 512 bytes / 512 bytes
I/O size (minimum/optimal): 512 bytes / 512 bytes
Disklabel type: gpt
Disk identifier: 077E0353-99FE-4490-AEDA-040F34765A69

Device           Start       End   Sectors  Size Type
/dev/mmcblk0p1    2048   1050623   1048576  512M EFI System
/dev/mmcblk0p2 1050624   3147775   2097152    1G Linux filesystem
/dev/mmcblk0p3 3147776 122140671 118992896 56.8G Linux filesystem


Disk /dev/mmcblk0boot1: 4 MiB, 4194304 bytes, 8192 sectors
Units: sectors of 1 * 512 = 512 bytes
Sector size (logical/physical): 512 bytes / 512 bytes
I/O size (minimum/optimal): 512 bytes / 512 bytes


Disk /dev/mmcblk0boot0: 4 MiB, 4194304 bytes, 8192 sectors
Units: sectors of 1 * 512 = 512 bytes
Sector size (logical/physical): 512 bytes / 512 bytes
I/O size (minimum/optimal): 512 bytes / 512 bytes


Disk /dev/mapper/dm_crypt-0: 56.8 GiB, 60922265600 bytes, 118988800 sectors
Units: sectors of 1 * 512 = 512 bytes
Sector size (logical/physical): 512 bytes / 512 bytes
I/O size (minimum/optimal): 512 bytes / 512 bytes


Disk /dev/mapper/ubuntu--vg-ubuntu--lv: 4 GiB, 4294967296 bytes, 8388608 sectors
Units: sectors of 1 * 512 = 512 bytes
Sector size (logical/physical): 512 bytes / 512 bytes
I/O size (minimum/optimal): 512 bytes / 512 bytes


Disk /dev/sda: 1.4 TiB, 1500267937792 bytes, 2930210816 sectors
Units: sectors of 1 * 512 = 512 bytes
Sector size (logical/physical): 512 bytes / 512 bytes
I/O size (minimum/optimal): 512 bytes / 512 bytes
Disklabel type: gpt
Disk identifier: 1DEAF6E4-963A-42E9-BFB9-00B3CD018497

Device     Start        End    Sectors  Size Type
/dev/sda1   2048 2930208767 2930206720  1.4T Microsoft basic data
```
Here you can see that my 1.5TB hard-drive partition is called **/dev/sda1**. Now you need to find the UUID of the partition:
```
sudo blkid
```
Should give you a similar output to:
```
/dev/mapper/dm_crypt-0: UUID="OLgaQg-GIKS-H7zM-U8iY-z3XQ-3JBS-0QWnk1" TYPE="LVM2_member"
/dev/mapper/ubuntu--vg-ubuntu--lv: UUID="8e379584-4ce3-4bf2-a81f-2cc5cfb919e6" TYPE="ext4"
/dev/mmcblk0p1: UUID="29BD-DE91" TYPE="vfat" PARTUUID="1555a088-82fb-4a55-af71-518011e32c8d"
/dev/mmcblk0p2: UUID="8568e49f-2bc9-4f00-ab65-40a8fe073662" TYPE="ext4" PARTUUID="bcd6fc23-5ce5-4057-bab2-1fced3066285"
/dev/mmcblk0p3: UUID="05355535-ad22-4979-98fd-13e585639c4e" TYPE="crypto_LUKS" PARTUUID="835c6449-5ad8-4993-84a1-f2f25d93eb71"
/dev/loop1: TYPE="squashfs"
/dev/loop2: TYPE="squashfs"
/dev/mmcblk0: PTUUID="077e0353-99fe-4490-aeda-040f34765a69" PTTYPE="gpt"
/dev/sda1: LABEL="GeniSys Data" UUID="B470EB4570EB0CC4" TYPE="ntfs" PARTLABEL="Elements" PARTUUID="07a78460-f599-4e31-a8ce-0b5b8b03811f"
```
If you look for my partition name, **/dev/sda1**, you will find the UUID. In this case: **B470EB4570EB0CC4**.

Now create a mount point, a user group and then add the user you want to belong to this group:
```
sudo groupadd fserver
sudo usermod -aG YourUsername
```
Now you need to edit **/etc/fstab** and add the rule to mount your hard-drive.
```
sudo nano /etc/fstab
```
And add the following line, make sure your use your own UUID.
```
UUID=YourUUID /fserver auto nosuid,nodev,nofail,x-gvfs-show 0 0
```

## Clone the repository
Clone the [HIAS](https://github.com/LeukemiaAiResearch/HIAS "HIAS") repository from the [Peter Moss Leukemia AI Research](https://github.com/LeukemiaAiResearch "Peter Moss Leukemia AI Research") Github Organization.

To clone the repository and install this project, make sure you have Git installed. Now navigate to the home directory on your device using terminal/commandline, and then use the following command.

```
  git clone https://github.com/LeukemiaAiResearch/HIAS.git
```

Once you have used the command above you will see a directory called **HIAS** in your home directory.

```
  ls
```

Using the ls command in your home directory should show you the following.

```
  HIAS
```

The HIAS directory is your project root directory for this tutorial.

### Developer Forks
Developers from the Github community that would like to contribute to the development of this project should first create a fork, and clone that repository. For detailed information please view the [CONTRIBUTING](../CONTRIBUTING.md "CONTRIBUTING") guide. You should pull the latest code from the development branch.

```
  git clone -b "1.1.0" https://github.com/LeukemiaAiResearch/HIAS.git
```

The **-b "1.1.0"** parameter ensures you get the code from the latest master branch. Before using the below command please check our latest development branch in the button at the top of the project README.

&nbsp;

# Installation
Now you need to install the HIAS server.

## Easy Install (Recommended)
The easiest way to install the HIAS Server is to use the installation scripts. You will find Shell, PHP & Python scripts designed for modular installation of the server. If one part of the installation fails during an easy installation, you can find it's related shell file in the **Scripts/Installation/Shell** directory and then execute it. To do an easy install, use the following command from the project root:

```
sh Scripts/Installation/Shell/Install.sh
```

**Shell Script** [Install.sh](../Scripts/Installation/Shell/Install.sh "Install.sh")

**PLEASE NOTE** Once you have completed an easy install, you have to take the following steps:

- Update all of the configuration files with the credentials created during the installation. You will find the configuration files mentioned in the manual installation guide below.
- You will find information that will help you debug and/or understand the auto installation process below in the manual installation guide.
- Continue to [Login To Your Server UI](#login-to-server-ui) to continue the tutorial.

## Manual Install
If you would like to manually install everything for more understanding, you can use the following guides.

### UFW Firewall
Now you will set up your firewall. You will now open the required ports, these ports will be open on your server, but are not open to the outside world.

To run this installation file use the following command:
```
sh Scripts/Installation/Shell/UFW.sh
```
The contents of the above file as follows:
```
read -p "? This script will install UFW Firewall on your HIAS Server. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
    echo "Installing UFW"
    sudo apt-get install ufw
    echo "Testing UFW"
    sudo ufw enable
    sudo ufw disable
    echo "HIAS Server opening default ports"
    sudo ufw allow 22
    sudo ufw allow 80
    sudo ufw allow 443
    sudo ufw allow 3524
    sudo ufw allow 5671
    sudo ufw allow 8883
    sudo ufw allow 8545
    sudo ufw allow 9001
    sudo ufw allow 15671
    sudo ufw allow 27017
    sudo ufw allow 30303/udp
    sudo ufw allow 30303/tcp
    sudo ufw allow OpenSSH
    echo "Enabling UFW"
    sudo ufw enable
    echo "Checking UFW"
    sudo ufw status
    echo "Installed UFW"
    exit 0
else
    echo "- UFW Firewall installation terminated";
    exit 1
fi
```
**Shell Script**  [UFW.sh](../Scripts/Installation/Shell/UFW.sh "UFW.sh")

### Fail2Ban
Fail2Ban adds an additional layer of security, by scanning server logs and looking for unusal activity. Fail2Ban is configured to work with IPTables by default, so we will do some reconfiguration to make it work with our firewall, UFW.

To run this installation file use the following command:
```
sh Scripts/Installation/Shell/Fail2Ban.sh
```
The contents of the above file as follows:
```
read -p "? This script will install Fail2Ban on your HIAS Server. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
    echo "Installing Fail2Ban"
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
    echo "Installed Fail2Ban"
    exit 0
else
    echo "- Fail2Ban installation terminated";
    exit 1
fi
```

**Shell Script**  [Fail2Ban.sh](../Scripts/Installation/Shell/Fail2Ban.sh "Fail2Ban.sh")

### NGINX

To run this installation file use the following command:
```
sh Scripts/Installation/Shell/NGINX.sh
```
The contents of the above file as follows:
```
FMSG="- NGINX installation terminated"

read -p "? This script will install NGINX on your HIAS Server. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
    echo "- Installing NGINX"
    sudo apt-get install nginx
    sudo cp /etc/nginx/sites-available/default /etc/nginx/sites-available/default.backup
    sudo mkdir -p /fserver/var
    sudo cp -a Root/var/www/ /fserver/var/
    sudo chown -R www-data:www-data /fserver/var/www/html
    sudo usermod -a -G www-data $USER
    echo ""
    read -p "? Please provide the full domain name of your server, including subdomain: " domain
    if [ "$domain" != "" ]; then
        sudo sed -i -- "s/server_name _;/server_name $domain;/g" /etc/nginx/sites-available/default
        sudo nginx -t
        sudo systemctl reload nginx
        echo "- Installed NGINX";
        exit 0
    else
        echo $FMSG;
        exit 1
    fi
else
    echo $FMSG;
    exit 1
fi
```
You can check the Nginx logs by using the following command:

```
cat /var/log/nginx/error.log
```
You can edit the configuration for the NGINX server using the following command:
```
sudo nano /etc/nginx/sites-available/default
```
You can reload the NGINX service using the following command:
```
sudo systemctl reload nginx
```

**Shell Script**  [NGINX.sh](../Scripts/Installation/Shell/NGINX.sh "NGINX.sh")

### Let's Encrypt
Security is everything, and it is even better when security is free! To encrypt your network you are going to use SSL provided by [Let’s Encrypt](https://letsencrypt.org/ "Let’s Encrypt"). Follow the commands below to set up Let’s Encrypt.

Make sure to choose 2 to redirect http (non-secure) to https (secure).

To run this installation file use the following command:
```
sh Scripts/Installation/Shell/LetsEncrypt.sh
```
The contents of the above file as follows:
```
read -p "? This script will install Let's Encypt for NGINX on your HIAS Server. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
    echo "- Installing Let's Encrypt NGINX"
    sudo add-apt-repository ppa:certbot/certbot
    sudo apt-get update
    sudo apt-get install python-certbot-nginx
    echo "- Follow commands to setup secure server"
    sudo certbot --nginx
    echo "- Installed Let's Encrypt NGINX"
    exit 0
else
    echo "- Let's Encypt NGINX installation terminated";
    exit 1
fi
```

If you have followed above correctly you should now be able to access your website, but only using the secure protocol, 443, ie: https. If you visit your site you should now see the default Nginx page.

**Shell Script**  [LetsEncrypt.sh](../Scripts/Installation/Shell/LetsEncrypt.sh "LetsEncrypt.sh")

### PHP
Now you will install PHP on your server. Follow the commands below and complete any required steps for the installation to accomplish this.

To run this installation file use the following command:
```
sh Scripts/Installation/Shell/PHP.sh
```
The contents of the above file as follows:
```
FMSG="- PHP installation terminated"

read -p "? This script will install PHP on your HIAS Server. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
    echo "- Installing PHP"
    sudo apt-get install php-fpm php-mysql
    sudo sed -i -- 's/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/g' /etc/php/7.2/fpm/php.ini
    sudo sed -i -- 's/upload_max_filesize = 20M/upload_max_filesize = 100M/g' /etc/php/7.2/fpm/php.ini
    sudo sed -i -- 's/post_max_size = 20M/post_max_size = 100M/g' /etc/php/7.2/fpm/php.ini
    sudo systemctl restart php7.2-fpm
    read -p "? Please provide the full domain name of your server, including subdomain: " domain
    read -p "? Please provide the IP of your server: " ip
    if [ "$domain" != "" ]; then
        sudo cp Root/etc/nginx/sites-available/default /etc/nginx/sites-available/default
        sudo sed -i -- "s#root /var/www/html;#root /fserver/var/www/html;#g" /etc/nginx/sites-available/default
        sudo sed -i -- "s/YourHiasDomainName/$domain/g" /etc/nginx/sites-available/default
        sudo sed -i -- "s/HiasServerIp/$ip/g" /etc/nginx/sites-available/default
        sudo nginx -t
        sudo systemctl reload nginx
        echo "- You can now view your PHP configuration at https://www.YourDomain.com/info";
        echo "- Installed PHP";
        exit 0
    else
        echo $FMSG;
        exit 1
    fi
else
    echo $FMSG;
    exit 1
fi
```
If you now visit the info page your website ie: https://www.YourDomain.com/info you should see the PHP configuration of your server.

![PHP config](../Media/Images/php.png)

**Shell Script**  [PHP.sh](../Scripts/Installation/Shell/PHP.sh "PHP.sh")

### MySql
Now it is time to install MySql on your server. Follow the commands below and complete any required steps for the installation to accomplish this. This will install MySQL on your hard-drive.

**Make sure you keep note of all passwords etc you create.**

**Hints:**

- Do not set up VALIDATE PASSWORD plugin
- Remove anonymous users
- Root restricted to local host
- Remove test database

To run this installation file use the following command:
```
bash Scripts/Installation/Shell/MySQL.sh
```
The contents of the above file as follows:
```
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
    sudo mysql -uroot -p$rpassword -e "GRANT SELECT, INSERT, UPDATE, DELETE  ON *.* TO $adbusername@localhost IDENTIFIED BY '$adbpassword'";
    sudo mysql -uroot -p$rpassword -e "SELECT host, user from mysql.user";
    read -p "! Enter a new database name: " dbname
    sudo mysql -uroot -p$rpassword -e "CREATE DATABASE $dbname";
    sudo mysql -uroot -p$rpassword -e 'show databases;'
    sudo mysql -uroot -p$rpassword -e "use $dbname;"
    sudo mysql -uroot  -p$rpassword $dbname < Scripts/Installation/SQL.sql;
    sudo sed -i "s/\"dbname\":.*/\"dbname\": \"$dbname\",/g" "/fserver/var/www/Classes/Core/confs.json"
    sudo sed -i "s/\"dbusername\":.*/\"dbusername\": \"$adbusername\",/g" "/fserver/var/www/Classes/Core/confs.json"
    escaped=$(printf '%s\n' "$adbpassword" | sed -e 's/[\/&]/\\&/g');
    sudo sed -i "s/\"dbpassword\":.*/\"dbpassword\": \"$escaped\",/g" "/fserver/var/www/Classes/Core/confs.json"
    read -p "! Enter a new encryption key, this key should be 32 characters an NO special characters: " ekey
    escaped=$(printf '%s\n' "$ekey" | sed -e 's/[\/&]/\\&/g');
    sudo sed -i "s/\"key\":.*/\"key\": \"$escaped\"/g" "/fserver/var/www/Classes/Core/confs.json"
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
```

**Shell Script**  [MySQL.sh](../Scripts/Installation/Shell/MySQL.sh "MySQL.sh")

### phpMyAdmin

![phpMyAdmin](../Media/Images/phpMyAdmin.png)

Now you should install phpMyAdmin. During installation press tab -> enter -> yes -> password, then create a link to phpMyAdmin.

To run this installation file use the following command:
```
sh Scripts/Installation/Shell/phpMyAdmin.sh
```
The contents of the above file as follows:
```
read -p "? This script will install phpMyAdmin on your HIAS Server. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
    echo "- Installing phpMyAdmin"
    echo "! tab -> enter -> yes -> password "
    sudo apt-get install phpmyadmin
    sudo ln -s /usr/share/phpmyadmin /fserver/var/www/html
    sudo sed -i "s/|\s*\((count(\$analyzed_sql_results\['select_expr'\]\)/| (\1)/g" /usr/share/phpmyadmin/libraries/sql.lib.php
    echo "- Installed phpMyAdmin";
    exit 0
else
    echo "- phpMyAdmin installation terminated";
    exit 1
fi
```
Now you should be able to visit phpMyAdmin by accessing the relevant directory on your website. IE: https://www.YourDomain.com/phpmyadmin/

**Shell Script**  [phpMyAdmin.sh](../Scripts/Installation/Shell/phpMyAdmin.sh "phpMyAdmin.sh")

### Mongo Database
We will use [Mongo DB](https://docs.mongodb.com/manual/tutorial/install-mongodb-on-ubuntu/ "Mongo DB") to store the data from our sensors.

To run this installation file use the following command:
```
bash Scripts/Installation/Shell/MongoDB.sh
```

**If the text editors open before the mongodb, you will need to close them and manually start the mongo console using the command mongo, the continue the installation by running each install file individually**

The contents of the above file as follows:
```
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
		escaped=$(printf '%s\n' "$dbp" | sed -e 's/[\/&]/\\&/g');
		sudo sed -i "s/\"db\":.*/\"db\": \"$dbn\",/g" "confs.json"
		sudo sed -i "s/\"dbu\":.*/\"dbu\": \"$dbu\",/g" "confs.json"
		sudo sed -i "s/\"dbp\":.*/\"dbp\": \"$escaped\"/g" "confs.json"
		sudo sed -i "s/\"mdbname\":.*/\"mdbname\": \"$dbn\",/g" "/fserver/var/www/Classes/Core/confs.json"
		sudo sed -i "s/\"mdbusername\":.*/\"mdbusername\": \"$dbu\",/g" "/fserver/var/www/Classes/Core/confs.json"
		sudo sed -i "s/\"mdbpassword\":.*/\"mdbpassword\": \"$escaped\",/g" "/fserver/var/www/Classes/Core/confs.json"
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
```
When the mongo client opens during installation, you need to follow these steps:

- Create an admin database and user, replacing **username** and **password** with your desired username and password.

To do this use the code below, replacing the values with your desired values:

```
use admin
db.createUser(
    {
        user: "YourAdminUser",
        pwd: "YourAdminUserPass",
        roles: [ "root" ]
    }
 )
```
You should see:
```
Successfully added user: { "user" : "username", "roles" : [ "root" ] }
```
- Now create a iotJumpWay database and user, change **YourMongoDatabaseName** to the name you want to use for the database:
```
use YourMongoDatabaseName
db.createUser(
    {
        user: "YourMongoDatabaseUser",
        pwd: "YourMongoDatabasePass",
        roles: [
            { role: "readWrite", db: "YourMongoDatabaseName" }
        ]
    }
)
```

**Shell Script**  [MongoDB.sh](../Scripts/Installation/Shell/MongoDB.sh "MongoDB.sh")

### SSL Security

![SSL Security](../Media/Images/SSL.png)

You need to remove vulnerable versions TLS 1/TLS 1.1, and enable TLS 1.3.

To run this installation file use the following command:
```
sh Scripts/Installation/Shell/SSL.sh
```
The contents of the above file as follows:
```
read -p "? This script will update the SSL security on your HIAS Server. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
    echo "- Updating SSL security...."
    sudo cp /etc/nginx/nginx.conf /etc/nginx/nginx.tls-old
    sudo sed -i -- "s/ssl_protocols TLSv1 TLSv1.1 TLSv1.2;/ssl_protocols TLSv1.2 TLSv1.3;/g" /etc/nginx/nginx.conf
    sudo sed -i -- "s/ssl_protocols TLSv1 TLSv1.1 TLSv1.2;/ssl_protocols TLSv1.2 TLSv1.3;/g" /etc/letsencrypt/options-ssl-nginx.conf
    sudo nginx -t
    sudo service nginx reload
    echo "- Updated SSL security!";
    exit 0
else
    echo "- SSL security updating terminated!";
    exit 1
fi
```
If everything went ok you will see:
```
nginx: the configuration file /etc/nginx/nginx.conf syntax is ok
nginx: configuration file /etc/nginx/nginx.conf test is successful
```
**Shell Script**  [SSL.sh](../Scripts/Installation/Shell/SSL.sh "SSL.sh")

### Private Ethereum Blockchain
We will use Ethereum to set up a private Blockchain for the HIAS network. During the installation, the geth console will open, you need to follow the [Deploy Smart Contracts With Geth](#deploy-smart-contracts-with-geth) instructions below.

To run this installation file use the following command:
```
bash Scripts/Installation/Shell/Blockchain.sh
```
The contents of the above file as follows:
```
FMSG="- HIAS Blockchain installation terminated"

read -p "? This script will install the HIAS Blockchain on your HIAS Server. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
	echo "- Installing HIAS Blockchain"
	sudo apt-get install software-properties-common
	sudo add-apt-repository -y ppa:ethereum/ethereum
	sudo apt-get update
	sudo apt-get install ethereum
	sudo cp -a Root/fserver/ethereum/ /fserver/
	echo "- You will now create the first of the 4 required HIAS Blockchain accounts. Follow the instructions given to create the account for your core HIAS Blockchain user. Make sure you save the information given to you and keep it safe. You will need this information for configuring your HIAS Blockchain and if you lose these details you will have to create a new installation."
	geth account new --datadir /fserver/ethereum/HIAS
	echo "- You will now create the second of the 4 required HIAS Blockchain accounts. Follow the instructions given to create the account for your iotJumpWay MQTT HIAS Blockchain user. Make sure you save the information given to you and keep it safe. You will need this information for configuring your HIAS Blockchain and if you lose these details you will have to create a new installation."
	geth account new --datadir /fserver/ethereum/HIAS
	echo "- You will now create the third of the 4 required HIAS Blockchain accounts. Follow the instructions given to create the account for your iotJumpWay AMQP HIAS Blockchain user. Make sure you save the information given to you and keep it safe. You will need this information for configuring your HIAS Blockchain and if you lose these details you will have to create a new installation."
	geth account new --datadir /fserver/ethereum/HIAS
	echo "- You will now create the fourth of the 4 required HIAS Blockchain accounts. Follow the instructions given to create the account for your personal HIAS Blockchain user. Make sure you save the information given to you and keep it safe. You will need this information for configuring your HIAS Blockchain and if you lose these details you will have to create a new installation."
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
	escaped=$(printf '%s\n' "$hpass" | sed -e 's/[\/&]/\\&/g');
	sudo sed -i 's/\"hpass\":.*/\"hpass\": \"'$escaped'\",/g' "confs.json"
	read -p "! Enter your iotJumpWay MQTT HIAS Blockchain user account address: " iaddress
	read -p "! Enter your iotJumpWay MQTT HIAS Blockchain user account password: " ipass
	sudo sed -i 's/\"iaddress\":.*/\"iaddress\": \"'$iaddress'\",/g' "confs.json"
	escaped=$(printf '%s\n' "$ipass" | sed -e 's/[\/&]/\\&/g');
	sudo sed -i 's/\"ipass\":.*/\"ipass\": \"'$escaped'\",/g' "confs.json"
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
		php Scripts/Installation/PHP/Blockchain.php "Contract" "$hcaddress" "HIAS Permissions & Registrations" "$haddress" "$hctransaction" "$habi"
		php Scripts/Installation/PHP/Blockchain.php "Contract" "$icaddress" "iotJumpWay Data Integrity" "$haddress" "$ictransaction" "$iabi"
		php Scripts/Installation/PHP/Blockchain.php "Contract" "$pcaddress" "Patients" "$haddress" "$pctransaction" "$pabi"
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
```
#### **Deploy Smart Contracts With Geth**

Now start your miner:
```
miner.start(1)
```
At this point you will be inside Geth console and your blockchain will be online.You will need to wait for your DAG to be generated. You will see logs such as **Generating DAG in progress               epoch=0 percentage=42 elapsed=3m31.734s**, when the percentage gets to 100%, your DAG is generated.

To deploy your smart contracts you will need the contents of:

- **/fserver/ethereum/Contracts/build/HIAS.abi**
- **/fserver/ethereum/Contracts/build/HIAS.bin**
- **/fserver/ethereum/Contracts/build/iotJumpWay.abi**
- **/fserver/ethereum/Contracts/build/iotJumpWay.bin**
- **/fserver/ethereum/Contracts/build/HIASPatients.abi**
- **/fserver/ethereum/Contracts/build/HIASPatients.bin**

First deploy the HIAS Smart Contract. Note that **0x** in the hbin variable is crucial:
```
  var habi = ContentsOfHIAS.abi
  var hbin = "0xContentsOfHIAS.bin"
  var haddress = "YourHiasBlockchainAddress"
  var hpass = "YourHiasBlockchainPassword"
  personal.unlockAccount(haddress, hpass, 1200)
  var newContract = eth.contract(habi)
  var deploy = {from:haddress, data:hbin, gas: 5000000 }
  var contractInstance = newContract.new(deploy, function(e, contract){
    if(!e) {
      if(!contract.address){
        console.log("Contract deployment transaction hash: " + contract.transactionHash);
      } else {
        console.log("Contract address: " + contract.address);
      }
    } else {
      console.log(e);
    }
  })
```
Now you have started the deployment process, once the contract is mined you will see similar to the following:

```
  Contract deployment transaction hash: 0xe196d1757cb583887efd47d88a0968a44a245f07c32f3f66d37764b51ae5c876
```
```
  Contract address: 0x38d38bef791e589502c74339aa411942b924b15f
```
You need to save the contract address and the transaction as you will need it for the rest of the installation.

Now repeat the process for the iotJumpWay Smart Contract:
```
  var iabi = ContentsOfiotJumpWay.abi
  var ibin = "0xContentsOfiotJumpWay.bin"
  var newContract = eth.contract(iabi)
  var deploy = {from:haddress, data:ibin, gas: 5000000}
  var contractInstance = newContract.new(deploy, function(e, contract){
    if(!e) {
      if(!contract.address){
        console.log("Contract deployment transaction hash: " + contract.transactionHash);
      } else {
        console.log("Contract address: " + contract.address);
      }
    } else {
      console.log(e);
    }
  })
```
Finally repeat the process for the Patients Smart Contract:
```
  var pabi = ContentsOfHIASPatients.abi
  var pbin = "0xContentsOfHIASPatients.bin"
  var newContract = eth.contract(pabi)
  var deploy = {from:haddress, data:pbin, gas: 5000000}
  var contractInstance = newContract.new(deploy, function(e, contract){
    if(!e) {
      if(!contract.address){
        console.log("Contract deployment transaction hash: " + contract.transactionHash);
      } else {
        console.log("Contract address: " + contract.address);
      }
    } else {
      console.log(e);
    }
  })
```
Now you can stop mining and exit geth:

```
miner.stop()
exit
```
You now need to log directly into your HIAS server and start the HIAS Blockchain and miner. Ensuring you are in the HIAS server console, use the following commands:
```
  read -p "! Enter your HIAS Blockchain chain ID: " chainid
  read -p "! Enter your HIAS Blockchain user address: " haddress
  read -p "! Enter your HIAS Server IP: " ip
  geth --mine --http --networkid $chainid -datadir /fserver/ethereum/HIAS --http.addr $ip --http.corsdomain "*" --miner.etherbase $haddress --http.api "eth,net,web3,personal" --allow-insecure-unlock console
  miner.start()
```

This needs to be running at all times which is why you logged directly into your HIAS device, if you do not have your Blockchain running, you will not be able to log in to the HIAS UI, if the Blockchain goes down, you will be logged out instantly. Now you can go back to your remote terminal to continue the installation.

**Shell Script**  [Blockchain.sh](../Scripts/Installation/Shell/Blockchain.sh "Blockchain.sh")

### iotJumpWay MQTT Broker
Now install the required MQTT software for our local iotJumpWay MQTT broker.

To run this installation file use the following command:
```
bash Scripts/Installation/Shell/MqttBroker.sh
```
The contents of the above file as follows:
```
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
	pip3 install jsonpickle
	pip3 install flask
	read -p "! Enter your HIAS domain name: " domain
	read -p "! Enter your HIAS IP: " ip
	sudo mkdir -p /fserver/libraries/mosquitto
	sudo mkdir -p /fserver/certs
	sudo mkdir -p /fserver/var/log/mosquitto
	sudo touch /fserver/var/log/mosquitto/mosquitto.log
	sudo chown -R $USER:$USER /fserver/var/log/mosquitto/mosquitto.log
	sudo cp /etc/letsencrypt/live/$domain/fullchain.pem /fserver/certs/
	sudo cp /etc/letsencrypt/live/$domain/cert.pem /fserver/certs/
	sudo cp /etc/letsencrypt/live/$domain/privkey.pem /fserver/certs/
	sudo chown -R $USER:$USER /fserver/libraries/mosquitto
	sudo chmod -R 775 /fserver/certs
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
	echo "cafile /fserver/certs/fullchain.pem" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "certfile /fserver/certs/cert.pem" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "keyfile /fserver/certs/privkey.pem" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "require_certificate false" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "listener 9001" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "protocol websockets" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "cafile /fserver/certs/fullchain.pem" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "certfile /fserver/certs/cert.pem" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "keyfile /fserver/certs/privkey.pem" | sudo tee -a /etc/mosquitto/mosquitto.conf
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
	echo "auth_opt_pass ${sdup}" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "auth_opt_userquery SELECT pw FROM mqttu WHERE uname = '%s'" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "auth_opt_superquery SELECT COUNT(*) FROM mqttu WHERE uname = '%s' AND super = 1" | sudo tee -a /etc/mosquitto/mosquitto.conf
	echo "auth_opt_aclquery SELECT topic FROM mqttua WHERE (username = '%s') AND (rw >= %d)" | sudo tee -a /etc/mosquitto/mosquitto.conf
	cd ~/HIAS
	sudo sed -i "s/\"host\":.*/\"host\": \"$domain\",/g" "confs.json"
	sudo sed -i -- "s/YourHiasIP/$ip/g" "confs.json"
	sudo sed -i "s/host:.*/host: \"$domain\",/g" "/fserver/var/www/html/iotJumpWay/Classes/iotJumpWay.js"
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
	sudo sed -i -- "s/ExecStart=\/usr\/bin\/certbot -q renew/ExecStart=\/usr\/bin\/certbot -q renew --deploy-hook \"\/home\/$USER\/HIAS\/Scripts\/System\/Certbot.sh\"/g" /lib/systemd/system/certbot.service
	sudo sed -i -- "s/user:user/$USER:$USER/g" Scripts/System/Certbot.sh
	chmod u+x Scripts/System/Certbot.sh
	sudo systemctl daemon-reload
	sudo systemctl enable mosquitto.service
	sudo systemctl start mosquitto.service
	sudo systemctl status mosquitto.service
	sudo systemctl restart certbot.service
	sudo systemctl status certbot.service
	echo "- Installed iotJumpWay!";
	exit 0
else
	echo "- iotJumpWay installation terminated!";
	exit 1
fi
```

**Shell Script**  [MqttBroker.sh](../Scripts/Installation/Shell/MqttBroker.sh "MqttBroker.sh")

### iotJumpWay AMQP Broker
Now install the required AMQP software for our local iotJumpWay MQTT broker.

To run this installation file use the following command:
```
bash Scripts/Installation/Shell/AmqpBroker.sh
```
The contents of the above file as follows:
```
read -p "? This script will install the iotJumpWay AMQP broker on your HIAS Server. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
	echo "- Installing iotJumpWay AMQP Broker...."
	sudo tee /etc/apt/sources.list.d/bintray.rabbitmq.list <<-EOF
	deb https://dl.bintray.com/rabbitmq-erlang/debian bionic erlang
	deb https://dl.bintray.com/rabbitmq/debian bionic main
	EOF
	sudo apt-get update -y
	sudo apt-get install -y rabbitmq-server
	sudo touch /etc/rabbitmq/rabbitmq.config
	pip3 install pika
	sudo wget https://dl.bintray.com/rabbitmq/community-plugins/rabbitmq_auth_backend_http-3.6.x-61ed0a93.ez -P /usr/lib/rabbitmq/lib/rabbitmq_server-3.6.10/plugins
	sudo wget http://www.rabbitmq.com/releases/plugins/v2.4.1/mochiweb-2.4.1.ez -P /usr/lib/rabbitmq/lib/rabbitmq_server-3.6.10/plugins
	sudo rabbitmq-plugins enable rabbitmq_auth_backend_http
	sudo rabbitmq-plugins enable rabbitmq_management
	pip3 install gevent
	sudo touch /etc/rabbitmq/rabbitmq.config
	read -p "? Enter your HIAS server URL (Without https://): " domain
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
	echo "   {ssl_options, [{cacertfile,\"/fserver/certs/fullchain.pem\"}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "                  {certfile,\"/fserver/certs/cert.pem\"}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "                  {keyfile,\"/fserver/certs/privkey.pem\"}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
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
	echo "     {ssl_opts, [{cacertfile, \"/fserver/certs/fullchain.pem\"}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "                 {certfile, \"/fserver/certs/cert.pem\"}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "                 {keyfile, \"/fserver/certs/privkey.pem\"}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
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
	echo "- Installed iotJumpWay AMQP Broker!";
	exit 0
else
	echo "- iotJumpWay AMQP Broker installation terminated!";
	exit 1
fi
```

**Shell Script**  [AmqpBroker.sh](../Scripts/Installation/Shell/AmqpBroker.sh "AmqpBroker.sh")

### iotJumpWay Location and Applications
Now setup the local iotJumpWay location and application. To run this installation file use the following command:
```
bash Scripts/Installation/Shell/iotJumpWay.sh
```
The contents of the above file as follows:
```
read -p "? This script will install your iotJumpWay location and core application on your HIAS Server. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
	echo "- Installing iotJumpWay location...."
	sudo apt install apache2-utils
	sudo mkdir -p /etc/nginx/security
	sudo touch /etc/nginx/security/htpasswd
	sudo chown -R $USER:$USER /etc/nginx/security
	read -p "! Enter your default location name. This field represents the physical location that your server is installed in, ie: Home, Office, Hospital, Center etc: " location
	read -p "! Enter your full domain name including https://: " domain
	read -p "! Enter your HIAS Blockchain user address: " haddress
	read -p "! Enter your HIAS Blockchain user pass: " hpass
	read -p "! Enter your HIAS iotJumpWay MQTT Blockchain user address: " iaddress
	read -p "! Enter your HIAS iotJumpWay AMQP Blockchain user address: " aaddress
	read -p "! Enter the IP of your HIAS Server: " ip
	read -p "! Enter the MAC address of your HIAS Server: " mac
	php Scripts/Installation/PHP/Location.php "$location" "Context Broker" "$haddress" "$hpass" "$ip" "$mac" "MQTT IoT Agent" "$iaddress" "AMQP IoT Agent" "$aaddress" "$domain"
	read -p "! Enter your HIAS Location Identifier (1): " lid
	read -p "! Enter your Context Broker Application Public Key: " haid
	read -p "! Enter your Context Broker Application name: " han
	read -p "! Enter your Context Broker Application MQTT username: " hun
	read -p "! Enter your Context Broker Application MQTT password: " hpw
	read -p "! Enter your iotJumpWay MQTT Application Public Key: " iaid
	read -p "! Enter your iotJumpWay MQTT Application Private Key: " iapk
	read -p "! Enter your iotJumpWay MQTT Application name: " ian
	read -p "! Enter your iotJumpWay MQTT Application MQTT username: " iun
	read -p "! Enter your iotJumpWay MQTT Application MQTT password: " ipw
	read -p "! Enter your iotJumpWay AMQP Application Public Key: " aaid
	read -p "! Enter your iotJumpWay AMQP Application MQTT username: " aun
	read -p "! Enter your iotJumpWay AMQP Application MQTT password: " apw
	hpw=$(printf '%s\n' "$hpw" | sed -e 's/[\/&]/\\&/g');
	ipw=$(printf '%s\n' "$ipw" | sed -e 's/[\/&]/\\&/g');
	apw=$(printf '%s\n' "$apw" | sed -e 's/[\/&]/\\&/g');
	iapk=$(printf '%s\n' "$iapk" | sed -e 's/[\/&]/\\&/g');
	sudo sed -i -- "s/YourIotJumpWayLocationID/$lid/g" "confs.json"
	sudo sed -i -- "s/YourIotJumpWayApplicationIdentifier/$iaid/g" "confs.json"
	sudo sed -i -- "s/YourIotJumpWayApplicationAuthKey/$iapk/g" "confs.json"
	sudo sed -i -- "s/YourContextBrokerApplicationID/$haid/g" "confs.json"
	sudo sed -i -- "s/YourContextBrokerApplicationName/$han/g" "confs.json"
	sudo sed -i -- "s/YourContextBrokerApplicationMqttUsername/$hun/g" "confs.json"
	sudo sed -i -- "s/YourContextBrokerApplicationMqttPassword/$hpw/g" "confs.json"
	sudo sed -i -- "s/YourIotJumpWayApplicationID/$iaid/g" "confs.json"
	sudo sed -i -- "s/YourIotJumpWayApplicationName/$ian/g" "confs.json"
	sudo sed -i -- "s/YourIotJumpWayApplicationUsername/$iun/g" "confs.json"
	sudo sed -i -- "s/YourIotJumpWayApplicationPassword/$ipw/g" "confs.json"
	sudo sed -i -- "s/YourAmqpApplicationIdentifier/$aaid/g" "confs.json"
	sudo sed -i -- "s/YourAmqpApplicationUsername/$aun/g" "confs.json"
	sudo sed -i -- "s/YourAmqpApplicationPasword/$apw/g" "confs.json"
	echo "- Installed iotJumpWay location and applications!";
	exit 0
else
	echo "- iotJumpWay location and applications installation terminated!";
	exit 1
fi
```
You will be provided with your credentials for your core iotJumpWay applications. You must keep these credentials safe.

**Shell Script**  [iotJumpWay.sh](../Scripts/Installation/Shell/iotJumpWay.sh "iotJumpWay.sh")

### Create Admin User
Now you should create your admin user that you will use to access the network, UI and the network TassAI streams. The following command executes a PHP script to add your chosen username as an admin user in the system.

The script will create an admin account and provide your with the password, make sure to copy and save the password and your username somewhere safe.

The script will also save your password with the Apache HTPassword system that protects the camera streams and APIs.

The script will also create an iotJumpWay application, allowing you to authenticate to the broker as your self. This can be used with the HIAS Staff Android Application.

To run this installation file use the following command:
```
bash Scripts/Installation/Shell/Admin.sh
```
The contents of the above file as follows:
```
read -p "? This script will create a new user account for your HIAS Server. You will need to copy and save the credentials provided to you at the end of this script. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
    echo "- Creating HIAS Server admin user"
    read -p "! Enter your full domain name including https://: " domain
    read -p "! Enter your HIAS Blockchain user address: " haddress
    read -p "! Enter your HIAS Blockchain user pass: " hpass
    read -p "! Enter your name: " name
    read -p "! Enter your email: " email
    read -p "! Enter your desired username (No spaces or special characters): " username
    read -p "! Enter your personal HIAS Blockchain account address: " paddress
    read -p "! Enter your personal HIAS Blockchain account password: " ppass
    read -p "! Enter your HIAS Location Identfier: " identifier
    read -p "! Enter local IP address of the device that the application will run on (IE: 192.168.1.98): " ip
    read -p "! Enter MAC address of the device that the application will run on: " mac
    sudo touch /etc/nginx/security/patients
    sudo touch /etc/nginx/security/beds
    sudo chown -R $USER:$USER /etc/nginx/security
    php Scripts/Installation/PHP/Admin.php "$name" "$email" "$username" "$paddress" "$ppass" "$ip" "$mac" "$domain" "$haddress" "$hpass" "$identifier"
    echo "Your account has been set up!";
else
    echo "- HIAS Server admin user creation terminated";
    exit
fi
```
You will be provided with your credentials. You must keep these credentials safe.

**Shell Script**  [Admin.sh](../Scripts/Installation/Shell/Admin.sh "Admin.sh")


### Finalize Server Settings
Now you need to finalize your server settings, to do this you need your server URL, IE: https://www.YourDomain.com, you will need to register free [Google Maps](https://developers.google.com/maps/documentation/javascript/get-api-key "Google Maps") and [Google Recaptcha](https://www.google.com/recaptcha "Google Recaptcha") site/secret keys, and you will need to provide your default latitude and longitude settings. The latitude and longitude settings will be used for the default coordinates for Google Maps in HIAS, they must be correct.

To run this installation file use the following command:
```
bash Scripts/Installation/Shell/Finalize.sh
```
The contents of the above file as follows:
```
read -p "? This script will finish the configuration of your HIAS Server. You will need your server URL, and your Google Recapthca site & secret keys. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
    read -p "! Enter your server URL. IE: https://www.YourDomain.com. This should match the domain used in the NGINX configuration: " domain
    read -p "! Enter your site Recaptcha key: " pub
    read -p "! Enter your secret Recaptcha key: " prv
    read -p "! Enter your Google Maps key: " gmaps
    read -p "! Enter your default latitude: " lat
    read -p "! Enter your default longitude: " lng
    read -p "! Enter the application ID for your iotJumpWay application: " app
    php Scripts/Installation/PHP/Finalize.php "$domain" "$pub" "$prv" "$gmaps" "$lat" "$lng" "$app"
else
    echo "- Server database finalization terminated";
    exit
fi
```

**Shell Script**  [Finalize.sh](../Scripts/Installation/Shell/Finalize.sh "Finalize.sh")

### TassAI (Computer Vision)
We will use the [HIAS TassAI](https://github.com/LeukemiaAiResearch/TassAI "HIAS TassAI") Facial Recognition Security System API to provide the ability for HIAS device and applications to carry out remote facial recognition requests using HTTP requests.

#### OpenVINO 2020.3
You will use the Intel® Distribution of OpenVINO™ toolkit for your server security API. OpenVINO™ is used to optimize and accelerate edge AI applications allowing lower powered devices to run fast applications without the need for cloud services.

To download the Intel® Distribution of OpenVINO™ toolkit on your device, please use [this page](https://software.intel.com/en-us/openvino-toolkit/choose-download) and download v 2020 3 LTS. Download to the home directory of your HIAS server and the use the TassAI installation file to install.

To run this installation file use the following command:
```
bash Scripts/Installation/Shell/TassAI.sh
```
The contents of the above file as follows:
```
read -p "? This script will install TassAI on your HIAS Server. The script assumes you have downloaded l_openvino_toolkit_p_2020.3.194 (2020 3 LTS) to your home directory. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
	echo "- Installing TassAI...."
	sudo mkdir -p /fserver/models/TassAI
	sudo chown -R $USER:$USER /fserver/models/TassAI
	read -p "! Enter your full domain name including https://: " domain
	read -p "! Enter your HIAS Blockchain user address: " haddress
	read -p "! Enter your HIAS Blockchain user pass: " hpass
	read -p "! Enter your HIAS public key: " user
	read -p "! Enter your HIAS private key: " pass
	read -p "! Enter your HIAS iotJumpWay Location ID: " lid
	read -p "! Enter your HIAS iotJumpWay Location Entity: " lie
	read -p "! Enter your zone name (No spaces or special characters). This field represents the zone that this device is installed in, ie: Office, Study, Lounge, Kitchen etc: " zone
	read -p "! Enter local IP address of the HIAS Server device (IE: 192.168.1.98): " ip
	read -p "! Enter MAC address of HIAS Server device: " mac
	php Scripts/Installation/PHP/TassAI.php "$lid"  "$lie" "$zone" "$ip" "$mac" "$domain" "$user" "$pass" "$haddress" "$hpass"
	read -p "! Enter your TassAI Device Zone ID: " zid
	read -p "! Enter your TassAI Device ID: " did
	read -p "! Enter your TassAI Device MQTT username: " un
	read -p "! Enter your TassAI Device MQTT password: " pw
	sudo sed -i -- "s/YourTassAIZoneID/$zid/g" "confs.json"
	sudo sed -i -- "s/YourTassAIDeviceID/$did/g" "confs.json"
	sudo sed -i -- "s/YourTassAIMqttUsername/$un/g" "confs.json"
	sudo sed -i -- "s/YourTassAIMqttPassword/$pw/g" "confs.json"
	cd ~/
	 tar -xvzf l_openvino_toolkit_p_2020.3.194.tgz
	cd l_openvino_toolkit_p_2020.3.194
	sudo ./install.sh
	cd /opt/intel/openvino/deployment_tools/model_optimizer/install_prerequisites
	sudo ./install_prerequisites.sh
	echo "# OpenVINO" | tee -a ~/.bashrc
	echo "source /opt/intel/openvino/bin/setupvars.sh" | tee -a ~/.bashrc
	source ~/.bashrc
	wget https://download.01.org/opencv/2020/openvinotoolkit/2020.3/open_model_zoo/models_bin/1/face-detection-retail-0004/FP16/face-detection-retail-0004.bin -P /fserver/models/TassAI/
	wget https://download.01.org/opencv/2020/openvinotoolkit/2020.3/open_model_zoo/models_bin/1/face-detection-retail-0004/FP16/face-detection-retail-0004.xml -P /fserver/models/TassAI/
	wget https://download.01.org/opencv/2020/openvinotoolkit/2020.3/open_model_zoo/models_bin/1/face-reidentification-retail-0095/FP16/face-reidentification-retail-0095.bin -P /fserver/models/TassAI/
	wget https://download.01.org/opencv/2020/openvinotoolkit/2020.3/open_model_zoo/models_bin/1/face-reidentification-retail-0095/FP16/face-reidentification-retail-0095.xml -P /fserver/models/TassAI/
	wget https://download.01.org/opencv/2020/openvinotoolkit/2020.3/open_model_zoo/models_bin/1/landmarks-regression-retail-0009/FP16/landmarks-regression-retail-0009.bin -P /fserver/models/TassAI/
	wget https://download.01.org/opencv/2020/openvinotoolkit/2020.3/open_model_zoo/models_bin/1/landmarks-regression-retail-0009/FP16/landmarks-regression-retail-0009.xml -P /fserver/models/TassAI/
	echo "- TassAI iotJumpWay device installation complete!";
	exit 0
else
	echo "- TassAI installation terminated!";
	exit 1
fi
```
You will be provided your credentials for this device, you should keep them safe.

**Shell Script**  [TassAI.sh](../Scripts/Installation/Shell/TassAI.sh "TassAI.sh")

### HIAS Server Services
Now you will set up services that will automatically run the iotJumpWay listener, the smart contract replenishment program, and the facial recognition API.

To run this installation file use the following command:
```
sh Scripts/Installation/Shell/Services.sh
```
The contents of the above file as follows:
```
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
```
**Shell Script**  [Services.sh](../Scripts/Installation/Shell/Services.sh "Services.sh")

Your services will now load every time your server boots up. To manage the services you can use:

```
sudo systemctl restart ContextBroker.service
sudo systemctl start ContextBroker.service
sudo systemctl stop ContextBroker.service
sudo systemctl status ContextBroker.service

sudo systemctl restart MQTT.service
sudo systemctl start MQTT.service
sudo systemctl stop MQTT.service
sudo systemctl status MQTT.service

sudo systemctl restart AMQP.service
sudo systemctl start AMQP.service
sudo systemctl stop AMQP.service
sudo systemctl status AMQP.service

sudo systemctl restart Security.service
sudo systemctl start Security.service
sudo systemctl stop Security.service
sudo systemctl status Security.service

sudo systemctl restart Replenish.service
sudo systemctl start Replenish.service
sudo systemctl stop Replenish.service
sudo systemctl status Replenish.service
```

### File Server
We will use Samba to create a private network file share.

To run this installation file use the following command:
```
sh Scripts/Installation/Shell/Samba.sh
```
The contents of the above file as follows:
```
read -p "? This script will install Samba on your HIAS Server. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
    echo "Installing Samba"
    sudo apt install samba
    sudo cp /etc/samba/smb.conf /etc/samba/smb.conf.backup
    sudo sed -i -- "s/;   bind interfaces only = yes/bind interfaces only = yes/g" /etc/samba/smb.conf
    testparm
    sudo systemctl restart smbd
    sudo systemctl status smbd
    echo "Creating Samba directory"
    sudo mkdir -p /fserver/samba
    sudo chgrp sambashare /fserver/samba
    echo "Creating Samba admins group & directory"
    sudo groupadd sambashare
    sudo groupadd smbadmins
    sudo mkdir -p /fserver/samba/smbadmins
    sudo chgrp smbadmins /fserver/samba/smbadmins
    sudo chmod -R 770 /fserver/samba/smbadmins
    echo "Creating Samba admin user"
    read -p "! Enter a new Samba username for your admin account: " sauser
    echo "$sauser"
    sudo useradd -M -d /fserver/samba/users -s /usr/sbin/nologin -G sambashare,smbadmins "$sauser"
    sudo smbpasswd -a "$sauser"
    sudo smbpasswd -e "$sauser"
    sudo mkdir /fserver/samba/users
    sudo chown "$sauser":sambashare /fserver/samba/users
    sudo chmod 2770 /fserver/samba/users
    echo "Creating Samba user"
    read -p "! Enter a new Samba username for yourself: " suser
    sudo useradd -M -d /fserver/samba/"$suser" -s /usr/sbin/nologin -G sambashare "$suser"
    sudo mkdir -p /fserver/samba/"$suser"
    sudo chown "$suser":sambashare /fserver/samba/"$suser"
    sudo chmod 2770 /fserver/samba/"$suser"
    sudo smbpasswd -a "$suser"
    sudo smbpasswd -e "$suser"
    echo "Finalizing Samba admin settings"
    echo "" | sudo tee -a /etc/samba/smb.conf
    echo "[users]" | sudo tee -a /etc/samba/smb.conf
    echo "  path = /fserver/samba/users" | sudo tee -a /etc/samba/smb.conf
    echo "  browseable = yes" | sudo tee -a /etc/samba/smb.conf
    echo "  read only = no" | sudo tee -a /etc/samba/smb.conf
    echo "  force create mode = 0660" | sudo tee -a /etc/samba/smb.conf
    echo "  force directory mode = 2770" | sudo tee -a /etc/samba/smb.conf
    echo "  valid users = @sambashare @smbadmins" | sudo tee -a /etc/samba/smb.conf
    echo "" | sudo tee -a /etc/samba/smb.conf
    echo "[smbadmins]" | sudo tee -a /etc/samba/smb.conf
    echo "  path = /fserver/samba/smbadmins" | sudo tee -a /etc/samba/smb.conf
    echo "  browseable = no" | sudo tee -a /etc/samba/smb.conf
    echo "  read only = no" | sudo tee -a /etc/samba/smb.conf
    echo "  force create mode = 0660" | sudo tee -a /etc/samba/smb.conf
    echo "  force directory mode = 2770" | sudo tee -a /etc/samba/smb.conf
    echo "  valid users = @smbadmins" | sudo tee -a /etc/samba/smb.conf
    echo "Finalizing Samba user settings"
    echo "" | sudo tee -a /etc/samba/smb.conf
    echo "[$suser]" | sudo tee -a /etc/samba/smb.conf
    echo "  path = /fserver/samba/$suser" | sudo tee -a /etc/samba/smb.conf
    echo "  browseable = no" | sudo tee -a /etc/samba/smb.conf
    echo "  read only = no" | sudo tee -a /etc/samba/smb.conf
    echo "  force create mode = 0660" | sudo tee -a /etc/samba/smb.conf
    echo "  force directory mode = 2770" | sudo tee -a /etc/samba/smb.conf
    echo "  valid users = $suser @smbadmins" | sudo tee -a /etc/samba/smb.conf
    echo "Reloading Samba and checking status"
    sudo systemctl restart smbd
    sudo systemctl status smbd
    echo "Installed Samba"
    exit 0
else
    echo "- Samba installation terminated";
    exit 1
fi
```

**Shell Script**  [Samba.sh](../Scripts/Installation/Shell/Samba.sh "Samba.sh")

### Install COVID-19 Data Analysis System
Now you will install your COVID-19 Data Analysis System. This system collects data from the COVID-19 API, an free API that provides statistical data for COVID-19 cases, deaths and recoveries. Data is sourced from Johns Hopkins CSSE.

First of all you need to download the full dataset which contains all the COVID-19 data since data started to be recorded.

To run this installation file use the following command:
```
sh Scripts/Installation/Shell/COVID19.sh
```
The contents of the above file as follows:
```
read -p "? This script will install the COVID-19 data analysis system on your HIAS Server. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
    echo "Installing COVID-19 data analysis system"
    sudo chmod -R 777 /fserver/var/www/html/Data-Analysis/COVID-19/Data/
    php Scripts/Installation/PHP/COVID19.php
    echo "Installed COVID-19 data analysis system"
    exit 0
else
    echo "- COVID-19 data analysis system installation terminated";
    exit 1
fi
```

You can update the system with the latest data by going to **Data Analysis -> COVID-19 -> Dashboard** and clicking on the refresh button. This will pull all data since the last time you refreshed.

**Shell Script**  [COVID19.sh](../Scripts/Installation/Shell/COVID19.sh "COVID19.sh")

&nbsp;

# Login To Your Server UI
![Login To Your Server UI](../Media/Images/UI.png)

Congratulations, you have the basics of the server installed!! Ensure that your Blockchain and the Context Broker & MQTT IoT Agent services are running, then you can visit your domain name and you should see the above page. You can then login with your username and password you created earlier.

![HIAS Dashboard](../Media/Images/dashboard.png)

The HIAS dashboard is your control panel for your encrypted intelligent and IoT connected  Hospital Intelligent Automation System.

**PLEASE NOTE:** The camera view you see in this screen shot is on of the modular addons for the HIAS Network.

&nbsp;

# HIAS IoT Network
![HIAS IoT Network](../Media/Images/HIAS-IoT-Dashboard.png)

The HIAS IoT network is powered by a new, fully open-source version of the [iotJumpWay](https://www.iotJumpWay.com "iotJumpWay"). The HIAS iotJumpway dashboard is your control panel for managing all of your network iotJumpWay zones, devices, sensors/actuators and applications. The modular systems that we build to be compatible with this network will all create their own iotJumpWay applications etc during installation, you will be able to manage all of these applications and devices through the iotJumpWay dashboard.

&nbsp;

# Contributing
Peter Moss Leukemia AI Research encourages and welcomes code contributions, bug fixes and enhancements from the Github community.

Please read the [CONTRIBUTING](../CONTRIBUTING.md "CONTRIBUTING") document for a full guide to forking our repositories and submitting your pull requests. You will also find information about our code of conduct on this page.

## Contributors

- [Adam Milton-Barker](https://www.leukemiaairesearch.com/team/adam-milton-barker "Adam Milton-Barker") - [Peter Moss Leukemia AI Research](https://www.leukemiaairesearch.com.ai "Peter Moss Leukemia AI Research") President/Founder & Intel Software Innovator, Sabadell, Spain

&nbsp;

# Versioning

You use SemVer for versioning. For the versions available, see [Releases](../releases "Releases").

&nbsp;

# License

This project is licensed under the **MIT License** - see the [LICENSE](../LICENSE "LICENSE") file for details.

&nbsp;

# Bugs/Issues

You use the [repo issues](../issues "repo issues") to track bugs and general requests related to using this project. See [CONTRIBUTING](../CONTRIBUTING.md "CONTRIBUTING") for more info on how to submit bugs, feature requests and proposals.