<?php session_start();

$pageDetails = [
    "PageID" => "Security",
    "SubPageID" => "TASS"
];

include dirname(__FILE__) . '/../../../Classes/Core/init.php';
include dirname(__FILE__) . '/../../../Classes/Core/GeniSys.php';
include dirname(__FILE__) . '/../../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../../Security/TASS/Classes/TASS.php';

$_GeniSysAi->checkSession();

$Locations = $iotJumpWay->getLocations();
$Zones = $iotJumpWay->getZones();
$MDevices = $iotJumpWay->getMDevices();
$Devices = $iotJumpWay->getDevices();

$TId = filter_input(INPUT_GET, 'tass', FILTER_SANITIZE_NUMBER_INT);
$TDevice = $TASS->getDevice($TId);

list($dev1On, $dev1Off) = $TASS->getStatusShow($TDevice["status"]);
list($lat, $lng) = $TASS->getMapMarkers($TDevice);

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
                                    <h6 class="panel-title txt-dark">TASS Security Camera Device #<?=$TId; ?></h6>
                                </div>
                                <div class="pull-right"></div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div class="form-wrap">
                                        <form data-toggle="validator" role="form" id="tass_update">
                                            <div class="row">
                                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Name</label>
                                                        <input type="text" class="form-control" id="name" name="name"
                                                            placeholder="TASS Device Name" required
                                                            value="<?=$TDevice["name"]; ?>">
                                                        <span class="help-block"> Name of TASS camera device</span>
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
                                                                <?=$TDevice["lid"] == $value["id"] ? " selected " : ""; ?>>
                                                                #<?=$value["id"]; ?>: <?=$value["name"]; ?></option>

                                                            <?php 
                                                                    endforeach;
                                                                endif;
                                                            ?>

                                                        </select>
                                                        <span class="help-block"> Location of TASS camera device</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label mb-10">Zone</label>
                                                        <select class="form-control" id="zid" name="zid" required>
                                                            <option value="">PLEASE SELECT</option>

                                                            <?php 
                                                                if(count($Zones)):
                                                                    foreach($Zones as $key => $value):
                                                            ?>

                                                            <option value="<?=$value["id"]; ?>"
                                                                <?=$TDevice["zid"] == $value["id"] ? " selected " : ""; ?>>
                                                                #<?=$value["id"]; ?>: <?=$value["zn"]; ?></option>

                                                            <?php 
                                                                    endforeach;
                                                                endif;
                                                            ?>

                                                        </select>
                                                        <span class="help-block"> Zone of TASS camera device</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label mb-10">iotJumpWay Device</label>
                                                        <select class="form-control" id="did" name="did" required>
                                                            <option value="">PLEASE SELECT</option>

                                                            <?php 
                                                                if(count($Devices)):
                                                                    foreach($Devices as $key => $value):
                                                            ?>

                                                            <option value="<?=$value["id"]; ?>"
                                                                <?=$TDevice["did"] == $value["id"] ? " selected " : ""; ?>>
                                                                #<?=$value["id"]; ?>: <?=$value["name"]; ?></option>

                                                            <?php 
                                                                    endforeach;
                                                                endif;
                                                            ?>

                                                        </select>
                                                        <span class="help-block"> iotJumpWay device</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">IP</label>
                                                        <input type="text" class="form-control hider" id="ip" name="ip" placeholder="TASS Device IP" required value="<?=$TDevice["ip"] ? $_GeniSys->_helpers->oDecrypt($TDevice["ip"]) : ""; ?>">
                                                        <span class="help-block"> IP of TASS camera device</span>
                                                    </div>
                                                    <div class="form-group mb-0">
                                                        <input type="hidden" class="form-control" id="update_tass" name="update_tass" required value="1">
                                                        <input type="hidden" class="form-control" id="id" name="id" required value="<?=$TDevice["id"]; ?>">
                                                        <button type="submit" class="btn btn-success btn-anim" id="tass_update"><i class="icon-rocket"></i><span class="btn-text">submit</span></button>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">MAC</label>
                                                        <input type="text" class="form-control hider" id="mac" name="mac" placeholder="TASS Device MAC" required value="<?=$TDevice["ip"] ? $_GeniSys->_helpers->oDecrypt($TDevice["ip"]) : ""; ?>">
                                                        <span class="help-block"> MAC of TASS camera device</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Stream Port</label>
                                                        <input type="text" class="form-control hider" id="sport" name="sport" placeholder="TASS Device MAC" required value="<?=$TDevice["sport"] ? $_GeniSys->_helpers->oDecrypt($TDevice["sport"]) : ""; ?>">
                                                        <span class="help-block"> MAC of TASS camera device</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Stream Directory</label>
                                                        <input type="text" class="form-control hider" id="strdir" name="strdir" placeholder="TASS Device Stream Directory" required value="<?=$TDevice["strdir"] ? $_GeniSys->_helpers->oDecrypt($TDevice["strdir"]) : ""; ?>">
                                                        <span class="help-block"> Name of TASS camera stream directory</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Stream File</label>
                                                        <input type="text" class="form-control hider" id="sportf" name="sportf" placeholder="TASS Device Stream File" required value="<?=$TDevice["sportf"] ? $_GeniSys->_helpers->oDecrypt($TDevice["sportf"]) : ""; ?>">
                                                        <span class="help-block"> Name of TASS camera stream file (.mjpg)</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Socket Port</label>
                                                        <input type="text" class="form-control hider" id="sckport" name="sckport" placeholder="TASS Device MAC" required value="<?=$TDevice["sckport"] ? $_GeniSys->_helpers->oDecrypt($TDevice["sckport"]) : ""; ?>">
                                                        <span class="help-block"> Port of TASS camera socket stream</span>
                                                    </div>
                                                </div>
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
								<div class="pull-right"><span id="offline1" style="color: #33F9FF !important;" class="<?=$dev1On; ?>"><i class="fas fa-power-off" style="color: #33F9FF !important;"></i> Online</span> <span id="online1" class="<?=$dev1Off; ?>" style="color: #99A3A4 !important;"><i class="fas fa-power-off" style="color: #99A3A4 !important;"></i> Offline</span></div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Status</label>
                                        <div class="col-md-9">
                                            <i class="fa fa-microchip data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idecpuU"><?=$TDevice["cpu"]; ?></span>% &nbsp;&nbsp;
                                            <i class="zmdi zmdi-memory data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idememU"><?=$TDevice["mem"]; ?></span>% &nbsp;&nbsp;
                                            <i class="far fa-hdd data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idehddU"><?=$TDevice["hdd"]; ?></span>% &nbsp;&nbsp;
                                            <i class="fa fa-thermometer-quarter data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idetempU"><?=$TDevice["tempr"]; ?></span>°C 
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <img src="<?=$domain; ?>/Security/TASS/<?=$_GeniSys->_helpers->oDecrypt($TDevice["strdir"]); ?>/<?=$_GeniSys->_helpers->oDecrypt($TDevice["sportf"]); ?>" style="width: 100%;" />
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
								<div class="pull-right"></div>
                                    <div class="form-group">
                                        <div class="col-md-12">
  									        <div id="map1" class="map" style="height: 300px;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div class="pull-right"><a href="javascipt:void(0)" id="reset_mqtt"><i
                                                class="fa fa-refresh"></i> Reset MQTT Password</a></div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5">MQTT Username</label>
                                        <div class="col-md-9">
                                            <p class="form-control-static" id="mqttut"><?=$_GeniSys->_helpers->oDecrypt($TDevice["mqttu"]); ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5">MQTT Password</label>
                                        <div class="col-md-9">
                                            <p class="form-control-static"><span id="mqttpt"><?=$_GeniSys->_helpers->oDecrypt($TDevice["mqttp"]); ?></span>
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
        <script type="text/javascript" src="<?=$domain; ?>/Security/TASS/Classes/TASS.js"></script>

		<script type="text/javascript">

            $(document).ready(function() {
                TASS.HideInputs();
            });

			function initMap() {

        		var latlng = new google.maps.LatLng("<?=floatval($lat); ?>", "<?=floatval($lng); ?>");
				var map = new google.maps.Map(document.getElementById('map1'), {
					zoom: 10,
					center: latlng
				});

                var loc = new google.maps.LatLng(<?=floatval($lat); ?>, <?=floatval($lng); ?>);
                var marker = new google.maps.Marker({
                    position: loc,
                    map: map,
                    title: 'Device '
                });
            }
            
		</script>
		<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?=$_GeniSys->_helpers->oDecrypt($_GeniSys->_confs["gmaps"]); ?>&callback=initMap"></script>

</body>

</html>