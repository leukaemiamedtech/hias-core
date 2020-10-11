#!/bin/bash

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