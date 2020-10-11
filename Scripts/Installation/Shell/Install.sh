#!/bin/bash

FMSG="- HIAS Server installation terminated"

read -p "? This script will install the HIAS Server on your device. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
    echo "- Installing HIAS Server"
    sh Scripts/Installation/Shell/UFW.sh
    if [ $? -ne 0 ]; then
        echo $FMSG;
        exit
    fi
    sh Scripts/Installation/Shell/Fail2Ban.sh
    if [ $? -ne 0 ]; then
        echo $FMSG;
        exit
    fi
    sh Scripts/Installation/Shell/NGINX.sh
    if [ $? -ne 0 ]; then
        echo $FMSG;
        exit
    fi
    sh Scripts/Installation/Shell/LetsEncrypt.sh
    if [ $? -ne 0 ]; then
        echo $FMSG;
        exit
    fi
    sh Scripts/Installation/Shell/PHP.sh
    if [ $? -ne 0 ]; then
        echo $FMSG;
        exit
    fi
    bash Scripts/Installation/Shell/MySQL.sh
    if [ $? -ne 0 ]; then
        echo $FMSG;
        exit
    fi
    bash Scripts/Installation/Shell/phpMyAdmin.sh
    if [ $? -ne 0 ]; then
        echo $FMSG;
        exit
    fi
    bash Scripts/Installation/Shell/MongoDB.sh
    if [ $? -ne 0 ]; then
        echo $FMSG;
        exit
    fi
    sh Scripts/Installation/Shell/SSL.sh
    if [ $? -ne 0 ]; then
        echo $FMSG;
        exit
    fi
    sh Scripts/Installation/Shell/Samba.sh
    if [ $? -ne 0 ]; then
        echo $FMSG;
        exit
    fi
    bash Scripts/Installation/Shell/Blockchain.sh
    if [ $? -ne 0 ]; then
        echo $FMSG;
        exit
    fi
    bash Scripts/Installation/Shell/iotJumpWay.sh
    if [ $? -ne 0 ]; then
        echo $FMSG;
        exit
    fi
    bash Scripts/Installation/Shell/iotJumpWayLocation.sh
    if [ $? -ne 0 ]; then
        echo $FMSG;
        exit
    fi
    bash Scripts/Installation/Shell/Admin.sh
    if [ $? -ne 0 ]; then
        echo $FMSG;
        exit
    fi
    bash Scripts/Installation/Shell/TassAI.sh
    if [ $? -ne 0 ]; then
        echo $FMSG;
        exit
    fi
    sh Scripts/Installation/Shell/COVID19.sh
    if [ $? -ne 0 ]; then
        echo $FMSG;
        exit
    fi
    sh Scripts/Installation/Shell/Services.sh
    if [ $? -ne 0 ]; then
        echo $FMSG;
        exit
    fi
    bash Scripts/Installation/Shell/Finalize.sh
    if [ $? -ne 0 ]; then
        echo $FMSG;
        exit
    fi
else
    echo $FMSG;
    exit
fi
