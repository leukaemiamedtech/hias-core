<?php session_start();

$pageDetails = [
	"PageID" => "HIASHDI",
	"SubPageID" => "HIASHDI",
	"LowPageID" => "Entity"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../HIASCDI/Classes/Interface.php';
include dirname(__FILE__) . '/../HIASHDI/Classes/Interface.php';

$broker = $HiasHdiInterface->get_hiashdi_entity();

header('Content-Disposition: attachment; filename="credentials.json"');
print_r(json_encode([
	"iotJumpWay" => [
		"host" => $HIAS->host,
		"port" => 8883,
		"location" => $broker["networkLocation"]["value"],
		"zone" => $broker["networkZone"]["value"],
		"entity" => $broker["id"],
		"name" => $broker["name"]["value"],
		"un" => $HIAS->helpers->oDecrypt($broker["authenticationMqttUser"]["value"]),
		"up" => $HIAS->helpers->oDecrypt($broker["authenticationMqttKey"]["value"]),
		"ipinfo" => $HIAS->helpers->oDecrypt($broker["authenticationIpinfoKey"]["value"])
	],
	"server" => [
		"host" => $HIAS->host,
		"ip" => $broker["ipAddress"]["value"],
		"port" => $broker["port"]["value"]
	],
	"hiashdi" => [
		"name" => $broker["name"]["value"],
		"version" => $HIAS->hiashdi->confs["hiashdiv"],
		"endpoint" => $HIAS->hiashdi->confs["url"]
	],
	"mongodb" => [
		"host" => "localhost",
		"db" => $HIAS->_mdbname,
		"un" => $HIAS->_mdbusername,
		"up" => $HIAS->_mdbpassword
	]
]));

?>