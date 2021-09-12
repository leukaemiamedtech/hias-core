<?php session_start();

$pageDetails = [
	"PageID" => "IoT",
	"SubPageID" => "Entities",
	"LowPageID" => "Devices"
];

include dirname(__FILE__) . '/../../Classes/Core/init.php';
include dirname(__FILE__) . '/../iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/../HIASCDI/Classes/Interface.php';
include dirname(__FILE__) . '/../AI/Classes/AI.php';

$did = filter_input(INPUT_GET, 'device', FILTER_SANITIZE_STRING);
$device = $iotJumpWay->get_device($did);

$address = "NA";
$pincode = "NA";
$service = "NA";
$characteristic = "NA";

if(in_array("ble",$device["protocols"]["value"]) || in_array("bluetooth",$device["protocols"]["value"])):
	$address = $device["bluetoothAddress"]["value"];
	$pincode = $device["bluetoothPinCode"]["value"];
	$service = $device["bluetoothServiceUUID"]["value"];
	$characteristic = $device["bluetoothCharacteristicUUID"]["value"];
endif;

header('Content-Disposition: attachment; filename="config.json"');
print_r(json_encode([
	"iotJumpWay" => [
		"host" => $HIAS->host,
		"port" => 8883,
		"location" => $device["networkLocation"]["value"],
		"zone" => $device["networkZone"]["value"],
		"device" => $device["id"],
		"deviceName" => $device["name"]["value"],
		"un" => $HIAS->helpers->oDecrypt($device["authenticationMqttUser"]["value"]),
		"up" => $HIAS->helpers->oDecrypt($device["authenticationMqttKey"]["value"]),
		"ipinfo" => $HIAS->helpers->oDecrypt($device["authenticationIpinfoKey"]["value"])
	],
	"ble" => [
		"address" => $address,
		"pin" => $pincode,
		"service" => $service,
		"characteristic" => $characteristic
	],
	"wifi" => [
		"ssid" => "",
		"key" => ""
	]
]));

?>