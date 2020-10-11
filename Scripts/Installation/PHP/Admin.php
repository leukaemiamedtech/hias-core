<?php

include 'pbkdf2.php';
include 'Htpasswd.php';
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

class Admin{

	public function __construct(Core $core)
	{
		$this->confs = $core->confs;
		$this->key = $core->key;
		$this->conn = $core->dbcon;
		$this->bcc = $this->getBlockchainConf();
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

	private function storeBlockchainTransaction($action, $hash, $device = 0, $name = 0)
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
			":aid" => $name,
			":action" => $action,
			':hash' => $this->encrypt($hash),
			":time" => time()
		]);
		$txid = $this->conn->lastInsertId();
		$pdoQuery->closeCursor();
		$pdoQuery = null;
		return $txid;
	}

	private function storeUserHistory($action, $hash, $location = 0, $zone = 0, $device = 0, $sensor = 0, $name = 0)
	{
		$pdoQuery = $this->conn->prepare("
			INSERT INTO  history (
				`uid`,
				`tuid`,
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
				:tuid,
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
			":tuid" => 1,
			":tlid" => $location,
			":tzid" => $zone,
			":tdid" => $device,
			":tsid" => $sensor,
			":taid" => $name,
			":action" => $action,
			":hash" => $hash,
			":time" => time()
		]);
		$txid = $this->conn->lastInsertId();
		$pdoQuery->closeCursor();
		$pdoQuery = null;
		return $txid;
	}

	public function create($name, $email, $user, $paddress, $ppass, $ip, $mac, $domain, $haddress, $hpass)
	{
		if(!$this->checkUser($user)):

			$pass=$this->password(12);
			$passhash=$this->createPasswordHash($pass);

			$htpasswd = new Htpasswd('/etc/nginx/security/htpasswd');
			$htpasswd->addUser($user, $pass, Htpasswd::ENCTYPE_APR_MD5);

			$pdoQuery = $this->conn->prepare("
				INSERT INTO users (
					`username`,
					`name`,
					`email`,
					`admin`,
					`password`,
					`bcaddress`,
					`bcpw`,
					`nfc`,
					`created`
				)  VALUES (
					:username,
					:name,
					:email,
					:admin,
					:password,
					:bcaddress,
					:bcpw,
					:nfc,
					:created
				)
			");
			$pdoQuery->execute([
				":username"=>$user,
				":name"=>$name,
				":email"=>$email,
				":admin"=>1,
				":password"=>$this->encrypt($passhash),
				":bcaddress"=>$paddress,
				":bcpw"=>$this->encrypt($ppass),
				":nfc"=>"",
				':created' => time()
			]);
			$uid = $this->conn->lastInsertId();
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			echo "";
			echo "!! NOTE THESE CREDENTIALS AND KEEP THEM IN A SAFE PLACE !!\n";
			echo "";
			echo "! Admin user, " . $user . ", has been created with ID " . $uid . " !!\n";
			echo "!! Your username is: " . $user . " !!\n";
			echo "!! Your password is: " . $pass . " !!\n";
			echo "!! THESE CREDENTIALS ARE ALSO USED FOR THE TASS STREAM AUTHENTICATION POP UP YOU WILL FACE WHEN YOU FIRST LOGIN !!\n";
			$this->application($name, $uid, $paddress, $ip, $mac, $domain, $haddress, $hpass);
			return True;
		else:
			echo "! A user with this username already exists!\n";
			return False;
		endif;
	}

	public function application($name, $uid, $paddress, $ip, $mac, $domain, $haddress, $hpass){

		$mqttUser = $this->generate_uuid();
		$mqttPass = $this->password();
		$mqttHash = create_hash($mqttPass);

		$pubKey = $this->generate_uuid();
		$privKey = $this->generateKey(32);
		$privKeyHash = $this->createPasswordHash($privKey);

		$htpasswd = new Htpasswd('/etc/nginx/security/htpasswd');
		$htpasswd->addUser($pubKey, $privKey, Htpasswd::ENCTYPE_APR_MD5);

		$query = $this->conn->prepare("
			INSERT INTO  mqtta  (
				`lid`,
				`uid`,
				`name`,
				`bcaddress`,
				`mqttu`,
				`mqttp`,
				`apub`,
				`aprv`,
				`ip`,
				`mac`,
				`lt`,
				`lg`,
				`status`,
				`time`
			)  VALUES (
				:lid,
				:uid,
				:name,
				:bcaddress,
				:mqttu,
				:mqttp,
				:apub,
				:aprv,
				:ip,
				:mac,
				:lt,
				:lg,
				:status,
				:time
			)
		");
		$query->execute([
			':lid' => 1,
			':uid' => $uid,
			':name' => $name,
			':bcaddress' => $paddress,
			':mqttu' => $this->encrypt($mqttUser),
			':mqttp' => $this->encrypt($mqttPass),
			':apub' => $pubKey,
			':aprv' => $this->encrypt($privKeyHash),
			':ip' => $this->encrypt($ip),
			':mac' => $this->encrypt($mac),
			':lt' => "",
			':lg' => "",
			':status' => "OFFLINE",
			':time' => time()
		]);
		$aid = $this->conn->lastInsertId();

		$query = $this->conn->prepare("
			INSERT INTO  mqttu  (
				`lid`,
				`aid`,
				`uname`,
				`pw`
			)  VALUES (
				:lid,
				:aid,
				:uname,
				:pw
			)
		");
		$query->execute([
			':lid' => 1,
			':aid' => $aid,
			':uname' => $mqttUser,
			':pw' => $mqttHash
		]);

		$query = $this->conn->prepare("
			INSERT INTO  mqttua  (
				`lid`,
				`aid`,
				`username`,
				`topic`,
				`rw`
			)  VALUES (
				:lid,
				:aid,
				:username,
				:topic,
				:rw
			)
		");
		$query->execute(array(
			':lid' => 1,
			':aid' => $aid,
			':username' => $mqttUser,
			':topic' => "1/Devices/#",
			':rw' => 4
		));

		$query = $this->conn->prepare("
			INSERT INTO  mqttua  (
				`lid`,
				`aid`,
				`username`,
				`topic`,
				`rw`
			)  VALUES (
				:lid,
				:aid,
				:username,
				:topic,
				:rw
			)
		");
		$query->execute(array(
			':lid' => 1,
			':aid' => $aid,
			':username' => $mqttUser,
			':topic' => "1/Applications/#",
			':rw' => 4
		));

		$query = $this->conn->prepare("
			UPDATE mqttl
			SET apps = apps + 1
			WHERE id = :id
		");
		$query->execute(array(
			':id'=> 1
		));

		$web3 = $this->blockchainConnection($domain, $pubKey, $privKey);
		$unlocked =  $this->unlockBlockchainAccount($web3, $haddress, $hpass);

		if($unlocked == "FAILED"):
			echo "Unlocking HIAS Blockhain Account Failed!\n";
			return False;
		endif;

		$contract = new Contract($web3->provider, $this->bcc["abi"]);
		$icontract = new Contract($web3->provider, $this->bcc["iabi"]);

		$hash = "";
		$msg = "";
		$contract->at($this->decrypt($this->bcc["contract"]))->send("deposit", 9000000000000000000, ["from" => $haddress, "value" => 9000000000000000000], function ($err, $resp) use (&$hash, &$msg) {
			if ($err !== null) {
				$hash = "FAILED";
				$msg = $err . "\n";
				return;
			}
			$hash = $resp;
		});

		if($hash == "FAILED"):
			echo " HIAS Blockchain deposit failed! \n";
			return False;
		else:
			$txid = $this->storeBlockchainTransaction("Deposit", $hash, 0, $aid);
			$this->storeUserHistory("Deposit", $txid, 1, 0, 0, 0, $aid);
			echo " HIAS Blockchain deposit ok!\n";
			$hash = "";
			$msg = "";
			$contract->at($this->decrypt($this->bcc["contract"]))->send("registerUser", $pubKey, $paddress, true, 1, $name, 1, $aid, time(), 1, ["from" => $haddress], function ($err, $resp) use (&$hash, &$msg) {
				if ($err !== null) {
					$hash = "FAILED";
					$msg = $err . "\n";
					return;
				}
				$hash = $resp;
			});

			if($hash == "FAILED"):
				echo " HIAS Blockchain registerUser failed!\n";
				return False;
			else:
				$txid = $this->storeBlockchainTransaction("Register User", $hash, 0, $aid);
				$this->storeUserHistory("Register User", $txid, 1, 0, 0, 0, $aid);
				$balance = $this->getBlockchainBalance($web3, $haddress);
				echo "Register user completed! You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
			endif;
		endif;

		$hash = "";
		$msg = "";
		$icontract->at($this->decrypt($this->bcc["icontract"]))->send("deposit", 9000000000000000000, ["from" => $haddress, "value" => 9000000000000000000], function ($err, $resp) use (&$hash, &$msg) {
			if ($err !== null) {
				$hash = "FAILED";
				$msg = $err . "\n";
				return;
			}
			$hash = $resp;
		});

		if($hash == "FAILED"):
			echo " HIAS Blockchain deposit failed!\n";
			return False;
		else:
			$txid = $this->storeBlockchainTransaction("Deposit", $hash, 0, $aid);
			$this->storeUserHistory("Deposit", $txid, 1, 0, 0, 0, $aid);
			echo " HIAS Blockchain deposit ok!\n";
			$icontract->at($this->decrypt($this->bcc["icontract"]))->send("registerAuthorized", $paddress, ["from" => $haddress], function ($err, $resp) use (&$hash, &$msg) {
				if ($err !== null) {
					$hash = "FAILED";
					$msg = $err . "\n";
					return;
				}
				$hash = $resp;
			});

			if($hash == "FAILED"):
				echo " HIAS Blockchain registerAuthorized failed!\n";
			else:
				$txid = $this->storeBlockchainTransaction("iotJumpWay Register Authorized", $hash, 0, $aid);
				$this->storeUserHistory("Register Authorized", $txid, 1, 0, 0, 0, $aid);
				$balance = $this->getBlockchainBalance($web3, $haddress);
				echo "iotJumpWay register authorized You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
			endif;
		endif;

		echo "";
		echo "!! NOTE THESE CREDENTIALS AND KEEP THEM IN A SAFE PLACE !!\n";
		echo "! Application, " . $name . ", has been created with ID " . $aid . " !\n";
		echo "!! Your application public key is: " . $pubKey . " !\n";
		echo "!! Your application private key is: " . $privKey . "\n";
		echo "!! Your application MQTT username is: " . $mqttUser . "\n";
		echo "!! Your application MQTT password is: " . $mqttPass . "\n";
		echo "";
		return True;
	}

	public function checkUser($username)
	{

		$pdoQuery = $this->conn->prepare("
			SELECT id
			FROM users
			WHERE username = :username
		");
		$pdoQuery->execute([
			":username"=>$username
		]);
		$response=$pdoQuery->fetch(PDO::FETCH_ASSOC);
		$pdoQuery->closeCursor();
		$pdoQuery = null;

		return $response["id"] ? True : False;
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

		$out = 0;
		$count = $c + $n + $s;
		if(!is_int($l) || !is_int($c) || !is_int($n) || !is_int($s)):
			trigger_error('Argument(s) not an integer', E_USER_WARNING);
			return false;
		elseif($l < 0 || $l > 20 || $c < 0 || $n < 0 || $s < 0):
			trigger_error('Argument(s) out of range', E_USER_WARNING);
			return false;
		elseif($c > $l):
			trigger_error('Number of password capitals required exceeds password length', E_USER_WARNING);
			return false;
		elseif($n > $l):
			trigger_error('Number of password numerals exceeds password length', E_USER_WARNING);
			return false;
		elseif($s > $l):
			trigger_error('Number of password capitals exceeds password length', E_USER_WARNING);
			return false;
		elseif($count > $l):
			trigger_error('Number of password special characters exceeds specified password length', E_USER_WARNING);
			return false;
		endif;
		$chars = "abcdefghijklmnopqrstuvwxyz";
		$caps = strtoupper($chars);
		$nums = "0123456789";
		$syms = "!@#$%^&*()-_?";
		for($i = 0; $i < $l; $i++) {
			$out .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		if($count):
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
		endif;

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
$Admin = new Admin($Core);
$Admin->create($argv[1], $argv[2], $argv[3], $argv[4], $argv[5], $argv[6], $argv[7], $argv[8], $argv[9], $argv[10]);

?>