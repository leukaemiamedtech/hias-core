<?php session_start();

$pageDetails = [
	"PageID" => "IoT",
	"SubPageID" => "Context",
	"LowPageID" => "Settings"
];

include dirname(__FILE__) . '/../../../Classes/Core/init.php';
include dirname(__FILE__) . '/../../../Classes/Core/GeniSys.php';
include dirname(__FILE__) . '/../../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../../iotJumpWay/ContextBroker/Classes/ContextBroker.php';

$_GeniSysAi->checkSession();
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
		<link href="<?=$domain; ?>/AI/GeniSysAI/Media/CSS/GeniSys.css" rel="stylesheet" type="text/css">
		<link href="<?=$domain; ?>/vendors/bower_components/fullcalendar/dist/fullcalendar.css" rel="stylesheet" type="text/css"/>
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
									<h6 class="panel-title txt-dark">HIAS Context Broker</h6>
								</div>
								<div class="pull-right"></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">

									<p>The HIAS Context Broker implements the HDSI (HIAS Data Services Interface) V1 API and allows easy management of HIAS device and application context data. HDSI is based on <a href="http://www.openmobilealliance.org/" target="_BLANK">Open Mobile Alliance</a>'s <a href="http://www.openmobilealliance.org/release/NGSI/V1_0-20120529-A/OMA-TS-NGSI_Context_Management-V1_0-20120529-A.pdf" target="_BLANK">NGSI</a>, and has been customized to meet the requirements of the HIAS network.</p>

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
				</div>

				<div class="row">
					<div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">HIAS Context Broker Configuration</h6>
								</div>
								<div class="pull-right"></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="form-wrap">
										<form data-toggle="validator" role="form" id="update_context_broker">
											<div class="row">
												<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
													<div class="form-group">
														<label for="name" class="control-label mb-10">Context Broker Endpoint</label>
														<input type="text" class="form-control" id="url" name="url" placeholder="Context Broker URL" required value="<?=$iotJumpWay->cb["url"]; ?>">
														<span class="help-block">URL of the context broker.</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Context Broker Local IP</label>
														<input type="text" class="form-control" id="local_ip" name="local_ip" placeholder="Context Broker Local IP" required value="<?=$iotJumpWay->cb["local_ip"]; ?>">
														<span class="help-block">The local IP for the context broker.</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">HDSI Version</label>
														<input type="text" class="form-control" id="hdsiv" name="hdsiv" placeholder="Current HDSI Version" required value="<?=$iotJumpWay->cb["hdsiv"]; ?>">
														<span class="help-block">Current HDSI Version.</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">About Endpoint</label>
														<input type="text" class="form-control" id="about_url" name="about_url" placeholder="Context Broker Agents enpoint" required value="<?=$iotJumpWay->cb["about_url"]; ?>">
														<span class="help-block">Listens for Context Broker information requests.</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">IoT Agents Endpoint</label>
														<input type="text" class="form-control" id="agents_url" name="agents_url" placeholder="Context Broker Agents enpoint" required value="<?=$iotJumpWay->cb["agents_url"]; ?>">
														<span class="help-block">Listens for IoT Agent retrieval, creation and update requests.</span>
													</div>
													<div class="form-group mb-0">
														<input type="hidden" class="form-control" id="update_cbroker" name="update_cbroker" required value="1">
														<button type="submit" class="btn btn-success btn-anim"><i class="icon-rocket"></i><span class="btn-text">Update Context Broker</span></button>
													</div>
												</div>
												<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
													<div class="form-group">
														<label for="name" class="control-label mb-10">Entities Endpoint</label>
														<input type="text" class="form-control" id="entities_url" name="entities_url" placeholder="Context Broker Entities enpoint" required value="<?=$iotJumpWay->cb["entities_url"]; ?>">
														<span class="help-block">Listens for entity retrieval, creation and update requests.</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Entity Types Endpoint</label>
														<input type="text" class="form-control" id="types_url" name="types_url" placeholder="Context Broker Types enpoint" required value="<?=$iotJumpWay->cb["types_url"]; ?>">
														<span class="help-block">Listens for entity types retrieval, creation and update requests.</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Commands Endpoint</label>
														<input type="text" class="form-control" id="commands_url" name="commands_url" placeholder="Context Broker Commands enpoint" required value="<?=$iotJumpWay->cb["commands_url"]; ?>">
														<span class="help-block">Listens for commands retrieval, creation and update requests.</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Subscriptions Endpoint</label>
														<input type="text" class="form-control" id="subscriptions_url" name="subscriptions_url" placeholder="Context Broker Subscriptions enpoint" required value="<?=$iotJumpWay->cb["subscriptions_url"]; ?>">
														<span class="help-block">Listens for Subscription retrieval, creation and update requests.</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Registrations Endpoint</label>
														<input type="text" class="form-control" id="registrations_url" name="registrations_url" placeholder="Context Broker Registrations enpoint" required value="<?=$iotJumpWay->cb["registrations_url"]; ?>">
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
									<h6 class="panel-title txt-dark">Broker Details (v1/about)</h6>
								</div>
								<div class="pull-right"></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div style="height: 400px; overflow-y: scroll; overflow-x: hidden;">
										<?php echo "<pre id='schema'>"; ?> <?php print_r(json_encode($ContextBroker->getBroker()["Data"], JSON_PRETTY_PRINT)); ?> <?php echo "</pre>"; ?>
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

		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/mqttws31.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/iotJumpWay.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/ContextBroker/Classes/ContextBroker.js"></script>

	</body>
</html>
