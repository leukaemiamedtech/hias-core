<?php session_start();

$pageDetails = [
	"PageID" => "AI",
	"SubPageID" => "AIALL"
];

include dirname(__FILE__) . '/../../../Classes/Core/init.php';
include dirname(__FILE__) . '/../../../Classes/Core/GeniSys.php';
include dirname(__FILE__) . '/../../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../../AI/ALL/Classes/ALL.php';
include dirname(__FILE__) . '/../../AI/Classes/AI.php';

$_GeniSysAi->checkSession();

$Locations = $iotJumpWay->getLocations();
$Zones = $iotJumpWay->getZones();
$Devices = $ALL->getDevices();

$TId = filter_input(INPUT_GET, 'device', FILTER_SANITIZE_NUMBER_INT);
$Device = $iotJumpWay->getDevice($TId);

list($dev1On, $dev1Off) = $iotJumpWay->getStatusShow($Device["context"]["Data"]["status"]["value"]);

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

	 	 <?php include dirname(__FILE__) . '/../../Includes/Nav.php'; ?>
	 	 <?php include dirname(__FILE__) . '/../../Includes/LeftNav.php'; ?>
	 	 <?php include dirname(__FILE__) . '/../../Includes/RightNav.php'; ?>

	 	 <div class="page-wrapper">
	 	 	 <div class="container-fluid pt-25">

	 	 	 	 <?php include dirname(__FILE__) . '/../../Includes/Stats.php'; ?>

	 	 	 	 <div class="row">
	 	 	 	 	 <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
	 	 	 	 	 	 <div class="panel panel-default card-view panel-refresh">
	 	 	 	 	 	 	 <div class="panel-heading">
	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 	 <div class="panel-wrapper collapse in">
	 	 	 	 	 	 	 	 <div class="panel-body">
	 	 	 	 	 	 	 	 	 <?php include dirname(__FILE__) . '/../../Includes/Weather.php'; ?>
	 	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 </div>
	 	 	 	 	 </div>
	 	 	 	 	 <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
	 	 	 	 	 	 <div class="panel panel-default card-view">
	 	 	 	 	 	 	 <div class="panel-wrapper collapse in">
	 	 	 	 	 	 	 	 <div class="panel-body">
	 	 	 	 	 	 	 	 	 <?php include dirname(__FILE__) . '/../../iotJumpWay/Includes/iotJumpWay.php'; ?>
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
	 	 	 	 	 	 	 	 	 <h6 class="panel-title txt-dark">ALL Classifier Device #<?=$TId; ?></h6>
	 	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 	 	 <div class="pull-right"></div>
	 	 	 	 	 	 	 	 <div class="clearfix"></div>
	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 	 <div class="panel-wrapper collapse in">
	 	 	 	 	 	 	 	 <div class="panel-body">
	 	 	 	 	 	 	 	 	 <div class="form-wrap">
	 	 	 	 	 	 	 	 	 	 <form data-toggle="validator" role="form" id="all_classifier_update">
	 	 	 	 	 	 	 	 	 	 	 <div class="row">
	 	 	 	 	 	 	 	 	 	 	 	 <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
													<div class="form-group">
														<label for="name" class="control-label mb-10">Name</label>
														<input type="text" class="form-control" id="name" name="name" placeholder="Device Name" required value="<?=$Device["context"]["Data"]["name"]["value"]; ?>">
														<span class="help-block"> Name of device</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Description</label>
														<input type="text" class="form-control" id="description" name="description" placeholder="Device Description" required value="<?=$Device["context"]["Data"]["description"]["value"]; ?>">
														<span class="help-block"> Description of device</span>
													</div>
													<div class="form-group">
														<label class="control-label mb-10">Category</label>
														<select class="form-control" id="category" name="category">

															<?php
																$categories = $iotJumpWay->getDeviceCategories();
																if(count($categories)):
																	foreach($categories as $key => $value):
															?>

															<option value="<?=$value["category"]; ?>" <?=$value["category"] == $Device["context"]["Data"]["category"]["value"][0] ? " selected " : ""; ?>><?=$value["category"]; ?></option>

															<?php
																	endforeach;
																endif;
															?>

														</select>
														<span class="help-block">Device category</span>
													</div>
													<div class="form-group">
														<label class="control-label mb-10">Model Type</label>
														<select class="form-control" id="ctype" name="ctype" required>
															<option value="">PLEASE SELECT</option>
															<option value="Classification" <?=$Device["context"]["Data"]["device"]["type"] == "Classification" ? " selected " : ""; ?>>Classification</option>
															<option value="Segmentation" <?=$Device["context"]["Data"]["device"]["type"] == "Segmentation" ? " selected " : ""; ?>>Segmentation</option>
														</select>
														<span class="help-block"> Type of ALL model</span>
													</div>
													<div class="form-group">
														<label class="control-label mb-10">IoT Agent</label>
														<select class="form-control" id="agent" name="agent">
															<option value="">PLEASE SELECT</option>

															<?php
																$agents = $iotJumpWay->getAgents();
																if(count($agents["Data"])):
																	foreach($agents["Data"] as $key => $value):
															?>

															<option value="http://<?=$value["ip"]["value"]; ?>:<?=$value["northPort"]["value"]; ?>" <?=$Device["context"]["Data"]["agent"]["url"] == "http://" . $value["ip"]["value"] . ":" . $value["northPort"]["value"] ? " selected " : ""; ?>><?=$value["name"]["value"]; ?> (http://<?=$value["ip"]["value"]; ?>:<?=$value["northPort"]["value"]; ?>)</option>

															<?php
																	endforeach;
																endif;
															?>

														</select>
														<span class="help-block">Device IoT Agent</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Hardware Device Name</label>
														<input type="text" class="form-control" id="deviceName" name="deviceName" placeholder="Hardware device name" required value="<?=$Device["context"]["Data"]["device"]["name"]; ?>">
														<span class="help-block">Name of hardware device</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Hardware Device Manufacturer</label>
														<input type="text" class="form-control" id="deviceManufacturer" name="deviceManufacturer" placeholder="Hardware device manufacturer" required value="<?=$Device["context"]["Data"]["device"]["manufacturer"]; ?>">
														<span class="help-block">Name of hardware manufacturer</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Hardware Device Model</label>
														<input type="text" class="form-control" id="deviceModel" name="deviceModel" placeholder="Hardware device model" required value="<?=$Device["context"]["Data"]["device"]["model"]; ?>">
														<span class="help-block">Hardware model</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Operating System</label>
														<input type="text" class="form-control" id="osName" name="osName" placeholder="Operating system name" required value="<?=$Device["context"]["Data"]["os"]["name"]; ?>">
														<span class="help-block">Operating system name</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Operating system manufacturer</label>
														<input type="text" class="form-control" id="osManufacturer" name="osManufacturer" placeholder="Operating system manufacturer" required value="<?=$Device["context"]["Data"]["os"]["manufacturer"]; ?>">
														<span class="help-block">Operating system manufacturer</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Operating system version</label>
														<input type="text" class="form-control" id="osVersion" name="osVersion" placeholder="Operating system version" required value="<?=$Device["context"]["Data"]["os"]["version"]; ?>">
														<span class="help-block">Operating system version</span>
													</div>
													<div class="form-group">
														<label class="control-label mb-10">Protocols</label>
														<select class="form-control" id="protocols" name="protocols[]" required multiple>

															<?php
																$protocols = $iotJumpWay->getContextBrokerProtocols();
																if(count($protocols)):
																	foreach($protocols as $key => $value):
															?>

																<option value="<?=$value["protocol"]; ?>" <?=in_array($value["protocol"], $Device["context"]["Data"]["protocols"]) ? " selected " : ""; ?>><?=$value["protocol"]; ?></option>

															<?php
																	endforeach;
																endif;
															?>

														</select>
														<span class="help-block">Supported Communication Protocols</span>
													</div>
													<div class="form-group">
														<label class="control-label mb-10">Sensors</label>
														<select class="form-control" id="sensorSelect">
															<option value="">Select Sensors To Add</option>

															<?php
																$sensors = $iotJumpWay->getThings(0, "Sensor");
																if(count($sensors["Data"])):
																	foreach($sensors["Data"] as $key => $value):
															?>

																<option value="<?=$value["sid"]["value"]; ?>"><?=$value["name"]["value"]; ?></option>

															<?php
																	endforeach;
																endif;
															?>

														</select><br />
														<div id="sensorContent">
															<?php
																if(isSet($Device["context"]["Data"]["sensors"])):
																	foreach($Device["context"]["Data"]["sensors"] AS $key => $value):
															?>

															<div class="row form-control" style="margin-bottom: 5px; margin-left: 0.5px;" id="sensor-<?=$key; ?>">
																<div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
																	<strong><?=$value["name"]["value"]; ?></strong>
																	<input type="hidden" class="form-control" name="sensors[]" value="<?=$value["sid"]["value"]; ?>" required>
																</div>
																<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
																	<a href="javascript:void(0);" class="removeSensor" data-id="<?=$key; ?>"><i class="fas fa-trash-alt"></i></a>
																</div>
															</div>

															<?php
																	endforeach;
																endif;
															?>
														</div>
														<span class="help-block">Device Sensors</span>
														<span class="hidden" id="lastSensor"><?=$key ? $key : 0; ?></span>
													</div>
													<div class="form-group">
														<label class="control-label mb-10">Actuators</label>
														<select class="form-control" id="actuatorSelect">
															<option value="">Select Actuators To Add</option>

															<?php
																$actuators = $iotJumpWay->getThings(0, "Actuator");
																if(count($actuators["Data"])):
																	foreach($actuators["Data"] as $key => $value):
															?>

																<option value="<?=$value["sid"]["value"]; ?>"><?=$value["name"]["value"]; ?></option>

															<?php
																	endforeach;
																endif;
															?>

														</select><br />
														<div id="actuatorContent">
															<?php
																if(isSet($Device["context"]["Data"]["actuators"])):
																	foreach($Device["context"]["Data"]["actuators"] AS $key => $value):
															?>

															<div class="row form-control" style="margin-bottom: 5px; margin-left: 0.5px;" id="actuator-<?=$key; ?>">
																<div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
																	<strong><?=$value["name"]["value"]; ?></strong>
																	<input type="hidden" class="form-control" name="actuators[]" value="<?=$value["sid"]["value"]; ?>" required>
																</div>
																<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
																	<a href="javascript:void(0);" class="removeActuator" data-id="<?=$key; ?>"><i class="fas fa-trash-alt"></i></a>
																</div>
															</div>

															<?php
																	endforeach;
																endif;
															?>
														</div>
														<span class="help-block">Device Actuators</span>
														<span class="hidden" id="lastActuator"><?=$key ? $key : 0; ?></span>
													</div>
													<div class="form-group">
														<label class="control-label mb-10">AI Models</label>
														<select class="form-control" id="ai" name="ai[]" multiple>

															<?php
																$models = $AI->getModels()["Data"];
																if(count($models)):
																	foreach($models as $key => $value):
															?>

																<option value="<?=$value["mid"]["value"]; ?>" <?=array_key_exists($value["name"]["value"], $Device["context"]["Data"]["ai"]) ? " selected " : ""; ?>><?=$value["name"]["value"]; ?></option>

															<?php
																	endforeach;
																endif;
															?>

														</select>
														<span class="help-block">Device AI Models</span>
													</div>
													<div class="form-group mb-0">
														<input type="hidden" class="form-control" id="update_all_classifier" name="update_all_classifier" required value="1">
														<button type="submit" class="btn btn-success btn-anim" id="genisysai_update"><i class="icon-rocket"></i><span class="btn-text">Submit</span></button>
													</div>
												</div>
												<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
													<div class="form-group">
														<label for="name" class="control-label mb-10">Dataset Used</label>
														<input type="text" class="form-control" id="datasetUsed" name="datasetUsed" placeholder="Dataset used to train and test model" required value="<?=$Device["context"]["Data"]["dataset"]["name"]; ?>">
														<span class="help-block">Dataset used to train and test model</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Dataset Link</label>
														<input type="text" class="form-control" id="datasetLink" name="datasetLink" placeholder="Dataset link" required value="<?=$Device["context"]["Data"]["dataset"]["url"]; ?>">
														<span class="help-block">Dataset link</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Dataset Author</label>
														<input type="text" class="form-control" id="datasetAuthor" name="datasetAuthor" placeholder="Dataset author" required value="<?=$Device["context"]["Data"]["dataset"]["author"]; ?>">
														<span class="help-block">Dataset author</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Dataset Folder</label>
														<input type="text" class="form-control" id="datasetFolder" name="datasetFolder" placeholder="Dataset folder" required value="<?=$Device["context"]["Data"]["dataset"]["folder"]; ?>">
														<span class="help-block">Dataset folder on HIAS</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Related Paper</label>
														<input type="text" class="form-control" id="relatedPaper" name="relatedPaper" placeholder="Related paper" required value="<?=$Device["context"]["Data"]["paper"]["title"]; ?>">
														<span class="help-block">Related research paper</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Related Paper Author</label>
														<input type="text" class="form-control" id="relatedPaperAuthor" name="relatedPaperAuthor" placeholder="Related paper author" required value="<?=$Device["context"]["Data"]["paper"]["author"]; ?>">
														<span class="help-block">Related research paper author</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Related Paper DOI</label>
														<input type="text" class="form-control" id="relatedPaperDOI" name="relatedPaperDOI" placeholder="Related paper DOI" required value="<?=$Device["context"]["Data"]["paper"]["doi"]; ?>">
														<span class="help-block">Related research paper DOI</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Related Paper Link</label>
														<input type="text" class="form-control" id="relatedPaperLink" name="relatedPaperLink" placeholder="Related paper link" required value="<?=$Device["context"]["Data"]["paper"]["link"]; ?>">
														<span class="help-block">Related research paper link</span>
													</div>
													<div class="form-group">
														<label class="control-label mb-10">Location</label>
														<select class="form-control" id="lid" name="lid" required>
															<option value="">PLEASE SELECT</option>

															<?php
																$Locations = $iotJumpWay->getLocations();
																if(count($Locations["Data"])):
																	foreach($Locations["Data"] as $key => $value):
															?>

																<option value="<?=$value["lid"]["value"]; ?>" <?=$value["lid"]["value"] == $Device["context"]["Data"]["lid"]["value"] ? " selected " : ""; ?>>#<?=$value["lid"]["value"]; ?>: <?=$value["name"]["value"]; ?></option>

															<?php
																	endforeach;
																endif;
															?>

														</select>
														<span class="help-block"> Location of device</span>
													</div>
													<div class="form-group">
														<label class="control-label mb-10">Zone</label>
														<select class="form-control" id="zid" name="zid" required>
															<option value="">PLEASE SELECT</option>
															<?php
																$Zones = $iotJumpWay->getZones();
																if(count($Zones["Data"])):
																	foreach($Zones["Data"] as $key => $value):
															?>

															<option value="<?=$value["zid"]["value"]; ?>" <?=$Device["context"]["Data"]["zid"]["value"] == $value["zid"]["value"] ? " selected " : ""; ?>>#<?=$value["zid"]["value"]; ?>: <?=$value["name"]["value"]; ?></option>

															<?php
																	endforeach;
																endif;
															?>

														</select>
														<span class="help-block"> Zone of device</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Coordinates</label>
														<input type="text" class="form-control hider" id="coordinates" name="coordinates" placeholder="iotJumpWay Location coordinates" required value="<?=$Device["context"]["Data"]["location"]["value"]["coordinates"][0]; ?>, <?=$Device["context"]["Data"]["location"]["value"]["coordinates"][1]; ?>">
														<span class="help-block">iotJumpWay Device coordinates</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">IP</label>
														<input type="text" class="form-control hider" id="ip" name="ip" placeholder="Device IP" required value="<?=$Device["context"]["Data"]["ip"]["value"] ? $_GeniSys->_helpers->oDecrypt($Device["context"]["Data"]["ip"]["value"]) : ""; ?>">
														<span class="help-block"> IP of device</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">MAC</label>
														<input type="text" class="form-control hider" id="mac" name="mac" placeholder="Device MAC" required value="<?=$Device["context"]["Data"]["mac"]["value"] ? $_GeniSys->_helpers->oDecrypt($Device["context"]["Data"]["mac"]["value"]) : ""; ?>">
														<span class="help-block"> MAC of device</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Bluetooth Address</label>
														<input type="text" class="form-control hider" id="bluetooth" name="bluetooth" placeholder="Device Bluetooth Address"  value="<?=$Device["context"]["Data"]["bluetooth"]["address"] ? $_GeniSys->_helpers->oDecrypt($Device["context"]["Data"]["bluetooth"]["address"]) : ""; ?>">
														<span class="help-block">Bluetooth address of device</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Stream Port</label>
														<input type="text" class="form-control hider" id="sport" name="sport" placeholder="ALL Device Stream Port" required value="<?=$Device["context"]["Data"]["stream"]["port"]; ?>">
														<span class="help-block">Port of ALL stream</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Proxy Endpoint</label>
														<input type="text" class="form-control hider" id="endpoint" name="endpoint" placeholder="ALL Device Proxy Endpoint" required value="<?=$Device["context"]["Data"]["proxy"]["endpoint"]; ?>">
														<span class="help-block">Endpoint name of NGINX reverse proxy</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Date Created</label>
														<p><?=$Device["context"]["Data"]["dateCreated"]["value"]; ?></p>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Date First Used</label>
														<p><?=$Device["context"]["Data"]["dateFirstUsed"]["value"] ? $Device["context"]["Data"]["dateFirstUsed"]["value"] : "NA"; ?></p>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Date Modified</label>
														<p><?=$Device["context"]["Data"]["dateModified"]["value"]; ?></p>
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
									<h6 class="panel-title txt-dark">Device Schema</h6>
								</div>
								<div class="pull-right"></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div  style="height: 400px; overflow-y: scroll; overflow-x: hidden;">
										<?php echo "<pre id='schema'>"; ?> <?php print_r(json_encode($Device["context"]["Data"], JSON_PRETTY_PRINT)); ?> <?php echo "</pre>"; ?>
									</div>
								</div>
							</div>
						</div><br />
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">Device History</h6>
								</div>
								<div class="pull-right"><a href="<?=$domain; ?>/iotJumpWay/<?=$Device["context"]["Data"]["lid"]["value"]; ?>/Zones/<?=$Device["context"]["Data"]["zid"]["value"]; ?>/Devices/<?=$Device["context"]["Data"]["did"]["value"]; ?>/History"><i class="fa fa-eye pull-left"></i> View All Device History</a></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="table-wrap mt-40">
										<div class="table-responsive">
											<table class="table mb-0">
												<thead>
												  <tr>
													<th>ID</th>
													<th>Action</th>
													<th>Receipt</th>
													<th>Time</th>
												  </tr>
												</thead>
												<tbody>

												<?php
													$userDetails = "";
													$history = $iotJumpWay->retrieveDeviceHistory($Device["context"]["Data"]["did"]["value"], 5);
													if(count($history)):
														foreach($history as $key => $value):
																if($value["uid"]):
																	$user = $_GeniSysAi->getUser($value["uid"]);
																	$userDetails = "User ID #" . $value["uid"] . " (" . $user["name"] . ") ";
																endif;
												?>

												  <tr>
													<td>#<?=$value["id"];?></td>
													<td><?=$userDetails;?><?=$value["action"];?></td>
													<td>

														<?php
															if($value["hash"]):
														?>
															<a href="<?=$domain; ?>/iotJumpWay/<?=$Device["context"]["Data"]["lid"]["value"]; ?>/Zones/<?=$Device["context"]["Data"]["zid"]["value"]; ?>/Devices/<?=$Device["context"]["Data"]["did"]["value"]; ?>/Transaction/<?=$value["hash"];?>">#<?=$value["hash"];?></a>
														<?php
															else:
														?>
															NA
														<?php
															endif;
														?>



													</td>
													<td><?=date("Y-m-d H:i:s", $value["time"]);?></td>
												  </tr>

												<?php
														endforeach;
													endif;
												?>

												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div><br />
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">Device Transactions</h6>
								</div>
								<div class="pull-right"><a href="<?=$domain; ?>/iotJumpWay/<?=$Device["context"]["Data"]["lid"]["value"]; ?>/Zones/<?=$Device["context"]["Data"]["zid"]["value"]; ?>/Devices/<?=$Device["context"]["Data"]["did"]["value"]; ?>/Transactions"><i class="fa fa-eye pull-left"></i> View All Device Transactions</a></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="table-wrap mt-40">
										<div class="table-responsive">
											<table class="table mb-0">
												<thead>
												  <tr>
													<th>ID</th>
													<th>Action</th>
													<th>Receipt</th>
													<th>Time</th>
												  </tr>
												</thead>
												<tbody>

												<?php
													$transactions = $iotJumpWay->retrieveDeviceTransactions($Device["context"]["Data"]["did"]["value"], 5);
													if(count($transactions)):
														foreach($transactions as $key => $value):
															if($value["uid"]):
																$user = $_GeniSysAi->getUser($value["uid"]);
																$userDetails = "User ID #" . $value["uid"] . " (" . $user["name"] . ") ";
															endif;
												?>

												  <tr>
													<td>#<?=$value["id"];?></td>
													<td><?=$userDetails;?><?=$value["action"];?></td>
													<td><a href="<?=$domain; ?>/iotJumpWay/<?=$Device["context"]["Data"]["lid"]["value"]; ?>/Zones/<?=$Device["context"]["Data"]["zid"]["value"]; ?>/Devices/<?=$Device["context"]["Data"]["did"]["value"]; ?>/Transaction/<?=$value["id"];?>">#<?=$value["id"];?></a></td>
													<td><?=date("Y-m-d H:i:s", $value["time"]);?></td>
												  </tr>

												<?php
														endforeach;
													endif;
												?>

												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div><br />
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">Device iotJumpWay Statuses</h6>
								</div>
								<div class="pull-right"><a href="<?=$domain; ?>/iotJumpWay/<?=$Device["context"]["Data"]["lid"]["value"]; ?>/Zones/<?=$Device["context"]["Data"]["zid"]["value"]; ?>/Devices/<?=$Device["context"]["Data"]["did"]["value"]; ?>Statuses"><i class="fa fa-eye pull-left"></i> View All Device Status Data</a></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="table-wrap mt-40">
										<div class="table-responsive">
											<table class="table mb-0">
												<thead>
												  <tr>
													<th>ID</th>
													<th>Status</th>
													<th>Time</th>
												  </tr>
												</thead>
												<tbody>

												<?php
													$Statuses = $iotJumpWay->retrieveDeviceStatuses($Device["context"]["Data"]["did"]["value"], 5);
													if($Statuses["Response"] == "OK"):
														foreach($Statuses["ResponseData"] as $key => $value):
												?>

												  <tr>
													<td>#<?=$value->_id;?></td>
													<td><?=$value->Status;?></td>
													<td><?=$value->Time;?> </td>
												  </tr>

												<?php
														endforeach;
													endif;
												?>

												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div><br />
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">Device iotJumpWay Life</h6>
								</div>
								<div class="pull-right"><a href="<?=$domain; ?>/iotJumpWay/<?=$Device["context"]["Data"]["lid"]["value"]; ?>/Zones/<?=$Device["context"]["Data"]["zid"]["value"]; ?>/Devices/<?=$Device["context"]["Data"]["did"]["value"]; ?>/Life"><i class="fa fa-eye pull-left"></i> View All Device Life Data</a></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="table-wrap mt-40">
										<div class="table-responsive">
											<table class="table mb-0">
												<thead>
												  <tr>
													<th>ID</th>
													<th>Details</th>
													<th>Time</th>
												  </tr>
												</thead>
												<tbody>

												<?php
													$Statuses = $iotJumpWay->retrieveDeviceLife($Device["context"]["Data"]["did"]["value"], 5);
													if($Statuses["Response"] == "OK"):
														foreach($Statuses["ResponseData"] as $key => $value):
												?>

												  <tr>
													<td>#<?=$value->_id;?></td>
													<td>
														<strong>CPU</strong>: <?=$value->Data->CPU;?>%<br />
														<strong>Memory</strong>: <?=$value->Data->Memory;?>%<br />
														<strong>Diskspace</strong>: <?=$value->Data->Diskspace;?>%<br />
														<strong>Temperature</strong>: <?=$value->Data->Temperature;?>°C<br />
														<strong>Latitude</strong>: <?=$value->Data->Latitude;?><br />
														<strong>Longitude</strong>: <?=$value->Data->Longitude;?><br />
													</td>
													<td><?=$value->Time;?> </td>
												  </tr>

												<?php
														endforeach;
													endif;
												?>

												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div><br />
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">Device iotJumpWay Sensors</h6>
								</div>
								<div class="pull-right"><a href="<?=$domain; ?>/iotJumpWay/<?=$Device["context"]["Data"]["lid"]["value"]; ?>/Zones/<?=$Device["context"]["Data"]["zid"]["value"]; ?>/Devices/<?=$Device["context"]["Data"]["did"]["value"]; ?>/Sensors"><i class="fa fa-eye pull-left"></i> View All Device Sensors Data</a></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="table-wrap mt-40">
										<div class="table-responsive">
											<table class="table mb-0">
												<thead>
												  <tr>
													<th>ID</th>
													<th>Type</th>
													<th>Sensor</th>
													<th>Value</th>
													<th>Message</th>
													<th>Time</th>
												  </tr>
												</thead>
												<tbody>

												<?php
													$Statuses = $iotJumpWay->retrieveDeviceSensors($Device["context"]["Data"]["did"]["value"], 5);
													if($Statuses["Response"] == "OK"):
														foreach($Statuses["ResponseData"] as $key => $value):
															$location = $iotJumpWay->getLocation($value->Location);
												?>
												  <tr>
													<td>#<?=$value->_id;?></td>
													<td><?=$value->Type;?></td>
													<td><?=$value->Sensor;?></td>
													<td>
														<?php
															if(($value->Sensor == "Facial API" || $value->Sensor == "Foscam Camera" || $value->Sensor == "USB Camera") && is_array($value->Value)):
																foreach($value->Value AS $key => $val):
																	 echo  $val[0] == 0 ? "<strong>Identification: </strong> Intruder<br />" :"<strong>Identification: </strong> User #" . $val[0] . "<br />";
																	echo "<strong>Distance: </strong> " . $val[1] . "<br />";
																	echo "<strong>Message: </strong> " . $val[2] . "<br /><br />";
																endforeach;
															else:
																echo $value->Value;
															endif;
														?>

													</td>
													<td><?=$value->Message;?></td>
													<td><?=$value->Time;?> </td>
												  </tr>

												<?php
														endforeach;
													endif;
												?>

												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div><br />
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">Device iotJumpWay Commands</h6>
								</div>
								<div class="pull-right"><a href="<?=$domain; ?>/iotJumpWay/<?=$Device["context"]["Data"]["lid"]["value"]; ?>/Zones/<?=$Device["context"]["Data"]["zid"]["value"]; ?>/Devices/<?=$Device["context"]["Data"]["did"]["value"]; ?>/Commands"><i class="fa fa-eye pull-left"></i> View All Device Commands Data</a></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="table-wrap mt-40">
										<div class="table-responsive">
											<table class="table mb-0">
												<thead>
												  <tr>
													<th>ID</th>
													<th>Details</th>
													<th>Status</th>
													<th>Time</th>
												  </tr>
												</thead>
												<tbody>

												<?php
													$Statuses = $iotJumpWay->retrieveDeviceCommands($Device["context"]["Data"]["did"]["value"], 5);
													if($Statuses["Response"] == "OK"):
														foreach($Statuses["ResponseData"] as $key => $value):
															$location = $iotJumpWay->getLocation($value->Location);
												?>

												  <tr>
													<td>#<?=$value->_id;?></td>
													<td><strong>Location:</strong> #<?=$value->Location;?> - <?=$location["name"]; ?></td>
													<td><?=$value->Status;?></td>
													<td><?=$value->Time;?> </td>
												  </tr>

												<?php
														endforeach;
													endif;
												?>

												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
	 	 	 	 	 </div>
	 	 	 	 	 <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
								<div class="pull-right"><span id="offline1" style="color: #33F9FF !important;" class="<?=$dev1On; ?>"><i class="fas fa-power-off" style="color: #33F9FF !important;"></i> Online</span> <span id="online1" class="<?=$dev1Off; ?>" style="color: #99A3A4 !important;"><i class="fas fa-power-off" style="color: #99A3A4 !important;"></i> Offline</span></div>
									<div class="form-group">
										<label class="control-label col-md-5">Status</label>
										<div class="col-md-12">
											<i class="fas fa-battery-full data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idebatU"><?=$Device["context"]["Data"]["batteryLevel"]["value"]; ?></span>% &nbsp;&nbsp;
											<i class="fa fa-microchip data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idecpuU"><?=$Device["context"]["Data"]["cpuUsage"]["value"]; ?></span>% &nbsp;&nbsp;
											<i class="zmdi zmdi-memory data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idememU"><?=$Device["context"]["Data"]["memoryUsage"]["value"]; ?></span>% &nbsp;&nbsp;
											<i class="far fa-hdd data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idehddU"><?=$Device["context"]["Data"]["hddUsage"]["value"]; ?></span>% &nbsp;&nbsp;
											<i class="fa fa-thermometer-quarter data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idetempU"><?=$Device["context"]["Data"]["temperature"]["value"]; ?></span>°C
										</div>
									</div>
								</div>
							</div>
						</div>
	 	 	 	 	 	 <div class="panel panel-default card-view panel-refresh">
	 	 	 	 	 	 	 <div class="panel-wrapper collapse in">
	 	 	 	 	 	 	 	 <div class="panel-body">
								<div class="pull-right"></div>
	 	 	 	 	 	 	 	 	 <div class="form-group">
	 	 	 	 	 	 	 	 	 	 <div class="col-md-12">
  										 	 <div id="map1" class="map" style="height: 300px;"></div>
	 	 	 	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 </div>
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="pull-right"><a href="javascipt:void(0)" id="reset_dvc_apriv"><i class="fa fa-refresh"></i> Reset API Key</a></div>
									<div class="form-group">
										<label class="control-label col-md-5">Identifier</label>
										<div class="col-md-9">
											<p class="form-control-static" id="idappid"><?=$Device["context"]["Data"]["id"]; ?></p>
											<p><strong>Last Updated:</strong> <?=array_key_exists("timestamp", $Device["context"]["Data"]["keys"]) ? $Device["context"]["Data"]["keys"]["timestamp"] : "NA"; ?></p>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="pull-right"></div>
									<div class="form-group">
										<label class="control-label col-md-5">Blockchain Address</label>
										<div class="col-md-9">
											<p class="form-control-static" id="bcid"><?=$Device["context"]["Data"]["blockchain"]["address"]; ?></p>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="pull-right"><a href="javascipt:void(0)" id="reset_dvc_mqtt"><i class="fa fa-refresh"></i> Reset MQTT Password</a></div>
									<div class="form-group">
										<label class="control-label col-md-5">MQTT Username</label>
										<div class="col-md-9">
											<p class="form-control-static" id="idmqttu"><?=$_GeniSys->_helpers->oDecrypt($Device["context"]["Data"]["mqtt"]["username"]); ?></p>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-5">MQTT Password</label>
										<div class="col-md-9">
											<p class="form-control-static"><span id="idmqttp"><?=$_GeniSys->_helpers->oDecrypt($Device["context"]["Data"]["mqtt"]["password"]); ?></span>
											<p><strong>Last Updated:</strong> <?=array_key_exists("timestamp", $Device["context"]["Data"]["mqtt"]) ? $Device["context"]["Data"]["mqtt"]["timestamp"] : "NA"; ?></p>
											</p>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="pull-right"><a href="javascipt:void(0)" id="reset_dvc_amqp"><i class="fa fa-refresh"></i> Reset AMQP Password</a></div>
									<div class="form-group">
										<label class="control-label col-md-5">AMQP Username</label>
										<div class="col-md-9">
											<p class="form-control-static" id="damqpu"><?=$Device["context"]["Data"]["amqp"]["username"] ? $_GeniSys->_helpers->oDecrypt($Device["context"]["Data"]["amqp"]["username"]) : ""; ?></p>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-5">AMQP Password</label>
										<div class="col-md-9">
											<p class="form-control-static"><span id="damqpp"><?=$Device["context"]["Data"]["amqp"]["password"] ? $_GeniSys->_helpers->oDecrypt($Device["context"]["Data"]["amqp"]["password"]) : ""; ?></span>
											<p><strong>Last Updated:</strong> <?=array_key_exists("timestamp", $Device["context"]["Data"]["amqp"]) ? $Device["context"]["Data"]["amqp"]["timestamp"] : "NA"; ?></p>
											</p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>

			<?php include dirname(__FILE__) . '/../../Includes/Footer.php'; ?>

		</div>

		<?php  include dirname(__FILE__) . '/../../Includes/JS.php'; ?>

		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/mqttws31.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/iotJumpWay.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/iotJumpWayUI.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/AI/ALL/Classes/ALL.js"></script>

		<script type="text/javascript">

	 	 	 $(document).ready(function() {
				iotJumpwayUI.HideDeviceInputs();
				iotJumpwayUI.StartDeviceLife();
	 	 	 });

			function initMap() {

				var latlng = new google.maps.LatLng("<?=floatval($Device["context"]["Data"]["location"]["value"]["coordinates"][0]); ?>", "<?=floatval($Device["context"]["Data"]["location"]["value"]["coordinates"][1]); ?>");
				var map = new google.maps.Map(document.getElementById('map1'), {
					zoom: 10,
					center: latlng
				});

				var loc = new google.maps.LatLng(<?=floatval($Device["context"]["Data"]["location"]["value"]["coordinates"][0]); ?>, <?=floatval($Device["context"]["Data"]["location"]["value"]["coordinates"][1]); ?>);
				var marker = new google.maps.Marker({
					position: loc,
					map: map,
					title: 'Device '
				});
			}

		</script>
		<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?=$_GeniSys->_helpers->oDecrypt($_GeniSys->_confs["gmaps"]); ?>&callback=initMap"></script>

	</body>
</html>