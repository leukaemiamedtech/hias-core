<?php

require __DIR__ . '/../../../vendor/autoload.php';

use Web3\Web3;
use Web3\Contract;
use Web3\Utils;

	class Blockchain
	{
		function __construct($_GeniSys)
		{
			$this->_GeniSys = $_GeniSys;
			$this->configs = $this->getConfig();
			$this->bcc = $this->getBlockchainConf();
			$this->web3 = $this->blockchainConnection();
		}

		public function getConfig()
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT *
				FROM blockchain
			");
			$pdoQuery->execute();
			$data=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $data;
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

		private function checkBlockchainPermissions($contract)
		{
			$allowed = "";
			$contract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["contract"]))->call("identifierAllowed", "User", $_SESSION["GeniSysAI"]["Identifier"], ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$allowed) {
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

		private function unlockBlockchainAccount($account, $pass)
		{
			$response = "";
			$personal = $this->web3->personal;
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

		private function getBlockchainBalance($user)
		{
			$nbalance = "";
			$this->web3->eth->getBalance($user, function ($err, $balance) use (&$nbalance) {
				if ($err !== null) {
					$response = "FAILED! " . $err;
					return;
				}
				$nbalance = $balance->toString();
			});

			return Utils::fromWei($nbalance, 'ether')[0];
		}

		public function transferHiasEther()
		{
			$contract = new Contract($this->web3->provider, $this->bcc["abi"]);
			$allowed = $this->checkBlockchainPermissions($contract);

			$from = filter_input(INPUT_POST, "acc", FILTER_SANITIZE_STRING);
			$pass = filter_input(INPUT_POST, "p", FILTER_SANITIZE_STRING);
			$to = filter_input(INPUT_POST, "bcaddress", FILTER_SANITIZE_STRING);

			$unlocked =  $this->unlockBlockchainAccount($from, $pass);

			$txn = "";
			$this->web3->eth->sendTransaction([
				'from' => $from,
				'to' => $to,
				'value' => '0x' . filter_input(INPUT_POST, "amount", FILTER_SANITIZE_NUMBER_INT)
			], function ($err, $transaction) use ($txn) {
				if ($err !== null):
					$txn = "FAILED! " . $err;
					return;
				endif;
				$txn = $transaction;
			});

			if($txn == "FAILED"):
				return [
					"Response"=> "FAILED",
					"Message" => "HIAS Ether Transfer Failed"
				];
			else:
				$action = "Transfer HIAS Ether";
				$txid = $this->storeBlockchainTransaction($action, $txn);
				$txid = $this->storeUserHistory($action, $txid);
				$balance1 = $this->getBlockchainBalance($from);
				$balance2 = $this->getBlockchainBalance($to);
				return [
					"Response"=> "OK",
					"Message" => "HIAS Ether Transfer OK!<br /><br />Sender Balance: " . $balance1 . "<br />Receiver Balance: " . $balance2
				];
			endif;
		}

		private function storeBlockchainTransaction($action, $hash, $contract = 0)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
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
				":uid" => $_SESSION["GeniSysAI"]["Uid"],
				":cid" => $contract,
				":action" => $action,
				':hash' => $this->_GeniSys->_helpers->oEncrypt($hash),
				":time" => time()
			]);
			$txid = $this->_GeniSys->_secCon->lastInsertId();
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			return $txid;
		}

		private function storeUserHistory($action, $hash = 0, $contract = 0)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
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
				":uid" => $_SESSION["GeniSysAI"]["Uid"],
				":tcid" => $contract,
				":action" => $action,
				":hash" => $hash,
				":time" => time()
			]);
			$txid = $this->_GeniSys->_secCon->lastInsertId();
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			return $txid;
		}

		public function storeTransaction()
		{
			$action = filter_input(INPUT_POST, "action", FILTER_SANITIZE_STRING);
			$hash = filter_input(INPUT_POST, "hash", FILTER_SANITIZE_STRING);
			$contract = filter_input(INPUT_POST, "contract", FILTER_SANITIZE_NUMBER_INT);

			if($hash):
				$txid = $this->storeBlockchainTransaction($action, $hash, $contract);
			else:
				$txid = 0;
			endif;

			$this->storeUserHistory($action, $txid, $contract);

			return ["Response" => "OK"];
		}

		public function updateConfig()
		{
			$contract = new Contract($this->web3->provider, $this->bcc["abi"]);
			$allowed = $this->checkBlockchainPermissions($contract);

			if(!filter_input(INPUT_POST, "address", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "HIAS Blockchain account password is required"
				];
			endif;

			if(!filter_input(INPUT_POST, "pw", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "HIAS Blockchain account address is required"
				];
			endif;

			$query = $this->_GeniSys->_secCon->prepare("
				UPDATE blockchain
				SET bcaddress = :address,
					pw = :pw
			");
			$query->execute([
				':address' => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "address", FILTER_SANITIZE_STRING)),
				':pw' => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "pw", FILTER_SANITIZE_STRING))
			]);
			$id = $this->_GeniSys->_secCon->lastInsertId();

			$this->storeUserHistory("Update Blockchain Settings");

			return [
				"Response"=> "OK",
				"Message" => "Blockchain configuration updated!"
			];
		}

		public function retrieveBlockchainHistory($limit = 0, $order = "")
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
				WHERE action = :action
				$orderer
				$limiter
			");
			$pdoQuery->execute([
				":action" => "Update Blockchain Settings"
			]);
			$response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			return $response;
		}

		public function getContracts()
		{

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT id,
					contract,
					name
				FROM contracts
				ORDER BY id DESC
			");
			$pdoQuery->execute();
			$data=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $data;
		}

		public function getContract($id)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT *
				FROM contracts
				WHERE id = :id
			");
			$pdoQuery->execute([":id" => $id]);
			$data=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $data;
		}

		public function storeContract()
		{

			if(!filter_input(INPUT_POST, "id", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Blockchain contract address is required"
				];
			endif;
			if(!filter_input(INPUT_POST, "acc", FILTER_SANITIZE_STRING)):
				return [
					"Response"=> "Failed",
					"Message" => "Blockchain account address is required"
				];
			endif;

			$contract = filter_input(INPUT_POST, "id", FILTER_SANITIZE_STRING);
			$name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
			$acc = filter_input(INPUT_POST, "acc", FILTER_SANITIZE_STRING);
			$txn = filter_input(INPUT_POST, "txn", FILTER_SANITIZE_STRING);
			$action = "Created HIAS Blockchain Contract";

			$query = $this->_GeniSys->_secCon->prepare("
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
			$query->execute([
				':contract' => $this->_GeniSys->_helpers->oEncrypt($contract),
				':name' => $this->_GeniSys->_helpers->oEncrypt($name),
				':acc' => $this->_GeniSys->_helpers->oEncrypt($acc),
				':txn' => $this->_GeniSys->_helpers->oEncrypt($txn),
				':abi' => $_POST["abi"],
				':uid' => $_SESSION["GeniSysAI"]["Uid"],
				':time' => time()
			]);
			$id = $this->_GeniSys->_secCon->lastInsertId();

			$txid = $this->storeBlockchainTransaction($action, $txn, $id);
			$txid = $this->storeUserHistory($action, $txid, $id);

			return [
				"Response"=> "OK",
				"Message" => "Contract stored!",
				"id" => $id
			];
		}

		public function retrieveContractTransactions($contractid, $limit = 0, $order = "")
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
				WHERE cid = :id
				$orderer
				$limiter
			");
			$pdoQuery->execute([
				":id" => $contractid
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

		public function retrieveContractTransactionReceipt($hash)
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

		public function retrieveContractHistory($contractid, $limit = 0, $order = "")
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
				WHERE tcid = :id
				$orderer
				$limiter
			");
			$pdoQuery->execute([
				":id" => $contractid
			]);
			$response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			return $response;
		}

		public function checkDataIntegrity()
		{
			return [
				"Respose"=>"OK",
				"Check"=>password_verify(filter_input(INPUT_POST, "current", FILTER_SANITIZE_STRING), filter_input(INPUT_POST, "hash", FILTER_SANITIZE_STRING)),
				"String"=>filter_input(INPUT_POST, "current", FILTER_SANITIZE_STRING),
				"Hash"=>filter_input(INPUT_POST, "hash", FILTER_SANITIZE_STRING)
			];
		}

	}

	$Blockchain = new Blockchain($_GeniSys);

	if(filter_input(INPUT_POST, "store_transaction", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($Blockchain->storeTransaction()));
	endif;
	if(filter_input(INPUT_POST, "store_contract", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($Blockchain->storeContract()));
	endif;
	if(filter_input(INPUT_POST, "update_bc", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($Blockchain->updateConfig()));
	endif;
	if(filter_input(INPUT_POST, "transfer", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($Blockchain->transferHiasEther()));
	endif;
	if(filter_input(INPUT_POST, "check_hash", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($Blockchain->checkDataIntegrity()));
	endif;