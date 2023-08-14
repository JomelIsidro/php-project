<?php
    class Visitor
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



        //the visitor will confirm the changing of date request of office head
        public function approvedChangeRequest($dt){
            $this->sql = "
                UPDATE tbl_request_appointment
                SET req_status = 3
                WHERE req_id = '$dt->req_id' 
            ";
            $this->cn->query($this->sql);
        }


        function getTodayArrivedVisitors($filter_data){
            $datenow = date("Y-m-d");
            $this->sql = "
                SELECT * FROM tbl_request_appointment r
                JOIN tbl_user u 
                ON u.user_id = r.req_userid
                JOIN tbl_office o 
                ON o.office_id = r.req_officeid
                JOIN tbl_office_user ou
                ON ou.office_user_id = o.office_userid
                WHERE DATE(r.req_dateTime) = '$datenow'
                AND r.req_is_arrived = 1
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


        function getTodayVisitors($filter_data){
            $datenow = date("Y-m-d");
            $this->sql = "
                SELECT * FROM tbl_request_appointment r
                JOIN tbl_user u 
                ON u.user_id = r.req_userid
                JOIN tbl_office o 
                ON o.office_id = r.req_officeid
                JOIN tbl_office_user ou
                ON ou.office_user_id = o.office_userid
                WHERE DATE(r.req_dateTime) = '$datenow'
                AND r.req_is_arrived = 0
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

        function getVisitorID($filter_data){
            $this->sql = "
                SELECT * FROM tbl_user u
                JOIN tbl_address a
                ON u.user_addrid = a.addr_id
                WHERE user_id = '$filter_data'
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

            return $this->gm->api_result($this->status, $this->payload, $this->code);

        }

     

    } //class
?>
