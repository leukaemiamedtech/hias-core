<?php session_start();

$pageDetails = [
    "PageID" => "Diagnosis",
    "SubPageID" => "DCOVID19"
];

include dirname(__FILE__) . '/../../../../Classes/Core/init.php';
include dirname(__FILE__) . '/../../../../Classes/Core/GeniSys.php';
include dirname(__FILE__) . '/../../../Diagnosis/COVID-19/CNN/Classes/COVID19.php';

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
									<h6 class="panel-title txt-dark">COVID-19 Tensorflow DenseNet Classifier</h6>
								</div>
								<div class="pull-right">
									<div class="pull-left inline-block dropdown"></div>
								</div>
								<div class="clearfix"></div>
                            </div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
                                    <p>This system uses the <a href="https://github.com/COVID-19-AI-Research-Project/AI-Classification/tree/master/Projects/2" title="Peter Moss COVID-19 AI Research Project COVID-19 Tensorflow DenseNet Classifier" target="_BLANK">Peter Moss COVID-19 AI Research Project COVID-19 Tensorflow DenseNet Classifier</a> and the <a href="https://www.kaggle.com/plameneduardo/sarscov2-ctscan-dataset" title="SARS-COV-2 Ct-Scan Dataset" target="_BLANK">SARS-COV-2 Ct-Scan Dataset</a>, a large dataset of CT scans for SARS-CoV-2 (COVID-19) identification created by our collaborators, Plamenlancaster: <a href="https://www.lancaster.ac.uk/lira/people/#d.en.397371"target="_BLANK">Professor Plamen Angelov</a> from <a href="https://www.lancaster.ac.uk/"target="_BLANK">Lancaster University</a>/ Centre Director @ <a href="https://www.lancaster.ac.uk/lira/"target="_BLANK">Lira</a>, &amp; his researcher, <a href="https://www.lancaster.ac.uk/sci-tech/about-us/people/eduardo-almeida-soares"target="_BLANK">Eduardo Soares PhD</a>.</p>
                                    <p>&nbsp;</p>

                                    <p>To use this diagnosis system you need to complete the <a href="https://github.com/COVID-19-AI-Research-Project/AI-Classification/tree/master/Projects/2" title="Peter Moss COVID-19 AI Research Project COVID-19 Tensorflow DenseNet Classifier" target="_BLANK">Peter Moss COVID-19 AI Research Project COVID-19 Tensorflow DenseNet Classifier</a> tutorial from our Github account and make sure that the classification server is running and accepting connections.</p>
								</div>
							</div>
						</div>	
					</div>
					<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">SARS-COV-2 Ct-Scan Dataset</h6>
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
                                        $images = glob($COVID19->dataFiles);
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
                                            echo "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><p>Please upload your SARS-COV-2 Ct-Scan Dataset data.</p></div>";
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
        <script type="text/javascript" src="<?=$domain; ?>/Diagnosis/COVID-19/CNN/Classes/COVID19.js"></script>

    </body>

</html>
