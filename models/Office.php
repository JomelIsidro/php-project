<?php
    class Office
    {
        protected $gm;
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
        }


        /*********************************** OFFICE ****************************************/
        //NEW process
        function selectAppointmentOfOfficeID($filter_data){
            $this->sql = "
                SELECT * FROM tbl_appointment a
                JOIN tbl_office o
                ON a.office_id = o.office_id
                JOIN tbl_office_user ou
                ON ou.office_user_id = o.office_userid
                WHERE ou.office_user_id = '$filter_data'
            ";

            $data = $this->gm->execute_query($this->sql, "Unauthorized User");

            if ($data['code'] == 200) {
                $this->payload = $data['data'];
                $this->status = $this->sucess_status;
                $this->code = 200;
            } 
            else {
                $this->payload = null;   
            }

            return array(
                "payload" => $this->payload
            );
        }

        //get account user_id for select option in adding and updating office
        function getOfficeUserID($filter_data){
            $this->sql = "
                SELECT * FROM tbl_account a
                JOIN tbl_office_user u
                ON a.acc_userid = u.office_user_id
                WHERE acc_role = 2
            ";

            if ($filter_data!=null) {
                $this->sql.= " AND acc_id ='$filter_data'";
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

        function getOffice($filter_data){
            $this->sql = "
                SELECT * FROM tbl_office_user ou
                JOIN tbl_office o
                ON ou.office_user_id = o.office_userid
            ";

            if ($filter_data!=null) {
                $this->sql.= " WHERE office_id ='$filter_data'";
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

        public function addOffice($dt){
            $this->sql = "
                INSERT INTO tbl_office(office_name, office_userid) 
                VALUES ('$dt->office_name','$dt->office_userid')
            ";
            $this->cn->query($this->sql);
        }

        public function updateOffice($dt){
            $this->sql = "
                UPDATE tbl_office
                SET office_name = '$dt->office_name', office_userid = '$dt->office_userid'
                WHERE office_id = '$dt->office_id'
            ";
            $this->cn->query($this->sql);
        }
     

    } //class
?>
