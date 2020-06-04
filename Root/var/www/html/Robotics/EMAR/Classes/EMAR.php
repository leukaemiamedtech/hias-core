<?php

    class EMAR
    {

        function __construct($_GeniSys)
        {
            $this->_GeniSys = $_GeniSys;
        }

        public function getDevices()
        {
            $pdoQuery = $this->_GeniSys->_secCon->prepare("
                SELECT emar.id,
                    emar.name,
                    emar.lid,
                    emar.zid,
                    emar.did,
                    emar.did2,
                    emar.did3,
                    device.name as dname,
                    device.status,
                    device.lt,
                    device.lg,
                    device.mqttu,
                    device.mqttp,
                    device2.name as dname2,
                    device2.status as status2,
                    device2.lt as lt2,
                    device2.lg as lg2,
                    device2.mqttu as mqttu2,
                    device2.mqttp as mqttp2,
                    device3.name as dname3,
                    device3.status as status3,
                    device3.lt as lt3,
                    device3.lg as lg3,
                    device3.mqttu as mqttu3,
                    device3.mqttp as mqttp3
                FROM emar emar
                INNER JOIN mqttld device
                ON device.id = emar.did
                INNER JOIN mqttld device2
                ON device2.id = emar.did2
                INNER JOIN mqttld device3
                ON device3.id = emar.did3
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
                SELECT emar.id,
                    emar.name,
                    emar.lid,
                    emar.zid,
                    emar.did,
                    emar.ip,
                    emar.mac,
                    emar.sport,
                    emar.sportf,
                    emar.sckport,
                    emar.sdir,
                    emar.sdir2,
                    emar.sdir3,
                    device.status,
                    device.lt,
                    device.lg,
                    device.tempr,
                    device.hdd,
                    device.mem,
                    device.cpu,
                    device.mqttu,
                    device.mqttp,
                    emar.name2,
                    emar.did2,
                    emar.ip2,
                    emar.mac2,
                    emar.sport2,
                    emar.sportf2,
                    emar.sckport2,
                    emar.name3,
                    emar.did3,
                    emar.ip3,
                    emar.mac3,
                    emar.sport3,
                    emar.sportf3,
                    emar.sckport3,
                    device2.status as status2,
                    device2.lt as lt2,
                    device2.lg as lg2,
                    device2.tempr as tempr2,
                    device2.hdd as hdd2,
                    device2.mem as mem2,
                    device2.cpu as cpu2,
                    device2.mqttu as mqttu2,
                    device2.mqttp as mqttp2,
                    device3.status as status3,
                    device3.lt as lt3,
                    device3.lg as lg3,
                    device3.tempr as tempr3,
                    device3.hdd as hdd3,
                    device3.mem as mem3,
                    device3.cpu as cpu3,
                    device3.mqttu as mqttu3,
                    device3.mqttp as mqttp3
                FROM emar emar 
                INNER JOIN mqttld device
                ON device.id = emar.did
                INNER JOIN mqttld device2
                ON device2.id = emar.did2
                INNER JOIN mqttld device3
                ON device3.id = emar.did3
                WHERE emar.id = :id 
            ");
            $pdoQuery->execute([
                ":id" => $id
            ]);
            $response=$pdoQuery->fetch(PDO::FETCH_ASSOC);
            return $response;
        }
        
        public function createiDevice($params = [])
        {
            
            $query = $this->_GeniSys->_secCon->prepare("
                INSERT INTO  mqttld  (
                    `lid`,
                    `zid`,
                    `name`,
                    `mqttu`,
                    `mqttp`,
                    `apub`,
                    `aprv`,
                    `time`
                )  VALUES (
                    :lid,
                    :zid,
                    :name,
                    :mqttu,
                    :mqttp,
                    :apub,
                    :aprv,
                    :time
                )
            ");
            $query->execute([
                ':lid' => $params["lid"],
                ':zid' => $params["zid"],
                ':name' => $params["name"],
                ':mqttu' =>$this->_GeniSys->_helpers->oEncrypt($params["mqttu"]),
                ':mqttp' =>$this->_GeniSys->_helpers->oEncrypt($params["mqttp"]),
                ':apub' => $this->_GeniSys->_helpers->oEncrypt($params["apub"]),
                ':aprv' => $this->_GeniSys->_helpers->oEncrypt($params["aprv"]),
                ':time' => time()
            ]);
            $did = $this->_GeniSys->_secCon->lastInsertId();
    
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
                ':lid' => $params["lid"],
                ':zid' => $params["zid"],
                ':did' => $did,
                ':uname' => $params["mqttu"],
                ':pw' => $params["mqttHash"]
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
                ':lid' => $params["lid"],
                ':zid' => $params["zid"],
                ':did' => $did,
                ':username' => $params["mqttu"],
                ':topic' => $params["lid"] . "/Device/" . $params["zid"] . "/" . $did . "#",
                ':rw' => 4
            ));
    
            $query = $this->_GeniSys->_secCon->prepare("
                UPDATE mqttl
                SET devices = devices + 1
                WHERE id = :id
            ");
            $query->execute(array(
                ':id'=>$params["lid"]
            ));

            return $did;
        }
        public function createDevice()
        {
            
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
            if(!filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "EMAR device name is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "name2", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "EMAR device 2 name is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "name3", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "EMAR device 3 name is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device IP is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "ip2", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device 2 IP is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "ip3", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device 3 IP is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device MAC is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "mac2", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device 2 MAC is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "mac3", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device 3 MAC is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "sport2", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device 2 stream port is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "sport3", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device 3 stream port is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "sportf2", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device 2 stream file is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "sportf3", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device 3 stream file is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "sckport2", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device 2 socket port is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "sckport3", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device 3 socket port is required"
                ];
            endif;

            # Device 1
            $mqttUser = $this->_GeniSys->_helpers->generateKey(12);
            $mqttPass = $this->_GeniSys->_helpers->password();
            $mqttHash = create_hash($mqttPass);
    
            $apiKey = $this->_GeniSys->_helpers->generateKey(30);
            $apiSecretKey = $this->_GeniSys->_helpers->generateKey(35);

            $did = $this->createiDevice([
                "lid" => filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT),
                "zid" => filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT),
                'name' => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING),
                'mqttu' => $mqttUser,
                'mqttp' => $mqttPass,
                'mqttHash' => $mqttHash,
                'apub' => $apiKey,
                'aprv' => $apiSecretKey
            ]);

            # Device 2
            $mqttUser2 = $this->_GeniSys->_helpers->generateKey(12);
            $mqttPass2 = $this->_GeniSys->_helpers->password();
            $mqttHash2 = create_hash($mqttPass2);
    
            $apiKey2 = $this->_GeniSys->_helpers->generateKey(30);
            $apiSecretKey2 = $this->_GeniSys->_helpers->generateKey(35);

            $did2 = $this->createiDevice([
                "lid" => filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT),
                "zid" => filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT),
                'name' => filter_input(INPUT_POST, "name2", FILTER_SANITIZE_STRING),
                'mqttu' => $mqttUser2,
                'mqttp' => $mqttPass2,
                'mqttHash' => $mqttHash2,
                'apub' => $apiKey2,
                'aprv' => $apiSecretKey2
            ]);

            # Device 3
            $mqttUser3 = $this->_GeniSys->_helpers->generateKey(12);
            $mqttPass3 = $this->_GeniSys->_helpers->password();
            $mqttHash3 = create_hash($mqttPass3);
    
            $apiKey3 = $this->_GeniSys->_helpers->generateKey(30);
            $apiSecretKey3 = $this->_GeniSys->_helpers->generateKey(35);

            $did3 = $this->createiDevice([
                "lid" => filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT),
                "zid" => filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT),
                'name' => filter_input(INPUT_POST, "name3", FILTER_SANITIZE_STRING),
                'mqttu' => $mqttUser3,
                'mqttp' => $mqttPass3,
                'mqttHash' => $mqttHash3,
                'apub' => $apiKey3,
                'aprv' => $apiSecretKey3
            ]);

            $pdoQuery = $this->_GeniSys->_secCon->prepare("
                INSERT INTO  emar  (
                    `name`,
                    `name2`,
                    `name3`,
                    `lid`,
                    `zid`,
                    `did`,
                    `did2`,
                    `did3`,
                    `ip`,
                    `ip2`,
                    `ip3`,
                    `mac`,
                    `mac2`,
                    `mac3`,
                    `sport`,
                    `sport2`,
                    `sport3`,
                    `sportf`,
                    `sportf2`,
                    `sportf3`,
                    `sckport`,
                    `sckport2`,
                    `sckport3`
                )  VALUES (
                    :name,
                    :name2,
                    :name3,
                    :lid,
                    :zid,
                    :did,
                    :did2,
                    :did3,
                    :ip,
                    :ip2,
                    :ip3,
                    :mac,
                    :mac2,
                    :mac3,
                    :sport,
                    :sport2,
                    :sport3,
                    :sportf,
                    :sportf2,
                    :sportf3,
                    :sckport,
                    :sckport2,
                    :sckport3
                )
            ");
            $pdoQuery->execute([
                ":name" => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING),
                ":name2" => filter_input(INPUT_POST, "name2", FILTER_SANITIZE_STRING),
                ":name3" => filter_input(INPUT_POST, "name3", FILTER_SANITIZE_STRING),
                ":lid" => filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT),
                ":zid" => filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT),
                ":did" => $did,
                ":did2" => $did2,
                ":did3" => $did3,
                ":ip" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING)),
                ":ip2" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "ip2", FILTER_SANITIZE_STRING)),
                ":ip3" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "ip3", FILTER_SANITIZE_STRING)),
                ":mac" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING)),
                ":mac2" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "mac2", FILTER_SANITIZE_STRING)),
                ":mac3" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "mac3", FILTER_SANITIZE_STRING)),
                ":sport" => filter_input(INPUT_POST, "sport", FILTER_SANITIZE_STRING) ? $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sport", FILTER_SANITIZE_STRING)) : "",
                ":sport2" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sport2", FILTER_SANITIZE_STRING)),
                ":sport3" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sport3", FILTER_SANITIZE_STRING)),
                ":sportf" => filter_input(INPUT_POST, "sportf", FILTER_SANITIZE_STRING) ? $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sportf", FILTER_SANITIZE_STRING)) : "",
                ":sportf2" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sportf2", FILTER_SANITIZE_STRING)),
                ":sportf3" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sportf3", FILTER_SANITIZE_STRING)),
                ":sckport" => filter_input(INPUT_POST, "sckport", FILTER_SANITIZE_STRING) ? $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sckport", FILTER_SANITIZE_STRING)) : "",
                ":sckport2" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sckport2", FILTER_SANITIZE_STRING)),
                ":sckport3" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sckport3", FILTER_SANITIZE_STRING))
            ]);
            $tid = $this->_GeniSys->_secCon->lastInsertId();
            $pdoQuery->closeCursor();
            $pdoQuery = null;

            return [
                "Response"=> "OK", 
                "Message" => "EMAR Device created!", 
                "DID" => $tid
            ];
        }

        public function updateDevice()
        {
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
            if(!filter_input(INPUT_POST, "did2", FILTER_SANITIZE_NUMBER_INT)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "iotJumpWay device 2 id is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "did3", FILTER_SANITIZE_NUMBER_INT)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "iotJumpWay device 3 id is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "ID is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "EMAR device name is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "name2", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "EMAR device 2 name is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "name3", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "EMAR device 3 name is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device IP is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "ip2", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device 2 IP is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "ip3", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device 3 IP is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device MAC is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "mac2", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device 2 MAC is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "mac3", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device 3 MAC is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "sport2", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device 2 stream port is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "sport3", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device 3 stream port is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "sdir2", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device 2 stream directory is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "sdir3", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device 3 stream directory is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "sportf2", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device 2 stream file is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "sportf3", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device 3 stream file is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "sckport2", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device 2 socket port is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "sckport3", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Device 3 socket port is required"
                ];
            endif;

            $pdoQuery = $this->_GeniSys->_secCon->prepare("
                UPDATE emar
                SET name = :name,
                    name2 = :name2, 
                    name3 = :name3, 
                    lid = :lid, 
                    zid = :zid, 
                    did = :did, 
                    did2 = :did2, 
                    did3 = :did3, 
                    ip = :ip, 
                    ip2 = :ip2,
                    ip3 = :ip3, 
                    mac = :mac, 
                    mac2 = :mac2,
                    mac3 = :mac3, 
                    sport = :sport, 
                    sport2 = :sport2, 
                    sport3 = :sport3, 
                    sdir = :sdir,   
                    sdir2 = :sdir2,  
                    sdir3 = :sdir3,  
                    sportf = :sportf,  
                    sportf2 = :sportf2,  
                    sportf3 = :sportf3, 
                    sckport = :sckport, 
                    sckport2 = :sckport2, 
                    sckport3 = :sckport3
                WHERE id = :id 
            ");
            $pdoQuery->execute([
                ":name" => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING),
                ":name2" => filter_input(INPUT_POST, "name2", FILTER_SANITIZE_STRING),
                ":name3" => filter_input(INPUT_POST, "name3", FILTER_SANITIZE_STRING),
                ":lid" => filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT),
                ":zid" => filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT),
                ":did" => filter_input(INPUT_POST, "did", FILTER_SANITIZE_NUMBER_INT),
                ":did2" => filter_input(INPUT_POST, "did2", FILTER_SANITIZE_NUMBER_INT),
                ":did3" => filter_input(INPUT_POST, "did3", FILTER_SANITIZE_NUMBER_INT),
                ":ip" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING)),
                ":ip2" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "ip2", FILTER_SANITIZE_STRING)),
                ":ip3" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "ip3", FILTER_SANITIZE_STRING)),
                ":mac" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING)),
                ":mac2" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "mac2", FILTER_SANITIZE_STRING)),
                ":mac3" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "mac3", FILTER_SANITIZE_STRING)),
                ":sport" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sport", FILTER_SANITIZE_STRING)),
                ":sport2" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sport2", FILTER_SANITIZE_STRING)),
                ":sport3" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sport3", FILTER_SANITIZE_STRING)),
                ":sdir" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sdir", FILTER_SANITIZE_STRING)),
                ":sdir2" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sdir2", FILTER_SANITIZE_STRING)),
                ":sdir3" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sdir3", FILTER_SANITIZE_STRING)),
                ":sportf" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sportf", FILTER_SANITIZE_STRING)),
                ":sportf2" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sportf2", FILTER_SANITIZE_STRING)),
                ":sportf3" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sportf3", FILTER_SANITIZE_STRING)),
                ":sckport" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sckport", FILTER_SANITIZE_STRING)),
                ":sckport2" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sckport2", FILTER_SANITIZE_STRING)),
                ":sckport3" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "sckport3", FILTER_SANITIZE_STRING)),
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

            #$htpasswd = new Htpasswd('/etc/nginx/emar/htpasswd');
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

		public function getLifes()
		{
            $pdoQuery = $this->_GeniSys->_secCon->prepare("
                SELECT emar.id,
                    device.status,
                    device.lt,
                    device.lg,
                    device.tempr,
                    device.hdd,
                    device.mem,
                    device.cpu,
                    device2.status as status2,
                    device2.lt as lt2,
                    device2.lg as lg2,
                    device2.tempr as tempr2,
                    device2.hdd as hdd2,
                    device2.mem as mem2,
                    device2.cpu as cpu2,
                    device3.status as status3,
                    device3.lt as lt3,
                    device3.lg as lg3,
                    device3.tempr as tempr3,
                    device3.hdd as hdd3,
                    device3.mem as mem3,
                    device3.cpu as cpu3
                FROM emar emar 
                INNER JOIN mqttld device
                ON device.id = emar.did
                INNER JOIN mqttld device2
                ON device2.id = emar.did2
                INNER JOIN mqttld device3
                ON device3.id = emar.did3
                WHERE emar.id = :id 
            ");
            $pdoQuery->execute([
                ":id" => filter_input(INPUT_POST, "device", FILTER_SANITIZE_NUMBER_INT)
            ]);
            $response=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			
			if($response["id"]):
				return  [
					'Response'=>'OK',
					'ResponseData'=>$response
				];
			else:
				return  [
					'Response'=>'FAILED',
					'Message'=>'EMAR device not found!'
				];
			endif;

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

		public function getMapMarkers($device)
		{
            if(!$device["lt"]):
                $lat = $this->_GeniSys->lt;
                $lng = $this->_GeniSys->lg;
            else:
                $lat = $device["lt"];
                $lng = $device["lg"];
            endif;
            if(!$device["lt2"]):
                $lat2 = $this->_GeniSys->lt;
                $lng2 = $this->_GeniSys->lg;
            else:
                $lat2 = $device["lt2"];
                $lng2 = $device["lg2"];
            endif;
            if(!$device["lt3"]):
                $lat3 = $this->_GeniSys->lt;
                $lng3 = $this->_GeniSys->lg;
            else:
                $lat3 = $device["lt3"];
                $lng3 = $device["lg3"];
            endif;

            return [$lat, $lng, $lat2, $lng2, $lat3, $lng3];
		}

    }
    
    $EMAR = new EMAR($_GeniSys);

    if(filter_input(INPUT_POST, "update_emar", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($EMAR->updateDevice()));
    endif;

    if(filter_input(INPUT_POST, "create_emar", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($EMAR->createDevice()));
    endif;

    if(filter_input(INPUT_POST, "reset_mqtt", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($EMAR->resetMqtt()));
    endif;
	if(filter_input(INPUT_POST, "get_lifes", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($EMAR->getLifes()));
	endif;