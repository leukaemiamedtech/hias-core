<?php session_start();

$pageDetails = [
    "PageID" => "Security",
    "SubPageID" => "GeniSysAI"
];

include dirname(__FILE__) . '/../../../Classes/Core/init.php';
include dirname(__FILE__) . '/../../../Classes/Core/GeniSys.php';
include dirname(__FILE__) . '/../../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../../Security/GeniSysAI/Classes/GeniSysAI.php';

$_GeniSysAi->checkSession();

$Locations = $iotJumpWay->getLocations();
$Zones = $iotJumpWay->getZones();
$MDevices = $iotJumpWay->getMDevices();
$Devices = $iotJumpWay->getDevices();

$TId = filter_input(INPUT_GET, 'genisysai', FILTER_SANITIZE_NUMBER_INT);
$TDevice = $GeniSysAI->getDevice($TId);

list($dev1On, $dev1Off) = $GeniSysAI->getStatusShow($TDevice["status"]);
list($lat, $lng) = $GeniSysAI->getMapMarkers($TDevice);

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
	 	 	 	 	 	 	 	 	 <h6 class="panel-title txt-dark">GeniSysAI Security Camera Device #<?=$TId; ?></h6>
	 	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 	 	 <div class="pull-right"></div>
	 	 	 	 	 	 	 	 <div class="clearfix"></div>
	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 	 <div class="panel-wrapper collapse in">
	 	 	 	 	 	 	 	 <div class="panel-body">
	 	 	 	 	 	 	 	 	 <div class="form-wrap">
	 	 	 	 	 	 	 	 	 	 <form data-toggle="validator" role="form" id="genisysai_update">
	 	 	 	 	 	 	 	 	 	 	 <div class="row">
	 	 	 	 	 	 	 	 	 	 	 	 <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
	 	 	 	 	 	 	 	 	 	 	 	 	 <div class="form-group">
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <label for="name" class="control-label mb-10">Name</label>
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <input type="text" class="form-control" id="name" name="name" placeholder="GeniSysAI Device Name" required value="<?=$TDevice["name"]; ?>">
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <span class="help-block"> Name of GeniSysAI camera device</span>
	 	 	 	 	 	 	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 	 	 	 	 	 	 	 <div class="form-group">
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <label class="control-label mb-10">Type</label>
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <select class="form-control" id="type" name="type" required>
	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 <option value="">PLEASE SELECT</option>
	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 <option value="API" <?=$TDevice["type"] == "API" ? " selected " : ""; ?>>API</option>
                                                            <option value="Realsense D415" <?=$TDevice["type"] == "Realsense D415" ? " selected " : ""; ?>>Realsense D415</option>
                                                            <option value="Realsense F200" <?=$TDevice["type"] == "Realsense F200" ? " selected " : ""; ?>>Realsense F200</option>
                                                            <option value="Realsense R200" <?=$TDevice["type"] == "Realsense R200" ? " selected " : ""; ?>>Realsense R200</option>
	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 <option value="Foscam Camera" <?=$TDevice["type"] == "Foscam Camera" ? " selected " : ""; ?>>Foscam Camera</option>
	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 <option value="USB Camera" <?=$TDevice["type"] == "USB Camera" ? " selected " : ""; ?>>USB Camera</option>
	 	 	 	 	 	 	 	 	 	 	 	 	 	 </select>
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <span class="help-block"> Location of GeniSysAI camera device</span>
	 	 	 	 	 	 	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 	 	 	 	 	 	 	 <div class="form-group">
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <label class="control-label mb-10">Location</label>
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <select class="form-control" id="lid" name="lid" required>
	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 <option value="">PLEASE SELECT</option>

	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 <?php
	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 if(count($Locations)):
	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 foreach($Locations as $key => $value):
	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 ?>

	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 <option value="<?=$value["id"]; ?>" <?=$TDevice["lid"] == $value["id"] ? " selected " : ""; ?>> #<?=$value["id"]; ?>: <?=$value["name"]; ?></option>

	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 <?php
	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 endforeach;
	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 endif;
	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 ?>

	 	 	 	 	 	 	 	 	 	 	 	 	 	 </select>
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <span class="help-block"> Location of GeniSysAI camera device</span>
	 	 	 	 	 	 	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 	 	 	 	 	 	 	 <div class="form-group">
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <label class="control-label mb-10">Zone</label>
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <select class="form-control" id="zid" name="zid" required>
	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 <option value="">PLEASE SELECT</option>

	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 <?php
	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 if(count($Zones)):
	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 foreach($Zones as $key => $value):
	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 ?>

	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 <option value="<?=$value["id"]; ?>"
	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 <?=$TDevice["zid"] == $value["id"] ? " selected " : ""; ?>>
	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 #<?=$value["id"]; ?>: <?=$value["zn"]; ?></option>

	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 <?php
	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 endforeach;
	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 endif;
	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 ?>

	 	 	 	 	 	 	 	 	 	 	 	 	 	 </select>
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <span class="help-block"> Zone of GeniSysAI camera device</span>
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
	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 <?=$TDevice["did"] == $value["id"] ? " selected " : ""; ?>>#<?=$value["id"]; ?>: <?=$value["name"]; ?></option>

	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 <?php
	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 endforeach;
	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 endif;
	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 ?>

	 	 	 	 	 	 	 	 	 	 	 	 	 	 </select>
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <span class="help-block"> iotJumpWay device</span>
	 	 	 	 	 	 	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 	 	 	 	 	 	 	 <div class="form-group">
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <label for="name" class="control-label mb-10">IP</label>
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <input type="text" class="form-control hider" id="ip" name="ip" placeholder="GeniSysAI Device IP" required value="<?=$TDevice["ip"] ? $_GeniSys->_helpers->oDecrypt($TDevice["ip"]) : ""; ?>">
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <span class="help-block"> IP of GeniSysAI camera device</span>
	 	 	 	 	 	 	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 	 	 	 	 	 	 	 <div class="form-group mb-0">
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <input type="hidden" class="form-control" id="update_genisysai" name="update_genisysai" required value="1">
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <input type="hidden" class="form-control" id="status" name="status" required value="<?=$TDevice["status"]; ?>">
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <input type="hidden" class="form-control" id="identifier" name="identifier" required value="<?=$TDevice["apub"]; ?>">
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <input type="hidden" class="form-control" id="id" name="id" required value="<?=$TDevice["id"]; ?>">
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <input type="hidden" class="form-control" id="did" name="did" required value="<?=$TDevice["did"]; ?>">
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <button type="submit" class="btn btn-success btn-anim" id="genisysai_update"><i class="icon-rocket"></i><span class="btn-text">Submit</span></button>
	 	 	 	 	 	 	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 	 	 	 	 	 	 <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
	 	 	 	 	 	 	 	 	 	 	 	 	 <div class="form-group">
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <label for="name" class="control-label mb-10">MAC</label>
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <input type="text" class="form-control hider" id="mac" name="mac" placeholder="GeniSysAI Device MAC" required value="<?=$TDevice["mac"] ? $_GeniSys->_helpers->oDecrypt($TDevice["mac"]) : ""; ?>">
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <span class="help-block"> MAC of GeniSysAI camera device</span>
	 	 	 	 	 	 	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 	 	 	 	 	 	 	 <div class="form-group">
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <label for="name" class="control-label mb-10">Stream Port</label>
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <input type="text" class="form-control hider" id="sport" name="sport" placeholder="GeniSysAI Device Stream Port" required value="<?=$TDevice["sport"] ? $_GeniSys->_helpers->oDecrypt($TDevice["sport"]) : ""; ?>">
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <span class="help-block"> Stream Port of GeniSysAI camera device</span>
	 	 	 	 	 	 	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 	 	 	 	 	 	 	 <div class="form-group">
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <label for="name" class="control-label mb-10">Stream Directory</label>
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <input type="text" class="form-control hider" id="strdir" name="strdir" placeholder="GeniSysAI Device Stream Directory" required value="<?=$TDevice["strdir"] ? $_GeniSys->_helpers->oDecrypt($TDevice["strdir"]) : ""; ?>">
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <span class="help-block"> Name of GeniSysAI camera stream directory</span>
	 	 	 	 	 	 	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 	 	 	 	 	 	 	 <div class="form-group">
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <label for="name" class="control-label mb-10">Stream File</label>
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <input type="text" class="form-control hider" id="sportf" name="sportf" placeholder="GeniSysAI Device Stream File" required value="<?=$TDevice["sportf"] ? $_GeniSys->_helpers->oDecrypt($TDevice["sportf"]) : ""; ?>">
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <span class="help-block"> Name of GeniSysAI camera stream file (.mjpg)</span>
	 	 	 	 	 	 	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 	 	 	 	 	 	 	 <div class="form-group">
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <label for="name" class="control-label mb-10">Socket Port</label>
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <input type="text" class="form-control hider" id="sckport" name="sckport" placeholder="GeniSysAI Device MAC" required value="<?=$TDevice["sckport"] ? $_GeniSys->_helpers->oDecrypt($TDevice["sckport"]) : ""; ?>">
	 	 	 	 	 	 	 	 	 	 	 	 	 	 <span class="help-block"> Port of GeniSysAI camera socket stream</span>
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
									<h6 class="panel-title txt-dark">Device History</h6>
								</div>
								<div class="pull-right"><a href="<?=$domain; ?>/iotJumpWay/<?=$TDevice["lid"]; ?>/Zones/<?=$TDevice["zid"]; ?>/Devices/<?=$TDevice["did"]; ?>/History"><i class="fa fa-eye pull-left"></i> View All Device History</a></div>
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
													$history = $iotJumpWay->retrieveDeviceHistory($TDevice["did"], 5);
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
															<a href="<?=$domain; ?>/iotJumpWay/<?=$TDevice["lid"]; ?>/Zones/<?=$TDevice["zid"]; ?>/Devices/<?=$TDevice["did"]; ?>/Transaction/<?=$value["hash"];?>">#<?=$value["hash"];?></a>
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
								<div class="pull-right"><a href="<?=$domain; ?>/iotJumpWay/<?=$TDevice["lid"]; ?>/Zones/<?=$TDevice["zid"]; ?>/Devices/<?=$TDevice["did"]; ?>/Transactions"><i class="fa fa-eye pull-left"></i> View All Device Transactions</a></div>
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
													$transactions = $iotJumpWay->retrieveDeviceTransactions($TDevice["did"], 5);
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
													<td><a href="<?=$domain; ?>/iotJumpWay/<?=$TDevice["lid"]; ?>/Zones/<?=$TDevice["zid"]; ?>/Devices/<?=$TDevice["did"]; ?>/Transaction/<?=$value["id"];?>">#<?=$value["id"];?></a></td>
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
								<div class="pull-right"><a href="<?=$domain; ?>/iotJumpWay/<?=$TDevice["lid"]; ?>/Zones/<?=$TDevice["zid"]; ?>/Devices/<?=$TDevice["did"]; ?>Statuses"><i class="fa fa-eye pull-left"></i> View All Device Status Data</a></div>
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
													$Statuses = $iotJumpWay->retrieveDeviceStatuses($TDevice["did"], 5);
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
								<div class="pull-right"><a href="<?=$domain; ?>/iotJumpWay/<?=$TDevice["lid"]; ?>/Zones/<?=$TDevice["zid"]; ?>/Devices/<?=$TDevice["did"]; ?>/Life"><i class="fa fa-eye pull-left"></i> View All Device Life Data</a></div>
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
													$Statuses = $iotJumpWay->retrieveDeviceLife($TDevice["did"], 5);
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
								<div class="pull-right"><a href="<?=$domain; ?>/iotJumpWay/<?=$TDevice["lid"]; ?>/Zones/<?=$TDevice["zid"]; ?>/Devices/<?=$TDevice["did"]; ?>/Sensors"><i class="fa fa-eye pull-left"></i> View All Device Sensors Data</a></div>
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
													$Statuses = $iotJumpWay->retrieveDeviceSensors($TDevice["did"], 5);
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
								<div class="pull-right"><a href="<?=$domain; ?>/iotJumpWay/<?=$TDevice["lid"]; ?>/Zones/<?=$TDevice["zid"]; ?>/Devices/<?=$TDevice["did"]; ?>/Commands"><i class="fa fa-eye pull-left"></i> View All Device Commands Data</a></div>
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
													$Statuses = $iotJumpWay->retrieveDeviceCommands($TDevice["did"], 5);
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
	 	 	 	 	 	 	 	 	 	 <div class="col-md-9">
	 	 	 	 	 	 	 	 	 	 	 <i class="fa fa-microchip data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idecpuU"><?=$TDevice["cpu"]; ?></span>% &nbsp;&nbsp;
	 	 	 	 	 	 	 	 	 	 	 <i class="zmdi zmdi-memory data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idememU"><?=$TDevice["mem"]; ?></span>% &nbsp;&nbsp;
	 	 	 	 	 	 	 	 	 	 	 <i class="far fa-hdd data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idehddU"><?=$TDevice["hdd"]; ?></span>% &nbsp;&nbsp;
	 	 	 	 	 	 	 	 	 	 	 <i class="fa fa-thermometer-quarter data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idetempU"><?=$TDevice["tempr"]; ?></span>°C
	 	 	 	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 </div>
	 	 	 	 	 	 <div class="panel panel-default card-view panel-refresh <?=$TDevice["type"] == "API" ? "hide" : ""; ?>">
	 	 	 	 	 	 	 <div class="panel-wrapper collapse in">
	 	 	 	 	 	 	 	 <div class="panel-body">
	 	 	 	 	 	 	 	 	 <img src="<?=$domain; ?>/Security/GeniSysAI/<?=$_GeniSys->_helpers->oDecrypt($TDevice["strdir"]); ?>/<?=$_GeniSys->_helpers->oDecrypt($TDevice["sportf"]); ?>" style="width: 100%;" />
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
									<div class="pull-right"><a href="javascipt:void(0)" id="reset_apriv"><i class="fa fa-refresh"></i> Reset API Key</a></div>
									<div class="form-group">
										<label class="control-label col-md-5">Identifier</label>
										<div class="col-md-9">
											<p class="form-control-static" id="idappid"><?=$TDevice["apub"]; ?></p>
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
											<p class="form-control-static" id="bcid"><?=$TDevice["bcaddress"]; ?></p>
										</div>
									</div>
								</div>
							</div>
						</div>
	 	 	 	 	 	 <div class="panel panel-default card-view panel-refresh">
	 	 	 	 	 	 	 <div class="panel-wrapper collapse in">
	 	 	 	 	 	 	 	 <div class="panel-body">
	 	 	 	 	 	 	 	 	 <div class="pull-right"><a href="javascipt:void(0)" id="reset_mqtt"><i
	 	 	 	 	 	 	 	 	 	 	 	 class="fa fa-refresh"></i> Reset MQTT Password</a></div>
	 	 	 	 	 	 	 	 	 <div class="form-group">
	 	 	 	 	 	 	 	 	 	 <label class="control-label col-md-5">MQTT Username</label>
	 	 	 	 	 	 	 	 	 	 <div class="col-md-9">
	 	 	 	 	 	 	 	 	 	 	 <p class="form-control-static" id="mqttut"><?=$_GeniSys->_helpers->oDecrypt($TDevice["mqttu"]); ?></p>
	 	 	 	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 	 	 	 </div>
	 	 	 	 	 	 	 	 	 <div class="form-group">
	 	 	 	 	 	 	 	 	 	 <label class="control-label col-md-5">MQTT Password</label>
	 	 	 	 	 	 	 	 	 	 <div class="col-md-9">
	 	 	 	 	 	 	 	 	 	 	 <p class="form-control-static"><span id="mqttpt"><?=$_GeniSys->_helpers->oDecrypt($TDevice["mqttp"]); ?></span>
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
	 	 <script type="text/javascript" src="<?=$domain; ?>/Security/GeniSysAI/Classes/GeniSysAI.js"></script>

		<script type="text/javascript">

	 	 	 $(document).ready(function() {
	 	 	 	 GeniSysAI.HideInputs();
	 	 	 });

			function initMap() {

	 	 		var latlng = new google.maps.LatLng("<?=floatval($lat); ?>", "<?=floatval($lng); ?>");
				var map = new google.maps.Map(document.getElementById('map1'), {
					zoom: 10,
					center: latlng
				});

	 	 	 	 var loc = new google.maps.LatLng(<?=floatval($lat); ?>, <?=floatval($lng); ?>);
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