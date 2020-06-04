<?php

    class Staff
    {
        private $lat = 41.5463;
        private $lng = 2.1086;

        function __construct($_GeniSys)
        {
            $this->_GeniSys = $_GeniSys;
        }

        public function getStaffs()
        {
            $pdoQuery = $this->_GeniSys->_secCon->prepare("
                SELECT users.id,
                    users.admin,
                    users.pic,
                    users.username,
                    mqtta.lid,
                    mqtta.status,
                    mqtta.mqttu,
                    mqtta.mqttp,
                    mqtta.id AS aid
                FROM users users
                INNER JOIN mqtta mqtta 
                ON users.id = mqtta.uid 
                ORDER BY id DESC
            ");
            $pdoQuery->execute();
            $response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
            $pdoQuery->closeCursor();
            $pdoQuery = null;
            return $response;
        }

        public function getStaff($id)
        {
            $pdoQuery = $this->_GeniSys->_secCon->prepare("
                SELECT users.*,
                    mqtta.lid,
                    mqtta.status,
                    mqtta.mqttu,
                    mqtta.mqttp,
                    mqtta.lt,
                    mqtta.lg,
                    mqtta.cpu,
                    mqtta.mem,
                    mqtta.hdd,
                    mqtta.tempr,
                    mqtta.id AS aid
                FROM users users
                INNER JOIN mqtta mqtta 
                ON users.id = mqtta.uid 
                WHERE users.id = :id 
            ");
            $pdoQuery->execute([
                ":id" => $id
            ]);
            $response=$pdoQuery->fetch(PDO::FETCH_ASSOC);
            return $response;
        }

        public function updateStaff()
        {
            if(!filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Staff ID is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Staff username is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Staff iotJumpWay application location id is required"
                ];
            endif;
            if(!filter_input(INPUT_POST, "aid", FILTER_SANITIZE_NUMBER_INT)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Staff iotJumpWay application id is required"
                ];
            endif;

            $pdoQuery = $this->_GeniSys->_secCon->prepare("
                UPDATE users
                SET username = :username,
                    admin = :admin, 
                    lid = :lid, 
                    aid = :aid 
                WHERE id = :id 
            ");
            $pdoQuery->execute([
                ":admin" => filter_input(INPUT_POST, "admin", FILTER_SANITIZE_STRING) ? filter_input(INPUT_POST, "admin", FILTER_SANITIZE_STRING) : 0,
                ":username" => filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING),
                ":lid" => filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT),
                ":aid" => filter_input(INPUT_POST, "aid", FILTER_SANITIZE_NUMBER_INT),
                ":id" => filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)
            ]);
            $pdoQuery->closeCursor();
            $pdoQuery = null;
            return [
                "Response"=> "OK", 
                "Message" => "Staff updated!"
            ];
        }

        public function createStaff()
        {
            if(!filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING)):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Staff username is required"
                ];
            endif;

            $pdoQuery = $this->_GeniSys->_secCon->prepare("
                SELECT id
                FROM users
                WHERE username = :username 
            ");
            $pdoQuery->execute([
                ":username" => filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING)
            ]);
            $response=$pdoQuery->fetch(PDO::FETCH_ASSOC);

            if($response["id"]):
                return [
                    "Response"=> "Failed", 
                    "Message" => "Staff username exists"
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
                INSERT INTO  users  (
                    `admin`,
                    `username`,
                    `password`,
                    `gpstime`,
                    `created`
                )  VALUES (
                    :admin,
                    :username,
                    :password,
                    :gpstime,
                    :time
                )
            ");
            $pdoQuery->execute([
                ":admin" => filter_input(INPUT_POST, "admin", FILTER_SANITIZE_NUMBER_INT) ? filter_input(INPUT_POST, "admin", FILTER_SANITIZE_NUMBER_INT) : 0,
                ":username" => filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING),
                ":password" =>  $this->_GeniSys->_helpers->oEncrypt($uPass),
                ":gpstime" => 0,
                ":time" => time()
            ]);
            $uid = $this->_GeniSys->_secCon->lastInsertId();
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
                    `uid`,
                    `lid`,
                    `name`,
                    `mqttu`,
                    `mqttp`,
                    `apub`,
                    `aprv`,
                    `time`
                )  VALUES (
                    :uid,
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
                ':uid' => $uid,
                ':lid' => filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT),
                ':name' => filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING),
                ':mqttu' =>$this->_GeniSys->_helpers->oEncrypt($mqttUser),
                ':mqttp' =>$this->_GeniSys->_helpers->oEncrypt($mqttPass),
                ':apub' => $this->_GeniSys->_helpers->oEncrypt($apiKey),
                ':aprv' => $this->_GeniSys->_helpers->oEncrypt($apiSecretKey),
                ':time' => time()
            ]);
            $this->aid = $this->_GeniSys->_secCon->lastInsertId();
    
            $query = $this->_GeniSys->_secCon->prepare("
                UPDATE users
                SET aid = :aid 
                WHERE id = :id
            ");
            $query->execute(array(
                ':aid'=>$this->aid,
                ':id'=>$uid
            ));
    
            $query = $this->_GeniSys->_secCon->prepare("
                INSERT INTO  mqttu  (
                    `lid`,
                    `uid`,
                    `aid`,
                    `uname`,
                    `pw`
                )  VALUES (
                    :lid,
                    :uid,
                    :aid,
                    :uname,
                    :pw
                )
            ");
            $query->execute([
                ':lid' => filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT),
                ':uid' => $uid,
                ':aid' => $this->aid,
                ':uname' => $mqttUser,
                ':pw' => $mqttHash
            ]);

            $query = $this->_GeniSys->_secCon->prepare("
                INSERT INTO  mqttua  (
                    `lid`,
                    `aid`,
                    `uid`,
                    `username`,
                    `topic`,
                    `rw`
                )  VALUES (
                    :lid,
                    :aid,
                    :uid,
                    :username,
                    :topic,
                    :rw
                )
            ");
            $query->execute(array(
                ':lid' => filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT),
                ':aid' => $this->aid,
                ':uid' => $uid,
                ':username' => $mqttUser,
                ':topic' => filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT)."/Devices/#",
                ':rw' => 4
            ));
    
            $query = $this->_GeniSys->_secCon->prepare("
                INSERT INTO  mqttua  (
                    `lid`,
                    `aid`,
                    `uid`,
                    `username`,
                    `topic`,
                    `rw`
                )  VALUES (
                    :lid,
                    :aid,
                    :uid,
                    :username,
                    :topic,
                    :rw
                )
            ");
            $query->execute(array(
                ':lid' => filter_input(INPUT_POST, "lid", FILTER_SANITIZE_NUMBER_INT),
                ':aid' => $this->aid,
                ':uid' => $uid,
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
                "Message" => "Staff created!", 
                "UID" => $uid
            ];
        }

        public function resetPassword()
        {
            $pass = $this->_GeniSys->_helpers->password();
            $passhash=$this->_GeniSys->_helpers->createPasswordHash($pass);

            $htpasswd = new Htpasswd('/etc/nginx/tass/htpasswd');
            $htpasswd->updateUser(filter_input(INPUT_POST, "user", FILTER_SANITIZE_STRING), $pass, Htpasswd::ENCTYPE_APR_MD5);
    
            $query = $this->_GeniSys->_secCon->prepare("
                UPDATE users
                SET password = :password 
                WHERE id = :id
            ");
            $query->execute(array(
                ':password' => $this->_GeniSys->_helpers->oEncrypt($passhash),
                ':id' => filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)
            ));

            return [
                "Response" => "OK",
                "pw" => $pass
            ];

        }

        public function resetMqtt()
        {
            $pdoQuery = $this->_GeniSys->_secCon->prepare("
                SELECT uname
                FROM mqttu
                WHERE aid = :aid
            ");
            $pdoQuery->execute([
                ":aid" => filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)
            ]);
            $mqtt=$pdoQuery->fetch(PDO::FETCH_ASSOC);
            $pdoQuery->closeCursor();
            $pdoQuery = null;

            $mqttPass = $this->_GeniSys->_helpers->password();
            $mqttHash = create_hash($mqttPass);
    
            $query = $this->_GeniSys->_secCon->prepare("
                UPDATE mqtta
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
                WHERE aid = :aid
            ");
            $query->execute(array(
                ':pw' => $mqttHash,
                ':aid' => filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)
            ));

            return [
                "Response"=> "OK", 
                "Message" => "MQTT password reset!", 
                "P" => $mqttPass
            ];

        }

		public function getMapMarkers($application)
		{
            if(!$application["lt"]):
                $lat = $this->lat;
                $lng = $this->lng;
            else:
                $lat = $application["lt"];
                $lng = $application["lg"];
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
    
    $Staff = new Staff($_GeniSys);

    if(filter_input(INPUT_POST, "update_staff", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($Staff->updateStaff()));
    endif;

    if(filter_input(INPUT_POST, "create_staff", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($Staff->createStaff()));
    endif;

    if(filter_input(INPUT_POST, "reset_mqtt_staff", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($Staff->resetMqtt()));
    endif;

    if(filter_input(INPUT_POST, "reset_u_pass", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($Staff->resetPassword()));
    endif;