<?php

require __DIR__ . '/../../../vendor/autoload.php';

use Web3\Web3;
use Web3\Contract;
use Web3\Utils;

	class AI
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
			$allowed = "";
			$errr = "";
			$this->contract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["contract"]))->call("identifierAllowed", "User", $_SESSION["GeniSysAI"]["Identifier"], ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$allowed, &$errr) {
				if ($err !== null) {
					$allowed = "FAILED";
					$errr = $err;
					return;
				}
				$allowed = $resp;
			});
			if(!$allowed):
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

		public function getModels($limit = 0)
		{
			$limiter = "";
			if($limit != 0):
				$limiter = "&limit=" . $limit;
			endif;

			$devices = json_decode($this->contextBrokerRequest("GET", $this->cb["entities_url"] . "?type=Model".$limiter, $this->createContextHeaders(), []), true);
			return $devices;
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

		public function createModel()
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

			if(!filter_input(INPUT_POST, "ntype", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Network type is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "ntype", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Network type is required"
				];
			endif;

			$pubKey = $this->_GeniSys->_helpers->generate_uuid();

			$query = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  models  (
					`pub`
				)  VALUES (
					:pub
				)
			");
			$query->execute([
				':pub' => $pubKey
			]);
			$mid = $this->_GeniSys->_secCon->lastInsertId();

			$properties=[];
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
				"id" => $pubKey,
				"type" => "Model",
				"category" => [
					"value" => [filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)]
				],
				"mid" => [
					"value" => $mid
				],
				"name" => [
					"value" => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)
				],
				"description" => [
					"value" => filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)
				],
				"model" => [
					"type" => filter_input(INPUT_POST, "mtype", FILTER_SANITIZE_STRING),
					"author" => filter_input(INPUT_POST, "author", FILTER_SANITIZE_STRING),
					"authorLink" => filter_input(INPUT_POST, "authorLink", FILTER_SANITIZE_STRING)
				],
				"network" => [
					"value" => filter_input(INPUT_POST, "ntype", FILTER_SANITIZE_STRING)
				],
				"language" => [
					"value" => filter_input(INPUT_POST, "language", FILTER_SANITIZE_STRING)
				],
				"framework" => [
					"value" => filter_input(INPUT_POST, "framework", FILTER_SANITIZE_STRING)
				],
				"toolkit" => [
					"value" => filter_input(INPUT_POST, "toolkit", FILTER_SANITIZE_STRING)
				],
				"dataset" => [
					"name" => filter_input(INPUT_POST, "datasetUsed", FILTER_SANITIZE_STRING),
					"author" => filter_input(INPUT_POST, "datasetAuthor", FILTER_SANITIZE_STRING),
					"url" => filter_input(INPUT_POST, "datasetLink", FILTER_SANITIZE_STRING),
					"type" => filter_input(INPUT_POST, "datasetType", FILTER_SANITIZE_STRING),
					"augmentation" => filter_input(INPUT_POST, "datasetAugmentation", FILTER_SANITIZE_NUMBER_INT) ? 1 : 0,
					"positiveLabel" => filter_input(INPUT_POST, "datasetPosLabel", FILTER_SANITIZE_STRING),
					"negativeLabel" => filter_input(INPUT_POST, "datasetNegLabel", FILTER_SANITIZE_STRING)
				],
				"paper" => [
					"title" => filter_input(INPUT_POST, "relatedPaper", FILTER_SANITIZE_STRING),
					"author" => filter_input(INPUT_POST, "relatedPaperAuthor", FILTER_SANITIZE_STRING),
					"doi" => filter_input(INPUT_POST, "relatedPaperDOI", FILTER_SANITIZE_STRING),
					"link" => filter_input(INPUT_POST, "relatedPaperLink", FILTER_SANITIZE_STRING)
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

			$response = json_decode($this->contextBrokerRequest("POST", $this->cb["entities_url"] . "?type=Model", $this->createContextHeaders(), json_encode($data)), true);

			if($response["Response"]=="OK"):
				return [
					"Response"=> "OK",
					"Message" => "Model Created!"
				];
			else:
				return [
					"Response"=> "FAILED",
					"Message" => "Model Created KO! " . $response["Description"]
				];
			endif;
		}

		public function updateModel()
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

			if(!filter_input(INPUT_POST, "ntype", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Network type is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "ntype", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Network type is required"
				];
			endif;

			$properties=[];
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

			$model = $this->getModel(filter_input(INPUT_GET, 'model', FILTER_SANITIZE_NUMBER_INT));

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
				"model" => [
					"type" => filter_input(INPUT_POST, "mtype", FILTER_SANITIZE_STRING),
					"link" => filter_input(INPUT_POST, "link", FILTER_SANITIZE_STRING),
					"author" => filter_input(INPUT_POST, "author", FILTER_SANITIZE_STRING),
					"authorLink" => filter_input(INPUT_POST, "authorLink", FILTER_SANITIZE_STRING)
				],
				"network" => [
					"value" => filter_input(INPUT_POST, "ntype", FILTER_SANITIZE_STRING)
				],
				"language" => [
					"value" => filter_input(INPUT_POST, "language", FILTER_SANITIZE_STRING)
				],
				"framework" => [
					"value" => filter_input(INPUT_POST, "framework", FILTER_SANITIZE_STRING)
				],
				"toolkit" => [
					"value" => filter_input(INPUT_POST, "toolkit", FILTER_SANITIZE_STRING)
				],
				"dataset" => [
					"name" => filter_input(INPUT_POST, "datasetUsed", FILTER_SANITIZE_STRING),
					"author" => filter_input(INPUT_POST, "datasetAuthor", FILTER_SANITIZE_STRING),
					"url" => filter_input(INPUT_POST, "datasetLink", FILTER_SANITIZE_STRING),
					"type" => filter_input(INPUT_POST, "datasetType", FILTER_SANITIZE_STRING),
					"augmentation" => filter_input(INPUT_POST, "datasetAugmentation", FILTER_SANITIZE_NUMBER_INT) ? 1 : 0,
					"positiveLabel" => filter_input(INPUT_POST, "datasetPosLabel", FILTER_SANITIZE_STRING),
					"negativeLabel" => filter_input(INPUT_POST, "datasetNegLabel", FILTER_SANITIZE_STRING)
				],
				"paper" => [
					"title" => filter_input(INPUT_POST, "relatedPaper", FILTER_SANITIZE_STRING),
					"author" => filter_input(INPUT_POST, "relatedPaperAuthor", FILTER_SANITIZE_STRING),
					"doi" => filter_input(INPUT_POST, "relatedPaperDOI", FILTER_SANITIZE_STRING),
					"link" => filter_input(INPUT_POST, "relatedPaperLink", FILTER_SANITIZE_STRING)
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

			$response = json_decode($this->contextBrokerRequest("PATCH", $this->cb["entities_url"]  . "/" . $model["context"]["Data"]["id"] .  "/attrs?type=Model", $this->createContextHeaders(), json_encode($data)), true);

			if($response["Response"]=="OK"):
				$model = $this->getModel(filter_input(INPUT_GET, 'model', FILTER_SANITIZE_NUMBER_INT));
				return [
					"Response"=> "OK",
					"Message" => "Model Updated!"
				];
			else:
				return [
					"Response"=> "FAILED",
					"Message" => "Model Update KO! " . $response["Description"]
				];
			endif;
		}

	 }

	 $AI = new AI($_GeniSys);

	 if(filter_input(INPUT_POST, "create_ai_model", FILTER_SANITIZE_NUMBER_INT)):
		 die(json_encode($AI->createModel()));
	 endif;

	 if(filter_input(INPUT_POST, "update_ai_model", FILTER_SANITIZE_NUMBER_INT)):
		 die(json_encode($AI->updateModel()));
	 endif;