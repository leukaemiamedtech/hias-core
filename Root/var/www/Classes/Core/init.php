<?php
header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
header("strict-transport-security: max-age=15768000");ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include dirname(__FILE__) . '/../../Classes/Htpasswd.php';
    class Core
    {
        private $dbname, $dbusername, $dbpassword;
        public  $dbcon, $config = null;

        public function __construct()
        {
            $config = json_decode(file_get_contents("/fserver/var/www/Classes/Core/confs.json", true));

            $this->config = $config;
            $this->dbname = $config->dbname;
            $this->dbusername = $config->dbusername;
            $this->dbpassword = $config->dbpassword;
            $this->mdbname = $config->dbname;
            $this->mdbusername = $config->mdbusername;
            $this->mdbpassword = $config->mdbpassword;
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

    class aiInit
    {
        private $_user = null;
        public $_secCon, $_confs, $_auth, $_helpers, $_pageDetails;

        function __construct(Core $_secCon, $_pageDetails)
        {
            $this->setCookie();

            $this->_secCon = $_secCon->dbcon;
            $this->_key = $_secCon->config->key;
            
            $this->_mdbname = $_secCon->config->mdbname;
            $this->_mdbusername = $_secCon->config->mdbusername;
            $this->_mdbpassword = $_secCon->config->mdbpassword;

            include dirname(__FILE__) . '/../../Classes/helpers.php';

            $this->_helpers = new Helpers($this);
            $this->_confs = $this->getConfigs();
            $this->_pageDetails = $_pageDetails;

            $this->lt = $this->_helpers->oDecrypt($this->_confs["lt"]);
            $this->lg = $this->_helpers->oDecrypt($this->_confs["lg"]);
        }

        private function setCookie()
        {

            if(!isSet($_COOKIE['GeniSysAI'])):
                $rander=rand();
                setcookie(
                    "GeniSysAI",
                    $rander,
                    time()+(10*365*24*60*60),
                    '/'
                    ,$_SERVER['SERVER_NAME'],
                    true,
                    true
                );
                $_COOKIE['GeniSysAI'] = $rander;
            endif;

        }

        protected function getConfigs()
        {
            $pdoQuery = $this->_secCon->prepare("
                SELECT server.version,
                    server.aid,
                    server.phpmyadmin,
                    server.recaptcha,
                    server.recaptchas,
                    server.gmaps,
                    server.lt,
                    server.lg,
                    server.meta_title,
                    server.meta_description,
                    server.domainString,
                    mqtta.status,
                    mqtta.lt as alt,
                    mqtta.lg as alg,
                    mqtta.cpu,
                    mqtta.mem,
                    mqtta.hdd,
                    mqtta.tempr
                FROM settings server
                INNER JOIN mqtta mqtta 
                ON mqtta.id = server.aid 
            ");
            $pdoQuery->execute();
            $response=$pdoQuery->fetch(PDO::FETCH_ASSOC);
            $pdoQuery->closeCursor();
            $pdoQuery = null;
            return $response;
        }

        public function updateConfigs()
        {
            $pdoQuery = $this->_secCon->prepare("
                UPDATE settings
                SET version = :version,
                    aid = :aid,
                    phpmyadmin = :phpmyadmin,
                    recaptcha = :recaptcha,
                    recaptchas = :recaptchas,
                    gmaps = :gmaps,
                    lt = :lt,
                    lg = :lg,
                    domainString = :domainString 
            ");
            $pdoQuery->execute([
                ":version" => filter_input(INPUT_POST, "version", FILTER_SANITIZE_STRING),
                ":aid" => filter_input(INPUT_POST, "aid", FILTER_SANITIZE_NUMBER_INT),
                ":phpmyadmin" => filter_input(INPUT_POST, "phpmyadmin", FILTER_SANITIZE_STRING),
                ":recaptcha" => $this->_helpers->oEncrypt(filter_input(INPUT_POST, "recaptcha", FILTER_SANITIZE_STRING)),
                ":recaptchas" => $this->_helpers->oEncrypt(filter_input(INPUT_POST, "recaptchas", FILTER_SANITIZE_STRING)),
                ":gmaps" => $this->_helpers->oEncrypt(filter_input(INPUT_POST, "gmaps", FILTER_SANITIZE_STRING)),
                ":lt" => $this->_helpers->oEncrypt(filter_input(INPUT_POST, "lt", FILTER_SANITIZE_STRING)),
                ":lg" => $this->_helpers->oEncrypt(filter_input(INPUT_POST, "lg", FILTER_SANITIZE_STRING)),
                ":domainString" => $this->_helpers->oEncrypt(filter_input(INPUT_POST, "domainString", FILTER_SANITIZE_STRING))
            ]);
            $pdoQuery->closeCursor();
            $pdoQuery = null;

            return [
                "Response"=> "OK", 
                "Message" => "Server Settings Updated!"
            ];
        }

    }

    $_secCon  = new Core();
    $_GeniSys = new aiInit($_secCon, $pageDetails);

    if(filter_input(INPUT_POST, "update_server", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($_GeniSys->updateConfigs()));
    endif;