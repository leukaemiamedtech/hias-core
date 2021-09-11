<?php
header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
header("strict-transport-security: max-age=15768000");ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../../vendor/autoload.php';

include dirname(__FILE__) . '/../../Classes/Htpasswd.php';
include dirname(__FILE__) . '/../../html/iotJumpWay/Classes/pbkdf2.php';
include dirname(__FILE__) . '/../../Classes/helpers.php';

include dirname(__FILE__) . '/../../html/HIASHDI/Classes/HIASHDI.php';
include dirname(__FILE__) . '/../../html/HIASCDI/Classes/HIASCDI.php';
include dirname(__FILE__) . '/../../html/HIASBCH/Classes/HIASBCH.php';

    class Core
    {
        private $dbname, $dbusername, $dbpassword;
        public  $dbcon, $config = null;

        public function __construct()
        {
            $config = json_decode(file_get_contents("/hias/var/www/Classes/Core/confs.json", true));

            $this->config = $config;
            $this->dbname = $config->dbname;
            $this->dbusername = $config->dbusername;
            $this->dbpassword = $config->dbpassword;
            $this->mdbname = $config->dbname;
            $this->mdbusername = $config->mdbusername;
            $this->mdbpassword = $config->mdbpassword;
            $this->ldapdc1 = $config->ldapdc1;
            $this->ldapdc2 = $config->ldapdc2;
            $this->ldapdc3 = $config->ldapdc3;
            $this->ldapport = $config->ldapport;
            $this->ldaps = $config->ldaps;
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

    class HIAS
    {
        private $_user = null;
        public $conn, $confs, $auth, $helpers, $page_details;

        function __construct(Core $conn, $page_details)
        {
            $ignore = [
                "API",
                "Install"
            ];

            $this->helpers = new Helpers($this);
            $this->helpers->set_cookie();

            $this->page_details = $page_details;

            $this->conn = $conn->dbcon;
            $this->key = $conn->config->key;

            $this->_mdbname = $conn->config->mdbname;
            $this->_mdbusername = $conn->config->mdbusername;
            $this->_mdbpassword = $conn->config->mdbpassword;

            $this->_ldapdc1 = $conn->config->ldapdc1;
            $this->_ldapdc2 = $conn->config->ldapdc2;
            $this->_ldapdc3 = $conn->config->ldapdc3;
            $this->_ldapport = $conn->config->ldapport;
            $this->_ldaps = $conn->config->ldaps;
            $this->ldapdc = "dc=".$this->_ldapdc1.",dc=".$this->_ldapdc2.",dc=".$this->_ldapdc3;
            $this->ldaphost = $this->_ldapdc1.".".$this->_ldapdc2.".".$this->_ldapdc3;
            $this->ldapport = $this->_ldapport;

            if($this->page_details["PageID"] !== "Install"):
                $this->hiascdi = new HIASCDI($this);
                $this->hiashdi = new HIASHDI($this);
            endif;

            $this->confs = $this->get_config();

            if($this->page_details["PageID"] !== "Install" && !$this->confs["installed"]):
                die(header("Location: /Install"));
            endif;

            if($this->page_details["PageID"] == "Install" && $this->confs["installed"]):
                die(header("Location: /"));
            endif;

            if(!isSet($this->page_details["LowPageID"]) || isSet($this->page_details["LowPageID"]) && !in_array($this->page_details["LowPageID"], $ignore)):
                $this->check_session();
            endif;

            if($this->page_details["PageID"] !== "Install"):
                $this->check_block();

                $this->hiasbch = new HIASBCH($this);
                $this->hiasbch->un = $conn->config->hiasbchun;
                $this->hiasbch->up = $conn->config->hiasbchup;

                $this->domain = $this->helpers->oDecrypt($this->confs["domainString"]);
                $this->host = str_replace("https://","", $this->domain);

                $this->lt = $this->helpers->oDecrypt($this->confs["lt"]);
                $this->lg = $this->helpers->oDecrypt($this->confs["lg"]);
            endif;
        }

        public function check_block()
        {
            $pdoQuery = $this->conn->prepare("
                SELECT ipv6
                FROM blocked
                Where ipv6 = :ipv6
                LIMIT 1
            ");
            $pdoQuery->execute([
                ":ipv6" => $this->helpers->get_ip()
            ]);
            $ip=$pdoQuery->fetch(PDO::FETCH_ASSOC);
            $pdoQuery->closeCursor();
            $pdoQuery = null;
            if(isSet($ip["ipv6"])):
                session_destroy();
                die(header("Location: /Blocked"));
            endif;
        }

        public function check_session()
        {
            if(isset($_SESSION["HIAS"]["Active"]) && $this->page_details["PageID"]=="Login"):
                die(header("Location: /Dashboard"));
            elseif(empty($_SESSION["HIAS"]["Active"]) && $this->page_details["PageID"]!="Login"):
                die(header("Location: /"));
            endif;
        }

        public function connect_to_ldap(){

            ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);
            $ds = ldap_connect($this->ldaphost, $this->ldapport);

            return $ds;
        }

        function ldap_ssha_password($password){
            $salt = substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',4)),0,4);
            return '{SSHA}' . base64_encode(sha1( $password.$salt, TRUE ). $salt);
        }

        function find_ldap_uid($ds)
        {
            $s = ldap_search($ds, "ou=users,dc=".$this->_ldapdc1.",dc=".$this->_ldapdc2.",dc=".$this->_ldapdc3, 'uidnumber=*');
            if ($s)
            {
                function sortByUidNumber($a, $b) {
                    return strnatcasecmp($a['uidnumber'][0], $b['uidnumber'][0]);
                }
                $result = ldap_get_entries($ds, $s);
                $count = $result['count'];
                unset($result['count']);
                usort($result, "sortByUidNumber");
                $biguid = $result[$count-1]['uidnumber'][0];
                return $biguid;
            }
            return null;
        }

        protected function get_config()
        {
            $pdoQuery = $this->conn->prepare("
                SELECT server.version,
                    server.installed,
                    server.lid,
                    server.aid,
                    server.phpmyadmin,
                    server.recaptcha,
                    server.recaptchas,
                    server.gmaps,
                    server.lt,
                    server.lg,
                    server.meta_title,
                    server.meta_description,
                    server.domainString
                FROM settings server
            ");
            $pdoQuery->execute();
            $configs=$pdoQuery->fetch(PDO::FETCH_ASSOC);
            $pdoQuery->closeCursor();
            $pdoQuery = null;

            if(isSet($_SESSION["HIAS"]["Active"]) && $configs["aid"] != NULL):
                $request = $this->hiascdi->request("GET", "entities/" . $configs["aid"] . "?type=HIASBCH", [], $configs["domainString"]);
                $context =  json_decode($request["body"], true);
                if(!isSet($context["Error"])):
                    $configs["status"] = $context["networkStatus"]["value"];
                    $configs["cpu"] = $context["cpuUsage"]["value"];
                    $configs["mem"] = $context["memoryUsage"]["value"];
                    $configs["hdd"] = $context["hddUsage"]["value"];
                    $configs["tempr"] = $context["temperature"]["value"];
                    $configs["alt"] = $context["location"]["value"]["coordinates"][0];
                    $configs["alg"] = $context["location"]["value"]["coordinates"][1];
                endif;
            endif;

            return $configs;
        }

        public function store_user_history($action, $hash = "", $contract = "", $location = "", $zone = "", $application = "", $device = "", $sensor = "", $agent = "", $user = "", $robotics = "")
        {
            $pdoQuery = $this->conn->prepare("
                INSERT INTO  history (
                    `uid`,
                    `tuid`,
                    `tcid`,
                    `tlid`,
                    `tzid`,
                    `tdid`,
                    `taid`,
                    `tagid`,
                    `tsid`,
                    `trid`,
                    `action`,
                    `hash`,
                    `time`
                )  VALUES (
                    :uid,
                    :tuid,
                    :tcid,
                    :tlid,
                    :tzid,
                    :tdid,
                    :taid,
                    :tagid,
                    :tsid,
                    :trid,
                    :action,
                    :hash,
                    :time
                )
            ");
            $pdoQuery->execute([
                ":uid" => $_SESSION["HIAS"]["Uid"],
                ":tuid" => $user ? $user : 0,
                ":tcid" => $contract ? $contract : "",
                ":tlid" => $location ? $location : "",
                ":tzid" => $zone ? $zone : "",
                ":tdid" => $device ? $device : "",
                ":taid" => $application ? $application : "",
                ":tagid" => $agent ? $agent : "",
                ":tsid" => $sensor ? $sensor : "",
                ":trid" => $robotics ? $robotics : "",
                ":action" => $action ? $action : "",
                ":hash" => $hash,
                ":time" => time()
            ]);
            $txid = $this->conn->lastInsertId();
            $pdoQuery->closeCursor();
            $pdoQuery = null;

            return $txid;
        }

        public function get_user($userId)
        {
            $request =  $this->hiascdi->request("GET", $this->hiascdi->confs["entities_url"] . "/" . $userId . "?type=Staff", []);
            $user =  json_decode($request["body"], true);

            return $user;
        }

        public function getMapMarkers($application)
        {
            if(!$application["lt"]):
                $lat = $this->lat;
                $lng = $this->lng;
            else:
                $lat = $device["lt"];
                $lng = $device["lg"];
            endif;

            return [$lat, $lng];
        }

        public function update_hiasbch_life_graph($params=[])
        {
            $data = $this->hiasbch_life_graph(filter_input(INPUT_GET, "device", FILTER_SANITIZE_STRING), 100);

            $cpu = [];
            $memory = [];
            $diskspace = [];
            $temperature = [];
            $dates = [];
            $points = [];

            if(isSet($data["ResponseData"])):
                foreach($data["ResponseData"] AS $key => $value):
                    if(isSet($value["Data"])):
                        $cpu[] = $value["Data"]["CPU"];
                        $memory[] = $value["Data"]["Memory"];
                        $diskspace[] = $value["Data"]["Diskspace"];
                        $temperature[] = $value["Data"]["Temperature"];
                        $dates[] = $value["Time"];
                    endif;
                endforeach;

                $dates = array_reverse($dates);

                $points = [[
                    "name" => "CPU",
                    "data" => array_reverse($cpu),
                    "type" => 'line',
                    "smooth" => true,
                    "color" => ['orange']
                ],
                [
                    "name" => "Memory",
                    "data" => array_reverse($memory),
                    "type" => 'line',
                    "smooth" => true,
                    "color" => ['yellow']
                ],
                [
                    "name" => "Diskspace",
                    "data" => array_reverse($diskspace),
                    "type" => 'line',
                    "smooth" => true,
                    "color" => ['red']
                ],
                [
                    "name" => "Temperature",
                    "data" => array_reverse($temperature),
                    "type" => 'line',
                    "smooth" => true,
                    "color" => ['purple']
                ]];
            endif;

            return [$dates, $points];
        }

    }

    $HIAS = new HIAS(new Core(), $pageDetails);


    if(filter_input(INPUT_POST, "update_hiasbch_entity", FILTER_VALIDATE_BOOLEAN)):
        die(json_encode($HIAS->hiasbch->update_hiasbch_entity()));
    endif;
    if(filter_input(INPUT_POST, "update_bc", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($HIAS->hiasbch->update_config()));
    endif;
    if(filter_input(INPUT_POST, "transfer", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($HIAS->hiasbch->transfer_hias_ether()));
    endif;
    if(filter_input(INPUT_POST, "store_transaction", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($HIAS->hiasbch->store_transaction()));
    endif;
    if(filter_input(INPUT_POST, "store_transaction_post", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($HIAS->hiasbch->store_transaction_post()));
    endif;
    if(filter_input(INPUT_POST, "store_contract", FILTER_VALIDATE_BOOLEAN)):
        die(json_encode($HIAS->hiasbch->store_contract()));
    endif;
    if(filter_input(INPUT_POST, "check_hash", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($HIAS->hiasbch->check_data_integrity()));
    endif;
    if(filter_input(INPUT_POST, "reset_hiasbch_key", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($HIAS->hiasbch->reset_network_key()));
    endif;
    if(filter_input(INPUT_POST, "reset_hiasbch_mqtt", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($HIAS->hiasbch->reset_mqtt_key()));
    endif;
    if(filter_input(INPUT_POST, "reset_hiasbch_amqp", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($HIAS->hiasbch->reset_amqp_key()));
    endif;
    if(filter_input(INPUT_POST, "update_hiasbch_life_graph", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($HIAS->hiasbch->update_hiasbch_life_graph()));
    endif;
