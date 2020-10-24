<?php session_start();

$pageDetails = [
	"PageID" => "AI",
	"SubPageID" => "Models"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../../Classes/Core/GeniSys.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../AI/Classes/AI.php';

$_GeniSysAi->checkSession();

$mid = filter_input(INPUT_GET, 'model', FILTER_SANITIZE_NUMBER_INT);
$model = $AI->getModel($mid);

?>

<!DOCTYPE html>
<html lang="en">

<head>
	 <meta charset="UTF-8" />
	 <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	 <meta name="robots" content="noindex, nofollow" />

	 <title><?=$_GeniSys->_confs["meta_title"]; ?></title>
	 <meta name="description" content="<?=$_GeniSys->_confs["meta_description"]; ?>" />
	 <meta name="keywords" content="" />
	 <meta name="author" content="hencework" />

	 <script src="https://kit.fontawesome.com/58ed2b8151.js" crossorigin="anonymous"></script>

	<link type="image/x-icon" rel="icon" href="<?=$domain; ?>/img/favicon.png" />
	<link type="image/x-icon" rel="shortcut icon" href="<?=$domain; ?>/img/favicon.png" />
	<link type="image/x-icon" rel="apple-touch-icon" href="<?=$domain; ?>/img/favicon.png" />

	<link href="<?=$domain; ?>/vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
	<link href="<?=$domain; ?>/vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
	<link href="<?=$domain; ?>/vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet" type="text/css">
	<link href="<?=$domain; ?>/dist/css/style.css" rel="stylesheet" type="text/css">
	<link href="<?=$domain; ?>/AI/GeniSysAI/Media/CSS/GeniSys.css" rel="stylesheet" type="text/css">
	<link href="<?=$domain; ?>/vendors/bower_components/fullcalendar/dist/fullcalendar.css" rel="stylesheet" type="text/css"/>
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
	 	 	 	 	 	 	 	 	 <h6 class="panel-title txt-dark">AI Classifier Model #<?=$mid; ?></h6>
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
														<input type="text" class="form-control" id="name" name="name" placeholder="Model Name" required value="<?=$model["context"]["Data"]["name"]["value"]; ?>">
														<span class="help-block">Name of Model</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Description</label>
														<input type="text" class="form-control" id="description" name="description" placeholder="Model Description" required value="<?=$model["context"]["Data"]["description"]["value"]; ?>">
														<span class="help-block">Description of model</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Link</label>
														<input type="text" class="form-control" id="link" name="link" placeholder="Model Link" required value="<?=$model["context"]["Data"]["model"]["link"]; ?>">
														<span class="help-block">Link to model</span>
													</div>
													<div class="form-group">
														<label class="control-label mb-10">Model Category</label>
														<select class="form-control" id="category" name="category" required>
															<option value="">PLEASE SELECT</option>
															<option value="Classification" <?=$model["context"]["Data"]["category"]["value"][0] == "Classification" ? " selected " : ""; ?>>Classification</option>
															<option value="Segmentation" <?=$model["context"]["Data"]["category"]["value"][0] == "Segmentation" ? " selected " : ""; ?>>Segmentation</option>
														</select>
														<span class="help-block">Category of AI model</span>
													</div>
													<div class="form-group">
														<label class="control-label mb-10">Model Type</label>
														<select class="form-control" id="mtype" name="mtype" required>
															<option value="">PLEASE SELECT</option>
															<option value="Acute Lymphoblastic Leukemia" <?=$model["context"]["Data"]["model"]["type"] == "Acute Lymphoblastic Leukemia" ? " selected " : ""; ?>>Acute Lymphoblastic Leukemia</option>
															<option value="Acute Myeloid Leukemia" <?=$model["context"]["Data"]["model"]["type"] == "Acute Myeloid Leukemia" ? " selected " : ""; ?>>Acute Myeloid Leukemia</option>
															<option value="COVID-19 (Sars CoV2)" <?=$model["context"]["Data"]["model"]["type"] == "COVID-19 (Sars CoV2)" ? " selected " : ""; ?>>COVID-19 (Sars CoV2)</option>
															<option value="Facial Recognition" <?=$model["context"]["Data"]["model"]["type"] == "Facial Recognition" ? " selected " : ""; ?>>Facial Recognition</option>
															<option value="Facial Identification" <?=$model["context"]["Data"]["model"]["type"] == "Facial Identification" ? " selected " : ""; ?>>Facial Identification</option>
															<option value="Object Recognition" <?=$model["context"]["Data"]["model"]["type"] == "Object Recognition" ? " selected " : ""; ?>>Object Recognition</option>
														</select>
														<span class="help-block">Type of AI network</span>
													</div>
													<div class="form-group">
														<label class="control-label mb-10">Network Type</label>
														<select class="form-control" id="ntype" name="ntype" required>
															<option value="">PLEASE SELECT</option>
															<option value="CNN" <?=$model["context"]["Data"]["network"]["value"] == "CNN" ? " selected " : ""; ?>>CNN</option>
															<option value="DNN" <?=$model["context"]["Data"]["network"]["value"] == "DNN" ? " selected " : ""; ?>>DNN</option>
															<option value="xDNN" <?=$model["context"]["Data"]["network"]["value"] == "xDNN" ? " selected " : ""; ?>>xDNN</option>
														</select>
														<span class="help-block">Type of AI network</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Programming Language</label>
														<select class="form-control" id="language" name="language" required>
															<option value="">PLEASE SELECT</option>
															<option value="C++" <?=$model["context"]["Data"]["language"]["value"] == "C++" ? " selected " : ""; ?>>C++</option>
															<option value="C#" <?=$model["context"]["Data"]["language"]["value"] == "C#" ? " selected " : ""; ?>>C#</option>
															<option value="Java" <?=$model["context"]["Data"]["language"]["value"] == "Java" ? " selected " : ""; ?>>Java</option>
															<option value="Javascript" <?=$model["context"]["Data"]["language"]["value"] == "Javascript" ? " selected " : ""; ?>>Javascript</option>
															<option value="Python" <?=$model["context"]["Data"]["language"]["value"] == "Python" ? " selected " : ""; ?>>Python</option>
														</select>
														<span class="help-block">Programming language used to develop the model</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Framework</label>
														<select class="form-control" id="framework" name="framework" required>
															<option value="">PLEASE SELECT</option>
															<option value="FastAI" <?=$model["context"]["Data"]["framework"]["value"] == "FastAI" ? " selected " : ""; ?>>FastAI</option>
															<option value="Keras" <?=$model["context"]["Data"]["framework"]["value"] == "Keras" ? " selected " : ""; ?>>Keras</option>
															<option value="PyTorch" <?=$model["context"]["Data"]["framework"]["value"] == "PyTorch" ? " selected " : ""; ?>>PyTorch</option>
															<option value="Tensorflow" <?=$model["context"]["Data"]["framework"]["value"] == "Tensorflow" ? " selected " : ""; ?>>Tensorflow</option>
															<option value="Torch" <?=$model["context"]["Data"]["framework"]["value"] == "Torch" ? " selected " : ""; ?>>Torch</option>
															<option value="NA" <?=$model["context"]["Data"]["framework"]["value"] == "NA" ? " selected " : ""; ?>>NA</option>
														</select>
														<span class="help-block">Framework used to develop the model</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Toolkit</label>
														<select class="form-control" id="toolkit" name="toolkit" required>
															<option value="">PLEASE SELECT</option>
															<option value="OpenVINO/oneAPI" <?=$model["context"]["Data"]["toolkit"]["value"] == "OpenVINO/oneAPI" ? " selected " : ""; ?>>OpenVINO/oneAPI</option>
															<option value="NA" <?=$model["context"]["Data"]["toolkit"]["value"] == "NA" ? " selected " : ""; ?>>NA</option>
														</select>
														<span class="help-block">Toolkit used to develop the model</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Dataset Used</label>
														<input type="text" class="form-control" id="datasetUsed" name="datasetUsed" placeholder="Dataset used to train and test model" required value="<?=$model["context"]["Data"]["dataset"]["name"]; ?>">
														<span class="help-block">Dataset used to train and test model</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Dataset Author</label>
														<input type="text" class="form-control" id="datasetAuthor" name="datasetAuthor" placeholder="Dataset author" required value="<?=$model["context"]["Data"]["dataset"]["author"]; ?>">
														<span class="help-block">Dataset author</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Dataset Link</label>
														<input type="text" class="form-control" id="datasetLink" name="datasetLink" placeholder="Dataset link" required value="<?=$model["context"]["Data"]["dataset"]["url"]; ?>">
														<span class="help-block">Dataset link</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Dataset Augmentation?</label>
														<input type="checkbox" class="" id="datasetAugmentation" name="datasetAugmentation"  value=1 <?=$model["context"]["Data"]["dataset"]["augmentation"] ? 1 : 0; ?> >
														<span class="help-block">Dataset Augmentation</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Dataset Type</label>
														<select class="form-control" id="datasetType" name="datasetType" required>
															<option value="">PLEASE SELECT</option>
															<option value="JPG" <?=$model["context"]["Data"]["dataset"]["type"] == "JPG" ? " selected " : ""; ?>>JPG</option>
															<option value="PNG" <?=$model["context"]["Data"]["dataset"]["type"] == "PNG" ? " selected " : ""; ?>>PNG</option>
															<option value="TIFF" <?=$model["context"]["Data"]["dataset"]["type"] == "TIFF" ? " selected " : ""; ?>>TIFF</option>
														</select>
														<span class="help-block">Dataset type</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Dataset Positive Label</label>
														<input type="text" class="form-control" id="datasetPosLabel" name="datasetPosLabel" placeholder="Dataset Positive Label" required value="<?=$model["context"]["Data"]["dataset"]["positiveLabel"]; ?>">
														<span class="help-block">To be used in the HIAS UI, test data is expected to include the label at the end of the file name. Specify the positive label here.</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Dataset Negative Label</label>
														<input type="text" class="form-control" id="datasetNegLabel" name="datasetNegLabel" placeholder="Dataset Negative Label" required value="<?=$model["context"]["Data"]["dataset"]["negativeLabel"]; ?>">
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
														<input type="text" class="form-control" id="author" name="author" placeholder="Author" required value="<?=$model["context"]["Data"]["model"]["author"]; ?>">
														<span class="help-block">Author(s)</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Author Link</label>
														<input type="text" class="form-control" id="authorLink" name="authorLink" placeholder="Author Link" required value="<?=$model["context"]["Data"]["model"]["authorLink"]; ?>">
														<span class="help-block">Author Link</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Related Paper</label>
														<input type="text" class="form-control" id="relatedPaper" name="relatedPaper" placeholder="Related paper" required value="<?=$model["context"]["Data"]["paper"]["title"]; ?>">
														<span class="help-block">Related research paper</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Related Paper Author</label>
														<input type="text" class="form-control" id="relatedPaperAuthor" name="relatedPaperAuthor" placeholder="Related paper author" required value="<?=$model["context"]["Data"]["paper"]["author"]; ?>">
														<span class="help-block">Related research paper author</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Related Paper DOI</label>
														<input type="text" class="form-control" id="relatedPaperDOI" name="relatedPaperDOI" placeholder="Related paper DOI" required value="<?=$model["context"]["Data"]["paper"]["doi"]; ?>">
														<span class="help-block">Related research paper DOI</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Related Paper Link</label>
														<input type="text" class="form-control" id="relatedPaperLink" name="relatedPaperLink" placeholder="Related paper link" required value="<?=$model["context"]["Data"]["paper"]["link"]; ?>">
														<span class="help-block">Related research paper link</span>
													</div>
													<div class="form-group">
														<label class="control-label mb-10">Model Properties <a href="javascript:void(0);" id="addModelProperty"><i class="fa fa-plus-circle"></i></a></label>
														<div id="propertyContent">
															<?php
																if(isSet($model["context"]["Data"]["properties"])):
																	foreach($model["context"]["Data"]["properties"] AS $key => $value):
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
																if(isSet($model["context"]["Data"]["commands"])):
																	foreach($model["context"]["Data"]["commands"] AS $key => $value):
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
															if(isSet($model["context"]["Data"]["states"])):
																foreach($model["context"]["Data"]["states"] AS $key => $value):
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
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div><br />
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
										<?php echo "<pre id='schema'>"; ?> <?php print_r(json_encode($model["context"]["Data"], JSON_PRETTY_PRINT)); ?> <?php echo "</pre>"; ?>
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

		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/mqttws31.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/iotJumpWay.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/iotJumpWayUI.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/AI/Classes/AI.js"></script>

	</body>
</html>