<?php session_start();

$pageDetails = [
	"PageID" => "IoT",
	"SubPageID" => "IoT",
	"LowPageID" => "Devices"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../../Classes/Core/GeniSys.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';

$_GeniSysAi->checkSession();
$response = $iotJumpWay->updateSensor();

?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
	</head>

	<body id="GeniSysAI">

		<?php  include dirname(__FILE__) . '/../Includes/JS.php'; ?>

		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/mqttws31.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/iotJumpWay.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/iotJumpWayUI.js"></script>

		<script>
			$(document).ready(function () {
				if("<?=$response["Response"]; ?>" === "OK"){
					Logging.logMessage("Core", "Forms", "Sensor/Actuator Updated OK");
					window.parent.$('.modal-title').text('Sensor/Actuator Update');
					window.parent.$('.modal-body').text("Sensor/Actuator Update OK!");
					window.parent.$('#responsive-modal').modal('show');
				} else {
					Logging.logMessage("Core", "Forms", "Sensor Update OK");
					window.parent.$('.modal-title').text('Sensor/Actuator Update');
					window.parent.$('.modal-body').text("Sensor/Actuator Update Failed!");
					window.parent.$('#responsive-modal').modal('show');
				}
			});
		</script>

	</body>

</html>
