#!/bin/bash

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