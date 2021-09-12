<?php session_start();

$pageDetails = [
	"PageID" => "IoT",
	"SubPageID" => "Data",
	"LowPageID" => "Statuses"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';

$lId = 1;
$Location = $iotJumpWay->get_location($lId);
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
									<h6 class="panel-title txt-dark">Entity Statuses</h6>
								</div>
								<div class="pull-right"><a href="/iotJumpWay/Data/Statuses"><i class="fa fa-eye pull-left"></i> View All Status Data</a></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="table-wrap mt-40">
										<div class="table-responsive">
											<table class="table mb-0">
												<thead>
												  <tr>
													<th>Details</th>
													<th>Status</th>
													<th>Time</th>
													<th>ACTION</th>
												  </tr>
												</thead>
												<tbody>

												<?php
													$Statuses = $iotJumpWay->retrieve_status_data(300);
													if($Statuses["Response"] == "OK" && !isSet($Statuses["ResponseData"]["Error"])):
														foreach($Statuses["ResponseData"] as $key => $value):
															$hashString = $value["Status"];
												?>

												  <tr>
													<td>
														<strong>Data ID:</strong> <?=$value['_id']['$oid'];?><br />
														<strong>Data Type:</strong> <?=$value["Use"];?><br />
													</td>
													<td>

														<strong>Location:</strong> <a href="/iotJumpWay/" style="color: #ccc;"><?=$value["Location"];?></a><br />

														<?php
															if($value["Zone"]!= "NA"):
														?>

														<strong>Zone:</strong> <a href="/iotJumpWay/<?=$value["Location"]; ?>/Zones/<?=$value["Zone"]; ?>" style="color: #ccc;"><?=$value["Zone"]; ?></a><br />

														<?php
															endif;
															if($value["HIASCDI"] != "NA"):
														?>

														<strong>HIASCDI:</strong> <a href="/HIASCDI/Entity" style="color: #ccc;"><?=$value["HIASCDI"]; ?></a><br />

														<?php
															endif;
															if($value["Agent"] != "NA"):
														?>

														<strong>Agent:</strong> <a href="/iotJumpWay/Agents/Agent/<?=$value["Agent"]; ?>" style="color: #ccc;"><?=$value["Agent"]; ?></a><br />

														<?php
															endif;
															if($value["Device"] != "NA"):
														?>

														<strong>Device:</strong> <a href="/iotJumpWay/<?=$value["Location"]; ?>/Zones/<?=$value["Zone"]; ?>/Devices/<?=$value["Device"]; ?>" style="color: #ccc;"><?=$value["Device"]; ?></a><br />

														<?php
															endif;
															if($value["Application"] != "NA"):
														?>

														<strong>Application:</strong> <a href="/iotJumpWay/<?=$value["Location"]; ?>/Applications/<?=$value["Application"]; ?>" style="color: #ccc;"><?=$value->Application; ?></a><br />

														<?php
															endif;
															if($value["Staff"] != "NA"):
														?>

														<strong>Staff:</strong> <a href="/Users/Staff/<?=$value["Staff"]; ?>" style="color: #ccc;"><?=$value["Staff"]; ?></a><br />

														<?php
															endif;
														?>
														<br />
													</td>
													<td><?=$value["Status"];?></td>
													<td><?=$value["Time"];?> </td>
													<td><a href="javascript:void(0);" class="btn btn-success btn-anim verify" data-user="<?=$_SESSION["HIAS"]["BC"]["BCUser"];?>" data-key="<?=$value['_id']['$oid'];?>" data-hash="<?=$hashString; ?>"><span class="btn-text">VERIFY</span></button></td>
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

		<div id="abi" class="hide"><?php echo json_encode($HIAS->hiasbch->confs["iabi"]); ?></div>
		<div id="address" class="hide"><?=$HIAS->hiasbch->confs["icontract"]; ?></div>

		<?php  include dirname(__FILE__) . '/../Includes/JS.php'; ?>

		<script type="text/javascript" src="/iotJumpWay/Classes/mqttws31.js"></script>
		<script type="text/javascript" src="/iotJumpWay/Classes/iotJumpWay.js"></script>
		<script type="text/javascript" src="/HIASBCH/Classes/HIASBCH.js"></script>
		<script type="text/javascript" src="/HIASBCH/Classes/web3.js"></script>
		<script type="text/javascript">

			window.addEventListener('load', function () {
				HIASBCH.connect("/hiasbch/api/");
				if(HIASBCH.isConnected()){
					msg = "Connected to HIASBCH!";
					Logging.logMessage("Core", "HIASBCH", msg);
				} else {
					msg = "Connection to HIASBCH failed!";
					Logging.logMessage("Core", "HIASBCH", msg);
				}
			});
		</script>

	</body>

</html>
