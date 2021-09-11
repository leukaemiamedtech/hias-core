<?php

require __DIR__ . '/../../../../vendor/autoload.php';

use Web3\Web3;
use Web3\Contract;
use Web3\Utils;

    class Staff
    {
        private $lat = 41.5463;
        private $lng = 2.1086;

        function __construct($hias, $iotJumpWay)
        {
            $this->hias = $hias;
            $this->iotJumpWay = $iotJumpWay;

            $this->ldapdc = "dc=".$this->hias->_ldapdc1.",dc=".$this->hias->_ldapdc2.",dc=".$this->hias->_ldapdc3;
        }

        public function get_staff_categories()
        {
            $pdoQuery = $this->hias->conn->prepare("
                SELECT id,
                    category
                FROM user_cats
                ORDER BY id ASC
            ");
            $pdoQuery->execute();
            $categories=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
            $pdoQuery->closeCursor();
            $pdoQuery = null;
            return $categories;
        }

        public function get_staff_members($limit = 0, $order = "id DESC", $attrs = Null, $values = Null)
        {
            $limiter = "";
            if($limit != 0):
                $limiter = "&limit=" . $limit;
            endif;

            if($attrs):
                $attrs="&attrs=" . $attrs;
            endif;

            if($values):
                $values="&q=" . $values;
            endif;

            $request = $this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "?type=Staff".$attrs.$limiter.$values, []);
            $staff = json_decode($request["body"], true);
            return $staff;
        }

        public function get_staff($id, $attrs = Null)
        {
            if($attrs):
                $attrs="&attrs=" . $attrs;
            endif;

            $request = $this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "/" . $id . "?type=Staff".$attrs, []);
            $staff = json_decode($request["body"], true);
            return $staff;
        }

        public function check_username($username)
        {
            $request = $this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "?type=Staff&q=username.value==".$username, []);
            $staff = json_decode($request["body"], true);
            if(isSet($staff["Error"])):
                return False;
            else:
                return True;
            endif;
        }

        public function check_email($email)
        {
            $request = $this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "?type=Staff&q=email.value==".$email, []);
            $staff = json_decode($request["body"], true);
            if(isSet($staff["Error"])):
                return False;
            else:
                return True;
            endif;
        }

        public function create_staff()
        {

            if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "iotJumpWay location id is required"
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
                    "Message" => "Staff first name is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "sname", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Staff surname is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Staff username is required"
                ];
            endif;

            if($this->check_username(filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING))):
                return [
                    "Response"=> "Failed",
                    "Message" => "Username exists"
                ];
            endif;

            if(!filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Staff email is required"
                ];
            endif;

            if($this->check_email(filter_input(INPUT_POST, "email", FILTER_SANITIZE_STRING))):
                return [
                    "Response"=> "Failed",
                    "Message" => "Email exists"
                ];
            endif;

            if(!filter_input(INPUT_POST, "streetAddress", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Location street address is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "addressLocality", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Location address locality is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "postalCode", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Location postal code is required"
                ];
            endif;

            $ip = filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING);
            $mac = filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING);
            $bt = filter_input(INPUT_POST, "bluetooth", FILTER_SANITIZE_STRING);
            $nfc = filter_input(INPUT_POST, "nfc", FILTER_SANITIZE_STRING);
            $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
            $admin = filter_input(INPUT_POST, "admin", FILTER_SANITIZE_NUMBER_INT) ? True : False;

            $pubKey = $this->hias->helpers->generate_uuid();
            $privKey = $this->hias->helpers->generate_key(32);
            $privKeyHash = $this->hias->helpers->password_hash($privKey);

            $category = explode(",", filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING));

            $unlocked =  $this->hias->hiasbch->unlock_account($_SESSION["HIAS"]["BC"]["BCUser"],
                                                              $this->hias->helpers->oDecrypt($_SESSION["HIAS"]["BC"]["BCPass"]));

            if($unlocked == "FAILED"):
                return [
                    "Response"=> "Failed",
                    "Message" => "Unlocking HIAS Blockhain Account Failed!"
                ];
            endif;

            $ds = $this->hias->connect_to_ldap();
            if($ds):

                $binddn = "cn=admin,".$this->hias->ldapdc;

                ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
                ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);

                if (ldap_bind($ds, $binddn, $this->hias->_ldaps)):

                    $info["cn"] = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);
                    $info["uid"] = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);
                    $info["givenName"] = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
                    $info["surname"] = filter_input(INPUT_POST, "sname", FILTER_SANITIZE_STRING);
                    $info['objectclass'][0] = "inetOrgPerson";
                    $info['objectclass'][1] = "posixAccount";
                    $info['objectclass'][2] = "top";
                    $info['gidnumber'] = $category[0];
                    $info['uidnumber'] = $this->hias->find_ldap_uid($ds) + 1;
                    $info["userPassword"] = $this->hias->ldap_ssha_password($privKey);
                    $info["homeDirectory"] = "/fserver/ldap/".filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);

                    $r = ldap_add($ds,"cn=".$info["cn"].",ou=users,".$this->hias->ldapdc, $info);
                    $sr = ldap_search($ds,$this->hias->ldapdc,"cn=".$info["cn"]);
                    $info = ldap_get_entries($ds,$sr);

                    $mqttUser = $this->hias->helpers->generate_uuid();
                    $mqttPass = $this->hias->helpers->password();
                    $mqttHash = create_hash($mqttPass);

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

                    $lid = filter_input(INPUT_POST, "lid", FILTER_SANITIZE_STRING);
                    $location = $this->iotJumpWay->get_location($lid);

                    $admin = filter_input(INPUT_POST, "admin", FILTER_SANITIZE_NUMBER_INT) ? True : False;
                    $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING) . " " . filter_input(INPUT_POST, "sname", FILTER_SANITIZE_STRING);

                    $htpasswd = new Htpasswd('/etc/nginx/security/htpasswd');
                    $htpasswd->addUser(filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING), $privKey, Htpasswd::ENCTYPE_APR_MD5);

                    $data = [
                        "id" => $pubKey,
                        "type" => "Staff",
                        "category" => [
                            "value" => $category
                        ],
                        "name" => [
                            "value" => $name
                        ],
                        "description" => [
                            "value" => filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)
                        ],
                        "address" => [
                            "type" => "PostalAddress",
                            "value" => [
                                "addressLocality" => filter_input(INPUT_POST, "addressLocality", FILTER_SANITIZE_STRING),
                                "postalCode" => filter_input(INPUT_POST, "postalCode", FILTER_SANITIZE_STRING),
                                "streetAddress" => filter_input(INPUT_POST, "streetAddress", FILTER_SANITIZE_STRING)
                            ]
                        ],
                        "username" => [
                            "value" => filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING)
                        ],
                        "email" => [
                            "value" => filter_input(INPUT_POST, "email", FILTER_SANITIZE_STRING)
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
                                    "value" => "IP address of user"
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
                                    "value" => "MAC address of user"
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
                                    "value" => "Bluetooth address of user"
                                ],
                                "timestamp" =>   [
                                    "type" => "DateTime",
                                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                                ]
                            ]
                        ],
                        "nfcAddress" => [
                            "value" => $nfc,
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

                    $request = $this->hias->hiascdi->request("POST", $this->hias->hiascdi->confs["entities_url"] . "?type=Staff", json_encode($data));
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
                            ':topic' => $location["id"] . "/#",
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

                        if($admin):
                            $this->iotJumpWay->addAmqpUserPerm($amid, "administrator");
                            $this->iotJumpWay->addAmqpUserPerm($amid, "managment");
                            $this->iotJumpWay->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Life", "configure");
                            $this->iotJumpWay->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Statuses", "configure");
                        endif;

                        $actionMsg = "";
                        $balanceMessage = "";

                        $this->hias->hiasbch->icontract->at($this->hias->helpers->oDecrypt($this->hias->hiasbch->confs["icontract"]))->send("registerAuthorized", $newBcUser, ["from" => $_SESSION["HIAS"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
                            if ($err !== null) {
                                $hash = "FAILED";
                                $msg = $err;
                                return;
                            }
                            $hash = $resp;
                        });

                        if($hash == "FAILED"):
                            $actionMsg .= " HIAS Blockchain registerAuthorized failed!\n";
                        else:
                            $this->hias->store_user_history("HIASBCH registerAuthorized (User)", $hash, 0, $location["id"], "", "",  "",  "",  "",  $pubKey);
                            $balance = $this->hias->hiasbch->check_balance($_SESSION["HIAS"]["BC"]["BCUser"], $this->hias->helpers->oDecrypt($_SESSION["HIAS"]["BC"]["BCPass"]));
                            $actionMsg = " HIASBCH registerAuthorized (User)  OK!\n";
                            $balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
                        endif;

                        $hash = "";
                        $this->hias->hiasbch->contract->at($this->hias->hiasbch->confs["contract"])->send("registerUser", $pubKey, $newBcUser, $admin, filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING), $location["id"], time(), $_SESSION["HIAS"]["Uid"], ["from" => $_SESSION["HIAS"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash) {
                            if ($err !== null) {
                                $hash = "FAILED";
                                $msg = $err;
                                return;
                            }
                            $hash = $resp;
                        });

                        if($hash == "FAILED"):
                            $actionMsg .= " HIAS Blockchain registerUser failed!\n";
                        else:
                            $this->hias->store_user_history("HIASBCH registerUser", $hash, 0, $location["id"], "", "",  "",  "",  "",  $pubKey);
                            $balance = $this->hias->hiasbch->check_balance($_SESSION["HIAS"]["BC"]["BCUser"], $this->hias->helpers->oDecrypt($_SESSION["HIAS"]["BC"]["BCPass"]));
                            $actionMsg = " HIASBCH registerUser OK!\n";
                            $balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
                        endif;

                        return [
                            "Response"=> "OK",
                            "Message" => "Staff & application created!" . $actionMsg . $balanceMessage,
                            "UID" => $pubKey,
                            "Uname" => filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING),
                            "Upass" => $privKey,
                            "BCU" => $newBcUser,
                            "BCP" => $bcPass,
                            "MU" => $mqttUser,
                            "MP" => $mqttPass
                        ];
                    else:
                        return [
                            "Response"=> "FAILED",
                            "Message" => "User creation failed!"
                        ];
                    endif;

                else:
                    return [
                        "Response"=> "Failed",
                        "Message" => "Could not bind to LDAP server."
                    ];
                endif;

            else:
                return [
                    "Response"=> "Failed",
                    "Message" => "Could not connect to LDAP server."
                ];
            endif;
        }

        public function update_staff()
        {
            if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "iotJumpWay location id is required"
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
                    "Message" => "Staff name is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Staff username is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Staff email is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "streetAddress", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Location street address is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "addressLocality", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Location address locality is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "postalCode", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Location postal code is required"
                ];
            endif;

            $sid = filter_input(INPUT_GET, 'staff', FILTER_SANITIZE_STRING);
            $staff = $this->get_staff($sid, "dateCreated,dateModified,*");

            if(isSet($staff["Error"])):
                return [
                    "Response"=> "Failed",
                    "Message" => "This user does not exist."
                ];
            endif;

            if($staff["permissionsCancelled"]["value"]):
                return [
                    "Response"=> "Failed",
                    "Message" => "This user is cancelled, to allow access again you must create a new user."
                ];
            endif;

            $unlocked =  $this->hias->hiasbch->unlock_account($_SESSION["HIAS"]["BC"]["BCUser"], $this->hias->helpers->oDecrypt($_SESSION["HIAS"]["BC"]["BCPass"]));

            if($unlocked == "FAILED"):
                return [
                    "Response"=> "Failed",
                    "Message" => "Unlocking HIAS Blockhain Account Failed!"
                ];
            endif;

            $lid = filter_input(INPUT_POST, "lid", FILTER_SANITIZE_STRING);
            $location = $this->iotJumpWay->get_location($lid);
            $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
            $ip = filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING);
            $mac = filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING);
            $bt = filter_input(INPUT_POST, "bluetooth", FILTER_SANITIZE_STRING);
            $nfc = filter_input(INPUT_POST, "nfc", FILTER_SANITIZE_STRING);
            $admin = filter_input(INPUT_POST, "admin", FILTER_SANITIZE_NUMBER_INT) ? True : False;
            $cancelled = filter_input(INPUT_POST, "cancelled", FILTER_SANITIZE_STRING) ? True : False;
            $allowed = filter_input(INPUT_POST, "cancelled", FILTER_SANITIZE_STRING) ? False : True;

            $data = [
                "category" => [
                    "value" => [filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)]
                ],
                "name" => [
                    "value" => $name
                ],
                "description" => [
                    "value" => filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)
                ],
                "username" => [
                    "value" => filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING)
                ],
                "email" => [
                    "value" => filter_input(INPUT_POST, "email", FILTER_SANITIZE_STRING)
                ],
                "address" => [
                    "type" => "PostalAddress",
                    "value" => [
                        "addressLocality" => filter_input(INPUT_POST, "addressLocality", FILTER_SANITIZE_STRING),
                        "postalCode" => filter_input(INPUT_POST, "postalCode", FILTER_SANITIZE_STRING),
                        "streetAddress" => filter_input(INPUT_POST, "streetAddress", FILTER_SANITIZE_STRING)
                    ]
                ],
                "ipAddress" => [
                    "value" => $ip,
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
                    "value" => $mac,
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
                    "value" => $bt,
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
                    "value" => $nfc,
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
                        ]
                    ]
                ],
                "permissionsCancelled" => [
                    "value" => $cancelled,
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Is cancelled"
                        ]
                    ]
                ],
                "dateModified" => [
                    "type" => "DateTime",
                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                ]
            ];

            $request = $this->hias->hiascdi->request("PATCH", $this->hias->hiascdi->confs["entities_url"] . "/" . $staff["id"] . "/attrs?type=Staff", json_encode($data));
            $response = json_decode($request["body"], true);
            if(!isSet($response["Error"])):

                $actionMsg = "";
                $balanceMessage = "";

                $hash = "";
                $this->hias->hiasbch->contract->at($this->hias->hiasbch->confs["contract"])->send("updateUser", $staff["id"], "User", $allowed, $admin, $name, $location["id"], time(), ["from" => $_SESSION["HIAS"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash) {
                    if ($err !== null) {
                        $hash = "FAILED! " . $err;
                        return;
                    }
                    $hash = $resp;
                });

                if($hash == "FAILED"):
                    $actionMsg .= " HIAS Blockchain updateUser failed!\n";
                else:
                    $this->hias->store_user_history("HIASBCH updateUser", $hash, 0, $location["id"], "", "",  "",  "",  "",  $sid);
                    $balance = $this->hias->hiasbch->check_balance($_SESSION["HIAS"]["BC"]["BCUser"], $this->hias->helpers->oDecrypt($_SESSION["HIAS"]["BC"]["BCPass"]));
                    $actionMsg = " HIASBCH updateUser  OK!\n";
                    $balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
                endif;

                if(!$staff["permissionsCancelled"]["value"] && filter_input(INPUT_POST, "cancelled", FILTER_SANITIZE_STRING)):

                    $query = $this->hias->conn->prepare("
                        DELETE FROM mqttu
                        WHERE uname = :uname
                    ");
                    $query->execute([
                        ':uname' => $this->hias->helpers->oDecrypt($staff["authenticationMqttUser"]["value"])
                    ]);

                    $query = $this->hias->conn->prepare("
                        DELETE FROM mqttua
                        WHERE username = :username
                    ");
                    $query->execute([
                        ':username' => $this->hias->helpers->oDecrypt($staff["authenticationMqttUser"]["value"])
                    ]);

                    $query = $this->hias->conn->prepare("
                        SELECT *
                        FROM amqpu
                        WHERE username = :username
                    ");
                    $query->execute([
                        ':username' => $this->hias->helpers->oDecrypt($staff["authenticationAmqpUser"]["value"])
                    ]);
                    $amqp=$query->fetch(PDO::FETCH_ASSOC);

                    $query = $this->hias->conn->prepare("
                        DELETE FROM amqpu
                        WHERE username = :username
                    ");
                    $query->execute([
                        ':username' => $this->hias->helpers->oDecrypt($staff["authenticationAmqpUser"]["value"])
                    ]);

                    $query = $this->hias->conn->prepare("
                        DELETE FROM amqpp
                        WHERE uid = :uid
                    ");
                    $query->execute([
                        ':uid' => $amqp["id"]
                    ]);

                    $query = $this->hias->conn->prepare("
                        DELETE FROM amqpvh
                        WHERE uid = :uid
                    ");
                    $query->execute([
                        ':uid' => $amqp["id"]
                    ]);

                    $query = $this->hias->conn->prepare("
                        DELETE FROM amqpvhr
                        WHERE uid = :uid
                    ");
                    $query->execute([
                        ':uid' => $amqp["id"]
                    ]);

                    $query = $this->hias->conn->prepare("
                        DELETE FROM amqpvhrt
                        WHERE uid = :uid
                    ");
                    $query->execute([
                        ':uid' => $amqp["id"]
                    ]);

                    $hash = "";
                    $this->hias->hiasbch->contract->at($this->hias->hiasbch->confs["contract"])->send("deregsiter", "User", $staff["id"], ["from" => $_SESSION["HIAS"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash) {
                        if ($err !== null) {
                            $hash = "FAILED! " . $err;
                            return;
                        }
                        $hash = $resp;
                    });

                    if($hash == "FAILED"):
                        $actionMsg .= "\nHIAS Blockchain deregsiter user failed!\n";
                    else:
                        $this->hias->store_user_history("HIASBCH deregsiter", $hash, 0, $location["id"], "", "",  "",  "",  "",  $sid);
                        $balance = $this->hias->hiasbch->check_balance($_SESSION["HIAS"]["BC"]["BCUser"], $this->hias->helpers->oDecrypt($_SESSION["HIAS"]["BC"]["BCPass"]));
                        if($balanceMessage == ""):
                            $balanceMessage = "\nYou were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
                        endif;
                    endif;

                    $this->hias->hiasbch->icontract->at($this->hias->helpers->oDecrypt($this->hias->hiasbch->confs["icontract"]))->send("deregisterAuthorized", $staff["authenticationBlockchainUser"]["value"], ["from" => $_SESSION["HIAS"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
                        if ($err !== null) {
                            $hash = "FAILED";
                            $msg = $err;
                            return;
                        }
                        $hash = $resp;
                    });

                    if($hash == "FAILED"):
                        $actionMsg .= "\nHIAS Blockchain deregisterAuthorized failed!\n";
                    else:
                        $this->hias->store_user_history("HIASBCH deregisterAuthorized", $hash, 0, $location["id"], "", "",  "",  "",  "",  $sid);
                        $balance = $this->hias->hiasbch->check_balance($_SESSION["HIAS"]["BC"]["BCUser"], $this->hias->helpers->oDecrypt($_SESSION["HIAS"]["BC"]["BCPass"]));
                        if($balanceMessage == ""):
                            $balanceMessage = "\nYou were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
                        endif;
                    endif;

                endif;

                $staff = $this->get_staff($sid, "dateCreated,dateModified,*");

                return [
                    "Response"=> "OK",
                    "Message" => "Staff updated!" . $actionMsg . $balanceMessage,
                    "Schema" => $staff
                ];
            else:
                return [
                    "Response"=> "FAILED",
                    "Message" => "Staff update failed!"
                ];
            endif;
        }

        public function reset_user_password()
        {
            $pass = $this->hias->helpers->password();

            $sid = filter_input(INPUT_GET, 'staff', FILTER_SANITIZE_STRING);
            $staff = $this->get_staff($sid);

            $ds = $this->hias->connect_to_ldap();

            $binddn = "cn=admin,".$this->hias->ldapdc;

            ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);

            if (ldap_bind($ds, $binddn, $this->hias->_ldaps)):
                $dn = "cn=".$staff["username"]["value"].",ou=users,".$this->hias->ldapdc;
                $newPass = ['userpassword' => $this->hias->ldap_ssha_password($pass)];

                if(ldap_mod_replace($ds, $dn, $newPass)):
                    $htpasswd = new Htpasswd('/etc/nginx/security/htpasswd');
                    $htpasswd->updateUser($staff["username"]["value"], $pass, Htpasswd::ENCTYPE_APR_MD5);
                    $this->hias->store_user_history("Reset staff password", 0, 0, $staff["networkLocation"]["value"], "", "",  "", "", $sid);
                    return [
                        "Response" => "OK",
                        "pw" => $pass
                    ];
                else:
                    $this->hias->store_user_history("Failed reset staff password", 0, 0, $staff["networkLocation"]["value"], "", "",  "", "", $sid);
                    return [
                        "Response" => "FAILED"
                    ];
                endif;
            else:
                $this->hias->store_user_history("Failed reset staff password", 0, 0, $staff["networkLocation"]["value"], "", "",  "", "", $sid);
                return [
                    "Response" => "FAILED"
                ];
            endif;


        }

        public function reset_user_key()
        {
            $sid = filter_input(INPUT_GET, 'staff', FILTER_SANITIZE_STRING);
            $staff = $this->get_staff($sid);

            $privKey = $this->hias->helpers->generate_key(32);
            $privKeyHash = $this->hias->helpers->password_hash($privKey);

            $data = [
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
                "dateModified" => [
                    "type" => "DateTime",
                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                ]
            ];

            $request = $this->hias->hiascdi->request("PATCH", $this->hias->hiascdi->confs["entities_url"] . "/" . $staff["id"] . "/attrs?type=Staff", json_encode($data));
            $response = json_decode($request["body"], true);
            if(!isSet($response["Error"])):

                $this->hias->store_user_history("Reset staff key", 0, 0, $staff["networkLocation"]["value"], "", "",  "", "", $sid);

                return [
                    "Response"=> "OK",
                    "Message" => "Reset Private API Key!",
                    "P" => $privKey
                ];
            else:
                return [
                    "Response"=> "FAILED",
                    "Message" => "Reset Private API Key failed!"
                ];
            endif;
        }

        public function reset_user_mqtt_key()
        {
            $sid = filter_input(INPUT_GET, 'staff', FILTER_SANITIZE_STRING);
            $staff = $this->get_staff($sid);

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

            $request = $this->hias->hiascdi->request("PATCH", $this->hias->hiascdi->confs["entities_url"] . "/" . $staff["id"] . "/attrs?type=Staff", json_encode($data));
            $response = json_decode($request["body"], true);
            if(!isSet($response["Error"])):

                $query = $this->hias->conn->prepare("
                    UPDATE mqttu
                    SET pw = :pw
                    WHERE uname = :uname
                ");
                $query->execute(array(
                    ':pw' => $mqttHash,
                    ':uname' => $staff["username"]["value"]
                ));

                $this->hias->store_user_history("Reset staff MQTT key", 0, 0, $staff["networkLocation"]["value"], "", "",  "", "", $sid);

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

        public function reset_user_amqp_key()
        {
            $sid = filter_input(INPUT_GET, 'staff', FILTER_SANITIZE_STRING);
            $staff = $this->get_staff($sid);

            $amqpPass = $this->hias->helpers->password();
            $amqpHash = $this->hias->helpers->password_hash($amqpPass);

            $data = [
                "authenticationAmqpKey" => [
                    "value" => $this->hias->helpers->oEncrypt($amqpPass),
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
                "dateModified" => [
                    "type" => "DateTime",
                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                ]
            ];

            $request = $this->hias->hiascdi->request("PATCH", $this->hias->hiascdi->confs["entities_url"] . "/" . $staff["id"] . "/attrs?type=Staff", json_encode($data));
            $response = json_decode($request["body"], true);
            if(!isSet($response["Error"])):

                $query = $this->hias->conn->prepare("
                    UPDATE amqpu
                    SET pw = :pw
                    WHERE username = :username
                ");
                $query->execute(array(
                    ':pw' => $this->hias->helpers->oEncrypt($amqpHash),
                    ':username' => $this->hias->helpers->oDecrypt($staff["authenticationAmqpUser"]["value"])
                ));

                $this->hias->store_user_history("Reset staff AMQP key", 0, 0, $staff["networkLocation"]["value"], "", "",  "", "", $sid);

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

        public function get_user_history($user, $limit = 0, $order = "")
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
                WHERE uid = :id
                    || tuid = :tuid
                $orderer
                $limiter
            ");
            $pdoQuery->execute([
                ":id" => $user,
                ":tuid" => $user
            ]);
            $response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
            return $response;
        }

        public function get_user_transactions($user, $limit = 0, $order = "")
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

            $request = $this->hias->hiashdi->request("GET", $this->hias->hiashdi->confs["data_url"] . "?type=Transactions&q=From==" . $user . $orderer . $limiter, []);
            $response = json_decode($request["body"], true);

            if(!isSet($response["Error"])):
                return  $response;
            else:
                return False;
            endif;
        }

        public function get_user_statuses($staff, $limit = 0, $order = "")
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

            $request = $this->hias->hiashdi->request("GET", $this->hias->hiashdi->confs["data_url"] . "?type=Statuses&q=Use==Staff;Staff==". $staff . $limiter . $orderer, []);
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

        public function get_staff_life()
        {
            $staff = $this->get_staff(filter_input(INPUT_GET, 'staff', FILTER_SANITIZE_STRING), "batteryLevel,cpuUsage,memoryUsage,hddUsage,temperature,networkStatus");

            if(!isSet($staff["Error"])):
                $response = [
                    "status" => $staff["networkStatus"]["value"]
                ];
                return  [
                    'Response' => 'OK',
                    'ResponseData' => $response
                ];
            else:
                return  [
                    'Response'=>'FAILED'
                ];
            endif;
        }

        public function update_staff_history()
        {
            $return = "";
            if(filter_input(INPUT_POST, 'staffHistory', FILTER_SANITIZE_STRING) == "Activity"):
                $userDetails = "";
                $history = $this->get_user_history(filter_input(INPUT_GET, "staff", FILTER_SANITIZE_STRING), 100);
                if(count($history)):
                    foreach($history as $key => $value):
                            if($value["uid"]):
                                $user = $this->hias->get_user($value["uid"]);
                                $userDetails = $user["name"]["value"];
                            endif;
                            if($value["hash"]):
                                $hash = '<a href="' . $this->hias->domain . '/HIASBCH/Transaction/' . $value["hash"] . '">' . $value["hash"] . '</a>';
                            else:
                                $hash = 'NA';
                            endif;

                            $return .= '<tr>
                                            <td>

                                                <div class="row">
                                                    <div class="col-lg-1 col-md-12 col-sm-12 col-xs-12">ID:</div>
                                                    <div class="col-lg-11 col-md-12 col-sm-12 col-xs-12">' . $value["id"].'</div>
                                                    <div class="col-lg-1 col-md-12 col-sm-12 col-xs-12">User:</div>
                                                    <div class="col-lg-11 col-md-12 col-sm-12 col-xs-12"><a href="/Users/Staff/' . $value["uid"].'">' . $userDetails.'</a></div>
                                                    <div class="col-lg-1 col-md-12 col-sm-12 col-xs-12">Action:</div>
                                                    <div class="col-lg-11 col-md-12 col-sm-12 col-xs-12">' . $value["action"].'</div>
                                                    <div class="col-lg-1 col-md-12 col-sm-12 col-xs-12">Hash:</div>
                                                    <div class="col-lg-11 col-md-12 col-sm-12 col-xs-12">'. $hash  .'</div>
                                                    <div class="col-lg-1 col-md-12 col-sm-12 col-xs-12">At:</div>
                                                    <div class="col-lg-11 col-md-12 col-sm-12 col-xs-12">' . date("Y-m-d H:i:s", $value["time"]).'</div>
                                                </div>

                                            </td>
                                        </tr>';
                    endforeach;
                    return [
                        "Response" => "OK",
                        "Message" => "Staff Activity found!",
                        "Data" => $return
                    ];
                else:
                    return [
                        "Response" => "FAILED",
                        "Message" => "Staff History not found!"
                    ];
                endif;
            elseif(filter_input(INPUT_POST, 'staffHistory', FILTER_SANITIZE_STRING) == "Transactions"):
                $transactions = $this->get_user_transactions(filter_input(INPUT_GET, "staff", FILTER_SANITIZE_STRING), 100);
                if($transactions !== False):
                    foreach($transactions as $key => $value):
                        if($value['To']):
                            $to = "<a href='/HIASBCH/Explorer/Address/" . $value['To'] . "' title='" . $value['To'] . "'>" . $value['To'] . "</a>";
                        else:
                            $to = "Contract Creation";
                        endif;

                        $return .= " <tr>
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
                        "Message" => "Staff Transactions found!",
                        "Data" => $return
                    ];
                else:
                    return [
                        "Response" => "FAILED",
                        "Message" => "Staff Transactions not found!"
                    ];
                endif;
            elseif(filter_input(INPUT_POST, 'staffHistory', FILTER_SANITIZE_STRING) == "Statuses"):
                $Statuses = $this->get_user_statuses(filter_input(INPUT_GET, "staff", FILTER_SANITIZE_STRING), 100);
                if($Statuses["Response"] == "OK"):
                    foreach($Statuses["ResponseData"] as $key => $value):
                        $return .= "<tr>
                                        <td>#" . $value['_id']['$oid'] . "</td>
                                        <td>" . $value["Status"] . "</td>
                                        <td>" . $value["Time"] . "</td>
                                    </tr>";
                    endforeach;
                    return [
                        "Response" => "OK",
                        "Message" => "Staff Statuses found!",
                        "Data" => $return
                    ];
                else:
                    return [
                        "Response" => "FAILED",
                        "Message" => "Staff Statuses not found!"
                    ];
                endif;
            else:
                return [
                    "Response" => "FAILED",
                    "Message" => "Staff History not found!"
                ];
            endif;
        }

    }

    $Staff = new Staff($HIAS, $iotJumpWay);

    if(filter_input(INPUT_POST, "update_staff", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($Staff->update_staff()));
    endif;

    if(filter_input(INPUT_POST, "reset_appkey_staff", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($Staff->reset_user_key()));
    endif;

    if(filter_input(INPUT_POST, "create_staff", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($Staff->create_staff()));
    endif;

    if(filter_input(INPUT_POST, "reset_mqtt_staff", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($Staff->reset_user_mqtt_key()));
    endif;

    if(filter_input(INPUT_POST, "reset_u_pass", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($Staff->reset_user_password()));
    endif;

    if(filter_input(INPUT_POST, "reset_user_amqp", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($Staff->reset_user_amqp_key()));
    endif;

    if(filter_input(INPUT_POST, "update_staff_history", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($Staff->update_staff_history()));
    endif;

    if(filter_input(INPUT_POST, "get_slife", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($Staff->get_staff_life()));
    endif;