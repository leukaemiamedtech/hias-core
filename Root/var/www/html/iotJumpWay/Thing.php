<?php session_start();

$pageDetails = [
	"PageID" => "IoT",
	"SubPageID" => "Entities",
	"LowPageID" => "Thing"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../../Classes/Core/GeniSys.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';

$_GeniSysAi->checkSession();

$tid = filter_input(INPUT_GET, 'thing', FILTER_SANITIZE_NUMBER_INT);
$Thing = $iotJumpWay->getThing($tid);

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

		<link href="<?=$domain; ?>/vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
		<link href="<?=$domain; ?>/vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
		<link href="<?=$domain; ?>/vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet" type="text/css">
		<link href="<?=$domain; ?>/dist/css/style.css" rel="stylesheet" type="text/css">
		<link href="<?=$domain; ?>/AI/GeniSysAI/Media/CSS/GeniSys.css" rel="stylesheet" type="text/css">
		<link href="<?=$domain; ?>/vendors/bower_components/fullcalendar/dist/fullcalendar.css" rel="stylesheet" type="text/css"/>
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
									<h6 class="panel-title txt-dark">iotJumpWay Thing #<?=$tid; ?></h6>
								</div>
								<div class="pull-right"></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="form-wrap">
										<form data-toggle="validator" role="form" id="sensor_update_form" action="<?=$domain; ?>/iotJumpWay/Things/Update" method="POST" enctype="multipart/form-data" target="uploader">
											<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
												<div class="form-group">
													<label for="name" class="control-label mb-10">Name</label>
													<input type="text" class="form-control" id="name" name="name" placeholder="Things Name" required value="<?=$Thing["context"]["Data"]["name"]["value"]; ?>">
													<span class="help-block">Name of Thing</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Description</label>
													<input type="text" class="form-control" id="description" name="description" placeholder="Things Name" required value="<?=$Thing["context"]["Data"]["description"]["value"]; ?>">
													<span class="help-block">Description of Thing</span>
												</div>
												<div class="form-group">
													<label class="control-label mb-10">Category</label>
													<select class="form-control" id="category" name="category" required>
														<option value="Sensor" <?=$Thing["context"]["Data"]["type"] == "Sensor" ? " selected " : ""; ?>>Sensor</option>
														<option value="Actuator" <?=$Thing["context"]["Data"]["type"] == "Actuator" ? " selected " : ""; ?>>Actuator</option>
													</select>
													<span class="help-block">Sensor or Actuator</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Manufacturer</label>
													<input type="text" class="form-control" id="manufacturer" name="manufacturer" placeholder="Thing manufacturer" required value="<?=$Thing["context"]["Data"]["thing"]["manufacturer"]; ?>">
													<span class="help-block">Name of thing manufacturer</span>
												</div>
												<div class="form-group">
													<label for="name" class="control-label mb-10">Model</label>
													<input type="text" class="form-control" id="model" name="model" placeholder="Thing model" required value="<?=$Thing["context"]["Data"]["thing"]["model"]; ?>">
													<span class="help-block">Thing model</span>
												</div>
												<div class="form-group">
													<label class="control-label mb-10">Properties <a href="javascript:void(0);" id="addProperty"><i class="fa fa-plus-circle"></i></a></label>
													<div id="propertyContent">
														<?php
															if(isSet($Thing["context"]["Data"]["properties"])):
																foreach($Thing["context"]["Data"]["properties"] AS $key => $value):
																	if($key != "image"):
														?>

														<div class="row" style="margin-bottom: 5px;" id="property-<?=$key; ?>">
															<div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
																<input type="text" class="form-control" name="properties[]" placeholder="<?=$key; ?>" value="<?=$key; ?>" required>
															</div>
															<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
																<a href="javascript:void(0);" class="removeProperty" data-id="<?=$key; ?>"><i class="fas fa-trash-alt"></i></a>
															</div>
														</div>

														<?php
																	endif;
																endforeach;
															endif;
														?>
													</div>
													<span class="help-block">Thing Properties</span>
												</div>
												<div class="form-group">
													<label class="control-label mb-10">Commands <a href="javascript:void(0);" id="addCommand"><i class="fa fa-plus-circle"></i></a></label>
													<div id="commandsContent">
														<?php
															if(isSet($Thing["context"]["Data"]["commands"])):
																foreach($Thing["context"]["Data"]["commands"] AS $key => $value):
																	if(is_array($value)):
																		$value = implode(',',$value);
																	endif;
														?>

														<div class="row" style="margin-bottom: 5px;" id="command-<?=$key; ?>">
															<div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
																<strong><?=ucfirst($key); ?></strong>
																<input type="text" class="form-control" name="commands[<?=$key; ?>]" placeholder="Commands as comma separated string" value="<?=$value; ?>" required>
															</div>
															<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
																<br /><a href="javascript:void(0);" class="removeCommand" data-id="<?=$key; ?>"><i class="fas fa-trash-alt"></i></a>
															</div>
														</div>

														<?php
																endforeach;
															endif;
														?>

													</div>
													<span class="help-block">Thing States</span>
												</div>
												<div class="form-group">
													<label class="control-label mb-10">States <a href="javascript:void(0);" id="addState"><i class="fa fa-plus-circle"></i></a></label>
													<div id="stateContent">

													<?php
														if(isSet($Thing["context"]["Data"]["states"])):
															foreach($Thing["context"]["Data"]["states"] AS $key => $value):
													?>

													<div class="row" style="margin-bottom: 5px;" id="state-<?=$key; ?>">
														<div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
															<input type="text" class="form-control" name="states[]" placeholder="State" value="<?=$value; ?>" required>
														</div>
														<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
															<a href="javascript:void(0);" class="removeState" data-id="<?=$key; ?>"><i class="fas fa-trash-alt"></i></a>
														</div>
													</div>

													<?php
															endforeach;
														endif;
													?>
													<span class="hide" id="lastState"><?=$key; ?></span>

													</div>
													<span class="help-block">Thing States</span>
												</div>
												<div class="form-group mb-0">
													<input type="hidden" name="thing" id="thing" value="<?=$Thing["context"]["Data"]["sid"]["value"]; ?>" />
													<button type="submit" class="btn btn-success btn-anim" id="update_thing"><i class="icon-rocket"></i><span class="btn-text">Update Thing</span></button>
												</div>
											</div>
											<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
												<div class="form-group">
													<label for="name" class="control-label mb-10">Image</label>
													<input type="file" class="form-control" id="image" name="image" />
													<span class="help-block">Thing image</span>
												</div>
												<div class="clearfix"></div>
											</div>
										</form>

										<iframe id="uploader" name="uploader" height="100" width="300" frameborder="0" scrolling="no" class=""></iframe>

									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">Thing Schema</h6>
								</div>
								<div class="pull-right"></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div style="height: 400px; overflow-y: scroll; overflow-x: hidden;">
										<?php echo "<pre id='schema'>"; ?> <?php print_r(json_encode($Thing["context"]["Data"], JSON_PRETTY_PRINT)); ?> <?php echo "</pre>"; ?>
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

		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/mqttws31.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/iotJumpWay.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/iotJumpWayUI.js"></script>

	</body>

</html>
