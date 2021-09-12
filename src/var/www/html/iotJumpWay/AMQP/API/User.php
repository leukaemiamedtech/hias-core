<?php
	header("Access-Control-Allow-Orgin: *");
	header("Access-Control-Allow-Methods: *");
	header("Access-Control-Allow-Headers: Authorization, Origin, X-Requested-With, Content-Type, Accept");

	$pageDetails = [
		"PageID" => "iotJumpWay",
		"SubPageID" => "AMQP",
		"LowPageID" => "API"
	];

	include dirname(__FILE__) . '/../../../../Classes/Core/init.php';

	class AMQP{

		protected $HIAS;

		public function __construct($HIAS)
		{
			$this->HIAS = $HIAS;
		}

		public function process()
		{
			#$amqppubKey = $this->HIAS->helpers->generate_uuid();
			#$amqpprvKey = $this->HIAS->helpers->generate_key(32);
			#$amqpKeyHash = $this->HIAS->helpers->oEncrypt($this->HIAS->helpers->password_hash($amqpprvKey));

			#print_r([
			#	$amqppubKey,
			#	$amqpprvKey,
			#	$amqpKeyHash
			#]);

			if($_SERVER['REQUEST_METHOD'] != "POST"):
				return $this->response("deny", 200);
			endif;
			if(!isSet($_POST["username"])):
				return $this->response("deny", 200);
			endif;
			if(!isSet($_POST["password"])):
				return $this->response("deny", 200);
			endif;

			$pdoQuery = $this->HIAS->conn->prepare("
				SELECT id,
					username,
					pw
				FROM amqpu
				WHERE username = :username
			");
			$pdoQuery->execute([
				":username" => $_POST["username"]
			]);
			$user=$pdoQuery->fetch(PDO::FETCH_ASSOC);

			if(!$user["username"]):
				return $this->response("deny", 200);
				$this->writeFile("debug.txt", $user);
			endif;

			if($this->HIAS->helpers->verify_password($_POST["password"], $this->HIAS->helpers->oDecrypt($user["pw"]))):

				$pdoQuery = $this->HIAS->conn->prepare("
					SELECT permission
					FROM amqpp
					WHERE uid = :uid
				");
				$pdoQuery->execute([
					":uid" => $user["id"]
				]);
				$permissions=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
				$this->writeFile("debugUser.txt", $permissions);

				$permissionList = "";
				foreach($permissions AS $key => $value):
					$permissionList .= $value["permission"] . " ";
				endforeach;

				$this->writeFile("debugUser.txt", "OK");
				return $this->response("Allow " . $permissionList, 200);
			else:
				$this->writeFile("debugUser.txt", "FO");
				return $this->response("deny", 200);
			endif;
		}

		private function responseStatus($code) {

			$status = [
				200 => 'OK',
				401 => 'Not Authorized',
				404 => 'Not Found',
				405 => 'Method Not Allowed',
				500 => 'Internal Server Error',
			];

			return ($status[$code])?$status[$code]:$status[500];

		}

		private function response($data, $status = 200)
		{
			header("HTTP/1.1 " . $status . " " . $this->responseStatus($status));
			return $data;
		}

		public function writeFile($file, $data)
		{
			$fps = fopen($file, 'w');
			fwrite($fps, print_r($data, TRUE));
			fclose($fps);
		}
	}

	$AMQP = new AMQP($HIAS);
	header("HTTP/1.1 200 OK");
	echo $AMQP->process();

