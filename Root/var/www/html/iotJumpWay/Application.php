<?php session_start();

$pageDetails = [
	"PageID" => "IoT",
	"SubPageID" => "IoT",
	"LowPageID" => "Application"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../../Classes/Core/GeniSys.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';

$_GeniSysAi->checkSession();

$Locations = $iotJumpWay->getLocations();

$AId = filter_input(INPUT_GET, 'application', FILTER_SANITIZE_NUMBER_INT);
$Application = $iotJumpWay->getApplication($AId);

list($appOn, $appOff) = $iotJumpWay->getStatusShow($Application["status"]);

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
		<link href="<?=$domain; ?>/GeniSysAI/Media/CSS/GeniSys.css" rel="stylesheet" type="text/css">
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
									<h6 class="panel-title txt-dark">Application #<?=$AId; ?></h6>
								</div>
								<div class="pull-right"></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="form-wrap">
										<form data-toggle="validator" role="form" id="application_update">
											<hr class="light-grey-hr"/>
											<div class="row">
												<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
													<div class="form-group">
														<label for="name" class="control-label mb-10">Name</label>
														<input type="text" class="form-control" id="name" name="name" placeholder="Application Name" required value="<?=$Application["name"]; ?>">
														<span class="help-block"> Name of application</span>
													</div>
													<div class="form-group">
														<label class="control-label mb-10">Location</label>
														<select class="form-control" id="lid" name="lid">

															<?php
																if(count($Locations)):
																	foreach($Locations as $key => $value):
															?>

															<option value="<?=$value["id"]; ?>" <?=$Application["lid"] == $value["id"] ? " selected " : ""; ?>>#<?=$value["id"]; ?>: <?=$value["name"]; ?></option>

															<?php
																	endforeach;
																endif;
															?>

														</select>
														<span class="help-block"> Location of application</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Is Admin:</label>
														<input type="checkbox" class="" id="admin" name="admin" value="1" <?=$Application["admin"] ? " checked " : ""; ?>>
														<span class="help-block"> Is application an admin?</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Is Cancelled:</label>
														<input type="checkbox" class="" id="cancelled" name="cancelled" value="1" <?=$Application["cancelled"] ? " checked " : ""; ?>>
														<span class="help-block"> Is application cancelled?</span>
													</div>
													<div class="form-group mb-0">
														<input type="hidden" class="form-control" id="update_application" name="update_application" required value="1">
														<input type="hidden" class="form-control" id="status" name="status" required value="<?=$Application["status"]; ?>">
														<input type="hidden" class="form-control" id="identifier" name="identifier" required value="<?=$Application["apub"]; ?>">
														<input type="hidden" class="form-control" id="id" name="id" required value="<?=$Application["id"]; ?>">
														<button type="submit" class="btn btn-success btn-anim" id="application_update"><i class="icon-rocket"></i><span class="btn-text">submit</span></button>
													</div>
												</div>
												<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
													<div class="form-group">
														<label for="name" class="control-label mb-10">IP</label>
														<input type="text" class="form-control hider" id="ip" name="ip" placeholder="Application IP" required value="<?=$Application["ip"] ? $_GeniSys->_helpers->oDecrypt($Application["ip"]) : ""; ?>">
														<span class="help-block"> IP of application</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">MAC</label>
														<input type="text" class="form-control hider" id="mac" name="mac" placeholder="Application MAC" required value="<?=$Application["mac"] ? $_GeniSys->_helpers->oDecrypt($Application["mac"]) : ""; ?>">
														<span class="help-block"> MAC of application</span>
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
									<h6 class="panel-title txt-dark">Application History</h6>
								</div>
								<div class="pull-right"><a href="<?=$domain; ?>/iotJumpWay/<?=$Application["lid"]; ?>/Applications/<?=$Application["id"]; ?>/History"><i class="fa fa-eye pull-left"></i> View All Application History</a></div>
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
													$history = $iotJumpWay->retrieveApplicationHistory($Application["id"], 5);
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
															<a href="<?=$domain; ?>/iotJumpWay/<?=$Application["lid"]; ?>/Applications/<?=$Application["id"]; ?>/Transaction/<?=$value["hash"];?>">#<?=$value["hash"];?></a>
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
									<h6 class="panel-title txt-dark">Application Transactions</h6>
								</div>
								<div class="pull-right"><a href="<?=$domain; ?>/iotJumpWay/<?=$Application["lid"]; ?>/Applications/<?=$Application["id"]; ?>/Transactions"><i class="fa fa-eye pull-left"></i> View All Application Transactions</a></div>
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
													$transactions = $iotJumpWay->retrieveApplicationTransactions($Application["id"], 5);
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
													<td><a href="<?=$domain; ?>/iotJumpWay/<?=$Application["lid"]; ?>/Applications/<?=$Application["id"]; ?>/Transaction/<?=$value["id"];?>">#<?=$value["id"];?></a></td>
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
									<h6 class="panel-title txt-dark">Application iotJumpWay Statuses</h6>
								</div>
								<div class="pull-right"><a href="<?=$domain; ?>/iotJumpWay/<?=$Application["lid"]; ?>/Applications/<?=$Application["id"]; ?>/Statuses"><i class="fa fa-eye pull-left"></i> View All Application Status Data</a></div>
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
													$Statuses = $iotJumpWay->retrieveApplicationStatuses($Application["id"], 5);
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
									<h6 class="panel-title txt-dark">Application iotJumpWay Life</h6>
								</div>
								<div class="pull-right"><a href="<?=$domain; ?>/iotJumpWay/<?=$Application["lid"]; ?>/Applications/<?=$Application["id"]; ?>/Life"><i class="fa fa-eye pull-left"></i> View All Application Life Data</a></div>
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
													$Statuses = $iotJumpWay->retrieveApplicationLife($Application["id"], 5);
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
									<h6 class="panel-title txt-dark">Application iotJumpWay Sensors</h6>
								</div>
								<div class="pull-right"><a href="<?=$domain; ?>/iotJumpWay/<?=$Application["lid"]; ?>/Applications/<?=$Application["id"]; ?>/Sensors"><i class="fa fa-eye pull-left"></i> View All Application Sensors Data</a></div>
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
													$Statuses = $iotJumpWay->retrieveApplicationSensors($Application["id"], 5);
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
								<div class="pull-right"><a href="<?=$domain; ?>/iotJumpWay/<?=$Application["lid"]; ?>/Applications/<?=$Application["id"]; ?>/Commands"><i class="fa fa-eye pull-left"></i> View All Application Commands Data</a></div>
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
													$Statuses = $iotJumpWay->retrieveApplicationCommands($Application["id"], 5);
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
								<div class="pull-right"><span id="offline1" style="color: #33F9FF !important;" class="<?=$appOn; ?>"><i class="fas fa-power-off" style="color: #33F9FF !important;"></i> Online</span> <span id="online1" class="<?=$appOff; ?>" style="color: #99A3A4 !important;"><i class="fas fa-power-off" style="color: #99A3A4 !important;"></i> Offline</span></div>
									<div class="form-group">
										<label class="control-label col-md-5">Status</label>
										<div class="col-md-9">
											<i class="fa fa-microchip data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idecpuU"><?=$Application["cpu"]; ?></span>% &nbsp;&nbsp;
											<i class="zmdi zmdi-memory data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idememU"><?=$Application["mem"]; ?></span>% &nbsp;&nbsp;
											<i class="far fa-hdd data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idehddU"><?=$Application["hdd"]; ?></span>% &nbsp;&nbsp;
											<i class="fa fa-thermometer-quarter data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idetempU"><?=$Application["tempr"]; ?></span>°C
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
									<div class="pull-right"><a href="javascipt:void(0)" id="reset_app_apriv"><i class="fa fa-refresh"></i> Reset API Key</a></div>
									<div class="form-group">
										<label class="control-label col-md-5">Identifier</label>
										<div class="col-md-9">
											<p class="form-control-static" id="appid"><?=$Application["apub"]; ?></p>
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
											<p class="form-control-static" id="bcid"><?=$Application["bcaddress"]; ?></p>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="pull-right"><a href="javascipt:void(0)" id="reset_app_mqtt"><i class="fa fa-refresh"></i> Reset MQTT Password</a></div>
									<div class="form-group">
										<label class="control-label col-md-5">MQTT Username</label>
										<div class="col-md-9">
											<p class="form-control-static " id="amqttu"><?=$_GeniSys->_helpers->oDecrypt($Application["mqttu"]); ?></p>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-5">MQTT Password</label>
										<div class="col-md-9">
											<p class="form-control-static"><span id="amqttp"><?=$_GeniSys->_helpers->oDecrypt($Application["mqttp"]); ?></span></p>
										</div>
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

		<?php
			if($Application["lt"] == ""):
				$coords = "41.54329,2.10942";
			else:
				$coords = $Application["lt"] . "," . $Application["lg"];
			endif;

		?>

		<script type="text/javascript" src="<?=$domain; ?>/vendors/bower_components/moment/min/moment.min.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/dist/js/dropdown-bootstrap-extended.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/dist/js/init.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/mqttws31.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/iotJumpWay.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/iotJumpWayUI.js"></script>

		<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?=$_GeniSys->_helpers->oDecrypt($_GeniSys->_confs["gmaps"]); ?>&callback=initMap"></script>
		<script>

			iotJumpwayUI.HideApplicationInputs();
			iotJumpwayUI.StartApplicationLife();

			function initMap() {

				var latlng = new google.maps.LatLng("<?=floatval($Application["lt"]); ?>", "<?=floatval($Application["lg"]); ?>");
				var map = new google.maps.Map(document.getElementById('map1'), {
					zoom: 10,
					center: latlng
				});

				var loc = new google.maps.LatLng(<?=floatval($Application["lt"]); ?>, <?=floatval($Application["lg"]); ?>);
				var marker = new google.maps.Marker({
					position: loc,
					map: map,
					title: 'Device '
				});
			}

		</script>

	</body>

</html>
