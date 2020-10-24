<?php session_start();

$pageDetails = [
	"PageID" => "HIS",
	"SubPageID" => "Patients"
];

include dirname(__FILE__) . '/../../../Classes/Core/init.php';
include dirname(__FILE__) . '/../../../Classes/Core/GeniSys.php';
include dirname(__FILE__) . '/../../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../../Hospital/Patients/Classes/Patients.php';

$_GeniSysAi->checkSession();

$Locations = $iotJumpWay->getLocations(0, "id ASC");

$PId = filter_input(INPUT_GET, 'patient', FILTER_SANITIZE_NUMBER_INT);
$Patient = $Patients->getPatient($PId);

$cancelled = $Patient["context"]["Data"]["status"]["cancelled"] ? True : False;

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
	<meta name="author" content="hencework" />

	<script src="https://kit.fontawesome.com/58ed2b8151.js" crossorigin="anonymous"></script>

	<link type="image/x-icon" rel="icon" href="<?=$domain; ?>/img/favicon.png" />
	<link type="image/x-icon" rel="shortcut icon" href="<?=$domain; ?>/img/favicon.png" />
	<link type="image/x-icon" rel="apple-touch-icon" href="<?=$domain; ?>/img/favicon.png" />

	<link href="<?=$domain; ?>/vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
	<link href="<?=$domain; ?>/vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
	<link href="<?=$domain; ?>/vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet" type="text/css">
	<link href="<?=$domain; ?>/dist/css/style.css" rel="stylesheet" type="text/css">
	<link href="<?=$domain; ?>/AI/GeniSysAI/Media/CSS/GeniSys.css" rel="stylesheet" type="text/css">
	<link href="<?=$domain; ?>/vendors/bower_components/fullcalendar/dist/fullcalendar.css" rel="stylesheet"
		type="text/css" />
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
									<h6 class="panel-title txt-dark"><i class="fa fa-hospital-user"></i> Hospital Patient #<?=$PId; ?></h6>
								</div>
								<div class="pull-right"></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="form-wrap">
										<form data-toggle="validator" role="form" id="patient_update">
											<hr class="light-grey-hr" />
											<div class="row">
												<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
													<div class="form-group">
														<label for="name" class="control-label mb-10">Name</label>
														<input type="text" class="form-control" id="name" name="name" placeholder="Patient Name" required value="<?=$Patient["context"]["Data"]["name"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block"> Name of patient</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Description</label>
														<input type="text" class="form-control" id="description" name="description" placeholder="Device Description" required value="<?=$Patient["context"]["Data"]["description"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block"> Patient description</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Username</label>
														<input type="text" class="form-control" id="username" name="username" placeholder="Patient Username" required value="<?=$Patient["context"]["Data"]["username"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block"> Username of patient</span>
													</div>
													<div class="form-group">
														<label class="control-label mb-10">Category</label>
														<select class="form-control" id="category" name="category" required <?=$cancelled ? " disabled " : ""; ?>>
															<option value="">PLEASE SELECT</option>

															<?php
																$categories = $Patients->getPatientCategories();
																if(count($categories)):
																	foreach($categories as $key => $value):
															?>

															<option value="<?=$value["category"]; ?>" <?=$Patient["context"]["Data"]["category"]["value"][0]==$value["category"] ? " selected " : ""; ?>><?=$value["category"]; ?></option>

															<?php
																	endforeach;
																endif;
															?>

														</select>
														<span class="help-block">Patient category</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Email *</label>
														<input type="text" class="form-control" id="email" name="email" placeholder="Email of patient" required value="<?=$Patient["context"]["Data"]["email"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">Email of patient</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Address Street Address</label>
														<input type="text" class="form-control" id="streetAddress" name="streetAddress" placeholder="iotJumpWay Location street address" required value="<?=$Patient["context"]["Data"]["username"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">iotJumpWay Location street address</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Address Locality</label>
														<input type="text" class="form-control" id="addressLocality" name="addressLocality" placeholder="iotJumpWay Location address locality" required value="<?=$Patient["context"]["Data"]["address"]["value"]["addressLocality"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">iotJumpWay Location address locality</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Address Postal Code</label>
														<input type="text" class="form-control" id="postalCode" name="postalCode" placeholder="iotJumpWay Location postal code" required value="<?=$Patient["context"]["Data"]["address"]["value"]["postalCode"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">iotJumpWay Location post code</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">NFC UID</label>
														<input type="text" class="form-control" id="nfc" name="nfc" placeholder="NFC UID"  value="<?=$Patient["context"]["Data"]["nfc"]["value"]; ?>" <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">UID of patient's NFC card/fob/implant</span>
													</div>
													<?php if(!$cancelled): ?>
													<div class="form-group mb-0">
														<input type="hidden" class="form-control" id="update_patient" name="update_patient" required value="1">
														<button type="submit" class="btn btn-success btn-anim"><i class="icon-rocket"></i><span class="btn-text">Update Patient</span></button>
													</div>
													<?php endif; ?>
												</div>
												<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
													<div class="form-group">
														<label for="name" class="control-label mb-10">Photo</label>
														<input type="file" class="form-control" id="photo" name="photo" />
														<span class="help-block"> Photo of patient</span>
													</div>
													<div class="form-group">
														<label class="control-label mb-10">Location</label>
														<select class="form-control" id="lid" name="lid" required <?=$cancelled ? " disabled " : ""; ?>>
															<option value="">PLEASE SELECT</option>

															<?php
																$Locations = $iotJumpWay->getLocations();
																if(count($Locations["Data"])):
																	foreach($Locations["Data"] as $key => $value):
															?>

																<option value="<?=$value["lid"]["value"]; ?>" <?=$Patient["context"]["Data"]["lid"]["value"]==$value["lid"]["value"] ? " selected " : ""; ?>>#<?=$value["lid"]["value"]; ?>: <?=$value["name"]["value"]; ?></option>

															<?php
																	endforeach;
																endif;
															?>

														</select>
														<span class="help-block"> Location of patient</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Active</label>
														<input type="checkbox" id="active" name="active" value="1" <?=$Patient["context"]["Data"]["status"]["active"] ? " checked " : ""; ?>  <?=$cancelled ? " disabled " : ""; ?>/>
														<span class="help-block">Is Patient Active?</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Admitted</label>
														<input type="checkbox" id="admitted" name="admitted" value="1" <?=$Patient["context"]["Data"]["status"]["admitted"] ? " checked "  : ""; ?>  <?=$cancelled ? " disabled " : ""; ?>/>
														<span class="help-block">Is Patient Admitted?</span>
													</div>
													<div class="form-group">
														<label for="name" class="control-label mb-10">Is cancelled:</label>
														<input type="checkbox" class="" id="cancelled" name="cancelled" value=1 <?=$Patient["context"]["Data"]["status"]["cancelled"] ? " checked " : ""; ?> <?=$cancelled ? " disabled " : ""; ?> <?=$cancelled ? " disabled " : ""; ?>>
														<span class="help-block">Is staff member cancelled?</span>
													</div>
													<div class="clearfix"></div>
												</div>
											</div>
											<hr class="light-grey-hr" />
										</form>
									</div>
								</div>
							</div>
						</div><br />
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">Patient History</h6>
								</div>
								<div class="pull-right"><a href="<?=$domain; ?>/Hospital/Patients/<?=$Patient["context"]["Data"]["pid"]["value"]; ?>/History"><i class="fa fa-eye pull-left"></i> View All Patient History</a></div>
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
													$history = $Patients->retrieveHistory($Patient["context"]["Data"]["pid"]["value"], 5);
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
															<a href="<?=$domain; ?>/Hospital/Patients/<?=$Patient["context"]["Data"]["pid"]["value"]; ?>/Transaction/<?=$value["hash"];?>">#<?=$value["hash"];?></a>
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
									<h6 class="panel-title txt-dark">Patient Transactions</h6>
								</div>
								<div class="pull-right"><a href="<?=$domain; ?>/Hospital/Patients/<?=$Patient["context"]["Data"]["pid"]["value"]; ?>/Transactions"><i class="fa fa-eye pull-left"></i> View All Patient Transactions</a></div>
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
													$transactions = $Patients->retrieveTransactions($Patient["context"]["Data"]["pid"]["value"], 5);
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
													<td><a href="<?=$domain; ?>/Hospital/Patients/<?=$Patient["context"]["Data"]["pid"]["value"]; ?>/Transaction/<?=$value["id"];?>">#<?=$value["id"];?></a></td>
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
						</div>
					</div>
					<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<img src="<?=$domain; ?>/Hospital/Patients/Media/Images/Uploads/<?=$Patient["context"]["Data"]["picture"]["value"];?>" style="width: 100%; !important;" />
								</div>
							</div>
						</div>
						<?php if(!$cancelled): ?>
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="pull-right"><a href="javascipt:void(0)" id="reset_pt_apriv"><i class="fa fa-refresh"></i> Reset API Key</a></div>
									<div class="form-group">
										<label class="control-label col-md-5">Identifier</label>
										<div class="col-md-9">
											<p class="form-control-static" id="idappid"><?=$Patient["context"]["Data"]["keys"]["public"]; ?></p>
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
											<p class="form-control-static" id="bcid"><?=$Patient["context"]["Data"]["blockchain"]["address"]; ?></p>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="pull-right"><a href="javascipt:void(0)" id="reset_patient_mqtt"><i
												class="fa fa-refresh"></i> Reset MQTT Password</a></div>
									<div class="form-group">
										<label class="control-label col-md-5">MQTT Username</label>
										<div class="col-md-9">
											<p class="form-control-static" id="pntmqttu"><?=$_GeniSys->_helpers->oDecrypt($Patient["context"]["Data"]["mqtt"]["username"]); ?></p>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-5">MQTT Password</label>
										<div class="col-md-9">
											<p class="form-control-static"><span id="pntmqttp"><?=$_GeniSys->_helpers->oDecrypt($Patient["context"]["Data"]["mqtt"]["password"]); ?></span>
											</p>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="pull-right"><a href="javascipt:void(0)" id="reset_patient_amqp"><i class="fa fa-refresh"></i> Reset AMQP Password</a></div>
									<div class="form-group">
										<label class="control-label col-md-5">AMQP Username</label>
										<div class="col-md-9">
											<p class="form-control-static" id="pnamqpu"><?=$Patient["context"]["Data"]["amqp"]["username"] ? $_GeniSys->_helpers->oDecrypt($Patient["context"]["Data"]["amqp"]["username"]) : ""; ?></p>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-5">AMQP Password</label>
										<div class="col-md-9">
											<p class="form-control-static"><span id="pnamqpp"><?=$Patient["context"]["Data"]["amqp"]["password"] ? $_GeniSys->_helpers->oDecrypt($Patient["context"]["Data"]["amqp"]["password"]) : ""; ?></span>
											<p><strong>Last Updated:</strong> <?=array_key_exists("timestamp", $Patient["context"]["Data"]["amqp"]) ? $Patient["context"]["Data"]["amqp"]["timestamp"] : "NA"; ?></p>
											</p>
										</div>
									</div>
								</div>
							</div>
						</div>
						<?php endif; ?>
					</div>
				</div>

			</div>

			<?php include dirname(__FILE__) . '/../../Includes/Footer.php'; ?>

		</div>

		<?php  include dirname(__FILE__) . '/../../Includes/JS.php'; ?>

		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/mqttws31.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/iotJumpWay.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/Hospital/Patients/Classes/Patients.js"></script>

		<script type="text/javascript">
			$(document).ready(function() {
				Patients.HideInputs();
			});
		</script>

	</body>
</html>