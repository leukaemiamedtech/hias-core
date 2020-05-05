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

            include dirname(__FILE__) . '/../../Classes/helpers.php';

            $this->_helpers = new Helpers($this);
            $this->_confs = $this->getConfigs();
            $this->_pageDetails = $_pageDetails;

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
                SELECT version,
                    phpmyadmin,
                    recaptcha,
                    recaptchas,
                    meta_title,
                    meta_description,
                    domainString
                FROM settings
            ");
            $pdoQuery->execute();
            $response=$pdoQuery->fetch(PDO::FETCH_ASSOC);
            $pdoQuery->closeCursor();
            $pdoQuery = null;
            return $response;
        }

        public function getCPULoad($coreCount = 2, $interval = 1)
        {
            $rs = sys_getloadavg();
            $interval = $interval >= 1 && 3 <= $interval ? $interval : 1;
            $load = $rs[$interval];
            return number_format(round(($load * 100) / $coreCount,2),2);
        }

        public function getMemoryUsage()
        {
            $free = shell_exec('free');
            $free = (string)trim($free);
            $free_arr = explode("\n", $free);
            $mem = explode(" ", $free_arr[1]);
            $mem = array_filter($mem);
            $mem = array_merge($mem);
            $memory_usage = $mem[2]/$mem[1]*100;

            return number_format($memory_usage,2);
        }

        public function getTemperature()
        {
            if (exec('cat /sys/class/thermal/thermal_zone0/temp', $t)):
                $temp = round($t[0] / 1000).' °C';
            endif;

            return $temp;
        }

        public function getSwap()
        {
            if (!($free = shell_exec('grep SwapFree /proc/meminfo | awk \'{print $2}\''))):
                $free = 0;
            endif;

            $free = (int)$free;

            if (!($total = shell_exec('grep SwapTotal /proc/meminfo | awk \'{print $2}\''))):
                $total = 0;
            endif;

            $total = (int)$total;
            $used = $total - $free;
            $percent_used = 0;

            if ($total > 0):
                $percent_used = 100 - (round($free / $total * 100));
            endif;

            return $percent_used;

        }

        public function getStats()
        {
            return [
                "CPU"=>number_format($this->getCPULoad(),2),
                "Memory"=>number_format($this->getMemoryUsage(),2),
                "Temperature"=>$this->getTemperature(),
                "Swap"=>$this->getSwap()
            ];

        }

    }

    $_secCon  = new Core();
    $_GeniSys = new aiInit(
        $_secCon,
        $pageDetails);

    if(filter_input(INPUT_POST, 'getServerStats', FILTER_SANITIZE_STRING)):
        die(json_encode($_GeniSys->getStats()));
    endif;