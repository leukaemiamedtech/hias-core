<?php

$pageDetails = [
	"PageID" => "HIS",
	"SubPageID" => "API",
	"LowPageID" => "Login"
];

include dirname(__FILE__) . '/../../../../../../Classes/Core/init.php';
include dirname(__FILE__) . '/../../../../../../Classes/Core/GeniSys.php';

require_once 'Classes/Auth.php';

class Login extends Auth{

	protected $_GeniSys;

	public function __construct($_GeniSys)
	{
		parent::__construct();
		$this->_GeniSys = $_GeniSys;
	}

	public function Login()
	{
		if(!isset($_POST["apub"])):
			return [
				"Response"=>"FAILED",
				"Message"=>"Public key must be provided"
			];
		endif;

		$pdoQuery = $this->_GeniSys->_secCon->prepare("
			SELECT users.id,
				users.name,
				mqtta.id as aid,
				mqtta.mqttu,
				mqtta.mqttp
			FROM mqtta mqtta
			INNER JOIN users users
			ON users.id = mqtta.uid
			WHERE mqtta.apub = :apub
		");
		$pdoQuery->execute([
			":apub" => $_POST["apub"]
		]);
		$user=$pdoQuery->fetch(PDO::FETCH_ASSOC);

		if(!$user["id"]):
			return [
				"Response"=>"FAILED",
				"ResponseMessage"=>"Invalid user"
			];
		else:
			return [
				"Response"=>"OK",
				"Message"=>"Access granted",
				"Data"=> [
					"AID" => $user["aid"],
					"UID" => $user["id"],
					"UN" => $user["name"]
				]
			];
		endif;
	}
}

try {
	$Login = new Login($_GeniSys);
	echo $Login->process();
} catch (Exception $e) {
	echo json_encode([
		'error' => $e->getMessage()
	]);
}