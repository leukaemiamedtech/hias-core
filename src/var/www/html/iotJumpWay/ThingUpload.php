<?php session_start();

$pageDetails = [
	"PageID" => "IoT",
	"SubPageID" => "IoT",
	"LowPageID" => "Devices"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';

$response = $iotJumpWay->create_thing();

?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
	</head>

	<body id="GeniSysAI">

		<?php  include dirname(__FILE__) . '/../Includes/JS.php'; ?>

		<script type="text/javascript" src="/iotJumpWay/Classes/mqttws31.js"></script>
		<script type="text/javascript" src="/iotJumpWay/Classes/iotJumpWay.js"></script>
		<script type="text/javascript" src="/iotJumpWay/Classes/iotJumpWayUI.js"></script>

		<script>
			$(document).ready(function () {
				if("<?=$response["Response"]; ?>" === "OK"){
					Logging.logMessage("Core", "Forms", "Thing Created OK");
					window.parent.$('.modal-title').text('Create Thing');
					window.parent.$('.modal-body').text("Thing Created OK!");
					window.parent.$('#responsive-modal').modal('show');
				} else {
					Logging.logMessage("Core", "Forms", "Thing Update OK");
					window.parent.$('.modal-title').text('Thing Update');
					window.parent.$('.modal-body').text("<?=$response["Message"]; ?>");
					window.parent.$('#responsive-modal').modal('show');
				}
			});
		</script>

	</body>

</html>
