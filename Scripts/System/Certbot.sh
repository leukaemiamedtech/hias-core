cp -L "$RENEWED_LINEAGE/{fullchain,privkey}.pem" /fserver/certs
chown $USER:$USER /fserver/libraries/mosquitto/certs/*.pem