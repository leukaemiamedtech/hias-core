<?php session_start();

class Core
{
    private $dbname, $dbusername, $dbpassword;
    public  $dbcon, $config = null;

    public function __construct()
    {
        $config = json_decode(file_get_contents("/fserver/var/www/Classes/Core/confs.json", true));

        $this->confs = $config;
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

class Database{

    private $key = "";
    private $out = "";

    public function __construct(Core $core)
    {
        $this->confs = $core->confs;
        $this->key = $core->key;
        $this->conn = $core->dbcon;
    }

    public function finalize($domain, $pub, $prv, $gmaps, $lat, $lng, $app){

        $pdoQuery = $this->conn->prepare("
            UPDATE settings
            SET domainString = :domain,
                recaptcha = :recaptcha,
                recaptchas = :recaptchas,
                gmaps = :gmaps,
                lt = :lt,
                lg = :lg,
                aid = :aid
        ");
        $pdoQuery->execute([
            ":domain"=>$this->encrypt($domain),
            ":recaptcha"=>$this->encrypt($pub),
            ":recaptchas"=>$this->encrypt($prv),
            ":gmaps"=>$this->encrypt($gmaps),
            ":lt"=>$this->encrypt($lat),
            ":lg"=>$this->encrypt($lng),
            ":aid"=>$app
        ]);
        $pdoQuery->closeCursor();
        $pdoQuery = null;

        echo "! Database has been finalized !";
        return True;
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
$Database = new Database($Core);
$Database->finalize($argv[1], $argv[2], $argv[3], $argv[4], $argv[5], $argv[6], $argv[7]);

?>