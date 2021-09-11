<?php session_start();

$pageDetails = [
    "PageID" => "IoT",
    "SubPageID" => "Entities",
    "LowPageID" => "Location"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';

$location = $iotJumpWay->get_location($HIAS->confs["lid"], "dateCreated,dateModified,*");
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
		<link href="/AI/GeniSysAI/Media/CSS/GeniSys.css" rel="stylesheet" type="text/css">
		<link href="/vendors/bower_components/fullcalendar/dist/fullcalendar.css" rel="stylesheet" type="text/css"/>
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
									<h6 class="panel-title txt-dark">HIAS Location</h6>
								</div>
								<div class="pull-right"></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">

									<div class="row">
										<div class="col-lg-12">

											<p>HIAS Locations represent the buildings that HIAS networks are installed in. The HIAS Location type is based on the <a href="https://fiware-datamodels.readthedocs.io/en/latest/Building/doc/introduction/index.html" target="_BLANK">NGSI Building Data Model</a>.</p>
											<p>&nbsp;</p>

										</div>
									</div>

									<div class="form-wrap">

										<form data-toggle="validator" role="form" id="location_update">
											<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
												<div class="form-group">
													<label for="name" class="control-label mb-10">Name</label>
													<input type="text" class="form-control" id="name" name="name" placeholder="Location Name" required value="<?=$location["name"]["value"]; ?>">
													<span class="help-block"> Name of location</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Description</label>
													<input type="text" class="form-control" id="description" name="description" placeholder="Location description" required value="<?=$location["description"]["value"]; ?>">
													<span class="help-block"> Description of location</span>
												</div>
												<div class="form-group">
													<label class="control-label mb-10">Category</label>
													<select class="form-control" id="category" name="category[]" required multiple>

														<?php
															$buildings = $iotJumpWay->get_location_categories();
															if(count($buildings)):
																foreach($buildings as $key => $value):
														?>

															<option value="<?=$value["building"]; ?>" <?=in_array($value["building"], $location["category"]["value"]) ? " selected " : ""; ?>><?=$value["building"]; ?></option>

														<?php
																endforeach;
															endif;
														?>

													</select>
													<span class="help-block">NGSI building category</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Address Street Address</label>
													<input type="text" class="form-control" id="streetAddress" name="streetAddress" placeholder="Location street address" required value="<?=$location["address"]["value"]["streetAddress"]; ?>">
													<span class="help-block">Location street address</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Address Locality</label>
													<input type="text" class="form-control" id="addressLocality" name="addressLocality" placeholder="Location address locality" required value="<?=$location["address"]["value"]["addressLocality"]; ?>">
													<span class="help-block">Location address locality</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Address Postal Code</label>
													<input type="text" class="form-control" id="postalCode" name="postalCode" placeholder="Location postal code" required value="<?=$location["address"]["value"]["postalCode"]; ?>">
													<span class="help-block">Location post code</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Coordinates</label>
													<input type="text" class="form-control" id="coordinates" name="coordinates" placeholder="Location coordinates" required value="<?=$location["location"]["value"]["coordinates"][0]; ?>, <?=$location["location"]["value"]["coordinates"][1]; ?>">
													<span class="help-block">Location coordinates</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Map URL</label>
													<input type="text" class="form-control" id="mapUrl" name="mapUrl" placeholder="Location Google Maps URL" required value="<?=$location["mapUrl"]["value"]; ?>, <?=$location["mapUrl"]["value"]; ?>">
													<span class="help-block">Location Google Maps URL</span>
												</div>
											</div>
											<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
												<div class="form-group">
													<label for="name" class="control-label mb-10">Zones</label>
													<input type="text" class="form-control" id="zones" name="zones" placeholder="Location zones" required value=<?=$location["zones"]["value"]; ?>>
													<span class="help-block">Location zones</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Devices</label>
													<input type="text" class="form-control" id="devices" name="devices" placeholder="Location devices" required value=<?=$location["devices"]["value"]; ?>>
													<span class="help-block">Location devices</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Applications</label>
													<input type="text" class="form-control" id="applications" name="applications" placeholder="Location applications" required value=<?=$location["applications"]["value"]; ?>>
													<span class="help-block">Location applications</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Users</label>
													<input type="text" class="form-control" id="users" name="users" placeholder="Location users" required value=<?=$location["users"]["value"]; ?>>
													<span class="help-block">Location users</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Patients</label>
													<input type="text" class="form-control" id="patients" name="patients" placeholder="Location patients" required value=<?=$location["patients"]["value"]; ?>>
													<span class="help-block">Location users</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Floors Below Ground</label>
													<input type="text" class="form-control" id="floorsBelowGround" name="floorsBelowGround" placeholder="Location floors below ground" required value=<?=$location["floorsBelowGround"]["value"]; ?>>
													<span class="help-block">Number of floors below ground in the location</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Floors Above Ground</label>
													<input type="text" class="form-control" id="floorsAboveGround" name="floorsAboveGround" placeholder="Location floors above ground" required value=<?=$location["floorsAboveGround"]["value"]; ?>>
													<span class="help-block">Number of floors above ground in the location</span>
												</div>
											</div>
											<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
												<div class="form-group">
													<label for="name" class="control-label mb-10">Opening Hours</label>
													<input type="text" class="form-control" id="mon" name="mon" placeholder="Opening hours Monday" required value="<?=isset($location["openingHours"][0])  ? $location["openingHours"][0] : ""; ?>"><br />
													<input type="text" class="form-control" id="tues" name="tues" placeholder="Opening hours Tuesday" required value="<?=isset($location["openingHours"][1])  ? $location["openingHours"][1] : ""; ?>"><br />
													<input type="text" class="form-control" id="wed" name="wed" placeholder="Opening hours Wednesday" required value="<?=isset($location["openingHours"][2])  ? $location["openingHours"][2] : ""; ?>"><br />
													<input type="text" class="form-control" id="thurs" name="thurs" placeholder="Opening hours Thursday" required value="<?=isset($location["openingHours"][3])  ? $location["openingHours"][3] : ""; ?>"><br />
													<input type="text" class="form-control" id="fri" name="fri" placeholder="Opening hours Friday" required value="<?=isset($location["openingHours"][4])  ? $location["openingHours"][4] : ""; ?>"><br />
													<input type="text" class="form-control" id="sat" name="sat" placeholder="Opening hours Saturday" required value="<?=isset($location["openingHours"][5])  ? $location["openingHours"][5] : ""; ?>"><br />
													<input type="text" class="form-control" id="sat" name="sun" placeholder="Opening hours Sunday" required value="<?=isset($location["openingHours"][6])  ? $location["openingHours"][6] : ""; ?>"><br />
													<span class="help-block">Location opening hours</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Date Created</label>
													<p><?=$location["dateCreated"]["value"]; ?></p>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Date Modified</label>
													<p><?=$location["dateModified"]["value"]; ?></p>
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
				</div>

			</div>

			<?php include dirname(__FILE__) . '/../Includes/Footer.php'; ?>

		</div>

		<?php  include dirname(__FILE__) . '/../Includes/JS.php'; ?>

		<script type="text/javascript" src="/iotJumpWay/Classes/mqttws31.js"></script>
		<script type="text/javascript" src="/iotJumpWay/Classes/iotJumpWay.js"></script>
		<script type="text/javascript" src="/iotJumpWay/Classes/iotJumpWayUI.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				iotJumpwayUI.HideLocationInputs();
			});
		</script>

	</body>

</html>
