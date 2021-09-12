<?php

require __DIR__ . '/../../../vendor/autoload.php';

use Web3\Web3;
use Web3\Contract;
use Web3\Utils;

    class HIASBCH
    {
        function __construct($hias)
        {
            $this->un = "";
            $this->up = "";

            $this->hias = $hias;
            $this->confs = $this->get_config();

            if(isSet($_SESSION["HIAS"]["Active"])):
                $this->set_config_contracts();
                $this->web3 = $this->connection();
                $this->contract = new Contract($this->web3->provider, $this->confs["abi"]);
                $this->icontract = new Contract($this->web3->provider, $this->confs["iabi"]);
                $this->permissions();
            endif;
        }

        public function get_config()
        {
            $pdoQuery = $this->hias->conn->prepare("
                SELECT hiasbch.entity
                FROM hiasbch hiasbch
            ");
            $pdoQuery->execute();
            $response=$pdoQuery->fetch(PDO::FETCH_ASSOC);
            $pdoQuery->closeCursor();
            $pdoQuery = null;

            return $response;
        }

        public function set_config_contracts()
        {
            $entity = $this->get_hiasbch();
            $contracts = $entity["contracts"]["value"];

            $authContract = "";
            $authContractAbi = "";
            $integrityContract = "";
            $integrityContractAbi = "";

            foreach($contracts as $key => $value):
                if($value["contract"]==$entity["authenticationContract"]["value"]):
                    $authContract = $value["contract"];
                    $authContractAbi = $value["abi"];
                endif;
                if($value["contract"]==$entity["dataIntegrityContract"]["value"]):
                    $integrityContract = $value["contract"];
                    $integrityContractAbi = $value["abi"];
                endif;
            endforeach;

            $this->confs["contract"] = $authContract;
            $this->confs["abi"] = $authContractAbi;
            $this->confs["icontract"] = $integrityContract;
            $this->confs["iabi"] = $integrityContractAbi;

        }

        public function update_config()
        {
            if(!filter_input(INPUT_POST, "dc", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "You must select the HIAS Smart Contract"
                ];
            endif;

            if(!filter_input(INPUT_POST, "ic", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "You must select the iotJumpWay Smart Contract"
                ];
            endif;

            $data = [
                "authenticationContract" => [
                    "value" => filter_input(INPUT_POST, "dc", FILTER_SANITIZE_STRING)
                ],
                "dataIntegrityContract" => [
                    "value" => filter_input(INPUT_POST, "ic", FILTER_SANITIZE_STRING)
                ]
            ];

            $response = $this->hias->hiascdi->request("PATCH", $this->hias->hiascdi->confs["entities_url"] . "/" . $this->hias->helpers->oDecrypt($this->confs["entity"]) . "/attrs?type=HIASBCH", json_encode($data));
            if($response["code"] == 204):

                $this->hias->store_user_history("HIASBCH Entity configuration update");

                return [
                    "Response"=> "OK",
                    "Message" => "HIASBCH configuration update OK!",
                    "Schema" => $this->get_hiasbch("dateCreated,dateModified,*")
                ];
            else:
                return [
                    "Response"=> "FAILED",
                    "Message" => "HIASBCH configuration update KO!"
                ];
            endif;
        }

        public function get_hiasbch()
        {
            $request = $this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "/" . $this->hias->helpers->oDecrypt($this->confs["entity"]) . "?type=HIASBCH&attrs=dateCreated,dateModified,*", []);
            $hiasbch = json_decode($request["body"], true);
            return $hiasbch;
        }

        private function connection()
        {
            $web3 = new Web3($this->hias->helpers->oDecrypt($this->hias->confs["domainString"]) . "/hiasbch/api/", 30, $_SESSION["HIAS"]["User"], $this->hias->helpers->oDecrypt($_SESSION["HIAS"]["Pass"]));
            return $web3;
        }

        private function permissions()
        {
            $allowed = "";
            $this->contract->at($this->confs["contract"])->call("addressAuthorized", $_SESSION["HIAS"]["BC"]["BCUser"], ["from" => $_SESSION["HIAS"]["BC"]["BCUser"]], function ($err, $resp) use (&$allowed) {
                if ($err !== null) {
                    $allowed = "FAILED";
                    return;
                }
                $allowed = $resp[0];
            });

            if($allowed != "true"):
                header('Location: /Logout');
            endif;
        }

        public function unlock_account($account, $pass)
        {
            $response = "";
            $personal = $this->web3->personal;
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

        public function create_user($pass)
        {
            $newAccount = "";
            $personal = $this->web3->personal;
            $personal->newAccount($pass, function ($err, $account) use (&$newAccount) {
                if ($err !== null) {
                    $newAccount = "FAILED!";
                    return;
                }
                $newAccount = $account;
            });

            return $newAccount;
        }

        public function check_balance($user)
        {
            $nbalance = "";
            $this->web3->eth->getBalance($user, function ($err, $balance) use (&$nbalance) {
                if ($err !== null) {
                    $response = "FAILED! " . $err;
                    return;
                }
                $nbalance = $balance->toString();
            });

            return Utils::fromWei($nbalance, 'ether')[0];
        }

        public function get_entities()
        {

            $request = $this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "?type=Application,Agent,Staff&orderBy=name.value", []);
            $zones = json_decode($request["body"], true);
            return $zones;
        }

        public function get_contracts()
        {
            $pdoQuery = $this->hias->conn->prepare("
                SELECT id,
                    contract,
                    name
                FROM hiasbch_contracts
                ORDER BY id DESC
            ");
            $pdoQuery->execute();
            $data=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
            $pdoQuery->closeCursor();
            $pdoQuery = null;
            return $data;
        }

        public function get_contract($id)
        {
            $pdoQuery = $this->hias->conn->prepare("
                SELECT *
                FROM hiasbch_contracts
                WHERE id = :id
            ");
            $pdoQuery->execute([":id" => $id]);
            $data=$pdoQuery->fetch(PDO::FETCH_ASSOC);
            $pdoQuery->closeCursor();
            $pdoQuery = null;
            return $data;
        }

        public function store_contract()
        {
            if(!filter_input(INPUT_POST, "id", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "HIASBCH contract address is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "acc", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "HIASBCH account address is required"
                ];
            endif;

            $contract = filter_input(INPUT_POST, "id", FILTER_SANITIZE_STRING);
            $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
            $acc = filter_input(INPUT_POST, "acc", FILTER_SANITIZE_STRING);
            $txn = filter_input(INPUT_POST, "txid", FILTER_SANITIZE_STRING);
            $action = "Deploy HIASBCH Contract";

            $entity = $this->get_hiasbch();
            $contracts = $entity["contracts"]["value"];

            $contracts[] = [
                "name" => $name,
                "contract" => $contract,
                "abi" => json_decode($_POST["abi"], True),
                "bin" => $_POST["bin"],
                "txn" => $txn,
                "uid" => $_SESSION["HIAS"]["Uid"],
                "time" => time()
            ];

            $data = [
                "contracts" => [
                    "value" => $contracts
                ]
            ];

            $request = $this->hias->hiascdi->request("PATCH", $this->hias->hiascdi->confs["entities_url"] . "/" . $entity["id"] . "/attrs?type=HIASBCH", json_encode($data));
            $response = json_decode($request["body"], true);
            if(!isSet($response["Error"])):

                $txid = $this->hias->store_user_history($action, $txn, $contract);

                return [
                    "Response"=> "OK",
                    "Message" => "Contract deployed and saved to HIASCDI!",
                    "id" => $contract
                ];
            else:
                return [
                    "Response"=> "FAILED",
                    "Message" => "Contract save failed!"
                ];
            endif;
        }

        public function transfer_hias_ether()
        {
            $contract = new Contract($this->web3->provider, $this->confs["abi"]);
            $allowed = $this->permissions($contract);

            $from = filter_input(INPUT_POST, "acc", FILTER_SANITIZE_STRING);
            $pass = filter_input(INPUT_POST, "p", FILTER_SANITIZE_STRING);
            $to = filter_input(INPUT_POST, "bcaddress", FILTER_SANITIZE_STRING);

            $unlocked =  $this->unlock_account($from, $pass);

            $txn = "";
            $this->web3->eth->sendTransaction([
                'from' => $from,
                'to' => $to,
                'value' => '0x' . filter_input(INPUT_POST, "amount", FILTER_SANITIZE_NUMBER_INT)
            ], function ($err, $transaction) use ($txn) {
                if ($err !== null):
                    $txn = "FAILED! " . $err;
                    return;
                endif;
                $txn = $transaction;
            });

            if($txn == "FAILED"):
                return [
                    "Response"=> "FAILED",
                    "Message" => "HIAS Ether Transfer Failed"
                ];
            else:
                $action = "Transfer HIAS Ether";
                $this->hias->store_user_history($action, $txn);
                $balance1 = $this->check_balance($from);
                $balance2 = $this->check_balance($to);
                return [
                    "Response"=> "OK",
                    "Message" => "HIAS Ether Transfer OK!<br /><br />Sender Balance: " . $balance1 . "<br />Receiver Balance: " . $balance2
                ];
            endif;
        }

        public function get_contract_history($contractid, $limit = 0, $order = "")
        {

            if($order):
                $orderer = "ORDER BY " . $order;
            else:
                $orderer = "ORDER BY id DESC";
            endif;

            if($limit):
                $limiter = "LIMIT " . $limit;
            endif;

            $pdoQuery = $this->hias->conn->prepare("
                SELECT *
                FROM history
                WHERE tcid = :id
                $orderer
                $limiter
            ");
            $pdoQuery->execute([
                ":id" => $contractid
            ]);
            $response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
            return $response;
        }

        public function get_location($id, $attrs = Null)
        {

            if($attrs):
                $attrs="&attrs=" . $attrs;
            endif;

            $response = $this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "/" . $id . "?type=Location" . $attrs, []);
            $location = json_decode($response["body"], true);
            return $location;
        }

        public function get_zone($id, $attrs = Null)
        {

            if($attrs):
                $attrs="&attrs=" . $attrs;
            endif;

            $response = $this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "/" . $id . "?type=Zone" . $attrs, []);
            $zone = json_decode($response["body"], true);
            return $zone;
        }

        public function update_hiasbch_entity()
        {
            $lid = filter_input(INPUT_POST, "lid", FILTER_SANITIZE_STRING);
            $location = $this->get_location($lid);
            if(isSet($location["Error"])):
                return [
                    "Response"=> "FAILED",
                    "Message" => "iotJumpWay location does note exist!"
                ];
            endif;

            $zid = filter_input(INPUT_POST, 'zid', FILTER_SANITIZE_STRING);
            $zone = $this->get_zone($zid);
            if(isSet($zone["Error"])):
                return [
                    "Response"=> "FAILED",
                    "Message" => "iotJumpWay zone does note exist!"
                ];
            endif;

            $ip = filter_input(INPUT_POST, 'ipAddress', FILTER_SANITIZE_STRING);
            $mac = filter_input(INPUT_POST, 'macAddress', FILTER_SANITIZE_STRING);
            $bt = filter_input(INPUT_POST, 'bluetooth', FILTER_SANITIZE_STRING);
            $coords = explode(",", filter_input(INPUT_POST, "coordinates", FILTER_SANITIZE_STRING));

            $protocols = $_POST["protocols"];

            $data = [
                "name" => [
                    "value" => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)
                ],
                "description" => [
                    "value" => filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)
                ],
                "version" => [
                    "value" => filter_input(INPUT_POST, "version", FILTER_SANITIZE_STRING)
                ],
                "host" => [
                    "value" => filter_input(INPUT_POST, "host", FILTER_SANITIZE_STRING)
                ],
                "port" => [
                    "value" => filter_input(INPUT_POST, "port", FILTER_SANITIZE_STRING)
                ],
                "endpoint" => [
                    "value" => filter_input(INPUT_POST, "endpoint", FILTER_SANITIZE_STRING)
                ],
                "deviceBrandName" => [
                    "value" => filter_input(INPUT_POST, "deviceBrandName", FILTER_SANITIZE_STRING)
                ],
                "deviceModel" => [
                    "value" => filter_input(INPUT_POST, "deviceModel", FILTER_SANITIZE_STRING)
                ],
                "deviceManufacturer" => [
                    "value" => filter_input(INPUT_POST, "deviceManufacturer", FILTER_SANITIZE_STRING)
                ],
                "deviceSerialNumber" => [
                    "value" => filter_input(INPUT_POST, "deviceSerialNumber", FILTER_SANITIZE_STRING)
                ],
                "os" => [
                    "value" => filter_input(INPUT_POST, "os", FILTER_SANITIZE_STRING)
                ],
                "osVersion" => [
                    "value" => filter_input(INPUT_POST, "osVersion", FILTER_SANITIZE_STRING)
                ],
                "osManufacturer" => [
                    "value" => filter_input(INPUT_POST, "osManufacturer", FILTER_SANITIZE_STRING)
                ],
                "software" => [
                    "value" => filter_input(INPUT_POST, "software", FILTER_SANITIZE_STRING)
                ],
                "softwareVersion" => [
                    "value" => filter_input(INPUT_POST, "softwareVersion", FILTER_SANITIZE_STRING)
                ],
                "softwareManufacturer" => [
                    "value" => filter_input(INPUT_POST, "softwareManufacturer", FILTER_SANITIZE_STRING)
                ],
                "location" => [
                    "type" => "geo:json",
                    "value" => [
                        "type" => "Point",
                        "coordinates" => [floatval($coords[0]), floatval($coords[1])]
                    ]
                ],
                "networkLocation" => [
                    "value" => $location["id"],
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Location entity ID"
                        ]
                    ]
                ],
                "networkZone" => [
                    "value" => $zone["id"],
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Zone entity ID"
                        ]
                    ]
                ],
                "ipAddress" => [
                    "value" => $ip,
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "IP address of entity"
                        ]
                    ]
                ],
                "macAddress" => [
                    "value" => $mac,
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "MAC address of entity"
                        ]
                    ]
                ],
                "bluetoothAddress" => [
                    "value" => $bt,
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Bluetooth address of entity"
                        ]
                    ]
                ],
                "protocols" => [
                    "value" =>$protocols
                ],
                "dateModified" => [
                    "type" => "DateTime",
                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                ]
            ];

            $response = $this->hias->hiascdi->request("PATCH", $this->hias->hiascdi->confs["entities_url"] . "/" . $this->hias->helpers->oDecrypt($this->confs["entity"]) . "/attrs?type=HIASBCH", json_encode($data));
            if($response["code"] == 204):

                $this->hias->store_user_history("HIASBCH Entity configuration update");

                return [
                    "Response"=> "OK",
                    "Message" => "HIASBCH Entity configuration update OK!",
                    "Schema" => $this->get_hiasbch()
                ];
            else:
                return [
                    "Response"=> "FAILED",
                    "Message" => "HIASBCH Entity configuration update KO!"
                ];
            endif;
        }

        public function reset_network_key()
        {
            $hiasbch = $this->get_hiasbch();

            $privKey = $this->hias->helpers->generate_key(32);
            $privKeyHash = $this->hias->helpers->password_hash($privKey);

            $data = [
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
                ]
            ];

            $request = $this->hias->hiascdi->request("PATCH", $this->hias->hiascdi->confs["entities_url"] . "/" . $hiasbch["id"] . "/attrs?type=HIASBCH", json_encode($data));
            $response = json_decode($request["body"], true);
            if(!isSet($response["Error"])):

                $htpasswd = new Htpasswd('/etc/nginx/security/htpasswd');
                $htpasswd->updateUser($hiasbch["id"], $privKey, Htpasswd::ENCTYPE_APR_MD5);

                $this->hias->store_user_history("HIAS Update Application Key");

                return [
                    "Response"=> "OK",
                    "Message" => "HIASBCH network key reset!",
                    "P" => $privKey
                ];
            else:
                return [
                    "Response"=> "FAILED",
                    "Message" => "HIASBCH network key reset failed!"
                ];
            endif;
        }

        public function reset_mqtt_key()
        {
            $hiasbch = $this->get_hiasbch();

            $mqttPass = $this->hias->helpers->password();
            $mqttHash = create_hash($mqttPass);

            $data = [
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
                "dateModified" => [
                    "type" => "DateTime",
                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                ]
            ];

            $request = $this->hias->hiascdi->request("PATCH", $this->hias->hiascdi->confs["entities_url"] . "/" . $hiasbch["id"] . "/attrs?type=HIASBCH", json_encode($data));
            $response = json_decode($request["body"], true);
            if(!isSet($response["Error"])):

                $query = $this->hias->conn->prepare("
                    UPDATE mqttu
                    SET pw = :pw
                    WHERE uname = :uname
                ");
                $query->execute(array(
                    ':pw' => $mqttHash,
                    ':uname' => $hiasbch["authenticationMqttUser"]["value"]
                ));

                $this->hias->store_user_history("Reset HIASBCH MQTT key");

                return [
                    "Response"=> "OK",
                    "Message" => "MQTT password reset!",
                    "P" => $mqttPass
                ];
            else:
                return [
                    "Response"=> "FAILED",
                    "Message" => "MQTT password reset failed!"
                ];
            endif;
        }

        public function reset_amqp_key()
        {
            $hiasbch = $this->get_hiasbch();

            $amqpPass = $this->hias->helpers->password();
            $amqpHash = $this->hias->helpers->password_hash($amqpPass);

            $data = [
                "authenticationAmqpKey" => [
                    "value" => $this->hias->helpers->oEncrypt($amqpPass),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "AMQP password"
                        ],
                        "timestamp" => [
                            "value" =>  date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "dateModified" => [
                    "type" => "DateTime",
                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                ]
            ];

            $response = $this->hias->hiascdi->request("POST", $this->hias->hiascdi->confs["entities_url"] . "/" . $hiasbch["id"] . "/attrs?type=HIASBCH", json_encode($data));
            $response = json_decode($response["body"], true);

            if(!isSet($response["error"])):
                $query = $this->hias->conn->prepare("
                    UPDATE amqpu
                    SET pw = :pw
                    WHERE username = :username
                ");
                $query->execute(array(
                    ':pw' => $this->hias->helpers->oEncrypt($amqpHash),
                    ':username' => $this->hias->helpers->oDecrypt($hiasbch["authenticationAmqpUser"]["value"])
                ));

                $this->hias->store_user_history("Reset HIASBCH AMQP Key");

                return [
                    "Response"=> "OK",
                    "Message" => "AMQP password reset!",
                    "P" => $amqpPass
                ];
            else:
                return [
                    "Response"=> "FAILED",
                    "Message" => "AMQP password reset failed!"
                ];
            endif;
        }

        public function get_block($hash)
        {
            $data = "";
            $msg = "";
            $eth = $this->web3->eth;
            $eth->getBlockByHash($hash, False, function ($err, $block) use (&$data) {
                if ($err !== null) {
                    $data = "FAILED";
                    $msg = $err;
                    return;
                }
                $data = $block;
            });

            if($data == "FAIL"):
                return [
                    "Response" => "FAILED",
                    "Message" => "Fetch Block Failed. " . $msg
                ];
            else:
                return [
                    "Response" => "OK",
                    "Message" => "Fetch Block OK. ",
                    "Data" => $data
                ];
            endif;

        }

        public function get_transactions($limit = 0, $order = "", $address = "")
        {
            $limiter = "";
            $orderer = "";

            if($limit):
                $limiter = "&limit=" . $limit;
            endif;

            if($order == ""):
                $orderer = "&orderBy=!BlockNumber";
            else:
                $orderer = "&orderBy=" . $order;
            endif;

            if($address != ""):
                $address = "&q=To==" . $address  . "||From==" . $address;
            endif;

            $request = $this->hias->hiashdi->request("GET", $this->hias->hiashdi->confs["data_url"] . "?type=Transactions" . $address . $orderer . $limiter, []);
            $response = json_decode($request["body"], true);

            if(!isSet($response["Error"])):
                return  [
                    'Response'=>'OK',
                    'ResponseData'=>$response
                ];
            else:
                return  [
                    'Response'=>'FAILED'
                ];
            endif;
        }

        public function get_transaction($hash)
        {
            $data = "";
            $msg = "";
            $eth = $this->web3->eth;
            $eth->getTransactionByHash($hash, function ($err, $block) use (&$data) {
                if ($err !== null) {
                    $data = "FAILED";
                    $msg = $err;
                    return;
                }
                $data = $block;
            });

            if($data == "FAIL"):
                return [
                    "Response" => "FAILED",
                    "Message" => "Fetch Transaction Failed. " . $msg
                ];
            else:
                return [
                    "Response" => "OK",
                    "Message" => "Fetch Transaction OK. ",
                    "Data" => $data
                ];
            endif;

        }

        public function get_transaction_receipt($hash)
        {

            $dreceipt = "";
            $msg = "";
            $eth = $this->web3->eth;
            $eth->getTransactionReceipt($hash, function ($err, $receipt) use (&$dreceipt) {
                if ($err !== null) {
                    $dreceipt = "FAILED";
                    $msg = $err;
                    return;
                }
                $dreceipt = $receipt;
            });

            if($dreceipt == "FAIL"):
                return [
                    "Response" => "FAILED",
                    "Message" => "Fetch Transaction Failed. " . $msg
                ];
            else:
                return [
                    "Response" => "OK",
                    "Message" => "Fetch Transaction OK. ",
                    "Receipt" => $dreceipt
                ];
            endif;

        }

        public function get_blockchain_historical($type, $limit = 0, $order = "")
        {
            $limiter = "";
            $orderer = "";

            if($limit):
                $limiter = "&limit=" . $limit;
            endif;

            if($order == ""):
                $orderer = "&orderBy=!Block";
            else:
                $orderer = "&orderBy=" . $order;
            endif;

            $request = $this->hias->hiashdi->request("GET", $this->hias->hiashdi->confs["data_url"] . "?type=" . $type. $orderer . $limiter, []);
            $response = json_decode($request["body"], true);

            if($response && !isSet($response["Error"])):
                return  [
                    'Response'=>'OK',
                    'ResponseData'=>$response
                ];
            else:
                return  [
                    'Response'=>'FAILED'
                ];
            endif;
        }

        public function get_accounts()
        {
            $data = "";
            $msg = "";
            $eth = $this->web3->eth;
            $eth->accounts(function ($err, $accounts) use (&$data) {
                if ($err !== null) {
                    $data = "FAILED";
                    $msg = $err;
                    return;
                }
                $data = $accounts;
            });

            if($data == "FAIL"):
                return [
                    "Response" => "FAILED",
                    "Message" => "Fetch Accounts Failed. " . $msg
                ];
            else:
                return [
                    "Response" => "OK",
                    "Message" => "Fetch Accounts OK. ",
                    "Data" => $data
                ];
            endif;

        }

        public function get_account_transactions($account, $limit = 0, $order = "", $type = "")
        {
            $limiter = "";
            $orderer = "";

            if($limit):
                $limiter = "&limit=" . $limit;
            endif;

            if($order == ""):
                $orderer = "&orderBy=!BlockNumber";
            else:
                $orderer = "&orderBy=" . $order;
            endif;

            $request = $this->hias->hiashdi->request("GET", $this->hias->hiashdi->confs["data_url"] . "?type=Transactions&q=From==" . $account . "||To==" . $account . "&options=count". $orderer . $limiter, []);
            $response = json_decode($request["body"], true);

            if(!isSet($response["Error"])):
                return  [
                    'Response'=>'OK',
                    'ResponseData'=>$response
                ];
            else:
                return  [
                    'Response'=>'FAILED'
                ];
            endif;
        }

        public function hiasbch_life_graph($hiasbch, $limit = 0, $order = "")
        {
            $limiter = "";
            $orderer = "";

            if($limit):
                $limiter = "&limit=" . $limit;
            endif;

            if($order == ""):
                $orderer = "&orderBy=!Time";
            else:
                $orderer = "&orderBy=" . $order;
            endif;

            $request = $this->hias->hiashdi->request("GET", $this->hias->hiashdi->confs["data_url"] . "?type=Life&q=Use==HIASBCH;HIASBCH==". $hiasbch . $limiter . $orderer, []);
            $response = json_decode($request["body"], true);

            if(!isSet($response["Error"])):
                return  [
                    'Response'=>'OK',
                    'ResponseData'=>$response
                ];
            else:
                return  [
                    'Response'=>'FAILED'
                ];
            endif;

        }

        public function update_hiasbch_life_graph($params=[])
        {
            $data = $this->hiasbch_life_graph($this->hias->confs["aid"], 100);

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

        public function check_data_integrity()
        {
            return [
                "Respose"=>"OK",
                "Check"=>password_verify(filter_input(INPUT_POST, "current", FILTER_SANITIZE_STRING), filter_input(INPUT_POST, "hash", FILTER_SANITIZE_STRING)),
                "String"=>filter_input(INPUT_POST, "current", FILTER_SANITIZE_STRING),
                "Hash"=>filter_input(INPUT_POST, "hash", FILTER_SANITIZE_STRING)
            ];
        }

    }