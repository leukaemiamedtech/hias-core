<?php

require __DIR__ . '/../../../../../vendor/autoload.php';

use Web3\Web3;
use Web3\Contract;
use Web3\Utils;

	class COVID19
	{

		function __construct($_GeniSys)
		{
			$this->_GeniSys = $_GeniSys;

			$this->bcc = $this->getBlockchainConf();
			$this->web3 = $this->blockchainConnection();
			$this->contract = new Contract($this->web3->provider, $this->bcc["abi"]);
			$this->checkBlockchainPermissions();

			$this->dataDir = "Data/";
			$this->dataDirFull = "/fserver/var/www/html/Detection/COVID-19/CNN/";
			$this->dataFiles = $this->dataDir . "*.png";
			$this->allowedFiles = ["png","PNG"];
			$this->api = $this->_GeniSys->_helpers->oDecrypt($this->_GeniSys->_confs["domainString"])."/Detection/COVID-19/CNN/API/Inference";
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

		private function storeUserHistory($action)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
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
			$txid = $this->_GeniSys->_secCon->lastInsertId();
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			return $txid;
		}

		public function deleteData()
		{
			$images = glob($this->dataFiles);
			foreach( $images as $image ):
				unlink($image);
			endforeach;

			return [
				"Response" => "OK",
				"Message" =>  "Deleted SARS-COV-2 Ct-Scan Dataset"
			];

		}

		public function uploadData()
		{
			$dataCells = '';
			if(is_array($_FILES) && !empty($_FILES['covdata'])):
				foreach($_FILES['covdata']['name'] as $key => $filename):
					$file_name = explode(".", $filename);
					if(in_array($file_name[1], $this->allowedFiles)):
						$sourcePath = $_FILES["covdata"]["tmp_name"][$key];
						$targetPath = $this->dataDir . $filename;
						if(!move_uploaded_file($sourcePath, $targetPath)):
							return [
								"Response" => "FAILED",
								"Message" => "Upload failed " . $targetPath
							];
						endif;
					else:
						return [
							"Response" => "FAILED",
							"Message" => "Please upload png files"
						];
					endif;
				endforeach;

				$images = glob($this->dataFiles);
				$count = 1;
				foreach($images as $image):
					$dataCells .= "<div class='col-lg-2 col-md-2 col-sm-2 col-xs-2'><img src='" . $image . "' style='width: 100%; cursor: pointer;' class='classify' title='" . $image . "' id='" . $image . "' /></div>";
					if($count%6 == 0):
						$dataCells .= "<div class='clearfix'></div>";
					endif;
					$count++;
				endforeach;

			else:
				return [
					"Response" => "FAILED",
					"Message" => "You must upload some images (png)"
				];
			endif;

			return [
				"Response" => "OK",
				"Message" => "Data upload OK!",
				"Data" => $dataCells
			];

		}

		public function classifyData()
		{
			$file = $this->dataDirFull . filter_input(INPUT_POST, "im", FILTER_SANITIZE_STRING);
			$mime = mime_content_type($file);
			$info = pathinfo($file);
			$name = $info['basename'];
			$toSend = new CURLFile($file, $mime, $name);

			$headers = [
				'Authorization: Basic '. base64_encode($_SESSION["GeniSysAI"]["User"] . ":" . $this->_GeniSys->_helpers->oDecrypt($_SESSION["GeniSysAI"]["Pass"]))
			];

			$ch = curl_init($this->api);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_POSTFIELDS, [
				'file'=> $toSend,
			]);

			$resp = curl_exec($ch);

			return json_decode($resp, true);

		}

	}

	$COVID19 = new COVID19($_GeniSys);

	if(filter_input(INPUT_POST, "deleteData", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($COVID19->deleteData()));
	endif;

	if(filter_input(INPUT_POST, "uploadCovData", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($COVID19->uploadData()));
	endif;

	if(filter_input(INPUT_POST, "classifyData", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($COVID19->classifyData()));
	endif;