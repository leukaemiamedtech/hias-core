<?php

    class Helpers{

        function __construct($_GeniSys)
        {
            $this->_GeniSys = $_GeniSys;
        }

        function decrypt($data) {
            $encryption_key = base64_decode($this->_GeniSys->_key);
            $method = "aes-" . strlen($encryption_key) * 8 . "-cbc";
            $iv = substr(base64_decode($data), 0, 16);
            $decoded = openssl_decrypt(substr(base64_decode($data), 16), $method, $encryption_key, TRUE, $iv);
            return $decoded;
        }

        public function oDecrypt($encrypted)
        {
            $encryption_key = base64_decode($this->_GeniSys->_key);
            list($encrypted_data, $iv) = explode("::", base64_decode($encrypted), 2);
            return openssl_decrypt($encrypted_data, "aes-256-cbc", $encryption_key, 0, $iv);
        }

        public function oEncrypt($value)
        {
            $encryption_key = base64_decode($this->_GeniSys->_key);
            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length("aes-256-cbc"));
            $encrypted = openssl_encrypt($value, "aes-256-cbc", $encryption_key, 0, $iv);
            return base64_encode($encrypted . "::" . $iv);
        }

        public function getUserIP()
        {
            if(array_key_exists(
                "HTTP_X_FORWARDED_FOR",
                $_SERVER) && !empty($_SERVER["HTTP_X_FORWARDED_FOR"])):

                if (strpos(
                    $_SERVER["HTTP_X_FORWARDED_FOR"],
                    ",") > 0):

                        $addr = explode(
                            ",",
                            $_SERVER["HTTP_X_FORWARDED_FOR"]);
                        return trim($addr[0]);
                else:
                    return $_SERVER["HTTP_X_FORWARDED_FOR"];
                endif;

            else:
                return $_SERVER["REMOTE_ADDR"];
            endif;
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

        public static function verifyPassword($password, $hash)
        {
            return password_verify(
                $password,
                $hash);
        }

        public static function createPasswordHash($password)
        {
            return password_hash(
                $password,
                PASSWORD_DEFAULT);
        }

        public    function generateKey($length = 10){
            $characters="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0987654321".time();
            $charactersLength = strlen($characters);
            $randomString = "";
            for ($i = $length; $i > 0; $i--)
            {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }

        public function createHMAC($params=[], $secret)
        {
            $parameters = null;
            foreach($params AS $paramsKey => $paramsValue):
                $parameters = $parameters . $paramsValue . ".";
            endforeach;
            return hash_hmac("sha256", rtrim($parameters, "."), $secret);
        }

        public function apiCall($method, $url, $data=[], $contentType, $headers = [], $security = false)
        {
            if(!$method):
                return [
                    "Response"=>"FAILED",
                    "ResponseMessage"=>"Method input is required!"
                ];
            endif;

            if(!$url):
                return [
                    "Response"=>"FAILED",
                    "ResponseMessage"=>"URL input is required!"
                ];
            endif;

            if(!$contentType):
                return [
                    "Response"=>"FAILED",
                    "ResponseMessage"=>"Content-Type input is required!"
                ];
            endif;

            $curl = curl_init($url);

            switch ($contentType):
                case "application/json":
                    switch ($security):
                        case true:
                            $headers = [
                                "Content-Type: ".$contentType,
                                "Content-Length: ".strlen(json_encode($data))
                            ];
                            break;
                        default:
                            $secret = $this->_GeniSys->_helpers->oDecrypt($this->_GeniSys->_confs["JumpWayAppSecret"]);
                            $secretHash = $this->_GeniSys->_helpers->createHMAC($data, $secret);

                            $headers = [
                                "Content-Type: ".$contentType,
                                "Content-Length: ".strlen(json_encode($data)),
                                "Authorization: Basic ". base64_encode($this->_GeniSys->_helpers->oDecrypt($this->_GeniSys->_confs["jumpwayAppID"]).":".$secretHash)
                            ];
                            break;
                    endswitch;
                    break;
            endswitch;

            switch ($method):

                case "POST":

                    switch ($contentType):

                        case "application/json":
                            curl_setopt($curl, CURLOPT_POST, true);
                            $data ? curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data)) : "";
                            break;

                        default:
                            curl_setopt($curl, CURLOPT_POST, true);
                            $data ? curl_setopt($curl, CURLOPT_POSTFIELDS, $data) : "";
                            break;

                    endswitch;
                    break;

                case "PUT":
                    $data ? curl_setopt($curl, CURLOPT_PUT, 1) : "";
                    break;

                default:
                    $url = sprintf("%s?%s", $url, http_build_query($data));
                    break;

            endswitch;

            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

            $result = curl_exec($curl);
            curl_close($curl);
            return $result;
        }
    }