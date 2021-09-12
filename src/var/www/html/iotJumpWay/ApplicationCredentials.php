<?php session_start();

$pageDetails = [
	"PageID" => "IoT",
	"SubPageID" => "Entities",
	"LowPageID" => "Application"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';

$aid = filter_input(INPUT_GET, 'application', FILTER_SANITIZE_STRING);
$application = $iotJumpWay->get_application($aid);

header('Content-Disposition: attachment; filename="credentials.json"');

if(in_array("mqtt",$application["protocols"]["value"])):
	$userName = $application["authenticationMqttUser"]["value"];
	$userPassword = $application["authenticationMqttKey"]["value"];
elseif(in_array("amqp",$application["protocols"]["value"])):
	$userName = $application["authenticationAmqpUser"]["value"];
	$userPassword = $application["authenticationAmqpKey"]["value"];
elseif(in_array("coap",$application["protocols"]["value"])):
	$userName = $application["authenticationCoapUser"]["value"];
	$userPassword = $application["authenticationCoapKey"]["value"];
elseif(in_array("ble",$application["protocols"]["value"]) || in_array("bluetooth",$application["protocols"]["value"])):
	$userName = $application["authenticationMqttUser"]["value"];
	$userPassword = $application["authenticationMqttKey"]["value"];
endif;

print_r(json_encode([
	"iotJumpWay" => [
		"host" => $HIAS->host,
		"port" => 8883,
		"location" => $application["networkLocation"]["value"],
		"zone" => $application["networkZone"]["value"],
		"entity" => $application["id"],
		"name" => $application["name"]["value"],
		"un" => $HIAS->helpers->oDecrypt($userName),
		"up" => $HIAS->helpers->oDecrypt($userPassword),
		"ipinfo" => $HIAS->helpers->oDecrypt($application["authenticationIpinfoKey"]["value"])
	],
	"hiascdi" => [
		"endpoint" => "/hiascdi/v1",
		"un" => $application["id"]
	],
	"hiashdi" => [
		"endpoint" => "/hiashdi/v1",
		"un" => $application["id"]
	],
	"hiasbch" => [
		"endpoint" => "/hiasbch/api/",
		"un" => $application["authenticationBlockchainUser"]["value"],
		"up" => $HIAS->helpers->oDecrypt($application["authenticationBlockchainKey"]["value"]),
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
