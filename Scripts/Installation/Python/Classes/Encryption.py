############################################################################################
#
# Project:       Peter Moss COVID-19 AI Research Project
# Repository:    COVID-19 Medical Support System Server
# Module:        Encryption
#
# Author:        Adam Milton-Barker (AdamMiltonBarker.com)
# Contributors:
# Title:         Encrytion
# Description:   Handles PHP / Python compatible encryption.
# License:       MIT License
# Last Modified: 2020-04-09
#
############################################################################################

import base64, hashlib, json, sys

from Crypto.Cipher import AES
from Crypto.Random import get_random_bytes

class Encryption():
    """ COVID-19 Medical Support System Server Encryption Class

    Handles PHP / Python compatible encryption for the Medical Support System Server.
    """

    def __init__(self):
        """ Initializes the class. """

        self.loadConfs()
        self.bs = 32
        self.key = hashlib.sha256(self.confs["key"].encode()).digest()[:32]

    def loadConfs(self):
        """ Load the server configuration. """

        with open("Root/var/www/Classes/Core/confs.json") as confs:
            self.confs = json.loads(confs.read())

    def paddit(self, data):
        """ Adds padding """

        return data + (self.bs - len(data) % self.bs) * chr(self.bs - len(data) % self.bs)


    def unpaddit(self, data):
        """ Removes padding """

        return data[:-ord(data[-1:])]

    def encrypt(self, data):
        """ Encrypts data """

        iv = get_random_bytes(AES.block_size)
        iv = hashlib.sha256(iv).hexdigest()[:16].encode("utf-8")

        cipher = AES.new(self.key, AES.MODE_CFB, iv)
        encrypted = cipher.encrypt(self.paddit(data))

        return base64.b64encode(encrypted + "::".encode('utf-8') + iv)


    def decrypt(self, data):
        """ Decrypts data """

        enc, iv = base64.b64decode(data).split("::".encode('utf-8'))
        cipher = AES.new(self.key, AES.MODE_CFB, iv)

        return self.unpaddit(cipher.decrypt(enc))