<?php session_start();

$pageDetails = [
	"PageID" => "HIASBCH",
	"SubPageID" => "HIASBCH",
	"LowPageID" => "Contracts"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';

$block = $HIAS->hiasbch->get_block(filter_input(INPUT_GET, 'block', FILTER_SANITIZE_STRING));

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
								<h6 class="panel-title txt-dark">HIASBCH Block</h6>

								<?php
									if($block["Response"]=="OK"):
								?>

								<hr style="margin-bottom: 10px; width: 100% !important;"/>
								<strong>Hash:</strong> <?=$block["Data"]->hash; ?><br />
								<strong>Miner:</strong> <a href="/HIASBCH/Explorer/Address/<?=$block["Data"]->miner; ?>" title="<?=$block["Data"]->miner; ?>"><?=$block["Data"]->miner; ?></a><br />
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
													if($block["Response"]=="OK"):
												?>

												<table class="table">
													<tr>
														<td>
															<strong>Parent Hash</strong>
															<p>Hash of the parent block</p>

														</td>
														<td><a href="/HIASBCH/Explorer/Block/<?=$block["Data"]->parentHash; ?>" title="<?=$block["Data"]->parentHash; ?>"><?=$block["Data"]->parentHash; ?></a></td>
													</tr>
													<tr>
														<td>
															<strong>Nonce</strong>
															<p>Number Used Only Once. Hash of the Proof of Work. A unique random 32 bit (4 byte) number that meets the difficulty restrictions and is used to hash the value of the block. The nonce must be lower than the target difficulty/target hash for a miner to be successful.</p>

														</td>
														<td><?=$block["Data"]->nonce; ?></td>
													</tr>
													<tr>
														<td>
															<strong>Mixhash</strong>
															<p>256 bit hash calculated using the nonce that is combined with the nonce to prove that enough computation was carried out.</p>

														</td>
														<td><?=$block["Data"]->mixHash; ?></td>
													</tr>
													<tr>
														<td>
															<strong>Height</strong>
															<p>Height of the blockchain as of current block</p>

														</td>
														<td><?=hexdec($block["Data"]->number); ?></td>
													</tr>
													<tr>
														<td>
															<strong>Size</strong>
															<p>Size of the current block </p>

														</td>
														<td><?=hexdec($block["Data"]->size); ?> bytes</td>
													</tr>
													<tr>
														<td>
															<strong>Difficulty</strong>
															<p>Difficulty level of current block (How hard it was to mine this block). Periodically adjusted to ensure the target number of mined blocks is met.</p>

														</td>
														<td><?=hexdec($block["Data"]->difficulty); ?></td>
													</tr>
													<tr>
														<td>
															<strong>Total Difficulty</strong>
															<p>Total difficulty level of blockchain as of current block<br />(How hard it is to mine new blocks)</p>

														</td>
														<td><?=hexdec($block["Data"]->totalDifficulty); ?></td>
													</tr>
													<tr>
														<td>
															<strong>Gas Limit</strong>
															<p>Maximum gas allowed in current block</p>

														</td>
														<td><?=hexdec($block["Data"]->gasLimit); ?> gwei</td>
													</tr>
													<tr>
														<td>
															<strong>Gas Used</strong>
															<p>Gas used in current block</p>

														</td>
														<td><?=hexdec($block["Data"]->gasUsed); ?> gwei</td>
													</tr>
													<tr>
														<td>
															<strong>Uncles</strong>
															<p>Blocks that were committed at the same time as the current block but had a lower share of the proof of work</p>

														</td>
														<td><?=count($block["Data"]->uncles); ?></td>
													</tr>
												</table>

												<?php
													else:
												?>

													<strong>BLOCK NOT FOUND</strong>

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
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<h6 class="panel-title txt-dark">Block Transactions (<?=count($block["Data"]->transactions); ?>)</h6>
								<hr style="margin-bottom: 10px; width: 100% !important;"/>
								<div class="pull-right"></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">

									<div class="form-wrap">
										<div class="row">
											<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

												<?php
													if(count($block["Data"]->transactions)):
												?>

												<table class="table">

												<?php
														foreach($block["Data"]->transactions as $key => $value):
															$transaction = $HIAS->hiasbch->get_transaction($value);
															if($transaction["Response"]=="OK"):
												?>

												<tr>
													<td>

														<div class="row">
															<div class="col-lg-1 col-md-12 col-sm-12 col-xs-12">Hash: </div>
															<div class="col-lg-11 col-md-12 col-sm-12 col-xs-12">

																&nbsp;&nbsp;<a href="/HIASBCH/Explorer/Transaction/<?=$transaction["Data"]->hash; ?>" title="<?=$transaction["Data"]->hash; ?>"><?=(strlen($transaction["Data"]->hash) > 40) ? substr($transaction["Data"]->hash,0,40).'...' : $transaction["Data"]->hash; ?></a>

															</div>
															<div class="col-lg-1 col-md-12 col-sm-12 col-xs-12">Fee:&nbsp;&nbsp;</div>
															<div class="col-lg-11 col-md-12 col-sm-12 col-xs-12">

																&nbsp;&nbsp;<?=hexdec($transaction["Data"]->gas) * hexdec($transaction["Data"]->gasPrice); ?>

															</div>
															<div class="col-lg-1 col-md-12 col-sm-12 col-xs-12">From:&nbsp;&nbsp;</div>
															<div class="col-lg-11 col-md-12 col-sm-12 col-xs-12">

																&nbsp;&nbsp;<a href="/HIASBCH/Explorer/Address/<?=$transaction["Data"]->from; ?>" title="<?=$transaction["Data"]->from; ?>"><?=(strlen($transaction["Data"]->from) > 40) ? substr($transaction["Data"]->from,0,40).'...' : $transaction["Data"]->from; ?></a>

															</div>
															<div class="col-lg-1  col-md-12 col-sm-12 col-xs-12">To:&nbsp;&nbsp;</div>
															<div class="col-lg-11 col-md-12 col-sm-12 col-xs-12">

															<?php if($transaction["Data"]->to): ?>
																&nbsp;&nbsp;<a href="/HIASBCH/Explorer/Address/<?=$transaction["Data"]->to; ?>" title="<?=$transaction["Data"]->to; ?>"><?=(strlen($transaction["Data"]->to) > 40) ? substr($transaction["Data"]->to,0,40).'...' : $transaction["Data"]->to; ?></a>
															<?php else: ?>
																&nbsp;&nbsp;Contract Creation
															<?php endif; ?>

															</div>
															<div class="col-lg-1 col-md-12 col-sm-12 col-xs-12">At:&nbsp;&nbsp;</div>
															<div class="col-lg-11 col-md-12 col-sm-12 col-xs-12">

																&nbsp;&nbsp;<?=date("Y-m-d H:i:s", hexdec($block["Data"]->timestamp)); ?>

															</div>
														</div>

													</td>
												</tr>

												<?php
															else:
												?>
												<?php
															endif;
												?>

												<?php
												?>

															</table>

												<?php
														endforeach;
													else:
												?>
													<strong>No transactions found</strong>

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
				</div>

			</div>

			<?php include dirname(__FILE__) . '/../Includes/Footer.php'; ?>

		</div>

		<?php  include dirname(__FILE__) . '/../Includes/JS.php'; ?>

		<script type="text/javascript" src="/iotJumpWay/Classes/mqttws31.js"></script>
		<script type="text/javascript" src="/iotJumpWay/Classes/iotJumpWay.js"></script>

	</body>
</html>