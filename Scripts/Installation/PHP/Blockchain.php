<?php

include 'pbkdf2.php';
include 'Htpasswd.php';

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

class Blockchain{

	public function __construct(Core $core)
	{
		$this->confs = $core->confs;
		$this->key = $core->key;
		$this->conn = $core->dbcon;
	}

	public function createConfig()
	{
		$pdoQuery = $this->conn->prepare("
			INSERT INTO  blockchain  (
				`dc`,
				`ic`,
				`pc`
			)  VALUES (
				:dc,
				:ic,
				:pc
			)
		");
		$pdoQuery->execute([
			':dc' => 1,
			':ic' => 2,
			':pc' => 3
		]);

		return True;
	}

	public function createContract($contract, $name, $acc, $txn, $abi)
	{
		$pdoQuery = $this->conn->prepare("
			INSERT INTO  contracts  (
				`contract`,
				`name`,
				`acc`,
				`txn`,
				`abi`,
				`uid`,
				`time`
			)  VALUES (
				:contract,
				:name,
				:acc,
				:txn,
				:abi,
				:uid,
				:time
			)
		");
		$pdoQuery->execute([
			':contract' => $this->encrypt($contract),
			':name' => $this->encrypt($name),
			':acc' => $this->encrypt($acc),
			':txn' => $this->encrypt($txn),
			':abi' => $abi,
			':uid' => 1,
			':time' => time()
		]);
		$contractId = $this->conn->lastInsertId();
		$pdoQuery->closeCursor();

		$pdoQuery = null;
		$pdoQuery = $this->conn->prepare("
			INSERT INTO  transactions (
				`uid`,
				`cid`,
				`action`,
				`hash`,
				`time`
			)  VALUES (
				:uid,
				:cid,
				:action,
				:hash,
				:time
			)
		");
		$pdoQuery->execute([
			":uid" => 1,
			":cid" => $contractId,
			":action" => "Created HIAS Blockchain Contract",
			':hash' => $this->encrypt($txn),
			":time" => time()
		]);
		$txid = $this->conn->lastInsertId();
		$pdoQuery->closeCursor();
		$pdoQuery = null;

		$pdoQuery = $this->conn->prepare("
			INSERT INTO  history (
				`uid`,
				`tcid`,
				`action`,
				`hash`,
				`time`
			)  VALUES (
				:uid,
				:tcid,
				:action,
				:hash,
				:time
			)
		");
		$pdoQuery->execute([
			":uid" => 1,
			":tcid" => $contractId,
			":action" => "Created HIAS Blockchain Contract",
			":hash" => $txid,
			":time" => time()
		]);
		$txid = $this->conn->lastInsertId();
		$pdoQuery->closeCursor();
		$pdoQuery = null;

		return True;
	}

	private function encrypt($value)
	{
		$encryption_key = base64_decode($this->key);
		$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length("aes-256-cbc"));
		$encrypted = openssl_encrypt($value, "aes-256-cbc", $encryption_key, 0, $iv);
		return base64_encode($encrypted . "::" . $iv);
	}

}

$Core  = new Core();
$Blockchain = new Blockchain($Core);
if($argv[1]=="Contract"):
	$Blockchain->createContract($argv[2], $argv[3], $argv[4], $argv[5], $argv[6]);
endif;
if($argv[1]=="Config"):
	$Blockchain->createConfig();
endif;

?>