<?php

	class HIASHDI
	{

		function __construct($hias)
		{
			$this->hias = $hias;
			$this->confs = $this->get_config();
		}

		private function get_config()
		{
			$pdoQuery = $this->hias->conn->prepare("
				SELECT *
				FROM hiashdi
			");
			$pdoQuery->execute();
			$response=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $response;
		}

		private function headers($username = "", $password = "", $content = [])
		{
			if($username):
				$basicAuth = $username . ":" . $password;
			else:
				$basicAuth = $_SESSION["HIAS"]["User"] . ":" . $this->hias->helpers->oDecrypt($_SESSION["HIAS"]["Pass"]);
			endif;

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

		public function request($method, $endpoint, $json, $domain = "", $username = "", $password = "", $content = [])
		{
			if($domain):
				$domain = $this->hias->helpers->oDecrypt($domain);
			else:
				$domain = $this->hias->helpers->oDecrypt($this->hias->confs["domainString"]);
			endif;

			if(!count($content)):
				$content = ["application/json"];
			endif;

			$path = $domain . "/" . $this->confs["url"] . "/" . $endpoint;

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
				curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers("", "", $content));
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
			elseif($method == "PATCH"):
				$ch = curl_init($path);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers("", "", $content));
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
			elseif($method == "PUT"):
				$ch = curl_init($path);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers("", "", $content));
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
			elseif($method == "DELETE"):
				$ch = curl_init($path);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers("", "", $content));
				curl_setopt($ch, CURLOPT_HEADER, 1);
				curl_setopt($ch, CURLOPT_TIMEOUT, 30);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
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
	}