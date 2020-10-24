<?php session_start();

$pageDetails = [
	"PageID" => "AI",
	"SubPageID" => "AIALL"
];

include dirname(__FILE__) . '/../../../Classes/Core/init.php';
include dirname(__FILE__) . '/../../../Classes/Core/GeniSys.php';
include dirname(__FILE__) . '/../../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../../AI/ALL/Classes/ALL.php';

$_GeniSysAi->checkSession();
$Devices = $ALL->getDevices();

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

	<link href="<?=$domain; ?>/vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
	<link href="<?=$domain; ?>/vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
	<link href="<?=$domain; ?>/vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet" type="text/css">
	<link href="<?=$domain; ?>/dist/css/style.css" rel="stylesheet" type="text/css">
	<link href="<?=$domain; ?>/AI/GeniSysAI/Media/CSS/GeniSys.css" rel="stylesheet" type="text/css">
</head>

<body>

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
									<h6 class="panel-title txt-dark">Acute Lymphoblastic Leukemia (ALL) Models</h6>
								</div>
								<div class="pull-right"></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<p>The Acute Lymphoblastic Leukemia (ALL) Models are a range of computer vision models for detecting Acute Lymphoblastic Leukemia, designed by the <a href="https://www.petermossamlallresearch.com/" target="_BLANK">Peter Moss Acute Lymphoblastic & Lymphoblastic AI Research Project</a> team. The models are designed to be used on constrained devices making them suitable for IoT networks. These models use a variety of programming languages, frameworks and hardware providing. You can download our ALL models from the <a href="https://github.com/AmlResearchProject" target="_BLANK">Peter Moss Acute Lymphoblastic & Lymphoblastic Leukemia AI Research Project Github repository</a>, instructions for installation are provided in the tutorials. To find out more about the Peter Moss Acute Lymphoblastic & Lymphoblastic AI Research Project, you can visit the <a href="https://www.leukemiaairesearch.com/research-projects/project/aml-all-ai-research-project" target="_BLANK">research project homepage</a> on our website, or visit the <a href="https://www.petermossamlallresearch.com/" target="_BLANK">official website</a>.</p>
								</div>
							</div>
						</div>
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<p>In addition to using Acute Lymphoblastic Leukemia detection models created by the Peter Moss Acute Lymphoblastic & Lymphoblastic AI Research Project team, you can develop your own models and connect them up to the HIAS network.</p>
								</div>
							</div>
						</div>
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">Acute Lymphoblastic Leukemia Detection Devices</h6>
								</div>
								<div class="pull-right"><a href="<?=$domain; ?>/AI/ALL/Create"><i class="fa fa-plus"></i></a></div>
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
														<th>DETAILS</th>
														<th>STATUS</th>
														<th>ACTION</th>
													</tr>
												</thead>
												<tbody>

												<?php
													if($Devices["Response"] != "Failed"):
														foreach($Devices["Data"] as $key => $value):
												?>

												  <tr>
													<td><a href="javascript:void(0)">#<?=$value["did"]["value"];?></a></td>
													<td>
														<strong>Name:</strong> <?=$value["name"]["value"];?><br />
														<strong>Zone:</strong> #<?=$value["zid"]["value"];?>
													</td>
													<td>
														<div class="label label-table <?=$value["status"]["value"] == "OFFLINE" ? "label-danger" : "label-success"; ?>">
															<?=$value["status"]["value"] == "OFFLINE" ? "OFFLINE" : "ONLINE"; ?>
														</div>
													</td>
													<td><a href="/AI/ALL/<?=$value["did"]["value"];?>"><i class="fa fa-edit"></i> Edit</a> | <a href="/AI/ALL/<?=$value["did"]["value"];?>/Classify"><i class="fa fa-bullseye"></i> Classify</a></td>
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
					<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12"></div>
				</div>

			</div>

			<?php include dirname(__FILE__) . '/../../Includes/Footer.php'; ?>

		</div>

		<?php  include dirname(__FILE__) . '/../../Includes/JS.php'; ?>

		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/mqttws31.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/iotJumpWay.js"></script>

</body>

</html>