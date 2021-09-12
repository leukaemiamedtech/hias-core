<?php

	class AI
	{
		function __construct($hias)
		{
			$this->hias = $hias;
		}

		public function get_model_types()
		{
			$pdoQuery = $this->hias->conn->prepare("
				SELECT *
				FROM hiascdi_ai_models
				ORDER BY model DESC
			");
			$pdoQuery->execute();
			$model_types=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $model_types;
		}

		public function get_model_categories()
		{
			$pdoQuery = $this->hias->conn->prepare("
				SELECT *
				FROM hiascdi_ai_model_categories
				ORDER BY category DESC
			");
			$pdoQuery->execute();
			$model_types=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $model_types;
		}

		public function get_models($limit = 0)
		{
			$limiter = "";
			if($limit != 0):
				$limiter = "&limit=" . $limit;
			endif;

			$request = $this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "?type=Model".$limiter, []);
			$models = json_decode($request["body"], true);
			return $models;
		}

		public function get_model($id, $attrs = Null)
		{

			if($attrs):
				$attrs="&attrs=" . $attrs;
			endif;

			$request = $this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "/" . $id . "?type=Model" . $attrs, []);
			$device = json_decode($request["body"], true);
			return $device;
		}

		public function create_model()
		{
			if(!filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Category is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Name is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Description is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "ntype", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Network type is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "ntype", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Network type is required"
				];
			endif;

			$pubKey = $this->hias->helpers->generate_uuid();

			$properties=[];
			if(isSet($_POST["properties"])):
				foreach($_POST["properties"] AS $key => $value):
					$properties[$value] = ["value" => ""];
				endforeach;
			endif;

			$commands=[];
			if(isSet($_POST["commands"])):
				foreach($_POST["commands"] AS $key => $value):
					$values = explode(",", $value);
					$commands[$key] = $values;
				endforeach;
			endif;

			$states=[];
			$state=[];
			if(isSet($_POST["states"])):
				$states = $_POST["states"];
				$state = "";
			endif;

			$data = [
				"id" => $pubKey,
				"type" => "Model",
				"category" => [
					"value" => [filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)]
				],
				"name" => [
					"value" => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)
				],
				"description" => [
					"value" => filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)
				],
				"modelType" => [
					"value" => filter_input(INPUT_POST, "mtype", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Model Type"
						]
					]
				],
				"modelLink" => [
					"value" => filter_input(INPUT_POST, "link", FILTER_SANITIZE_STRING),
					"type" => "URL",
					"metadata" => [
						"description" => [
							"value" => "Model link"
						]
					]
				],
				"modelAuthor" => [
					"value" => filter_input(INPUT_POST, "author", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Model Author"
						]
					]
				],
				"modelAuthorLink" => [
					"value" => filter_input(INPUT_POST, "authorLink", FILTER_SANITIZE_STRING),
					"type" => "URL",
					"metadata" => [
						"description" => [
							"value" => "Model Author"
						]
					]
				],
				"modelAccuracy" => [
					"value" => filter_input(INPUT_POST, "accuracy", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Achieved model accuracy"
						]
					]
				],
				"modelSpecificity" => [
					"value" => filter_input(INPUT_POST, "specificity", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Achieved model specificity"
						]
					]
				],
				"modelPrecision" => [
					"value" => filter_input(INPUT_POST, "precision", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Achieved model precision"
						]
					]
				],
				"modelAuc" => [
					"value" => filter_input(INPUT_POST, "auc", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Achieved AUC (Area Under Curve) / ROC (Receiver operating characteristic)"
						]
					]
				],
				"modelRecall" => [
					"value" => filter_input(INPUT_POST, "recall", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Achieved recall"
						]
					]
				],
				"modelTruePositives" => [
					"value" => filter_input(INPUT_POST, "truePositives", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Number of true positives generated by the model"
						]
					]
				],
				"modelFalsePositives" => [
					"value" => filter_input(INPUT_POST, "falsePositives", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Number of false positives generated by the model"
						]
					]
				],
				"modelTrueNegatives" => [
					"value" => filter_input(INPUT_POST, "trueNegatives", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Number of true negatives generated by the model"
						]
					]
				],
				"modelFalseNegatives" => [
					"value" => filter_input(INPUT_POST, "falseNegatives", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Number of false negatives generated by the model"
						]
					]
				],
				"modelMisclassification" => [
					"value" => filter_input(INPUT_POST, "misclassification", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Total model misclassification"
						]
					]
				],
				"networkArchitecture" => [
					"value" => filter_input(INPUT_POST, "ntype", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Model Network Architecture"
						]
					]
				],
				"programmingLanguage" => [
					"value" => filter_input(INPUT_POST, "language", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Model Programming Language"
						]
					]
				],
				"programmingFramework" => [
					"value" => filter_input(INPUT_POST, "framework", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Model Programming Framework"
						]
					]
				],
				"programmingToolkit" => [
					"value" => filter_input(INPUT_POST, "toolkit", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Model Programming Toolkit"
						]
					]
				],
				"datasetName" => [
					"value" => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Model Dataset"
						]
					]
				],
				"datasetType" => [
					"value" => filter_input(INPUT_POST, "datasetType", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Model Dataset Type"
						]
					]
				],
				"datasetLink" => [
					"value" => filter_input(INPUT_POST, "datasetLink", FILTER_SANITIZE_STRING),
					"type" => "URL",
					"metadata" => [
						"description" => [
							"value" => "Model Dataset Link"
						]
					]
				],
				"datasetAuthor" => [
					"value" => filter_input(INPUT_POST, "datasetAuthor", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Model Dataset Author(s)"
						]
					]
				],
				"datasetAuthorLink" => [
					"value" => filter_input(INPUT_POST, "datasetAuthorLink", FILTER_SANITIZE_STRING),
					"type" => "URL",
					"metadata" => [
						"description" => [
							"value" => "Model Dataset Author(s)"
						]
					]
				],
				"dataAugmentation" => [
					"value" => filter_input(INPUT_POST, "datasetAugmentation", FILTER_SANITIZE_STRING),
					"type" => "Boolean",
					"metadata" => [
						"description" => [
							"value" => "Model Dataset Augmentation"
						]
					]
				],
				"dataPositiveLabel" => [
					"value" => filter_input(INPUT_POST, "datasetPosLabel", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Model Dataset Positive Label"
						]
					]
				],
				"dataNegativeLabel" => [
					"value" => filter_input(INPUT_POST, "datasetNegLabel", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Model Dataset Negative Label"
						]
					]
				],
				"paperTitle" => [
					"value" => filter_input(INPUT_POST, "relatedPaper", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Title of related paper"
						]
					]
				],
				"paperAuthor" => [
					"value" => filter_input(INPUT_POST, "relatedPaperAuthor", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Author(s) of related paper"
						]
					]
				],
				"paperDoi" => [
					"value" => filter_input(INPUT_POST, "relatedPaperDOI", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "DOI of related paper"
						]
					]
				],
				"paperLink" => [
					"value" => filter_input(INPUT_POST, "relatedPaperLink", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Link to related paper"
						]
					]
				],
				"properties" => [
					"value" => $properties,
					"type" => "StructuredValue",
					"metadata" => [
						"description" => [
							"value" => "Model properties"
						]
					]
				],
				"commands" => [
					"value" => $commands,
					"type" => "StructuredValue",
					"metadata" => [
						"description" => [
							"value" => "Model commands"
						]
					]
				],
				"states" => [
					"value" => $states,
					"type" => "StructuredValue",
					"metadata" => [
						"description" => [
							"value" => "Model states"
						]
					]
				],
				"state" => [
					"value" => $state,
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Model state"
						]
					]
				],
				"dateCreated" => [
					"type" => "DateTime",
					"value" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"dateModified" => [
					"type" => "DateTime",
					"value" => date('Y-m-d\TH:i:s.Z\Z', time())
				]
			];

			$request = $this->hias->hiascdi->request("POST", $this->hias->hiascdi->confs["entities_url"] . "?type=Model", json_encode($data));
			$response = json_decode($request["body"], true);

			if(!isSet($response["Error"])):
				return [
					"Response"=> "OK",
					"Message" => "Model Created!"
				];
			else:
				return [
					"Response"=> "FAILED",
					"Message" => "Model Created KO! " . $response["Description"]
				];
			endif;
		}

		public function update_model()
		{
			if(!filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Category is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Name is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Description is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "ntype", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Network type is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "ntype", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Network type is required"
				];
			endif;

			$properties=[];
			if(isSet($_POST["properties"])):
				foreach($_POST["properties"] AS $key => $value):
					$properties[$value] = ["value" => ""];
				endforeach;
			endif;

			$commands=[];
			if(isSet($_POST["commands"])):
				foreach($_POST["commands"] AS $key => $value):
					$values = explode(",", $value);
					$commands[$key] = $values;
				endforeach;
			endif;

			$states=[];
			$state=[];
			if(isSet($_POST["states"])):
				$states = $_POST["states"];
				$state = "";
			endif;

			$mid = filter_input(INPUT_GET, 'model', FILTER_SANITIZE_STRING);

			$data = [
				"category" => [
					"value" => [filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)]
				],
				"name" => [
					"value" => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)
				],
				"description" => [
					"value" => filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)
				],
				"modelType" => [
					"value" => filter_input(INPUT_POST, "mtype", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Model Type"
						]
					]
				],
				"modelLink" => [
					"value" => filter_input(INPUT_POST, "link", FILTER_SANITIZE_STRING),
					"type" => "URL",
					"metadata" => [
						"description" => [
							"value" => "Model link"
						]
					]
				],
				"modelAuthor" => [
					"value" => filter_input(INPUT_POST, "author", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Model Author"
						]
					]
				],
				"modelAuthorLink" => [
					"value" => filter_input(INPUT_POST, "authorLink", FILTER_SANITIZE_STRING),
					"type" => "URL",
					"metadata" => [
						"description" => [
							"value" => "Model Author"
						]
					]
				],
				"modelAccuracy" => [
					"value" => filter_input(INPUT_POST, "accuracy", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Achieved model accuracy"
						]
					]
				],
				"modelSpecificity" => [
					"value" => filter_input(INPUT_POST, "specificity", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Achieved model specificity"
						]
					]
				],
				"modelPrecision" => [
					"value" => filter_input(INPUT_POST, "precision", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Achieved model precision"
						]
					]
				],
				"modelAuc" => [
					"value" => filter_input(INPUT_POST, "auc", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Achieved AUC (Area Under Curve) / ROC (Receiver operating characteristic)"
						]
					]
				],
				"modelRecall" => [
					"value" => filter_input(INPUT_POST, "recall", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Achieved recall"
						]
					]
				],
				"modelTruePositives" => [
					"value" => filter_input(INPUT_POST, "truePositives", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Number of true positives generated by the model"
						]
					]
				],
				"modelFalsePositives" => [
					"value" => filter_input(INPUT_POST, "falsePositives", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Number of false positives generated by the model"
						]
					]
				],
				"modelTrueNegatives" => [
					"value" => filter_input(INPUT_POST, "trueNegatives", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Number of true negatives generated by the model"
						]
					]
				],
				"modelFalseNegatives" => [
					"value" => filter_input(INPUT_POST, "falseNegatives", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Number of false negatives generated by the model"
						]
					]
				],
				"modelMisclassification" => [
					"value" => filter_input(INPUT_POST, "misclassification", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Total model misclassification"
						]
					]
				],
				"networkArchitecture" => [
					"value" => filter_input(INPUT_POST, "ntype", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Model Network Architecture"
						]
					]
				],
				"programmingLanguage" => [
					"value" => filter_input(INPUT_POST, "language", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Model Programming Language"
						]
					]
				],
				"programmingFramework" => [
					"value" => filter_input(INPUT_POST, "framework", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Model Programming Framework"
						]
					]
				],
				"programmingToolkit" => [
					"value" => filter_input(INPUT_POST, "toolkit", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Model Programming Toolkit"
						]
					]
				],
				"datasetName" => [
					"value" => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Model Dataset"
						]
					]
				],
				"datasetType" => [
					"value" => filter_input(INPUT_POST, "datasetType", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Model Dataset Type"
						]
					]
				],
				"datasetLink" => [
					"value" => filter_input(INPUT_POST, "datasetLink", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Model Dataset Link"
						]
					]
				],
				"datasetAuthor" => [
					"value" => filter_input(INPUT_POST, "datasetAuthor", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Model Dataset Author(s)"
						]
					]
				],
				"datasetAuthorLink" => [
					"value" => filter_input(INPUT_POST, "datasetAuthorLink", FILTER_SANITIZE_STRING),
					"type" => "URL",
					"metadata" => [
						"description" => [
							"value" => "Model Dataset Author(s)"
						]
					]
				],
				"dataAugmentation" => [
					"value" => filter_input(INPUT_POST, "datasetAugmentation", FILTER_SANITIZE_STRING),
					"type" => "Boolean",
					"metadata" => [
						"description" => [
							"value" => "Model Dataset Augmentation"
						]
					]
				],
				"dataPositiveLabel" => [
					"value" => filter_input(INPUT_POST, "datasetPosLabel", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Model Dataset Positive Label"
						]
					]
				],
				"dataNegativeLabel" => [
					"value" => filter_input(INPUT_POST, "datasetNegLabel", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Model Dataset Negative Label"
						]
					]
				],
				"paperTitle" => [
					"value" => filter_input(INPUT_POST, "relatedPaper", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Title of related paper"
						]
					]
				],
				"paperAuthor" => [
					"value" => filter_input(INPUT_POST, "relatedPaperAuthor", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Author(s) of related paper"
						]
					]
				],
				"paperDoi" => [
					"value" => filter_input(INPUT_POST, "relatedPaperDOI", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "DOI of related paper"
						]
					]
				],
				"paperLink" => [
					"value" => filter_input(INPUT_POST, "relatedPaperLink", FILTER_SANITIZE_STRING),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Link to related paper"
						]
					]
				],
				"properties" => [
					"value" => $properties,
					"type" => "StructuredValue",
					"metadata" => [
						"description" => [
							"value" => "Model properties"
						]
					]
				],
				"commands" => [
					"value" => $commands,
					"type" => "StructuredValue",
					"metadata" => [
						"description" => [
							"value" => "Model commands"
						]
					]
				],
				"states" => [
					"value" => $states,
					"type" => "StructuredValue",
					"metadata" => [
						"description" => [
							"value" => "Model states"
						]
					]
				],
				"state" => [
					"value" => $state,
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Model state"
						]
					]
				],
				"dateModified" => [
					"type" => "DateTime",
					"value" => date('Y-m-d\TH:i:s.Z\Z', time())
				]
			];

			$request = $this->hias->hiascdi->request("POST", $this->hias->hiascdi->confs["entities_url"]  . "/" . $mid .  "/attrs?type=Model", json_encode($data));
			$response = json_decode($request["body"], true);

			if(!isSet($response["Error"])):
				return [
					"Response"=> "OK",
					"Message" => "Model Updated!"
				];
			else:
				return [
					"Response"=> "FAILED",
					"Message" => "Model Update KO! " . $response["Description"]
				];
			endif;
		}

	 }

	 $AI = new AI($HIAS);

	 if(filter_input(INPUT_POST, "create_ai_model", FILTER_SANITIZE_NUMBER_INT)):
		 die(json_encode($AI->create_model()));
	 endif;

	 if(filter_input(INPUT_POST, "update_ai_model", FILTER_SANITIZE_NUMBER_INT)):
		 die(json_encode($AI->update_model()));
	 endif;