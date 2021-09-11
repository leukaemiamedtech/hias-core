<?php

	class Login
	{
		private $hias = null;
		private $_Secure = 1;

		function __construct($hias)
		{
			$this->hias = $hias;
		}

		public function login()
		{
			$this->hias->check_block();

			if($this->_Secure):
				$verified = $this->recaptcha();
				if(!$verified):
					return  [
						"Response"=>"FAILED",
						"ResponseMessage"=>"Google ReCaptcha failed, access DENIED!",
						"SessionAttempts"=>$_SESSION["Attempts"]
					];
				endif;
			endif;

			$gsysuser = $this->get_user_by_name(filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING), filter_input(INPUT_POST,'password',FILTER_SANITIZE_STRING));

			if($gsysuser["id"]):

				$ds = $this->hias->connect_to_ldap();
				if ($ds):

					$binddn = "cn=".filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING).",ou=users,dc=".$this->hias->_ldapdc1.",dc=".$this->hias->_ldapdc2.",dc=".$this->hias->_ldapdc3."";

					if (!ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3)):
						return  [
							"Response"=>"FAILED",
							"ResponseMessage"=>"Could load LDAP version 3!"
						];
					endif;
					if (!ldap_set_option($ds, LDAP_OPT_REFERRALS, 0)):
						return  [
							"Response"=>"FAILED",
							"ResponseMessage"=>"Could load LDAP referrals!"
						];
					endif;
					if (!ldap_start_tls($ds)):
						return  [
							"Response"=>"FAILED",
							"ResponseMessage"=>"Could not connect to LDAP server securely!"
						];
					endif;

					if (ldap_bind($ds, $binddn, filter_input(INPUT_POST,'password',FILTER_SANITIZE_STRING))): session_regenerate_id();
						$_SESSION["HIAS"]=[
							"Active"=>True,
							"Uid"=>$gsysuser["id"],
							"Admin"=>$gsysuser["admin"],
							"Identifier"=>$gsysuser["apub"],
							"User"=>filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING),
							"Pass"=>$this->hias->helpers->oEncrypt(filter_input(INPUT_POST, "password", FILTER_SANITIZE_STRING)),
							"Pic"=>$gsysuser["pic"],
							"Mqtt"=> [
								"Location" => $gsysuser["lid"],
								"Application" => $gsysuser["apub"],
								"ApplicationName" => $gsysuser["name"],
								"User" => $gsysuser["mqttu"],
								"Pass" => $gsysuser["mqttp"]
							],
							"BC"=> [
								"BCUser" => $gsysuser["bcaddress"],
								"BCPass" => $gsysuser["bcpw"]
							]
						];

						$pdoQuery = $this->hias->conn->prepare("
							INSERT INTO logins (
								`ipv6`,
								`browser`,
								`language`,
								`time`
							)  VALUES (
								:ipv6,
								:browser,
								:language,
								:time
							)
						");
						$pdoQuery->execute([
							":ipv6" => $this->hias->helpers->oEncrypt($this->hias->helpers->get_ip()),
							":browser" => $this->hias->helpers->oEncrypt($_SERVER["HTTP_USER_AGENT"]),
							":language" => $this->hias->helpers->oEncrypt($_SERVER["HTTP_ACCEPT_LANGUAGE"]),
							":time" => time()
						]);
						$pdoQuery->closeCursor();
						$pdoQuery = null;

						$this->hias->store_user_history("System Login", 0, 0);

						return  [
							"Response"=>"OK",
							"ResponseMessage"=>"Welcome"
						];
					else:
						$pdoQuery = $this->hias->conn->prepare("
							INSERT INTO loginsf (
								`ipv6`,
								`browser`,
								`language`,
								`time`
							)  VALUES (
								:ipv6,
								:browser,
								:language,
								:time
							)
						");
						$pdoQuery->execute([
							":ipv6" => $this->hias->helpers->oEncrypt($this->hias->helpers->get_ip()),
							":browser" => $this->hias->helpers->oEncrypt($_SERVER["HTTP_USER_AGENT"]),
							":language" => $this->hias->helpers->oEncrypt($_SERVER["HTTP_ACCEPT_LANGUAGE"]),
							":time" => time()
						]);
						$pdoQuery->closeCursor();
						$pdoQuery = null;

						$_SESSION["Attempts"] += 1;
						if($_SESSION["Attempts"] >= 3):
							$_SESSION["Attempts"] = 0;

							$pdoQuery = $this->hias->conn->prepare("
								INSERT INTO blocked (
									`ipv6`,
									`banned`
								)  VALUES (
									:ipv6,
									:banned
								)
							");
							$pdoQuery->execute([
								":ipv6" => $this->hias->helpers->get_ip(),
								":banned" => time()
							]);
							$pdoQuery->closeCursor();
							$pdoQuery = null;

							return  [
								"Response"=>"BLOCKED",
								"ResponseMessage"=>"Password incorrect, access BLOCKED!",
								"SessionAttempts"=>$_SESSION["Attempts"]
							];
						else:
							return  [
								"Response"=>"FAILED",
								"ResponseMessage"=>"Login FAILED! " . ldap_error($ds),
								"SessionAttempts"=>$_SESSION["Attempts"]
							];
						endif;
					endif;
				else:
					return  [
						"Response"=>"FAILED",
						"ResponseMessage"=>"Could not connect to LDAP server!"
					];
				endif;

			else:

				$pdoQuery = $this->hias->conn->prepare("
					INSERT INTO loginsf (
						`ipv6`,
						`browser`,
						`language`,
						`time`
					)  VALUES (
						:ipv6,
						:browser,
						:language,
						:time
					)
				");
				$pdoQuery->execute([
					":ipv6" => $this->hias->helpers->oEncrypt($this->hias->helpers->get_ip()),
					":browser" => $this->hias->helpers->oEncrypt($_SERVER["HTTP_USER_AGENT"]),
					":language" => $this->hias->helpers->oEncrypt($_SERVER["HTTP_ACCEPT_LANGUAGE"]),
					":time" => time()
				]);
				$pdoQuery->closeCursor();
				$pdoQuery = null;

				$_SESSION["Attempts"] += 1;
				if($_SESSION["Attempts"] >= 3):
					$_SESSION["Attempts"] = 0;

					$pdoQuery = $this->hias->conn->prepare("
						INSERT INTO blocked (
							`ipv6`,
							`banned`
						)  VALUES (
							:ipv6,
							:banned
						)
					");
					$pdoQuery->execute([
						":ipv6" => $this->hias->helpers->get_ip(),
						":banned" => time()
					]);
					$pdoQuery->closeCursor();
					$pdoQuery = null;

					return  [
						"Response"=>"BLOCKED",
						"ResponseMessage"=>"Username incorrect, access BLOCKED!",
						"SessionAttempts"=>$_SESSION["Attempts"]
					];
				else:
					return  [
						"Response"=>"FAILED",
						"ResponseMessage"=>"Username incorrect, access DENIED!"
					];
				endif;
			endif;

		}

		public function recaptcha()
		{
			if(!filter_input(INPUT_POST,'g-recaptcha-response',FILTER_SANITIZE_STRING)):
				return [
					'Response'=>'FAILED',
					'ResponseMessage'=>'Please verify using Recaptcha.',
				];
			endif;

			$fields = array(
				'secret'=>urlencode($this->hias->helpers->oDecrypt($this->hias->confs["recaptchas"])),
				'response'=>urlencode(filter_input(INPUT_POST, 'g-recaptcha-response', FILTER_SANITIZE_STRING))
			);
			$fields_string = "";

			foreach($fields as $key=>$value):
				$fields_string .= $key.'='.$value.'&';
			endforeach;
			rtrim($fields_string,'&');

			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL,'https://www.google.com/recaptcha/api/siteverify');
			curl_setopt($ch,CURLOPT_POST,count($fields));
			curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			$result = curl_exec($ch);
			$result=json_decode($result, TRUE);

			if($result['success']===true):
				$_SESSION["Attempts"] = !isSet($_SESSION["Attempts"]) ? 0 : $_SESSION["Attempts"];
				return True;
			else:
				return False;
			endif;

		}

		public function resetpass()
		{
			if($this->_Secure):
				$verified = $this->recaptcha();
				if(!$verified):
					return  [
						"Response"=>"FAILED",
						"ResponseMessage"=>"Google ReCaptcha failed, access DENIED!",
						"SessionAttempts"=>$_SESSION["Attempts"]
					];
				endif;
			endif;

			$gsysuser = $this->get_user_by_name(filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING));

			if($gsysuser["id"]):

				$pass = $this->hias->helpers->password();
				$passhash=$this->hias->helpers->createPasswordHash($pass);

				$htpasswd = new Htpasswd('/etc/nginx/security/htpasswd');
				$htpasswd->updateUser(filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING), $pass, Htpasswd::ENCTYPE_APR_MD5);

				$query = $this->hias->conn->prepare("
					UPDATE users
					SET password = :password
					WHERE username = :username
				");
				$query->execute(array(
					':password' => $this->hias->helpers->oEncrypt($passhash),
					':username' => filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING)
				));

				return [
					"Response" => "OK",
					"pw" => $pass
				];
			else:
				return  [
					"Response"=>"FAILED",
					"ResponseMessage"=>"Username not found!"
				];
			endif;
		}

		public function get_user($userId)
		{
			$pdoQuery = $this->hias->conn->prepare("
				SELECT users.id,
					users.password,
					mqtt.id as aid,
					mqtt.apub
				FROM users users
				INNER JOIN mqtta mqtt
				ON users.aid = mqtt.id
				WHERE users.id = :id
			");
			$pdoQuery->execute([
				":id"=> $userId
			]);
			$user=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			$request =  $this->hias->hiascdi->request("GET", $this->hiascdi->confs["entities_url"] . "/" . $user["apub"] . "?attrs=name&type=Staff", []);
			$user =  json_decode($request["body"], true);

			return $user;
		}

		public function get_user_by_name($username = "", $password = "")
		{
			if($password):
				$request = $this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "?type=Staff&q=username.value:" . $username, [], "", $username, $password);
			else:
				$request = $this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "?type=Staff&q=username.value:" . $username, []);
			endif;

			$context =  json_decode($request["body"], true)[0];
			$user["id"] = $context["id"];
			$user["admin"] = $context["permissionsAdmin"]["value"];
			$user["lid"] = $context["networkLocation"]["value"];
			$user["pic"] = $context["picture"]["value"];
			$user["name"] = $context["name"]["value"];
			$user["apub"] = $context["id"];
			$user["mqttu"] = $context["authenticationMqttUser"]["value"];
			$user["mqttp"] = $context["authenticationMqttKey"]["value"];
			$user["bcaddress"] = $context["authenticationBlockchainUser"]["value"];
			$user["bcpw"] = $context["authenticationBlockchainKey"]["value"];

			return $user;
		}
	}

$Login = new Login($HIAS);

if(filter_input(INPUT_POST, "login", FILTER_SANITIZE_STRING)):
	die(json_encode($Login->login()));
endif;
if(filter_input(INPUT_POST, "reset_pass", FILTER_SANITIZE_STRING)):
	die(json_encode($Login->resetpass()));
endif;