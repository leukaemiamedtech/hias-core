<?php

require __DIR__ . '/../../../../vendor/autoload.php';

use Web3\Web3;
use Web3\Contract;
use Web3\Utils;

	class Beds
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

		private function checkBlockchainPatientPermissions()
		{
			$allowed = "";
			$this->contract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["pcontract"]))->call("userAllowed", ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$allowed) {
				if ($err !== null) {
					$allowed = "FAILED";
					return;
				}
				$allowed = $resp[0];
			});
			if($allowed != "true"):
				header('Location: /Dashboard');
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

		private function storeBlockchainTransaction($action, $hash, $did, $bid)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  transactions (
					`uid`,
					`action`,
					`did`,
					`bid`,
					`hash`,
					`time`
				)  VALUES (
					:uid,
					:action,
					:did,
					:bid,
					:hash,
					:time
				)
			");
			$pdoQuery->execute([
				":uid" => $_SESSION["GeniSysAI"]["Uid"],
				":action" => $action,
				":did" => $did,
				":bid" => $bid,
				':hash' => $this->_GeniSys->_helpers->oEncrypt($hash),
				":time" => time()
			]);
			$txid = $this->_GeniSys->_secCon->lastInsertId();
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			return $txid;
		}

		private function storeUserHistory($action, $hashid, $lid, $zid, $device, $bed)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  history (
					`uid`,
					`tlid`,
					`tzid`,
					`tdid`,
					`tbid`,
					`action`,
					`hash`,
					`time`
				)  VALUES (
					:uid,
					:tlid,
					:tzid,
					:tdid,
					:tbid,
					:action,
					:hash,
					:time
				)
			");
			$pdoQuery->execute([
				":uid" => $_SESSION["GeniSysAI"]["Uid"],
				":tlid" => $lid,
				":tzid" => $zid,
				":tdid" => $device,
				":tbid" => $bed,
				":action" => $action,
				':hash' => $hashid,
				":time" => time()
			]);
			$txid = $this->_GeniSys->_secCon->lastInsertId();
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			return $txid;
		}

		public function getBeds()
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT beds.id,
					beds.lid,
					beds.did,
					mqttld.status,
					mqttld.mqttu,
					mqttld.mqttp
				FROM beds beds
				INNER JOIN mqttld mqttld
				ON beds.id = mqttld.bid
				ORDER BY id DESC
			");
			$pdoQuery->execute();
			$response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $response;
		}

		public function getBed($id)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
					SELECT beds.*,
					mqttld.lid,
					mqttld.name,
					mqttld.status,
					mqttld.mqttu,
					mqttld.mqttp,
					mqttld.apub,
					mqttld.lt,
					mqttld.lg,
					mqttld.cpu,
					mqttld.mem,
					mqttld.hdd,
					mqttld.tempr,
					mqttld.id AS did
				FROM beds beds
				INNER JOIN mqttld mqttld
				ON beds.did = mqttld.id
				WHERE beds.id = :id
			");
			$pdoQuery->execute([
				":id" => $id
			]);
			$response=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			return $response;
		}

		public function createBed()
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
					"Message" => "iotJumpWay location id is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "ip", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "Bed IP is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "mac", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "Bed MAC is required"
				];
			endif;

			$unlocked =  $this->unlockBlockchainAccount();

			if($unlocked == "FAILED"):
				return [
					"Response"=> "Failed",
					"Message" => "Unlocking HIAS Blockhain Account Failed!"
				];
			endif;

			$lid = filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT);
			$zid = filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT);

			$mqttUser = $this->_GeniSys->_helpers->generate_uuid();
			$mqttPass = $this->_GeniSys->_helpers->password();
			$mqttHash = create_hash($mqttPass);

			$pubKey = $this->_GeniSys->_helpers->generate_uuid();
			$privKey = $this->_GeniSys->_helpers->generateKey(32);
			$privKeyHash = $this->_GeniSys->_helpers->createPasswordHash($privKey);

			$bcPass = $this->_GeniSys->_helpers->password();
			$newBcUser = $this->createBlockchainUser($bcPass);

			if($newBcUser == "FAILED"):
				return [
					"Response"=> "Failed",
					"Message" => "Creating New HIAS Blockhain Account Failed!"
				];
			endif;

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  beds  (
					`lid`,
					`zid`,
					`bcaddress`,
					`ip`,
					`mac`,
					`gpstime`,
					`created`
				)  VALUES (
					:lid,
					:zid,
					:bcaddress,
					:ip,
					:mac,
					:gpstime,
					:time
				)
			");
			$pdoQuery->execute([
				":lid" => filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT),
				":zid" => $zid,
				":bcaddress" => $newBcUser,
				":ip" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING)),
				":mac" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING)),
				":gpstime" => 0,
				":time" => time()
			]);
			$bid = $this->_GeniSys->_secCon->lastInsertId();
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			$query = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  mqttld  (
					`lid`,
					`zid`,
					`name`,
					`mqttu`,
					`mqttp`,
					`bcaddress`,
					`apub`,
					`aprv`,
					`time`
				)  VALUES (
					:lid,
					:zid,
					:name,
					:mqttu,
					:mqttp,
					:bcaddress,
					:apub,
					:aprv,
					:time
				)
			");
			$query->execute([
				':lid' => $lid,
				':zid' => $zid,
				':name' => "Bed " . $bid,
				':mqttu' => $this->_GeniSys->_helpers->oEncrypt($mqttUser),
				':mqttp' => $this->_GeniSys->_helpers->oEncrypt($mqttPass),
				':bcaddress' => $newBcUser,
				':apub' => $pubKey,
				':aprv' => $this->_GeniSys->_helpers->oEncrypt($privKeyHash),
				':time' => time()
			]);
			$did = $this->_GeniSys->_secCon->lastInsertId();

			$query = $this->_GeniSys->_secCon->prepare("
				UPDATE beds
				SET did = :did
				WHERE id = :id
			");
			$query->execute(array(
				':did'=>$did,
				':id'=>$lid
			));

			$query = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  mqttu  (
					`lid`,
					`zid`,
					`did`,
					`bid`,
					`uname`,
					`pw`
				)  VALUES (
					:lid,
					:zid,
					:did,
					:bid,
					:uname,
					:pw
				)
			");
			$query->execute([
				":lid" => $lid,
				":zid" => $zid,
				":did" =>  $did,
				":bid" =>  $bid,
				':uname' => $mqttUser,
				':pw' => $mqttHash
			]);

			$query = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  mqttua  (
					`lid`,
					`zid`,
					`did`,
					`bid`,
					`username`,
					`topic`,
					`rw`
				)  VALUES (
					:lid,
					:zid,
					:did,
					:bid,
					:username,
					:topic,
					:rw
				)
			");
			$query->execute(array(
				":lid" => $lid,
				":zid" => $zid,
				":did" =>  $did,
				":bid" =>  $bid,
				':username' => $mqttUser,
				':topic' => $lid . "/Device/" . $zid . "/" . $did . "/#",
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

			$query = $this->_GeniSys->_secCon->prepare("
				UPDATE mqttld
				SET bid = :bid
				WHERE id = :id
			");
			$query->execute(array(
				':bid'=>$bid,
				':id'=>$did
			));

			$htpasswd = new Htpasswd('/etc/nginx/security/beds');
			$htpasswd->addUser($pubKey, $privKey, Htpasswd::ENCTYPE_APR_MD5);

			$hash = "";
			$msg = "";
			$actionMsg = "";
			$balanceMessage = "";
			$this->contract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["contract"]))->send("registerDevice", $pubKey, $newBcUser, $lid, $zid, $did, "Bed " . $bid, $_SESSION["GeniSysAI"]["Uid"], time(), ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
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
				$txid = $this->storeBlockchainTransaction("Register Bed Device", $hash, $did, $bid);
				$this->storeUserHistory("Register Bed Device", $txid, $lid, $zid, $did, $bid);
				$balance = $this->getBlockchainBalance();
				$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!";
			endif;

			$hash = "";
			$msg = "";
			$actionMsg = "";
			$balanceMessage = "";
			$this->icontract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["icontract"]))->send("registerAuthorized", $newBcUser, ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
				if ($err !== null) {
					$hash = "FAILED";
					$msg = $err;
					return;
				}
				$hash = $resp;
			});

			if($hash == "FAILED"):
				$actionMsg  .= " HIAS Blockchain registerAuthorized failed! " . $msg;
			else:
				$txid = $this->storeBlockchainTransaction("iotJumpWay Register Authorized Bed Device", $hash, $did, $bid);
				$this->storeUserHistory("iotJumpWay Register Authorized Bed Device", $txid, $lid, $zid, $did, $bid);
				$balance = $this->getBlockchainBalance();
				if($balanceMessage == ""):
					$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!";
				endif;
			endif;

			return [
				"Response"=> "OK",
				"Message" => "Bed & device created! " . $actionMsg . $balanceMessage,
				"BID" => $bid,
				"Uname" => "Bed " . $bid,
				"AppID" => $pubKey,
				"AppKey" => $privKey,
				"BCU" => $newBcUser,
				"BCP" => $bcPass,
				"MU" => $mqttUser,
				"MP" => $mqttPass
			];
		}

		public function updateBed()
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
					"Message" => "iotJumpWay location id is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "did", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "iotJumpWay device id is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "ip", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "Bed IP is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "mac", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "Bed MAC is required"
				];
			endif;

			$unlocked =  $this->unlockBlockchainAccount();

			if($unlocked == "FAILED"):
				return [
					"Response"=> "Failed",
					"Message" => "Unlocking HIAS Blockhain Account Failed!"
				];
			endif;

			$lid = filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT);
			$zid = filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT);
			$did = filter_input(INPUT_POST, "did", FILTER_SANITIZE_NUMBER_INT);
			$id = filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT);
			$ip = filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING);
			$mac = filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING);

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				UPDATE beds
				SET lid = :lid,
					zid = :zid,
					did = :did,
					ip = :ip,
					mac = :mac
				WHERE id = :id
			");
			$pdoQuery->execute([
				":lid" => $lid,
				":zid" => $zid ,
				":did" => $did,
				":ip" => $this->_GeniSys->_helpers->oEncrypt($ip),
				":mac" => $this->_GeniSys->_helpers->oEncrypt($mac),
				":id" => $id
			]);
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			$hash = "";
			$msg = "";
			$this->contract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["contract"]))->send("updateDevice", filter_input(INPUT_POST, "identifier", FILTER_SANITIZE_STRING), "Device", $lid, $zid, $did, filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING), filter_input(INPUT_POST, "status", FILTER_SANITIZE_STRING), time(), ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
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
				$txid = $this->storeBlockchainTransaction("iotJumpWay Update Authorized Bed Device", $hash, $did, $id);
				$this->storeUserHistory("iotJumpWay Update Authorized Bed Device", $txid, $lid, $zid, $did, $id);
				$balance = $this->getBlockchainBalance();
				$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!";
			endif;

			return [
				"Response"=> "OK",
				"Message" => "Bed updated!" . $actionMsg . $balanceMessage
			];
		}

		public function resetMqtt()
		{
			$mqttPass = $this->_GeniSys->_helpers->password();
			$mqttHash = create_hash($mqttPass);

			$bid = filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT);

			$query = $this->_GeniSys->_secCon->prepare("
				UPDATE mqttld
				SET mqttp = :mqttp
				WHERE bid = :bid
			");
			$query->execute(array(
				':mqttp' => $this->_GeniSys->_helpers->oEncrypt($mqttPass),
				':bid' => $bid
			));

			$query = $this->_GeniSys->_secCon->prepare("
				UPDATE mqttu
				SET pw = :pw
				WHERE bid = :bid
			");
			$query->execute(array(
				':pw' => $mqttHash,
				':bid' => $bid
			));

			$this->storeUserHistory("Reset Bed  MQTT Password", 0, 0, 0, 0, $bid);

			return [
				"Response"=> "OK",
				"Message" => "MQTT password reset!",
				"P" => $mqttPass
			];

		}

		public function resetAppKey()
		{
			$identifier = filter_input(INPUT_POST, "identifier", FILTER_SANITIZE_STRING);
			$bid = filter_input(INPUT_POST, "bid", FILTER_SANITIZE_NUMBER_INT);
			$id = filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT);

			$privKey = $this->_GeniSys->_helpers->generateKey(32);
			$privKeyHash = $this->_GeniSys->_helpers->createPasswordHash($privKey);

			$htpasswd = new Htpasswd('/etc/nginx/security/beds');
			$htpasswd->addUser($identifier, $privKey, Htpasswd::ENCTYPE_APR_MD5);
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

			$this->storeUserHistory("Reset Bed Key", 0, 0, 0, $id, $bid);

			return [
				"Response"=> "OK",
				"Message" => "Application key reset!",
				"P" => $privKey
			];

		}

		public function retrieveTransactions($bed, $limit = 0, $order = "")
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
				WHERE bid = :id
				$orderer
				$limiter
			");
			$pdoQuery->execute([
				":id" => $bed
			]);
			$response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			return $response;
		}

		public function retrieveHistory($bed, $limit = 0, $order = "")
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
				WHERE tbid = :id
				$orderer
				$limiter
			");
			$pdoQuery->execute([
				":id" => $bed
			]);
			$response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			return $response;
		}

		public function getMapMarkers($application)
		{
			if(!$application["lt"]):
				$lat = $this->_GeniSys->lt;
				$lng = $this->_GeniSys->lg;
			else:
				$lat = $application["lt"];
				$lng = $application["lg"];
			endif;

			return [$lat, $lng];
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

	$Beds = new Beds($_GeniSys);

	if(filter_input(INPUT_POST, "create_bed", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($Beds->createBed()));
	endif;
	if(filter_input(INPUT_POST, "update_bed", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($Beds->updateBed()));
	endif;
	if(filter_input(INPUT_POST, "reset_mqtt_bed", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($Beds->resetMqtt()));
	endif;
	if(filter_input(INPUT_POST, "reset_bd_apriv", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($Beds->resetAppKey()));
	endif;