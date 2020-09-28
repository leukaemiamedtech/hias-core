<?php session_start();

$pageDetails = [
	"PageID" => "Diagnosis",
	"SubPageID" => "DALL"
];

include dirname(__FILE__) . '/../../../../Classes/Core/init.php';
include dirname(__FILE__) . '/../../../../Classes/Core/GeniSys.php';
include dirname(__FILE__) . '/../../../Detection/ALL/CNN/Classes/ALL.php';

$_GeniSysAi->checkSession();
$stats = $_GeniSysAi->getStats();

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

			<?php include dirname(__FILE__) . '/../../../Includes/Nav.php'; ?>
			<?php include dirname(__FILE__) . '/../../../Includes/LeftNav.php'; ?>
			<?php include dirname(__FILE__) . '/../../../Includes/RightNav.php'; ?>

			<div class="page-wrapper">
			<div class="container-fluid pt-25">

				<?php include dirname(__FILE__) . '/../../../Includes/Stats.php'; ?>

				<div class="row">
					<div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<?php include dirname(__FILE__) . '/../../../Includes/Weather.php'; ?>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
						<div class="panel panel-default card-view">
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<?php include dirname(__FILE__) . '/../../../iotJumpWay/Includes/iotJumpWay.php'; ?>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="panel panel-default card-view">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">HIAS Acute Lymphoblastic Leukemia Detection System</h6>
								</div>
								<div class="pull-right">
									<div class="pull-left inline-block dropdown"></div>
								</div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">

									<p>This system uses the <a href="https://github.com/AMLResearchProject/ALL-Detection-System-2020/tree/master/RPI4" title="Peter Moss Leukemia AI Research Project Tensorflow 2.0 AllDS2020 CNN For Raspberry Pi 4" target="_BLANK">Peter Moss Tensorflow 2.0 AllDS2020 CNN For Raspberry Pi 4</a> and the <a href="https://homes.di.unimi.it/scotti/all/" title="Acute Lymphoblastic Leukemia Image Database for Image Processing" target="_BLANK">Acute Lymphoblastic Leukemia Image Database for Image Processing</a> created by  <a href="https://homes.di.unimi.it/scotti/" target="_BLANK">Fabio Scotti</a>, Associate Professor Dipartimento di Informatica at Università degli Studi di Milano.</p>
									<p>&nbsp;</p>

									<p>To use this diagnosis system you need to complete the <a href="https://github.com/AMLResearchProject/ALL-Detection-System-2020/tree/master/RPI4" title="Peter Moss Tensorflow 2.0 AllDS2020 CNN For Raspberry Pi 4" target="_BLANK">Peter Moss Tensorflow 2.0 AllDS2020 CNN For Raspberry Pi 4</a> tutorial from our Github account and make sure that the classification server is running and accepting connections.</p>
									<p>&nbsp;</p>

									<h6>DISCLAIMER</h6>
									<p>This project should be used for research purposes only. The purpose of the project is to show the potential of Artificial Intelligence for medical support systems such as diagnosis systems. Although the classifier is accurate, and shows good results both on paper and in real world testing, it is not meant to be an alternative to professional medical diagnosis. Developers that have contributed to this project have experience in using Artificial Intelligence for detecting certain types of cancer & COVID-19. They are not a doctors, medical or cancer/COVID-19 experts. Please use this system responsibly.</p>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">Acute Lymphoblastic Leukemia Image Database for Image Processing Dataset</h6>
								</div>
								<div class="pull-right">
									<a href="#" id="uploadData"><i class="fas fa-upload  fa-fw"></i>&nbsp;UPLOAD DATA</a>&nbsp;&nbsp;&nbsp;<a href="#" id="deleteData"><i class="fas fa-trash  fa-fw"></i>&nbsp;DELETE DATA</a>
									<div class="pull-left inline-block dropdown"></div>
								</div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">

									<input type="file" id="dataup" class="hide" accept="image/*" multiple />

									<div class="row" id="dataBlock">

									<?php
										$images = glob($ALL->dataFiles);
										$count = 1;
										if(count($images)):
											foreach( $images as $image ):
												echo "<div class='col-lg-2 col-md-2 col-sm-2 col-xs-2'><img src='" . $image . "' style='width: 100%; cursor: pointer;' class='classify' title='" . $image . "' id='" . $image . "' /></div>";
												if($count%6 == 0):
													echo"<div class='clearfix'></div>";
												endif;
												$count++;
											endforeach;
										else:
											echo "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><p>Please upload your Acute Lymphoblastic Leukemia Image Database for Image Processing data.</p></div>";
										endif;
									?>

									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">Diagnosis Results</h6>
								</div>
								<div class="pull-right">
								</div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">

									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" id="imageView"></div>
									<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
										<strong><span id="imName"></span></strong><br /><br />
										<span id="imClass"></span><br />
										<span id="imConf"></span><br />
										<span id="imResult"></span>
									</div>

								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<?php include dirname(__FILE__) . '/../../../Includes/Footer.php'; ?>

		</div>

		<?php  include dirname(__FILE__) . '/../../../Includes/JS.php'; ?>

		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/mqttws31.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/iotJumpWay.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/Detection/ALL/CNN/Classes/ALL.js"></script>

	</body>

</html>
