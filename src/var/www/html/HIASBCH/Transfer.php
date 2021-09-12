<?php session_start();

$pageDetails = [
	"PageID" => "HIASBCH",
	"SubPageID" => "HIASBCH",
	"LowPageID" => "Transfer"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';

$contract = $HIAS->hiasbch->get_contract(filter_input(INPUT_GET, "contract", FILTER_SANITIZE_NUMBER_INT));

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
										<h6 class="panel-title txt-dark">Transfer HIAS ETHER</h6>
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

															<p>HIAS/HIASBCH ETHER is the private currency that allows the HIAS network to function. In the real world HIAS ETHER has no value. HIAS users, agents and applications require HIAS ETHER to be able to interact with the network.</p>
															<p>&nbsp;</p>

															<p>You can use this area to transfer HIAS ETHER to HIAS users, agents and applications.</p>
															<p>&nbsp;</p>

															<p>Read the official <a href="https://github.com/AIIAL/HIASBCH/docs/" target="_BLANK">HIASBCH Documentation</a> for more information.</p>

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
										<h6 class="panel-title txt-dark">HIAS ETHER Transfer Interface</h6>
									</div>
									<div class="pull-right"></div>
									<div class="clearfix"></div>
								</div>
								<div class="panel-wrapper collapse in">
									<div class="panel-body">
										<div class="form-wrap">

											<form data-toggle="validator" role="form" id="send">
												<div class="row">
													<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
														<div class="form-group">
															<label for="name" class="control-label mb-10">HIAS Ether Amount</label>
															<input type="text" class="form-control" id="amount" name="amount" placeholder="HIAS Ether Amount" required value="" autocomplete="false">
															<span class="help-block">HIAS Ether Amount</span>
														</div>
														<div class="form-group mb-0">
															<input type="hidden" class="form-control" id="acc" name="acc" required value="<?=$HIAS->hiasbch->un; ?>" autocomplete="false">
															<input type="hidden" class="form-control" id="p" name="p" required value="<?=$HIAS->hiasbch->up; ?>" autocomplete="false">
															<input type="hidden" class="form-control" id="transfer" name="transfer" required value="1">
															<button type="submit" class="btn btn-success btn-anim" id="transfer_ether"><i class="icon-rocket"></i><span class="btn-text">Transfer</span></button>
														</div>
													</div>
													<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
														<div class="form-group">
															<label for="name" class="control-label mb-10">Send to:</label>
															<select name="bcaddress" id="bcaddress" class="form-control">

																<option value="">PLEASE SELECT</option>
																<?php
																	$entities = $HIAS->hiasbch->get_entities();
																	if(!isSet($entities["Error"])):
																		foreach($entities as $key => $value):
																?>

																	<option value="<?=$value["authenticationBlockchainUser"]["value"]; ?>"><?=$value["name"]["value"]; ?></option>

																<?php
																		endforeach;
																	endif;
																?>

															</select>
															<span class="help-block">HIAS Blockchain account to send to</span>
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
							<div class="panel panel-default card-view">
								<div class="panel-wrapper collapse in">
									<div class="panel-body">
										<div id="dataLog" style="border: 0px solid; height: 385px; overflow: scroll; padding: 5px; color: #fff; font-size: 10px; overflow-x: hidden;"></div>
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
		<script type="text/javascript" src="/HIASBCH/Classes/web3.js"></script>
		<script type="text/javascript">
			window.addEventListener('load', function () {
				HIASBCH.connect("/hiasbch/api/");
				if(HIASBCH.isConnected()){
					msg = "Connected to HIASBCH!";
					Logging.logMessage("Core", "HIASBCH", msg);
					HIASBCH.logData(msg);
				} else {
					msg = "Connection to HIASBCH failed!";
					Logging.logMessage("Core", "HIASBCH", msg);
					HIASBCH.logData(msg);
				}
			});
		</script>
	</body>
</html>
