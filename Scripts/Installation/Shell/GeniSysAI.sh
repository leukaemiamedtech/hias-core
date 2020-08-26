#!/bin/bash

read -p "? This script will install GeniSysAI on your server. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
    echo "- Installing GeniSysAI...."
    sudo apt install python3-pip
    sudo apt install cmake
    sudo apt install python3-opencv
    sudo mkdir -p /fserver/models/GeniSysAI
    pip3 install zmq
    pip3 install dlib
    pip3 install imutils
    wget http://dlib.net/files/shape_predictor_68_face_landmarks.dat.bz2 -P /fserver/models/GeniSysAI/
    wget http://dlib.net/files/dlib_face_recognition_resnet_model_v1.dat.bz2 -P /fserver/models/GeniSysAI/
    sudo bzip2 /fserver/models/GeniSysAI/shape_predictor_68_face_landmarks.dat.bz2 --decompress
    sudo bzip2 /fserver/models/GeniSysAI/dlib_face_recognition_resnet_model_v1.dat.bz2 --decompress
    echo "- Installed GeniSysAI!";
    exit 0
else
    echo "- GeniSysAI installation terminated!";
    exit 1
fi
