<?php session_start();

$pageDetails = [
	"PageID" => "HIS",
	"SubPageID" => "Staff",
	"LowPageID" => "Cancelled"
];

include dirname(__FILE__) . '/../../../Classes/Core/init.php';
include dirname(__FILE__) . '/../../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../../Users/Staff/Classes/Staff.php';

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
	<meta name="author" content="hencework" />

	<script src="https://kit.fontawesome.com/58ed2b8151.js" crossorigin="anonymous"></script>

	<link type="image/x-icon" rel="icon" href="/img/favicon.png" />
	<link type="image/x-icon" rel="shortcut icon" href="/img/favicon.png" />
	<link type="image/x-icon" rel="apple-touch-icon" href="/img/favicon.png" />

	<link href="/dist/css/style.css" rel="stylesheet" type="text/css">
</head>
<body>

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
									<h6 class="panel-title txt-dark">Cancelled HIAS Staff</h6>
								</div>
								<div class="pull-right"></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">

									<p>This area allows you to review all cancelled HIAS Staff accounts.</p>

								</div>
							</div>
						</div>
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark"><i class="fa fa-users"></i> Cancelled Staff</h6>
								</div>
								<div class="pull-right"><a href="/Users/Staff/Create"><i class="fa fa-plus"></i> Create Staff Member</a></div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="table-wrap mt-40">
										<div class="table-responsive">
											<table class="table mb-0">
												<thead>
													<tr>
														<th></th>
														<th>DETAILS</th>
														<th>STATUS</th>
														<th>ACTION</th>
													</tr>
												</thead>
												<tbody>

												<?php
													$limit = 0;
													$attrs = "picture.value,id,permissionsCancelled.value,username.value,permissionsAdmin.value,networkStatus.value";
													$Staffs = $Staff->get_staff_members(0, "id DESC", $attrs, "permissionsCancelled.value==True");
													if(!isSet($Staffs["Error"])):
														foreach($Staffs as $key => $value):
												?>

													<tr>
														<td><img src="/Users/Staff/Media/Images/Uploads/<?=$value["picture"]["value"];?>" style="max-width: 100px; !important;" /></td>
														<td>
															<strong>Name:</strong> <?=$value["username"]["value"];?><br />
															<strong>ID:</strong> <?=$value["id"];?>
															<strong>Admin:</strong> <?=$value["permissionsAdmin"]["value"] ? "Yes" : "No";?><br />
															<?=$value["permissionsCancelled"]["value"] ? "<strong>CANCELLED</strong><br /><br />" : "";?>
														</td>
														<td>
															<div class="label label-table <?=$value["networkStatus"]["value"] == "OFFLINE" ? "label-danger" : "label-success"; ?>">
																<?=$value["networkStatus"]["value"] == "OFFLINE" ? "OFFLINE" : "ONLINE"; ?>
															</div>
														</td>
														<td><a href="/Users/Staff/<?=$value["id"];?>"><i class="fa fa-edit"></i> Edit</a></a></td>
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
					<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12"></div>
				</div>

			</div>

			<?php include dirname(__FILE__) . '/../../Includes/Footer.php'; ?>

		</div>

		<?php  include dirname(__FILE__) . '/../../Includes/JS.php'; ?>

		<script type="text/javascript" src="/iotJumpWay/Classes/mqttws31.js"></script>
		<script type="text/javascript" src="/iotJumpWay/Classes/iotJumpWay.js"></script>

</body>

</html>