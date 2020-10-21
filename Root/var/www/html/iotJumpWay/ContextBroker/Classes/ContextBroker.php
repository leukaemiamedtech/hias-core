<?php
require __DIR__ . '/../../../../vendor/autoload.php';

use Web3\Web3;
use Web3\Contract;
use Web3\Utils;

	class ContextBroker
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

		public function getBroker()
		{
			$broker = json_decode($this->contextBrokerRequest("GET", $this->cb["about_url"], $this->createContextHeaders(), []), true);
			return $broker;
		}

		private function storeBlockchainTransaction($action, $hash, $device = 0, $agent = 0)
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
				":aid" => $agent,
				":action" => $action,
				':hash' => $this->_GeniSys->_helpers->oEncrypt($hash),
				":time" => time()
			]);
			$txid = $this->_GeniSys->_secCon->lastInsertId();
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $txid;
		}

		private function storeUserHistory($action, $hash, $location = 0, $zone = 0, $device = 0, $sensor = 0, $agent = 0)
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
				":taid" => $agent,
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

		public function updateContextBroker()
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				UPDATE contextbroker
				SET hdsiv = :hdsiv,
					url = :url,
					local_ip = :local_ip,
					about_url = :about_url,
					entities_url = :entities_url,
					subscriptions_url = :subscriptions_url,
					registrations_url = :registrations_url,
					agents_url = :agents_url,
					commands_url = :commands_url
			");
			$pdoQuery->execute([
				":hdsiv" => filter_input(INPUT_POST, "hdsiv", FILTER_SANITIZE_STRING),
				":url" => filter_input(INPUT_POST, "url", FILTER_SANITIZE_STRING),
				":local_ip" => filter_input(INPUT_POST, "local_ip", FILTER_SANITIZE_STRING),
				":about_url" => filter_input(INPUT_POST, "about_url", FILTER_SANITIZE_STRING),
				":entities_url" => filter_input(INPUT_POST, "entities_url", FILTER_SANITIZE_STRING),
				":subscriptions_url" => filter_input(INPUT_POST, "subscriptions_url", FILTER_SANITIZE_STRING),
				":registrations_url" => filter_input(INPUT_POST, "registrations_url", FILTER_SANITIZE_STRING),
				":agents_url" => filter_input(INPUT_POST, "agents_url", FILTER_SANITIZE_STRING),
				":commands_url" => filter_input(INPUT_POST, "commands_url", FILTER_SANITIZE_STRING)
			]);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return [
				"Response"=> "OK",
				"Message" => "Context Broker updated!"
			];
		}

		public function getAgents($limit = 0)
		{
			$limiter = "";
			if($limit != 0):
				$limiter = "?limit=" . $limit;
			endif;

			$locations = json_decode($this->contextBrokerRequest("GET", $this->cb["agents_url"] . $limiter, $this->createContextHeaders(), []), true);
			return $locations;
		}

		public function getAgent($id, $attrs = Null)
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
			$agent=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			if($attrs):
				$attrs="?attrs=" . $attrs;
			endif;

			$agent["context"] = json_decode($this->contextBrokerRequest("GET", $this->cb["agents_url"] . "/" . $agent["apub"] . $attrs, $this->createContextHeaders(), []), true);
			return $agent;
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

		public function createAgent()
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

			if(!filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "IP is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "northPort", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "North Port is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "commands", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Commands endpoint is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "about", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "About endpoint is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "MAC is required"
				];
			endif;

			if(!count($_POST["protocols"])):
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

			$newBcUser = $this->createBlockchainUser($bcPass);

			if($newBcUser == "FAILED"):
				return [
					"Response"=> "Failed",
					"Message" => "Creating New HIAS Blockhain Account Failed!"
				];
			endif;

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
					"value" => ["IoT Agent"]
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
					"value" => 1
				],
				"patients" => [
					"value" => 0
				],
				"cancelled" => [
					"value" => 0
				],
				"location" => [
					"type" => "geo:json",
					"value" => [
						"type" => "Point",
						"coordinates" => [floatval($coords[0]), floatval($coords[1])]
					]
				],
				"agent" => [
					"url" => "Self"
				],
				"endpoints" => [
					"commands" => filter_input(INPUT_POST, "commands", FILTER_SANITIZE_STRING),
					"about" => filter_input(INPUT_POST, "about", FILTER_SANITIZE_STRING)
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
				"northPort" => [
					"value" => filter_input(INPUT_POST, "northPort", FILTER_SANITIZE_NUMBER_INT)
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

			$response = json_decode($this->contextBrokerRequest("POST", $this->cb["agents_url"], $this->createContextHeaders(), json_encode($data)), true);

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
					':topic' => $location["context"]["Data"]["lid"]["entity"] . "/Devices/#",
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
					':topic' => $location["context"]["Data"]["lid"]["entity"] . "/Applications/#",
					':rw' => 2
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

				$this->addAmqpUserPerm($amid, "administrator");
				$this->addAmqpUserPerm($amid, "managment");
				$this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Life", "configure");
				$this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Statuses", "configure");

				$unlocked =  $this->unlockBlockchainAccount();

				if($unlocked == "FAILED"):
					return [
						"Response"=> "Failed",
						"Message" => "Unlocking HIAS Blockhain Account Failed!"
					];
				endif;

				$hash = "";
				$msg = "";
				$this->contract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["contract"]))->send("registerApplication", $pubKey, $newBcUser, True, $lid, $aid, $name, $_SESSION["GeniSysAI"]["Uid"], time(), ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
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
					$txid = $this->storeBlockchainTransaction("Register IoT Agent", $hash, 0, $aid);
					$this->storeUserHistory("Register IoT Agent", $txid, $lid, 0, 0, 0, $aid);
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
					$txid = $this->storeBlockchainTransaction("iotJumpWay Register IoT Agent", $hash, 0, $aid);
					$this->storeUserHistory("iotJumpWay Register IoT Agent", $txid, $lid, 0, 0, 0, $aid);
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
						$txid = $this->storeBlockchainTransaction("Patients Register IoT Agent", $hash, 0, $aid);
						$this->storeUserHistory("Patients Register IoT Agent", $txid, $lid, 0, 0, 0, $aid);
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

		public function updateAgent()
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

			if(!filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "IP is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "northPort", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "North Port is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "commands", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Commands endpoint is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "about", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "About endpoint is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "MAC is required"
				];
			endif;

			if(!count($_POST["protocols"])):
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

			$aid = filter_input(INPUT_GET, 'agent', FILTER_SANITIZE_NUMBER_INT);
			$agent = $this->getAgent($aid);

			if($agent["context"]["Data"]["cancelled"]["value"]):
				return [
					"Response"=> "Failed",
					"Message" => "This agent is cancelled, to allow access again you must create a new agent."
				];
			endif;

			$lid = filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT);
			$location = $this->getLocation($lid);

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
					$models[] = $value;
				endforeach;
			endif;

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

			if($agent["context"]["Data"]["lid"]["value"] != $lid):
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
					':topicN' => $agent["context"]["Data"]["lid"]["entity"] . "/Devices/#",
					':aid' => $aid,
					':topic' => $location["context"]["Data"]["id"] . "/Devices/#",
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
					':topicN' => $agent["context"]["Data"]["lid"]["entity"] . "/Applications/#",
					':aid' => $aid,
					':topic' => $location["context"]["Data"]["id"] . "/Applications/#",
				]);
				$pdoQuery->closeCursor();
				$pdoQuery = null;
			endif;

			$data = [
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
					"url" => "Self"
				],
				"endpoints" => [
					"commands" => filter_input(INPUT_POST, "commands", FILTER_SANITIZE_STRING),
					"about" => filter_input(INPUT_POST, "about", FILTER_SANITIZE_STRING)
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
					"value" => $agent["context"]["Data"]["status"]["value"],
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"ip" => [
					"value" => $ip,
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"northPort" => [
					"value" => filter_input(INPUT_POST, "northPort", FILTER_SANITIZE_NUMBER_INT)
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

			$response = json_decode($this->contextBrokerRequest("PATCH", $this->cb["agents_url"] . "/" . $agent["context"]["Data"]["id"] . "/attrs", $this->createContextHeaders(), json_encode($data)), true);

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

				$this->contract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["contract"]))->send("updateApplication", $agent["context"]["Data"]["id"], "Application", $allowed, $admin, $lid, $name, $agent["context"]["Data"]["status"]["value"], time(), ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
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
					$txid = $this->storeBlockchainTransaction("Update IoT Agent", $hash, 0, $aid);
					$this->storeUserHistory("Update IoT Agent", $txid, $lid, 0, 0, 0, $aid);
					$balance = $this->getBlockchainBalance();
					$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!";
				endif;

				if(!$agent["context"]["Data"]["patients"]["value"] && filter_input(INPUT_POST, "patients", FILTER_SANITIZE_STRING)):

					$this->pcontract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["pcontract"]))->send("registerUser", $agent["context"]["Data"]["blockchain"]["address"], ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
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
						$txid = $this->storeBlockchainTransaction("Patients Register IoT Agent", $hash, 0, $aid);
						$this->storeUserHistory("Patients Register IoT Agent", $txid, $lid, 0, 0, 0, $aid);
						$balance = $this->getBlockchainBalance();
						if($balanceMessage == ""):
							$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
						endif;
					endif;

				endif;

				if($agent["context"]["Data"]["patients"]["value"] && !filter_input(INPUT_POST, "patients", FILTER_SANITIZE_STRING)):

					$this->pcontract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["pcontract"]))->send("deregisterUser", $agent["context"]["Data"]["blockchain"]["address"], ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
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
						$txid = $this->storeBlockchainTransaction("Patients Deregister IoT Agent", $hash, 0, $aid);
						$this->storeUserHistory("Patients Deregister IoT Agent", $txid, $lid, 0, 0, 0, $aid);
						$balance = $this->getBlockchainBalance();
						if($balanceMessage == ""):
							$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
						endif;
					endif;

				endif;

				if(!$agent["context"]["Data"]["cancelled"]["value"] && filter_input(INPUT_POST, "cancelled", FILTER_SANITIZE_STRING)):

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
						':username' => $this->_GeniSys->_helpers->oDecrypt($agent["context"]["Data"]["amqp"]["username"])
					]);
					$amqp=$query->fetch(PDO::FETCH_ASSOC);

					$query = $this->_GeniSys->_secCon->prepare("
						DELETE FROM amqpu
						WHERE username = :username
					");
					$query->execute([
						':username' => $this->_GeniSys->_helpers->oDecrypt($agent["context"]["Data"]["amqp"]["username"])
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

					$this->contract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["contract"]))->send("deregsiter", "Application", $agent["context"]["Data"]["id"], ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash) {
						if ($err !== null) {
							$hash = "FAILED! " . $err;
							return;
						}
						$hash = $resp;
					});

					if($hash == "FAILED"):
						$actionMsg .= " HIAS Blockchain deregsiter agent failed!\n";
					else:
						$txid = $this->storeBlockchainTransaction("Deregister IoT Agent", $hash, 0, $aid);
						$this->storeUserHistory("Deregister IoT Agent", $txid, $lid, 0, 0, 0, $aid);
						$balance = $this->getBlockchainBalance();
						if($balanceMessage == ""):
							$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
						endif;
					endif;

					$this->icontract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["icontract"]))->send("deregisterAuthorized", $agent["context"]["Data"]["blockchain"]["address"], ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
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
						$txid = $this->storeBlockchainTransaction("Deregister IoT Agent", $hash, 0, $aid);
						$this->storeUserHistory("Deregister IoT Agent", $txid, $lid, 0, 0, 0, $aid);
						$balance = $this->getBlockchainBalance();
						if($balanceMessage == ""):
							$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
						endif;
					endif;

					$this->pcontract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["pcontract"]))->send("deregisterUser", $agent["context"]["Data"]["blockchain"]["address"], ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
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
						$txid = $this->storeBlockchainTransaction("Patients Deregister IoT Agent", $hash, 0, $aid);
						$this->storeUserHistory("Patients Deregister IoT Agent", $txid, $lid, 0, 0, 0, $aid);
						$balance = $this->getBlockchainBalance();
						if($balanceMessage == ""):
							$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
						endif;
					endif;

				endif;

				$agent = $this->getAgent($aid);

				return [
					"Response"=> "OK",
					"Message" => "IoT Agent updated!" . $actionMsg . $balanceMessage,
					"Schema" => $agent["context"]["Data"]
				];
			else:
				return [
					"Response"=> "FAILED",
					"Message" => "IoT Agent update failed!"
				];
			endif;
		}

		public function resetAppMqtt()
		{
			$id = filter_input(INPUT_GET, 'agent', FILTER_SANITIZE_NUMBER_INT);
			$agent = $this->getAgent($id);

			$mqttPass = $this->_GeniSys->_helpers->password();
			$mqttHash = create_hash($mqttPass);

			$data = [
				"mqtt" => [
					"username" => $agent["context"]["Data"]["mqtt"]["username"],
					"password" => $this->_GeniSys->_helpers->oEncrypt($mqttPass),
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"dateModified" => [
					"type" => "DateTime",
					"value" => date('Y-m-d\TH:i:s.Z\Z', time())
				]
			];

			$response = json_decode($this->contextBrokerRequest("PATCH", $this->cb["agents_url"] . "/" . $agent["context"]["Data"]["id"] . "/attrs", $this->createContextHeaders(), json_encode($data)), true);

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

				$this->storeUserHistory("Reset Agent MQTT Password", 0, $agent["context"]["Data"]["lid"]["value"], 0, 0, 0, $id);

				return [
					"Response"=> "OK",
					"Message" => "Agent MQTT password reset!",
					"P" => $mqttPass
				];
			else:
				return [
					"Response"=> "FAILED",
					"Message" => "Agent MQTT password reset failed!"
				];
			endif;
		}

		public function resetAppAmqpKey()
		{
			$id = filter_input(INPUT_GET, 'agent', FILTER_SANITIZE_NUMBER_INT);
			$agent = $this->getAgent($id);

			$amqpPass = $this->_GeniSys->_helpers->password();
			$amqpHash = $this->_GeniSys->_helpers->createPasswordHash($amqpPass);

			$data = [
				"amqp" => [
					"username" => $agent["context"]["Data"]["amqp"]["username"],
					"password" => $this->_GeniSys->_helpers->oEncrypt($amqpPass),
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"dateModified" => [
					"type" => "DateTime",
					"value" => date('Y-m-d\TH:i:s.Z\Z', time())
				]
			];

			$response = json_decode($this->contextBrokerRequest("PATCH", $this->cb["agents_url"] . "/" . $agent["context"]["Data"]["id"] . "/attrs", $this->createContextHeaders(), json_encode($data)), true);

			if($response["Response"]=="OK"):
				$query = $this->_GeniSys->_secCon->prepare("
					UPDATE amqpu
					SET pw = :pw
					WHERE username = :username
				");
				$query->execute(array(
					':pw' => $this->_GeniSys->_helpers->oEncrypt($amqpHash),
					':username' => $this->_GeniSys->_helpers->oDecrypt($agent["context"]["Data"]["amqp"]["username"])
				));

				$this->storeUserHistory("Reset Agent AMQP Password", 0, $agent["context"]["Data"]["lid"]["value"], 0, 0, 0, $id);

				return [
					"Response"=> "OK",
					"Message" => "Agent AMQP password reset!",
					"P" => $amqpPass
				];
			else:
				return [
					"Response"=> "FAILED",
					"Message" => "Agent AMQP password reset failed!"
				];
			endif;
		}

		public function resetAppKey()
		{
			$id = filter_input(INPUT_GET, 'agent', FILTER_SANITIZE_NUMBER_INT);
			$agent = $this->getAgent($id);

			$privKey = $this->_GeniSys->_helpers->generateKey(32);
			$privKeyHash = $this->_GeniSys->_helpers->createPasswordHash($privKey);

			$data = [
				"keys" => [
					"public" => $agent["context"]["Data"]["keys"]["public"],
					"private" => $this->_GeniSys->_helpers->oEncrypt($privKeyHash),
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"dateModified" => [
					"type" => "DateTime",
					"value" => date('Y-m-d\TH:i:s.Z\Z', time())
				]
			];

			$response = json_decode($this->contextBrokerRequest("PATCH", $this->cb["agents_url"] . "/" . $agent["context"]["Data"]["id"] . "/attrs", $this->createContextHeaders(), json_encode($data)), true);

			if($response["Response"]=="OK"):
				$this->storeUserHistory("Update Agent Key", 0, $agent["context"]["Data"]["lid"]["value"], 0, 0, 0, $id);
				return [
					"Response"=> "OK",
					"Message" => "Agent key reset!",
					"P" => $privKey
				];
			else:
				return [
					"Response"=> "FAILED",
					"Message" => "Agent key reset failed!"
				];
			endif;
		}

		public function retrieveAgentTransactions($agent, $limit = 0, $order = "")
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
				":id" => $agent
			]);
			$response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			return $response;
		}

		public function retrieveAgentTransaction($txn)
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

		public function retrieveAgentTransactionReceipt($hash)
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

		public function retrieveAgentHistory($agent, $limit = 0, $order = "")
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
				":id" => $agent
			]);
			$response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			return $response;
		}

		public function retrieveAgentStatuses($agent, $limit = 0, $order = -1)
		{
			$mngConn = new MongoDB\Driver\Manager("mongodb://localhost:27017/".$this->_GeniSys->_mdbname.'', ["username" => $this->_GeniSys->_mdbusername, "password" => $this->_GeniSys->_mdbpassword]);
			$query = new MongoDB\Driver\Query(['Application' => strval($agent)], ['limit' => $limit, 'sort' => ['Time' => $order]]);
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

		public function retrieveAgentLife($agent, $limit = 0, $order = -1)
		{
			$mngConn = new MongoDB\Driver\Manager("mongodb://localhost:27017/".$this->_GeniSys->_mdbname.'', ["username" => $this->_GeniSys->_mdbusername, "password" => $this->_GeniSys->_mdbpassword]);
			$query = new MongoDB\Driver\Query(['Application' => strval($agent)], ['limit' => $limit, 'sort' => ['Time' => $order]]);
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

		public function retrieveAgentCommands($agent, $limit = 0, $order = -1)
		{
			$mngConn = new MongoDB\Driver\Manager("mongodb://localhost:27017/".$this->_GeniSys->_mdbname.'', ["username" => $this->_GeniSys->_mdbusername, "password" => $this->_GeniSys->_mdbpassword]);
			$query = new MongoDB\Driver\Query(['Application' => strval($agent)], ['limit' => $limit, 'sort' => ['Time' => $order]]);
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

		public function retrieveAgentSensors($agent, $limit = 0, $order = -1)
		{
			$mngConn = new MongoDB\Driver\Manager("mongodb://localhost:27017/".$this->_GeniSys->_mdbname.'', ["username" => $this->_GeniSys->_mdbusername, "password" => $this->_GeniSys->_mdbpassword]);
			$query = new MongoDB\Driver\Query(['Application' => strval($agent)], ['limit' => $limit, 'sort' => ['Time' => $order]]);
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

	}

	$ContextBroker = new ContextBroker($_GeniSys);

	if(filter_input(INPUT_POST, "update_cbroker", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($ContextBroker->updateContextBroker()));
	endif;
	if(filter_input(INPUT_POST, "create_agent", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($ContextBroker->createAgent()));
	endif;
	if(filter_input(INPUT_POST, "update_agent", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($ContextBroker->updateAgent()));
	endif;
	if(filter_input(INPUT_POST, "reset_agent_apriv", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($ContextBroker->resetAppKey()));
	endif;
	if(filter_input(INPUT_POST, "reset_agent_mqtt", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($ContextBroker->resetAppMqtt()));
	endif;
	if(filter_input(INPUT_POST, "reset_agent_amqp", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($ContextBroker->resetAppAmqpKey()));
	endif;