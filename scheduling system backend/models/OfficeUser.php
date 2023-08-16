<?php
    class OfficeUser 
    {
        protected $gm;
        protected $auth;
        protected $sql;
        protected $cn;
        protected $code;
        protected $payload;
        protected $status;

        private $sucess_status = "Successfully retrieved requested records";
        private $failed_status = "Failed to retrieve records";

        public function __construct(\PDO $pdo)
        {
            $this->cn = $pdo;
            $this->gm = new GlobalMethods($pdo);
            $this->auth = new Auth($pdo);
        }

        function getOfficeUser($filter_data){
            $this->sql = "
                SELECT * FROM tbl_office_user ou
                JOIN tbl_account a 
                ON a.acc_userid = ou.office_user_id
                WHERE a.acc_role = 2
                ORDER BY office_user_id DESC
            ";

            if ($filter_data!=null) {
                $this->sql.= " WHERE office_user_id ='$filter_data'";
            }
            $this->code = 403;

            $data = $this->gm->execute_query($this->sql, "Unauthorized User");

            if ($data['code'] == 200) {
                $this->payload = $data['data'];
                $this->status = $this->sucess_status;
                $this->code = 200;
            } else {
                $this->payload = null;
                $this->status = $this->failed_status;
                $this->code = 403;
            }

            return $this->gm->api_result($this->status, $this->payload, $this->code);
        }

        public function addOfficeUser($dt){
            $role = 2;

            $this->sql = "
                INSERT INTO tbl_office_user(office_user_fname, office_user_mname, office_user_lname, office_user_extname, office_user_email) 
                VALUES ('$dt->office_user_fname','$dt->office_user_mname','$dt->office_user_lname','$dt->office_user_extname','$dt->office_user_email')
            ";
            $this->cn->query($this->sql);

            $this->sql = "SELECT * FROM `tbl_office_user` ORDER BY office_user_id DESC LIMIT 1 ";
            $data = $this->gm->execute_query($this->sql, "Unauthorized User");
            $last_insert_userid = $data['data'][0]['office_user_id'];
            $encryptedPassword = $this->auth->encryptPassword($dt->office_user_password);

            $token = $this->auth->generateToken(
                $data['data'][0]['office_user_fname'], 
                $data['data'][0]['office_user_lname'], 
                $data['data'][0]['office_user_email']
            );
            $tk = explode(".", $token);
            $signature = $tk[2];

            $sql2 = "
                INSERT INTO tbl_account
                (acc_username, acc_password, acc_role, acc_token, acc_userid) 
                VALUES ('$dt->office_user_email','$encryptedPassword', '$role', 
                '$signature', '$last_insert_userid')
            ";
            $this->cn->query($sql2);

            // $this->payload = array(
            //     'office_user_fname' => $dt->office_user_fname,
            //     'id' => $last_insert_userid,
            //     'encrypted_password' => $encryptedPassword,
            //     'signature' => $signature
            // );

            // return $this->payload;
        }

      

    } //class
?>
