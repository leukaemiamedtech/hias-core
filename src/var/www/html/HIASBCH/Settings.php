<?php session_start();

$pageDetails = [
	"PageID" => "HIASBCH",
	"SubPageID" => "HIASBCH",
	"LowPageID" => "Settings"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';

$hiasbch = $HIAS->hiasbch->get_hiasbch();
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
										<h6 class="panel-title txt-dark">HIASBCH</h6>
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

															<p>the HIAS Blockchain (HIASBCH) is a private blockchain created using Ethereum. The blockchain provides permissions management for HIAS staff, devices, applications, and agents; and also handles data integrity for data that is published through the HIAS network.</p>
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
							<div class="panel panel-default card-view panel-refresh">
								<div class="panel-heading">
									<div class="pull-left">
										<h6 class="panel-title txt-dark">HIAS HIASBCH Settings</h6>
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
																		<label for="name" class="control-label mb-10">HIASBCH Entity</label>
																		<input type="text" class="form-control hider" id="entity" name="entity" placeholder="HIASBCH entity ID" required value="<?=$HIAS->helpers->oDecrypt($HIAS->hiasbch->confs["entity"]); ?>" autocomplete="false">
																		<span class="help-block">The HIASBCH entity stored in the HIASCDI Context Broker</span>
																	</div>
																	<div class="form-group">
																		<label for="name" class="control-label mb-10">HIASBCH Core Account</label>
																		<input type="text" class="form-control hider" id="un" name="" value="<?=$HIAS->hiasbch->un; ?>" readonly>
																		<span class="help-block">The HIASBCH core HIASBCH account (Miner)</span>
																	</div>
																	<div class="form-group">
																		<label for="name" class="control-label mb-10">HIASBCH Core Account Password</label>
																		<input type="text" class="form-control hider" id="up" name="" value="<?=$HIAS->hiasbch->up; ?>" readonly>
																		<span class="help-block">The HIASBCH core HIASBCH account password</span>
																	</div>
																	<div class="form-group mb-0">
																		<input type="hidden" class="form-control" id="update_bc" name="update_bc" required value="1">
																		<button type="submit" class="btn btn-success btn-anim " id="update_blockchain"><i class="icon-rocket"></i><span class="btn-text">Update</span></button>
																	</div>
																</div>
																<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
																	<div class="form-group">
																		<label class="control-label mb-10">HIAS Permissions Smart Contract</label>
																		<select class="form-control" id="dc" name="dc" required>
																			<option value="">PLEASE SELECT</option>

																			<?php
																				$i=0;
																				$contracts = $hiasbch["contracts"]["value"];
																				if(count($contracts)):
																					foreach($contracts as $key => $value):
																			?>

																			<option value="<?=$value["contract"]; ?>" <?=$hiasbch["authenticationContract"]["value"] == $value["contract"] ? " selected " : ""; ?>><?=$value["name"]; ?></option>

																			<?php
																					$i++;
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
																				$i=0;
																				$contracts = $hiasbch["contracts"]["value"];
																				if(count($contracts)):
																					foreach($contracts as $key => $value):
																			?>

																			<option value="<?=$value["contract"]; ?>" <?=$hiasbch["dataIntegrityContract"]["value"] == $value["contract"] ? " selected " : ""; ?>><?=$value["name"]; ?></option>

																			<?php
																					$i++;
																					endforeach;
																				endif;
																			?>

																		</select>
																		<span class="help-block">iotJumpWay Permissions Smart Contract</span>
																	</div>
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
