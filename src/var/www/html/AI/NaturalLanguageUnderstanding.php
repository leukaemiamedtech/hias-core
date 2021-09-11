<?php session_start();

$pageDetails = [
    "PageID" => "AI",
    "SubPageID" => "AIAgents",
	"LowPageID" => "AIAgents"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../AI/Classes/AiAgents.php';


$aid = filter_input(INPUT_GET, 'agent', FILTER_SANITIZE_STRING);
$agent = $AiAgents->get_agent($aid);

list($dev1On, $dev1Off) = $iotJumpWay->get_device_status($agent["networkStatus"]["value"]);

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
	 	 	 	 	 	 <div class="panel panel-default card-view panel-refresh" style="padding: 0px !important;">

							<div class="scroll_450px" id="chatWindow"></div>
							<form data-toggle="validator" role="form" id="genisysai_chat">
								<textarea name="GeniSysAiChat" id="GeniSysAiChat" class="form-control"></textarea>
								<input type="hidden" class="form-control" id="chatToGeniSys" name="chatToGeniSys" required value="1">
								<button type="submit" class="btn btn-success btn-anim" id="send_chat" style="width: 100%;"><i class="icon-rocket"></i><span class="btn-text">Communicate</span></button>
							</form>

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
											<i class="fas fa-battery-full data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idebatU"><?=$agent["batteryLevel"]["value"]; ?></span>% &nbsp;&nbsp;
											<i class="fa fa-microchip data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idecpuU"><?=$agent["cpuUsage"]["value"]; ?></span>% &nbsp;&nbsp;
											<i class="zmdi zmdi-memory data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idememU"><?=$agent["memoryUsage"]["value"]; ?></span>% &nbsp;&nbsp;
											<i class="far fa-hdd data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idehddU"><?=$agent["hddUsage"]["value"]; ?></span>% &nbsp;&nbsp;
											<i class="fa fa-thermometer-quarter data-right-rep-icon txt-light" aria-hidden="true"></i>&nbsp;<span id="idetempU"><?=$agent["temperature"]["value"]; ?></span>°C
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
		<script type="text/javascript" src="/AI/Classes/AiAgents.js"></script>

		<script type="text/javascript">

	 	 	 $(document).ready(function() {
				AiAgents.StartAgentLife();
	 	 	 });

		</script>

	</body>
</html>