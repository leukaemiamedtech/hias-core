<?php session_start();

$pageDetails = [
	"PageID" => "HIASCDI",
	"SubPageID" => "HIASCDI",
	"LowPageID" => "Console"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../HIASCDI/Classes/Interface.php';

$cb = $HiasInterface->get_hiascdi_entity();
list($dev1On, $dev1Off) = $iotJumpWay->get_device_status($cb["networkStatus"]["value"]);
$stats = $iotJumpWay->get_stats();

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
						<div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
							<div class="panel panel-default card-view panel-refresh">
								<div class="panel-heading">
									<div class="pull-left">
										<h6 class="panel-title txt-dark">HIASCDI Console</h6>
									</div>
									<div class="pull-right text-right">
										<div class="form-group">
											<div class="col-md-12">
												<i class="fas fa-battery-full data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="batteryUsage"><?=$cb["batteryLevel"]["value"]; ?></span>% &nbsp;&nbsp;
												<i class="fa fa-microchip data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="cpuUsage"><?=$cb["cpuUsage"]["value"]; ?></span>% &nbsp;&nbsp;
												<i class="zmdi zmdi-memory data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="memoryUsage"><?=$cb["memoryUsage"]["value"]; ?></span>% &nbsp;&nbsp;
												<i class="far fa-hdd data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="hddUsage"><?=$cb["hddUsage"]["value"]; ?></span>% &nbsp;&nbsp;
												<i class="fa fa-thermometer-quarter data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="temperatureUsage"><?=$cb["temperature"]["value"]; ?></span>°C &nbsp;&nbsp;
												<span id="offline1" style="color: #33F9FF !important;" class="<?=$dev1On; ?>"><i class="fas fa-power-off" style="color: #33F9FF !important;"></i> Online</span> <span id="online1" class="<?=$dev1Off; ?>" style="color: #99A3A4 !important;"><i class="fas fa-power-off" style="color: #99A3A4 !important;"></i> Offline</span>
											</div>
										</div>
									</div>
									<div class="clearfix"></div>
								</div>
								<div class="panel-wrapper collapse in">
									<div class="panel-body">

										<div class="row">
											<div class="col-lg-12">

												<p>HIASCDI Console allows you to send real-time requests to HIASCDI. Read the official <a href="https://hiascdi.readthedocs.io/en/latest/" target="_BLANK">HIASCDI Documentation</a> for more information.</p>
												<p>&nbsp;</p>

											</div>
										</div>

										<div class="form-wrap">
											<form data-toggle="validator" role="form" id="hiascdi_console_form">

												<div class="row">
													<div class="col-lg-12">
														<label for="name" class="control-label mb-10">Request:</label>
													</div>
												</div>
												<div class="row">
													<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
														<div class="form-group">
															<select class="form-control" id="method" name="method" required>
																<option value="GET">GET</option>
																<option value="POST">POST</option>
																<option value="PATCH">PATCH</option>
																<option value="PUT">PUT</option>
																<option value="DELETE">DELETE</option>
															</select>
														</div>
													</div>
													<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
														<div class="form-group">
															<select class="form-control" id="accept" name="accept" required>
																<option value="application/json">application/json</option>
																<option value="text/plain">text/plain</option>
															</select>
														</div>
													</div>
													<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
														<div class="form-group">
															<select class="form-control" id="endpoint" name="endpoint" required>
																<option value="<?=$HIAS->hiascdi->confs["entities_url"]; ?>"><?=$HIAS->hiascdi->confs["entities_url"]; ?></option>
																<option value="<?=$HIAS->hiascdi->confs["types_url"]; ?>"><?=$HIAS->hiascdi->confs["types_url"]; ?></option>
																<option value="<?=$HIAS->hiascdi->confs["subscriptions_url"]; ?>"><?=$HIAS->hiascdi->confs["subscriptions_url"]; ?></option>
																<option value="/">Root</option>
															</select>
														</div>
													</div>
													<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
														<div class="form-group">
															<input type="text" class="form-control" id="entity" name="entity" placeholder="Entity ID if relevant" value="">
														</div>
													</div>
													<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
														<div class="form-group">
															<input type="text" class="form-control" id="params" name="params" placeholder="Request Parameter String" value="">
														</div>
													</div>
													<div class="col-lg-2 col-md-12 col-sm-12 col-xs-12">
														<div class="form-group mb-0">
															<input type="hidden" class="form-control" id="send_hiascdi_console" name="send_hiascdi_console" value=True>
															<button type="submit" class="btn btn-success btn-anim"><i class="icon-rocket"></i><span class="btn-text">Send</span></button>
														</div>
													</div>
													<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
														<div class="form-group">
															<label for="name" class="control-label mb-10">Body:</label>
															<textarea class="form-control" rows=20 id="body" name="body"></textarea>
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
										<div class="form-group">
											<label for="name" class="control-label mb-10">Response: <span id="rcode">NA</span> | Length: <span id="rlen">NA</span></label>
											<textarea class="form-control" rows=18 id="response" style="background: #333; color: #fff;" name="response"></textarea><br /><span id="rtime"></span><br /><br />
											<label for="name" class="control-label mb-10">Response Headers</label>
											<textarea class="form-control" rows=7 id="response_headers" style="background: #333; color: #fff;" name="response_headers"></textarea><br /><span id="rtime"></span>
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
			<script type="text/javascript" src="/HIASCDI/Classes/HIASCDI.js"></script>

	</body>
</html>