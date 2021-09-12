<?php

    class iotJumpWay
    {

        function __construct($hias)
        {
            $this->hias = $hias;
        }

        public function get_locations($limit = 0, $order = "id DESC")
        {
            $limiter = "";
            if($limit != 0):
                $limiter = "&limit=" . $limit;
            endif;

            $request = $this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "?type=Location".$limiter, []);
            $locations = json_decode($request["body"], true);
            return $locations;
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

        public function get_technologies()
        {
            $pdoQuery = $this->hias->conn->prepare("
                SELECT technology
                FROM technologies
                ORDER BY technology ASC
            ");
            $pdoQuery->execute();
            $buildings=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
            $pdoQuery->closeCursor();
            $pdoQuery = null;
            return $buildings;
        }

        public function get_location($id, $attrs = Null)
        {
            if($attrs):
                $attrs="&attrs=" . $attrs;
            endif;

            $request = $this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "/" . $id . "?type=Location" . $attrs, []);
            $location = json_decode($request["body"], true);
            return $location;
        }

        public function check_location($lid)
        {
            $request = $this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "/" . $lid . "?type=Location", []);
            $location = json_decode($request["body"], true);
            if(isSet($location["id"])):
                return True;
            else:
                return False;
            endif;
        }

        public function update_location()
        {
            if(!filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Name is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Location description is required"
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
            if(!filter_input(INPUT_POST, "coordinates", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Location coordinates is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "mon", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Monday's opening hours are required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "tues", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Tuesday's opening hours are required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "wed", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Wednesday's opening hours are required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "thurs", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Thursday's opening hours are required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "fri", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Friday's opening hours are required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "sat", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Saturday's opening hours are required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "sun", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Sunday's opening hours are required"
                ];
            endif;
            if(!isSet($_POST["category"])):
                return [
                    "Response"=> "Failed",
                    "Message" => "Sunday's opening hours are required"
                ];
            endif;

            $location = $this->get_location($this->hias->confs["lid"]);

            $coords = explode(",", filter_input(INPUT_POST, "coordinates", FILTER_SANITIZE_STRING));

            $data = [
                "type" => "Location",
                "category" => [
                    "value" => $_POST["category"]
                ],
                "name" => [
                    "value" => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)
                ],
                "description" => [
                    "value" => filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)
                ],
                "floorsAboveGround" => [
                    "value" => filter_input(INPUT_POST, "floorsAboveGround", FILTER_SANITIZE_NUMBER_INT)
                ],
                "floorsBelowGround" => [
                    "value" => filter_input(INPUT_POST, "floorsBelowGround", FILTER_SANITIZE_NUMBER_INT)
                ],
                "zones" => [
                    "type" => "Number",
                    "value" => filter_input(INPUT_POST, "zones", FILTER_SANITIZE_NUMBER_INT),
                    "metadata" => [
                        "description" => [
                            "value" => "Number of zones within the location"
                        ]
                    ]
                ],
                "devices" => [
                    "type" => "Number",
                    "value" => filter_input(INPUT_POST, "devices", FILTER_SANITIZE_NUMBER_INT),
                    "metadata" => [
                        "description" => [
                            "value" => "Number of devices within the location"
                        ]
                    ]
                ],
                "applications" => [
                    "type" => "Number",
                    "value" => filter_input(INPUT_POST, "applications", FILTER_SANITIZE_NUMBER_INT),
                    "metadata" => [
                        "description" => [
                            "value" => "Number of applications within the location"
                        ]
                    ]
                ],
                "users" => [
                    "type" => "Number",
                    "value" => filter_input(INPUT_POST, "users", FILTER_SANITIZE_NUMBER_INT),
                    "metadata" => [
                        "description" => [
                            "value" => "Number of users registered to the location"
                        ]
                    ]
                ],
                "location" => [
                    "numerical" => 1,
                    "type" => "geo:json",
                    "value" => [
                        "type" => "Point",
                        "coordinates" => [floatval($coords[0]), floatval($coords[1])]
                    ]
                ],
                "address" => [
                    "type" => "PostalAddress",
                    "value" => [
                        "addressLocality" => filter_input(INPUT_POST, "addressLocality", FILTER_SANITIZE_STRING),
                        "postalCode" => filter_input(INPUT_POST, "postalCode", FILTER_SANITIZE_STRING),
                        "streetAddress" => filter_input(INPUT_POST, "streetAddress", FILTER_SANITIZE_STRING)
                    ]
                ],
                "mapUrl" => [
                    "type" => "URL",
                    "value" => filter_input(INPUT_POST, "mapUrl", FILTER_SANITIZE_STRING)
                ],
                "openingHours" => [
                    filter_input(INPUT_POST, "mon", FILTER_SANITIZE_STRING),
                    filter_input(INPUT_POST, "tues", FILTER_SANITIZE_STRING),
                    filter_input(INPUT_POST, "wed", FILTER_SANITIZE_STRING),
                    filter_input(INPUT_POST, "thurs", FILTER_SANITIZE_STRING),
                    filter_input(INPUT_POST, "fri", FILTER_SANITIZE_STRING),
                    filter_input(INPUT_POST, "sat", FILTER_SANITIZE_STRING),
                    filter_input(INPUT_POST, "sun", FILTER_SANITIZE_STRING)
                ],
                "dateModified" => [
                    "type" => "DateTime",
                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                ]
            ];

            $request = $this->hias->hiascdi->request("POST", $this->hias->hiascdi->confs["entities_url"] . "/" . $location["id"] . "/attrs?type=Location", json_encode($data));
            $response = json_decode($request["body"], true);

            if($request["code"] == 204):
                $this->hias->store_user_history("Update Location");
                return [
                    "Response"=> "OK",
                    "Message" => "Location updated!"
                ];
            else:
                return [
                    "Response"=> "FAILED",
                    "Message" => "Location update failed!"
                ];
            endif;
        }

        public function get_zones($limit = 0, $order = "id DESC")
        {
            $limiter = "";
            if($limit != 0):
                $limiter = "&limit=" . $limit;
            endif;

            $request = $this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "?type=Zone".$limiter, []);
            $zones = json_decode($request["body"], true);
            return $zones;
        }

        public function get_zone($id, $attrs = Null)
        {
            if($attrs):
                $attrs="&attrs=" . $attrs;
            endif;

            $request = $this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "/" . $id . "?type=Zone" . $attrs, []);
            $zone = json_decode($request["body"], true);
            return $zone;
        }

        public function check_zone($zid)
        {
            $request = $this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "/" . $zid . "?type=Zone" , []);
            $zone = json_decode($request["body"], true);
            if($zone["id"]):
                return True;
            else:
                return False;
            endif;
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

        public function create_zone()
        {
            if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Location ID is required"
                ];
            endif;

            if(!$this->check_location(filter_input(INPUT_POST, "lid", FILTER_SANITIZE_STRING))):
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
                    "Message" => "Description is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "coordinates", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Coordinates are required"
                ];
            endif;

            if(!isSet($_POST["category"])):
                return [
                    "Response"=> "Failed",
                    "Message" => "Category is required"
                ];
            endif;

            $lid = filter_input(INPUT_POST, "lid", FILTER_SANITIZE_STRING);

            $zone = $this->hias->helpers->generate_uuid();
            $coords = explode(",", filter_input(INPUT_POST, "coordinates", FILTER_SANITIZE_STRING));

            $data = [
                "id" => $zone,
                "type" => "Zone",
                "category" => [
                    "value" => $_POST["category"]
                ],
                "name" => [
                    "value" => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)
                ],
                "description" => [
                    "value" => filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)
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
                    "value" => $lid,
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
                        "coordinates" => [floatval($coords[0]), floatval($coords[1])]
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

            $request = $this->hias->hiascdi->request("POST", $this->hias->hiascdi->confs["entities_url"] . "?type=Zone", json_encode($data));
            $response = json_decode($request["body"], true);

            $this->hias->store_user_history("Created Zone", 0, 0, $lid, $zone);

            return [
                "Response"=> "OK",
                "Message" => "Zone created!",
                "LID" => $lid,
                "ZID" => $zone
            ];
        }

        public function update_zone()
        {

            if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Location ID is required"
                ];
            endif;

            if(!$this->check_location(filter_input(INPUT_POST, "lid", FILTER_SANITIZE_STRING))):
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
                    "Message" => "Description is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "coordinates", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Coordinates are required"
                ];
            endif;

            if(!isSet($_POST["category"])):
                return [
                    "Response"=> "Failed",
                    "Message" => "Category is required"
                ];
            endif;

            $lid = filter_input(INPUT_POST, "lid", FILTER_SANITIZE_STRING);
            $location = $this->get_location($lid);

            $zid = filter_input(INPUT_GET, 'zone', FILTER_SANITIZE_STRING);
            $zone = $this->get_zone($zid);

            $coords = explode(",", filter_input(INPUT_POST, "coordinates", FILTER_SANITIZE_STRING));

            $data = [
                "type" => "Zone",
                "category" => [
                    "value" => $_POST["category"]
                ],
                "name" => [
                    "value" => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)
                ],
                "description" => [
                    "value" => filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)
                ],
                "devices" => [
                    "value" => filter_input(INPUT_POST, "devices", FILTER_SANITIZE_NUMBER_INT),
                    "type" => "Number",
                    "metadata" => [
                        "description" => [
                            "value" => "Number of devices connected to this zone"
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
                "location" => [
                    "type" => "geo:json",
                    "value" => [
                        "type" => "Point",
                        "coordinates" => [floatval($coords[0]), floatval($coords[1])]
                    ]
                ],
                "dateModified" => [
                    "type" => "DateTime",
                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                ]
            ];

            $request = $this->hias->hiascdi->request("POST", $this->hias->hiascdi->confs["entities_url"] . "/" . $zone["id"] . "/attrs?type=Zone", json_encode($data));
            $response = json_decode($request["body"], true);

            $this->hias->store_user_history("Updated Zone", 0, 0, $location["id"], $zone["id"]);

            return [
                "Response"=> "OK",
                "Message" => "Zone updated!"
            ];
        }

        public function get_core_components($attrs = "")
        {
            if($attrs):
                $attrs="&attrs=" . $attrs;
            endif;

            $request = $this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "?type=HIASCDI,HIASHDI,HIASBCH" . $attrs, []);
            $things = json_decode($request["body"], true);
            return $things;
        }

        public function get_things($limit = 0, $category = "")
        {
            $limiter = "";
            if($limit != 0):
                $limiter = "&limit=" . $limit;
            endif;
            $scategory = "";
            if($category != ""):
                $scategory = "&category=" . $category;
            endif;

            $request = $this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "?type=Thing".$limiter.$scategory, []);
            $things = json_decode($request["body"], true);
            return $things;
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

        public function create_thing()
        {
            if(!filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Category is required"
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
                    "Message" => "Description is required"
                ];
            endif;

            if (!empty($_FILES['image']['name']) && ($_FILES['image']['error'] == 0)):

                $cleaned_file = preg_replace('/\.(?=.*?\.)/', '_', $_FILES['image']['name']);
                $cleaned_file = str_replace([' '], "-", $cleaned_file);

                if($_FILES["image"]["type"] == "image/jpeg" | $_FILES["image"]["type"] == "image/png" | $_FILES["image"]["type"] ==  "image/gif"):

                    $cleaned_file = preg_replace('/\.(?=.*?\.)/', '_', $_FILES['image']['name']);
                    $cleaned_file = str_replace([' '], "-", $cleaned_file);

                    if (getimagesize($_FILES["image"]["tmp_name"]) !== false):
                        $valid_file_extensions = [
                            ".jpg",
                            ".jpeg",
                            ".gif",
                            ".png",
                            ".JPG",
                            ".JPEG",
                            ".GIF",
                            ".PNG"
                        ];
                        $file_extension = strrchr($_FILES["image"]["name"], ".");
                        if (in_array($file_extension, $valid_file_extensions)):
                            $fileName=time().'_'.$cleaned_file;
                            if(move_uploaded_file($_FILES["image"]["tmp_name"],"Media/Images/Things/".$fileName)):
                                switch (strtolower($_FILES['image']['type'])):
                                    case 'image/jpeg':
                                        $image = imagecreatefromjpeg("Media/Images/Things/".$fileName);
                                        break;
                                    case 'image/png':
                                        $image = imagecreatefrompng("Media/Images/Things/".$fileName);
                                        break;
                                    case 'image/gif':
                                        $image = imagecreatefromgif("Media/Images/Things/".$fileName);
                                        break;
                                    default:
                                endswitch;

                                $pubKey = $this->hias->helpers->generate_uuid();

                                $properties=[];
                                if(isSet($_POST["properties"])):
                                    foreach($_POST["properties"] AS $key => $value):
                                        $properties[$value] = ["value" => ""];
                                    endforeach;
                                endif;
                                $properties["image"] = ["value" => $fileName];

                                $commands=[];
                                if(isSet($_POST["commands"])):
                                    foreach($_POST["commands"] AS $key => $value):
                                        $values = explode(",", $value);
                                        $commands[$key] = $values;
                                    endforeach;
                                endif;

                                $states=[];
                                $state=[];
                                if(isSet($_POST["states"])):
                                    $states = $_POST["states"];
                                    $state = [
                                        "value" => "",
                                        "metadata" => [
                                            "timestamp" => ""
                                        ]
                                    ];
                                endif;

                                $data = [
                                    "id" => $pubKey,
                                    "type" => "Thing",
                                    "category" => [
                                        "value" => [filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)]
                                    ],
                                    "name" => [
                                        "value" => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)
                                    ],
                                    "description" => [
                                        "value" => filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)
                                    ],
                                    "manufacturer" => [
                                        "value" => filter_input(INPUT_POST, "manufacturer", FILTER_SANITIZE_STRING)
                                    ],
                                    "model" => [
                                        "value" => filter_input(INPUT_POST, "model", FILTER_SANITIZE_STRING)
                                    ],
                                    "properties" => [
                                        "value" => $properties
                                    ],
                                    "commands" => [
                                        "value" => $commands
                                    ],
                                    "states" => [
                                        "value" => $states
                                    ],
                                    "state" => [
                                        "value" => $state
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

                                $request = $this->hias->hiascdi->request("POST", $this->hias->hiascdi->confs["entities_url"] . "?type=Thing", json_encode($data));
                                $thing = json_decode($request["body"], true);

                                if(!isSet($thing["Error"])):
                                    $this->hias->store_user_history("Created Thing", 0, 0, "", "", "", $pubKey);
                                    return [
                                        "Response"=> "OK",
                                        "Message" => "Thing created!"
                                    ];
                                else:
                                    return [
                                        "Response"=> "FAILED",
                                        "Message" => "Thing update KO! " . $response["Description"]
                                    ];
                                endif;

                            endif;

                        else:
                            return [
                                "Response" => "FAILED",
                                "Message" => "File uploaded FAILED, invalid image file."
                            ];
                        endif;

                    else:
                        return [
                            "Response" => "FAILED",
                                "Message" => "File uploaded FAILED, invalid image file."
                        ];
                    endif;
                else:
                    return [
                        "Response" => "FAILED",
                                "Message" => "File uploaded FAILED, invalid image file."
                    ];
                endif;

            else:
                return [
                    "Response"=> "FAILED",
                    "Message" => "Please provide a sensor image!"
                ];
            endif;
        }

        public function update_thing()
        {
            if(!filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Category is required"
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
                    "Message" => "Description is required"
                ];
            endif;

            $tid = filter_input(INPUT_POST, 'thing', FILTER_SANITIZE_STRING);
            $thing = $this->get_thing($tid);

            $properties=[];

            if($_FILES["image"]["type"] == "image/jpeg" | $_FILES["image"]["type"] == "image/png" | $_FILES["image"]["type"] ==  "image/gif"):

                $cleaned_file = preg_replace('/\.(?=.*?\.)/', '_', $_FILES['image']['name']);
                $cleaned_file = str_replace([' '], "-", $cleaned_file);

                if (getimagesize($_FILES["image"]["tmp_name"]) !== false):
                    $valid_file_extensions = [
                        ".jpg",
                        ".jpeg",
                        ".gif",
                        ".png",
                        ".JPG",
                        ".JPEG",
                        ".GIF",
                        ".PNG"
                    ];
                    $file_extension = strrchr($_FILES["image"]["name"], ".");
                    if (in_array($file_extension, $valid_file_extensions)):
                        $fileName=time().'_'.$cleaned_file;
                        if(move_uploaded_file($_FILES["image"]["tmp_name"],"Media/Images/Things/".$fileName)):
                            switch (strtolower($_FILES['image']['type'])):
                                case 'image/jpeg':
                                    $image = imagecreatefromjpeg("Media/Images/Things/".$fileName);
                                    break;
                                case 'image/png':
                                    $image = imagecreatefrompng("Media/Images/Things/".$fileName);
                                    break;
                                case 'image/gif':
                                    $image = imagecreatefromgif("Media/Images/Things/".$fileName);
                                    break;
                                default:
                            endswitch;

                            $properties["image"] = ["value" => $fileName];

                        endif;

                    else:
                        return [
                            "Response" => "FAILED",
                            "Message" => "File uploaded FAILED, invalid image file."
                        ];
                    endif;

                else:
                    return [
                        "Response" => "FAILED",
                            "Message" => "File uploaded FAILED, invalid image file."
                    ];
                endif;
            else:
                $properties["image"] = ["value" => $thing["properties"]["value"]["image"]["value"]];
            endif;

            if(isSet($_POST["properties"])):
                foreach($_POST["properties"] AS $key => $value):
                    $properties[$value] = ["value" => ""];
                endforeach;
            endif;

            $commands=[];
            if(isSet($_POST["commands"])):
                foreach($_POST["commands"] AS $key => $value):
                    $values = explode(",", $value);
                    $commands[$key] = $values;
                endforeach;
            endif;

            $states=[];
            $state=[];
            if(isSet($_POST["states"])):
                $states = $_POST["states"];
                $state = [
                    "value" => "",
                    "metadata" => [
                        "timestamp" => ""
                    ]
                ];
            endif;

            $data = [
                "category" => [
                    "value" => [filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)]
                ],
                "name" => [
                    "value" => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)
                ],
                "description" => [
                    "value" => filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)
                ],
                "manufacturer" => [
                    "value" => filter_input(INPUT_POST, "manufacturer", FILTER_SANITIZE_STRING)
                ],
                "model" => [
                    "value" => filter_input(INPUT_POST, "model", FILTER_SANITIZE_STRING)
                ],
                "properties" => [
                    "value" => $properties
                ],
                "commands" => [
                    "value" => $commands
                ],
                "states" => [
                    "value" => $states
                ],
                "state" => [
                    "value" => $state
                ],
                "dateModified" => [
                    "type" => "DateTime",
                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                ]
            ];

            $request = $this->hias->hiascdi->request("POST", $this->hias->hiascdi->confs["entities_url"] . "/" . $thing["id"] . "/attrs?type=Thing", json_encode($data));
            $response = json_decode($request["body"], true);

            if($response["Response"]=="OK"):

                $schema = $this->get_thing($tid);
                return [
                    "Response"=> "OK",
                    "Message" => "Thing updated!",
                    "Schema" => $schema
                ];
            else:
                return [
                    "Response"=> "FAILED",
                    "Message" => "Thing update KO! " . $response["Description"]
                ];
            endif;
        }

        public function get_devices($limit = 0, $order = "id DESC", $query = "")
        {
            $limiter = "";
            if($limit != 0):
                $limiter = "&limit=" . $limit;
            endif;

            if($query):
                $query="&q=" . $query;
            endif;

            $request = $this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "?type=Device".$query.$limiter, []);
            $devices = json_decode($request["body"], true);
            return $devices;
        }

        public function get_device($id, $attrs = Null)
        {
            if($attrs):
                $attrs="&attrs=" . $attrs;
            endif;

            $request = $this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "/" . $id . "?type=Device" . $attrs, []);
            $device = json_decode($request["body"], true);
            return $device;
        }

        public function get_device_categories()
        {
            $pdoQuery = $this->hias->conn->prepare("
                SELECT category
                FROM hiascdi_device_cats
                ORDER BY category ASC
            ");
            $pdoQuery->execute();
            $categories=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
            $pdoQuery->closeCursor();
            $pdoQuery = null;
            return $categories;
        }

        public function get_device_models()
        {
            $pdoQuery = $this->hias->conn->prepare("
                SELECT category
                FROM hiascdi_device_models
                ORDER BY category ASC
            ");
            $pdoQuery->execute();
            $categories=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
            $pdoQuery->closeCursor();
            $pdoQuery = null;
            return $categories;
        }

        public function create_device()
        {
            if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Location ID is required"
                ];
            endif;

            if(!$this->check_location(filter_input(INPUT_POST, "lid", FILTER_SANITIZE_STRING))):
                return [
                    "Response"=> "Failed",
                    "Message" => "iotJumpWay location does not exist"
                ];
            endif;

            if(!filter_input(INPUT_POST, "zid", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Zone ID is required"
                ];
            endif;

            if(!$this->check_zone(filter_input(INPUT_POST, "zid", FILTER_SANITIZE_STRING))):
                return [
                    "Response"=> "Failed",
                    "Message" => "iotJumpWay zone does not exist"
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

            if(!filter_input(INPUT_POST, "deviceName", FILTER_SANITIZE_STRING)):
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

            if(!filter_input(INPUT_POST, "agent", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "IoT Agent is required"
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

            if(!filter_input(INPUT_POST, "authenticationIpinfoKey", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "IP Info key is required"
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
                    "Message" => "Category is required"
                ];
            endif;

            if(count($_POST["technologies"]) == 0):
                return [
                    "Response"=> "Failed",
                    "Message" => "A minimum of 1 technology is required"
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
            $did = $pubKey;
            $privKey = $this->hias->helpers->generate_key(32);
            $privKeyHash = $this->hias->helpers->password_hash($privKey);

            $amqppubKey = $this->hias->helpers->generate_uuid();
            $amqpprvKey = $this->hias->helpers->generate_key(32);
            $amqpKeyHash = $this->hias->helpers->password_hash($amqpprvKey);

            $bcPass = $this->hias->helpers->password();

            $lid = filter_input(INPUT_POST, 'lid', FILTER_SANITIZE_STRING);
            $location = $this->get_location($lid);

            $zid = filter_input(INPUT_POST, 'zid', FILTER_SANITIZE_STRING);
            $zone = $this->get_zone($zid);

            $ip = filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING);
            $mac = filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING);
            $bt = filter_input(INPUT_POST, "bluetooth", FILTER_SANITIZE_STRING);
            $btOnly = filter_input(INPUT_POST, "bluetoothOnly", FILTER_SANITIZE_NUMBER_INT) ? True : False;
            $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
            $coords = explode(",", filter_input(INPUT_POST, "coordinates", FILTER_SANITIZE_STRING));

            if(in_array("ble", $_POST["protocols"]) || in_array("bluetooth", $_POST["protocols"])):
                $btPin =  $this->hias->helpers->generate_key(6);
                $btService = $this->hias->helpers->generate_uuid();
                $btCharacteristic = $this->hias->helpers->generate_uuid();
            else:
                $btPin = "NA";
                $btService = "NA";
                $btCharacteristic = "NA";
            endif;

            $protocols = $_POST["protocols"];

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

            $newBcUser = $this->hias->hiasbch->create_user($bcPass);

            if($newBcUser == "FAILED"):
                return [
                    "Response"=> "Failed",
                    "Message" => "Creating New HIAS Blockhain Account Failed!"
                ];
            endif;

            $data = [
                "id" => $pubKey,
                "type" => "Device",
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
                "brandName" => [
                    "value" => filter_input(INPUT_POST, "deviceName", FILTER_SANITIZE_STRING)
                ],
                "modelName" => [
                    "value" => filter_input(INPUT_POST, "deviceModel", FILTER_SANITIZE_STRING)
                ],
                "manufacturerName" => [
                    "value" => filter_input(INPUT_POST, "deviceManufacturer", FILTER_SANITIZE_STRING)
                ],
                "serialNumber" => [
                    "value" => filter_input(INPUT_POST, "serialNumber", FILTER_SANITIZE_STRING)
                ],
                "os" => [
                    "value" => filter_input(INPUT_POST, "osName", FILTER_SANITIZE_STRING)
                ],
                "osVersion" => [
                    "value" => filter_input(INPUT_POST, "osVersion", FILTER_SANITIZE_STRING),
                    "metadata" => [
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
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
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
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
                    "value" => $location["id"],
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Location entity ID"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "networkZone" => [
                    "value" => $zone["id"],
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Zone entity ID"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
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
                        "timestamp" => [
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
                        "timestamp" => [
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
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "bluetoothOnly" => [
                    "value" => $btOnly,
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Whether device only supports Bluetooth/BLE"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "bluetoothServiceUUID" => [
                    "value" => $btService,
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Bluetooth service UUID"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "bluetoothCharacteristicUUID" => [
                    "value" => $btCharacteristic,
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Bluetooth characteristic UUID"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "bluetoothPinCode" => [
                    "value" => $btPin,
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Bluetooth security pincode"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "protocols" => [
                    "type" => "StructuredValue",
                    "value" => $protocols,
                    "metadata" => [
                        "description" => [
                            "value" => "Supported protocols"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "northPort" => [
                    "value" => filter_input(INPUT_POST, "northPort", FILTER_SANITIZE_NUMBER_INT),
                    "metadata" => [
                        "description" => [
                            "value" => "North port of the Agent"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "iotType" => [
                    "value" => $_POST["iot"],
                    "metadata" => [
                        "description" => [
                            "value" => "IoT device type"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "permissionsAdmin" => [
                    "value" => True,
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Has admin permissions"
                        ],
                        "timestamp" => [
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
                    "value" => $this->hias->helpers->oEncrypt(filter_input(INPUT_POST, "authenticationIpinfoKey", FILTER_SANITIZE_STRING)),
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
                "technologies" => [
                    "value" => $_POST["technologies"],
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" =>  "Technologies used with this device"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
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

            $request = $this->hias->hiascdi->request("POST", $this->hias->hiascdi->confs["entities_url"] . "?type=Device", json_encode($data));
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
                    ':topic' => $location["id"] . "/Devices/" . $zone["id"] . "/" . $pubKey . "/#",
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

                $hash = "";
                $msg = "";
                $actionMsg = "";
                $balanceMessage = "";
                $this->hias->hiasbch->contract->at($this->hias->hiasbch->confs["contract"])->send("registerDevice", $pubKey, $newBcUser, $location["id"], $zone["id"], $name, 1, time(), ["from" => $_SESSION["HIAS"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
                    if ($err !== null) {
                        $hash = "FAILED";
                        $msg = $err;
                        return;
                    }
                    $hash = $resp;
                });

                if($hash == "FAILED"):
                    $actionMsg = " HIASBCH registerDevice failed!\n" . $msg;
                else:
                    $this->hias->store_user_history("HIASBCH registerDevice", $hash, 0, $lid, $zid, 0,  $pubKey);
                    $balance = $this->hias->hiasbch->check_balance($_SESSION["HIAS"]["BC"]["BCUser"], $this->hias->helpers->oDecrypt($_SESSION["HIAS"]["BC"]["BCPass"]));
                    $actionMsg = " HIASBCH registerDevice OK!\n";
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
                    $this->hias->store_user_history("HIASBCH registerAuthorized (IoT Device)", $hash, 0, $lid, $zid, "",  $pubKey, "", "");
                    $balance = $this->hias->hiasbch->check_balance($_SESSION["HIAS"]["BC"]["BCUser"], $this->hias->helpers->oDecrypt($_SESSION["HIAS"]["BC"]["BCPass"]));
                    $actionMsg .= " HIASBCH registerAuthorized OK!\n";
                    $balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!";
                endif;

                return [
                    "Response"=> "OK",
                    "Message" => "Device created!" . $actionMsg . $balanceMessage,
                    "LID" => $lid,
                    "ZID" => $zid,
                    "DID" => $did,
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
                    "Message" => "Device creating failed"
                ];
            endif;
        }

        public function update_device()
        {
            if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Location ID is required"
                ];
            endif;

            if(!$this->check_location(filter_input(INPUT_POST, "lid", FILTER_SANITIZE_STRING))):
                return [
                    "Response"=> "Failed",
                    "Message" => "iotJumpWay location does not exist"
                ];
            endif;

            if(!filter_input(INPUT_POST, "zid", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Zone ID is required"
                ];
            endif;

            if(!$this->check_zone(filter_input(INPUT_POST, "zid", FILTER_SANITIZE_STRING))):
                return [
                    "Response"=> "Failed",
                    "Message" => "iotJumpWay location does not exist"
                ];
            endif;

            if(!isSet($_POST["category"])):
                return [
                    "Response"=> "Failed",
                    "Message" => "Category is required"
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

            if(!filter_input(INPUT_POST, "deviceName", FILTER_SANITIZE_STRING)):
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

            if(!filter_input(INPUT_POST, "agent", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "IoT Agent is required"
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

            if(!filter_input(INPUT_POST, "authenticationIpinfoKey", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "IPInfo key is required"
                ];
            endif;

            if(!isSet($_POST["protocols"])):
                return [
                    "Response"=> "Failed",
                    "Message" => "At least one M2M protocol is required"
                ];
            endif;

            $unlocked =  $this->hias->hiasbch->unlock_account($_SESSION["HIAS"]["BC"]["BCUser"], $this->hias->helpers->oDecrypt($_SESSION["HIAS"]["BC"]["BCPass"]));

            if($unlocked == "FAILED"):
                return [
                    "Response"=> "Failed",
                    "Message" => "Unlocking HIAS Blockhain Account Failed!"
                ];
            endif;

            $lid = filter_input(INPUT_POST, 'lid', FILTER_SANITIZE_STRING);
            $location = $this->get_location($lid);

            $zid = filter_input(INPUT_POST, 'zid', FILTER_SANITIZE_STRING);
            $zone = $this->get_zone($zid);

            $did = filter_input(INPUT_GET, "device", FILTER_SANITIZE_STRING);
            $device = $this->get_device($did);

            $ip = filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING);
            $mac = filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING);
            $bt = filter_input(INPUT_POST, "bluetooth", FILTER_SANITIZE_STRING);
            $btOnly = filter_input(INPUT_POST, "bluetoothOnly", FILTER_SANITIZE_NUMBER_INT) ? True : False;
            $btPin = filter_input(INPUT_POST, "bluetoothPinCode", FILTER_SANITIZE_STRING);
            $btService = filter_input(INPUT_POST, "bluetoothServiceUUID", FILTER_SANITIZE_STRING);
            $btCharacteristic = filter_input(INPUT_POST, "bluetoothCharacteristicUUID", FILTER_SANITIZE_STRING);
            $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
            $coords = explode(",", filter_input(INPUT_POST, "coordinates", FILTER_SANITIZE_STRING));

            $protocols = [];
            foreach($_POST["protocols"] AS $key => $value):
                $protocols[] = $value;
            endforeach;

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
                "type" => "Device",
                "category" => [
                    "value" => $_POST["category"]
                ],
                "name" => [
                    "value" => $name
                ],
                "description" => [
                    "value" => filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)
                ],
                "brandName" => [
                    "value" => filter_input(INPUT_POST, "deviceName", FILTER_SANITIZE_STRING)
                ],
                "modelName" => [
                    "value" => filter_input(INPUT_POST, "deviceModel", FILTER_SANITIZE_STRING)
                ],
                "manufacturerName" => [
                    "value" => filter_input(INPUT_POST, "deviceManufacturer", FILTER_SANITIZE_STRING)
                ],
                "serialNumber" => [
                    "value" => filter_input(INPUT_POST, "serialNumber", FILTER_SANITIZE_STRING)
                ],
                "os" => [
                    "value" => filter_input(INPUT_POST, "osName", FILTER_SANITIZE_STRING)
                ],
                "osVersion" => [
                    "value" => filter_input(INPUT_POST, "osVersion", FILTER_SANITIZE_STRING),
                    "metadata" => [
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
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
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
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
                        ],
                        "timestamp" => [
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
                        "timestamp" => [
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
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "bluetoothOnly" => [
                    "value" => $btOnly,
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Whether device only supports Bluetooth/BLE"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "bluetoothPinCode" => [
                    "value" => $btPin,
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Bluetooth security pincode"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "bluetoothServiceUUID" => [
                    "value" => $btService,
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Bluetooth service UUID"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "bluetoothCharacteristicUUID" => [
                    "value" => $btCharacteristic,
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Bluetooth characteristic UUID"
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
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
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
                "iotType" => [
                    "value" => $_POST["iot"],
                    "metadata" => [
                        "description" => [
                            "value" => "IoT device type"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "permissionsAdmin" => [
                    "value" => True,
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Has admin permissions"
                        ]
                    ]
                ],
                "permissionsCancelled" => [
                    "value" => False,
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Is cancelled"
                        ]
                    ]
                ],
                "technologies" => [
                    "value" => $_POST["technologies"],
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" =>  "Technologies used with this device"
                        ]
                    ]
                ],
                "sensors" => [
                    "value" => $sensors,
                    "type" => "StructuredValue",
                    "metadata" => [
                        "description" => [
                            "value" =>  "Sensors connected to this device"
                        ]
                    ]
                ],
                "actuators" => [
                    "value" => $actuators,
                    "type" => "StructuredValue",
                    "metadata" => [
                        "description" => [
                            "value" =>  "Actuators connected to this device"
                        ]
                    ]
                ],
                "dateModified" => [
                    "type" => "DateTime",
                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                ]
            ];

            $request = $this->hias->hiascdi->request("PATCH", $this->hias->hiascdi->confs["entities_url"] . "/" . $device["id"] . "/attrs?type=Device", json_encode($data));
            $response = json_decode($request["body"], true);
            if(!isSet($response["Error"])):

                $hash = "";
                $msg = "";
                $this->hias->hiasbch->contract->at($this->hias->hiasbch->confs["contract"])->send("updateDevice", $device["id"], "Device", $location["id"], $zone["id"], $name, time(), ["from" => $_SESSION["HIAS"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
                    if ($err !== null) {
                        $hash = "FAILED";
                        $msg = $err;
                        return;
                    }
                    $hash = $resp;
                });

                $balance = "";
                $balanceMessage = "";
                $actionMsg = "";
                if($hash == "FAILED"):
                    $actionMsg = " HIASBCH updateDevice failed! " . $msg;
                else:
                    $this->hias->store_user_history("HIASBCH updateDevice (IoT Device)", $hash, 0, $lid, $zid, "",  $device["id"], "", "");
                    $balance = $this->hias->hiasbch->check_balance($_SESSION["HIAS"]["BC"]["BCUser"], $this->hias->helpers->oDecrypt($_SESSION["HIAS"]["BC"]["BCPass"]));
                    $balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!";
                endif;

                $device = $this->get_device($did, "dateCreated,dateModified,*");

                return [
                    "Response"=> "OK",
                    "Message" => "Device updated!" . $actionMsg . $balanceMessage,
                    "Schema" => $device
                ];
            else:
                return [
                    "Response"=> "Failed",
                    "Message" => "There was a problem updating this device context data!"
                ];
            endif;
        }

        public function get_device_life()
        {
            $Device = $this->get_device(filter_input(INPUT_GET, 'device', FILTER_SANITIZE_STRING), "batteryLevel,cpuUsage,memoryUsage,hddUsage,temperature,networkStatus");
            if(!isSet($Device["Error"])):
                $response = [
                    "battery" => $Device["batteryLevel"]["value"],
                    "cpu" => $Device["cpuUsage"]["value"],
                    "mem" => $Device["memoryUsage"]["value"],
                    "hdd" => $Device["hddUsage"]["value"],
                    "tempr" => $Device["temperature"]["value"],
                    "status" => $Device["networkStatus"]["value"]
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

        public function get_device_status($status)
        {
            if($status=="ONLINE"):
                $on = "  ";
                $off = " hide ";
            else:
                $on = " hide ";
                $off = "  ";
            endif;

            return [$on, $off];
        }

        public function device_life_graph($device, $limit = 0, $order = "")
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

            $request = $this->hias->hiashdi->request("GET", $this->hias->hiashdi->confs["data_url"] . "?type=Life&q=Use==Device;Device==". $device . $limiter . $orderer, []);
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

        public function update_device_life_graph($params=[])
        {
            $data = $this->device_life_graph(filter_input(INPUT_GET, "device", FILTER_SANITIZE_STRING), 100);

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

        public function device_sensors_graph($params=[])
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

            $request = $this->hias->hiashdi->request("GET", $this->hias->hiashdi->confs["data_url"] . "?type=Sensors&q=Use==Device;Device==". $params["device"] . $type . "&attrs=Value,Time,Type" . $limiter . $orderer, []);
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

        public function update_device_sensors_graph($params=[])
        {
            $types = [];
            $dates = [];
            $points = [];

            $data = $this->device_sensors_graph([
                "device" => filter_input(INPUT_GET, "device", FILTER_SANITIZE_STRING),
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

        public function get_device_statuses($device, $limit = 0, $order = "")
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

            $request = $this->hias->hiashdi->request("GET", $this->hias->hiashdi->confs["data_url"] . "?type=Statuses&q=Use==Device;Device==". $device . $limiter . $orderer, []);
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

        public function get_device_life_data($device, $limit = 0, $order = "")
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

            $request = $this->hias->hiashdi->request("GET", $this->hias->hiashdi->confs["data_url"] . "?type=Life&q=Use==Device;Device==". $device . $limiter . $orderer, []);
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

        public function get_device_sensors_data($device, $limit = 0, $order = "")
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

            $request = $this->hias->hiashdi->request("GET", $this->hias->hiashdi->confs["data_url"] . "?type=Sensors&q=Use==Device;Device==". $device . $limiter . $orderer, []);
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

        public function get_device_actuators_data($device, $limit = 0, $order = -1)
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

            $request = $this->hias->hiashdi->request("GET", $this->hias->hiashdi->confs["data_url"] . "?type=Actuators&q=Use==Device;Device==". $device . $limiter . $orderer, []);
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

        public function get_device_transactions($device, $limit = 0, $order = "")
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

            $request = $this->hias->hiashdi->request("GET", $this->hias->hiashdi->confs["data_url"] . "?type=Transactions&q=From==" . $device . $orderer . $limiter, []);
            $response = json_decode($request["body"], true);

            if(!isSet($response["Error"])):
                return  $response;
            else:
                return False;
            endif;
        }

        public function get_device_history($device, $limit = 0, $order = "")
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
                WHERE tdid = :id
                $orderer
                $limiter
            ");
            $pdoQuery->execute([
                ":id" => $device
            ]);
            $response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
            return $response;
        }

        public function update_device_history()
        {
            $return = "";
            if(filter_input(INPUT_POST, 'deviceHistory', FILTER_SANITIZE_STRING) == "Activity"):
                $userDetails = "";
                $history = $this->get_device_history(filter_input(INPUT_GET, "device", FILTER_SANITIZE_STRING), 100);
                if(count($history)):
                    foreach($history as $key => $value):
                            if($value["uid"]):
                                $user = $this->hias->get_user($value["uid"]);
                                $userDetails = $user["name"]["value"];
                            endif;
                            if($value["hash"]):
                                $hash = '<a href="' . $this->hias->domain . '/HIASBCH/Explorer/Transaction/' . $value["hash"] . '">#' . $value["hash"] . '</a>';
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
                        "Message" => "Device Activity found!",
                        "Data" => $return
                    ];
                else:
                    return [
                        "Response" => "FAILED",
                        "Message" => "Device History not found!"
                    ];
                endif;
            elseif(filter_input(INPUT_POST, 'deviceHistory', FILTER_SANITIZE_STRING) == "Transactions"):
                $transactions = $this->get_device_transactions(filter_input(INPUT_POST, "DeviceAddress", FILTER_SANITIZE_STRING), 100);
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
                        "Message" => "Device Transactions found!",
                        "Data" => $return
                    ];
                else:
                    return [
                        "Response" => "FAILED",
                        "Message" => "Device Transactions not found!"
                    ];
                endif;
            elseif(filter_input(INPUT_POST, 'deviceHistory', FILTER_SANITIZE_STRING) == "Statuses"):
                $Statuses = $this->get_device_statuses(filter_input(INPUT_GET, "device", FILTER_SANITIZE_STRING), 100);
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
                        "Message" => "Device Statuses found!",
                        "Data" => $return
                    ];
                else:
                    return [
                        "Response" => "FAILED",
                        "Message" => "Device Statuses not found!"
                    ];
                endif;
            elseif(filter_input(INPUT_POST, 'deviceHistory', FILTER_SANITIZE_STRING) == "Life"):
                $life = $this->get_device_life_data(filter_input(INPUT_GET, "device", FILTER_SANITIZE_STRING), 100);
                if($life["Response"] == "OK"):
                    foreach($life["ResponseData"] as $key => $value):
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
                        "Message" => "Device Life found!",
                        "Data" => $return
                    ];
                else:
                    return [
                        "Response" => "FAILED",
                        "Message" => "Device Life not found!"
                    ];
                endif;
            elseif(filter_input(INPUT_POST, 'deviceHistory', FILTER_SANITIZE_STRING) == "Sensors"):
                $Sensors = $this->get_device_sensors_data(filter_input(INPUT_GET, "device", FILTER_SANITIZE_STRING), 100);
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
                        if($value['Message']):
                            $message = $value["Message"];
                        else:
                            $message = "NA";
                        endif;
                        $return .= "
                        <tr>
                            <td>
                                <div class='row'>
                                    <div class='col-lg-2 col-md-12 col-sm-12 col-xs-12'>ID:</div>
                                    <div class='col-lg-10 col-md-12 col-sm-12 col-xs-12'>" . $value['_id']['$oid'] . "</div>
                                    <div class='col-lg-2 col-md-12 col-sm-12 col-xs-12'>Type:</div>
                                    <div class='col-lg-10 col-md-12 col-sm-12 col-xs-12'>" . $value['Type'] . "</div>
                                    <div class='col-lg-2 col-md-12 col-sm-12 col-xs-12'>Values:</div>
                                    <div class='col-lg-10 col-md-12 col-sm-12 col-xs-12'>" . $values . "</div>
                                    <div class='col-lg-2 col-md-12 col-sm-12 col-xs-12'>Message:</div>
                                    <div class='col-lg-10 col-md-12 col-sm-12 col-xs-12'>" . $message . "</div>
                                    <div class='col-lg-2 col-md-12 col-sm-12 col-xs-12'>At:</div>
                                    <div class='col-lg-10 col-md-12 col-sm-12 col-xs-12'>" . $value['Time'] . "</div>
                                </div>

                            </td>
                        </tr>";
                    endforeach;
                    return [
                        "Response" => "OK",
                        "Message" => "Device Sensors found!",
                        "Data" => $return
                    ];
                else:
                    return [
                        "Response" => "FAILED",
                        "Message" => "Device Sensors not found!"
                    ];
                endif;
            elseif(filter_input(INPUT_POST, 'deviceHistory', FILTER_SANITIZE_STRING) == "Actuators"):
                $Actuators = $this->get_device_actuators_data(filter_input(INPUT_GET, "device", FILTER_SANITIZE_STRING), 100);
                if($Actuators["Response"] == "OK"):
                    foreach($Actuators["ResponseData"] as $key => $value):
                        $values = "";
                        if(is_array($value["Value"])):
                            foreach($value["Value"] AS $key => $val):
                                $values .= "<strong>" . $key . ":</strong> " . $val . "<br />";
                            endforeach;
                        else:
                            $values = $value["Value"];
                        endif;
                        if($value['Message']):
                            $message = $value["Message"];
                        else:
                            $message = "NA";
                        endif;

                        $return .= "
                        <tr>
                            <td>
                                <div class='row'>
                                    <div class='col-lg-2 col-md-12 col-sm-12 col-xs-12'>ID:</div>
                                    <div class='col-lg-10 col-md-12 col-sm-12 col-xs-12'>" . $value['_id']['$oid'] . "</div>
                                    <div class='col-lg-2 col-md-12 col-sm-12 col-xs-12'>Type:</div>
                                    <div class='col-lg-10 col-md-12 col-sm-12 col-xs-12'>" . $value['Type'] . "</div>
                                    <div class='col-lg-2 col-md-12 col-sm-12 col-xs-12'>Values:</div>
                                    <div class='col-lg-10 col-md-12 col-sm-12 col-xs-12'>" . $values . "</div>
                                    <div class='col-lg-2 col-md-12 col-sm-12 col-xs-12'>Message:</div>
                                    <div class='col-lg-10 col-md-12 col-sm-12 col-xs-12'>" . $message . "</div>
                                    <div class='col-lg-2 col-md-12 col-sm-12 col-xs-12'>At:</div>
                                    <div class='col-lg-10 col-md-12 col-sm-12 col-xs-12'>" . $value['Time'] . "</div>
                                </div>

                            </td>
                        </tr>";
                    endforeach;
                    return [
                        "Response" => "OK",
                        "Message" => "Device Actuators found!",
                        "Data" => $return
                    ];
                else:
                    return [
                        "Response" => "FAILED",
                        "Message" => "Device Actuators not found!"
                    ];
                endif;
            else:
                return [
                    "Response" => "FAILED",
                    "Message" => "Device History not found!"
                ];
            endif;
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

        public function resetDvcMqtt()
        {
            $id = filter_input(INPUT_GET, 'device', FILTER_SANITIZE_NUMBER_INT);
            $Device = $this->get_device($id);

            $mqttPass = $this->hias->helpers->password();
            $mqttHash = create_hash($mqttPass);

            $data = [
                "mqtt" => [
                    "username" => $Device["mqtt"]["username"],
                    "password" => $this->hias->helpers->oEncrypt($mqttPass),
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
                    UPDATE mqttu
                    SET pw = :pw
                    WHERE did = :did
                ");
                $query->execute(array(
                    ':pw' => $mqttHash,
                    ':did' => $id
                ));

                $this->hias->store_user_history("Reset Device MQTT Password", 0, $Device["lid"]["value"], $Device["zid"]["value"], $id);

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

        public function resetDvcKey()
        {
            $id = filter_input(INPUT_GET, 'device', FILTER_SANITIZE_NUMBER_INT);
            $Device = $this->get_device($id);

            $privKey = $this->hias->helpers->generate_key(32);
            $privKeyHash = $this->hias->helpers->password_hash($privKey);

            $data = [
                "keys" => [
                    "public" => $Device["keys"]["public"],
                    "private" => $this->hias->helpers->oEncrypt($privKeyHash),
                    "timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
                ],
                "dateModified" => [
                    "type" => "DateTime",
                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                ]
            ];

            $response = json_decode($this->hias->hiascdi->request("POST", $this->hias->hiascdi->confs["entities_url"] . "/" . $Device["id"] . "/attrs?type=Device", json_encode($data)), true);

            if($response["Response"]=="OK"):
                $this->hias->store_user_history("Reset Device Key", 0, $Device["lid"]["value"], $Device["zid"]["value"], $id);
                return [
                    "Response"=> "OK",
                    "Message" => "Device key reset!",
                    "P" => $privKey
                ];
            else:
                return [
                    "Response"=> "FAILED",
                    "Message" => "Device key reset failed!"
                ];
            endif;

        }

        public function get_applications($limit = 0, $order = "id DESC", $category = "")
        {
            $limiter = "";
            if($limit != 0):
                $limiter = "&limit=" . $limit;
            endif;
            $scategory = "";
            if($category != ""):
                $scategory = "&category=" . $category;
            endif;

            $request = $this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "?type=Application".$limiter.$scategory, []);
            $zones = json_decode($request["body"], true);
            return $zones;
        }

        public function get_application($id, $attrs = Null)
        {
            if($attrs):
                $attrs="&attrs=" . $attrs;
            endif;

            $request = $this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "/" . $id . "?type=Application" . $attrs, []);
            $application = json_decode($request["body"], true);
            return $application;
        }

        public function get_application_categories()
        {
            $pdoQuery = $this->hias->conn->prepare("
                SELECT category
                FROM application_cats
                ORDER BY category ASC
            ");
            $pdoQuery->execute();
            $categories=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
            $pdoQuery->closeCursor();
            $pdoQuery = null;
            return $categories;
        }

        public function get_application_status($status)
        {
            if($status=="ONLINE"):
                $on = "  ";
                $off = " hide ";
            else:
                $on = " hide ";
                $off = "  ";
            endif;

            return [$on, $off];
        }

        public function create_application()
        {
            if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Location ID is required"
                ];
            endif;

            if(!$this->check_location(filter_input(INPUT_POST, "lid", FILTER_SANITIZE_STRING))):
                return [
                    "Response"=> "Failed",
                    "Message" => "iotJumpWay location does not exist"
                ];
            endif;

            if(!filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Category is required"
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

            if(!filter_input(INPUT_POST, "deviceName", FILTER_SANITIZE_STRING)):
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

            if(!filter_input(INPUT_POST, "softwareName", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Software name is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "softwareVersion", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Software version is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "softwareManufacturer", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Software manufacturer is required"
                ];
            endif;

            if(!filter_input(INPUT_POST, "agent", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "IoT Agent is required"
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

            if(!isSet($_POST["protocols"])):
                return [
                    "Response"=> "Failed",
                    "Message" => "At least one M2M protocol is required"
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
            $location = $this->get_location($lid);

            $ip = filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING);
            $mac = filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING);
            $bt = filter_input(INPUT_POST, "bluetooth", FILTER_SANITIZE_STRING);
            $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
            $coords = explode(",", filter_input(INPUT_POST, "coordinates", FILTER_SANITIZE_STRING));

            $protocols = $_POST["protocols"];

            $sensors = [];
            if(isSet($_POST["sensors"])):
                foreach($_POST["sensors"] AS $key => $value):
                    $sensor = $this->get_thing($value);
                    unset($sensor["id"]);
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
                    unset($actuator["id"]);
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

            $models = [];
            if(isSet($_POST["ai"])):
                foreach($_POST["ai"] AS $key => $value):
                    $model = $this->get_model($value);
                    $mname = $model["name"]["value"];
                    unset($model["id"]);
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

            $allowed = 0;
            $admin = filter_input(INPUT_POST, "admin", FILTER_SANITIZE_STRING) ? True : False;

            $htpasswd = new Htpasswd('/etc/nginx/security/htpasswd');
            $htpasswd->addUser($pubKey, $privKey, Htpasswd::ENCTYPE_APR_MD5);

            $data = [
                "id" => $pubKey,
                "type" => "Application",
                "category" => [
                    "value" => [filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)]
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
                "brandName" => [
                    "value" => filter_input(INPUT_POST, "deviceName", FILTER_SANITIZE_STRING)
                ],
                "modelName" => [
                    "value" => filter_input(INPUT_POST, "deviceModel", FILTER_SANITIZE_STRING)
                ],
                "manufacturerName" => [
                    "value" => filter_input(INPUT_POST, "deviceManufacturer", FILTER_SANITIZE_STRING)
                ],
                "serialNumber" => [
                    "value" => filter_input(INPUT_POST, "serialNumber", FILTER_SANITIZE_STRING)
                ],
                "os" => [
                    "value" => filter_input(INPUT_POST, "osName", FILTER_SANITIZE_STRING)
                ],
                "osVersion" => [
                    "value" => filter_input(INPUT_POST, "osVersion", FILTER_SANITIZE_STRING),
                    "metadata" => [
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
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
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "softwareManufacturer" => [
                    "value" => filter_input(INPUT_POST, "softwareManufacturer", FILTER_SANITIZE_STRING)
                ],
                "agent" => [
                    "url" => filter_input(INPUT_POST, "agent", FILTER_SANITIZE_STRING)
                ],
                "location" => [
                    "type" => "geo:json",
                    "value" => [
                        "type" => "Point",
                        "coordinates" => [floatval($coords[0]), floatval($coords[1])]
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
                    "value" => $location["id"],
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Location entity ID"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
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
                        "timestamp" => [
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
                        "timestamp" => [
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
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "protocols" => [
                    "type" => "StructuredValue",
                    "value" => $protocols,
                    "metadata" => [
                        "description" => [
                            "value" => "Supported protocols"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "permissionsAdmin" => [
                    "value" => True,
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Has admin permissions"
                        ],
                        "timestamp" => [
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
                "models" => [
                    "value" => $models,
                    "type" => "StructuredValue",
                    "metadata" => [
                        "description" => [
                            "value" => "Supported models"
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

            $request = $this->hias->hiascdi->request("POST", $this->hias->hiascdi->confs["entities_url"] . "?type=Application", json_encode($data));
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

                $amid = $this->addAmqpUser($amqppubKey, $amqpKeyHash);
                $this->addAmqpUserVh($amid, "iotJumpWay");
                $this->addAmqpVhPerm($amid, "iotJumpWay", "exchange", "Core", "read");
                $this->addAmqpVhPerm($amid, "iotJumpWay", "exchange", "Core", "write");
                $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Statuses", "read");
                $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Statuses", "write");
                $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Life", "read");
                $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Life", "write");
                $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Sensors", "read");
                $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Sensors", "write");
                $this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "read", "Statuses");
                $this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "write", "Statuses");
                $this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "read", "Life");
                $this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "write", "Life");
                $this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "read", "Sensors");
                $this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "write", "Sensors");

                if($admin):
                    $this->addAmqpUserPerm($amid, "administrator");
                    $this->addAmqpUserPerm($amid, "managment");
                    $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Statuses", "configure");
                    $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Life", "configure");
                    $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Sensors", "configure");
                endif;

                $unlocked =  $this->hias->hiasbch->unlock_account($_SESSION["HIAS"]["BC"]["BCUser"], $this->hias->helpers->oDecrypt($_SESSION["HIAS"]["BC"]["BCPass"]));

                if($unlocked == "FAILED"):
                    return [
                        "Response"=> "Failed",
                        "Message" => "Unlocking HIAS Blockhain Account Failed!"
                    ];
                endif;

                $hash = "";
                $msg = "";

                $this->hias->hiasbch->contract->at($this->hias->hiasbch->confs["contract"])->send("registerApplication", $pubKey, $newBcUser, True, $location["id"], $name, 1, time(), ["from" => $_SESSION["HIAS"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
                    if ($err !== null) {
                        $hash = "FAILED";
                        $msg = $err;
                        return;
                    }
                    $hash = $resp;
                });

                $actionMsg = "";
                $balanceMessage = "";

                if($hash == "FAILED"):
                    $actionMsg = " HIASBCH registerApplication failed!\n";
                else:
                    $this->hias->store_user_history("HIASBCH Register Application", $hash, 0, 0, 0, $pubKey);
                    $balance = $this->hias->hiasbch->check_balance($_SESSION["HIAS"]["BC"]["BCUser"], $this->hias->helpers->oDecrypt($_SESSION["HIAS"]["BC"]["BCPass"]));
                    $balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
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
                    $actionMsg .= " HIASBCH registerAuthorized failed!\n";
                else:
                    $this->hias->store_user_history("HIASBCH Register Authorized User", $hash, 0, 0, 0, $pubKey);
                    $balance = $this->hias->hiasbch->check_balance($_SESSION["HIAS"]["BC"]["BCUser"], $this->hias->helpers->oDecrypt($_SESSION["HIAS"]["BC"]["BCPass"]));
                    if($balanceMessage == ""):
                        $balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
                    endif;
                endif;

                return [
                    "Response"=> "OK",
                    "Message" => $actionMsg . $balanceMessage,
                    "LID" => $lid,
                    "AID" => $pubKey,
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
                    "Message" => "Application creation failed!"
                ];
            endif;
        }

        public function update_application()
        {
            if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Location ID is required"
                ];
            endif;

            if(!$this->check_location(filter_input(INPUT_POST, "lid", FILTER_SANITIZE_STRING))):
                return [
                    "Response"=> "Failed",
                    "Message" => "Location does not exist"
                ];
            endif;

            if(!filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "Category is required"
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

            if(!filter_input(INPUT_POST, "deviceName", FILTER_SANITIZE_STRING)):
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

            if(!filter_input(INPUT_POST, "agent", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed",
                    "Message" => "IoT Agent is required"
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

            if(!isSet($_POST["protocols"])):
                return [
                    "Response"=> "Failed",
                    "Message" => "At least one M2M protocol is required"
                ];
            endif;

            $unlocked =  $this->hias->hiasbch->unlock_account($_SESSION["HIAS"]["BC"]["BCUser"], $this->hias->helpers->oDecrypt($_SESSION["HIAS"]["BC"]["BCPass"]));

            if($unlocked == "FAILED"):
                return [
                    "Response"=> "Failed",
                    "Message" => "Unlocking HIAS Blockhain Account Failed!"
                ];
            endif;

            $aid = filter_input(INPUT_GET, 'application', FILTER_SANITIZE_STRING);
            $application = $this->get_application($aid);

            if($application["permissionsCancelled"]["value"]):
                return [
                    "Response"=> "Failed",
                    "Message" => "This application is cancelled, to allow access again you must create a new application."
                ];
            endif;

            $lid = filter_input(INPUT_POST, "lid", FILTER_SANITIZE_STRING);
            $location = $this->get_location($lid);

            $ip = filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING);
            $mac = filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING);
            $bt = filter_input(INPUT_POST, "bluetooth", FILTER_SANITIZE_STRING);
            $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
            $coords = explode(",", filter_input(INPUT_POST, "coordinates", FILTER_SANITIZE_STRING));
            $allowed = filter_input(INPUT_POST, "cancelled", FILTER_SANITIZE_NUMBER_INT) ? True : False;
            $admin = filter_input(INPUT_POST, "admin", FILTER_SANITIZE_NUMBER_INT) ? True : False;

            $protocols = [];
            foreach($_POST["protocols"] AS $key => $value):
                $protocols[] = $value;
            endforeach;

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

            $data = [
                "type" => "Application",
                "category" => [
                    "value" => [filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING)]
                ],
                "name" => [
                    "value" => $name
                ],
                "description" => [
                    "value" => filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING)
                ],
                "brandName" => [
                    "value" => filter_input(INPUT_POST, "deviceName", FILTER_SANITIZE_STRING)
                ],
                "modelName" => [
                    "value" => filter_input(INPUT_POST, "deviceModel", FILTER_SANITIZE_STRING)
                ],
                "manufacturerName" => [
                    "value" => filter_input(INPUT_POST, "deviceManufacturer", FILTER_SANITIZE_STRING)
                ],
                "serialNumber" => [
                    "value" => filter_input(INPUT_POST, "serialNumber", FILTER_SANITIZE_STRING)
                ],
                "os" => [
                    "value" => filter_input(INPUT_POST, "osName", FILTER_SANITIZE_STRING)
                ],
                "osVersion" => [
                    "value" => filter_input(INPUT_POST, "osVersion", FILTER_SANITIZE_STRING),
                    "metadata" => [
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
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
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
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
                "ipAddress" => [
                    "value" => $ip,
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "IP address of entity"
                        ],
                        "timestamp" => [
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
                        "timestamp" => [
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
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "protocols" => [
                    "type" => "StructuredValue",
                    "value" => $protocols,
                    "metadata" => [
                        "description" => [
                            "value" => "Supported protocols"
                        ],
                        "timestamp" => [
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
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                        ]
                    ]
                ],
                "permissionsCancelled" => [
                    "value" => $allowed,
                    "type" => "Text",
                    "metadata" => [
                        "description" => [
                            "value" => "Is cancelled"
                        ],
                        "timestamp" => [
                            "value" => date('Y-m-d\TH:i:s.Z\Z', time())
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
                "models" => [
                    "value" => $models,
                    "type" => "StructuredValue",
                    "metadata" => [
                        "description" => [
                            "value" => "Supported models"
                        ]
                    ]
                ],
                "dateModified" => [
                    "type" => "DateTime",
                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                ]
            ];

            $request = $this->hias->hiascdi->request("POST", $this->hias->hiascdi->confs["entities_url"] . "/" . $application["id"] . "/attrs?type=Application", json_encode($data));
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

                $this->hias->hiasbch->contract->at($this->hias->hiasbch->confs["contract"])->send("updateApplication", $application["id"], "Application", $allowed, $admin, $location["id"], $name,  time(), ["from" => $_SESSION["HIAS"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
                    if ($err !== null) {
                        $hash = "FAILED";
                        $msg = $err;
                        return;
                    }
                    $hash = $resp;
                });

                if($hash == "FAILED"):
                    $actionMsg = " HIASBCH updateApplication failed! " . $msg;
                else:
                    $this->hias->store_user_history("HIASBCH Update Application", $hash, 0, 0, 0, $application["id"]);
                    $balance = $this->hias->hiasbch->check_balance($_SESSION["HIAS"]["BC"]["BCUser"], $this->hias->helpers->oDecrypt($_SESSION["HIAS"]["BC"]["BCPass"]));
                    $balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!";
                endif;

                if(!$application["permissionsCancelled"]["value"] && filter_input(INPUT_POST, "cancelled", FILTER_SANITIZE_STRING)):

                    $query = $this->hias->conn->prepare("
                        DELETE FROM mqttu
                        WHERE uname = :uname
                    ");
                    $query->execute([
                        ':uname' => $this->hias->helpers->oDecrypt($application["authenticationMqttUser"]["value"])
                    ]);

                    $query = $this->hias->conn->prepare("
                        DELETE FROM mqttua
                        WHERE username = :username
                    ");
                    $query->execute([
                        ':username' => $this->hias->helpers->oDecrypt($application["authenticationMqttUser"]["value"])
                    ]);

                    $query = $this->hias->conn->prepare("
                        SELECT *
                        FROM amqpu
                        WHERE username = :username
                    ");
                    $query->execute([
                        ':username' => $this->hias->helpers->oDecrypt($application["authenticationAmqpUser"]["value"])
                    ]);
                    $amqp=$query->fetch(PDO::FETCH_ASSOC);

                    $query = $this->hias->conn->prepare("
                        DELETE FROM amqpu
                        WHERE username = :username
                    ");
                    $query->execute([
                        ':username' => $this->hias->helpers->oDecrypt($application["authenticationAmqpUser"]["value"])
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

                    $this->hias->hiasbch->contract->at($this->hias->hiasbch->confs["contract"])->send("deregsiter", "Application", $application["id"], ["from" => $_SESSION["HIAS"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash) {
                        if ($err !== null) {
                            $hash = "FAILED! " . $err;
                            return;
                        }
                        $hash = $resp;
                    });

                    if($hash == "FAILED"):
                        $actionMsg .= " HIASBCH deregsiter user application failed!\n";
                    else:
                        $this->hias->store_user_history("HIASBCH Deregister Authorized Application", $hash, 0, 0, 0, $application["id"]);
                        $balance = $this->hias->hiasbch->check_balance($_SESSION["HIAS"]["BC"]["BCUser"], $this->hias->helpers->oDecrypt($_SESSION["HIAS"]["BC"]["BCPass"]));
                        if($balanceMessage == ""):
                            $balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
                        endif;
                    endif;

                    $this->hias->hiasbch->icontract->at($this->hias->hiasbch->confs["icontract"])->send("deregisterAuthorized", $application["authenticationBlockchainUser"]["value"], ["from" => $_SESSION["HIAS"]["BC"]["BCUser"]], function ($err, $resp) use (&$hash, &$msg) {
                        if ($err !== null) {
                            $hash = "FAILED";
                            $msg = $err;
                            return;
                        }
                        $hash = $resp;
                    });

                    if($hash == "FAILED"):
                        $actionMsg .= " HIASBCH deregisterAuthorized failed!\n";
                    else:
                        $this->hias->store_user_history("HIASBCH Deregister Authorized User", $hash, 0, 0, 0, $application["id"]);
                        $balance = $this->hias->hiasbch->check_balance($_SESSION["HIAS"]["BC"]["BCUser"], $this->hias->helpers->oDecrypt($_SESSION["HIAS"]["BC"]["BCPass"]));
                        if($balanceMessage == ""):
                            $balanceMessage = " You were rewarded for this action! Your balance is now: " . $balance . " HIAS Ether!\n";
                        endif;
                    endif;

                endif;

                $application = $this->get_application($aid, "dateCreated,dateModified,*");

                return [
                    "Response"=> "OK",
                    "Message" => "Application updated!" . $actionMsg . $balanceMessage,
                    "Schema" => $application
                ];
            else:
                return [
                    "Response"=> "FAILED",
                    "Message" => "Application update failed!"
                ];
            endif;
        }

        public function application_life_graph($application, $limit = 0, $order = "")
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

            $request = $this->hias->hiashdi->request("GET", $this->hias->hiashdi->confs["data_url"] . "?type=Life&q=Use==Application;Application==". $application . $limiter . $orderer, []);
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

        public function update_application_life_graph($params=[])
        {

            $data = $this->application_life_graph(filter_input(INPUT_GET, "application", FILTER_SANITIZE_STRING), 100);

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

        public function application_sensors_graph($params=[])
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

            $request = $this->hias->hiashdi->request("GET", $this->hias->hiashdi->confs["data_url"] . "?type=Sensors&q=Use==Application;Application==". $params["application"] . $type . "&attrs=Value,Time,Type" . $limiter . $orderer, []);
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

        public function update_application_sensors_graph($params=[])
        {
            $types = [];
            $dates = [];
            $points = [];

            $data = $this->application_sensors_graph([
                "application" => filter_input(INPUT_GET, "application", FILTER_SANITIZE_STRING),
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

        public function get_application_statuses_data($application, $limit = 0, $order = "")
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

            $request = $this->hias->hiashdi->request("GET", $this->hias->hiashdi->confs["data_url"] . "?type=Statuses&q=Use==Application;Application==". $application . $limiter . $orderer, []);
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

        public function get_application_life_data($application, $limit = 0, $order = "")
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

            $request = $this->hias->hiashdi->request("GET", $this->hias->hiashdi->confs["data_url"] . "?type=Life&q=Use==Application;Application==". $application . $limiter . $orderer, []);
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

        public function get_application_sensors_data($application, $limit = 0, $order = "")
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

            $request = $this->hias->hiashdi->request("GET", $this->hias->hiashdi->confs["data_url"] . "?type=Sensors&q=Use==Application;Application==". $application . $limiter . $orderer, []);
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

        public function get_application_actuators_data($application, $limit = 0, $order = -1)
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

            $request = $this->hias->hiashdi->request("GET", $this->hias->hiashdi->confs["data_url"] . "?type=Actuators&q=Use==Application;Application==". $application . $limiter . $orderer, []);
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

        public function get_application_transactions($application, $limit = 0, $order = "")
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

            $request = $this->hias->hiashdi->request("GET", $this->hias->hiashdi->confs["data_url"] . "?type=Transactions&q=From==" . $application . $orderer . $limiter, []);
            $response = json_decode($request["body"], true);

            if(!isSet($response["Error"])):
                return  $response;
            else:
                return False;
            endif;
        }

        public function get_application_history($application, $limit = 0, $order = "")
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
                WHERE taid = :id
                $orderer
                $limiter
            ");
            $pdoQuery->execute([
                ":id" => $application
            ]);
            $response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
            return $response;
        }

        public function update_application_history()
        {
            $return = "";
            if(filter_input(INPUT_POST, 'applicationHistory', FILTER_SANITIZE_STRING) == "Activity"):
                $userDetails = "";
                $history = $this->get_application_history(filter_input(INPUT_GET, "application", FILTER_SANITIZE_STRING), 100);
                if(count($history)):
                    foreach($history as $key => $value):
                        if($value["uid"]):
                            $user = $this->hias->get_user($value["uid"]);
                            $userDetails = $user["name"]["value"];
                        endif;
                        if($value["hash"]):
                            $hash = '<a href="' . $this->hias->domain . '/HIASBCH/Explorer/Transaction/' . $value["hash"] . '">#' . $value["hash"] . '</a>';
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
                        "Message" => "Application Activity found!",
                        "Data" => $return
                    ];
                else:
                    return [
                        "Response" => "FAILED",
                        "Message" => "Application History not found!"
                    ];
                endif;
            elseif(filter_input(INPUT_POST, 'applicationHistory', FILTER_SANITIZE_STRING) == "Transactions"):
                $transactions = $this->get_application_transactions(filter_input(INPUT_POST, "ApplicationAddress", FILTER_SANITIZE_STRING), 100);
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
                        "Message" => "Application Transactions found!",
                        "Data" => $return
                    ];
                else:
                    return [
                        "Response" => "FAILED",
                        "Message" => "Application Transactions not found!"
                    ];
                endif;
            elseif(filter_input(INPUT_POST, 'applicationHistory', FILTER_SANITIZE_STRING) == "Statuses"):
                $Statuses = $this->get_application_statuses_data(filter_input(INPUT_GET, "application", FILTER_SANITIZE_STRING), 100);
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
                        "Message" => "Application Statuses found!",
                        "Data" => $return
                    ];
                else:
                    return [
                        "Response" => "FAILED",
                        "Message" => "Application Statuses not found!"
                    ];
                endif;
            elseif(filter_input(INPUT_POST, 'applicationHistory', FILTER_SANITIZE_STRING) == "Life"):
                $life = $this->get_application_life_data(filter_input(INPUT_GET, "application", FILTER_SANITIZE_STRING), 100);
                if($life["Response"] == "OK"):
                    foreach($life["ResponseData"] as $key => $value):
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
                        "Message" => "Application Life found!",
                        "Data" => $return
                    ];
                else:
                    return [
                        "Response" => "FAILED",
                        "Message" => "Application Life not found!"
                    ];
                endif;
            elseif(filter_input(INPUT_POST, 'applicationHistory', FILTER_SANITIZE_STRING) == "Sensors"):
                $Sensors = $this->get_application_sensors_data(filter_input(INPUT_GET, "application", FILTER_SANITIZE_STRING), 100);
                if($Sensors["Response"] == "OK"):
                    foreach($Sensors["ResponseData"] as $key => $value):
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
                        "Message" => "Application Sensors found!",
                        "Data" => $return
                    ];
                else:
                    return [
                        "Response" => "FAILED",
                        "Message" => "Application Sensors not found!"
                    ];
                endif;
            elseif(filter_input(INPUT_POST, 'applicationHistory', FILTER_SANITIZE_STRING) == "Actuators"):
                $Actuators = $this->get_application_actuators_data(filter_input(INPUT_GET, "application", FILTER_SANITIZE_STRING), 100);
                if($Actuators["Response"] == "OK"):
                    foreach($Actuators["ResponseData"] as $key => $value):
                        $values = "";
                        if(is_array($value["Value"])):
                            foreach($value["Value"] AS $key => $val):
                                $values .= "<strong>" . $key . ":</strong> " . $val . "<br />";
                            endforeach;
                        else:
                            $values = $value["Value"];
                        endif;
                        if($value['Message']):
                            $message = $value["Message"];
                        else:
                            $message = "NA";
                        endif;
                        $return .= "
                        <tr>
                            <td>
                                <div class='row'>
                                    <div class='col-lg-2 col-md-12 col-sm-12 col-xs-12'>ID:</div>
                                    <div class='col-lg-10 col-md-12 col-sm-12 col-xs-12'>" . $value['_id']['$oid'] . "</div>
                                    <div class='col-lg-2 col-md-12 col-sm-12 col-xs-12'>Type:</div>
                                    <div class='col-lg-10 col-md-12 col-sm-12 col-xs-12'>" . $value['Type'] . "</div>
                                    <div class='col-lg-2 col-md-12 col-sm-12 col-xs-12'>Values:</div>
                                    <div class='col-lg-10 col-md-12 col-sm-12 col-xs-12'>" . $values . "</div>
                                    <div class='col-lg-2 col-md-12 col-sm-12 col-xs-12'>Message:</div>
                                    <div class='col-lg-10 col-md-12 col-sm-12 col-xs-12'>" . $message . "</div>
                                    <div class='col-lg-2 col-md-12 col-sm-12 col-xs-12'>At:</div>
                                    <div class='col-lg-10 col-md-12 col-sm-12 col-xs-12'>" . $value['Time'] . "</div>
                                </div>

                            </td>
                        </tr>";
                    endforeach;
                    return [
                        "Response" => "OK",
                        "Message" => "Application Actuators found!",
                        "Data" => $return
                    ];
                else:
                    return [
                        "Response" => "FAILED",
                        "Message" => "Application Actuators not found!"
                    ];
                endif;
            else:
                return [
                    "Response" => "FAILED",
                    "Message" => "Application History not found!"
                ];
            endif;
        }

        public function reset_app_key()
        {
            $id = filter_input(INPUT_GET, 'application', FILTER_SANITIZE_STRING);
            $application = $this->get_application($id);

            if(isSet($application["Error"])):
                return [
                    "Response"=> "FAILED",
                    "Message" => "Application key reset failed!"
                ];
            endif;

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

            $request = $this->hias->hiascdi->request("POST", $this->hias->hiascdi->confs["entities_url"] . "/" . $application["id"] . "/attrs?type=Application", json_encode($data));
            $response = json_decode($request["body"], true);
            if(!isSet($response["Error"])):

                $htpasswd = new Htpasswd('/etc/nginx/security/htpasswd');
                $htpasswd->updateUser($application["id"], $privKey, Htpasswd::ENCTYPE_APR_MD5);

                $this->hias->store_user_history("HIAS Update Application Key", 0, 0, 0, 0, $application["id"]);

                return [
                    "Response"=> "OK",
                    "Message" => "Application key reset!",
                    "P" => $privKey
                ];
            else:
                return [
                    "Response"=> "FAILED",
                    "Message" => "Application key reset failed!"
                ];
            endif;
        }

        public function resetAppMqtt()
        {
            $id = filter_input(INPUT_GET, 'application', FILTER_SANITIZE_NUMBER_INT);
            $Application = $this->get_application($id);

            $mqttPass = $this->hias->helpers->password();
            $mqttHash = create_hash($mqttPass);

            $data = [
                "mqtt" => [
                    "username" => $Application["mqtt"]["username"],
                    "password" => $this->hias->helpers->oEncrypt($mqttPass),
                    "timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
                ],
                "dateModified" => [
                    "type" => "DateTime",
                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                ]
            ];

            $response = json_decode($this->hias->hiascdi->request("POST", $this->hias->hiascdi->confs["entities_url"] . "/" . $Application["id"] . "/attrs?type=Application", json_encode($data)), true);

            if($response["Response"]=="OK"):
                $query = $this->hias->conn->prepare("
                    UPDATE mqttu
                    SET pw = :pw
                    WHERE aid = :aid
                ");
                $query->execute(array(
                    ':pw' => $mqttHash,
                    ':aid' => $id
                ));

                $this->hias->store_user_history("Reset Application MQTT Password", 0, $Application["lid"]["value"], 0, 0, 0, $id);

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

        public function resetAppAmqpKey()
        {
            $id = filter_input(INPUT_GET, 'application', FILTER_SANITIZE_NUMBER_INT);
            $Application = $this->get_application($id);

            $amqpPass = $this->hias->helpers->password();
            $amqpHash = $this->hias->helpers->password_hash($amqpPass);

            $data = [
                "amqp" => [
                    "username" => $Application["amqp"]["username"],
                    "password" => $this->hias->helpers->oEncrypt($amqpPass),
                    "timestamp" => date('Y-m-d\TH:i:s.Z\Z', time())
                ],
                "dateModified" => [
                    "type" => "DateTime",
                    "value" => date('Y-m-d\TH:i:s.Z\Z', time())
                ]
            ];

            $response = json_decode($this->hias->hiascdi->request("POST", $this->hias->hiascdi->confs["entities_url"] . "/" . $Application["id"] . "/attrs?type=Application", json_encode($data)), true);

            if($response["Response"]=="OK"):
                $query = $this->hias->conn->prepare("
                    UPDATE amqpu
                    SET pw = :pw
                    WHERE username = :username
                ");
                $query->execute(array(
                    ':pw' => $this->hias->helpers->oEncrypt($amqpHash),
                    ':username' => $this->hias->helpers->oDecrypt($Application["amqp"]["username"])
                ));

                $this->hias->store_user_history("Reset Application AMQP Password", 0, $Application["lid"]["value"], 0, 0, 0, $id);

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

        public function retrieve_status_data($limit = 0, $order = "")
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

            $request = $this->hias->hiashdi->request("GET", $this->hias->hiashdi->confs["data_url"] . "?type=Statuses" . $limiter . $orderer, []);
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

        public function retrieve_life_data($limit = 0, $order = "")
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

            $request = $this->hias->hiashdi->request("GET", $this->hias->hiashdi->confs["data_url"] . "?type=Life" . $limiter . $orderer, []);
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

        public function retrieve_command_data($limit = 0, $order = "")
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

            $request = $this->hias->hiashdi->request("GET", $this->hias->hiashdi->confs["data_url"] . "?type=Commands" . $limiter . $orderer, []);
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

        public function retrieve_sensor_data($limit = 0, $order = "")
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

            $request = $this->hias->hiashdi->request("GET", $this->hias->hiashdi->confs["data_url"] . "?type=Sensors" . $limiter . $orderer, []);
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

        public function retrieve_actuator_data($limit = 0, $order = "")
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

            $request = $this->hias->hiashdi->request("GET", $this->hias->hiashdi->confs["data_url"] . "?type=Actuators" . $limiter . $orderer, []);
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

        public function get_application_life_stats()
        {
            $Application = $this->get_application(filter_input(INPUT_GET, 'application', FILTER_SANITIZE_STRING), "batteryLevel,cpuUsage,memoryUsage,hddUsage,temperature,networkStatus");

            if(!isSet($Application["Error"])):
                $response = [
                    "battery" => $Application["batteryLevel"]["value"],
                    "cpu" => $Application["cpuUsage"]["value"],
                    "mem" => $Application["memoryUsage"]["value"],
                    "hdd" => $Application["hddUsage"]["value"],
                    "tempr" => $Application["temperature"]["value"],
                    "status" => $Application["networkStatus"]["value"]
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

        public function get_environment_devices($limit = 0, $order = "id DESC")
        {
            $limiter = "";
            if($limit != 0):
                $limiter = "&limit=" . $limit;
            endif;

            $request = $this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "?type=Device&category=environment".$limiter, []);
            $devices = json_decode($request["body"], true);
            return $devices;
        }

        public function update_environment_sensors($params=[])
        {
            $dates = [];
            $temperatures = [];
            $humidities = [];
            $lights = [];
            $smokes = [];
            $data = $this->device_sensors_graph([
                "device" => filter_input(INPUT_POST, "currentSensor", FILTER_SANITIZE_STRING),
                "data" => "Temperature",
                "limit" => 100
            ]);
            if($data["Response"]=="OK"):
                $dates = array_column(json_decode(json_encode($data["ResponseData"],true)), 'Time');
                $temperatures = array_column(json_decode(json_encode($data["ResponseData"]),true), 'Value');
                $data = $this->device_sensors_graph([
                    "device" => filter_input(INPUT_POST, "currentSensor", FILTER_SANITIZE_STRING),
                    "data" => "Humidity",
                    "limit" => 100
                ]);
                $humidities = array_column(json_decode(json_encode($data["ResponseData"]),true), 'Value');
                $data = $this->device_sensors_graph([
                    "device" => filter_input(INPUT_POST, "currentSensor", FILTER_SANITIZE_STRING),
                    "data" => "Light",
                    "limit" => 100
                ]);
                $lights = array_column(json_decode(json_encode($data["ResponseData"]),true), 'Value');
                $data = $this->device_sensors_graph([
                    "device" => filter_input(INPUT_POST, "currentSensor", FILTER_SANITIZE_STRING),
                    "data" => "Smoke",
                    "limit" => 100
                ]);
                $smokes = array_column(json_decode(json_encode($data["ResponseData"]),true), 'Value');
                $dates = array_reverse($dates);
                $temperatures = array_reverse($temperatures);
                $humidities = array_reverse($humidities);
                $lights = array_reverse($lights);
                $smokes = array_reverse($smokes);
            endif;

            return [$dates, $temperatures, $humidities, $lights, $smokes];
        }

        public function get_deviceEType()
        {
            $pdoQuery = $this->hias->conn->prepare("
                SELECT *
                FROM mqttld
                WHERE id = :id
                ORDER BY id DESC
            ");
            $pdoQuery->execute([
                ":id" => filter_input(INPUT_GET, "device", FILTER_SANITIZE_NUMBER_INT)
            ]);
            $device=$pdoQuery->fetch(PDO::FETCH_ASSOC);
            $pdoQuery->closeCursor();
            $pdoQuery = null;

            $return = "";
            $options = "";

            if(filter_input(INPUT_POST, "etype", FILTER_SANITIZE_STRING) == ("sensors" || "actuators" || "models")):

                $attrs="&attrs=" . filter_input(INPUT_POST, "etype", FILTER_SANITIZE_STRING) . ".name.value";

                $device = json_decode($this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "/" . $device["apub"] . "?type=Device" . $attrs, []), true);

                if(filter_input(INPUT_POST, "etype", FILTER_SANITIZE_STRING) == "sensors"):
                    if(isSet($device)):
                        foreach($device["sensors"] AS $key => $value):
                            $options .= '<option value="'. $value["name"]["value"] . '">'. $value["name"]["value"] . '</option>';
                        endforeach;
                    endif;

                    $return = '<div class="form-group refresh">
                                    <label class="control-label mb-10">From Sensor Type</label>
                                    <select class="form-control" id="s_type" name="s_type" required>
                                        <option value="">PLEASE SELECT</option>
                                        ' . $options . '
                                    </select>
                                </div>';

                endif;

            endif;

            if($return):
                return  [
                    'Response'=>'OK',
                    'ResponseData'=>$return
                ];
            else:
                return  [
                    'Response'=>'FAILED'
                ];
            endif;
        }

        public function get_deviceSType()
        {
            $pdoQuery = $this->hias->conn->prepare("
                SELECT *
                FROM mqttld
                WHERE id = :id
                ORDER BY id DESC
            ");
            $pdoQuery->execute([
                ":id" => filter_input(INPUT_GET, "device", FILTER_SANITIZE_NUMBER_INT)
            ]);
            $device=$pdoQuery->fetch(PDO::FETCH_ASSOC);
            $pdoQuery->closeCursor();
            $pdoQuery = null;

            $return = "";
            $options = "";

            $attrs="&attrs=" . filter_input(INPUT_POST, "etype", FILTER_SANITIZE_STRING) . ".properties";

            $device = json_decode($this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "/" . $device["apub"] . "?type=Device" . $attrs, []), true);

            if(isSet($device)):
                foreach($device["sensors"] AS $key => $value):
                    foreach($value["properties"] AS $pkey => $pvalue):
                        $options .= '<option value="'. $pkey . '">'. $pkey . '</option>';
                    endforeach;
                endforeach;
            endif;

            $return = '<div class="form-group refresh srefresh">
                            <label class="control-label mb-10">From Sensor</label>
                            <select class="form-control" id="s_sensor" name="s_sensor" required>
                                <option value="">PLEASE SELECT</option>
                                ' . $options . '
                            </select>
                        </div>';

            if($return):
                return  [
                    'Response'=>'OK',
                    'ResponseData'=>$return
                ];
            else:
                return  [
                    'Response'=>'FAILED'
                ];
            endif;
        }

        public function get_deviceSValueRange()
        {

            $return = '<div class="form-group refresh srefresh srrefresh">
                            <label class="control-label mb-10">With Sensor Value Range</label>
                            <select class="form-control" id="s_range" name="s_range" required>
                                <option value="">PLEASE SELECT</option>
                                <option value="lower">Lower Than</option>
                                <option value="higher">Higher Than</option>
                                <option value="lower_equal">Lower Than Or Equal To</option>
                                <option value="higher_equal">Higher Than Or Equal To</option>
                                <option value="equal">Equal To</option>
                            </select>
                        </div>
                        <div class="form-group refresh srefresh srrefresh">
                            <label for="name" class="control-label mb-10">With Sensor Value</label>
                            <input type="text" class="form-control" id="s_value" name="s_value" placeholder="Rule Trigger Value" required value="" required>
                        </div>';

            $devices = json_decode($this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "?type=Device&category=Output&attrs=id,name.value,actuators", []), true);

            $options2 = "";
            if(isSet($devices)):
                foreach($devices AS $key => $value):
                    $options2 .= '<option value="'. $value["id"] . '">'. $value["name"]["value"] . '</option>';
                endforeach;
            endif;

            $return .= '<div class="form-group refresh srefresh srrefresh">
                            <label class="control-label mb-10">Send Output Device Command</label>
                            <select class="form-control" id="o_device" name="o_device" required>
                                <option value="">PLEASE SELECT</option>
                                ' . $options2 . '
                            </select>
                        </div>';

            return  [
                'Response'=>'OK',
                'ResponseData'=>$return
            ];
        }

        public function getODevice()
        {

            $return = "";
            $options = "";
            $options2 = "";

            $device = json_decode($this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "/" . filter_input(INPUT_POST, "dtype", FILTER_SANITIZE_STRING). "?type=Device&attrs=id,name.value,actuators", []), true);

            if(isSet($device)):
                foreach($device["actuators"] AS $key => $value):
                    $options .= '<option value="'. $value["name"]["value"] . '">'. $value["name"]["value"] . '</option>';
                endforeach;
            endif;

            $return = '<div class="form-group refresh srefresh srrefresh orefresh">
                            <label class="control-label mb-10">To Actuator</label>
                            <select class="form-control" id="o_a_type" name="o_a_type" required>
                                <option value="">PLEASE SELECT</option>
                                ' . $options . '
                            </select>
                        </div>';

            if($return):
                return  [
                    'Response'=>'OK',
                    'ResponseData'=>$return
                ];
            else:
                return  [
                    'Response'=>'FAILED'
                ];
            endif;
        }

        public function GetODeviceACommands()
        {
            $return = "";
            $options = "";
            $options2 = "";

            $device = json_decode($this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "/" . filter_input(INPUT_POST, "dtype", FILTER_SANITIZE_STRING). "?type=Device&attrs=actuators." . filter_input(INPUT_POST, "atype", FILTER_SANITIZE_STRING) . ".commands", []), true);

            if(isSet($device)):
                foreach($device["actuators"] AS $key => $value):
                    $options .= '<option value="'. $value["name"]["value"] . '">'. $value["name"]["value"] . '</option>';
                endforeach;
            endif;

            $return = '<div class="form-group refresh srefresh srrefresh orefresh">
                            <label class="control-label mb-10">Command To Send</label>
                            <select class="form-control" id="o_a_c_type" name="o_a_c_type" required>
                                <option value="">PLEASE SELECT</option>
                                ' . $options . '
                            </select>
                        </div>';

            if($return):
                return  [
                    'Response'=>'OK',
                    'ResponseData'=>$device
                ];
            else:
                return  [
                    'Response'=>'FAILED'
                ];
            endif;
        }

        public function get_stats()
        {
            $stats = [];
            if($this->hias->confs["aid"] !== ""):
                $request =  $this->hias->hiascdi->request("GET", $this->hias->hiascdi->confs["entities_url"] . "/" . $this->hias->confs["aid"] . "?type=HIASBCH&attrs=batteryLevel.value,cpuUsage.value,memoryUsage.value,hddUsage.value,temperature.value", []);
                $context =  json_decode($request["body"], true);
                if(!isSet($context["Error"])):
                    $stats["cpu"] = $context["cpuUsage"]["value"];
                    $stats["mem"] = $context["memoryUsage"]["value"];
                    $stats["hdd"] = $context["hddUsage"]["value"];
                    $stats["tempr"] = $context["temperature"]["value"];
                endif;
            endif;

            return $stats;
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

        public function reset_device_amqp_key()
        {
            $id = filter_input(INPUT_GET, 'device', FILTER_SANITIZE_STRING);
            $device = $this->get_device($id);

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

            $response = json_decode($this->hias->hiascdi->request("POST", $this->hias->hiascdi->confs["entities_url"] . "/" . $device["id"] . "/attrs?type=Device", json_encode($data)), true);

            if($response["Response"]=="OK"):
                $query = $this->hias->conn->prepare("
                    UPDATE amqpu
                    SET pw = :pw
                    WHERE username = :username
                ");
                $query->execute(array(
                    ':pw' => $this->hias->helpers->oEncrypt($amqpHash),
                    ':username' => $this->hias->helpers->oDecrypt($device["amqp"]["username"])
                ));

                $this->hias->store_user_history("Reset Device AMQP Key", 0, $device["networkLocation"]["value"], $Device["networkZone"]["value"], $id);

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

        public function tempCreateCredentials(){

            $mqttUser = $this->hias->helpers->generate_uuid();
            $mqttPass = $this->hias->helpers->password();
            $mqttHash = create_hash($mqttPass);

            $pubKey = "3c7068ff-234b-4124-8403-e812522e29a0";
            $privKey = $this->hias->helpers->generate_key(32);
            $privKeyHash = $this->hias->helpers->password_hash($privKey);

            $amqppubKey = $this->hias->helpers->generate_uuid();
            $amqpprvKey = $this->hias->helpers->generate_key(32);
            $amqpKeyHash = $this->hias->helpers->password_hash($amqpprvKey);

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
                ':topic' => $location["id"] . "/Devices/" . $zone["id"] . "/" . $pubKey . "/#",
                ':rw' => 4
            ));

            $amid = $this->addAmqpUser($amqppubKey, $amqpKeyHash);
            $this->addAmqpUserVh($amid, "iotJumpWay");
            $this->addAmqpVhPerm($amid, "iotJumpWay", "exchange", "Core", "read");
            $this->addAmqpVhPerm($amid, "iotJumpWay", "exchange", "Core", "write");
            $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Statuses", "read");
            $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Statuses", "write");
            $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Life", "read");
            $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Life", "write");
            $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Sensors", "read");
            $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Sensors", "write");
            $this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "read", "Statuses");
            $this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "write", "Statuses");
            $this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "read", "Life");
            $this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "write", "Life");
            $this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "read", "Sensors");
            $this->addAmqpVhTopic($amid, "iotJumpWay", "topic", "Core", "write", "Sensors");

            $this->addAmqpUserPerm($amid, "administrator");
            $this->addAmqpUserPerm($amid, "managment");
            $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Statuses", "configure");
            $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Life", "configure");
            $this->addAmqpVhPerm($amid, "iotJumpWay", "queue", "Sensors", "configure");

            return [
                "Response"=> "OK",
                "Message" => "OK",
                "MU" => $this->hias->helpers->oEncrypt($mqttUser),
                "MP" => $this->hias->helpers->oEncrypt($mqttPass),
                "AMU" => $this->hias->helpers->oEncrypt($amqppubKey),
                "AMP" => $this->hias->helpers->oEncrypt($amqpprvKey),
                "AppID" => $pubKey,
                "AppKeyEnc" => $this->hias->helpers->oEncrypt($privKeyHash)
            ];

        }

        public function raise()
        {
            return [
                "lid" => $_SESSION["HIAS"]["Mqtt"]["Location"],
                "aid" => $_SESSION["HIAS"]["Mqtt"]["Application"],
                "an" => $_SESSION["HIAS"]["Mqtt"]["ApplicationName"],
                "un" => $this->hias->helpers->oDecrypt($_SESSION["HIAS"]["Mqtt"]["User"]),
                "uc" => $this->hias->helpers->oDecrypt($_SESSION["HIAS"]["Mqtt"]["Pass"]),
                "bcid" => $_SESSION["HIAS"]["Identifier"],
                "bcaddr" => $_SESSION["HIAS"]["BC"]["BCUser"]
            ];
        }

    }

    $iotJumpWay = new iotJumpWay($HIAS);

    if(filter_input(INPUT_POST, 'getServerStats', FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($iotJumpWay->get_stats()));
    endif;

    if(filter_input(INPUT_POST, "raise", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($iotJumpWay->raise()));
    endif;

    if(filter_input(INPUT_POST, "update_location", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($iotJumpWay->update_location()));
    endif;
    if(filter_input(INPUT_POST, "create_zone", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($iotJumpWay->create_zone()));
    endif;
    if(filter_input(INPUT_POST, "update_zone", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($iotJumpWay->update_zone()));
    endif;
    if(filter_input(INPUT_POST, "create_device", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($iotJumpWay->create_device()));
    endif;
    if(filter_input(INPUT_POST, "update_device", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($iotJumpWay->update_device()));
    endif;
    if(filter_input(INPUT_POST, "update_device_life_graph", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($iotJumpWay->update_device_life_graph()));
    endif;
    if(filter_input(INPUT_POST, "update_device_sensors_graph", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($iotJumpWay->update_device_sensors_graph()));
    endif;
    if(filter_input(INPUT_POST, "update_device_history", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($iotJumpWay->update_device_history()));
    endif;
    if(filter_input(INPUT_POST, "create_application", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($iotJumpWay->create_application()));
    endif;
    if(filter_input(INPUT_POST, "update_application", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($iotJumpWay->update_application()));
    endif;
    if(filter_input(INPUT_POST, "update_application_life_graph", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($iotJumpWay->update_application_life_graph()));
    endif;
    if(filter_input(INPUT_POST, "update_application_sensors_graph", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($iotJumpWay->update_application_sensors_graph()));
    endif;
    if(filter_input(INPUT_POST, "update_application_history", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($iotJumpWay->update_application_history()));
    endif;
    if(filter_input(INPUT_POST, "reset_mqtt_app", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($iotJumpWay->resetAppMqtt()));
    endif;
    if(filter_input(INPUT_POST, "reset_mqtt_dvc", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($iotJumpWay->resetDvcMqtt()));
    endif;
    if(filter_input(INPUT_POST, "reset_key_dvc", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($iotJumpWay->resetDvcKey()));
    endif;
    if(filter_input(INPUT_POST, "reset_app_apriv", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($iotJumpWay->reset_app_key()));
    endif;
    if(filter_input(INPUT_POST, "reset_app_amqp", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($iotJumpWay->resetAppAmqpKey()));
    endif;
    if(filter_input(INPUT_POST, "reset_dvc_amqp", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($iotJumpWay->reset_device_amqp_key()));
    endif;
    if(filter_input(INPUT_POST, "get_life", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($iotJumpWay->get_device_life()));
    endif;
    if(filter_input(INPUT_POST, "get_alife", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($iotJumpWay->get_application_life_stats()));
    endif;
    if(filter_input(INPUT_POST, "get_environment_sensors", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($iotJumpWay->update_environment_sensors()));
    endif;
    if(filter_input(INPUT_POST, "get_e_type", FILTER_VALIDATE_BOOLEAN)):
        die(json_encode($iotJumpWay->get_deviceEType()));
    endif;
    if(filter_input(INPUT_POST, "get_s_type", FILTER_VALIDATE_BOOLEAN)):
        die(json_encode($iotJumpWay->get_deviceSType()));
    endif;
    if(filter_input(INPUT_POST, "get_sv_range", FILTER_VALIDATE_BOOLEAN)):
        die(json_encode($iotJumpWay->get_deviceSValueRange()));
    endif;
    if(filter_input(INPUT_POST, "get_out_device", FILTER_VALIDATE_BOOLEAN)):
        die(json_encode($iotJumpWay->getODevice()));
    endif;
    if(filter_input(INPUT_POST, "get_out_device_actuator", FILTER_VALIDATE_BOOLEAN)):
        die(json_encode($iotJumpWay->GetODeviceACommands()));
    endif;
