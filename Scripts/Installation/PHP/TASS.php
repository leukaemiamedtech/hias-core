<?php

class Core
{
    private $dbname, $dbusername, $dbpassword;
    public  $dbcon, $config = null;

    public function __construct()
    {
        $config = json_decode(file_get_contents("/fserver/var/www/Classes/Core/confs.json", true));

        $this->confs = $confs;
        $this->key = $config->key;
        $this->dbname = $config->dbname;
        $this->dbusername = $config->dbusername;
        $this->dbpassword = $config->dbpassword;
        $this->connect();
    }

    function connect()
    {
        try
        {
            $this->dbcon = new PDO(
                'mysql:host=localhost'.';dbname='.$this->dbname,
                $this->dbusername,
                $this->dbpassword,
                [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"]
            );
            $this->dbcon->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );
            $this->dbcon->setAttribute(
                PDO::ATTR_EMULATE_PREPARES,
                false
            );
        }
        catch(PDOException $e)
        {
            die($e);
        }
    }
}

class TASS{

    public function __construct(Core $core, $location, $zone, $ip, $mac)
    {
        $this->confs = $core->confs;
        $this->key = $core->key;
        $this->conn = $core->dbcon;
        $this->lid = $location;
        $this->zn = $zone;
        $this->ip = $ip;
        $this->mac = $mac;
    }  
		
    public function zone(){  
        
        $query = $this->conn->prepare("
            INSERT INTO  mqttlz  (
                `lid`,
                `zn`,
                `time`
            )  VALUES (
                :lid,
                :zn,
                :time
            )
        ");
        $query->execute([
            ':lid' => $this->lid,
            ':zn' => $this->zn,
            ':time' => time()
        ]);
        $this->zid = $this->conn->lastInsertId();

        echo "! Zone, " . $this->zn . " has been created with ID " . $this->zid . "!";
    }  

    public function device(){ 

        include 'pbkdf2.php';

        $mqttUser = $this->apiKey(12);
        $mqttPass = $this->password();
        $mqttHash = create_hash($mqttPass);

        $apiKey = $this->apiKey(30);
        $apiSecretKey = $this->apiKey(35);
        
        $query = $this->conn->prepare("
            INSERT INTO  mqttld  (
                `lid`,
                `zid`,
                `name`,
                `mqttu`,
                `mqttp`,
                `apub`,
                `aprv`,
                `time`
            )  VALUES (
                :lid,
                :zid,
                :name,
                :mqttu,
                :mqttp,
                :apub,
                :aprv,
                :time
            )
        ");
        $query->execute([
            ':lid' => $this->lid,
            ':zid' => $this->zid,
            ':name' => "TASS",
            ':mqttu' =>$this->encrypt($mqttUser),
            ':mqttp' =>$this->encrypt($mqttPass),
            ':apub' => $this->encrypt($apiKey),
            ':aprv' => $this->encrypt($apiSecretKey),
            ':time' => time()
        ]);
        $this->did = $this->conn->lastInsertId();

        $query = $this->conn->prepare("
            INSERT INTO  mqttu  (
                `lid`,
                `zid`,
                `did`,
                `uname`,
                `pw`
            )  VALUES (
                :lid,
                :zid,
                :did,
                :uname,
                :pw
            )
        ");
        $query->execute([
            ':lid' => $this->lid,
            ':zid' => $this->zid,
            ':did' => $this->did,
            ':uname' => $mqttUser,
            ':pw' => $mqttHash
        ]);

        $query = $this->conn->prepare("
            INSERT INTO  mqttua  (
                `lid`,
                `zid`,
                `did`,
                `username`,
                `topic`,
                `rw`
            )  VALUES (
                :lid,
                :zid,
                :did,
                :username,
                :topic,
                :rw
            )
        ");
        $query->execute(array(
            ':lid' => $this->lid,
            ':zid' => $this->zid,
            ':did' => $this->did,
            ':username' => $mqttUser,
            ':topic' => $this->lid."/Device/" . $this->zid . "/" . $this->did . "#",
            ':rw' => 4
        ));

        $query = $this->conn->prepare("
            UPDATE mqttl
            SET devices = devices + 1
            WHERE id = :id
        ");
        $query->execute(array(
            ':id'=>$this->lid
        ));

        $pdoQuery = $this->conn->prepare("
            INSERT INTO  tass  (
                `name`,
                `lid`,
                `zid`,
                `did`,
                `ip`,
                `mac`,
                `sport`,
                `sportf`,
                `sckport`
            )  VALUES (
                :name,
                :lid,
                :zid,
                :did,
                :ip,
                :mac,
                :sport,
                :sportf,
                :sckport
            )
        ");
        $pdoQuery->execute([
            ":name" => "TASS",
            ":lid" => $this->lid,
            ":zid" => $this->zid,
            ":did" => $this->did,
            ":ip" => $this->encrypt($this->ip),
            ":mac" => $this->encrypt($this->mac),
            ":sport" => $this->encrypt("8080"),
            ":sportf" => $this->encrypt("stream.mjpg"),
            ":sckport" => $this->encrypt("8181")
        ]);

        echo "";
        echo "!! NOTE THESE CREDENTIALS AND KEEP THEM IN A SAFE PLACE !!\n";
        echo "! Device, " . $this->zn . " has been created with ID " . $this->zid . "!\n";
        echo "!! Your device public key is: " . $apiKey . " !!\n";
        echo "!! Your device private key is: " . $apiSecretKey . " !!\n";
        echo "!! Your device MQTT username is: " . $mqttUser . " !!\n";
        echo "!! Your device MQTT password is: " . $mqttPass . " !!\n";
        echo "";
        return True;
    }   

    public	function apiKey($length = 30){
        $characters='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0987654321'.time();
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = $length; $i > 0; $i--)
        {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
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

    private function encrypt($value)
    {
        $encryption_key = base64_decode($this->key);
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length("aes-256-cbc"));
        $encrypted = openssl_encrypt($value, "aes-256-cbc", $encryption_key, 0, $iv);
        return base64_encode($encrypted . "::" . $iv);
    }
}


$Core  = new Core();
$TASS = new TASS($Core, $argv[1], $argv[2], $argv[3], $argv[4]);
$TASS->zone();
$TASS->device();

?>