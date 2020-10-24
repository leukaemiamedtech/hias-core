<?php session_start();

$pageDetails = [
    "PageID" => "IoT",
    "SubPageID" => "Location",
    "LowPageID" => "Locations"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../../Classes/Core/GeniSys.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';

$_GeniSysAi->checkSession();

$LId = 1;
$Location = $iotJumpWay->getLocation($LId);
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
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">iotJumpWay Location #<?=$LId; ?></h6>
								</div>
								<div class="pull-right"></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="form-wrap">

										<form data-toggle="validator" role="form" id="location_update">
											<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
												<div class="form-group">
													<label for="name" class="control-label mb-10">Name</label>
													<input type="text" class="form-control" id="name" name="name" placeholder="iotJumpWay Location Name" required value="<?=$Location["context"]["Data"]["name"]["value"]; ?>">
													<span class="help-block"> Name of iotJumpWay Location</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Description</label>
													<input type="text" class="form-control" id="description" name="description" placeholder="iotJumpWay Location description" required value="<?=$Location["context"]["Data"]["description"]["value"]; ?>">
													<span class="help-block"> Description of iotJumpWay Location</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Address Street Address</label>
													<input type="text" class="form-control" id="streetAddress" name="streetAddress" placeholder="iotJumpWay Location street address" required value="<?=$Location["context"]["Data"]["address"]["value"]["streetAddress"]; ?>">
													<span class="help-block">iotJumpWay Location street address</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Address Locality</label>
													<input type="text" class="form-control" id="addressLocality" name="addressLocality" placeholder="iotJumpWay Location address locality" required value="<?=$Location["context"]["Data"]["address"]["value"]["addressLocality"]; ?>">
													<span class="help-block">iotJumpWay Location address locality</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Address Postal Code</label>
													<input type="text" class="form-control" id="postalCode" name="postalCode" placeholder="iotJumpWay Location postal code" required value="<?=$Location["context"]["Data"]["address"]["value"]["postalCode"]; ?>">
													<span class="help-block">iotJumpWay Location post code</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Coordinates</label>
													<input type="text" class="form-control" id="coordinates" name="coordinates" placeholder="iotJumpWay Location coordinates" required value="<?=$Location["context"]["Data"]["location"]["value"]["coordinates"][0]; ?>, <?=$Location["context"]["Data"]["location"]["value"]["coordinates"][1]; ?>">
													<span class="help-block">iotJumpWay Location coordinates</span>
												</div>
											</div>
											<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
												<div class="form-group">
													<label for="name" class="control-label mb-10">Zones</label>
													<input type="text" class="form-control" id="zones" name="zones" placeholder="iotJumpWay Location zones" required value="<?=$Location["context"]["Data"]["zones"]["value"]; ?>">
													<span class="help-block">iotJumpWay Location zones</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Devices</label>
													<input type="text" class="form-control" id="devices" name="devices" placeholder="iotJumpWay Location devices" required value="<?=$Location["context"]["Data"]["devices"]["value"]; ?>">
													<span class="help-block">iotJumpWay Location devices</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Applications</label>
													<input type="text" class="form-control" id="applications" name="applications" placeholder="iotJumpWay Location applications" required value="<?=$Location["context"]["Data"]["applications"]["value"]; ?>">
													<span class="help-block">iotJumpWay Location applications</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Users</label>
													<input type="text" class="form-control" id="users" name="users" placeholder="iotJumpWay Location users" required value="<?=$Location["context"]["Data"]["users"]["value"]; ?>">
													<span class="help-block">iotJumpWay Location users</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Patients</label>
													<input type="text" class="form-control" id="patients" name="patients" placeholder="iotJumpWay Location patients" required value="<?=$Location["context"]["Data"]["patients"]["value"]; ?>">
													<span class="help-block">iotJumpWay Location users</span>
												</div>
											</div>
											<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
												<div class="form-group">
													<label for="name" class="control-label mb-10">Floors Below Ground</label>
													<input type="text" class="form-control" id="floorsBelowGround" name="floorsBelowGround" placeholder="iotJumpWay Location floors below ground" required value="<?=$Location["context"]["Data"]["floorsBelowGround"]["value"]; ?>">
													<span class="help-block">Number of floors below ground in the iotJumpWay Location</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Floors Above Ground</label>
													<input type="text" class="form-control" id="floorsAboveGround" name="floorsAboveGround" placeholder="iotJumpWay Location floors above ground" required value="<?=$Location["context"]["Data"]["floorsAboveGround"]["value"]; ?>">
													<span class="help-block">Number of floors above ground in the iotJumpWay Location</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Opening Hours</label>
													<input type="text" class="form-control" id="openingHours" name="openingHours" placeholder="iotJumpWay Location opening hours" required value="<?=$Location["context"]["Data"]["openingHours"]["value"]; ?>">
													<span class="help-block">iotJumpWay Location postal code</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Date Modified</label>
													<p><?=$Location["context"]["Data"]["dateModified"]["value"]; ?></p>
												</div>
												<div class="form-group mb-0">
													<input type="hidden" class="form-control" id="update_location" name="update_location" required value="1">
													<button type="submit" class="btn btn-success btn-anim"><i class="icon-rocket"></i><span class="btn-text">Update Location</span></button>
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
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">iotJumpWay Location Zones</h6>
								</div>
								<div class="pull-right"><a href="<?=$domain; ?>/iotJumpWay/Zones"><i class="fa fa-eye"></i></a> | <a href="<?=$domain; ?>/iotJumpWay/Zones/Create"><i class="fa fa-plus"></i></a></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body" style="height: 425px;">
									<div class="table-wrap mt-40">
										<div class="table-responsive">
											<table class="table mb-0">
												<thead>
													<tr>
													<th>ID</th>
													<th>Name</th>
													<th>ACTION</th>
													</tr>
												</thead>
												<tbody>

												<?php
													$Zones = $iotJumpWay->getZones(5);
													if(count($Zones["Data"])):
														foreach($Zones["Data"] as $key => $value):
												?>

													<tr>
														<td><a href="javascript:void(0)">#<?=$value["zid"]["value"];?></a></td>
														<td><?=$value["name"]["value"];?></td>
														<td><a href="<?=$domain; ?>/iotJumpWay/<?=$value["lid"]["value"];?>/Zones/<?=$value["zid"]["value"];?>"><i class="fa fa-edit"></i></a></a></td>
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
									<h6 class="panel-title txt-dark">iotJumpWay Location Devices</h6>
								</div>
								<div class="pull-right"><a href="<?=$domain; ?>/iotJumpWay/Devices"><i class="fa fa-eye"></i></a> | <a href="<?=$domain; ?>/iotJumpWay/Devices/Create"><i class="fa fa-plus"></i></a></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body" style="height: 425px;">
									<div class="table-wrap mt-40">
										<div class="table-responsive">
											<table class="table mb-0">
												<thead>
													<tr>
													<th>ID</th>
													<th>Name</th>
													<th>Zone</th>
													<th>ACTION</th>
													</tr>
												</thead>
												<tbody>

												<?php
													$Devices = $iotJumpWay->getDevices(5);
													if(count($Devices["Data"])):
														foreach($Devices["Data"] as $key => $value):

												?>

													<tr>
													<td><a href="javascript:void(0)">#<?=$value["did"]["value"];?></a></td>
													<td><?=$value["name"]["value"];?></td>
													<td>#<?=$value["zid"]["value"];?> </td>
													<td><a href="<?=$domain; ?>/iotJumpWay/<?=$value["lid"]["value"];?>/Zones/<?=$value["zid"]["value"];?>/Devices/<?=$value["did"]["value"];?>"><i class="fa fa-edit"></i></a></a></td>
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
									<h6 class="panel-title txt-dark">iotJumpWay Location Applications</h6>
								</div>
								<div class="pull-right"><a href="<?=$domain; ?>/iotJumpWay/Applications"><i class="fa fa-eye"></i></a> | <a href="<?=$domain; ?>/iotJumpWay/Applications/Create"><i class="fa fa-plus"></i></a></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body" style="height: 425px;">
									<div class="table-wrap mt-40">
										<div class="table-responsive">
											<table class="table mb-0">
												<thead>
													<tr>
													<th>ID</th>
													<th>Name</th>
													<th>ACTION</th>
													</tr>
												</thead>
												<tbody>

												<?php
													$Applications = $iotJumpWay->getApplications(5);
													if(count($Applications["Data"])):
														foreach($Applications["Data"] as $key => $value):
												?>

													<tr>
													<td><a href="javascript:void(0)">#<?=$value["aid"]["value"];?></a></td>
													<td><?=$value["name"]["value"];?></td>
													<td><a href="<?=$domain; ?>/iotJumpWay/<?=$value["lid"]["value"];?>/Applications/<?=$value["aid"]["value"];?>"><i class="fa fa-edit"></i></a></a></td>
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

			</div>

			<?php include dirname(__FILE__) . '/../Includes/Footer.php'; ?>

		</div>

		<?php  include dirname(__FILE__) . '/../Includes/JS.php'; ?>

		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/mqttws31.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/iotJumpWay.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/iotJumpWayUI.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				iotJumpwayUI.HideLocationInputs();
			});
		</script>

	</body>

</html>
