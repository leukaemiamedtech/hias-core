<?php session_start();

$pageDetails = [
	"PageID" => "IoT",
	"SubPageID" => "IoT",
	"LowPageID" => "Data"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../../Classes/Core/GeniSys.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../Blockchain/Classes/Blockchain.php';

$_GeniSysAi->checkSession();

$LId = 1;
$Location = $iotJumpWay->getLocation($LId);
$blockchainConf = $Blockchain->getBlockchainConf();


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
		<link href="<?=$domain; ?>/GeniSysAI/Media/CSS/GeniSys.css" rel="stylesheet" type="text/css">
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
									<h6 class="panel-title txt-dark">iotJumpWay Location Device/Application Life</h6>
								</div>
								<div class="pull-right"><a href="<?=$domain; ?>/iotJumpWay/Data/Life"><i class="fa fa-eye pull-left"></i> View All Life Data</a></div>
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
													<th>Use</th>
													<th>Device/Application Details</th>
													<th>Data</th>
													<th>Time</th>
													<th>ACTION</th>
												  </tr>
												</thead>
												<tbody>

												<?php
													$Life = $iotJumpWay->retrieveLife(5);
													if($Life["Response"] == "OK"):
														foreach($Life["ResponseData"] as $key => $value):

															$location = $iotJumpWay->getLocation($value->Location);
															$device = $iotJumpWay->getDevice($value->Device);
															$zone = $iotJumpWay->getZone($value->Zone);
															$application = $iotJumpWay->getApplication($value->Application);
															$hashString = (string)$value->Data->CPU . (string)$value->Data->Memory . (string)$value->Data->Diskspace . (string)$value->Data->Temperature . (string)$value->Data->Latitude . (string)$value->Data->Longitude;

												?>

												  <tr>
													<td>#<?=$value->_id;?></td>
													<td><?=$value->Use;?></td>
													<td><strong>Location:</strong> #<?=$value->Location;?> - <?=$location["name"]; ?><br />
														<strong>Zone:</strong> <?=$value->Zone != 0 ? "#" . $value->Zone . " - " . $zone["zn"] : "NA"; ?><br />
														<strong>Device</strong> <?=$value->Device != 0 ? "#" . $value->Device . " - " . $device["name"] : "NA"; ?><br />
														<strong>Application</strong> <?=$value->Application != 0 ? "#" . $value->Application . " - " . $application["name"] : "NA";?>
													</td>
													<td>
														<strong>CPU</strong>: <?=$value->Data->CPU;?>%<br />
														<strong>Memory</strong>: <?=$value->Data->Memory;?>%<br />
														<strong>Diskspace</strong>: <?=$value->Data->Diskspace;?>%<br />
														<strong>Temperature</strong>: <?=$value->Data->Temperature;?>°C<br />
														<strong>Latitude</strong>: <?=$value->Data->Latitude;?><br />
														<strong>Longitude</strong>: <?=$value->Data->Longitude;?><br />
													</td>
													<td><?=$value->Time;?> </td>
													<td><a href="javascript:void(0);" class="btn btn-success btn-anim verify" data-user="<?=$_SESSION["GeniSysAI"]["BC"]["BCUser"];?>" data-key="<?=$value->_id;?>" data-hash="<?=$hashString; ?>"><span class="btn-text">VERIFY</span></button></td>
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

				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">iotJumpWay Location Device/Application Statuses</h6>
								</div>
								<div class="pull-right"><a href="<?=$domain; ?>/iotJumpWay/Data/Statuses"><i class="fa fa-eye pull-left"></i> View All Status Data</a></div>
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
													<th>Use</th>
													<th>Details</th>
													<th>Status</th>
													<th>Time</th>
													<th>ACTION</th>
												  </tr>
												</thead>
												<tbody>

												<?php
													$Statuses = $iotJumpWay->retrieveStatuses(5);
													if($Statuses["Response"] == "OK"):
														foreach($Statuses["ResponseData"] as $key => $value):
															$location = $iotJumpWay->getLocation($value->Location);
															$device = $iotJumpWay->getDevice($value->Device);
															$zone = $iotJumpWay->getZone($value->Zone);
															$application = $iotJumpWay->getApplication($value->Application);
															$hashString = (string)$value->Status;
												?>

												  <tr>
													<td>#<?=$value->_id;?></td>
													<td><?=$value->Use;?></td>
													<td><strong>Location:</strong> #<?=$value->Location;?> - <?=$location["name"]; ?><br />
														<strong>Zone:</strong> <?=$value->Zone != 0 ? "#" . $value->Zone . " - " . $zone["zn"] : "NA"; ?><br />
														<strong>Device</strong> <?=$value->Device != 0 ? "#" . $value->Device . " - " . $device["name"] : "NA"; ?><br />
														<strong>Application</strong> <?=$value->Application != 0 ? "#" . $value->Application . " - " . $application["name"] : "NA";?>
													</td>
													<td><?=$value->Status;?></td>
													<td><?=$value->Time;?> </td>
													<td><a href="javascript:void(0);" class="btn btn-success btn-anim verify" data-user="<?=$_SESSION["GeniSysAI"]["BC"]["BCUser"];?>" data-key="<?=$value->_id;?>" data-hash="<?=$hashString; ?>"><span class="btn-text">VERIFY</span></button></td>
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

				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">iotJumpWay Location Device/Application Commmands</h6>
								</div>
								<div class="pull-right"><a href="<?=$domain; ?>/iotJumpWay/Data/Commands"><i class="fa fa-eye pull-left"></i> View All Commmand Data</a></div>
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
													<th>Use</th>
													<th>Details</th>
													<th>Type</th>
													<th>Value</th>
													<th>Message</th>
													<th>Time</th>
													<th>ACTION</th>
												  </tr>
												</thead>
												<tbody>

												<?php
													$Commands = $iotJumpWay->retrieveCommands();
													if($Commands["Response"] == "OK"):
														foreach($Commands["ResponseData"] as $key => $value):

															$location = $iotJumpWay->getLocation($value->Location);
															$device = $iotJumpWay->getDevice($value->From);
															$devicet = $iotJumpWay->getDevice($value->To);
															$zone = $iotJumpWay->getZone($value->Zone);
															$hashString = (string)$value->From . (string)$value->Type . (string)$value->Value . (string)$value->Message ;
												?>

												  <tr>
													<td>#<?=$value->_id;?></td>
													<td><?=$value->Use;?></td>
													<td>Location #<?=$value->Location;?>: <?=$location["name"]; ?><br />
														Zone <?=$value->Zone != 0 ? "#" . $value->Zone . ": " . $zone["zn"] : "NA"; ?><br />
														From <?=$value->From != 0 ? "#" . $value->From . ": " . $device["name"] : "NA"; ?><br />
														To <?=$value->To != 0 ? "#" . $value->To . ": " . $devicet["name"] : "NA"; ?><br />
													</td>
													<td><?=$value->Type;?></td>
													<td><?=$value->Value;?></td>
													<td><?=$value->Message;?></td>
													<td><?=$value->Time;?> </td>
													<td><a href="javascript:void(0);" class="btn btn-success btn-anim verify" data-user="<?=$_SESSION["GeniSysAI"]["BC"]["BCUser"];?>" data-key="<?=$value->_id;?>" data-hash="<?=$hashString; ?>"><span class="btn-text">VERIFY</span></button></td>
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

				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">iotJumpWay Location Device/Application Sensors</h6>
								</div>
								<div class="pull-right"><a href="<?=$domain; ?>/iotJumpWay/Data/Sensors"><i class="fa fa-eye pull-left"></i> View All Sensor Data</a></div>
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
													<th>Use</th>
													<th>Details</th>
													<th>Type</th>
													<th>Sensor</th>
													<th>Value</th>
													<th>Message</th>
													<th>Time</th>
													<th>ACTION</th>
												  </tr>
												</thead>
												<tbody>

												<?php
													$Sensors = $iotJumpWay->retrieveSensors();
													if($Sensors["Response"] == "OK"):
														foreach($Sensors["ResponseData"] as $key => $value):
															$location = $iotJumpWay->getLocation($value->Location);
															$device = $iotJumpWay->getDevice($value->Device);
															$zone = $iotJumpWay->getZone($value->Zone);
															$application = $iotJumpWay->getApplication($value->Application);
															$hashString = (string)$value->Sensor . (string)$value->Type . (string)$value->Value . (string)$value->Message;
												?>

												  <tr>
													<td>#<?=$value->_id;?></td>
													<td><?=$value->Use;?></td>
													<td><strong>Location:</strong> #<?=$value->Location;?> - <?=$location["name"]; ?><br />
														<strong>Zone:</strong> <?=$value->Zone != 0 ? "#" . $value->Zone . " - " . $zone["zn"] : "NA"; ?><br />
														<strong>Device</strong> <?=$value->Device != 0 ? "#" . $value->Device . " - " . $device["name"] : "NA"; ?><br />
														<strong>Application</strong> <?=$value->Application != 0 ? "#" . $value->Application . " - " . $application["name"] : "NA";?>
													</td>
													<td><?=$value->Type;?></td>
													<td><?=$value->Sensor;?></td>
													<td><?=$value->Value; ?></td>
													<td><?=$value->Message;?></td>
													<td><?=$value->Time;?> </td>
													<td><a href="javascript:void(0);" class="btn btn-success btn-anim verify" data-user="<?=$_SESSION["GeniSysAI"]["BC"]["BCUser"];?>" data-key="<?=$value->_id;?>" data-hash="<?=$hashString; ?>"><span class="btn-text">VERIFY</span></button></td>
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

		<div id="abi" class="hide"><?=$blockchainConf["iabi"]; ?></div>
		<div id="address" class="hide"><?=$Blockchain->_GeniSys->_helpers->oDecrypt($blockchainConf["icontract"]); ?></div>

		<?php  include dirname(__FILE__) . '/../Includes/JS.php'; ?>

		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/mqttws31.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/iotJumpWay.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/Blockchain/Classes/Blockchain.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/Blockchain/Classes/web3.js"></script>
		<script type="text/javascript">

			window.addEventListener('load', function () {
				Blockchain.connect("<?=$domain; ?>/Blockchain/API/");
				if(Blockchain.isConnected()){
					msg = "Connected to HIAS Blockchain!";
					Logging.logMessage("Core", "Blockchain", msg);
				} else {
					msg = "Connection to HIAS Blockchain failed!";
					Logging.logMessage("Core", "Blockchain", msg);
				}
			});
		</script>

	</body>

</html>
