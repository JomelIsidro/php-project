<?php
    class Account
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

        
        //Admin and Office User
        public function updateAdminOfficeAccount($dt){
            //$encryptedPassword = $this->auth->encryptPassword($dt->acc_password);

            $this->sql = "
                UPDATE tbl_account
                SET acc_username = '$dt->office_user_email'
                WHERE acc_userid = '$dt->acc_userid' AND acc_role = '$dt->acc_role'
            ";
            $this->cn->query($this->sql);

            $this->sql = "
                UPDATE tbl_office_user
                SET office_user_fname = '$dt->office_user_fname', office_user_mname = '$dt->office_user_mname',
                office_user_lname = '$dt->office_user_lname', office_user_extname = '$dt->office_user_extname',
                office_user_email = '$dt->office_user_email'
                WHERE office_user_id = '$dt->acc_userid'
            ";
            $this->cn->query($this->sql);
        }
     
        //get office user 
        function getOfficeUserAccountID($filter_data){
            $this->sql = "
                SELECT * FROM tbl_account a
                JOIN tbl_office_user ou 
                ON a.acc_userid = ou.office_user_id
                WHERE office_user_id ='$filter_data'
                ORDER BY office_user_id DESC LIMIT 1
            ";

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

        //get office user 
        function changeAdminOfficePassword($dt){
            $pw = $dt->current_password;
            $encryptedPassword = $this->auth->encryptPassword($dt->new_password);

            $this->sql = "
                SELECT * FROM tbl_account a
                JOIN tbl_office_user ou 
                ON a.acc_userid = ou.office_user_id 
                WHERE office_user_id ='$dt->acc_userid' LIMIT 1
            ";

            $data = $this->gm->execute_query($this->sql, "Unauthorized User");

            if ($data['code'] == 200) {
                if($this->auth->pwordCheck($pw, $data['data'][0]['acc_password'])) {

                    $sql2 = "
                        UPDATE tbl_account
                        SET acc_password = '$encryptedPassword'
                        WHERE acc_userid = '$dt->acc_userid'
                    ";
                    $this->cn->query($sql2);

                    $this->payload = array(
                        "result" => "updated"
                    );
                    $this->status = $this->sucess_status;
                    $this->code = 200;
                }
                else {
                    $this->payload = array(
                        "result" => "wrongPassword"
                    );
                    $this->status = $this->failed_status;
                    //$this->code = 403;
                }
            } 
            else {
                $this->payload = null;
                $this->status = $this->failed_status;
                $this->code = 403;
            }

            return $this->gm->api_result($this->status, $this->payload, $this->code);
        }

    } //class
?>
