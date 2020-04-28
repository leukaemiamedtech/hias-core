sudo apt purge nginx nginx-common
sudo apt autoremove
sudo rm -rf /etc/nginx
sudo apt remove --purge mysql*
sudo apt purge mysql*
sudo apt autoclean
sudo certbot delete
sudo apt purge python-certbot-apache
sudo rm -rf /etc/letsencrypt/
sudo rm -rf /var/lib/letsencrypt/
sudo rm -rf /var/log/letsencrypt/
sudo rm -rf /var/www/html/
sudo apt purge phpmyadmin*
sudo apt autoremove samba samba-common
sudo apt purge samba samba-common
sudo apt remove --purge fail2ban*
sudo rm -rf /etc/fail2ban
sudo pip3 uninstall JumpWayMQTT