<?php session_start();

$pageDetails = [
    "PageID" => "HIASCDI",
    "SubPageID" => "HIASCDI",
    "LowPageID" => "Settings"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../HIASCDI/Classes/Interface.php';

$cb = $HiasInterface->get_hiascdi_entity();
list($dev1On, $dev1Off) = $iotJumpWay->get_device_status($cb["networkStatus"]["value"]);

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
        <link href="/AI/GeniSysAI/Media/CSS/GeniSys.css" rel="stylesheet" type="text/css">
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
                                    <h6 class="panel-title txt-dark">HIAS Contextual Data Interface (HIASCDI)</h6>
                                </div>
                                <div class="pull-right"></div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">

                                    <p>The HIAS Contextual Data Interface (HIASCDI) is an Python implementation of an NGSI V2 Context Broker. The purpose of HIASCDI is to provide easy creation, modification and retrieval of contextual data for HIAS staff, IoT devices, robots, AI models, devices, applicatons and softwares etc. HIASCDI is based on <a href="https://fiware.github.io/specifications/ngsiv2/stable/" target="_BLANK">FIWARE NGSI V2 Specification</a> and implements custom entities/data models based on <a href="https://fiware-datamodels.readthedocs.io/en/latest/index.html" target="_BLANK">FIWARE Data Models</a>.</p>
                                    <p>&nbsp;</p>

                                    <p>Read the official <a href="https://hiascdi.readthedocs.io/en/latest/?badge=latest" target="_BLANK">HIASCDI Documentation</a> for more information.</p>

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
                                            <i class="fas fa-battery-full data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="batteryUsage"><?=$cb["batteryLevel"]["value"]; ?></span>% &nbsp;&nbsp;
                                            <i class="fa fa-microchip data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="cpuUsage"><?=$cb["cpuUsage"]["value"]; ?></span>% &nbsp;&nbsp;
                                            <i class="zmdi zmdi-memory data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="memoryUsage"><?=$cb["memoryUsage"]["value"]; ?></span>% &nbsp;&nbsp;
                                            <i class="far fa-hdd data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="hddUsage"><?=$cb["hddUsage"]["value"]; ?></span>% &nbsp;&nbsp;
                                            <i class="fa fa-thermometer-quarter data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="temperatureUsage"><?=$cb["temperature"]["value"]; ?></span>°C
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
                                <div class="pull-left">
                                    <h6 class="panel-title txt-dark">HIASCDI Service</h6>
                                </div>
                                <div class="pull-right"></div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">

                                    <div class="col-md-12">

                                        <p>The HIASCDI service enables HIASCDI to start when the device it is installed on turns on. The details of the service are as follows:</p>
                                        <p>&nbsp;</p>

                                    </div>
                                    <div class="col-md-6">

                                        <h6>Service Details</h6>
                                        <p><strong>Service Name:</strong> HIASCDI.service<br /><strong>Service Location:</strong> /lib/systemd/system/HIASCDI.service</p>

                                    </div>
                                    <div class="col-md-6">

                                        <h6>Service Usage</h6>
                                        <p><strong>Start:</strong> sudo systemctl start HIASCDI.service<br /><strong>Stop:</strong> sudo systemctl stop HIASCDI.service<br /><strong>Status:</strong> sudo systemctl status HIASCDI.service<br /></p>

                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-heading">
                                <div class="pull-left">
                                    <h6 class="panel-title txt-dark">HIASCDI Configuration</h6>
                                </div>
                                <div class="pull-right"></div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">

                                    <div class="row">
                                        <div class="col-lg-12">

                                            <p>The HIASCDI Configuration is stored in the MySQL database and provides the key configuration settings for HIASCDI & the HIASCDI API.</p>

                                            <p>Read the official <a href="https://hiascdi.readthedocs.io/en/latest/" target="_BLANK">HIASCDI Documentation</a> for more information.</p>
                                            <p>&nbsp;</p>

                                        </div>
                                    </div>

                                    <div class="form-wrap">
                                        <form data-toggle="validator" role="form" id="update_hiascdi_broker">
                                            <div class="row">
                                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Context Broker Entity ID</label>
                                                        <input type="text" class="form-control hider" id="entity" name="entity" placeholder="Context Broker Entity ID" required value="<?=$HIAS->hiascdi->confs["entity"]; ?>">
                                                        <span class="help-block">Entity ID of the context broker.</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Context Broker Endpoint</label>
                                                        <input type="text" class="form-control" id="url" name="url" placeholder="Context Broker URL" required value="<?=$HIAS->hiascdi->confs["url"]; ?>">
                                                        <span class="help-block">URL of the context broker.</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Context Broker Local IP</label>
                                                        <input type="text" class="form-control hider" id="local_ip" name="local_ip" placeholder="Context Broker Local IP" required value="<?=$HIAS->hiascdi->confs["local_ip"]; ?>">
                                                        <span class="help-block">The local IP for the context broker.</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">HIASCDI Version</label>
                                                        <input type="text" class="form-control" id="hdsiv" name="hdsiv" placeholder="Current HDSI Version" required value="<?=$HIAS->hiascdi->confs["hdsiv"]; ?>">
                                                        <span class="help-block">Current HDSI Version.</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">NGSI Version</label>
                                                        <input type="text" class="form-control" id="ngsiv" name="ngsiv" placeholder="Current NGSI Version" required value="<?=$HIAS->hiascdi->confs["ngsiv"]; ?>">
                                                        <span class="help-block">Current NGSI Version.</span>
                                                    </div>
                                                    <div class="form-group mb-0">
                                                        <input type="hidden" class="form-control" id="update_hiascdi_broker" name="update_hiascdi_broker" required value="1">
                                                        <button type="submit" class="btn btn-success btn-anim"><i class="icon-rocket"></i><span class="btn-text">Update Context Broker Settings</span></button>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Entities Endpoint</label>
                                                        <input type="text" class="form-control" id="entities_url" name="entities_url" placeholder="Context Broker Entities enpoint" required value="<?=$HIAS->hiascdi->confs["entities_url"]; ?>">
                                                        <span class="help-block">Listens for entity retrieval, creation and update requests.</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Entity Types Endpoint</label>
                                                        <input type="text" class="form-control" id="types_url" name="types_url" placeholder="Context Broker Types enpoint" required value="<?=$HIAS->hiascdi->confs["types_url"]; ?>">
                                                        <span class="help-block">Listens for entity types retrieval, creation and update requests.</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Commands Endpoint</label>
                                                        <input type="text" class="form-control" id="commands_url" name="commands_url" placeholder="Context Broker Commands enpoint" required value="<?=$HIAS->hiascdi->confs["commands_url"]; ?>">
                                                        <span class="help-block">Listens for commands retrieval, creation and update requests.</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Subscriptions Endpoint</label>
                                                        <input type="text" class="form-control" id="subscriptions_url" name="subscriptions_url" placeholder="Context Broker Subscriptions enpoint" required value="<?=$HIAS->hiascdi->confs["subscriptions_url"]; ?>">
                                                        <span class="help-block">Listens for Subscription retrieval, creation and update requests.</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Registrations Endpoint</label>
                                                        <input type="text" class="form-control" id="registrations_url" name="registrations_url" placeholder="Context Broker Registrations enpoint" required value="<?=$HIAS->hiascdi->confs["registrations_url"]; ?>">
                                                        <span class="help-block">Listens for Regsitrations retrieval, creation and update requests.</span>
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
                            <div class="panel-heading">
                                <div class="pull-left">
                                    <h6 class="panel-title txt-dark">HIASCDI Stats</h6>
                                </div>
                                <div class="pull-right"></div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">

                                    <div class="row">
                                        <div class="col-lg-12">

                                            <p>HIASCDI API Call: <a href="/<?=$HIAS->hiascdi->confs["url"]; ?>" target="_BLANK">/<?=$HIAS->hiascdi->confs["url"]; ?>/</a></p>
                                            <p>&nbsp;</p>

                                        </div>
                                    </div>

                                    <div style="height: 250px; overflow-y: scroll; overflow-x: hidden; padding-right: 10px;">
                                        <?php echo "<pre id='schema'>"; ?> <?php print_r(json_encode($HiasInterface->get_hiascdi_root(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); ?> <?php echo "</pre>"; ?>
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
        <script type="text/javascript" src="/HIASCDI/Classes/HIASCDI.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                HIASCDI.HideInputSecret();
                HIASCDI.Monitor();
            });
        </script>
    </body>
</html>
