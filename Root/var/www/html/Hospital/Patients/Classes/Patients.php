<?php

    class Patients
    {

        function __construct($_GeniSys)
        {
            $this->_GeniSys = $_GeniSys;
        }

        public function getPatients()
        {
            $pdoQuery = $this->_GeniSys->_secCon->prepare("
                SELECT *
                FROM patients
                ORDER BY id DESC
            ");
            $pdoQuery->execute();
            $response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
            $pdoQuery->closeCursor();
            $pdoQuery = null;
            return $response;
        }

        public function getPatient($id)
        {
            $pdoQuery = $this->_GeniSys->_secCon->prepare("
                SELECT patients.*,
                    mqtta.lid,
                    mqtta.mqttu,
                    mqtta.mqttp,
                    mqtta.id AS aid
                FROM patients patients
                INNER JOIN mqtta mqtta 
                ON patients.id = mqtta.pid 
                WHERE patients.id = :id 
            ");
            $pdoQuery->execute([
                ":id" => $id
            ]);
            $response=$pdoQuery->fetch(PDO::FETCH_ASSOC);
            return $response;
        }

        public function createPatient()
        {
            if(!filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Patients email is required"
                ];
            endif;

            $pdoQuery = $this->_GeniSys->_secCon->prepare("
                SELECT id
                FROM patients
                WHERE email = :email 
            ");
            $pdoQuery->execute([
                ":email" => filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)
            ]);
            $response=$pdoQuery->fetch(PDO::FETCH_ASSOC);

            if($response["id"]):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Patients email exists"
                ];
            endif;

            if(!filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Patients username is required"
                ];
            endif;

            $pdoQuery = $this->_GeniSys->_secCon->prepare("
                SELECT id
                FROM patients
                WHERE username = :username 
            ");
            $pdoQuery->execute([
                ":username" => filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING)
            ]);
            $response=$pdoQuery->fetch(PDO::FETCH_ASSOC);

            if($response["id"]):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Patients username exists"
                ];
            endif;
            if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "iotJumpWay location id is required"
                ];
            endif;

            $uPass = $this->_GeniSys->_helpers->password();

            $pdoQuery = $this->_GeniSys->_secCon->prepare("
                INSERT INTO  patients  (
                    `email`,
                    `name`,
                    `username`,
                    `password`,
                    `gpstime`,
                    `created`
                )  VALUES (
                    :email,
                    :name,
                    :username,
                    :password,
                    :gpstime,
                    :time
                )
            ");
            $pdoQuery->execute([
                ":email" => filter_input(INPUT_POST, "email", FILTER_SANITIZE_STRING),
                ":name" => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING),
                ":username" => filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING),
                ":password" =>  $this->_GeniSys->_helpers->oEncrypt($uPass),
                ":gpstime" => 0,
                ":time" => time()
            ]);
            $pid = $this->_GeniSys->_secCon->lastInsertId();
            $pdoQuery->closeCursor();
            $pdoQuery = null;

            $mqttUser = $this->_GeniSys->_helpers->generateKey(12);
            $mqttPass = $this->_GeniSys->_helpers->password();
            $mqttHash = create_hash($mqttPass);
    
            $apiKey = $this->_GeniSys->_helpers->generateKey(30);
            $apiSecretKey = $this->_GeniSys->_helpers->generateKey(35);

            $htpasswd = new Htpasswd('/etc/nginx/tass/htpasswd');
            $htpasswd->addUser(filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING), $mqttPass, Htpasswd::ENCTYPE_APR_MD5);
            
            $query = $this->_GeniSys->_secCon->prepare("
                INSERT INTO  mqtta  (
                    `pid`,
                    `lid`,
                    `name`,
                    `mqttu`,
                    `mqttp`,
                    `apub`,
                    `aprv`,
                    `time`
                )  VALUES (
                    :pid,
                    :lid,
                    :name,
                    :mqttu,
                    :mqttp,
                    :apub,
                    :aprv,
                    :time
                )
            ");
            $query->execute([
                ':pid' => $pid,
                ':lid' => filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT),
                ':name' => filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING),
                ':mqttu' =>$this->_GeniSys->_helpers->oEncrypt($mqttUser),
                ':mqttp' =>$this->_GeniSys->_helpers->oEncrypt($mqttPass),
                ':apub' => $this->_GeniSys->_helpers->oEncrypt($apiKey),
                ':aprv' => $this->_GeniSys->_helpers->oEncrypt($apiSecretKey),
                ':time' => time()
            ]);
            $aid = $this->_GeniSys->_secCon->lastInsertId();
    
            $query = $this->_GeniSys->_secCon->prepare("
                INSERT INTO  mqttu  (
                    `lid`,
                    `pid`,
                    `aid`,
                    `uname`,
                    `pw`
                )  VALUES (
                    :lid,
                    :pid,
                    :aid,
                    :uname,
                    :pw
                )
            ");
            $query->execute([
                ':lid' => filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT),
                ':pid' => $pid,
                ':aid' => $aid,
                ':uname' => $mqttUser,
                ':pw' => $mqttHash
            ]);

            $query = $this->_GeniSys->_secCon->prepare("
                INSERT INTO  mqttua  (
                    `lid`,
                    `pid`,
                    `aid`,
                    `username`,
                    `topic`,
                    `rw`
                )  VALUES (
                    :lid,
                    :pid,
                    :aid,
                    :username,
                    :topic,
                    :rw
                )
            ");
            $query->execute(array(
                ':lid' => filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT),
                ':pid' => $pid,
                ':aid' => $aid,
                ':username' => $mqttUser,
                ':topic' => filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT)."/Devices/#",
                ':rw' => 4
            ));
    
            $query = $this->_GeniSys->_secCon->prepare("
                INSERT INTO  mqttua  (
                    `lid`,
                    `pid`,
                    `aid`,
                    `username`,
                    `topic`,
                    `rw`
                )  VALUES (
                    :lid,
                    :pid,
                    :aid,
                    :username,
                    :topic,
                    :rw
                )
            ");
            $query->execute(array(
                ':lid' => filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT),
                ':pid' => $pid,
                ':aid' => $aid,
                ':username' => $mqttUser,
                ':topic' => filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT)."/Applications/#",
                ':rw' => 2
            ));
    
            $query = $this->_GeniSys->_secCon->prepare("
                UPDATE mqttl
                SET apps = apps + 1
                WHERE id = :id
            ");
            $query->execute(array(
                ':id'=>filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT)
            ));

            return [
                "Response"=> "OK", 
                "Message" => "Patients created!", 
                "UID" => $uid
            ];
        }

        public function updatePatient()
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
                    "Message" => "Patient name is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Patient email is required"
                ];
            endif;

            $pdoQuery = $this->_GeniSys->_secCon->prepare("
                SELECT id
                FROM patients
                WHERE email = :email 
            ");
            $pdoQuery->execute([
                ":email" => filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)
            ]);
            $response=$pdoQuery->fetch(PDO::FETCH_ASSOC);

            if($response["id"] && $response["id"] != filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Patient email exists"
                ];
            endif;

            if(!filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Patient username is required"
                ];
            endif;

            $pdoQuery = $this->_GeniSys->_secCon->prepare("
                SELECT id
                FROM patients
                WHERE username = :username 
            ");
            $pdoQuery->execute([
                ":username" => filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING)
            ]);
            $response=$pdoQuery->fetch(PDO::FETCH_ASSOC);

            if($response["id"] && $response["id"] != filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Patient username exists"
                ];
            endif;

            if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "iotJumpWay location id is required"
                ];
            endif;

            $pdoQuery = $this->_GeniSys->_secCon->prepare("
                UPDATE patients
                SET name = :name,
                    username = :username, 
                    email = :email
                WHERE id = :id 
            ");
            $pdoQuery->execute([
                ":name" => filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING),
                ":username" => filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING),
                ":email" => filter_input(INPUT_POST, "email", FILTER_SANITIZE_STRING),
                ":id" => filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)
            ]);
            $pdoQuery->closeCursor();
            $pdoQuery = null;
            return [
                "Response"=> "OK", 
                "Message" => "Patient updated!"
            ];
        }

        public function resetMqtt()
        {
            $pdoQuery = $this->_GeniSys->_secCon->prepare("
                SELECT uname
                FROM mqttu
                WHERE pid = :pid
            ");
            $pdoQuery->execute([
                ":pid" => filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)
            ]);
            $mqtt=$pdoQuery->fetch(PDO::FETCH_ASSOC);
            $pdoQuery->closeCursor();
            $pdoQuery = null;

            $mqttPass = $this->_GeniSys->_helpers->password();
            $mqttHash = create_hash($mqttPass);

            $htpasswd = new Htpasswd('/etc/nginx/tass/htpasswd');
            $htpasswd->updateUser($mqtt["uname"], $mqttPass, Htpasswd::ENCTYPE_APR_MD5);
    
            $query = $this->_GeniSys->_secCon->prepare("
                UPDATE mqtta
                SET mqttp = :mqttp 
                WHERE pid = :id
            ");
            $query->execute(array(
                ':mqttp' => $this->_GeniSys->_helpers->oEncrypt($mqttPass),
                ':id' => filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)
            ));
    
            $query = $this->_GeniSys->_secCon->prepare("
                UPDATE mqttu
                SET pw = :pw 
                WHERE pid = :pid
            ");
            $query->execute(array(
                ':pw' => $mqttHash,
                ':pid' => filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)
            ));

            return [
                "Response"=> "OK", 
                "Message" => "MQTT password reset!", 
                "P" => $mqttPass
            ];

        }

    }
    
    $Patients = new Patients($_GeniSys);

    if(filter_input(INPUT_POST, "create_patient", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($Patients->createPatient()));
    endif;

    if(filter_input(INPUT_POST, "update_patient", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($Patients->updatePatient()));
    endif;

    if(filter_input(INPUT_POST, "reset_mqtt_patient", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($Patients->resetMqtt()));
    endif;