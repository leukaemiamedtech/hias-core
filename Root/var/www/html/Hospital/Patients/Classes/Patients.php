<?php

require __DIR__ . '/../../../../vendor/autoload.php';

use Web3\Web3;
use Web3\Contract;
use Web3\Utils;

	class Patients
	{

		function __construct($_GeniSys)
		{
			$this->_GeniSys = $_GeniSys;
			$this->bcc = $this->getBlockchainConf();
			$this->web3 = $this->blockchainConnection();
			$this->contract = new Contract($this->web3->provider, $this->bcc["abi"]);
			$this->checkBlockchainPermissions();
			$this->pcontract = new Contract($this->web3->provider, $this->bcc["pabi"]);
			$this->checkBlockchainPatientPermissions();
			$this->icontract = new Contract($this->web3->provider, $this->bcc["iabi"]);
		}

		public function getBlockchainConf()
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

		private function checkBlockchainPatientPermissions()
		{
			$allowed = "";
			$this->pcontract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["pcontract"]))->call("userAllowed", ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$allowed) {
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

		private function storeBlockchainTransaction($action, $hash, $pid)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  transactions (
					`uid`,
					`action`,
					`pid`,
					`hash`,
					`time`
				)  VALUES (
					:uid,
					:action,
					:pid,
					:hash,
					:time
				)
			");
			$pdoQuery->execute([
				":uid" => $_SESSION["GeniSysAI"]["Uid"],
				":action" => $action,
				":pid" => $pid,
				':hash' => $this->_GeniSys->_helpers->oEncrypt($hash),
				":time" => time()
			]);
			$txid = $this->_GeniSys->_secCon->lastInsertId();
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			return $txid;
		}

		private function storeUserHistory($action, $hashid, $patient)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  history (
					`uid`,
					`tpid`,
					`action`,
					`hash`,
					`time`
				)  VALUES (
					:uid,
					:tpid,
					:action,
					:hash,
					:time
				)
			");
			$pdoQuery->execute([
				":uid" => $_SESSION["GeniSysAI"]["Uid"],
				":tpid" => $patient,
				":action" => $action,
				':hash' => $hashid,
				":time" => time()
			]);
			$txid = $this->_GeniSys->_secCon->lastInsertId();
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			return $txid;
		}

		public function retrieveTransactions($patient, $limit = 0, $order = "")
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
				WHERE pid = :id
				$orderer
				$limiter
			");
			$pdoQuery->execute([
				":id" => $patient
			]);
			$response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			return $response;
		}

		public function retrieveContractTransaction($txn)
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

		public function retrieveTransactionReceipt($hash)
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

		public function retrieveHistory($patient, $limit = 0, $order = "")
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
				WHERE tpid = :id
				$orderer
				$limiter
			");
			$pdoQuery->execute([
				":id" => $patient
			]);
			$response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			return $response;
		}

		public function getPatients()
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT patients.id,
					patients.username,
					patients.pic,
					mqtta.lid,
					mqtta.status,
					mqtta.mqttu,
					mqtta.mqttp,
					mqtta.id AS aid
				FROM patients patients
				INNER JOIN mqtta mqtta
				ON mqtta.id = patients.aid
				ORDER BY patients.id DESC
			");
			$pdoQuery->execute();
			$response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $response;
		}

		public function getPatient($id)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT patients.*,
					mqtta.id as aid,
					mqtta.lid,
					mqtta.status,
					mqtta.mqttu,
					mqtta.mqttp,
					mqtta.apub,
					mqtta.lt,
					mqtta.lg,
					mqtta.cpu,
					mqtta.mem,
					mqtta.hdd,
					mqtta.tempr,
					mqtta.id AS aid
				FROM patients patients
				INNER JOIN mqtta mqtta
				ON patients.id = mqtta.pid
				WHERE patients.id = :id
			");
			$pdoQuery->execute([
				":id" => $id
			]);
			$response=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			return $response;
		}

		public function createPatient()
		{
			if(!filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)):
				return [
					"Response"=> "Failed",
					"Message" => "Patient email is required"
				];
			endif;

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT id
				FROM patients
				WHERE email = :email
			");
			$pdoQuery->execute([
				":email" => filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)
			]);
			$response=$pdoQuery->fetch(PDO::FETCH_ASSOC);

			if($response["id"]):
				return [
					"Response"=> "Failed",
					"Message" => "Patient email exists"
				];
			endif;

			if(!filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Patient username is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "Patient iotJumpWay location ID is required"
				];
			endif;

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT id
				FROM patients
				WHERE username = :username
			");
			$pdoQuery->execute([
				":username" => filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING)
			]);
			$response=$pdoQuery->fetch(PDO::FETCH_ASSOC);

			if($response["id"]):
				return [
					"Response"=> "Failed",
					"Message" => "Patient username exists"
				];
			endif;
			if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "Patient iotJumpWay location id is required"
				];
			endif;

			$uPass = $this->_GeniSys->_helpers->password();
			$passhash = $this->_GeniSys->_helpers->createPasswordHash($uPass);

			$mqttUser = $this->_GeniSys->_helpers->generate_uuid();
			$mqttPass = $this->_GeniSys->_helpers->password();
			$mqttHash = create_hash($mqttPass);

			$bcPass = $this->_GeniSys->_helpers->password();

			$pubKey = $this->_GeniSys->_helpers->generate_uuid();
			$privKey = $this->_GeniSys->_helpers->generate_uuid();
			$privKeyHash = $this->_GeniSys->_helpers->createPasswordHash($privKey);

			$lid = filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT);

			$htpasswd = new Htpasswd('/etc/nginx/security/patients');
			$htpasswd->addUser(filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING), $uPass, Htpasswd::ENCTYPE_APR_MD5);
			$htpasswd->addUser($pubKey, $privKey, Htpasswd::ENCTYPE_APR_MD5);

			$unlocked =  $this->unlockBlockchainAccount();

			if($unlocked == "FAILED"):
				return [
					"Response"=> "Failed",
					"Message" => "Unlocking HIAS Blockhain Account Failed!"
				];
			endif;

			$newBcUser = $this->createBlockchainUser($bcPass);

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				INSERT INTO patients (
					`uid`,
					`lid`,
					`name`,
					`email`,
					`username`,
					`password`,
					`bcaddress`,
					`bcpass`,
					`gpstime`,
					`created`
				)  VALUES (
					:uid,
					:lid,
					:name,
					:email,
					:username,
					:password,
					:bcaddress,
					:bcpass,
					:gpstime,
					:time
				)
			");
			$pdoQuery->execute([
				":uid" => $_SESSION["GeniSysAI"]["Uid"],
				":lid" => $lid,
				":name" => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING),
				":email" => filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL),
				":username" => filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING),
				":password" =>  $this->_GeniSys->_helpers->oEncrypt($passhash),
				":bcaddress" =>  $this->_GeniSys->_helpers->oEncrypt($newBcUser),
				":bcpass" =>  $this->_GeniSys->_helpers->oEncrypt($bcPass),
				":gpstime" => 0,
				":time" => time()
			]);
			$pid = $this->_GeniSys->_secCon->lastInsertId();
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			$query = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  mqtta  (
					`pid`,
					`lid`,
					`name`,
					`mqttu`,
					`mqttp`,
					`apub`,
					`aprv`,
					`bcaddress`,
					`bcpw`,
					`time`
				)  VALUES (
					:pid,
					:lid,
					:name,
					:mqttu,
					:mqttp,
					:apub,
					:aprv,
					:bcaddress,
					:bcpw,
					:time
				)
			");
			$query->execute([
				':pid' => $pid,
				':lid' => $lid,
				':name' => "Patient " . $pid,
				':mqttu' =>$this->_GeniSys->_helpers->oEncrypt($mqttUser),
				':mqttp' =>$this->_GeniSys->_helpers->oEncrypt($mqttPass),
				':apub' => $pubKey,
				':aprv' => $this->_GeniSys->_helpers->oEncrypt($privKeyHash),
				':bcaddress' => $this->_GeniSys->_helpers->oEncrypt($newBcUser),
				':bcpw' => $this->_GeniSys->_helpers->oEncrypt($bcPass),
				':time' => time()
			]);
			$aid = $this->_GeniSys->_secCon->lastInsertId();

			$hash = "";
			$this->pcontract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["pcontract"]))->send("registerPatient", $pubKey, $newBcUser, True, False, False, $pid, $aid, time(), $_SESSION["GeniSysAI"]["Uid"], ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash) {
				if ($err !== null) {
					$hash = "FAILED";
					return;
				}
				$hash = $resp;
			});

			$actionMsg = "";
			$balanceMessage = "";

			if($hash == "FAILED"):
				$actionMsg = " HIAS Blockchain registerPatient failed!";
			else:
				$txid = $this->storeBlockchainTransaction("Register Patient", $hash, $pid);
				$this->storeUserHistory("Register Patient", $txid, $pid);
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
				$txid = $this->storeBlockchainTransaction("iotJumpWay Register Authorized", $hash, $pid);
				$this->storeUserHistory("Register Authorized", $txid, $pid);
				$balance = $this->getBlockchainBalance();
				if($balanceMessage == ""):
					$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!";
				endif;
			endif;

			$query = $this->_GeniSys->_secCon->prepare("
				UPDATE patients
				SET aid = :aid
				WHERE id = :id
			");
			$query->execute(array(
				':aid'=> $aid,
				':id'=> $pid
			));

			$query = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  mqttu  (
					`lid`,
					`pid`,
					`aid`,
					`uname`,
					`pw`
				)  VALUES (
					:lid,
					:pid,
					:aid,
					:uname,
					:pw
				)
			");
			$query->execute([
				':lid' => $lid,
				':pid' => $pid,
				':aid' => $aid,
				':uname' => $mqttUser,
				':pw' => $mqttHash
			]);

			$query = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  mqttua  (
					`lid`,
					`pid`,
					`aid`,
					`username`,
					`topic`,
					`rw`
				)  VALUES (
					:lid,
					:pid,
					:aid,
					:username,
					:topic,
					:rw
				)
			");
			$query->execute(array(
				':lid' => $lid,
				':pid' => $pid,
				':aid' => $aid,
				':username' => $mqttUser,
				':topic' => $lid."/Patients/#",
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
				"Message" => "Patient & application created! " . $actionMsg . $balanceMessage,
				"UID" => $pid,
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

		public function updatePatient()
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
					"Message" => "Patient name is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)):
				return [
					"Response"=> "Failed",
					"Message" => "Patient email is required"
				];
			endif;

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT id
				FROM patients
				WHERE email = :email
			");
			$pdoQuery->execute([
				":email" => filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)
			]);
			$response=$pdoQuery->fetch(PDO::FETCH_ASSOC);

			if($response["id"] && $response["id"] != filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "Patient email exists"
				];
			endif;

			if(!filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Patient username is required"
				];
			endif;

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT id
				FROM patients
				WHERE username = :username
			");
			$pdoQuery->execute([
				":username" => filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING)
			]);
			$response=$pdoQuery->fetch(PDO::FETCH_ASSOC);

			if($response["id"] && $response["id"] != filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "Patient username exists"
				];
			endif;

			if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "iotJumpWay location id is required"
				];
			endif;

			$name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
			$username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);
			$email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_STRING);
			$active = filter_input(INPUT_POST, "active", FILTER_SANITIZE_NUMBER_INT) ? True : False;
			$admitted = filter_input(INPUT_POST, "admitted", FILTER_SANITIZE_NUMBER_INT) ? True : False;
			$discharged = filter_input(INPUT_POST, "discharged", FILTER_SANITIZE_NUMBER_INT) ? True : False;
			$aid = filter_input(INPUT_POST, "aid", FILTER_SANITIZE_NUMBER_INT);
			$pid = filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT);

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				UPDATE patients
				SET name = :name,
					username = :username,
					email = :email,
					active = :active,
					admitted = :admitted,
					discharged = :discharged
				WHERE id = :id
			");
			$pdoQuery->execute([
				":name" => $name,
				":username" => $username,
				":email" => $email,
				":active" => filter_input(INPUT_POST, "active", FILTER_SANITIZE_NUMBER_INT) ? 1 : 0,
				":admitted" => filter_input(INPUT_POST, "admitted", FILTER_SANITIZE_NUMBER_INT) ? 1 : 0,
				":discharged" => filter_input(INPUT_POST, "discharged", FILTER_SANITIZE_NUMBER_INT) ? 1 : 0,
				":id" => $pid
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
			$this->pcontract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["pcontract"]))->send("updatePatient", filter_input(INPUT_POST, "identifier", FILTER_SANITIZE_STRING), filter_input(INPUT_POST, "bcu", FILTER_SANITIZE_STRING), $active, $admitted, $discharged, $pid, $aid, time(), ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash) {
				if ($err !== null) {
					$hash = "FAILED";
					return;
				}
				$hash = $resp;
			});

			$actionMsg = "";
			$balanceMessage = "";

			if($hash == "FAILED"):
				$actionMsg = " HIAS Blockchain updatePatient failed!";
			else:
				$txid = $this->storeBlockchainTransaction("Update Patient", $hash, $pid);
				$this->storeUserHistory("Update Patient", $txid, $pid);
				$balance = $this->getBlockchainBalance();
				$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!";
			endif;

			return [
				"Response"=> "OK",
				"Message" => "Patient updated!" . $actionMsg . $balanceMessage
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
			$query->execute([
				':mqttp' => $this->_GeniSys->_helpers->oEncrypt($mqttPass),
				':id' => filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)
			]);

			$query = $this->_GeniSys->_secCon->prepare("
				UPDATE mqttu
				SET pw = :pw
				WHERE aid = :aid
			");
			$query->execute([
				':pw' => $mqttHash,
				':aid' => filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)
			]);

			$this->storeUserHistory("Reset Patient MQTT Password", 0, filter_input(INPUT_POST, "pid", FILTER_SANITIZE_STRING));

			return [
				"Response"=> "OK",
				"Message" => "MQTT password reset!",
				"P" => $mqttPass
			];

		}

		public function resetAppKey()
		{
			$identifier = filter_input(INPUT_POST, "identifier", FILTER_SANITIZE_STRING);
			$pid = filter_input(INPUT_POST, "pid", FILTER_SANITIZE_NUMBER_INT);
			$id = filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT);

			$privKey = $this->_GeniSys->_helpers->generateKey(32);
			$privKeyHash = $this->_GeniSys->_helpers->createPasswordHash($privKey);

			$htpasswd = new Htpasswd('/etc/nginx/security/patients');
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

			$this->storeUserHistory("Reset Patient Key", 0, filter_input(INPUT_POST, "pid", FILTER_SANITIZE_STRING));

			return [
				"Response"=> "OK",
				"Message" => "Application key reset!",
				"P" => $privKey
			];

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

	$Patients = new Patients($_GeniSys);

	if(filter_input(INPUT_POST, "create_patient", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($Patients->createPatient()));
	endif;
	if(filter_input(INPUT_POST, "update_patient", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($Patients->updatePatient()));
	endif;
	if(filter_input(INPUT_POST, "reset_mqtt_patient", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($Patients->resetMqtt()));
	endif;
	if(filter_input(INPUT_POST, "reset_pt_apriv", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($Patients->resetAppKey()));
	endif;