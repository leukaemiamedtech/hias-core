<?php session_start();

$pageDetails = [
    "PageID" => "HIASBCH",
    "SubPageID" => "HIASBCH",
    "LowPageID" => "Settings"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';

$hiasbch = $HIAS->hiasbch->get_hiasbch();
list($dev1On, $dev1Off) = $iotJumpWay->get_device_status($hiasbch["networkStatus"]["value"]);
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
                                        <h6 class="panel-title txt-dark">The HIASBCH Private Ethereum Blockchain</h6>
                                    </div>
                                    <div class="pull-right"></div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="panel-wrapper collapse in">
                                    <div class="panel-body">
                                        <div class="form-wrap">
                                            <form data-toggle="validator" role="form" id="bc_config">
                                                <div class="row">
                                                    <div class="col-lg-12col-md-12 col-sm-12 col-xs-12">
                                                        <div class="form-group">

                                                            <p>HIAS Blockchain (HIASBCH) is a private blockchain created using Ethereum. The blockchain provides permissions management for HIAS staff, devices, applications, and agents; and also handles data integrity for data that is published through the HIAS network.</p>
                                                            <p>&nbsp;</p>

                                                            <p>HIAS Ether is mined on HIASBCH using the HIAS blockchain account. This Ether is used to replenish HIAS smart contracts, HIAS core blockchain features, and HIAS UI users.</p>
                                                            <p>&nbsp;</p>

                                                            <p>Read the official <a href="https://github.com/AIIAL/HIASBCH/docs/" target="_BLANK">HIASBCH Documentation</a> for more information.</p>
                                                            <p>&nbsp;</p>

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
                                            <div class="col-md-12">
                                                <i class="fas fa-battery-full data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="batteryUsage"><?=$hiasbch["batteryLevel"]["value"]; ?></span>% &nbsp;&nbsp;
                                                <i class="fa fa-microchip data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="cpuUsage"><?=$hiasbch["cpuUsage"]["value"]; ?></span>% &nbsp;&nbsp;
                                                <i class="zmdi zmdi-memory data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="memoryUsage"><?=$hiasbch["memoryUsage"]["value"]; ?></span>% &nbsp;&nbsp;
                                                <i class="far fa-hdd data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="hddUsage"><?=$hiasbch["hddUsage"]["value"]; ?></span>% &nbsp;&nbsp;
                                                <i class="fa fa-thermometer-quarter data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="temperatureUsage"><?=$hiasbch["temperature"]["value"]; ?></span>°C
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                            <div class="panel panel-default card-view panel-refresh">
                                <div class="panel-heading">
                                    <div class="pull-left">
                                        <h6 class="panel-title txt-dark">Latest HIASBCH Blocks</h6>
                                    </div>
                                    <div class="pull-right"></div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="panel-wrapper collapse in">
                                    <div class="panel-body">

                                        <div class="form-group">

                                            <table class="table">

                                                <tr>
                                                    <td>Block</td>
                                                    <td>Size</td>
                                                    <td>Miner</td>
                                                </tr>


                                                <?php
                                                    $blocks = $HIAS->hiasbch->get_blockchain_historical("Blocks", 11);
                                                    if($blocks["Response"]=="OK"):
                                                        foreach($blocks["ResponseData"] as $key => $value):
                                                ?>

                                                <tr>
                                                    <td><a href="/HIASBCH/Explorer/Block/<?=$value["Hash"]; ?>" title="<?=$value["Hash"]; ?>"><?=$value["Block"]; ?></a><br /><span style="font-size: 8;"><?=($HIAS->helpers->time_ago(time()-$value["Timestamp"])); ?> ago</span></td>
                                                    <td><?=$value["Size"]; ?> bytes</td>
                                                    <td><a href="/HIASBCH/Explorer/Address/<?=$value["Miner"]; ?>" title="<?=$value["Miner"]; ?>"><?=(strlen($value["Miner"]) > 40) ? substr($value["Miner"],0,20).'...' : $value["Miner"]; ?></a></td>
                                                </tr>

                                                <?php
                                                        endforeach;
                                                    endif;
                                                ?>
                                            </table>


                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                            <div class="panel panel-default card-view panel-refresh">
                                <div class="panel-heading">
                                    <div class="pull-left">
                                        <h6 class="panel-title txt-dark">HIASBCH Service</h6>
                                    </div>
                                    <div class="pull-right"></div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="panel-wrapper collapse in">
                                    <div class="panel-body">

                                        <div class="col-md-12">

                                            <p>The HIASBCH service enables HIASBCH to start when the device it is installed on turns on. The details of the service are as follows:</p>
                                            <p>&nbsp;</p>

                                        </div>
                                        <div class="col-md-6">

                                            <h6>Service Details</h6>
                                            <p><strong>Service Name:</strong> HIASBCH.service<br /><strong>Service Location:</strong> /lib/systemd/system/HIASBCH.service</p>

                                        </div>
                                        <div class="col-md-6">

                                            <h6>Service Usage</h6>
                                            <p><strong>Start:</strong> sudo systemctl start HIASBCH.service<br /><strong>Stop:</strong> sudo systemctl stop HIASBCH.service<br /><strong>Status:</strong> sudo systemctl status HIASBCH.service<br /></p>

                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default card-view panel-refresh">
                                <div class="panel-heading">
                                    <div class="pull-left">
                                        <h6 class="panel-title txt-dark">Latest HIASBCH Transactions</h6>
                                    </div>
                                    <div class="pull-right"></div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="panel-wrapper collapse in">
                                    <div class="panel-body">
                                        <div class="form-wrap">
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                    <div class="form-group">

                                                        <table class="table">

                                                            <tr>
                                                                <td>Block</td>
                                                                <td>Details</td>
                                                            </tr>


                                                            <?php
                                                                $blocks = $HIAS->hiasbch->get_blockchain_historical("Transactions", 6, "!BlockNumber");
                                                                if($blocks["Response"]=="OK"):
                                                                    foreach($blocks["ResponseData"] as $key => $value):
                                                                        $block = $HIAS->hiasbch->get_block($value["BlockHash"]);
                                                            ?>

                                                            <tr>
                                                                <td><a href="/HIASBCH/Explorer/Block/<?=$value["BlockHash"]; ?>" title="<?=$value["BlockHash"]; ?>"><?=$value["BlockNumber"]; ?></a><br /></td>
                                                                <td>

                                                                    <div class="row">
                                                                        <div class="col-lg-1 col-md-12 col-sm-12 col-xs-12">Hash:</div>
                                                                        <div class="col-lg-11 col-md-12 col-sm-12 col-xs-12">

                                                                            <a href="/HIASBCH/Explorer/Transaction/<?=$value["Hash"]; ?>" title="<?=$value["Hash"]; ?>"><?=$value["Hash"]; ?></a>

                                                                        </div>
                                                                        <div class="col-lg-1 col-md-12 col-sm-12 col-xs-12">Fee:</div>
                                                                        <div class="col-lg-11 col-md-12 col-sm-12 col-xs-12">

                                                                            <?=$value["Gas"] * $value["GasPrice"]; ?>

                                                                        </div>
                                                                        <div class="col-lg-1 col-md-12 col-sm-12 col-xs-12">From:</div>
                                                                        <div class="col-lg-11 col-md-12 col-sm-12 col-xs-12">

                                                                            <a href="/HIASBCH/Explorer/Address/<?=$value["From"]; ?>" title="<?=$value["From"]; ?>"><?=$value["From"]; ?></a>

                                                                        </div>
                                                                        <div class="col-lg-1  col-md-12 col-sm-12 col-xs-12">To:</div>
                                                                        <div class="col-lg-11 col-md-12 col-sm-12 col-xs-12">

                                                                        <?php if($value["To"]): ?>
                                                                            <a href="/HIASBCH/Explorer/Address/<?=$value["To"]; ?>" title="<?=$value["To"]; ?>"><?=$value["To"]; ?></a>
                                                                        <?php else: ?>
                                                                            Contract Creation
                                                                        <?php endif; ?>

                                                                        </div>
                                                                        <div class="col-lg-1 col-md-12 col-sm-12 col-xs-12">At:</div>
                                                                        <div class="col-lg-11 col-md-12 col-sm-12 col-xs-12">

                                                                            <?=date("Y-m-d H:i:s", hexdec($block["Data"]->timestamp)); ?> (<span style="font-size: 8;"><?=($HIAS->helpers->time_ago(time()-hexdec($block["Data"]->timestamp))); ?> ago</span>)

                                                                        </div>
                                                                    </div>

                                                                </td>
                                                            </tr>

                                                            <?php
                                                                    endforeach;
                                                                endif;
                                                            ?>
                                                        </table>

                                                    </div>
                                                </div>
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
        <script type="text/javascript" src="/HIASBCH/Classes/HIASBCH.js"></script>
        <script type="text/javascript">

            $(document).ready(function() {
                HIASBCH.hideSecret();
                HIASBCH.hideInputs();
            });
        </script>
    </body>
</html>
