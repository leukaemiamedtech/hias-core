<?php session_start();

$pageDetails = [
	"PageID" => "Robotics",
	"SubPageID" => "EMAR"
];

include dirname(__FILE__) . '/../../../Classes/Core/init.php';
include dirname(__FILE__) . '/../../../Classes/Core/GeniSys.php';
include dirname(__FILE__) . '/../../Robotics/EMAR/Classes/EMAR.php';
include dirname(__FILE__) . '/../../iotJumpWay/Classes/iotJumpWay.php';

$_GeniSysAi->checkSession();

$Locations = $iotJumpWay->getLocations();
$Zones = $iotJumpWay->getZones();
$Devices = $iotJumpWay->getDevices();

$TId = filter_input(INPUT_GET, 'emar', FILTER_SANITIZE_NUMBER_INT);
$TDevice = $EMAR->getDevice($TId);

list($dev1On, $dev1Off) = $EMAR->getStatusShow($TDevice["status"]);

list($lat, $lng) = $EMAR->getMapMarkers($TDevice);

$lats  = [[
	"lat"=> floatval($lat), 
	"lng" => floatval($lng)
]];

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

	<link href="<?=$domain; ?>/vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
	<link href="<?=$domain; ?>/vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
	<link href="<?=$domain; ?>/vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet" type="text/css">
	<link href="<?=$domain; ?>/dist/css/style.css" rel="stylesheet" type="text/css">
	<link href="<?=$domain; ?>/GeniSysAI/Media/CSS/GeniSys.css" rel="stylesheet" type="text/css">
	<link href="<?=$domain; ?>/vendors/bower_components/fullcalendar/dist/fullcalendar.css" rel="stylesheet" type="text/css" />

	<style>
	  /* Always set the map height explicitly to define the size of the div
	   * element that contains the map. */
	  .map {
		height: 100%;
	  }
	  /* Optional: Makes the sample page fill the window. */
	  html, body {
		height: 100%;
		margin: 0;
		padding: 0;
	  }
	</style>
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
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="panel panel-default card-view">
							<div class="panel-wrapper collapse in">
								<div class="panel-body">

									<div class="row">
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
											<div class="row <?=$dev1Off; ?>" id="cam2">
												<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
													<img src="<?=$domain; ?>/Robotics/EMAR/Media/Images/EMAR-Offline.png" style="width: 100%;" />
												</div>
												<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
													<img src="<?=$domain; ?>/Robotics/EMAR/Media/Images/EMAR-Offline.png" style="width: 100%;" />
												</div>
											</div>
											<img src="<?=$domain; ?>/Robotics/EMAR/<?=$_GeniSys->_helpers->oDecrypt($TDevice["sdir"]); ?>/<?=$_GeniSys->_helpers->oDecrypt($TDevice["sportf"]); ?>" id="cam2on" class="<?=$dev1On; ?>" style="width: 100%;" onerror="EMAR.imgError('cam2');" />
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<?php include dirname(__FILE__) . '/../../Robotics/EMAR/Includes/EMAR.php'; ?>
				
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									  <div id="map1" class="map" style="height: 500px;"></div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">EMAR Robotic Unit #<?=$TId; ?></h6>
								</div>
								<div class="pull-right">
									
									<span id="offline1" style="color: #33F9FF !important;" class="<?=$dev1On; ?>"><i class="fas fa-power-off" style="color: #33F9FF !important;"></i> Online</span> <span id="online1" class="<?=$dev1Off; ?>" style="color: #99A3A4 !important;"><i class="fas fa-power-off" style="color: #99A3A4 !important;"></i> Offline</span> &nbsp;&nbsp;
									
									<i class="fa fa-microchip data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="ecpuU"><?=$TDevice["cpu"]; ?></span>% &nbsp;&nbsp;
									<i class="zmdi zmdi-memory data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="ememU"><?=$TDevice["mem"]; ?></span>% &nbsp;&nbsp;
									<i class="far fa-hdd data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="ehddU"><?=$TDevice["hdd"]; ?></span>% &nbsp;&nbsp;
									<i class="fa fa-thermometer-quarter data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="etempU"><?=$TDevice["tempr"]; ?></span>°C 
							
								</div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="form-wrap">
										<form data-toggle="validator" role="form" id="emar_update">
											<div class="row">
												<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">

													<h4>Device</h4><br />
													
													<div class="form-group">
														<label class="control-label mb-10">MQTT Username</label>
														<div class="form-control hiderm" id="mqttu" style="background: #333;" readonly><?=$_GeniSys->_helpers->oDecrypt($TDevice["mqttu"]); ?></div>
														<span class="help-block">EMAR iotJumpWay MQTT username</span>
													</div>
													<div class="pull-right"><a href="javascipt:void(0)" class="reset_mqtt" id="1"><i class="fa fa-refresh"></i> Reset MQTT Password</a></div>
													<div class="form-group">
														<label class="control-label mb-10">MQTT Password</label>
														<div class="form-control hiderm" id="mqttp" style="background: #333;" readonly><?=$_GeniSys->_helpers->oDecrypt($TDevice["mqttp"]); ?></div>
														<span class="help-block">EMAR iotJumpWay MQTT password</span>
													</div>
													<div class="form-group">
														<label class="control-label mb-10">Location</label>
														<select class="form-control" id="lid" name="lid" required>
															<option value="">PLEASE SELECT</option>

															<?php 
																if(count($Locations)):
																	foreach($Locations as $key => $value):
															?>

															<option value="<?=$value["id"]; ?>" <?=$value["id"]==$TDevice["lid"] ? " selected " : ""; ?>>#<?=$value["id"]; ?>: <?=$value["name"]; ?></option>

															<?php 
																	endforeach;
																endif;
															?>

														</select>
														<span class="help-block">EMAR iotJumpWay Location</span>
													</div>
													<div class="form-group">
														<label class="control-label mb-10">Zone</label>
														<select class="form-control" id="zid" name="zid" required>
															<option value="">PLEASE SELECT</option>
															
															<?php 
																if(count($Zones)):
																	foreach($Zones as $key => $value):
															?>

															<option value="<?=$value["id"]; ?>" <?=$value["id"]==$TDevice["zid"] ? " selected " : ""; ?>>#<?=$value["id"]; ?>:
																<?=$value["zn"]; ?></option>

															<?php 
																	endforeach;
																endif;
															?>

														</select>
														<span class="help-block">EMAR iotJumpWay Zone</span>
													</div>
													<div class="form-group">
														<label class="control-label mb-10">Device</label>
														<select class="form-control" id="did" name="did" required>
															<option value="">PLEASE SELECT</option>
															<?php 
																if(count($Devices)):
																	foreach($Devices as $key => $value):
															?>

															<option value="<?=$value["id"]; ?>" <?=$TDevice["did"]==$value["id"] ? " selected " : ""; ?>>#<?=$value["id"]; ?>: <?=$value["name"]; ?></option>

															<?php 
																	endforeach;
																endif;
															?>

														</select>
														<span class="help-block">EMAR iotJumpWay Device</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Device Name</label>
														<input type="text" class="form-control" id="name" name="name"
															placeholder="EMAR Device Name" required
															value="<?=$TDevice["name"]; ?>">
														<span class="help-block">EMAR iotJumpWay Device Name</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">IP</label>
														<input type="text" class="form-control hider" id="ip" name="ip"
															placeholder="EMAR Device IP" required
															value="<?=$TDevice["ip"] ? $_GeniSys->_helpers->oDecrypt($TDevice["ip"]) : ""; ?>">
														<span class="help-block">EMAR iotJumpWay Device IP</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">MAC Address</label>
														<input type="text" class="form-control hider" id="mac" name="mac"
															placeholder="EMAR Device MAC" required
															value="<?=$TDevice["mac"] ? $_GeniSys->_helpers->oDecrypt($TDevice["mac"]) : ""; ?>">
														<span class="help-block">EMAR iotJumpWay Device MAC</span>
													</div>
													<div class="form-group mb-0">
														<input type="hidden" class="form-control" id="update_emar" name="update_emar" required value="1">
														<input type="hidden" class="form-control" id="id" name="id" required value="<?=$TDevice["id"]; ?>">
														<button type="submit" class="btn btn-success btn-anim"><i class="icon-rocket"></i><span class="btn-text">Update</span></button>
													</div>
												</div>
												<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">

													<h4>Real-Time Object Detection & Depth</h4><br />

													<div class="form-group">
														<label for="name" class="control-label mb-10">Stream Port</label>
														<input type="text" class="form-control hider" id="sport" name="sport"
															placeholder="EMAR Device Stream Port" required
															value="<?=$TDevice["sport"] ? $_GeniSys->_helpers->oDecrypt($TDevice["sport"]) : ""; ?>">
														<span class="help-block">Stream port of EMAR live stream</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Stream Directory</label>
														<input type="text" class="form-control hider" id="sdir" name="sdir"
															placeholder="EMAR Device Stream Directory" 
															value="<?=$TDevice["sdir"] ? $_GeniSys->_helpers->oDecrypt($TDevice["sdir"]) : ""; ?>" required>
														<span class="help-block">Stream directory of EMAR live stream</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Stream File</label>
														<input type="text" class="form-control hider" id="sportf" name="sportf"
															placeholder="EMAR Device Stream File" required
															value="<?=$TDevice["sportf"] ? $_GeniSys->_helpers->oDecrypt($TDevice["sportf"]) : ""; ?>">
														<span class="help-block">Stream file of EMAR live stream</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Socket Port</label>
														<input type="text" class="form-control hider" id="sckport" name="sckport"
															placeholder="EMAR Device Socket Port" required
															value="<?=$TDevice["sckport"] ? $_GeniSys->_helpers->oDecrypt($TDevice["sckport"]) : ""; ?>">
														<span class="help-block">Socket port of EMAR live stream</span>
													</div>

												</div>
											</div>
										</form>
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
		
		<script type="text/javascript" src="<?=$domain; ?>/Robotics/EMAR/Classes/EMAR.js"></script>

		<script type="text/javascript">

			EMAR.HideInputs();
			EMAR.UpdateLife();

			var locations =  <?php echo json_encode( $lats ); ?>;
			function initMap() {

				var latlng = new google.maps.LatLng("<?=floatval($lat); ?>", "<?=floatval($lng); ?>");
				var map = new google.maps.Map(document.getElementById('map1'), {
					zoom: 10,
					center: latlng
				});

				for (var j = 0; j < locations.length; j++) {
					var loc = new google.maps.LatLng(locations[j]["lat"], locations[j]["lng"]);
					var marker = new google.maps.Marker({
						position: loc,
						map: map,
						title: 'Device ' + (j + 1) 
					});
				}
			}
		</script>
		<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?=$_GeniSys->_helpers->oDecrypt($_GeniSys->_confs["gmaps"]); ?>&callback=initMap"></script>


</body>
</html>