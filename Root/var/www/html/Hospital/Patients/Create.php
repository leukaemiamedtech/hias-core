<?php session_start();

$pageDetails = [
	"PageID" => "HIS",
	"SubPageID" => "Patients"
];

include dirname(__FILE__) . '/../../../Classes/Core/init.php';
include dirname(__FILE__) . '/../../../Classes/Core/GeniSys.php';
include dirname(__FILE__) . '/../../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../../Hospital/Patients/Classes/Patients.php';

$_GeniSysAi->checkSession();

$Locations = $iotJumpWay->getLocations(0, "id ASC");

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
									<h6 class="panel-title txt-dark"><i class="fa fa-hospital-user"></i> Create Hospital Patient
									</h6>
								</div>
								<div class="pull-right"></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="form-wrap">
										<form data-toggle="validator" role="form" id="patient_create">
											<hr class="light-grey-hr" />
											<div class="row">
												<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
													<div class="form-group">
														<label for="name" class="control-label mb-10">Name</label>
														<input type="text" class="form-control" id="name" name="name" placeholder="Patient Name" required value="">
														<span class="help-block"> Name of patient</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Description</label>
														<input type="text" class="form-control" id="description" name="description" placeholder="Device Description" required value="">
														<span class="help-block"> Patient description</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Username</label>
														<input type="text" class="form-control" id="username" name="username" placeholder="Patient Username" required value="">
														<span class="help-block"> Username of patient</span>
													</div>
													<div class="form-group">
														<label class="control-label mb-10">Category</label>
														<select class="form-control" id="category" name="category" required>
															<option value="">PLEASE SELECT</option>

															<?php
																$categories = $Patients->getPatientCategories();
																if(count($categories)):
																	foreach($categories as $key => $value):
															?>

															<option value="<?=$value["category"]; ?>"><?=$value["category"]; ?></option>

															<?php
																	endforeach;
																endif;
															?>

														</select>
														<span class="help-block">Patient category</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Email *</label>
														<input type="text" class="form-control" id="email" name="email" placeholder="Email of patient" required value="">
														<span class="help-block">Email of patient</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Address Street Address</label>
														<input type="text" class="form-control" id="streetAddress" name="streetAddress" placeholder="iotJumpWay Location street address" required value="">
														<span class="help-block">iotJumpWay Location street address</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Address Locality</label>
														<input type="text" class="form-control" id="addressLocality" name="addressLocality" placeholder="iotJumpWay Location address locality" required value="">
														<span class="help-block">iotJumpWay Location address locality</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Address Postal Code</label>
														<input type="text" class="form-control" id="postalCode" name="postalCode" placeholder="iotJumpWay Location postal code" required value="">
														<span class="help-block">iotJumpWay Location post code</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">NFC UID</label>
														<input type="text" class="form-control" id="nfc" name="nfc" placeholder="NFC UID"  value="">
														<span class="help-block">UID of patient's NFC card/fob/implant</span>
													</div>
													<div class="form-group mb-0">
														<input type="hidden" class="form-control" id="create_patient" name="create_patient" required value="1">
														<button type="submit" class="btn btn-success btn-anim"><i class="icon-rocket"></i><span lass="btn-text">submit</span></button>
													</div>
												</div>
												<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
													<div class="form-group">
														<label class="control-label mb-10">Location</label>
														<select class="form-control" id="lid" name="lid" required>
															<option value="">PLEASE SELECT</option>

															<?php
																$Locations = $iotJumpWay->getLocations();
																if(count($Locations["Data"])):
																	foreach($Locations["Data"] as $key => $value):
															?>

																<option value="<?=$value["lid"]["value"]; ?>">#<?=$value["lid"]["value"]; ?>: <?=$value["name"]["value"]; ?></option>

															<?php
																	endforeach;
																endif;
															?>

														</select>
														<span class="help-block"> Location of patient</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Is Active:</label>
														<input type="checkbox" class="" id="active" name="active" value=1>
														<span class="help-block">Is patient active?</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Is Admitted:</label>
														<input type="checkbox" class="" id="admitted" name="admitted" value=1>
														<span class="help-block">Is patient admitted?</span>
													</div>
													<div class="clearfix"></div>
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

		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/mqttws31.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/iotJumpWay.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/Hospital/Patients/Classes/Patients.js"></script>

	</body>
</html>