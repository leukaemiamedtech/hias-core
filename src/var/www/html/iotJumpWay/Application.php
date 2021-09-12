<?php session_start();

$pageDetails = [
    "PageID" => "IoT",
    "SubPageID" => "Entities",
    "LowPageID" => "Application"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWayAgents.php';
include dirname(__FILE__) . '/../HIASCDI/Classes/Interface.php';
include dirname(__FILE__) . '/../AI/Classes/AI.php';

$aid = filter_input(INPUT_GET, 'application', FILTER_SANITIZE_STRING);
$application = $iotJumpWay->get_application($aid, "dateCreated,dateModified,*");

list($appOn, $appOff) = $iotJumpWay->get_application_status($application["networkStatus"]["value"]);

$cancelled = $application["permissionsCancelled"]["value"] ? True : False;

$data = $iotJumpWay->application_life_graph($aid, 100);

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
        <meta name="author" content="hencework"/>

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
                                    <h6 class="panel-title txt-dark">Application Resource Monitor</h6>
                                </div>
                                <div class="pull-right">
                                    <select class="form-control" id="applicationGraphs" name="applicationGraphs" required>
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
                                    <h6 class="panel-title txt-dark">HIAS Application</h6>
                                </div>
                                <div class="pull-right"><a href="/iotJumpWay/Applications/<?=$aid; ?>/Configuration" download><i class="fas fa-download"></i> Download Credentials</a></div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div class="form-wrap">
                                        <form data-toggle="validator" role="form" id="application_update">
                                            <hr class="light-grey-hr"/>
                                            <div class="row">
                                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Name</label>
                                                        <input type="text" class="form-control" id="name" name="name" placeholder="Application Name" required value="<?=$application["name"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block"> Name of application</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Description</label>
                                                        <input type="text" class="form-control" id="description" name="description" placeholder="Application Description" required value="<?=$application["description"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block"> Description of application</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label mb-10">Category</label>
                                                        <select class="form-control" id="category" name="category" <?=$cancelled ? " disabled " : ""; ?>>

                                                            <?php
                                                                $categories = $iotJumpWay->get_application_categories();
                                                                if(count($categories)):
                                                                    foreach($categories as $key => $value):
                                                            ?>

                                                            <option value="<?=$value["category"]; ?>" <?=$value["category"] == $application["category"]["value"][0] ? " selected " : ""; ?>><?=$value["category"]; ?></option>

                                                            <?php
                                                                    endforeach;
                                                                endif;
                                                            ?>

                                                        </select>
                                                        <span class="help-block">Application category</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label mb-10">IoT Agent</label>
                                                        <select class="form-control" id="agent" name="agent">
                                                            <option value="">PLEASE SELECT</option>
                                                            <option value="Self" <?=$application["agent"]["value"] == "Self" ? " selected " : ""; ?>>Self</option>

                                                            <?php
                                                                $agents = $iotJumpWayAgents->get_agents(0, "", "IoT%20Agent");
                                                                if(isSet($agents)):
                                                                    foreach($agents as $key => $value):
                                                            ?>

                                                            <option value="http://<?=$value["ipAddress"]["value"]; ?>:<?=$value["northPort"]["value"]; ?>" <?=$application["agent"]["value"] == "http://" . $value["ipAddress"]["value"] . ":" . $value["northPort"]["value"] ? " selected " : ""; ?>><?=$value["name"]["value"]; ?> (http://<?=$value["ipAddress"]["value"]; ?>:<?=$value["northPort"]["value"]; ?>)</option>

                                                            <?php
                                                                    endforeach;
                                                                endif;
                                                            ?>

                                                        </select>
                                                        <span class="help-block">Application IoT Agent</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Hardware Application Name</label>
                                                        <input type="text" class="form-control" id="deviceName" name="deviceName" placeholder="Hardware device name" required value="<?=$application["brandName"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block">Name of hardware device</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Hardware Application Model</label>
                                                        <input type="text" class="form-control" id="deviceModel" name="deviceModel" placeholder="Hardware device model" required value="<?=$application["modelName"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block">Hardware model</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Hardware Application Manufacturer</label>
                                                        <input type="text" class="form-control" id="deviceManufacturer" name="deviceManufacturer" placeholder="Hardware device manufacturer" required value="<?=$application["manufacturerName"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block">Name of hardware manufacturer</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Operating System</label>
                                                        <input type="text" class="form-control" id="osName" name="osName" placeholder="Operating system name" required value="<?=$application["os"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block">Operating system name</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Operating system manufacturer</label>
                                                        <input type="text" class="form-control" id="osManufacturer" name="osManufacturer" placeholder="Operating system manufacturer" required value="<?=$application["osManufacturer"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block">Operating system manufacturer</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Operating system version</label>
                                                        <input type="text" class="form-control" id="osVersion" name="osVersion" placeholder="Operating system version" required value="<?=$application["osVersion"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block">Operating system version</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label mb-10">Protocols</label>
                                                        <select class="form-control" id="protocols" name="protocols[]" required multiple <?=$cancelled ? " disabled " : ""; ?>>

                                                            <?php
                                                                $protocols = $HiasInterface->get_protocols();
                                                                if(count($protocols)):
                                                                    foreach($protocols as $key => $value):
                                                            ?>

                                                                <option value="<?=$value["protocol"]; ?>" <?=in_array($value["protocol"], $application["protocols"]["value"]) ? " selected " : ""; ?>><?=$value["protocol"]; ?></option>

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
                                                                if(isSet($application["sensors"])):
                                                                    foreach($application["sensors"]["value"] AS $key => $value):
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
                                                                if(isSet($application["actuators"])):
                                                                    foreach($application["actuators"]["value"] AS $key => $value):
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
                                                    <div class="form-group">
                                                        <label class="control-label mb-10">AI Models</label>
                                                        <select class="form-control" id="ai" name="ai[]" multiple>

                                                            <?php
                                                                $models = $AI->get_models();
                                                                if(!isSet($models["Error"])):
                                                                    foreach($models as $key => $value):
                                                            ?>

                                                                <option value="<?=$value["id"]; ?>" <?=array_key_exists($value["name"]["value"], $application["models"]["value"]) ? " selected " : ""; ?>><?=$value["name"]["value"]; ?></option>

                                                            <?php
                                                                    endforeach;
                                                                endif;
                                                            ?>

                                                        </select>
                                                        <span class="help-block">Application AI Models</span>
                                                    </div>

                                                    <?php if(!$cancelled): ?>

                                                    <div class="form-group mb-0">
                                                        <input type="hidden" class="form-control" id="update_application" name="update_application" required value="1">
                                                        <button type="submit" class="btn btn-success btn-anim" id="application_update"><i class="icon-rocket"></i><span class="btn-text">Update</span></button>
                                                    </div>

                                                    <?php endif; ?>

                                                </div>
                                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                                    <div class="form-group">
                                                        <label class="control-label mb-10">Location</label>
                                                        <select class="form-control" id="lid" name="lid" required <?=$cancelled ? " disabled " : ""; ?>>
                                                            <option value="">PLEASE SELECT</option>

                                                            <?php
                                                                $Locations = $iotJumpWay->get_locations();
                                                                if(count($Locations)):
                                                                    foreach($Locations as $key => $value):
                                                            ?>

                                                                <option value="<?=$value["id"]; ?>" <?=$value["id"] == $application["networkLocation"]["value"] ? " selected " : ""; ?>><?=$value["name"]["value"]; ?></option>

                                                            <?php
                                                                    endforeach;
                                                                endif;
                                                            ?>

                                                        </select>
                                                        <span class="help-block"> Location of application</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Coordinates</label>
                                                        <input type="text" class="form-control hider" id="coordinates" name="coordinates" placeholder="iotJumpWay Location coordinates" required value="<?=$application["location"]["value"]["coordinates"][0]; ?>, <?=$application["location"]["value"]["coordinates"][1]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block">iotJumpWay Application coordinates</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">IP</label>
                                                        <input type="text" class="form-control hider" id="ip" name="ip" placeholder="Device IP" required value="<?=$application["ipAddress"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block"> IP of application device</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">MAC</label>
                                                        <input type="text" class="form-control hider" id="mac" name="mac" placeholder="Device MAC" required value="<?=$application["macAddress"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block"> MAC of application device</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Bluetooth Address</label>
                                                        <input type="text" class="form-control hider" id="bluetooth" name="bluetooth" placeholder="Device Bluetooth Address"  value="<?=$application["bluetoothAddress"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block">Bluetooth address of application device</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Is Admin:</label>
                                                        <input type="checkbox" class="" id="admin" name="admin" value=1 <?=$application["permissionsAdmin"]["value"] ? " checked " : ""; ?> <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block"> Is application an admin?</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Is Cancelled:</label>
                                                        <input type="checkbox" class="" id="cancelled" name="cancelled" value=1 <?=$application["permissionsCancelled"]["value"] ? " checked " : ""; ?> <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block"> Is application cancelled?</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Date Created</label>
                                                        <p><?=$application["dateCreated"]["value"]; ?></p>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Date First Used</label>
                                                        <p><?=$application["dateFirstUsed"]["value"] ? $application["dateFirstUsed"]["value"] : "NA"; ?></p>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Date Modified</label>
                                                        <p><?=$application["dateModified"]["value"]; ?></p>
                                                    </div>
                                                    <div class="clearfix"></div>
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
                                    <h6 class="panel-title txt-dark">Application History</h6>
                                </div>
                                <div class="pull-right">
                                    <select class="form-control" id="applicationHistory" name="applicationHistory" required>
                                        <option value="Activity">Application Activity</option>
                                        <option value="Transactions">Application Transactions</option>
                                        <option value="Statuses">Application Statuses</option>
                                        <option value="Life">Application Life</option>
                                        <option value="Sensors">Application Sensors</option>
                                        <option value="Actuators">Application Actuators</option>

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
                                                    <tbody id="applicationHistoryContainer">

                                                    <?php
                                                        $userDetails = "";
                                                        $history = $iotJumpWay->get_application_history($application["id"], 100);
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
                    <?php if(!$cancelled): ?>
                    <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                <div class="pull-right"><span id="offline1" style="color: #33F9FF !important;" class="<?=$appOn; ?>"><i class="fas fa-power-off" style="color: #33F9FF !important;"></i> Online</span> <span id="online1" class="<?=$appOff; ?>" style="color: #99A3A4 !important;"><i class="fas fa-power-off" style="color: #99A3A4 !important;"></i> Offline</span></div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Status</label>
                                        <div class="col-md-12">
                                            <i class="fas fa-battery-full data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idebatU"><?=$application["batteryLevel"]["value"]; ?></span>% &nbsp;&nbsp;
                                            <i class="fa fa-microchip data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idecpuU"><?=$application["cpuUsage"]["value"]; ?></span>% &nbsp;&nbsp;
                                            <i class="zmdi zmdi-memory data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idememU"><?=$application["memoryUsage"]["value"]; ?></span>% &nbsp;&nbsp;
                                            <i class="far fa-hdd data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idehddU"><?=$application["hddUsage"]["value"]; ?></span>% &nbsp;&nbsp;
                                            <i class="fa fa-thermometer-quarter data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idetempU"><?=$application["temperature"]["value"]; ?></span>°C
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
                                    <h6 class="panel-title txt-dark">Application Schema</h6>
                                </div>
                                <div class="pull-right"></div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div  style="height: 400px; overflow-y: scroll; overflow-x: hidden;">
                                        <?php echo "<pre id='schema'>"; ?> <?php print_r(json_encode($application, JSON_PRETTY_PRINT)); ?> <?php echo "</pre>"; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div class="pull-right"><a href="javascipt:void(0)" id="reset_app_apriv"><i class="fa fa-refresh"></i> Reset API Key</a></div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Identifier</label>
                                        <div class="col-md-9">
                                            <p class="form-control-static hiderstr" id="idappid"><?=$application["authenticationUser"]["value"]; ?></p>
                                            <p><strong>Last Updated:</strong> <?=$application["authenticationKey"]["metadata"]["timestamp"]["value"]; ?></p>
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
                                            <p class="form-control-static hiderstr" id="bcid"><?=$application["authenticationBlockchainUser"]["value"]; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div class="pull-right"><a href="javascipt:void(0)" id="reset_app_mqtt"><i class="fa fa-refresh"></i> Reset MQTT Password</a></div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5">MQTT Username</label>
                                        <div class="col-md-9">
                                            <p class="form-control-static hiderstr" id="idmqttu"><?=$HIAS->helpers->oDecrypt($application["authenticationMqttUser"]["value"]); ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5">MQTT Password</label>
                                        <div class="col-md-9">
                                            <p class="form-control-static hiderstr"><span id="idmqttp"><?=$HIAS->helpers->oDecrypt($application["authenticationMqttKey"]["value"]); ?></span>
                                            <p><strong>Last Updated:</strong> <?=$application["authenticationMqttKey"]["metadata"]["timestamp"]["value"]; ?></p>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div class="pull-right"><a href="javascipt:void(0)" id="reset_app_amqp"><i class="fa fa-refresh"></i> Reset AMQP Password</a></div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5">AMQP Username</label>
                                        <div class="col-md-9">
                                            <p class="form-control-static hiderstr" id="damqpu"><?=$HIAS->helpers->oDecrypt($application["authenticationAmqpUser"]["value"]); ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5">AMQP Password</label>
                                        <div class="col-md-9">
                                            <p class="form-control-static hiderstr"><span id="damqpp"><?=$HIAS->helpers->oDecrypt($application["authenticationAmqpKey"]["value"]); ?></span>
                                            <p><strong>Last Updated:</strong> <?=$application["authenticationAmqpKey"]["metadata"]["timestamp"]["value"]; ?></p>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

            </div>

            <?php include dirname(__FILE__) . '/../Includes/Footer.php'; ?>

        </div>

        <?php  include dirname(__FILE__) . '/../Includes/JS.php'; ?>

        <?php
            if($application["lt"] == ""):
                $coords = "41.54329,2.10942";
            else:
                $coords = $application["lt"] . "," . $application["lg"];
            endif;

        ?>

        <script type="text/javascript" src="/iotJumpWay/Classes/mqttws31.js"></script>
        <script type="text/javascript" src="/iotJumpWay/Classes/iotJumpWay.js"></script>
        <script type="text/javascript" src="/iotJumpWay/Classes/iotJumpWayUI.js"></script>

        <script type="text/javascript" src="/vendors/bower_components/echarts/dist/echarts-en.min.js"></script>
        <script type="text/javascript" src="/vendors/echarts-liquidfill.min.js"></script>

        <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?=$HIAS->helpers->oDecrypt($HIAS->confs["gmaps"]); ?>&callback=initMap"></script>
        <script>

            iotJumpwayUI.HideSecret();
            iotJumpwayUI.StartApplicationLife();

            function initMap() {

                var latlng = new google.maps.LatLng("<?=floatval($application["location"]["value"]["coordinates"][0]); ?>", "<?=floatval($application["location"]["value"]["coordinates"][1]); ?>");
                var map = new google.maps.Map(document.getElementById('map1'), {
                    zoom: 10,
                    center: latlng
                });

                var loc = new google.maps.LatLng(<?=floatval($application["location"]["value"]["coordinates"][0]); ?>, <?=floatval($application["location"]["value"]["coordinates"][1]); ?>);
                var marker = new google.maps.Marker({
                    position: loc,
                    map: map,
                    title: 'Application '
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

            iotJumpwayUI.AppLifeInterval = setInterval(function() {
                iotJumpwayUI.updateApplicationLifeGraph();
            }, 1000);

        </script>

    </body>

</html>
