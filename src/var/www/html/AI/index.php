<?php session_start();

$pageDetails = [
	"PageID" => "AI",
	"SubPageID" => "Models"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../AI/Classes/AI.php';


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
	<meta name="author" content="hencework" />

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
									<h6 class="panel-title txt-dark">HIAS Artificial Intelligence Models</h6>
								</div>
								<div class="pull-right"></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<p>The HIAS Artificial Intelligence Models are official HIAS models programmed by the <a href="https://www.leukemiaairesearch.com/association/volunteers/" target="_BLANK">Asociación de Investigacion en Inteligencia Artificial Para la Leucemia Peter Moss Volunteers</a> and community models designed by the <a href="https://github.com/AIIAL" target="_BLANK">Asociación de Investigacion en Inteligencia Artificial Para la Leucemia Peter Moss Github Community</a>.</p>
									<p>&nbsp;</p>
									<p>HIAS Artificial Intelligence Models power HIAS AI Agents which connect the models to the HIAS network.</p>
								</div>
							</div>
						</div>
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">AI Models</h6>
								</div>
								<div class="pull-right"><a href="/AI/Create"><i class="fa fa-plus"></i> Create AI Model</a></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">

									<div class="row scroll_450px">

										<?php
											$models = $AI->get_models();
											if(!isSet($models["Error"])):
												foreach($models as $key => $value):
										?>

										<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
											<div class="panel-wrapper collapse in small" style="background: #333; margin: 5px; padding: 10px; color: #fff;">

												<br /><strong>Name:</strong> <a href="/AI/Model/<?=$value["id"];?>"><?=$value["name"]["value"];?></a><br />
												<strong>Author:</strong> <?=$value["modelAuthor"]["value"];?><br />
												<strong>Category:</strong> <?=$value["category"]["value"][0];?><br />
												<strong>Architecture:</strong> <?=$value["networkArchitecture"]["value"];?><br />
												<strong>Type:</strong> <?=$value["modelType"]["value"];?><br /><br />

												<div class="pull-right small"><strong>Last Updated: <?=$value["dateModified"]["value"]; ?></strong></div>

												<a href="/AI/Model/<?=$value["id"];?>"><i class="fa fa-edit"></i>&nbsp;Edit</a>


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

			</div>

			<?php include dirname(__FILE__) . '/../Includes/Footer.php'; ?>

		</div>

		<?php  include dirname(__FILE__) . '/../Includes/JS.php'; ?>

		<script type="text/javascript" src="/iotJumpWay/Classes/mqttws31.js"></script>
		<script type="text/javascript" src="/iotJumpWay/Classes/iotJumpWay.js"></script>

</body>

</html>