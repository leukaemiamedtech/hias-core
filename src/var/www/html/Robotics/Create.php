<?php session_start();

$pageDetails = [
    "PageID" => "Robotics",
    "SubPageID" => "Create",
    "LowPageID" => "Create"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWayAgents.php';
include dirname(__FILE__) . '/../HIASCDI/Classes/Interface.php';
include dirname(__FILE__) . '/../AI/Classes/AI.php';
include dirname(__FILE__) . '/../Robotics/Classes/Robotics.php';

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
        <meta name="author" content="hencework"/>

        <script src="https://kit.fontawesome.com/58ed2b8151.js" crossorigin="anonymous"></script>

        <link type="image/x-icon" rel="icon" href="/img/favicon.png" />
        <link type="image/x-icon" rel="shortcut icon" href="/img/favicon.png" />
        <link type="image/x-icon" rel="apple-touch-icon" href="/img/favicon.png" />

        <link href="/vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
        <link href="/vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
        <link href="/vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet" type="text/css">
        <link href="/dist/css/style.css" rel="stylesheet" type="text/css">
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
                                        <h6 class="panel-title txt-dark">Create HIAS Robtics Unit</h6>
                                    </div>
                                    <div class="pull-right"></div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="panel-wrapper collapse in">
                                    <div class="panel-body">
                                        <div class="form-wrap">
                                            <form data-toggle="validator" role="form" id="robotics_create_form">
                                                <div class="row">
                                                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="name" class="control-label mb-10">Name</label>
                                                            <input type="text" class="form-control" id="name" name="name" placeholder="Robotics Name" required value="">
                                                            <span class="help-block"> Name of Robtics Unit</span>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label mb-10">Category</label>
                                                            <select class="form-control" id="category" name="category[]" required multiple>
                                                                <option value="">PLEASE SELECT</option>

                                                                <?php
                                                                    $robotics_types = $Robotics->get_robotics_categories();
                                                                    if(count($robotics_types)):
                                                                        foreach($robotics_types as $key => $value):
                                                                ?>

                                                                <option value="<?=$value["category"]; ?>"><?=$value["category"]; ?></option>

                                                                <?php
                                                                        endforeach;
                                                                    endif;
                                                                ?>

                                                            </select>
                                                            <span class="help-block">Robotics device category</span>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label mb-10">IoT Agent</label>
                                                            <select class="form-control" id="agent" name="agent">
                                                                <option value="">PLEASE SELECT</option>

                                                                <?php
                                                                    $agents = $iotJumpWayAgents->get_agents(0, "", "IoT%20Agent");
                                                                    if(!isSet($agents["Error"])):
                                                                        foreach($agents as $key => $value):
                                                                ?>

                                                                <option value="http://<?=$value["ipAddress"]["value"]; ?>:<?=$value["northPort"]["value"]; ?>"><?=$value["name"]["value"]; ?> (http://<?=$value["ipAddress"]["value"]; ?>:<?=$value["northPort"]["value"]; ?>)</option>

                                                                <?php
                                                                        endforeach;
                                                                    endif;
                                                                ?>

                                                            </select>
                                                            <span class="help-block">Device IoT Agent</span>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="name" class="control-label mb-10">Description</label>
                                                            <textarea class="form-control" id="description" name="description" placeholder="Device Description" required></textarea>
                                                            <span class="help-block"> Description of Robtics Unit</span>
                                                        </div>
                                                        <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Unit Brand Name</label>
                                                            <input type="text" class="form-control" id="deviceBrandName" name="deviceBrandName" placeholder="Hardware device name" required value="">
                                                            <span class="help-block">Brand name of Robtics Unit</span>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label mb-10">Unit type</label>
                                                            <select class="form-control" id="deviceModel" name="deviceModel" required>
                                                                <option value="">PLEASE SELECT</option>

                                                                <?php
                                                                    $categories = $Robotics->get_robotics_types();
                                                                    if(count($categories)):
                                                                        foreach($categories as $key => $value):
                                                                ?>

                                                                <option value="<?=$value["r_type"]; ?>"><?=$value["r_type"]; ?></option>

                                                                <?php
                                                                        endforeach;
                                                                    endif;
                                                                ?>

                                                            </select>
                                                            <span class="help-block">Unit type of robotics</span>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="name" class="control-label mb-10">Unit Manufacturer</label>
                                                            <input type="text" class="form-control" id="deviceManufacturer" name="deviceManufacturer" placeholder="Hardware device manufacturer" required value="">
                                                            <span class="help-block">Name of device manufacturer</span>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="name" class="control-label mb-10">Operating System</label>
                                                            <input type="text" class="form-control" id="osName" name="osName" placeholder="Operating system name" required value="">
                                                            <span class="help-block">Operating system name</span>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="name" class="control-label mb-10">Operating system manufacturer</label>
                                                            <input type="text" class="form-control" id="osManufacturer" name="osManufacturer" placeholder="Operating system manufacturer" required value="">
                                                            <span class="help-block">Operating system manufacturer</span>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="name" class="control-label mb-10">Operating system version</label>
                                                            <input type="text" class="form-control" id="osVersion" name="osVersion" placeholder="Operating system version" required value="">
                                                            <span class="help-block">Operating system version</span>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label mb-10">Protocols</label>
                                                            <select class="form-control" id="protocols" name="protocols[]" required multiple>

                                                                <?php
                                                                    $protocols = $HiasInterface->get_protocols();
                                                                    if(count($protocols)):
                                                                        foreach($protocols as $key => $value):
                                                                ?>

                                                                    <option value="<?=$value["protocol"]; ?>"><?=$value["protocol"]; ?></option>

                                                                <?php
                                                                        endforeach;
                                                                    endif;
                                                                ?>

                                                            </select>
                                                            <span class="help-block">Supported Communication Protocols</span>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label mb-10">AI Models</label>
                                                            <select class="form-control" id="ai" name="ai[]" multiple>

                                                                <?php
                                                                    $models = $AI->get_models();
                                                                    if(!isSet($models["Error"])):
                                                                        foreach($models as $key => $value):
                                                                ?>

                                                                    <option value="<?=$value["id"]; ?>"><?=$value["name"]["value"]; ?></option>

                                                                <?php
                                                                        endforeach;
                                                                    endif;
                                                                ?>

                                                            </select>
                                                            <span class="help-block">AI Models</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label class="control-label mb-10">Location</label>
                                                            <select class="form-control" id="lid" name="lid" required>
                                                                <option value="">PLEASE SELECT</option>

                                                                <?php
                                                                    $Locations = $iotJumpWay->get_locations();
                                                                    if(!isSet($Locations["Error"])):
                                                                        foreach($Locations as $key => $value):
                                                                ?>

                                                                    <option value="<?=$value["id"]; ?>"><?=$value["name"]["value"]; ?></option>

                                                                <?php
                                                                        endforeach;
                                                                    endif;
                                                                ?>

                                                            </select>
                                                            <span class="help-block"> Location of Robtics Unit</span>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="name" class="control-label mb-10">Coordinates</label>
                                                            <input type="text" class="form-control" id="coordinates" name="coordinates" placeholder="iotJumpWay Location coordinates" required value="">
                                                            <span class="help-block">Robotics coordinates</span>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="name" class="control-label mb-10">IP</label>
                                                            <input type="text" class="form-control hider" id="ip" name="ip" placeholder="Device IP" required value="">
                                                            <span class="help-block"> IP of Robtics Unit</span>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="name" class="control-label mb-10">MAC</label>
                                                            <input type="text" class="form-control hider" id="mac" name="mac" placeholder="Device MAC" required value="">
                                                            <span class="help-block"> MAC of Robtics Unit</span>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="name" class="control-label mb-10">North Port</label>
                                                            <input type="text" class="form-control hider" id="northPort" name="northPort" placeholder="Device MAC" required value="">
                                                            <span class="help-block"> North Port of Robtics Unit</span>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="name" class="control-label mb-10">IPinfo Key</label>
                                                            <input type="text" class="form-control hider" id="authenticationIpinfoKey" name="authenticationIpinfoKey" placeholder="IPInfo key" required value="" >
                                                            <span class="help-block"><a hef="https://ipinfo.io/" target="_BLANK">IPinfo</a> key</span>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="name" class="control-label mb-10">Bluetooth Address</label>
                                                            <input type="text" class="form-control hider" id="bluetooth" name="bluetooth" placeholder="Device Bluetooth Address"  value="">
                                                            <span class="help-block">Bluetooth address of Robtics Unit</span>
                                                        </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Camera Proxy Endpoint</label>
                                                        <input type="text" class="form-control hider" id="endpoint" name="endpoint" placeholder="Robotics Unit Proxy Endpoint" required value="">
                                                            <span class="help-block"> Name of Robotics Unit camera stream HIAS proxy endpoint (Robotics/EMAR-Mini/Camera/YourENDPOINT)</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Camera Stream Port</label>
                                                        <input type="text" class="form-control hider" id="stream_port" name="stream_port" placeholder="Robotics Unit Stream Port" required value="">
                                                        <span class="help-block"> Port of Robotics Unit camera stream</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Camera Stream File</label>
                                                        <input type="text" class="form-control hider" id="stream_file" name="stream_file" placeholder="Robotics Unit Stream File" required value="">
                                                        <span class="help-block">File name of Robotics Unit camera stream (.mjpg)</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Camera Socket Port</label>
                                                        <input type="text" class="form-control hider" id="socket_port" name="socket_port" placeholder="Robotics Unit MAC" required value="">
                                                        <span class="help-block"> Port of Robotics Unit camera socket stream</span>
                                                    </div>
                                                    </div>
                                                </div>
                                                <div class="form-group mb-0">
                                                    <input type="hidden" class="form-control" id="create_robotics" name="create_robotics" required value=1>
                                                    <button type="submit" class="btn btn-success btn-anim" id="robotics_create"><i class="icon-rocket"></i><span class="btn-text">Create</span></button>
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
        <script type="text/javascript" src="/Robotics/Classes/Robotics.js"></script>

    </body>
</html>