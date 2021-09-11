<?php session_start();

$pageDetails = [
    "PageID" => "IoT",
    "SubPageID" => "Entities",
    "LowPageID" => "Devices"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWayAgents.php';
include dirname(__FILE__) . '/../HIASCDI/Classes/Interface.php';
include dirname(__FILE__) . '/../AI/Classes/AI.php';

$lid = filter_input(INPUT_GET, 'location', FILTER_SANITIZE_STRING);
$zid = filter_input(INPUT_GET, 'zone', FILTER_SANITIZE_STRING);
$did = filter_input(INPUT_GET, 'device', FILTER_SANITIZE_STRING);

$device = $iotJumpWay->get_device($did, "dateCreated,dateModified,*");

$cancelled = $device["permissionsCancelled"]["value"] ? True : False;

list($dev1On, $dev1Off) = $iotJumpWay->get_device_status($device["networkStatus"]["value"]);

$data = $iotJumpWay->device_life_graph($did, 100);

$cpu = [];
$memory = [];
$diskspace = [];
$temperature = [];
$dates = [];

if(isSet($data["ResponseData"])):
    foreach($data["ResponseData"] AS $key => $value):
        if(isSet($value["Data"])):
            $cpu[] = $value["Data"]["CPU"];
            $memory[] = $value["Data"]["Memory"];
            $diskspace[] = $value["Data"]["Diskspace"];
            $temperature[] = $value["Data"]["Temperature"];
            $dates[] = $value["Time"];
        endif;
    endforeach;
endif;

$dates = array_reverse($dates);

$points = [[
    "name" => "CPU",
    "data" => array_reverse($cpu),
    "type" => 'line',
    "smooth" => true,
    "color" => ['orange']
],
[
    "name" => "Memory",
    "data" => array_reverse($memory),
    "type" => 'line',
    "smooth" => true,
    "color" => ['yellow']
],
[
    "name" => "Diskspace",
    "data" => array_reverse($diskspace),
    "type" => 'line',
    "smooth" => true,
    "color" => ['red']
],
[
    "name" => "Temperature",
    "data" => array_reverse($temperature),
    "type" => 'line',
    "smooth" => true,
    "color" => ['purple']
]];
$legend = ["CPU","Memory","Diskspace","Temperature"];

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
                                    <h6 class="panel-title txt-dark">Device Resource Monitor</h6>
                                </div>
                                <div class="pull-right">
                                    <select class="form-control" id="deviceGraphs" name="deviceGraphs" required>
                                        <option value="Life">Life Monitor</option>
                                        <option value="Sensors">Sensors Monitor</option>

                                    </select>
                                    <div class="pull-left inline-block dropdown">
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div id="e_chart_1" class="" style="height: 375px;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-heading">
                                <div class="pull-left">
                                    <h6 class="panel-title txt-dark">HIAS Device</h6>
                                </div>
                                <div class="pull-right"><a href="/iotJumpWay/<?=$lid; ?>/Zones/<?=$zid; ?>/Devices/<?=$did; ?>/Configuration" download><i class="fas fa-download"></i> Device Configuration</a></div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">

                                    <div class="form-wrap">
                                        <form data-toggle="validator" role="form" id="device_update">
                                            <div class="row">
                                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Name</label>
                                                        <input type="text" class="form-control" id="name" name="name" placeholder="Device Name" required value="<?=$device["name"]["value"]; ?>">
                                                        <span class="help-block"> Name of device</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Description</label>
                                                        <input type="text" class="form-control" id="description" name="description" placeholder="Device Description" required value="<?=$device["description"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block"> Description of device</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label mb-10">Category</label>
                                                        <select class="form-control" id="category" name="category[]" required multiple>
                                                            <option value="">PLEASE SELECT</option>

                                                            <?php
                                                                $categories = $iotJumpWay->get_device_categories();
                                                                if(count($categories)):
                                                                    foreach($categories as $key => $value):
                                                            ?>

                                                            <option value="<?=$value["category"]; ?>" <?=in_array($value["category"], $device["category"]["value"]) ? " selected " : ""; ?>><?=$value["category"]; ?></option>

                                                            <?php
                                                                    endforeach;
                                                                endif;
                                                            ?>

                                                        </select>
                                                        <span class="help-block">Device categories</span>
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

                                                            <option value="http://<?=$value["ipAddress"]["value"]; ?>:<?=$value["northPort"]["value"]; ?>" <?=$device["agent"]["value"] == "http://" . $value["ipAddress"]["value"] . ":" . $value["northPort"]["value"] ? " selected " : ""; ?>><?=$value["name"]["value"]; ?> (http://<?=$value["ipAddress"]["value"]; ?>:<?=$value["northPort"]["value"]; ?>)</option>

                                                            <?php
                                                                    endforeach;
                                                                endif;
                                                            ?>

                                                        </select>
                                                        <span class="help-block">Device IoT Agent</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">IoT Type</label><br />
                                                        Input: <input type="radio" id="iot" name="iot" value="Input" <?=$device["iotType"]["value"] == "Input" ? " checked " : ""; ?>> Output: <input type="radio" id="iot" name="iot" value="Output" <?=$device["iotType"]["value"] == "Output" ? " checked " : ""; ?>> Dual: <input type="radio" id="iot" name="iot" value="Dual" <?=$device["iotType"]["value"] == "Dual" ? " checked " : ""; ?>> NA: <input type="radio" id="iot" name="iot" value="NA" <?=$device["iotType"]["value"] == "NA" ? " checked " : ""; ?>>
                                                        <span class="help-block">Device IoT type</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label mb-10">Technologies</label>
                                                        <select class="form-control" id="technologies" name="technologies[]" multiple required>
                                                            <option value="">PLEASE SELECT</option>

                                                            <?php
                                                                $technologies = $iotJumpWay->get_technologies();
                                                                if(count($technologies)):
                                                                    foreach($technologies as $key => $value):
                                                            ?>

                                                            <option value="<?=$value["technology"]; ?>" <?=in_array($value["technology"], $device["technologies"]["value"]) ? " selected " : ""; ?>><?=$value["technology"]; ?></option>

                                                            <?php
                                                                    endforeach;
                                                                endif;
                                                            ?>

                                                        </select>
                                                        <span class="help-block">Technologies used with this device</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Device Brand Name</label>
                                                        <input type="text" class="form-control" id="deviceName" name="deviceName" placeholder="Hardware device name" required value="<?=$device["brandName"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block">Brand name of device</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label mb-10">Device Model</label>
                                                        <select class="form-control" id="deviceModel" name="deviceModel" required>
                                                            <option value="">PLEASE SELECT</option>

                                                            <?php
                                                                $categories = $iotJumpWay->get_device_models();
                                                                if(count($categories)):
                                                                    foreach($categories as $key => $value):
                                                            ?>

                                                            <option value="<?=$value["category"]; ?>" <?=$value["category"] == $device["modelName"]["value"] ? " selected " : ""; ?>><?=$value["category"]; ?></option>

                                                            <?php
                                                                    endforeach;
                                                                endif;
                                                            ?>

                                                        </select>
                                                        <span class="help-block">Model of device</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Device Manufacturer</label>
                                                        <input type="text" class="form-control" id="deviceManufacturer" name="deviceManufacturer" placeholder="Hardware device manufacturer" required value="<?=$device["manufacturerName"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block">Name of device manufacturer</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Operating System</label>
                                                        <input type="text" class="form-control" id="osName" name="osName" placeholder="Operating system name" required value="<?=$device["os"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block">Operating system name</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Operating system manufacturer</label>
                                                        <input type="text" class="form-control" id="osManufacturer" name="osManufacturer" placeholder="Operating system manufacturer" required value="<?=$device["osManufacturer"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block">Operating system manufacturer</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Operating system version</label>
                                                        <input type="text" class="form-control" id="osVersion" name="osVersion" placeholder="Operating system version" required value="<?=$device["osVersion"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block">Operating system version</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Software</label>
                                                        <input type="text" class="form-control" id="softwareName" name="softwareName" placeholder="Software" required value="<?=$device["software"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block">HIAS software</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Software Version</label>
                                                        <input type="text" class="form-control" id="softwareVersion" name="softwareVersion" placeholder="Software Version" required value="<?=$device["softwareVersion"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block">HIAS software version</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Software Manufacturer</label>
                                                        <input type="text" class="form-control" id="softwareManufacturer" name="softwareManufacturer" placeholder="Software Manufacturer" required value="<?=$device["softwareManufacturer"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block">HIAS software manufacturer</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label mb-10">Protocols</label>
                                                        <select class="form-control" id="protocols" name="protocols[]" required multiple>

                                                            <?php
                                                                $protocols = $HiasInterface->get_protocols();
                                                                if(count($protocols)):
                                                                    foreach($protocols as $key => $value):
                                                            ?>

                                                                <option value="<?=$value["protocol"]; ?>" <?=in_array($value["protocol"], $device["protocols"]["value"]) ? " selected " : ""; ?>><?=$value["protocol"]; ?></option>

                                                            <?php
                                                                    endforeach;
                                                                endif;
                                                            ?>

                                                        </select>
                                                        <span class="help-block">Supported Communication Protocols</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label mb-10">Sensors</label>
                                                        <select class="form-control" id="sensorSelect">
                                                            <option value="">Select Sensors To Add</option>

                                                            <?php
                                                                $sensors = $iotJumpWay->get_things(0, "sensor");
                                                                if(!isset($sensors["Error"])):
                                                                    foreach($sensors as $key => $value):
                                                            ?>

                                                                <option value="<?=$value["id"]; ?>"><?=$value["name"]["value"]; ?></option>

                                                            <?php
                                                                    endforeach;
                                                                endif;
                                                            ?>

                                                        </select><br />
                                                        <div id="sensorContent">
                                                            <?php
                                                                if(isSet($device["sensors"]["value"])):
                                                                    foreach($device["sensors"]["value"] AS $key => $value):
                                                            ?>

                                                            <div class="row form-control" style="margin-bottom: 5px; margin-left: 0.5px;" id="sensor-<?=$key; ?>">
                                                                <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                                                                    <strong><?=$value["name"]["value"]; ?></strong>
                                                                    <input type="hidden" class="form-control" name="sensors[]" value="<?=$value["id"]; ?>" required>
                                                                </div>
                                                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                                                                    <a href="javascript:void(0);" class="removeSensor" data-id="<?=$key; ?>"><i class="fas fa-trash-alt"></i></a>
                                                                </div>
                                                            </div>

                                                            <?php
                                                                    endforeach;
                                                                endif;
                                                            ?>
                                                        </div>
                                                        <span class="help-block">Device Sensors</span>
                                                        <span class="hidden" id="lastSensor"><?=$key ? $key : 0; ?></span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label mb-10">Actuators</label>
                                                        <select class="form-control" id="actuatorSelect">
                                                            <option value="">Select Actuators To Add</option>

                                                            <?php
                                                                $actuators = $iotJumpWay->get_things(0, "actuator");
                                                                print_r($actuators);
                                                                if(!isSet($actuators["Error"])):
                                                                    foreach($actuators as $key => $value):
                                                            ?>

                                                                <option value="<?=$value["id"]; ?>"><?=$value["name"]["value"]; ?></option>

                                                            <?php
                                                                    endforeach;
                                                                endif;
                                                            ?>

                                                        </select><br />
                                                        <div id="actuatorContent">
                                                            <?php
                                                                if(isSet($device["actuators"])):
                                                                    foreach($device["actuators"]["value"] AS $key => $value):
                                                            ?>

                                                            <div class="row form-control" style="margin-bottom: 5px; margin-left: 0.5px;" id="actuator-<?=$key; ?>">
                                                                <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                                                                    <strong><?=$value["name"]["value"]; ?></strong>
                                                                    <input type="hidden" class="form-control" name="actuators[]" value="<?=$value["id"]; ?>" required>
                                                                </div>
                                                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                                                                    <a href="javascript:void(0);" class="removeActuator" data-id="<?=$key; ?>"><i class="fas fa-trash-alt"></i></a>
                                                                </div>
                                                            </div>

                                                            <?php
                                                                    endforeach;
                                                                endif;
                                                            ?>
                                                        </div>
                                                        <span class="help-block">Device Actuators</span>
                                                        <span class="hidden" id="lastActuator"><?=$key ? $key : 0; ?></span>
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

                                                                <option value="<?=$value["id"]; ?>" <?=$value["id"] == $device["networkLocation"]["value"] ? " selected " : ""; ?>><?=$value["name"]["value"]; ?></option>

                                                            <?php
                                                                    endforeach;
                                                                endif;
                                                            ?>

                                                        </select>
                                                        <span class="help-block"> Location of device</span>
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

                                                            <option value="<?=$value["id"]; ?>" <?=$device["networkZone"]["value"] == $value["id"] ? " selected " : ""; ?>><?=$value["name"]["value"]; ?></option>

                                                            <?php
                                                                    endforeach;
                                                                endif;
                                                            ?>

                                                        </select>
                                                        <span class="help-block"> Zone of device</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Coordinates</label>
                                                        <input type="text" class="form-control hider" id="coordinates" name="coordinates" placeholder="HIAS Location coordinates" required value="<?=$device["location"]["value"]["coordinates"][0]; ?>, <?=$device["location"]["value"]["coordinates"][1]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block">HIAS Device coordinates</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">IP</label>
                                                        <input type="text" class="form-control hider" id="ip" name="ip" placeholder="Device IP" required value="<?=$device["ipAddress"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block"> IP of device</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">MAC</label>
                                                        <input type="text" class="form-control hider" id="mac" name="mac" placeholder="Device MAC" required value="<?=$device["macAddress"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block"> MAC of device</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Bluetooth Only?</label>
                                                        <input type="checkbox" class="" id="bluetoothOnly" name="bluetoothOnly"  value=1 <?=$device["bluetoothOnly"]["value"]? " checked " : ""; ?> <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block">Device only supports Bluetooth/BLE?</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Bluetooth Address</label>
                                                        <input type="text" class="form-control hider" id="bluetooth" name="bluetooth" placeholder="Device Bluetooth Address"  value="<?=$device["bluetoothAddress"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block">Bluetooth address of device</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Bluetooth Service UUID</label>
                                                        <input type="text" class="form-control hider" id="bluetoothServiceUUID" name="bluetoothServiceUUID" placeholder="Bluetooth Service UUID"  value="<?=$device["bluetoothServiceUUID"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block">Bluetooth service UUID</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Bluetooth Characteristics UUID</label>
                                                        <input type="text" class="form-control hider" id="bluetoothCharacteristicUUID" name="bluetoothCharacteristicUUID" placeholder="Bluetooth characteristics UUID"  value="<?=$device["bluetoothCharacteristicUUID"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block">Bluetooth characteristics UUID</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Bluetooth Pincode</label>
                                                        <input type="text" class="form-control hider" id="bluetoothPinCode" name="bluetoothPinCode" placeholder="Bluetooth server pincode"  value="<?=$device["bluetoothPinCode"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block">Bluetooth server pincode</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">IPinfo Key</label>
                                                        <input type="text" class="form-control hider" id="authenticationIpinfoKey" name="authenticationIpinfoKey" placeholder="IPInfo key" required value="<?=$HIAS->helpers->oDecrypt($device["authenticationIpinfoKey"]["value"]); ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block"><a hef="https://ipinfo.io/" target="_BLANK">IPinfo</a> key</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Date Created</label>
                                                        <p><?=$device["dateCreated"]["value"]; ?></p>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Date First Used</label>
                                                        <p><?=$device["dateFirstUsed"]["value"] ? $device["dateFirstUsed"]["value"] : "NA"; ?></p>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Date Modified</label>
                                                        <p><?=$device["dateModified"]["value"]; ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group mb-0">
                                                <input type="hidden" class="form-control" id="update_device" name="update_device" required value="1">
                                                <button type="submit" class="btn btn-success btn-anim" id="device_update"><i class="icon-rocket"></i><span class="btn-text">Update</span></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div><br />
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-heading">
                                <div class="pull-left">
                                    <h6 class="panel-title txt-dark">Device Rules</h6>
                                </div>
                                <div class="pull-right"></div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">

                                    <div class="form-wrap">
                                        <form data-toggle="validator" role="form" id="device_rules">
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="rules">
                                                    <div class="form-group">
                                                        <label class="control-label mb-10">On Event Type</label>
                                                        <select class="form-control" id="e_type" name="e_type" required>
                                                            <option value="">PLEASE SELECT</option>
                                                            <option value="sensors">Sensor Value</option>
                                                            <option value="actuators">Actuator State</option>
                                                            <option value="models">Model Prediction</option>
                                                            <option value="status">Status</option>
                                                            <option value="time">Time</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                    <div class="form-group mb-0">
                                                        <button type="submit" class="btn btn-success btn-anim" id="add_rule"><i class="icon-rocket"></i><span class="btn-text">Add Rule</span></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div><br />
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-heading">
                                <div class="pull-left">
                                    <h6 class="panel-title txt-dark">Device History</h6>
                                </div>
                                <div class="pull-right">
                                    <select class="form-control" id="deviceHistory" name="deviceHistory" required>
                                        <option value="Activity">Device Activity</option>
                                        <option value="Transactions">Device Transactions</option>
                                        <option value="Statuses">Device Statuses</option>
                                        <option value="Life">Device Life</option>
                                        <option value="Sensors">Device Sensors</option>
                                        <option value="Actuators">Device Actuators</option>

                                    </select>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div class="scroll_450px">
                                        <div class="table-wrap mt-40">
                                            <div class="table-responsive">
                                                <table class="table mb-0">
                                                    <tbody id="deviceHistoryContainer">

                                                    <?php
                                                        $userDetails = "";
                                                        $history = $iotJumpWay->get_device_history($device["id"], 100);
                                                        if(count($history)):
                                                            foreach($history as $key => $value):
                                                                    if($value["uid"]):
                                                                        $user = $HIAS->get_user($value["uid"]);
                                                                        $userDetails = $user["name"]["value"];
                                                                    endif;
                                                    ?>

                                                    <tr>
                                                        <td>

                                                            <div class="row">
                                                                <div class="col-lg-1 col-md-12 col-sm-12 col-xs-12">ID:</div>
                                                                <div class="col-lg-11 col-md-12 col-sm-12 col-xs-12"><?=$value["id"];?></div>
                                                                <div class="col-lg-1 col-md-12 col-sm-12 col-xs-12">User:</div>
                                                                <div class="col-lg-11 col-md-12 col-sm-12 col-xs-12"><a href="/Users/Staff/<?=$value["uid"]; ?>"><?=$userDetails;?></a></div>
                                                                <div class="col-lg-1 col-md-12 col-sm-12 col-xs-12">Action:</div>
                                                                <div class="col-lg-11 col-md-12 col-sm-12 col-xs-12"><?=$value["action"];?></div>
                                                                <div class="col-lg-1 col-md-12 col-sm-12 col-xs-12">Hash:</div>
                                                                <div class="col-lg-11 col-md-12 col-sm-12 col-xs-12"><?php if($value["hash"]): ?><a href="/HIASBCH/Explorer/Transaction/<?=$value["hash"];?>"><?=$value["hash"];?></a><?php else: ?> NA <?php endif; ?></div>
                                                                <div class="col-lg-1 col-md-12 col-sm-12 col-xs-12">At:</div>
                                                                <div class="col-lg-11 col-md-12 col-sm-12 col-xs-12"><?=date("Y-m-d H:i:s", $value["time"]);?></div>
                                                            </div>

                                                        </td>
                                                    </tr>

                                                    <?php
                                                            endforeach;
                                                        endif;
                                                    ?>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
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
                                        <div class="col-md-12">
                                            <i class="fas fa-battery-full data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idebatU"><?=$device["batteryLevel"]["value"]; ?></span>% &nbsp;&nbsp;
                                            <i class="fa fa-microchip data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idecpuU"><?=$device["cpuUsage"]["value"]; ?></span>% &nbsp;&nbsp;
                                            <i class="zmdi zmdi-memory data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idememU"><?=$device["memoryUsage"]["value"]; ?></span>% &nbsp;&nbsp;
                                            <i class="far fa-hdd data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idehddU"><?=$device["hddUsage"]["value"]; ?></span>% &nbsp;&nbsp;
                                            <i class="fa fa-thermometer-quarter data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idetempU"><?=$device["temperature"]["value"]; ?></span>°C
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                <div class="pull-right"></div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Location</label>
                                        <div class="col-md-12">
                                              <div id="map1" class="map" style="height: 300px;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-heading">
                                <div class="pull-left">
                                    <h6 class="panel-title txt-dark">Device Schema</h6>
                                </div>
                                <div class="pull-right"></div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div  style="height: 400px; overflow-y: scroll; overflow-x: hidden;">
                                        <?php echo "<pre id='schema'>"; ?> <?php print_r(json_encode($device, JSON_PRETTY_PRINT)); ?> <?php echo "</pre>"; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div class="pull-right"><a href="javascipt:void(0)" id="reset_dvc_apriv"><i class="fa fa-refresh"></i> Reset API Key</a></div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Identifier</label>
                                        <div class="col-md-9">
                                            <p class="form-control-static hiderstr" id="idappid"><?=$device["authenticationUser"]["value"]; ?></p>
                                            <p><strong>Last Updated:</strong> <?=$device["authenticationKey"]["metadata"]["timestamp"]["value"]; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div class="pull-right"></div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Blockchain Address</label>
                                        <div class="col-md-9">
                                            <p class="form-control-static hiderstr" id="bcid"><?=$device["authenticationBlockchainUser"]["value"]; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div class="pull-right"><a href="javascipt:void(0)" id="reset_dvc_mqtt"><i class="fa fa-refresh"></i> Reset MQTT Password</a></div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5">MQTT Username</label>
                                        <div class="col-md-9">
                                            <p class="form-control-static hiderstr" id="idmqttu"><?=$HIAS->helpers->oDecrypt($device["authenticationMqttUser"]["value"]); ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5">MQTT Password</label>
                                        <div class="col-md-9">
                                            <p class="form-control-static hiderstr"><span id="idmqttp"><?=$HIAS->helpers->oDecrypt($device["authenticationMqttKey"]["value"]); ?></span>
                                            <p><strong>Last Updated:</strong> <?=$device["authenticationMqttKey"]["metadata"]["timestamp"]["value"]; ?></p>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div class="pull-right"><a href="javascipt:void(0)" id="reset_dvc_amqp"><i class="fa fa-refresh"></i> Reset AMQP Password</a></div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5">AMQP Username</label>
                                        <div class="col-md-9">
                                            <p class="form-control-static hiderstr" id="damqpu"><?=$HIAS->helpers->oDecrypt($device["authenticationAmqpUser"]["value"]); ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5">AMQP Password</label>
                                        <div class="col-md-9">
                                            <p class="form-control-static hiderstr"><span id="damqpp"><?=$HIAS->helpers->oDecrypt($device["authenticationAmqpKey"]["value"]); ?></span>
                                            <p><strong>Last Updated:</strong> <?=$device["authenticationAmqpKey"]["metadata"]["timestamp"]["value"]; ?></p>
                                            </p>
                                        </div>
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

        <script type="text/javascript" src="/vendors/bower_components/echarts/dist/echarts-en.min.js"></script>
        <script type="text/javascript" src="/vendors/echarts-liquidfill.min.js"></script>
        <script type="text/javascript">

            $(document).ready(function() {
                iotJumpwayUI.StartDeviceLife();
            });

            function initMap() {

                var latlng = new google.maps.LatLng("<?=floatval($device["location"]["value"]["coordinates"][0]); ?>", "<?=floatval($device["location"]["value"]["coordinates"][1]); ?>");
                var map = new google.maps.Map(document.getElementById('map1'), {
                    zoom: 10,
                    center: latlng
                });

                var loc = new google.maps.LatLng(<?=floatval($device["location"]["value"]["coordinates"][0]); ?>, <?=floatval($device["location"]["value"]["coordinates"][1]); ?>);
                var marker = new google.maps.Marker({
                    position: loc,
                    map: map,
                    title: 'Device '
                });
            }

            var eChart_1 = echarts.init(document.getElementById('e_chart_1'));

            var option = {
                tooltip: {
                    trigger: 'axis',
                    backgroundColor: 'rgba(33,33,33,1)',
                    borderRadius: 0,
                    padding: 10,
                    axisPointer: {
                        type: 'cross',
                        label: {
                            backgroundColor: 'rgba(33,33,33,1)'
                        }
                    },
                    textStyle: {
                        color: '#fff',
                        fontStyle: 'normal',
                        fontWeight: 'normal',
                        fontFamily: "'Montserrat', sans-serif",
                        fontSize: 12
                    }
                },
                xAxis: {
                    type: 'category',
                    name: 'Time',
                    nameLocation: 'middle',
                    nameGap: 50,
                    axisLabel: {
                        textStyle: {
                            color: '#fff',
                            fontStyle: 'normal',
                            fontWeight: 'normal',
                            fontFamily: "'Montserrat', sans-serif",
                            fontSize: 12
                        },
                        interval: 1,
                        rotate: 45
                    },
                    data: <?=json_encode($dates); ?>
                },
                yAxis: {
                    axisLabel: {
                        textStyle: {
                            color: '#fff',
                            fontStyle: 'normal',
                            fontWeight: 'normal',
                            fontFamily: "'Montserrat', sans-serif",
                            fontSize: 12
                        }
                    },
                    type: 'value',
                    name: 'Y-Axis',
                    nameLocation: 'center',
                    nameGap: 50
                },
                grid: {
                    top: 10,
                    left: 0,
                    right: 0,
                    bottom: 100,
                    containLabel: true
                },
                series: <?=json_encode($points); ?>
            };
            eChart_1.setOption(option);
            eChart_1.resize();

            iotJumpwayUI.LifeInterval = setInterval(function() {
                iotJumpwayUI.updateDeviceLifeGraph();
            }, 1000);

            iotJumpwayUI.HideSecret();

        </script>
        <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?=$HIAS->helpers->oDecrypt($HIAS->confs["gmaps"]); ?>&callback=initMap"></script>

    </body>
</html>