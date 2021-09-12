<?php session_start();

$pageDetails = [
	"PageID" => "HIASBCH",
	"SubPageID" => "HIASBCH",
	"LowPageID" => "Contracts"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';

$cb = $HIAS->hiasbch->get_hiasbch();
$contracts = $cb["contracts"]["value"];

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
										<h6 class="panel-title txt-dark">HIASBCH Console</h6>
									</div>
									<div class="pull-right"></div>
									<div class="clearfix"></div>
								</div>
								<div class="panel-wrapper collapse in">
									<div class="panel-body">

										<div class="row">
											<div class="col-lg-12">

												<p>HIASBCH Console allows you to send real-time requests to the HIASBCH private blockchain.</p>
												<p>Read the official <a href="https://github.com/AIIAL/HIASBCH/docs/" target="_BLANK">HIASCDI Documentation</a> for more information.</p>
												<p>&nbsp;</p>

											</div>
										</div>

										<div class="form-wrap">

											<form data-toggle="validator" role="form" id="send">
												<div class="row">
													<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
														<div class="form-group">
															<label for="name" class="control-label mb-10">HIASBCH Contract Address</label>

															<select id="contract" class="form-control" name="contract" required>
																<option value="">PLEASE SELECT</option>

																<?php
																	$i=0;
																	$contracts = $cb["contracts"]["value"];
																	if(count($contracts)):
																		foreach($contracts as $key => $value):
																?>

																<option value="<?=$value["contract"];?>"><?=$value["name"];?></option>

																<?php
																		$i++;
																		endforeach;
																	endif;
																?>

															</select>

															<span class="help-block"> HIASBCH contract address</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Interaction Function</label>
															<input type="text" class="form-control" id="func" name="func" placeholder="Interaction Endpoint" required value="">
															<span class="help-block">Contact function to send the below data to</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Call Type</label>
															Send <input type="radio" id="type" name="type" value="Send" checked> Call <input type="radio" id="type" name="type" value="Call">
															<span class="help-block">Whether the call is to "Send" or "Call" to a Contract Function</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Data To Send</label>
															<textarea class="form-control" id="data" name="data" placeholder="Contract data as JSON Array"  rows="12"></textarea>
															<span class="help-block">Contract data as JSON Array, only required if the function you have provided above has parameters.</span>
														</div>
														<div class="form-group mb-0">
															<input type="hidden" class="form-control" id="acc" name="acc" required value="<?=$HIAS->hiasbch->un; ?>" autocomplete="false">
															<input type="hidden" class="form-control" id="p" name="p" required value="<?=$HIAS->hiasbch->up; ?>" autocomplete="false">
															<input type="hidden" id="contract" name="contract" required value="<?=$contract["contract"]; ?>">
															<button type="submit" class="btn btn-success btn-anim" id="console_interact"><i class="icon-rocket"></i><span class="btn-text">Interact</span></button>
														</div>
													</div>
													<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
														<div class="form-group">
															<label for="name" class="control-label mb-10">Application Binary Interface</label>
															<textarea class="form-control" id="abi" name="abi" placeholder="Application Binary Interface" required rows="12"></textarea>
															<span class="help-block">Application Binary Interface</span>
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
