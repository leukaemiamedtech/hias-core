######################################################################################################
#
# Organization:  Peter Moss Leukemia AI Research
# Repository:    HIAS: Hospital Intelligent Automation System
#
# Author:        Adam Milton-Barker (AdamMiltonBarker.com)
#
# Title:         TassAI Class
# Description:   TassAI functions for the Hospital Intelligent Automation System.
# License:       MIT License
# Last Modified: 2020-08-26
#
######################################################################################################

import cv2
import os
import os.path as osp

import numpy as np

from Classes.Helpers import Helpers

from Classes.OpenVINO.ie_module import InferenceContext
from Classes.OpenVINO.landmarks_detector import LandmarksDetector
from Classes.OpenVINO.face_detector import FaceDetector
from Classes.OpenVINO.faces_database import FacesDatabase
from Classes.OpenVINO.face_identifier import FaceIdentifier

class TassAI():

	def __init__(self):
		""" TassAI Class

		TassAI functions for the Hospital Intelligent Automation System.
		"""

		self.Helpers = Helpers("TassAI", False)

		self.qs = 16
		self.context = InferenceContext([self.Helpers.confs["TassAI"]["runas"], self.Helpers.confs["TassAI"]["runas"], self.Helpers.confs["TassAI"]["runas"]], "", "", "")

		self.Helpers.logger.info("TassAI Helper Class initialization complete.")

	def load_models(self):
		""" Loads all models. """

		face_detector_net = self.load_model(
			self.Helpers.confs["TassAI"]["detection"])
		face_detector_net.reshape({"data": [1, 3, 384, 672]})

		landmarks_net = self.load_model(
			self.Helpers.confs["TassAI"]["landmarks"])

		face_reid_net = self.load_model(
			self.Helpers.confs["TassAI"]["reidentification"])

		self.face_detector = FaceDetector(face_detector_net,
											confidence_threshold=0.6,
											roi_scale_factor=1.15)

		self.landmarks_detector = LandmarksDetector(landmarks_net)

		self.face_identifier = FaceIdentifier(face_reid_net,
												match_threshold=0.3,
												match_algo='HUNGARIAN')

		self.face_detector.deploy(self.Helpers.confs["TassAI"]["runas"], self.context)
		self.landmarks_detector.deploy(self.Helpers.confs["TassAI"]["runas"], self.context,
										queue_size=self.qs)
		self.face_identifier.deploy(self.Helpers.confs["TassAI"]["runas"], self.context,
									queue_size=self.qs)

		self.Helpers.logger.info("Models loaded")

	def load_model(self, model_path):
		""" Loads a model from path. """

		model_path = osp.abspath(model_path)
		model_weights_path = osp.splitext(model_path)[0] + ".bin"

		self.Helpers.logger.info("Loading the model from '%s'" % (model_path))
		model = self.context.ie_core.read_network(model_path, model_weights_path)
		self.Helpers.logger.info("Model loaded")

		return model

	def load_known(self):
		""" Loads known data. """

		self.faces_database = FacesDatabase(self.Helpers.confs["TassAI"]["data"], self.face_identifier,
											self.landmarks_detector, self.face_detector, True)
		self.face_identifier.set_faces_database(self.faces_database)
		self.Helpers.logger.info("Database is built, registered %s identities" %
					(len(self.faces_database)))

	def process(self, frame):
		""" Processes a frame. """

		orig_image = frame.copy()
		frame = frame.transpose((2, 0, 1))
		frame = np.expand_dims(frame, axis=0)

		self.face_detector.clear()
		self.landmarks_detector.clear()
		self.face_identifier.clear()

		self.face_detector.start_async(frame)
		rois = self.face_detector.get_roi_proposals(frame)
		if self.qs < len(rois):
			self.Helpers.logger.info("Too many faces for processing." \
					" Will be processed only %s of %s." % \
					(self.qs, len(rois)))
			rois = rois[:self.qs]
		self.landmarks_detector.start_async(frame, rois)
		landmarks = self.landmarks_detector.get_landmarks()

		self.face_identifier.start_async(frame, rois, landmarks)
		face_identities, unknowns = self.face_identifier.get_matches()

		outputs = [rois, landmarks, face_identities]

		return outputs

	def draw_text_with_background(self, frame, text, origin,
									font=cv2.FONT_HERSHEY_SIMPLEX, scale=1.0,
									color=(0, 0, 0), thickness=1, bgcolor=(255, 255, 255)):
		text_size, baseline = cv2.getTextSize(text, font, scale, thickness)
		cv2.rectangle(frame,
						tuple((origin + (0, baseline)).astype(int)),
						tuple((origin + (text_size[0], -text_size[1])).astype(int)),
						bgcolor, cv2.FILLED)
		cv2.putText(frame, text,
					tuple(origin.astype(int)),
					font, scale, color, thickness)
		return text_size, baseline

	def draw_detection_roi(self, frame, roi, identity):
		label = self.face_identifier.get_identity_label(identity.id)

		# Draw face ROI border
		cv2.rectangle(frame,
					tuple(roi.position), tuple(roi.position + roi.size),
					(0, 220, 0), 2)

		# Draw identity label
		text_scale = 0.5
		font = cv2.FONT_HERSHEY_SIMPLEX
		text_size = cv2.getTextSize("H1", font, text_scale, 1)
		line_height = np.array([0, text_size[0][1]])
		if label is "Unknown":
			text = label
		else:
			text = "User #" + label
		if identity.id != FaceIdentifier.UNKNOWN_ID:
			text += ' %.2f%%' % (100.0 * (1 - identity.distance))
		self.draw_text_with_background(frame, text,
										roi.position - line_height * 0.5,
										font, scale=text_scale)

		return frame, label

	def draw_detection_keypoints(self, frame, roi, landmarks):
		keypoints = [landmarks.left_eye,
				landmarks.right_eye,
				landmarks.nose_tip,
				landmarks.left_lip_corner,
				landmarks.right_lip_corner,
				landmarks.right_lip_corner]

		for point in keypoints:
				center = roi.position + roi.size * point
				cv2.circle(frame, tuple(center.astype(int)), 2, (0, 255, 255), 2)

		return frame
