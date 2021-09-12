<?php

	class Helpers{

		function __construct($hias)
		{
			$this->hias = $hias;
		}

		public function set_cookie()
		{
			if(!isSet($_COOKIE['GeniSysAI'])):
				$rander=rand();
				setcookie("GeniSysAI", $rander, time()+(10*365*24*60*60), '/', $_SERVER['SERVER_NAME'], true, true);
				$_COOKIE['GeniSysAI'] = $rander;
			endif;
		}

		public function oDecrypt($encrypted)
		{
			$encryptionkey = base64_decode($this->hias->key);
			list($encrypted_data, $iv) = explode("::", base64_decode($encrypted), 2);
			return openssl_decrypt($encrypted_data, "aes-256-cbc", $encryptionkey, 0, $iv);
		}

		public function oEncrypt($value)
		{
			$encryptionkey = base64_decode($this->hias->key);
			$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length("aes-256-cbc"));
			$encrypted = openssl_encrypt($value, "aes-256-cbc", $encryptionkey, 0, $iv);
			return base64_encode($encrypted . "::" . $iv);
		}

		function decrypt($data) {
			$encryptionkey = base64_decode($this->hias->key);
			$method = "aes-" . strlen($encryptionkey) * 8 . "-cbc";
			$iv = substr(base64_decode($data), 0, 16);
			$decoded = openssl_decrypt(substr(base64_decode($data), 16), $method, $encryptionkey, TRUE, $iv);
			return $decoded;
		}

		public function get_ip()
		{
			if(array_key_exists("HTTP_X_FORWARDED_FOR", $_SERVER) && !empty($_SERVER["HTTP_X_FORWARDED_FOR"])):
				if (strpos($_SERVER["HTTP_X_FORWARDED_FOR"],",") > 0):
					$addr = explode( ",", $_SERVER["HTTP_X_FORWARDED_FOR"]);
					return trim($addr[0]);
				else:
					return $_SERVER["HTTP_X_FORWARDED_FOR"];
				endif;
			else:
				return $_SERVER["REMOTE_ADDR"];
			endif;
		}

		public function generate_uuid() {
			return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
				mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
				mt_rand( 0, 0xffff ),
				mt_rand( 0, 0x0C2f ) | 0x4000,
				mt_rand( 0, 0x3fff ) | 0x8000,
				mt_rand( 0, 0x2Aff ), mt_rand( 0, 0xffD3 ), mt_rand( 0, 0xff4B )
			);
		}

		public function password($l = 20, $c = 2, $n = 2, $s = 2) {
			$out = "";
			$count = $c + $n + $s;
			if(!is_int($l) || !is_int($c) || !is_int($n) || !is_int($s)) {
				trigger_error('Argument(s) not an integer', E_USER_WARNING);
				return false;
			}
			elseif($l < 0 || $l > 20 || $c < 0 || $n < 0 || $s < 0) {
				trigger_error('Argument(s) out of range', E_USER_WARNING);
				return false;
			}
			elseif($c > $l) {
				trigger_error('Number of password capitals required exceeds password length', E_USER_WARNING);
				return false;
			}
			elseif($n > $l) {
				trigger_error('Number of password numerals exceeds password length', E_USER_WARNING);
				return false;
			}
			elseif($s > $l) {
				trigger_error('Number of password capitals exceeds password length', E_USER_WARNING);
				return false;
			}
			elseif($count > $l) {
				trigger_error('Number of password special characters exceeds specified password length', E_USER_WARNING);
				return false;
			}
			$chars = "abcdefghijklmnopqrstuvwxyz";
			$caps = strtoupper($chars);
			$nums = "0123456789";
			$syms = "!@#$%^&*()-_?";
			for($i = 0; $i < $l; $i++) {
				$out .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
			}
			if($count) {
				$tmp1 = str_split($out);
				$tmp2 = array();
				for($i = 0; $i < $c; $i++) {
					array_push($tmp2, substr($caps, mt_rand(0, strlen($caps) - 1), 1));
				}
				for($i = 0; $i < $n; $i++) {
					array_push($tmp2, substr($nums, mt_rand(0, strlen($nums) - 1), 1));
				}
				for($i = 0; $i < $s; $i++) {
					array_push($tmp2, substr($syms, mt_rand(0, strlen($syms) - 1), 1));
				}
				$tmp1 = array_slice($tmp1, 0, $l - $count);
				$tmp1 = array_merge($tmp1, $tmp2);
				shuffle($tmp1);
				$out = implode('', $tmp1);
			}

			return $out;
		}

		public static function password_hash($password) {
			return password_hash($password, PASSWORD_DEFAULT);
		}

		public static function verify_password($password, $hash)
		{
			return password_verify(
				$password,
				$hash);
		}

		public	function generate_key($length = 10){
			$characters="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0987654321".time();
			$charactersLength = strlen($characters);
			$randomString = "";
			for ($i = $length; $i > 0; $i--)
			{
				$randomString .= $characters[rand(0, $charactersLength - 1)];
			}
			return $randomString;
		}

		public function time_ago($timestamp) {
			$periods = ['day' => 86400,
						'hour' => 3600,
						'minute' => 60];
			$timeAgo = "";
			foreach($periods AS $name => $seconds):
				$num = floor($timestamp / $seconds);
				$timestamp -= ($num * $seconds);
				$timeAgo .= $num.' '.$name.(($num > 1) ? 's' : '').' ';
			endforeach;

			return trim($timeAgo);
		}
	}