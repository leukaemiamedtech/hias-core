<?php session_start();

$pageDetails = [
	"PageID" => "HIASBCH",
	"SubPageID" => "HIASBCH",
	"LowPageID" => "Contracts"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';

$transaction = $HIAS->hiasbch->get_transaction(filter_input(INPUT_GET, 'transaction', FILTER_SANITIZE_STRING));
$receipt = $HIAS->hiasbch->get_transaction_receipt(filter_input(INPUT_GET, 'transaction', FILTER_SANITIZE_STRING));
$block = $HIAS->hiasbch->get_block($transaction["Data"]->blockHash);
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

								<h6 class="panel-title txt-dark">HIASBCH Transaction</h6>
								<hr style="margin-bottom: 10px; width: 100% !important;"/>

								<?php
									if($transaction["Data"]):
								?>

								<strong>Hash:</strong> <?=$transaction["Data"]->hash; ?><br />
								<strong>Timestamp:</strong> <?=date("Y-m-d H:i:s", hexdec($block["Data"]->timestamp)); ?> (<span style="font-size: 8;"><?=($HIAS->helpers->time_ago(time()-hexdec($block["Data"]->timestamp))); ?> ago</span>)<br />

								<?php
									endif;
								?>

								<div class="pull-right"></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">

									<div class="form-wrap">
										<div class="row">
											<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

												<?php
													if($transaction["Data"]):
												?>

												<table class="table">
													<tr>
														<td>
															<strong>Status</strong>
															<p>Status of the transaction</p>

														</td>
														<td><?=hexdec($receipt["Receipt"]->status) == 1  ? "OK" : "Failed"; ?></td>
													</tr>
													<tr>
														<td>
															<strong>Block</strong>
															<p>The block this transaction belongs to</p>

														</td>
														<td><a href="/HIASBCH/Explorer/Block/<?=$transaction["Data"]->blockHash; ?>" title="<?=$transaction["Data"]->blockHash; ?>"><?=hexdec($transaction["Data"]->blockNumber); ?></a></td>
													</tr>
													<tr>
														<td>
															<strong>From</strong>
															<p>The account that sent this transaction</p>

														</td>
														<td><a href="/HIASBCH/Explorer/Address/<?=$transaction["Data"]->from; ?>" title="<?=$transaction["Data"]->from; ?>"><?=$transaction["Data"]->from; ?></a></td>
													</tr>
													<tr>
														<td>
															<strong>To</strong>
															<p>The account that this transaction was sent to or contract that was created</p>

														</td>
														<td>

															<?php if($transaction["Data"]->to): ?>
																<a href="/HIASBCH/Explorer/Address/<?=$transaction["Data"]->to; ?>" title="<?=$transaction["Data"]->to; ?>"><?=$transaction["Data"]->to; ?></a><br />Account
															<?php else: ?>
																<a href="/HIASBCH/Explorer/Contract/<?=$receipt["Receipt"]->contractAddress; ?>" title="<?=$receipt["Receipt"]->contractAddress; ?>"><?=$receipt["Receipt"]->contractAddress; ?></a><br />Contract
															<?php endif; ?>

														</td>
													</tr>
													<tr>
														<td>
															<strong>Nonce</strong>
															<p>No of transactions or contract creations made by sender. Prevents replay attacks.</p>

														</td>
														<td><?=hexdec($transaction["Data"]->nonce); ?></td>
													</tr>
													<tr>
														<td>
															<strong>Index</strong>
															<p>Index of the transaction within the block</p>

														</td>
														<td><?=hexdec($transaction["Data"]->transactionIndex); ?></td>
													</tr>
													<tr>
														<td>
															<strong>Value</strong>
															<p>Amount to be sent to recipient or contract</p>

														</td>
														<td><?=hexdec($transaction["Data"]->value); ?> wei</td>
													</tr>
													<tr>
														<td>
															<strong>Gas Used</strong>
															<p>Gas used in this transaction</p>

														</td>
														<td><?=hexdec($receipt["Receipt"]->gasUsed); ?></td>
													</tr>
													<tr>
														<td>
															<strong>Fee</strong>
															<p>The fee for this transaction</p>

														</td>
														<td><?=hexdec($transaction["Data"]->gas) * hexdec($transaction["Data"]->gasPrice); ?> wei</td>
													</tr>
													<tr>
														<td>
															<strong>Cumulative Gas Used</strong>
															<p>All gas used in the current block</p>

														</td>
														<td><?=hexdec($receipt["Receipt"]->cumulativeGasUsed); ?></td>
													</tr>
												</table>

												<?php
													else:
												?>

													<strong>RECEIPT NOT READY, PLEASE TRY AGAIN IN A FEW MOMENTS</strong>

												<?php
													endif;
												?>

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