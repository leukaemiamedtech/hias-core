<?php session_start();

    $pageDetails = [
        "PageID" => "Robotics",
        "SubPageID" => "List",
        "LowPageID" => "List"
    ];

    include dirname(__FILE__) . '/../../Classes/Core/init.php';

    include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';
    include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWayAgents.php';
    include dirname(__FILE__) . '/../HIASCDI/Classes/Interface.php';

    include dirname(__FILE__) . '/../AI/Classes/AI.php';
    include dirname(__FILE__) . '/../Robotics/Classes/Robotics.php';

    $rid = filter_input(INPUT_GET, 'unit', FILTER_SANITIZE_STRING);
    $robotic = $Robotics->get_robotic($rid, "dateCreated,dateModified,id,*");

    list($dev1On, $dev1Off) = $iotJumpWay->get_device_status($robotic["networkStatus"]["value"]);

    $data = $Robotics->robotics_life_graph($rid, 100);

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
     <meta name="viewport" content="width=Robotics Unit-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
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
                                    <h6 class="panel-title txt-dark">AI Agent Resource Monitor</h6>
                                </div>
                                <div class="pull-right">
                                    <select class="form-control" id="agentGraphs" name="agentGraphs" required>
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
                        </div>
                    <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div class="pull-right"><span id="offline1" style="color: #33F9FF !important;" class="<?=$dev1On; ?>"><i class="fas fa-power-off" style="color: #33F9FF !important;"></i> Online</span> <span id="online1" class="<?=$dev1Off; ?>" style="color: #99A3A4 !important;"><i class="fas fa-power-off" style="color: #99A3A4 !important;"></i> Offline</span></div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Status</label>
                                        <div class="col-md-12">
                                            <i class="fas fa-battery-full data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idebatU"><?=$robotic["batteryLevel"]["value"]; ?></span>% &nbsp;&nbsp;
                                            <i class="fa fa-microchip data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idecpuU"><?=$robotic["cpuUsage"]["value"]; ?></span>% &nbsp;&nbsp;
                                            <i class="zmdi zmdi-memory data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idememU"><?=$robotic["memoryUsage"]["value"]; ?></span>% &nbsp;&nbsp;
                                            <i class="far fa-hdd data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idehddU"><?=$robotic["hddUsage"]["value"]; ?></span>% &nbsp;&nbsp;
                                            <i class="fa fa-thermometer-quarter data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idetempU"><?=$robotic["temperature"]["value"]; ?></span>°C
                                        </div>

                                    </div>
                                    <div class="pull-right"><p><br /><strong>Last Updated:</strong> <?=$robotic["networkStatus"]["metadata"]["timestamp"]["value"]; ?></p></div>
                                </div>

                            </div>
                        </div>
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div id="map1" style="height:265px;"></div>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>

                <?php include dirname(__FILE__) . '/../Robotics/Includes/Visual.php'; ?>

                <?php include dirname(__FILE__) . '/../Robotics/Includes/Controls.php'; ?>

                <div class="row">
                    <div class="col-lg-8  col-md-12 col-sm-12 col-xs-12">
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-heading">
                                <div class="pull-left">
                                   EMAR MINI Unit
                                </div>
                                <div class="pull-right"><a href="/Robotics/EMAR-Mini/Unit/<?=$robotic["id"]; ?>/Credentials" download><i class="fas fa-download"></i> Download Credentials</a></div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div class="form-wrap">
                                        <form data-toggle="validator" role="form" id="robotics_update_form">
                                            <div class="row">
                                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Name</label>
                                                        <input type="text" class="form-control" id="name" name="name" placeholder="Robotics Unit Name" required value="<?=$robotic["name"]["value"]; ?>">
                                                        <span class="help-block"> Name of Robotics Unit</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Unit Description</label>
                                                        <input type="text" class="form-control" id="description" name="description" placeholder="Robotics Unit Description" required value="<?=$robotic["description"]["value"]; ?>">
                                                        <span class="help-block"> Description of Robotics Unit</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label mb-10">Unit Category</label>
                                                        <select class="form-control" id="category" name="category[]" required multiple>

                                                            <?php
                                                                $robotic_cats = $Robotics->get_robotics_categories();
                                                                if(count($robotic_cats)):
                                                                    foreach($robotic_cats as $key => $value):
                                                            ?>

                                                            <option value="<?=$value["category"]; ?>" <?=in_array($value["category"], $robotic["category"]["value"]) ? " selected " : ""; ?>><?=$value["category"]; ?></option>

                                                            <?php
                                                                    endforeach;
                                                                endif;
                                                            ?>

                                                        </select>
                                                        <span class="help-block">Robotics Unit category</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Unit Brand Name</label>
                                                        <input type="text" class="form-control" id="deviceBrandName" name="deviceBrandName" placeholder="Robotics Unit name" required value="<?=$robotic["deviceBrandName"]["value"]; ?>">
                                                        <span class="help-block">Name of Robotics Unit</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label mb-10">Unit Model</label>
                                                        <select class="form-control" id="deviceModel" name="deviceModel" required>
                                                            <option value="">PLEASE SELECT</option>

                                                            <?php
                                                                $categories = $Robotics->get_robotics_types();
                                                                if(count($categories)):
                                                                    foreach($categories as $key => $value):
                                                            ?>

                                                            <option value="<?=$value["r_type"]; ?>" <?=$value["r_type"] == $robotic["deviceModel"]["value"] ? " selected " : ""; ?>><?=$value["r_type"]; ?></option>

                                                            <?php
                                                                    endforeach;
                                                                endif;
                                                            ?>

                                                        </select>
                                                        <span class="help-block">Unit type of robotics</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Unit Manufacturer</label>
                                                        <input type="text" class="form-control" id="deviceManufacturer" name="deviceManufacturer" placeholder="Robotics Unit manufacturer" required value="<?=$robotic["deviceManufacturer"]["value"]; ?>">
                                                        <span class="help-block">Name of hardware manufacturer</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Operating System</label>
                                                        <input type="text" class="form-control" id="os" name="os" placeholder="Operating system name" required value="<?=$robotic["os"]["value"]; ?>">
                                                        <span class="help-block">Operating system name</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Operating system manufacturer</label>
                                                        <input type="text" class="form-control" id="osManufacturer" name="osManufacturer" placeholder="Operating system manufacturer" required value="<?=$robotic["osManufacturer"]["value"]; ?>">
                                                        <span class="help-block">Operating system manufacturer</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Operating system version</label>
                                                        <input type="text" class="form-control" id="osVersion" name="osVersion" placeholder="Operating system version" required value="<?=$robotic["osVersion"]["value"]; ?>">
                                                        <span class="help-block">Operating system version</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Camera Proxy Endpoint</label>
                                                        <input type="text" class="form-control hider" id="endpoint" name="endpoint" placeholder="Robotics Unit Proxy Endpoint" required value="<?=$robotic["endpoint"]["value"]; ?>">
                                                            <span class="help-block"> Name of Robotics Unit camera stream HIAS proxy endpoint (Robotics/EMAR-Mini/Camera/YourENDPOINT)</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Camera Stream Port</label>
                                                        <input type="text" class="form-control hider" id="stream_port" name="stream_port" placeholder="Robotics Unit Stream Port" required value="<?=$robotic["streamPort"]["value"]; ?>">
                                                        <span class="help-block"> Port of Robotics Unit camera stream</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Camera Stream File</label>
                                                        <input type="text" class="form-control hider" id="stream_file" name="stream_file" placeholder="Robotics Unit Stream File" required value="<?=$robotic["streamFile"]["value"]; ?>">
                                                        <span class="help-block">File name of Robotics Unit camera stream (.mjpg)</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Camera Socket Port</label>
                                                        <input type="text" class="form-control hider" id="socket_port" name="socket_port" placeholder="Robotics Unit MAC" required value="<?=$robotic["socketPort"]["value"]; ?>">
                                                        <span class="help-block"> Port of Robotics Unit camera socket stream</span>
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
                                                                if(isSet($robotic["sensors"]["value"])):
                                                                    foreach($robotic["sensors"]["value"] AS $key => $value):
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
                                                        <span class="help-block">Robotics Unit Sensors</span>
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
                                                                if(isSet($robotic["actuators"])):
                                                                    foreach($robotic["actuators"]["value"] AS $key => $value):
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
                                                        <span class="help-block">Robotics Unit Actuators</span>
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

                                                                <option value="<?=$value["id"]; ?>" <?=array_key_exists($value["name"]["value"], $robotic["models"]) ? " selected " : ""; ?>><?=$value["name"]["value"]; ?></option>

                                                            <?php
                                                                    endforeach;
                                                                endif;
                                                            ?>

                                                        </select>
                                                        <span class="help-block">Robotics Unit AI Models</span>
                                                    </div>
                                                    <div class="form-group mb-0">
                                                        <input type="hidden" class="form-control" id="update_robotics" name="update_robotics" required value="1">
                                                        <input type="hidden" class="form-control" id="lentity" name="lentity" required value="<?=$robotic["networkLocation"]["value"]; ?>">
                                                        <input type="hidden" class="form-control" id="dentity" name="dentity" required value="<?=$robotic["id"]; ?>">
                                                        <input type="hidden" class="form-control" id="uidentifier" name="uidentifier" required value="<?=$_SESSION["HIAS"]["Uid"]; ?>">
                                                        <button type="submit" class="btn btn-success btn-anim"><i class="icon-rocket"></i><span class="btn-text">Update</span></button>
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

                                                                    <option value="<?=$value["id"]; ?>" <?=$value["id"] == $robotic["networkLocation"]["value"] ? " selected " : ""; ?>><?=$value["name"]["value"]; ?></option>

                                                                <?php
                                                                        endforeach;
                                                                    endif;
                                                                ?>

                                                        </select>
                                                        <span class="help-block"> Location of Robotics Unit</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label mb-10">Unit IoT Agent</label>
                                                        <select class="form-control" id="agent" name="agent">
                                                            <option value="">PLEASE SELECT</option>

                                                            <?php
                                                                $robotics = $iotJumpWayAgents->get_agents(0, "", "IoT Agent");
                                                                if(!isSet($robotics["Error"])):
                                                                    foreach($robotics as $key => $value):
                                                            ?>

                                                            <option value="http://<?=$value["ipAddress"]["value"]; ?>:<?=$value["northPort"]["value"]; ?>" <?=$robotic["agent"]["value"] == "http://" . $value["ipAddress"]["value"] . ":" . $value["northPort"]["value"] ? " selected " : ""; ?>><?=$value["name"]["value"]; ?> (http://<?=$value["ipAddress"]["value"]; ?>:<?=$value["northPort"]["value"]; ?>)</option>

                                                            <?php
                                                                    endforeach;
                                                                endif;
                                                            ?>

                                                        </select>
                                                        <span class="help-block">Robotics Unit IoT Agent</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Coordinates</label>
                                                        <input type="text" class="form-control" id="coordinates" name="coordinates" placeholder="iotJumpWay Location coordinates" required value="<?=$robotic["location"]["value"]["coordinates"][0]; ?>, <?=$robotic["location"]["value"]["coordinates"][1]; ?>">
                                                        <span class="help-block">iotJumpWay Robotics Unit coordinates</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label mb-10">Protocols</label>
                                                        <select class="form-control" id="protocols" name="protocols[]" required multiple>

                                                            <?php
                                                                $protocols = $HiasInterface->get_protocols();
                                                                if(count($protocols)):
                                                                    foreach($protocols as $key => $value):
                                                            ?>

                                                                <option value="<?=$value["protocol"]; ?>" <?=in_array($value["protocol"], $robotic["protocols"]["value"]) ? " selected " : ""; ?>><?=$value["protocol"]; ?></option>

                                                            <?php
                                                                    endforeach;
                                                                endif;
                                                            ?>

                                                        </select>
                                                        <span class="help-block">Supported Communication Protocols</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">IP</label>
                                                        <input type="text" class="form-control hider" id="ipAddress" name="ipAddress" placeholder="Robotics Unit IP" required value="<?=$robotic["ipAddress"]["value"]; ?>">
                                                        <span class="help-block"> IP of Robotics Unit</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">MAC</label>
                                                        <input type="text" class="form-control hider" id="macAddress" name="macAddress" placeholder="Robotics Unit MAC" required value="<?=$robotic["macAddress"]["value"]; ?>">
                                                        <span class="help-block"> MAC of Robotics Unit</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Bluetooth Address</label>
                                                        <input type="text" class="form-control hider" id="bluetoothAddress" name="bluetoothAddress" placeholder="Robotics Unit Bluetooth Address"  value="<?=$robotic["bluetoothAddress"]["value"]; ?>">
                                                        <span class="help-block">Bluetooth address of Robotics Unit</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">North Port</label>
                                                        <input type="text" class="form-control hider" id="northPort" name="northPort" placeholder="Robotics Unit North Port" required value="<?=$robotic["northPort"]["value"]; ?>">
                                                        <span class="help-block"> North Port of Robtics Unit</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">IPinfo Key</label>
                                                        <input type="text" class="form-control hider" id="authenticationIpinfoKey" name="authenticationIpinfoKey" placeholder="IPInfo key" required value="<?=$HIAS->helpers->oDecrypt($robotic["authenticationIpinfoKey"]["value"]); ?>" >
                                                        <span class="help-block"><a hef="https://ipinfo.io/" target="_BLANK">IPinfo</a> key</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Date Created</label>
                                                        <p><?=$robotic["dateCreated"]["value"]; ?></p>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Date First Used</label>
                                                        <p><?=$robotic["dateFirstUsed"]["value"] ? $robotic["dateFirstUsed"]["value"] : "NA"; ?></p>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Date Modified</label>
                                                        <p><?=$robotic["dateModified"]["value"]; ?></p>
                                                    </div>

                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-heading">
                                <div class="pull-left">
                                    <h6 class="panel-title txt-dark">Robotics Unit History</h6>
                                </div>
                                <div class="pull-right">
                                    <select class="form-control" id="roboticsHistory" name="roboticsHistory" required>
                                        <option value="Activity">Robotics Unit Activity</option>
                                        <option value="Transactions">Robotics Unit Transactions</option>
                                        <option value="Statuses">Robotics Unit Statuses</option>
                                        <option value="Life">Robotics Unit Life</option>
                                        <option value="Sensors">Robotics Unit Sensors</option>
                                        <option value="Actuators">Robotics Unit Actuators</option>

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
                                                    <tbody id="roboticsHistoryContainer">

                                                    <?php
                                                        $userDetails = "";
                                                        $history = $Robotics->get_robotics_history($robotic["id"], 100);
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
                            <div class="panel-heading">
                                <div class="pull-left">
                                    <h6 class="panel-title txt-dark">Agent Schema</h6>
                                </div>
                                <div class="pull-right"></div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div  style="height: 400px; overflow-y: scroll; overflow-x: hidden;">
                                        <?php echo "<pre id='schema'>"; ?> <?php print_r(json_encode($robotic, JSON_PRETTY_PRINT)); ?> <?php echo "</pre>"; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div class="pull-right"><a href="javascipt:void(0)" id="reset_emar_apriv"><i class="fa fa-refresh"></i> Reset API Key</a></div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Identifier</label>
                                        <div class="col-md-9">
                                            <p class="form-control-static hiderstr" id="idappid"><?=$robotic["id"]; ?></p>
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
                                            <p class="form-control-static hiderstr" id="bcid"><?=$robotic["authenticationBlockchainUser"]["value"]; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div class="pull-right"><a href="javascipt:void(0)" id="reset_emar_mqtt"><i class="fa fa-refresh"></i> Reset MQTT Password</a></div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5">MQTT Username</label>
                                        <div class="col-md-9">
                                            <p class="form-control-static hiderstr" id="amqttu"><?=$HIAS->helpers->oDecrypt($robotic["authenticationMqttUser"]["value"]); ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5">MQTT Password</label>
                                        <div class="col-md-9">
                                            <p class="form-control-static hiderstr"><span id="amqttp"><?=$HIAS->helpers->oDecrypt($robotic["authenticationMqttKey"]["value"]); ?></span></p>
                                            <p><strong>Last Updated:</strong> <?=$robotic["authenticationMqttKey"]["metadata"]["timestamp"]["value"]; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div class="pull-right"><a href="javascipt:void(0)" id="reset_agent_amqp"><i class="fa fa-refresh"></i> Reset AMQP Password</a></div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5">AMQP Username</label>
                                        <div class="col-md-9">
                                            <p class="form-control-static  hiderstr" id="appamqpu"><?=$robotic["authenticationAmqpUser"]["value"] ? $HIAS->helpers->oDecrypt($robotic["authenticationAmqpUser"]["value"]) : ""; ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5">AMQP Password</label>
                                        <div class="col-md-9">
                                            <p class="form-control-static  hiderstr"><span id="appamqpp"><?=$robotic["authenticationAmqpKey"]["value"] ? $HIAS->helpers->oDecrypt($robotic["authenticationAmqpKey"]["value"]) : ""; ?></span>
                                            <p><strong>Last Updated:</strong> <?=$robotic["authenticationAmqpKey"]["metadata"]["timestamp"]["value"]; ?></p>
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
        <script type="text/javascript" src="/Robotics/Classes/Robotics.js"></script>

        <script type="text/javascript">

               $(document).ready(function() {
                iotJumpwayUI.HideSecret();
               });

            function initMap() {

                var latlng = new google.maps.LatLng("<?=floatval($robotic["location"]["value"]["coordinates"][0]); ?>", "<?=floatval($robotic["location"]["value"]["coordinates"][1]); ?>");
                var map = new google.maps.Map(document.getElementById('map1'), {
                    zoom: 10,
                    center: latlng
                });

                var loc = new google.maps.LatLng(<?=floatval($robotic["location"]["value"]["coordinates"][0]); ?>, <?=floatval($robotic["location"]["value"]["coordinates"][1]); ?>);
                var marker = new google.maps.Marker({
                    position: loc,
                    map: map,
                    title: 'agent '
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

            Robotics.LifeInterval = setInterval(function() {
                Robotics.updateRoboticsSensorsGraph();
            }, 1000);

        </script>
        <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?=$HIAS->helpers->oDecrypt($HIAS->confs["gmaps"]); ?>&callback=initMap"></script>

    </body>
</html>