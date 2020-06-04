<?php session_start();

$pageDetails = [
    "PageID" => "IoT",
    "SubPageID" => "IoT",
    "LowPageID" => "Locations"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../../Classes/Core/GeniSys.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';

$_GeniSysAi->checkSession();

$Applications = $iotJumpWay->getApplications(0, "id ASC");
list($on, $off) = $_GeniSysAi->getStatusShow($_GeniSys->_confs["status"]);

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
		<meta name="author" content="hencework"/>

		<script src="https://kit.fontawesome.com/58ed2b8151.js" crossorigin="anonymous"></script>

		<link type="image/x-icon" rel="icon" href="<?=$domain; ?>/img/favicon.png" />
		<link type="image/x-icon" rel="shortcut icon" href="<?=$domain; ?>/img/favicon.png" />
		<link type="image/x-icon" rel="apple-touch-icon" href="<?=$domain; ?>/img/favicon.png" />

        <link href="<?=$domain; ?>/vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
        <link href="<?=$domain; ?>/vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>		
		<link href="<?=$domain; ?>/vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet" type="text/css">
		<link href="<?=$domain; ?>/dist/css/style.css" rel="stylesheet" type="text/css">
		<link href="<?=$domain; ?>/GeniSysAI/Media/CSS/GeniSys.css" rel="stylesheet" type="text/css">
		<link href="<?=$domain; ?>/vendors/bower_components/fullcalendar/dist/fullcalendar.css" rel="stylesheet" type="text/css"/>
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
									<h6 class="panel-title txt-dark">Edit HIAS Server Settings</h6>
								</div>
								<div class="pull-right"></div>
								<div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div class="form-wrap">
                                        <form data-toggle="validator" role="form" id="server_update">
                                            <div class="row">
                                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                                    <div class="form-group">
                                                        <label class="control-label mb-10">iotJumpWay Application</label>
                                                        <select class="form-control" id="aid" name="aid" required>
                                                            <option value="">PLEASE SELECT</option>

                                                            <?php 
                                                                if(count($Applications)):
                                                                    foreach($Applications as $key => $value):
                                                            ?>

                                                            <option value="<?=$value["id"]; ?>"
                                                                <?=$_GeniSys->_confs["aid"] == $value["id"] ? " selected " : ""; ?>>#<?=$value["id"]; ?>: <?=$value["name"]; ?></option>

                                                            <?php 
                                                                    endforeach;
                                                                endif;
                                                            ?>

                                                        </select>
                                                        <span class="help-block">HIAS iotJumpWay application</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">HIAS Version</label>
                                                        <input type="text" class="form-control" id="version" name="version" placeholder="HIAS Version" required value="<?=$_GeniSys->_confs["version"]; ?>">
                                                        <span class="help-block">HIAS Version Number</span> 
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Domain</label>
                                                        <input type="text" class="form-control hider" id="domainString" name="domainString" placeholder="HIAS Domain Name" required value="<?=$_GeniSys->_confs["domainString"] ? $_GeniSys->_helpers->oDecrypt($_GeniSys->_confs["domainString"]) : ""; ?>">
                                                        <span class="help-block"> Domain Name For HIAS Server</span> 
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">phpMyAdmin Directory</label>
                                                        <input type="text" class="form-control hider" id="phpmyadmin" name="phpmyadmin" placeholder="HIAS phpMyAdmin Directory" required value="<?=$_GeniSys->_confs["phpmyadmin"]; ?>">
                                                        <span class="help-block">HIAS phpMyAdmin Directory</span> 
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Default Latitude</label>
                                                        <input type="text" class="form-control hider" id="lt" name="lt" placeholder="Default Latitude" required value="<?=$_GeniSys->_confs["lt"] ? $_GeniSys->_helpers->oDecrypt($_GeniSys->_confs["lt"]) : ""; ?>">
                                                        <span class="help-block"> Default Latitude Used In HIAS</span> 
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Default Longitude</label>
                                                        <input type="text" class="form-control hider" id="lg" name="lg" placeholder="Default Longitude" required value="<?=$_GeniSys->_confs["lg"] ? $_GeniSys->_helpers->oDecrypt($_GeniSys->_confs["lg"]) : ""; ?>">
                                                        <span class="help-block"> Default Longitude Used In HIAS</span> 
                                                    </div>
                                                    <div class="form-group mb-0">
                                                        <input type="hidden" class="form-control" id="update_server" name="update_server" required value="1">
                                                        <button type="submit" class="btn btn-success btn-anim"><i class="icon-rocket"></i><span class="btn-text">submit</span></button>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Google Maps API Key</label>
                                                        <input type="text" class="form-control hider" id="gmaps" name="gmaps" placeholder="Google Maps API Key" required value="<?=$_GeniSys->_confs["gmaps"] ? $_GeniSys->_helpers->oDecrypt($_GeniSys->_confs["gmaps"]) : ""; ?>">
                                                        <span class="help-block"> Google Maps API Key</span> 
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Recaptcha API Public Key</label>
                                                        <input type="text" class="form-control hider" id="recaptcha" name="recaptcha" placeholder="Recaptcha API Public Key" required value="<?=$_GeniSys->_confs["recaptcha"] ? $_GeniSys->_helpers->oDecrypt($_GeniSys->_confs["recaptcha"]) : ""; ?>">
                                                        <span class="help-block"> Recaptcha Public API key</span> 
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="control-label mb-10">Recaptcha API Private Key</label>
                                                        <input type="text" class="form-control hider" id="recaptchas" name="recaptchas" placeholder="Recaptcha API Public Key" required value="<?=$_GeniSys->_confs["recaptchas"] ? $_GeniSys->_helpers->oDecrypt($_GeniSys->_confs["recaptchas"]) : ""; ?>">
                                                        <span class="help-block"> Recaptcha Private API key</span> 
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
									<h6 class="panel-title txt-dark">Server Stats</h2>
								</div>
								<div class="pull-right"><span id="offline1" style="color: #33F9FF !important;" class="<?=$on; ?>"><i class="fas fa-power-off" style="color: #33F9FF !important;"></i> Online</span> <span id="online1" class="<?=$off; ?>" style="color: #99A3A4 !important;"><i class="fas fa-power-off" style="color: #99A3A4 !important;"></i> Offline</span></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<i class="fa fa-microchip data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="svrecpu"><?=$_GeniSys->_confs["cpu"]; ?></span>% &nbsp;&nbsp;
									<i class="zmdi zmdi-memory data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="svremem"><?=$_GeniSys->_confs["mem"]; ?></span>% &nbsp;&nbsp;
									<i class="far fa-hdd data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="svrehdd"><?=$_GeniSys->_confs["hdd"]; ?></span>% &nbsp;&nbsp;
									<i class="fa fa-thermometer-quarter data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="svretempr"><?=$_GeniSys->_confs["tempr"]; ?></span>°C 
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
					</div>
				</div>
				
			</div>
			
			<?php include dirname(__FILE__) . '/../Includes/Footer.php'; ?>
			
		</div>

		<?php  include dirname(__FILE__) . '/../Includes/JS.php'; ?>
		
        <script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/mqttws31.js"></script>
        <script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/iotJumpWay.js"></script>
		<script type="text/javascript">
			GeniSys.HideInputs()
		</script>

    </body>

</html>
