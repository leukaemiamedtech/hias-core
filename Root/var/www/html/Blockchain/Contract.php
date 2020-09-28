<?php session_start();

$pageDetails = [
	"PageID" => "Blockchain",
	"SubPageID" => "Contracts"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../../Classes/Core/GeniSys.php';
include dirname(__FILE__) . '/../Blockchain/Classes/Blockchain.php';

$_GeniSysAi->checkSession();
$contract = $Blockchain->getContract(filter_input(INPUT_GET, "contract", FILTER_SANITIZE_NUMBER_INT));

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
		<link href="<?=$domain; ?>/GeniSysAI/Media/CSS/GeniSys.css" rel="stylesheet" type="text/css">
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
										<h6 class="panel-title txt-dark">HIAS Blockchain Smart Contract #<?=$contract["id"]; ?></h6>
									</div>
									<div class="pull-right"><button type="submit" class="btn btn-success btn-anim" id="replenish"><i class="icon-rocket"></i><span class="btn-text">Replenish</span></button></div>
									<div class="clearfix"></div>
								</div>
								<div class="panel-wrapper collapse in">
									<div class="panel-body">
										<div class="form-wrap">

											<form data-toggle="validator" role="form" id="send">
												<div class="row">
													<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
														<div class="form-group">
															<label for="name" class="control-label mb-10">HIAS Blockchain Account</label>
															<input type="text" class="form-control" id="acc" name="acc" placeholder="HIAS Blockchain account" required value="" autocomplete="false">
															<span class="help-block">HIAS Blockchain account</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">HIAS Blockchain Password</label>
															<input type="password" class="form-control" id="p" name="p" placeholder="HIAS Blockchain password" required value="" autocomplete="false">
															<span class="help-block">HIAS Blockchain password</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">Contract Name</label>
															<input type="text" class="form-control" id="name" name="name" placeholder="HIAS Blockchain Contract Name" required value="<?=$Blockchain->_GeniSys->_helpers->oDecrypt($contract["name"]); ?>">
															<span class="help-block"> HIAS Blockchain contract name</span>
														</div>
														<div class="form-group">
															<label for="name" class="control-label mb-10">HIAS Blockchain Contract Address</label>
															<input type="text" class="form-control" id="contract" name="contract" placeholder="HIAS Blockchain Contract Address" required value="<?=$Blockchain->_GeniSys->_helpers->oDecrypt($contract["contract"]); ?>">
															<span class="help-block"> HIAS Blockchain contract address</span>
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
															<input type="hidden" id="id" name="id" required value="<?=$contract["id"]; ?>">
															<button type="submit" class="btn btn-success btn-anim" id="interact"><i class="icon-rocket"></i><span class="btn-text">Interact</span></button>
														</div>
													</div>
													<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
														<div class="form-group">
															<label for="name" class="control-label mb-10">Application Binary Interface</label>
															<textarea class="form-control" id="abi" name="abi" placeholder="Application Binary Interface" required rows="12"><?=$contract["abi"]; ?></textarea>
															<span class="help-block">Application Binary Interface</span>
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
										<h6 class="panel-title txt-dark">Contract History</h6>
									</div>
									<div class="pull-right"><a href="<?=$domain; ?>/Blockchain/Contracts/<?=$contract["id"]; ?>/History"><i class="fa fa-eye pull-left"></i> View All Contract History</a></div>
									<div class="clearfix"></div>
								</div>
								<div class="panel-wrapper collapse in">
									<div class="panel-body">
										<div class="table-wrap mt-40">
											<div class="table-responsive">
												<table class="table mb-0">
													<thead>
													<tr>
														<th>ID</th>
														<th>Action</th>
														<th>Receipt</th>
														<th>Time</th>
													</tr>
													</thead>
													<tbody>

													<?php
														$userDetails = "";
														$history = $Blockchain->retrieveContractHistory($contract["id"], 5);
														if(count($history)):
															foreach($history as $key => $value):
																	if($value["uid"]):
																		$user = $_GeniSysAi->getUser($value["uid"]);
																		$userDetails = "User ID #" . $value["uid"] . " (" . $user["name"] . ") ";
																	endif;
													?>

													<tr>
														<td>#<?=$value["id"];?></td>
														<td><?=$userDetails;?><?=$value["action"];?></td>
														<td>

															<?php
																if($value["hash"]):
															?>
																<a href="<?=$domain; ?>/Blockchain/Contracts/<?=$contract["id"]; ?>/Transaction/<?=$value["hash"];?>">#<?=$value["hash"];?></a>
															<?php
																else:
															?>
																NA
															<?php
																endif;
															?>

														</td>
														<td><?=date("Y-m-d H:i:s", $value["time"]);?></td>
													</tr>

													<?php
															endforeach;
														endif;
													?>

													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div><br />
							<div class="panel panel-default card-view panel-refresh">
								<div class="panel-heading">
									<div class="pull-left">
										<h6 class="panel-title txt-dark">Device Transactions</h6>
									</div>
									<div class="pull-right"><a href="<?=$domain; ?>/Blockchain/Contracts/<?=$contract["id"]; ?>/Transactions"><i class="fa fa-eye pull-left"></i> View All Device Transactions</a></div>
									<div class="clearfix"></div>
								</div>
								<div class="panel-wrapper collapse in">
									<div class="panel-body">
										<div class="table-wrap mt-40">
											<div class="table-responsive">
												<table class="table mb-0">
													<thead>
													<tr>
														<th>ID</th>
														<th>Action</th>
														<th>Receipt</th>
														<th>Time</th>
													</tr>
													</thead>
													<tbody>

													<?php
														$transactions = $Blockchain->retrieveContractTransactions($contract["id"], 5);
														if(count($transactions)):
															foreach($transactions as $key => $value):
																if($value["uid"]):
																	$user = $_GeniSysAi->getUser($value["uid"]);
																	$userDetails = "User ID #" . $value["uid"] . " (" . $user["name"] . ") ";
																endif;
													?>

													<tr>
														<td>#<?=$value["id"];?></td>
														<td><?=$userDetails;?><?=$value["action"];?></td>
														<td><a href="<?=$domain; ?>/Blockchain/Contracts/<?=$contract["id"]; ?>/Transaction/<?=$value["id"];?>">#<?=$value["id"];?></a></td>
														<td><?=date("Y-m-d H:i:s", $value["time"]);?></td>
													</tr>

													<?php
															endforeach;
														endif;
													?>

													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div><br />
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
