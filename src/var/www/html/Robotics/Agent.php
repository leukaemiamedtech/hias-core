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

$aid = filter_input(INPUT_GET, 'agent', FILTER_SANITIZE_STRING);
$agent = $AiAgents->get_agent($aid);

list($appOn, $appOff) = $iotJumpWay->get_device_status($agent["network"]["status"]["value"]);

$cancelled = $agent["permissions"]["cancelled"]["value"] ? True : False;

$data = $AiAgents->agent_life_graph($aid, 100, -1);

$cpu = [];
$memory = [];
$diskspace = [];
$temperature = [];
$dates = [];

if(isSet($data["ResponseData"])):
	foreach($data["ResponseData"] AS $key => $value):
		if(isSet($value->Data)):
			$cpu[] = $value->Data->CPU;
			$memory[] = $value->Data->Memory;
			$diskspace[] = $value->Data->Diskspace;
			$temperature[] = $value->Data->Temperature;
			$dates[] = $value->Time;
		endif;
	endforeach;
endif;

$dates = array_reverse($dates);

$points = [[
	"name" => "CPU",
	"data" => array_reverse($cpu),
	"type" => 'line',
	"smooth" => true,
	"color" => ['orange']
],
[
	"name" => "Memory",
	"data" => array_reverse($memory),
	"type" => 'line',
	"smooth" => true,
	"color" => ['yellow']
],
[
	"name" => "Diskspace",
	"data" => array_reverse($diskspace),
	"type" => 'line',
	"smooth" => true,
	"color" => ['red']
],
[
	"name" => "Temperature",
	"data" => array_reverse($temperature),
	"type" => 'line',
	"smooth" => true,
	"color" => ['purple']
]];
$legend = ["CPU","Memory","Diskspace","Temperature"];
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
									<h6 class="panel-title txt-dark">AI Agent Resource Monitor</h6>
								</div>
								<div class="pull-right">
									<select class="form-control" id="agentGraphs" name="agentGraphs" required>
										<option value="Life">Life Monitor</option>
										<option value="Sensors">Sensors Monitor</option>

									</select>
									<div class="pull-left inline-block dropdown">
									</div>
								</div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div id="e_chart_1" class="" style="height: 375px;"></div>
								</div>
							</div>
						</div>
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">Agent</h6>
								</div>
								<div class="pull-right"><a href="/AI/Agents/<?=$agent["id"]; ?>/Credentials" download><i class="fas fa-download"></i> Agent Credentials</a></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="form-wrap">
										<form data-toggle="validator" role="form" id="ai_agent_update">
											<hr class="light-grey-hr"/>
											<div class="row">
												<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
													<div class="form-group">
														<label for="name" class="control-label mb-10">Name</label>
														<input type="text" class="form-control" id="name" name="name" placeholder="agent Name" required value="<?=$agent["name"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block"> Name of agent</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Description</label>
														<input type="text" class="form-control" id="description" name="description" placeholder="agent Description" required value="<?=$agent["description"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block"> Description of agent</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Agent Function</label>
														<select class="form-control" id="function" name="function" required>
															<option value="">PLEASE SELECT</option>
															<option value="Diagnosis" <?=$agent["function"]["value"] == "Diagnosis" ? " selected " : ""; ?>>Diagnosis</option>
															<option value="Facial Identification" <?=$model["function"]["value"] == "Facial Identification" ? " selected " : ""; ?>>Facial Identification</option>
															<option value="Natural Language Understanding" <?=$agent["function"]["value"] == "Natural Language Understanding" ? " selected " : ""; ?>>Natural Language Understanding</option>
															<option value="Natural Language Understanding" <?=$agent["function"]["value"] == "Natural Language Understanding" ? " selected " : ""; ?>>Predictive</option>
														</select>
														<span class="help-block">The function of the agent</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Device Name</label>
														<input type="text" class="form-control" id="deviceName" name="deviceName" placeholder="Hardware device name" required value="<?=$agent["deviceBrandName"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">Name of device agent is installed on.</span>
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

															<option value="<?=$value["category"]; ?>"<?=$agent["deviceModel"]["value"] == $value["category"] ? " selected " : ""; ?> ><?=$value["category"]; ?></option>

															<?php
																	endforeach;
																endif;
															?>

														</select>
														<span class="help-block">Model of device</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Device Model</label>
														<input type="text" class="form-control" id="deviceModel" name="deviceModel" placeholder="Hardware device model" required value="<?=$agent["deviceModel"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">Model of device agent is installed on.</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Device Manufacturer</label>
														<input type="text" class="form-control" id="deviceManufacturer" name="deviceManufacturer" placeholder="Hardware device manufacturer" required value="<?=$agent["deviceManufacturer"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">Manufacturer of device agent is installed on.</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Operating System</label>
														<input type="text" class="form-control" id="os" name="os" placeholder="Operating system name" required value="<?=$agent["os"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">Operating system name</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Operating system manufacturer</label>
														<input type="text" class="form-control" id="osManufacturer" name="osManufacturer" placeholder="Operating system manufacturer" required value="<?=$agent["osManufacturer"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">Operating system manufacturer</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Operating system version</label>
														<input type="text" class="form-control" id="osVersion" name="osVersion" placeholder="Operating system version" required value="<?=$agent["osVersion"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">Operating system version</span>
													</div>
													<div class="form-group">
														<label class="control-label mb-10">Protocols</label>
														<select class="form-control" id="protocols" name="protocols[]" required multiple <?=$cancelled ? " disabled " : ""; ?>>

															<?php
																$protocols = $HiasInterface->get_protocols();
																if(count($protocols)):
																	foreach($protocols as $key => $value):
															?>

																<option value="<?=$value["protocol"]; ?>" <?=in_array($value["protocol"], $agent["protocols"]) ? " selected " : ""; ?>><?=$value["protocol"]; ?></option>

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

																<option value="<?=$value["id"]; ?>" <?=array_key_exists($value["name"]["value"], $agent["models"]) ? " selected " : ""; ?>><?=$value["name"]["value"]; ?></option>

															<?php
																	endforeach;
																endif;
															?>

														</select>
														<span class="help-block">Device AI Models</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Data Directory</label>
														<input type="text" class="form-control" id="dataDir" name="dataDir" placeholder="Data directort" required value="<?=$agent["dataDir"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">UI data directory name</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Inference Endpoint</label>
														<input type="text" class="form-control" id="endpoint" name="endpoint" placeholder="Data directort" required value="<?=$agent["endpoint"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">UI data directory name</span>
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
																$Locations = $iotJumpWay->get_locations();
																if(!isSet($Locations["Error"])):
																	foreach($Locations as $key => $value):
															?>

																<option value="<?=$value["id"]; ?>" <?=$value["id"] == $agent["network"]["location"]["id"]["value"] ? " selected " : ""; ?>><?=$value["name"]["value"]; ?></option>

															<?php
																	endforeach;
																endif;
															?>

														</select>
														<span class="help-block"> Location of agent</span>
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

															<option value="<?=$value["id"]; ?>" <?=$agent["network"]["zone"]["id"]["value"] == $value["id"] ? " selected " : ""; ?>><?=$value["name"]["value"]; ?></option>

															<?php
																	endforeach;
																endif;
															?>

														</select>
														<span class="help-block">Zone that HIASCDI is installed in</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Coordinates</label>
														<input type="text" class="form-control" id="coordinates" name="coordinates" placeholder="iotJumpWay Location coordinates" required value="<?=$agent["location"]["value"]["coordinates"][0]; ?>, <?=$agent["location"]["value"]["coordinates"][1]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">iotJumpWay agent coordinates</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">IP</label>
														<input type="text" class="form-control hider" id="ip" name="ip" placeholder="agent IP" required value="<?=$agent["ip"]["value"] ? $HIAS->helpers->oDecrypt($agent["ip"]["value"]) : ""; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block"> IP of agent</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">MAC</label>
														<input type="text" class="form-control hider" id="mac" name="mac" placeholder="agent MAC" required value="<?=$agent["mac"]["value"] ? $HIAS->helpers->oDecrypt($agent["mac"]["value"]) : ""; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block"> MAC of agent</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">North Port</label>
														<input type="text" class="form-control hider" id="northPort" name="northPort" placeholder="North Port of agent" required value="<?=$agent["northPort"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block"> North Port of agent</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">IPinfo Key</label>
														<input type="text" class="form-control hider" id="ipinfo" name="ipinfo" placeholder="IPInfo key" required value="<?=$HIAS->helpers->oDecrypt($agent["authentication"]["ipinfo"]["value"]); ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block"><a hef="https://ipinfo.io/" target="_BLANK">IPinfo</a> key</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Bluetooth Address</label>
														<input type="text" class="form-control hider" id="bluetooth" name="bluetooth" placeholder="agent Bluetooth Address"  value="<?=$agent["bluetooth"]["address"] ? $HIAS->helpers->oDecrypt($agent["bluetooth"]["address"]) : ""; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">Bluetooth address of agent</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Is Cancelled:</label>
														<input type="checkbox" class="" id="cancelled" name="cancelled" value=1 <?=$agent["permissions"]["cancelled"]["value"] ? " checked " : ""; ?> <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block"> Is agent cancelled?</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Date Created</label>
														<p><?=$agent["dateCreated"]["value"]; ?></p>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Date First Used</label>
														<p><?=$agent["dateFirstUsed"]["value"] ? $agent["dateFirstUsed"]["value"] : "NA"; ?></p>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Date Modified</label>
														<p><?=$agent["dateModified"]["value"]; ?></p>
													</div>
													<div class="clearfix"></div>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">Agent History</h6>
								</div>
								<div class="pull-right">
									<select class="form-control" id="agentHistory" name="agentHistory" required>
										<option value="Activity">Agent Activity</option>
										<option value="Transactions">Agent Transactions</option>
										<option value="Statuses">Agent Statuses</option>
										<option value="Life">Agent Life</option>
										<option value="Sensors">Agent Sensors</option>
										<option value="Actuators">Agent Actuators</option>

									</select>
								</div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="scroll_450px">
										<div class="table-wrap mt-40">
											<div class="table-responsive">
												<table class="table mb-0">
													<tbody id="agentHistoryContainer">

													<?php
														$userDetails = "";
														$history = $AiAgents->get_agent_history($agent["id"], 100);
														if(count($history)):
															foreach($history as $key => $value):
																	if($value["uid"]):
																		$user = $HIAS->get_user($value["uid"]);
																		$userDetails = "User ID " . $value["uid"] . " (" . $user["name"]["value"] . ") ";
																	endif;
													?>

													<tr>
														<td>#<?=$value["id"];?></td>
														<td><?=$userDetails;?><?=$value["action"];?></td>
														<td><?php if($value["hash"]): ?><a href="/iotJumpWay/Agents/<?=$agent["id"]; ?>/Transaction/<?=$value["hash"];?>">#<?=$value["hash"];?></a><?php else: ?> NA <?php endif; ?> </td>
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
											<i class="fas fa-battery-full data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idebatU"><?=$agent["batteryLevel"]["value"]; ?></span>% &nbsp;&nbsp;
											<i class="fa fa-microchip data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idecpuU"><?=$agent["cpuUsage"]["value"]; ?></span>% &nbsp;&nbsp;
											<i class="zmdi zmdi-memory data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idememU"><?=$agent["memoryUsage"]["value"]; ?></span>% &nbsp;&nbsp;
											<i class="far fa-hdd data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idehddU"><?=$agent["hddUsage"]["value"]; ?></span>% &nbsp;&nbsp;
											<i class="fa fa-thermometer-quarter data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idetempU"><?=$agent["temperature"]["value"]; ?></span>°C
										</div>

									</div>
									<div class="pull-right"><p><br /><strong>Last Updated:</strong> <?=$agent["network"]["status"]["metadata"]["timestamp"]; ?></p></div>
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
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">Agent Schema</h6>
								</div>
								<div class="pull-right"></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div  style="height: 400px; overflow-y: scroll; overflow-x: hidden;">
										<?php echo "<pre id='schema'>"; ?> <?php print_r(json_encode($agent, JSON_PRETTY_PRINT)); ?> <?php echo "</pre>"; ?>
									</div>
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
											<p class="form-control-static" id="appid"><?=$agent["id"]; ?></p>
											<p><strong>Last Updated:</strong> <?=$agent["authentication"]["network"]["private"]["metadata"]["timestamp"]; ?></p>
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
											<p class="form-control-static" id="bcid"><?=$agent["authentication"]["blockchain"]["address"]["value"]; ?></p>
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
											<p class="form-control-static " id="amqttu"><?=$HIAS->helpers->oDecrypt($agent["authentication"]["mqtt"]["username"]["value"]); ?></p>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-5">MQTT Password</label>
										<div class="col-md-9">
											<p class="form-control-static"><span id="amqttp"><?=$HIAS->helpers->oDecrypt($agent["authentication"]["mqtt"]["password"]["value"]); ?></span></p>
											<p><strong>Last Updated:</strong> <?=$agent["authentication"]["mqtt"]["password"]["metadata"]["timestamp"]; ?></p>
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
											<p class="form-control-static" id="appamqpu"><?=$agent["authentication"]["amqp"]["username"]["value"] ? $HIAS->helpers->oDecrypt($agent["authentication"]["amqp"]["username"]["value"]) : ""; ?></p>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-5">AMQP Password</label>
										<div class="col-md-9">
											<p class="form-control-static"><span id="appamqpp"><?=$agent["authentication"]["amqp"]["password"]["value"] ? $HIAS->helpers->oDecrypt($agent["authentication"]["amqp"]["password"]["value"]) : ""; ?></span>
											<p><strong>Last Updated:</strong> <?=$agent["authentication"]["amqp"]["password"]["metadata"]["timestamp"]; ?></p>
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

			<?php include dirname(__FILE__) . '/../Includes/Footer.php'; ?>

		</div>

		<?php  include dirname(__FILE__) . '/../Includes/JS.php'; ?>

		<script type="text/javascript" src="/iotJumpWay/Classes/mqttws31.js"></script>
		<script type="text/javascript" src="/iotJumpWay/Classes/iotJumpWay.js"></script>
		<script type="text/javascript" src="/iotJumpWay/Classes/iotJumpWayUI.js"></script>
		<script type="text/javascript" src="/HIASCDI/Classes/HIASCDI.js"></script>
		<script type="text/javascript" src="/AI/Classes/AiAgents.js"></script>

		<script type="text/javascript" src="/vendors/bower_components/echarts/dist/echarts-en.min.js"></script>
		<script type="text/javascript" src="/vendors/echarts-liquidfill.min.js"></script>

		<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?=$HIAS->helpers->oDecrypt($HIAS->confs["gmaps"]); ?>&callback=initMap"></script>
		<script>

			function initMap() {

				var latlng = new google.maps.LatLng("<?=floatval($agent["location"]["value"]["coordinates"][0]); ?>", "<?=floatval($agent["location"]["value"]["coordinates"][1]); ?>");
				var map = new google.maps.Map(document.getElementById('map1'), {
					zoom: 10,
					center: latlng
				});

				var loc = new google.maps.LatLng(<?=floatval($agent["location"]["value"]["coordinates"][0]); ?>, <?=floatval($agent["location"]["value"]["coordinates"][1]); ?>);
				var marker = new google.maps.Marker({
					position: loc,
					map: map,
					title: 'agent '
				});
			}

			var eChart_1 = echarts.init(document.getElementById('e_chart_1'));

			var option = {
				tooltip: {
					trigger: 'axis',
					backgroundColor: 'rgba(33,33,33,1)',
					borderRadius: 0,
					padding: 10,
					axisPointer: {
						type: 'cross',
						label: {
							backgroundColor: 'rgba(33,33,33,1)'
						}
					},
					textStyle: {
						color: '#fff',
						fontStyle: 'normal',
						fontWeight: 'normal',
						fontFamily: "'Montserrat', sans-serif",
						fontSize: 12
					}
				},
				xAxis: {
					type: 'category',
					name: 'Time',
					nameLocation: 'middle',
					nameGap: 50,
					axisLabel: {
						textStyle: {
							color: '#fff',
							fontStyle: 'normal',
							fontWeight: 'normal',
							fontFamily: "'Montserrat', sans-serif",
							fontSize: 12
						},
						interval: 1,
						rotate: 45
					},
					data: <?=json_encode($dates); ?>
				},
				yAxis: {
					axisLabel: {
						textStyle: {
							color: '#fff',
							fontStyle: 'normal',
							fontWeight: 'normal',
							fontFamily: "'Montserrat', sans-serif",
							fontSize: 12
						}
					},
					type: 'value',
					name: 'Y-Axis',
					nameLocation: 'center',
					nameGap: 50
				},
				grid: {
					top: 10,
					left: 0,
					right: 0,
					bottom: 100,
					containLabel: true
				},
				series: <?=json_encode($points); ?>
			};
			eChart_1.setOption(option);
			eChart_1.resize();

			AiAgents.LifeInterval = setInterval(function() {
				AiAgents.updateAgentLifeGraph();
			}, 1000);
		</script>

	</body>

</html>
