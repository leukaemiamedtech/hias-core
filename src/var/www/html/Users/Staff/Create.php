<?php session_start();

$pageDetails = [
	"PageID" => "HIS",
	"SubPageID" => "Staff",
	"LowPageID" => "Active"
];

include dirname(__FILE__) . '/../../../Classes/Core/init.php';
include dirname(__FILE__) . '/../../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../../Users/Staff/Classes/Staff.php';

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
	<meta name="author" content="hencework" />

	<script src="https://kit.fontawesome.com/58ed2b8151.js" crossorigin="anonymous"></script>

	<link type="image/x-icon" rel="icon" href="/img/favicon.png" />
	<link type="image/x-icon" rel="shortcut icon" href="/img/favicon.png" />
	<link type="image/x-icon" rel="apple-touch-icon" href="/img/favicon.png" />

	<link href="/dist/css/style.css" rel="stylesheet" type="text/css">
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
									<h6 class="panel-title txt-dark"><i class="fa fa-users"></i> Create new HIAS Staff account
									</h6>
								</div>
								<div class="pull-right"></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="form-wrap">
										<form data-toggle="validator" role="form" id="staff_create">
											<hr class="light-grey-hr" />
											<div class="row">
												<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
													<div class="form-group">
														<label for="name" class="control-label mb-10">First Name</label>
														<input type="text" class="form-control" id="name" name="name" placeholder="Staff Name" required value="">
														<span class="help-block"> First name of staff</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Surname</label>
														<input type="text" class="form-control" id="sname" name="sname" placeholder="Staff Name" required value="">
														<span class="help-block"> Surname of staff</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Description</label>
														<input type="text" class="form-control" id="description" name="description" placeholder="Device Description" required value="">
														<span class="help-block"> Staff description</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Username</label>
														<input type="text" class="form-control" id="username" name="username" placeholder="Staff Username" required value="">
														<span class="help-block"> Username of staff</span>
													</div>
													<div class="form-group">
														<label class="control-label mb-10">Category</label>
														<select class="form-control" id="category" name="category" required >
															<option value="">PLEASE SELECT</option>

															<?php
																$categories = $Staff->get_staff_categories();
																if(count($categories)):
																	foreach($categories as $key => $value):
															?>

															<option value="<?=$value["id"]; ?>,<?=$value["category"]; ?>"><?=$value["category"]; ?></option>

															<?php
																	endforeach;
																endif;
															?>

														</select>
														<span class="help-block">Staff category</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Email *</label>
														<input type="text" class="form-control" id="email" name="email" placeholder="Email of staff member" required value="">
														<span class="help-block">Email of staff member</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Address Street Address</label>
														<input type="text" class="form-control" id="streetAddress" name="streetAddress" placeholder="Location street address" required value="">
														<span class="help-block">Location street address</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Address Locality</label>
														<input type="text" class="form-control" id="addressLocality" name="addressLocality" placeholder="Location address locality" required value="">
														<span class="help-block">Location address locality</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Address Postal Code</label>
														<input type="text" class="form-control" id="postalCode" name="postalCode" placeholder="Location postal code" required value="">
														<span class="help-block">Location post code</span>
													</div>
													<div class="form-group mb-0">
														<input type="hidden" class="form-control" id="create_staff" name="create_staff" required value="1">
														<button type="submit" class="btn btn-success btn-anim"><i class="icon-rocket"></i><span class="btn-text">submit</span></button>
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
														<span class="help-block"> Authorized location</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">IP</label>
														<input type="text" class="form-control hider" id="ip" name="ip" placeholder="IP address"  required value="">
														<span class="help-block"> IP address of staff member</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">MAC</label>
														<input type="text" class="form-control hider" id="mac" name="mac" placeholder="Mac address" required value="">
														<span class="help-block"> MAC address of staff member</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Bluetooth Address</label>
														<input type="text" class="form-control hider" id="bluetooth" name="bluetooth" placeholder="Bluetooth address"  value="">
														<span class="help-block">Bluetooth address of staff member</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">NFC UID</label>
														<input type="text" class="form-control" id="nfc" name="nfc" placeholder="NFC card/fob/implant UID"  value="">
														<span class="help-block">NFC card/fob/implant UID of staff member</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Is Admin:</label>
														<input type="checkbox" class="" id="admin" name="admin" value=1>
														<span class="help-block">Is staff member an admin?</span>
													</div>
												</div>
											</div>
											<hr class="light-grey-hr" />
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
					</div>
				</div>

			</div>

			<?php include dirname(__FILE__) . '/../../Includes/Footer.php'; ?>

		</div>

		<?php  include dirname(__FILE__) . '/../../Includes/JS.php'; ?>

		<script type="text/javascript" src="/iotJumpWay/Classes/mqttws31.js"></script>
		<script type="text/javascript" src="/iotJumpWay/Classes/iotJumpWay.js"></script>

		<script type="text/javascript" src="/Users/Staff/Classes/Staff.js"></script>

	</body>
</html>