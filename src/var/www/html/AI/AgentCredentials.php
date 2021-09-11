<?php session_start();

$pageDetails = [
	"PageID" => "AI",
	"SubPageID" => "AIAgents",
	"LowPageID" => "AIAgents"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../HIASCDI/Classes/Interface.php';
include dirname(__FILE__) . '/../AI/Classes/AI.php';
include dirname(__FILE__) . '/../AI/Classes/AiAgents.php';

$aid = filter_input(INPUT_GET, 'agent', FILTER_SANITIZE_STRING);
$agent = $AiAgents->get_agent($aid);

header('Content-Disposition: attachment; filename="credentials.json"');
print_r(json_encode([
	"iotJumpWay" => [
		"host" => $HIAS->host,
		"port" => 8883,
		"location" => $agent["networkLocation"]["value"],
		"zone" => $agent["networkZone"]["value"],
		"entity" => $agent["id"],
		"name" => $agent["name"]["value"],
		"un" => $HIAS->helpers->oDecrypt($agent["authenticationMqttUser"]["value"]),
		"up" => $HIAS->helpers->oDecrypt($agent["authenticationMqttKey"]["value"]),
		"ipinfo" => $HIAS->helpers->oDecrypt($agent["authenticationIpinfoKey"]["value"])
	],
	"server" => [
		"ip" => $agent["ipAddress"]["value"],
		"port" => $agent["northPort"]["value"]
	],
	"socket" => [
		"ip" => $agent["ipAddress"]["value"],
		"port" => $agent["socketPort"]["value"]
	],
	"stream" => [
		"ip" => $agent["ipAddress"]["value"],
		"port" => $agent["streamPort"]["value"]
	]
]));
