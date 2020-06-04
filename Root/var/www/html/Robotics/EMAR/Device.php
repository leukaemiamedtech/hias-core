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
list($dev2On, $dev2Off) = $EMAR->getStatusShow($TDevice["status2"]);
list($dev3On, $dev3Off) = $EMAR->getStatusShow($TDevice["status3"]);

list($lat, $lng, $lat2, $lng2, $lat3, $lng3) = $EMAR->getMapMarkers($TDevice);

$lats  = [[
	"lat"=> floatval($lat), 
	"lng" => floatval($lng)
],[
	"lat"=> floatval($lat2), 
	"lng" => floatval($lng2)
],[
	"lat"=> floatval($lat3), 
	"lng" => floatval($lng3)
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
										<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
											<img src="<?=$domain; ?>/Robotics/EMAR/Media/Images/EMAR-Offline.png" style="width: 100%;" class="<?=$dev2Off; ?>" />
											<img src="<?=$domain; ?>/Robotics/EMAR/<?=$_GeniSys->_helpers->oDecrypt($TDevice["sdir2"]); ?>/<?=$_GeniSys->_helpers->oDecrypt($TDevice["sportf2"]); ?>" class="<?=$dev2On; ?>" style="width: 100%;" />
										</div>
										<div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
											<div class="row <?=$dev3Off; ?>">
												<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
													<img src="<?=$domain; ?>/Robotics/EMAR/Media/Images/EMAR-Offline.png" style="width: 100%;" />
												</div>
												<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
													<img src="<?=$domain; ?>/Robotics/EMAR/Media/Images/EMAR-Offline.png" style="width: 100%;" />
												</div>
											</div>
											<img src="<?=$domain; ?>/Robotics/EMAR/<?=$_GeniSys->_helpers->oDecrypt($TDevice["sdir3"]); ?>/<?=$_GeniSys->_helpers->oDecrypt($TDevice["sportf3"]); ?>" class="<?=$dev3On; ?>" style="width: 100%;" />
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<?php include dirname(__FILE__) . '/../../Robotics/EMAR/Includes/EMAR.php'; ?>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">EMAR Robotic Unit #<?=$TId; ?></h6>
									<h2 class="panel-title txt-dark">Device #1</h2>
								</div>
								<div class="pull-right"><span id="offline1" style="color: #33F9FF !important;" class="<?=$dev1On; ?>"><i class="fas fa-power-off" style="color: #33F9FF !important;"></i> Online</span> <span id="online1" class="<?=$dev1Off; ?>" style="color: #99A3A4 !important;"><i class="fas fa-power-off" style="color: #99A3A4 !important;"></i> Offline</span></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<i class="fa fa-microchip data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="ecpuU"><?=$TDevice["cpu"]; ?></span>% &nbsp;&nbsp;
									<i class="zmdi zmdi-memory data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="ememU"><?=$TDevice["mem"]; ?></span>% &nbsp;&nbsp;
									<i class="far fa-hdd data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="ehddU"><?=$TDevice["hdd"]; ?></span>% &nbsp;&nbsp;
									<i class="fa fa-thermometer-quarter data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="etempU"><?=$TDevice["tempr"]; ?></span>°C 
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">EMAR Robotic Unit #<?=$TId; ?></h6>
									<h2 class="panel-title txt-dark">Device #2</h2>
								</div>
								<div class="pull-right"><span id="offline2" style="color: #33F9FF !important;" class="<?=$dev2On; ?>"><i class="fas fa-power-off" style="color: #33F9FF !important;"></i> Online</span> <span id="online2" class="<?=$dev2Off; ?>" style="color: #99A3A4 !important;"><i class="fas fa-power-off" style="color: #99A3A4 !important;"></i> Offline</span></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<i class="fa fa-microchip data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="ecpuU2"><?=$TDevice["cpu2"]; ?></span>% &nbsp;&nbsp;
									<i class="zmdi zmdi-memory data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="ememU2"><?=$TDevice["mem2"]; ?></span>% &nbsp;&nbsp;
									<i class="far fa-hdd data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="ehddU2"><?=$TDevice["hdd2"]; ?></span>% &nbsp;&nbsp;
									<i class="fa fa-thermometer-quarter data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="etempU2"><?=$TDevice["tempr2"]; ?></span>°C 
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">EMAR Robotic Unit #<?=$TId; ?></h6>
									<h2 class="panel-title txt-dark">Device #3</h2>
								</div>
								<div class="pull-right"><span id="offline3" style="color: #33F9FF !important;" class="<?=$dev3On; ?>"><i class="fas fa-power-off" style="color: #33F9FF !important;"></i> Online</span> <span id="online3" class="<?=$dev3Off; ?>" style="color: #99A3A4 !important;"><i class="fas fa-power-off" style="color: #99A3A4 !important;"></i> Offline</span></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<i class="fa fa-microchip data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="ecpuU3"><?=$TDevice["cpu3"]; ?></span>% &nbsp;&nbsp;
									<i class="zmdi zmdi-memory data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="ememU3"><?=$TDevice["mem3"]; ?></span>% &nbsp;&nbsp;
									<i class="far fa-hdd data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="ehddU3"><?=$TDevice["hdd3"]; ?></span>% &nbsp;&nbsp;
									<i class="fa fa-thermometer-quarter data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="etempU3"><?=$TDevice["tempr3"]; ?></span>°C 
								</div>
							</div>
						</div>
					</div>
				</div>
				
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
								<div class="pull-right"></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="form-wrap">
										<form data-toggle="validator" role="form" id="emar_update">
											<div class="row">
												<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">

													<h2>Device 1</h2>
													<h4>Wheels & LEDs</h4><br />

													<div class="form-group">
														<label class="control-label mb-10">iotJumpWay ID</label>
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
														<span class="help-block"> iotJummpWay Location for EMAR device 1</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Device 1 Name</label>
														<input type="text" class="form-control" id="name" name="name"
															placeholder="EMAR Device Name" required
															value="<?=$TDevice["name"]; ?>">
														<span class="help-block">Name of EMAR device 1</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Device 1 IP</label>
														<input type="text" class="form-control hider" id="ip" name="ip"
															placeholder="EMAR Device IP" required
															value="<?=$TDevice["ip"] ? $_GeniSys->_helpers->oDecrypt($TDevice["ip"]) : ""; ?>">
														<span class="help-block">IP of EMAR device 1</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Device 1 MAC Address</label>
														<input type="text" class="form-control hider" id="mac" name="mac"
															placeholder="EMAR Device MAC" required
															value="<?=$TDevice["mac"] ? $_GeniSys->_helpers->oDecrypt($TDevice["mac"]) : ""; ?>">
														<span class="help-block">MAC Address of EMAR device 1</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Device 1 Stream Port</label>
														<input type="text" class="form-control hider" id="sport" name="sport"
															placeholder="EMAR Device Stream Port" 
															value="<?=$TDevice["sport"] ? $_GeniSys->_helpers->oDecrypt($TDevice["sport"]) : ""; ?>">
														<span class="help-block">Stream port of EMAR device 1 live stream</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Device 1 Stream Directory</label>
														<input type="text" class="form-control hider" id="sdir" name="sdir"
															placeholder="EMAR Device Stream Directory" 
															value="<?=$TDevice["sdir"] ? $_GeniSys->_helpers->oDecrypt($TDevice["sdir"]) : ""; ?>">
														<span class="help-block">Stream directory of EMAR device 1 live stream</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Device 1 Stream File</label>
														<input type="text" class="form-control hider" id="sportf" name="sportf"
															placeholder="EMAR Device Stream File" 
															value="<?=$TDevice["sportf"] ? $_GeniSys->_helpers->oDecrypt($TDevice["sportf"]) : ""; ?>">
														<span class="help-block">Stream file of EMAR device 1 live stream</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Device 1 Socket Port</label>
														<input type="text" class="form-control hider" id="sckport" name="sckport"
															placeholder="EMAR Device Socket Port" 
															value="<?=$TDevice["sckport"] ? $_GeniSys->_helpers->oDecrypt($TDevice["sckport"]) : ""; ?>">
														<span class="help-block">Socket port of EMAR device 1 live stream</span>
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
														<span class="help-block"> Location of EMAR device</span>
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
														<span class="help-block"> Zone of EMAR device</span>
													</div>
													<div class="form-group mb-0">
														<input type="hidden" class="form-control" id="update_emar" name="update_emar" required value="1">
														<input type="hidden" class="form-control" id="id" name="id" required value="<?=$TDevice["id"]; ?>">
														<button type="submit" class="btn btn-success btn-anim"><i class="icon-rocket"></i><span class="btn-text">Update</span></button>
													</div>
												</div>
												<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">

													<h2>Device 2</h2>
													<h4>Real-Time Video Stream & Arm</h4><br />

													<div class="form-group">
														<label class="control-label mb-10">iotJumpWay ID</label>
														<select class="form-control" id="did2" name="did2" required>
															<option value="">PLEASE SELECT</option>
															
															<?php 
																if(count($Devices)):
																	foreach($Devices as $key => $value):
															?>

															<option value="<?=$value["id"]; ?>" <?=$TDevice["did2"]==$value["id"] ? " selected " : ""; ?>>#<?=$value["id"]; ?>: <?=$value["name"]; ?></option>

															<?php 
																	endforeach;
																endif;
															?>

														</select>
														<span class="help-block"> iotJummpWay Location for EMAR device 2</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Device 2 Name</label>
														<input type="text" class="form-control" id="name2" name="name2"
															placeholder="EMAR Device Name" required
															value="<?=$TDevice["name2"]; ?>">
														<span class="help-block">Name of EMAR device 1</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Device 2 IP</label>
														<input type="text" class="form-control hider" id="ip2" name="ip2"
															placeholder="EMAR Device IP" required
															value="<?=$TDevice["ip2"] ? $_GeniSys->_helpers->oDecrypt($TDevice["ip2"]) : ""; ?>">
														<span class="help-block">IP of EMAR device 2</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Device 2 MAC Address</label>
														<input type="text" class="form-control hider" id="mac2" name="mac2"
															placeholder="EMAR Device MAC" required
															value="<?=$TDevice["mac2"] ? $_GeniSys->_helpers->oDecrypt($TDevice["mac2"]) : ""; ?>">
														<span class="help-block">MAC Address of EMAR device 2</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Device 2 Stream Port</label>
														<input type="text" class="form-control hider" id="sport2" name="sport2"
															placeholder="EMAR Device Stream Port" required
															value="<?=$TDevice["sport2"] ? $_GeniSys->_helpers->oDecrypt($TDevice["sport2"]) : ""; ?>">
														<span class="help-block">Stream port of EMAR device 2 live stream</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Device 2 Stream Directory</label>
														<input type="text" class="form-control hider" id="sdir2" name="sdir2"
															placeholder="EMAR Device Stream Directory" 
															value="<?=$TDevice["sdir2"] ? $_GeniSys->_helpers->oDecrypt($TDevice["sdir2"]) : ""; ?>" required>
														<span class="help-block">Stream directory of EMAR device 2 live stream</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Device 2 Stream File</label>
														<input type="text" class="form-control hider" id="sportf2" name="sportf2"
															placeholder="EMAR Device Stream File" required
															value="<?=$TDevice["sportf2"] ? $_GeniSys->_helpers->oDecrypt($TDevice["sportf2"]) : ""; ?>">
														<span class="help-block">Stream file of EMAR device 2 live stream</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Device 2 Socket Port</label>
														<input type="text" class="form-control hider" id="sckport2" name="sckport2"
															placeholder="EMAR Device Socket Port" required
															value="<?=$TDevice["sckport2"] ? $_GeniSys->_helpers->oDecrypt($TDevice["sckport2"]) : ""; ?>">
														<span class="help-block">Socket port of EMAR device 2 live stream</span>
													</div>

												</div>
												<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">

													<h2>Device 3</h2>
													<h4>Real-Time Object Detection & Depth</h4><br />

													<div class="form-group">
														<label class="control-label mb-10">iotJumpWay ID</label>
														<select class="form-control" id="did3" name="did3" required>
                                                            <option value="">PLEASE SELECT</option>
															<?php 
																if(count($Devices)):
																	foreach($Devices as $key => $value):
															?>

															<option value="<?=$value["id"]; ?>" <?=$TDevice["did3"]==$value["id"] ? " selected " : ""; ?>>#<?=$value["id"]; ?>: <?=$value["name"]; ?></option>

															<?php 
																	endforeach;
																endif;
															?>

														</select>
														<span class="help-block"> iotJummpWay Location for EMAR device 3</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Device 3 Name</label>
														<input type="text" class="form-control" id="name3" name="name3"
															placeholder="EMAR Device Name" required
															value="<?=$TDevice["name3"]; ?>">
														<span class="help-block">Name of EMAR device 1</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Device 3 IP</label>
														<input type="text" class="form-control hider" id="ip3" name="ip3"
															placeholder="EMAR Device IP" required
															value="<?=$TDevice["ip3"] ? $_GeniSys->_helpers->oDecrypt($TDevice["ip3"]) : ""; ?>">
														<span class="help-block">IP of EMAR device 3</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Device 3 MAC Address</label>
														<input type="text" class="form-control hider" id="mac3" name="mac3"
															placeholder="EMAR Device MAC" required
															value="<?=$TDevice["mac3"] ? $_GeniSys->_helpers->oDecrypt($TDevice["mac3"]) : ""; ?>">
														<span class="help-block">MAC Address of EMAR device 3</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Device 3 Stream Port</label>
														<input type="text" class="form-control hider" id="sport3" name="sport3"
															placeholder="EMAR Device Stream Port" required
															value="<?=$TDevice["sport3"] ? $_GeniSys->_helpers->oDecrypt($TDevice["sport3"]) : ""; ?>">
														<span class="help-block">Stream port of EMAR device 3 live stream</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Device 3 Stream Directory</label>
														<input type="text" class="form-control hider" id="sdir3" name="sdir3"
															placeholder="EMAR Device Stream Directory" 
															value="<?=$TDevice["sdir3"] ? $_GeniSys->_helpers->oDecrypt($TDevice["sdir3"]) : ""; ?>" required>
														<span class="help-block">Stream directory of EMAR device 3 live stream</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Device 3 Stream File</label>
														<input type="text" class="form-control hider" id="sportf3" name="sportf3"
															placeholder="EMAR Device Stream File" required
															value="<?=$TDevice["sportf3"] ? $_GeniSys->_helpers->oDecrypt($TDevice["sportf3"]) : ""; ?>">
														<span class="help-block">Stream file of EMAR device 3 live stream</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Device 3 Socket Port</label>
														<input type="text" class="form-control hider" id="sckport3" name="sckport3"
															placeholder="EMAR Device Socket Port" required
															value="<?=$TDevice["sckport3"] ? $_GeniSys->_helpers->oDecrypt($TDevice["sckport3"]) : ""; ?>">
														<span class="help-block">Socket port of EMAR device 3 live stream</span>
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
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="pull-right"><a href="javascipt:void(0)" class="reset_mqtt" id="1"><i class="fa fa-refresh"></i> Reset MQTT Password</a></div>
									<div class="form-group">
										<label class="control-label col-md-5">EMAR Device 1<br />MQTT Username</label>
										<div class="col-md-9">
											<p class="form-control-static hiderm" id="mqttu"><?=$_GeniSys->_helpers->oDecrypt($TDevice["mqttu"]); ?></p>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-5">MQTT Password</label>
										<div class="col-md-9">
											<p class="form-control-static hiderm"><span id="mqttp"><?=$_GeniSys->_helpers->oDecrypt($TDevice["mqttp"]); ?></span>
											</p>
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
									<div class="pull-right"><a href="javascipt:void(0)" class="reset_mqtt" id="2"><i class="fa fa-refresh"></i> Reset MQTT Password</a></div>
									<div class="form-group">
										<label class="control-label col-md-5">EMAR Device 2<br />MQTT Username</label>
										<div class="col-md-9">
											<p class="form-control-static hiderm" id="mqttu2"><?=$_GeniSys->_helpers->oDecrypt($TDevice["mqttu2"]); ?></p>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-5">MQTT Password</label>
										<div class="col-md-9">
											<p class="form-control-static hiderm"><span id="mqttp2"><?=$_GeniSys->_helpers->oDecrypt($TDevice["mqttp2"]); ?></span>
											</p>
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
									<div class="pull-right"><a href="javascipt:void(0)"  class="reset_mqtt" id="3"><i class="fa fa-refresh"></i> Reset MQTT Password</a></div>
									<div class="form-group">
										<label class="control-label col-md-5">EMAR Device 3<br />MQTT Username</label>
										<div class="col-md-9">
											<p class="form-control-static hiderm" id="mqttu3"><?=$_GeniSys->_helpers->oDecrypt($TDevice["mqttu3"]); ?></p>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-5">MQTT Password</label>
										<div class="col-md-9">
											<p class="form-control-static hiderm"><span id="mqttp3"><?=$_GeniSys->_helpers->oDecrypt($TDevice["mqttp3"]); ?></span>
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
		
		<script type="text/javascript" src="<?=$domain; ?>/Robotics/EMAR/Classes/EMAR.js"></script>

		<script type="text/javascript">

    		EMAR.HideInputs();
    		EMAR.UpdateLife();

        	var locations =  <?php echo json_encode( $lats ); ?>;
			function initMap() {

        		var latlng = new google.maps.LatLng("<?=floatval($lat2); ?>", "<?=floatval($lng2); ?>");
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