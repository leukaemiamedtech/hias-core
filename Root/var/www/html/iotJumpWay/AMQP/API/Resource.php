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
			if(!isSet($_POST["vhost"])):
				return $this->response("deny", 200);
			endif;
			if(!isSet($_POST["resource"])):
				return $this->response("deny", 200);
			endif;
			if(!isSet($_POST["name"])):
				return $this->response("deny", 200);
			endif;
			if(!isSet($_POST["permission"])):
				return $this->response("deny", 200);
			endif;

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT resources.permission
				FROM amqpvhr resources
				INNER JOIN amqpu users
				ON resources.uid = users.id
				WHERE users.username = :username
					&& resources.vhost = :vhost
					&& resources.rtype = :resource
					&& resources.rname = :name
					&& resources.permission = :permission
			");
			$pdoQuery->execute([
				":username" => $_POST["username"],
				":vhost" => $_POST["vhost"],
				":resource" => $_POST["resource"],
				":name" => $_POST["name"],
				":permission" => $_POST["permission"]
			]);
			$access=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			$this->writeFile("debug.txt", $access);

			if(!$access["permission"]):
				return $this->response("deny", 200);
			endif;

			return $this->response("allow", 200);
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

