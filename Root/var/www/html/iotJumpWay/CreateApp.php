<?php session_start();

$pageDetails = [
    "PageID" => "IoT",
    "SubPageID" => "IoT",
    "LowPageID" => "Application"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../../Classes/Core/GeniSys.php';
include dirname(__FILE__) . '/../TASS/Classes/TASS.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';

$_GeniSysAi->checkSession();

$Locations = $iotJumpWay->getLocations();

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

				<div class="row">
					<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
						<div class="panel panel-default card-view pa-0 bg-gradient3">
							<div class="panel-wrapper collapse in">
								<div class="panel-body pa-0">
									<div class="sm-data-box">
										<div class="container-fluid">
											<div class="row">
												<div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
													<span class="txt-light block counter"><span class=""><?=$stats["CPU"]; ?>%</span></span>
													<span class="weight-500 uppercase-font block font-13 txt-light">CPU</span>
												</div>
												<div class="col-xs-6 text-center  pl-0 pr-0 pt-25 data-wrap-right">
													<div id="sparkline_4" class="sp-small-chart" ></div>
												</div>
											</div>	
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
						<div class="panel panel-default card-view pa-0 bg-gradient3">
							<div class="panel-wrapper collapse in">
								<div class="panel-body pa-0">
									<div class="sm-data-box">
										<div class="container-fluid">
											<div class="row">
												<div class="col-xs-6 text-center pl-0 pr-0 txt-light data-wrap-left">
													<span class="block counter"><span class=""><?=$stats["Memory"]; ?>%</span></span>
													<span class="weight-500 uppercase-font block">Memory</span>
												</div>
												<div class="col-xs-6 text-center  pl-0 pr-0 txt-light data-wrap-right">
													<i class=" zmdi zmdi-memory data-right-rep-icon"></i>
												</div>
											</div>	
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
						<div class="panel panel-default card-view pa-0 bg-gradient3">
							<div class="panel-wrapper collapse in">
								<div class="panel-body pa-0">
									<div class="sm-data-box">
										<div class="container-fluid">
											<div class="row">
												<div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
													<span class="txt-light block counter"><span class="counter-anim">46.43</span>%</span>
													<span class="weight-500 uppercase-font block txt-light">Swap</span>
												</div>
												<div class="col-xs-6 text-center  pl-0 pr-0  data-wrap-right">
													<i class="zmdi zmdi-refresh-alt  data-right-rep-icon txt-light"></i>
												</div>
											</div>	
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
						<div class="panel panel-default card-view pa-0 bg-gradient3">
							<div class="panel-wrapper collapse in">
								<div class="panel-body pa-0">
									<div class="sm-data-box">
										<div class="container-fluid">
											<div class="row">
												<div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
													<span class="txt-light block counter"><span class=""><?=$stats["Temperature"]; ?></span></span>
													<span class="weight-500 uppercase-font block txt-light">Temp</span>
												</div>
												<div class="col-xs-6 text-center  pl-0 pr-0 data-wrap-right">
													<i class="fa fa-thermometer-quarter data-right-rep-icon txt-light" aria-hidden="true"></i>
												</div>
											</div>	
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
                </div>
                
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
									<h6 class="panel-title txt-dark">Create iotJumpWay Application</h6>
								</div>
								<div class="pull-right"></div>
								<div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div class="form-wrap">
                                        <form data-toggle="validator" role="form" id="form">
                                            <hr class="light-grey-hr"/>
                                            <div class="form-group">
                                                <label for="name" class="control-label mb-10">Name</label>
                                                <input type="text" class="form-control" id="name" name="name" placeholder="Application Name" required value="">
                                                <span class="help-block"> Name of application</span> 
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label mb-10">Location</label>
                                                <select class="form-control" id="lid" name="lid">
                                                
                                                    <?php 
                                                        if(count($Locations)):
                                                            foreach($Locations as $key => $value):
                                                    ?>

                                                    <option value="<?=$value["id"]; ?>">#<?=$value["id"]; ?>: <?=$value["name"]; ?></option>

                                                    <?php 
                                                            endforeach;
                                                        endif;
                                                    ?>

                                                </select>
                                                <span class="help-block"> Location of application</span> 
                                            </div>
                                            <div class="form-group">
                                                <label for="name" class="control-label mb-10">IP</label>
                                                <input type="text" class="form-control" id="ip" name="ip" placeholder="Application IP" required value="">
                                                <span class="help-block"> IP of application</span> 
                                            </div>
                                            <div class="form-group">
                                                <label for="name" class="control-label mb-10">MAC</label>
                                                <input type="text" class="form-control" id="mac" name="mac" placeholder="Application MAC" required value="">
                                                <span class="help-block"> MAC of application</span> 
                                            </div>
                                            <div class="form-group">
                                                <label for="name" class="control-label mb-10">Latitude</label>
                                                <input type="text" class="form-control" id="lt" name="lt" placeholder="Application Latitude" required value="">
                                                <span class="help-block"> Latitude of application</span> 
                                            </div>
                                            <div class="form-group">
                                                <label for="name" class="control-label mb-10">Longitude</label>
                                                <input type="text" class="form-control" id="lg" name="lg" placeholder="Application Longitude" required value="">
                                                <span class="help-block"> Longitude of application</span> 
                                            </div>
								            <div class="clearfix"></div>
                                            <hr class="light-grey-hr"/>
                                            <div class="form-group mb-0">
                                                <input type="hidden" class="form-control" id="create_application" name="create_application" required value="1">
                                                <button type="submit" class="btn btn-success btn-anim" id="application_create"><i class="icon-rocket"></i><span class="btn-text">submit</span></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
						</div>
					</div>
					<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
					</div>
				</div>
				
			</div>
			
			<?php include dirname(__FILE__) . '/../Includes/Footer.php'; ?>
			
		</div>

        <?php  include dirname(__FILE__) . '/../Includes/JS.php'; ?>
        
        <script type="text/javascript" src="<?=$domain; ?>/vendors/bower_components/moment/min/moment.min.js"></script>
        <script type="text/javascript" src="<?=$domain; ?>/vendors/bower_components/simpleWeather/jquery.simpleWeather.min.js"></script>
        <script type="text/javascript" src="<?=$domain; ?>/dist/js/simpleweather-data.js"></script>
        
        <script type="text/javascript" src="<?=$domain; ?>/vendors/bower_components/waypoints/lib/jquery.waypoints.min.js"></script>
        <script type="text/javascript" src="<?=$domain; ?>/vendors/bower_components/jquery.counterup/jquery.counterup.min.js"></script>
        
        <script type="text/javascript" src="<?=$domain; ?>/dist/js/dropdown-bootstrap-extended.js"></script>
        
        <script type="text/javascript" src="<?=$domain; ?>/vendors/jquery.sparkline/dist/jquery.sparkline.min.js"></script>
        
        <script type="text/javascript" src="<?=$domain; ?>/vendors/bower_components/owl.carousel/dist/owl.carousel.min.js"></script>
        
        <script type="text/javascript" src="<?=$domain; ?>/vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.js"></script>
        
        <script type="text/javascript" src="<?=$domain; ?>/vendors/bower_components/echarts/dist/echarts-en.min.js"></script>
        <script type="text/javascript" src="<?=$domain; ?>/vendors/echarts-liquidfill.min.js"></script>
        
        <script type="text/javascript" src="<?=$domain; ?>/vendors/bower_components/switchery/dist/switchery.min.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/vendors/bower_components/fullcalendar/dist/fullcalendar.min.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/dist/js/fullcalendar-data.js"></script>
        
        <script type="text/javascript" src="<?=$domain; ?>/dist/js/init.js"></script>
        <script type="text/javascript" src="<?=$domain; ?>/dist/js/dashboard-data.js"></script>

        <script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/mqttws31.js"></script>
        <script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/iotJumpWay.js"></script>
        <script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/iotJumpWayUI.js"></script>

        <script type="text/javascript" src="<?=$domain; ?>/TASS/Classes/TASS.js"></script>

		<script type="text/javascript" src="<?=$domain; ?>/vendors/bower_components/bootstrap-validator/dist/validator.min.js"></script>

        <script type="text/javascript" src="<?=$domain; ?>/Hospital/Staff/Classes/Staff.js"></script>

    </body>

</html>
