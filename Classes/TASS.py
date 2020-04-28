############################################################################################
#
# Project:       Peter Moss COVID-19 AI Research Project
# Repository:    COVID-19 Medical Support System Server
# Project:       EMAR, Emergency Assistance Robot
#
# Author:        Adam Milton-Barker (AdamMiltonBarker.com)
# Contributors:
# Title:         TASS Class
# Description:   TASS functions for the COVID-19 Medical Support System Server Emergency 
#                Assistance Robot.
# License:       MIT License
# Last Modified: 2020-04-19
#
############################################################################################

import cv2, dlib, os

import numpy as np

from Classes.Helpers import Helpers

class TASS():
    
    def __init__(self):
        """ TASS Class
    
        TASS functions for the COVID-19 Medical Support System Server Emergency
        Assistance Robot.
        """
        
        self.Helpers = Helpers("TASS", False)
        
        # Sets up DLIB features
        self.detector = dlib.get_frontal_face_detector()
        self.predictor = dlib.shape_predictor(self.Helpers.confs["tass"]["dlib"])
        self.recognizer = dlib.face_recognition_model_v1(self.Helpers.confs["tass"]["dlibr"])
        
        self.Helpers.logger.info("TASS Helper Class initialization complete.")
        
    def connect(self):
        """ Connects to the local TASS. """
        
        self.lcv = cv2.VideoCapture(self.Helpers.confs["tass"]["vid"])
        
        self.Helpers.logger.info("Connected To TASS")
        
    def processim(self, frame):
        """ Reads & processes frame from the local TASS. """
    
        # Makes a copy of the frame
        raw = frame.copy()
        # Resizes the frame
        frame = cv2.resize(frame, (640, 480)) 
        # Converts to grayscale
        gray = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)
        
        return raw, frame, gray
    
    def preprocess(self):
        """ Encodes the known users images. """
        
        self.encoded = []
        
        # Loops through all images in the security folder
        for filename in os.listdir(self.Helpers.confs["tass"]["data"]):
            # Checks file type
            if filename.lower().endswith(tuple(self.Helpers.confs["tass"]["core"]["allowed"])):
                fpath = os.path.join(self.Helpers.confs["tass"]["data"], filename)
                # Gets user id from filename
                user = os.path.splitext(filename)[0]
                # Reads the image
                image = cv2.imread(fpath)
                # Gets faces and coordinates
                faces, coords = self.faces(image)
                # Saves the user id and encoded image to a list
                self.encoded.append((user, self.encode(image, coords)[0]))
        
        self.Helpers.logger.info("Known data preprocessed!")
    
    def faces(self, image):
        """ Finds faces and their coordinates in an image. """
        
        # Find faces
        faces = self.detector(image, 1)
        # Gets coordinates for faces
        coords = [self.predictor(image, face) for face in faces]
        
        return faces, coords
        
    def encode(self, image, coords):
        """ Encodes an image. """
        
        return [np.array(self.recognizer.compute_face_descriptor(image, pose, 1)) for pose in coords]
            
    def compare(self, known, face):
        """ Compares two encodings. """
        
        # Calculate if difference is less than or equal to threshold
        return (np.linalg.norm(known - face, axis=1) <= self.Helpers.confs["tass"]["threshold"])
    
    def match(self, frame, coords):
        """ Checks faces for matches against known users. """
             
        person = 0
        result = "Unknown"
        
        i = 0
        # Loops through known encodings
        for enc in self.encoded:
            # Encode current frame 
            encoded = self.encode(frame, coords[i])
            # Calculate if difference is less than or equal to 
            matches = self.compare(enc[1], encoded) 
            # Loops through matches
            if matches[0] == True:
                # If known add people
                result = "User " + str(enc[0])
                person = int(enc[0])
                msg = "TASS identified User #" + str(person)
            else:
                # If unknown add people
                msg = "TASS identified unknown!"
            self.Helpers.logger.info(msg)
            i+=1
                    
        return person, result
    