<?php

include 'pbkdf2.php';
require '/fserver/var/www/vendor/autoload.php';

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

		$this->confs = $config;
		$this->key = $config->key;
		$this->dbname = $config->dbname;
		$this->dbusername = $config->dbusername;
		$this->dbpassword = $config->dbpassword;
		$this->mdbname = $config->mdbname;
		$this->mdbusername = $config->mdbusername;
		$this->mdbpassword = $config->mdbpassword;
		$this->connect();
		$this->mngConn = new MongoDB\Driver\Manager("mongodb://localhost:27017/".$this->mdbname.'', ["username" => $this->mdbusername, "password" => $this->mdbpassword]);
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

class TassAI{

	public function __construct(Core $core)
	{
		$this->confs = $core->confs;
		$this->key = $core->key;
		$this->conn = $core->dbcon;
		$this->bcc = $this->getBlockchainConf();
		$this->mngConn = $core->mngConn;
		$this->mdbname = $core->mdbname;
	}

	public function getBlockchainConf()
	{
		$pdoQuery = $this->conn->prepare("
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

	private function blockchainConnection($domain, $pub, $prv)
	{
		$web3 = new Web3($domain . "/Blockchain/API/", 30, $pub, $prv);
		return $web3;
	}

	private function unlockBlockchainAccount($web3, $account, $pass)
	{
		$response = "";
		$personal = $web3->personal;
		$personal->unlockAccount($account, $pass, function ($err, $unlocked) use (&$response) {
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

	private function getBlockchainBalance($web3, $account)
	{
		$nbalance = "";
		$web3->eth->getBalance($account, function ($err, $balance) use (&$nbalance) {
			if ($err !== null) {
				$response = "FAILED! " . $err;
				return;
			}
			$nbalance = $balance->toString();
		});
		return Utils::fromWei($nbalance, 'ether')[0];
	}

	private function createBlockchainUser($web3, $pass)
	{
		$newAccount = "";
		$personal = $web3->personal;
		$personal->newAccount($pass, function ($err, $account) use (&$newAccount) {
			if ($err !== null) {
				$newAccount = "FAILED!";
				return;
			}
			$newAccount = $account;
		});

		return $newAccount;
	}

	private function storeBlockchainTransaction($action, $hash, $device = 0, $application = 0)
	{
		$pdoQuery = $this->conn->prepare("
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
			":uid" => 1,
			":did" => $device,
			":aid" => $application,
			":action" => $action,
			':hash' => $this->encrypt($hash),
			":time" => time()
		]);
		$txid = $this->conn->lastInsertId();
		$pdoQuery->closeCursor();
		$pdoQuery = null;
		return $txid;
	}

	private function storeUserHistory($action, $hash, $location = 0, $zone = 0, $device = 0, $sensor = 0, $application = 0)
	{
		$pdoQuery = $this->conn->prepare("
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
			":uid" => 1,
			":tlid" => $location,
			":tzid" => $zone,
			":tdid" => $device,
			":tsid" => $sensor,
			":taid" => $application,
			":action" => $action,
			":hash" => $hash,
			":time" => time()
		]);
		$txid = $this->conn->lastInsertId();
		$pdoQuery->closeCursor();
		$pdoQuery = null;
		return $txid;
	}

	public function zone($lid, $lie, $zone){

		$id = $this->generate_uuid();

		$query = $this->conn->prepare("
			INSERT INTO  mqttlz  (
				`pub`
			)  VALUES (
				:pub
			)
		");
		$query->execute([
			':pub' => $id
		]);
		$zid = $this->conn->lastInsertId();

		$data = [
			"id" => $id,
			"type" => "Zone",
			"category" => [
				"value" => ["Room"]
			],
			"name" => [
				"value" => $zone
			],
			"description" => [
				"value" => $zone
			],
			"devices" => [
				"value" => 1
			],
			"lid" => [
				"value" => $lid,
				"entity" => $lie
			],
			"zid" => [
				"value" => $zid
			],
			"devices" => [
				"value" => 0
			],
			"location" => [
				"type" => "geo:json",
				"value" => [
					"type" => "Point",
					"coordinates" => [0, 0]
				]
			],
			"dateCreated" => [
				"type" => "DateTime",
				"value" => date('Y-m-d\TH:i:s.Z\Z', time())
			],
			"dateModified" => [
				"type" => "DateTime",
				"value" => date('Y-m-d\TH:i:s.Z\Z', time())
			]
		];

		$insert = new \MongoDB\Driver\BulkWrite;
		$insertData = $insert->insert($data);
		$result = $this->mngConn->executeBulkWrite($this->mdbname.'.Zones', $insert);

		echo "! Zone, " . $zone . ", has been created with ID " . $zid . " with Identifier " . $id . " !\n";

		$this->lid = $lid;
		$this->lie = $lie;
		$this->zid = $zid;
		$this->zie = $id;
	}

	public function device($ip, $mac, $domain, $user, $pass, $bcauthu, $bcauthp){

		$web3 = $this->blockchainConnection($domain, $user, $pass);

		$pubKey = $this->generate_uuid();
		$privKey = $this->generateKey(32);
		$privKeyHash = $this->createPasswordHash($privKey);

		$mqttUser = $this->generate_uuid();
		$mqttPass = $this->password();
		$mqttHash = create_hash($mqttPass);

		$amqppubKey = $this->generate_uuid();
		$amqpprvKey = $this->generateKey(32);
		$amqpKeyHash = $this->createPasswordHash($amqpprvKey);

		$bcPass = $this->password();

		$unlocked =  $this->unlockBlockchainAccount($web3, $bcauthu, $bcauthp);

		if($unlocked == "FAILED"):
			echo "Unlocking HIAS Blockhain Account Failed!\n";
			return False;
		endif;

		$contract = new Contract($web3->provider, $this->bcc["abi"]);
		$icontract = new Contract($web3->provider, $this->bcc["iabi"]);

		$newBcUser = $this->createBlockchainUser($web3, $bcPass);

		if($newBcUser == "FAILED"):
			echo "Creating New HIAS Blockhain Account Failed!\n";
		endif;

		$query = $this->conn->prepare("
			INSERT INTO  mqttld  (
				`apub`
			)  VALUES (
				:apub
			)
		");
		$query->execute([
			':apub' => $pubKey
		]);
		$did = $this->conn->lastInsertId();

		$name = "Server Security API";

		$data = [
			"id" => $pubKey,
			"type" => "Device",
			"category" => [
				"value" => [filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)]
			],
			"name" => [
				"value" => $name
			],
			"description" => [
				"value" => $name
			],
			"lid" => [
				"value" => $this->lid,
				"entity" => $this->lie
			],
			"zid" => [
				"value" => $this->zid,
				"entity" => $this->zie
			],
			"did" => [
				"value" => $did
			],
			"location" => [
				"type" => "geo:json",
				"value" => [
					"type" => "Point",
					"coordinates" => [0,0]
				]
			],
			"agent" => [
				"url" => ""
			],
			"device" => [
				"name" => "UPDATE THIS FIELD",
				"manufacturer" => "UPDATE THIS FIELD",
				"model" => "UPDATE THIS FIELD",
				"version" => "UPDATE THIS FIELD"
			],
			"os" => [
				"name" => "UPDATE THIS FIELD",
				"manufacturer" => "UPDATE THIS FIELD",
				"version" => "UPDATE THIS FIELD"
			],
			"protocols" => ["MQTT"],
			"status" => [
				"value" => "OFFLINE",
				"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
			],
			"keys" => [
				"public" => $pubKey,
				"private" => $this->encrypt($privKeyHash),
				"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
			],
			"blockchain" => [
				"address" => $newBcUser,
				"password" => $this->encrypt($bcPass)
			],
			"mqtt" => [
				"username" => $this->encrypt($mqttUser),
				"password" => $this->encrypt($mqttPass),
				"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
			],
			"coap" => [
				"username" => "",
				"password" => ""
			],
			"amqp" => [
				"username" => $this->encrypt($amqppubKey),
				"password" => $this->encrypt($amqpprvKey),
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
				"value" => $ip,
				"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
			],
			"mac" => [
				"value" => $this->encrypt($mac),
				"timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
			],
			"bluetooth" => [
				"address" => $this->encrypt(""),
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

		$insert = new \MongoDB\Driver\BulkWrite;
		$inserted = $insert->insert($data);
		$result = $this->mngConn->executeBulkWrite($this->mdbname.'.Devices', $insert);

		$query = $this->conn->prepare("
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
			':lid' => $this->lid,
			':zid' => $this->zid,
			':did' => $did,
			':uname' => $mqttUser,
			':pw' => $mqttHash
		]);

		$query = $this->conn->prepare("
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
			':lid' => $this->lid,
			':zid' => $this->zid,
			':did' => $did,
			':username' => $mqttUser,
			':topic' => $this->lie . "/Devices/" . $this->zie . "/".$did."/#",
			':rw' => 4
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

		$hash = "";
		$msg = "";
		$contract->at($this->decrypt($this->bcc["contract"]))->send("deposit", 5000000000000000000, ["from" => $bcauthu, "value" => 5000000000000000000], function ($err, $resp) use (&$hash, &$msg) {
			if ($err !== null) {
				$hash = "FAILED";
				$msg = $err;
				return;
			}
			$hash = $resp;
		});

		$actionMsg = "";

		if($hash == "FAILED"):
			echo " HIAS Blockchain HIAS deposit failed! " . $msg . "\n";
		else:
			$txid = $this->storeBlockchainTransaction("Deposit", $hash, $did, 0);
			$this->storeUserHistory("Deposit", $txid, 1, 1, $did, 0, 0);
			echo " HIAS Blockchain HIAS deposit complete!\n";

			$hash = "";
			$msg = "";
			$actionMsg = "";
			$balanceMessage = "";
			$contract->at($this->decrypt($this->bcc["contract"]))->send("registerDevice", $pubKey, $newBcUser, 1, 1, $did, "Server Security API", 1, time(), ["from" => $bcauthu], function ($err, $resp) use (&$hash, &$msg) {
				if ($err !== null) {
					$hash = "FAILED";
					$msg = $err;
					return;
				}
				$hash = $resp;
			});

			if($hash == "FAILED"):
				echo " HIAS Blockchain registerDevice failed!\n";
				return False;
			else:
				$txid = $this->storeBlockchainTransaction("Register Device", $hash, $did, 0);
				$this->storeUserHistory("Register Device", $txid, 1, 1, $did, 0, 0);
				$balance = $this->getBlockchainBalance($web3, $bcauthu);
				echo "HIAS Blockchain registerDevice complete! You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";

				$hash = "";
				$msg = "";
				$icontract->at($this->decrypt($this->bcc["icontract"]))->send("deposit", 5000000000000000000, ["from" => $bcauthu, "value" => 5000000000000000000], function ($err, $resp) use (&$hash, &$msg) {
					if ($err !== null) {
						$hash = "FAILED";
						$msg = $err;
						return;
					}
					$hash = $resp;
				});

				$actionMsg = "";

				if($hash == "FAILED"):
					echo " HIAS Blockchain iotJumpWay deposit failed!"  . $msg . "\n";
				else:
					$txid = $this->storeBlockchainTransaction("Deposit", $hash, $did, 0);
					$this->storeUserHistory("Deposit", $txid, 1, 1, $did, 0, 0);
					echo " HIAS Blockchain iotJumpWay deposit complete!\n";

					$hash = "";
					$msg = "";
					$icontract->at($this->decrypt($this->bcc["icontract"]))->send("registerAuthorized", $newBcUser, ["from" => $bcauthu], function ($err, $resp) use (&$hash, &$msg) {
						if ($err !== null) {
							$hash = "FAILED";
							$msg = $err;
							return;
						}
						$hash = $resp;
					});

					if($hash == "FAILED"):
						echo " HIAS Blockchain registerAuthorized failed!\n";
					else:
						$txid = $this->storeBlockchainTransaction("iotJumpWay Register Authorized", $hash, $did, 0);
						$this->storeUserHistory("Register Authorized", $txid, 1, 1, $did, 0, 0);
						$balance = $this->getBlockchainBalance($web3, $bcauthu);
						echo "HIAS Blockchain registerAuthorized complete You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
					endif;
				endif;

				echo "";
				echo "!! NOTE THESE CREDENTIALS AND KEEP THEM IN A SAFE PLACE !!\n";
				echo "! Device, Server Security API, has been created with ID " . $did . " !\n";
				echo "!! Your device public key is: " . $pubKey . " !!\n";
				echo "!! Your device private key is: " . $privKey . " !!\n";
				echo "!! Your device MQTT username is: " . $mqttUser . " !!\n";
				echo "!! Your device MQTT password is: " . $mqttPass . " !!\n";
				echo "";

			endif;
		endif;
		return True;
	}

	private function addAmqpUser($username, $key)
	{
		$query = $this->conn->prepare("
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
			':pw' => $this->encrypt($key)
		]);
		$amid = $this->conn->lastInsertId();
		return $amid;
	}

	private function addAmqpUserPerm($uid, $permission)
	{
		$query = $this->conn->prepare("
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
		$query = $this->conn->prepare("
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
		$query = $this->conn->prepare("
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
		$query = $this->conn->prepare("
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

	public function generate_uuid() {
		return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
			mt_rand( 0, 0xffff ),
			mt_rand( 0, 0x0C2f ) | 0x4000,
			mt_rand( 0, 0x3fff ) | 0x8000,
			mt_rand( 0, 0x2Aff ), mt_rand( 0, 0xffD3 ), mt_rand( 0, 0xff4B )
		);
	}

	public	function generateKey($length = 30){
		$characters='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0987654321'.time();
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = $length; $i > 0; $i--)
		{
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	public function password($l = 20, $c = 2, $n = 2, $s = 2) {
		$out = "";
		$count = $c + $n + $s;
		if(!is_int($l) || !is_int($c) || !is_int($n) || !is_int($s)) {
			trigger_error('Argument(s) not an integer', E_USER_WARNING);
			return false;
		}
		elseif($l < 0 || $l > 20 || $c < 0 || $n < 0 || $s < 0) {
			trigger_error('Argument(s) out of range', E_USER_WARNING);
			return false;
		}
		elseif($c > $l) {
			trigger_error('Number of password capitals required exceeds password length', E_USER_WARNING);
			return false;
		}
		elseif($n > $l) {
			trigger_error('Number of password numerals exceeds password length', E_USER_WARNING);
			return false;
		}
		elseif($s > $l) {
			trigger_error('Number of password capitals exceeds password length', E_USER_WARNING);
			return false;
		}
		elseif($count > $l) {
			trigger_error('Number of password special characters exceeds specified password length', E_USER_WARNING);
			return false;
		}
		$chars = "abcdefghijklmnopqrstuvwxyz";
		$caps = strtoupper($chars);
		$nums = "0123456789";
		$syms = "!@#$%^&*()-_?";
		for($i = 0; $i < $l; $i++) {
			$out .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		if($count) {
			$tmp1 = str_split($out);
			$tmp2 = array();
			for($i = 0; $i < $c; $i++) {
				array_push($tmp2, substr($caps, mt_rand(0, strlen($caps) - 1), 1));
			}
			for($i = 0; $i < $n; $i++) {
				array_push($tmp2, substr($nums, mt_rand(0, strlen($nums) - 1), 1));
			}
			for($i = 0; $i < $s; $i++) {
				array_push($tmp2, substr($syms, mt_rand(0, strlen($syms) - 1), 1));
			}
			$tmp1 = array_slice($tmp1, 0, $l - $count);
			$tmp1 = array_merge($tmp1, $tmp2);
			shuffle($tmp1);
			$out = implode('', $tmp1);
		}

		return $out;
	}

	public static function createPasswordHash($password)
	{
		return password_hash(
			$password,
			PASSWORD_DEFAULT);
	}

	private function encrypt($value)
	{
		$encryption_key = base64_decode($this->key);
		$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length("aes-256-cbc"));
		$encrypted = openssl_encrypt($value, "aes-256-cbc", $encryption_key, 0, $iv);
		return base64_encode($encrypted . "::" . $iv);
	}

	public function decrypt($encrypted)
	{
		$encryption_key = base64_decode($this->key);
		list($encrypted_data, $iv) = explode("::", base64_decode($encrypted), 2);
		return openssl_decrypt($encrypted_data, "aes-256-cbc", $encryption_key, 0, $iv);
	}
}

$Core  = new Core();
$TassAI = new TassAI($Core);
$TassAI->zone($argv[1], $argv[2], $argv[3]);
$TassAI->device($argv[4], $argv[5], $argv[6], $argv[7], $argv[8], $argv[9], $argv[10]);

?>