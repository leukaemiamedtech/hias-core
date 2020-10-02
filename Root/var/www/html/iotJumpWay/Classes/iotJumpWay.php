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
				$this->checkBlockchainPermissions();
			endif;
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

		public function getLocations($limit = 0, $order = "id DESC")
		{
			$limiter = "";
			if($limit != 0):
				$limiter = "LIMIT " . $limit;
			endif;

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT *
				FROM mqttl
				ORDER BY $order
				$limiter
			");
			$pdoQuery->execute();
			$response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $response;
		}

		public function getLocation($id)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT *
				FROM mqttl
				WHERE id = :id
			");
			$pdoQuery->execute([
				":id" => $id
			]);
			$response=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $response;
		}

		public function update()
		{
			if(!filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "ID is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Name is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Location IP is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Location MAC is required"
				];
			endif;

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				UPDATE mqttl
				SET name = :name,
					ip = :ip,
					mac = :mac
				WHERE id = :id
			");
			$pdoQuery->execute([
				":name" => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING),
				":ip" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING)),
				":mac" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING)),
				":id" => filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)
			]);
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			$this->storeUserHistory("Update Location", 0, filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT), 0, 0);

			return [
				"Response"=> "OK",
				"Message" => "Location updated!"
			];
		}

		public function getZones($limit = 0, $order = "id DESC")
		{
			$limiter = "";
			if($limit != 0):
				$limiter = "LIMIT " . $limit;
			endif;

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT zone.*,
					location.name as loc
				FROM mqttlz zone
				INNER JOIN mqttl location
				ON zone.lid = location.id
				ORDER BY $order
				$limiter
			");
			$pdoQuery->execute();
			$response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $response;
		}

		public function getZone($id)
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
			$response=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $response;
		}

		public function createZone()
		{
			if(!filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Name is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "Location ID is required"
				];
			endif;

			$lid = filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT);

			$query = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  mqttlz  (
					`lid`,
					`zn`,
					`time`
				)  VALUES (
					:lid,
					:zn,
					:time
				)
			");
			$query->execute([
				':lid' => $lid,
				':zn' => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING),
				':time' => time()
			]);
			$zid = $this->_GeniSys->_secCon->lastInsertId();

			$query = $this->_GeniSys->_secCon->prepare("
				UPDATE mqttl
				SET zones = zones + 1
				WHERE id = :id
			");
			$query->execute([
				':id' => $lid
			]);

			$this->storeUserHistory("Created Zone", $lid, $zid, 0);

			return [
				"Response"=> "OK",
				"Message" => "Zone created!",
				"LID" => filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT),
				"ZID" => $zid
			];
		}

		public function updateZone()
		{
			if(!filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "ID is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "Location ID is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Name is required"
				];
			endif;

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				UPDATE mqttlz
				SET zn = :zn,
					lid = :lid
				WHERE id = :id
			");
			$pdoQuery->execute([
				":zn" => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING),
				":lid" => filter_input(INPUT_POST, "lid", FILTER_SANITIZE_STRING),
				":id" => filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)
			]);
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			$this->storeUserHistory("Updated Zone", filter_input(INPUT_POST, "lid", FILTER_SANITIZE_STRING), filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT), 0);

			return [
				"Response"=> "OK",
				"Message" => "Zone updated!"
			];
		}

		public function getDevices($limit = 0, $order = "id DESC")
		{
			$limiter = "";
			if($limit != 0):
				$limiter = "LIMIT " . $limit;
			endif;

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT device.*,
					location.name as loc,
					zone.zn as zne
				FROM mqttld device
				INNER JOIN mqttl location
				ON device.lid = location.id
				INNER JOIN mqttlz zone
				ON device.zid = zone.id
				ORDER BY $order
				$limiter
			");
			$pdoQuery->execute();
			$response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $response;
		}

		public function getDevice($id)
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
			$response=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $response;
		}

		public function createDevice()
		{
			if(!filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Name is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "Location ID is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "Zone ID is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Location IP is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Location MAC is required"
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

			$bcPass = $this->_GeniSys->_helpers->password();

			$lid = filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT);
			$zid = filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT);
			$ip = filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING);
			$mac = filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING);

			$newBcUser = $this->createBlockchainUser($bcPass);

			if($newBcUser == "FAILED"):
				return [
					"Response"=> "Failed",
					"Message" => "Creating New HIAS Blockhain Account Failed!"
				];
			endif;

			$query = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  mqttld  (
					`lid`,
					`zid`,
					`name`,
					`mqttu`,
					`mqttp`,
					`bcaddress`,
					`bcpw`,
					`apub`,
					`aprv`,
					`ip`,
					`mac`,
					`lt`,
					`lg`,
					`time`
				)  VALUES (
					:lid,
					:zid,
					:name,
					:mqttu,
					:mqttp,
					:bcaddress,
					:bcpw,
					:apub,
					:aprv,
					:ip,
					:mac,
					:lt,
					:lg,
					:time
				)
			");
			$query->execute([
				':lid' => $lid,
				':zid' => $zid,
				':name' => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING),
				':mqttu' =>$this->_GeniSys->_helpers->oEncrypt($mqttUser),
				':mqttp' =>$this->_GeniSys->_helpers->oEncrypt($mqttPass),
				':bcaddress' => $newBcUser,
				':bcpw' => $this->_GeniSys->_helpers->oEncrypt($bcPass),
				':apub' => $pubKey,
				':aprv' => $this->_GeniSys->_helpers->oEncrypt($privKeyHash),
				':ip' => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING)),
				':mac' => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING)),
				':lt' => "",
				':lg' => "",
				':time' => time()
			]);
			$did = $this->_GeniSys->_secCon->lastInsertId();

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
				':topic' => $lid."/Devices/#",
				':rw' => 4
			));

			$query = $this->_GeniSys->_secCon->prepare("
				UPDATE mqttl
				SET devices = devices + 1
				WHERE id = :id
			");
			$query->execute(array(
				':id'=>$lid
			));

			$hash = "";
			$msg = "";
			$actionMsg = "";
			$balanceMessage = "";
			$this->contract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["contract"]))->send("registerDevice", $pubKey, $newBcUser, $lid, $zid, $did, filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING), $_SESSION["GeniSysAI"]["Uid"], time(), ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
				if ($err !== null) {
					$hash = "FAILED";
					$msg = $err;
					return;
				}
				$hash = $resp;
			});

			if($hash == "FAILED"):
				$actionMsg = " HIAS Blockchain registerDevice failed! " . $msg;
			else:
				$txid = $this->storeBlockchainTransaction("Register Device", $hash, $did);
				$this->storeUserHistory("Register Device", $txid, $lid, $zid, $did);
				$balance = $this->getBlockchainBalance();
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
				if($balanceMessage == ""):
					$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!";
				endif;
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
		}

		public function updateDevice()
		{

			if(!filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "ID is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "Location ID is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "Zone ID is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "ip", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "IP is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "mac", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "MAC is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Name is required"
				];
			endif;

			$lid = filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT);
			$zid = filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT);
			$id = filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT);

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				UPDATE mqttld
				SET name = :name,
					lid = :lid,
					zid = :zid,
					ip = :ip,
					mac = :mac
				WHERE id = :id
			");
			$pdoQuery->execute([
				":name" => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING),
				":lid" => $lid,
				":zid" => $zid,
				":ip" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING)),
				":mac" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING)),
				":id" => $id
			]);
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			$unlocked =  $this->unlockBlockchainAccount();

			if($unlocked == "FAILED"):
				return [
					"Response"=> "Failed",
					"Message" => "Unlocking HIAS Blockhain Account Failed!"
				];
			endif;

			$hash = "";
			$msg = "";
			$this->contract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["contract"]))->send("updateDevice", filter_input(INPUT_POST, "identifier", FILTER_SANITIZE_STRING), "Device", $lid, $zid, $id, filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING), filter_input(INPUT_POST, "status", FILTER_SANITIZE_STRING), time(), ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
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
				$txid = $this->storeBlockchainTransaction("Update Device", $hash, $id);
				$this->storeUserHistory("Updated Device", $txid, $lid, $zid, $id);
				$balance = $this->getBlockchainBalance();
				$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!";
			endif;

			return [
				"Response"=> "OK",
				"Message" => "Device updated!" . $actionMsg . $balanceMessage
			];
		}

		public function resetDvcMqtt()
		{
			$lid = filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT);
			$zid = filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT);
			$id = filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT);

			$mqttPass = $this->_GeniSys->_helpers->password();
			$mqttHash = create_hash($mqttPass);

			$query = $this->_GeniSys->_secCon->prepare("
				UPDATE mqttld
				SET mqttp = :mqttp
				WHERE id = :id
			");
			$query->execute(array(
				':mqttp' => $this->_GeniSys->_helpers->oEncrypt($mqttPass),
				':id' => $id
			));

			$query = $this->_GeniSys->_secCon->prepare("
				UPDATE mqttu
				SET pw = :pw
				WHERE did = :did
			");
			$query->execute(array(
				':pw' => $mqttHash,
				':did' => $id
			));

			$this->storeUserHistory("Reset Device MQTT Password", 0, $lid, $zid, $id);

			return [
				"Response"=> "OK",
				"Message" => "MQTT password reset!",
				"P" => $mqttPass
			];

		}

		public function resetDvcKey()
		{
			$lid = filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT);
			$zid = filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT);
			$id = filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT);

			$privKey = $this->_GeniSys->_helpers->generateKey(32);
			$privKeyHash = $this->_GeniSys->_helpers->createPasswordHash($privKey);

			$query = $this->_GeniSys->_secCon->prepare("
				UPDATE mqttld
				SET aprv = :aprv
				WHERE id = :id
			");
			$query->execute(array(
				':aprv' => $this->_GeniSys->_helpers->oEncrypt($privKeyHash),
				':id' => $id
			));

			$this->storeUserHistory("Reset Device Key", 0, $lid, $zid, $id);

			return [
				"Response"=> "OK",
				"Message" => "Device key reset!",
				"P" => $privKey
			];

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
			$mngConn = new MongoDB\Driver\Manager('mongodb://'.$this->_GeniSys->_mdbusername.':'.$this->_GeniSys->_mdbpassword.'@localhost/'.$this->_GeniSys->_mdbname.'');
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
			$mngConn = new MongoDB\Driver\Manager('mongodb://'.$this->_GeniSys->_mdbusername.':'.$this->_GeniSys->_mdbpassword.'@localhost/'.$this->_GeniSys->_mdbname.'');
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
			$mngConn = new MongoDB\Driver\Manager('mongodb://'.$this->_GeniSys->_mdbusername.':'.$this->_GeniSys->_mdbpassword.'@localhost/'.$this->_GeniSys->_mdbname.'');

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
			$mngConn = new MongoDB\Driver\Manager('mongodb://'.$this->_GeniSys->_mdbusername.':'.$this->_GeniSys->_mdbpassword.'@localhost/'.$this->_GeniSys->_mdbname.'');
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

		public function getMDevices()
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT *
				FROM mqttu
				ORDER BY id DESC
			");
			$pdoQuery->execute();
			$response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $response;
		}

		public function getSensors($limit = 0, $order = "id DESC")
		{
			$limiter = "";
			if($limit != 0):
				$limiter = "LIMIT " . $limit;
			endif;

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT *
				FROM sensors
				ORDER BY $order
				$limiter
			");
			$pdoQuery->execute();
			$response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $response;
		}

		public function getSensor($id)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT *
				FROM sensors
				WHERE id = :id
			");
			$pdoQuery->execute([
				":id" => $id
			]);
			$response=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $response;
		}

		public function createSensor()
		{
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

			if (!empty($_FILES['image']['name']) && ($_FILES['image']['error'] == 0)):

				$cleaned_file = preg_replace('/\.(?=.*?\.)/', '_', $_FILES['image']['name']);
				$cleaned_file = str_replace([' '], "-", $cleaned_file);

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

								$query = $this->_GeniSys->_secCon->prepare("
									INSERT INTO  sensors  (
										`name`,
										`type`,
										`image`
									)  VALUES (
										:name,
										:type,
										:image
									)
								");
								$query->execute([
									':name' => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING),
									':type' => filter_input(INPUT_POST, "type", FILTER_SANITIZE_STRING),
									':image' => $fileName
								]);
								$sensor = $this->_GeniSys->_secCon->lastInsertId();

								$this->storeUserHistory("Created Sensor", 0, 0, 0, 0, $sensor);

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
				$limiter = "LIMIT " . $limit;
			endif;

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT application.*,
					location.name as loc
				FROM mqtta application
				INNER JOIN mqttl location
				ON application.lid = location.id
				ORDER BY $order
				$limiter
			");
			$pdoQuery->execute();
			$response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $response;
		}

		public function getApplication($id)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT *
				FROM mqtta
				WHERE id = :id
			");
			$pdoQuery->execute([
				":id" => $id
			]);
			$response=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $response;
		}

		public function createApplication()
		{
			if(!filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Name is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "Location ID is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Location IP is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Location MAC is required"
				];
			endif;

			$mqttUser = $this->_GeniSys->_helpers->generate_uuid();
			$mqttPass = $this->_GeniSys->_helpers->password();
			$mqttHash = create_hash($mqttPass);

			$bcPass = $this->_GeniSys->_helpers->password();

			$pubKey = $this->_GeniSys->_helpers->generate_uuid();
			$privKey = $this->_GeniSys->_helpers->generateKey(32);
			$privKeyHash = $this->_GeniSys->_helpers->createPasswordHash($privKey);

			$lid = filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT);

			$htpasswd = new Htpasswd('/etc/nginx/security/htpasswd');
			$htpasswd->addUser($pubKey, $privKey, Htpasswd::ENCTYPE_APR_MD5);

			$newBcUser = $this->createBlockchainUser($bcPass);

			$allowed = filter_input(INPUT_POST, "cancelled", FILTER_SANITIZE_STRING) ? False : True;
			$admin = filter_input(INPUT_POST, "admin", FILTER_SANITIZE_STRING) ? True : False;

			$query = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  mqtta  (
					`lid`,
					`name`,
					`bcaddress`,
					`bcpw`,
					`mqttu`,
					`mqttp`,
					`apub`,
					`aprv`,
					`ip`,
					`mac`,
					`lt`,
					`lg`,
					`status`,
					`time`
				)  VALUES (
					:lid,
					:name,
					:bcaddress,
					:bcpw,
					:mqttu,
					:mqttp,
					:apub,
					:aprv,
					:ip,
					:mac,
					:lt,
					:lg,
					:status,
					:time
				)
			");
			$query->execute([
				':lid' => $lid,
				':name' => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING),
				':bcaddress' => $newBcUser,
				':bcpw' => $this->_GeniSys->_helpers->oEncrypt($bcPass),
				':mqttu' =>$this->_GeniSys->_helpers->oEncrypt($mqttUser),
				':mqttp' =>$this->_GeniSys->_helpers->oEncrypt($mqttPass),
				':apub' => $pubKey,
				':aprv' => $this->_GeniSys->_helpers->oEncrypt($privKeyHash),
				':ip' => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING)),
				':mac' => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING)),
				':lt' => "",
				':lg' => "",
				':status' => "OFFLINE",
				':time' => time()
			]);
			$aid = $this->_GeniSys->_secCon->lastInsertId();

			$unlocked =  $this->unlockBlockchainAccount();

			if($unlocked == "FAILED"):
				return [
					"Response"=> "Failed",
					"Message" => "Unlocking HIAS Blockhain Account Failed!"
				];
			endif;

			$hash = "";
			$msg = "";
			$this->contract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["contract"]))->send("registerApplication", $pubKey, $newBcUser, $admin, $lid, $aid, filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING), $_SESSION["GeniSysAI"]["Uid"], time(), ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
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
				$actionMsg = " HIAS Blockchain registerApplication failed! " . $msg;
			else:
				$txid = $this->storeBlockchainTransaction("Register Application", $hash, 0, $aid);
				$this->storeUserHistory("Register Application", $txid, $lid, 0, 0, 0, $aid);
				$balance = $this->getBlockchainBalance();
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
				$txid = $this->storeBlockchainTransaction("iotJumpWay Register Authorized", $hash, 0, $aid);
				$this->storeUserHistory("Register Authorized", $txid, $lid, 0, 0, 0, $aid);
				$balance = $this->getBlockchainBalance();
				if($balanceMessage == ""):
					$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!";
				endif;
			endif;

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
				':topic' => $lid."/Devices/#",
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
				':topic' => $lid."/Applications/#",
				':rw' => 2
			));

			$query = $this->_GeniSys->_secCon->prepare("
				UPDATE mqttl
				SET apps = apps + 1
				WHERE id = :id
			");
			$query->execute(array(
				':id'=>$lid
			));

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
		}

		public function updateApplication()
		{
			if(!filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "ID is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Name is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Location IP is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Location MAC is required"
				];
			endif;

			$id = filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT);
			$lid = filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT);
			$allowed = filter_input(INPUT_POST, "cancelled", FILTER_SANITIZE_STRING) ? False : True;
			$admin = filter_input(INPUT_POST, "admin", FILTER_SANITIZE_STRING) ? True : False;

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				UPDATE mqtta
				SET name = :name,
					lid = :lid,
					ip = :ip,
					mac = :mac,
					admin = :admin,
					cancelled = :cancelled
				WHERE id = :id
			");
			$pdoQuery->execute([
				":name" => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING),
				":lid" => $lid,
				":ip" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING)),
				":mac" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING)),
				":admin" => filter_input(INPUT_POST, "admin", FILTER_SANITIZE_STRING) ? 1 : 0,
				":cancelled" => filter_input(INPUT_POST, "cancelled", FILTER_SANITIZE_STRING) ? 1 : 0,
				":id" => $id
			]);
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				UPDATE mqttu
				SET lid = :lid
				WHERE aid = :aid
			");
			$pdoQuery->execute([
				':lid' => $lid,
				':aid' => $id
			]);
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				UPDATE mqttua
				SET lid = :lid
				WHERE aid = :aid
			");
			$pdoQuery->execute([
				':lid' => $lid,
				':aid' => $id
			]);
			$pdoQuery->closeCursor();
			$pdoQuery = null;

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

			$this->contract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["contract"]))->send("updateApplication", filter_input(INPUT_POST, "identifier", FILTER_SANITIZE_STRING), "Application", $allowed, $admin, $lid, filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING), filter_input(INPUT_POST, "status", FILTER_SANITIZE_STRING), time(), ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
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
				$txid = $this->storeBlockchainTransaction("Update Application", $hash, 0, $id);
				$this->storeUserHistory("Update Application", $txid, $lid, 0, 0, 0, $id);
				$balance = $this->getBlockchainBalance();
				$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!";
			endif;

			return [
				"Response"=> "OK",
				"Message" => "Application updated!" . $actionMsg . $balanceMessage
			];
		}

		public function resetAppMqtt()
		{
			$mqttPass = $this->_GeniSys->_helpers->password();
			$mqttHash = create_hash($mqttPass);

			$lid = filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT);
			$id = filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT);

			$query = $this->_GeniSys->_secCon->prepare("
				UPDATE mqtta
				SET mqttp = :mqttp
				WHERE id = :id
			");
			$query->execute(array(
				':mqttp' => $this->_GeniSys->_helpers->oEncrypt($mqttPass),
				':id' => $id
			));

			$query = $this->_GeniSys->_secCon->prepare("
				UPDATE mqttu
				SET pw = :pw
				WHERE aid = :aid
			");
			$query->execute(array(
				':pw' => $mqttHash,
				':aid' => $id
			));

			$this->storeUserHistory("Update Application MQTT Password", 0, $lid, 0, 0, 0, $id);

			return [
				"Response"=> "OK",
				"Message" => "MQTT password reset!",
				"P" => $mqttPass
			];

		}

		public function resetAppKey()
		{
			$identifier = filter_input(INPUT_POST, "identifier", FILTER_SANITIZE_STRING);
			$lid = filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT);
			$id = filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT);

			$privKey = $this->_GeniSys->_helpers->generateKey(32);
			$privKeyHash = $this->_GeniSys->_helpers->createPasswordHash($privKey);

			$htpasswd = new Htpasswd('/etc/nginx/security/htpasswd');
			$htpasswd->updateUser($identifier, $privKey, Htpasswd::ENCTYPE_APR_MD5);

			$query = $this->_GeniSys->_secCon->prepare("
				UPDATE mqtta
				SET aprv = :aprv
				WHERE id = :id
			");
			$query->execute(array(
				':aprv' => $this->_GeniSys->_helpers->oEncrypt($privKeyHash),
				':id' => $id
			));

			$this->storeUserHistory("Update Application Key", 0, $lid, 0, 0, 0, $id);

			return [
				"Response"=> "OK",
				"Message" => "Application key reset!",
				"P" => $privKey
			];

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
			$mngConn = new MongoDB\Driver\Manager('mongodb://'.$this->_GeniSys->_mdbusername.':'.$this->_GeniSys->_mdbpassword.'@localhost/'.$this->_GeniSys->_mdbname.'');
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
			$mngConn = new MongoDB\Driver\Manager('mongodb://'.$this->_GeniSys->_mdbusername.':'.$this->_GeniSys->_mdbpassword.'@localhost/'.$this->_GeniSys->_mdbname.'');
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
			$mngConn = new MongoDB\Driver\Manager('mongodb://'.$this->_GeniSys->_mdbusername.':'.$this->_GeniSys->_mdbpassword.'@localhost/'.$this->_GeniSys->_mdbname.'');
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
			$mngConn = new MongoDB\Driver\Manager('mongodb://'.$this->_GeniSys->_mdbusername.':'.$this->_GeniSys->_mdbpassword.'@localhost/'.$this->_GeniSys->_mdbname.'');
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
			$mngConn = new MongoDB\Driver\Manager('mongodb://'.$this->_GeniSys->_mdbusername.':'.$this->_GeniSys->_mdbpassword.'@localhost/'.$this->_GeniSys->_mdbname.'');
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
			$mngConn = new MongoDB\Driver\Manager('mongodb://'.$this->_GeniSys->_mdbusername.':'.$this->_GeniSys->_mdbpassword.'@localhost/'.$this->_GeniSys->_mdbname.'');
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
			$mngConn = new MongoDB\Driver\Manager('mongodb://'.$this->_GeniSys->_mdbusername.':'.$this->_GeniSys->_mdbpassword.'@localhost/'.$this->_GeniSys->_mdbname.'');
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
			$mngConn = new MongoDB\Driver\Manager('mongodb://'.$this->_GeniSys->_mdbusername.':'.$this->_GeniSys->_mdbpassword.'@localhost/'.$this->_GeniSys->_mdbname.'');
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
			$mngConn = new MongoDB\Driver\Manager('mongodb://'.$this->_GeniSys->_mdbusername.':'.$this->_GeniSys->_mdbpassword.'@localhost/'.$this->_GeniSys->_mdbname.'');
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

		public function getLife()
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT id,
					cpu,
					mem,
					hdd,
					tempr,
					status
				FROM mqttld
				WHERE id = :id
			");
			$pdoQuery->execute([
				":id" => filter_input(INPUT_POST, "device", FILTER_SANITIZE_NUMBER_INT)
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

		public function getALife()
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
	if(filter_input(INPUT_POST, "get_life", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($iotJumpWay->getLife()));
	endif;
	if(filter_input(INPUT_POST, "get_alife", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($iotJumpWay->getALife()));
	endif;
	if(filter_input(INPUT_POST, "get_slife", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($iotJumpWay->getSLife()));
	endif;
