<?php

require __DIR__ . '/../../../../vendor/autoload.php';

use Web3\Web3;
use Web3\Contract;
use Web3\Utils;

	class EMAR
	{

		function __construct($_GeniSys)
		{
			$this->_GeniSys = $_GeniSys;
			$this->bcc = $this->getBlockchainConf();
			$this->web3 = $this->blockchainConnection();
			$this->contract = new Contract($this->web3->provider, $this->bcc["abi"]);
			$this->icontract = new Contract($this->web3->provider, $this->bcc["iabi"]);
			$this->checkBlockchainPermissions();
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

		public function getDevices()
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT emar.id,
					emar.name,
					emar.lid,
					emar.zid,
					emar.did,
					device.name as dname,
					device.status,
					device.lt,
					device.lg,
					device.mqttu,
					device.mqttp
				FROM emar emar
				INNER JOIN mqttld device
				ON device.id = emar.did
				ORDER BY id DESC
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
				SELECT emar.id,
					emar.name,
					emar.lid,
					emar.zid,
					emar.did,
					emar.ip,
					emar.mac,
					emar.sdir,
					device.status,
					device.lt,
					device.lg,
					device.tempr,
					device.apub,
					device.bcaddress,
					device.hdd,
					device.mem,
					device.cpu,
					device.mqttu,
					device.mqttp,
					emar.sport,
					emar.sportf,
					emar.sckport
				FROM emar emar
				INNER JOIN mqttld device
				ON device.id = emar.did
				WHERE emar.id = :id
			");
			$pdoQuery->execute([
				":id" => $id
			]);
			$response=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			return $response;
		}

		public function createiDevice($params = [])
		{
			$query = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  mqttld  (
					`lid`,
					`zid`,
					`name`,
					`mqttu`,
					`mqttp`,
					`apub`,
					`aprv`,
					`time`
				)  VALUES (
					:lid,
					:zid,
					:name,
					:mqttu,
					:mqttp,
					:apub,
					:aprv,
					:time
				)
			");
			$query->execute([
				':lid' => $params["lid"],
				':zid' => $params["zid"],
				':name' => $params["name"],
				':mqttu' =>$this->_GeniSys->_helpers->oEncrypt($params["mqttu"]),
				':mqttp' =>$this->_GeniSys->_helpers->oEncrypt($params["mqttp"]),
				':apub' => $this->_GeniSys->_helpers->oEncrypt($params["apub"]),
				':aprv' => $this->_GeniSys->_helpers->oEncrypt($params["aprv"]),
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
				':lid' => $params["lid"],
				':zid' => $params["zid"],
				':did' => $did,
				':uname' => $params["mqttu"],
				':pw' => $params["mqttHash"]
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
				':lid' => $params["lid"],
				':zid' => $params["zid"],
				':did' => $did,
				':username' => $params["mqttu"],
				':topic' => $params["lid"] . "/Device/" . $params["zid"] . "/" . $did . "#",
				':rw' => 4
			));

			$query = $this->_GeniSys->_secCon->prepare("
				UPDATE mqttl
				SET devices = devices + 1
				WHERE id = :id
			");
			$query->execute(array(
				':id'=>$params["lid"]
			));

			return $did;
		}
		public function createDevice()
		{
			if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "iotJumpWay location id is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "iotJumpWay zone id is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "EMAR device name is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Device IP is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Device MAC is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "sport", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Device stream port is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "sportf", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Device stream file is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "sckport", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Device socket port is required"
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
				':bcaddress' => $this->_GeniSys->_helpers->oEncrypt($newBcUser),
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
			$this->contract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["contract"]))->send("registerDevice", $pubKey, $newBcUser, $lid, $zid, $did, filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING), $_SESSION["GeniSysAI"]["Uid"], time(), ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
				if ($err !== null) {
					$hash = "FAILED";
					$msg = $err;
					return;
				}
				$hash = $resp;
			});

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  emar  (
					`name`,
					`lid`,
					`zid`,
					`did`,
					`ip`,
					`mac`,
					`sdir`,
					`sport`,
					`sportf`,
					`sckport`
				)  VALUES (
					:name,
					:lid,
					:zid,
					:did,
					:ip,
					:mac,
					:sdir,
					:sport,
					:sportf,
					:sckport
				)
			");
			$pdoQuery->execute([
				":name" => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING),
				":lid" => $lid,
				":zid" => $zid,
				":did" => $did,
				":ip" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING)),
				":mac" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING)),
				":sdir" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sdir", FILTER_SANITIZE_STRING)),
				":sport" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sport", FILTER_SANITIZE_STRING)),
				":sportf" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sportf", FILTER_SANITIZE_STRING)),
				":sckport" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sckport", FILTER_SANITIZE_STRING))
			]);
			$eid = $this->_GeniSys->_secCon->lastInsertId();
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			if($hash == "FAILED"):
				$actionMsg = " HIAS Blockchain registerDevice failed! ";
			else:
				$txid = $this->storeBlockchainTransaction("Register EMAR Device", $hash, $did);
				$this->storeUserHistory("Register EMAR Device", $txid, $lid, $zid, $did);
				$balance = $this->getBlockchainBalance();
				$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!";
			endif;

			return [
				"Response"=> "OK",
				"Message" => "Device created!" . $actionMsg . $balanceMessage,
				"LID" => $lid,
				"ZID" => $zid,
				"EDID" => $eid,
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
			if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "iotJumpWay location id is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "iotJumpWay zone id is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "did", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "iotJumpWay device id is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "ID is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "EMAR device name is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Device IP is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Device MAC is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "sport", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Stream port is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "sdir", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Stream directory is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "sportf", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Stream file is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "sckport", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Socket port is required"
				];
			endif;

			$lid = filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT);
			$zid = filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT);
			$did = filter_input(INPUT_POST, "did", FILTER_SANITIZE_NUMBER_INT);
			$ip = filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING);
			$mac = filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING);

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				UPDATE emar
				SET name = :name,
					lid = :lid,
					zid = :zid,
					did = :did,
					ip = :ip,
					mac = :mac,
					sport = :sport,
					sdir = :sdir,
					sportf = :sportf,
					sckport = :sckport
				WHERE id = :id
			");
			$pdoQuery->execute([
				":name" => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING),
				":lid" => $lid,
				":zid" => $zid,
				":did" => $did,
				":ip" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING)),
				":mac" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING)),
				":sport" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sport", FILTER_SANITIZE_STRING)),
				":sdir" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sdir", FILTER_SANITIZE_STRING)),
				":sportf" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sportf", FILTER_SANITIZE_STRING)),
				":sckport" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sckport", FILTER_SANITIZE_STRING)),
				":id" => filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)
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
			$this->contract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["contract"]))->send("updateDevice", filter_input(INPUT_POST, "identifier", FILTER_SANITIZE_STRING), "Device", $lid, $zid, $did, $name, filter_input(INPUT_POST, "status", FILTER_SANITIZE_STRING), time(), ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
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
				$txid = $this->storeBlockchainTransaction("Update EMAR Device", $hash, $did);
				$this->storeUserHistory("Updated EMAR Device", $txid, $lid, $zid, $did);
				$balance = $this->getBlockchainBalance();
				$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!";
			endif;

			return [
				"Response"=> "OK",
				"Message" => "Device updated!" . $actionMsg . $balanceMessage
			];
		}

		public function resetMqtt()
		{
			$lid = filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT);
			$zid = filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT);
			$did = filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT);

			$mqttPass = $this->_GeniSys->_helpers->password();
			$mqttHash = create_hash($mqttPass);

			$query = $this->_GeniSys->_secCon->prepare("
				UPDATE mqttld
				SET mqttp = :mqttp
				WHERE id = :id
			");
			$query->execute(array(
				':mqttp' => $this->_GeniSys->_helpers->oEncrypt($mqttPass),
				':id' => $did
			));

			$query = $this->_GeniSys->_secCon->prepare("
				UPDATE mqttu
				SET pw = :pw
				WHERE did = :did
			");
			$query->execute(array(
				':pw' => $mqttHash,
				':did' => $did
			));

			$this->storeUserHistory("Reset EMAR Device MQTT Password", 0, $lid, $zid, $did);

			return [
				"Response"=> "OK",
				"Message" => "Device MQTT password reset!",
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

			$this->storeUserHistory("Reset EMAR Device Key", 0, $lid, $zid, $id);

			return [
				"Response"=> "OK",
				"Message" => "Device key reset!",
				"P" => $privKey
			];

		}

		public function getLifes()
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT emar.id,
					device.status,
					device.lt,
					device.lg,
					device.tempr,
					device.hdd,
					device.mem,
					device.cpu
				FROM emar emar
				INNER JOIN mqttld device
				ON device.id = emar.did
				WHERE emar.id = :id
			");
			$pdoQuery->execute([
				":id" => filter_input(INPUT_POST, "device", FILTER_SANITIZE_NUMBER_INT)
			]);
			$response=$pdoQuery->fetch(PDO::FETCH_ASSOC);

			if($response["id"]):
				return  [
					'Response'=>'OK',
					'ResponseData'=>$response
				];
			else:
				return  [
					'Response'=>'FAILED',
					'Message'=>'EMAR device not found!'
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

		public function getMapMarkers($device)
		{
			if(!$device["lt"]):
				$lat = $this->_GeniSys->lt;
				$lng = $this->_GeniSys->lg;
			else:
				$lat = $device["lt"];
				$lng = $device["lg"];
			endif;

			return [$lat, $lng];
		}

	}

	$EMAR = new EMAR($_GeniSys);

	if(filter_input(INPUT_POST, "update_emar", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($EMAR->updateDevice()));
	endif;
	if(filter_input(INPUT_POST, "create_emar", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($EMAR->createDevice()));
	endif;
	 if(filter_input(INPUT_POST, "reset_mqtt", FILTER_SANITIZE_NUMBER_INT)):
	 	 die(json_encode($EMAR->resetMqtt()));
	 endif;
	 if(filter_input(INPUT_POST, "reset_key", FILTER_SANITIZE_NUMBER_INT)):
	 	 die(json_encode($EMAR->resetDvcKey()));
	 endif;
	if(filter_input(INPUT_POST, "get_lifes", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($EMAR->getLifes()));
	endif;