<?php

require __DIR__ . '/../../vendor/autoload.php';

use Web3\Web3;
use Web3\Contract;
use Web3\Utils;

	class _GeniSysAi
	{
		private $_GeniSys = null;
		private $_Secure = 1;

		function __construct($_GeniSys)
		{
			$this->_GeniSys = $_GeniSys;

			if(isSet($_SESSION["GeniSysAI"]["Active"])):
				$this->bcc = $this->getBlockchainConf();
				$this->web3 = $this->blockchainConnection();
				$this->contract = new Contract($this->web3->provider, $this->bcc["abi"]);
			endif;
		}

		public function getBlockchainConf()
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT blockchain.*,
					contracts.contract,
					contracts.abi
				FROM blockchain blockchain
				INNER JOIN contracts contracts
				ON contracts.id = blockchain.dc
			");
			$pdoQuery->execute();
			$response=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $response;
		}

		private function blockchainConnection()
		{
			$this->web3 = new Web3($this->_GeniSys->_helpers->oDecrypt($this->_GeniSys->_confs["domainString"]) . "/Blockchain/API/", 30, $_SESSION["GeniSysAI"]["User"], $this->_GeniSys->_helpers->oDecrypt($_SESSION["GeniSysAI"]["Pass"]));
			return $this->web3;
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

		private function storeUserHistory($action, $hashid, $uid)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  history (
					`uid`,
					`tuid`,
					`action`,
					`hash`,
					`time`
				)  VALUES (
					:uid,
					:tuid,
					:action,
					:hash,
					:time
				)
			");
			$pdoQuery->execute([
				":uid" => $_SESSION["GeniSysAI"]["Uid"],
				":tuid" => $uid,
				":action" => $action,
				':hash' => $hashid,
				":time" => time()
			]);
			$txid = $this->_GeniSys->_secCon->lastInsertId();
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			return $txid;
		}

		public function checkBlock()
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT ipv6
				FROM blocked
				Where ipv6 = :ipv6
				LIMIT 1
			");
			$pdoQuery->execute([
				":ipv6" => $this->_GeniSys->_helpers->getUserIP()
			]);
			$ip=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			if($ip["ipv6"]):
				session_destroy();
				die(header("Location: /Blocked"));
			endif;
		}

		public function checkSession()
		{
			$this->checkBlock();
			if(isset($_SESSION["GeniSysAI"]["Active"]) && $this->_GeniSys->_pageDetails["PageID"]=="Login"):
				die(header("Location: /Dashboard"));
			elseif(empty($_SESSION["GeniSysAI"]["Active"]) && $this->_GeniSys->_pageDetails["PageID"]!="Login"):
				die(header("Location: /"));
			endif;
		}

		public function login()
		{
			$this->checkBlock();

			if($this->_Secure):
				$verified = $this->recaptcha();
				if(!$verified):
					return  [
						"Response"=>"FAILED",
						"ResponseMessage"=>"Google ReCaptcha failed, access DENIED!",
						"SessionAttempts"=>$_SESSION["Attempts"]
					];
				endif;
			endif;

			$gsysuser = $this->getUserByName(filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING));

			if($gsysuser["id"]):
				if($this->verifyPassword(filter_input(INPUT_POST,'password',FILTER_SANITIZE_STRING),
					$this->_GeniSys->_helpers->oDecrypt($gsysuser["password"]))):  session_regenerate_id();

					$_SESSION["GeniSysAI"]=[
						"Active"=>true,
						"Uid"=>$gsysuser["id"],
						"Identifier"=>$gsysuser["apub"],
						"User"=>filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING),
						"Pass"=>$this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "password", FILTER_SANITIZE_STRING)),
						"Pic"=>$gsysuser["pic"],
						"Mqtt"=> [
							"Location" => $gsysuser["lid"],
							"Application" => $gsysuser["aid"],
							"ApplicationName" => $gsysuser["name"],
							"User" => $gsysuser["mqttu"],
							"Pass" => $gsysuser["mqttp"]
						],
						"BC"=> [
							"BCUser" => $gsysuser["bcaddress"],
							"BCPass" => $gsysuser["bcpw"]
						]
					];

					$pdoQuery = $this->_GeniSys->_secCon->prepare("
						INSERT INTO logins (
							`ipv6`,
							`browser`,
							`language`,
							`time`
						)  VALUES (
							:ipv6,
							:browser,
							:language,
							:time
						)
					");
					$pdoQuery->execute([
						":ipv6" => $this->_GeniSys->_helpers->oEncrypt($this->_GeniSys->_helpers->getUserIP()),
						":browser" => $this->_GeniSys->_helpers->oEncrypt($_SERVER["HTTP_USER_AGENT"]),
						":language" => $this->_GeniSys->_helpers->oEncrypt($_SERVER["HTTP_ACCEPT_LANGUAGE"]),
						":time" => time()
					]);
					$pdoQuery->closeCursor();
					$pdoQuery = null;

					$this->storeUserHistory("System Login", 0, 0);

					return  [
						"Response"=>"OK",
						"ResponseMessage"=>"Welcome"
					];
				else:
					$pdoQuery = $this->_GeniSys->_secCon->prepare("
						INSERT INTO loginsf (
							`ipv6`,
							`browser`,
							`language`,
							`time`
						)  VALUES (
							:ipv6,
							:browser,
							:language,
							:time
						)
					");
					$pdoQuery->execute([
						":ipv6" => $this->_GeniSys->_helpers->oEncrypt($this->_GeniSys->_helpers->getUserIP()),
						":browser" => $this->_GeniSys->_helpers->oEncrypt($_SERVER["HTTP_USER_AGENT"]),
						":language" => $this->_GeniSys->_helpers->oEncrypt($_SERVER["HTTP_ACCEPT_LANGUAGE"]),
						":time" => time()
					]);
					$pdoQuery->closeCursor();
					$pdoQuery = null;

					$_SESSION["Attempts"] += 1;
					if($_SESSION["Attempts"] >= 3):
						$_SESSION["Attempts"] = 0;

						$pdoQuery = $this->_GeniSys->_secCon->prepare("
							INSERT INTO blocked (
								`ipv6`,
								`banned`
							)  VALUES (
								:ipv6,
								:banned
							)
						");
						$pdoQuery->execute([
							":ipv6" => $this->_GeniSys->_helpers->getUserIP(),
							":banned" => time()
						]);
						$pdoQuery->closeCursor();
						$pdoQuery = null;

						return  [
							"Response"=>"BLOCKED",
							"ResponseMessage"=>"Password incorrect, access BLOCKED!",
							"SessionAttempts"=>$_SESSION["Attempts"]
						];
					else:
						return  [
							"Response"=>"FAILED",
							"ResponseMessage"=>"Password incorrect, access DENIED!",
							"SessionAttempts"=>$_SESSION["Attempts"]
						];
					endif;

				endif;

			else:

				$pdoQuery = $this->_GeniSys->_secCon->prepare("
					INSERT INTO loginsf (
						`ipv6`,
						`browser`,
						`language`,
						`time`
					)  VALUES (
						:ipv6,
						:browser,
						:language,
						:time
					)
				");
				$pdoQuery->execute([
					":ipv6" => $this->_GeniSys->_helpers->oEncrypt($this->_GeniSys->_helpers->getUserIP()),
					":browser" => $this->_GeniSys->_helpers->oEncrypt($_SERVER["HTTP_USER_AGENT"]),
					":language" => $this->_GeniSys->_helpers->oEncrypt($_SERVER["HTTP_ACCEPT_LANGUAGE"]),
					":time" => time()
				]);
				$pdoQuery->closeCursor();
				$pdoQuery = null;

				$_SESSION["Attempts"] += 1;
				if($_SESSION["Attempts"] >= 3):
					$_SESSION["Attempts"] = 0;

					$pdoQuery = $this->_GeniSys->_secCon->prepare("
						INSERT INTO blocked (
							`ipv6`,
							`banned`
						)  VALUES (
							:ipv6,
							:banned
						)
					");
					$pdoQuery->execute([
						":ipv6" => $this->_GeniSys->_helpers->getUserIP(),
						":banned" => time()
					]);
					$pdoQuery->closeCursor();
					$pdoQuery = null;

					return  [
						"Response"=>"BLOCKED",
						"ResponseMessage"=>"Username incorrect, access BLOCKED!",
						"SessionAttempts"=>$_SESSION["Attempts"]
					];
				else:
					return  [
						"Response"=>"FAILED",
						"ResponseMessage"=>"Username incorrect, access DENIED!"
					];
				endif;
			endif;

		}

		public function recaptcha()
		{
			$this->checkBlock();

			if(!filter_input(INPUT_POST,'g-recaptcha-response',FILTER_SANITIZE_STRING)):
				return [
					'Response'=>'FAILED',
					'ResponseMessage'=>'Please verify using Recaptcha.',
				];
			endif;

			$fields = array(
				'secret'=>urlencode($this->_GeniSys->_helpers->oDecrypt($this->_GeniSys->_confs["recaptchas"])),
				'response'=>urlencode(filter_input(INPUT_POST, 'g-recaptcha-response', FILTER_SANITIZE_STRING))
			);
			$fields_string = "";

			foreach($fields as $key=>$value):
				$fields_string .= $key.'='.$value.'&';
			endforeach;
			rtrim($fields_string,'&');

			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL,'https://www.google.com/recaptcha/api/siteverify');
			curl_setopt($ch,CURLOPT_POST,count($fields));
			curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			$result = curl_exec($ch);
			$result=json_decode($result, TRUE);

			if($result['success']===true):
				$_SESSION["Attempts"] = !isSet($_SESSION["Attempts"]) ? 0 : $_SESSION["Attempts"];
				return True;
			else:
				return False;
			endif;

		}

		private static function passwordHash($password) {
			return password_hash($password, PASSWORD_DEFAULT);
		}

		public function resetpass()
		{
			if($this->_Secure):
				$verified = $this->recaptcha();
				if(!$verified):
					return  [
						"Response"=>"FAILED",
						"ResponseMessage"=>"Google ReCaptcha failed, access DENIED!",
						"SessionAttempts"=>$_SESSION["Attempts"]
					];
				endif;
			endif;

			$this->checkBlock();

			$gsysuser = $this->getUserByName(filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING));

			if($gsysuser["id"]):

				$pass = $this->_GeniSys->_helpers->password();
				$passhash=$this->_GeniSys->_helpers->createPasswordHash($pass);

				$htpasswd = new Htpasswd('/etc/nginx/security/htpasswd');
				$htpasswd->updateUser(filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING), $pass, Htpasswd::ENCTYPE_APR_MD5);

				$query = $this->_GeniSys->_secCon->prepare("
					UPDATE users
					SET password = :password
					WHERE username = :username
				");
				$query->execute(array(
					':password' => $this->_GeniSys->_helpers->oEncrypt($passhash),
					':username' => filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING)
				));

				return [
					"Response" => "OK",
					"pw" => $pass
				];
			else:
				return  [
					"Response"=>"FAILED",
					"ResponseMessage"=>"Username not found!"
				];
			endif;
		}

		public function getUser($userId)
		{
			$this->checkBlockchainPermissions();

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT id,
					name,
					password
				FROM users
				WHERE id = :id
			");
			$pdoQuery->execute([
				":id"=> $userId
			]);
			$response=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			return $response;
		}

		public function getUserByName($username)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT users.id,
					users.bcaddress,
					users.bcpw,
					users.password,
					users.pic,
					mqtt.lid,
					mqtt.id as aid,
					mqtt.name,
					mqtt.mqttu,
					mqtt.apub,
					mqtt.mqttp
				FROM users users
				INNER JOIN mqtta mqtt
				ON users.id = mqtt.uid
				WHERE users.username = :username
			");
			$pdoQuery->execute([
				":username"=> $username
			]);
			$response=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			return $response;
		}

		private static function verifyPassword($password,$hash) {
			return password_verify($password, $hash);
		}

		public function getStats()
		{
			$this->checkBlockchainPermissions();

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT cpu,
					mem,
					hdd,
					tempr
				FROM mqtta
				Where id = :id
			");
			$pdoQuery->execute([
				":id" => $this->_GeniSys->_confs["aid"]
			]);
			$stats=$pdoQuery->fetch(PDO::FETCH_ASSOC);

			return $stats;
		}

		public function getMapMarkers($application)
		{
			if(!$application["lt"]):
				$lat = $this->lat;
				$lng = $this->lng;
			else:
				$lat = $device["lt"];
				$lng = $device["lg"];
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

		public function raise()
		{
			return [
				"lid" => $_SESSION["GeniSysAI"]["Mqtt"]["Location"],
				"aid" => $_SESSION["GeniSysAI"]["Mqtt"]["Application"],
				"an" => $_SESSION["GeniSysAI"]["Mqtt"]["ApplicationName"],
				"un" => $this->_GeniSys->_helpers->oDecrypt($_SESSION["GeniSysAI"]["Mqtt"]["User"]),
				"uc" => $this->_GeniSys->_helpers->oDecrypt($_SESSION["GeniSysAI"]["Mqtt"]["Pass"]),
				"bcid" => $_SESSION["GeniSysAI"]["Identifier"],
				"bcaddr" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]
			];
		}
	}

$_GeniSysAi = new _GeniSysAi($_GeniSys);

if(filter_input(INPUT_POST, "login", FILTER_SANITIZE_STRING)):
	die(json_encode($_GeniSysAi->login()));
endif;
if(filter_input(INPUT_POST, "reset_pass", FILTER_SANITIZE_STRING)):
	die(json_encode($_GeniSysAi->resetpass()));
endif;
if(filter_input(INPUT_POST, 'getServerStats', FILTER_SANITIZE_NUMBER_INT)):
	die(json_encode($_GeniSysAi->getStats()));
endif;
if(filter_input(INPUT_POST, "raise", FILTER_SANITIZE_NUMBER_INT)):
	die(json_encode($_GeniSysAi->raise()));
endif;

$domain = $_GeniSys->_helpers->oDecrypt($_GeniSys->_confs["domainString"]);

if(isset($_SESSION["GeniSysAI"]["User"])):
	$stats = $_GeniSysAi->getStats();
endif;