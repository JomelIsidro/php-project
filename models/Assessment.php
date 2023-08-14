<?php
    class Assessment
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

        //new process

        //check if the visitor has request
        function checkRequest($userid){
            $datenow = date("Y-m-d");
            $this->sql = "
                SELECT * FROM tbl_request_appointment r
                WHERE DATE(r.req_dateTime) >= '$datenow'
                AND r.req_userid = '$userid'
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

        //check if the visitor has an assessment today
        function checkTodayAssessment($userid){
            $datenow = date("Y-m-d");
            $this->sql = "
                SELECT * FROM tbl_assessment
                WHERE DATE(assessment_issued) = '$datenow'
                AND user_id = '$userid'
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

        //check if the visitor has a request for today
        function checkTodayRequest($userid){
            $datenow = date("Y-m-d");
            $this->sql = "
                SELECT * FROM tbl_request_appointment
                WHERE req_userid = '$userid'
                AND DATE(req_dateTime) = '$datenow'
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

        //check if the visitor has a request for future
        function checkFutureRequest($userid){
            $datenow = date("Y-m-d");
            $this->sql = "
                SELECT * FROM tbl_request_appointment
                WHERE req_userid = '$userid'
                AND DATE(req_dateTime) > '$datenow'
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
        function getAssessmentID($userid){
            $datenow = date("Y-m-d");
            $this->sql = "
                SELECT * FROM tbl_assessment 
                WHERE user_id = '$userid'
                AND DATE(assessment_issued) = '$datenow'
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

        function dontDisplayAssessment($userid){
            $datenow = date("Y-m-d");
            $this->sql = "
                SELECT * FROM tbl_set_appointment s 
                JOIN tbl_req_appointment r
                ON s.set_id = r.req_setid 
                WHERE r.req_userid = '$userid'
                AND DATE(s.StartTime) > '$datenow'
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

        function displayAssessment($userid){
            $datenow = date("Y-m-d");
            $userID = $userid;
            $this->sql = "
                SELECT * FROM tbl_set_appointment s 
                JOIN tbl_req_appointment r
                ON s.set_id = r.req_setid 
                WHERE r.req_userid = '$userid'
                AND DATE(s.StartTime) = '$datenow'
            ";

            $dt = $this->gm->execute_query($this->sql, "Unauthorized User");

            if ($dt['code'] == 200) {
                $startDate = $dt['data'][0]['StartTime'];
                $time = strtotime($startDate);
                $startTime = date('Y-m-d',$time);

                $sql2 = "
                    SELECT * FROM tbl_assessment
                    WHERE DATE(assessment_issued) = '$startTime'
                    AND user_id = '$userID'
                ";

                $data = $this->gm->execute_query($sql2, "Unauthorized User");

                if ($data['code'] == 200) {
                    $this->payload = $data['data'];
                    $this->status = "hasRequestAndAssessment";
                    $this->code = 200;
                }
                else {
                    $this->payload = "noAssessment";  
                    $this->status = "noAssessmentStatus"; 
                }
            } 
            else {
                $this->payload = "noRequest";   
                $this->status = "noRequestStatus"; 
            }

            return array(
                "payload" => $this->payload,
                "status" => $this->status
            );

        }

        function noRequest($userid){
            $datenow = date("Y-m-d");
            $this->sql = "
                SELECT * FROM tbl_set_appointment s 
                JOIN tbl_req_appointment r
                ON s.set_id = r.req_setid 
                WHERE DATE(s.StartTime) >= '$datenow'
                AND r.req_userid = '$userid'
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
        
        
        



      

        
        


    } //class
?>
