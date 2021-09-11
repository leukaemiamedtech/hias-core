<?php session_start();

$pageDetails = [
	"PageID" => "AI",
	"SubPageID" => "Models"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../AI/Classes/AI.php';

$mid = filter_input(INPUT_GET, 'model', FILTER_SANITIZE_STRING);
$model = $AI->get_model($mid, "dateCreated,dateModified,*");

?>

<!DOCTYPE html>
<html lang="en">

<head>
	 <meta charset="UTF-8" />
	 <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	 <meta name="robots" content="noindex, nofollow" />

	 <title><?=$HIAS->confs["meta_title"]; ?></title>
	 <meta name="description" content="<?=$HIAS->confs["meta_description"]; ?>" />
	 <meta name="keywords" content="" />
	 <meta name="author" content="hencework" />

	 <script src="https://kit.fontawesome.com/58ed2b8151.js" crossorigin="anonymous"></script>

	<link type="image/x-icon" rel="icon" href="/img/favicon.png" />
	<link type="image/x-icon" rel="shortcut icon" href="/img/favicon.png" />
	<link type="image/x-icon" rel="apple-touch-icon" href="/img/favicon.png" />

	<link href="/vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
	<link href="/vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
	<link href="/vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet" type="text/css">
	<link href="/dist/css/style.css" rel="stylesheet" type="text/css">
	<link href="/AI/GeniSysAI/Media/CSS/GeniSys.css" rel="stylesheet" type="text/css">
	<link href="/vendors/bower_components/fullcalendar/dist/fullcalendar.css" rel="stylesheet" type="text/css"/>
</head>

<body id="GeniSysAI">

	 <div class="preloader-it">
	 	 <div class="la-anim-1"></div>
	 </div>

	 <div class="wrapper theme-6-active pimary-color-pink">

	 	 <?php include dirname(__FILE__) . '/../Includes/Nav.php'; ?>
	 	 <?php include dirname(__FILE__) . '/../Includes/LeftNav.php'; ?>
	 	 <?php include dirname(__FILE__) . '/../Includes/RightNav.php'; ?>

	 	 <div class="page-wrapper">
	 	 	 <div class="container-fluid pt-25">

	 	 	 	 <?php include dirname(__FILE__) . '/../Includes/Stats.php'; ?>

	 	 	 	 <div class="row">
	 	 	 	 	 <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
	 	 	 	 	 	 <div class="panel panel-default card-view panel-refresh">
	 	 	 	 	 	 	 <div class="panel-heading">
	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 	 <div class="panel-wrapper collapse in">
	 	 	 	 	 	 	 	 <div class="panel-body">
	 	 	 	 	 	 	 	 	 <?php include dirname(__FILE__) . '/../Includes/Weather.php'; ?>
	 	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 </div>
	 	 	 	 	 </div>
	 	 	 	 	 <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
	 	 	 	 	 	 <div class="panel panel-default card-view">
	 	 	 	 	 	 	 <div class="panel-wrapper collapse in">
	 	 	 	 	 	 	 	 <div class="panel-body">
	 	 	 	 	 	 	 	 	 <?php include dirname(__FILE__) . '/../iotJumpWay/Includes/iotJumpWay.php'; ?>
	 	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 </div>
	 	 	 	 	 </div>
	 	 	 	 </div>

	 	 	 	 <div class="row">
	 	 	 	 	 <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
	 	 	 	 	 	 <div class="panel panel-default card-view panel-refresh">
	 	 	 	 	 	 	 <div class="panel-heading">
	 	 	 	 	 	 	 	 <div class="pull-left">
	 	 	 	 	 	 	 	 	 <h6 class="panel-title txt-dark">HIAS AI Model</h6>
	 	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 	 	 <div class="pull-right"></div>
	 	 	 	 	 	 	 	 <div class="clearfix"></div>
	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 	 <div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="form-wrap">
										<form data-toggle="validator" role="form" id="model_update">
											<div class="row">
												<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
													<div class="form-group">
														<label for="name" class="control-label mb-10">Name</label>
														<input type="text" class="form-control" id="name" name="name" placeholder="Model Name" required value="<?=$model["name"]["value"]; ?>">
														<span class="help-block">Name of Model</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Description</label>
														<input type="text" class="form-control" id="description" name="description" placeholder="Model Description" required value="<?=$model["description"]["value"]; ?>">
														<span class="help-block">Description of model</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Link</label>
														<input type="text" class="form-control" id="link" name="link" placeholder="Model Link" required value="<?=$model["modelLink"]["value"]; ?>">
														<span class="help-block">Link to model</span>
													</div>
													<div class="form-group">
														<label class="control-label mb-10">Model Category</label>
														<select class="form-control" id="category" name="category" required>
															<option value="">PLEASE SELECT</option>

															<?php
																$model_cats = $AI->get_model_categories();
																if(count($model_cats)):
																	foreach($model_cats as $key => $value):
															?>

															<option value="<?=$value["category"]; ?>" <?=$model["category"]["value"][0] == $value["category"] ? " selected " : ""; ?>><?=$value["category"]; ?></option>

															<?php
																	endforeach;
																endif;
															?>

														</select>
														<span class="help-block">Category of AI model</span>
													</div>
													<div class="form-group">
														<label class="control-label mb-10">Model Type</label>
														<select class="form-control" id="mtype" name="mtype" required>
															<option value="">PLEASE SELECT</option>

															<?php
																$model_types = $AI->get_model_types();
																if(count($model_types)):
																	foreach($model_types as $key => $value):
															?>

															<option value="<?=$value["model"]; ?>" <?=$model["modelType"]["value"] == $value["model"] ? " selected " : ""; ?>><?=$value["model"]; ?></option>

															<?php
																	endforeach;
																endif;
															?>

														</select>
														<span class="help-block">Type of AI network</span>
													</div>
													<div class="form-group">
														<label class="control-label mb-10">Network Type</label>
														<select class="form-control" id="ntype" name="ntype" required>
															<option value="">PLEASE SELECT</option>
															<option value="CNN" <?=$model["networkArchitecture"]["value"] == "CNN" ? " selected " : ""; ?>>CNN</option>
															<option value="DNN" <?=$model["networkArchitecture"]["value"] == "DNN" ? " selected " : ""; ?>>DNN</option>
															<option value="xDNN" <?=$model["networkArchitecture"]["value"] == "xDNN" ? " selected " : ""; ?>>xDNN</option>
														</select>
														<span class="help-block">Type of AI network</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Modal Accuracy</label>
														<input type="text" class="form-control" id="accuracy" name="accuracy" placeholder="Achieved accuracy" required value="<?=$model["modelAccuracy"]["value"]; ?>">
														<span class="help-block">Achieved accuracy</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Modal Specificity</label>
														<input type="text" class="form-control" id="specificity" name="specificity" placeholder="Achieved specificity" required value="<?=$model["modelSpecificity"]["value"]; ?>">
														<span class="help-block">Achieved specificity</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Modal Precision</label>
														<input type="text" class="form-control" id="precision" name="precision" placeholder="Achieved precision" required value="<?=$model["modelPrecision"]["value"]; ?>">
														<span class="help-block">Achieved precision</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Modal Recall</label>
														<input type="text" class="form-control" id="recall" name="recall" placeholder="Achieved recall" required value="<?=$model["modelRecall"]["value"]; ?>">
														<span class="help-block">Achieved recall</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Modal AUC/ROC</label>
														<input type="text" class="form-control" id="auc" name="auc" placeholder="Achieved AUC/ROC" required value="<?=$model["modelAuc"]["value"]; ?>">
														<span class="help-block">Achieved AUC (Area Under Curve) / ROC (Receiver operating characteristic)</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">True Positives</label>
														<input type="text" class="form-control" id="truePositives" name="truePositives" placeholder="True Positives" required value="<?=$model["modelTruePositives"]["value"]; ?>">
														<span class="help-block">Number of true positives generated by the model</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">False Positives</label>
														<input type="text" class="form-control" id="falsePositives" name="falsePositives" placeholder="False Positives" required value="<?=$model["modelFalsePositives"]["value"]; ?>">
														<span class="help-block">Number of false positives generated by the model</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">True Negatives</label>
														<input type="text" class="form-control" id="trueNegatives" name="trueNegatives" placeholder="True Negatives" required value="<?=$model["modelTrueNegatives"]["value"]; ?>">
														<span class="help-block">Number of true negatives generated by the model</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">False Negatives</label>
														<input type="text" class="form-control" id="falseNegatives" name="falseNegatives" placeholder="False Negatives" required value="<?=$model["modelFalseNegatives"]["value"]; ?>">
														<span class="help-block">Number of false negatives generated by the model</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Misclassification</label>
														<input type="text" class="form-control" id="misclassification" name="misclassification" placeholder="" required value="<?=$model["modelMisclassification"]["value"]; ?>">
														<span class="help-block">Total model misclassification</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Dataset Used</label>
														<input type="text" class="form-control" id="datasetUsed" name="datasetUsed" placeholder="Dataset used to train and test model" required value="<?=$model["datasetName"]["value"]; ?>">
														<span class="help-block">Dataset used to train and test model</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Dataset Link</label>
														<input type="text" class="form-control" id="datasetLink" name="datasetLink" placeholder="Dataset link" required value="<?=$model["datasetLink"]["value"]; ?>">
														<span class="help-block">Dataset link</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Dataset Author</label>
														<input type="text" class="form-control" id="datasetAuthor" name="datasetAuthor" placeholder="Dataset author" required value="<?=$model["datasetAuthor"]["value"]; ?>">
														<span class="help-block">Dataset author</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Dataset Author Link</label>
														<input type="text" class="form-control" id="datasetAuthorLink" name="datasetAuthorLink" placeholder="Dataset author link" required value="<?=$model["datasetAuthorLink"]["value"]; ?>">
														<span class="help-block">Dataset author link</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Dataset Augmentation?</label>
														<input type="checkbox" class="" id="dataAugmentation" name="dataAugmentation"  value=1 <?=$model["dataAugmentation"]["value"] ? 1 : 0; ?> >
														<span class="help-block">Dataset Augmentation</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Dataset Type</label>
														<select class="form-control" id="datasetType" name="datasetType" required>
															<option value="">PLEASE SELECT</option>
															<option value="TEXT" <?=$model["datasetType"]["value"] == "TEXT" ? " selected " : ""; ?>>TEXT</option>
															<option value="JPG" <?=$model["datasetType"]["value"] == "JPG" ? " selected " : ""; ?>>JPG</option>
															<option value="PNG" <?=$model["datasetType"]["value"] == "PNG" ? " selected " : ""; ?>>PNG</option>
															<option value="TIFF" <?=$model["datasetType"]["value"] == "TIFF" ? " selected " : ""; ?>>TIFF</option>
															<option value="AUDIO" <?=$model["datasetType"]["value"] == "AUDIO" ? " selected " : ""; ?>>AUDIO</option>
															<option value="VIDEO" <?=$model["datasetType"]["value"] == "VIDEO" ? " selected " : ""; ?>>VIDEO</option>
														</select>
														<span class="help-block">Dataset type</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Dataset Positive Label</label>
														<input type="text" class="form-control" id="datasetPosLabel" name="datasetPosLabel" placeholder="Dataset Positive Label" required value="<?=$model["dataPositiveLabel"]["value"]; ?>">
														<span class="help-block">To be used in the HIAS UI, test data is expected to include the label at the end of the file name. Specify the positive label here.</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Dataset Negative Label</label>
														<input type="text" class="form-control" id="datasetNegLabel" name="datasetNegLabel" placeholder="Dataset Negative Label" required value="<?=$model["dataNegativeLabel"]["value"]; ?>">
														<span class="help-block">To be used in the HIAS UI, test data is expected to include the label at the end of the file name. Specify the negative label here.</span>
													</div>
													<div class="form-group mb-0">
														<input type="hidden" class="form-control" id="update_ai_model" name="update_ai_model" required value="1">
														<button type="submit" class="btn btn-success btn-anim" id="ai_model_update"><i class="icon-rocket"></i><span class="btn-text">Update</span></button>
													</div>
												</div>
												<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
													<div class="form-group">
														<label for="name" class="control-label mb-10">Author(s)</label>
														<input type="text" class="form-control" id="author" name="author" placeholder="Author" required value="<?=$model["modelAuthor"]["value"]; ?>">
														<span class="help-block">Author(s)</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Author Link</label>
														<input type="text" class="form-control" id="authorLink" name="authorLink" placeholder="Author Link" required value="<?=$model["modelAuthorLink"]["value"]; ?>">
														<span class="help-block">Author Link</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Related Paper</label>
														<input type="text" class="form-control" id="relatedPaper" name="relatedPaper" placeholder="Related paper" required value="<?=$model["paperTitle"]["value"]; ?>">
														<span class="help-block">Related research paper</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Related Paper Author</label>
														<input type="text" class="form-control" id="relatedPaperAuthor" name="relatedPaperAuthor" placeholder="Related paper author" required value="<?=$model["paperAuthor"]["value"]; ?>">
														<span class="help-block">Related research paper author</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Related Paper DOI</label>
														<input type="text" class="form-control" id="relatedPaperDOI" name="relatedPaperDOI" placeholder="Related paper DOI" required value="<?=$model["paperDoi"]["value"]; ?>">
														<span class="help-block">Related research paper DOI</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Related Paper Link</label>
														<input type="text" class="form-control" id="relatedPaperLink" name="relatedPaperLink" placeholder="Related paper link" required value="<?=$model["paperLink"]["value"]; ?>">
														<span class="help-block">Related research paper link</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Programming Language</label>
														<select class="form-control" id="language" name="language" required>
															<option value="">PLEASE SELECT</option>
															<option value="C++" <?=$model["programmingLanguage"]["value"] == "C++" ? " selected " : ""; ?>>C++</option>
															<option value="C#" <?=$model["programmingLanguage"]["value"] == "C#" ? " selected " : ""; ?>>C#</option>
															<option value="Java" <?=$model["programmingLanguage"]["value"] == "Java" ? " selected " : ""; ?>>Java</option>
															<option value="Javascript" <?=$model["programmingLanguage"]["value"] == "Javascript" ? " selected " : ""; ?>>Javascript</option>
															<option value="Python" <?=$model["programmingLanguage"]["value"] == "Python" ? " selected " : ""; ?>>Python</option>
														</select>
														<span class="help-block">Programming language used to develop the model</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Framework</label>
														<select class="form-control" id="framework" name="framework" required>
															<option value="">PLEASE SELECT</option>
															<option value="FastAI" <?=$model["programmingFramework"]["value"] == "FastAI" ? " selected " : ""; ?>>FastAI</option>
															<option value="Keras" <?=$model["programmingFramework"]["value"] == "Keras" ? " selected " : ""; ?>>Keras</option>
															<option value="PyTorch" <?=$model["programmingFramework"]["value"] == "PyTorch" ? " selected " : ""; ?>>PyTorch</option>
															<option value="Tensorflow" <?=$model["programmingFramework"]["value"] == "Tensorflow" ? " selected " : ""; ?>>Tensorflow</option>
															<option value="Torch" <?=$model["programmingFramework"]["value"] == "Torch" ? " selected " : ""; ?>>Torch</option>
															<option value="NA" <?=$model["programmingFramework"]["value"] == "NA" ? " selected " : ""; ?>>NA</option>
														</select>
														<span class="help-block">Framework used to develop the model</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Toolkit</label>
														<select class="form-control" id="toolkit" name="toolkit" required>
															<option value="">PLEASE SELECT</option>
															<option value="OpenVINO/oneAPI" <?=$model["programmingToolkit"]["value"] == "OpenVINO/oneAPI" ? " selected " : ""; ?>>OpenVINO/oneAPI</option>
															<option value="NA" <?=$model["programmingToolkit"]["value"] == "NA" ? " selected " : ""; ?>>NA</option>
														</select>
														<span class="help-block">Toolkit used to develop the model</span>
													</div>
													<div class="form-group">
														<label class="control-label mb-10">Model Properties <a href="javascript:void(0);" id="addModelProperty"><i class="fa fa-plus-circle"></i></a></label>
														<div id="propertyContent">
															<?php
																if(isSet($model["properties"]["value"])):
																	foreach($model["properties"]["value"] AS $key => $value):
																		if($key != "image"):
															?>

															<div class="row" style="margin-bottom: 5px;" id="model-property-<?=$key; ?>">
																<div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
																	<input type="text" class="form-control" name="properties[]" placeholder="<?=$key; ?>" value="<?=$key; ?>" required>
																</div>
																<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
																	<a href="javascript:void(0);" class="removeModelProperty" data-id="<?=$key; ?>"><i class="fas fa-trash-alt"></i></a>
																</div>
															</div>

															<?php
																		endif;
																	endforeach;
																endif;
															?>
														</div>
														<span class="help-block">Model Properties</span>
													</div>
													<div class="form-group">
														<label class="control-label mb-10">Commands <a href="javascript:void(0);" id="addModelCommand"><i class="fa fa-plus-circle"></i></a></label>
														<div id="commandsContent">
															<?php
																if(isSet($model["commands"]["value"])):
																	foreach($model["commands"]["value"] AS $key => $value):
																		if(is_array($value)):
																			$value = implode(',',$value);
																		endif;
															?>

															<div class="row" style="margin-bottom: 5px;" id="model-command-<?=$key; ?>">
																<div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
																	<strong><?=ucfirst($key); ?></strong>
																	<input type="text" class="form-control" name="commands[<?=$key; ?>]" placeholder="Commands as comma separated string" value="<?=$value; ?>" required>
																</div>
																<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
																	<br /><a href="javascript:void(0);" class="removeModelCommand" data-id="<?=$key; ?>"><i class="fas fa-trash-alt"></i></a>
																</div>
															</div>

															<?php
																	endforeach;
																endif;
															?>

														</div>
														<span class="help-block">Model Commands</span>
													</div>
													<div class="form-group">
														<label class="control-label mb-10">States <a href="javascript:void(0);" id="addModelState"><i class="fa fa-plus-circle"></i></a></label>
														<div id="stateContent">

														<?php
															if(isSet($model["states"]["value"])):
																foreach($model["states"]["value"] AS $key => $value):
														?>

														<div class="row" style="margin-bottom: 5px;" id="model-state-<?=$key; ?>">
															<div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
																<input type="text" class="form-control" name="states[]" placeholder="State" value="<?=$value; ?>" required>
															</div>
															<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
																<a href="javascript:void(0);" class="removeModelState" data-id="<?=$key; ?>"><i class="fas fa-trash-alt"></i></a>
															</div>
														</div>

														<?php
																endforeach;
															endif;
														?>
														<span class="hide" id="lastState"><?=$key; ?></span>

														</div>
														<span class="help-block">Model States</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Date Created</label>
														<p><?=$model["dateCreated"]["value"]; ?></p>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Date Modified</label>
														<p><?=$model["dateModified"]["value"]; ?></p>
													</div>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
	 	 	 	 	 <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">Model Schema</h6>
								</div>
								<div class="pull-right"></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div  style="height: 400px; overflow-y: scroll; overflow-x: hidden;">
										<?php echo "<pre id='schema'>"; ?> <?php print_r(json_encode($model, JSON_PRETTY_PRINT)); ?> <?php echo "</pre>"; ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>

			<?php include dirname(__FILE__) . '/../Includes/Footer.php'; ?>

		</div>

		<?php  include dirname(__FILE__) . '/../Includes/JS.php'; ?>

		<script type="text/javascript" src="/iotJumpWay/Classes/mqttws31.js"></script>
		<script type="text/javascript" src="/iotJumpWay/Classes/iotJumpWay.js"></script>
		<script type="text/javascript" src="/iotJumpWay/Classes/iotJumpWayUI.js"></script>
		<script type="text/javascript" src="/AI/Classes/AI.js"></script>

	</body>
</html>