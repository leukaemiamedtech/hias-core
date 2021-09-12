<?php

$pageDetails = [
	"PageID" => "API",
	"SubPageID" => "API",
	"LowPageID" => "API"
];

include dirname(__FILE__) . '/../../../../../Classes/Core/init.php';

require_once 'Classes/Auth.php';

class NLU extends Auth{

	protected $hias;

	public function __construct($hias)
	{
		parent::__construct();
		$this->hias = $hias;
	}

	public function Authenticate()
	{
		return [
			"Response"=>"OK",
			"Message"=>"Access granted",
			"Data"=> [
				"MqttUser" => $this->hias->helpers->oDecrypt($this->application["authenticationMqttUser"]["value"]),
				"MqttPass" => $this->hias->helpers->oDecrypt($this->application["authenticationMqttKey"]["value"])
			]
		];
	}

	public function Communicate()
	{

		if(!isset($_POST["query"])):
			return [
				"Response"=>"FAILED",
				"Message"=>"Query must be provided"
			];
		endif;

		$path = str_replace(' ', '%20', $this->domain . "/" . $this->hias->hiascdi->confs["url"] . "/" . $this->hias->hiascdi->confs["entities_url"] . "?type=Agent&q=agentType.value==Natural Language Understanding;networkStatus.value==ONLINE");

		$request = $this->request("GET", $path, $this->identifier, $this->apikey);
		$nlu = json_decode($request["body"], true);

		if(isSet($nlu["Error"])):
			return [
				"Response"=>"FAILED",
				"Message"=>"There are currently no Natural Language Understanding Engines online."
			];
		endif;

		$path = $this->domain . "/AI/GeniSysAI/" . $nlu[0]["endpoint"]["value"] . "/Inference";
		$params = ["query" => $_POST["query"]];

		$request = $this->request("POST", $path, $this->identifier, $this->apikey, json_encode($params));
		$responce = json_decode($request["body"], true);

		return [
			"Response"=>$responce["Response"],
			"Message"=>$responce["Response"] == "OK" ? "Request OK" : "Request failed!",
			"Data"=> [
				"Audio" => False,
				"Response" => $responce["ResponseData"]["Response"]
			]
		];
	}
}

try {
	$NLU = new NLU($HIAS);
	echo $NLU->process();
} catch (Exception $e) {
	echo json_encode([
		'error' => $e->getMessage()
	]);
}