<?php session_start();

$pageDetails = [
	"PageID" => "HIS",
	"SubPageID" => "Beds"
];

include dirname(__FILE__) . '/../../../Classes/Core/init.php';
include dirname(__FILE__) . '/../../../Classes/Core/GeniSys.php';
include dirname(__FILE__) . '/../../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../../Hospital/Beds/Classes/Beds.php';

$_GeniSysAi->checkSession();

$Locations = $iotJumpWay->getLocations(0, "id ASC");
$Zones = $iotJumpWay->getZones(0, "id ASC");
$Devices = $iotJumpWay->getDevices(0, "id ASC");
$Applications = $iotJumpWay->getApplications(0, "id ASC");

$BId = filter_input(INPUT_GET, 'bed', FILTER_SANITIZE_NUMBER_INT);
$Bed = $Beds->getBed($BId);

list($lat, $lng) = $Beds->getMapMarkers($Bed);
list($on, $off) = $Beds->getStatusShow($Bed["status"]);

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

	<link href="<?=$domain; ?>/vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet"
		type="text/css" />
	<link href="<?=$domain; ?>/vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet"
		type="text/css" />
	<link href="<?=$domain; ?>/vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet"
		type="text/css">
	<link href="<?=$domain; ?>/dist/css/style.css" rel="stylesheet" type="text/css">
	<link href="<?=$domain; ?>/GeniSysAI/Media/CSS/GeniSys.css" rel="stylesheet" type="text/css">
	<link href="<?=$domain; ?>/vendors/bower_components/fullcalendar/dist/fullcalendar.css" rel="stylesheet"
		type="text/css" />
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
									<h6 class="panel-title txt-dark"><i class="fa fa-bed"></i> Bed #<?=$BId; ?></h6>
								</div>
								<div class="pull-right"></div>
								<div class="clearfix"></div>
								<hr class="light-grey-hr" />
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="form-wrap">
										<form data-toggle="validator" role="form" id="bed_update">
											<div class="row">
												<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
													<div class="form-group">
														<label class="control-label mb-10">iotJumpWay Location</label>
														<select class="form-control" id="lid" name="lid" required>
															<option value="">PLEASE SELECT</option>

															<?php
																if(count($Locations)):
																	foreach($Locations as $key => $value):
															?>

															<option value="<?=$value["id"]; ?>"
																<?=$Bed["lid"] == $value["id"] ? " selected " : ""; ?>>
																#<?=$value["id"]; ?>: <?=$value["name"]; ?></option>

															<?php
																	endforeach;
																endif;
															?>

														</select>
														<span class="help-block">iotJumpWay Location of bed</span>
													</div>
													<div class="form-group">
														<label class="control-label mb-10">iotJumpWay Zone</label>
														<select class="form-control" id="zid" name="zid" required>
															<option value="">PLEASE SELECT</option>

															<?php
																if(count($Zones)):
																	foreach($Zones as $key => $value):
															?>

															<option value="<?=$value["id"]; ?>"
																<?=$Bed["zid"] == $value["id"] ? " selected " : ""; ?>>
																#<?=$value["id"]; ?>: <?=$value["zn"]; ?></option>

															<?php
																	endforeach;
																endif;
															?>

														</select>


														<span class="help-block"> Zone of bed</span>
													</div>
													<div class="form-group">
														<label class="control-label mb-10">iotJumpWay Device</label>
														<select class="form-control" id="did" name="did" required>
															<option value="">PLEASE SELECT</option>

															<?php
																if(count($Devices)):
																	foreach($Devices as $key => $value):
															?>

															<option value="<?=$value["id"]; ?>"
																<?=$Bed["did"] == $value["id"] ? " selected " : ""; ?>>
																#<?=$value["id"]; ?>: <?=$value["name"]; ?></option>

															<?php
																	endforeach;
																endif;
															?>

														</select>
														<span class="help-block"> iotJumpWay device</span>
													</div>
													<div class="form-group mb-0">
														<input type="hidden" class="form-control" id="update_bed" name="update_bed" required value="1">
														<input type="hidden" class="form-control" id="identifier" name="identifier" required value="<?=$Bed["apub"]; ?>">
														<input type="hidden" class="form-control" id="status" name="status" required value="<?=$Bed["status"]; ?>">
														<input type="hidden" class="form-control" id="name" name="name" required value="<?=$Bed["name"]; ?>">
														<input type="hidden" class="form-control" id="id" name="id" required value="<?=$Bed["id"]; ?>">
														<button type="submit" class="btn btn-success btn-anim" id="bed_update"><i class="icon-rocket"></i><span class="btn-text">submit</span></button>
													</div>
												</div>
												<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
													<div class="form-group">
														<label for="name" class="control-label mb-10">IP</label>
														<input type="text" class="form-control hider" id="ip" name="ip" placeholder="TASS Device IP" required value="<?=$Bed["ip"] ? $_GeniSys->_helpers->oDecrypt($Bed["ip"]) : ""; ?>">
														<span class="help-block"> IP of bed</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">MAC</label>
														<input type="text" class="form-control hider" id="mac" name="mac" placeholder="TASS Device MAC" required value="<?=$Bed["mac"] ? $_GeniSys->_helpers->oDecrypt($Bed["mac"]) : ""; ?>">
														<span class="help-block"> MAC of bed</span>
													</div>
													<div class="clearfix"></div>
												</div>
											</div>
											<hr class="light-grey-hr" />
										</form>
									</div>
								</div>
							</div>
						</div><br />
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">Bed History</h6>
								</div>
								<div class="pull-right"><a href="<?=$domain; ?>/Hospital/Beds/<?=$Bed["id"]; ?>/History"><i class="fa fa-eye pull-left"></i> View All Bed History</a></div>
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
													$history = $Beds->retrieveHistory($Bed["id"], 5);
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
															<a href="<?=$domain; ?>/Hospital/Beds/<?=$Bed["id"]; ?>/Transaction/<?=$value["hash"];?>">#<?=$value["hash"];?></a>
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
									<h6 class="panel-title txt-dark">Bed Transactions</h6>
								</div>
								<div class="pull-right"><a href="<?=$domain; ?>/Hospital/Beds/<?=$Bed["id"]; ?>/Transactions"><i class="fa fa-eye pull-left"></i> View All Bed Transactions</a></div>
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
													$transactions = $Beds->retrieveTransactions($Bed["id"], 5);
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
													<td><a href="<?=$domain; ?>/Hospital/Beds/<?=$Bed["id"]; ?>/Transaction/<?=$value["id"];?>">#<?=$value["id"];?></a></td>
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
					<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">Bed Device #<?=$Bed["did"]; ?></h6>
								</div>
								<div class="pull-right"><span id="offline3" style="color: #33F9FF !important;" class="<?=$on; ?>"><i class="fas fa-power-off" style="color: #33F9FF !important;"></i> Online</span> <span id="online3" class="<?=$off; ?>" style="color: #99A3A4 !important;"><i class="fas fa-power-off" style="color: #99A3A4 !important;"></i> Offline</span></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<i class="fa fa-microchip data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="bedcpu"><?=$Bed["cpu"]; ?></span>% &nbsp;&nbsp;
									<i class="zmdi zmdi-memory data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="bedmem"><?=$Bed["mem"]; ?></span>% &nbsp;&nbsp;
									<i class="far fa-hdd data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="bedhdd"><?=$Bed["hdd"]; ?></span>% &nbsp;&nbsp;
									<i class="fa fa-thermometer-quarter data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="bedtempr"><?=$Bed["tempr"]; ?></span>°C
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
									<div class="pull-right"><a href="javascipt:void(0)" id="reset_bd_apriv"><i class="fa fa-refresh"></i> Reset API Key</a></div>
									<div class="form-group">
										<label class="control-label col-md-5">Identifier</label>
										<div class="col-md-9">
											<p class="form-control-static" id="idappid"><?=$Bed["apub"]; ?></p>
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
											<p class="form-control-static" id="bcid"><?=$Bed["bcaddress"]; ?></p>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="pull-right"><a href="javascipt:void(0)" id="reset_bed_mqtt"><i
												class="fa fa-refresh"></i> Reset MQTT Password</a></div>
									<div class="form-group">
										<label class="control-label col-md-5">MQTT Username</label>
										<div class="col-md-9">
											<p class="form-control-static" id="bedmqttu"><?=$_GeniSys->_helpers->oDecrypt($Bed["mqttu"]); ?></p>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-5">MQTT Password</label>
										<div class="col-md-9">
											<p class="form-control-static"><span id="bedmqttp"><?=$_GeniSys->_helpers->oDecrypt($Bed["mqttp"]); ?></span>
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

		<script type="text/javascript" src="<?=$domain; ?>/Hospital/Beds/Classes/Beds.js"></script>

		<script type="text/javascript">

			$(document).ready(function() {
				Beds.HideInputs();
			});

			function initMap() {

				var latlng = new google.maps.LatLng("<?=floatval($lat); ?>", "<?=floatval($lng); ?>");
				var map = new google.maps.Map(document.getElementById('map1'), {
					zoom: 10,
					center: latlng
				});

				var marker = new google.maps.Marker({
					position: latlng,
					map: map,
					title: 'Approximate location'
				});
			}

		</script>
		<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?=$_GeniSys->_helpers->oDecrypt($_GeniSys->_confs["gmaps"]); ?>&callback=initMap"></script>

	</body>
</html>