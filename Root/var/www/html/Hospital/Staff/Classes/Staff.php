<?php

require __DIR__ . '/../../../../vendor/autoload.php';

use Web3\Web3;
use Web3\Contract;
use Web3\Utils;

	class Staff
	{
		private $lat = 41.5463;
		private $lng = 2.1086;

		function __construct($_GeniSys)
		{
			$this->_GeniSys = $_GeniSys;
			$this->bcc = $this->getBlockchainConf();
			$this->web3 = $this->blockchainConnection();
			$this->contract = new Contract($this->web3->provider, $this->bcc["abi"]);
			$this->icontract = new Contract($this->web3->provider, $this->bcc["iabi"]);
			$allowed = $this->checkBlockchainPermissions();
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

		private function storeBlockchainTransaction($action, $hash, $aid = 0)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  transactions (
					`uid`,
					`action`,
					`hash`,
					`aid`,
					`time`
				)  VALUES (
					:uid,
					:action,
					:hash,
					:aid,
					:time
				)
			");
			$pdoQuery->execute([
				":uid" => $_SESSION["GeniSysAI"]["Uid"],
				":action" => $action,
				':hash' => $this->_GeniSys->_helpers->oEncrypt($hash),
				":aid" => $aid,
				":time" => time()
			]);
			$txid = $this->_GeniSys->_secCon->lastInsertId();
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			return $txid;
		}

		private function storeUserHistory($action, $hashid, $uid, $aid = 0)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  history (
					`uid`,
					`tuid`,
					`taid`,
					`action`,
					`hash`,
					`time`
				)  VALUES (
					:uid,
					:tuid,
					:taid,
					:action,
					:hash,
					:time
				)
			");
			$pdoQuery->execute([
				":uid" => $_SESSION["GeniSysAI"]["Uid"],
				":tuid" => $uid,
				":taid" => $aid,
				":action" => $action,
				':hash' => $hashid,
				":time" => time()
			]);
			$txid = $this->_GeniSys->_secCon->lastInsertId();
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			return $txid;
		}

		public function getStaffs()
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT users.id,
					users.admin,
					users.pic,
					users.username,
					mqtta.lid,
					mqtta.status,
					mqtta.mqttu,
					mqtta.mqttp,
					mqtta.id AS aid
				FROM users users
				INNER JOIN mqtta mqtta
				ON users.id = mqtta.uid
				ORDER BY id DESC
			");
			$pdoQuery->execute();
			$response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $response;
		}

		public function getStaff($id)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT users.*,
					mqtta.lid,
					mqtta.status,
					mqtta.mqttu,
					mqtta.mqttp,
					mqtta.apub,
					mqtta.ip,
					mqtta.mac,
					mqtta.lt,
					mqtta.lg,
					mqtta.cpu,
					mqtta.mem,
					mqtta.hdd,
					mqtta.tempr,
					mqtta.id AS aid
				FROM users users
				INNER JOIN mqtta mqtta
				ON users.id = mqtta.uid
				WHERE users.id = :id
			");
			$pdoQuery->execute([
				":id" => $id
			]);
			$response=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			return $response;
		}

		public function createStaff()
		{
			if(!filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Staff name is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Staff username is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL)):
				return [
					"Response"=> "Failed",
					"Message" => "Staff email is required"
				];
			endif;

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT id
				FROM users
				WHERE username = :username
			");
			$pdoQuery->execute([
				":username" => filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING)
			]);
			$response=$pdoQuery->fetch(PDO::FETCH_ASSOC);

			if($response["id"]):
				return [
					"Response"=> "Failed",
					"Message" => "Staff username exists"
				];
			endif;

			if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "iotJumpWay location id is required"
				];
			endif;

			$uPass = $this->_GeniSys->_helpers->password();
			$passhash = $this->_GeniSys->_helpers->createPasswordHash($uPass);

			$bcPass = $this->_GeniSys->_helpers->password();

			$mqttUser = $this->_GeniSys->_helpers->generate_uuid();
			$mqttPass = $this->_GeniSys->_helpers->password();
			$mqttHash = create_hash($mqttPass);

			$pubKey = $this->_GeniSys->_helpers->generate_uuid();
			$privKey = $this->_GeniSys->_helpers->generateKey(32);
			$privKeyHash = $this->_GeniSys->_helpers->createPasswordHash($privKey);

			$unlocked =  $this->unlockBlockchainAccount();

			if($unlocked == "FAILED"):
				return [
					"Response"=> "Failed",
					"Message" => "Unlocking HIAS Blockhain Account Failed!"
				];
			endif;

			$newBcUser = $this->createBlockchainUser($bcPass);

			if($newBcUser == "FAILED"):
				return [
					"Response"=> "Failed",
					"Message" => "Creating New HIAS Blockhain Account Failed!"
				];
			endif;

			$lid = filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT);
			$admin = filter_input(INPUT_POST, "admin", FILTER_SANITIZE_NUMBER_INT) ? True : False;

			$htpasswd = new Htpasswd('/etc/nginx/security/htpasswd');
			$htpasswd->addUser(filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING), $uPass, Htpasswd::ENCTYPE_APR_MD5);

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  users (
					`admin`,
					`bcaddress`,
					`bcpw`,
					`cancelled`,
					`lid`,
					`name`,
					`email`,
					`username`,
					`password`,
					`nfc`,
					`gpstime`,
					`cz`,
					`czt`,
					`welcomed`,
					`created`
				)  VALUES (
					:admin,
					:bcaddress,
					:bcpw,
					:cancelled,
					:lid,
					:name,
					:email,
					:username,
					:password,
					:nfc,
					:gpstime,
					:cz,
					:czt,
					:welcomed,
					:time
				)
			");
			$pdoQuery->execute([
				":admin" => filter_input(INPUT_POST, "admin", FILTER_SANITIZE_NUMBER_INT) ? filter_input(INPUT_POST, "admin", FILTER_SANITIZE_NUMBER_INT) : 0,
				':bcaddress' => $this->_GeniSys->_helpers->oEncrypt($newBcUser),
				':bcpw' => $this->_GeniSys->_helpers->oEncrypt($bcPass),
				":cancelled" => 0,
				':lid' => $lid,
				":name" => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING),
				":email" => filter_input(INPUT_POST, "email", FILTER_SANITIZE_STRING),
				":username" => filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING),
				":password" =>  $this->_GeniSys->_helpers->oEncrypt($passhash),
				":nfc" => filter_input(INPUT_POST, "nfc", FILTER_SANITIZE_STRING) ? filter_input(INPUT_POST, "nfc", FILTER_SANITIZE_STRING) : "",
				":gpstime" => 0,
				":cz" => 0,
				":czt" => 0,
				":welcomed" => 0,
				":time" => time()
			]);
			$uid = $this->_GeniSys->_secCon->lastInsertId();
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			$query = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  mqtta  (
					`uid`,
					`lid`,
					`name`,
					`mqttu`,
					`mqttp`,
					`apub`,
					`aprv`,
					`time`
				)  VALUES (
					:uid,
					:lid,
					:name,
					:mqttu,
					:mqttp,
					:apub,
					:aprv,
					:time
				)
			");
			$query->execute([
				':uid' => $uid,
				':lid' => $lid,
				':name' => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING),
				':mqttu' => $this->_GeniSys->_helpers->oEncrypt($mqttUser),
				':mqttp' => $this->_GeniSys->_helpers->oEncrypt($mqttPass),
				':apub' => $pubKey,
				':aprv' => $this->_GeniSys->_helpers->oEncrypt($privKeyHash),
				':time' => time()
			]);
			$aid = $this->_GeniSys->_secCon->lastInsertId();

			$query = $this->_GeniSys->_secCon->prepare("
				UPDATE users
				SET aid = :aid
				WHERE id = :id
			");
			$query->execute(array(
				':aid' => $aid,
				':id' => $uid
			));

			$hash = "";
			$this->contract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["contract"]))->send("registerUser", $pubKey, $newBcUser, $admin, $uid, filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING), $lid, $aid, time(), $_SESSION["GeniSysAI"]["Uid"], ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash) {
				if ($err !== null) {
					$hash = "FAILED! " . $err;
					return;
				}
				$hash = $resp;
			});

			$actionMsg = "";
			$balanceMessage = "";

			if($hash == "FAILED"):
				$actionMsg = " HIAS Blockchain registerUser failed!";
			else:
				$txid = $this->storeBlockchainTransaction("Register User", $hash);
				$this->storeUserHistory("Register User", $txid, $uid);
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
				$txid = $this->storeBlockchainTransaction("iotJumpWay Register Authorized", $hash, $aid);
				$this->storeUserHistory("Register Authorized", $txid, $uid, $aid);
				$balance = $this->getBlockchainBalance();
				if($balanceMessage == ""):
					$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!";
				endif;
			endif;

			$query = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  mqttu  (
					`lid`,
					`uid`,
					`aid`,
					`uname`,
					`pw`
				)  VALUES (
					:lid,
					:uid,
					:aid,
					:uname,
					:pw
				)
			");
			$query->execute([
				':lid' => $lid,
				':uid' => $uid,
				':aid' => $aid,
				':uname' => $mqttUser,
				':pw' => $mqttHash
			]);

			$query = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  mqttua  (
					`lid`,
					`aid`,
					`uid`,
					`username`,
					`topic`,
					`rw`
				)  VALUES (
					:lid,
					:aid,
					:uid,
					:username,
					:topic,
					:rw
				)
			");
			$query->execute(array(
				':lid' => $lid,
				':aid' => $aid,
				':uid' => $uid,
				':username' => $mqttUser,
				':topic' => $lid."/Devices/#",
				':rw' => 4
			));

			$query = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  mqttua  (
					`lid`,
					`aid`,
					`uid`,
					`username`,
					`topic`,
					`rw`
				)  VALUES (
					:lid,
					:aid,
					:uid,
					:username,
					:topic,
					:rw
				)
			");
			$query->execute(array(
				':lid' => $lid,
				':aid' => $aid,
				':uid' => $uid,
				':username' => $mqttUser,
				':topic' => $lid."/Applications/#",
				':rw' => 4
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
				"Message" => "Staff & application created!" . $actionMsg . $balanceMessage,
				"UID" => $uid,
				"Uname" => filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING),
				"Upass" => $uPass,
				"AppID" => $pubKey,
				"AppKey" => $privKey,
				"BCU" => $newBcUser,
				"BCP" => $bcPass,
				"MU" => $mqttUser,
				"MP" => $mqttPass
			];
		}

		public function updateStaff()
		{
			$response = '';

			if(!filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "Staff ID is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Staff username is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "Staff iotJumpWay application location id is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "aid", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "Staff iotJumpWay application id is required"
				];
			endif;

			$id = filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT);
			$lid = filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT);
			$aid = filter_input(INPUT_POST, "aid", FILTER_SANITIZE_NUMBER_INT);
			$allowed = filter_input(INPUT_POST, "cancelled", FILTER_SANITIZE_STRING) ? False : True;
			$admin = filter_input(INPUT_POST, "admin", FILTER_SANITIZE_STRING) ? True : False;

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				UPDATE users
				SET username = :username,
					name = :name,
					admin = :admin,
					cancelled = :cancelled,
					nfc = :nfc,
					lid = :lid,
					aid = :aid
				WHERE id = :id
			");
			$pdoQuery->execute([
				":username" => filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING),
				":name" => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING),
				":admin" => filter_input(INPUT_POST, "admin", FILTER_SANITIZE_STRING) ? filter_input(INPUT_POST, "admin", FILTER_SANITIZE_STRING) : 0,
				":cancelled" => filter_input(INPUT_POST, "cancelled", FILTER_SANITIZE_STRING) ? filter_input(INPUT_POST, "cancelled", FILTER_SANITIZE_STRING) : 0,
				":nfc" => filter_input(INPUT_POST, "nfc", FILTER_SANITIZE_STRING) ? filter_input(INPUT_POST, "nfc", FILTER_SANITIZE_STRING) : "",
				":lid" => $lid,
				":aid" => $aid,
				":id" => $id
			]);

			$query = $this->_GeniSys->_secCon->prepare("
				UPDATE mqtta
				SET lid = :lid
				WHERE id = :id
			");
			$query->execute([
				':lid' => $lid,
				':id' => $aid
			]);

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

			$unlocked =  $this->unlockBlockchainAccount();

			if($unlocked == "FAILED"):
				return [
					"Response"=> "Failed",
					"Message" => "Unlocking HIAS Blockhain Account Failed!"
				];
			endif;

			$hash = "";
			$this->contract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["contract"]))->send("updateUser", filter_input(INPUT_POST, "identifier", FILTER_SANITIZE_STRING), "User", $allowed, $admin, $id, filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING), $lid, $aid, time(), ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash) {
				if ($err !== null) {
					$hash = "FAILED! " . $err;
					return;
				}
				$hash = $resp;
			});

			$actionMsg = "";
			$balanceMessage = "";

			if($hash == "FAILED"):
				$actionMsg = " HIAS Blockchain updateUser failed!";
			else:
				$txid = $this->storeBlockchainTransaction("Update User", $hash);
				$this->storeUserHistory("Update User", $txid, $id);
			endif;

			$this->contract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["contract"]))->send("updateApplication", filter_input(INPUT_POST, "identifier", FILTER_SANITIZE_STRING), "Application", $allowed, $admin, $lid, filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING), filter_input(INPUT_POST, "status", FILTER_SANITIZE_STRING), time(), ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash) {
				if ($err !== null) {
					$hash = "FAILED! " . $err;
					return;
				}
				$hash = $resp;
			});

			if($hash == "FAILED"):
				$actionMsg = " HIAS Blockchain updateApplication failed!";
			else:
				$txid = $this->storeBlockchainTransaction("Update User Application", $hash);
				$this->storeUserHistory("Update User Application", $txid, $id, $aid);
				$balance = $this->getBlockchainBalance();
				$balanceMessage = " You were rewarded for this action!<br /><br />Your Balance Is Now: " . $balance . " HIAS Ether!";
			endif;

			return [
				"Response"=> "OK",
				"Message" => "Staff updated!" . $actionMsg . $balanceMessage
			];
		}

		public function resetPassword()
		{
			$pass = $this->_GeniSys->_helpers->password();
			$passhash=$this->_GeniSys->_helpers->createPasswordHash($pass);

			$htpasswd = new Htpasswd('/etc/nginx/security/htpasswd');
			//$htpasswd->addUser(filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING), $pass, Htpasswd::ENCTYPE_APR_MD5);
			$htpasswd->updateUser(filter_input(INPUT_POST, "user", FILTER_SANITIZE_STRING), $pass, Htpasswd::ENCTYPE_APR_MD5);

			$query = $this->_GeniSys->_secCon->prepare("
				UPDATE users
				SET password = :password
				WHERE id = :id
			");
			$query->execute(array(
				':password' => $this->_GeniSys->_helpers->oEncrypt($passhash),
				':id' => filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)
			));

			$this->storeUserHistory("Reset User Password", 0, filter_input(INPUT_POST, "id", FILTER_SANITIZE_STRING));

			return [
				"Response" => "OK",
				"pw" => $pass
			];

		}

		public function resetMqtt()
		{
			$mqttPass = $this->_GeniSys->_helpers->password();
			$mqttHash = create_hash($mqttPass);

			$query = $this->_GeniSys->_secCon->prepare("
				UPDATE mqtta
				SET mqttp = :mqttp
				WHERE id = :id
			");
			$query->execute(array(
				':mqttp' => $this->_GeniSys->_helpers->oEncrypt($mqttPass),
				':id' => filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)
			));

			$query = $this->_GeniSys->_secCon->prepare("
				UPDATE mqttu
				SET pw = :pw
				WHERE aid = :aid
			");
			$query->execute(array(
				':pw' => $mqttHash,
				':aid' => filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)
			));

			$this->storeUserHistory("Reset User MQTT Password", 0, filter_input(INPUT_POST, "uid", FILTER_SANITIZE_STRING));

			return [
				"Response"=> "OK",
				"Message" => "MQTT password reset!",
				"P" => $mqttPass
			];

		}

		public function resetAppKey()
		{
			$privKey = $this->_GeniSys->_helpers->generateKey(32);
			$privKeyHash = $this->_GeniSys->_helpers->createPasswordHash($privKey);

			$query = $this->_GeniSys->_secCon->prepare("
				UPDATE mqtta
				SET aprv = :aprv
				WHERE id = :id
			");
			$query->execute(array(
				':aprv' => $this->_GeniSys->_helpers->oEncrypt($privKeyHash),
				':id' => filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)
			));

			$this->storeUserHistory("Reset User API Key", 0, filter_input(INPUT_POST, "uid", FILTER_SANITIZE_STRING));

			return [
				"Response"=> "OK",
				"Message" => "API Key password reset!",
				"P" => $privKey
			];

		}

		public function getMapMarkers($application)
		{
			if(!$application["lt"]):
				$lat = $this->lat;
				$lng = $this->lng;
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

		public function retrieveTransactions($user, $limit = 0, $order = "")
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
				WHERE uid = :id
				$orderer
				$limiter
			");
			$pdoQuery->execute([
				":id" => $user
			]);
			$response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			return $response;
		}

		public function retrieveHistory($user, $limit = 0, $order = "")
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
				WHERE uid = :id
				$orderer
				$limiter
			");
			$pdoQuery->execute([
				":id" => $user
			]);
			$response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			return $response;
		}

		public function retrieveStatuses($application, $limit = 0, $order = -1)
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

	}

	$Staff = new Staff($_GeniSys);

	if(filter_input(INPUT_POST, "update_staff", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($Staff->updateStaff()));
	endif;

	if(filter_input(INPUT_POST, "create_staff", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($Staff->createStaff()));
	endif;

	if(filter_input(INPUT_POST, "reset_mqtt_staff", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($Staff->resetMqtt()));
	endif;

	if(filter_input(INPUT_POST, "reset_u_pass", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($Staff->resetPassword()));
	endif;

	if(filter_input(INPUT_POST, "reset_appkey_staff", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($Staff->resetAppKey()));
	endif;