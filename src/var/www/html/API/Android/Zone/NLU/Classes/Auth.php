<?php

	abstract class Auth
	{
		protected $invalidMethod = False;
		protected $endpoint,
			$verb,
			$method,
			$args,
			$indentifier,
			$apikey,
			$application=[];

		private $debug = False;

		function __construct()
		{
			header("Access-Control-Allow-Orgin: *");
			header("Access-Control-Allow-Methods: *");
			header("Content-Type: application/json");
			header("Access-Control-Allow-Headers: Authorization, Origin, X-Requested-With, Content-Type, Accept");

			$this->method = $_SERVER['REQUEST_METHOD'];
			$this->args = explode('/', rtrim($_REQUEST['params'], '/'));
			$this->endpoint = array_shift($this->args);

			if (array_key_exists(0,$this->args) && !is_numeric($this->args[0])):
				$this->verb = array_shift($this->args);
			endif;

			if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)):
				if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE'):
					$this->method = 'DELETE';
				elseif($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT'):
					$this->method = 'PUT';
				else:
					$this->_response('Unexpected Header', 401);
				endif;
			endif;

			switch($this->method):
				case 'DELETE':
					$this->invalidMethod = True;
				case 'POST':
					if($_SERVER['CONTENT_TYPE']=='application/json'):
						$_POST = json_decode(file_get_contents('php://input'), true);
						if($this->debug):
							$this->writeFile("type.txt", ["Server","JSON"]);
						endif;
					elseif($_SERVER['CONTENT_TYPE']=='application/x-www-form-urlencoded'):
						$_POST  = json_decode(file_get_contents("php://input"), true);
						if($this->debug):
							$this->writeFile("type.txt", ["Server","URL"]);
						endif;
					endif;
					break;
				case 'GET':
					$this->invalidMethod = True;
					break;
				case 'PUT':
					$this->invalidMethod = True;
					break;
				default:
					$this->invalidMethod = True;
					break;
			endswitch;
		}

		private function _cleanInputs($data)
		{
			$clean_input = Array();
			if (is_array($data)) {
				foreach ($data as $k => $v) {
					$clean_input[$k] = $this->_cleanInputs($v);
				}
			} else {
				$clean_input = trim(strip_tags($data));
			}
			return $clean_input;

		}

		protected static function verifyPassword($password, $hash) {
			return password_verify($password, $hash);
		}

		private function getAuthHeaders(){
			if(!isSet($_SERVER["PHP_AUTH_USER"]) || !isSet($_SERVER["PHP_AUTH_PW"])):
				return False;
			else:
				$this->identifier = $_SERVER["PHP_AUTH_USER"];
				$this->apikey = $_SERVER["PHP_AUTH_PW"];
				return true;
			endif;
		}

		private function checkAuth()
		{
			$path = $this->domain . "/" . $this->hias->hiascdi->confs["url"] . "/" . $this->hias->hiascdi->confs["entities_url"] . "/" . $this->identifier . "?type=Application";

			$request = $this->request("GET", $path, $this->identifier, $this->apikey);
			$application = json_decode($request["body"], true);

			if(isSet($application["Error"])):
				return False;
			endif;

			if($application["authenticationUser"]["value"] !== $this->identifier):
				return False;
			endif;

			if($this->verifyPassword($this->apikey, $this->hias->helpers->oDecrypt($application["authenticationKey"]["value"]))):
				$this->application = $application;
				return True;
			else:
				return False;
			endif;
		}

		private function headers($username = "", $password = "", $content = [])
		{
			$basicAuth = $username . ":" . $password;
			$basicAuth = base64_encode($basicAuth);

			$headers = [
				'Authorization: Basic '. $basicAuth
			];

			foreach($content as $key => $value):
				$headers[] = "Accept: " . $value;
				$headers[] = "Content-Type: " . $value;
			endforeach;

			return $headers;
		}

		public function request($method, $path, $username = "", $password = "", $json = [])
		{
			$content = ["application/json"];

			if($method == "GET"):
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers($username, $password, $content));
				curl_setopt($ch, CURLOPT_HEADER, 1);
				curl_setopt($ch, CURLOPT_URL, $path);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$response = curl_exec($ch);
				$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
				$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				$header = substr($response, 0, $header_size);
				$body = substr($response, $header_size);
				curl_close($ch);
			elseif($method == "POST"):
				$ch = curl_init($path);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers($username, $password, $content));
				curl_setopt($ch, CURLOPT_HEADER, 1);
				curl_setopt($ch, CURLOPT_TIMEOUT, 30);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
				$response = curl_exec($ch);
				$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
				$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				$header = substr($response, 0, $header_size);
				$body = substr($response, $header_size);
				curl_close($ch);
			endif;

			return [
				"body" => $body,
				"code" => $httpcode,
				"header" => $header
			];
		}

		public function process()
		{
			$this->domain = $this->hias->helpers->oDecrypt($this->hias->confs["domainString"]);

			if($this->invalidMethod):
				return $this->_response(["Response"=>"FAILED","Message"=>"Invalid Method"], 405);
			endif;

			if(!isset($_POST["id"])):
				return $this->_response(["Response"=>"FAILED","Message"=>"No Application ID Provided"], 404);
			endif;

			$authHeaders=$this->getAuthHeaders();

			if (!$authHeaders):
				return $this->_response(["Response"=>"FAILED","Message"=>"No Authorisation Provided"], 401);
			endif;

			if(!$this->checkAuth()):
				return $this->_response(["Response"=>"FAILED","Message"=>"Invalid Authorisation Provided"], 401);
			endif;

			if ((int)method_exists($this, $this->endpoint) > 0):
				return $this->_response($this->{$this->endpoint}($this->args), 200);
			endif;

			return $this->_response(["Response"=>"FAILED","Message"=>"No Matching API Endpoint: ".$this->endpoint], 404);
		}

		private function _requestStatus($code) {

			$status = [
				200 => 'OK',
				401 => 'Not Authorized',
				404 => 'Not Found',
				405 => 'Method Not Allowed',
				500 => 'Internal Server Error',
			];

			return ($status[$code])?$status[$code]:$status[500];
		}

		private function _response($data, $status = 200)
		{
			header("HTTP/1.1 " . $status . " " . $this->_requestStatus($status));
			return json_encode($data);
		}

		public function writeFile($file,$data)
		{
			$fps = fopen($file, 'w');
			fwrite($fps, print_r($data, TRUE));
			fclose($fps);
		}
	}
