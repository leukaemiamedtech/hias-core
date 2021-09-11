<?php

	class Server
	{
		function __construct($hias)
		{
			$this->hias = $hias;
		}

		public function update_configs()
		{
			$pdoQuery = $this->hias->conn->prepare("
				UPDATE settings
				SET version = :version,
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
				":phpmyadmin" => filter_input(INPUT_POST, "phpmyadmin", FILTER_SANITIZE_STRING),
				":recaptcha" => $this->hias->helpers->oEncrypt(filter_input(INPUT_POST, "recaptcha", FILTER_SANITIZE_STRING)),
				":recaptchas" => $this->hias->helpers->oEncrypt(filter_input(INPUT_POST, "recaptchas", FILTER_SANITIZE_STRING)),
				":gmaps" => $this->hias->helpers->oEncrypt(filter_input(INPUT_POST, "gmaps", FILTER_SANITIZE_STRING)),
				":lt" => $this->hias->helpers->oEncrypt(filter_input(INPUT_POST, "lt", FILTER_SANITIZE_STRING)),
				":lg" => $this->hias->helpers->oEncrypt(filter_input(INPUT_POST, "lg", FILTER_SANITIZE_STRING)),
				":domainString" => $this->hias->helpers->oEncrypt(filter_input(INPUT_POST, "domainString", FILTER_SANITIZE_STRING))
			]);
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			$this->hias->store_user_history("Updated Server Config");

			return [
				"Response"=> "OK",
				"Message" => "Server Settings Updated!"
			];
		}

		public function server_life_graph($hiasbch, $limit = 0, $order = "")
		{
			$limiter = "";
			$orderer = "";

			if($limit):
				$limiter = "&limit=" . $limit;
			endif;

			if($order == ""):
				$orderer = "&orderBy=!Time";
			else:
				$orderer = "&orderBy=" . $order;
			endif;

			$request = $this->hias->hiashdi->request("GET", $this->hias->hiashdi->confs["data_url"] . "?type=Life&q=Use==HIASBCH;HIASBCH==". $hiasbch . $limiter . $orderer, []);
			$response = json_decode($request["body"], true);

			if(!isSet($response["Error"])):
				return  [
					'Response'=>'OK',
					'ResponseData'=>$response
				];
			else:
				return  [
					'Response'=>'FAILED'
				];
			endif;
		}

		public function update_server_life_graph($params=[])
		{
			$data = $this->server_life_graph($this->hias->confs["aid"], 100, "");

			$cpu = [];
			$memory = [];
			$diskspace = [];
			$temperature = [];
			$dates = [];

			if(isSet($data["ResponseData"])):
				foreach($data["ResponseData"] AS $key => $value):
					if(isSet($value["Data"])):
						$cpu[] = $value["Data"]["CPU"];
						$memory[] = $value["Data"]["Memory"];
						$diskspace[] = $value["Data"]["Diskspace"];
						$temperature[] = $value["Data"]["Temperature"];
						$dates[] = $value["Time"];
					endif;
				endforeach;

				$dates = array_reverse($dates);

				$points = [[
					"name" => "CPU",
					"data" => array_reverse($cpu),
					"type" => 'line',
					"smooth" => true,
					"color" => ['orange']
				],
				[
					"name" => "Memory",
					"data" => array_reverse($memory),
					"type" => 'line',
					"smooth" => true,
					"color" => ['yellow']
				],
				[
					"name" => "Diskspace",
					"data" => array_reverse($diskspace),
					"type" => 'line',
					"smooth" => true,
					"color" => ['red']
				],
				[
					"name" => "Temperature",
					"data" => array_reverse($temperature),
					"type" => 'line',
					"smooth" => true,
					"color" => ['purple']
				]];
			endif;

			return [$dates, $points];
		}
	}

	$Server = new Server($HIAS);
	if(filter_input(INPUT_POST, "update_server", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($Server->update_configs()));
	endif;
	if(filter_input(INPUT_POST, "update_server_life_graph", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($Server->update_server_life_graph()));
	endif;