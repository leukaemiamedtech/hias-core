<?php session_start();

$pageDetails = [
	"PageID" => "AI",
	"SubPageID" => "Models"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../AI/Classes/AI.php';

?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=model-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<meta name="robots" content="noindex, nofollow" />

		<title><?=$HIAS->confs["meta_title"]; ?></title>
		<meta name="description" content="<?=$HIAS->confs["meta_description"]; ?>" />
		<meta name="keywords" content="" />
		<meta name="author" content="hencework"/>

		<script src="https://kit.fontawesome.com/58ed2b8151.js" crossorigin="anonymous"></script>

		<link type="image/x-icon" rel="icon" href="/img/favicon.png" />
		<link type="image/x-icon" rel="shortcut icon" href="/img/favicon.png" />
		<link type="image/x-icon" rel="apple-touch-icon" href="/img/favicon.png" />

		<link href="/vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
		<link href="/vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
		<link href="/vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet" type="text/css">
		<link href="/dist/css/style.css" rel="stylesheet" type="text/css">
		<link href="/AI/GeniSysAI/Media/CSS/GeniSys.css" rel="stylesheet" type="text/css">
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
									<h6 class="panel-title txt-dark">Create HIAS AI Model</h6>
								</div>
								<div class="pull-right"></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="form-wrap">
										<form data-toggle="validator" role="form" id="ai_model">
										<div class="row">
											<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
												<div class="form-group">
													<label for="name" class="control-label mb-10">Name</label>
													<input type="text" class="form-control" id="name" name="name" placeholder="Model Name" required value="">
													<span class="help-block">Name of Model</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Description</label>
													<input type="text" class="form-control" id="description" name="description" placeholder="Model Description" required value="">
													<span class="help-block">Description of model</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Link</label>
													<input type="text" class="form-control" id="link" name="link" placeholder="Model Link" required value="">
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

														<option value="<?=$value["category"]; ?>"><?=$value["category"]; ?></option>

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

														<option value="<?=$value["model"]; ?>"><?=$value["model"]; ?></option>

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
														<option value="CNN">CNN</option>
														<option value="DNN">DNN</option>
														<option value="xDNN">xDNN</option>
													</select>
													<span class="help-block">Type of AI network</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Modal Accuracy</label>
													<input type="text" class="form-control" id="accuracy" name="accuracy" placeholder="Achieved accuracy" required value="">
													<span class="help-block">Achieved accuracy</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Modal Specificity</label>
													<input type="text" class="form-control" id="specificity" name="specificity" placeholder="Achieved specificity" required value="">
													<span class="help-block">Achieved specificity</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Modal Precision</label>
													<input type="text" class="form-control" id="precision" name="precision" placeholder="Achieved precision" required value="">
													<span class="help-block">Achieved precision</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Modal Recall</label>
													<input type="text" class="form-control" id="recall" name="recall" placeholder="Achieved recall" required value="">
													<span class="help-block">Achieved recall</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Modal AUC/ROC</label>
													<input type="text" class="form-control" id="auc" name="auc" placeholder="Achieved AUC/ROC" required value="">
													<span class="help-block">Achieved AUC (Area Under Curve) / ROC (Receiver operating characteristic)</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">True Positives</label>
													<input type="text" class="form-control" id="truePositives" name="truePositives" placeholder="True Positives" required value="">
													<span class="help-block">Number of true positives generated by the model</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">False Positives</label>
													<input type="text" class="form-control" id="falsePositives" name="falsePositives" placeholder="False Positives" required value="">
													<span class="help-block">Number of false positives generated by the model</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">True Negatives</label>
													<input type="text" class="form-control" id="trueNegatives" name="trueNegatives" placeholder="True Negatives" required value="">
													<span class="help-block">Number of true negatives generated by the model</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">False Negatives</label>
													<input type="text" class="form-control" id="falseNegatives" name="falseNegatives" placeholder="False Negatives" required value="">
													<span class="help-block">Number of false negatives generated by the model</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Misclassification</label>
													<input type="text" class="form-control" id="misclassification" name="misclassification" placeholder="" required value="">
													<span class="help-block">Total model misclassification</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Dataset Used</label>
													<input type="text" class="form-control" id="datasetUsed" name="datasetUsed" placeholder="Dataset used to train and test model" required value="">
													<span class="help-block">Dataset used to train and test model</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Dataset Link</label>
													<input type="text" class="form-control" id="datasetLink" name="datasetLink" placeholder="Dataset link" required value="">
													<span class="help-block">Dataset link</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Dataset Author</label>
													<input type="text" class="form-control" id="datasetAuthor" name="datasetAuthor" placeholder="Dataset author" required value="">
													<span class="help-block">Dataset author</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Dataset Author Link</label>
													<input type="text" class="form-control" id="datasetAuthorLink" name="datasetAuthorLink" placeholder="Dataset author link" required value="">
													<span class="help-block">Dataset author link</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Dataset Augmentation?</label>
													<input type="checkbox" class="" id="datasetAugmentation" name="datasetAugmentation"  value=1  >
													<span class="help-block">Dataset Augmentation</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Dataset Type</label>
													<select class="form-control" id="datasetType" name="datasetType" required>
														<option value="">PLEASE SELECT</option>
														<option value="TEXT">TEXT</option>
														<option value="JPG">JPG</option>
														<option value="PNG">PNG</option>
														<option value="TIFF">TIFF</option>
														<option value="AUDIO">AUDIO</option>
														<option value="VIDEO">VIDEO</option>
													</select>
													<span class="help-block">Dataset type</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Dataset Positive Label</label>
													<input type="text" class="form-control" id="datasetPosLabel" name="datasetPosLabel" placeholder="Dataset Positive Label" required value="">
													<span class="help-block">To be used in the HIAS UI, test data is expected to include the label at the end of the file name. Specify the positive label here.</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Dataset Negative Label</label>
													<input type="text" class="form-control" id="datasetNegLabel" name="datasetNegLabel" placeholder="Dataset Negative Label" required value="">
													<span class="help-block">To be used in the HIAS UI, test data is expected to include the label at the end of the file name. Specify the negative label here.</span>
												</div>
												<div class="form-group mb-0">
													<input type="hidden" class="form-control" id="create_ai_model" name="create_ai_model" required value="1">
													<button type="submit" class="btn btn-success btn-anim" id="ai_model_create"><i class="icon-rocket"></i><span class="btn-text">Create</span></button>
												</div>
											</div>
											<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
												<div class="form-group">
													<label for="name" class="control-label mb-10">Author(s)</label>
													<input type="text" class="form-control" id="author" name="author" placeholder="Author" required value="">
													<span class="help-block">Author(s)</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Author Link</label>
													<input type="text" class="form-control" id="authorLink" name="authorLink" placeholder="Author Link" required value="">
													<span class="help-block">Author Link</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Related Paper</label>
													<input type="text" class="form-control" id="relatedPaper" name="relatedPaper" placeholder="Related paper" required value="">
													<span class="help-block">Related research paper</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Related Paper Author</label>
													<input type="text" class="form-control" id="relatedPaperAuthor" name="relatedPaperAuthor" placeholder="Related paper author" required value="">
													<span class="help-block">Related research paper author</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Related Paper DOI</label>
													<input type="text" class="form-control" id="relatedPaperDOI" name="relatedPaperDOI" placeholder="Related paper DOI" required value="">
													<span class="help-block">Related research paper DOI</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Related Paper Link</label>
													<input type="text" class="form-control" id="relatedPaperLink" name="relatedPaperLink" placeholder="Related paper link" required value="">
													<span class="help-block">Related research paper link</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Programming Language</label>
													<select class="form-control" id="language" name="language" required>
														<option value="">PLEASE SELECT</option>
														<option value="C++">C++</option>
														<option value="C#">C#</option>
														<option value="Java">Java</option>
														<option value="Javascript">Javascript</option>
														<option value="Python">Python</option>
													</select>
													<span class="help-block">Programming language used to develop the model</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Framework</label>
													<select class="form-control" id="framework" name="framework" required>
														<option value="">PLEASE SELECT</option>
														<option value="FastAI">FastAI</option>
														<option value="Keras">Keras</option>
														<option value="PyTorch">PyTorch</option>
														<option value="Tensorflow">Tensorflow</option>
														<option value="Torch">Torch</option>
														<option value="NA">NA</option>
													</select>
													<span class="help-block">Dataset used to train and test model</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Toolkit</label>
													<select class="form-control" id="toolkit" name="toolkit" required>
														<option value="">PLEASE SELECT</option>
														<option value="OpenVINO/oneAPI">OpenVINO/oneAPI</option>
														<option value="NA">NA</option>
													</select>
													<span class="help-block">Dataset used to train and test model</span>
												</div>
												<div class="form-group">
													<label class="control-label mb-10">Properties <a href="javascript:void(0);" id="addModelProperty"><i class="fa fa-plus-circle"></i></a></label>
													<div id="propertyContent">
													</div>
													<span class="help-block">Model Properties</span>
												</div>
												<div class="form-group">
													<label class="control-label mb-10">Commands <a href="javascript:void(0);" id="addModelCommand"><i class="fa fa-plus-circle"></i></a></label>
													<div id="commandsContent">
													</div>
													<span class="help-block">Model Commands</span>
												</div>
												<div class="form-group">
													<label class="control-label mb-10">States <a href="javascript:void(0);" id="addModelState"><i class="fa fa-plus-circle"></i></a></label>
													<div id="stateContent">
													</div>
													<span class="help-block">Model States</span>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
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
