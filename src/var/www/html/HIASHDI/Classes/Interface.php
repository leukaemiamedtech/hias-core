<?php

	class HiasHdiInterface
	{

		function __construct($hias)
		{
			$this->hias = $hias;
		}

		public function get_hiashdi_root()
		{
			$request = $this->hias->hiashdi->request("GET", "", []);
			$response = json_decode($request["body"], true);
			return $response;
		}

		public function get_hiashdi_entity($attrs = Null)
		{
			if($attrs):
				$attrs="&attrs=" . $attrs;
			endif;

			$request = $this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "/" . $this->hias->hiashdi->confs["entity"] . "?type=HIASHDI" . $attrs, []);
			$response = json_decode($request["body"], true);
			return $response;
		}

		public function get_protocols()
		{
			$pdoQuery = $this->hias->conn->prepare("
				SELECT protocol
				FROM cbProtocols
				ORDER BY protocol ASC
			");
			$pdoQuery->execute();
			$categories=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $categories;
		}

		public function get_ai_models()
		{
			$pdoQuery = $this->hias->conn->prepare("
				SELECT model
				FROM cbAI
				ORDER BY model ASC
			");
			$pdoQuery->execute();
			$categories=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $categories;
		}

		public function get_location($id, $attrs = Null)
		{

			if($attrs):
				$attrs="&attrs=" . $attrs;
			endif;

			$response = $this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "/" . $id . "?type=Location" . $attrs, []);
			$location = json_decode($response["body"], true);
			return $location;
		}

		public function get_zone($id, $attrs = Null)
		{

			if($attrs):
				$attrs="&attrs=" . $attrs;
			endif;

			$response = $this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "/" . $id . "?type=Zone" . $attrs, []);
			$zone = json_decode($response["body"], true);
			return $zone;
		}

		public function get_hiashdi_life()
		{
			$request = $this->get_hiashdi_entity("batteryLevel,cpuUsage,memoryUsage,hddUsage,temperature,rssi,networkStatus");
			return $request;

			if(!isSet($request["Error"])):
				$response = [
					"battery" => $request["batteryLevel"]["value"],
					"cpu" => $request["cpuUsage"]["value"],
					"mem" => $request["memoryUsage"]["value"],
					"hdd" => $request["hddUsage"]["value"],
					"tempr" => $request["temperature"]["value"],
					"rssi" => $request["rssi"]["value"],
					"status" => $request["networkStatus"]["value"]
				];
				return  [
					'Response' => 'OK',
					'Data' => $response
				];
			else:
				return  [
					'Response'=>'FAILED',
					'Data' => $Device
				];
			endif;
		}

		public function update_hiashdi()
		{
			$pdoQuery = $this->hias->conn->prepare("
				UPDATE hiashdi
				SET entity = :entity,
					hiashdiv = :hiashdiv,
					url = :url,
					local_ip = :local_ip,
					data_url = :data_url
			");
			$pdoQuery->execute([
				":entity" => filter_input(INPUT_POST, "entity", FILTER_SANITIZE_STRING),
				":hiashdiv" => filter_input(INPUT_POST, "hiashdiv", FILTER_SANITIZE_STRING),
				":url" => filter_input(INPUT_POST, "url", FILTER_SANITIZE_STRING),
				":local_ip" => filter_input(INPUT_POST, "local_ip", FILTER_SANITIZE_STRING),
				":data_url" => filter_input(INPUT_POST, "data_url", FILTER_SANITIZE_STRING)
			]);
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			$this->hias->store_user_history("HISAHDI Core configuration update");

			return [
				"Response"=> "OK",
				"Message" => "HISAHDI Core configuration update OK!"
			];
		}

		public function update_hiashdi_entity()
		{
			$lid = filter_input(INPUT_POST, "lid", FILTER_SANITIZE_STRING);
			$location = $this->get_location($lid);

			$zid = filter_input(INPUT_POST, 'zid', FILTER_SANITIZE_STRING);
			$zone = $this->get_zone($zid);

			$ip = filter_input(INPUT_POST, 'ipAddress', FILTER_SANITIZE_STRING);
			$mac = filter_input(INPUT_POST, 'macAddress', FILTER_SANITIZE_STRING);
			$bt = filter_input(INPUT_POST, 'bluetooth', FILTER_SANITIZE_STRING);
			$coords = explode(",", filter_input(INPUT_POST, "coordinates", FILTER_SANITIZE_STRING));
			$protocols = $_POST["protocols"];

			$protocols = [];
			foreach($_POST["protocols"] AS $key => $value):
				$protocols[] = $value;
			endforeach;

			$data = [
				"name" => [
					"value" => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)
				],
				"description" => [
					"value" => filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)
				],
				"version" => [
					"value" => filter_input(INPUT_POST, "version", FILTER_SANITIZE_STRING)
				],
				"host" => [
					"value" => filter_input(INPUT_POST, "host", FILTER_SANITIZE_STRING)
				],
				"port" => [
					"value" => filter_input(INPUT_POST, "port", FILTER_SANITIZE_STRING)
				],
				"endpoint" => [
					"value" => filter_input(INPUT_POST, "endpoint", FILTER_SANITIZE_STRING)
				],
				"deviceBrandName" => [
					"value" => filter_input(INPUT_POST, "deviceBrandName", FILTER_SANITIZE_STRING)
				],
				"deviceModel" => [
					"value" => filter_input(INPUT_POST, "deviceModel", FILTER_SANITIZE_STRING)
				],
				"deviceManufacturer" => [
					"value" => filter_input(INPUT_POST, "deviceManufacturer", FILTER_SANITIZE_STRING)
				],
				"deviceSerialNumber" => [
					"value" => filter_input(INPUT_POST, "deviceSerialNumber", FILTER_SANITIZE_STRING)
				],
				"os" => [
					"value" => filter_input(INPUT_POST, "os", FILTER_SANITIZE_STRING)
				],
				"osVersion" => [
					"value" => filter_input(INPUT_POST, "osVersion", FILTER_SANITIZE_STRING)
				],
				"osManufacturer" => [
					"value" => filter_input(INPUT_POST, "osManufacturer", FILTER_SANITIZE_STRING)
				],
				"software" => [
					"value" => filter_input(INPUT_POST, "software", FILTER_SANITIZE_STRING)
				],
				"softwareVersion" => [
					"value" => filter_input(INPUT_POST, "softwareVersion", FILTER_SANITIZE_STRING)
				],
				"softwareManufacturer" => [
					"value" => filter_input(INPUT_POST, "softwareManufacturer", FILTER_SANITIZE_STRING)
				],
				"location" => [
					"type" => "geo:json",
					"value" => [
						"type" => "Point",
						"coordinates" => [floatval($coords[0]), floatval($coords[1])]
					]
				],
				"networkLocation" => [
					"value" => $location["id"],
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Location entity ID"
						]
					]
				],
				"networkZone" => [
					"value" => $zone["id"],
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Zone entity ID"
						]
					]
				],
				"ipAddress" => [
					"value" => $ip,
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "IP address of entity"
						]
					]
				],
				"macAddress" => [
					"value" => $mac,
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "MAC address of entity"
						]
					]
				],
				"bluetoothAddress" => [
					"value" => $bt,
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Bluetooth address of entity"
						]
					]
				],
				"protocols" => [
					"value" =>$protocols
				]
			];

			$request = $this->hias->hiascdi->request("PATCH", $this->hias->hiascdi->confs["entities_url"] . "/" . $this->hias->hiashdi->confs["entity"] . "/attrs?type=HIASHDI", json_encode($data));
			$response = json_decode($request["body"], true);

			if(!isSet($response["Error"])):

				$this->hias->store_user_history("HIASHDI Entity configuration update");

				return [
					"Response"=> "OK",
					"Message" => "HIASHDI Entity configuration update OK!",
					"Schema" => $this->get_hiashdi_entity("dateCreated,dateModified,*")
				];
			else:
				return [
					"Response"=> "KO",
					"Message" => $response
				];
			endif;
		}

		public function reset_network_key()
		{
			$broker = $this->get_hiashdi_entity();

			$privKey = $this->hias->helpers->generate_key(32);
			$privKeyHash = $this->hias->helpers->password_hash($privKey);

			$data = [
				"authenticationUser" => [
					"value" => $this->hias->helpers->oEncrypt($privKeyHash),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Public key for accessing the network APIs"
						],
						"timestamp" => [
							"value" => date('Y-m-d\TH:i:s.Z\Z', time())
						]
					]
				]
			];

			$response = $this->hias->hiascdi->request("PATCH", $this->hias->hiascdi->confs["entities_url"] . "/" . $broker["id"] . "/attrs?type=HIASHDI", json_encode($data));
			$response = json_decode($response["body"], true);

			if(!isSet($response["error"])):
				return [
					"Response"=> "OK",
					"Message" => "HIASHDI key reset!",
					"P" => $privKey
				];
			else:
				return [
					"Response"=> "FAILED",
					"Message" => "HIASHDI key reset failed!"
				];
			endif;

		}

		public function reset_mqtt_key()
		{
			$broker = $this->get_hiashdi_entity();

			$mqttPass = $this->hias->helpers->password();
			$mqttHash = create_hash($mqttPass);

			$data = [
				"authenticationMqttKey" => [
					"value" => $this->hias->helpers->oEncrypt($mqttPass),
					"type" => "Text",
					"metadata" => [
						"description" => [
							"value" => "Public key for accessing the network APIs"
						],
						"timestamp" => [
							"value" => date('Y-m-d\TH:i:s.Z\Z', time())
						]
					]
				]
			];

			$response = $this->hias->hiascdi->request("PATCH", $this->hias->hiascdi->confs["entities_url"] . "/" . $broker["id"] . "/attrs?type=HIASHDI", json_encode($data));
			$response = json_decode($response["body"], true);

			if(!isSet($response["error"])):
				$query = $this->hias->conn->prepare("
					UPDATE mqttu
					SET pw = :pw
					WHERE uname = :username
				");
				$query->execute(array(
					':pw' => $mqttHash,
					':username' => $this->hias->helpers->oDecrypt($broker["authenticationMqttUser"]["value"])
				));
				return [
					"Response"=> "OK",
					"Message" => "HIASHDI MQTT key reset!",
					"P" => $mqttPass
				];
			else:
				return [
					"Response"=> "FAILED",
					"Message" => "HIASHDI MQTT key reset failed!"
				];
			endif;

		}

		public function console_request()
		{
			$params = "";

			if(!filter_input(INPUT_POST, "method", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Request method is required!"
				];
			endif;
			if(!filter_input(INPUT_POST, "endpoint", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Request endpoint is required!"
				];
			endif;
			if(filter_input(INPUT_POST, "params", FILTER_SANITIZE_STRING)):
				$params = str_replace(' ', '%20', filter_input(INPUT_POST, "params", FILTER_SANITIZE_STRING));
			endif;
			if(!filter_input(INPUT_POST, "accept", FILTER_SANITIZE_STRING)):
				$accept = "application/json";
			else:
				$accept = filter_input(INPUT_POST, "accept", FILTER_SANITIZE_STRING);
			endif;

			if(filter_input(INPUT_POST, "entity", FILTER_SANITIZE_STRING)):
				$endpoint = filter_input(INPUT_POST, "endpoint", FILTER_SANITIZE_STRING) . "/" . filter_input(INPUT_POST, "entity", FILTER_SANITIZE_STRING);
			else:
				$endpoint = filter_input(INPUT_POST, "endpoint", FILTER_SANITIZE_STRING);
			endif;

			$request = $this->hias->hiashdi->request(filter_input(INPUT_POST, "method", FILTER_SANITIZE_STRING), $endpoint  . $params, $_POST["body"], "", "", "",  [$accept]);
			$response = json_decode($request["body"], true);

			if(!isSet($response["Error"])):
				return [
					"Response" => "OK",
					"Message" => "hiashdi Console Request OK!",
					"Code" => $request["code"],
					"Headers" => $request["header"],
					"Body" => $response
				];
			else:
				return [
					"Response" => "KO",
					"Message" => "hiashdi Console Request KO!",
					"Headers" => $request["header"],
					"Code" => $request["code"]
				];
			endif;
		}
	}

	$HiasHdiInterface = new HiasHdiInterface($HIAS);

	if(filter_input(INPUT_POST, "update_hiashdi", FILTER_VALIDATE_BOOLEAN)):
		die(json_encode($HiasHdiInterface->update_hiashdi_entity()));
	endif;
	if(filter_input(INPUT_POST, "update_hbroker", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($HiasHdiInterface->update_hiashdi()));
	endif;
	if(filter_input(INPUT_POST, "send_hiashdi_console", FILTER_VALIDATE_BOOLEAN) == True):
		die(json_encode($HiasHdiInterface->console_request()));
	endif;
	if(filter_input(INPUT_POST, "get_hiashdi_life", FILTER_VALIDATE_BOOLEAN) == True):
		die(json_encode($HiasHdiInterface->get_hiashdi_life()));
	endif;
	if(filter_input(INPUT_POST, "reset_hiashdi_key", FILTER_VALIDATE_BOOLEAN) == True):
		die(json_encode($HiasHdiInterface->reset_network_key()));
	endif;
	if(filter_input(INPUT_POST, "reset_hiashdi_mqtt", FILTER_VALIDATE_BOOLEAN) == True):
		die(json_encode($HiasHdiInterface->reset_mqtt_key()));
	endif;