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
		if(!isset($_POST["uname"])):
			return [
				"Response"=>"FAILED",
				"Message"=>"Username must be provided"
			];
		endif;

		if(!isset($_POST["upass"])):
			return [
				"Response"=>"FAILED",
				"Message"=>"Password must be provided"
			];
		endif;

		$pdoQuery = $this->_GeniSys->_secCon->prepare("
			SELECT users.id,
				users.name,
				users.password,
				mqtta.id as aid,
				mqtta.mqttu,
				mqtta.mqttp,
				mqtta.apub,
				mqtta.aprv
			FROM users users
			INNER JOIN mqtta mqtta
			ON users.id = mqtta.uid
			WHERE users.username = :username
		");
		$pdoQuery->execute([
			":username" => $_POST["uname"]
		]);
		$user=$pdoQuery->fetch(PDO::FETCH_ASSOC);

		if(!$user["id"]):
			return [
				"Response"=>"FAILED",
				"ResponseMessage"=>"Invalid user"
			];
		elseif($this->verifyPassword($_POST["upass"],
			$this->_GeniSys->_helpers->oDecrypt($user["password"]))):

			return [
				"Response"=>"OK",
				"Message"=>"Access granted",
				"Data"=> [
					"AID" => $user["aid"],
					"UID" => $user["id"],
					"UN" => $user["name"],
					"APB" => $this->_GeniSys->_helpers->oDecrypt($user["apub"]),
					"APV" => $this->_GeniSys->_helpers->oDecrypt($user["aprv"])
				]
			];
		else:
			return [
				"Response"=>"FAILED",
				"Message"=>"Access denied"
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