<?php

	abstract class Auth
	{
		protected $invalidMethod = False;
		protected $_Endpoint,
			$_Verb,
			$_Method,
			$_Args,
			$_application=[];

		function __construct()
		{
			header("Access-Control-Allow-Orgin: *");
			header("Access-Control-Allow-Methods: *");
			header("Content-Type: application/json");
			header("Access-Control-Allow-Headers: Authorization, Origin, X-Requested-With, Content-Type, Accept");

			$this->_Method = $_SERVER['REQUEST_METHOD'];
			$this->_Args = explode('/', rtrim($_REQUEST['params'], '/'));
			$this->_Endpoint = array_shift($this->_Args);

			if (array_key_exists(0,$this->_Args) && !is_numeric($this->_Args[0])):
				$this->_Verb = array_shift($this->_Args);
			endif;

			if ($this->_Method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)):
				if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE'):
					$this->_Method = 'DELETE';
				elseif($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT'):
					$this->_Method = 'PUT';
				else:
					$this->_response('Unexpected Header', 401);
				endif;
			endif;

			switch($this->_Method):
				case 'DELETE':
					$this->invalidMethod = True;
				case 'POST':
					if($_SERVER['CONTENT_TYPE']=='application/json'):
						$_POST = json_decode(file_get_contents('php://input'), true);
					elseif($_SERVER['CONTENT_TYPE']=='application/x-www-form-urlencoded'):
						$_POST  = json_decode(file_get_contents("php://input"), true);
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

		protected static function verifyPassword($password,$hash) {
			return password_verify($password, $hash);
		}

		public function process()
		{
			if($this->invalidMethod):
				return $this->_response(["Response"=>"FAILED","Message"=>"Invalid Method"], 405);
			endif;

			if ((int)method_exists($this, $this->_Endpoint) > 0):
				return $this->_response($this->{$this->_Endpoint}($this->_Args));
			endif;

			return $this->_response("No Endpoint: ".$this->_Endpoint, 404);

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
	}
