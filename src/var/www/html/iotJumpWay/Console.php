<?php session_start();

$pageDetails = [
    "PageID" => "IoT",
    "SubPageID" => "Entities",
    "LowPageID" => "Agents"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWayAgents.php';
include dirname(__FILE__) . '/../HIASCDI/Classes/Interface.php';

$LId = 1;
$Location = $iotJumpWay->get_location($LId);
$Zones = $iotJumpWay->get_zones();

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

        <link href="/dist/css/style.css" rel="stylesheet" type="text/css">
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
                                    <h6 class="panel-title txt-dark">Communicate With iotJumpWay Location Devices</h6>
                                </div>
                                <div class="pull-right"></div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div class="form-wrap">
                                        <form data-toggle="validator" role="form" id="iotJumpWay_console">
                                            <div class="row">
                                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                                    <div class="form-group">
                                                        <label class="control-label mb-10">Device</label>
                                                        <select class="form-control" id="did" name="did" required>
                                                            <option value="">PLEASE SELECT</option>

                                                            <?php
                                                                $devices = $iotJumpWay->get_devices();
                                                                if(!isSet($devices["Error"])):
                                                                    foreach($devices as $key => $value):
                                                            ?>

                                                            <option value="<?=$value["id"]; ?>"><?=$value["name"]["value"];?></option>

                                                            <?php
                                                                    endforeach;
                                                                endif;
                                                            ?>

                                                        </select>
                                                        <span class="help-block">Device to send command to</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label mb-10">Zone</label>
                                                        <select class="form-control" id="zid" name="zid" required>
                                                            <option value="">PLEASE SELECT</option>
                                                            <?php
                                                                $Zones = $iotJumpWay->get_zones();
                                                                if(!isSet($Locations["Error"])):
                                                                    foreach($Zones as $key => $value):
                                                            ?>

                                                            <option value="<?=$value["id"]; ?>"><?=$value["name"]["value"]; ?></option>

                                                            <?php
                                                                    endforeach;
                                                                endif;
                                                            ?>

                                                        </select>
                                                        <span class="help-block"> Zone of device</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Name</label>
                                                        <input type="text" class="form-control" id="name" name="name" required>
                                                        <span class="help-block">Device command Name</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Type</label>
                                                        <input type="text" class="form-control" id="type" name="type" required>
                                                        <span class="help-block">Device command Type</span>
                                                    </div>
                                                    <div class="form-group mb-0">

                                                        <button type="submit" class="btn btn-success btn-anim" id="device_update"><i class="icon-rocket"></i><span class="btn-text">Send</span></button>
                                                    </div>

                                                </div>
                                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Value</label>
                                                        <input type="text" class="form-control" id="value" name="value" required>
                                                        <span class="help-block">Device command Value</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Message</label>
                                                        <input type="text" class="form-control" id="message" name="message" required>
                                                        <span class="help-block">Device command Message</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
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
        <script type="text/javascript" src="/iotJumpWay/Classes/iotJumpWayUI.js"></script>

    </body>

</html>
