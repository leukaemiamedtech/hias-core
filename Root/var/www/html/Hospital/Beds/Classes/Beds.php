<?php

    class Beds
    {

        function __construct($_GeniSys)
        {
            $this->_GeniSys = $_GeniSys;
        }

        public function getBeds()
        {
            $pdoQuery = $this->_GeniSys->_secCon->prepare("
                SELECT *
                FROM beds
                ORDER BY id DESC
            ");
            $pdoQuery->execute();
            $response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
            $pdoQuery->closeCursor();
            $pdoQuery = null;
            return $response;
        }

        public function getBed($id)
        {
            $pdoQuery = $this->_GeniSys->_secCon->prepare("
                    SELECT beds.*,
                    mqttld.lid,
                    mqttld.mqttu,
                    mqttld.mqttp,
                    mqttld.id AS did
                FROM beds beds
                INNER JOIN mqttld mqttld 
                ON beds.did = mqttld.id 
                WHERE beds.id = :id 
            ");
            $pdoQuery->execute([
                ":id" => $id
            ]);
            $response=$pdoQuery->fetch(PDO::FETCH_ASSOC);
            return $response;
        }

        public function createBed()
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
                    "Message" => "iotJumpWay location id is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "ip", FILTER_SANITIZE_NUMBER_INT)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Bed IP is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "mac", FILTER_SANITIZE_NUMBER_INT)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Bed MAC is required"
                ];
            endif;

            $name = $this->_GeniSys->_helpers->generateKey(12);
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
                ':lid' => filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT),
                ':zid' => filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT),
                ':name' => $name,
                ':mqttu' => $this->_GeniSys->_helpers->oEncrypt($mqttUser),
                ':mqttp' => $this->_GeniSys->_helpers->oEncrypt($mqttPass),
                ':apub' => $this->_GeniSys->_helpers->oEncrypt($apiKey),
                ':aprv' => $this->_GeniSys->_helpers->oEncrypt($apiSecretKey),
                ':time' => time()
            ]);
            $did = $this->_GeniSys->_secCon->lastInsertId();

            $pdoQuery = $this->_GeniSys->_secCon->prepare("
                INSERT INTO  beds  (
                    `lid`,
                    `zid`,
                    `did`,
                    `ip`,
                    `mac`,
                    `gpstime`,
                    `created`
                )  VALUES (
                    :lid,
                    :zid,
                    :did,
                    :ip,
                    :mac,
                    :gpstime,
                    :time
                )
            ");
            $pdoQuery->execute([
                ":lid" => filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT),
                ":zid" => filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT),
                ":did" =>  $did,
                ":ip" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING)),
                ":mac" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING)),
                ":gpstime" => 0,
                ":time" => time()
            ]);
            $bid = $this->_GeniSys->_secCon->lastInsertId();
            $pdoQuery->closeCursor();
            $pdoQuery = null;
    
            $query = $this->_GeniSys->_secCon->prepare("
                INSERT INTO  mqttu  (
                    `lid`,
                    `zid`,
                    `did`,
                    `bid`,
                    `uname`,
                    `pw`
                )  VALUES (
                    :lid,
                    :zid,
                    :did,
                    :bid,
                    :uname,
                    :pw
                )
            ");
            $query->execute([
                ":lid" => filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT),
                ":zid" => filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT),
                ":did" =>  $did,
                ":bid" =>  $bid,
                ':uname' => $mqttUser,
                ':pw' => $mqttHash
            ]);

            $query = $this->_GeniSys->_secCon->prepare("
                INSERT INTO  mqttua  (
                    `lid`,
                    `zid`,
                    `did`,
                    `bid`,
                    `username`,
                    `topic`,
                    `rw`
                )  VALUES (
                    :lid,
                    :zid,
                    :did,
                    :bid,
                    :username,
                    :topic,
                    :rw
                )
            ");
            $query->execute(array(
                ":lid" => filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT),
                ":zid" => filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT),
                ":did" =>  $did,
                ":bid" =>  $bid,
                ':username' => $mqttUser,
                ':topic' => filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT) . "/Device/" . filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT) . "/" . $did . "#",
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
    
            $query = $this->_GeniSys->_secCon->prepare("
                UPDATE mqttld
                SET bid = :bid
                WHERE id = :id
            ");
            $query->execute(array(
                ':bid'=>$bid,
                ':id'=>$did
            ));

            return [
                "Response"=> "OK", 
                "Message" => "Bed created!", 
                "BID" => $bid
            ];
        }

        public function updateBed()
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
                    "Message" => "iotJumpWay location id is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "did", FILTER_SANITIZE_NUMBER_INT)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "iotJumpWay device id is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "ip", FILTER_SANITIZE_NUMBER_INT)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Bed IP is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "mac", FILTER_SANITIZE_NUMBER_INT)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Bed MAC is required"
                ];
            endif;

            $pdoQuery = $this->_GeniSys->_secCon->prepare("
                UPDATE beds
                SET lid = :lid, 
                    zid = :zid, 
                    did = :did, 
                    ip = :ip, 
                    mac = :mac
                WHERE id = :id 
            ");
            $pdoQuery->execute([
                ":lid" => filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT),
                ":zid" => filter_input(INPUT_POST, "zid", FILTER_SANITIZE_NUMBER_INT),
                ":did" => filter_input(INPUT_POST, "did", FILTER_SANITIZE_NUMBER_INT),
                ":ip" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "ip", FILTER_SANITIZE_STRING)),
                ":mac" => $this->_GeniSys->_helpers->oEncrypt(filter_input(INPUT_POST, "mac", FILTER_SANITIZE_STRING)),
                ":id" => filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)
            ]);
            $pdoQuery->closeCursor();
            $pdoQuery = null;
            return [
                "Response"=> "OK", 
                "Message" => "Bed updated!"
            ];
        }

        public function resetMqtt()
        {
            $pdoQuery = $this->_GeniSys->_secCon->prepare("
                SELECT uname
                FROM mqttu
                WHERE bid = :bid
            ");
            $pdoQuery->execute([
                ":bid" => filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)
            ]);
            $mqtt=$pdoQuery->fetch(PDO::FETCH_ASSOC);
            $pdoQuery->closeCursor();
            $pdoQuery = null;

            $mqttPass = $this->_GeniSys->_helpers->password();
            $mqttHash = create_hash($mqttPass);

            $htpasswd = new Htpasswd('/etc/nginx/tass/htpasswd');
            $htpasswd->updateUser($mqtt["uname"], $mqttPass, Htpasswd::ENCTYPE_APR_MD5);
    
            $query = $this->_GeniSys->_secCon->prepare("
                UPDATE mqttld
                SET mqttp = :mqttp 
                WHERE bid = :bid
            ");
            $query->execute(array(
                ':mqttp' => $this->_GeniSys->_helpers->oEncrypt($mqttPass),
                ':bid' => filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)
            ));
    
            $query = $this->_GeniSys->_secCon->prepare("
                UPDATE mqttu
                SET pw = :pw 
                WHERE bid = :bid
            ");
            $query->execute(array(
                ':pw' => $mqttHash,
                ':bid' => filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)
            ));

            return [
                "Response"=> "OK", 
                "Message" => "MQTT password reset!", 
                "P" => $mqttPass
            ];

        }

    }
    
    $Beds = new Beds($_GeniSys);

    if(filter_input(INPUT_POST, "create_bed", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($Beds->createBed()));
    endif;

    if(filter_input(INPUT_POST, "update_bed", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($Beds->updateBed()));
    endif;

    if(filter_input(INPUT_POST, "reset_mqtt_bed", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($Beds->resetMqtt()));
    endif;