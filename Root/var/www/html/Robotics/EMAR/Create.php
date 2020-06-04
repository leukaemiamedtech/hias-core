<?php session_start();

$pageDetails = [
    "PageID" => "Robotics",
    "SubPageID" => "EMAR"
];

include dirname(__FILE__) . '/../../../Classes/Core/init.php';
include dirname(__FILE__) . '/../../../Classes/Core/GeniSys.php';
include dirname(__FILE__) . '/../../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../../Robotics/EMAR/Classes/EMAR.php';

$_GeniSysAi->checkSession();

$Locations = $iotJumpWay->getLocations(0, "id ASC");
$Zones = $iotJumpWay->getZones(0, "id ASC");
$MDevices = $iotJumpWay->getMDevices(0, "id ASC");
$Devices = $iotJumpWay->getDevices(0, "id ASC");

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
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-heading">
                                <div class="pull-left">
                                    <h6 class="panel-title txt-dark">Create EMAR Robotic Unit</h6>
                                </div>
                                <div class="pull-right"></div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div class="form-wrap">
                                        <form data-toggle="validator" role="form" id="emar_create">
                                            <div class="row">
                                                <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">

                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Device 1 Name</label>
                                                        <input type="text" class="form-control" id="name" name="name"
                                                            placeholder="EMAR Device Name" required value="">
                                                        <span class="help-block"> Name of EMAR device 1</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">IP</label>
                                                        <input type="text" class="form-control" id="ip" name="ip"
                                                            placeholder="EMAR Device IP" required value="">
                                                        <span class="help-block"> IP of EMAR device 1</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">MAC</label>
                                                        <input type="text" class="form-control" id="mac" name="mac"
                                                            placeholder="EMAR Device MAC" required value="">
                                                        <span class="help-block"> MAC of EMAR device 1</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Stream Port</label>
                                                        <input type="text" class="form-control" id="sport" name="sport"
                                                            placeholder="EMAR Device Stream Port"  value="">
                                                        <span class="help-block"> Stream port of EMAR device 1</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Stream File</label>
                                                        <input type="text" class="form-control" id="sportf" name="sportf"
                                                            placeholder="EMAR Stream File"  value="">
                                                        <span class="help-block"> Stream file of EMAR device 1</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Socket Port</label>
                                                        <input type="text" class="form-control" id="sckport" name="sckport"
                                                            placeholder="EMAR Device Socket Port"  value="">
                                                        <span class="help-block"> Socket port of EMAR device 1</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label mb-10">Location</label>
                                                        <select class="form-control" id="lid" name="lid" required>
                                                            <option value="">PLEASE SELECT</option>

                                                            <?php 
                                                                if(count($Locations)):
                                                                    foreach($Locations as $key => $value):
                                                            ?>

                                                            <option value="<?=$value["id"]; ?>">#<?=$value["id"]; ?>:
                                                                <?=$value["name"]; ?></option>

                                                            <?php 
                                                                    endforeach;
                                                                endif;
                                                            ?>

                                                        </select>
                                                        <span class="help-block"> Location of EMAR device</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label mb-10">Zone</label>
                                                        <select class="form-control" id="zid" name="zid" required>
                                                            <option value="">PLEASE SELECT</option>

                                                            <?php 
                                                                if(count($Zones)):
                                                                    foreach($Zones as $key => $value):
                                                            ?>

                                                            <option value="<?=$value["id"]; ?>">#<?=$value["id"]; ?>:
                                                                <?=$value["zn"]; ?></option>

                                                            <?php 
                                                                    endforeach;
                                                                endif;
                                                            ?>

                                                        </select>
                                                        <span class="help-block"> Zone of EMAR device</span>
                                                    </div>
                                                    <div class="form-group mb-0">
                                                        <input type="hidden" class="form-control" id="create_emar" name="create_emar" required value="1">
                                                        <button type="submit" class="btn btn-success btn-anim"><i class="icon-rocket"></i><span class="btn-text">submit</span></button>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">

                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Device 2 Name</label>
                                                        <input type="text" class="form-control" id="name2" name="name2"
                                                            placeholder="EMAR Device Name" required value="">
                                                        <span class="help-block"> Name of EMAR device 2</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Device 2 IP</label>
                                                        <input type="text" class="form-control" id="ip2" name="ip2"
                                                            placeholder="EMAR Device IP" required value="">
                                                        <span class="help-block"> IP of EMAR device 2</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Device 2 MAC</label>
                                                        <input type="text" class="form-control" id="mac2" name="mac2"
                                                            placeholder="EMAR Device MAC" required value="">
                                                        <span class="help-block"> MAC of EMAR device 2</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Device 2 Stream Port</label>
                                                        <input type="text" class="form-control" id="sport2" name="sport2"
                                                            placeholder="EMAR Device Stream Port" required value="">
                                                        <span class="help-block"> Stream port of EMAR device 2</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Device 2 Stream File</label>
                                                        <input type="text" class="form-control" id="sportf2" name="sportf2"
                                                            placeholder="EMAR Stream File" required value="">
                                                        <span class="help-block"> Stream file of EMAR device 2</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Device 2 Socket Port</label>
                                                        <input type="text" class="form-control" id="sckport2" name="sckport2"
                                                            placeholder="EMAR Device Socket Port" required value="">
                                                        <span class="help-block"> Socket port of EMAR device 2</span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">

                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Device 3 Name</label>
                                                        <input type="text" class="form-control" id="name3" name="name3"
                                                            placeholder="EMAR Device Name" required value="">
                                                        <span class="help-block"> Name of EMAR device 3</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Device 3 IP</label>
                                                        <input type="text" class="form-control" id="ip3" name="ip3"
                                                            placeholder="EMAR Device IP" required value="">
                                                        <span class="help-block"> IP of EMAR device 3</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Device 3 MAC</label>
                                                        <input type="text" class="form-control" id="mac3" name="mac3"
                                                            placeholder="EMAR Device MAC" required value="">
                                                        <span class="help-block"> MAC of EMAR device 3</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Device 3 Stream Port</label>
                                                        <input type="text" class="form-control" id="sport3" name="sport3"
                                                            placeholder="EMAR Device Stream Port" required value="">
                                                        <span class="help-block"> Stream port of EMAR device 3</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Device 3 Stream File</label>
                                                        <input type="text" class="form-control" id="sportf3" name="sportf3"
                                                            placeholder="EMAR Stream File" required value="">
                                                        <span class="help-block"> Stream file of EMAR device 3</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Device 3 Socket Port</label>
                                                        <input type="text" class="form-control" id="sckport3" name="sckport3"
                                                            placeholder="EMAR Device Socket Port" required value="">
                                                        <span class="help-block"> Socket port of EMAR device 3</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">

                                                </div>
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

            <?php include dirname(__FILE__) . '/../../Includes/Footer.php'; ?>

        </div>

        <?php  include dirname(__FILE__) . '/../../Includes/JS.php'; ?>

		<script type="text/javascript" src="<?=$domain; ?>/Robotics/EMAR/Classes/EMAR.js"></script>
        <script type="text/javascript">
        </script>

    </body>
</html>