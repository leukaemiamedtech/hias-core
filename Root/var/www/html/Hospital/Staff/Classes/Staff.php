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

			if(isSet($_SESSION["GeniSysAI"]["Active"])):
				$this->bcc = $this->getBlockchainConf();
				$this->web3 = $this->blockchainConnection();
				$this->contract = new Contract($this->web3->provider, $this->bcc["abi"]);
				$this->icontract = new Contract($this->web3->provider, $this->bcc["iabi"]);
				$this->pcontract = new Contract($this->web3->provider, $this->bcc["pabi"]);
				$allowed = $this->checkBlockchainPermissions();
			endif;
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

		private function storeBlockchainTransaction($action, $hash, $aid = 0, $uid = 0)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  transactions (
					`uid`,
					`tuid`,
					`action`,
					`hash`,
					`aid`,
					`time`
				)  VALUES (
					:uid,
					:tuid,
					:action,
					:hash,
					:aid,
					:time
				)
			");
			$pdoQuery->execute([
				":uid" => $_SESSION["GeniSysAI"]["Uid"],
				":tuid" => $uid,
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

		public function getStaffCategories()
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT category
				FROM cbUserCats
				ORDER BY category ASC
			");
			$pdoQuery->execute();
			$categories=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $categories;
		}

		public function getStaffs($limit = 0, $order = "id DESC")
		{
			$limiter = "";
			if($limit != 0):
				$limiter = "&limit=" . $limit;
			endif;

			$staff = json_decode($this->contextBrokerRequest("GET", $this->cb["entities_url"] . "?type=Staff".$limiter, $this->createContextHeaders(), []), true);
			return $staff;
		}

		public function getStaff($id, $attrs = Null)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT users.*
				FROM users users
				WHERE users.id = :id
			");
			$pdoQuery->execute([
				":id" => $id
			]);
			$staff=$pdoQuery->fetch(PDO::FETCH_ASSOC);

			if($attrs):
				$attrs="&attrs=" . $attrs;
			endif;

			$staff["context"] = json_decode($this->contextBrokerRequest("GET", $this->cb["entities_url"] . "/" . $staff["pub"] . "?type=Staff" . $attrs, $this->createContextHeaders(), []), true);
			return $staff;
		}

		public function createStaff()
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

			if(!filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL)):
				return [
					"Response"=> "Failed",
					"Message" => "Staff email is required"
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

			$uPass = $this->_GeniSys->_helpers->password();
			$passhash = $this->_GeniSys->_helpers->createPasswordHash($uPass);

			$bcPass = $this->_GeniSys->_helpers->password();

			$mqttUser = $this->_GeniSys->_helpers->generate_uuid();
			$mqttPass = $this->_GeniSys->_helpers->password();
			$mqttHash = create_hash($mqttPass);

			$pubKey = $this->_GeniSys->_helpers->generate_uuid();
			$privKey = $this->_GeniSys->_helpers->generateKey(32);
			$privKeyHash = $this->_GeniSys->_helpers->createPasswordHash($privKey);

			$amqppubKey = $this->_GeniSys->_helpers->generate_uuid();
			$amqpprvKey = $this->_GeniSys->_helpers->generateKey(32);
			$amqpKeyHash = $this->_GeniSys->_helpers->createPasswordHash($amqpprvKey);

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
			$location = $this->getLocation($lid);

			$admin = filter_input(INPUT_POST, "admin", FILTER_SANITIZE_NUMBER_INT) ? True : False;
			$name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);

			$htpasswd = new Htpasswd('/etc/nginx/security/htpasswd');
			$htpasswd->addUser(filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING), $uPass, Htpasswd::ENCTYPE_APR_MD5);

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				INSERT INTO users (
					`username`,
					`password`,
					`bcaddress`,
					`bcpw`
				)  VALUES (
					:username,
					:password,
					:bcaddress,
					:bcpw
				)
			");
			$pdoQuery->execute([
				":username" => filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING),
				":password" => $this->_GeniSys->_helpers->oEncrypt($passhash),
				":bcaddress" => $newBcUser,
				":bcpw" => $this->_GeniSys->_helpers->oEncrypt($bcPass)
			]);
			$uid = $this->_GeniSys->_secCon->lastInsertId();
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			$query = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  mqtta  (
					`id`
				)  VALUES (
					:id
				)
			");
			$query->execute([
				':id' => 0
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
					"value" => 0
				],
				"location" => [
					"type" => "geo:json",
					"value" => [
						"type" => "Point",
						"coordinates" => [0, 0]
					]
				],
				"device" => [
					"name" => "",
					"manufacturer" => "",
					"model" => "",
					"version" => ""
				],
				"os" => [
					"name" => "",
					"manufacturer" => "",
					"version" => ""
				],
				"protocols" => ["MQTT"],
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
					"value" => $this->_GeniSys->_helpers->oEncrypt(""),
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"mac" => [
					"value" => $this->_GeniSys->_helpers->oEncrypt(""),
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"bluetooth" => [
					"address" => "",
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"ai" => [],
				"sensors" => [],
				"actuators" => [],
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
					UPDATE mqtta
					SET apub = :apub
					WHERE id = :id
				");
				$query->execute(array(
					':apub'=> $response["Entity"]["id"],
					':id'=> $aid
				));

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
						`uid`,
						`aid`,
						`username`,
						`topic`,
						`rw`
					)  VALUES (
						:lid,
						:uid,
						:aid,
						:username,
						:topic,
						:rw
					)
				");
				$query->execute(array(
					':lid' => $lid,
					':uid' => $uid,
					':aid' => $aid,
					':username' => $mqttUser,
					':topic' => $location["context"]["Data"]["id"] . "/Devices/#",
					':rw' => 4
				));

				$query = $this->_GeniSys->_secCon->prepare("
					INSERT INTO  mqttua  (
						`lid`,
						`uid`,
						`aid`,
						`username`,
						`topic`,
						`rw`
					)  VALUES (
						:lid,
						:uid,
						:aid,
						:username,
						:topic,
						:rw
					)
				");
				$query->execute(array(
					':lid' => $lid,
					':uid' => $uid,
					':aid' => $aid,
					':username' => $mqttUser,
					':topic' => $location["context"]["Data"]["id"] . "/Applications/#",
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
					$txid = $this->storeBlockchainTransaction("Register Application", $hash, $aid, $uid);
					$this->storeUserHistory("Register Application", $txid, $uid, $aid);
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
					$txid = $this->storeBlockchainTransaction("iotJumpWay Register Authorized User Application", $hash, $aid, $uid);
					$this->storeUserHistory("iotJumpWay Register Authorized User Application", $txid, $uid, $aid);
					$balance = $this->getBlockchainBalance();
					if($balanceMessage == ""):
						$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
					endif;
				endif;

				$data = [
					"id" => $pubKey,
					"type" => "Staff",
					"category" => [
						"value" => [filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)]
					],
					"name" => [
						"value" => $name
					],
					"username" => [
						"value" => filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING)
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
					"zid" => [
						"value" => 0,
						"entity" => "",
						"timestamp" => "",
						"welcomed" => ""
					],
					"aid" => [
						"value" => $aid,
						"entity" => $response["Entity"]["id"]
					],
					"uid" => [
						"value" => $uid
					],
					"permissions" => [
						"adminAccess" => filter_input(INPUT_POST, "admin", FILTER_SANITIZE_NUMBER_INT),
						"patientsAccess" => filter_input(INPUT_POST, "patients", FILTER_SANITIZE_NUMBER_INT),
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
					"status" => [
						"value" => "OFFLINE"
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

				$response = json_decode($this->contextBrokerRequest("POST", $this->cb["entities_url"] . "?type=Staff", $this->createContextHeaders(), json_encode($data)), true);

				if($response["Response"]=="OK"):

					$query = $this->_GeniSys->_secCon->prepare("
						UPDATE users
						SET pub = :pub,
							aid = :aid
						WHERE id = :id
					");
					$query->execute(array(
						':pub' => $response["Entity"]["id"],
						':aid' => $aid,
						':id' => $uid
					));

					$hash = "";
					$this->contract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["contract"]))->send("registerUser", $pubKey, $newBcUser, $admin, $uid, filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING), $lid, $aid, time(), $_SESSION["GeniSysAI"]["Uid"], ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash) {
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
						$actionMsg = " HIAS Blockchain registerUser failed!\n";
					else:
						$txid = $this->storeBlockchainTransaction("HIAS Register User", $hash, $aid, $uid);
						$this->storeUserHistory("HIAS Register User", $txid, $uid, $aid);
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
						$txid = $this->storeBlockchainTransaction("iotJumpWay Register Authorized", $hash, $aid, $uid);
						$this->storeUserHistory("iotJumpWay Register Authorized", $txid, $uid, $aid);
						$balance = $this->getBlockchainBalance();
						if($balanceMessage == ""):
							$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!";
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
							$txid = $this->storeBlockchainTransaction("Patients Register User", $hash, $aid, $uid);
							$this->storeUserHistory("Patients Register User", $txid, $uid, $aid);
							$balance = $this->getBlockchainBalance();
							if($balanceMessage == ""):
								$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
							endif;
						endif;

					endif;

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
						"MP" => $mqttPass,
						"AU" => $amqppubKey,
						"AP" => $amqpprvKey
					];
				else:
					return [
						"Response"=> "FAILED",
						"Message" => "User creation failed!"
					];
				endif;
			else:
				return [
					"Response"=> "FAILED",
					"Message" => "User creation failed!"
				];
			endif;
		}

		public function updateStaff()
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

			if(!filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL)):
				return [
					"Response"=> "Failed",
					"Message" => "Staff email is required"
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

			$SId = filter_input(INPUT_GET, 'staff', FILTER_SANITIZE_NUMBER_INT);
			$Staffer = $this->getStaff($SId);

			if($Staffer["context"]["Data"]["permissions"]["cancelled"]):
				return [
					"Response"=> "Failed",
					"Message" => "This user is cancelled, to allow access again you must create a new user."
				];
			endif;

			$lid = filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT);
			$aid = $Staffer["context"]["Data"]["aid"]["value"];
			$name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
			$allowed = filter_input(INPUT_POST, "cancelled", FILTER_SANITIZE_STRING) ? False : True;
			$admin = filter_input(INPUT_POST, "admin", FILTER_SANITIZE_STRING) ? True : False;
			$pallowed = filter_input(INPUT_POST, "patients", FILTER_SANITIZE_STRING) ? True : False;

			$data = [
				"category" => [
					"value" => [filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)]
				],
				"name" => [
					"value" => $name
				],
				"username" => [
					"value" => filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING)
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
					"value" => $Staffer["context"]["Data"]["picture"]["value"]
				],
				"lid" => [
					"value" => $lid,
					"entity" => $Staffer["context"]["Data"]["lid"]["entity"]
				],
				"zid" => [
					"value" => 0,
					"entity" => "",
					"timestamp" => "",
					"welcomed" => ""
				],
				"permissions" => [
					"adminAccess" => filter_input(INPUT_POST, "admin", FILTER_SANITIZE_NUMBER_INT) ? filter_input(INPUT_POST, "admin", FILTER_SANITIZE_NUMBER_INT) : 0,
					"patientsAccess" => filter_input(INPUT_POST, "patients", FILTER_SANITIZE_NUMBER_INT) ? filter_input(INPUT_POST, "patients", FILTER_SANITIZE_NUMBER_INT) : 0,
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
			$response = json_decode($this->contextBrokerRequest("PATCH", $this->cb["entities_url"] . "/" . $Staffer["context"]["Data"]["id"] . "/attrs?type=Staff", $this->createContextHeaders(), json_encode($data)), true);
			if($response["Response"]=="OK"):

				$data = [
					"lid" => [
						"value" => $lid,
						"entity" => $Staffer["context"]["Data"]["lid"]["entity"]
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
					"dateModified" => [
						"type" => "DateTime",
						"value" => date('Y-m-d\TH:i:s.Z\Z', time())
					]
				];
				$response = json_decode($this->contextBrokerRequest("PATCH", $this->cb["entities_url"] . "/" . $Staffer["context"]["Data"]["aid"]["entity"] . "/attrs?type=Application", $this->createContextHeaders(), json_encode($data)), true);

				$pdoQuery = $this->_GeniSys->_secCon->prepare("
					UPDATE users
					SET username = :username
					WHERE id = :id
				");
				$pdoQuery->execute([
					":username" => filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING),
					":id" => $SId
				]);

				if($Staffer["context"]["Data"]["lid"]["value"] != $lid):
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
						':topicN' =>  $Staffer["context"]["Data"]["lid"]["entity"] . "/Devices/#",
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
						':topicN' => $Staffer["context"]["Data"]["lid"]["entity"] . "/Applications/#",
						':aid' => $aid,
						':topic' => $location["context"]["Data"]["id"] . "/Applications/#"
					]);
					$pdoQuery->closeCursor();
					$pdoQuery = null;
				endif;

				$unlocked =  $this->unlockBlockchainAccount();

				if($unlocked == "FAILED"):
					return [
						"Response"=> "Failed",
						"Message" => "Unlocking HIAS Blockhain Account Failed!"
					];
				endif;

				$hash = "";
				$this->contract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["contract"]))->send("updateUser", $Staffer["context"]["Data"]["aid"]["entity"], "User", $allowed, $admin, $SId, $name, $lid, $aid, time(), ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash) {
					if ($err !== null) {
						$hash = "FAILED! " . $err;
						return;
					}
					$hash = $resp;
				});

				$actionMsg = "";
				$balanceMessage = "";

				if($hash == "FAILED"):
					$actionMsg = " HIAS Blockchain updateUser failed!\n";
				else:
					$txid = $this->storeBlockchainTransaction("Update User", $hash, $aid, $SId);
					$this->storeUserHistory("Update User", $txid, $SId, $aid);
				endif;

				$this->contract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["contract"]))->send("updateApplication", $Staffer["context"]["Data"]["aid"]["entity"], "Application", $allowed, $admin, $lid, $name, $Staffer["context"]["Data"]["status"]["value"], time(), ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash) {
					if ($err !== null) {
						$hash = "FAILED! " . $err;
						return;
					}
					$hash = $resp;
				});

				if($hash == "FAILED"):
					$actionMsg .= " HIAS Blockchain updateApplication failed!\n";
				else:
					$txid = $this->storeBlockchainTransaction("Update User Application", $hash, $aid, $SId);
					$this->storeUserHistory("Update User Application", $txid, $SId, $aid);
					$balance = $this->getBlockchainBalance();
					$balanceMessage = " You were rewarded for this action!<br /><br />Your Balance Is Now: " . $balance . " HIAS Ether!\n";
				endif;

				if($Staffer["context"]["Data"]["permissions"]["patientsAccess"] && !filter_input(INPUT_POST, "patients", FILTER_SANITIZE_STRING)):

					$this->pcontract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["pcontract"]))->send("deregisterUser", $Staffer["context"]["Data"]["blockchain"]["address"], ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
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
						$txid = $this->storeBlockchainTransaction("Patients Deregister User", $hash, $aid, $SId);
						$this->storeUserHistory("Patients Deregister User", $txid, $SId, $aid);
						$balance = $this->getBlockchainBalance();
						if($balanceMessage == ""):
							$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
						endif;
					endif;

				endif;

				if(!$Staffer["context"]["Data"]["permissions"]["patientsAccess"] && filter_input(INPUT_POST, "patients", FILTER_SANITIZE_STRING)):

					$this->pcontract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["pcontract"]))->send("registerUser", $Staffer["context"]["Data"]["blockchain"]["address"], ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
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
						$txid = $this->storeBlockchainTransaction("Patients Register User", $hash, $aid, $SId);
						$this->storeUserHistory("Patients Register User", $txid, $SId, $aid);
						$balance = $this->getBlockchainBalance();
						if($balanceMessage == ""):
							$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
						endif;
					endif;

				endif;

				if(!$Staffer["context"]["Data"]["permissions"]["cancelled"] && filter_input(INPUT_POST, "cancelled", FILTER_SANITIZE_STRING)):

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
						':username' => $this->_GeniSys->_helpers->oDecrypt($Staffer["context"]["Data"]["amqp"]["username"])
					]);
					$amqp=$query->fetch(PDO::FETCH_ASSOC);

					$query = $this->_GeniSys->_secCon->prepare("
						DELETE FROM amqpu
						WHERE username = :username
					");
					$query->execute([
						':username' => $this->_GeniSys->_helpers->oDecrypt($Staffer["context"]["Data"]["amqp"]["username"])
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

					$this->contract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["contract"]))->send("deregsiter", "User", $Staffer["context"]["Data"]["id"], ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash) {
						if ($err !== null) {
							$hash = "FAILED! " . $err;
							return;
						}
						$hash = $resp;
					});

					if($hash == "FAILED"):
						$actionMsg .= " HIAS Blockchain deregsiter user failed!\n";
					else:
						$txid = $this->storeBlockchainTransaction("Deregister User", $hash, $aid, $SId);
						$this->storeUserHistory("Deregister User", $txid, $SId, $aid);
						$balance = $this->getBlockchainBalance();
						if($balanceMessage == ""):
							$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
						endif;
					endif;

					$this->contract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["contract"]))->send("deregsiter", "Application", $Staffer["context"]["Data"]["id"], ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash) {
						if ($err !== null) {
							$hash = "FAILED! " . $err;
							return;
						}
						$hash = $resp;
					});

					if($hash == "FAILED"):
						$actionMsg .= " HIAS Blockchain deregsiter user application failed!\n";
					else:
						$txid = $this->storeBlockchainTransaction("Deregister User Application", $hash, $aid, $SId);
						$this->storeUserHistory("Deregister User Application", $txid, $SId, $aid);
						$balance = $this->getBlockchainBalance();
						if($balanceMessage == ""):
							$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
						endif;
					endif;

					$this->icontract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["icontract"]))->send("deregisterAuthorized", $Staffer["context"]["Data"]["blockchain"]["address"], ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
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
						$txid = $this->storeBlockchainTransaction("iotJumpWay Deregister Authorized", $hash, $aid, $SId);
						$this->storeUserHistory("iotJumpWay Deregister Authorized", $txid, $SId, $aid);
						$balance = $this->getBlockchainBalance();
						if($balanceMessage == ""):
							$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
						endif;
					endif;

					$this->pcontract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["pcontract"]))->send("deregisterUser", $Staffer["context"]["Data"]["blockchain"]["address"], ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
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
						$txid = $this->storeBlockchainTransaction("Patients Deregister User", $hash, $aid, $SId);
						$this->storeUserHistory("Patients Deregister Authorized", $txid, $SId, $aid);
						$balance = $this->getBlockchainBalance();
						if($balanceMessage == ""):
							$balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
						endif;
					endif;

				endif;

				return [
					"Response"=> "OK",
					"Message" => "Staff updated!" . $actionMsg . $balanceMessage
				];
			else:
				return [
					"Response"=> "FAILED",
					"Message" => "Staff update failed!"
				];
			endif;
		}

		public function resetPassword()
		{
			$pass = $this->_GeniSys->_helpers->password();
			$passhash=$this->_GeniSys->_helpers->createPasswordHash($pass);

			$SId = filter_input(INPUT_GET, 'staff', FILTER_SANITIZE_NUMBER_INT);
			$Staffer = $Staff->getStaff($SId);

			$htpasswd = new Htpasswd('/etc/nginx/security/htpasswd');
			$htpasswd->updateUser($Staffer["context"]["Data"]["username"]["value"], $pass, Htpasswd::ENCTYPE_APR_MD5);

			$query = $this->_GeniSys->_secCon->prepare("
				UPDATE users
				SET password = :password
				WHERE id = :id
			");
			$query->execute(array(
				':password' => $this->_GeniSys->_helpers->oEncrypt($passhash),
				':id' => $SId
			));

			$this->storeUserHistory("Reset Staff Password", 0, $SId);

			return [
				"Response" => "OK",
				"pw" => $pass
			];
		}

		public function resetMqtt()
		{
			$mqttPass = $this->_GeniSys->_helpers->password();
			$mqttHash = create_hash($mqttPass);

			$SId = filter_input(INPUT_GET, 'staff', FILTER_SANITIZE_NUMBER_INT);
			$Staffer = $this->getStaff($SId);

			$data = [
				"mqtt" => [
					"username" => $Staffer["context"]["Data"]["mqtt"]["username"],
					"password" => $this->_GeniSys->_helpers->oEncrypt($mqttPass),
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"dateModified" => [
					"type" => "DateTime",
					"value" => date('Y-m-d\TH:i:s.Z\Z', time())
				]
			];

			$response = json_decode($this->contextBrokerRequest("PATCH", $this->cb["entities_url"] . "/" . $Staffer["context"]["Data"]["aid"]["entity"] . "/attrs?type=Application", $this->createContextHeaders(), json_encode($data)), true);

			if($response["Response"]=="OK"):
				$response = json_decode($this->contextBrokerRequest("PATCH", $this->cb["entities_url"] . "/" . $Staffer["context"]["Data"]["id"] . "/attrs?type=Staff", $this->createContextHeaders(), json_encode($data)), true);
				if($response["Response"]=="OK"):
					$query = $this->_GeniSys->_secCon->prepare("
						UPDATE mqttu
						SET pw = :pw
						WHERE aid = :aid
					");
					$query->execute(array(
						':pw' => $mqttHash,
						':aid' => $Staffer["context"]["Data"]["aid"]["value"]
					));

					$this->storeUserHistory("Reset User MQTT Password", $txid, $SId, $Staffer["context"]["Data"]["aid"]["value"]);

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
			else:
				return [
					"Response"=> "FAILED",
					"Message" => "MQTT password reset failed!"
				];
			endif;

		}

		public function resetAppKey()
		{
			$privKey = $this->_GeniSys->_helpers->generateKey(32);
			$privKeyHash = $this->_GeniSys->_helpers->createPasswordHash($privKey);

			$SId = filter_input(INPUT_GET, 'staff', FILTER_SANITIZE_NUMBER_INT);
			$Staffer = $this->getStaff($SId);

			$data = [
				"keys" => [
					"public" => $Staffer["context"]["Data"]["keys"]["public"],
					"private" => $this->_GeniSys->_helpers->oEncrypt($privKeyHash),
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"dateModified" => [
					"type" => "DateTime",
					"value" => date('Y-m-d\TH:i:s.Z\Z', time())
				]
			];

			$response = json_decode($this->contextBrokerRequest("PATCH", $this->cb["entities_url"] . "/" . $Staffer["context"]["Data"]["aid"]["entity"] . "/attrs?type=Application", $this->createContextHeaders(), json_encode($data)), true);

			if($response["Response"]=="OK"):
				$response = json_decode($this->contextBrokerRequest("PATCH", $this->cb["entities_url"] . "/" . $Staffer["context"]["Data"]["id"] . "/attrs?type=Staff", $this->createContextHeaders(), json_encode($data)), true);
				if($response["Response"]=="OK"):

					$this->storeUserHistory("Reset Private API Key", 0, $SId, $Staffer["context"]["Data"]["aid"]["value"]);

					return [
						"Response"=> "OK",
						"Message" => "Reset Private API Key!",
						"P" => $privKey
					];
				else:
					return [
						"Response"=> "FAILED",
						"Message" => "Reset Private API Key failed!"
					];
				endif;
			else:
				return [
					"Response"=> "FAILED",
					"Message" => "Reset Private API Key failed!"
				];
			endif;
		}

		public function resetAppAmqpKey()
		{
			$SId = filter_input(INPUT_GET, 'staff', FILTER_SANITIZE_NUMBER_INT);
			$Staffer = $this->getStaff($SId);

			$amqpPass = $this->_GeniSys->_helpers->password();
			$amqpHash = $this->_GeniSys->_helpers->createPasswordHash($amqpPass);

			$data = [
				"amqp" => [
					"username" => $Staffer["context"]["Data"]["amqp"]["username"],
					"password" => $this->_GeniSys->_helpers->oEncrypt($amqpPass),
					"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
				],
				"dateModified" => [
					"type" => "DateTime",
					"value" => date('Y-m-d\TH:i:s.Z\Z', time())
				]
			];

			$response = json_decode($this->contextBrokerRequest("PATCH", $this->cb["entities_url"] . "/" . $Staffer["context"]["Data"]["aid"]["entity"] . "/attrs?type=Application", $this->createContextHeaders(), json_encode($data)), true);

			if($response["Response"]=="OK"):
				$response = json_decode($this->contextBrokerRequest("PATCH", $this->cb["entities_url"] . "/" . $Staffer["context"]["Data"]["id"] . "/attrs?type=Staff", $this->createContextHeaders(), json_encode($data)), true);
				if($response["Response"]=="OK"):

					$query = $this->_GeniSys->_secCon->prepare("
						UPDATE amqpu
						SET pw = :pw
						WHERE username = :username
					");
					$query->execute(array(
						':pw' => $this->_GeniSys->_helpers->oEncrypt($amqpHash),
						':username' => $this->_GeniSys->_helpers->oDecrypt($Staffer["context"]["Data"]["amqp"]["username"])
					));

					$this->storeUserHistory("Reset User Application AMQP Password", 0, $SId, $Staffer["context"]["Data"]["aid"]["value"]);

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
			else:
				return [
					"Response"=> "FAILED",
					"Message" => "AMQP password reset failed!"
				];
			endif;
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
					|| tuid = :tuid
				$orderer
				$limiter
			");
			$pdoQuery->execute([
				":id" => $user,
				":tuid" => $user
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
					|| tuid = :tuid
				$orderer
				$limiter
			");
			$pdoQuery->execute([
				":id" => $user,
				":tuid" => $user
			]);
			$response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			return $response;
		}

		public function retrieveStatuses($application, $limit = 0, $order = -1)
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

	if(filter_input(INPUT_POST, "reset_user_amqp", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($Staff->resetAppAmqpKey()));
	endif;