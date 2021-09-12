<?php session_start();

$pageDetails = [
	"PageID" => "Install",
	"LowPageID" => "Install",
	"PageID" => "Install"
];

include dirname(__FILE__) . '/../Classes/Core/init.php';
include dirname(__FILE__) . '/../Classes/Core/Install.php';

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

		<link type="text/css" rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

		<link type="image/x-icon" rel="icon" href="/img/favicon.png" />
		<link type="image/x-icon" rel="shortcut icon" href="/img/favicon.png" />
		<link type="image/x-icon" rel="apple-touch-icon" href="/img/favicon.png" />

		<link href="/vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>

		<link href="/vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet" type="text/css">

		<link href="/dist/css/style.css" rel="stylesheet" type="text/css">
	</head>
	<body id="GeniSysAI">
		<div class="preloader-it">
			<div class="la-anim-1"></div>
		</div>

		<div class="wrapper pa-0">
			<header class="sp-header">
				<div class="sp-logo-wrap pull-left">
					<a href="index.html">
						<img class="brand-img mr-10" src="/img/logo.png" alt="brand"/>
						<span class="brand-text" style="color: black !important;">HIAS</span>
					</a>
				</div>
				<div class="form-group mb-0 pull-right"></div>
				<div class="clearfix"></div>
			</header>

			<div class="page-wrapper pa-0 ma-0 auth-page">
				<div class="container-fluid">

					<div class="table-struct full-width full-height">
						<div class="table-cell vertical-align-middle auth-form-wrap">
							<form data-toggle="validator" role="form" id="Install">

								<div class="  ml-auto mr-auto no-float card-view pt-30 pb-30">
									<div class="row">
										<div class="col-sm-12 col-xs-12">
											<div class="mb-30">
												<h3 class="text-center txt-dark mb-10">Installation</h3>
												<h6 class="text-center nonecase-font txt-grey">Finalize HIAS Core Installation</h6>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-6 col-xs-12">
											<div class="mb-30">
												<h3 class="text-center txt-dark mb-10"></h3>
												<h6 class="text-center nonecase-font txt-grey"></h6>
											</div>
											<div class="form-wrap">
												<div class="form-group">
													<label class="control-label mb-10" for="username">Location Name</label>
													<input type="text" class="form-control" required="" id="location" name="location" placeholder="Location Name" value="">
													<span class="help-block"> Name of location</span>
												</div>
												<div class="form-group">
													<label class="control-label mb-10">Location Category</label>
													<select class="form-control" id="location_category" name="location_category[]" required multiple>

														<?php
															$buildings = $Install->get_location_categories();
															if(count($buildings)):
																foreach($buildings as $key => $value):
														?>

															<option value="<?=$value["building"]; ?>"><?=$value["building"]; ?></option>

														<?php
																endforeach;
															endif;
														?>

													</select>
													<span class="help-block">Location Building category</span>
												</div>
											</div>
										</div>
										<div class="col-sm-6 col-xs-12">
											<div class="mb-30">
												<h3 class="text-center txt-dark mb-10"></h3>
												<h6 class="text-center nonecase-font txt-grey"></h6>
											</div>
											<div class="form-wrap">
												<div class="form-group">
													<label for="name" class="control-label mb-10">Zone Name</label>
													<input type="text" class="form-control" id="zone" name="zone" placeholder="Zone Name" required value="">
													<span class="help-block"> Name of zone</span>
												</div>
												<div class="form-group">
													<label class="control-label mb-10">Category</label>
													<select class="form-control" id="zone_category" name="zone_category[]" required multiple>

														<option value="">PLEASE SELECT</option>

														<?php
															$categories = $Install->get_zone_categories();
															if(!isSet($categories["Error"])):
																foreach($categories as $key => $value):
														?>

														<option value="<?=$value["category"]; ?>"><?=$value["category"]; ?></option>

														<?php
																endforeach;
															endif;
														?>

													</select>
													<span class="help-block">Zone category</span>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-6 col-xs-12">
											<div class="form-wrap">
												<div class="form-group">
													<label for="name" class="control-label mb-10">Location Latitude</label>
													<input type="text" class="form-control" id="lat" name="lat" placeholder="Location Latitude" required value="">
													<span class="help-block">Location Latitude</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Location Longitude</label>
													<input type="text" class="form-control" id="lng" name="lng" placeholder="Location Longitude" required value="2.1086">
													<span class="help-block">Location Longitude</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Recaptcha Public Key</label>
													<input type="text" class="form-control" id="recaptcha" name="recaptcha" placeholder="Your Public Recaptcha Key" required value="">
													<span class="help-block">Your Public Recaptcha Key</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Recaptcha Private Key</label>
													<input type="text" class="form-control" id="recaptchas" name="recaptchas" placeholder="Your Public Recaptcha Key" required value="">
													<span class="help-block">Your Public Recaptcha Key</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Google Maps Key</label>
													<input type="text" class="form-control" id="gmaps" name="gmaps" placeholder="Your Google Maps Key" required value="">
													<span class="help-block">Your Google Maps Key</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">IP Info Key</label>
													<input type="text" class="form-control" id="ipinfo" name="ipinfo" placeholder="IP Info Key" required value="">
													<span class="help-block">IP Info Key</span>
												</div>
											</div>
										</div>
										<div class="col-sm-6 col-xs-12">
											<div class="form-wrap">
												<div class="form-group">
													<label for="name" class="control-label mb-10">Your Name</label>
													<input type="text" class="form-control" id="first_name" name="first_name" placeholder="Your First Name" required value="">
													<span class="help-block"> Your first name</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Your Surname</label>
													<input type="text" class="form-control" id="second_name" name="second_name" placeholder="Your Surname" required value="">
													<span class="help-block"> Your surname</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Your Username</label>
													<input type="text" class="form-control" id="username" name="username" placeholder="Your Username" required value="">
													<span class="help-block"> Your username</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Your Email</label>
													<input type="email" class="form-control" id="your_email" name="your_email" placeholder="Your Email" required value="">
													<span class="help-block"> Your email</span>
												</div>
												<div class="form-group mb-0">
													<input type="hidden" class="form-control" id="complete_installation" name="complete_installation" required value="1">
													<button type="submit" class="btn btn-success btn-anim"><i class="icon-rocket"></i><span class="btn-text">Complete installation</span></button>
												</div>
											</div>
										</div>
									</div>
								</div>

							</form>
						</div>
					</div>

				</div>

			</div>

		</div>

		<?php  include dirname(__FILE__) . '/Includes/JS.php'; ?>

		<script type="text/javascript" src="/Media/JS/Install.js"></script>

	</body>
</html>
