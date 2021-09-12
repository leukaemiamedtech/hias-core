<?php

require '../vendor/autoload.php';

use Web3\Web3;
use Web3\Contract;
use Web3\Utils;

    class Install
    {
        private $hias = null;
        private $user = null;

        function __construct($hias)
        {
            $this->hias = $hias;

            $users = exec('/usr/bin/users', $output);
            $this->user = explode(" ", $output[0])[0];
            $this->installConfs = parse_ini_file("/home/" . $this->user . "/HIAS-Core/scripts/install.config");

            $pabi = file_get_contents("../../../hiasbch/contracts/build/permissions.abi");
            $this->pabi = json_decode($pabi, true);

            $iabi = file_get_contents("../../../hiasbch/contracts/build/integrity.abi");
            $this->iabi = json_decode($iabi, true);

            $this->pbin = file_get_contents("../../../hiasbch/contracts/build/permissions.bin");
            $this->ibin = file_get_contents("../../../hiasbch/contracts/build/integrity.bin");

            $this->hiasbch = "";
            $this->hiasbchkey = "";
            $this->hiasbchun = "";
            $this->hiasbchup = "";

            $this->hiascdi = "";
            $this->hiascdikey = "";
            $this->hiascdiun = "";
            $this->hiascdiup = "";

            $this->hiashdi = "";
            $this->hiashdikey = "";
            $this->hiashdiun = "";
            $this->hiashdiup = "";

            $this->mngConn = new MongoDB\Driver\Manager("mongodb://localhost:27017/".$this->installConfs["mongodbname"].'', ["username" => $this->installConfs["mongodbuser"], "password" => $this->installConfs["mongodbpass"]]);

        }

        public function server_settings(){

            $pdoQuery = $this->hias->conn->prepare("
                UPDATE settings
                SET domainString = :domain,
                    recaptcha = :recaptcha,
                    recaptchas = :recaptchas,
                    gmaps = :gmaps,
                    lt = :lt,
                    lg = :lg
            ");
            $pdoQuery->execute([
                ":domain"=>$this->hias->helpers->oEncrypt("https://".$this->installConfs["domain"]),
                ":recaptcha"=>$this->hias->helpers->oEncrypt(filter_input(INPUT_POST, "recaptcha", FILTER_SANITIZE_STRING)),
                ":recaptchas"=>$this->hias->helpers->oEncrypt(filter_input(INPUT_POST, "recaptchas", FILTER_SANITIZE_STRING)),
                ":gmaps"=>$this->hias->helpers->oEncrypt(filter_input(INPUT_POST, "gmaps", FILTER_SANITIZE_STRING)),
                ":lt"=>$this->hias->helpers->oEncrypt(filter_input(INPUT_POST, "lat", FILTER_SANITIZE_STRING)),
                ":lg"=>$this->hias->helpers->oEncrypt(filter_input(INPUT_POST, "lng", FILTER_SANITIZE_STRING))
            ]);
            $pdoQuery->closeCursor();
            $pdoQuery = null;

        }

        public function get_location_categories()
        {
            $pdoQuery = $this->hias->conn->prepare("
                SELECT building
                FROM building_types
                ORDER BY building ASC
            ");
            $pdoQuery->execute();
            $buildings=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
            $pdoQuery->closeCursor();
            $pdoQuery = null;
            return $buildings;
        }

        public function get_zone_categories()
        {
            $pdoQuery = $this->hias->conn->prepare("
                SELECT category
                FROM zone_cats
                ORDER BY category ASC
            ");
            $pdoQuery->execute();
            $categories=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
            $pdoQuery->closeCursor();
            $pdoQuery = null;
            return $categories;
        }

        public function addAmqpUser($username, $key)
        {
            $query = $this->hias->conn->prepare("
                INSERT INTO  amqpu  (
                    `username`,
                    `pw`
                )  VALUES (
                    :username,
                    :pw
                )
            ");
            $query->execute([
                ':username' => $username,
                ':pw' => $this->hias->helpers->oEncrypt($key)
            ]);
            $amid = $this->hias->conn->lastInsertId();
            return $amid;
        }

        public function addAmqpUserPerm($uid, $permission)
        {
            $query = $this->hias->conn->prepare("
                INSERT INTO  amqpp  (
                    `uid`,
                    `permission`
                )  VALUES (
                    :uid,
                    :permission
                )
            ");
            $query->execute([
                ':uid' => $uid,
                ':permission' => $permission
            ]);
        }

        public function addAmqpUserVh($uid, $vhost)
        {
            $query = $this->hias->conn->prepare("
                INSERT INTO  amqpvh  (
                    `uid`,
                    `vhost`
                )  VALUES (
                    :uid,
                    :vhost
                )
            ");
            $query->execute([
                ':uid' => $uid,
                ':vhost' => $vhost
            ]);
        }

        public function addAmqpVhPerm($uid, $vhost, $rtype, $rname, $permission)
        {
            $query = $this->hias->conn->prepare("
                INSERT INTO  amqpvhr  (
                    `uid`,
                    `vhost`,
                    `rtype`,
                    `rname`,
                    `permission`
                )  VALUES (
                    :uid,
                    :vhost,
                    :rtype,
                    :rname,
                    :permission
                )
            ");
            $query->execute([
                ':uid' => $uid,
                ':vhost' => $vhost,
                ':rtype' => $rtype,
                ':rname' => $rname,
                ':permission' => $permission
            ]);
        }

        public function addAmqpVhTopic($uid, $vhost, $rtype, $rname, $permission, $rkey)
        {
            $query = $this->hias->conn->prepare("
                INSERT INTO  amqpvhrt  (
                    `uid`,
                    `vhost`,
                    `rtype`,
                    `rname`,
                    `permission`,
                    `rkey`
                )  VALUES (
                    :uid,
                    :vhost,
                    :rtype,
                    :rname,
                    :permission,
                    :rkey
                )
            ");
            $query->execute([
                ':uid' => $uid,
                ':vhost' => $vhost,
                ':rtype' => $rtype,
                ':rname' => $rname,
                ':permission' => $permission,
                ':rkey' => $rkey
            ]);
        }

        private function unlockBlockchainAccount($web3, $account, $pass)
        {
            $response = "";
            $personal = $web3->personal;
            $personal->unlockAccount($account, $pass, function ($err, $unlocked) use (&$response) {
                if ($err !== null) {
                    $response = "FAILED! " . $err;
                    return;
                }
                if ($unlocked) {
                    $response = "OK";
                } else {
                    $response = "FAILED";
                }
            });
            return $response;
        }

        public function create_user($web3, $pass)
        {
            $newAccount = "";
            $personal = $web3->personal;
            $personal->newAccount($pass, function ($err, $account) use (&$newAccount) {
                if ($err !== null) {
                    $newAccount = "FAILED!";
                    return;
                }
                $newAccount = $account;
            });

            return $newAccount;
        }

        private function blockchainConnection($domain, $pub, $prv)
        {
            $web3 = new Web3($domain . "/hiasbch/api/", 30, $pub, $prv);
            return $web3;
        }

        private function getBlockchainBalance($web3, $account)
        {
            $nbalance = "";
            $web3->eth->getBalance($account, function ($err, $balance) use (&$nbalance) {
                if ($err !== null) {
                    $response = "FAILED! " . $err;
                    return;
                }
                $nbalance = $balance->toString();
            });
            return Utils::fromWei($nbalance, 'ether')[0];
        }

        private function storeUserHistory($action, $hash, $location = "", $zone = "", $device = "", $sensor = "", $application = "")
        {
            $pdoQuery = $this->hias->conn->prepare("
                INSERT INTO  history (
                    `uid`,
                    `tlid`,
                    `tzid`,
                    `tdid`,
                    `tsid`,
                    `taid`,
                    `action`,
                    `hash`,
                    `time`
                )  VALUES (
                    :uid,
                    :tlid,
                    :tzid,
                    :tdid,
                    :tsid,
                    :taid,
                    :action,
                    :hash,
                    :time
                )
            ");
            $pdoQuery->execute([
                ":uid" => 1,
                ":tlid" => $location,
                ":tzid" => $zone,
                ":tdid" => $device,
                ":tsid" => $sensor,
                ":taid" => $application,
                ":action" => $action,
                ":hash" => $hash,
                ":time" => time()
            ]);
            $hash = $this->hias->conn->lastInsertId();
            $pdoQuery->closeCursor();
            $pdoQuery = null;
            return $hash;
        }

        public function create_location(){

            $this->location = $this->hias->helpers->generate_uuid();

            $data = [
                "id" => $this->location,
                "type" => "Location",
                "category" => [
                    "value" => $_POST["location_category"]
                ],
                "name" => [
                    "value" => filter_input(INPUT_POST, "location", FILTER_SANITIZE_STRING)
                ],
                "description" => [
                    "value" =>filter_input(INPUT_POST, "location", FILTER_SANITIZE_STRING)
                ],
                "floorsAboveGround" => [
                    "value" => 0
                ],
                "floorsBelowGround" => [
                    "value" => 0
                ],
                "zones" => [
                    "value" => 0
                ],
                "devices" => [
                    "value" => 0
                ],
                "applications" => [
                    "value" => 0
                ],
                "users" => [
                    "value" => 0
                ],
                "patients" => [
                    "value" => 0
                ],
                "location" => [
                    "type" => "geo:json",
                    "value" => [
                        "type" => "Point",
                        "coordinates" => [0, 0]
                    ]
                ],
                "address" => [
                    "type" => "PostalAddress",
                    "value" => [
                        "addressLocality" => "",
                        "postalCode" => "",
                        "streetAddress" => ""
                    ]
                ],
                "mapUrl" => [
                    "value" => ""
                ],
                "mapUrl" => [
                    "value" => ""
                ],
                "openingHours" => [
                    "value" => []
                ],
                "dateCreated" => [
                    "type" => "DateTime",
                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                ],
                "dateModified" => [
                    "type" => "DateTime",
                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                ]
            ];

            $insert = new \MongoDB\Driver\BulkWrite;
            $_id1 = $insert->insert($data);
            $result = $this->mngConn->executeBulkWrite($this->installConfs["mongodbname"].'.Entities', $insert);

        }

        public function create_zone(){

            $this->zone = $this->hias->helpers->generate_uuid();

            $data = [
                "id" => $this->zone,
                "type" => "Zone",
                "category" => [
                    "value" => $_POST["zone_category"]
                ],
                "name" => [
                    "value" => filter_input(INPUT_POST, "zone", FILTER_SANITIZE_STRING)
                ],
                "description" => [
                    "value" => filter_input(INPUT_POST, "zone", FILTER_SANITIZE_STRING)
                ],
                "devices" => [
                    "value" => 0,
                    "type" => "Number",
                    "metadata" => [
                        "description" => [
                            "value" => "Number of devices connected to this zone"
                        ]
                    ]
                ],
                "networkLocation" => [
                    "value" => $this->location,
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Location entity ID"
                        ]
                    ]
                ],
                "location" => [
                    "type" => "geo:json",
                    "value" => [
                        "type" => "Point",
                        "coordinates" => [0,0]
                    ]
                ],
                "dateCreated" => [
                    "type" => "DateTime",
                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                ],
                "dateModified" => [
                    "type" => "DateTime",
                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                ]
            ];

            $insert = new \MongoDB\Driver\BulkWrite;
            $_id1 = $insert->insert($data);
            $result = $this->mngConn->executeBulkWrite($this->installConfs["mongodbname"].'.Entities', $insert);

        }

        public function finalize_hiasbch(){

            $mqttUser = $this->hias->helpers->generate_uuid();
            $mqttPass = $this->hias->helpers->password();
            $mqttHash = create_hash($mqttPass);

            $pubKey = $this->hias->helpers->generate_uuid();
            $privKey = $this->hias->helpers->generate_key(32);
            $privKeyHash = $this->hias->helpers->password_hash($privKey);

            $amqppubKey = $this->hias->helpers->generate_uuid();
            $amqpprvKey = $this->hias->helpers->generate_key(32);
            $amqpKeyHash = $this->hias->helpers->password_hash($amqpprvKey);

            $this->hiasbch = $pubKey;
            $this->hiasbchkey = $privKey;
            $this->hiasbchun = $mqttUser;
            $this->hiasbchup = $mqttPass;

            $data = [
                "id" => $pubKey,
                "type" => "HIASBCH",
                "name" => [
                    "value" => "HIASBCH"
                ],
                "description" => [
                    "value" => "HIAS Private Ethereum Blockchain"
                ],
                "batteryLevel" => [
                    "value" => 0.00
                ],
                "cpuUsage" => [
                    "value" => 0.00
                ],
                "memoryUsage" => [
                    "value" => 0.00
                ],
                "hddUsage" => [
                    "value" => 0.00
                ],
                "temperature" => [
                    "value" => 0.00
                ],
                "rssi" => [
                    "value" => 0.00
                ],
                "deviceBrandName" => [
                    "value" => "Generic"
                ],
                "deviceModel" => [
                    "value" => "Generic"
                ],
                "deviceManufacturer" => [
                    "value" => "Generic"
                ],
                "deviceSerialNumber" => [
                    "value" => "Generic"
                ],
                "os" => [
                    "value" => "Ubuntu"
                ],
                "osVersion" => [
                    "value" => "20.04.2"
                ],
                "osManufacturer" => [
                    "value" => "Canonical"
                ],
                "software" => [
                    "value" => "HIASBCH"
                ],
                "softwareVersion" => [
                    "value" => "1.0.0"
                ],
                "softwareManufacturer" => [
                    "value" => "Asociación de Investigacion en Inteligencia Artificial Para la Leucemia Peter Moss"
                ],
                "location" => [
                    "type" => "geo:json",
                    "value" => [
                        "type" => "Point",
                        "coordinates" => [floatval(filter_input(INPUT_POST, "lat", FILTER_SANITIZE_STRING)), floatval(filter_input(INPUT_POST, "lng", FILTER_SANITIZE_STRING))]
                    ]
                ],
                "networkStatus" => [
                    "value" => "OFFLINE",
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Network online status"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "networkLocation" => [
                    "value" => $this->location,
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Location entity ID"
                        ]
                    ]
                ],
                "networkZone" => [
                    "value" => $this->zone,
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Zone entity ID"
                        ]
                    ]
                ],
                "ipAddress" => [
                    "value" => $this->installConfs["ip"],
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "IP address of entity"
                        ]
                    ]
                ],
                "macAddress" => [
                    "value" => "",
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "MAC address of entity"
                        ]
                    ]
                ],
                "bluetoothAddress" => [
                    "value" => "",
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Bluetooth address of entity"
                        ]
                    ]
                ],
                "protocols" => [
                    "value" =>["mqtt","http"]
                ],
                "version" => [
                    "value" => 1.0
                ],
                "host" => [
                    "value" => $this->installConfs['domain']
                ],
                "port" => [
                    "value" => 9582
                ],
                "endpoint" => [
                    "value" => "hiasbch/v1"
                ],
                "chainid" => [
                    "value" => $this->installConfs['hiasbchchain']
                ],
                "lastScannedBlock" => [
                    "value" => 0,
                    "metadata" => [
                        "description" => [
                            "value" => "Number of last block scanned by HIASBCH explorer"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "authenticationContract" => [
                    "value" => $this->installConfs['hiasbchpermissions']
                ],
                "dataIntegrityContract" => [
                    "value" => $this->installConfs['hiasbchintegrity']
                ],
                "contracts" => [
                    "value" => [
                        [
                            "name"  => "HIAS v3 Permissions Smart Contract",
                            "contract"  => $this->installConfs['hiasbchpermissions'],
                            "abi"  => $this->pabi,
                            "bin"  => $this->pbin,
                            "transaction"  => $this->installConfs['hiasbchpermissionst'],
                            "time"  => time()
                        ],
                        [
                            "name"  => "HIAS V3 Data Integrity Smart Contract",
                            "contract"  => $this->installConfs['hiasbchintegrity'],
                            "abi"  => $this->iabi,
                            "bin"  => $this->ibin,
                            "transaction"  => $this->installConfs['hiasbchintegrityt'],
                            "time"  => time()
                        ]
                    ],
                ],
                "authenticationUser" => [
                    "value" => $pubKey,
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Public key for accessing the network"
                        ]
                    ]
                ],
                "authenticationKey" => [
                    "value" => $this->hias->helpers->oEncrypt($privKeyHash),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Private key for accessing the network"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "authenticationBlockchainUser" => [
                    "value" => $this->installConfs['hiasbchuser'],
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" =>  "Blockchain address"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "authenticationBlockchainKey" => [
                    "value" => $this->hias->helpers->oEncrypt($this->installConfs['hiasbchpass']),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" =>  "Blockchain password"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "authenticationMqttUser" => [
                    "value" => $this->hias->helpers->oEncrypt($mqttUser),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" =>  "MQTT user"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "authenticationMqttKey" => [
                    "value" => $this->hias->helpers->oEncrypt($mqttPass),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" =>  "MQTT password"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "authenticationAmqpUser" => [
                    "value" => $this->hias->helpers->oEncrypt($amqppubKey),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" =>  "AMQP user"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "authenticationAmqpKey" => [
                    "value" => $this->hias->helpers->oEncrypt($amqpprvKey),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" =>  "AMQP password"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "authenticationCoapUser" => [
                    "value" => $this->hias->helpers->oEncrypt(""),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" =>  "CoAP user"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "authenticationCoapKey" => [
                    "value" => $this->hias->helpers->oEncrypt(""),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" =>  "CoAP password"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "authenticationIpinfoKey" => [
                    "value" => $this->hias->helpers->oEncrypt(filter_input(INPUT_POST, "ipinfo", FILTER_SANITIZE_STRING)),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" =>  "IPInfo API key"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "dateCreated" => [
                    "type" => "DateTime",
                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                ],
                "dateFirstUsed" => [
                    "type" => "DateTime",
                    "value" => ""
                ],
                "dateModified" => [
                    "type" => "DateTime",
                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                ]
            ];

            $insert = new \MongoDB\Driver\BulkWrite;
            $_id1 = $insert->insert($data);
            $result = $this->mngConn->executeBulkWrite($this->installConfs["mongodbname"].'.Entities', $insert);

            $pdoQuery = $this->hias->conn->prepare("
                UPDATE settings
                SET aid = :aid
            ");
            $pdoQuery->execute([
                ":aid"=>$pubKey
            ]);
            $pdoQuery->closeCursor();
            $pdoQuery = null;

            $query = $this->hias->conn->prepare("
                INSERT INTO  mqttu  (
                    `uname`,
                    `pw`
                )  VALUES (
                    :uname,
                    :pw
                )
            ");
            $query->execute([
                ':uname' => $mqttUser,
                ':pw' => $mqttHash
            ]);

            $query = $this->hias->conn->prepare("
                INSERT INTO  mqttua  (
                    `username`,
                    `topic`,
                    `rw`
                )  VALUES (
                    :username,
                    :topic,
                    :rw
                )
            ");
            $query->execute(array(
                ':username' => $mqttUser,
                ':topic' => $this->location . "/#",
                ':rw' => 4
            ));

            $query = $this->hias->conn->prepare("
                INSERT INTO  hiasbch  (
                    `entity`
                )  VALUES (
                    :entity
                )
            ");
            $query->execute([
                ':entity' => $this->hias->helpers->oEncrypt($pubKey)
            ]);

            $amid = $this->addAmqpUser($amqppubKey, $amqpKeyHash);
            $this->addAmqpUserVh($amid, "iotJumpWay");
            $this->addAmqpVhPerm($amid, "iotJumpWay", "exchange", "Core", "read");
            $this->addAmqpVhPerm($amid, "iotJumpWay", "exchange", "Core", "write");
            $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Life", "read");
            $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Life", "write");
            $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Statuses", "read");
            $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Statuses", "write");
            $this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "read", "Life");
            $this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "write", "Life");
            $this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "read", "Statuses");
            $this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "write", "Statuses");

            $htpasswd = new Htpasswd('/etc/nginx/security/htpasswd');
            $htpasswd->addUser($pubKey, $privKey, Htpasswd::ENCTYPE_APR_MD5);

            $web3 = $this->blockchainConnection("https://".$this->installConfs['domain'], $pubKey, $privKey);

            $unlocked =  $this->unlockBlockchainAccount($web3, $this->installConfs['hiasbchuser'], $this->installConfs['hiasbchpass']);

            if($unlocked == "FAILED"):
                return [
                    "Response"=> "Failed",
                    "Message" => "Unlocking HIAS Blockhain Account Failed!"
                ];
            endif;

            $contract = new Contract($web3->provider, $this->pabi);
            $icontract = new Contract($web3->provider, $this->iabi);

            $hash = "";
            $msg = "";
            $contract->at($this->installConfs['hiasbchpermissions'])->send("deposit", 9000000000000000000, ["from" => $this->installConfs['hiasbchuser'], "value" => 9000000000000000000], function ($err, $resp) use (&$hash, &$msg) {
                if ($err !== null) {
                    $hash = "FAILED";
                    $msg = $err . "<br />";
                    return;
                }
                $hash = $resp;
            });

            if($hash == "FAILED"):
                return ["Response"=>"FAILED","Message"=>"HIASBCH deposit 1 failed! " . $msg];
            else:
                $this->storeUserHistory("Deposit", $hash);

                $hash = "";
                $msg = "";

                $contract->at($this->installConfs['hiasbchpermissions'])->send("initiate", $pubKey, $this->installConfs['hiasbchuser'], true, "HIASBCH", $this->location, 1, time(), ["from" => $this->installConfs['hiasbchuser']], function ($err, $resp) use (&$hash, &$msg) {
                    if ($err !== null) {
                        $hash = "FAILED";
                        $msg = $err;
                        return;
                    }
                    $hash = $resp;
                });

                if($hash == "FAILED"):
                    return ["Response"=>"FAILED","Message"=>"HIASBCH permissions smart contract initiate failed! " . $msg];
                else:
                    $this->storeUserHistory("Initiate", $hash);
                    $balance = $this->getBlockchainBalance($web3, $this->installConfs['hiasbchuser']);
                    $msg .= "HIASBCH initiate complete! You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!<br />";

                    $contract->at($this->installConfs['hiasbchpermissions'])->send("deposit", 9000000000000000000, ["from" => $this->installConfs['hiasbchuser'], "value" => 9000000000000000000], function ($err, $resp) use (&$hash, &$msg) {
                        if ($err !== null) {
                            $hash = "FAILED";
                            $msg = $err . "<br />";
                            return;
                        }
                        $hash = $resp;
                    });

                    if($hash == "FAILED"):
                        return ["Response"=>"FAILED","Message"=>"HIASBCH deposit 2 failed! " . $msg];
                    else:
                        $this->storeUserHistory("Deposit", $hash);
                        sleep(25);
                        $contract->at($this->installConfs['hiasbchpermissions'])->send("registerComponent", $pubKey, $this->installConfs['hiasbchuser'], "HIASBCH", $this->location, $this->zone, "HIASBCH", $pubKey, time(), ["from" => $this->installConfs['hiasbchuser']], function ($err, $resp) use (&$hash, &$msg) {
                            if ($err !== null) {
                                $hash = "FAILED";
                                $msg = $err . "<br />";
                                return;
                            }
                            $hash = $resp;
                        });

                        if($hash == "FAILED"):
                            return ["Response"=>"FAILED","Message"=>"HIASBCH registerComponent failed! " . $msg];
                        else:
                            $this->storeUserHistory("Register Component", $hash);
                            $balance = $this->getBlockchainBalance($web3, $this->installConfs['hiasbchuser']);
                            $msg .= "HIASBCH registerComponent complete! You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!<br />";

                            $icontract->at($this->installConfs['hiasbchintegrity'])->send("deposit", 9000000000000000000, ["from" => $this->installConfs['hiasbchuser'], "value" => 9000000000000000000], function ($err, $resp) use (&$hash, &$msg) {
                                if ($err !== null) {
                                    $hash = "FAILED";
                                    $msg = $err . "<br />";
                                    return;
                                }
                                $hash = $resp;
                            });

                            if($hash == "FAILED"):
                                return ["Response"=>"FAILED","Message"=>"HIASBCH deposit 3 failed! " . $msg];
                            else:
                                $this->storeUserHistory("Deposit", $hash);

                                $hash = "";
                                $msg = "";
                                $icontract->at($this->installConfs['hiasbchintegrity'])->send("initiate", $this->installConfs['hiasbchuser'], ["from" => $this->installConfs['hiasbchuser']], function ($err, $resp) use (&$hash, &$msg) {
                                    if ($err !== null) {
                                        $hash = "FAILED";
                                        $msg = $err;
                                        return;
                                    }
                                    $hash = $resp;
                                });

                                if($hash == "FAILED"):
                                    return ["Response"=>"FAILED","Message"=>"HIASBCH data integrity smart contract initiate failed! " . $msg];
                                else:
                                    $this->storeUserHistory("Integrity Initiate", $hash);
                                    $balance = $this->getBlockchainBalance($web3, $this->installConfs['hiasbchuser']);
                                    $msg .= "HIASBCH data integrity smart contract initiate complete! You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!<br />";

                                    $contract->at($this->installConfs['hiasbchpermissions'])->send("deposit", 9000000000000000000, ["from" => $this->installConfs['hiasbchuser'], "value" => 9000000000000000000], function ($err, $resp) use (&$hash, &$msg) {
                                        if ($err !== null) {
                                            $hash = "FAILED";
                                            $msg = $err . "<br />";
                                            return;
                                        }
                                        $hash = $resp;
                                    });

                                    if($hash == "FAILED"):
                                        return ["Response"=>"FAILED","Message"=>"HIASBCH deposit 4 failed! " . $msg];
                                    else:
                                        $this->storeUserHistory("Deposit", $hash);
                                        sleep(25);
                                        $icontract->at($this->installConfs['hiasbchintegrity'])->send("registerAuthorized", $this->installConfs['hiasbchuser'], ["from" => $this->installConfs['hiasbchuser']], function ($err, $resp) use (&$hash, &$msg) {
                                            if ($err !== null) {
                                                $hash = "FAILED";
                                                $msg = $err . "<br />";
                                                return;
                                            }
                                            $hash = $resp;
                                        });

                                        if($hash == "FAILED"):
                                            $msg .= " HIASBCH registerAuthorized failed! " . $msg . "<br />";
                                        else:
                                            $this->storeUserHistory("Register Authorized", $hash);
                                            $balance = $this->getBlockchainBalance($web3, $this->installConfs['hiasbchuser']);
                                            $msg .= "HIASBCH register authorized complete! You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!<br />";
                                        endif;

                                    endif;
                                endif;

                            endif;

                        endif;
                    endif;

                endif;
            endif;

        }

        public function create_hiascdi(){

            $mqttUser = $this->hias->helpers->generate_uuid();
            $mqttPass = $this->hias->helpers->password();
            $mqttHash = create_hash($mqttPass);

            $pubKey = $this->hias->helpers->generate_uuid();
            $privKey = $this->hias->helpers->generate_key(32);
            $privKeyHash = $this->hias->helpers->password_hash($privKey);

            $amqppubKey = $this->hias->helpers->generate_uuid();
            $amqpprvKey = $this->hias->helpers->generate_key(32);
            $amqpKeyHash = $this->hias->helpers->password_hash($amqpprvKey);

            $this->hiascdi = $pubKey;
            $this->hiascdikey = $privKey;
            $this->hiascdiun = $mqttUser;
            $this->hiascdiup = $mqttPass;

            $bcPass = $this->hias->helpers->password();

            $htpasswd = new Htpasswd('/etc/nginx/security/htpasswd');
            $htpasswd->addUser($pubKey, $privKey, Htpasswd::ENCTYPE_APR_MD5);

            $web3 = $this->blockchainConnection("https://".$this->installConfs['domain'], $pubKey, $privKey);

            $unlocked =  $this->unlockBlockchainAccount($web3, $this->installConfs['hiasbchuser'], $this->installConfs['hiasbchpass']);

            if($unlocked == "FAILED"):
                return [
                    "Response"=> "Failed",
                    "Message" => "Unlocking HIAS Blockhain Account Failed!"
                ];
            endif;

            $contract = new Contract($web3->provider, $this->pabi);
            $icontract = new Contract($web3->provider, $this->iabi);

            $newBcUser = $this->create_user($web3, $bcPass);

            if($newBcUser == "FAILED"):
                return [
                    "Response"=> "Failed",
                    "Message" => "Creating New HIAS Blockhain Account Failed!"
                ];
            endif;

            $data = [
                "id" => $pubKey,
                "type" => "HIASCDI",
                "name" => [
                    "value" => "HIASCDI"
                ],
                "description" => [
                    "value" => "HIAS Contextual Data Interface"
                ],
                "batteryLevel" => [
                    "value" => 0.00
                ],
                "cpuUsage" => [
                    "value" => 0.00
                ],
                "memoryUsage" => [
                    "value" => 0.00
                ],
                "hddUsage" => [
                    "value" => 0.00
                ],
                "temperature" => [
                    "value" => 0.00
                ],
                "rssi" => [
                    "value" => 0.00
                ],
                "deviceBrandName" => [
                    "value" => "Generic"
                ],
                "deviceModel" => [
                    "value" => "Generic"
                ],
                "deviceManufacturer" => [
                    "value" => "Generic"
                ],
                "deviceSerialNumber" => [
                    "value" => "Generic"
                ],
                "os" => [
                    "value" => "Ubuntu"
                ],
                "osVersion" => [
                    "value" => "20.04.2"
                ],
                "osManufacturer" => [
                    "value" => "Canonical"
                ],
                "software" => [
                    "value" => "HIASCDI"
                ],
                "softwareVersion" => [
                    "value" => "1.0.0"
                ],
                "softwareManufacturer" => [
                    "value" => "Asociación de Investigacion en Inteligencia Artificial Para la Leucemia Peter Moss"
                ],
                "location" => [
                    "type" => "geo:json",
                    "value" => [
                        "type" => "Point",
                        "coordinates" => [floatval(filter_input(INPUT_POST, "lat", FILTER_SANITIZE_STRING)), floatval(filter_input(INPUT_POST, "lng", FILTER_SANITIZE_STRING))]
                    ]
                ],
                "networkStatus" => [
                    "value" => "OFFLINE",
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Network online status"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "networkLocation" => [
                    "value" => $this->location,
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Location entity ID"
                        ]
                    ]
                ],
                "networkZone" => [
                    "value" => $this->zone,
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Zone entity ID"
                        ]
                    ]
                ],
                "ipAddress" => [
                    "value" => $this->installConfs["ip"],
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "IP address of entity"
                        ]
                    ]
                ],
                "macAddress" => [
                    "value" => "",
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "MAC address of entity"
                        ]
                    ]
                ],
                "bluetoothAddress" => [
                    "value" => "",
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Bluetooth address of entity"
                        ]
                    ]
                ],
                "protocols" => [
                    "value" =>["mqtt","http"]
                ],
                "version" => [
                    "value" => "2.0.0"
                ],
                "host" => [
                    "value" => $this->installConfs['domain']
                ],
                "port" => [
                    "value" => 3524
                ],
                "endpoint" => [
                    "value" => "hiascdi/v1"
                ],
                "authenticationUser" => [
                    "value" => $pubKey,
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Public key for accessing the network"
                        ]
                    ]
                ],
                "authenticationKey" => [
                    "value" => $this->hias->helpers->oEncrypt($privKeyHash),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Private key for accessing the network"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "authenticationBlockchainUser" => [
                    "value" => $newBcUser,
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" =>  "Blockchain address"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "authenticationBlockchainKey" => [
                    "value" => $this->hias->helpers->oEncrypt($bcPass),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" =>  "Blockchain password"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "authenticationMqttUser" => [
                    "value" => $this->hias->helpers->oEncrypt($mqttUser),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" =>  "MQTT user"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "authenticationMqttKey" => [
                    "value" => $this->hias->helpers->oEncrypt($mqttPass),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" =>  "MQTT password"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "authenticationAmqpUser" => [
                    "value" => $this->hias->helpers->oEncrypt($amqppubKey),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" =>  "AMQP user"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "authenticationAmqpKey" => [
                    "value" => $this->hias->helpers->oEncrypt($amqpprvKey),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" =>  "AMQP password"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "authenticationCoapUser" => [
                    "value" => $this->hias->helpers->oEncrypt(""),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" =>  "CoAP user"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "authenticationCoapKey" => [
                    "value" => $this->hias->helpers->oEncrypt(""),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" =>  "CoAP password"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "authenticationIpinfoKey" => [
                    "value" => $this->hias->helpers->oEncrypt(filter_input(INPUT_POST, "ipinfo", FILTER_SANITIZE_STRING)),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" =>  "IPInfo API key"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "dateCreated" => [
                    "type" => "DateTime",
                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                ],
                "dateFirstUsed" => [
                    "type" => "DateTime",
                    "value" => ""
                ],
                "dateModified" => [
                    "type" => "DateTime",
                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                ]
            ];

            $insert = new \MongoDB\Driver\BulkWrite;
            $_id1 = $insert->insert($data);
            $result = $this->mngConn->executeBulkWrite($this->installConfs["mongodbname"].'.Entities', $insert);

            $query = $this->hias->conn->prepare("
                INSERT INTO  mqttu  (
                    `uname`,
                    `pw`
                )  VALUES (
                    :uname,
                    :pw
                )
            ");
            $query->execute([
                ':uname' => $mqttUser,
                ':pw' => $mqttHash
            ]);

            $query = $this->hias->conn->prepare("
                INSERT INTO  mqttua  (
                    `username`,
                    `topic`,
                    `rw`
                )  VALUES (
                    :username,
                    :topic,
                    :rw
                )
            ");
            $query->execute(array(
                ':username' => $mqttUser,
                ':topic' => $this->location . "/#",
                ':rw' => 4
            ));

            $pdoQuery = $this->hias->conn->prepare("
                UPDATE hiascdi
                SET entity = :entity
            ");
            $pdoQuery->execute([
                ":entity"=>$pubKey
            ]);
            $pdoQuery->closeCursor();
            $pdoQuery = null;

            $amid = $this->addAmqpUser($amqppubKey, $amqpKeyHash);
            $this->addAmqpUserVh($amid, "iotJumpWay");
            $this->addAmqpVhPerm($amid, "iotJumpWay", "exchange", "Core", "read");
            $this->addAmqpVhPerm($amid, "iotJumpWay", "exchange", "Core", "write");
            $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Life", "read");
            $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Life", "write");
            $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Statuses", "read");
            $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Statuses", "write");
            $this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "read", "Life");
            $this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "write", "Life");
            $this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "read", "Statuses");
            $this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "write", "Statuses");

            $hash = "";
            $msg = "";
            $contract->at($this->installConfs['hiasbchpermissions'])->send("deposit", 9000000000000000000, ["from" => $this->installConfs['hiasbchuser'], "value" => 9000000000000000000], function ($err, $resp) use (&$hash, &$msg) {
                if ($err !== null) {
                    $hash = "FAILED";
                    $msg = $err . "<br />";
                    return;
                }
                $hash = $resp;
            });

            if($hash == "FAILED"):
                return ["Response"=>"FAILED","Message"=>"HIASBCH deposit 1 failed! " . $msg];
            else:
                $this->storeUserHistory("Deposit", $hash);
                sleep(25);
                $contract->at($this->installConfs['hiasbchpermissions'])->send("registerComponent", $pubKey, $newBcUser, "HIASCDI", $this->location, $this->zone, "HIASCDI", $pubKey, time(), ["from" => $this->installConfs['hiasbchuser']], function ($err, $resp) use (&$hash, &$msg) {
                    if ($err !== null) {
                        $hash = "FAILED";
                        $msg = $err . "<br />";
                        return;
                    }
                    $hash = $resp;
                });

                if($hash == "FAILED"):
                    return ["Response"=>"FAILED","Message"=>"HIASBCH registerComponent failed! " . $msg];
                else:
                    $this->storeUserHistory("Register Component", $hash);
                    $balance = $this->getBlockchainBalance($web3, $this->installConfs['hiasbchuser']);
                    $msg .= "HIASBCH registerComponent complete! You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!<br />";

                    $icontract->at($this->installConfs['hiasbchintegrity'])->send("deposit", 9000000000000000000, ["from" => $this->installConfs['hiasbchuser'], "value" => 9000000000000000000], function ($err, $resp) use (&$hash, &$msg) {
                        if ($err !== null) {
                            $hash = "FAILED";
                            $msg = $err . "<br />";
                            return;
                        }
                        $hash = $resp;
                    });

                    if($hash == "FAILED"):
                        return ["Response"=>"FAILED","Message"=>"HIASBCH deposit 2 failed! " . $msg];
                    else:
                        $this->storeUserHistory("Deposit", $hash);
                        sleep(25);
                        $icontract->at($this->installConfs['hiasbchintegrity'])->send("registerAuthorized", $newBcUser, ["from" => $this->installConfs['hiasbchuser']], function ($err, $resp) use (&$hash, &$msg) {
                            if ($err !== null) {
                                $hash = "FAILED";
                                $msg = $err . "<br />";
                                return;
                            }
                            $hash = $resp;
                        });

                        if($hash == "FAILED"):
                            return ["Response"=>"FAILED","Message"=>"HIASBCH registerAuthorized failed! " . $msg];
                        else:
                            $this->storeUserHistory("Register Authorized", $hash);
                            $balance = $this->getBlockchainBalance($web3, $this->installConfs['hiasbchuser']);
                            $msg .= "HIASBCH register authorized complete! You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!<br />";
                        endif;

                    endif;
                endif;

            endif;

        }

        public function create_hiashdi(){

            $mqttUser = $this->hias->helpers->generate_uuid();
            $mqttPass = $this->hias->helpers->password();
            $mqttHash = create_hash($mqttPass);

            $pubKey = $this->hias->helpers->generate_uuid();
            $privKey = $this->hias->helpers->generate_key(32);
            $privKeyHash = $this->hias->helpers->password_hash($privKey);

            $amqppubKey = $this->hias->helpers->generate_uuid();
            $amqpprvKey = $this->hias->helpers->generate_key(32);
            $amqpKeyHash = $this->hias->helpers->password_hash($amqpprvKey);

            $this->hiashdi = $pubKey;
            $this->hiashdikey = $privKey;
            $this->hiashdiun = $mqttUser;
            $this->hiashdiup = $mqttPass;

            $bcPass = $this->hias->helpers->password();

            $htpasswd = new Htpasswd('/etc/nginx/security/htpasswd');
            $htpasswd->addUser($pubKey, $privKey, Htpasswd::ENCTYPE_APR_MD5);

            $web3 = $this->blockchainConnection("https://".$this->installConfs['domain'], $pubKey, $privKey);

            $unlocked =  $this->unlockBlockchainAccount($web3, $this->installConfs['hiasbchuser'], $this->installConfs['hiasbchpass']);

            if($unlocked == "FAILED"):
                return [
                    "Response"=> "Failed",
                    "Message" => "Unlocking HIAS Blockhain Account Failed!"
                ];
            endif;

            $contract = new Contract($web3->provider, $this->pabi);
            $icontract = new Contract($web3->provider, $this->iabi);

            $newBcUser = $this->create_user($web3, $bcPass);

            if($newBcUser == "FAILED"):
                return [
                    "Response"=> "Failed",
                    "Message" => "Creating New HIAS Blockhain Account Failed!"
                ];
            endif;

            $data = [
                "id" => $pubKey,
                "type" => "HIASHDI",
                "name" => [
                    "value" => "HIASHDI"
                ],
                "description" => [
                    "value" => "HIAS Historical Data Interface"
                ],
                "batteryLevel" => [
                    "value" => 0.00
                ],
                "cpuUsage" => [
                    "value" => 0.00
                ],
                "memoryUsage" => [
                    "value" => 0.00
                ],
                "hddUsage" => [
                    "value" => 0.00
                ],
                "temperature" => [
                    "value" => 0.00
                ],
                "rssi" => [
                    "value" => 0.00
                ],
                "deviceBrandName" => [
                    "value" => "Generic"
                ],
                "deviceModel" => [
                    "value" => "Generic"
                ],
                "deviceManufacturer" => [
                    "value" => "Generic"
                ],
                "deviceSerialNumber" => [
                    "value" => "Generic"
                ],
                "os" => [
                    "value" => "Ubuntu"
                ],
                "osVersion" => [
                    "value" => "20.04.2"
                ],
                "osManufacturer" => [
                    "value" => "Canonical"
                ],
                "software" => [
                    "value" => "HIASCDI"
                ],
                "softwareVersion" => [
                    "value" => "1.0.0"
                ],
                "softwareManufacturer" => [
                    "value" => "Asociación de Investigacion en Inteligencia Artificial Para la Leucemia Peter Moss"
                ],
                "location" => [
                    "type" => "geo:json",
                    "value" => [
                        "type" => "Point",
                        "coordinates" => [floatval(filter_input(INPUT_POST, "lat", FILTER_SANITIZE_STRING)), floatval(filter_input(INPUT_POST, "lng", FILTER_SANITIZE_STRING))]
                    ]
                ],
                "networkStatus" => [
                    "value" => "OFFLINE",
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Network online status"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "networkLocation" => [
                    "value" => $this->location,
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Location entity ID"
                        ]
                    ]
                ],
                "networkZone" => [
                    "value" => $this->zone,
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Zone entity ID"
                        ]
                    ]
                ],
                "ipAddress" => [
                    "value" => $this->installConfs["ip"],
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "IP address of entity"
                        ]
                    ]
                ],
                "macAddress" => [
                    "value" => "",
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "MAC address of entity"
                        ]
                    ]
                ],
                "bluetoothAddress" => [
                    "value" => "",
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Bluetooth address of entity"
                        ]
                    ]
                ],
                "protocols" => [
                    "value" =>["mqtt","http"]
                ],
                "version" => [
                    "value" => "2.0.0"
                ],
                "host" => [
                    "value" => $this->installConfs['domain']
                ],
                "port" => [
                    "value" => 3525
                ],
                "endpoint" => [
                    "value" => "hiaschi/v1"
                ],
                "authenticationUser" => [
                    "value" => $pubKey,
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Public key for accessing the network"
                        ]
                    ]
                ],
                "authenticationKey" => [
                    "value" => $this->hias->helpers->oEncrypt($privKeyHash),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Private key for accessing the network"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "authenticationBlockchainUser" => [
                    "value" => $newBcUser,
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" =>  "Blockchain address"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "authenticationBlockchainKey" => [
                    "value" => $this->hias->helpers->oEncrypt($bcPass),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" =>  "Blockchain password"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "authenticationMqttUser" => [
                    "value" => $this->hias->helpers->oEncrypt($mqttUser),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" =>  "MQTT user"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "authenticationMqttKey" => [
                    "value" => $this->hias->helpers->oEncrypt($mqttPass),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" =>  "MQTT password"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "authenticationAmqpUser" => [
                    "value" => $this->hias->helpers->oEncrypt($amqppubKey),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" =>  "AMQP user"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "authenticationAmqpKey" => [
                    "value" => $this->hias->helpers->oEncrypt($amqpprvKey),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" =>  "AMQP password"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "authenticationCoapUser" => [
                    "value" => $this->hias->helpers->oEncrypt(""),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" =>  "CoAP user"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "authenticationCoapKey" => [
                    "value" => $this->hias->helpers->oEncrypt(""),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" =>  "CoAP password"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "authenticationIpinfoKey" => [
                    "value" => $this->hias->helpers->oEncrypt(filter_input(INPUT_POST, "ipinfo", FILTER_SANITIZE_STRING)),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" =>  "IPInfo API key"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "dateCreated" => [
                    "type" => "DateTime",
                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                ],
                "dateFirstUsed" => [
                    "type" => "DateTime",
                    "value" => ""
                ],
                "dateModified" => [
                    "type" => "DateTime",
                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                ]
            ];

            $insert = new \MongoDB\Driver\BulkWrite;
            $_id1 = $insert->insert($data);
            $result = $this->mngConn->executeBulkWrite($this->installConfs["mongodbname"].'.Entities', $insert);

            $query = $this->hias->conn->prepare("
                INSERT INTO  mqttu  (
                    `uname`,
                    `pw`
                )  VALUES (
                    :uname,
                    :pw
                )
            ");
            $query->execute([
                ':uname' => $mqttUser,
                ':pw' => $mqttHash
            ]);

            $query = $this->hias->conn->prepare("
                INSERT INTO  mqttua  (
                    `username`,
                    `topic`,
                    `rw`
                )  VALUES (
                    :username,
                    :topic,
                    :rw
                )
            ");
            $query->execute(array(
                ':username' => $mqttUser,
                ':topic' => $this->location . "/#",
                ':rw' => 4
            ));

            $pdoQuery = $this->hias->conn->prepare("
                UPDATE hiashdi
                SET entity = :entity
            ");
            $pdoQuery->execute([
                ":entity"=>$pubKey
            ]);
            $pdoQuery->closeCursor();
            $pdoQuery = null;

            $amid = $this->addAmqpUser($amqppubKey, $amqpKeyHash);
            $this->addAmqpUserVh($amid, "iotJumpWay");
            $this->addAmqpVhPerm($amid, "iotJumpWay", "exchange", "Core", "read");
            $this->addAmqpVhPerm($amid, "iotJumpWay", "exchange", "Core", "write");
            $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Life", "read");
            $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Life", "write");
            $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Statuses", "read");
            $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Statuses", "write");
            $this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "read", "Life");
            $this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "write", "Life");
            $this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "read", "Statuses");
            $this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "write", "Statuses");

            $hash = "";
            $msg = "";
            $contract->at($this->installConfs['hiasbchpermissions'])->send("deposit", 9000000000000000000, ["from" => $this->installConfs['hiasbchuser'], "value" => 9000000000000000000], function ($err, $resp) use (&$hash, &$msg) {
                if ($err !== null) {
                    $hash = "FAILED";
                    $msg = $err . "<br />";
                    return;
                }
                $hash = $resp;
            });

            if($hash == "FAILED"):
                return ["Response"=>"FAILED","Message"=>"HIASBCH deposit 1 failed! " . $msg];
            else:
                $this->storeUserHistory("Deposit", $hash);
                sleep(25);
                $contract->at($this->installConfs['hiasbchpermissions'])->send("registerComponent", $pubKey, $newBcUser, "HIASHDI", $this->location, $this->zone, "HIASHDI", $pubKey, time(), ["from" => $this->installConfs['hiasbchuser']], function ($err, $resp) use (&$hash, &$msg) {
                    if ($err !== null) {
                        $hash = "FAILED";
                        $msg = $err . "<br />";
                        return;
                    }
                    $hash = $resp;
                });

                if($hash == "FAILED"):
                    return ["Response"=>"FAILED","Message"=>"HIASBCH registerComponent failed! " . $msg];
                else:
                    $this->storeUserHistory("Register Component", $hash);
                    $balance = $this->getBlockchainBalance($web3, $this->installConfs['hiasbchuser']);
                    $msg .= "HIASBCH registerComponent complete! You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!<br />";

                    $icontract->at($this->installConfs['hiasbchintegrity'])->send("deposit", 9000000000000000000, ["from" => $this->installConfs['hiasbchuser'], "value" => 9000000000000000000], function ($err, $resp) use (&$hash, &$msg) {
                        if ($err !== null) {
                            $hash = "FAILED";
                            $msg = $err . "<br />";
                            return;
                        }
                        $hash = $resp;
                    });

                    if($hash == "FAILED"):
                        return ["Response"=>"FAILED","Message"=>"HIASBCH deposit 2 failed! " . $msg];
                    else:
                        $this->storeUserHistory("Deposit", $hash);
                        sleep(25);
                        $icontract->at($this->installConfs['hiasbchintegrity'])->send("registerAuthorized", $newBcUser, ["from" => $this->installConfs['hiasbchuser']], function ($err, $resp) use (&$hash, &$msg) {
                            if ($err !== null) {
                                $hash = "FAILED";
                                $msg = $err . "<br />";
                                return;
                            }
                            $hash = $resp;
                        });

                        if($hash == "FAILED"):
                            return ["Response"=>"FAILED","Message"=>"HIASBCH registerAuthorized failed! " . $msg];
                        else:
                            $this->storeUserHistory("Register Authorized", $hash);
                            $balance = $this->getBlockchainBalance($web3, $this->installConfs['hiasbchuser']);
                            $msg .= "HIASBCH register authorized complete! You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!<br />";
                        endif;

                    endif;
                endif;

            endif;

        }

        public function create_staff(){

            $mqttUser = $this->hias->helpers->generate_uuid();
            $mqttPass = $this->hias->helpers->password();
            $mqttHash = create_hash($mqttPass);

            $pubKey = $this->hias->helpers->generate_uuid();
            $privKey = $this->hias->helpers->generate_key(32);
            $privKeyHash = $this->hias->helpers->password_hash($privKey);

            $amqppubKey = $this->hias->helpers->generate_uuid();
            $amqpprvKey = $this->hias->helpers->generate_key(32);
            $amqpKeyHash = $this->hias->helpers->password_hash($amqpprvKey);

            $bcPass = $this->hias->helpers->password();

            $htpasswd = new Htpasswd('/etc/nginx/security/htpasswd');
            $htpasswd->addUser(filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING), $privKey, Htpasswd::ENCTYPE_APR_MD5);

            $web3 = $this->blockchainConnection("https://".$this->installConfs['domain'], filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING), $privKey);

            $unlocked =  $this->unlockBlockchainAccount($web3, $this->installConfs['hiasbchuser'], $this->installConfs['hiasbchpass']);

            if($unlocked == "FAILED"):
                return [
                    "Response"=> "Failed",
                    "Message" => "Unlocking HIAS Blockhain Account Failed!"
                ];
            endif;

            $contract = new Contract($web3->provider, $this->pabi);
            $icontract = new Contract($web3->provider, $this->iabi);

            $newBcUser = $this->create_user($web3, $bcPass);

            if($newBcUser == "FAILED"):
                return [
                    "Response"=> "Failed",
                    "Message" => "Creating New HIAS Blockhain Account Failed!"
                ];
            endif;

            $ds = $this->hias->connect_to_ldap();
            if($ds):

                $binddn = "cn=admin,".$this->hias->ldapdc;

                if (!ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3)):
                    return  [
                        "Response"=>"FAILED",
                        "ResponseMessage"=>"Could load LDAP version 3!"
                    ];
                endif;
                if (!ldap_set_option($ds, LDAP_OPT_REFERRALS, 0)):
                    return  [
                        "Response"=>"FAILED",
                        "ResponseMessage"=>"Could load LDAP referrals!"
                    ];
                endif;
                if (!ldap_start_tls($ds)):
                    return  [
                        "Response"=>"FAILED",
                        "ResponseMessage"=>"Could not connect to LDAP server securely!"
                    ];
                endif;

                if (ldap_bind($ds, $binddn, $this->hias->_ldaps)):

                    $info=[];
                    $info['ou'] = "users";
                    $info['objectclass'] = "organizationalUnit";

                    $r = ldap_add($ds,"ou=users,".$this->hias->ldapdc, $info);

                    $info=[];
                    $info["cn"] = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);
                    $info["uid"] = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);
                    $info["givenName"] = filter_input(INPUT_POST, "first_name", FILTER_SANITIZE_STRING);
                    $info["surname"] = filter_input(INPUT_POST, "second_name", FILTER_SANITIZE_STRING);
                    $info['objectclass'][0] = "inetOrgPerson";
                    $info['objectclass'][1] = "posixAccount";
                    $info['objectclass'][2] = "top";
                    $info['gidnumber'] = 1;
                    $info['uidnumber'] = 1;
                    $info["userPassword"] = $this->hias->ldap_ssha_password($privKey);
                    $info["homeDirectory"] = "/hias/ldap/".filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);

                    $r = ldap_add($ds,"cn=".$info["cn"].",ou=users,".$this->hias->ldapdc, $info);
                    $sr = ldap_search($ds,$this->hias->ldapdc,"cn=".$info["cn"]);
                    $info = ldap_get_entries($ds,$sr);

                    $admin = True;
                    $name = filter_input(INPUT_POST, "first_name", FILTER_SANITIZE_STRING) . " " . filter_input(INPUT_POST, "second_name", FILTER_SANITIZE_STRING);

                    $data = [
                        "id" => $pubKey,
                        "type" => "Staff",
                        "category" => [
                            "value" => "Management"
                        ],
                        "name" => [
                            "value" => $name
                        ],
                        "description" => [
                            "value" => $name . " staff account"
                        ],
                        "address" => [
                            "type" => "PostalAddress",
                            "value" => [
                                "addressLocality" => "",
                                "postalCode" => "",
                                "streetAddress" => ""
                            ]
                        ],
                        "username" => [
                            "value" => filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING)
                        ],
                        "email" => [
                            "value" => filter_input(INPUT_POST, "your_email", FILTER_SANITIZE_STRING)
                        ],
                        "picture" => [
                            "value" => "default.png"
                        ],
                        "location" => [
                            "type" => "geo:json",
                            "value" => [
                                "type" => "Point",
                                "coordinates" => [0, 0]
                            ]
                        ],
                        "networkStatus" => [
                            "value" => "OFFLINE",
                            "type" => "Text",
                            "metadata" => [
                                "description" => [
                                    "value" => "Network online status"
                                ],
                                "timestamp" =>   [
                                    "type" => "DateTime",
                                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                                ]
                            ]
                        ],
                        "networkLocation" => [
                            "value" => $this->location,
                            "type" => "Text",
                            "metadata" => [
                                "description" => [
                                    "value" => "Location entity ID"
                                ]
                            ]
                        ],
                        "ipAddress" => [
                            "value" => "",
                            "type" => "Text",
                            "metadata" => [
                                "description" => [
                                    "value" => "IP address of user"
                                ],
                                "timestamp" =>   [
                                    "type" => "DateTime",
                                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                                ]
                            ]
                        ],
                        "macAddress" => [
                            "value" => "",
                            "type" => "Text",
                            "metadata" => [
                                "description" => [
                                    "value" => "MAC address of user"
                                ],
                                "timestamp" =>   [
                                    "type" => "DateTime",
                                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                                ]
                            ]
                        ],
                        "bluetoothAddress" => [
                            "value" => "",
                            "type" => "Text",
                            "metadata" => [
                                "description" => [
                                    "value" => "Bluetooth address of user"
                                ],
                                "timestamp" =>   [
                                    "type" => "DateTime",
                                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                                ]
                            ]
                        ],
                        "nfcAddress" => [
                            "value" => "",
                            "type" => "Text",
                            "metadata" => [
                                "description" => [
                                    "value" => "NFC address of user"
                                ],
                                "timestamp" =>   [
                                    "type" => "DateTime",
                                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                                ]
                            ]
                        ],
                        "permissionsAdmin" => [
                            "value" => $admin,
                            "type" => "Text",
                            "metadata" => [
                                "description" => [
                                    "value" => "Has admin permissions"
                                ],
                                "timestamp" =>   [
                                    "type" => "DateTime",
                                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                                ]
                            ]
                        ],
                        "permissionsCancelled" => [
                            "value" => False,
                            "type" => "Text",
                            "metadata" => [
                                "description" => [
                                    "value" => "Is cancelled"
                                ],
                                "timestamp" =>   [
                                    "type" => "DateTime",
                                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                                ]
                            ]
                        ],
                        "authenticationUser" => [
                            "value" => $pubKey,
                            "type" => "Text",
                            "metadata" => [
                                "description" => [
                                    "value" => "Public key for accessing the network APIs"
                                ]
                            ]
                        ],
                        "authenticationKey" => [
                            "value" => $this->hias->helpers->oEncrypt($privKeyHash),
                            "type" => "Text",
                            "metadata" => [
                                "description" => [
                                    "value" => "Private key for accessing the network APIs"
                                ],
                                "timestamp" =>   [
                                    "type" => "DateTime",
                                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                                ]
                            ]
                        ],
                        "authenticationBlockchainUser" => [
                            "value" => $newBcUser,
                            "type" => "Text",
                            "metadata" => [
                                "description" => [
                                    "value" =>  "Blockchain address"
                                ],
                                "timestamp" =>   [
                                    "type" => "DateTime",
                                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                                ]
                            ]
                        ],
                        "authenticationBlockchainKey" => [
                            "value" => $this->hias->helpers->oEncrypt($bcPass),
                            "type" => "Text",
                            "metadata" => [
                                "description" => [
                                    "value" =>  "Blockchain password"
                                ],
                                "timestamp" =>   [
                                    "type" => "DateTime",
                                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                                ]
                            ]
                        ],
                        "authenticationMqttUser" => [
                            "value" => $this->hias->helpers->oEncrypt($mqttUser),
                            "type" => "Text",
                            "metadata" => [
                                "description" => [
                                    "value" =>  "MQTT user"
                                ],
                                "timestamp" =>   [
                                    "type" => "DateTime",
                                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                                ]
                            ]
                        ],
                        "authenticationMqttKey" => [
                            "value" => $this->hias->helpers->oEncrypt($mqttPass),
                            "type" => "Text",
                            "metadata" => [
                                "description" => [
                                    "value" =>  "MQTT password"
                                ],
                                "timestamp" =>   [
                                    "type" => "DateTime",
                                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                                ]
                            ]
                        ],
                        "authenticationAmqpUser" => [
                            "value" => $this->hias->helpers->oEncrypt($amqppubKey),
                            "type" => "Text",
                            "metadata" => [
                                "description" => [
                                    "value" =>  "AMQP user"
                                ],
                                "timestamp" =>   [
                                    "type" => "DateTime",
                                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                                ]
                            ]
                        ],
                        "authenticationAmqpKey" => [
                            "value" => $this->hias->helpers->oEncrypt($amqpprvKey),
                            "type" => "Text",
                            "metadata" => [
                                "description" => [
                                    "value" =>  "AMQP password"
                                ],
                                "timestamp" =>   [
                                    "type" => "DateTime",
                                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                                ]
                            ]
                        ],
                        "authenticationCoapUser" => [
                            "value" => $this->hias->helpers->oEncrypt(""),
                            "type" => "Text",
                            "metadata" => [
                                "description" => [
                                    "value" =>  "CoAP user"
                                ],
                                "timestamp" =>   [
                                    "type" => "DateTime",
                                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                                ]
                            ]
                        ],
                        "authenticationCoapKey" => [
                            "value" => $this->hias->helpers->oEncrypt(""),
                            "type" => "Text",
                            "metadata" => [
                                "description" => [
                                    "value" =>  "CoAP password"
                                ],
                                "timestamp" =>   [
                                    "type" => "DateTime",
                                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                                ]
                            ]
                        ],
                        "authenticationIpinfoKey" => [
                            "value" => $this->hias->helpers->oEncrypt(filter_input(INPUT_POST, "ipinfo", FILTER_SANITIZE_STRING)),
                            "type" => "Text",
                            "metadata" => [
                                "description" => [
                                    "value" =>  "IPInfo API key"
                                ],
                                "timestamp" =>   [
                                    "type" => "DateTime",
                                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                                ]
                            ]
                        ],
                        "dateCreated" => [
                            "type" => "DateTime",
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ],
                        "dateFirstUsed" => [
                            "type" => "DateTime",
                            "value" => ""
                        ],
                        "dateModified" => [
                            "type" => "DateTime",
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ];

                    $insert = new \MongoDB\Driver\BulkWrite;
                    $_id1 = $insert->insert($data);
                    $result = $this->mngConn->executeBulkWrite($this->installConfs["mongodbname"].'.Entities', $insert);

                    $query = $this->hias->conn->prepare("
                        INSERT INTO  mqttu  (
                            `uname`,
                            `pw`
                        )  VALUES (
                            :uname,
                            :pw
                        )
                    ");
                    $query->execute([
                        ':uname' => $mqttUser,
                        ':pw' => $mqttHash
                    ]);

                    $query = $this->hias->conn->prepare("
                        INSERT INTO  mqttua  (
                            `username`,
                            `topic`,
                            `rw`
                        )  VALUES (
                            :username,
                            :topic,
                            :rw
                        )
                    ");
                    $query->execute(array(
                        ':username' => $mqttUser,
                        ':topic' => $this->location . "/#",
                        ':rw' => 4
                    ));

                    $amid = $this->addAmqpUser($amqppubKey, $amqpKeyHash);
                    $this->addAmqpUserVh($amid, "iotJumpWay");
                    $this->addAmqpVhPerm($amid, "iotJumpWay", "exchange", "Core", "read");
                    $this->addAmqpVhPerm($amid, "iotJumpWay", "exchange", "Core", "write");
                    $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Life", "read");
                    $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Life", "write");
                    $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Statuses", "read");
                    $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Statuses", "write");
                    $this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "read", "Life");
                    $this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "write", "Life");
                    $this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "read", "Statuses");
                    $this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "write", "Statuses");
                    $this->addAmqpUserPerm($amid, "administrator");
                    $this->addAmqpUserPerm($amid, "managment");
                    $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Life", "configure");
                    $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Statuses", "configure");

                    $hash = "";
                    $msg = "";
                    $contract->at($this->installConfs['hiasbchpermissions'])->send("deposit", 9000000000000000000, ["from" => $this->installConfs['hiasbchuser'], "value" => 9000000000000000000], function ($err, $resp) use (&$hash, &$msg) {
                        if ($err !== null) {
                            $hash = "FAILED";
                            $msg = $err . "<br />";
                            return;
                        }
                        $hash = $resp;
                    });

                    if($hash == "FAILED"):
                        return ["Response"=>"FAILED","Message"=>"HIASBCH deposit 1 failed! " . $msg];
                    else:
                        $this->storeUserHistory("Deposit", $hash);
                        sleep(25);
                        $contract->at($this->installConfs['hiasbchpermissions'])->send("registerUser", $pubKey, $newBcUser, $admin, $name, $this->location, time(), 1, ["from" => $this->installConfs['hiasbchuser']], function ($err, $resp) use (&$hash, &$msg) {
                            if ($err !== null) {
                                $hash = "FAILED";
                                $msg = $err . "<br />";
                                return;
                            }
                            $hash = $resp;
                        });

                        if($hash == "FAILED"):
                            return ["Response"=>"FAILED","Message"=>"HIASBCH registerComponent failed! " . $msg];
                        else:
                            $this->storeUserHistory("Register Component", $hash);
                            $balance = $this->getBlockchainBalance($web3, $this->installConfs['hiasbchuser']);
                            $msg .= "HIASBCH registerComponent complete! You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!<br />";

                            $icontract->at($this->installConfs['hiasbchintegrity'])->send("deposit", 9000000000000000000, ["from" => $this->installConfs['hiasbchuser'], "value" => 9000000000000000000], function ($err, $resp) use (&$hash, &$msg) {
                                if ($err !== null) {
                                    $hash = "FAILED";
                                    $msg = $err . "<br />";
                                    return;
                                }
                                $hash = $resp;
                            });

                            if($hash == "FAILED"):
                                return ["Response"=>"FAILED","Message"=>"HIASBCH deposit 2 failed! " . $msg];
                            else:
                                $this->storeUserHistory("Deposit", $hash);
                                sleep(25);
                                $icontract->at($this->installConfs['hiasbchintegrity'])->send("registerAuthorized", $newBcUser, ["from" => $this->installConfs['hiasbchuser']], function ($err, $resp) use (&$hash, &$msg) {
                                    if ($err !== null) {
                                        $hash = "FAILED";
                                        $msg = $err . "<br />";
                                        return;
                                    }
                                    $hash = $resp;
                                });

                                if($hash == "FAILED"):
                                    return ["Response"=>"FAILED","Message"=>"HIASBCH registerAuthorized failed! " . $msg];
                                else:
                                    $this->storeUserHistory("Register Authorized", $hash);
                                    $balance = $this->getBlockchainBalance($web3, $this->installConfs['hiasbchuser']);
                                    $msg .= "HIASBCH register authorized complete! You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!<br />";
                                endif;

                            endif;

                        endif;

                    endif;

                else:
                    return [
                        "Response"=> "Failed",
                        "Message" => "Connecting To LDAP Failed!"
                    ];
                endif;
            else:
                return [
                    "Response"=> "Failed",
                    "Message" => "Connecting To LDAP Failed!"
                ];
            endif;

            $pdoQuery = $this->hias->conn->prepare("
                UPDATE settings
                SET installed = :installed
            ");
            $pdoQuery->execute([
                ":installed"=>1
            ]);
            $pdoQuery->closeCursor();
            $pdoQuery = null;

            return [
                "Response"=> "OK",
                "Message" => "Your HIAS Core installation is complete! Please note the following credentials:<br /><br />HIAS Location: " . $this->location . "<br />HIAS Zone: " . $this->zone . "<br /><br />HIASBCH Public Key: " . $this->hiasbch . "<br />HIASBCH Private Key: " . $this->hiasbchkey . "<br />HIASBCH User: " . $this->hiasbchun . "<br />HIASBCH Password: " . $this->hiasbchup . "<br /><br />HIASCDI Public Key: " . $this->hiascdi . "<br />HIASCDI Private Key: " . $this->hiascdikey . "<br />HIASCDI User: " . $this->hiascdiun . "<br />HIASCDI Password: " . $this->hiascdiup . "<br /><br />HIASHDI Public Key: " . $this->hiashdi . "<br />HIASHDI Private Key: " . $this->hiashdikey . "<br />HIASHDI User: " . $this->hiashdiun . "<br />HIASHDI Password: " . $this->hiashdiup . "<br /><br />Your Personal Password: " . $privKey . "<br /><br />Please return to the installation guide to create the server services and set up HIASBCH, HIASCDI & HIASHDI."
            ];

        }

        public function complete_install(){

            $this->server_settings();
            $this->create_location();
            $this->create_zone();

            $hiasbch = $this->finalize_hiasbch();
            if(isSet($hiasbch["Response"])):
                return $hiasbch;
            endif;

            $hiascdi = $this->create_hiascdi();
            if(isSet($hiascdi["Response"])):
                return $hiascdi;
            endif;

            $hiashdi = $this->create_hiashdi();
            if(isSet($hiashdi["Response"])):
                return $hiashdi;
            endif;

            return $this->create_staff();

        }

    }

$Install = new Install($HIAS);

if(filter_input(INPUT_POST, "complete_installation", FILTER_SANITIZE_STRING)):
    die(json_encode($Install->complete_install()));
endif;