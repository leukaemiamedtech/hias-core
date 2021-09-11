<?php session_start();

$pageDetails = [
	"PageID" => "HIASBCH",
	"SubPageID" => "HIASBCH",
	"LowPageID" => "Contracts"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';

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
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<h6 class="panel-title txt-dark">HIASBCH Address</h6>

								<hr style="margin-bottom: 10px; width: 100% !important;"/>
								<strong>Hash:</strong> <?=filter_input(INPUT_GET, 'address', FILTER_SANITIZE_STRING); ?><br />

								<div class="pull-right"></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">

									<div class="form-wrap">
										<div class="row">
											<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<h6 class="panel-title txt-dark">Address Transactions</h6>
								<hr style="margin-bottom: 10px; width: 100% !important;"/>

								<div class="pull-right"></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">

									<div class="form-wrap">
										<div class="row">
											<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

												<table class="table">

												<?php
													$transactions = $HIAS->hiasbch->get_account_transactions(filter_input(INPUT_GET, 'address', FILTER_SANITIZE_STRING), 100);
													if($transactions["Response"]=="OK"):
														foreach($transactions["ResponseData"] as $key => $value):
															$block = $HIAS->hiasbch->get_block($value["BlockHash"]);
												?>

												<tr>
													<td>

														<div class="row">
															<div class="col-lg-1 col-md-12 col-sm-12 col-xs-12">Hash: </div>
															<div class="col-lg-11 col-md-12 col-sm-12 col-xs-12">

																&nbsp;&nbsp;<a href="/HIASBCH/Explorer/Transaction/<?=$value["Hash"]; ?>" title="<?=$value["Hash"]; ?>"><?=$value["Hash"]; ?></a>

															</div>
															<div class="col-lg-1 col-md-12 col-sm-12 col-xs-12">Fee:&nbsp;&nbsp;</div>
															<div class="col-lg-11 col-md-12 col-sm-12 col-xs-12">

																&nbsp;&nbsp;<?=$value["Gas"] * $value["GasPrice"]; ?>

															</div>
															<div class="col-lg-1  col-md-12 col-sm-12 col-xs-12">From:&nbsp;&nbsp;</div>
															<div class="col-lg-11 col-md-12 col-sm-12 col-xs-12">

																&nbsp;&nbsp;<a href="/HIASBCH/Explorer/Address/<?=$value["From"]; ?>" title="<?=$value["From"]; ?>"><?=$value["From"]; ?></a>

															</div>
															<div class="col-lg-1  col-md-12 col-sm-12 col-xs-12">To:&nbsp;&nbsp;</div>
															<div class="col-lg-11 col-md-12 col-sm-12 col-xs-12">

															<?php if($value["To"]): ?>
																&nbsp;&nbsp;<a href="/HIASBCH/Explorer/Address/<?=$value["To"]; ?>" title="<?=$value["To"]; ?>"><?=$value["To"]; ?></a>
															<?php else: ?>
																&nbsp;&nbsp;Contract Creation
															<?php endif; ?>

															</div>
															<div class="col-lg-1 col-md-12 col-sm-12 col-xs-12">At:&nbsp;&nbsp;</div>
															<div class="col-lg-11 col-md-12 col-sm-12 col-xs-12">

																&nbsp;&nbsp;<?=date("Y-m-d H:i:s", hexdec($block["Data"]->timestamp)); ?> (<span style="font-size: 8;"><?=($HIAS->helpers->time_ago(time()-hexdec($block["Data"]->timestamp))); ?> ago</span>)

															</div>
														</div>

													</td>
												</tr>

												<?php
														endforeach;
													endif;
												?>

												</table>

											</div>
										</div>
									</div>
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

		<script type="text/javascript" src="/iotJumpWay/Classes/mqttws31.js"></script>
		<script type="text/javascript" src="/iotJumpWay/Classes/iotJumpWay.js"></script>

	</body>
</html>