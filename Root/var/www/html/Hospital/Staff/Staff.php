<?php session_start();

$pageDetails = [
	"PageID" => "HIS",
	"SubPageID" => "Staff"
];

include dirname(__FILE__) . '/../../../Classes/Core/init.php';
include dirname(__FILE__) . '/../../../Classes/Core/GeniSys.php';
include dirname(__FILE__) . '/../../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../../Hospital/Staff/Classes/Staff.php';

$_GeniSysAi->checkSession();

$Locations = $iotJumpWay->getLocations(0, "id ASC");
$Zones = $iotJumpWay->getZones(0, "id ASC");
$Applications = $iotJumpWay->getApplications(0, "id ASC");

$SId = filter_input(INPUT_GET,  'staff', FILTER_SANITIZE_NUMBER_INT);
$Staffer = $Staff->getStaff($SId);
$Application = $iotJumpWay->getApplication($Staffer["context"]["Data"]["aid"]["value"]);

list($on, $off) = $iotJumpWay->getStatusShow($Application["context"]["Data"]["status"]["value"]);

$cancelled = $Staffer["context"]["Data"]["permissions"]["cancelled"] ? True : False;
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
	<link href="<?=$domain; ?>/AI/GeniSysAI/Media/CSS/GeniSys.css" rel="stylesheet" type="text/css">
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
									<h6 class="panel-title txt-dark"><i class="fa fa-users"></i> Hospital Staff #<?=$SId; ?></h6>
								</div>
								<div class="pull-right"><a href="javascipt:void(0)" id="reset_pass"><i class="fa fa-refresh"></i> Reset Password</a></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="form-wrap">
										<form data-toggle="validator" role="form" id="staff_update">
											<hr class="light-grey-hr" />
											<div class="row">
												<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
													<div class="form-group">
														<label for="name" class="control-label mb-10">Name</label>
														<input type="text" class="form-control" id="name" name="name" placeholder="Staff Name" required value="<?=$Staffer["context"]["Data"]["name"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">Name of staff</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Description</label>
														<input type="text" class="form-control" id="description" name="description" placeholder="Device Description" required value="<?=$Staffer["context"]["Data"]["description"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">Staff description</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Username</label>
														<input type="text" class="form-control" id="username" name="username" placeholder="Staff Username" required value="<?=$Staffer["context"]["Data"]["username"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block"> Username of staff</span>
													</div>
													<div class="form-group">
														<label class="control-label mb-10">Category</label>
														<select class="form-control" id="category" name="category" required <?=$cancelled ? " disabled " : ""; ?>>
															<option value="">PLEASE SELECT</option>

															<?php
																$categories = $Staff->getStaffCategories();
																if(count($categories)):
																	foreach($categories as $key => $value):
															?>

															<option value="<?=$value["category"]; ?>" <?=$Staffer["context"]["Data"]["category"]["value"][0]==$value["category"] ? " selected " : ""; ?>><?=$value["category"]; ?></option>

															<?php
																	endforeach;
																endif;
															?>

														</select>
														<span class="help-block">Staff category</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Email *</label>
														<input type="text" class="form-control" id="email" name="email" placeholder="Email of staff member" required value="<?=$Staffer["context"]["Data"]["email"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">Email of staff member</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Address Street Address</label>
														<input type="text" class="form-control" id="streetAddress" name="streetAddress" placeholder="iotJumpWay Location street address" required value="<?=$Staffer["context"]["Data"]["address"]["value"]["streetAddress"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">iotJumpWay Location street address</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Address Locality</label>
														<input type="text" class="form-control" id="addressLocality" name="addressLocality" placeholder="iotJumpWay Location address locality" required value="<?=$Staffer["context"]["Data"]["address"]["value"]["addressLocality"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">iotJumpWay Location address locality</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Address Postal Code</label>
														<input type="text" class="form-control" id="postalCode" name="postalCode" placeholder="iotJumpWay Location postal code" required value="<?=$Staffer["context"]["Data"]["address"]["value"]["postalCode"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">iotJumpWay Location post code</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">NFC UID</label>
														<input type="text" class="form-control" id="nfc" name="nfc" placeholder="NFC UID"  value="<?=$Staffer["context"]["Data"]["nfc"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">UID of staff member's NFC card/fob/implant</span>
													</div>
													<?php if(!$cancelled): ?>
													<div class="form-group mb-0">
														<input type="hidden" class="form-control" id="update_staff" name="update_staff" required value="1">
														<button type="submit" class="btn btn-success btn-anim"><i class="icon-rocket"></i><span class="btn-text">Update Staff</span></button>
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

																<option value="<?=$value["lid"]["value"]; ?>" <?=$Staffer["context"]["Data"]["lid"]["value"]==$value["lid"]["value"] ? " selected " : ""; ?>>#<?=$value["lid"]["value"]; ?>: <?=$value["name"]["value"]; ?></option>

															<?php
																	endforeach;
																endif;
															?>

														</select>
														<span class="help-block"> Location of staff</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Is Admin:</label>
														<input type="checkbox" class="" id="admin" name="admin" value=1 <?=$Staffer["context"]["Data"]["permissions"]["adminAccess"] ? " checked " : ""; ?> <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">Is staff member an admin?</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Has Patient Access:</label>
														<input type="checkbox" class="" id="patients" name="patients" value=1 <?=$Staffer["context"]["Data"]["permissions"]["patientsAccess"] ? " checked " : ""; ?> <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">Is staff member has patients access?</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Is cancelled:</label>
														<input type="checkbox" class="" id="cancelled" name="cancelled" value=1 <?=$Staffer["context"]["Data"]["permissions"]["cancelled"] ? " checked " : ""; ?> <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">Is staff member cancelled?</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Date Created</label>
														<p><?=$Staffer["context"]["Data"]["dateCreated"]["value"]; ?></p>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Date First Used</label>
														<p><?=$Staffer["context"]["Data"]["dateFirstUsed"]["value"] ? $Staffer["context"]["Data"]["dateFirstUsed"]["value"] : "NA"; ?></p>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Date Modified</label>
														<p><?=$Staffer["context"]["Data"]["dateModified"]["value"]; ?></p>
													</div>
													<div class="clearfix"></div>
												</div>
												<hr class="light-grey-hr" />
											</div>
										</form>
									</div>
								</div>
							</div>
						</div><br />
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">User History</h6>
								</div>
								<div class="pull-right"><a href="<?=$domain; ?>/Hospital/Staff/<?=$Staffer["context"]["Data"]["aid"]["value"]; ?>/History"><i class="fa fa-eye pull-left"></i> View All User History</a></div>
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
													$history = $Staff->retrieveHistory($Staffer["context"]["Data"]["uid"]["value"], 5);
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
															<a href="<?=$domain; ?>/iotJumpWay/<?=$Application["context"]["Data"]["lid"]["value"]; ?>/Applications/<?=$Application["context"]["Data"]["aid"]["value"]; ?>/Transaction/<?=$value["hash"];?>">#<?=$value["hash"];?></a>
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
									<h6 class="panel-title txt-dark">User Transactions</h6>
								</div>
								<div class="pull-right"><a href="<?=$domain; ?>/Hospital/Staff/<?=$Staffer["context"]["Data"]["aid"]["value"]; ?>/Transactions"><i class="fa fa-eye pull-left"></i> View All User Transactions</a></div>
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
													<th>Hash</th>
													<th>Time</th>
												  </tr>
												</thead>
												<tbody>

												<?php
													$transactions = $Staff->retrieveTransactions($Staffer["context"]["Data"]["uid"]["value"], 5);
													if(count($transactions)):
														foreach($transactions as $key => $value):
												?>

												  <tr>
													<td>#<?=$value["id"];?></td>
													<td><?=$userDetails;?><?=$value["action"];?></td>
													<td><a href="<?=$domain; ?>/iotJumpWay/<?=$Application["context"]["Data"]["lid"]["value"]; ?>/Applications/<?=$Application["context"]["Data"]["aid"]["value"]; ?>/Transaction/<?=$value["id"];?>">#<?=$value["id"];?></a></td>
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
									<h6 class="panel-title txt-dark">User iotJumpWay Application Statuses</h6>
								</div>
								<div class="pull-right"><a href="<?=$domain; ?>/Hospital/Staff/<?=$Staffer["context"]["Data"]["uid"]["value"]; ?>/Statuses"><i class="fa fa-eye pull-left"></i> View All User Status Data</a></div>
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
													$Statuses = $Staff->retrieveStatuses($Staffer["context"]["Data"]["aid"]["entity"], 5);
													if($Statuses["Response"] == "OK"):
														foreach($Statuses["ResponseData"] as $key => $value):
												?>

												  <tr>
													<td>#<?=$value->_id;?></td>
													<td><strong>Location:</strong> #<?=$value->Location;?></td>
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
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">Staff Application #<?=$Staffer["context"]["Data"]["aid"]["value"]; ?></h6>
								</div>
								<div class="pull-right"><span id="offline3" style="color: #33F9FF !important;" class="<?=$on; ?>"><i class="fas fa-power-off" style="color: #33F9FF !important;"></i> Online</span> <span id="online3" class="<?=$off; ?>" style="color: #99A3A4 !important;"><i class="fas fa-power-off" style="color: #99A3A4 !important;"></i> Offline</span></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<i class="fas fa-battery-full data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idebatU"><?=$Application["context"]["Data"]["batteryLevel"]["value"]; ?></span>% &nbsp;&nbsp;
									<i class="fa fa-microchip data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="usrcpu"><?=$Application["context"]["Data"]["cpuUsage"]["value"]; ?></span>% &nbsp;&nbsp;
									<i class="zmdi zmdi-memory data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="usrmem"><?=$Application["context"]["Data"]["memoryUsage"]["value"]; ?></span>% &nbsp;&nbsp;
									<i class="far fa-hdd data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="userhdd"><?=$Application["context"]["Data"]["hddUsage"]["value"]; ?></span>% &nbsp;&nbsp;
									<i class="fa fa-thermometer-quarter data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="usrtempr"><?=$Application["context"]["Data"]["temperature"]["value"]; ?></span>°C
								</div>
							</div>
						</div>
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<img src="<?=$domain; ?>/Hospital/Staff/Media/Images/Uploads/<?=$Staffer["context"]["Data"]["picture"]["value"];?>" style="width: 100%; !important;" />
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
									<div class="pull-right"><a href="javascipt:void(0)" id="reset_staff_apriv"><i
												class="fa fa-refresh"></i> Reset API Key</a></div>
									<div class="form-group">
										<label class="control-label col-md-5">Identifier</label>
										<div class="col-md-9">
											<p class="form-control-static" id="usrappid"><?=$Application["context"]["Data"]["keys"]["public"]; ?></p>
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
											<p class="form-control-static" id="usrbcid"><?=$Application["context"]["Data"]["blockchain"]["address"]; ?></p>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="pull-right"><a href="javascipt:void(0)" id="reset_staff_mqtt"><i
												class="fa fa-refresh"></i> Reset MQTT Password</a></div>
									<div class="form-group">
										<label class="control-label col-md-5">MQTT Username</label>
										<div class="col-md-9">
											<p class="form-control-static" id="usrmqttu"><?=$_GeniSys->_helpers->oDecrypt($Application["context"]["Data"]["mqtt"]["username"]); ?></p>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-5">MQTT Password</label>
										<div class="col-md-9">
											<p class="form-control-static"><span id="usrmqttp"><?=$_GeniSys->_helpers->oDecrypt($Application["context"]["Data"]["mqtt"]["password"]); ?></span>
											</p>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="pull-right"><a href="javascipt:void(0)" id="reset_user_amqp"><i class="fa fa-refresh"></i> Reset AMQP Password</a></div>
									<div class="form-group">
										<label class="control-label col-md-5">AMQP Username</label>
										<div class="col-md-9">
											<p class="form-control-static" id="appamqpu"><?=$Application["context"]["Data"]["amqp"]["username"] ? $_GeniSys->_helpers->oDecrypt($Application["context"]["Data"]["amqp"]["username"]) : ""; ?></p>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-5">AMQP Password</label>
										<div class="col-md-9">
											<p class="form-control-static"><span id="appamqpp"><?=$Application["context"]["Data"]["amqp"]["password"] ? $_GeniSys->_helpers->oDecrypt($Application["context"]["Data"]["amqp"]["password"]) : ""; ?></span>
											<p><strong>Last Updated:</strong> <?=array_key_exists("timestamp", $Application["context"]["Data"]["amqp"]) ? $Application["context"]["Data"]["amqp"]["timestamp"] : "NA"; ?></p>
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

		<script type="text/javascript" src="<?=$domain; ?>/Hospital/Staff/Classes/Staff.js"></script>
		<script type="text/javascript">

			$(document).ready(function() {
				Staff.HideInputs();
				iotJumpwayUI.StartStaffLife();
			});

			function initMap() {

				var latlng = new google.maps.LatLng("<?=floatval($Application["context"]["Data"]["location"]["value"]["coordinates"][0]); ?>", "<?=floatval($Application["context"]["Data"]["location"]["value"]["coordinates"][1]); ?>");
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