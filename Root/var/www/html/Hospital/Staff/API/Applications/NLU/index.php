<?php

$pageDetails = [
	"PageID" => "HIS",
	"SubPageID" => "API",
	"LowPageID" => "NLU"
];

include dirname(__FILE__) . '/../../../../../../Classes/Core/init.php';
include dirname(__FILE__) . '/../../../../../../Classes/Core/GeniSys.php';

require_once 'Classes/Auth.php';

class NLU extends Auth{

	protected $_GeniSys;

	public function __construct($_GeniSys)
	{
		parent::__construct();
		$this->_GeniSys = $_GeniSys;
	}

	public function Infer()
	{
		if(!isset($_POST["query"])):
			return [
				"Response"=>"FAILED",
				"Message"=>"Query must be provided"
			];
		endif;

		if(!isset($_POST["uid"])):
			return [
				"Response"=>"FAILED",
				"Message"=>"User ID must be provided"
			];
		endif;

		if(!isset($_POST["uname"])):
			return [
				"Response"=>"FAILED",
				"Message"=>"Username must be provided"
			];
		endif;

		if(!isset($_POST["upass"])):
			return [
				"Response"=>"FAILED",
				"Message"=>"User password must be provided"
			];
		endif;

		$pdoQuery = $this->_GeniSys->_secCon->prepare("
			SELECT users.id,
				users.name,
				users.cz,
				users.czt
			FROM users users
			WHERE users.id = :id
		");
		$pdoQuery->execute([
			":id" => $_POST["uid"]
		]);
		$user=$pdoQuery->fetch(PDO::FETCH_ASSOC);

		if(!$user["id"]):
			return [
				"Response"=>"FAILED",
				"Message"=>"Invalid user"
			];
		endif;

		$basicAuth = $_POST["uname"] . ":" . $_POST["upass"];
		$basicAuth = base64_encode($basicAuth);

		$headers = [
			"Content-Type: application/json",
			'Authorization: Basic '. $basicAuth
		];

		if($user["czt"] > time() - 5 * 60):
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT genisysainlu.id,
					genisysainlu.lid,
					genisysainlu.zid,
					genisysainlu.did,
					mqttld.status
				FROM genisysainlu genisysainlu
				INNER JOIN mqttld mqttld
				ON mqttld.id = genisysainlu.did
				WHERE genisysainlu.zid = :id
					&& mqttld.status = :status
			");
			$pdoQuery->execute([
				":id" => $user["cz"],
				":status" => "ONLINE"
			]);
			$nlu=$pdoQuery->fetch(PDO::FETCH_ASSOC);

			if($nlu["id"] != null):
				$endpoint = "Audio";
				$audio = true;
			else:
				$nlu = $this->checkNLU();
				if($nlu["id"] != null):
					$endpoint = "Infer";
					$audio = false;
				else:
					return [
						"Response" => "FAILED",
						"Message" => "There are no active Natural Language Understanding Engines in your location!"
					];
				endif;
			endif;
		else:
			$nlu = $this->checkNLU();
			if($nlu["id"] != null):
				$endpoint = "Infer";
				$audio = false;
			else:
				return [
					"Response" => "FAILED",
					"Message" => "There are no active Natural Language Understanding Engines in your location!"
				];
			endif;
		endif;

		$response = $this->apiCall("POST", $this->_GeniSys->_helpers->oDecrypt($this->_GeniSys->_confs["domainString"]) . "/GeniSysAI/NLU/API/" . $endpoint, $headers, json_encode(["user" => $_POST["uid"], "query" => $_POST["query"]]));

		return [
			"Response" => "OK",
			"Message" => "NLU request successful!wtf",
			"Audio" => $audio,
			"Data" => json_decode($response, true)
		];
	}

	private function checkNLU()
	{
		$pdoQuery = $this->_GeniSys->_secCon->prepare("
			SELECT genisysainlu.id,
				genisysainlu.lid,
				genisysainlu.zid,
				genisysainlu.did
			FROM genisysainlu genisysainlu
			INNER JOIN mqttld mqttld
			ON mqttld.id = genisysainlu.did
			WHERE mqttld.status = :status
		");
		$pdoQuery->execute([
			":status" => "ONLINE"
		]);
		$nlu=$pdoQuery->fetch(PDO::FETCH_ASSOC);
		return $nlu;
	}

	private function apiCall($method, $url, $headers, $json)
	{
		$ch = curl_init($url);
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

		$parsedText = str_replace(chr(10), "", $body);
		$parsedText = str_replace(chr(13), "", $parsedText);

		return $parsedText;
	}
}

try {
	$NLU = new NLU($_GeniSys);
	echo $NLU->process();
} catch (Exception $e) {
	echo json_encode([
		'error' => $e->getMessage()
	]);
}