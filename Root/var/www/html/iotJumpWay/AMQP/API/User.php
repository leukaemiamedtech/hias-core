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
	include dirname(__FILE__) . '/../../../../Classes/Core/GeniSys.php';

	class AMQP{

		protected $_GeniSys;

		public function __construct($_GeniSys)
		{
			$this->_GeniSys = $_GeniSys;
		}

		public function process()
		{
			if($_SERVER['REQUEST_METHOD'] != "POST"):
				return $this->response("deny", 200);
			endif;
			if(!isSet($_POST["username"])):
				return $this->response("deny", 200);
			endif;
			if(!isSet($_POST["password"])):
				return $this->response("deny", 200);
			endif;

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
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
			endif;

			if($this->verifyPassword($_POST["password"], $this->_GeniSys->_helpers->oDecrypt($user["pw"]))):

				$pdoQuery = $this->_GeniSys->_secCon->prepare("
					SELECT permission
					FROM amqpp
					WHERE uid = :uid
				");
				$pdoQuery->execute([
					":uid" => $user["id"]
				]);
				$permissions=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
				$this->writeFile("debug.txt", $permissions);

				$permissionList = "";
				foreach($permissions AS $key => $value):
					$permissionList .= $value["permission"] . " ";
				endforeach;

				return $this->response("Allow " . $permissionList, 200);
			else:
				return $this->response("deny", 200);
			endif;
		}

		protected static function verifyPassword($password,$hash) {
			return password_verify($password, $hash);
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

	$AMQP = new AMQP($_GeniSys);
	$AMQP->writeFile("request.txt", $_POST);
	header("HTTP/1.1 200 OK");
	echo $AMQP->process();

