#!/bin/bash

read -p "? This script will install TASSAI on your server. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
    echo "- Installing TASSAI...."
    sudo apt install python3-pip
    sudo apt install cmake
    sudo apt install python3-opencv
    sudo mkdir -p /fserver/models/TASS
    pip3 install zmq
    pip3 install dlib
    pip3 install imutils
    wget http://dlib.net/files/shape_predictor_68_face_landmarks.dat.bz2 -P /fserver/models/TASS/
    wget http://dlib.net/files/dlib_face_recognition_resnet_model_v1.dat.bz2 -P /fserver/models/TASS/
    sudo bzip2 /fserver/models/TASS/shape_predictor_68_face_landmarks.dat.bz2 --decompress
    sudo bzip2 /fserver/models/TASS/dlib_face_recognition_resnet_model_v1.dat.bz2 --decompress
    echo "- Installed TASSAI!";
    exit 0
else
    echo "- TASSAI installation terminated!";
    exit 1
fi
