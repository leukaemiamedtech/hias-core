<?php

include 'pbkdf2.php';
include 'Htpasswd.php';

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

class Admin{

    public function __construct(Core $core, $user, $lid, $ip, $mac)
    {
        $this->confs = $core->confs;
        $this->key = $core->key;
        $this->conn = $core->dbcon;
        $this->user = $user;
        $this->lid = (int)$lid;
        $this->ip = $ip;
        $this->mac = $mac;
    }     

    public function create()
    {
        if(!$this->checkUser($this->user)):

            $pass=$this->password(12);
            $passhash=$this->passwordHash($pass);

            $htpasswd = new Htpasswd('/etc/nginx/tass/htpasswd');
            $htpasswd->addUser($user, $pass, Htpasswd::ENCTYPE_APR_MD5);

            $pdoQuery = $this->conn->prepare("
                INSERT INTO users (
                    `username`,
                    `admin`,
                    `password`,
                    `created`
                )  VALUES (
                    :username,
                    :admin,
                    :password,
                    :created
                )
            ");
            $pdoQuery->execute([
                ":username"=>$this->user,
                ":admin"=>1,
                ":password"=>$this->encrypt($passhash),
                ':created' => time()
            ]);
            $this->uid = $this->conn->lastInsertId();
            $pdoQuery->closeCursor();
            $pdoQuery = null;

            echo "";
            echo "!! NOTE THESE CREDENTIALS AND KEEP THEM IN A SAFE PLACE !!\n";
            echo "";
            echo "! Admin user, " . $this->user . " has been created with ID " . $this->uid . " !!\n";
            echo "!! Your username is: " . $this->user . " !!\n";
            echo "!! Your password is: " . $pass . " !!\n";
            echo "!! THESE CREDENTIALS ARE ALSO USED FOR THE TASS STREAM AUTHENTICATION POP UP YOU WILL FACE WHEN YOU FIRST LOGIN !!\n";
            $this->application();

            return True;
        else:
            echo "! A user with this username already exists!\n";
            return False;
        endif;
    }
		
    public function application(){ 

        $apiKey = $this->apiKey(30);
        $apiSecretKey = $this->apiKey(35);

        $mqttUser = $this->apiKey(12);
        $mqttPass = $this->password();
        $mqttHash = create_hash($mqttPass);
        
        $query = $this->conn->prepare("
            INSERT INTO  mqtta  (
                `name`,
                `lid`,
                `uid`,
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
                :uid,
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
            ':name' => $this->user,
            ':lid' => $this->lid,
            ':uid' => $this->uid,
            ':mqttu' =>$this->encrypt($mqttUser),
            ':mqttp' =>$this->encrypt($mqttPass),
            ':apub' => $this->encrypt($apiKey),
            ':aprv' => $this->encrypt($apiSecretKey),
            ':ip' => $this->encrypt($this->ip),
            ':mac' => $this->encrypt($this->mac),
            ':time' => time()
        ]);
        $this->aid = $this->conn->lastInsertId();

        $query = $this->conn->prepare("
            UPDATE users
            SET aid = :aid
            WHERE id = :id
        ");
        $query->execute(array(
            ':aid'=>$this->aid,
            ':id'=>$this->lid
        ));

        $query = $this->conn->prepare("
            INSERT INTO  mqttu  (
                `lid`,
                `aid`,
                `uid`,
                `uname`,
                `pw`
            )  VALUES (
                :lid,
                :aid,
                :uid,
                :uname,
                :pw
            )
        ");
        $query->execute([
            ':lid' => $this->lid,
            ':aid' => $this->aid,
            ':uid' => $this->uid,
            ':uname' => $mqttUser,
            ':pw' => $mqttHash
        ]);

        $query = $this->conn->prepare("
            INSERT INTO  mqttua  (
                `lid`,
                `aid`,
                `uid`,
                `username`,
                `topic`,
                `rw`
            )  VALUES (
                :lid,
                :aid,
                :uid,
                :username,
                :topic,
                :rw
            )
        ");
        $query->execute(array(
            ':lid' => $this->lid,
            ':aid' => $this->aid,
            ':uid' => $this->uid,
            ':username' => $mqttUser,
            ':topic' => $this->lid."/Devices/#",
            ':rw' => 4
        ));

        $query = $this->conn->prepare("
            INSERT INTO  mqttua  (
                `lid`,
                `aid`,
                `uid`,
                `username`,
                `topic`,
                `rw`
            )  VALUES (
                :lid,
                :aid,
                :uid,
                :username,
                :topic,
                :rw
            )
        ");
        $query->execute(array(
            ':lid' => $this->lid,
            ':aid' => $this->aid,
            ':uid' => $this->uid,
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
        echo "!! Application, " . $this->user . " has been created with ID " . $this->aid . " !!\n";
        echo "!! Your application public key is: " . $apiKey . " !!\n";
        echo "!! Your application private key is: " . $apiSecretKey . " !!\n";
        echo "!! Your application MQTT username is: " . $mqttUser . " !!\n";
        echo "!! Your application MQTT password is: " . $mqttPass . " !!\n";
        echo "";
        return True;
    }

    public function checkUser($username)
    {

        $pdoQuery = $this->conn->prepare("
            SELECT id
            FROM users
            WHERE username = :username
        ");
        $pdoQuery->execute([
            ":username"=>$username
        ]);
        $response=$pdoQuery->fetch(PDO::FETCH_ASSOC);
        $pdoQuery->closeCursor();
        $pdoQuery = null;

        return $response["id"] ? True : False;
    }

    private function decrypt($encrypted)
    {
        $encryption_key = base64_decode($this->key);
        list($encrypted_data, $iv) = explode("::", base64_decode($encrypted), 2);
        return openssl_decrypt($encrypted_data, "aes-256-cbc", $encryption_key, 0, $iv);
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

        $out = 0;
        $count = $c + $n + $s;
        if(!is_int($l) || !is_int($c) || !is_int($n) || !is_int($s)):
            trigger_error('Argument(s) not an integer', E_USER_WARNING);
            return false;
        elseif($l < 0 || $l > 20 || $c < 0 || $n < 0 || $s < 0):
            trigger_error('Argument(s) out of range', E_USER_WARNING);
            return false;
        elseif($c > $l):
            trigger_error('Number of password capitals required exceeds password length', E_USER_WARNING);
            return false;
        elseif($n > $l):
            trigger_error('Number of password numerals exceeds password length', E_USER_WARNING);
            return false;
        elseif($s > $l):
            trigger_error('Number of password capitals exceeds password length', E_USER_WARNING);
            return false;
        elseif($count > $l):
            trigger_error('Number of password special characters exceeds specified password length', E_USER_WARNING);
            return false;
        endif;
        $chars = "abcdefghijklmnopqrstuvwxyz";
        $caps = strtoupper($chars);
        $nums = "0123456789";
        $syms = "!@#$%^&*()-_?";
        for($i = 0; $i < $l; $i++) {
            $out .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        if($count):
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
        endif;
    
        return $out;
    }

    private static function passwordHash($password) {
        return password_hash($password, PASSWORD_DEFAULT);
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
$Admin = new Admin($Core, $argv[1], $argv[2], $argv[3], $argv[4]);
$Admin->create();

?>