<?php session_start();

$pageDetails = [
	"PageID" => "HIASBCH",
	"SubPageID" => "HIASBCH",
	"LowPageID" => "Contracts"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../HIASCDI/Classes/Interface.php';

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
										<h6 class="panel-title txt-dark">HIASBCH Entity</h6>
									</div>
									<div class="pull-right"><a href="/HIASBCH/Configuration" download><i class="fas fa-download"></i> HIASBCH Configuration</a></div>
									<div class="clearfix"></div>
								</div>
								<div class="panel-wrapper collapse in">
									<div class="panel-body">

										<div class="row">
											<div class="col-lg-12">

												<p>HIASBCH Entity type is a contextual representation of the HIAS Private Ethereum Blockchain.</p>
												<p>&nbsp;</p>

												<p>Read the official <a href="https://github.com/AIIAL/HIASBCH/docs/" target="_BLANK">HIASBCH Documentation</a> for more information.</p>
												<p>&nbsp;</p>

											</div>
										</div>

										<div class="form-wrap">
											<form data-toggle="validator" role="form" id="update_hiasbch_form">
												<div class="row">
													<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
														<div class="form-group">
															<label for="name" class="control-label mb-10">Name</label>
															<input type="text" class="form-control" id="name" name="name" placeholder="Name of HIASBCH Blockchain" required value="<?=$hiasbch["name"]["value"]; ?>">
															<span class="help-block">Name of HIASBCH Blockchain</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Description</label>
															<input type="text" class="form-control" id="description" name="description" placeholder="HIASBCH Blockchain Description" required value="<?=$hiasbch["description"]["value"]; ?>">
															<span class="help-block">Description of HIASBCH Blockchain</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Version</label>
															<input type="text" class="form-control" id="version" name="version" placeholder="HIASBCH Blockchain Version" required value="<?=$hiasbch["version"]["value"]; ?>">
															<span class="help-block">HIASBCH Blockchain Version</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Host</label>
															<input type="text" class="form-control  hider" id="host" name="host" placeholder="HIASBCH Blockchain Host" required value="<?=$hiasbch["host"]["value"]; ?>">
															<span class="help-block">HIASBCH Blockchain Host</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Port</label>
															<input type="text" class="form-control hider" id="port" name="port" placeholder="HIASBCH Blockchain Port" required value="<?=$hiasbch["port"]["value"]; ?>">
															<span class="help-block">HIASBCH Blockchain Port</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Endpoint</label>
															<input type="text" class="form-control hider" id="endpoint" name="endpoint" placeholder="HIASBCH Blockchain Endpoint" required value="<?=$hiasbch["endpoint"]["value"]; ?>">
															<span class="help-block">HIASBCH Blockchain Endpoint</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Device Brand Name</label>
															<input type="text" class="form-control" id="deviceBrandName" name="deviceBrandName" placeholder="Device Brand Name" required value="<?=$hiasbch["deviceBrandName"]["value"]; ?>">
															<span class="help-block">Brand name of the device that the HIASBCH Blockchain is installed on.</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Device Model</label>
															<input type="text" class="form-control" id="deviceModel" name="deviceModel" placeholder="Device Model" required value="<?=$hiasbch["deviceModel"]["value"]; ?>">
															<span class="help-block">Model of the device that the HIASBCH Blockchain is installed on.</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Device Manufacturer</label>
															<input type="text" class="form-control" id="deviceManufacturer" name="deviceManufacturer" placeholder="Device Manufacturer" required value="<?=$hiasbch["deviceManufacturer"]["value"]; ?>">
															<span class="help-block">Manufacturer of the device that the HIASBCH Blockchain is installed on.</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Device Serial Number</label>
															<input type="text" class="form-control" id="deviceSerialNumber" name="deviceSerialNumber" placeholder="Device Manufacturer" required value="<?=$hiasbch["deviceSerialNumber"]["value"]; ?>">
															<span class="help-block">Serial number of the device that the HIASBCH Blockchain is installed on.</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Operating System</label>
															<input type="text" class="form-control" id="os" name="os" placeholder="Operating system name" required value="<?=$hiasbch["os"]["value"]; ?>">
															<span class="help-block">Operating system installed on the device that the HIASBCH Blockchain is installed on.</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Operating System Version</label>
															<input type="text" class="form-control" id="osVersion" name="osVersion" placeholder="Operating System Version" required value="<?=$hiasbch["osVersion"]["value"]; ?>">
															<span class="help-block">Installed operating system version.</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Operating System Manufacturer</label>
															<input type="text" class="form-control" id="osManufacturer" name="osManufacturer" placeholder="Operating System manufacturer" required value="<?=$hiasbch["osManufacturer"]["value"]; ?>">
															<span class="help-block">Installed operating system Manufacturer</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Software</label>
															<input type="text" class="form-control" id="software" name="software" placeholder="Software" required value="<?=$hiasbch["software"]["value"]; ?>">
															<span class="help-block">HIAS software</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Software Version</label>
															<input type="text" class="form-control" id="softwareVersion" name="softwareVersion" placeholder="Software Version" required value="<?=$hiasbch["softwareVersion"]["value"]; ?>">
															<span class="help-block">HIAS software version</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Software Manufacturer</label>
															<input type="text" class="form-control" id="softwareManufacturer" name="softwareManufacturer" placeholder="Software Manufacturer" required value="<?=$hiasbch["softwareManufacturer"]["value"]; ?>">
															<span class="help-block">HIAS software manufacturer</span>
														</div>
													</div>
													<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
														<div class="form-group">
															<label class="control-label mb-10">Location</label>
															<select class="form-control" id="lid" name="lid" required>
																<option value="">PLEASE SELECT</option>

																<?php
																	$Locations = $iotJumpWay->get_locations();
																	if(!isSet($Locations["Error"])):
																		foreach($Locations as $key => $value):
																?>

																	<option value="<?=$value["id"]; ?>" <?=$value["id"] == $hiasbch["networkLocation"]["value"] ? " selected " : ""; ?>><?=$value["name"]["value"]; ?></option>

																<?php
																		endforeach;
																	endif;
																?>

															</select>
															<span class="help-block">Location that HIASBCH Blockchain is installed in</span>
														</div>
														<div class="form-group">
															<label class="control-label mb-10">Zone</label>
															<select class="form-control" id="zid" name="zid" required>
																<option value="">PLEASE SELECT</option>
																<?php
																	$Zones = $iotJumpWay->get_zones();
																	if(!isSet($Zones["Error"])):
																		foreach($Zones as $key => $value):
																?>

																<option value="<?=$value["id"]; ?>" <?=$hiasbch["networkZone"]["value"] == $value["id"] ? " selected " : ""; ?>><?=$value["name"]["value"]; ?></option>

																<?php
																		endforeach;
																	endif;
																?>

															</select>
															<span class="help-block">Zone that HIASBCH Blockchain is installed in</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Coordinates</label>
															<input type="text" class="form-control" id="coordinates" name="coordinates" placeholder="iotJumpWay Location coordinates" required value="<?=$hiasbch["location"]["value"]["coordinates"][0]; ?>, <?=$hiasbch["location"]["value"]["coordinates"][1]; ?>">
															<span class="help-block">iotJumpWay Device coordinates</span>
														</div>
														<div class="form-group">
															<label class="control-label mb-10">Protocols</label>
															<select class="form-control" id="protocols" name="protocols[]" required multiple>

																<?php
																	$protocols = $HiasInterface->get_protocols();
																	if(count($protocols)):
																		foreach($protocols as $key => $value):
																?>

																	<option value="<?=$value["protocol"]; ?>" <?=in_array($value["protocol"], $hiasbch["protocols"]["value"]) ? " selected " : ""; ?>><?=$value["protocol"]; ?></option>

																<?php
																		endforeach;
																	endif;
																?>

															</select>
															<span class="help-block">Supported Communication Protocols</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">IP</label>
															<input type="text" class="form-control hider" id="ipAddress" name="ipAddress" placeholder="IP Address Of HIASBCH Blockchain" required value="<?=$hiasbch["ipAddress"]["value"]; ?>">
															<span class="help-block">IP address of HIASBCH Blockchain device</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">MAC</label>
															<input type="text" class="form-control hider" id="macAddress" name="macAddress" placeholder="MAC Address Of HIASBCH Blockchain" required value="<?=$hiasbch["macAddress"]["value"] ? $hiasbch["macAddress"]["value"] : ""; ?>">
															<span class="help-block">MAC address of HIASBCH Blockchain device</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Bluetooth Address</label>
															<input type="text" class="form-control hider" id="bluetooth" name="bluetooth" placeholder="Device Bluetooth Address"  value="<?=$hiasbch["bluetoothAddress"]["value"]; ?>">
															<span class="help-block">Bluetooth address of HIASBCH Blockchain device</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Date Created</label>
															<p><?=$hiasbch["dateCreated"]["value"]; ?></p>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Date First Used</label>
															<p><?=$hiasbch["dateFirstUsed"]["value"] ? $hiasbch["dateFirstUsed"]["value"] : "NA"; ?></p>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Date Modified</label>
															<p><?=$hiasbch["dateModified"]["value"]; ?></p>
														</div>
													</div>
												</div>
												<div class="form-group mb-0">
													<input type="hidden" class="form-control" id="update_hiasbch_entity" name="update_hiasbch_entity" required value=True>
													<button type="submit" class="btn btn-success btn-anim" id="update_hiasbch_entity_submit"><i class="icon-rocket"></i><span class="btn-text">Update</span></button>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div><br />
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
							<div class="panel panel-default card-view panel-refresh">
								<div class="panel-wrapper collapse in">
									<div class="panel-body">
									<div class="pull-right"></div>
										<div class="form-group">
											<label class="control-label col-md-5">Location</label>
											<div class="col-md-12">
												<div id="map1" class="map" style="height: 300px;"></div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="panel panel-default card-view panel-refresh">
								<div class="panel-heading">
									<div class="pull-left">
										<h6 class="panel-title txt-dark">HIASBCH Schema</h6>
									</div>
									<div class="pull-right"></div>
									<div class="clearfix"></div>
								</div>
								<div class="panel-wrapper collapse in">
									<div class="panel-body">
										<div  style="height: 400px; overflow-y: scroll; overflow-x: hidden;">
											<?php echo "<pre id='schema'>"; ?> <?php print_r(json_encode($hiasbch, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); ?> <?php echo "</pre>"; ?>
										</div>
									</div>
								</div>
							</div>
							<div class="panel panel-default card-view panel-refresh">
								<div class="panel-wrapper collapse in">
									<div class="panel-body">
										<div class="pull-right"><a href="javascipt:void(0)" id="reset_hiasbch_key"><i class="fa fa-refresh"></i> Reset Network Key</a></div>
										<div class="form-group">
											<label class="control-label col-md-5">Network Identifier</label>
											<div class="col-md-9">
												<p class="form-control-static hiderstr" id="network_identifier"><?=$hiasbch["id"]; ?></p>
												<p><strong>Last Updated:</strong> <?=$hiasbch["authenticationKey"]["metadata"]["timestamp"]["value"]; ?></p>
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
												<p class="form-control-static hiderstr" id="hiascdi_blockchain_address"><?=$hiasbch["authenticationBlockchainUser"]["value"]; ?></p>
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-md-5">Blockchain Key</label>
											<div class="col-md-9">
												<p class="form-control-static hiderstr" id="hiascdi_blockchain_key"><?=$HIAS->helpers->oDecrypt($hiasbch["authenticationBlockchainKey"]["value"]); ?></p>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="panel panel-default card-view panel-refresh">
								<div class="panel-wrapper collapse in">
									<div class="panel-body">
										<div class="pull-right"><a href="javascipt:void(0)" id="reset_hiasbch_mqtt"><i class="fa fa-refresh"></i> Reset MQTT Password</a></div>
										<div class="form-group">
											<label class="control-label col-md-5">MQTT Username</label>
											<div class="col-md-9">
												<p class="form-control-static hiderstr" id="hiascdi_mqqt_username"><?=$HIAS->helpers->oDecrypt($hiasbch["authenticationMqttUser"]["value"]); ?></p>
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-md-5">MQTT Password</label>
											<div class="col-md-9">
												<p class="form-control-static"><span id="hiascdi_mqqt_password" class="hiderstr"><?=$HIAS->helpers->oDecrypt($hiasbch["authenticationMqttKey"]["value"]); ?></span>
												<p><strong>Last Updated:</strong> <?=$hiasbch["authenticationMqttKey"]["metadata"]["timestamp"]["value"]; ?></p>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="panel panel-default card-view panel-refresh">
								<div class="panel-wrapper collapse in">
									<div class="panel-body">
										<div class="pull-right"><a href="javascipt:void(0)" id="reset_hiasbch_amqp"><i class="fa fa-refresh"></i> Reset AMQP Password</a></div>
										<div class="form-group">
											<label class="control-label col-md-5">AMQP Username</label>
											<div class="col-md-9">
												<p class="form-control-static hiderstr" id="damqpu"><?=$HIAS->helpers->oDecrypt($hiasbch["authenticationAmqpUser"]["value"]); ?></p>
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-md-5">AMQP Password</label>
											<div class="col-md-9">
												<p class="form-control-static hiderstr"><span id="damqpp"><?=$HIAS->helpers->oDecrypt($hiasbch["authenticationAmqpKey"]["value"]); ?></span>
												<p><strong>Last Updated:</strong> <?=$hiasbch["authenticationAmqpKey"]["metadata"]["timestamp"]["value"]; ?></p>
												</p>
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
		<script type="text/javascript" src="/HIASBCH/Classes/HIASBCH.js"></script>
		<script type="text/javascript">

			$(document).ready(function() {
				HIASBCH.hideSecret();
			});

			function initMap() {

				var latlng = new google.maps.LatLng("<?=floatval($hiasbch["location"]["value"]["coordinates"][0]); ?>", "<?=floatval($hiasbch["location"]["value"]["coordinates"][1]); ?>");
				var map = new google.maps.Map(document.getElementById('map1'), {
					zoom: 10,
					center: latlng
				});

				var loc = new google.maps.LatLng(<?=floatval($hiasbch["location"]["value"]["coordinates"][0]); ?>, <?=floatval($hiasbch["location"]["value"]["coordinates"][1]); ?>);
				var marker = new google.maps.Marker({
					position: loc,
					map: map,
					title: 'Device '
				});
			}

		</script>
		<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?=$HIAS->helpers->oDecrypt($HIAS->confs["gmaps"]); ?>&callback=initMap"></script>

	</body>
</html>