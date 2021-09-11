<?php session_start();

$pageDetails = [
    "PageID" => "IoT",
    "SubPageID" => "Agents",
	"LowPageID" => "Agents"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWayAgents.php';

$aid = filter_input(INPUT_GET, 'agent', FILTER_SANITIZE_STRING);
$agent = $iotJumpWayAgents->get_agent($aid);

header('Content-Disposition: attachment; filename="credentials.json"');

if(in_array("mqtt",$agent["protocols"]["value"])):
	$userName = $agent["authenticationMqttUser"]["value"];
	$userPassword = $agent["authenticationMqttKey"]["value"];
elseif(in_array("amqp",$agent["protocols"]["value"])):
	$userName = $agent["authenticationAmqpUser"]["value"];
	$userPassword = $agent["authenticationAmqpKey"]["value"];
elseif(in_array("coap",$agent["protocols"]["value"])):
	$userName = $agent["authenticationCoapUser"]["value"];
	$userPassword = $agent["authenticationCoapKey"]["value"];
elseif(in_array("ble",$agent["protocols"]["value"]) || in_array("bluetooth",$agent["protocols"]["value"])):
	$userName = $agent["authenticationMqttUser"]["value"];
	$userPassword = $agent["authenticationMqttKey"]["value"];
endif;

print_r(json_encode([
	"iotJumpWay" => [
		"host" => $HIAS->host,
		"port" => 8883,
		"location" => $agent["networkLocation"]["value"],
		"zone" => $agent["networkZone"]["value"],
		"entity" => $agent["id"],
		"name" => $agent["name"]["value"],
		"un" => $HIAS->helpers->oDecrypt($userName),
		"up" => $HIAS->helpers->oDecrypt($userPassword),
		"ipinfo" => $HIAS->helpers->oDecrypt($agent["authenticationIpinfoKey"]["value"])
	],
	"server" => [
		"host" => $HIAS->host,
		"ip" => $agent["ipAddress"]["value"],
		"port" => $agent["northPort"]["value"]
	],
	"hiascdi" => [
		"endpoint" => "/hiascdi/v1",
		"un" => $agent["id"]
	],
	"hiashdi" => [
		"endpoint" => "/hiashdi/v1",
		"un" => $agent["id"]
	],
	"hiasbch" => [
		"endpoint" => "/hiasbch/api/",
		"un" => $agent["authenticationBlockchainUser"]["value"],
		"up" => $HIAS->helpers->oDecrypt($agent["authenticationBlockchainKey"]["value"]),
		"contracts" =>[
			"hias" => [
				"contract" => $HIAS->hiasbch->confs["contract"],
				"abi" => $HIAS->hiasbch->confs["abi"]
			],
			"iotJumpWay" => [
				"contract" => $HIAS->hiasbch->confs["icontract"],
				"abi" => $HIAS->hiasbch->confs["iabi"]
			]
		]
	]
]));
