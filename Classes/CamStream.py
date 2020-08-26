############################################################################################
#
# Project:       Peter Moss Leukemia AI Research
# Repository:    HIAS: Hospital Intelligent Automation System
# Project:       GeniSysAI
#
# Author:        Adam Milton-Barker (AdamMiltonBarker.com)
# Contributors:
# Title:         CamRead Class
# Description:   The CamStream Class streams the processed frame to a local server.
# License:       MIT License
# Last Modified: 2020-06-04
#
############################################################################################

import base64, cv2, sys, time

import numpy as np

from http.server import BaseHTTPRequestHandler, HTTPServer
from socketserver import ThreadingMixIn
from threading import Thread

from io import BytesIO
from PIL import Image

from Classes.Helpers import Helpers
from Classes.Socket import Socket

capture = None

class CamStream(Thread):
    """ CamStream Class

    The CamStream Class streams the processed frame to a local server.
    """

    def __init__(self):
        """ Initializes the class. """

        super(CamStream, self).__init__()
        self.Helpers = Helpers("CamStream")

        self.Helpers.logger.info("CamStream Helper Class initialization complete.")

    def run(self):
        """ Runs the module. """
        global capture

        # Allows time for socket server to start
        self.Helpers.logger.info("CamStream waiting 2 seconds for CamRead socket server.")
        time.sleep(2)
        self.Helpers.logger.info("CamStream continuing.")
        # Starts the socket module
        self.Socket = Socket("CamStream")
        # Subscribes to the socket server
        capture = self.Socket.subscribe(self.Helpers.confs["genisysai"]["socket"]["ip"],
                                     self.Helpers.confs["genisysai"]["socket"]["port"])

        try:
            # Starts web server
            server = ThreadedHTTPServer((self.Helpers.confs["genisysai"]["ip"], self.Helpers.confs["genisysai"]["port"]), CamHandler)
            self.Helpers.logger.info("Stream server started on http://" + self.Helpers.confs["genisysai"]["ip"] + ":" + str(self.Helpers.confs["genisysai"]["port"]))
            server.serve_forever()
        except KeyboardInterrupt:
            # Closes socket server
            capture.close()
            exit()

class CamHandler(BaseHTTPRequestHandler):
    def do_GET(self):
        # Responds to .mjpg requests
        if self.path.endswith('.mjpg'):
            # Sets headers and response
            self.send_response(200)
            self.send_header('Content-type', 'multipart/x-mixed-replace; boundary=--jpgboundary')
            self.end_headers()

            try:
                while True:
                    # Gets processed frame from socket server
                    frame = capture.recv_string()
                    # Decodes the frame
                    frame = cv2.imdecode(np.fromstring(base64.b64decode(frame), dtype=np.uint8), 1)
                    # Converts image to RGB
                    rgb = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
                    # Converts rgb array to jpg image
                    jpg = Image.fromarray(rgb)
                    # Creates a temporary file
                    tmpFile = BytesIO()
                    # Saves the jpg to the temp file
                    jpg.save(tmpFile,'JPEG')
                    # Set boundaries
                    self.wfile.write("--jpgboundary".encode())
                    # Finish headers
                    self.send_header('Content-type', 'image/jpeg')
                    self.send_header('Content-length', str(tmpFile.getbuffer().nbytes))
                    self.end_headers()
                    # Send the frame to the browser
                    self.wfile.write(tmpFile.getvalue())
            except Exception as e:
                print("errror " + str(e))
                return
            return

        else:
            # Sets headers and response
            self.send_response(403)
            self.send_header('Content-type','multipart/x-mixed-replace; boundary=--jpgboundary')
            self.end_headers()

class ThreadedHTTPServer(ThreadingMixIn, HTTPServer):
	"""Handle requests in a separate thread."""