<?php session_start();

$pageDetails = [
	"PageID" => "IoT",
	"SubPageID" => "Entities",
	"LowPageID" => "Devices"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';

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

		<link href="/dist/css/style.css" rel="stylesheet" type="text/css">

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
									<h6 class="panel-title txt-dark">HIAS Devices</h6>
								</div>
								<div class="pull-right"><a href="/iotJumpWay/Devices/Create"><i class="fa fa-plus"></i> Create Device</a></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">

									<?php
										$iDevices = $iotJumpWay->get_devices();
										if(!isSet($iDevices["Error"])):
											foreach($iDevices as $key => $value):
									?>

									<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
										<div class="panel-wrapper collapse in small" style="background: #333; margin: 5px; padding: 10px; color: #fff;">

											<div class="row">

												<div class="col-md-12 small">
													<i class="fas fa-battery-full data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idebatU"><?=number_format($value["batteryLevel"]["value"], 2); ?></span>% &nbsp;&nbsp;
													<i class="fa fa-microchip data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idecpuU"><?=number_format($value["cpuUsage"]["value"], 2); ?></span>% &nbsp;&nbsp;
													<i class="zmdi zmdi-memory data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idememU"><?=number_format($value["memoryUsage"]["value"], 2); ?></span>% &nbsp;&nbsp;
													<i class="far fa-hdd data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idehddU"><?=number_format($value["hddUsage"]["value"], 2);?></span>% &nbsp;&nbsp;
													<i class="fa fa-thermometer-quarter data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idetempU"><?=number_format($value["temperature"]["value"], 2); ?></span>°C
												</div>

											</div>

											<br /><strong>Name:</strong> <a href="/iotJumpWay/<?=$value["networkLocation"]["value"];?>/Zones/<?=$value["networkZone"]["value"];?>/Devices/<?=$value["id"];?>"><strong><?=$value["name"]["value"];?></strong></a><br />
											<strong>Type:</strong> <?=$value["category"]["value"][0];?><br />
											<strong>Zone:</strong> <a href="/iotJumpWay/<?=$value["networkZone"]["value"];?>/Zones/<?=$value["networkZone"]["value"];?>"><?=$value["networkZone"]["value"];?></a><br /><br />

											<a href="/iotJumpWay/<?=$value["networkLocation"]["value"];?>/Zones/<?=$value["networkZone"]["value"];?>/Devices/<?=$value["id"];?>"><i class="fa fa-edit"></i>&nbsp;Edit</a><br /><br />

											<div class="pull-right small"><strong>Last Updated: <?=$value["dateModified"]["value"]; ?></strong></div>

											<div class="label label-table <?=$value["networkStatus"]["value"] == "ONLINE" ? "label-success" : "label-danger"; ?>">
												<?=$value["networkStatus"]["value"] == "ONLINE" ? "ONLINE" : "OFFLINE"; ?>
											</div>

										</div>
									</div>

									<?php
											endforeach;
										endif;
									?>

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

	</body>

</html>
