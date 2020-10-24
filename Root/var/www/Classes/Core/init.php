<?php
header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
header("strict-transport-security: max-age=15768000");ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include dirname(__FILE__) . '/../../Classes/Htpasswd.php';
require __DIR__ . '/../../vendor/autoload.php';

use Web3\Web3;
use Web3\Contract;
use Web3\Utils;

	class Core
	{
		private $dbname, $dbusername, $dbpassword;
		public  $dbcon, $config = null;

		public function __construct()
		{
			$config = json_decode(file_get_contents("/fserver/var/www/Classes/Core/confs.json", true));

			$this->config = $config;
			$this->dbname = $config->dbname;
			$this->dbusername = $config->dbusername;
			$this->dbpassword = $config->dbpassword;
			$this->mdbname = $config->dbname;
			$this->mdbusername = $config->mdbusername;
			$this->mdbpassword = $config->mdbpassword;
			$this->connect();
		}

		function connect()
		{
			try
			{
				$this->dbcon = new PDO(
					'mysql:host=localhost'.';dbname='.$this->dbname,
					$this->dbusername,
					$this->dbpassword,
					[PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"]
				);
				$this->dbcon->setAttribute(
					PDO::ATTR_ERRMODE,
					PDO::ERRMODE_EXCEPTION
				);
				$this->dbcon->setAttribute(
					PDO::ATTR_EMULATE_PREPARES,
					false
				);
			}
			catch(PDOException $e)
			{
				die($e);
			}
		}
	}

	class aiInit
	{
		private $_user = null;
		public $_secCon, $_confs, $_auth, $_helpers, $_pageDetails;

		function __construct(Core $_secCon, $_pageDetails)
		{
			$this->setCookie();

			$this->_secCon = $_secCon->dbcon;
			$this->_key = $_secCon->config->key;

			$this->_mdbname = $_secCon->config->mdbname;
			$this->_mdbusername = $_secCon->config->mdbusername;
			$this->_mdbpassword = $_secCon->config->mdbpassword;

			include dirname(__FILE__) . '/../../Classes/helpers.php';

			$this->_helpers = new Helpers($this);
			$this->cb = $this->getContextBrokerConf();
			$this->_confs = $this->getConfigs();
			$this->_pageDetails = $_pageDetails;

			$this->lt = $this->_helpers->oDecrypt($this->_confs["lt"]);
			$this->lg = $this->_helpers->oDecrypt($this->_confs["lg"]);

			if(isSet($_SESSION["GeniSysAI"]["Active"])):
				$this->bcc = $this->getBlockchainConf();
				$this->web3 = $this->blockchainConnection();
				$this->contract = new Contract($this->web3->provider, $this->bcc["abi"]);
			endif;
		}

		private function setCookie()
		{
			if(!isSet($_COOKIE['GeniSysAI'])):
				$rander=rand();
				setcookie("GeniSysAI", $rander, time()+(10*365*24*60*60), '/', $_SERVER['SERVER_NAME'], true, true);
				$_COOKIE['GeniSysAI'] = $rander;
			endif;
		}

		public function getContextBrokerConf()
		{
			$pdoQuery = $this->_secCon->prepare("
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
			$basicAuth = $_SESSION["GeniSysAI"]["User"] . ":" . $this->_helpers->oDecrypt($_SESSION["GeniSysAI"]["Pass"]);
			$basicAuth = base64_encode($basicAuth);

			return [
				"Content-Type: application/json",
				'Authorization: Basic '. $basicAuth
			];
		}

		private function contextBrokerRequest($method, $url, $headers, $json, $domain)
		{
			$path = $this->_helpers->oDecrypt($domain) . "/" . $this->cb["url"] . "/" . $url;

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
			endif;

			return $body;
		}

		public function getBlockchainConf()
		{
			$pdoQuery = $this->_secCon->prepare("
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
			$web3 = new Web3($this->_helpers->oDecrypt($this->_confs["domainString"]) . "/Blockchain/API/", 30, $_SESSION["GeniSysAI"]["User"], $this->_helpers->oDecrypt($_SESSION["GeniSysAI"]["Pass"]));
			return $web3;
		}

		private function checkBlockchainPermissions()
		{
			$allowed = "";
			$this->contract->at($this->_helpers->oDecrypt($this->bcc["contract"]))->call("identifierAllowed", "User", $_SESSION["GeniSysAI"]["Identifier"], ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$allowed) {
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

		private function storeUserHistory($action)
		{
			$pdoQuery = $this->_secCon->prepare("
				INSERT INTO  history (
					`uid`,
					`action`,
					`time`
				)  VALUES (
					:uid,
					:action,
					:time
				)
			");
			$pdoQuery->execute([
				":uid" => $_SESSION["GeniSysAI"]["Uid"],
				":action" => $action,
				":time" => time()
			]);
			$txid = $this->_secCon->lastInsertId();
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			return $txid;
		}

		protected function getConfigs()
		{
			$pdoQuery = $this->_secCon->prepare("
				SELECT server.version,
					server.aid,
					server.phpmyadmin,
					server.recaptcha,
					server.recaptchas,
					server.gmaps,
					server.lt,
					server.lg,
					server.meta_title,
					server.meta_description,
					server.domainString,
					mqtta.apub
				FROM settings server
				INNER JOIN mqtta mqtta
				ON mqtta.id = server.aid
			");
			$pdoQuery->execute();
			$configs=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			if(isSet($_SESSION["GeniSysAI"]["Active"])):
				$context =  json_decode($this->contextBrokerRequest("GET", "entities/" . $configs["apub"] . "?type=Application", $this->createContextHeaders(), [], $configs["domainString"]), true);
				$configs["status"] = $context["Data"]["status"]["value"];
				$configs["cpu"] = $context["Data"]["cpuUsage"]["value"];
				$configs["mem"] = $context["Data"]["memoryUsage"]["value"];
				$configs["hdd"] = $context["Data"]["hddUsage"]["value"];
				$configs["tempr"] = $context["Data"]["temperature"]["value"];
				$configs["alt"] = $context["Data"]["location"]["value"]["coordinates"][0];
				$configs["alg"] = $context["Data"]["location"]["value"]["coordinates"][1];
			endif;

			return $configs;
		}

		public function updateConfigs()
		{
			$this->checkBlockchainPermissions();

			$pdoQuery = $this->_secCon->prepare("
				UPDATE settings
				SET version = :version,
					aid = :aid,
					phpmyadmin = :phpmyadmin,
					recaptcha = :recaptcha,
					recaptchas = :recaptchas,
					gmaps = :gmaps,
					lt = :lt,
					lg = :lg,
					domainString = :domainString
			");
			$pdoQuery->execute([
				":version" => filter_input(INPUT_POST, "version", FILTER_SANITIZE_STRING),
				":aid" => filter_input(INPUT_POST, "aid", FILTER_SANITIZE_NUMBER_INT),
				":phpmyadmin" => filter_input(INPUT_POST, "phpmyadmin", FILTER_SANITIZE_STRING),
				":recaptcha" => $this->_helpers->oEncrypt(filter_input(INPUT_POST, "recaptcha", FILTER_SANITIZE_STRING)),
				":recaptchas" => $this->_helpers->oEncrypt(filter_input(INPUT_POST, "recaptchas", FILTER_SANITIZE_STRING)),
				":gmaps" => $this->_helpers->oEncrypt(filter_input(INPUT_POST, "gmaps", FILTER_SANITIZE_STRING)),
				":lt" => $this->_helpers->oEncrypt(filter_input(INPUT_POST, "lt", FILTER_SANITIZE_STRING)),
				":lg" => $this->_helpers->oEncrypt(filter_input(INPUT_POST, "lg", FILTER_SANITIZE_STRING)),
				":domainString" => $this->_helpers->oEncrypt(filter_input(INPUT_POST, "domainString", FILTER_SANITIZE_STRING))
			]);
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			$this->storeUserHistory("Updated Server Config");

			return [
				"Response"=> "OK",
				"Message" => "Server Settings Updated!"
			];
		}

	}

	$_secCon  = new Core();
	$_GeniSys = new aiInit($_secCon, $pageDetails);

	if(filter_input(INPUT_POST, "update_server", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($_GeniSys->updateConfigs()));
	endif;