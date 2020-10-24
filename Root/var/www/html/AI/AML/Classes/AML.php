<?php

require __DIR__ . '/../../../../vendor/autoload.php';

use Web3\Web3;
use Web3\Contract;
use Web3\Utils;

	class AML
	{
		function __construct($_GeniSys)
		{
			if(isSet($_SESSION["GeniSysAI"]["Active"])):
				$this->_GeniSys = $_GeniSys;
				$this->bcc = $this->getBlockchainConf();
				$this->web3 = $this->blockchainConnection();
				$this->contract = new Contract($this->web3->provider, $this->bcc["abi"]);
				$this->icontract = new Contract($this->web3->provider, $this->bcc["iabi"]);
				$this->checkBlockchainPermissions();
			endif;
			$this->cb = $this->getContextBrokerConf();
		}

		public function setClassifierConfs()
		{
			$TId = filter_input(INPUT_GET, 'device', FILTER_SANITIZE_NUMBER_INT);
			$Device = $this->getDevice($TId);

			$this->dataDir = "Data/" . $Device["context"]["Data"]["dataset"]["folder"] . "/";
			$this->dataDirFull = "/fserver/var/www/html/AI/AML//";
			$this->dataFiles = $this->dataDir . "*.jpg";
			$this->amlowedFiles = ["jpg","JPG"];
			$this->api = $this->_GeniSys->_helpers->oDecrypt($this->_GeniSys->_confs["domainString"])."/AI/AML/" . $Device["context"]["Data"]["proxy"]["endpoint"] . "/Inference";
		}

		public function getContextBrokerConf()
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

		public function getBlockchainConf()
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT blockchain.*,
					contracts.contract,
					contracts.abi,
					icontracts.contract as icontract,
					icontracts.abi as iabi
				FROM blockchain blockchain
				INNER JOIN contracts contracts
				ON contracts.id = blockchain.dc
				INNER JOIN contracts icontracts
				ON icontracts.id = blockchain.ic
			");
			$pdoQuery->execute();
			$response=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $response;
		}

		private function blockchainConnection()
		{
			if(isSet($_SESSION["GeniSysAI"]["Active"])):
				$web3 = new Web3($this->_GeniSys->_helpers->oDecrypt($this->_GeniSys->_confs["domainString"]) . "/Blockchain/API/", 30, $_SESSION["GeniSysAI"]["User"], $this->_GeniSys->_helpers->oDecrypt($_SESSION["GeniSysAI"]["Pass"]));
				return $web3;
			endif;
		}

		private function checkBlockchainPermissions()
		{
			$amlowed = "";
			$errr = "";
			$this->contract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["contract"]))->call("identifierAllowed", "User", $_SESSION["GeniSysAI"]["Identifier"], ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$amlowed, &$errr) {
				if ($err !== null) {
					$amlowed = "FAILED";
					$errr = $err;
					return;
				}
				$amlowed = $resp;
			});
			if(!$amlowed):
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

		private function lockBlockchainAccount()
		{
			$response = "";
			$personal = $this->web3->personal;
			$personal->lockAccount($_SESSION["GeniSysAI"]["BC"]["BCUser"], function ($err, $unlocked) use (&$response) {
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

		public function checkLocation($lid)
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

		public function checkZone($zid)
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

		public function getDevices($limit = 0)
		{
			$limiter = "";
			if($limit != 0):
				$limiter = "&limit=" . $limit;
			endif;

			$devices = json_decode($this->contextBrokerRequest("GET", $this->cb["entities_url"] . "?type=Device&category=AMLClassifier".$limiter, $this->createContextHeaders(), []), true);
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
					"Message" => "iotJumpWay zone does not exist"
				];
			endif;

			if(!filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Category is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "ctype", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Model type is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "agent", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "IoT Agent is required"
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

			if(!filter_input(INPUT_POST, "coordinates", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Coordinates entity is required"
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

			if(!count($_POST["protocols"])):
				return [
					"Response"=> "Failed",
					"Message" => "At least one M2M protocol is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "datasetUsed", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Dataset used is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "datasetLink", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Dataset link is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "datasetAuthor", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Dataset author is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "datasetFolder", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Dataset folder is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "relatedPaper", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Related paper is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "sport", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Device server port is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "endpoint", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Proxy endpoint is required"
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

			mkdir("Data/" . filter_input(INPUT_POST, "datasetFolder", FILTER_SANITIZE_STRING), 0777, true);

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
					`apub`
				)  VALUES (
					:apub
				)
			");
			$query->execute([
				':apub' => $pubKey
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
				"paper" => [
					"title" => filter_input(INPUT_POST, "relatedPaper", FILTER_SANITIZE_STRING),
					"author" => filter_input(INPUT_POST, "relatedPaperAuthor", FILTER_SANITIZE_STRING),
					"doi" => filter_input(INPUT_POST, "relatedPaperDOI", FILTER_SANITIZE_STRING),
					"link" => filter_input(INPUT_POST, "relatedPaperLink", FILTER_SANITIZE_STRING)
				],
				"dataset" => [
					"name" => filter_input(INPUT_POST, "datasetUsed", FILTER_SANITIZE_STRING),
					"author" => filter_input(INPUT_POST, "datasetAuthor", FILTER_SANITIZE_STRING),
					"url" => filter_input(INPUT_POST, "datasetLink", FILTER_SANITIZE_STRING),
					"folder" => filter_input(INPUT_POST, "datasetFolder", FILTER_SANITIZE_STRING)
				],
				"device" => [
					"type" => filter_input(INPUT_POST, "ctype", FILTER_SANITIZE_STRING),
					"name" => filter_input(INPUT_POST, "deviceName", FILTER_SANITIZE_STRING),
					"manufacturer" => filter_input(INPUT_POST, "deviceManufacturer", FILTER_SANITIZE_STRING),
					"model" => filter_input(INPUT_POST, "deviceModel", FILTER_SANITIZE_STRING)
				],
				"proxy" => [
					"endpoint" => filter_input(INPUT_POST, "endpoint", FILTER_SANITIZE_STRING)
				],
				"stream" => [
					"port" => filter_input(INPUT_POST, "sport", FILTER_SANITIZE_STRING),
					"file" => ""
				],
				"socket" => [
					"port" => ""
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
					"LID" => filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT),
					"ZID" => filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT),
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
					"Message" => "iotJumpWay zone does not exist"
				];
			endif;

			if(!filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Category is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "ctype", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Model type is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "agent", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "IoT Agent is required"
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

			if(!filter_input(INPUT_POST, "coordinates", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Coordinates entity is required"
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

			if(!count($_POST["protocols"])):
				return [
					"Response"=> "Failed",
					"Message" => "At least one M2M protocol is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "datasetUsed", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Dataset used is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "datasetLink", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Dataset link is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "datasetAuthor", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Dataset author is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "datasetFolder", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Dataset folder is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "relatedPaper", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Related paper is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "sport", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Device server port is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "endpoint", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Proxy endpoint is required"
				];
			endif;

			$unlocked =  $this->unlockBlockchainAccount();

			if($unlocked == "FAILED"):
				return [
					"Response"=> "Failed",
					"Message" => "Unlocking HIAS Blockhain Account Failed!"
				];
			endif;

			$ip = filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING);
			$mac = filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING);
			$bluetooth = filter_input(INPUT_POST, "bluetooth", FILTER_SANITIZE_STRING);
			$name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
			$status = filter_input(INPUT_POST, "status", FILTER_SANITIZE_STRING);
			$coords = explode(",", filter_input(INPUT_POST, "coordinates", FILTER_SANITIZE_STRING));

			$did = filter_input(INPUT_GET, "device", FILTER_SANITIZE_NUMBER_INT);
			$device = $this->getDevice($did);

			$lid = filter_input(INPUT_POST, 'lid', FILTER_SANITIZE_NUMBER_INT);
			$location = $this->getLocation($lid);

			$zid = filter_input(INPUT_POST, 'zid', FILTER_SANITIZE_NUMBER_INT);
			$zone = $this->getZone($zid);

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
				"paper" => [
					"title" => filter_input(INPUT_POST, "relatedPaper", FILTER_SANITIZE_STRING),
					"author" => filter_input(INPUT_POST, "relatedPaperAuthor", FILTER_SANITIZE_STRING),
					"doi" => filter_input(INPUT_POST, "relatedPaperDOI", FILTER_SANITIZE_STRING),
					"link" => filter_input(INPUT_POST, "relatedPaperLink", FILTER_SANITIZE_STRING)
				],
				"dataset" => [
					"name" => filter_input(INPUT_POST, "datasetUsed", FILTER_SANITIZE_STRING),
					"author" => filter_input(INPUT_POST, "datasetAuthor", FILTER_SANITIZE_STRING),
					"url" => filter_input(INPUT_POST, "datasetLink", FILTER_SANITIZE_STRING),
					"folder" => filter_input(INPUT_POST, "datasetFolder", FILTER_SANITIZE_STRING)
				],
				"device" => [
					"type" => filter_input(INPUT_POST, "ctype", FILTER_SANITIZE_STRING),
					"name" => filter_input(INPUT_POST, "deviceName", FILTER_SANITIZE_STRING),
					"manufacturer" => filter_input(INPUT_POST, "deviceManufacturer", FILTER_SANITIZE_STRING),
					"model" => filter_input(INPUT_POST, "deviceModel", FILTER_SANITIZE_STRING)
				],
				"proxy" => [
					"endpoint" => filter_input(INPUT_POST, "endpoint", FILTER_SANITIZE_STRING)
				],
				"stream" => [
					"port" => filter_input(INPUT_POST, "sport", FILTER_SANITIZE_STRING),
					"file" => ""
				],
				"socket" => [
					"port" => ""
				],
				"os" => [
					"name" => filter_input(INPUT_POST, "osName", FILTER_SANITIZE_STRING),
					"manufacturer" => filter_input(INPUT_POST, "osManufacturer", FILTER_SANITIZE_STRING),
					"version" => filter_input(INPUT_POST, "osVersion", FILTER_SANITIZE_STRING)
				],
				"protocols" => $protocols,
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

		public function deleteData()
		{
			$images = glob($this->dataFiles);
			foreach( $images as $image ):
				unlink($image);
			endforeach;

			return [
				"Response" => "OK",
				"Message" =>  "Deleted Acute Lymphoblastic Leukemia Image Database for Image Processing Dataset"
			];

		}

		public function uploadData()
		{
			$dataCells = '';
			if(is_array($_FILES) && !empty($_FILES['amldata'])):
				foreach($_FILES['amldata']['name'] as $key => $filename):
					$file_name = explode(".", $filename);
					if(in_array($file_name[1], $this->amlowedFiles)):
						$sourcePath = $_FILES["amldata"]["tmp_name"][$key];
						$targetPath = $this->dataDir . $filename;
						if(!move_uploaded_file($sourcePath, $targetPath)):
							return [
								"Response" => "FAILED",
								"Message" => "Upload failed " . $targetPath
							];
						endif;
					else:
						return [
							"Response" => "FAILED",
							"Message" => "Please upload jpg files"
						];
					endif;
				endforeach;

				$images = glob($this->dataFiles);
				$count = 1;
				foreach($images as $image):
					$dataCells .= "<div class='col-lg-2 col-md-2 col-sm-2 col-xs-2'><img src='../" . $image . "' style='width: 100%; cursor: pointer;' class='classify' title='" . $image . "' id='" . $image . "' /></div>";
					if($count%6 == 0):
						$dataCells .= "<div class='clearfix'></div>";
					endif;
					$count++;
				endforeach;

			else:
				return [
					"Response" => "FAILED",
					"Message" => "You must upload some images (jpg)"
				];
			endif;

			return [
				"Response" => "OK",
				"Message" => "Data upload OK!",
				"Data" => $dataCells
			];

		}

		public function classifyData()
		{
			$file = $this->dataDirFull . filter_input(INPUT_POST, "im", FILTER_SANITIZE_STRING);
			$mime = mime_content_type($file);
			$info = pathinfo($file);
			$name = $info['basename'];
			$toSend = new CURLFile($file, $mime, $name);

			$headers = [
				'Authorization: Basic '. base64_encode($_SESSION["GeniSysAI"]["User"] . ":" . $this->_GeniSys->_helpers->oDecrypt($_SESSION["GeniSysAI"]["Pass"]))
			];

			$ch = curl_init($this->api);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_POSTFIELDS, [
				'file'=> $toSend,
			]);

			$resp = curl_exec($ch);

			return json_decode($resp, true);

		}

	 }

	 $AML = new AML($_GeniSys);

	 if(filter_input(INPUT_POST, "create_aml_classifier", FILTER_SANITIZE_NUMBER_INT)):
		 die(json_encode($AML->createDevice()));
	 endif;

	 if(filter_input(INPUT_POST, "update_aml_classifier", FILTER_SANITIZE_NUMBER_INT)):
		 die(json_encode($AML->updateDevice()));
	 endif;

	 if(filter_input(INPUT_POST, "reset_mqtt", FILTER_SANITIZE_NUMBER_INT)):
		 die(json_encode($AML->resetMqtt()));
	 endif;

	if(filter_input(INPUT_POST, "reset_key", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($AML->resetDvcKey()));
	endif;

	if(filter_input(INPUT_POST, "get_tlife", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($AML->getLife()));
	endif;

	if(filter_input(INPUT_POST, "deleteData", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($AML->deleteData()));
	endif;

	if(filter_input(INPUT_POST, "uploadAllData", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($AML->uploadData()));
	endif;

	if(filter_input(INPUT_POST, "classifyData", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($AML->classifyData()));
	endif;
