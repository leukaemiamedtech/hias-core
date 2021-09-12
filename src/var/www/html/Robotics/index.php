<?php session_start();

$pageDetails = [
    "PageID" => "Robotics",
    "SubPageID" => "List",
	"LowPageID" => "List"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../Robotics/Classes/Robotics.php';

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
	</head>

	<body>

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
									<h6 class="panel-title txt-dark">Network Robotics</h6>
								</div>
								<div class="pull-right"><a href="/Robotics/Create"><i class="fas fa-plus-circle"></i> Create Robot</a></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">

									<div class="scroll_450px">

										<?php
											$roboticss = $Robotics->get_robotics(0);
											if(!isSet($roboticss["Error"])):
												foreach($roboticss as $key => $value):
													if($value["deviceModel"]["value"] == "EMAR-Mini"):
														$path = "EMAR-Mini";
													elseif($value["deviceModel"]["value"] == "EMAR"):
														$path = "EMAR";
													endif;
										?>

										<div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
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

												<br /><strong>Name:</strong> <a href="/Robotics/<?=$value["deviceModel"]["value"];?>/Unit/<?=$value["id"];?>"><?=$value["name"]["value"];?></a><br />
												<strong>Type:</strong> <?=$value["category"]["value"][0];?><br /><br />
												<a href="/Robotics/<?=$value["deviceModel"]["value"];?>/Unit/<?=$value["id"];?>"><i class="fa fa-edit"></i> Manage</a><br /><br />

												<div class="pull-right small"><strong>Last Updated: <?=$value["dateModified"]["value"]; ?></strong></div>

												<div class="label label-table <?=$value["networkStatus"]["value"] == "OFFLINE" ? "label-danger" : "label-success"; ?>">
													<?=$value["networkStatus"]["value"] == "OFFLINE" ? "OFFLINE" : "ONLINE"; ?>
												</div>

											</div>
										</div>

										<?php
												endforeach;
											else:
										?>

										<p>No Robotics Installed!</p>

										<?php
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

</body>
</html>