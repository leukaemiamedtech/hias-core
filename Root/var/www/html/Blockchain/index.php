<?php session_start();

$pageDetails = [
	"PageID" => "Blockchain",
	"SubPageID" => "Settings"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../../Classes/Core/GeniSys.php';
include dirname(__FILE__) . '/../Blockchain/Classes/Blockchain.php';

$_GeniSysAi->checkSession();

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

		<link href="<?=$domain; ?>/vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet" type="text/css">
		<link href="<?=$domain; ?>/dist/css/style.css" rel="stylesheet" type="text/css">
		<link href="<?=$domain; ?>/AI/GeniSysAI/Media/CSS/GeniSys.css" rel="stylesheet" type="text/css">
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
										<h6 class="panel-title txt-dark">HIAS Blockchain</h6>
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

															<p>The HIAS Blockchain is a private blockchain created using Ethereum. The blockchain provides permissions management for HIAS staff, devices and applications and patients, and also handles data integrity for data that is published through the HIAS network from connected devices and applications. There are three core smart contracts on the HIAS Blockchain which provide the core functionality, but you are able to create and deploy your own smart contracts and interact with them through the HIAS UI Blockchain management area.</p>

														</div>
													</div>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div><br />
							<div class="panel panel-default card-view panel-refresh">
								<div class="panel-heading">
									<div class="pull-left">
										<h6 class="panel-title txt-dark">HIAS Blockchain Settings</h6>
									</div>
									<div class="pull-right"></div>
									<div class="clearfix"></div>
								</div>
								<div class="panel-wrapper collapse in">
									<div class="panel-body">
										<div class="form-wrap">
											<div class="row">
												<div class="col-lg-12col-md-12 col-sm-12 col-xs-12">
													<div class="form-group">
														<form data-toggle="validator" role="form" id="blockchain_update">
															<div class="row">
																<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
																	<div class="form-group">
																		<label class="control-label mb-10">HIAS Permissions Smart Contract</label>
																		<select class="form-control" id="dc" name="dc" required>
																			<option value="">PLEASE SELECT</option>

																			<?php
																				$contracts = $Blockchain->getContracts();
																				if(count($contracts)):
																					foreach($contracts as $key => $value):
																			?>

																			<option value="<?=$value["id"]; ?>" <?=$Blockchain->bcc["dc"] == $value["id"] ? " selected " : ""; ?>>#<?=$value["id"]; ?>: <?=$Blockchain->_GeniSys->_helpers->oDecrypt($value["name"]); ?></option>

																			<?php
																					endforeach;
																				endif;
																			?>

																		</select>
																		<span class="help-block">HIAS Permissions Smart Contract</span>
																	</div>
																	<div class="form-group">
																		<label class="control-label mb-10">iotJumpWay Permissions Smart Contract</label>
																		<select class="form-control" id="ic" name="ic" required>
																			<option value="">PLEASE SELECT</option>

																			<?php
																				$contracts = $Blockchain->getContracts();
																				if(count($contracts)):
																					foreach($contracts as $key => $value):
																			?>

																			<option value="<?=$value["id"]; ?>" <?=$Blockchain->bcc["ic"] == $value["id"] ? " selected " : ""; ?>>#<?=$value["id"]; ?>: <?=$Blockchain->_GeniSys->_helpers->oDecrypt($value["name"]); ?></option>

																			<?php
																					endforeach;
																				endif;
																			?>

																		</select>
																		<span class="help-block">iotJumpWay Permissions Smart Contract</span>
																	</div>
																	<div class="form-group">
																		<label class="control-label mb-10">Patients Permissions Smart Contract</label>
																		<select class="form-control" id="pc" name="pc" required>
																			<option value="">PLEASE SELECT</option>

																			<?php
																				$contracts = $Blockchain->getContracts();
																				if(count($contracts)):
																					foreach($contracts as $key => $value):
																			?>

																			<option value="<?=$value["id"]; ?>" <?=$Blockchain->bcc["pc"] == $value["id"] ? " selected " : ""; ?>>#<?=$value["id"]; ?>: <?=$Blockchain->_GeniSys->_helpers->oDecrypt($value["name"]); ?></option>

																			<?php
																					endforeach;
																				endif;
																			?>

																		</select>
																		<span class="help-block">Patients Permissions Smart Contract</span>
																	</div>
																	<div class="form-group mb-0">
																		<input type="hidden" class="form-control" id="update_bc" name="update_bc" required value="1">
																		<button type="submit" class="btn btn-success btn-anim " id="update_blockchain"><i class="icon-rocket"></i><span class="btn-text">Update</span></button>
																	</div>
																</div>
																<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
																</div>
															</div>
														</form>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
							<div class="panel panel-default card-view">
								<div class="panel-wrapper collapse in">
									<div class="panel-body">
										<div id="dataLog" style="height: 385px; overflow: scroll; padding: 5px; color: #fff; font-size: 10px; overflow-x: hidden;"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

			<?php include dirname(__FILE__) . '/../Includes/Footer.php'; ?>

		</div>

		<?php  include dirname(__FILE__) . '/../Includes/JS.php'; ?>

		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/mqttws31.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/iotJumpWay.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/Blockchain/Classes/Blockchain.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/Blockchain/Classes/web3.js"></script>
		<script type="text/javascript">
			window.addEventListener('load', function () {
				Blockchain.connect("<?=$domain; ?>/Blockchain/API/");
				if(Blockchain.isConnected()){
					msg = "Connected to HIAS Blockchain!";
					Logging.logMessage("Core", "Blockchain", msg);
					Blockchain.logData(msg);
				} else {
					msg = "Connection to HIAS Blockchain failed!";
					Logging.logMessage("Core", "Blockchain", msg);
					Blockchain.logData(msg);
				}
			});
		</script>
	</body>
</html>
