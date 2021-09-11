<?php session_start();

$pageDetails = [
	"PageID" => "AI",
	"SubPageID" => "AIAgents",
	"LowPageID" => "AIAgents"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../HIASCDI/Classes/Interface.php';
include dirname(__FILE__) . '/../AI/Classes/AI.php';
include dirname(__FILE__) . '/../AI/Classes/AiAgents.php';

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
		<meta name="author" content="hencework"/>

		<script src="https://kit.fontawesome.com/58ed2b8151.js" crossorigin="anonymous"></script>

		<link type="image/x-icon" rel="icon" href="/img/favicon.png" />
		<link type="image/x-icon" rel="shortcut icon" href="/img/favicon.png" />
		<link type="image/x-icon" rel="apple-touch-icon" href="/img/favicon.png" />

		<link href="/vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
		<link href="/vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
		<link href="/vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet" type="text/css">
		<link href="/dist/css/style.css" rel="stylesheet" type="text/css">
	</head>
	<body>

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
										<h6 class="panel-title txt-dark">Create HIAS AI Agent</h6>
									</div>
									<div class="pull-right"></div>
									<div class="clearfix"></div>
								</div>
								<div class="panel-wrapper collapse in">
									<div class="panel-body">
										<div class="form-wrap">
											<form data-toggle="validator" role="form" id="ai_agent_create">
												<div class="row">
													<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
														<div class="form-group">
															<label for="name" class="control-label mb-10">Name</label>
															<input type="text" class="form-control" id="name" name="name" placeholder="Agent Name" required value="">
															<span class="help-block"> Name of Agent</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Description</label>
															<textarea class="form-control" id="description" name="description" placeholder="Device Description" required></textarea>
															<span class="help-block"> Description of Agent</span>
														</div>
														<div class="form-group">
															<label class="control-label mb-10">Agent Type</label>
															<select class="form-control" id="atype" name="atype" required>
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
															<label for="name" class="control-label mb-10">Device Brand Name</label>
															<input type="text" class="form-control" id="deviceName" name="deviceName" placeholder="Hardware device name" required value="">
															<span class="help-block">Brand name of device</span>
														</div>
														<div class="form-group">
															<label class="control-label mb-10">Device Model</label>
															<select class="form-control" id="deviceModel" name="deviceModel" required>
																<option value="">PLEASE SELECT</option>

																<?php
																	$categories = $iotJumpWay->get_device_models();
																	if(count($categories)):
																		foreach($categories as $key => $value):
																?>

																<option value="<?=$value["category"]; ?>"><?=$value["category"]; ?></option>

																<?php
																		endforeach;
																	endif;
																?>

															</select>
															<span class="help-block">Model of device</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Device Manufacturer</label>
															<input type="text" class="form-control" id="deviceManufacturer" name="deviceManufacturer" placeholder="Hardware device manufacturer" required value="">
															<span class="help-block">Name of device manufacturer</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Operating System</label>
															<input type="text" class="form-control" id="osName" name="osName" placeholder="Operating system name" required value="">
															<span class="help-block">Operating system name</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Operating system manufacturer</label>
															<input type="text" class="form-control" id="osManufacturer" name="osManufacturer" placeholder="Operating system manufacturer" required value="">
															<span class="help-block">Operating system manufacturer</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Operating system version</label>
															<input type="text" class="form-control" id="osVersion" name="osVersion" placeholder="Operating system version" required value="">
															<span class="help-block">Operating system version</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Software</label>
															<input type="text" class="form-control" id="softwareName" name="softwareName" placeholder="Software name" required value="">
															<span class="help-block">Software name</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Software Version</label>
															<input type="text" class="form-control" id="softwareVersion" name="softwareVersion" placeholder="Software version" required value="">
															<span class="help-block">Software name</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Software Manufacturer</label>
															<input type="text" class="form-control" id="softwareManufacturer" name="softwareManufacturer" placeholder="Software manufacturer" required value="">
															<span class="help-block">Software name</span>
														</div>
														<div class="form-group">
															<label class="control-label mb-10">Protocols</label>
															<select class="form-control" id="protocols" name="protocols[]" required multiple>

																<?php
																	$protocols = $HiasInterface->get_protocols();
																	if(count($protocols)):
																		foreach($protocols as $key => $value):
																?>

																	<option value="<?=$value["protocol"]; ?>"><?=$value["protocol"]; ?></option>

																<?php
																		endforeach;
																	endif;
																?>

															</select>
															<span class="help-block">Supported Communication Protocols</span>
														</div>
														<div class="form-group">
															<label class="control-label mb-10">AI Models</label>
															<select class="form-control" id="ai" name="ai[]" multiple>

																<?php
																	$models = $AI->get_models();
																	if(!isSet($models["Error"])):
																		foreach($models as $key => $value):
																?>

																	<option value="<?=$value["id"]; ?>"><?=$value["name"]["value"]; ?></option>

																<?php
																		endforeach;
																	endif;
																?>

															</select>
															<span class="help-block">Device AI Models</span>
														</div>
														<div class="form-group">
															<label class="control-label mb-10">Sensors</label>
															<select class="form-control" id="sensorSelect">
																<option value="">Select Sensors To Add</option>

																<?php
																	$sensors = $iotJumpWay->get_things(0, "sensor");
																	if(!isset($sensors["Error"])):
																		foreach($sensors as $key => $value):
																?>

																	<option value="<?=$value["id"]; ?>"><?=$value["name"]["value"]; ?></option>

																<?php
																		endforeach;
																	endif;
																?>

															</select><br />
															<div id="sensorContent">
															</div>
															<span class="help-block">Device Sensors</span>
															<span class="hidden" id="lastSensor"><?=$key ? $key : 0; ?></span>
														</div>
														<div class="form-group">
															<label class="control-label mb-10">Actuators</label>
															<select class="form-control" id="actuatorSelect">
																<option value="">Select Actuators To Add</option>

																<?php
																	$actuators = $iotJumpWay->get_things(0, "actuator");
																	if(!isSet($actuators["Error"])):
																		foreach($actuators["value"] as $key => $value):
																?>

																	<option value="<?=$value["id"]; ?>"><?=$value["name"]["value"]; ?></option>

																<?php
																		endforeach;
																	endif;
																?>

															</select><br />
															<div id="actuatorContent">
															</div>
															<span class="help-block">Device Actuators</span>
															<span class="hidden" id="lastActuator"><?=$key ? $key : 0; ?></span>
														</div>
													</div>
													<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
														<div class="form-group">
															<label class="control-label mb-10">Location</label>
															<select class="form-control" id="lid" name="lid" required>
																<option value="">PLEASE SELECT</option>

																<?php
																	$Locations = $iotJumpWay->get_locations();
																	if(!isSet($Locations["Error"])):
																		foreach($Locations as $key => $value):
																?>

																	<option value="<?=$value["id"]; ?>"><?=$value["name"]["value"]; ?></option>

																<?php
																		endforeach;
																	endif;
																?>

															</select>
															<span class="help-block"> Location of Agent</span>
														</div>
														<div class="form-group">
															<label class="control-label mb-10">Zone</label>
															<select class="form-control" id="zid" name="zid" required>
																<option value="">PLEASE SELECT</option>
																<?php
																	$Zones = $iotJumpWay->get_zones();
																	if(!isSet($Zones["Error"])):
																		foreach($Zones as $key => $value):
																?>

																<option value="<?=$value["id"]; ?>"><?=$value["name"]["value"]; ?></option>

																<?php
																		endforeach;
																	endif;
																?>

															</select>
															<span class="help-block">Zone that HIASCDI is installed in</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Coordinates</label>
															<input type="text" class="form-control" id="coordinates" name="coordinates" placeholder="iotJumpWay Location coordinates" required value="">
															<span class="help-block">iotJumpWay Agent coordinates</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">IP</label>
															<input type="text" class="form-control hider" id="ip" name="ip" placeholder="Device IP" required value="">
															<span class="help-block"> IP of Agent</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">MAC</label>
															<input type="text" class="form-control hider" id="mac" name="mac" placeholder="Device MAC" required value="">
															<span class="help-block"> MAC of Agent</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Bluetooth Only?</label>
															<input type="checkbox" class="" id="bluetoothOnly" name="bluetoothOnly"  value=1 >
															<span class="help-block">Agent only supports Bluetooth/BLE?</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Bluetooth Address</label>
															<input type="text" class="form-control hider" id="bluetooth" name="bluetooth" placeholder="Device Bluetooth Address"  value="">
															<span class="help-block">Bluetooth address of Agent</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">North Port</label>
															<input type="text" class="form-control hider" id="northPort" name="northPort" placeholder="North Port of Agent" required value="">
															<span class="help-block"> North Port of Agent</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Socket Port</label>
															<input type="text" class="form-control hider" id="socketPort" name="socketPort" placeholder="Socket Port of agent" required value="">
															<span class="help-block"> Socket Port of agent</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Stream Port</label>
															<input type="text" class="form-control hider" id="streamPort" name="streamPort" placeholder="Strean Port of agent" required value="">
															<span class="help-block"> Socket Port of agent</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Data directory</label>
															<input type="text" class="form-control hider" id="dataDir" name="dataDir" placeholder="Name of HIAS data directory" required value="">
															<span class="help-block"> Name of HIAS data directory to be created</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">API Endpoint</label>
															<input type="text" class="form-control hider" id="endpoint" name="endpoint" placeholder="Name of Agent API endpoint" required value="">
															<span class="help-block"> Name of Agent API endpoint</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">IPinfo Key</label>
															<input type="text" class="form-control hider" id="authenticationIpinfoKey" name="authenticationIpinfoKey" placeholder="IPInfo key" required value="" >
															<span class="help-block"><a hef="https://ipinfo.io/" target="_BLANK">IPinfo</a> key</span>
														</div>
													</div>
												</div>
												<div class="form-group mb-0">
													<input type="hidden" class="form-control" id="category" name="category" required value="AI Agent">
													<input type="hidden" class="form-control" id="create_agent" name="create_agent" required value=1>
													<button type="submit" class="btn btn-success btn-anim" id="agent_create"><i class="icon-rocket"></i><span class="btn-text">Create</span></button>
												</div>
											</form>
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
		<script type="text/javascript" src="/AI/Classes/AiAgents.js"></script>

	</body>
</html>