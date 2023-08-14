<?php
    class Auth
    {
        protected $gm;
        protected $status;
        protected $payload;
        protected $sql;
        protected $code;

        private $reg_success_status = "Register Successfully";

        private $login_success_status = "Login Successfully";
        private $login_failed_status = "Login Failed";
        
        public function __construct(\PDO $pdo)
        {
            $this->cn = $pdo;
            $this->gm = new GlobalMethods($pdo);
        }

        protected function generateHeader()
        {
            $h = [
                'type' => 'JWT',
                'alg' => 'HS256',
                'app' => 'sample',
                'dev' => 'Juan Dela Cruz'
            ];
            return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($h)));
        }

        protected function generatePayload($usercode, $fullname, $position)
        {
            $p = [
                "ucode" => $usercode,
                "role" => $position,
                "ito" => $fullname,
                "iby" => "JDCruz",
                "ie" => "jdcruz@gmail.com",
                "idate" => date_create()
            ];
            return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($p)));
        }

        public function generateToken($empno, $empfullname, $arole)
        {
            $header = $this->generateHeader();
            $payload = $this->generatePayload($empno, $empfullname, $arole);

            $signature = hash_hmac('sha256', "$header.$payload", "secret");
            $signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($signature)));

            return "$header.$payload.$signature";
        }

        function encryptPassword($pw) : ?string {
            $hashFormat="$2y$10$";
            $saltLength=22;
            $salt=$this->generateSalt($saltLength);
            return crypt($pw, $hashFormat.$salt);
        }

        function generateSalt($len) {
            $urs=md5(uniqid(mt_rand(), true));
            $b64string=base64_encode($urs);
            $b64string=str_replace('+','.', $b64string);
            return substr($b64string, 0, $len);
        }
        
        function pwordCheck($pw, $existingHash) {
            $hash=crypt($pw, $existingHash);
            if($hash===$existingHash) {
                return true;
            } 
            else {
                return false;
            }
        }
        

        /***************************** Register User ****************************/
        function register_user($reg_dt) {
            $acc_username = $reg_dt->user_email;
            $acc_password = $reg_dt->acc_password;
            $acc_role = 0;

            $user_fname = $reg_dt->user_fname;
            $user_mname = $reg_dt->user_mname;
            $user_lname = $reg_dt->user_lname;
            $user_extname = $reg_dt->user_extname;
            $user_age = $reg_dt->user_age;
            $user_phonenumber = $reg_dt->user_phonenumber;
            $user_email = $reg_dt->user_email;

            $addr_housenumber = $reg_dt->addr_housenumber;
            $addr_street = $reg_dt->addr_street;
            $addr_brgy = $reg_dt->addr_brgy;
            $addr_city = $reg_dt->addr_city;
            $addr_province = $reg_dt->addr_province;
        
            $encryptedPassword = $this->encryptPassword($acc_password);

            $sql6 = "
                INSERT INTO tbl_address
                (addr_housenumber, addr_street, addr_brgy, addr_city, addr_province) 
                VALUES ('$addr_housenumber', '$addr_street', '$addr_brgy','$addr_city', '$addr_province')
            ";
            $this->cn->query($sql6);

            //select user_id from tbl_address to insert addr_id as foreign key in tbl_user
            $this->sql5 = "SELECT addr_id FROM `tbl_address` ORDER BY addr_id DESC LIMIT 1 ";
            $addr = $this->gm->execute_query($this->sql5, "Unauthorized User");
            $addr_id = $addr['data'][0]['addr_id'];

            $sql2 = "
                INSERT INTO tbl_user
                (user_fname, user_mname, user_lname, user_extname, user_age, user_phonenumber, user_email, user_addrid) 
                VALUES ('$user_fname', '$user_mname', '$user_lname','$user_extname', '$user_age',
                '$user_phonenumber','$user_email','$addr_id')
            ";
            $this->cn->query($sql2);

            //select user_id from tbl_user to insert user_id as foreign key in tbl_account
            $this->sql = "SELECT user_id FROM `tbl_user` ORDER BY user_id DESC LIMIT 1 ";
            $data = $this->gm->execute_query($this->sql, "Unauthorized User");
            $last_insert_userid = $data['data'][0]['user_id'];

            //select from database to generate token
            $sql3 = "SELECT * FROM `tbl_user` ";
            $dt3 = $this->gm->execute_query($sql3, "Unauthorized User");
            $token = $this->generateToken($dt3['data'][0]['user_fname'], 
                                        $dt3['data'][0]['user_phonenumber'], 
                                        $dt3['data'][0]['user_email']
            );
            $tk = explode(".", $token);
            $signature = $tk[2];

            $this->sql = "
                INSERT INTO tbl_account
                (acc_username, acc_password, acc_role, acc_token, acc_userid) 
                VALUES ('$acc_username', '$encryptedPassword', '$acc_role', 
                '$signature', '$last_insert_userid')
            ";
            $this->cn->query($this->sql);

            $this->payload = array(
                'username' => $acc_username,
                'password' => $encryptedPassword,
                'token' => $signature
            );

            $this->code = 200;
            $this->status = $this->reg_success_status;
            return $this->gm->api_result($this->status, $this->payload, $this->code);
        }

        /***************************** Login ****************************/
        public function login($log_dt)
        {
            $un = $log_dt->username; //changed to email
            $pw = $log_dt->password;

            $this->code = 403;

            $this->sql = "
                SELECT * FROM tbl_account a
                INNER JOIN tbl_user u
                ON a.acc_userid = u.user_id 
                WHERE user_email = '$un'  LIMIT 1
            ";
            $dt = $this->gm->execute_query($this->sql, "Unauthorized User");

            if ($dt['code'] == 200) {
                if($this->pwordCheck($pw, $dt['data'][0]['acc_password'])) {
                    $token = $this->generateToken($dt['data'][0]['user_fname'], 
                                                  $dt['data'][0]['user_phonenumber'], 
                                                  $dt['data'][0]['user_email']
                    );
                    $tk = explode(".", $token);
                    $signature = $tk[2];

                    $this->sql = "
                        UPDATE tbl_account 
                        SET acc_token = '$signature'
                        WHERE acc_userid = ".$dt['data'][0]['acc_userid'];
                    $this->cn->query($this->sql);

                    $this->code = 200;
                    $this->status = $this->login_success_status;
                    $this->payload = array(
                        'acc_role' => $dt['data'][0]['acc_role'],
                        'acc_userid' => $dt['data'][0]['acc_userid'],
                        'user_fname' => $dt['data'][0]['user_fname'],
                        'user_lname' => $dt['data'][0]['user_lname'],
                        'user_address' => $dt['data'][0]['user_addrid'],
                        'user_is_verified' => $dt['data'][0]['user_is_verified'],
                        'token' => $signature
                    );
                }
                else {
                    $this->status =  $this->login_failed_status;
                    $this->payload = null;
                    $this->code = 403;
                }
            }
            else{
                $this->sql = "
                    SELECT * FROM tbl_account a
                    INNER JOIN tbl_office_user ou
                    ON a.acc_userid = ou.office_user_id 
                    WHERE office_user_email = '$un'  LIMIT 1
                ";
                $dt = $this->gm->execute_query($this->sql, "Unauthorized User");

                if($this->pwordCheck($pw, $dt['data'][0]['acc_password'])) {
                    $token = $this->generateToken(
                        $dt['data'][0]['office_user_fname'], 
                        $dt['data'][0]['office_user_lname'], 
                        $dt['data'][0]['office_user_email']
                    );
                    $tk = explode(".", $token);
                    $signature = $tk[2];

                    $this->sql = "
                        UPDATE tbl_account 
                        SET acc_token = '$signature'
                        WHERE acc_userid = ".$dt['data'][0]['acc_userid'];
                    $this->cn->query($this->sql);

                    $this->code = 200;
                    $this->status = $this->login_success_status;
                    $this->payload = array(
                        'acc_role' => $dt['data'][0]['acc_role'],
                        'acc_userid' => $dt['data'][0]['acc_userid'],
                        'office_user_fname' => $dt['data'][0]['office_user_fname'],
                        'office_user_lname' => $dt['data'][0]['office_user_lname'],
                        'token' => $signature
                    );
                }
                else {
                    $this->status =  $this->login_failed_status;
                    $this->payload = null;
                    $this->code = 403;
                }
            }

            return $this->gm->api_result($this->status, $this->payload, $this->code);
        }

        /***************************** Sign In With Google ****************************/
        public function google_login($log_dt)
        {
            $email = $log_dt->user_email;

            $this->code = 403;

            $this->sql = "
                SELECT tbl_account.*, tbl_user.* 
                FROM tbl_account 
                INNER JOIN tbl_user 
                ON tbl_account.acc_userid = tbl_user.user_id 
                WHERE user_email = '$email' LIMIT 1
            ";
            $dt = $this->gm->execute_query($this->sql, "Unauthorized User");

            if ($dt['code'] == 200) {
                $token = $this->generateToken($dt['data'][0]['user_fname'], 
                                                $dt['data'][0]['user_phonenumber'], 
                                                $dt['data'][0]['user_email']
                );
                $tk = explode(".", $token);
                $signature = $tk[2];

                //acc_tokenissued = '".date(DATE_ATOM, mktime())."' 

                // Update token every time you login

                $this->sql = "
                    UPDATE tbl_account 
                    SET acc_token = '$signature'
                    WHERE acc_userid = ".$dt['data'][0]['acc_userid'];
                $this->cn->query($this->sql);

                $this->code = 200;
                $this->status = $this->login_success_status;
                $this->payload = array(
                    'acc_role' => $dt['data'][0]['acc_role'],
                    'acc_userid' => $dt['data'][0]['acc_userid'],
                    'user_fname' => $dt['data'][0]['user_fname'],
                    'user_lname' => $dt['data'][0]['user_lname'],
                    'user_address' => $dt['data'][0]['user_addrid'],
                    'user_is_verified' => $dt['data'][0]['user_is_verified'],
                    'token' => $signature
                );
            }
            else{
                $this->status =  $this->login_failed_status;
                $this->payload = null;
                $this->code = 403;
            }

            return $this->gm->api_result($this->status, $this->payload, $this->code);
        }

        public function validate_token($token, $user)
        {
            $sql = "SELECT * FROM `tbl_account`  WHERE `acc_userid` = '$user' ";
            $dt = $this->gm->execute_query($sql, "Unauthorized User");
            if ($dt['data'][0]['acc_token'] === $token)
                return true;
            return false;
        }
    }
?>
