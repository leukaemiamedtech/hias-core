<?php

    class _GeniSysAi
    {
        private $_GeniSys = null;

        function __construct($_GeniSys)
        {
            $this->_GeniSys = $_GeniSys;
        }

        public function login()
        {
            $this->checkBlock();

            if(!filter_input(INPUT_POST,'g-recaptcha-response',FILTER_SANITIZE_STRING)):
                return [
                    'Response'=>'FAILED',
                    'ResponseMessage'=>'Please verify using Recaptcha.',
                ];
            endif;

            $fields = array(
                'secret'=>urlencode($this->_GeniSys->_helpers->oDecrypt($this->_GeniSys->_confs["recaptchas"])),
                'response'=>urlencode(filter_input(INPUT_POST, 'g-recaptcha-response', FILTER_SANITIZE_STRING))
            );
            $fields_string = "";

            foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
            rtrim($fields_string,'&');

            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL,'https://www.google.com/recaptcha/api/siteverify');
            curl_setopt($ch,CURLOPT_POST,count($fields));
            curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $result = curl_exec($ch);
            $result=json_decode($result, TRUE);

            if($result['success']===true):
                $_SESSION["Attempts"] = !isSet($_SESSION["Attempts"]) ? 0 : $_SESSION["Attempts"];

                $this->checkBlock();

                $gsysuser = $this->getUserByName(filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING));

                if($gsysuser["id"]):
                    if($this->verifyPassword(filter_input(INPUT_POST,'password',FILTER_SANITIZE_STRING),
                        $this->_GeniSys->_helpers->oDecrypt($gsysuser["password"]))):  session_regenerate_id();

                        $_SESSION["GeniSysAI"]=[
                            "Active"=>true,
                            "User"=>filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING)
                        ];

                        $pdoQuery = $this->_GeniSys->_secCon->prepare("
                            INSERT INTO logins (
                                `ipv6`,
                                `browser`,
                                `language`,
                                `time`
                            )  VALUES (
                                :ipv6,
                                :browser,
                                :language,
                                :time
                            )
                        ");
                        $pdoQuery->execute([
                            ":ipv6" => $this->_GeniSys->_helpers->oEncrypt($this->_GeniSys->_helpers->getUserIP()),
                            ":browser" => $this->_GeniSys->_helpers->oEncrypt($_SERVER["HTTP_USER_AGENT"]),
                            ":language" => $this->_GeniSys->_helpers->oEncrypt($_SERVER["HTTP_ACCEPT_LANGUAGE"]),
                            ":time" => time()
                        ]);
                        $pdoQuery->closeCursor();
                        $pdoQuery = null;

                        return  [
                            "Response"=>"OK",
                            "ResponseMessage"=>"Welcome"
                        ];

                    else:

                        $pdoQuery = $this->_GeniSys->_secCon->prepare("
                            INSERT INTO loginsf (
                                `ipv6`,
                                `browser`,
                                `language`,
                                `time`
                            )  VALUES (
                                :ipv6,
                                :browser,
                                :language,
                                :time
                            )
                        ");
                        $pdoQuery->execute([
                            ":ipv6" => $this->_GeniSys->_helpers->oEncrypt($this->_GeniSys->_helpers->getUserIP()),
                            ":browser" => $this->_GeniSys->_helpers->oEncrypt($_SERVER["HTTP_USER_AGENT"]),
                            ":language" => $this->_GeniSys->_helpers->oEncrypt($_SERVER["HTTP_ACCEPT_LANGUAGE"]),
                            ":time" => time()
                        ]);
                        $pdoQuery->closeCursor();
                        $pdoQuery = null;

                        $_SESSION["Attempts"] += 1;

                        if($_SESSION["Attempts"] >= 3):

                            $_SESSION["Attempts"] = 0;

                            $pdoQuery = $this->_GeniSys->_secCon->prepare("
                                INSERT INTO blocked (
                                    `ipv6`,
                                    `banned`
                                )  VALUES (
                                    :ipv6,
                                    :banned
                                )
                            ");
                            $pdoQuery->execute([
                                ":ipv6" => $this->_GeniSys->_helpers->getUserIP(),
                                ":banned" => time()
                            ]);
                            $pdoQuery->closeCursor();
                            $pdoQuery = null;

                            return  [
                                "Response"=>"BLOCKED",
                                "ResponseMessage"=>"Password incorrect, access BLOCKED!",
                                "SessionAttempts"=>$_SESSION["Attempts"]
                            ];

                        else:

                            return  [
                                "Response"=>"FAILED",
                                "ResponseMessage"=>"Password incorrect, access DENIED!",
                                "SessionAttempts"=>$_SESSION["Attempts"]
                            ];

                        endif;

                    endif;

                else:

                    $pdoQuery = $this->_GeniSys->_secCon->prepare("
                        INSERT INTO loginsf (
                            `ipv6`,
                            `browser`,
                            `language`,
                            `time`
                        )  VALUES (
                            :ipv6,
                            :browser,
                            :language,
                            :time
                        )
                    ");
                    $pdoQuery->execute([
                        ":ipv6" => $this->_GeniSys->_helpers->oEncrypt($this->_GeniSys->_helpers->getUserIP()),
                        ":browser" => $this->_GeniSys->_helpers->oEncrypt($_SERVER["HTTP_USER_AGENT"]),
                        ":language" => $this->_GeniSys->_helpers->oEncrypt($_SERVER["HTTP_ACCEPT_LANGUAGE"]),
                        ":time" => time()
                    ]);
                    $pdoQuery->closeCursor();
                    $pdoQuery = null;

                    $_SESSION["Attempts"] += 1;

                    if($_SESSION["Attempts"] >= 3):

                        $_SESSION["Attempts"] = 0;

                        $pdoQuery = $this->_GeniSys->_secCon->prepare("
                            INSERT INTO blocked (
                                `ipv6`,
                                `banned`
                            )  VALUES (
                                :ipv6,
                                :banned
                            )
                        ");
                        $pdoQuery->execute([
                            ":ipv6" => $this->_GeniSys->_helpers->getUserIP(),
                            ":banned" => time()
                        ]);
                        $pdoQuery->closeCursor();
                        $pdoQuery = null;

                        return  [
                            "Response"=>"BLOCKED",
                            "ResponseMessage"=>"Username incorrect, access BLOCKED!",
                            "SessionAttempts"=>$_SESSION["Attempts"]
                        ];

                    else:

                        return  [
                            "Response"=>"FAILED",
                            "ResponseMessage"=>"Username incorrect, access DENIED!"
                        ];

                    endif;

                endif;


            else:

                return  [
                    "Response"=>"FAILED",
                    "ResponseMessage"=>"Google ReCaptcha failed, access DENIED!",
                    "SessionAttempts"=>$_SESSION["Attempts"]
                ];

            endif;

        }

        public function getUser($userId)
        {

            $pdoQuery = $this->_GeniSys->_secCon->prepare("
                SELECT id,
                    password
                FROM users
                WHERE id = :id
            ");
            $pdoQuery->execute([
                ":id"=> $userId
            ]);
            $response=$pdoQuery->fetch(PDO::FETCH_ASSOC);
            $pdoQuery->closeCursor();
            $pdoQuery = null;

            return $response;
        }

        public function getUserByName($username)
        {

            $pdoQuery = $this->_GeniSys->_secCon->prepare("
                SELECT id,
                    password
                FROM users
                WHERE username = :username
            ");
            $pdoQuery->execute([
                ":username"=> $username
            ]);
            $response=$pdoQuery->fetch(PDO::FETCH_ASSOC);
            $pdoQuery->closeCursor();
            $pdoQuery = null;

            return $response;
        }

        public function checkBlock()
        {
            $pdoQuery = $this->_GeniSys->_secCon->prepare("
                SELECT ipv6
                FROM blocked
                Where ipv6 = :ipv6
                LIMIT 1
            ");
            $pdoQuery->execute([
                ":ipv6" => $this->_GeniSys->_helpers->getUserIP()
            ]);
            $ip=$pdoQuery->fetch(PDO::FETCH_ASSOC);
            $pdoQuery->closeCursor();
            $pdoQuery = null;

            if($ip["ipv6"]):
                session_destroy();
                die(header("Location: /Blocked"));
            endif;
        }

        public function checkSession()
        {
            $this->checkBlock();
            if(isset($_SESSION["GeniSysAI"]["Active"]) && $this->_GeniSys->_pageDetails["PageID"]=="Login"):
                die(header("Location: /Dashboard"));
            elseif(empty($_SESSION["GeniSysAI"]["Active"]) && $this->_GeniSys->_pageDetails["PageID"]!="Login"):
                die(header("Location: /"));
            endif;
        }

        public static function verifyPassword($password,$hash) {
            return password_verify($password, $hash);
        }
    }

$_GeniSysAi = new _GeniSysAi($_GeniSys);

if(filter_input(INPUT_POST, "login", FILTER_SANITIZE_STRING)):
    die(json_encode($_GeniSysAi->login()));
endif;