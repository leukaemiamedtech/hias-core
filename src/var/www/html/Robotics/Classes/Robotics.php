<?php

    class Robotics
    {
        function __construct($hias, $iotJumpWay)
        {
            $this->hias = $hias;
            $this->iotJumpWay = $iotJumpWay;
        }

        public function get_thing($id, $attrs = Null)
        {
            if($attrs):
                $attrs="&attrs=" . $attrs;
            endif;

            $request = $this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "/" . $id . "?type=Thing" . $attrs, []);
            $thing = json_decode($request["body"], true);
            return $thing;
        }

        public function get_robotics_types()
        {
            $query = $this->hias->conn->prepare("
                SELECT id,
                    r_type
                FROM robotics_types
                ORDER BY id ASC

            ");
            $query->execute();
            $response=$query->fetchAll(PDO::FETCH_ASSOC);
            return $response;
        }

        public function get_robotics_categories()
        {
            $query = $this->hias->conn->prepare("
                SELECT id,
                    category
                FROM robotics_categories
                ORDER BY id ASC

            ");
            $query->execute();
            $response=$query->fetchAll(PDO::FETCH_ASSOC);
            return $response;
        }

        public function get_robotics($limit = 0, $category = "")
        {
            $limiter = "";
            if($limit != 0):
                $limiter = "?limit=" . $limit;
            endif;
            if($category != ""):
                $category = str_replace(' ', '%20', $category);
                $category = "&category=" . $category;
            endif;

            $request = $this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "?type=Robotics".$category.$limiter, []);
            $robotics = json_decode($request["body"], true);
            return $robotics;
        }

        public function get_robotic($id, $attrs = Null)
        {

            if($attrs):
                $attrs="&attrs=" . $attrs;
            endif;

            $request = $this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "/" . $id . "?type=Robotics" . $attrs, []);
            $robotics = json_decode($request["body"], true);
            return $robotics;
        }

        public function get_model($id, $attrs = Null)
        {

            if($attrs):
                $attrs="&attrs=" . $attrs;
            endif;

            $request = $this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "/" . $id . "?type=Model" . $attrs, []);
            $device = json_decode($request["body"], true);
            return $device;
        }

        public function create_robotics()
        {
            if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Location ID is required"
                ];
            endif;

            if(!$this->iotJumpWay->check_location(filter_input(INPUT_POST, "lid", FILTER_SANITIZE_STRING))):
                return [
                    "Response"=> "Failed",
                    "Message" => "iotJumpWay location does not exist"
                ];
            endif;

            if(!filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Name is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Name is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "coordinates", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Coordinates entity is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "deviceBrandName", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Hardware device name is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "deviceManufacturer", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Hardware device manufacturer is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "deviceModel", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Hardware device model is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "osName", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Operating system name is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "osManufacturer", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Operating system manufacturer is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "osVersion", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Operating system version is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "osVersion", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Operating system version is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "IP is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "MAC is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "agent", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "IoT Agent is required"
                ];
            endif;

            if(!isSet($_POST["protocols"])):
                return [
                    "Response"=> "Failed",
                    "Message" => "At least one M2M protocol is required"
                ];
            endif;

            if(!isSet($_POST["category"])):
                return [
                    "Response"=> "Failed",
                    "Message" => "At least one category is required"
                ];
            endif;

            $unlocked =  $this->hias->hiasbch->unlock_account($_SESSION["HIAS"]["BC"]["BCUser"], $this->hias->helpers->oDecrypt($_SESSION["HIAS"]["BC"]["BCPass"]));

            if($unlocked == "FAILED"):
                return [
                    "Response"=> "Failed",
                    "Message" => "Unlocking HIAS Blockhain Account Failed!"
                ];
            endif;

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

            $newBcUser = $this->hias->hiasbch->create_user($bcPass);

            if($newBcUser == "FAILED"):
                return [
                    "Response"=> "Failed",
                    "Message" => "Creating New HIAS Blockhain Account Failed!"
                ];
            endif;

            $lid = filter_input(INPUT_POST, 'lid', FILTER_SANITIZE_STRING);
            $location = $this->iotJumpWay->get_location($lid);

            $ip = filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING);
            $mac = filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING);
            $bt = filter_input(INPUT_POST, "bluetooth", FILTER_SANITIZE_STRING);
            $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
            $coords = explode(",", filter_input(INPUT_POST, "coordinates", FILTER_SANITIZE_STRING));

            $models = [];
            if(isSet($_POST["ai"])):
                foreach($_POST["ai"] AS $key => $value):
                    $model = $this->get_model($value);
                    $mname = $model["name"]["value"];
                    unset($model["type"]);
                    unset($model["mid"]);
                    unset($model["name"]);
                    unset($model["description"]);
                    unset($model["network"]);
                    unset($model["language"]);
                    unset($model["framework"]);
                    unset($model["toolkit"]);
                    unset($model["dateCreated"]);
                    unset($model["dateModified"]);
                    $models[$mname] = $model;
                endforeach;
            endif;

            $sensors = [];
            if(isSet($_POST["sensors"])):
                foreach($_POST["sensors"] AS $key => $value):
                    $sensor = $this->get_thing($value);
                    unset($sensor["type"]);
                    unset($sensor["category"]);
                    unset($sensor["description"]);
                    unset($sensor["thing"]);
                    unset($sensor["properties"]["image"]);
                    unset($sensor["dateCreated"]);
                    unset($sensor["dateModified"]);
                    $sensors[] = $sensor;
                endforeach;
            endif;

            $actuators = [];
            if(isSet($_POST["actuators"])):
                foreach($_POST["actuators"] AS $key => $value):
                    $actuator = $this->get_thing($value);
                    unset($actuator["type"]);
                    unset($actuator["category"]);
                    unset($actuator["description"]);
                    unset($actuator["thing"]);
                    unset($actuator["properties"]["image"]);
                    unset($actuator["dateCreated"]);
                    unset($actuator["dateModeified"]);
                    $actuators[] = $actuator;
                endforeach;
            endif;

            $htpasswd = new Htpasswd('/etc/nginx/security/htpasswd');
            $htpasswd->addUser($pubKey, $privKey, Htpasswd::ENCTYPE_APR_MD5);

            $protocols = $_POST["protocols"];

            $data = [
                "id" => $pubKey,
                "type" => "Robotics",
                "category" => [
                    "value" => $_POST["category"]
                ],
                "name" => [
                    "value" => $name
                ],
                "description" => [
                    "value" => filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)
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
                    "value" => filter_input(INPUT_POST, "deviceBrandName", FILTER_SANITIZE_STRING)
                ],
                "deviceModel" => [
                    "value" => filter_input(INPUT_POST, "deviceModel", FILTER_SANITIZE_STRING)
                ],
                "deviceManufacturer" => [
                    "value" => filter_input(INPUT_POST, "deviceManufacturer", FILTER_SANITIZE_STRING)
                ],
                "deviceSerialNumber" => [
                    "value" => filter_input(INPUT_POST, "serialNumber", FILTER_SANITIZE_STRING)
                ],
                "os" => [
                    "value" => filter_input(INPUT_POST, "os", FILTER_SANITIZE_STRING)
                ],
                "osVersion" => [
                    "value" => filter_input(INPUT_POST, "osVersion", FILTER_SANITIZE_STRING),
                    "metadata" => [
                        "timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
                    ]
                ],
                "osManufacturer" => [
                    "value" => filter_input(INPUT_POST, "osManufacturer", FILTER_SANITIZE_STRING)
                ],
                "software" => [
                    "value" => filter_input(INPUT_POST, "softwareName", FILTER_SANITIZE_STRING)
                ],
                "softwareVersion" => [
                    "value" => filter_input(INPUT_POST, "softwareVersion", FILTER_SANITIZE_STRING),
                    "metadata" => [
                        "timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
                    ]
                ],
                "softwareManufacturer" => [
                    "value" => filter_input(INPUT_POST, "softwareManufacturer", FILTER_SANITIZE_STRING)
                ],
                "agent" => [
                    "value" => filter_input(INPUT_POST, "agent", FILTER_SANITIZE_STRING)
                ],
                "location" => [
                    "type" => "geo:json",
                    "value" => [
                        "type" => "Point",
                        "coordinates" => [floatval($coords[0]), floatval($coords[1])]
                    ]
                ],
                "northPort" => [
                    "value" => filter_input(INPUT_POST, "northPort", FILTER_SANITIZE_NUMBER_INT),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Northport of the Robotics unit"
                        ]
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
                    "value" => $location["id"],
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Location entity ID"
                        ]
                    ]
                ],
                "ipAddress" => [
                    "value" => $ip,
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "IP address of entity"
                        ],
                        "timestamp" =>   [
                            "type" => "DateTime",
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "macAddress" => [
                    "value" => $mac,
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "MAC address of entity"
                        ],
                        "timestamp" =>   [
                            "type" => "DateTime",
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
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
                    "type" => "StructuredValue",
                    "value" => $protocols,
                    "metadata" => [
                        "description" => [
                            "value" => "Supported protocols"
                        ]
                    ]
                ],
                "northPort" => [
                    "value" => filter_input(INPUT_POST, "northPort", FILTER_SANITIZE_NUMBER_INT),
                    "metadata" => [
                        "description" => [
                            "value" => "North port of the Agent"
                        ],
                        "timestamp" =>   [
                            "type" => "DateTime",
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "streamPort" => [
                    "value" => filter_input(INPUT_POST, "stream_port", FILTER_SANITIZE_NUMBER_INT),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Stream port of the Robotics unit camera unit"
                        ]
                    ]
                ],
                "streamFile" => [
                    "value" => filter_input(INPUT_POST, "stream_file", FILTER_SANITIZE_STRING),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "File path used to access stream of the Robotics unit camera unit"
                        ]
                    ]
                ],
                "endpoint" => [
                    "value" => filter_input(INPUT_POST, "endpoint", FILTER_SANITIZE_STRING),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Name of endpoint used by the HIAS proxy to route traffic to the agent"
                        ]
                    ]
                ],
                "socketPort" => [
                    "value" => filter_input(INPUT_POST, "socket_port", FILTER_SANITIZE_NUMBER_INT),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Socket port of the Robotics unit camera unit"
                        ]
                    ]
                ],
                "models" => [
                    "value" => $models,
                    "type" => "StructuredValue",
                    "metadata" => [
                        "description" => [
                            "value" => "Supported models"
                        ]
                    ]
                ],
                "sensors" => [
                    "value" => $sensors,
                    "type" => "StructuredValue",
                    "metadata" => [
                        "description" => [
                            "value" =>  "Sensors connected to this device"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "actuators" => [
                    "value" => $actuators,
                    "type" => "StructuredValue",
                    "metadata" => [
                        "description" => [
                            "value" =>  "Actuators connected to this device"
                        ],
                        "timestamp" => [
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
                    "value" => $this->hias->helpers->oEncrypt(filter_input(INPUT_POST, "authenticationIpinfoKey", FILTER_SANITIZE_STRING)),
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

            $request = $this->hias->hiascdi->request("POST", $this->hias->hiascdi->confs["entities_url"] . "?type=Robotics", json_encode($data));
            $response = json_decode($request["body"], true);
            if(!isSet($response["Error"])):
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
                    ':topic' => $location["id"] . "/Robotics/" . $pubKey . "/#",
                    ':rw' => 4
                ));

                $amid = $this->iotJumpWay->addAmqpUser($amqppubKey, $amqpKeyHash);
                $this->iotJumpWay->addAmqpUserVh($amid, "iotJumpWay");
                $this->iotJumpWay->addAmqpVhPerm($amid, "iotJumpWay", "exchange", "Core", "read");
                $this->iotJumpWay->addAmqpVhPerm($amid, "iotJumpWay", "exchange", "Core", "write");
                $this->iotJumpWay->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Life", "read");
                $this->iotJumpWay->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Life", "write");
                $this->iotJumpWay->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Statuses", "read");
                $this->iotJumpWay->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Statuses", "write");
                $this->iotJumpWay->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "read", "Life");
                $this->iotJumpWay->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "write", "Life");
                $this->iotJumpWay->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "read", "Statuses");
                $this->iotJumpWay->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "write", "Statuses");

                $hash = "";
                $msg = "";
                $actionMsg = "";
                $balanceMessage = "";
                $this->hias->hiasbch->contract->at($this->hias->hiasbch->confs["contract"])->send("registerAgent", $pubKey, $newBcUser, $location["id"], "NA", $name, $_SESSION["HIAS"]["Uid"], time(), ["from" => $_SESSION["HIAS"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
                    if ($err !== null) {
                        $hash = "FAILED";
                        $msg = $err;
                        return;
                    }
                    $hash = $resp;
                });

                if($hash == "FAILED"):
                    $actionMsg = " HIASBCH registerAgent failed!\n" . $msg;
                else:
                    $this->hias->store_user_history("HIASBCH registerAgent (Robotics)", $hash, 0, $lid, "", "",  "", "", "", "", $pubKey);
                    $balance = $this->hias->hiasbch->check_balance($_SESSION["HIAS"]["BC"]["BCUser"], $this->hias->helpers->oDecrypt($_SESSION["HIAS"]["BC"]["BCPass"]));
                    $actionMsg = " HIASBCH registerAgent OK!\n";
                    $balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!";
                endif;

                $this->hias->hiasbch->icontract->at($this->hias->hiasbch->confs["icontract"])->send("registerAuthorized", $newBcUser, ["from" => $_SESSION["HIAS"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
                    if ($err !== null) {
                        $hash = "FAILED";
                        $msg = $err;
                        return;
                    }
                    $hash = $resp;
                });

                if($hash == "FAILED"):
                    $actionMsg .= " HIASBCH registerAuthorized failed! " . $msg;
                else:
                    $this->hias->store_user_history("HIASBCH registerAuthorized (Robotics)", $hash, 0, $lid, "", "",  "", "", "", "", $pubKey);
                    $balance = $this->hias->hiasbch->check_balance($_SESSION["HIAS"]["BC"]["BCUser"], $this->hias->helpers->oDecrypt($_SESSION["HIAS"]["BC"]["BCPass"]));
                    $actionMsg .= " HIASBCH registerAuthorized OK!\n";
                    $balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!";
                endif;

                return [
                    "Response"=> "OK",
                    "Message" => "Robotics Unit created!" . $actionMsg . $balanceMessage,
                    "LID" => $lid,
                    "DID" => $pubKey,
                    "MU" => $mqttUser,
                    "MP" => $mqttPass,
                    "BU" => $newBcUser,
                    "BP" => $bcPass,
                    "AppID" => $pubKey,
                    "AppKey" => $privKey
                ];
            else:
                return [
                    "Response"=> "FAILED",
                    "Message" => "Robotics Unit creation failed"
                ];
            endif;
        }

        public function update_robotics()
        {
            if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Location ID is required"
                ];
            endif;

            if(!$this->iotJumpWay->check_location(filter_input(INPUT_POST, "lid", FILTER_SANITIZE_STRING))):
                return [
                    "Response"=> "Failed",
                    "Message" => "iotJumpWay location does not exist"
                ];
            endif;

            if(!filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Name is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Name is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "coordinates", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Coordinates entity is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "deviceBrandName", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Hardware device name is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "deviceManufacturer", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Hardware device manufacturer is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "deviceModel", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Hardware device model is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "os", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Operating system name is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "osManufacturer", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Operating system manufacturer is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "osVersion", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Operating system version is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "osVersion", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Operating system version is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "ipAddress", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "IP is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "northPort", FILTER_SANITIZE_NUMBER_INT)):
                return [
                    "Response"=> "Failed",
                    "Message" => "North Port is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "macAddress", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "MAC is required"
                ];
            endif;

            if(!count($_POST["protocols"])):
                return [
                    "Response"=> "Failed",
                    "Message" => "At least one M2M protocol is required"
                ];
            endif;

            $aid = filter_input(INPUT_GET, 'unit', FILTER_SANITIZE_STRING);
            $robotics = $this->get_robotic($aid);

            $lid = filter_input(INPUT_POST, "lid", FILTER_SANITIZE_STRING);
            $location = $this->iotJumpWay->get_location($lid);

            $ip = filter_input(INPUT_POST, "ipAddress", FILTER_SANITIZE_STRING);
            $mac = filter_input(INPUT_POST, "macAddress", FILTER_SANITIZE_STRING);
            $bt = filter_input(INPUT_POST, "bluetoothAddress", FILTER_SANITIZE_STRING);
            $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
            $coords = explode(",", filter_input(INPUT_POST, "coordinates", FILTER_SANITIZE_STRING));

            $protocols = $_POST["protocols"];

            $models = [];
            if(isSet($_POST["ai"])):
                foreach($_POST["ai"] AS $key => $value):
                    $model = $this->get_model($value);
                    $mname = $model["name"]["value"];
                    unset($model["type"]);
                    unset($model["mid"]);
                    unset($model["name"]);
                    unset($model["description"]);
                    unset($model["network"]);
                    unset($model["language"]);
                    unset($model["framework"]);
                    unset($model["toolkit"]);
                    unset($model["dateCreated"]);
                    unset($model["dateModified"]);
                    $models[$mname] = $model;
                endforeach;
            endif;

            $sensors = [];
            if(isSet($_POST["sensors"])):
                foreach($_POST["sensors"] AS $key => $value):
                    $sensor = $this->get_thing($value);
                    unset($sensor["type"]);
                    unset($sensor["category"]);
                    unset($sensor["description"]);
                    unset($sensor["thing"]);
                    unset($sensor["properties"]["image"]);
                    unset($sensor["dateCreated"]);
                    unset($sensor["dateModified"]);
                    $sensors[] = $sensor;
                endforeach;
            endif;

            $actuators = [];
            if(isSet($_POST["actuators"])):
                foreach($_POST["actuators"] AS $key => $value):
                    $actuator = $this->get_thing($value);
                    unset($actuator["type"]);
                    unset($actuator["category"]);
                    unset($actuator["description"]);
                    unset($actuator["thing"]);
                    unset($actuator["properties"]["image"]);
                    unset($actuator["dateCreated"]);
                    unset($actuator["dateModeified"]);
                    $actuators[] = $actuator;
                endforeach;
            endif;

            $data = [
                "type" => "Robotics",
                "type" => "Robotics",
                "category" => [
                    "value" => $_POST["category"]
                ],
                "name" => [
                    "value" => $name
                ],
                "description" => [
                    "value" => filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)
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
                    "value" => filter_input(INPUT_POST, "deviceBrandName", FILTER_SANITIZE_STRING)
                ],
                "deviceModel" => [
                    "value" => filter_input(INPUT_POST, "deviceModel", FILTER_SANITIZE_STRING)
                ],
                "deviceManufacturer" => [
                    "value" => filter_input(INPUT_POST, "deviceManufacturer", FILTER_SANITIZE_STRING)
                ],
                "deviceSerialNumber" => [
                    "value" => filter_input(INPUT_POST, "serialNumber", FILTER_SANITIZE_STRING)
                ],
                "os" => [
                    "value" => filter_input(INPUT_POST, "os", FILTER_SANITIZE_STRING)
                ],
                "osVersion" => [
                    "value" => filter_input(INPUT_POST, "osVersion", FILTER_SANITIZE_STRING),
                    "metadata" => [
                        "timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
                    ]
                ],
                "osManufacturer" => [
                    "value" => filter_input(INPUT_POST, "osManufacturer", FILTER_SANITIZE_STRING)
                ],
                "software" => [
                    "value" => filter_input(INPUT_POST, "softwareName", FILTER_SANITIZE_STRING)
                ],
                "softwareVersion" => [
                    "value" => filter_input(INPUT_POST, "softwareVersion", FILTER_SANITIZE_STRING),
                    "metadata" => [
                        "timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
                    ]
                ],
                "softwareManufacturer" => [
                    "value" => filter_input(INPUT_POST, "softwareManufacturer", FILTER_SANITIZE_STRING)
                ],
                "agent" => [
                    "value" => filter_input(INPUT_POST, "agent", FILTER_SANITIZE_STRING)
                ],
                "location" => [
                    "type" => "geo:json",
                    "value" => [
                        "type" => "Point",
                        "coordinates" => [floatval($coords[0]), floatval($coords[1])]
                    ]
                ],
                "northPort" => [
                    "value" => filter_input(INPUT_POST, "northPort", FILTER_SANITIZE_NUMBER_INT),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Northport of the Robotics unit"
                        ]
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
                "ipAddress" => [
                    "value" => $ip,
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "IP address of entity"
                        ],
                        "timestamp" =>   [
                            "type" => "DateTime",
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "macAddress" => [
                    "value" => $mac,
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "MAC address of entity"
                        ],
                        "timestamp" =>   [
                            "type" => "DateTime",
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
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
                    "type" => "StructuredValue",
                    "value" => $protocols,
                    "metadata" => [
                        "description" => [
                            "value" => "Supported protocols"
                        ]
                    ]
                ],
                "northPort" => [
                    "value" => filter_input(INPUT_POST, "northPort", FILTER_SANITIZE_NUMBER_INT),
                    "metadata" => [
                        "description" => [
                            "value" => "North port of the Agent"
                        ],
                        "timestamp" =>   [
                            "type" => "DateTime",
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "streamPort" => [
                    "value" => filter_input(INPUT_POST, "stream_port", FILTER_SANITIZE_NUMBER_INT),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Stream port of the Robotics unit camera unit"
                        ]
                    ]
                ],
                "streamFile" => [
                    "value" => filter_input(INPUT_POST, "stream_file", FILTER_SANITIZE_STRING),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "File path used to access stream of the Robotics unit camera unit"
                        ]
                    ]
                ],
                "endpoint" => [
                    "value" => filter_input(INPUT_POST, "endpoint", FILTER_SANITIZE_STRING),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Name of endpoint used by the HIAS proxy to route traffic to the agent"
                        ]
                    ]
                ],
                "socketPort" => [
                    "value" => filter_input(INPUT_POST, "socket_port", FILTER_SANITIZE_NUMBER_INT),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Socket port of the Robotics unit camera unit"
                        ]
                    ]
                ],
                "models" => [
                    "value" => $models,
                    "type" => "StructuredValue",
                    "metadata" => [
                        "description" => [
                            "value" => "Supported models"
                        ]
                    ]
                ],
                "sensors" => [
                    "value" => $sensors,
                    "type" => "StructuredValue",
                    "metadata" => [
                        "description" => [
                            "value" =>  "Sensors connected to this device"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "actuators" => [
                    "value" => $actuators,
                    "type" => "StructuredValue",
                    "metadata" => [
                        "description" => [
                            "value" =>  "Actuators connected to this device"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "authenticationIpinfoKey" => [
                    "value" => $this->hias->helpers->oEncrypt(filter_input(INPUT_POST, "authenticationIpinfoKey", FILTER_SANITIZE_STRING)),
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
                "dateModified" => [
                    "type" => "DateTime",
                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                ]
            ];

            $request = $this->hias->hiascdi->request("POST", $this->hias->hiascdi->confs["entities_url"] . "/" . $robotics["id"] . "/attrs?type=Robotics", json_encode($data));
            $response = json_decode($request["body"], true);

            if(!isSet($response["Error"])):

                $unlocked =  $this->hias->hiasbch->unlock_account($_SESSION["HIAS"]["BC"]["BCUser"], $this->hias->helpers->oDecrypt($_SESSION["HIAS"]["BC"]["BCPass"]));

                if($unlocked == "FAILED"):
                    return [
                        "Response"=> "Failed",
                        "Message" => "Unlocking HIAS Blockhain Account Failed!"
                    ];
                endif;

                $hash = "";
                $msg = "";
                $actionMsg = "";
                $balanceMessage = "";

                $this->hias->hiasbch->contract->at($this->hias->hiasbch->confs["contract"])->send("updateAgent", $robotics["id"], "Agent", $lid, "NA", $name,  time(), ["from" => $_SESSION["HIAS"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
                    if ($err !== null) {
                        $hash = "FAILED";
                        $msg = $err;
                        return;
                    }
                    $hash = $resp;
                });

                if($hash == "FAILED"):
                    $actionMsg = " HIASBCH updateDevice (Robotics) failed! " . $msg;
                else:
                    $this->hias->store_user_history("HIASBCH updateAgent (Robotics)", $hash, 0, $lid, "", "", "", "", "", "", $robotics["id"]);
                    $balance = $this->hias->hiasbch->check_balance($_SESSION["HIAS"]["BC"]["BCUser"]);
                    $balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!";
                endif;

                return [
                    "Response"=> "OK",
                    "Message" => "Robotics updated!" . $actionMsg . $balanceMessage,
                    "Schema" => $robotics,
                    "BC" => $_SESSION["HIAS"]["BC"]["BCUser"]
                ];
            else:
                return [
                    "Response"=> "FAILED",
                    "Message" => "Robotics update failed!",
                    "BC" => $_SESSION["HIAS"]["BC"]["BCUser"]
                ];
            endif;
        }
        public function get_robotics_history($robotics, $limit = 0, $order = "")
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
                WHERE trid = :id
                $orderer
                $limiter
            ");
            $pdoQuery->execute([
                ":id" => $robotics
            ]);
            $response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
            return $response;
        }

        public function get_robotics_transactions($robotics, $limit = 0, $order = "")
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

            $request = $this->hias->hiashdi->request("GET", $this->hias->hiashdi->confs["data_url"] . "?type=Transactions&q=From==" . $robotics . $orderer . $limiter, []);
            $response = json_decode($request["body"], true);

            if(!isSet($response["Error"])):
                return  $response;
            else:
                return False;
            endif;
        }

        public function get_robotics_statuses($robotics, $limit = 0, $order = "")
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

            $request = $this->hias->hiashdi->request("GET", $this->hias->hiashdi->confs["data_url"] . "?type=Statuses&q=Use==Robotics;Robotics==". $robotics . $limiter . $orderer, []);
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

        public function get_robotics_life($robotics, $limit = 0, $order = -1)
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

            $request = $this->hias->hiashdi->request("GET", $this->hias->hiashdi->confs["data_url"] . "?type=Life&q=Use==Robotics;Robotics==". $robotics . $limiter . $orderer, []);
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

        public function get_robotics_sensors($robotics, $limit = 0, $order = -1)
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

            $request = $this->hias->hiashdi->request("GET", $this->hias->hiashdi->confs["data_url"] . "?type=Sensors&q=Use==Robotics;Robotics==". $robotics . $limiter . $orderer, []);
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

        public function get_robotics_actuators($robotics, $limit = 0, $order = -1)
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

            $request = $this->hias->hiashdi->request("GET", $this->hias->hiashdi->confs["data_url"] . "?type=Actuators&q=Use==Robotics;Robotics==". $robotics . $limiter . $orderer, []);
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

        public function robotics_life_graph($robotics, $limit = 0, $order = -1)
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

            $request = $this->hias->hiashdi->request("GET", $this->hias->hiashdi->confs["data_url"] . "?type=Life&q=Use==Robotics;Robotics==". $robotics . $limiter . $orderer, []);
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

        public function update_robotics_life_graph($params=[])
        {

            $data = $this->robotics_life_graph(filter_input(INPUT_GET, "robotics", FILTER_SANITIZE_STRING), 100);

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

        public function robotics_sensors_graph($params=[])
        {
            $limiter = "";
            $orderer = "";
            $type = "";

            if(isSet($params["data"])):
                $type = ";Type==" . $params["data"];
            endif;

            if(isSet($params["limit"])):
                $limiter = "&limit=" . $params["limit"];
            endif;

            if(!isSet($params["order"])):
                $orderer = "&orderBy=!Time";
            else:
                $orderer = "&orderBy=" . $params["order"];
            endif;

            $request = $this->hias->hiashdi->request("GET", $this->hias->hiashdi->confs["data_url"] . "?type=Sensors&q=Use==Robotics;Robotics==". $params["robotics"] . $type . "&attrs=Value,Time,Type" . $limiter . $orderer, []);
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

        public function update_robotics_sensors_graph($params=[])
        {
            $types = [];
            $dates = [];
            $points = [];

            $data = $this->robotics_sensors_graph([
                "robotics" => filter_input(INPUT_GET, "robotics", FILTER_SANITIZE_STRING),
                "limit" => 100
            ]);
            if(isSet($data["ResponseData"])):
                $data = array_reverse($data["ResponseData"]);

                if(count($data)):
                    $i=0;
                    foreach($data AS $key => $value):
                        if(is_array($value["Value"])):
                            foreach($value["Value"] AS $ikey => $ivalue):
                                $types[$ikey][] = $ivalue;
                            endforeach;
                            $dates[] = $value["Time"];
                        else:
                            $types[$value["Type"]][] = $value["Value"];
                            if(!in_array(date("Y-m-d H:i", strtotime($value["Time"])), $dates)):
                                $dates[] = date("Y-m-d H:i", strtotime($value["Time"]));
                            endif;
                        endif;
                        $i++;
                    endforeach;

                    $colors = [
                        'orange',
                        'cyan',
                        'yellow',
                        'red',
                        'purple',
                        'green'
                    ];

                    if(count($types)):
                        ksort($types);
                        $i = 0;
                        foreach($types AS $tkey => $tvalue):
                            $points[] = [
                                "name" => $tkey,
                                "data" => $tvalue,
                                "type" => 'line',
                                "smooth" => true,
                                "color" => [$colors[$i]]
                            ];
                            $i++;
                        endforeach;
                    endif;
                endif;
            endif;

            return [$dates, $points];
        }

        public function update_robotics_history()
        {
            $return = "";
            if(filter_input(INPUT_POST, 'roboticsHistory', FILTER_SANITIZE_STRING) == "Activity"):
                $userDetails = "";
                $history = $this->get_robotics_history(filter_input(INPUT_GET, "unit", FILTER_SANITIZE_STRING), 100);
                if(count($history)):
                    foreach($history as $key => $value):
                        if($value["uid"]):
                            $user = $this->hias->get_user($value["uid"]);
                            $userDetails = $user["name"]["value"] . "<br />";
                        endif;
                        if($value["hash"]):
                            $hash = '<a href="' . $this->hias->domain . '/HIASBCH/Explorer/Transaction/' . $value["hash"] . '">' . $value["hash"]  . '</a>';
                        else:
                            $hash = 'NA';
                        endif;

                        $return .= '
                        <tr>
                            <td>
                                <div class="row">
                                    <div class="col-lg-1 col-md-12 col-sm-12 col-xs-12">ID:</div>
                                    <div class="col-lg-11 col-md-12 col-sm-12 col-xs-12">' . $value["id"] . '</div>
                                    <div class="col-lg-1 col-md-12 col-sm-12 col-xs-12">User:</div>
                                    <div class="col-lg-11 col-md-12 col-sm-12 col-xs-12"><a href="/Users/Staff/'.$value["uid"].'">' . $userDetails . '</a></div>
                                    <div class="col-lg-1 col-md-12 col-sm-12 col-xs-12">Action:</div>
                                    <div class="col-lg-11 col-md-12 col-sm-12 col-xs-12">' . $value["action"] . '</div>
                                    <div class="col-lg-1 col-md-12 col-sm-12 col-xs-12">Hash:</div>
                                    <div class="col-lg-11 col-md-12 col-sm-12 col-xs-12">' . $hash . '</div>
                                    <div class="col-lg-1 col-md-12 col-sm-12 col-xs-12">At:</div>
                                    <div class="col-lg-11 col-md-12 col-sm-12 col-xs-12">' . date("Y-m-d H:i:s", $value["time"]) . '</div>
                                </div>
                            </td>
                        </tr>';
                    endforeach;
                    return [
                        "Response" => "OK",
                        "Message" => "Robotics Activity found!",
                        "Data" => $return
                    ];
                else:
                    return [
                        "Response" => "FAILED",
                        "Message" => "Robotics History not found!"
                    ];
                endif;
            elseif(filter_input(INPUT_POST, 'roboticsHistory', FILTER_SANITIZE_STRING) == "Transactions"):
                $transactions = $this->get_robotics_transactions(filter_input(INPUT_POST, "RobotAddress", FILTER_SANITIZE_STRING), 100);
                if($transactions !== False):
                    foreach($transactions as $key => $value):
                        $block = $this->hias->hiasbch->get_block($value["BlockHash"]);

                        if($value['To']):
                            $to = "<a href='/HIASBCH/Explorer/Address/" . $value['To'] . "' title='" . $value['To'] . "'>" . $value['To'] . "</a>";
                        else:
                            $to = "Contract Creation";
                        endif;

                        $return .= "
                        <tr>
                            <td>
                                <div class='row'>
                                    <div class='col-lg-1 col-md-12 col-sm-12 col-xs-12'>Block:</div>
                                    <div class='col-lg-11 col-md-12 col-sm-12 col-xs-12'>
                                        <a href='/HIASBCH/Explorer/Block/" . $value['BlockHash'] . "' title='" . $value['BlockHash'] . "'>" . $value['BlockNumber'] . "</a>
                                    </div>
                                    <div class='col-lg-1 col-md-12 col-sm-12 col-xs-12'>Hash:</div>
                                    <div class='col-lg-11 col-md-12 col-sm-12 col-xs-12'>
                                        <a href='/HIASBCH/Explorer/Transaction/" . $value['Hash'] . "' title='" . $value['Hash'] . "'>" . $value['Hash'] . "</a>
                                    </div>
                                        <div class='col-lg-1 col-md-12 col-sm-12 col-xs-12'>Fee:</div>
                                        <div class='col-lg-11 col-md-12 col-sm-12 col-xs-12'>" . $value['Gas'] * $value['GasPrice'] . " </div>
                                        <div class='col-lg-1 col-md-12 col-sm-12 col-xs-12'>From:</div>
                                        <div class='col-lg-11 col-md-12 col-sm-12 col-xs-12'>
                                            <a href='/HIASBCH/Explorer/Address/" . $value['From'] . "' title='" . $value['From'] . "'>" . $value['From'] . "</a>
                                        </div>
                                        <div class='col-lg-1  col-md-12 col-sm-12 col-xs-12'>To:</div>
                                        <div class='col-lg-11 col-md-12 col-sm-12 col-xs-12'>$to</div>
                                        <div class='col-lg-1 col-md-12 col-sm-12 col-xs-12'>At:</div>
                                        <div class='col-lg-11 col-md-12 col-sm-12 col-xs-12'>
                                            " . date('Y-m-d H:i:s', hexdec($block['Data']->timestamp)) . " (<span style='font-size: 8;'>" . ($this->hias->helpers->time_ago(time()-hexdec($block['Data']->timestamp))) . " ago</span>)
                                    </div>
                                </div>

                            </td>
                        </tr>";
                    endforeach;
                    return [
                        "Response" => "OK",
                        "Message" => "Robotics Transactions found!",
                        "Data" => $return
                    ];
                else:
                    return [
                        "Response" => "FAILED",
                        "Message" => "Robotics Transactions not found!"
                    ];
                endif;
            elseif(filter_input(INPUT_POST, 'roboticsHistory', FILTER_SANITIZE_STRING) == "Statuses"):
                $Statuses = $this->get_robotics_statuses(filter_input(INPUT_GET, "unit", FILTER_SANITIZE_STRING), 100);
                if($Statuses["Response"] == "OK"):
                    foreach($Statuses["ResponseData"] as $key => $value):
                        $return .= "
                        <tr>
                            <td>
                                <div class='row'>
                                    <div class='col-lg-1 col-md-12 col-sm-12 col-xs-12'>ID:</div>
                                    <div class='col-lg-11 col-md-12 col-sm-12 col-xs-12'>" . $value['_id']['$oid'] . "</div>
                                    <div class='col-lg-1 col-md-12 col-sm-12 col-xs-12'>Status:</div>
                                    <div class='col-lg-11 col-md-12 col-sm-12 col-xs-12'>" . $value['Status'] . "</div>
                                    <div class='col-lg-1 col-md-12 col-sm-12 col-xs-12'>At:</div>
                                    <div class='col-lg-11 col-md-12 col-sm-12 col-xs-12'>" . $value['Time'] . "</div>
                                </div>

                            </td>
                        </tr>";
                    endforeach;
                    return [
                        "Response" => "OK",
                        "Message" => "Robotics Statuses found!",
                        "Data" => $return
                    ];
                else:
                    return [
                        "Response" => "FAILED",
                        "Message" => "Robotics Statuses not found!"
                    ];
                endif;
            elseif(filter_input(INPUT_POST, 'roboticsHistory', FILTER_SANITIZE_STRING) == "Life"):
                $Statuses = $this->get_robotics_life(filter_input(INPUT_GET, "unit", FILTER_SANITIZE_STRING), 100);
                if($Statuses["Response"] == "OK"):
                    foreach($Statuses["ResponseData"] as $key => $value):
                        $return .= "
                        <tr>
                            <td>
                                <div class='row'>
                                    <div class='col-lg-2 col-md-12 col-sm-12 col-xs-12'>ID:</div>
                                    <div class='col-lg-10 col-md-12 col-sm-12 col-xs-12'>" . $value['_id']['$oid'] . "</div>
                                    <div class='col-lg-2 col-md-12 col-sm-12 col-xs-12'>CPU:</div>
                                    <div class='col-lg-10 col-md-12 col-sm-12 col-xs-12'>" . $value['Data']['CPU'] . "%</div>
                                    <div class='col-lg-2 col-md-12 col-sm-12 col-xs-12'>Memory:</div>
                                    <div class='col-lg-10 col-md-12 col-sm-12 col-xs-12'>" . $value['Data']['Memory'] . "%</div>
                                    <div class='col-lg-2 col-md-12 col-sm-12 col-xs-12'>Diskspace:</div>
                                    <div class='col-lg-10 col-md-12 col-sm-12 col-xs-12'>" . $value['Data']['Diskspace'] . "%</div>
                                    <div class='col-lg-2 col-md-12 col-sm-12 col-xs-12'>Temperature:</div>
                                    <div class='col-lg-10 col-md-12 col-sm-12 col-xs-12'>" . $value['Data']['Temperature'] . "℃</div>
                                    <div class='col-lg-2 col-md-12 col-sm-12 col-xs-12'>Latitude:</div>
                                    <div class='col-lg-10 col-md-12 col-sm-12 col-xs-12'>" . $value['Data']['Latitude'] . "</div>
                                    <div class='col-lg-2 col-md-12 col-sm-12 col-xs-12'>Longitude:</div>
                                    <div class='col-lg-10 col-md-12 col-sm-12 col-xs-12'>" . $value['Data']['Longitude'] . "</div>
                                </div>

                            </td>
                        </tr>";
                    endforeach;
                    return [
                        "Response" => "OK",
                        "Message" => "Robotics Life found!",
                        "Data" => $return
                    ];
                else:
                    return [
                        "Response" => "FAILED",
                        "Message" => "Robotics Life not found!"
                    ];
                endif;
            elseif(filter_input(INPUT_POST, 'roboticsHistory', FILTER_SANITIZE_STRING) == "Sensors"):
                $Sensors = $this->get_robotics_sensors(filter_input(INPUT_GET, "unit", FILTER_SANITIZE_STRING), 100);
                if($Sensors["Response"] == "OK"):
                    foreach($Sensors["ResponseData"] as $key => $value):
                        $values = "";
                        if(is_array($value["Value"])):
                            foreach($value["Value"] AS $key => $val):
                                $values .= "<strong>" . $key . ":</strong> " . $val . "<br />";
                            endforeach;
                        else:
                            $values = $value["Value"];
                        endif;
                        $return .= "
                        <tr>
                            <td>
                                <div class='row'>
                                    <div class='col-lg-2 col-md-12 col-sm-12 col-xs-12'>ID:</div>
                                    <div class='col-lg-10 col-md-12 col-sm-12 col-xs-12'>" . $value['_id']['$oid'] . "</div>
                                    <div class='col-lg-2 col-md-12 col-sm-12 col-xs-12'>Values:</div>
                                    <div class='col-lg-10 col-md-12 col-sm-12 col-xs-12'>" . $values . "</div>
                                    <div class='col-lg-2 col-md-12 col-sm-12 col-xs-12'>Message:</div>
                                    <div class='col-lg-10 col-md-12 col-sm-12 col-xs-12'>" . $value['Message'] . "</div>
                                    <div class='col-lg-2 col-md-12 col-sm-12 col-xs-12'>At:</div>
                                    <div class='col-lg-10 col-md-12 col-sm-12 col-xs-12'>" . $value['Time'] . "</div>
                                </div>

                            </td>
                        </tr>";
                    endforeach;
                    return [
                        "Response" => "OK",
                        "Message" => "Robotics Sensors found!",
                        "Data" => $return
                    ];
                else:
                    return [
                        "Response" => "FAILED",
                        "Message" => "Robotics Sensors not found!"
                    ];
                endif;
            elseif(filter_input(INPUT_POST, 'roboticsHistory', FILTER_SANITIZE_STRING) == "Actuators"):
                $Sensors = $this->get_robotics_actuators(filter_input(INPUT_GET, "unit", FILTER_SANITIZE_STRING), 100);
                if($Sensors["Response"] == "OK"):
                    foreach($Sensors["ResponseData"] as $key => $value):
                        $values = "";
                        if(is_array($value["Value"])):
                            foreach($value["Value"] AS $key => $val):
                                $values .= "<strong>" . $key . ":</strong> " . $val . "<br />";
                            endforeach;
                        else:
                            $values = $value["Value"];
                        endif;

                        $return .= "
                        <tr>
                            <td>
                                <div class='row'>
                                    <div class='col-lg-2 col-md-12 col-sm-12 col-xs-12'>ID:</div>
                                    <div class='col-lg-10 col-md-12 col-sm-12 col-xs-12'>" . $value['_id']['$oid'] . "</div>
                                    <div class='col-lg-2 col-md-12 col-sm-12 col-xs-12'>Values:</div>
                                    <div class='col-lg-10 col-md-12 col-sm-12 col-xs-12'>" . $values . "</div>
                                    <div class='col-lg-2 col-md-12 col-sm-12 col-xs-12'>Message:</div>
                                    <div class='col-lg-10 col-md-12 col-sm-12 col-xs-12'>" . $value['Message'] . "</div>
                                    <div class='col-lg-2 col-md-12 col-sm-12 col-xs-12'>At:</div>
                                    <div class='col-lg-10 col-md-12 col-sm-12 col-xs-12'>" . $value['Time'] . "</div>
                                </div>

                            </td>
                        </tr>";
                    endforeach;
                    return [
                        "Response" => "OK",
                        "Message" => "Robotics Actuators found!",
                        "Data" => $return
                    ];
                else:
                    return [
                        "Response" => "FAILED",
                        "Message" => "Robotics Actuators not found!"
                    ];
                endif;
            else:
                return [
                    "Response" => "FAILED",
                    "Message" => "Robotics History not found!"
                ];
            endif;
        }

        public function reset_api_key()
        {
            $id = filter_input(INPUT_GET, 'unit', FILTER_SANITIZE_STRING);
            $unit = $this->get_robotic($id);

            $privKey = $this->hias->helpers->generate_key(32);
            $privKeyHash = $this->hias->helpers->password_hash($privKey);

            $htpasswd = new Htpasswd('/etc/nginx/security/htpasswd');
            $htpasswd->updateUser(filter_input(INPUT_GET, 'unit', FILTER_SANITIZE_STRING), $privKey, Htpasswd::ENCTYPE_APR_MD5);

            $data = [
                "authenticationKey" => [
                    "value" => $this->hias->helpers->oEncrypt($privKeyHash),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Private key for accessing the network APIs"
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

            $request = $this->hias->hiascdi->request("POST", $this->hias->hiascdi->confs["entities_url"] . $unit["id"] . "/attrs?type=Robotics", json_encode($data));
            $response = json_decode($request["body"], true);
            if(!isSet($response["Error"])):
                $this->hias->store_user_history("Reset Robotics Key", 0, $unit["networkLocation"]["value"], "", $id);
                return [
                    "Response"=> "OK",
                    "Message" => "Robotics key reset!",
                    "P" => $privKey
                ];
            else:
                return [
                    "Response"=> "FAILED",
                    "Message" => "Robotics key reset failed!"
                ];
            endif;

        }

        public function reset_mqtt_pass()
        {
            $id = filter_input(INPUT_GET, 'unit', FILTER_SANITIZE_STRING);
            $unit = $this->get_robotic($id);

            $mqttPass = $this->hias->helpers->password();
            $mqttHash = create_hash($mqttPass);

            $data = [
                "authenticationMqttKey" => [
                    "value" => $this->hias->helpers->oEncrypt($mqttPass),
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "MQTT password"
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

            $request = $this->hias->hiascdi->request("POST", $this->hias->hiascdi->confs["entities_url"] . "/" . $unit["id"] . "/attrs?type=Robotics", json_encode($data));
            $response = json_decode($request["body"], true);
            if(!isSet($response["Error"])):
                $query = $this->hias->conn->prepare("
                    UPDATE mqttu
                    SET pw = :pw
                    WHERE uname = :uname
                ");
                $query->execute(array(
                    ':pw' => $mqttHash,
                    ':uname' => $unit["authenticationMqttUser"]["value"]
                ));

                $this->hias->store_user_history("Reset Device MQTT Password", 0, $unit["networkLocation"]["value"], "", $id);

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

        public function reset_amqp_pass()
        {
            $id = filter_input(INPUT_GET, 'device', FILTER_SANITIZE_NUMBER_INT);
            $Device = $this->get_device($id);

            $amqpPass = $this->hias->helpers->password();
            $amqpHash = $this->hias->helpers->password_hash($amqpPass);

            $data = [
                "amqp" => [
                    "username" => $Device["amqp"]["username"],
                    "password" => $this->hias->helpers->oEncrypt($amqpPass),
                    "timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
                ],
                "dateModified" => [
                    "type" => "DateTime",
                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                ]
            ];

            $response = json_decode($this->hias->hiascdi->request("POST", $this->hias->hiascdi->confs["entities_url"] . "/" . $Device["id"] . "/attrs?type=Device", json_encode($data)), true);

            if($response["Response"]=="OK"):
                $query = $this->hias->conn->prepare("
                    UPDATE amqpu
                    SET pw = :pw
                    WHERE username = :username
                ");
                $query->execute(array(
                    ':pw' => $this->hias->helpers->oEncrypt($amqpHash),
                    ':username' => $this->hias->helpers->oDecrypt($Device["amqp"]["username"])
                ));

                $this->hias->store_user_history("Reset Device AMQP Key", 0, $Device["lid"]["value"], $Device["zid"]["value"], $id);

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

    }

    $Robotics = new Robotics($HIAS, $iotJumpWay);

    if(filter_input(INPUT_POST, "create_robotics", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($Robotics->create_robotics()));
    endif;
    if(filter_input(INPUT_POST, "update_robotics", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($Robotics->update_robotics()));
    endif;
    if(filter_input(INPUT_POST, "reset_emar_key", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($Robotics->reset_api_key()));
    endif;
    if(filter_input(INPUT_POST, "reset_emar_mqtt", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($Robotics->reset_mqtt_pass()));
    endif;
    if(filter_input(INPUT_POST, "update_robotics_sensors_graph", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($Robotics->update_robotics_sensors_graph()));
    endif;
    if(filter_input(INPUT_POST, "update_robotics_life_graph", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($Robotics->update_robotics_life_graph()));
    endif;
    if(filter_input(INPUT_POST, "update_robotics_history", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($Robotics->update_robotics_history()));
    endif;
    if(filter_input(INPUT_POST, "uploadAllData", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($Robotics->upload_data()));
    endif;
    if(filter_input(INPUT_POST, "deleteData", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($Robotics->delete_data()));
    endif;
    if(filter_input(INPUT_POST, "classifyData", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($Robotics->infer()));
    endif;