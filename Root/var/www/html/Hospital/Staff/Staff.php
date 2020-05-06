<?php session_start();

$pageDetails = [
    "PageID" => "HIS",
    "SubPageID" => "Staff"
];

include dirname(__FILE__) . '/../../../Classes/Core/init.php';
include dirname(__FILE__) . '/../../../Classes/Core/GeniSys.php';
include dirname(__FILE__) . '/../../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../../Hospital/Staff/Classes/Staff.php';

$_GeniSysAi->checkSession();

$SId = filter_input(INPUT_GET,  'staff', FILTER_SANITIZE_NUMBER_INT);
$Staff = $Staff->getStaff($SId);

$Locations = $iotJumpWay->getLocations();
$Zones = $iotJumpWay->getZones();
$MDevices = $iotJumpWay->getMDevices();
$Applications = $iotJumpWay->getApplications();

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
            
            <?php include dirname(__FILE__) . '/../../Includes/Nav.php'; ?>
            <?php include dirname(__FILE__) . '/../../Includes/LeftNav.php'; ?>
            <?php include dirname(__FILE__) . '/../../Includes/RightNav.php'; ?>

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
									<h6 class="panel-title txt-dark"><i class="fa fa-users"></i> Staff #<?=$SId; ?></h6>
								</div>
								<div class="pull-right"><a href="javascipt:void(0)" id="reset_pass"><i class="fa fa-refresh"></i> Reset Password</a></div>
								<div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div class="form-wrap">
                                        <form data-toggle="validator" role="form" id="tass">
                                            <hr class="light-grey-hr"/>
                                            <div class="form-group clearfix">
                                                <label class="control-label col-md-5">Latitude</label>
                                                <div class="col-md-9">
                                                    <p class="form-control-static"><?=$Staff["lt"]; ?></p>
                                                    <span class="help-block"> Latitude of user</span> 
                                                </div>
                                            </div>
                                            <div class="form-group clearfix">
                                                <label class="control-label col-md-5">Longitude</label>
                                                <div class="col-md-9">
                                                    <p class="form-control-static"><?=$Staff["lg"]; ?></p>
                                                    <span class="help-block"> Longitude of user</span> 
                                                </div>
                                            </div>	
                                            <hr class="light-grey-hr"/>
                                            <div class="form-group">
                                                <label for="name" class="control-label mb-10">Username</label>
                                                <input type="text" class="form-control" id="username" name="username" placeholder="TASS Device Name" required value="<?=$Staff["username"]; ?>">
                                                <span class="help-block"> Username of staff member</span> 
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label mb-10">Location</label>
                                                <select class="form-control" id="lid" name="lid">
                                                
                                                    <?php 
                                                        if(count($Locations)):
                                                            foreach($Locations as $key => $value):
                                                    ?>

                                                    <option value="<?=$value["id"]; ?>" <?=$Staff["lid"] == $value["id"] ? " selected " : ""; ?>>#<?=$value["id"]; ?>: <?=$value["name"]; ?></option>

                                                    <?php 
                                                            endforeach;
                                                        endif;
                                                    ?>

                                                </select>
                                                <span class="help-block"> Location of staff member</span> 
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label mb-10">iotJumpWay Application</label>
                                                <select class="form-control" id="aid" name="aid">
                                                
                                                    <?php 
                                                        if(count($Applications)):
                                                            foreach($Applications as $key => $value):
                                                    ?>

                                                    <option value="<?=$value["id"]; ?>" <?=$Staff["aid"] == $value["id"] ? " selected " : ""; ?>>#<?=$value["id"]; ?>: <?=$value["name"]; ?></option>

                                                    <?php 
                                                            endforeach;
                                                        endif;
                                                    ?>

                                                </select>
                                                <span class="help-block"> iotJumpWay application</span> 
                                            </div>
                                            <div class="form-group">
                                                <label for="name" class="control-label mb-10">Photo</label>
                                                <input type="file" class="form-control" id="photo" name="photo" />
                                                <span class="help-block"> Photo of staff member</span> 
                                            </div>
								            <div class="clearfix"></div>
                                            <hr class="light-grey-hr"/>
                                            <div class="form-group mb-0">
                                                <input type="hidden" class="form-control" id="update_tass" name="update_tass" required value="1">
                                                <input type="hidden" class="form-control" id="id" name="id" required value="<?=$Staff["id"]; ?>">
                                                <button type="submit" class="btn btn-success btn-anim" id="tass_update"><i class="icon-rocket"></i><span class="btn-text">submit</span></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
						</div>
					</div>
					<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div id="map_canvas" style="height:300px;"></div>
                                </div>
                            </div>
                        </div>
					</div>
					<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <img src="<?=$domain; ?>/Team/Media/Images/Uploads/<?=$Staff["pic"];?>" style="width: 100%; !important;" />
                                </div>
                            </div>
                        </div>
					</div>
					<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
								    <div class="pull-right"><a href="javascipt:void(0)" id="reset_staff_mqtt"><i class="fa fa-refresh"></i> Reset MQTT Password</a></div>
                                    <div class="form-group">
										<label class="control-label col-md-5">MQTT Username</label>
                                        <div class="col-md-9">
                                            <p class="form-control-static"><?=$_GeniSys->_helpers->oDecrypt($Staff["mqttu"]); ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
										<label class="control-label col-md-5">MQTT Password</label>
                                        <div class="col-md-9">
                                            <p class="form-control-static"><span id="mqttp"><?=$_GeniSys->_helpers->oDecrypt($Staff["mqttp"]); ?></span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
					</div>
				</div>
				
			</div>
			
			<?php include dirname(__FILE__) . '/../../Includes/Footer.php'; ?>
			
		</div>
        <div id="responsive-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h5 class="modal-title"></h5>
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <?php  include dirname(__FILE__) . '/../../Includes/JS.php'; ?>

        <?php 
            if($Staff["lt"] == ""):
                $coords = "41.54329,2.10942";
            else:
                $coords = $Staff["lt"] . "," . $Staff["lg"];
            endif;

        ?>
        
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

        <script type="text/javascript" src="<?=$domain; ?>/TASS/Classes/TASS.js"></script>

		<script type="text/javascript" src="<?=$domain; ?>/vendors/bower_components/bootstrap-validator/dist/validator.min.js"></script>

        <script type="text/javascript" src="<?=$domain; ?>/Hospital/Staff/Classes/Staff.js"></script>

		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB8shvpI37cre-_3GSguznWalRN2AjYSGc"></script>
		<script>
        
            var settings = {
                zoom: 16,
                center: new google.maps.LatLng(<?=$coords; ?>),
                mapTypeControl: false,
                scrollwheel: false,
                draggable: true,
                panControl:false,
                scaleControl: false,
                zoomControl: false,
                streetViewControl:false,
                navigationControl: false,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                styles: [
                    {
                        "featureType": "landscape.natural.landcover",
                        "elementType": "labels.text.stroke",
                        "stylers": [
                            {
                                "visibility": "on"
                            }
                        ]
                    }
                ]};		
                var map = new google.maps.Map(document.getElementById("map_canvas"), settings);	
                google.maps.event.addDomListener(window, "resize", function() {
                    var center = map.getCenter();
                    google.maps.event.trigger(map, "resize");
                    map.setCenter(center);
                });	
                
                var infowindow = new google.maps.InfoWindow();	
                var companyPos = new google.maps.LatLng(<?=$coords; ?>);	
                var companyMarker = new google.maps.Marker({
                    position: companyPos,
                    map: map,
                    title:"Our Office",
                    zIndex: 3});	
                google.maps.event.addListener(companyMarker, 'click', function() {
                    infowindow.open(map,companyMarker);
                });

        </script>

    </body>

</html>
