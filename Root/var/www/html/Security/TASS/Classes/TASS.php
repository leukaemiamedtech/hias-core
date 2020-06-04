<?php

    class TASS
    {
        function __construct($_GeniSys)
        {
            $this->_GeniSys = $_GeniSys;
        }

        public function getDevices()
        {
            $pdoQuery = $this->_GeniSys->_secCon->prepare("
                SELECT tass.id,
                    tass.name,
                    tass.type,
                    tass.lid,
                    tass.zid,
                    tass.did,
                    location.name as loc,
                    zone.zn as zne,
                    device.name as dvc,
                    device.status
                FROM tass tass
				INNER JOIN mqttld device
				ON tass.did = device.id 
				INNER JOIN mqttl location
				ON tass.lid = location.id 
				INNER JOIN mqttlz zone
				ON tass.zid = zone.id 
                ORDER BY id DESC
            ");
            $pdoQuery->execute();
            $response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
            $pdoQuery->closeCursor();
            $pdoQuery = null;
            return $response;
        }

        public function getDevice($id)
        {
            $pdoQuery = $this->_GeniSys->_secCon->prepare("
                SELECT tass.id,
                    tass.lid,
                    tass.zid,
                    tass.did,
                    tass.ip,
                    tass.mac,
                    tass.name,
                    tass.sport,
                    tass.sportf,
                    tass.sckport,
                    tass.strdir,
                    device.status,
                    device.lt,
                    device.lg,
                    device.tempr,
                    device.hdd,
                    device.mem,
                    device.cpu,
                    device.mqttu,
                    device.mqttp 
                FROM tass tass
                INNER JOIN mqttld device
                ON device.id = tass.did
                WHERE tass.id = :id 
            ");
            $pdoQuery->execute([
                ":id" => $id
            ]);
            $response=$pdoQuery->fetch(PDO::FETCH_ASSOC);
            return $response;
        }

        public function createDevice()
        {
            if(!filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "TASS device name is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "type", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "TASS device types is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "iotJumpWay location id is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "iotJumpWay zone id is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device IP is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device MAC is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "sport", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device stream port is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "sportf", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device stream file is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "strdir", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device stream directory is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "sckport", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device socket port is required"
                ];
            endif;

            $mqttUser = $this->_GeniSys->_helpers->generateKey(12);
            $mqttPass = $this->_GeniSys->_helpers->password();
            $mqttHash = create_hash($mqttPass);
    
            $apiKey = $this->_GeniSys->_helpers->generateKey(30);
            $apiSecretKey = $this->_GeniSys->_helpers->generateKey(35);
			
			$query = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  mqttld  (
					`lid`,
					`zid`,
					`name`,
					`mqttu`,
					`mqttp`,
					`apub`,
					`aprv`,
					`ip`,
					`mac`,
					`lt`,
					`lg`,
					`time`
				)  VALUES (
					:lid,
					:zid,
					:name,
					:mqttu,
					:mqttp,
					:apub,
					:aprv,
					:ip,
					:mac,
					:lt,
					:lg,
					:time
				)
			");
			$query->execute([
				':lid' => filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT),
				':zid' => filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT),
				':name' => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING),
				':mqttu' =>$this->_GeniSys->_helpers->oEncrypt($mqttUser),
				':mqttp' =>$this->_GeniSys->_helpers->oEncrypt($mqttPass),
				':apub' => $this->_GeniSys->_helpers->oEncrypt($apiKey),
				':aprv' => $this->_GeniSys->_helpers->oEncrypt($apiSecretKey),
				':ip' => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING)),
				':mac' => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING)),
				':lt' => "",
				':lg' => "",
				':time' => time()
			]);
            $this->did = $this->_GeniSys->_secCon->lastInsertId();
    
            $query = $this->_GeniSys->_secCon->prepare("
                INSERT INTO  mqttu  (
                    `lid`,
                    `zid`,
                    `did`,
                    `uname`,
                    `pw`
                )  VALUES (
                    :lid,
                    :zid,
                    :did,
                    :uname,
                    :pw
                )
            ");
            $query->execute([
                ':lid' => filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT),
                ':zid' => filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT),
                ':did' => $this->did,
                ':uname' => $mqttUser,
                ':pw' => $mqttHash
            ]);
    
            $query = $this->_GeniSys->_secCon->prepare("
                INSERT INTO  mqttua  (
                    `lid`,
                    `zid`,
                    `did`,
                    `username`,
                    `topic`,
                    `rw`
                )  VALUES (
                    :lid,
                    :zid,
                    :did,
                    :username,
                    :topic,
                    :rw
                )
            ");
            $query->execute(array(
                ':lid' => filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT),
                ':zid' => filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT),
                ':did' => $this->did,
                ':username' => $mqttUser,
                ':topic' => filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT) . "/Device/" . filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT) . "/" . $this->did . "#",
                ':rw' => 4
            ));
    
            $query = $this->_GeniSys->_secCon->prepare("
                UPDATE mqttl
                SET devices = devices + 1
                WHERE id = :id
            ");
            $query->execute(array(
                ':id'=>filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT)
            ));

            $pdoQuery = $this->_GeniSys->_secCon->prepare("
                INSERT INTO  tass  (
                    `name`,
                    `type`,
                    `lid`,
                    `zid`,
                    `did`,
                    `ip`,
                    `mac`,
                    `sport`,
                    `sportf`,
                    `sckport`,
                    `strdir`
                )  VALUES (
                    :name,
                    :type,
                    :lid,
                    :zid,
                    :did,
                    :ip,
                    :mac,
                    :sport,
                    :sportf,
                    :sckport,
                    :strdir
                )
            ");
            $pdoQuery->execute([
                ":name" => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING),
                ":type" => filter_input(INPUT_POST, "type", FILTER_SANITIZE_STRING),
                ":lid" => filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT),
                ":zid" => filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT),
                ":did" => $this->did,
                ":ip" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING)),
                ":mac" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING)),
                ":sport" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sport", FILTER_SANITIZE_STRING)),
                ":sportf" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sportf", FILTER_SANITIZE_STRING)),
                ":sckport" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sckport", FILTER_SANITIZE_STRING)),
                ":strdir" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "strdir", FILTER_SANITIZE_STRING))
            ]);
            $tid = $this->_GeniSys->_secCon->lastInsertId();
            $pdoQuery->closeCursor();
            $pdoQuery = null;

            return [
                "Response"=> "OK", 
                "Message" => "Device created!", 
                "DID" => $tid
            ];
        }

        public function updateDevice()
        {
            if(!filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "ID is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "TASS device name is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "iotJumpWay location id is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "iotJumpWay zone id is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "did", FILTER_SANITIZE_NUMBER_INT)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "iotJumpWay device id is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device IP is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device MAC is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "sport", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device stream port is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "sportf", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device stream file is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "strdir", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device stream directory is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "sckport", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device socket port is required"
                ];
            endif;

            $pdoQuery = $this->_GeniSys->_secCon->prepare("
                UPDATE tass
                SET name = :name,
                    lid = :lid, 
                    zid = :zid, 
                    did = :did, 
                    ip = :ip, 
                    mac = :mac, 
                    sport = :sport, 
                    sportf = :sportf, 
                    sckport = :sckport, 
                    strdir = :strdir
                WHERE id = :id 
            ");
            $pdoQuery->execute([
                ":name" => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING),
                ":lid" => filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT),
                ":zid" => filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT),
                ":did" => filter_input(INPUT_POST, "did", FILTER_SANITIZE_NUMBER_INT),
                ":ip" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING)),
                ":mac" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING)),
                ":sport" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sport", FILTER_SANITIZE_STRING)),
                ":sportf" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sportf", FILTER_SANITIZE_STRING)),
                ":sckport" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sckport", FILTER_SANITIZE_STRING)),
                ":strdir" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "strdir", FILTER_SANITIZE_STRING)),
                ":id" => filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)
            ]);
            $pdoQuery->closeCursor();
            $pdoQuery = null;
            return [
                "Response"=> "OK", 
                "Message" => "Device updated!"
            ];
        }

        public function resetMqtt()
        {
            $pdoQuery = $this->_GeniSys->_secCon->prepare("
                SELECT uname
                FROM mqttu
                WHERE did = :did
            ");
            $pdoQuery->execute([
                ":did" => filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)
            ]);
            $mqtt=$pdoQuery->fetch(PDO::FETCH_ASSOC);
            $pdoQuery->closeCursor();
            $pdoQuery = null;

            $mqttPass = $this->_GeniSys->_helpers->password();
            $mqttHash = create_hash($mqttPass);

            #$htpasswd = new Htpasswd('/etc/nginx/tass/htpasswd');
            #$htpasswd->updateUser($mqtt["uname"], $mqttPass, Htpasswd::ENCTYPE_APR_MD5);
    
            $query = $this->_GeniSys->_secCon->prepare("
                UPDATE mqttld
                SET mqttp = :mqttp 
                WHERE id = :id
            ");
            $query->execute(array(
                ':mqttp' => $this->_GeniSys->_helpers->oEncrypt($mqttPass),
                ':id' => filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)
            ));
    
            $query = $this->_GeniSys->_secCon->prepare("
                UPDATE mqttu
                SET pw = :pw 
                WHERE did = :did
            ");
            $query->execute(array(
                ':pw' => $mqttHash,
                ':did' => filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)
            ));

            return [
                "Response"=> "OK", 
                "Message" => "MQTT password reset!", 
                "P" => $mqttPass
            ];

        }	

		public function getLife()
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT id,
					cpu,
					mem,
					hdd,
					tempr,
					status
				FROM mqttld
				WHERE id = :id 
			");
			$pdoQuery->execute([
				":id" => filter_input(INPUT_POST, "device", FILTER_SANITIZE_NUMBER_INT)
			]);
			$response=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			
			if($response["id"]):
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

		public function getMapMarkers($device)
		{
            if(!$device["lt"]):
                $lat = $this->_GeniSys->lt;
                $lng = $this->_GeniSys->lg;
            else:
                $lat = $device["lt"];
                $lng = $device["lg"];
            endif;

            return [$lat, $lng];
		}	

		public function getStatusShow($status)
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

    }
    
    $TASS = new TASS($_GeniSys);

    if(filter_input(INPUT_POST, "update_tass", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($TASS->updateDevice()));
    endif;
    if(filter_input(INPUT_POST, "create_tass", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($TASS->createDevice()));
    endif;
    if(filter_input(INPUT_POST, "reset_mqtt", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($TASS->resetMqtt()));
    endif;
	if(filter_input(INPUT_POST, "get_tlife", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($TASS->getLife()));
	endif;
	