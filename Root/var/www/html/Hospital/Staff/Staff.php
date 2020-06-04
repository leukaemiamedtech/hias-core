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

$Locations = $iotJumpWay->getLocations(0, "id ASC");
$Zones = $iotJumpWay->getZones(0, "id ASC");
$MDevices = $iotJumpWay->getMDevices(0, "id ASC");
$Applications = $iotJumpWay->getApplications(0, "id ASC");

$SId = filter_input(INPUT_GET,  'staff', FILTER_SANITIZE_NUMBER_INT);
$Staffer = $Staff->getStaff($SId);

list($lat, $lng) = $Staff->getMapMarkers($Staffer);
list($on, $off) = $Staff->getStatusShow($Staffer["status"]);

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

    <link href="<?=$domain; ?>/vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet"
        type="text/css" />
    <link href="<?=$domain; ?>/vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet"
        type="text/css" />
    <link href="<?=$domain; ?>/vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet"
        type="text/css">
    <link href="<?=$domain; ?>/dist/css/style.css" rel="stylesheet" type="text/css">
    <link href="<?=$domain; ?>/GeniSysAI/Media/CSS/GeniSys.css" rel="stylesheet" type="text/css">
    <link href="<?=$domain; ?>/vendors/bower_components/fullcalendar/dist/fullcalendar.css" rel="stylesheet"
        type="text/css" />
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
                                    <h6 class="panel-title txt-dark"><i class="fa fa-users"></i> Hospital Staff #<?=$SId; ?></h6>
                                </div>
                                <div class="pull-right"><a href="javascipt:void(0)" id="reset_pass"><i
                                            class="fa fa-refresh"></i> Reset Password</a></div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div class="form-wrap">
                                        <form data-toggle="validator" role="form" id="staff_update">
                                            <hr class="light-grey-hr" />
                                            <div class="row">
                                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Username</label>
                                                        <input type="text" class="form-control" id="username" name="username" placeholder="TASS Device Name" required value="<?=$Staffer["username"]; ?>">
                                                        <span class="help-block"> Username of staff member</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label mb-10">Location</label>
                                                        <select class="form-control" id="lid" name="lid" required>
                                                            <option value="">PLEASE SELECT</option>

                                                            <?php 
                                                                if(count($Locations)):
                                                                    foreach($Locations as $key => $value):
                                                            ?>

                                                            <option value="<?=$value["id"]; ?>"
                                                                <?=$Staffer["lid"] == $value["id"] ? " selected " : ""; ?>>
                                                                #<?=$value["id"]; ?>: <?=$value["name"]; ?></option>

                                                            <?php 
                                                                    endforeach;
                                                                endif;
                                                            ?>

                                                        </select>
                                                        <span class="help-block"> Location of staff member</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label mb-10">iotJumpWay Application</label>
                                                        <select class="form-control" id="aid" name="aid" required>
                                                            <option value="">PLEASE SELECT</option>

                                                            <?php 
                                                                if(count($Applications)):
                                                                    foreach($Applications as $key => $value):
                                                            ?>

                                                            <option value="<?=$value["id"]; ?>"
                                                                <?=$Staffer["aid"] == $value["id"] ? " selected " : ""; ?>>
                                                                #<?=$value["id"]; ?>: <?=$value["name"]; ?></option>

                                                            <?php 
                                                                    endforeach;
                                                                endif;
                                                            ?>

                                                        </select>
                                                        <span class="help-block"> iotJumpWay application</span>
                                                    </div>
                                                    <div class="form-group mb-0">
                                                        <input type="hidden" class="form-control" id="update_staff" name="update_staff" required value="1">
                                                        <input type="hidden" class="form-control" id="id" name="id" required value="<?=$Staffer["id"]; ?>">
                                                        <button type="submit" class="btn btn-success btn-anim"><i class="icon-rocket"></i><span class="btn-text">submit</span></button>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Photo</label>
                                                        <input type="file" class="form-control" id="photo" name="photo" />
                                                        <span class="help-block"> Photo of staff member</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Is Admin:</label>
                                                        <input type="checkbox" class="" id="admin" name="admin" value="1" <?=$Staffer["admin"] ? " checked " : ""; ?>>
                                                        <span class="help-block"> Is staff member an admin?</span>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <hr class="light-grey-hr" />
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">Staff Application #<?=$Staffer["aid"]; ?></h6>
								</div>
								<div class="pull-right"><span id="offline3" style="color: #33F9FF !important;" class="<?=$on; ?>"><i class="fas fa-power-off" style="color: #33F9FF !important;"></i> Online</span> <span id="online3" class="<?=$off; ?>" style="color: #99A3A4 !important;"><i class="fas fa-power-off" style="color: #99A3A4 !important;"></i> Offline</span></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<i class="fa fa-microchip data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="usrcpu"><?=$Staffer["cpu"]; ?></span>% &nbsp;&nbsp;
									<i class="zmdi zmdi-memory data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="usrmem"><?=$Staffer["mem"]; ?></span>% &nbsp;&nbsp;
									<i class="far fa-hdd data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="userhdd"><?=$Staffer["hdd"]; ?></span>% &nbsp;&nbsp;
									<i class="fa fa-thermometer-quarter data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="usrtempr"><?=$Staffer["tempr"]; ?></span>°C 
								</div>
							</div>
						</div>
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <img src="<?=$domain; ?>/Team/Media/Images/Uploads/<?=$Staffer["pic"];?>" style="width: 100%; !important;" />
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div id="map1" style="height:300px;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div class="pull-right"><a href="javascipt:void(0)" id="reset_staff_mqtt"><i
                                                class="fa fa-refresh"></i> Reset MQTT Password</a></div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5">MQTT Username</label>
                                        <div class="col-md-9">
                                            <p class="form-control-static" id="usrmqttu"><?=$_GeniSys->_helpers->oDecrypt($Staffer["mqttu"]); ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5">MQTT Password</label>
                                        <div class="col-md-9">
                                            <p class="form-control-static"><span id="usrmqttp"><?=$_GeniSys->_helpers->oDecrypt($Staffer["mqttp"]); ?></span>
                                            </p>
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

        <?php  include dirname(__FILE__) . '/../../Includes/JS.php'; ?>

        <script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/mqttws31.js"></script>
        <script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/iotJumpWay.js"></script>

        <script type="text/javascript" src="<?=$domain; ?>/Hospital/Staff/Classes/Staff.js"></script>
        <script type="text/javascript">

            $(document).ready(function() {
                Staff.HideInputs();
            });
            
			function initMap() {

        		var latlng = new google.maps.LatLng("<?=floatval($lat); ?>", "<?=floatval($lng); ?>");
				var map = new google.maps.Map(document.getElementById('map1'), {
					zoom: 10,
					center: latlng
                });
                
                var marker = new google.maps.Marker({
                    position: latlng,
                    map: map,
                    title: 'Approximate location'
                });
            }
            
		</script>
		<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?=$_GeniSys->_helpers->oDecrypt($_GeniSys->_confs["gmaps"]); ?>&callback=initMap"></script>

    </body>
</html>