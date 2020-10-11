cp -L "$RENEWED_LINEAGE/{fullchain,privkey}.pem" /fserver/libraries/mosquitto/certs
chown user:user /fserver/libraries/mosquitto/certs/*.pem