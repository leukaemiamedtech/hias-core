<?php session_start();

$pageDetails = [
    "PageID" => "Robotics",
    "SubPageID" => "List",
    "LowPageID" => "List"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';

include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWayAgents.php';
include dirname(__FILE__) . '/../HIASCDI/Classes/Interface.php';

include dirname(__FILE__) . '/../AI/Classes/AI.php';
include dirname(__FILE__) . '/../Robotics/Classes/Robotics.php';

$rid = filter_input(INPUT_GET, 'unit', FILTER_SANITIZE_STRING);
$robotic = $Robotics->get_robotic($rid, "dateCreated,dateModified,id,*");

header('Content-Disposition: attachment; filename="credentials.json"');
print_r(json_encode([
	"iotJumpWay" => [
		"host" => $HIAS->host,
		"port" => 8883,
		"location" => $robotic["networkLocation"]["value"],
		"entity" => $robotic["id"],
		"name" => $robotic["name"]["value"],
		"un" => $HIAS->helpers->oDecrypt($robotic["authenticationMqttUser"]["value"]),
		"up" => $HIAS->helpers->oDecrypt($robotic["authenticationMqttKey"]["value"]),
		"ipinfo" => $HIAS->helpers->oDecrypt($robotic["authenticationIpinfoKey"]["value"])
	],
	"server" => [
		"ip" => $robotic["ipAddress"]["value"],
		"port" => $robotic["northPort"]["value"]
	],
	"stream" => [
		"ip" => $robotic["ipAddress"]["value"],
		"file" => $robotic["streamFile"]["value"],
		"port" => $robotic["streamPort"]["value"]
	],
	"socket" => [
		"ip" => $robotic["ipAddress"]["value"],
		"port" => $robotic["socketPort"]["value"]
	]
]));
