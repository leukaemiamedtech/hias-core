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

class Location{

    public function __construct(Core $core, $location, $application, $ip, $mac)
    {
        $this->confs = $core->confs;
        $this->key = $core->key;
        $this->conn = $core->dbcon;
        $this->ln = $location;
        $this->an = $application;
        $this->ip = $ip;
        $this->mac = $mac;
    }     
		
    public function location(){ 
        
        $query = $this->conn->prepare("
            INSERT INTO  mqttl  (
                `name`,
                `apps`,
                `time`
            )  VALUES (
                :name,
                :apps,
                :time
            )
        ");
        $query->execute(array(
            ':name' => $this->ln,
            ':apps' => 0,
            ':time' => time()
        ));
        $this->lid = $this->conn->lastInsertId();

        echo "! Location, " . $this->ln . " has been created with ID " . $this->lid . "!";
        return True;
    }
		
    public function application(){ 

        $apiKey = $this->apiKey(30);
        $apiSecretKey = $this->apiKey(35);

        include 'pbkdf2.php';

        $mqttUser = $this->apiKey(12);
        $mqttPass = $this->password();
        $mqttHash = create_hash($mqttPass);
        
        $query = $this->conn->prepare("
            INSERT INTO  mqtta  (
                `name`,
                `lid`,
                `mqttu`,
                `mqttp`,
                `apub`,
                `aprv`,
                `ip`,
                `mac`,
                `time`
            )  VALUES (
                :name,
                :lid,
                :mqttu,
                :mqttp,
                :apub,
                :aprv,
                :ip,
                :mac,
                :time
            )
        ");
        $query->execute([
            ':name' => $this->an,
            ':lid' => $this->lid,
            ':mqttu' =>$this->encrypt($mqttUser),
            ':mqttp' =>$this->encrypt($mqttHash),
            ':apub' => $this->encrypt($apiKey),
            ':aprv' => $this->encrypt($apiSecretKey),
            ':ip' => $this->encrypt($this->ip),
            ':mac' => $this->encrypt($this->mac),
            ':time' => time()
        ]);
        $this->aid = $this->conn->lastInsertId();

        $query = $this->conn->prepare("
            INSERT INTO  mqttu  (
                `lid`,
                `aid`,
                `uname`,
                `pw`
            )  VALUES (
                :lid,
                :aid,
                :uname,
                :pw
            )
        ");
        $query->execute([
            ':lid' => $this->lid,
            ':aid' => $this->aid,
            ':uname' => $mqttUser,
            ':pw' => $mqttHash
        ]);

        $query = $this->conn->prepare("
            INSERT INTO  mqttua  (
                `lid`,
                `aid`,
                `username`,
                `topic`,
                `rw`
            )  VALUES (
                :lid,
                :aid,
                :username,
                :topic,
                :rw
            )
        ");
        $query->execute(array(
            ':lid' => $this->lid,
            ':aid' => $this->aid,
            ':username' => $mqttUser,
            ':topic' => $this->lid."/Devices/#",
            ':rw' => 4
        ));

        $query = $this->conn->prepare("
            INSERT INTO  mqttua  (
                `lid`,
                `aid`,
                `username`,
                `topic`,
                `rw`
            )  VALUES (
                :lid,
                :aid,
                :username,
                :topic,
                :rw
            )
        ");
        $query->execute(array(
            ':lid' => $this->lid,
            ':aid' => $this->aid,
            ':username' => $mqttUser,
            ':topic' => $this->lid."/Applications/#",
            ':rw' => 2
        ));

        $query = $this->conn->prepare("
            UPDATE mqttl
            SET apps = apps + 1
            WHERE id = :id
        ");
        $query->execute(array(
            ':id'=>$this->lid
        ));

        echo "";
        echo "!! NOTE THESE CREDENTIALS AND KEEP THEM IN A SAFE PLACE !!\n";
        echo "! Application, " . $this->an . " has been created with ID " . $this->aid . "!\n";
        echo "!! Your application public key is: " . $apiKey . "!\n";
        echo "!! Your application private key is: " . $apiSecretKey . "\n";
        echo "!! Your application MQTT username is: " . $mqttUser . "\n";
        echo "!! Your application MQTT password is: " . $mqttPass . "\n";
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
$Location = new Location($Core, $argv[1], $argv[2], $argv[3], $argv[4]);
$Location->location();
$Location->application();

?>