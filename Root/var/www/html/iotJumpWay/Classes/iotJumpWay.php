<?php
include dirname(__FILE__) . '/../../iotJumpWay/Classes/pbkdf2.php';
require __DIR__ . '/../../../vendor/autoload.php';

use Web3\Web3;
use Web3\Contract;
use Web3\Utils;

	class iotJumpWay
	{

		function __construct($_GeniSys)
		{
			$this->_GeniSys = $_GeniSys;

			if(isSet($_SESSION["GeniSysAI"]["Active"])):
				$this->bcc = $this->getBlockchainConf();
				$this->web3 = $this->blockchainConnection();
				$this->contract = new Contract($this->web3->provider, $this->bcc["abi"]);
				$this->icontract = new Contract($this->web3->provider, $this->bcc["iabi"]);
				$this->pcontract = new Contract($this->web3->provider, $this->bcc["pabi"]);
				$this->checkBlockchainPermissions();
			endif;
			$this->cb = $this->getContextBrokerConf();
		}

		private function getContextBrokerConf()
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT *
				FROM contextbroker
			");
			$pdoQuery->execute();
			$response=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $response;
		}

		private function createContextHeaders()
		{
			$basicAuth = $_SESSION["GeniSysAI"]["User"] . ":" . $this->_GeniSys->_helpers->oDecrypt($_SESSION["GeniSysAI"]["Pass"]);
			$basicAuth = base64_encode($basicAuth);

			return [
				"Content-Type: application/json",
				'Authorization: Basic '. $basicAuth
			];
		}

		private function contextBrokerRequest($method, $endpoint, $headers, $json)
		{
			$path = $this->_GeniSys->_helpers->oDecrypt($this->_GeniSys->_confs["domainString"]) . "/" . $this->cb["url"] . "/" . $endpoint;

			if($method == "GET"):
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_HEADER, 1);
				curl_setopt($ch, CURLOPT_URL, $path);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$response = curl_exec($ch);
				$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
				$header = substr($response, 0, $header_size);
				$body = substr($response, $header_size);
				curl_close($ch);
			elseif($method == "POST"):
				$ch = curl_init($path);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_HEADER, 1);
				curl_setopt($ch, CURLOPT_TIMEOUT, 30);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
				$response = curl_exec($ch);
				$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
				$header = substr($response, 0, $header_size);
				$body = substr($response, $header_size);
				curl_close($ch);
			elseif($method == "PATCH"):
				$ch = curl_init($path);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_HEADER, 1);
				curl_setopt($ch, CURLOPT_TIMEOUT, 30);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
				$response = curl_exec($ch);
				$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
				$header = substr($response, 0, $header_size);
				$body = substr($response, $header_size);
				curl_close($ch);
			endif;

			return $body;
		}

		public function getContextBrokerProtocols()
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
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

		public function getContextBrokerAiModels()
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
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

		private function getBlockchainConf()
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT blockchain.*,
					contracts.contract,
					contracts.abi,
					icontracts.contract as icontract,
					icontracts.abi as iabi,
					pcontracts.contract as pcontract,
					pcontracts.abi as pabi
				FROM blockchain blockchain
				INNER JOIN contracts contracts
				ON contracts.id = blockchain.dc
				INNER JOIN contracts icontracts
				ON icontracts.id = blockchain.ic
				INNER JOIN contracts pcontracts
				ON pcontracts.id = blockchain.pc
			");
			$pdoQuery->execute();
			$response=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $response;
		}

		private function blockchainConnection()
		{
			$web3 = new Web3($this->_GeniSys->_helpers->oDecrypt($this->_GeniSys->_confs["domainString"]) . "/Blockchain/API/", 30, $_SESSION["GeniSysAI"]["User"], $this->_GeniSys->_helpers->oDecrypt($_SESSION["GeniSysAI"]["Pass"]));

			return $web3;
		}

		private function checkBlockchainPermissions()
		{
			$allowed = "";
			$this->contract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["contract"]))->call("identifierAllowed", "User", $_SESSION["GeniSysAI"]["Identifier"], ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$allowed) {
				if ($err !== null) {
					$allowed = "FAILED";
					return;
				}
				$allowed = $resp[0];
			});

			if($allowed != "true"):
				header('Location: /Logout');
			endif;
		}

		private function unlockBlockchainAccount()
		{
			$response = "";
			$personal = $this->web3->personal;
			$personal->unlockAccount($_SESSION["GeniSysAI"]["BC"]["BCUser"], $this->_GeniSys->_helpers->oDecrypt($_SESSION["GeniSysAI"]["BC"]["BCPass"]), function ($err, $unlocked) use (&$response) {
				if ($err !== null) {
					$response = "FAILED! " . $err;
					return;
				}
				if ($unlocked) {
					$response = "OK";
				} else {
					$response = "FAILED";
				}
			});
			return $response;
		}

		private function createBlockchainUser($pass)
		{
			$newAccount = "";
			$personal = $this->web3->personal;
			$personal->newAccount($pass, function ($err, $account) use (&$newAccount) {
				if ($err !== null) {
					$newAccount = "FAILED!";
					return;
				}
				$newAccount = $account;
			});
			return $newAccount;
		}

		private function getBlockchainBalance()
		{
			$nbalance = "";
			$this->web3->eth->getBalance($_SESSION["GeniSysAI"]["BC"]["BCUser"], function ($err, $balance) use (&$nbalance) {
				if ($err !== null) {
					$response = "FAILED! " . $err;
					return;
				}
				$nbalance = $balance->toString();
			});
			return Utils::fromWei($nbalance, 'ether')[0];
		}

		private function storeBlockchainTransaction($action, $hash, $device = 0, $application = 0)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  transactions (
					`uid`,
					`did`,
					`aid`,
					`action`,
					`hash`,
					`time`
				)  VALUES (
					:uid,
					:did,
					:aid,
					:action,
					:hash,
					:time
				)
			");
			$pdoQuery->execute([
				":uid" => $_SESSION["GeniSysAI"]["Uid"],
				":did" => $device,
				":aid" => $application,
				":action" => $action,
				':hash' => $this->_GeniSys->_helpers->oEncrypt($hash),
				":time" => time()
			]);
			$txid = $this->_GeniSys->_secCon->lastInsertId();
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $txid;
		}

		private function storeUserHistory($action, $hash, $location = 0, $zone = 0, $device = 0, $sensor = 0, $application = 0)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  history (
					`uid`,
					`tlid`,
					`tzid`,
					`tdid`,
					`tsid`,
					`taid`,
					`action`,
					`hash`,
					`time`
				)  VALUES (
					:uid,
					:tlid,
					:tzid,
					:tdid,
					:tsid,
					:taid,
					:action,
					:hash,
					:time
				)
			");
			$pdoQuery->execute([
				":uid" => $_SESSION["GeniSysAI"]["Uid"],
				":tlid" => $location,
				":tzid" => $zone,
				":tdid" => $device,
				":tsid" => $sensor,
				":taid" => $application,
				":action" => $action,
				":hash" => $hash,
				":time" => time()
			]);
			$txid = $this->_GeniSys->_secCon->lastInsertId();
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $txid;
		}

		private function addAmqpUser($username, $key)
		{
			$query = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  amqpu  (
					`username`,
					`pw`
				)  VALUES (
					:username,
					:pw
				)
			");
			$query->execute([
				':username' => $username,
				':pw' => $this->_GeniSys->_helpers->oEncrypt($key)
			]);
			$amid = $this->_GeniSys->_secCon->lastInsertId();
			return $amid;
		}

		private function addAmqpUserPerm($uid, $permission)
		{
			$query = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  amqpp  (
					`uid`,
					`permission`
				)  VALUES (
					:uid,
					:permission
				)
			");
			$query->execute([
				':uid' => $uid,
				':permission' => $permission
			]);
		}

		private function addAmqpUserVh($uid, $vhost)
		{
			$query = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  amqpvh  (
					`uid`,
					`vhost`
				)  VALUES (
					:uid,
					:vhost
				)
			");
			$query->execute([
				':uid' => $uid,
				':vhost' => $vhost
			]);
		}

		private function addAmqpVhPerm($uid, $vhost, $rtype, $rname, $permission)
		{
			$query = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  amqpvhr  (
					`uid`,
					`vhost`,
					`rtype`,
					`rname`,
					`permission`
				)  VALUES (
					:uid,
					:vhost,
					:rtype,
					:rname,
					:permission
				)
			");
			$query->execute([
				':uid' => $uid,
				':vhost' => $vhost,
				':rtype' => $rtype,
				':rname' => $rname,
				':permission' => $permission
			]);
		}

		private function addAmqpVhTopic($uid, $vhost, $rtype, $rname, $permission, $rkey)
		{
			$query = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  amqpvhrt  (
					`uid`,
					`vhost`,
					`rtype`,
					`rname`,
					`permission`,
					`rkey`
				)  VALUES (
					:uid,
					:vhost,
					:rtype,
					:rname,
					:permission,
					:rkey
				)
			");
			$query->execute([
				':uid' => $uid,
				':vhost' => $vhost,
				':rtype' => $rtype,
				':rname' => $rname,
				':permission' => $permission,
				':rkey' => $rkey
			]);
		}

		private function checkLocation($lid)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT id
				FROM mqttl
				WHERE id = :id
			");
			$pdoQuery->execute([
				":id" => $lid
			]);
			$location=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			if($location["id"]):
				return True;
			else:
				return False;
			endif;
		}

		private function checkZone($zid)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT id
				FROM mqttlz
				WHERE id = :id
			");
			$pdoQuery->execute([
				":id" => $zid
			]);
			$location=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			if($location["id"]):
				return True;
			else:
				return False;
			endif;
		}

		public function getAgents($limit = 0)
		{
			$limiter = "";
			if($limit != 0):
				$limiter = "?limit=" . $limit;
			endif;

			$agents = json_decode($this->contextBrokerRequest("GET", $this->cb["agents_url"] . $limiter, $this->createContextHeaders(), []), true);
			return $agents;
		}

		public function getLocations($limit = 0, $order = "id DESC")
		{
			$limiter = "";
			if($limit != 0):
				$limiter = "&limit=" . $limit;
			endif;

			$locations = json_decode($this->contextBrokerRequest("GET", $this->cb["entities_url"] . "?type=Location".$limiter, $this->createContextHeaders(), []), true);
			return $locations;
		}

		public function getLocation($id, $attrs = Null)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT *
				FROM mqttl
				WHERE id = :id
			");
			$pdoQuery->execute([
				":id" => $id
			]);
			$location=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			if($attrs):
				$attrs="&attrs=" . $attrs;
			endif;

			$location["context"] = json_decode($this->contextBrokerRequest("GET", $this->cb["entities_url"] . "/" . $location["pub"] . "?type=Location" . $attrs, $this->createContextHeaders(), []), true);
			return $location;
		}

		public function getModel($id, $attrs = Null)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT *
				FROM models
				WHERE id = :id
				ORDER BY id DESC
			");
			$pdoQuery->execute([
				":id" => $id
			]);
			$model=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			if($attrs):
				$attrs="&attrs=" . $attrs;
			endif;

			$device["context"] = json_decode($this->contextBrokerRequest("GET", $this->cb["entities_url"] . "/" . $model["pub"] . "?type=Model" . $attrs, $this->createContextHeaders(), []), true);
			return $device;
		}

		public function update()
		{
			if(!filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Name is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Location description is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "streetAddress", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Location street address is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "addressLocality", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Location address locality is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "postalCode", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Location postal code is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "coordinates", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Location coordinates is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "zones", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "Location zones is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "devices", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "Location zones is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "applications", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "Location applications is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "users", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "Location users is required"
				];
			endif;

			$location = $this->getLocation(1);

			$coords = explode(",", filter_input(INPUT_POST, "coordinates", FILTER_SANITIZE_STRING));

			$data = [
				"category" => [
					"value" => ["Office"]
				],
				"name" => [
					"value" => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)
				],
				"description" => [
					"value" => filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)
				],
				"floorsAboveGround" => [
					"value" => filter_input(INPUT_POST, "floorsAboveGround", FILTER_SANITIZE_NUMBER_INT)
				],
				"floorsBelowGround" => [
					"value" => filter_input(INPUT_POST, "floorsBelowGround", FILTER_SANITIZE_NUMBER_INT)
				],
				"zones" => [
					"value" => filter_input(INPUT_POST, "zones", FILTER_SANITIZE_NUMBER_INT)
				],
				"devices" => [
					"value" => filter_input(INPUT_POST, "devices", FILTER_SANITIZE_NUMBER_INT)
				],
				"applications" => [
					"value" => filter_input(INPUT_POST, "applications", FILTER_SANITIZE_NUMBER_INT)
				],
				"users" => [
					"value" => filter_input(INPUT_POST, "users", FILTER_SANITIZE_NUMBER_INT)
				],
				"patients" => [
					"value" => filter_input(INPUT_POST, "patients", FILTER_SANITIZE_NUMBER_INT)
				],
				"location" => [
					"type" => "geo:json",
					"value" => [
						"type" => "Point",
						"coordinates" => [floatval($coords[0]), floatval($coords[1])]
					]
				],
				"address" => [
					"type" => "PostalAddress",
					"value" => [
						"addressLocality" => filter_input(INPUT_POST, "addressLocality", FILTER_SANITIZE_STRING),
						"postalCode" => filter_input(INPUT_POST, "postalCode", FILTER_SANITIZE_STRING),
						"streetAddress" => filter_input(INPUT_POST, "streetAddress", FILTER_SANITIZE_STRING)
					]
				],
				"openingHours" => [
					"value" => filter_input(INPUT_POST, "openingHours", FILTER_SANITIZE_STRING)
				],
				"dateModified" => [
					"type" => "DateTime",
					"value" => date('Y-m-d\TH:i:s.Z\Z', time())
				]
			];

			$response = json_decode($this->contextBrokerRequest("PATCH", $this->cb["entities_url"] . "/" . $location["context"]["Data"]["id"] . "/attrs?type=Location", $this->createContextHeaders(), json_encode($data)), true);

			if($response["Response"] == "OK"):
				$this->storeUserHistory("Update Location", 0, $location["context"]["Data"]["lid"]["value"], 0, 0);
				return [
					"Response"=> "OK",
					"Message" => "Location updated!"
				];
			else:
				return [
					"Response"=> "FAILED",
					"Message" => "Location update failed!"
				];
			endif;
		}

		public function getZones($limit = 0, $order = "id DESC")
		{
			$limiter = "";
			if($limit != 0):
				$limiter = "&limit=" . $limit;
			endif;

			$zones = json_decode($this->contextBrokerRequest("GET", $this->cb["entities_url"] . "?type=Zone".$limiter, $this->createContextHeaders(), []), true);
			return $zones;
		}

		public function getZone($id, $attrs = Null)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT *
				FROM mqttlz
				WHERE id = :id
				ORDER BY id DESC
			");
			$pdoQuery->execute([
				":id" => $id
			]);
			$zone=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			if($attrs):
				$attrs="&attrs=" . $attrs;
			endif;

			$zone["context"] = json_decode($this->contextBrokerRequest("GET", $this->cb["entities_url"] . "/" . $zone["pub"] . "?type=Zone" . $attrs, $this->createContextHeaders(), []), true);
			return $zone;
		}

		public function getZoneCategories()
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT category
				FROM cbZoneCats
				ORDER BY category ASC
			");
			$pdoQuery->execute();
			$categories=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $categories;
		}

		public function createZone()
		{
			if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "Location ID is required"
				];
			endif;

			if(!$this->checkLocation(filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT))):
				return [
					"Response"=> "Failed",
					"Message" => "iotJumpWay location does not exist"
				];
			endif;

			if(!filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Name is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Description is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "coordinates", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Coordinates are required"
				];
			endif;

			if(!filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Category is required"
				];
			endif;

			$lid = filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT);
			$location = $this->getLocation($lid);

			$zone = $this->_GeniSys->_helpers->generate_uuid();
			$coords = explode(",", filter_input(INPUT_POST, "coordinates", FILTER_SANITIZE_STRING));

			$query = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  mqttlz  (
					`id`
				)  VALUES (
					:id
				)
			");
			$query->execute([
				':id' => 0
			]);
			$zid = $this->_GeniSys->_secCon->lastInsertId();

			$data = [
				"id" => $zone,
				"type" => "Zone",
				"category" => [
					"value" => [filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)]
				],
				"name" => [
					"value" => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)
				],
				"description" => [
					"value" => filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)
				],
				"devices" => [
					"value" => filter_input(INPUT_POST, "devices", FILTER_SANITIZE_NUMBER_INT)
				],
				"lid" => [
					"value" => $lid,
					"entity" => $location["context"]["Data"]["id"]
				],
				"zid" => [
					"value" => $zid,
					"entity" => $zone
				],
				"devices" => [
					"value" => 0
				],
				"location" => [
					"type" => "geo:json",
					"value" => [
						"type" => "Point",
						"coordinates" => [floatval($coords[0]), floatval($coords[1])]
					]
				],
				"dateCreated" => [
					"type" => "DateTime",
					"value" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"dateModified" => [
					"type" => "DateTime",
					"value" => date('Y-m-d\TH:i:s.Z\Z', time())
				]
			];

			$response = json_decode($this->contextBrokerRequest("POST", $this->cb["entities_url"] . "?type=Zone", $this->createContextHeaders(), json_encode($data)), true);

			$this->storeUserHistory("Created Zone", $lid, $zid, 0, 0, 0);

			$query = $this->_GeniSys->_secCon->prepare("
				UPDATE mqttlz
				SET pub = :pub
				WHERE id = :id
			");
			$query->execute([
				':pub' => $response["Entity"]["id"],
				':id' => $zid
			]);

			return [
				"Response"=> "OK",
				"Message" => "Zone created!",
				"LID" => $lid,
				"ZID" => $zid
			];
		}

		public function updateZone()
		{

			if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "Location ID is required"
				];
			endif;

			if(!$this->checkLocation(filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT))):
				return [
					"Response"=> "Failed",
					"Message" => "iotJumpWay location does not exist"
				];
			endif;

			if(!filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Name is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Description is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "coordinates", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Coordinates are required"
				];
			endif;

			if(!filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Category is required"
				];
			endif;

			$ZId = filter_input(INPUT_GET, 'zone', FILTER_SANITIZE_NUMBER_INT);
			$Zone = $this->getZone($ZId);

			$coords = explode(",", filter_input(INPUT_POST, "coordinates", FILTER_SANITIZE_STRING));

			$data = [
				"category" => [
					"value" => [filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)]
				],
				"name" => [
					"value" => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)
				],
				"description" => [
					"value" => filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)
				],
				"devices" => [
					"value" => filter_input(INPUT_POST, "devices", FILTER_SANITIZE_NUMBER_INT)
				],
				"lid" => [
					"value" => filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT),
					"entity" => filter_input(INPUT_POST, "lentity", FILTER_SANITIZE_STRING)
				],
				"zid" => [
					"value" => $ZId,
					"entity" => $Zone["context"]["Data"]["zid"]["entity"]
				],
				"location" => [
					"type" => "geo:json",
					"value" => [
						"type" => "Point",
						"coordinates" => [floatval($coords[0]), floatval($coords[1])]
					]
				],
				"dateModified" => [
					"type" => "DateTime",
					"value" => date('Y-m-d\TH:i:s.Z\Z', time())
				]
			];

			$response = json_decode($this->contextBrokerRequest("PATCH", $this->cb["entities_url"] . "/" . $Zone["context"]["Data"]["zid"]["entity"] . "/attrs?type=Zone", $this->createContextHeaders(), json_encode($data)), true);

			$this->storeUserHistory("Updated Zone", filter_input(INPUT_POST, "lid", FILTER_SANITIZE_STRING), $ZId, 0, 0, 0);

			return [
				"Response"=> "OK",
				"Message" => "Zone updated!"
			];
		}

		public function getDevices($limit = 0, $order = "id DESC")
		{
			$limiter = "";
			if($limit != 0):
				$limiter = "&limit=" . $limit;
			endif;

			$devices = json_decode($this->contextBrokerRequest("GET", $this->cb["entities_url"] . "?type=Device".$limiter, $this->createContextHeaders(), []), true);
			return $devices;
		}

		public function getDevice($id, $attrs = Null)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT *
				FROM mqttld
				WHERE id = :id
				ORDER BY id DESC
			");
			$pdoQuery->execute([
				":id" => $id
			]);
			$device=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			if($attrs):
				$attrs="&attrs=" . $attrs;
			endif;

			$device["context"] = json_decode($this->contextBrokerRequest("GET", $this->cb["entities_url"] . "/" . $device["apub"] . "?type=Device" . $attrs, $this->createContextHeaders(), []), true);
			return $device;
		}

		public function getDeviceCategories()
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT category
				FROM cbDeviceCats
				ORDER BY category ASC
			");
			$pdoQuery->execute();
			$categories=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $categories;
		}

		public function createDevice()
		{
			if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "Location ID is required"
				];
			endif;

			if(!$this->checkLocation(filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT))):
				return [
					"Response"=> "Failed",
					"Message" => "iotJumpWay location does not exist"
				];
			endif;

			if(!filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "Zone ID is required"
				];
			endif;

			if(!$this->checkZone(filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT))):
				return [
					"Response"=> "Failed",
					"Message" => "iotJumpWay location does not exist"
				];
			endif;

			if(!filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Category is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Name is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Name is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "coordinates", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Coordinates entity is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "deviceName", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Hardware device name is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "deviceManufacturer", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Hardware device manufacturer is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "deviceModel", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Hardware device model is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "osName", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Operating system name is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "osManufacturer", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Operating system manufacturer is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "osVersion", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Operating system version is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "osVersion", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Operating system version is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "agent", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "IoT Agent is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "IP is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "MAC is required"
				];
			endif;

			if(!isSet($_POST["protocols"])):
				return [
					"Response"=> "Failed",
					"Message" => "At least one M2M protocol is required"
				];
			endif;

			$unlocked =  $this->unlockBlockchainAccount();

			if($unlocked == "FAILED"):
				return [
					"Response"=> "Failed",
					"Message" => "Unlocking HIAS Blockhain Account Failed!"
				];
			endif;

			$mqttUser = $this->_GeniSys->_helpers->generate_uuid();
			$mqttPass = $this->_GeniSys->_helpers->password();
			$mqttHash = create_hash($mqttPass);

			$pubKey = $this->_GeniSys->_helpers->generate_uuid();
			$privKey = $this->_GeniSys->_helpers->generateKey(32);
			$privKeyHash = $this->_GeniSys->_helpers->createPasswordHash($privKey);

			$amqppubKey = $this->_GeniSys->_helpers->generate_uuid();
			$amqpprvKey = $this->_GeniSys->_helpers->generateKey(32);
			$amqpKeyHash = $this->_GeniSys->_helpers->createPasswordHash($amqpprvKey);

			$bcPass = $this->_GeniSys->_helpers->password();

			$lid = filter_input(INPUT_POST, 'lid', FILTER_SANITIZE_NUMBER_INT);
			$location = $this->getLocation($lid);

			$zid = filter_input(INPUT_POST, 'zid', FILTER_SANITIZE_NUMBER_INT);
			$zone = $this->getZone($zid);

			$ip = filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING);
			$mac = filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING);
			$bluetooth = filter_input(INPUT_POST, "bluetooth", FILTER_SANITIZE_STRING);
			$name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
			$coords = explode(",", filter_input(INPUT_POST, "coordinates", FILTER_SANITIZE_STRING));

			$protocols = [];
			foreach($_POST["protocols"] AS $key => $value):
				$protocols[] = $value;
			endforeach;

			$models = [];
			if(isSet($_POST["ai"])):
				foreach($_POST["ai"] AS $key => $value):
					$model = $this->getModel($value)["context"]["Data"];
					$mname = $model["name"]["value"];
					unset($model["id"]);
					unset($model["type"]);
					unset($model["mid"]);
					unset($model["name"]);
					unset($model["description"]);
					unset($model["network"]);
					unset($model["language"]);
					unset($model["framework"]);
					unset($model["toolkit"]);
					unset($model["dateCreated"]);
					unset($model["dateModified"]);
					$models[$mname] = $model;
				endforeach;
			endif;

			$sensors = [];
			if(isSet($_POST["sensors"])):
				foreach($_POST["sensors"] AS $key => $value):
					$sensor = $this->getThing($value)["context"]["Data"];
					unset($sensor["id"]);
					unset($sensor["type"]);
					unset($sensor["category"]);
					unset($sensor["description"]);
					unset($sensor["thing"]);
					unset($sensor["properties"]["image"]);
					unset($sensor["dateCreated"]);
					unset($sensor["dateModified"]);
					$sensors[] = $sensor;
				endforeach;
			endif;

			$actuators = [];
			if(isSet($_POST["actuators"])):
				foreach($_POST["actuators"] AS $key => $value):
					$actuator = $this->getThing($value)["context"]["Data"];
					unset($actuator["id"]);
					unset($actuator["type"]);
					unset($actuator["category"]);
					unset($actuator["description"]);
					unset($actuator["thing"]);
					unset($actuator["properties"]["image"]);
					unset($actuator["dateCreated"]);
					unset($actuator["dateModeified"]);
					$actuators[] = $actuator;
				endforeach;
			endif;

			$newBcUser = $this->createBlockchainUser($bcPass);

			if($newBcUser == "FAILED"):
				return [
					"Response"=> "Failed",
					"Message" => "Creating New HIAS Blockhain Account Failed!"
				];
			endif;

			$query = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  mqttld  (
					`id`
				)  VALUES (
					:id
				)
			");
			$query->execute([
				':id' => 0
			]);
			$did = $this->_GeniSys->_secCon->lastInsertId();

			$data = [
				"id" => $pubKey,
				"type" => "Device",
				"category" => [
					"value" => [filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)]
				],
				"name" => [
					"value" => $name
				],
				"description" => [
					"value" => filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)
				],
				"lid" => [
					"value" => $lid,
					"entity" => $location["context"]["Data"]["id"]
				],
				"zid" => [
					"value" => $zid,
					"entity" => $zone["context"]["Data"]["id"]
				],
				"did" => [
					"value" => $did
				],
				"location" => [
					"type" => "geo:json",
					"value" => [
						"type" => "Point",
						"coordinates" => [floatval($coords[0]), floatval($coords[1])]
					]
				],
				"agent" => [
					"url" => filter_input(INPUT_POST, "agent", FILTER_SANITIZE_STRING)
				],
				"device" => [
					"name" => filter_input(INPUT_POST, "deviceName", FILTER_SANITIZE_STRING),
					"manufacturer" => filter_input(INPUT_POST, "deviceManufacturer", FILTER_SANITIZE_STRING),
					"model" => filter_input(INPUT_POST, "deviceModel", FILTER_SANITIZE_STRING),
					"version" => filter_input(INPUT_POST, "deviceVersion", FILTER_SANITIZE_STRING)
				],
				"os" => [
					"name" => filter_input(INPUT_POST, "osName", FILTER_SANITIZE_STRING),
					"manufacturer" => filter_input(INPUT_POST, "osManufacturer", FILTER_SANITIZE_STRING),
					"version" => filter_input(INPUT_POST, "osVersion", FILTER_SANITIZE_STRING)
				],
				"protocols" => $protocols,
				"status" => [
					"value" => "OFFLINE",
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"keys" => [
					"public" => $pubKey,
					"private" => $this->_GeniSys->_helpers->oEncrypt($privKeyHash),
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"blockchain" => [
					"address" => $newBcUser,
					"password" => $this->_GeniSys->_helpers->oEncrypt($bcPass)
				],
				"mqtt" => [
					"username" => $this->_GeniSys->_helpers->oEncrypt($mqttUser),
					"password" => $this->_GeniSys->_helpers->oEncrypt($mqttPass),
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"coap" => [
					"username" => "",
					"password" => ""
				],
				"amqp" => [
					"username" => $this->_GeniSys->_helpers->oEncrypt($amqppubKey),
					"password" => $this->_GeniSys->_helpers->oEncrypt($amqpprvKey),
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"batteryLevel" => [
					"value" => 0.00
				],
				"cpuUsage" => [
					"value" => 0.00
				],
				"memoryUsage" => [
					"value" => 0.00
				],
				"hddUsage" => [
					"value" => 0.00
				],
				"temperature" => [
					"value" => 0.00
				],
				"ip" => [
					"value" => $ip,
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"mac" => [
					"value" => $this->_GeniSys->_helpers->oEncrypt($mac),
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"bluetooth" => [
					"address" => $bluetooth ? $this->_GeniSys->_helpers->oEncrypt($bluetooth) : "",
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"ai" => $models,
				"sensors" => $sensors,
				"actuators" => $actuators,
				"dateCreated" => [
					"type" => "DateTime",
					"value" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"dateFirstUsed" => [
					"type" => "DateTime",
					"value" => ""
				],
				"dateModified" => [
					"type" => "DateTime",
					"value" => date('Y-m-d\TH:i:s.Z\Z', time())
				]
			];

			$response = json_decode($this->contextBrokerRequest("POST", $this->cb["entities_url"] . "?type=Device", $this->createContextHeaders(), json_encode($data)), true);

			if($response["Response"]=="OK"):

				$query = $this->_GeniSys->_secCon->prepare("
					UPDATE mqttld
					SET apub = :apub
					WHERE id = :id
				");
				$query->execute(array(
					':apub'=> $response["Entity"]["id"],
					':id'=> $did
				));

				$query = $this->_GeniSys->_secCon->prepare("
					INSERT INTO  mqttu  (
						`lid`,
						`zid`,
						`did`,
						`uname`,
						`pw`
					)  VALUES (
						:lid,
						:zid,
						:did,
						:uname,
						:pw
					)
				");
				$query->execute([
					':lid' => $lid,
					':zid' => $zid,
					':did' => $did,
					':uname' => $mqttUser,
					':pw' => $mqttHash
				]);

				$query = $this->_GeniSys->_secCon->prepare("
					INSERT INTO  mqttua  (
						`lid`,
						`zid`,
						`did`,
						`username`,
						`topic`,
						`rw`
					)  VALUES (
						:lid,
						:zid,
						:did,
						:username,
						:topic,
						:rw
					)
				");
				$query->execute(array(
					':lid' => $lid,
					':zid' => $zid,
					':did' => $did,
					':username' => $mqttUser,
					':topic' => $location["context"]["Data"]["id"] . "/Devices/" . $zone["context"]["Data"]["id"] . "/" . $pubKey . "/#",
					':rw' => 4
				));

				$amid = $this->addAmqpUser($amqppubKey, $amqpKeyHash);
				$this->addAmqpUserVh($amid, "iotJumpWay");
				$this->addAmqpVhPerm($amid, "iotJumpWay", "exchange", "Core", "read");
				$this->addAmqpVhPerm($amid, "iotJumpWay", "exchange", "Core", "write");
				$this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Life", "read");
				$this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Life", "write");
				$this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Statuses", "read");
				$this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Statuses", "write");
				$this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "read", "Life");
				$this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "write", "Life");
				$this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "read", "Statuses");
				$this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "write", "Statuses");

				$hash = "";
				$msg = "";
				$actionMsg = "";
				$balanceMessage = "";
				$this->contract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["contract"]))->send("registerDevice", $pubKey, $newBcUser, $lid, $zid, $did, $name, $_SESSION["GeniSysAI"]["Uid"], time(), ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
					if ($err !== null) {
						$hash = "FAILED";
						$msg = $err;
						return;
					}
					$hash = $resp;
				});

				if($hash == "FAILED"):
					$actionMsg = " HIAS Blockchain registerDevice failed!\n" . $msg;
				else:
					$txid = $this->storeBlockchainTransaction("Register Device", $hash, $did);
					$this->storeUserHistory("Register Device", $txid, $lid, $zid, $did);
					$balance = $this->getBlockchainBalance();
					$actionMsg = " HIAS Blockchain registerDevice OK!\n";
					$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!";
				endif;

				$this->icontract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["icontract"]))->send("registerAuthorized", $newBcUser, ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
					if ($err !== null) {
						$hash = "FAILED";
						$msg = $err;
						return;
					}
					$hash = $resp;
				});

				if($hash == "FAILED"):
					$actionMsg .= " HIAS Blockchain registerAuthorized failed! " . $msg;
				else:
					$txid = $this->storeBlockchainTransaction("iotJumpWay Register Authorized", $hash, $did);
					$this->storeUserHistory("Register Authorized", $txid, $lid, $zid, $did);
					$balance = $this->getBlockchainBalance();
					$actionMsg .= " HIAS Blockchain registerAuthorized OK!\n";
					$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!";
				endif;

				return [
					"Response"=> "OK",
					"Message" => "Device created!" . $actionMsg . $balanceMessage,
					"LID" => $lid,
					"ZID" => $zid,
					"DID" => $did,
					"MU" => $mqttUser,
					"MP" => $mqttPass,
					"BU" => $newBcUser,
					"BP" => $bcPass,
					"AppID" => $pubKey,
					"AppKey" => $privKey
				];
			else:
				return [
					"Response"=> "FAILED",
					"Message" => "Device creating failed"
				];
			endif;
		}

		public function updateDevice()
		{
			if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "Location ID is required"
				];
			endif;

			if(!$this->checkLocation(filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT))):
				return [
					"Response"=> "Failed",
					"Message" => "iotJumpWay location does not exist"
				];
			endif;

			if(!filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "Zone ID is required"
				];
			endif;

			if(!$this->checkZone(filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT))):
				return [
					"Response"=> "Failed",
					"Message" => "iotJumpWay location does not exist"
				];
			endif;

			if(!filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Category is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Name is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Name is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "coordinates", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Coordinates entity is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "deviceName", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Hardware device name is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "deviceManufacturer", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Hardware device manufacturer is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "deviceModel", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Hardware device model is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "osName", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Operating system name is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "osManufacturer", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Operating system manufacturer is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "osVersion", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Operating system version is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "osVersion", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Operating system version is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "agent", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "IoT Agent is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "IP is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "MAC is required"
				];
			endif;

			if(!isSet($_POST["protocols"])):
				return [
					"Response"=> "Failed",
					"Message" => "At least one M2M protocol is required"
				];
			endif;

			$unlocked =  $this->unlockBlockchainAccount();

			if($unlocked == "FAILED"):
				return [
					"Response"=> "Failed",
					"Message" => "Unlocking HIAS Blockhain Account Failed!"
				];
			endif;

			$lid = filter_input(INPUT_POST, 'lid', FILTER_SANITIZE_NUMBER_INT);
			$location = $this->getLocation($lid);

			$zid = filter_input(INPUT_POST, 'zid', FILTER_SANITIZE_NUMBER_INT);
			$zone = $this->getZone($zid);

			$did = filter_input(INPUT_GET, "device", FILTER_SANITIZE_NUMBER_INT);
			$device = $this->getDevice($did);

			$ip = filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING);
			$mac = filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING);
			$bluetooth = filter_input(INPUT_POST, "bluetooth", FILTER_SANITIZE_STRING);
			$name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
			$coords = explode(",", filter_input(INPUT_POST, "coordinates", FILTER_SANITIZE_STRING));

			$protocols = [];
			foreach($_POST["protocols"] AS $key => $value):
				$protocols[] = $value;
			endforeach;

			$models = [];
			if(isSet($_POST["ai"])):
				foreach($_POST["ai"] AS $key => $value):
					$model = $this->getModel($value)["context"]["Data"];
					$mname = $model["name"]["value"];
					unset($model["id"]);
					unset($model["type"]);
					unset($model["mid"]);
					unset($model["name"]);
					unset($model["description"]);
					unset($model["network"]);
					unset($model["language"]);
					unset($model["framework"]);
					unset($model["toolkit"]);
					unset($model["dateCreated"]);
					unset($model["dateModified"]);
					$models[$mname] = $model;
				endforeach;
			endif;

			$sensors = [];
			if(isSet($_POST["sensors"])):
				foreach($_POST["sensors"] AS $key => $value):
					$sensor = $this->getThing($value)["context"]["Data"];
					unset($sensor["id"]);
					unset($sensor["type"]);
					unset($sensor["category"]);
					unset($sensor["description"]);
					unset($sensor["thing"]);
					unset($sensor["properties"]["image"]);
					unset($sensor["dateCreated"]);
					unset($sensor["dateModified"]);
					$sensors[] = $sensor;
				endforeach;
			endif;

			$actuators = [];
			if(isSet($_POST["actuators"])):
				foreach($_POST["actuators"] AS $key => $value):
					$actuator = $this->getThing($value)["context"]["Data"];
					unset($actuator["id"]);
					unset($actuator["type"]);
					unset($actuator["category"]);
					unset($actuator["description"]);
					unset($actuator["thing"]);
					unset($actuator["properties"]["image"]);
					unset($actuator["dateCreated"]);
					unset($actuator["dateModeified"]);
					$actuators[] = $actuator;
				endforeach;
			endif;

			if($device["context"]["Data"]["lid"]["value"] != $lid):
				$query = $this->_GeniSys->_secCon->prepare("
					UPDATE mqttu
					SET lid = :lid
					WHERE did = :did
				");
				$query->execute([
					':lid' => $lid,
					':did' => $did
				]);
				$pdoQuery->closeCursor();
				$pdoQuery = null;

				$query = $this->_GeniSys->_secCon->prepare("
					UPDATE mqttua
					SET lid = :lid
					WHERE did = :did
				");
				$query->execute([
					':lid' => $lid,
					':did' => $did
				]);
				$pdoQuery->closeCursor();
				$pdoQuery = null;

				$query = $this->_GeniSys->_secCon->prepare("
					UPDATE mqttua
					SET topic = :topicN
					WHERE did = :did
						& topic = :topic
				");
				$query->execute([
					':topicN' => $device["context"]["Data"]["lid"]["entity"] . "/ " . $device["context"]["Data"]["zid"]["entity"] . "/Devices/" . $device["context"]["Data"]["id"] . "/#",
					':did' => $did,
					':topic' => $location["context"]["Data"]["id"] . "/Devices/" . $zone["context"]["Data"]["id"] . "/Devices/" . $device["context"]["Data"]["id"] . "/#"
				]);
				$pdoQuery->closeCursor();
				$pdoQuery = null;
			endif;

			$data = [
				"type" => "Device",
				"category" => [
					"value" => [filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)]
				],
				"name" => [
					"value" => $name
				],
				"description" => [
					"value" => filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)
				],
				"lid" => [
					"value" => $lid,
					"entity" => $location["context"]["Data"]["id"]
				],
				"zid" => [
					"value" => $zid,
					"entity" => $zone["context"]["Data"]["id"]
				],
				"did" => [
					"value" => $did
				],
				"location" => [
					"type" => "geo:json",
					"value" => [
						"type" => "Point",
						"coordinates" => [floatval($coords[0]), floatval($coords[1])]
					]
				],
				"agent" => [
					"url" => filter_input(INPUT_POST, "agent", FILTER_SANITIZE_STRING)
				],
				"device" => [
					"name" => filter_input(INPUT_POST, "deviceName", FILTER_SANITIZE_STRING),
					"manufacturer" => filter_input(INPUT_POST, "deviceManufacturer", FILTER_SANITIZE_STRING),
					"model" => filter_input(INPUT_POST, "deviceModel", FILTER_SANITIZE_STRING)
				],
				"os" => [
					"name" => filter_input(INPUT_POST, "osName", FILTER_SANITIZE_STRING),
					"manufacturer" => filter_input(INPUT_POST, "osManufacturer", FILTER_SANITIZE_STRING),
					"version" => filter_input(INPUT_POST, "osVersion", FILTER_SANITIZE_STRING)
				],
				"protocols" => $protocols,
				"status" => [
					"value" => $device["context"]["Data"]["status"]["value"],
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"ip" => [
					"value" => $this->_GeniSys->_helpers->oEncrypt($ip),
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"mac" => [
					"value" => $this->_GeniSys->_helpers->oEncrypt($mac),
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"bluetooth" => [
					"address" => $bluetooth ? $this->_GeniSys->_helpers->oEncrypt($bluetooth) : "",
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"ai" => $models,
				"sensors" => $sensors,
				"actuators" => $actuators,
				"dateModified" => [
					"type" => "DateTime",
					"value" => date('Y-m-d\TH:i:s.Z\Z', time())
				]
			];

			$response = json_decode($this->contextBrokerRequest("PATCH", $this->cb["entities_url"] . "/" . $device["context"]["Data"]["id"] . "/attrs?type=Device", $this->createContextHeaders(), json_encode($data)), true);

			if($response["Response"]=="OK"):

				$hash = "";
				$msg = "";
				$this->contract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["contract"]))->send("updateDevice", $device["context"]["Data"]["id"], "Device", $lid, $zid, $did, $name, $device["context"]["Data"]["status"]["value"], time(), ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
					if ($err !== null) {
						$hash = "FAILED";
						$msg = $err;
						return;
					}
					$hash = $resp;
				});

				$balance = "";
				$balanceMessage = "";
				$actionMsg = "";
				if($hash == "FAILED"):
					$actionMsg = " HIAS Blockchain updateDevice failed! " . $msg;
				else:
					$txid = $this->storeBlockchainTransaction("Update Device", $hash, $did);
					$this->storeUserHistory("Updated Device", $txid, $lid, $zid, $did);
					$balance = $this->getBlockchainBalance();
					$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!";
				endif;

				$device = $this->getDevice($did);

				return [
					"Response"=> "OK",
					"Message" => "Device updated!" . $actionMsg . $balanceMessage,
					"Schema" => $device["context"]["Data"]
				];
			else:
				return [
					"Response"=> "Failed",
					"Message" => "There was a problem updating this device context data!"
				];
			endif;
		}

		public function resetDvcMqtt()
		{
			$id = filter_input(INPUT_GET, 'device', FILTER_SANITIZE_NUMBER_INT);
			$Device = $this->getDevice($id);

			$mqttPass = $this->_GeniSys->_helpers->password();
			$mqttHash = create_hash($mqttPass);

			$data = [
				"mqtt" => [
					"username" => $Device["context"]["Data"]["mqtt"]["username"],
					"password" => $this->_GeniSys->_helpers->oEncrypt($mqttPass),
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"dateModified" => [
					"type" => "DateTime",
					"value" => date('Y-m-d\TH:i:s.Z\Z', time())
				]
			];

			$response = json_decode($this->contextBrokerRequest("PATCH", $this->cb["entities_url"] . "/" . $Device["context"]["Data"]["id"] . "/attrs?type=Device", $this->createContextHeaders(), json_encode($data)), true);

			if($response["Response"]=="OK"):
				$query = $this->_GeniSys->_secCon->prepare("
					UPDATE mqttu
					SET pw = :pw
					WHERE did = :did
				");
				$query->execute(array(
					':pw' => $mqttHash,
					':did' => $id
				));

				$this->storeUserHistory("Reset Device MQTT Password", 0, $Device["context"]["Data"]["lid"]["value"], $Device["context"]["Data"]["zid"]["value"], $id);

				return [
					"Response"=> "OK",
					"Message" => "MQTT password reset!",
					"P" => $mqttPass
				];
			else:
				return [
					"Response"=> "FAILED",
					"Message" => "MQTT password reset failed!"
				];
			endif;
		}

		public function resetDvcKey()
		{
			$id = filter_input(INPUT_GET, 'device', FILTER_SANITIZE_NUMBER_INT);
			$Device = $this->getDevice($id);

			$privKey = $this->_GeniSys->_helpers->generateKey(32);
			$privKeyHash = $this->_GeniSys->_helpers->createPasswordHash($privKey);

			$data = [
				"keys" => [
					"public" => $Device["context"]["Data"]["keys"]["public"],
					"private" => $this->_GeniSys->_helpers->oEncrypt($privKeyHash),
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"dateModified" => [
					"type" => "DateTime",
					"value" => date('Y-m-d\TH:i:s.Z\Z', time())
				]
			];

			$response = json_decode($this->contextBrokerRequest("PATCH", $this->cb["entities_url"] . "/" . $Device["context"]["Data"]["id"] . "/attrs?type=Device", $this->createContextHeaders(), json_encode($data)), true);

			if($response["Response"]=="OK"):
				$this->storeUserHistory("Reset Device Key", 0, $Device["context"]["Data"]["lid"]["value"], $Device["context"]["Data"]["zid"]["value"], $id);
				return [
					"Response"=> "OK",
					"Message" => "Device key reset!",
					"P" => $privKey
				];
			else:
				return [
					"Response"=> "FAILED",
					"Message" => "Device key reset failed!"
				];
			endif;

		}

		public function resetDvcAmqpKey()
		{
			$id = filter_input(INPUT_GET, 'device', FILTER_SANITIZE_NUMBER_INT);
			$Device = $this->getDevice($id);

			$amqpPass = $this->_GeniSys->_helpers->password();
			$amqpHash = $this->_GeniSys->_helpers->createPasswordHash($amqpPass);

			$data = [
				"amqp" => [
					"username" => $Device["context"]["Data"]["amqp"]["username"],
					"password" => $this->_GeniSys->_helpers->oEncrypt($amqpPass),
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"dateModified" => [
					"type" => "DateTime",
					"value" => date('Y-m-d\TH:i:s.Z\Z', time())
				]
			];

			$response = json_decode($this->contextBrokerRequest("PATCH", $this->cb["entities_url"] . "/" . $Device["context"]["Data"]["id"] . "/attrs?type=Device", $this->createContextHeaders(), json_encode($data)), true);

			if($response["Response"]=="OK"):
				$query = $this->_GeniSys->_secCon->prepare("
					UPDATE amqpu
					SET pw = :pw
					WHERE username = :username
				");
				$query->execute(array(
					':pw' => $this->_GeniSys->_helpers->oEncrypt($amqpHash),
					':username' => $this->_GeniSys->_helpers->oDecrypt($Device["context"]["Data"]["amqp"]["username"])
				));

				$this->storeUserHistory("Reset Device AMQP Key", 0, $Device["context"]["Data"]["lid"]["value"], $Device["context"]["Data"]["zid"]["value"], $id);

				return [
					"Response"=> "OK",
					"Message" => "AMQP password reset!",
					"P" => $amqpPass
				];
			else:
				return [
					"Response"=> "FAILED",
					"Message" => "AMQP password reset failed!"
				];
			endif;
		}

		public function retrieveDeviceTransactions($device, $limit = 0, $order = "")
		{
			if($order):
				$orderer = "ORDER BY " . $order;
			else:
				$orderer = "ORDER BY id DESC";
			endif;

			if($limit):
				$limiter = "LIMIT " . $limit;
			endif;

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT *
				FROM transactions
				WHERE did = :id
				$orderer
				$limiter
			");
			$pdoQuery->execute([
				":id" => $device
			]);
			$response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			return $response;
		}

		public function retrieveDeviceTransaction($txn)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT id,
					action,
					hash
				FROM transactions
				WHERE id = :id
			");
			$pdoQuery->execute([
				":id" => $txn
			]);
			$response=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			return $response;
		}

		public function retrieveDeviceTransactionReceipt($hash)
		{
			$dreceipt = "";
			$msg = "";
			$eth = $this->web3->eth;
			$eth->getTransactionReceipt($hash, function ($err, $receipt) use (&$dreceipt) {
				if ($err !== null) {
					$dreceipt = "FAILED";
					$msg = $err;
					return;
				}
				$dreceipt = $receipt;
			});

			if($dreceipt == "FAIL"):
				return [
					"Response" => "FAILED",
					"Message" => "Fetch Transaction Failed. " . $msg
				];
			else:
				return [
					"Response" => "OK",
					"Message" => "Fetch Transaction OK. ",
					"Receipt" => $dreceipt
				];
			endif;

		}

		public function retrieveDeviceHistory($device, $limit = 0, $order = "")
		{
			if($order):
				$orderer = "ORDER BY " . $order;
			else:
				$orderer = "ORDER BY id DESC";
			endif;

			if($limit):
				$limiter = "LIMIT " . $limit;
			endif;

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT *
				FROM history
				WHERE tdid = :id
				$orderer
				$limiter
			");
			$pdoQuery->execute([
				":id" => $device
			]);
			$response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			return $response;
		}

		public function retrieveDeviceStatuses($device, $limit = 0, $order = -1)
		{
			$mngConn = new MongoDB\Driver\Manager("mongodb://localhost:27017/".$this->_GeniSys->_mdbname.'', ["username" => $this->_GeniSys->_mdbusername, "password" => $this->_GeniSys->_mdbpassword]);
			$query = new MongoDB\Driver\Query(['Device' => strval($device)], ['limit' => $limit, 'sort' => ['Time' => $order]]);
			$rows = $mngConn->executeQuery($this->_GeniSys->_mdbname.".Statuses", $query);

			$mngoData = [];

			foreach ($rows as $document):
				$mngoData[]=$document;
			endforeach;

			if(count($mngoData)):
				return  [
					'Response'=>'OK',
					'ResponseData' => $mngoData
				];
			else:
				return  [
					'Response'=>'FAILED'
				];
			endif;
		}

		public function retrieveDeviceLife($device, $limit = 0, $order = -1)
		{
			$mngConn = new MongoDB\Driver\Manager("mongodb://localhost:27017/".$this->_GeniSys->_mdbname.'', ["username" => $this->_GeniSys->_mdbusername, "password" => $this->_GeniSys->_mdbpassword]);
			$query = new MongoDB\Driver\Query(['Device' => strval($device)], ['limit' => $limit, 'sort' => ['Time' => $order]]);
			$rows = $mngConn->executeQuery($this->_GeniSys->_mdbname.".Life", $query);

			$mngoData = [];

			foreach ($rows as $document):
				$mngoData[]=$document;
			endforeach;

			if(count($mngoData)):
				return  [
					'Response'=>'OK',
					'ResponseData' => $mngoData
				];
			else:
				return  [
					'Response'=>'FAILED'
				];
			endif;
		}

		public function retrieveDeviceCommands($device, $limit = 0, $order = -1)
		{
			$mngConn = new MongoDB\Driver\Manager("mongodb://localhost:27017/".$this->_GeniSys->_mdbname.'', ["username" => $this->_GeniSys->_mdbusername, "password" => $this->_GeniSys->_mdbpassword]);

			$filter = [
				'$and'  => [
					['Use' => 'Device'],
					['To' => strval($device)]
				]
			];

			$query = new MongoDB\Driver\Query(['To' => strval($device)], ['limit' => $limit, 'sort' => ['Time' => $order]]);
			$rows = $mngConn->executeQuery($this->_GeniSys->_mdbname.".Commands", $query);

			$mngoData = [];

			foreach ($rows as $document):
				$mngoData[]=$document;
			endforeach;

			if(count($mngoData)):
				return  [
					'Response'=>'OK',
					'ResponseData' => $mngoData
				];
			else:
				return  [
					'Response'=>'FAILED'
				];
			endif;
		}

		public function retrieveDeviceSensors($device, $limit = 0, $order = -1)
		{
			$mngConn = new MongoDB\Driver\Manager("mongodb://localhost:27017/".$this->_GeniSys->_mdbname.'', ["username" => $this->_GeniSys->_mdbusername, "password" => $this->_GeniSys->_mdbpassword]);
			$query = new MongoDB\Driver\Query(['Device' => strval($device)], ['limit' => $limit, 'sort' => ['Time' => $order]]);
			$rows = $mngConn->executeQuery($this->_GeniSys->_mdbname.".Sensors", $query);

			$mngoData = [];

			foreach ($rows as $document):
				$mngoData[]=$document;
			endforeach;

			if(count($mngoData)):
				return  [
					'Response'=>'OK',
					'ResponseData' => $mngoData
				];
			else:
				return  [
					'Response'=>'FAILED'
				];
			endif;
		}

		public function getLife()
		{
			$Device = $this->getDevice(filter_input(INPUT_GET, 'device', FILTER_SANITIZE_NUMBER_INT), "batteryLevel,cpuUsage,memoryUsage,hddUsage,temperature,status");

			if($Device["context"]["Response"]=="OK"):
				$response = [
					"battery" => $Device["context"]["Data"]["batteryLevel"]["value"],
					"cpu" => $Device["context"]["Data"]["cpuUsage"]["value"],
					"mem" => $Device["context"]["Data"]["memoryUsage"]["value"],
					"hdd" => $Device["context"]["Data"]["hddUsage"]["value"],
					"tempr" => $Device["context"]["Data"]["temperature"]["value"],
					"status" => $Device["context"]["Data"]["status"]["value"]
				];
				return  [
					'Response' => 'OK',
					'ResponseData' => $response
				];
			else:
				return  [
					'Response'=>'FAILED'
				];
			endif;
		}

		public function getThings($limit = 0, $category = "")
		{
			$limiter = "";
			if($limit != 0):
				$limiter = "&limit=" . $limit;
			endif;
			$scategory = "";
			if($category != ""):
				$scategory = "&category=" . $category;
			endif;

			$things = json_decode($this->contextBrokerRequest("GET", $this->cb["entities_url"] . "?type=Thing".$limiter.$scategory, $this->createContextHeaders(), []), true);
			return $things;
		}

		public function getThing($id, $attrs = Null)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT *
				FROM things
				WHERE id = :id
			");
			$pdoQuery->execute([
				":id" => $id
			]);
			$thing=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			if($attrs):
				$attrs="&attrs=" . $attrs;
			endif;

			$thing["context"] = json_decode($this->contextBrokerRequest("GET", $this->cb["entities_url"] . "/" . $thing["pub"] . "?type=Thing" . $attrs, $this->createContextHeaders(), []), true);
			return $thing;
		}

		public function createThing()
		{
			if(!filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Category is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Name is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Description is required"
				];
			endif;

			if (!empty($_FILES['image']['name']) && ($_FILES['image']['error'] == 0)):

				$cleaned_file = preg_replace('/\.(?=.*?\.)/', '_', $_FILES['image']['name']);
				$cleaned_file = str_replace([' '], "-", $cleaned_file);

				if($_FILES["image"]["type"] == "image/jpeg" | $_FILES["image"]["type"] == "image/png" | $_FILES["image"]["type"] ==  "image/gif"):

					$cleaned_file = preg_replace('/\.(?=.*?\.)/', '_', $_FILES['image']['name']);
					$cleaned_file = str_replace([' '], "-", $cleaned_file);

					if (getimagesize($_FILES["image"]["tmp_name"]) !== false):
						$valid_file_extensions = [
							".jpg",
							".jpeg",
							".gif",
							".png",
							".JPG",
							".JPEG",
							".GIF",
							".PNG"
						];
						$file_extension = strrchr($_FILES["image"]["name"], ".");
						if (in_array($file_extension, $valid_file_extensions)):
							$fileName=time().'_'.$cleaned_file;
							if(move_uploaded_file($_FILES["image"]["tmp_name"],"Media/Images/Things/".$fileName)):
								switch (strtolower($_FILES['image']['type'])):
									case 'image/jpeg':
										$image = imagecreatefromjpeg("Media/Images/Things/".$fileName);
										break;
									case 'image/png':
										$image = imagecreatefrompng("Media/Images/Things/".$fileName);
										break;
									case 'image/gif':
										$image = imagecreatefromgif("Media/Images/Things/".$fileName);
										break;
									default:
								endswitch;

								$pubKey = $this->_GeniSys->_helpers->generate_uuid();

								$query = $this->_GeniSys->_secCon->prepare("
									INSERT INTO  things  (
										`pub`
									)  VALUES (
										:pub
									)
								");
								$query->execute([
									':pub' => $pubKey
								]);
								$sid = $this->_GeniSys->_secCon->lastInsertId();

								$properties=[];
								if(isSet($_POST["properties"])):
									foreach($_POST["properties"] AS $key => $value):
										$properties[$value] = ["value" => ""];
									endforeach;
								endif;
								$properties["image"] = ["value" => $fileName];

								$commands=[];
								if(isSet($_POST["commands"])):
									foreach($_POST["commands"] AS $key => $value):
										$values = explode(",", $value);
										$commands[$key] = $values;
									endforeach;
								endif;

								$states=[];
								$state=[];
								if(isSet($_POST["states"])):
									$states = $_POST["states"];
									$state = [
										"value" => "",
										"timestamp" => ""
									];
								endif;

								$data = [
									"id" => $pubKey,
									"type" => "Thing",
									"category" => [
										"value" => [filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)]
									],
									"name" => [
										"value" => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)
									],
									"description" => [
										"value" => filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)
									],
									"thing" => [
										"manufacturer" => filter_input(INPUT_POST, "manufacturer", FILTER_SANITIZE_STRING),
										"model" => filter_input(INPUT_POST, "model", FILTER_SANITIZE_STRING)
									],
									"sid" => [
										"value" => $sid
									],
									"properties" => $properties,
									"commands" => $commands,
									"states" => $states,
									"state" => $state,
									"dateCreated" => [
										"type" => "DateTime",
										"value" => date('Y-m-d\TH:i:s.Z\Z', time())
									],
									"dateModified" => [
										"type" => "DateTime",
										"value" => date('Y-m-d\TH:i:s.Z\Z', time())
									]
								];

								$response = json_decode($this->contextBrokerRequest("POST", $this->cb["entities_url"] . "?type=Thing", $this->createContextHeaders(), json_encode($data)), true);

								if($response["Response"]=="OK"):
									$this->storeUserHistory("Created Thing", 0, 0, 0, 0, $sid);
									return [
										"Response"=> "OK",
										"Message" => "Thing updated!"
									];
								else:
									return [
										"Response"=> "FAILED",
										"Message" => "Thing update KO! " . $response["Description"]
									];
								endif;

							endif;

						else:
							return [
								"Response" => "FAILED",
								"Message" => "File uploaded FAILED, invalid image file."
							];
						endif;

					else:
						return [
							"Response" => "FAILED",
								"Message" => "File uploaded FAILED, invalid image file."
						];
					endif;
				else:
					return [
						"Response" => "FAILED",
								"Message" => "File uploaded FAILED, invalid image file."
					];
				endif;

			else:
				return [
					"Response"=> "FAILED",
					"Message" => "Please provide a sensor image!"
				];
			endif;
		}

		public function updateThing()
		{
			if(!filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Category is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Name is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Description is required"
				];
			endif;

			$tid = filter_input(INPUT_POST, 'thing', FILTER_SANITIZE_NUMBER_INT);
			$thing = $this->getThing($tid);

			$properties=[];

			if($_FILES["image"]["type"] == "image/jpeg" | $_FILES["image"]["type"] == "image/png" | $_FILES["image"]["type"] ==  "image/gif"):

				$cleaned_file = preg_replace('/\.(?=.*?\.)/', '_', $_FILES['image']['name']);
				$cleaned_file = str_replace([' '], "-", $cleaned_file);

				if (getimagesize($_FILES["image"]["tmp_name"]) !== false):
					$valid_file_extensions = [
						".jpg",
						".jpeg",
						".gif",
						".png",
						".JPG",
						".JPEG",
						".GIF",
						".PNG"
					];
					$file_extension = strrchr($_FILES["image"]["name"], ".");
					if (in_array($file_extension, $valid_file_extensions)):
						$fileName=time().'_'.$cleaned_file;
						if(move_uploaded_file($_FILES["image"]["tmp_name"],"Media/Images/Things/".$fileName)):
							switch (strtolower($_FILES['image']['type'])):
								case 'image/jpeg':
									$image = imagecreatefromjpeg("Media/Images/Things/".$fileName);
									break;
								case 'image/png':
									$image = imagecreatefrompng("Media/Images/Things/".$fileName);
									break;
								case 'image/gif':
									$image = imagecreatefromgif("Media/Images/Things/".$fileName);
									break;
								default:
							endswitch;

							$properties["image"] = ["value" => $fileName];

						endif;

					else:
						return [
							"Response" => "FAILED",
							"Message" => "File uploaded FAILED, invalid image file."
						];
					endif;

				else:
					return [
						"Response" => "FAILED",
							"Message" => "File uploaded FAILED, invalid image file."
					];
				endif;
			else:
				$properties["image"] = ["value" => $thing["context"]["Data"]["properties"]["image"]["value"]];
			endif;

			if(isSet($_POST["properties"])):
				foreach($_POST["properties"] AS $key => $value):
					$properties[$value] = ["value" => ""];
				endforeach;
			endif;

			$commands=[];
			if(isSet($_POST["commands"])):
				foreach($_POST["commands"] AS $key => $value):
					$values = explode(",", $value);
					$commands[$key] = $values;
				endforeach;
			endif;

			$states=[];
			$state=[];
			if(isSet($_POST["states"])):
				$states = $_POST["states"];
				$state = [
					"value" => "",
					"timestamp" => ""
				];
			endif;

			$data = [
				"category" => [
					"value" => [filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)]
				],
				"name" => [
					"value" => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)
				],
				"description" => [
					"value" => filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)
				],
				"thing" => [
					"manufacturer" => filter_input(INPUT_POST, "manufacturer", FILTER_SANITIZE_STRING),
					"model" => filter_input(INPUT_POST, "model", FILTER_SANITIZE_STRING)
				],
				"properties" => $properties,
				"commands" => $commands,
				"states" => $states,
				"state" => $state,
				"dateModified" => [
					"type" => "DateTime",
					"value" => date('Y-m-d\TH:i:s.Z\Z', time())
				]
			];

			$response = json_decode($this->contextBrokerRequest("PATCH", $this->cb["entities_url"] . "/" . $thing["context"]["Data"]["id"] . "/attrs?type=Thing", $this->createContextHeaders(), json_encode($data)), true);

			if($response["Response"]=="OK"):

				$schema = $this->getThing($tid);
				return [
					"Response"=> "OK",
					"Message" => "Thing updated!",
					"Schema" => $schema["context"]["Data"]
				];
			else:
				return [
					"Response"=> "FAILED",
					"Message" => "Thing update KO! " . $response["Description"]
				];
			endif;
		}

		public function updateSensor()
		{
			if(!filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "ID is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "type", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Type is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Name is required"
				];
			endif;

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				UPDATE sensors
				SET name = :name,
					type = :type
				WHERE id = :id
			");
			$pdoQuery->execute([
				":name" => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING),
				":type" => filter_input(INPUT_POST, "type", FILTER_SANITIZE_STRING),
				":id" => filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)
			]);
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			if($_FILES["image"]["type"] == "image/jpeg" | $_FILES["image"]["type"] == "image/png" | $_FILES["image"]["type"] ==  "image/gif"):

				if (getimagesize($_FILES["image"]["tmp_name"]) !== false):
					$valid_file_extensions = [
						".jpg",
						".jpeg",
						".gif",
						".png",
						".JPG",
						".JPEG",
						".GIF",
						".PNG"
					];
					$file_extension = strrchr($_FILES["image"]["name"], ".");
					if (in_array($file_extension, $valid_file_extensions)):
						$fileName=time().'_'.$cleaned_file;
						if(move_uploaded_file($_FILES["image"]["tmp_name"],"Media/Images/Sensors/".$fileName)):
							switch (strtolower($_FILES['image']['type'])):
								case 'image/jpeg':
									$image = imagecreatefromjpeg("Media/Images/Sensors/".$fileName);
									break;
								case 'image/png':
									$image = imagecreatefrompng("Media/Images/Sensors/".$fileName);
									break;
								case 'image/gif':
									$image = imagecreatefromgif("Media/Images/Sensors/".$fileName);
									break;
								default:
							endswitch;

							$pdoQuery = $this->_GeniSys->_secCon->prepare("
								UPDATE sensors
								SET image = :image
								WHERE id = :id
							");
							$pdoQuery->execute([
								":image" => $fileName,
								":id" => filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)
							]);
							$pdoQuery->closeCursor();
							$pdoQuery = null;

							return [
								"Response"=> "OK",
								"Message" => "Sensor created!",
								"SID" => $sensor
							];

						endif;

					else:
						return [
							"Response" => "FAILED",
							"Message" => "File uploaded FAILED, invalid image file."
						];
					endif;

				else:
					return [
						"Response" => "FAILED",
							"Message" => "File uploaded FAILED, invalid image file."
					];
				endif;
			endif;

			$this->storeUserHistory("Updated Sensor", 0, 0, 0, 0, filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT));

			return [
				"Response"=> "OK",
				"Message" => "Sensor updated!"
			];
		}

		public function getApplications($limit = 0, $order = "id DESC")
		{
			$limiter = "";
			if($limit != 0):
				$limiter = "&limit=" . $limit;
			endif;

			$zones = json_decode($this->contextBrokerRequest("GET", $this->cb["entities_url"] . "?type=Application".$limiter, $this->createContextHeaders(), []), true);
			return $zones;
		}

		public function getApplication($id, $attrs = Null)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT *
				FROM mqtta
				WHERE id = :id
				ORDER BY id DESC
			");
			$pdoQuery->execute([
				":id" => $id
			]);
			$application=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			if($attrs):
				$attrs="&attrs=" . $attrs;
			endif;

			$application["context"] = json_decode($this->contextBrokerRequest("GET", $this->cb["entities_url"] . "/" . $application["apub"] . "?type=Application" . $attrs, $this->createContextHeaders(), []), true);
			return $application;
		}

		public function getApplicationCategories()
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT category
				FROM cbApplicationCats
				ORDER BY category ASC
			");
			$pdoQuery->execute();
			$categories=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $categories;
		}

		public function createApplication()
		{
			if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "Location ID is required"
				];
			endif;

			if(!$this->checkLocation(filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT))):
				return [
					"Response"=> "Failed",
					"Message" => "iotJumpWay location does not exist"
				];
			endif;

			if(!filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Category is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Name is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Name is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "coordinates", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Coordinates entity is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "deviceName", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Hardware device name is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "deviceManufacturer", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Hardware device manufacturer is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "deviceModel", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Hardware device model is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "osName", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Operating system name is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "osManufacturer", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Operating system manufacturer is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "osVersion", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Operating system version is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "agent", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "IoT Agent is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "IP is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "MAC is required"
				];
			endif;

			if(!isSet($_POST["protocols"])):
				return [
					"Response"=> "Failed",
					"Message" => "At least one M2M protocol is required"
				];
			endif;

			$unlocked =  $this->unlockBlockchainAccount();

			if($unlocked == "FAILED"):
				return [
					"Response"=> "Failed",
					"Message" => "Unlocking HIAS Blockhain Account Failed!"
				];
			endif;

			$mqttUser = $this->_GeniSys->_helpers->generate_uuid();
			$mqttPass = $this->_GeniSys->_helpers->password();
			$mqttHash = create_hash($mqttPass);

			$pubKey = $this->_GeniSys->_helpers->generate_uuid();
			$privKey = $this->_GeniSys->_helpers->generateKey(32);
			$privKeyHash = $this->_GeniSys->_helpers->createPasswordHash($privKey);

			$amqppubKey = $this->_GeniSys->_helpers->generate_uuid();
			$amqpprvKey = $this->_GeniSys->_helpers->generateKey(32);
			$amqpKeyHash = $this->_GeniSys->_helpers->createPasswordHash($amqpprvKey);

			$bcPass = $this->_GeniSys->_helpers->password();

			$lid = filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT);
			$location = $this->getLocation($lid);

			$ip = filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING);
			$mac = filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING);
			$bluetooth = filter_input(INPUT_POST, "bluetooth", FILTER_SANITIZE_STRING);
			$name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
			$coords = explode(",", filter_input(INPUT_POST, "coordinates", FILTER_SANITIZE_STRING));

			$allowed = 0;
			$pallowed = filter_input(INPUT_POST, "patients", FILTER_SANITIZE_STRING) ? True : False;
			$admin = filter_input(INPUT_POST, "admin", FILTER_SANITIZE_STRING) ? True : False;

			$newBcUser = $this->createBlockchainUser($bcPass);

			if($newBcUser == "FAILED"):
				return [
					"Response"=> "Failed",
					"Message" => "Creating New HIAS Blockhain Account Failed!"
				];
			endif;

			$htpasswd = new Htpasswd('/etc/nginx/security/htpasswd');
			$htpasswd->addUser($pubKey, $privKey, Htpasswd::ENCTYPE_APR_MD5);

			$protocols = [];
			foreach($_POST["protocols"] AS $key => $value):
				$protocols[] = $value;
			endforeach;

			$models = [];
			if(isSet($_POST["ai"])):
				foreach($_POST["ai"] AS $key => $value):
					$model = $this->getModel($value)["context"]["Data"];
					$mname = $model["name"]["value"];
					unset($model["id"]);
					unset($model["type"]);
					unset($model["mid"]);
					unset($model["name"]);
					unset($model["description"]);
					unset($model["network"]);
					unset($model["language"]);
					unset($model["framework"]);
					unset($model["toolkit"]);
					unset($model["dateCreated"]);
					unset($model["dateModified"]);
					$models[$mname] = $model;
				endforeach;
			endif;

			$sensors = [];
			if(isSet($_POST["sensors"])):
				foreach($_POST["sensors"] AS $key => $value):
					$sensor = $this->getThing($value)["context"]["Data"];
					unset($sensor["id"]);
					unset($sensor["type"]);
					unset($sensor["category"]);
					unset($sensor["description"]);
					unset($sensor["thing"]);
					unset($sensor["properties"]["image"]);
					unset($sensor["dateCreated"]);
					unset($sensor["dateModified"]);
					$sensors[] = $sensor;
				endforeach;
			endif;

			$actuators = [];
			if(isSet($_POST["actuators"])):
				foreach($_POST["actuators"] AS $key => $value):
					$actuator = $this->getThing($value)["context"]["Data"];
					unset($actuator["id"]);
					unset($actuator["type"]);
					unset($actuator["category"]);
					unset($actuator["description"]);
					unset($actuator["thing"]);
					unset($actuator["properties"]["image"]);
					unset($actuator["dateCreated"]);
					unset($actuator["dateModeified"]);
					$actuators[] = $actuator;
				endforeach;
			endif;

			$query = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  mqtta  (
					`apub`
				)  VALUES (
					:apub
				)
			");
			$query->execute([
				':apub' => $pubKey
			]);
			$aid = $this->_GeniSys->_secCon->lastInsertId();

			$data = [
				"id" => $pubKey,
				"type" => "Application",
				"category" => [
					"value" => [filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)]
				],
				"name" => [
					"value" => $name
				],
				"description" => [
					"value" => filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)
				],
				"lid" => [
					"value" => $lid,
					"entity" => $location["context"]["Data"]["id"]
				],
				"aid" => [
					"value" => $aid
				],
				"admin" => [
					"value" => filter_input(INPUT_POST, "admin", FILTER_SANITIZE_NUMBER_INT) ? filter_input(INPUT_POST, "admin", FILTER_SANITIZE_NUMBER_INT) : 0
				],
				"patients" => [
					"value" => filter_input(INPUT_POST, "patients", FILTER_SANITIZE_NUMBER_INT) ? filter_input(INPUT_POST, "patients", FILTER_SANITIZE_NUMBER_INT) : 0
				],
				"cancelled" => [
					"value" => filter_input(INPUT_POST, "cancelled", FILTER_SANITIZE_NUMBER_INT) ? filter_input(INPUT_POST, "cancelled", FILTER_SANITIZE_NUMBER_INT) : 0
				],
				"location" => [
					"type" => "geo:json",
					"value" => [
						"type" => "Point",
						"coordinates" => [floatval($coords[0]), floatval($coords[1])]
					]
				],
				"agent" => [
					"url" => filter_input(INPUT_POST, "agent", FILTER_SANITIZE_STRING)
				],
				"device" => [
					"name" => filter_input(INPUT_POST, "deviceName", FILTER_SANITIZE_STRING),
					"manufacturer" => filter_input(INPUT_POST, "deviceManufacturer", FILTER_SANITIZE_STRING),
					"model" => filter_input(INPUT_POST, "deviceModel", FILTER_SANITIZE_STRING),
					"version" => filter_input(INPUT_POST, "deviceVersion", FILTER_SANITIZE_STRING)
				],
				"os" => [
					"name" => filter_input(INPUT_POST, "osName", FILTER_SANITIZE_STRING),
					"manufacturer" => filter_input(INPUT_POST, "osManufacturer", FILTER_SANITIZE_STRING),
					"version" => filter_input(INPUT_POST, "osVersion", FILTER_SANITIZE_STRING)
				],
				"protocols" => $protocols,
				"status" => [
					"value" => "OFFLINE",
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"keys" => [
					"public" => $pubKey,
					"private" => $this->_GeniSys->_helpers->oEncrypt($privKeyHash),
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"blockchain" => [
					"address" => $newBcUser,
					"password" => $this->_GeniSys->_helpers->oEncrypt($bcPass)
				],
				"mqtt" => [
					"username" => $this->_GeniSys->_helpers->oEncrypt($mqttUser),
					"password" => $this->_GeniSys->_helpers->oEncrypt($mqttPass),
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"coap" => [
					"username" => "",
					"password" => ""
				],
				"amqp" => [
					"username" => $this->_GeniSys->_helpers->oEncrypt($amqppubKey),
					"password" => $this->_GeniSys->_helpers->oEncrypt($amqpprvKey),
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"batteryLevel" => [
					"value" => 0.00
				],
				"cpuUsage" => [
					"value" => 0.00
				],
				"memoryUsage" => [
					"value" => 0.00
				],
				"hddUsage" => [
					"value" => 0.00
				],
				"temperature" => [
					"value" => 0.00
				],
				"ip" => [
					"value" => $ip,
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"mac" => [
					"value" => $this->_GeniSys->_helpers->oEncrypt($mac),
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"bluetooth" => [
					"address" => $bluetooth ? $this->_GeniSys->_helpers->oEncrypt($bluetooth) : "",
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"ai" => $models,
				"sensors" => $sensors,
				"actuators" => $actuators,
				"dateCreated" => [
					"type" => "DateTime",
					"value" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"dateFirstUsed" => [
					"type" => "DateTime",
					"value" => ""
				],
				"dateModified" => [
					"type" => "DateTime",
					"value" => date('Y-m-d\TH:i:s.Z\Z', time())
				]
			];

			$response = json_decode($this->contextBrokerRequest("POST", $this->cb["entities_url"] . "?type=Application", $this->createContextHeaders(), json_encode($data)), true);

			if($response["Response"]=="OK"):

				$query = $this->_GeniSys->_secCon->prepare("
					INSERT INTO  mqttu  (
						`lid`,
						`aid`,
						`uname`,
						`pw`
					)  VALUES (
						:lid,
						:aid,
						:uname,
						:pw
					)
				");
				$query->execute([
					':lid' => $lid,
					':aid' => $aid,
					':uname' => $mqttUser,
					':pw' => $mqttHash
				]);

				$query = $this->_GeniSys->_secCon->prepare("
					INSERT INTO  mqttua  (
						`lid`,
						`aid`,
						`username`,
						`topic`,
						`rw`
					)  VALUES (
						:lid,
						:aid,
						:username,
						:topic,
						:rw
					)
				");
				$query->execute(array(
					':lid' => $lid,
					':aid' => $aid,
					':username' => $mqttUser,
					':topic' => $location["context"]["Data"]["id"] . "/Devices/#",
					':rw' => 4
				));

				$query = $this->_GeniSys->_secCon->prepare("
					INSERT INTO  mqttua  (
						`lid`,
						`aid`,
						`username`,
						`topic`,
						`rw`
					)  VALUES (
						:lid,
						:aid,
						:username,
						:topic,
						:rw
					)
				");
				$query->execute(array(
					':lid' => $lid,
					':aid' => $aid,
					':username' => $mqttUser,
					':topic' => $location["context"]["Data"]["id"] . "/Applications/#",
					':rw' => 4
				));

				$amid = $this->addAmqpUser($amqppubKey, $amqpKeyHash);
				$this->addAmqpUserVh($amid, "iotJumpWay");
				$this->addAmqpVhPerm($amid, "iotJumpWay", "exchange", "Core", "read");
				$this->addAmqpVhPerm($amid, "iotJumpWay", "exchange", "Core", "write");
				$this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Life", "read");
				$this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Life", "write");
				$this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Statuses", "read");
				$this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Statuses", "write");
				$this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "read", "Life");
				$this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "write", "Life");
				$this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "read", "Statuses");
				$this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "write", "Statuses");

				if($admin):
					$this->addAmqpUserPerm($amid, "administrator");
					$this->addAmqpUserPerm($amid, "managment");
					$this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Life", "configure");
					$this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Statuses", "configure");
				endif;

				$unlocked =  $this->unlockBlockchainAccount();

				if($unlocked == "FAILED"):
					return [
						"Response"=> "Failed",
						"Message" => "Unlocking HIAS Blockhain Account Failed!"
					];
				endif;

				$hash = "";
				$msg = "";
				$this->contract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["contract"]))->send("registerApplication", $pubKey, $newBcUser, $admin, $lid, $aid, $name, $_SESSION["GeniSysAI"]["Uid"], time(), ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
					if ($err !== null) {
						$hash = "FAILED";
						$msg = $err;
						return;
					}
					$hash = $resp;
				});

				$actionMsg = "";
				$balanceMessage = "";

				if($hash == "FAILED"):
					$actionMsg = " HIAS Blockchain registerApplication failed!\n";
				else:
					$txid = $this->storeBlockchainTransaction("Register Application", $hash, 0, $aid);
					$this->storeUserHistory("Register Application", $txid, $lid, 0, 0, 0, $aid);
					$balance = $this->getBlockchainBalance();
					$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
				endif;

				$this->icontract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["icontract"]))->send("registerAuthorized", $newBcUser, ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
					if ($err !== null) {
						$hash = "FAILED";
						$msg = $err;
						return;
					}
					$hash = $resp;
				});

				if($hash == "FAILED"):
					$actionMsg .= " HIAS Blockchain registerAuthorized failed!\n";
				else:
					$txid = $this->storeBlockchainTransaction("iotJumpWay Register Authorized", $hash, 0, $aid);
					$this->storeUserHistory("Register Authorized", $txid, $lid, 0, 0, 0, $aid);
					$balance = $this->getBlockchainBalance();
					if($balanceMessage == ""):
						$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
					endif;
				endif;

				if(filter_input(INPUT_POST, "patients", FILTER_SANITIZE_STRING)):

					$this->pcontract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["pcontract"]))->send("registerUser", $newBcUser, ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
						if ($err !== null) {
							$hash = "FAILED";
							$msg = $err;
							return;
						}
						$hash = $resp;
					});

					if($hash == "FAILED"):
						$actionMsg .= " HIAS Blockchain patients registerUser failed!\n";
					else:
						$txid = $this->storeBlockchainTransaction("Patients Register User", $hash, 0, $aid);
						$this->storeUserHistory("Patients Register User", $txid, $lid, 0, 0, 0, $aid);
						$balance = $this->getBlockchainBalance();
						if($balanceMessage == ""):
							$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
						endif;
					endif;

				endif;

				return [
					"Response"=> "OK",
					"Message" => $actionMsg . $balanceMessage,
					"LID" => $lid,
					"AID" => $aid,
					"MU" => $mqttUser,
					"MP" => $mqttPass,
					"BU" => $newBcUser,
					"BP" => $bcPass,
					"AppID" => $pubKey,
					"AppKey" => $privKey
				];
			else:
				return [
					"Response"=> "FAILED",
					"Message" => "Application creation failed!"
				];
			endif;
		}

		public function updateApplication()
		{
			if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "Location ID is required"
				];
			endif;

			if(!$this->checkLocation(filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT))):
				return [
					"Response"=> "Failed",
					"Message" => "iotJumpWay location does not exist"
				];
			endif;

			if(!filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Category is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Name is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Name is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "coordinates", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Coordinates entity is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "deviceName", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Hardware device name is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "deviceManufacturer", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Hardware device manufacturer is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "deviceModel", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Hardware device model is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "osName", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Operating system name is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "osManufacturer", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Operating system manufacturer is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "osVersion", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Operating system version is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "osVersion", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Operating system version is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "agent", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "IoT Agent is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "IP is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "MAC is required"
				];
			endif;

			if(!isSet($_POST["protocols"])):
				return [
					"Response"=> "Failed",
					"Message" => "At least one M2M protocol is required"
				];
			endif;

			$unlocked =  $this->unlockBlockchainAccount();

			if($unlocked == "FAILED"):
				return [
					"Response"=> "Failed",
					"Message" => "Unlocking HIAS Blockhain Account Failed!"
				];
			endif;

			$aid = filter_input(INPUT_GET, 'application', FILTER_SANITIZE_NUMBER_INT);
			$application = $this->getApplication($aid);

			if($application["context"]["Data"]["cancelled"]["value"]):
				return [
					"Response"=> "Failed",
					"Message" => "This application is cancelled, to allow access again you must create a new application."
				];
			endif;

			$lid = filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT);
			$location = $this->getLocation($lid);

			$identifier = filter_input(INPUT_POST, "identifier", FILTER_SANITIZE_STRING);
			$ip = filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING);
			$mac = filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING);
			$bluetooth = filter_input(INPUT_POST, "bluetooth", FILTER_SANITIZE_STRING);
			$name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
			$coords = explode(",", filter_input(INPUT_POST, "coordinates", FILTER_SANITIZE_STRING));
			$allowed = filter_input(INPUT_POST, "cancelled", FILTER_SANITIZE_NUMBER_INT) ? False : True;
			$admin = filter_input(INPUT_POST, "admin", FILTER_SANITIZE_NUMBER_INT) ? True : False;

			$protocols = [];
			foreach($_POST["protocols"] AS $key => $value):
				$protocols[] = $value;
			endforeach;

			$models = [];
			if(isSet($_POST["ai"])):
				foreach($_POST["ai"] AS $key => $value):
					$model = $this->getModel($value)["context"]["Data"];
					$mname = $model["name"]["value"];
					unset($model["id"]);
					unset($model["type"]);
					unset($model["mid"]);
					unset($model["name"]);
					unset($model["description"]);
					unset($model["network"]);
					unset($model["language"]);
					unset($model["framework"]);
					unset($model["toolkit"]);
					unset($model["dateCreated"]);
					unset($model["dateModified"]);
					$models[$mname] = $model;
				endforeach;
			endif;

			$sensors = [];
			if(isSet($_POST["sensors"])):
				foreach($_POST["sensors"] AS $key => $value):
					$sensor = $this->getThing($value)["context"]["Data"];
					unset($sensor["id"]);
					unset($sensor["type"]);
					unset($sensor["category"]);
					unset($sensor["description"]);
					unset($sensor["thing"]);
					unset($sensor["properties"]["image"]);
					unset($sensor["dateCreated"]);
					unset($sensor["dateModified"]);
					$sensors[] = $sensor;
				endforeach;
			endif;

			$actuators = [];
			if(isSet($_POST["actuators"])):
				foreach($_POST["actuators"] AS $key => $value):
					$actuator = $this->getThing($value)["context"]["Data"];
					unset($actuator["id"]);
					unset($actuator["type"]);
					unset($actuator["category"]);
					unset($actuator["description"]);
					unset($actuator["thing"]);
					unset($actuator["properties"]["image"]);
					unset($actuator["dateCreated"]);
					unset($actuator["dateModeified"]);
					$actuators[] = $actuator;
				endforeach;
			endif;

			if($application["context"]["Data"]["lid"]["value"] != $lid):
				$query = $this->_GeniSys->_secCon->prepare("
					UPDATE mqttu
					SET lid = :lid
					WHERE aid = :aid
				");
				$query->execute([
					':lid' => $lid,
					':aid' => $aid
				]);
				$pdoQuery->closeCursor();
				$pdoQuery = null;

				$query = $this->_GeniSys->_secCon->prepare("
					UPDATE mqttua
					SET lid = :lid
					WHERE aid = :aid
				");
				$query->execute([
					':lid' => $lid,
					':aid' => $aid
				]);
				$pdoQuery->closeCursor();
				$pdoQuery = null;

				$query = $this->_GeniSys->_secCon->prepare("
					UPDATE mqttua
					SET topic = :topicN
					WHERE aid = :aid
						& topic = :topic
				");
				$query->execute([
					':topic' => $application["context"]["Data"]["lid"]["value"] . "/Devices/#",
					':aid' => $aid,
					':topic' => $location["context"]["Data"]["id"] . "/Devices/#"
				]);
				$pdoQuery->closeCursor();
				$pdoQuery = null;

				$query = $this->_GeniSys->_secCon->prepare("
					UPDATE mqttua
					SET topic = :topicN
					WHERE aid = :aid
						& topic = :topic
				");
				$query->execute([
					':topic' => $application["context"]["Data"]["lid"]["value"] . "/Applications/#",
					':aid' => $aid,
					':topic' => $location["context"]["Data"]["id"] . "/Applications/#"
				]);
				$pdoQuery->closeCursor();
				$pdoQuery = null;
			endif;

			$data = [
				"type" => "Application",
				"category" => [
					"value" => [filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)]
				],
				"name" => [
					"value" => $name
				],
				"description" => [
					"value" => filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)
				],
				"lid" => [
					"value" => $lid,
					"entity" => $location["context"]["Data"]["id"]
				],
				"aid" => [
					"value" => $aid
				],
				"admin" => [
					"value" => filter_input(INPUT_POST, "admin", FILTER_SANITIZE_NUMBER_INT) ? filter_input(INPUT_POST, "admin", FILTER_SANITIZE_NUMBER_INT) : 0
				],
				"patients" => [
					"value" => filter_input(INPUT_POST, "patients", FILTER_SANITIZE_NUMBER_INT) ? filter_input(INPUT_POST, "patients", FILTER_SANITIZE_NUMBER_INT) : 0
				],
				"cancelled" => [
					"value" => filter_input(INPUT_POST, "cancelled", FILTER_SANITIZE_NUMBER_INT) ? filter_input(INPUT_POST, "cancelled", FILTER_SANITIZE_NUMBER_INT) : 0
				],
				"location" => [
					"type" => "geo:json",
					"value" => [
						"type" => "Point",
						"coordinates" => [floatval($coords[0]), floatval($coords[1])]
					]
				],
				"agent" => [
					"url" => filter_input(INPUT_POST, "agent", FILTER_SANITIZE_STRING)
				],
				"device" => [
					"name" => filter_input(INPUT_POST, "deviceName", FILTER_SANITIZE_STRING),
					"manufacturer" => filter_input(INPUT_POST, "deviceManufacturer", FILTER_SANITIZE_STRING),
					"model" => filter_input(INPUT_POST, "deviceModel", FILTER_SANITIZE_STRING)
				],
				"os" => [
					"name" => filter_input(INPUT_POST, "osName", FILTER_SANITIZE_STRING),
					"manufacturer" => filter_input(INPUT_POST, "osManufacturer", FILTER_SANITIZE_STRING),
					"version" => filter_input(INPUT_POST, "osVersion", FILTER_SANITIZE_STRING)
				],
				"protocols" => $protocols,
				"status" => [
					"value" => $application["context"]["Data"]["status"]["value"],
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"ip" => [
					"value" => $ip,
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"mac" => [
					"value" => $this->_GeniSys->_helpers->oEncrypt($mac),
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"bluetooth" => [
					"address" => $bluetooth ? $this->_GeniSys->_helpers->oEncrypt($bluetooth) : "",
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"ai" => $models,
				"sensors" => $sensors,
				"actuators" => $actuators,
				"dateModified" => [
					"type" => "DateTime",
					"value" => date('Y-m-d\TH:i:s.Z\Z', time())
				]
			];

			$response = json_decode($this->contextBrokerRequest("PATCH", $this->cb["entities_url"] . "/" . $application["context"]["Data"]["id"] . "/attrs?type=Application", $this->createContextHeaders(), json_encode($data)), true);

			if($response["Response"]=="OK"):

				$unlocked =  $this->unlockBlockchainAccount();

				if($unlocked == "FAILED"):
					return [
						"Response"=> "Failed",
						"Message" => "Unlocking HIAS Blockhain Account Failed!"
					];
				endif;

				$hash = "";
				$msg = "";
				$actionMsg = "";
				$balanceMessage = "";

				$this->contract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["contract"]))->send("updateApplication", $application["context"]["Data"]["id"], "Application", $allowed, $admin, $lid, $name, $application["context"]["Data"]["status"]["value"], time(), ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
					if ($err !== null) {
						$hash = "FAILED";
						$msg = $err;
						return;
					}
					$hash = $resp;
				});

				if($hash == "FAILED"):
					$actionMsg = " HIAS Blockchain updateApplication failed! " . $msg;
				else:
					$txid = $this->storeBlockchainTransaction("Update Application", $hash, 0, $aid);
					$this->storeUserHistory("Update Application", $txid, $lid, 0, 0, 0, $aid);
					$balance = $this->getBlockchainBalance();
					$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!";
				endif;

				if(!$application["context"]["Data"]["patients"]["value"] && filter_input(INPUT_POST, "patients", FILTER_SANITIZE_STRING)):

					$this->pcontract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["pcontract"]))->send("registerUser", $application["context"]["Data"]["blockchain"]["address"], ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
						if ($err !== null) {
							$hash = "FAILED";
							$msg = $err;
							return;
						}
						$hash = $resp;
					});

					if($hash == "FAILED"):
						$actionMsg .= " HIAS Blockchain patients registerUser failed!\n";
					else:
						$txid = $this->storeBlockchainTransaction("Patients Register Application", $hash, 0, $aid);
						$this->storeUserHistory("Patients Register Application", $txid, $lid, 0, 0, 0, $aid);
						$balance = $this->getBlockchainBalance();
						if($balanceMessage == ""):
							$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
						endif;
					endif;

				endif;

				if($application["context"]["Data"]["patients"]["value"] && !filter_input(INPUT_POST, "patients", FILTER_SANITIZE_STRING)):

					$this->pcontract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["pcontract"]))->send("deregisterUser", $application["context"]["Data"]["blockchain"]["address"], ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
						if ($err !== null) {
							$hash = "FAILED";
							$msg = $err;
							return;
						}
						$hash = $resp;
					});

					if($hash == "FAILED"):
						$actionMsg .= " HIAS Blockchain patients deregisterUser failed!\n";
					else:
						$txid = $this->storeBlockchainTransaction("Patients Deregister Application", $hash, 0, $aid);
						$this->storeUserHistory("Patients Deregister Application", $txid, $lid, 0, 0, 0, $aid);
						$balance = $this->getBlockchainBalance();
						if($balanceMessage == ""):
							$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
						endif;
					endif;

				endif;

				if(!$application["context"]["Data"]["cancelled"]["value"] && filter_input(INPUT_POST, "cancelled", FILTER_SANITIZE_STRING)):

					$query = $this->_GeniSys->_secCon->prepare("
						DELETE FROM mqttu
						WHERE aid = :aid
					");
					$query->execute([
						':aid' => $aid
					]);

					$query = $this->_GeniSys->_secCon->prepare("
						DELETE FROM mqttua
						WHERE aid = :aid
					");
					$query->execute([
						':aid' => $aid
					]);

					$query = $this->_GeniSys->_secCon->prepare("
						SELECT *
						FROM amqpu
						WHERE username = :username
					");
					$query->execute([
						':username' => $this->_GeniSys->_helpers->oDecrypt($application["context"]["Data"]["amqp"]["username"])
					]);
					$amqp=$query->fetch(PDO::FETCH_ASSOC);

					$query = $this->_GeniSys->_secCon->prepare("
						DELETE FROM amqpu
						WHERE username = :username
					");
					$query->execute([
						':username' => $this->_GeniSys->_helpers->oDecrypt($application["context"]["Data"]["amqp"]["username"])
					]);

					$query = $this->_GeniSys->_secCon->prepare("
						DELETE FROM amqpp
						WHERE uid = :uid
					");
					$query->execute([
						':uid' => $amqp["id"]
					]);

					$query = $this->_GeniSys->_secCon->prepare("
						DELETE FROM amqpvh
						WHERE uid = :uid
					");
					$query->execute([
						':uid' => $amqp["id"]
					]);

					$query = $this->_GeniSys->_secCon->prepare("
						DELETE FROM amqpvhr
						WHERE uid = :uid
					");
					$query->execute([
						':uid' => $amqp["id"]
					]);

					$query = $this->_GeniSys->_secCon->prepare("
						DELETE FROM amqpvhrt
						WHERE uid = :uid
					");
					$query->execute([
						':uid' => $amqp["id"]
					]);

					$this->contract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["contract"]))->send("deregsiter", "Application", $application["context"]["Data"]["id"], ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash) {
						if ($err !== null) {
							$hash = "FAILED! " . $err;
							return;
						}
						$hash = $resp;
					});

					if($hash == "FAILED"):
						$actionMsg .= " HIAS Blockchain deregsiter user application failed!\n";
					else:
						$txid = $this->storeBlockchainTransaction("Deregister Application", $hash, 0, $aid);
						$this->storeUserHistory("Deregister Application", $txid, $lid, 0, 0, 0, $aid);
						$balance = $this->getBlockchainBalance();
						if($balanceMessage == ""):
							$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
						endif;
					endif;

					$this->icontract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["icontract"]))->send("deregisterAuthorized", $application["context"]["Data"]["blockchain"]["address"], ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
						if ($err !== null) {
							$hash = "FAILED";
							$msg = $err;
							return;
						}
						$hash = $resp;
					});

					if($hash == "FAILED"):
						$actionMsg .= " HIAS Blockchain deregisterAuthorized failed!\n";
					else:
						$txid = $this->storeBlockchainTransaction("Deregister Authorized", $hash, 0, $aid);
						$this->storeUserHistory("Deregister Authorized", $txid, $lid, 0, 0, 0, $aid);
						$balance = $this->getBlockchainBalance();
						if($balanceMessage == ""):
							$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
						endif;
					endif;

					$this->pcontract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["pcontract"]))->send("deregisterUser", $application["context"]["Data"]["blockchain"]["address"], ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
						if ($err !== null) {
							$hash = "FAILED";
							$msg = $err;
							return;
						}
						$hash = $resp;
					});

					if($hash == "FAILED"):
						$actionMsg .= " HIAS Blockchain patients deregisterAuthorized failed!\n";
					else:
						$txid = $this->storeBlockchainTransaction("Patients Deregister Authorized", $hash, 0, $aid);
						$this->storeUserHistory("Patients Deregister Authorized", $txid, $lid, 0, 0, 0, $aid);
						$balance = $this->getBlockchainBalance();
						if($balanceMessage == ""):
							$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
						endif;
					endif;

				endif;

				$application = $this->getApplication($aid);

				return [
					"Response"=> "OK",
					"Message" => "Application updated!" . $actionMsg . $balanceMessage,
					"Schema" => $application["context"]["Data"]
				];
			else:
				return [
					"Response"=> "FAILED",
					"Message" => "Application update failed!"
				];
			endif;
		}

		public function resetAppMqtt()
		{
			$id = filter_input(INPUT_GET, 'application', FILTER_SANITIZE_NUMBER_INT);
			$Application = $this->getApplication($id);

			$mqttPass = $this->_GeniSys->_helpers->password();
			$mqttHash = create_hash($mqttPass);

			$data = [
				"mqtt" => [
					"username" => $Application["context"]["Data"]["mqtt"]["username"],
					"password" => $this->_GeniSys->_helpers->oEncrypt($mqttPass),
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"dateModified" => [
					"type" => "DateTime",
					"value" => date('Y-m-d\TH:i:s.Z\Z', time())
				]
			];

			$response = json_decode($this->contextBrokerRequest("PATCH", $this->cb["entities_url"] . "/" . $Application["context"]["Data"]["id"] . "/attrs?type=Application", $this->createContextHeaders(), json_encode($data)), true);

			if($response["Response"]=="OK"):
				$query = $this->_GeniSys->_secCon->prepare("
					UPDATE mqttu
					SET pw = :pw
					WHERE aid = :aid
				");
				$query->execute(array(
					':pw' => $mqttHash,
					':aid' => $id
				));

				$this->storeUserHistory("Reset Application MQTT Password", 0, $Application["context"]["Data"]["lid"]["value"], 0, 0, 0, $id);

				return [
					"Response"=> "OK",
					"Message" => "MQTT password reset!",
					"P" => $mqttPass
				];
			else:
				return [
					"Response"=> "FAILED",
					"Message" => "MQTT password reset failed!"
				];
			endif;
		}

		public function resetAppAmqpKey()
		{
			$id = filter_input(INPUT_GET, 'application', FILTER_SANITIZE_NUMBER_INT);
			$Application = $this->getApplication($id);

			$amqpPass = $this->_GeniSys->_helpers->password();
			$amqpHash = $this->_GeniSys->_helpers->createPasswordHash($amqpPass);

			$data = [
				"amqp" => [
					"username" => $Application["context"]["Data"]["amqp"]["username"],
					"password" => $this->_GeniSys->_helpers->oEncrypt($amqpPass),
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"dateModified" => [
					"type" => "DateTime",
					"value" => date('Y-m-d\TH:i:s.Z\Z', time())
				]
			];

			$response = json_decode($this->contextBrokerRequest("PATCH", $this->cb["entities_url"] . "/" . $Application["context"]["Data"]["id"] . "/attrs?type=Application", $this->createContextHeaders(), json_encode($data)), true);

			if($response["Response"]=="OK"):
				$query = $this->_GeniSys->_secCon->prepare("
					UPDATE amqpu
					SET pw = :pw
					WHERE username = :username
				");
				$query->execute(array(
					':pw' => $this->_GeniSys->_helpers->oEncrypt($amqpHash),
					':username' => $this->_GeniSys->_helpers->oDecrypt($Application["context"]["Data"]["amqp"]["username"])
				));

				$this->storeUserHistory("Reset Application AMQP Password", 0, $Application["context"]["Data"]["lid"]["value"], 0, 0, 0, $id);

				return [
					"Response"=> "OK",
					"Message" => "AMQP password reset!",
					"P" => $amqpPass
				];
			else:
				return [
					"Response"=> "FAILED",
					"Message" => "AMQP password reset failed!"
				];
			endif;
		}

		public function resetAppKey()
		{
			$id = filter_input(INPUT_GET, 'application', FILTER_SANITIZE_NUMBER_INT);
			$Application = $this->getApplication($id);

			$privKey = $this->_GeniSys->_helpers->generateKey(32);
			$privKeyHash = $this->_GeniSys->_helpers->createPasswordHash($privKey);

			$data = [
				"keys" => [
					"public" => $Application["context"]["Data"]["keys"]["public"],
					"private" => $this->_GeniSys->_helpers->oEncrypt($privKeyHash),
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"dateModified" => [
					"type" => "DateTime",
					"value" => date('Y-m-d\TH:i:s.Z\Z', time())
				]
			];

			$response = json_decode($this->contextBrokerRequest("PATCH", $this->cb["entities_url"] . "/" . $Application["context"]["Data"]["id"] . "/attrs?type=Application", $this->createContextHeaders(), json_encode($data)), true);

			if($response["Response"]=="OK"):
				$this->storeUserHistory("Update Application Key", 0, $Application["context"]["Data"]["lid"]["value"], 0, 0, 0, $id);
				return [
					"Response"=> "OK",
					"Message" => "Application key reset!",
					"P" => $privKey
				];
			else:
				return [
					"Response"=> "FAILED",
					"Message" => "Application key reset failed!"
				];
			endif;
		}

		public function retrieveApplicationTransactions($application, $limit = 0, $order = "")
		{
			if($order):
				$orderer = "ORDER BY " . $order;
			else:
				$orderer = "ORDER BY id DESC";
			endif;

			if($limit):
				$limiter = "LIMIT " . $limit;
			endif;

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT *
				FROM transactions
				WHERE aid = :id
				$orderer
				$limiter
			");
			$pdoQuery->execute([
				":id" => $application
			]);
			$response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			return $response;
		}

		public function retrieveApplicationTransaction($txn)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT id,
					action,
					hash
				FROM transactions
				WHERE id = :id
			");
			$pdoQuery->execute([
				":id" => $txn
			]);
			$response=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			return $response;
		}

		public function retrieveApplicationTransactionReceipt($hash)
		{
			$dreceipt = "";
			$msg = "";
			$eth = $this->web3->eth;
			$eth->getTransactionReceipt($hash, function ($err, $receipt) use (&$dreceipt) {
				if ($err !== null) {
					$dreceipt = "FAILED";
					$msg = $err;
					return;
				}
				$dreceipt = $receipt;
			});

			if($dreceipt == "FAIL"):
				return [
					"Response" => "FAILED",
					"Message" => "Fetch Transaction Failed. " . $msg
				];
			else:
				return [
					"Response" => "OK",
					"Message" => "Fetch Transaction OK. ",
					"Receipt" => $dreceipt
				];
			endif;

		}

		public function retrieveApplicationHistory($application, $limit = 0, $order = "")
		{
			if($order):
				$orderer = "ORDER BY " . $order;
			else:
				$orderer = "ORDER BY id DESC";
			endif;

			if($limit):
				$limiter = "LIMIT " . $limit;
			endif;

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT *
				FROM history
				WHERE taid = :id
				$orderer
				$limiter
			");
			$pdoQuery->execute([
				":id" => $application
			]);
			$response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			return $response;
		}

		public function retrieveApplicationStatuses($application, $limit = 0, $order = -1)
		{
			$mngConn = new MongoDB\Driver\Manager("mongodb://localhost:27017/".$this->_GeniSys->_mdbname.'', ["username" => $this->_GeniSys->_mdbusername, "password" => $this->_GeniSys->_mdbpassword]);
			$query = new MongoDB\Driver\Query(['Application' => strval($application)], ['limit' => $limit, 'sort' => ['Time' => $order]]);
			$rows = $mngConn->executeQuery($this->_GeniSys->_mdbname.".Statuses", $query);

			$mngoData = [];

			foreach ($rows as $document):
				$mngoData[]=$document;
			endforeach;

			if(count($mngoData)):
				return  [
					'Response'=>'OK',
					'ResponseData' => $mngoData
				];
			else:
				return  [
					'Response'=>'FAILED'
				];
			endif;
		}

		public function retrieveApplicationLife($application, $limit = 0, $order = -1)
		{
			$mngConn = new MongoDB\Driver\Manager("mongodb://localhost:27017/".$this->_GeniSys->_mdbname.'', ["username" => $this->_GeniSys->_mdbusername, "password" => $this->_GeniSys->_mdbpassword]);
			$query = new MongoDB\Driver\Query(['Application' => strval($application)], ['limit' => $limit, 'sort' => ['Time' => $order]]);
			$rows = $mngConn->executeQuery($this->_GeniSys->_mdbname.".Life", $query);

			$mngoData = [];

			foreach ($rows as $document):
				$mngoData[]=$document;
			endforeach;

			if(count($mngoData)):
				return  [
					'Response'=>'OK',
					'ResponseData' => $mngoData
				];
			else:
				return  [
					'Response'=>'FAILED'
				];
			endif;
		}

		public function retrieveApplicationCommands($application, $limit = 0, $order = -1)
		{
			$mngConn = new MongoDB\Driver\Manager("mongodb://localhost:27017/".$this->_GeniSys->_mdbname.'', ["username" => $this->_GeniSys->_mdbusername, "password" => $this->_GeniSys->_mdbpassword]);
			$query = new MongoDB\Driver\Query(['Application' => strval($application)], ['limit' => $limit, 'sort' => ['Time' => $order]]);
			$rows = $mngConn->executeQuery($this->_GeniSys->_mdbname.".Commands", $query);

			$mngoData = [];

			foreach ($rows as $document):
				$mngoData[]=$document;
			endforeach;

			if(count($mngoData)):
				return  [
					'Response'=>'OK',
					'ResponseData' => $mngoData
				];
			else:
				return  [
					'Response'=>'FAILED'
				];
			endif;
		}

		public function retrieveApplicationSensors($application, $limit = 0, $order = -1)
		{
			$mngConn = new MongoDB\Driver\Manager("mongodb://localhost:27017/".$this->_GeniSys->_mdbname.'', ["username" => $this->_GeniSys->_mdbusername, "password" => $this->_GeniSys->_mdbpassword]);
			$query = new MongoDB\Driver\Query(['Application' => strval($application)], ['limit' => $limit, 'sort' => ['Time' => $order]]);
			$rows = $mngConn->executeQuery($this->_GeniSys->_mdbname.".Sensors", $query);

			$mngoData = [];

			foreach ($rows as $document):
				$mngoData[]=$document;
			endforeach;

			if(count($mngoData)):
				return  [
					'Response'=>'OK',
					'ResponseData' => $mngoData
				];
			else:
				return  [
					'Response'=>'FAILED'
				];
			endif;
		}

		public function retrieveStatuses($limit = 0, $order = -1)
		{
			$mngConn = new MongoDB\Driver\Manager("mongodb://localhost:27017/".$this->_GeniSys->_mdbname.'', ["username" => $this->_GeniSys->_mdbusername, "password" => $this->_GeniSys->_mdbpassword]);
			$query = new MongoDB\Driver\Query([], ['limit' => $limit, 'sort' => ['Time' => $order]]);

			$rows = $mngConn->executeQuery($this->_GeniSys->_mdbname.".Statuses", $query);

			$mngoData = [];

			foreach ($rows as $document):
				$mngoData[]=$document;
			endforeach;

			if(count($mngoData)):
				return  [
					'Response'=>'OK',
					'ResponseData'=>$mngoData
				];
			else:
				return  [
					'Response'=>'FAILED'
				];
			endif;

		}

		public function retrieveCommands($params=[])
		{
			$mngConn = new MongoDB\Driver\Manager("mongodb://localhost:27017/".$this->_GeniSys->_mdbname.'', ["username" => $this->_GeniSys->_mdbusername, "password" => $this->_GeniSys->_mdbpassword]);
			$query = new MongoDB\Driver\Query([], ['limit' => 5, 'sort' => ['Time' => -1]]);

			$rows = $mngConn->executeQuery($this->_GeniSys->_mdbname.".Commands", $query);

			$mngoData = [];

			foreach ($rows as $document):
				$mngoData[]=$document;
			endforeach;

			if(count($mngoData)):
				return  [
					'Response'=>'OK',
					'ResponseData'=>$mngoData
				];
			else:
				return  [
					'Response'=>'FAILED'
				];
			endif;

		}

		public function retrieveSensors($params=[])
		{
			$mngConn = new MongoDB\Driver\Manager("mongodb://localhost:27017/".$this->_GeniSys->_mdbname.'', ["username" => $this->_GeniSys->_mdbusername, "password" => $this->_GeniSys->_mdbpassword]);
			$query = new MongoDB\Driver\Query([], ['limit' => 5, 'sort' => ['Time' => -1]]);

			$rows = $mngConn->executeQuery($this->_GeniSys->_mdbname.".Sensors", $query);

			$mngoData = [];

			foreach ($rows as $document):
				$mngoData[]=$document;
			endforeach;

			if(count($mngoData)):
				return  [
					'Response'=>'OK',
					'ResponseData'=>$mngoData
				];
			else:
				return  [
					'Response'=>'FAILED'
				];
			endif;

		}

		public function retrieveLife($limit = 0, $order = -1)
		{
			$mngConn = new MongoDB\Driver\Manager("mongodb://localhost:27017/".$this->_GeniSys->_mdbname.'', ["username" => $this->_GeniSys->_mdbusername, "password" => $this->_GeniSys->_mdbpassword]);
			$query = new MongoDB\Driver\Query([], ['limit' => $limit, 'sort' => ['Time' => $order]]);

			$rows = $mngConn->executeQuery($this->_GeniSys->_mdbname.".Life", $query);

			$mngoData = [];

			foreach ($rows as $document):
				$mngoData[]=$document;
			endforeach;

			if(count($mngoData)):
				return  [
					'Response'=>'OK',
					'ResponseData'=>$mngoData
				];
			else:
				return  [
					'Response'=>'FAILED'
				];
			endif;

		}

		public function retrieveActuators($params=[])
		{
			$mngConn = new MongoDB\Driver\Manager("mongodb://localhost:27017/".$this->_GeniSys->_mdbname.'', ["username" => $this->_GeniSys->_mdbusername, "password" => $this->_GeniSys->_mdbpassword]);
			$query = new MongoDB\Driver\Query([], ['limit' => 5, 'sort' => ['Time' => -1]]);

			$rows = $mngConn->executeQuery($this->_GeniSys->_mdbname.".Actuators", $query);

			$mngoData = [];

			foreach ($rows as $document):
				$mngoData[]=$document;
			endforeach;

			if(count($mngoData)):
				return  [
					'Response'=>'OK',
					'ResponseData'=>$mngoData
				];
			else:
				return  [
					'Response'=>'FAILED'
				];
			endif;

		}

		public function getALife()
		{
			$Application = $this->getApplication(filter_input(INPUT_GET, 'application', FILTER_SANITIZE_NUMBER_INT), "batteryLevel,cpuUsage,memoryUsage,hddUsage,temperature,status");

			if($Application["context"]["Response"]=="OK"):
				$response = [
					"battery" => $Application["context"]["Data"]["batteryLevel"]["value"],
					"cpu" => $Application["context"]["Data"]["cpuUsage"]["value"],
					"mem" => $Application["context"]["Data"]["memoryUsage"]["value"],
					"hdd" => $Application["context"]["Data"]["hddUsage"]["value"],
					"tempr" => $Application["context"]["Data"]["temperature"]["value"],
					"status" => $Application["context"]["Data"]["status"]["value"]
				];
				return  [
					'Response' => 'OK',
					'ResponseData' => $response
				];
			else:
				return  [
					'Response'=>'FAILED'
				];
			endif;
		}

		public function getSLife()
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT id,
					cpu,
					mem,
					hdd,
					tempr,
					status
				FROM mqtta
				WHERE id = :id
			");
			$pdoQuery->execute([
				":id" => filter_input(INPUT_POST, "application", FILTER_SANITIZE_NUMBER_INT)
			]);
			$response=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			if($response["id"]):
				return  [
					'Response'=>'OK',
					'ResponseData'=>$response
				];
			else:
				return  [
					'Response'=>'FAILED'
				];
			endif;
		}

		public function getStatusShow($status)
		{
			if($status=="ONLINE"):
				$on = "  ";
				$off = " hide ";
			else:
				$on = " hide ";
				$off = "  ";
			endif;

			return [$on, $off];
		}

	}

	$iotJumpWay = new iotJumpWay($_GeniSys);

	if(filter_input(INPUT_POST, "update_location", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($iotJumpWay->update()));
	endif;
	if(filter_input(INPUT_POST, "create_zone", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($iotJumpWay->createZone()));
	endif;
	if(filter_input(INPUT_POST, "update_zone", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($iotJumpWay->updateZone()));
	endif;
	if(filter_input(INPUT_POST, "create_device", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($iotJumpWay->createDevice()));
	endif;
	if(filter_input(INPUT_POST, "update_device", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($iotJumpWay->updateDevice()));
	endif;
	if(filter_input(INPUT_POST, "create_application", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($iotJumpWay->createApplication()));
	endif;
	if(filter_input(INPUT_POST, "update_application", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($iotJumpWay->updateApplication()));
	endif;
	if(filter_input(INPUT_POST, "update_application", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($iotJumpWay->updateApplication()));
	endif;
	if(filter_input(INPUT_POST, "reset_mqtt_app", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($iotJumpWay->resetAppMqtt()));
	endif;
	if(filter_input(INPUT_POST, "reset_mqtt_dvc", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($iotJumpWay->resetDvcMqtt()));
	endif;
	if(filter_input(INPUT_POST, "reset_key_dvc", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($iotJumpWay->resetDvcKey()));
	endif;
	if(filter_input(INPUT_POST, "reset_app_apriv", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($iotJumpWay->resetAppKey()));
	endif;
	if(filter_input(INPUT_POST, "reset_app_amqp", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($iotJumpWay->resetAppAmqpKey()));
	endif;
	if(filter_input(INPUT_POST, "reset_dvc_amqp", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($iotJumpWay->resetDvcAmqpKey()));
	endif;
	if(filter_input(INPUT_POST, "get_life", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($iotJumpWay->getLife()));
	endif;
	if(filter_input(INPUT_POST, "get_alife", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($iotJumpWay->getALife()));
	endif;
	if(filter_input(INPUT_POST, "get_slife", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($iotJumpWay->getSLife()));
	endif;
