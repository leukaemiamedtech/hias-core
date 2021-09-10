cp -L "$RENEWED_LINEAGE/{fullchain,privkey,certs}.pem"  /certs/
chown :hiascore /certs/*.pem
systemctl restart nginx
systemctl restart mosquitto
systemctl restart slapd