<?php session_start();

$pageDetails = [
    "PageID" => "HIASHDI",
    "SubPageID" => "HIASHDI",
    "LowPageID" => "Settings"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../HIASHDI/Classes/Interface.php';

$hb = $HiasHdiInterface->get_hiashdi_entity();
list($dev1On, $dev1Off) = $iotJumpWay->get_device_status($hb["networkStatus"]["value"]);

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
                                    <h6 class="panel-title txt-dark">HIAS Historical Data Interface (HIASHDI)</h6>
                                </div>
                                <div class="pull-right"></div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">

                                    <p>HIASHDI (HIAS Historical Data Interface) is an implementation of a REST API Server. HIASHDI stores and exposes historical data from the HIAS Network,.</p>

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
                                            <i class="fas fa-battery-full data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="batteryUsage"><?=$hb["batteryLevel"]["value"]; ?></span>% &nbsp;&nbsp;
                                            <i class="fa fa-microchip data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="cpuUsage"><?=$hb["cpuUsage"]["value"]; ?></span>% &nbsp;&nbsp;
                                            <i class="zmdi zmdi-memory data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="memoryUsage"><?=$hb["memoryUsage"]["value"]; ?></span>% &nbsp;&nbsp;
                                            <i class="far fa-hdd data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="hddUsage"><?=$hb["hddUsage"]["value"]; ?></span>% &nbsp;&nbsp;
                                            <i class="fa fa-thermometer-quarter data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="temperatureUsage"><?=$hb["temperature"]["value"]; ?></span>°C
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
                                    <h6 class="panel-title txt-dark">HIASHDI Service</h6>
                                </div>
                                <div class="pull-right"></div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">

                                    <div class="col-md-12">

                                        <p>The HIASHDI service enables HIASHDI to start when the device it is installed on turns on. The details of the service are as follows:</p>
                                        <p>&nbsp;</p>

                                    </div>
                                    <div class="col-md-6">

                                        <h6>Service Details</h6>
                                        <p><strong>Service Name:</strong> HIASHDI.service<br /><strong>Service Location:</strong> /lib/systemd/system/HIASHDI.service</p>

                                    </div>
                                    <div class="col-md-6">

                                        <h6>Service Usage</h6>
                                        <p><strong>Start:</strong> sudo systemctl start HIASHDI.service<br /><strong>Stop:</strong> sudo systemctl stop HIASHDI.service<br /><strong>Status:</strong> sudo systemctl status HIASHDI.service<br /></p>

                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-heading">
                                <div class="pull-left">
                                    <h6 class="panel-title txt-dark">HIASHDI Configuration</h6>
                                </div>
                                <div class="pull-right"></div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">

                                    <div class="row">
                                        <div class="col-lg-12">

                                            <p>The HIASHDI Configuration is stored in the MySQL database and provides the key configuration settings for HIASHDI & the HIASHDI API.</p>

                                            <p>Read the official <a href="https://github.com/AIIAL/HIASHDI/blob/main/documentation/getting-started.md" target="_BLANK">HIASHDI Documentation</a> for more information.</p>
                                            <p>&nbsp;</p>

                                        </div>
                                    </div>

                                    <div class="form-wrap">
                                        <form data-toggle="validator" role="form" id="update_hiashdi_broker">
                                            <div class="row">
                                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Historical Broker Entity ID</label>
                                                        <input type="text" class="form-control hider" id="entity" name="entity" placeholder="Historical Broker Entity ID" required value="<?=$HIAS->hiashdi->confs["entity"]; ?>">
                                                        <span class="help-block">Entity ID of the historical broker.</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Historical Broker Endpoint</label>
                                                        <input type="text" class="form-control" id="url" name="url" placeholder="Historical Broker URL" required value="<?=$HIAS->hiashdi->confs["url"]; ?>">
                                                        <span class="help-block">URL of the historical broker.</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Historical Broker Local IP</label>
                                                        <input type="text" class="form-control hider" id="local_ip" name="local_ip" placeholder="Historical Broker Local IP" required value="<?=$HIAS->hiashdi->confs["local_ip"]; ?>">
                                                        <span class="help-block">The local IP for the historical broker.</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">HIASHDI Version</label>
                                                        <input type="text" class="form-control" id="hiashdiv" name="hiashdiv" placeholder="Current HIASHDI Version" required value="<?=$HIAS->hiashdi->confs["hiashdiv"]; ?>">
                                                        <span class="help-block">Current HIASHDI Version.</span>
                                                    </div>
                                                    <div class="form-group mb-0">
                                                        <input type="hidden" class="form-control" id="update_hbroker" name="update_hbroker" required value="1">
                                                        <button type="submit" class="btn btn-success btn-anim"><i class="icon-rocket"></i><span class="btn-text">Update Historical Broker Settings</span></button>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Data Endpoint</label>
                                                        <input type="text" class="form-control" id="data_url" name="data_url" placeholder="Historical Broker Data enpoint" required value="<?=$HIAS->hiashdi->confs["data_url"]; ?>">
                                                        <span class="help-block">Listens for data retrieval, creation and update requests.</span>
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
                                    <h6 class="panel-title txt-dark">HIASHDI Retrieve Broker Response</h6>
                                </div>
                                <div class="pull-right"></div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">

                                    <div class="row">
                                        <div class="col-lg-12">

                                            <p>HIASHDI API Call: <a href="/<?=$HIAS->hiashdi->confs["url"]; ?>" target="_BLANK">/<?=$HIAS->hiashdi->confs["url"]; ?>/</a></p>
                                            <p>&nbsp;</p>

                                        </div>
                                    </div>

                                    <div style="height: 400px; overflow-y: scroll; overflow-x: hidden; padding-right: 10px;">
                                        <?php echo "<pre id='schema'>"; ?> <?php print_r(json_encode($HiasHdiInterface->get_hiashdi_root(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); ?> <?php echo "</pre>"; ?>
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
        <script type="text/javascript" src="/HIASHDI/Classes/HIASHDI.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                HIASHDI.HideSecret();
                HIASHDI.HideInputSecret();
                HIASHDI.Monitor();
            });
        </script>
    </body>
</html>
