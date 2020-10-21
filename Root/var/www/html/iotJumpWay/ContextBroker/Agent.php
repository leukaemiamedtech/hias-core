<?php session_start();

$pageDetails = [
	"PageID" => "IoT",
	"SubPageID" => "Context",
	"LowPageID" => "Agents"
];

include dirname(__FILE__) . '/../../../Classes/Core/init.php';
include dirname(__FILE__) . '/../../../Classes/Core/GeniSys.php';
include dirname(__FILE__) . '/../../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../../iotJumpWay/ContextBroker/Classes/ContextBroker.php';
include dirname(__FILE__) . '/../../AI/Classes/AI.php';

$_GeniSysAi->checkSession();

$AId = filter_input(INPUT_GET, 'agent', FILTER_SANITIZE_NUMBER_INT);
$Agent = $ContextBroker->getAgent($AId);
list($appOn, $appOff) = $iotJumpWay->getStatusShow($Agent["context"]["Data"]["status"]["value"]);

$cancelled = $Agent["context"]["Data"]["cancelled"]["value"] ? True : False;

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
		<meta name="author" content="hencework"/>

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
									<h6 class="panel-title txt-dark">Agent #<?=$AId; ?></h6>
								</div>
								<div class="pull-right"></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="form-wrap">
										<form data-toggle="validator" role="form" id="iot_agent_update">
											<hr class="light-grey-hr"/>
											<div class="row">
												<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
													<div class="form-group">
														<label for="name" class="control-label mb-10">Name</label>
														<input type="text" class="form-control" id="name" name="name" placeholder="Agent Name" required value="<?=$Agent["context"]["Data"]["name"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block"> Name of agent</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Description</label>
														<input type="text" class="form-control" id="description" name="description" placeholder="Agent Description" required value="<?=$Agent["context"]["Data"]["description"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block"> Description of agent</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Hardware Agent Name</label>
														<input type="text" class="form-control" id="deviceName" name="deviceName" placeholder="Hardware device name" required value="<?=$Agent["context"]["Data"]["device"]["name"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">Name of hardware device</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Hardware Agent Model</label>
														<input type="text" class="form-control" id="deviceModel" name="deviceModel" placeholder="Hardware device model" required value="<?=$Agent["context"]["Data"]["device"]["model"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">Hardware model</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Hardware Agent Manufacturer</label>
														<input type="text" class="form-control" id="deviceManufacturer" name="deviceManufacturer" placeholder="Hardware device manufacturer" required value="<?=$Agent["context"]["Data"]["device"]["manufacturer"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">Name of hardware manufacturer</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Operating System</label>
														<input type="text" class="form-control" id="osName" name="osName" placeholder="Operating system name" required value="<?=$Agent["context"]["Data"]["os"]["name"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">Operating system name</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Operating system manufacturer</label>
														<input type="text" class="form-control" id="osManufacturer" name="osManufacturer" placeholder="Operating system manufacturer" required value="<?=$Agent["context"]["Data"]["os"]["manufacturer"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">Operating system manufacturer</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Operating system version</label>
														<input type="text" class="form-control" id="osVersion" name="osVersion" placeholder="Operating system version" required value="<?=$Agent["context"]["Data"]["os"]["version"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">Operating system version</span>
													</div>
													<div class="form-group">
														<label class="control-label mb-10">Protocols</label>
														<select class="form-control" id="protocols" name="protocols[]" required multiple <?=$cancelled ? " disabled " : ""; ?>>

															<?php
																$protocols = $iotJumpWay->getContextBrokerProtocols();
																if(count($protocols)):
																	foreach($protocols as $key => $value):
															?>

																<option value="<?=$value["protocol"]; ?>" <?=in_array($value["protocol"], $Agent["context"]["Data"]["protocols"]) ? " selected " : ""; ?>><?=$value["protocol"]; ?></option>

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
																if(isSet($Agent["context"]["Data"]["sensors"])):
																	foreach($Agent["context"]["Data"]["sensors"] AS $key => $value):
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
																if(isSet($Agent["context"]["Data"]["actuators"])):
																	foreach($Agent["context"]["Data"]["actuators"] AS $key => $value):
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

																<option value="<?=$value["mid"]["value"]; ?>" <?=array_key_exists($value["name"]["value"], $Agent["context"]["Data"]["ai"]) ? " selected " : ""; ?>><?=$value["name"]["value"]; ?></option>

															<?php
																	endforeach;
																endif;
															?>

														</select>
														<span class="help-block">Device AI Models</span>
													</div>

													<?php if(!$cancelled): ?>

													<div class="form-group mb-0">
														<input type="hidden" class="form-control" id="update_agent" name="update_agent" required value="1">
														<button type="submit" class="btn btn-success btn-anim" id="agent_update"><i class="icon-rocket"></i><span class="btn-text">Update</span></button>
													</div>

													<?php endif; ?>

												</div>
												<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
													<div class="form-group">
														<label class="control-label mb-10">Location</label>
														<select class="form-control" id="lid" name="lid" required <?=$cancelled ? " disabled " : ""; ?>>
															<option value="">PLEASE SELECT</option>

															<?php
																$Locations = $iotJumpWay->getLocations();
																if(count($Locations["Data"])):
																	foreach($Locations["Data"] as $key => $value):
															?>

																<option value="<?=$value["lid"]["value"]; ?>" <?=$value["lid"]["value"] == $Agent["context"]["Data"]["lid"]["value"] ? " selected " : ""; ?>>#<?=$value["lid"]["value"]; ?>: <?=$value["name"]["value"]; ?></option>

															<?php
																	endforeach;
																endif;
															?>

														</select>
														<span class="help-block"> Location of agent</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Coordinates</label>
														<input type="text" class="form-control" id="coordinates" name="coordinates" placeholder="iotJumpWay Location coordinates" required value="<?=$Agent["context"]["Data"]["location"]["value"]["coordinates"][0]; ?>, <?=$Agent["context"]["Data"]["location"]["value"]["coordinates"][1]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">iotJumpWay Agent coordinates</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">IP</label>
														<input type="text" class="form-control hider" id="ip" name="ip" placeholder="Agent IP" required value="<?=$Agent["context"]["Data"]["ip"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block"> IP of agent</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">North Port</label>
														<input type="text" class="form-control hider" id="northPort" name="northPort" placeholder="North Port of agent" required value="<?=$Agent["context"]["Data"]["northPort"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block"> North Port of agent</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">About Endpoint</label>
														<input type="text" class="form-control" id="about" name="about" placeholder="About Endpoint" required value="<?=$Agent["context"]["Data"]["endpoints"]["about"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">About Endpoint of agent</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Command Endpoint</label>
														<input type="text" class="form-control" id="commands" name="commands" placeholder="Command Endpoint" required value="<?=$Agent["context"]["Data"]["endpoints"]["commands"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">Command Endpoint of agent</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">MAC</label>
														<input type="text" class="form-control hider" id="mac" name="mac" placeholder="Agent MAC" required value="<?=$Agent["context"]["Data"]["mac"]["value"] ? $_GeniSys->_helpers->oDecrypt($Agent["context"]["Data"]["mac"]["value"]) : ""; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block"> MAC of agent</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Bluetooth Address</label>
														<input type="text" class="form-control hider" id="bluetooth" name="bluetooth" placeholder="Agent Bluetooth Address"  value="<?=$Agent["context"]["Data"]["bluetooth"]["address"] ? $_GeniSys->_helpers->oDecrypt($Agent["context"]["Data"]["bluetooth"]["address"]) : ""; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">Bluetooth address of agent</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Is Admin:</label>
														<input type="checkbox" class="" id="admin" name="admin" value=1 <?=$Agent["context"]["Data"]["admin"]["value"] ? " checked " : ""; ?> <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block"> Is agent an admin?</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Has Patient Access:</label>
														<input type="checkbox" class="" id="patients" name="patients" value=1 <?=$Agent["context"]["Data"]["patients"]["value"] ? " checked " : ""; ?> <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">Does staff member has patients access?</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Is Cancelled:</label>
														<input type="checkbox" class="" id="cancelled" name="cancelled" value=1 <?=$Agent["context"]["Data"]["cancelled"]["value"] ? " checked " : ""; ?> <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block"> Is agent cancelled?</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Date Created</label>
														<p><?=$Agent["context"]["Data"]["dateCreated"]["value"]; ?></p>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Date First Used</label>
														<p><?=$Agent["context"]["Data"]["dateFirstUsed"]["value"] ? $Agent["context"]["Data"]["dateFirstUsed"]["value"] : "NA"; ?></p>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Date Modified</label>
														<p><?=$Agent["context"]["Data"]["dateModified"]["value"]; ?></p>
													</div>
													<div class="clearfix"></div>
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
										<?php echo "<pre id='schema'>"; ?> <?php print_r(json_encode($Agent["context"]["Data"], JSON_PRETTY_PRINT)); ?> <?php echo "</pre>"; ?>
									</div>
								</div>
							</div>
						</div><br />
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">Agent History</h6>
								</div>
								<div class="pull-right"><a href="<?=$domain; ?>/iotJumpWay/ContextBroker/Agents/<?=$Agent["context"]["Data"]["aid"]["value"]; ?>/History"><i class="fa fa-eye pull-left"></i> View All Agent History</a></div>
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
													$history = $ContextBroker->retrieveAgentHistory($Agent["context"]["Data"]["aid"]["value"], 5);
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
															<a href="<?=$domain; ?>/iotJumpWay/ContextBroker/Agents/<?=$Agent["context"]["Data"]["aid"]["value"]; ?>/Transaction/<?=$value["hash"];?>">#<?=$value["hash"];?></a>
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
									<h6 class="panel-title txt-dark">Agent Transactions</h6>
								</div>
								<div class="pull-right"><a href="<?=$domain; ?>/iotJumpWay/ContextBroker/Agents/<?=$Agent["context"]["Data"]["aid"]["value"]; ?>/Transactions"><i class="fa fa-eye pull-left"></i> View All Agent Transactions</a></div>
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
													$transactions = $ContextBroker->retrieveAgentTransactions($Agent["context"]["Data"]["aid"]["value"], 5);
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
													<td><a href="<?=$domain; ?>/iotJumpWay/ContextBroker/Agents/<?=$Agent["context"]["Data"]["aid"]["value"]; ?>/Transaction/<?=$value["id"];?>">#<?=$value["id"];?></a></td>
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
									<h6 class="panel-title txt-dark">Agent iotJumpWay Statuses</h6>
								</div>
								<div class="pull-right"><a href="<?=$domain; ?>/iotJumpWay/ContextBroker/Agents/<?=$Agent["context"]["Data"]["aid"]["value"]; ?>/Statuses"><i class="fa fa-eye pull-left"></i> View All Agent Status Data</a></div>
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
													$Statuses = $ContextBroker->retrieveAgentStatuses($Agent["context"]["Data"]["id"], 5);
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
									<h6 class="panel-title txt-dark">Agent iotJumpWay Life</h6>
								</div>
								<div class="pull-right"><a href="<?=$domain; ?>/iotJumpWay/ContextBroker/Agents/<?=$Agent["context"]["Data"]["aid"]["value"]; ?>/Life"><i class="fa fa-eye pull-left"></i> View All Agent Life Data</a></div>
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
													$Statuses = $ContextBroker->retrieveAgentLife($Agent["context"]["Data"]["id"], 5);
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
									<h6 class="panel-title txt-dark">Agent iotJumpWay Sensors</h6>
								</div>
								<div class="pull-right"><a href="<?=$domain; ?>/iotJumpWay/ContextBroker/Agents/<?=$Agent["context"]["Data"]["aid"]["value"]; ?>/Sensors"><i class="fa fa-eye pull-left"></i> View All Agent Sensors Data</a></div>
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
													$Statuses = $ContextBroker->retrieveAgentSensors($Agent["context"]["Data"]["id"], 5);
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
									<h6 class="panel-title txt-dark">Agent iotJumpWay Commands</h6>
								</div>
								<div class="pull-right"><a href="<?=$domain; ?>/iotJumpWay/ContextBroker/Agents/<?=$Agent["context"]["Data"]["aid"]["value"]; ?>/Commands"><i class="fa fa-eye pull-left"></i> View All Agent Commands Data</a></div>
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
													$Statuses = $ContextBroker->retrieveAgentCommands($Agent["context"]["Data"]["id"], 5);
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
					<?php if(!$cancelled): ?>
					<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
								<div class="pull-right"><span id="offline1" style="color: #33F9FF !important;" class="<?=$appOn; ?>"><i class="fas fa-power-off" style="color: #33F9FF !important;"></i> Online</span> <span id="online1" class="<?=$appOff; ?>" style="color: #99A3A4 !important;"><i class="fas fa-power-off" style="color: #99A3A4 !important;"></i> Offline</span></div>
									<div class="form-group">
										<label class="control-label col-md-5">Status</label>
										<div class="col-md-12">
											<i class="fas fa-battery-full data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idebatU"><?=$Agent["context"]["Data"]["batteryLevel"]["value"]; ?></span>% &nbsp;&nbsp;
											<i class="fa fa-microchip data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idecpuU"><?=$Agent["context"]["Data"]["cpuUsage"]["value"]; ?></span>% &nbsp;&nbsp;
											<i class="zmdi zmdi-memory data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idememU"><?=$Agent["context"]["Data"]["memoryUsage"]["value"]; ?></span>% &nbsp;&nbsp;
											<i class="far fa-hdd data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idehddU"><?=$Agent["context"]["Data"]["hddUsage"]["value"]; ?></span>% &nbsp;&nbsp;
											<i class="fa fa-thermometer-quarter data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idetempU"><?=$Agent["context"]["Data"]["temperature"]["value"]; ?></span>°C
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div id="map1" style="height:300px;"></div>
								</div>
							</div>
						</div>
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="pull-right"><a href="javascipt:void(0)" id="reset_agent_apriv"><i class="fa fa-refresh"></i> Reset API Key</a></div>
									<div class="form-group">
										<label class="control-label col-md-5">Identifier</label>
										<div class="col-md-9">
											<p class="form-control-static" id="appid"><?=$Agent["context"]["Data"]["id"]; ?></p>
											<p><strong>Last Updated:</strong> <?=array_key_exists("timestamp", $Agent["context"]["Data"]["keys"]) ? $Agent["context"]["Data"]["keys"]["timestamp"] : "NA"; ?></p>
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
											<p class="form-control-static" id="bcid"><?=$Agent["context"]["Data"]["blockchain"]["address"]; ?></p>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="pull-right"><a href="javascipt:void(0)" id="reset_agent_mqtt"><i class="fa fa-refresh"></i> Reset MQTT Password</a></div>
									<div class="form-group">
										<label class="control-label col-md-5">MQTT Username</label>
										<div class="col-md-9">
											<p class="form-control-static " id="amqttu"><?=$_GeniSys->_helpers->oDecrypt($Agent["context"]["Data"]["mqtt"]["username"]); ?></p>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-5">MQTT Password</label>
										<div class="col-md-9">
											<p class="form-control-static"><span id="amqttp"><?=$_GeniSys->_helpers->oDecrypt($Agent["context"]["Data"]["mqtt"]["password"]); ?></span></p>
											<p><strong>Last Updated:</strong> <?=array_key_exists("timestamp", $Agent["context"]["Data"]["keys"]) ? $Agent["context"]["Data"]["keys"]["timestamp"] : "NA"; ?></p>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="pull-right"><a href="javascipt:void(0)" id="reset_agent_amqp"><i class="fa fa-refresh"></i> Reset AMQP Password</a></div>
									<div class="form-group">
										<label class="control-label col-md-5">AMQP Username</label>
										<div class="col-md-9">
											<p class="form-control-static" id="appamqpu"><?=$Agent["context"]["Data"]["amqp"]["username"] ? $_GeniSys->_helpers->oDecrypt($Agent["context"]["Data"]["amqp"]["username"]) : ""; ?></p>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-5">AMQP Password</label>
										<div class="col-md-9">
											<p class="form-control-static"><span id="appamqpp"><?=$Agent["context"]["Data"]["amqp"]["password"] ? $_GeniSys->_helpers->oDecrypt($Agent["context"]["Data"]["amqp"]["password"]) : ""; ?></span>
											<p><strong>Last Updated:</strong> <?=array_key_exists("timestamp", $Agent["context"]["Data"]["amqp"]) ? $Agent["context"]["Data"]["amqp"]["timestamp"] : "NA"; ?></p>
											</p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php endif; ?>
				</div>

			</div>

			<?php include dirname(__FILE__) . '/../../Includes/Footer.php'; ?>

		</div>

		<?php  include dirname(__FILE__) . '/../../Includes/JS.php'; ?>

		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/mqttws31.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/iotJumpWay.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/iotJumpWayUI.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/ContextBroker/Classes/ContextBroker.js"></script>

		<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?=$_GeniSys->_helpers->oDecrypt($_GeniSys->_confs["gmaps"]); ?>&callback=initMap"></script>
		<script>

			ContextBroker.HideAgentInputs();
			ContextBroker.StartAgentLife();

			function initMap() {

				var latlng = new google.maps.LatLng("<?=floatval($Agent["context"]["Data"]["location"]["value"]["coordinates"][0]); ?>", "<?=floatval($Agent["context"]["Data"]["location"]["value"]["coordinates"][1]); ?>");
				var map = new google.maps.Map(document.getElementById('map1'), {
					zoom: 10,
					center: latlng
				});

				var loc = new google.maps.LatLng(<?=floatval($Agent["context"]["Data"]["location"]["value"]["coordinates"][0]); ?>, <?=floatval($Agent["context"]["Data"]["location"]["value"]["coordinates"][1]); ?>);
				var marker = new google.maps.Marker({
					position: loc,
					map: map,
					title: 'Agent '
				});
			}

		</script>

	</body>

</html>
