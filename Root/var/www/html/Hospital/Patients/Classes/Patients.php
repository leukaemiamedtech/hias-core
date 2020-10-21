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
			$this->cb = $this->getContextBrokerConf();
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

		public function getPatients($limit = 0, $order = "id DESC")
		{
			$limiter = "";
			if($limit != 0):
				$limiter = "&limit=" . $limit;
			endif;

			$patients = json_decode($this->contextBrokerRequest("GET", $this->cb["entities_url"] . "?type=Patient".$limiter, $this->createContextHeaders(), []), true);
			return $patients;
		}

		public function getPatientCategories()
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT category
				FROM cbPatientsCats
				ORDER BY category ASC
			");
			$pdoQuery->execute();
			$categories=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $categories;
		}

		public function getPatient($id, $attrs = Null)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT patients.*
				FROM patients patients
				WHERE patients.id = :id
			");
			$pdoQuery->execute([
				":id" => $id
			]);
			$patient=$pdoQuery->fetch(PDO::FETCH_ASSOC);

			if($attrs):
				$attrs="&attrs=" . $attrs;
			endif;

			$patient["context"] = json_decode($this->contextBrokerRequest("GET", $this->cb["entities_url"] . "/" . $patient["pub"] . "?type=Patient" . $attrs, $this->createContextHeaders(), []), true);
			return $patient;
		}

		public function createPatient()
		{

			if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "iotJumpWay location id is required"
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
					"Message" => "Staff name is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Staff username is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)):
				return [
					"Response"=> "Failed",
					"Message" => "Patient email is required"
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

			if($response["id"]):
				return [
					"Response"=> "Failed",
					"Message" => "Patient username exists"
				];
			endif;

			$uPass = $this->_GeniSys->_helpers->password();
			$passhash = $this->_GeniSys->_helpers->createPasswordHash($uPass);

			$mqttUser = $this->_GeniSys->_helpers->generate_uuid();
			$mqttPass = $this->_GeniSys->_helpers->generateKey(32);
			$mqttHash = create_hash($mqttPass);

			$bcPass = $this->_GeniSys->_helpers->password();

			$pubKey = $this->_GeniSys->_helpers->generate_uuid();
			$privKey = $this->_GeniSys->_helpers->generateKey(32);
			$privKeyHash = $this->_GeniSys->_helpers->createPasswordHash($privKey);

			$amqppubKey = $this->_GeniSys->_helpers->generate_uuid();
			$amqpprvKey = $this->_GeniSys->_helpers->generateKey(32);
			$amqpKeyHash = $this->_GeniSys->_helpers->createPasswordHash($amqpprvKey);

			$lid = filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT);
			$location = $this->getLocation($lid);

			$name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
			$username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);

			$active = filter_input(INPUT_POST, "active", FILTER_SANITIZE_NUMBER_INT) ? True : False;
			$admitted = filter_input(INPUT_POST, "admitted", FILTER_SANITIZE_NUMBER_INT) ? True : False;
			$discharged = filter_input(INPUT_POST, "admitted", FILTER_SANITIZE_NUMBER_INT) ? False : True;

			$htpasswd = new Htpasswd('/etc/nginx/security/patients');
			$htpasswd->addUser($username, $uPass, Htpasswd::ENCTYPE_APR_MD5);
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
					`pub`,
					`username`,
					`password`,
					`bcaddress`,
					`bcpass`
				)  VALUES (
					:pub,
					:username,
					:password,
					:bcaddress,
					:bcpass
				)
			");
			$pdoQuery->execute([
				":pub" => $pubKey,
				":username" => $username,
				":password" =>  $this->_GeniSys->_helpers->oEncrypt($passhash),
				":bcaddress" =>  $newBcUser,
				":bcpass" =>  $this->_GeniSys->_helpers->oEncrypt($bcPass)
			]);
			$pid = $this->_GeniSys->_secCon->lastInsertId();

			$data = [
				"id" => $pubKey,
				"type" => "Patient",
				"category" => [
					"value" => [filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)]
				],
				"name" => [
					"value" => $name
				],
				"username" => [
					"value" => $username
				],
				"description" => [
					"value" => filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)
				],
				"email" => [
					"value" => filter_input(INPUT_POST, "email", FILTER_SANITIZE_STRING)
				],
				"nfc" => [
					"value" => filter_input(INPUT_POST, "nfc", FILTER_SANITIZE_STRING)
				],
				"picture" => [
					"value" => "default.png"
				],
				"lid" => [
					"value" => $lid,
					"entity" => $location["context"]["Data"]["id"]
				],
				"pid" => [
					"value" => $pid,
					"entity" => $pubKey
				],
				"zid" => [
					"value" => 0,
					"entity" => "",
					"timestamp" => "",
					"welcomed" => ""
				],
				"status" => [
					"online" => "OFFLINE",
					"active" => filter_input(INPUT_POST, "active", FILTER_SANITIZE_NUMBER_INT) ? filter_input(INPUT_POST, "active", FILTER_SANITIZE_NUMBER_INT) : 0,
					"admitted" => filter_input(INPUT_POST, "admitted", FILTER_SANITIZE_NUMBER_INT) ? filter_input(INPUT_POST, "admitted", FILTER_SANITIZE_NUMBER_INT) : 0,
					"cancelled" => 0
				],
				"address" => [
					"type" => "PostalAddress",
					"value" => [
						"addressLocality" => filter_input(INPUT_POST, "addressLocality", FILTER_SANITIZE_STRING),
						"postalCode" => filter_input(INPUT_POST, "postalCode", FILTER_SANITIZE_STRING),
						"streetAddress" => filter_input(INPUT_POST, "streetAddress", FILTER_SANITIZE_STRING)
					]
				],
				"keys" => [
					"public" => $pubKey,
					"private" => $this->_GeniSys->_helpers->oEncrypt($privKeyHash),
					"nfc" => filter_input(INPUT_POST, "nfc", FILTER_SANITIZE_STRING),
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"blockchain" => [
					"address" => $newBcUser,
					"password" => $this->_GeniSys->_helpers->oEncrypt($bcPass),
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
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

			$response = json_decode($this->contextBrokerRequest("POST", $this->cb["entities_url"] . "?type=Patient", $this->createContextHeaders(), json_encode($data)), true);
			if($response["Response"]=="OK"):


				$hash = "";
				$this->pcontract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["pcontract"]))->send("registerPatient", $pubKey, $newBcUser, $active, $admitted, $discharged, $pid, $pid, time(), $_SESSION["GeniSysAI"]["Uid"], ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash) {
					if ($err !== null) {
						$hash = "FAILED";
						return;
					}
					$hash = $resp;
				});

				$actionMsg = "";
				$balanceMessage = "";

				if($hash == "FAILED"):
					$actionMsg = " HIAS Blockchain registerPatient failed!\n";
				else:
					$txid = $this->storeBlockchainTransaction("Register Patient", $hash, $pid);
					$this->storeUserHistory("Register Patient", $txid, $pid);
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
					$txid = $this->storeBlockchainTransaction("iotJumpWay Register Authorized", $hash, $pid);
					$this->storeUserHistory("Register Authorized", $txid, $pid);
					$balance = $this->getBlockchainBalance();
					if($balanceMessage == ""):
						$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
					endif;
				endif;

				$query = $this->_GeniSys->_secCon->prepare("
					INSERT INTO  mqttu  (
						`lid`,
						`pid`,
						`uname`,
						`pw`
					)  VALUES (
						:lid,
						:pid,
						:uname,
						:pw
					)
				");
				$query->execute([
					':lid' => $lid,
					':pid' => $pid,
					':uname' => $mqttUser,
					':pw' => $mqttHash
				]);

				$query = $this->_GeniSys->_secCon->prepare("
					INSERT INTO  mqttua  (
						`lid`,
						`pid`,
						`username`,
						`topic`,
						`rw`
					)  VALUES (
						:lid,
						:pid,
						:username,
						:topic,
						:rw
					)
				");
				$query->execute(array(
					':lid' => $lid,
					':pid' => $pid,
					':username' => $mqttUser,
					':topic' => $location["context"]["Data"]["id"]."/Patients/#",
					':rw' => 4
				));

				return [
					"Response"=> "OK",
					"Message" => "Patient created! " . $actionMsg . $balanceMessage,
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
			else:
				return [
					"Response" => "FAILED",
					"Patient create failed!"
				];
			endif;
		}

		public function updatePatient()
		{

			if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT)):
				return [
					"Response"=> "Failed",
					"Message" => "iotJumpWay location id is required"
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
					"Message" => "Staff name is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Staff username is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)):
				return [
					"Response"=> "Failed",
					"Message" => "Patient email is required"
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

			if(!filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Patient username is required"
				];
			endif;

			$pid = filter_input(INPUT_GET, "patient", FILTER_SANITIZE_NUMBER_INT);
			$patient = $this->getPatient($pid);

			if($patient["context"]["Data"]["status"]["cancelled"]):
				return [
					"Response"=> "Failed",
					"Message" => "This patient is cancelled, to allow access again you must create a new patient."
				];
			endif;

			$lid = filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT);
			$location = $this->getLocation($lid);

			$name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
			$username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);

			$email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_STRING);

			$active = filter_input(INPUT_POST, "active", FILTER_SANITIZE_NUMBER_INT) ? True : False;
			$admitted = filter_input(INPUT_POST, "admitted", FILTER_SANITIZE_NUMBER_INT) ? True : False;
			$discharged = filter_input(INPUT_POST, "admitted", FILTER_SANITIZE_NUMBER_INT) ? False : True;

			$data = [
				"category" => [
					"value" => [filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)]
				],
				"name" => [
					"value" => $name
				],
				"username" => [
					"value" => $username
				],
				"description" => [
					"value" => filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)
				],
				"email" => [
					"value" => filter_input(INPUT_POST, "email", FILTER_SANITIZE_STRING)
				],
				"nfc" => [
					"value" => filter_input(INPUT_POST, "nfc", FILTER_SANITIZE_STRING)
				],
				"picture" => [
					"value" => "default.png"
				],
				"lid" => [
					"value" => $lid,
					"entity" => $location["context"]["Data"]["id"]
				],
				"status" => [
					"online" => "OFFLINE",
					"active" => filter_input(INPUT_POST, "active", FILTER_SANITIZE_NUMBER_INT) ? filter_input(INPUT_POST, "active", FILTER_SANITIZE_NUMBER_INT) : 0,
					"admitted" => filter_input(INPUT_POST, "admitted", FILTER_SANITIZE_NUMBER_INT) ? filter_input(INPUT_POST, "admitted", FILTER_SANITIZE_NUMBER_INT) : 0,
					"cancelled" => filter_input(INPUT_POST, "cancelled", FILTER_SANITIZE_NUMBER_INT) ? filter_input(INPUT_POST, "cancelled", FILTER_SANITIZE_NUMBER_INT) : 0
				],
				"address" => [
					"type" => "PostalAddress",
					"value" => [
						"addressLocality" => filter_input(INPUT_POST, "addressLocality", FILTER_SANITIZE_STRING),
						"postalCode" => filter_input(INPUT_POST, "postalCode", FILTER_SANITIZE_STRING),
						"streetAddress" => filter_input(INPUT_POST, "streetAddress", FILTER_SANITIZE_STRING)
					]
				],
				"dateModified" => [
					"type" => "DateTime",
					"value" => date('Y-m-d\TH:i:s.Z\Z', time())
				]
			];

			$response = json_decode($this->contextBrokerRequest("PATCH", $this->cb["entities_url"] . "/" . $patient["context"]["Data"]["id"] . "/attrs?type=Patient", $this->createContextHeaders(), json_encode($data)), true);

			if($response["Response"]=="OK"):

				$pdoQuery = $this->_GeniSys->_secCon->prepare("
					UPDATE patients
					SET username = :username
					WHERE id = :id
				");
				$pdoQuery->execute([
					":username" => $username,
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
				$this->pcontract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["pcontract"]))->send("updatePatient", $patient["context"]["Data"]["id"], $patient["context"]["Data"]["blockchain"]["address"], $active, $admitted, $discharged, $pid, $pid, time(), ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash) {
					if ($err !== null) {
						$hash = "FAILED";
						return;
					}
					$hash = $resp;
				});

				$actionMsg = "";
				$balanceMessage = "";

				if($hash == "FAILED"):
					$actionMsg = " HIAS Blockchain updatePatient failed!\n";
				else:
					$txid = $this->storeBlockchainTransaction("Update Patient", $hash, $pid);
					$this->storeUserHistory("Update Patient", $txid, $pid);
					$balance = $this->getBlockchainBalance();
					$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
				endif;

				if(!$patient["context"]["Data"]["status"]["cancelled"] && filter_input(INPUT_POST, "cancelled", FILTER_SANITIZE_STRING)):

					$query = $this->_GeniSys->_secCon->prepare("
						DELETE FROM mqttu
						WHERE pid = :pid
					");
					$query->execute([
						':pid' => $pid
					]);

					$query = $this->_GeniSys->_secCon->prepare("
						DELETE FROM mqttua
						WHERE aid = :aid
					");
					$query->execute([
						':aid' => $pid
					]);

					$this->pcontract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["pcontract"]))->send("deregisterUser", $patient["context"]["Data"]["id"], ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
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
						$txid = $this->storeBlockchainTransaction("Deregister Patient", $hash, $pid);
						$this->storeUserHistory("Deregister Patient", $txid, $pid);
						$balance = $this->getBlockchainBalance();
						if($balanceMessage == ""):
							$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
						endif;
					endif;

				endif;

				return [
					"Response"=> "OK",
					"Message" => "Patient updated!" . $actionMsg . $balanceMessage
				];
			else:
				return [
					"Response"=> "FAILED",
					"Message" => "Patient update failed!"
				];
			endif;
		}

		public function resetMqtt()
		{
			$mqttPass = $this->_GeniSys->_helpers->generateKey(32);
			$mqttHash = create_hash($mqttPass);

			$pid = filter_input(INPUT_GET, 'patient', FILTER_SANITIZE_NUMBER_INT);
			$patient = $this->getPatient($pid);

			$data = [
				"mqtt" => [
					"username" => $patient["context"]["Data"]["mqtt"]["username"],
					"password" => $this->_GeniSys->_helpers->oEncrypt($mqttPass),
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"dateModified" => [
					"type" => "DateTime",
					"value" => date('Y-m-d\TH:i:s.Z\Z', time())
				]
			];

			$response = json_decode($this->contextBrokerRequest("PATCH", $this->cb["entities_url"] . "/" . $patient["context"]["Data"]["id"] . "/attrs?type=Patient", $this->createContextHeaders(), json_encode($data)), true);
			if($response["Response"]=="OK"):
				$query = $this->_GeniSys->_secCon->prepare("
					UPDATE mqttu
					SET pw = :pw
					WHERE pid = :pid
				");
				$query->execute(array(
					':pw' => $mqttHash,
					':pid' => $patient["context"]["Data"]["pid"]["value"]
				));

				$this->storeUserHistory("Reset User MQTT Password", 0, $pid);

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
			$pid = filter_input(INPUT_GET, 'patient', FILTER_SANITIZE_NUMBER_INT);
			$patient = $this->getPatient($pid);

			$amqpPass = $this->_GeniSys->_helpers->generateKey(32);
			$amqpHash = $this->_GeniSys->_helpers->createPasswordHash($amqpPass);

			$data = [
				"amqp" => [
					"username" => $patient["context"]["Data"]["amqp"]["username"],
					"password" => $this->_GeniSys->_helpers->oEncrypt($amqpPass),
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"dateModified" => [
					"type" => "DateTime",
					"value" => date('Y-m-d\TH:i:s.Z\Z', time())
				]
			];

			$response = json_decode($this->contextBrokerRequest("PATCH", $this->cb["entities_url"] . "/" . $patient["context"]["Data"]["id"] . "/attrs?type=Patient", $this->createContextHeaders(), json_encode($data)), true);

			if($response["Response"]=="OK"):

				$this->storeUserHistory("Reset Patient Application AMQP Password", 0, $pid);

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

			$pid = filter_input(INPUT_GET, 'patient', FILTER_SANITIZE_NUMBER_INT);
			$patient = $this->getPatient($pid);

			$privKey = $this->_GeniSys->_helpers->generateKey(32);
			$privKeyHash = $this->_GeniSys->_helpers->createPasswordHash($privKey);

			$htpasswd = new Htpasswd('/etc/nginx/security/patients');
			$htpasswd->updateUser($patient["context"]["Data"]["id"], $privKey, Htpasswd::ENCTYPE_APR_MD5);

			$data = [
				"keys" => [
					"public" => $patient["context"]["Data"]["keys"]["public"],
					"private" => $this->_GeniSys->_helpers->oEncrypt($privKeyHash),
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"dateModified" => [
					"type" => "DateTime",
					"value" => date('Y-m-d\TH:i:s.Z\Z', time())
				]
			];

			$response = json_decode($this->contextBrokerRequest("PATCH", $this->cb["entities_url"] . "/" . $patient["context"]["Data"]["id"] . "/attrs?type=Patient", $this->createContextHeaders(), json_encode($data)), true);

			if($response["Response"]=="OK"):

				$this->storeUserHistory("Reset Patient Application Key", 0, $pid);

				return [
					"Response"=> "OK",
					"Message" => "App key reset!",
					"P" => $privKey
				];

			else:
				return [
					"Response"=> "FAILED",
					"Message" => "App key reset failed!"
				];
			endif;

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

	if(filter_input(INPUT_POST, "reset_patient_amqp", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($Patients->resetAppAmqpKey()));
	endif;