<?php session_start();

$pageDetails = [
    "PageID" => "Home"
];


include dirname(__FILE__) . '/../Classes/Core/init.php';
include dirname(__FILE__) . '/iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/iotJumpWay/Classes/iotJumpWayAgents.php';
include dirname(__FILE__) . '/AI/Classes/AiAgents.php';

$TDevice = $iotJumpWay->get_device($HIAS->confs["aid"]);
$stats = $iotJumpWay->get_stats();

$dates = [];
$temperatures = [];

$envDevice = "";
$tempDevices = $iotJumpWay->get_environment_devices();

if(filter_input(INPUT_GET, "device", FILTER_SANITIZE_STRING)):
    $envDevice = filter_input(INPUT_GET, "device", FILTER_SANITIZE_STRING);
elseif(!isSet($tempDevices["Error"])):
    $envDevice = $tempDevices[0]["id"];
endif;

if($envDevice):
    $data = $iotJumpWay->device_sensors_graph([
        "device" => $envDevice,
        "data" => "Temperature",
        "limit" => 100
    ]);
    if($data["Response"] !== "FAILED"):
        $dates = array_column(json_decode(json_encode($data["ResponseData"], true)), 'Time');
        $temperatures = array_column(json_decode(json_encode($data["ResponseData"]), true), 'Value');
        $data = $iotJumpWay->device_sensors_graph([
            "device" => $envDevice,
            "data" => "Humidity",
            "limit" => 100
        ]);
        $humidities = array_column(json_decode(json_encode($data["ResponseData"]), true), 'Value');
        $data = $iotJumpWay->device_sensors_graph([
            "device" => $envDevice,
            "data" => "Light",
            "limit" => 100
        ]);
        $lights = array_column(json_decode(json_encode($data["ResponseData"]), true), 'Value');
        $data = $iotJumpWay->device_sensors_graph([
            "device" => $envDevice,
            "data" => "Smoke",
            "limit" => 100
        ]);
        $smoke = array_column(json_decode(json_encode($data["ResponseData"]), true), 'Value');
        $dates = array_reverse($dates);
        $temperatures = array_reverse($temperatures);
        $humidities = array_reverse($humidities);
        $lights = array_reverse($lights);
        $smoke = array_reverse($smoke);
    endif;
endif;

$cpu = [];
$memory = [];
$diskspace = [];
$temperature = [];
$dates = [];

$data = $HIAS->hiasbch->hiasbch_life_graph($HIAS->confs["aid"], 100);

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

    <body>

        <div class="preloader-it">
            <div class="la-anim-1"></div>
        </div>

        <div class="wrapper theme-6-active pimary-color-pink">

            <?php include dirname(__FILE__) . '/Includes/Nav.php'; ?>
            <?php include dirname(__FILE__) . '/Includes/LeftNav.php'; ?>
            <?php include dirname(__FILE__) . '/Includes/RightNav.php'; ?>

            <div class="page-wrapper">
            <div class="container-fluid pt-25">

                <?php include dirname(__FILE__) . '/Includes/Stats.php'; ?>

                <div class="row">
                    <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-heading">
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <?php include dirname(__FILE__) . '/Includes/Weather.php'; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                        <div class="panel panel-default card-view">
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <?php include dirname(__FILE__) . '/iotJumpWay/Includes/iotJumpWay.php'; ?>
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
                                    <h6 class="panel-title txt-dark">Location Environment Monitoring</h6>
                                </div>
                                <div class="pull-right">
                                    <select class="form-control" id="currentSensor" name="currentSensor" required>
                                        <option value="">PLEASE SELECT</option>
                                        <?php
                                            if(!isSet($tempDevices["Error"])):
                                                foreach($tempDevices as $key => $value):
                                        ?>

                                        <option value="<?=$value["id"]; ?>" <?=$envDevice==$value["id"] ? " selected " : ""; ?>><?=$value["name"]["value"]; ?> (<?=$value["id"]; ?>)</option>

                                        <?php
                                                endforeach;
                                            endif;
                                        ?>

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
                </div>

                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-heading">
                                <div class="pull-left">
                                    <h6 class="panel-title txt-dark">Core Components</h6>
                                </div>
                                <div class="pull-right"></div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">

                                    <div class="scroll_450px">

                                        <?php
                                            $cores = $iotJumpWay->get_core_components("dateCreated,dateModified,*");
                                            if(!isSet($cores["Error"])):
                                                foreach($cores as $key => $value):
                                                    if($value["type"]=="HIASCDI"):
                                                        $curl = "/HIASCDI/";
                                                        $eurl = "/HIASCDI/Entity";
                                                    elseif($value["type"]=="HIASHDI"):
                                                        $curl = "/HIASHDI/";
                                                        $eurl = "/HIASHDI/Entity";
                                                    elseif($value["type"]=="HIASBCH"):
                                                        $curl = "/HIASBCH/";
                                                        $eurl = "/HIASBCH/Entity";
                                                    endif;
                                        ?>

                                        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                            <div class="panel-wrapper collapse in small" style="background: #333; margin: 5px; padding: 10px; color: #fff;">

                                                <div class="row">

                                                    <div class="col-md-12 small">
                                                        <i class="fas fa-battery-full data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idebatU"><?=number_format($value["batteryLevel"]["value"], 2); ?></span>% &nbsp;&nbsp;
                                                        <i class="fa fa-microchip data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idecpuU"><?=number_format($value["cpuUsage"]["value"], 2); ?></span>% &nbsp;&nbsp;
                                                        <i class="zmdi zmdi-memory data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idememU"><?=number_format($value["memoryUsage"]["value"], 2); ?></span>% &nbsp;&nbsp;
                                                        <i class="far fa-hdd data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idehddU"><?=number_format($value["hddUsage"]["value"], 2);?></span>% &nbsp;&nbsp;
                                                        <i class="fa fa-thermometer-quarter data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idetempU"><?=number_format($value["temperature"]["value"], 2); ?></span>°C
                                                    </div>

                                                </div>

                                                <br /><strong>Name:</strong> <a href="<?=$curl; ?>"><?=$value["name"]["value"];?></a><br />
                                                <strong>Type:</strong> <?=$value["type"];?><br />
                                                <strong>Zone:</strong> <a href="/iotJumpWay/<?=$value["networkLocation"]["value"];?>/Zones/<?=$value["networkZone"]["value"];?>"><?=$value["networkZone"]["value"];?></a><br /><br />

                                                <a href="<?=$curl; ?>"><i class="fa fa-edit"></i>&nbsp;Config</a>&nbsp;&nbsp;<a href="<?=$eurl; ?>"><i class="fa fa-edit"></i>&nbsp;Entity</a><br /><br />

                                                <div class="pull-right small"><strong>Last Updated: <?=$value["dateModified"]["value"]; ?></strong></div>

                                                <div class="label label-table <?=$value["networkStatus"]["value"] == "ONLINE" ? "label-success" : "label-danger"; ?>">
                                                    <?=$value["networkStatus"]["value"] == "ONLINE" ? "ONLINE" : "OFFLINE"; ?>
                                                </div>

                                            </div>
                                        </div>

                                        <?php
                                                endforeach;
                                            else:
                                        ?>

                                        <p>No components installed</p>

                                        <?php
                                            endif;
                                        ?>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-heading">
                                <div class="pull-left">
                                    <h6 class="panel-title txt-dark">Network CCTV</h6>
                                </div>
                                <div class="pull-right">
                                    <div class="pull-left inline-block dropdown">
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">

                                    <div class="scroll_450px">

                                        <?php
                                            $agents = $AiAgents->get_agents(0, "AI Agent", "Facial Recognition");
                                            if(!isSet($agents["Error"])):
                                                foreach($agents as $key => $value):
                                        ?>

                                        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">

                                            <img src="/AI/TassAI/<?=$value["endpoint"]["value"]; ?>/strem.mjpg" style="width: 100%;" />

                                        </div>

                                        <?php
                                                endforeach;
                                            else:
                                        ?>

                                        <p>No TassAI CCTV cameras installed</p>

                                        <?php
                                            endif;
                                        ?>

                                    </div>

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
                                    <h6 class="panel-title txt-dark">HIAS Core Statistics</h6>
                                </div>
                                <div class="pull-right"><div class="pull-left inline-block dropdown"></div></div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div id="e_chart_2" class="" style="height: 375px;"></div>
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
                                    <h6 class="panel-title txt-dark">AI Diagnostics Agents</h6>
                                </div>
                                <div class="pull-right"></div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">

                                    <div class="scroll_250px">

                                        <?php
                                            $agents = $AiAgents->get_agents(0, "AI Agent", "Diagnosis");
                                            if(!isSet($agents["Error"])):
                                                foreach($agents as $key => $value):
                                                    $url = "/AI/Agents/". $value["id"];
                                                    if($value["agentType"]["value"]=="Diagnosis"):
                                                        $path = "Diagnosis";
                                                        $link = "Diagnose";
                                                        $icon = "fas fa-microscope";
                                                    endif;
                                                    if($value["agentType"]["value"]=="Facial Recognition"):
                                                        $path = "FacialRecognition";
                                                        $link = "View";
                                                        $icon = "fas fa-video";
                                                    endif;
                                                    if($value["agentType"]["value"]=="Object Detection"):
                                                        $path = "ObjectDetection";
                                                        $link = "View";
                                                        $icon = "fas fa-video";
                                                    endif;
                                                    if($value["agentType"]["value"]=="Natural Language Understanding"):
                                                        $path = "NaturalLanguageUnderstanding";
                                                        $link = "Communicate";
                                                        $icon = "fas fa-comment";
                                                    endif;
                                        ?>

                                        <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
                                            <div class="panel-wrapper collapse in small" style="background: #333; margin: 5px; padding: 10px; color: #fff;">

                                                <div class="row">

                                                    <div class="col-md-12 small">
                                                        <i class="fas fa-battery-full data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idebatU"><?=number_format($value["batteryLevel"]["value"], 2); ?></span>% &nbsp;&nbsp;
                                                        <i class="fa fa-microchip data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idecpuU"><?=number_format($value["cpuUsage"]["value"], 2); ?></span>% &nbsp;&nbsp;
                                                        <i class="zmdi zmdi-memory data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idememU"><?=number_format($value["memoryUsage"]["value"], 2); ?></span>% &nbsp;&nbsp;
                                                        <i class="far fa-hdd data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idehddU"><?=number_format($value["hddUsage"]["value"], 2);?></span>% &nbsp;&nbsp;
                                                        <i class="fa fa-thermometer-quarter data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idetempU"><?=number_format($value["temperature"]["value"], 2); ?></span>°C
                                                    </div>

                                                </div>

                                                <br /><strong>Name:</strong> <a href="<?=$url; ?>"><?=$value["name"]["value"];?></a><br />
                                                <strong>Type:</strong> <?=$value["agentType"]["value"];?><br />
                                                <strong>Zone:</strong> <a href="/iotJumpWay/<?=$value["networkZone"]["value"];?>/Zones/<?=$value["networkZone"]["value"];?>"><?=$value["networkZone"]["value"];?></a><br /><br />

                                                <a href="<?=$url; ?>"><i class="fa fa-edit"></i>&nbsp;Edit</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="/AI/Agents/<?=$value["id"];?>/<?=$path ; ?>/Inference"><i class="<?=$icon; ?>"></i>&nbsp;<?=$link; ?></a><br /><br />

                                                <div class="pull-right small"><strong>Last Updated: <?=$value["dateModified"]["value"]; ?></strong></div>

                                                <div class="label label-table <?=$value["networkStatus"]["value"] == "ONLINE" ? "label-success" : "label-danger"; ?>">
                                                    <?=$value["networkStatus"]["value"] == "ONLINE" ? "ONLINE" : "OFFLINE"; ?>
                                                </div>

                                            </div>
                                        </div>

                                        <?php
                                                endforeach;
                                            else:
                                        ?>

                                        <p>No AI Diagnostics Agents installed</p>

                                        <?php
                                            endif;
                                        ?>

                                    </div>
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
                                    <h6 class="panel-title txt-dark">Network IoT Agents </h6>
                                </div>
                                <div class="pull-right"></div>
                                <div class="pull-right"><a href="/iotJumpWay/Agents"><i class="fa fa-eye"></i> View All</a> | <a href="/iotJumpWay/Agents/Create"><i class="fas fa-plus-circle"></i> Create Agent</a></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">

                                    <div class="scroll_450px">

                                        <?php
                                            $agents = $iotJumpWayAgents->get_agents(0, "dateCreated,dateModified,*", "IoT Agent");
                                            if(!isSet($agents["Error"])):
                                                foreach($agents as $key => $value):
                                                    if($value["category"]["value"][0]=="IoT Agent"):
                                                        $url = "/iotJumpWay/Agents/Agent/". $value["id"];
                                                    else:
                                                        $url = "/AI/Agents/". $value["id"];
                                                    endif;
                                        ?>

                                        <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
                                            <div class="panel-wrapper collapse in small" style="background: #333; margin: 5px; padding: 10px; color: #fff;">

                                                <div class="row">

                                                    <div class="col-md-12 small">
                                                        <i class="fas fa-battery-full data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idebatU"><?=number_format($value["batteryLevel"]["value"], 2); ?></span>% &nbsp;&nbsp;
                                                        <i class="fa fa-microchip data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idecpuU"><?=number_format($value["cpuUsage"]["value"], 2); ?></span>% &nbsp;&nbsp;
                                                        <i class="zmdi zmdi-memory data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idememU"><?=number_format($value["memoryUsage"]["value"], 2); ?></span>% &nbsp;&nbsp;
                                                        <i class="far fa-hdd data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idehddU"><?=number_format($value["hddUsage"]["value"], 2);?></span>% &nbsp;&nbsp;
                                                        <i class="fa fa-thermometer-quarter data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idetempU"><?=number_format($value["temperature"]["value"], 2); ?></span>°C
                                                    </div>

                                                </div>

                                                <br /><strong>Name:</strong> <a href="<?=$url; ?>"><?=$value["name"]["value"];?></a><br />
                                                <strong>Type:</strong> <?=$value["category"]["value"][0];?><br />
                                                <strong>Zone:</strong> <a href="/iotJumpWay/<?=$value["networkZone"]["value"];?>/Zones/<?=$value["networkZone"]["value"];?>"><?=$value["networkZone"]["value"];?></a><br /><br />

                                                <a href="<?=$url; ?>"><i class="fa fa-edit"></i>&nbsp;Edit</a><br /><br />

                                                <div class="pull-right small"><strong>Last Updated: <?=$value["dateModified"]["value"]; ?></strong></div>

                                                <div class="label label-table <?=$value["networkStatus"]["value"] == "ONLINE" ? "label-success" : "label-danger"; ?>">
                                                    <?=$value["networkStatus"]["value"] == "ONLINE" ? "ONLINE" : "OFFLINE"; ?>
                                                </div>

                                            </div>
                                        </div>

                                        <?php
                                                endforeach;
                                            else:
                                        ?>

                                        <p>No network IoT Agents installed</p>

                                        <?php
                                            endif;
                                        ?>

                                    </div>
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
                                    <h6 class="panel-title txt-dark">Network AI Agents</h6>
                                </div>
                                <div class="pull-right"><a href="/iotJumpWay/Agents"><i class="fa fa-eye"></i> View All</a> | <a href="/iotJumpWay/Agents/Create"><i class="fas fa-plus-circle"></i> Create Agent</a></div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">

                                    <div class="scroll_450px">

                                        <?php
                                            $agents = $iotJumpWayAgents->get_agents(0, "dateCreated,dateModified,*", "AI Agent");
                                            if(!isSet($agents["Error"])):
                                                foreach($agents as $key => $value):
                                                    if($value["category"]["value"][0]=="IoT Agent"):
                                                        $url = "/iotJumpWay/Agents/Agent/". $value["id"];
                                                    else:
                                                        $url = "/AI/Agents/". $value["id"];
                                                    endif;
                                                    if($value["agentType"]["value"]=="Diagnosis"):
                                                        $path = "Diagnosis";
                                                        $link = "Diagnose";
                                                        $icon = "fas fa-microscope";
                                                    endif;
                                                    if($value["agentType"]["value"]=="Facial Recognition"):
                                                        $path = "FacialRecognition";
                                                        $link = "View";
                                                        $icon = "fas fa-video";
                                                    endif;
                                                    if($value["agentType"]["value"]=="Object Detection"):
                                                        $path = "ObjectDetection";
                                                        $link = "View";
                                                        $icon = "fas fa-video";
                                                    endif;
                                                    if($value["agentType"]["value"]=="Natural Language Understanding"):
                                                        $path = "NaturalLanguageUnderstanding";
                                                        $link = "Communicate";
                                                        $icon = "fas fa-comment";
                                                    endif;
                                        ?>

                                        <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
                                            <div class="panel-wrapper collapse in small" style="background: #333; margin: 5px; padding: 10px; color: #fff;">

                                                <div class="row">

                                                    <div class="col-md-12 small">
                                                        <i class="fas fa-battery-full data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idebatU"><?=number_format($value["batteryLevel"]["value"], 2); ?></span>% &nbsp;&nbsp;
                                                        <i class="fa fa-microchip data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idecpuU"><?=number_format($value["cpuUsage"]["value"], 2); ?></span>% &nbsp;&nbsp;
                                                        <i class="zmdi zmdi-memory data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idememU"><?=number_format($value["memoryUsage"]["value"], 2); ?></span>% &nbsp;&nbsp;
                                                        <i class="far fa-hdd data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idehddU"><?=number_format($value["hddUsage"]["value"], 2);?></span>% &nbsp;&nbsp;
                                                        <i class="fa fa-thermometer-quarter data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idetempU"><?=number_format($value["temperature"]["value"], 2); ?></span>°C
                                                    </div>

                                                </div>

                                                <br /><strong>Name:</strong> <a href="<?=$url; ?>"><?=$value["name"]["value"];?></a><br />
                                                <strong>Type:</strong> <?=$value["agentType"]["value"];?><br />
                                                <strong>Zone:</strong> <a href="/iotJumpWay/<?=$value["networkLocation"]["value"];?>/Zones/<?=$value["networkZone"]["value"];?>"><?=$value["networkZone"]["value"];?></a><br /><br />

                                                <a href="<?=$url; ?>"><i class="fa fa-edit"></i>&nbsp;Edit</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="/AI/Agents/<?=$value["id"];?>/<?=$path ; ?>/Inference"><i class="<?=$icon; ?>"></i>&nbsp;<?=$link; ?></a><br /><br />

                                                <div class="pull-right small"><strong>Last Updated: <?=$value["dateModified"]["value"]; ?></strong></div>

                                                <div class="label label-table <?=$value["networkStatus"]["value"] == "ONLINE" ? "label-success" : "label-danger"; ?>">
                                                    <?=$value["networkStatus"]["value"] == "ONLINE" ? "ONLINE" : "OFFLINE"; ?>
                                                </div>

                                            </div>
                                        </div>

                                        <?php
                                                endforeach;
                                            else:
                                        ?>

                                        <p>No network AI Agents installed</p>

                                        <?php
                                            endif;
                                        ?>

                                    </div>
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
                                    <h6 class="panel-title txt-dark">Network Devices</h6>
                                </div>
                                <div class="pull-right"><a href="/iotJumpWay/Devices"><i class="fa fa-eye"></i> View All</a> | <a href="/iotJumpWay/Devices/Create"><i class="fas fa-plus-circle"></i> Create Device</a></div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">

                                    <div class="scroll_450px">

                                        <?php
                                            $iDevices = $iotJumpWay->get_devices();
                                            if(!isSet($iDevices["Error"])):
                                                foreach($iDevices as $key => $value):
                                        ?>

                                        <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
                                            <div class="panel-wrapper collapse in small" style="background: #333; margin: 5px; padding: 10px; color: #fff;">

                                                <div class="row">

                                                    <div class="col-md-12 small">
                                                        <i class="fas fa-battery-full data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idebatU"><?=number_format($value["batteryLevel"]["value"], 2); ?></span>% &nbsp;&nbsp;
                                                        <i class="fa fa-microchip data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idecpuU"><?=number_format($value["cpuUsage"]["value"], 2); ?></span>% &nbsp;&nbsp;
                                                        <i class="zmdi zmdi-memory data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idememU"><?=number_format($value["memoryUsage"]["value"], 2); ?></span>% &nbsp;&nbsp;
                                                        <i class="far fa-hdd data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idehddU"><?=number_format($value["hddUsage"]["value"], 2);?></span>% &nbsp;&nbsp;
                                                        <i class="fa fa-thermometer-quarter data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idetempU"><?=number_format($value["temperature"]["value"], 2); ?></span>°C
                                                    </div>

                                                </div>

                                                <br /><strong>Name:</strong> <a href="/iotJumpWay/<?=$value["networkLocation"]["value"];?>/Zones/<?=$value["networkZone"]["value"];?>/Devices/<?=$value["id"];?>"><?=$value["name"]["value"];?></a><br />
                                                <strong>Category:</strong> <?=$value["category"]["value"][0];?><br />
                                                <strong>Device:</strong> <?=$value["modelName"]["value"];?><br />
                                                <strong>Zone:</strong> <a href="/iotJumpWay/<?=$value["networkZone"]["value"];?>/Zones/<?=$value["networkZone"]["value"];?>"><?=$value["networkZone"]["value"];?></a><br /><br />

                                                <a href="/iotJumpWay/<?=$value["networkLocation"]["value"];?>/Zones/<?=$value["networkZone"]["value"];?>/Devices/<?=$value["id"];?>"><i class="fa fa-edit"></i>&nbsp;Edit</a><br /><br />

                                                <div class="pull-right small"><strong>Last Updated: <?=$value["dateModified"]["value"]; ?></strong></div>

                                                <div class="label label-table <?=$value["networkStatus"]["value"] == "ONLINE" ? "label-success" : "label-danger"; ?>">
                                                    <?=$value["networkStatus"]["value"] == "ONLINE" ? "ONLINE" : "OFFLINE"; ?>
                                                </div>

                                            </div>
                                        </div>

                                        <?php
                                                endforeach;
                                            else:
                                        ?>

                                        <p>No network devices installed</p>

                                        <?php
                                            endif;
                                        ?>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <?php include dirname(__FILE__) . '/Includes/Footer.php'; ?>

        </div>

        <?php  include dirname(__FILE__) . '/Includes/JS.php'; ?>

        <script type="text/javascript" src="/vendors/bower_components/echarts/dist/echarts-en.min.js"></script>
        <script type="text/javascript" src="/vendors/echarts-liquidfill.min.js"></script>
        <script type="text/javascript">

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
                color: ['#635bd6'],
                xAxis: {
                    type: 'category',
                    axisLabel: {
                        textStyle: {
                            color: '#ffffff'
                        },
                        interval: 1,
                        rotate: 45
                    },
                    data: <?=json_encode($dates); ?>
                },
                yAxis: {
                    axisLabel: {
                        textStyle: {
                            color: '#ffffff'
                        }
                    },
                    type: 'value'
                },
                grid: {
                    top: 10,
                    left: 0,
                    right: 0,
                    bottom: 100,
                    containLabel: true
                },
                series: [{
                    name: 'Temperature',
                    data: <?=json_encode($temperatures); ?>,
                    type: 'line',
                    smooth: true,
                    color: ['red'],
                },{
                    name: 'Humidity',
                    data: <?=json_encode($humidities); ?>,
                    type: 'line',
                    smooth: true
                },{
                    name: 'Light',
                    data: <?=json_encode($lights); ?>,
                    type: 'line',
                    smooth: true,
                    color: ['cyan'],
                },{
                    name: 'Smoke',
                    data: <?=json_encode($smoke); ?>,
                    type: 'line',
                    smooth: true,
                    color: ['gray'],
                }]
            };
            eChart_1.setOption(option);
            eChart_1.resize();

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
                color: ['#635bd6'],
                xAxis: {
                    type: 'category',
                    axisLabel: {
                        textStyle: {
                            color: '#ffffff'
                        },
                        interval: 1,
                        rotate: 45
                    },
                    data: <?=json_encode($dates); ?>
                },
                yAxis: {
                    axisLabel: {
                        textStyle: {
                            color: '#ffffff'
                        }
                    },
                    type: 'value'
                },
                grid: {
                    top: 10,
                    left: 0,
                    right: 0,
                    bottom: 100,
                    containLabel: true
                },
                series: [{
                    name: 'Temperature',
                    data: <?=json_encode($temperatures); ?>,
                    type: 'line',
                    smooth: true,
                    color: ['red'],
                },{
                    name: 'Humidity',
                    data: <?=json_encode($humidities); ?>,
                    type: 'line',
                    smooth: true
                },{
                    name: 'Light',
                    data: <?=json_encode($lights); ?>,
                    type: 'line',
                    smooth: true,
                    color: ['cyan'],
                },{
                    name: 'Smoke',
                    data: <?=json_encode($smoke); ?>,
                    type: 'line',
                    smooth: true,
                    color: ['gray'],
                }]
            };
            eChart_1.setOption(option);
            eChart_1.resize();


            var hiasbch_stats = echarts.init(document.getElementById('e_chart_2'));

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
            hiasbch_stats.setOption(option);
            hiasbch_stats.resize();
        </script>
        <script type="text/javascript" src="/iotJumpWay/Classes/mqttws31.js"></script>
        <script type="text/javascript" src="/iotJumpWay/Classes/iotJumpWay.js"></script>
        <script type="text/javascript" src="/iotJumpWay/Classes/iotJumpWayUI.js"></script>
        <script type="text/javascript" src="/HIASBCH/Classes/HIASBCH.js"></script>
        <script type="text/javascript">

            setInterval(function() {
                iotJumpwayUI.updateHomeSensors();
                HIASBCH.updateLifeGraph();
            }, 60000);

        </script>


    </body>
</html>
