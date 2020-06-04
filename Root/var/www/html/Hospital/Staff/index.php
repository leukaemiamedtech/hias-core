<?php session_start();

$pageDetails = [
    "PageID" => "HIS",
    "SubPageID" => "Staff",
    "LowPageID" => "List"
];

include dirname(__FILE__) . '/../../../Classes/Core/init.php';
include dirname(__FILE__) . '/../../../Classes/Core/GeniSys.php';
include dirname(__FILE__) . '/../../Hospital/Staff/Classes/Staff.php';

$_GeniSysAi->checkSession();
$Staffs = $Staff->getStaffs();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="robots" content="noindex, nofollow" />

    <title><?=$_GeniSys->_confs["meta_title"]; ?></title>
    <meta name="description" content="<?=$_GeniSys->_confs["meta_description"]; ?>" />
    <meta name="keywords" content="" />
    <meta name="author" content="hencework" />

    <script src="https://kit.fontawesome.com/58ed2b8151.js" crossorigin="anonymous"></script>

    <link type="image/x-icon" rel="icon" href="<?=$domain; ?>/img/favicon.png" />
    <link type="image/x-icon" rel="shortcut icon" href="<?=$domain; ?>/img/favicon.png" />
    <link type="image/x-icon" rel="apple-touch-icon" href="<?=$domain; ?>/img/favicon.png" />

    <link href="<?=$domain; ?>/vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet"
        type="text/css" />
    <link href="<?=$domain; ?>/vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet"
        type="text/css" />
    <link href="<?=$domain; ?>/vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet"
        type="text/css">
    <link href="<?=$domain; ?>/dist/css/style.css" rel="stylesheet" type="text/css">
    <link href="<?=$domain; ?>/GeniSysAI/Media/CSS/GeniSys.css" rel="stylesheet" type="text/css">
    <link href="<?=$domain; ?>/vendors/bower_components/fullcalendar/dist/fullcalendar.css" rel="stylesheet"
        type="text/css" />
</head>

<body>

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
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="panel panel-default card-view panel-refresh">
                            <div class="panel-heading">
                                <div class="pull-left">
                                    <h6 class="panel-title txt-dark"><i class="fa fa-users"></i> Hospital Staff</h6>
                                </div>
                                <div class="pull-right"><a href="<?=$domain; ?>/Hospital/Staff/Create"><i
                                            class="fa fa-plus"></i></a></div>
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
                                                        <th>PHOTO</th>
                                                        <th>DETAILS</th>
                                                        <th>STATUS</th>
                                                        <th>ACTION</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                <?php 
													if(count($Staffs)):
														foreach($Staffs as $key => $value):
												?>

                                                    <tr>
                                                        <td>#<?=$value["id"];?></td>
                                                        <td><img src="<?=$domain; ?>/Team/Media/Images/Uploads/<?=$value["pic"];?>" style="max-width: 100px; !important;" /></td>
                                                        <td>
                                                            <strong>Name:</strong> <?=$value["username"];?><br />
                                                            <strong>Admin:</strong> <?=$value["admin"] ? "Yes" : "No";?>
                                                        </td>
                                                        <td>
                                                            <div class="label label-table <?=$value["status"] == "OFFLINE" ? "label-danger" : "label-success"; ?>">
                                                                <?=$value["status"] == "OFFLINE" ? "OFFLINE" : "ONLINE"; ?>
                                                            </div>
                                                        </td>
                                                        <td><a href="<?=$domain; ?>/Hospital/Staff/<?=$value["id"];?>/"><i class="fa fa-edit"></i> Edit</a></a></td>
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

            <?php include dirname(__FILE__) . '/../../Includes/Footer.php'; ?>

        </div>

        <?php  include dirname(__FILE__) . '/../../Includes/JS.php'; ?>

        <script type="text/javascript" src="<?=$domain; ?>/vendors/bower_components/moment/min/moment.min.js"></script>
        <script type="text/javascript"
            src="<?=$domain; ?>/vendors/bower_components/simpleWeather/jquery.simpleWeather.min.js"></script>
        <script type="text/javascript" src="<?=$domain; ?>/dist/js/simpleweather-data.js"></script>

        <script type="text/javascript"
            src="<?=$domain; ?>/vendors/bower_components/waypoints/lib/jquery.waypoints.min.js"></script>
        <script type="text/javascript"
            src="<?=$domain; ?>/vendors/bower_components/jquery.counterup/jquery.counterup.min.js"></script>

        <script type="text/javascript" src="<?=$domain; ?>/dist/js/dropdown-bootstrap-extended.js"></script>

        <script type="text/javascript" src="<?=$domain; ?>/vendors/jquery.sparkline/dist/jquery.sparkline.min.js">
        </script>

        <script type="text/javascript"
            src="<?=$domain; ?>/vendors/bower_components/owl.carousel/dist/owl.carousel.min.js"></script>

        <script type="text/javascript"
            src="<?=$domain; ?>/vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.js"></script>

        <script type="text/javascript" src="<?=$domain; ?>/vendors/bower_components/echarts/dist/echarts-en.min.js">
        </script>
        <script type="text/javascript" src="<?=$domain; ?>/vendors/echarts-liquidfill.min.js"></script>

        <script type="text/javascript" src="<?=$domain; ?>/vendors/bower_components/switchery/dist/switchery.min.js">
        </script>
        <script type="text/javascript"
            src="<?=$domain; ?>/vendors/bower_components/fullcalendar/dist/fullcalendar.min.js"></script>
        <script type="text/javascript" src="<?=$domain; ?>/dist/js/fullcalendar-data.js"></script>

        <script type="text/javascript" src="<?=$domain; ?>/dist/js/init.js"></script>
        <script type="text/javascript" src="<?=$domain; ?>/dist/js/dashboard-data.js"></script>

        <script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/mqttws31.js"></script>
        <script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/iotJumpWay.js"></script>

</body>

</html>