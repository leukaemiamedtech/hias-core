<?php session_start();

$pageDetails = [
    "PageID" => "IoT",
    "SubPageID" => "Data",
    "LowPageID" => "Commands"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';

$lId = 1;
$Location = $iotJumpWay->get_location($lId);
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
        <link href="/vendors/bower_components/fullcalendar/dist/fullcalendar.css" rel="stylesheet" type="text/css"/>
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
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-heading">
                                <div class="pull-left">
                                    <h6 class="panel-title txt-dark">Entity Commmands</h6>
                                </div>
                                <div class="pull-right"><a href="/iotJumpWay/Data/Commands"><i class="fa fa-eye pull-left"></i> View All Commmand Data</a></div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div class="table-wrap mt-40">
                                        <div class="table-responsive">
                                            <table class="table mb-0">
                                                <thead>
                                                  <tr>
                                                    <th>ID</th>
                                                    <th>Details</th>
                                                    <th>Type</th>
                                                    <th>Value</th>
                                                    <th>Message</th>
                                                    <th>Time</th>
                                                    <th>ACTION</th>
                                                  </tr>
                                                </thead>
                                                <tbody>

                                                <?php
                                                    $Commands = $iotJumpWay->retrieve_command_data(300);
                                                    if($Commands["Response"] == "OK" && !isSet($Commands["ResponseData"]["Error"])):
                                                        foreach($Commands["ResponseData"] as $key => $value):
															$hashString = (string)$value["From"] . (string)$value["Actuator"] . (string)$value["Type"] . (string)$value["Value"] . (string)$value["Message"] ;
                                                ?>

                                                <tr>
                                                  <td>#<?=$value["_id"]['$oid'];?></td>
                                                  <td>
                                                      Location #<?=$value["Location"];?><br />
                                                      Zone <?=$value["Zone"] != 0 ? "#" . $value["Zone"] : "NA"; ?><br />
                                                      From <?=$value["From"]; ?><br />
                                                      To <?=$value["Device"]; ?><br />
                                                  </td>
                                                  <td><?=$value["Type"];?></td>
                                                  <td><?=$value["Value"];?></td>
                                                  <td><?=$value["Message"];?></td>
                                                  <td><?=$value["Time"];?> </td>
                                                  <td><a href="javascript:void(0);" class="btn btn-success btn-anim verify" data-user="<?=$_SESSION["HIAS"]["BC"]["BCUser"];?>" data-key="<?=$value["_id"]['$oid'];?>" data-hash="<?=$hashString; ?>"><span class="btn-text">VERIFY</span></button></td>
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

            </div>

            <?php include dirname(__FILE__) . '/../Includes/Footer.php'; ?>

        </div>

        <div id="abi" class="hide"><?php echo json_encode($HIAS->hiasbch->confs["iabi"]); ?></div>
        <div id="address" class="hide"><?=$HIAS->hiasbch->confs["icontract"]; ?></div>

        <?php  include dirname(__FILE__) . '/../Includes/JS.php'; ?>

        <script type="text/javascript" src="/iotJumpWay/Classes/mqttws31.js"></script>
        <script type="text/javascript" src="/iotJumpWay/Classes/iotJumpWay.js"></script>
        <script type="text/javascript" src="/HIASBCH/Classes/HIASBCH.js"></script>
        <script type="text/javascript" src="/HIASBCH/Classes/web3.js"></script>
        <script type="text/javascript">

            window.addEventListener('load', function () {
                HIASBCH.connect("/hiasbch/api/");
                if(HIASBCH.isConnected()){
                    msg = "Connected to HIASBCH!";
                    Logging.logMessage("Core", "HIASBCH", msg);
                } else {
                    msg = "Connection to HIASBCH failed!";
                    Logging.logMessage("Core", "HIASBCH", msg);
                }
            });
        </script>

    </body>

</html>
