<?php session_start();

$pageDetails = [
    "PageID" => "HIS",
    "SubPageID" => "Staff",
    "LowPageID" => "Active"
];

include dirname(__FILE__) . '/../../../Classes/Core/init.php';
include dirname(__FILE__) . '/../../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../../Users/Staff/Classes/Staff.php';

$SId = filter_input(INPUT_GET,  'staff', FILTER_SANITIZE_STRING);
$Staffer = $Staff->get_staff($SId, "dateCreated,dateModified,id,*");

list($on, $off) = $iotJumpWay->get_application_status($Staffer["networkStatus"]["value"]);

$cancelled = $Staffer["permissionsCancelled"]["value"] ? True : False;

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

        <?php include dirname(__FILE__) . '/../../Includes/Nav.php'; ?>
        <?php include dirname(__FILE__) . '/../../Includes/LeftNav.php'; ?>
        <?php include dirname(__FILE__) . '/../../Includes/RightNav.php'; ?>

        <div class="page-wrapper">
            <div class="container-fluid pt-25">

                <?php include dirname(__FILE__) . '/../../Includes/Stats.php'; ?>

                <div class="row">
                    <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-heading">
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <?php include dirname(__FILE__) . '/../../Includes/Weather.php'; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                        <div class="panel panel-default card-view">
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <?php include dirname(__FILE__) . '/../../iotJumpWay/Includes/iotJumpWay.php'; ?>
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
                                    <img src="/Users/Staff/Media/Images/Uploads/<?=$Staffer["picture"]["value"];?>" style="width: 5%; !important; float: left; margin-right: 10px; margin-bottom: 5px;" /><?=$SId; ?><br /><h6 class="panel-title txt-dark">HIAS Staff</h6>

                                </div>
                                <div class="pull-right"><a href="javascipt:void(0);" id="reset_pass"><i class="fa fa-refresh"></i> Reset Password</a></div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div class="form-wrap">
                                        <form data-toggle="validator" role="form" id="staff_update">
                                            <hr class="light-grey-hr" />
                                            <div class="row">
                                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Name</label>
                                                        <input type="text" class="form-control" id="name" name="name" placeholder="Staff Name" required value="<?=$Staffer["name"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block">Name of staff</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Description</label>
                                                        <input type="text" class="form-control" id="description" name="description" placeholder="Device Description" required value="<?=$Staffer["description"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block">Staff description</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Username</label>
                                                        <input type="text" class="form-control" id="username" name="username" placeholder="Staff Username" required value="<?=$Staffer["username"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block"> Username of staff</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label mb-10">Category</label>
                                                        <select class="form-control" id="category" name="category" required <?=$cancelled ? " disabled " : ""; ?>>
                                                            <option value="">PLEASE SELECT</option>

                                                            <?php
                                                                $categories = $Staff->get_staff_categories();
                                                                if(count($categories)):
                                                                    foreach($categories as $key => $value):
                                                            ?>

                                                            <option value="<?=$value["category"]; ?>" <?=$Staffer["category"]["value"][0]==$value["category"] ? " selected " : ""; ?>><?=$value["category"]; ?></option>

                                                            <?php
                                                                    endforeach;
                                                                endif;
                                                            ?>

                                                        </select>
                                                        <span class="help-block">Staff category</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Email *</label>
                                                        <input type="text" class="form-control" id="email" name="email" placeholder="Email of staff member" required value="<?=$Staffer["email"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block">Email of staff member</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Address Street Address</label>
                                                        <input type="text" class="form-control" id="streetAddress" name="streetAddress" placeholder="iotJumpWay Location street address" required value="<?=$Staffer["address"]["value"]["streetAddress"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block">iotJumpWay Location street address</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Address Locality</label>
                                                        <input type="text" class="form-control" id="addressLocality" name="addressLocality" placeholder="iotJumpWay Location address locality" required value="<?=$Staffer["address"]["value"]["addressLocality"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block">iotJumpWay Location address locality</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Address Postal Code</label>
                                                        <input type="text" class="form-control" id="postalCode" name="postalCode" placeholder="iotJumpWay Location postal code" required value="<?=$Staffer["address"]["value"]["postalCode"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block">iotJumpWay Location post code</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Bluetooth Address</label>
                                                        <input type="text" class="form-control" id="bluetooth" name="bluetooth" placeholder="Bluetooth address"  value="<?=$Staffer["nfcAddress"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block">Bluetooth address of staff member</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">NFC UID</label>
                                                        <input type="text" class="form-control" id="nfc" name="nfc" placeholder="NFC UID"  value="<?=$Staffer["nfcAddress"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block">UID of staff member's NFC card/fob/implant</span>
                                                    </div>
                                                    <?php if(!$cancelled): ?>
                                                    <div class="form-group mb-0">
                                                        <input type="hidden" class="form-control" id="update_staff" name="update_staff" required value="1">
                                                        <button type="submit" class="btn btn-success btn-anim"><i class="icon-rocket"></i><span class="btn-text">Update Staff</span></button>
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
                                                                if(!isSet($Locations["Error"])):
                                                                    foreach($Locations as $key => $value):
                                                            ?>

                                                                <option value="<?=$value["id"]; ?>" <?=$value["id"] == $Staffer["networkLocation"]["value"] ? " selected " : ""; ?>><?=$value["name"]["value"]; ?></option>

                                                            <?php
                                                                    endforeach;
                                                                endif;
                                                            ?>

                                                        </select>
                                                        <span class="help-block"> Location of staff</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Is Admin:</label>
                                                        <input type="checkbox" class="" id="admin" name="admin" value=1 <?=$Staffer["permissionsAdmin"]["value"] ? " checked " : ""; ?> <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block">Is staff member an admin?</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Is cancelled:</label>
                                                        <input type="checkbox" class="" id="cancelled" name="cancelled" value=1 <?=$Staffer["permissionsCancelled"]["value"] ? " checked " : ""; ?> <?=$cancelled ? " disabled " : ""; ?>>
                                                        <span class="help-block">Is staff member cancelled?</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Date Created</label>
                                                        <p><?=$Staffer["dateCreated"]["value"]; ?></p>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Date First Used</label>
                                                        <p><?=$Staffer["dateFirstUsed"]["value"] ? $Staffer["dateFirstUsed"]["value"] : "NA"; ?></p>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Date Modified</label>
                                                        <p><?=$Staffer["dateModified"]["value"]; ?></p>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <hr class="light-grey-hr" />
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div><br />
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-heading">
                                <div class="pull-left">
                                    <h6 class="panel-title txt-dark">Staff History</h6>
                                </div>
                                <div class="pull-right">
                                    <select class="form-control" id="staffHistory" name="staffHistory" required>
                                        <option value="Activity">Staff Activity</option>
                                        <option value="Transactions">Staff Transactions</option>
                                        <option value="Statuses">Staff Statuses</option>
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
                                                    <tbody id="staffHistoryContainer">

                                                    <?php
                                                        $userDetails = "";
                                                        $history = $Staff->get_user_history($Staffer["id"], 100);
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
                        </div><br />
                    </div>
                    <?php if(!$cancelled): ?>
                    <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-heading">
                                <div class="pull-left">
                                    <h6 class="panel-title txt-dark">Status</h6>
                                </div>
                                <div class="pull-right"><span id="offline3" style="color: #33F9FF !important;" class="<?=$on; ?>"><i class="fas fa-power-off" style="color: #33F9FF !important;"></i> Online</span> <span id="online3" class="<?=$off; ?>" style="color: #99A3A4 !important;"><i class="fas fa-power-off" style="color: #99A3A4 !important;"></i> Offline</span></div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div id="map1" style="height:300px;"></div>
                                </div>
                            </div>
                        </div><br />
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-heading">
                                <div class="pull-left">
                                    <h6 class="panel-title txt-dark">User Schema</h6>
                                </div>
                                <div class="pull-right"></div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div  style="height: 400px; overflow-y: scroll; overflow-x: hidden;">
                                        <?php echo "<pre id='schema'>"; ?> <?php print_r(json_encode($Staffer, JSON_PRETTY_PRINT)); ?> <?php echo "</pre>"; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div class="pull-right"><a href="javascipt:void(0)" id="reset_staff_apriv"><i
                                                class="fa fa-refresh"></i> Reset API Key</a></div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Identifier</label>
                                        <div class="col-md-9">
                                            <p class="form-control-static hiderstr" id="usrappid"><?=$Staffer["authenticationUser"]["value"]; ?></p>
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
                                            <p class="form-control-static hiderstr" id="usrbcid"><?=$Staffer["authenticationBlockchainUser"]["value"]; ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Blockchain Key</label>
                                        <div class="col-md-9">
                                            <p class="form-control-static hiderstr" id="usrbcp"><?=$HIAS->helpers->oDecrypt($Staffer["authenticationBlockchainKey"]["value"]); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div class="pull-right"><a href="javascipt:void(0)" id="reset_staff_mqtt"><i
                                                class="fa fa-refresh"></i> Reset MQTT Password</a></div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5">MQTT Username</label>
                                        <div class="col-md-9">
                                            <p class="form-control-static hiderstr" id="usrmqttu"><?=$HIAS->helpers->oDecrypt($Staffer["authenticationMqttUser"]["value"]); ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5">MQTT Password</label>
                                        <div class="col-md-9">
                                            <p class="form-control-static hiderstr"><span id="usrmqttp"><?=$HIAS->helpers->oDecrypt($Staffer["authenticationMqttKey"]["value"]); ?></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div class="pull-right"><a href="javascipt:void(0)" id="reset_user_amqp"><i class="fa fa-refresh"></i> Reset AMQP Password</a></div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5">AMQP Username</label>
                                        <div class="col-md-9">
                                            <p class="form-control-static hiderstr" id="appamqpu"><?=$Staffer["authenticationAmqpUser"]["value"] ? $HIAS->helpers->oDecrypt($Staffer["authenticationAmqpUser"]["value"]) : ""; ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5">AMQP Password</label>
                                        <div class="col-md-9">
                                            <p class="form-control-static hiderstr" hiderstr><span id="appamqpp"><?=$Staffer["authenticationAmqpKey"]["value"] ? $HIAS->helpers->oDecrypt($Staffer["authenticationAmqpKey"]["value"]) : ""; ?></span>
                                            <p><strong>Last Updated:</strong> <?=$Staffer["authenticationAmqpKey"]["metadata"]["timestamp"]["value"]; ?></p>
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

            <?php include dirname(__FILE__) . '/../../Includes/Footer.php'; ?>

        </div>

        <?php  include dirname(__FILE__) . '/../../Includes/JS.php'; ?>

        <script type="text/javascript" src="/iotJumpWay/Classes/mqttws31.js"></script>
        <script type="text/javascript" src="/iotJumpWay/Classes/iotJumpWay.js"></script>
        <script type="text/javascript" src="/iotJumpWay/Classes/iotJumpWayUI.js"></script>

        <script type="text/javascript" src="/Users/Staff/Classes/Staff.js"></script>
        <script type="text/javascript">

            $(document).ready(function() {

                iotJumpwayUI.HideSecret();
                iotJumpwayUI.StartStaffLife();
            });

            function initMap() {

                var latlng = new google.maps.LatLng("<?=floatval($Staffer["location"]["value"]["coordinates"][0]); ?>", "<?=floatval($Staffer["location"]["value"]["coordinates"][1]); ?>");
                var map = new google.maps.Map(document.getElementById('map1'), {
                    zoom: 10,
                    center: latlng
                });

                var marker = new google.maps.Marker({
                    position: latlng,
                    map: map,
                    title: 'Approximate location'
                });
            }

        </script>
        <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?=$HIAS->helpers->oDecrypt($HIAS->confs["gmaps"]); ?>&callback=initMap"></script>

    </body>
</html>