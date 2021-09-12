<?php session_start();

$pageDetails = [
	"PageID" => "HIASBCH",
	"SubPageID" => "HIASBCH",
	"LowPageID" => "Settings"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';

$hiasbch = $HIAS->hiasbch->get_hiasbch();

header('Content-Disposition: attachment; filename="credentials.json"');

if(in_array("mqtt",$hiasbch["protocols"]["value"])):
	$userName = $hiasbch["authenticationMqttUser"]["value"];
	$userPassword = $hiasbch["authenticationMqttKey"]["value"];
elseif(in_array("amqp",$hiasbch["protocols"]["value"])):
	$userName = $hiasbch["authenticationAmqpUser"]["value"];
	$userPassword = $hiasbch["authenticationAmqpKey"]["value"];
elseif(in_array("coap",$hiasbch["protocols"]["value"])):
	$userName = $hiasbch["authenticationCoapUser"]["value"];
	$userPassword = $hiasbch["authenticationCoapKey"]["value"];
elseif(in_array("ble",$hiasbch["protocols"]["value"]) || in_array("bluetooth",$hiasbch["protocols"]["value"])):
	$userName = $hiasbch["authenticationMqttUser"]["value"];
	$userPassword = $hiasbch["authenticationMqttKey"]["value"];
endif;

print_r(json_encode([
	"iotJumpWay" => [
		"host" => $HIAS->host,
		"port" => 8883,
		"location" => $hiasbch["networkLocation"]["value"],
		"zone" => $hiasbch["networkZone"]["value"],
		"entity" => $hiasbch["id"],
		"name" => $hiasbch["name"]["value"],
		"un" => $HIAS->helpers->oDecrypt($userName),
		"up" => $HIAS->helpers->oDecrypt($userPassword),
		"ipinfo" => $HIAS->helpers->oDecrypt($hiasbch["authenticationIpinfoKey"]["value"])
	],
	"server" => [
		"host" => $HIAS->host,
		"ip" => $hiasbch["ipAddress"]["value"],
		"port" => $hiasbch["port"]["value"]
	],
	"hiascdi" => [
		"endpoint" => "/hiascdi/v1",
		"un" => $hiasbch["id"]
	],
	"hiashdi" => [
		"endpoint" => "/hiashdi/v1",
		"un" => $hiasbch["id"]
	],
	"hiasbch" => [
		"endpoint" => "/hiasbch/api/",
		"un" => $hiasbch["authenticationBlockchainUser"]["value"],
		"up" => $HIAS->helpers->oDecrypt($hiasbch["authenticationBlockchainKey"]["value"]),
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
