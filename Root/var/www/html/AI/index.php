<?php session_start();

$pageDetails = [
	"PageID" => "AI",
	"SubPageID" => "Models"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../../Classes/Core/GeniSys.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../AI/Classes/AI.php';

$_GeniSysAi->checkSession();
$Models = $AI->getModels();

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
									<h6 class="panel-title txt-dark">HIAS Artificial Intelligence Models</h6>
								</div>
								<div class="pull-right"></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<p>The AI Models are Artificial Intelligence Models created for the HIAS network, designed by the <a href="https://www.leukemiaairesearch.com/" target="_BLANK">Leukemia AI Research</a> team in our research projects (<a href="https://www.petermossamlallresearch.com/" target="_BLANK">Peter Moss Acute Lymphoblastic & Lymphoblastic AI Research Project</a> and <a href="https://github.com/COVID-19-AI-Research-Project" target="_BLANK">Peter Moss COVID-19 AI Research Project</a>).</p>
								</div>
							</div>
						</div>
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<p>In addition to using Leukemia AI Research AI Models, you can develop your own models and add them to the HIAS network for use in AI devices.</p>
								</div>
							</div>
						</div>
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">AI Models</h6>
								</div>
								<div class="pull-right"><a href="<?=$domain; ?>/AI/Create"><i class="fa fa-plus"></i></a></div>
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
														<th>ACTION</th>
													</tr>
												</thead>
												<tbody>

												<?php
													if($Models["Response"] != "Failed"):
														foreach($Models["Data"] as $key => $value):
												?>

												  <tr>
													<td><a href="javascript:void(0)">#<?=$value["mid"]["value"];?></a></td>
													<td>
														<strong>Name:</strong> <?=$value["name"]["value"];?>
													</td>
													<td><a href="/AI/Model/<?=$value["mid"]["value"];?>"><i class="fa fa-edit"></i> Edit</a></td>
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

			<?php include dirname(__FILE__) . '/../Includes/Footer.php'; ?>

		</div>

		<?php  include dirname(__FILE__) . '/../Includes/JS.php'; ?>

		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/mqttws31.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/iotJumpWay.js"></script>

</body>

</html>