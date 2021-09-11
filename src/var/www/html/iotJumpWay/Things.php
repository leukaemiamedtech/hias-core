<?php session_start();

$pageDetails = [
	"PageID" => "IoT",
	"SubPageID" => "Entities",
	"LowPageID" => "Things"
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
					<div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">HIAS Things</h6>
								</div>
								<div class="pull-right"><a href="/iotJumpWay/Things/Create"><i class="fa fa-plus"></i> Create Thing</a></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="table-wrap mt-40">
										<div class="table-responsive">
											<table class="table mb-0">
												<thead>
												  <tr>
													<th>DETAILS</th>
													<th></th>
													<th>ACTION</th>
												  </tr>
												</thead>
												<tbody>

												<?php
													$Sensors = $iotJumpWay->get_things(0);
													if(!isSet($Sensors["Error"])):
														foreach($Sensors as $key => $value):
												?>

												  <tr>
													<td style="width: 10%;"><a href="/iotJumpWay/Things/<?=$value["id"];?>"><img src="/iotJumpWay/Media/Images/Things/<?=$value["properties"]["value"]["image"]["value"];?>" style="width: 100%;" /></a></td>
													<td>
														<strong>ID:</strong> <a href="/iotJumpWay/Things/<?=$value["id"];?>"><?=$value["id"];?></a><br />
														<strong>Name:</strong> <a href="/iotJumpWay/Things/<?=$value["id"];?>"><?=$value["name"]["value"];?></a><br />
														<strong>Type:</strong> <?=$value["category"]["value"][0];?>
													</td>
													<td><a href="/iotJumpWay/Things/<?=$value["id"];?>"><i class="fa fa-edit"></i> Edit</a></td>
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

		<script type="text/javascript" src="/iotJumpWay/Classes/mqttws31.js"></script>
		<script type="text/javascript" src="/iotJumpWay/Classes/iotJumpWay.js"></script>

	</body>

</html>
