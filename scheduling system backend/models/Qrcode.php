<?php
    class Qrcode
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

        function selectQRCode($user_id){
            $dateToday = date('Y-m-d');
            $this->sql = "
                SELECT * FROM tbl_assessment 
                WHERE user_id = '$user_id'
                AND DATE(assessment_issued) = '$dateToday'
                LIMIT 1
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











        //old process
        function getQrID($qrid){
            $this->sql = "
                SELECT * FROM tbl_req_appointment r
                JOIN tbl_user u
                ON r.req_userid = u.user_id
                JOIN tbl_set_appointment s 
                ON s.set_id = r.req_setid 
                JOIN tbl_office o
                ON o.office_id = s.set_officeid
                JOIN tbl_office_user ou 
                ON ou.office_user_id = o.office_userid
                WHERE req_id = '$qrid' LIMIT 1
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

     
       //display qr code if the visitor is already take an assessment
        function getQrCode($user_id){
           $dateToday = date("Y-m-d");
            $this->sql = "
                SELECT * FROM tbl_set_appointment s
                JOIN tbl_req_appointment r
                ON s.set_id = r.req_setid 
                WHERE r.req_userid = '$user_id'
                AND DATE(s.StartTime) = '$dateToday'
            ";

            $dt = $this->gm->execute_query($this->sql, "Unauthorized User");

            if ($dt['code'] == 200) {
                $startDate = $dt['data'][0]['StartTime'];
                $time = strtotime($startDate);
                $startTime = date('Y-m-d',$time);

                $sql2 = "
                    SELECT * FROM tbl_assessment
                    WHERE DATE(assessment_issued) = '$startTime'
                    AND user_id = '$user_id'
                ";
                $dt2 = $this->gm->execute_query($sql2, "Unauthorized User");

                if ($dt2['code'] == 200) { 
                    $this->payload = $dt2['data'];
                    $this->status = $this->sucess_status;
                    $this->code = 200;
                }
                else {
                    $this->payload = null;   
                }
            } 
            else {
                $this->payload = null;   
            }

            return array(
                "payload" => $this->payload
            );

        }
        
        //update req_is_arrived if the qr was scanned
        function scanQrCode($dt){
            $this->sql = "
                UPDATE tbl_request_appointment
                SET req_is_arrived = 1
                WHERE req_id = '$dt->req_id'
            ";
            $this->cn->query($this->sql);
        }
        

    
    
    
    } //class
?>
