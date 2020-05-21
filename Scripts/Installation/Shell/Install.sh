#!/bin/bash

FMSG="- GeniSysAI installation terminated"

read -p "? This script will install the GeniSysAI network server on your device. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
    echo "- Installing GeniSysAI Server"
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
    sh Scripts/Installation/Shell/MySQL.sh
    if [ $? -ne 0 ]; then
        echo $FMSG;
        exit
    fi
    sh Scripts/Installation/Shell/phpMyAdmin.sh
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
    sh Scripts/Installation/Shell/iotJumpWay.sh
    if [ $? -ne 0 ]; then
        echo $FMSG;
        exit
    fi
    sh Scripts/Installation/Shell/iotJumpWayLocation.sh
    if [ $? -ne 0 ]; then
        echo $FMSG;
        exit
    fi
    sh Scripts/Installation/Shell/Finalize.sh
    if [ $? -ne 0 ]; then
        echo $FMSG;
        exit
    fi
    sh Scripts/Installation/Shell/Admin.sh
    if [ $? -ne 0 ]; then
        echo $FMSG;
        exit
    fi
    sh Scripts/Installation/Shell/TASS.sh
    if [ $? -ne 0 ]; then
        echo $FMSG;
        exit
    fi
    sh Scripts/Installation/Shell/iotJumpWayTASS.sh
    if [ $? -ne 0 ]; then
        echo $FMSG;
        exit
    fi
else
    echo $FMSG;
    exit
fi
