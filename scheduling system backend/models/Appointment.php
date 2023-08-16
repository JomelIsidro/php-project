<?php
    class Appointment
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


        /*********************************** VISTOR ****************************************/
        //this is the validation of holidays in requesting an appointment
         function validateHolidays($filter_data){
            $this->sql = "
                SELECT * FROM tbl_holidays
                WHERE holi_date = '$filter_data'
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


        /*********************************** ADMIN ****************************************/

        //add holidays to disable date in requesting of visitor
        public function addHolidays($dt){
            $this->sql = "
                INSERT INTO tbl_holidays(holi_date,holi_desc) 
                VALUES ('$dt->holi_date','$dt->holi_desc')
            ";
            $this->cn->query($this->sql);
        }

        //display holidays
        function selectHolidays($filter_data){
            $this->sql = "
                SELECT * FROM tbl_holidays 
                ORDER BY holi_date DESC
            ";

            $data = $this->gm->execute_query($this->sql, "Unauthorized User");

            if ($data['code'] == 200) {
                $this->payload = $data['data'];

                $value = array();

                for($i=0; $i<count($this->payload); $i++) {
                    // $convertDate = strftime("%B %d, %Y",strtotime($data['data'][$i]['holi_date']));
                    $holidays = array(
                        'holi_desc' => $data['data'][$i]['holi_desc'],
                        'holi_date' => $data['data'][$i]['holi_date']
                        // 'holi_date' => $convertDate
                    );
                    $value[] = $holidays;
                }

                $this->status = $this->sucess_status;
                $this->code = 200;
            } 
            else {
                $this->payload = null;   
            }

            return array(
                "payload" => $value
            );
        }


        /*********************************** VISITOR ****************************************/
        //display all request
        function selectAllRequestAppointmentID($filter_data){
            $getDate = date('Y-m-d'); // get date today
            $this->sql = "
                SELECT * FROM tbl_request_appointment a
                JOIN tbl_office o
                ON a.req_officeid = o.office_id
                JOIN tbl_office_user ou 
                ON ou.office_user_id = o.office_userid
                WHERE a.req_userid = '$filter_data'
                AND DATE(a.req_dateTime) > '$getDate'
                ORDER BY a.req_dateTime ASC
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
        

        //display request for today
        function selectTodayRequestAppointmentID($filter_data){
            $getDate = date('Y-m-d'); // get date today
            $getTime = date('H:i'); //get time today
            $this->sql = "
                SELECT * FROM tbl_request_appointment a
                JOIN tbl_office o
                ON a.req_officeid = o.office_id
                JOIN tbl_office_user ou 
                ON ou.office_user_id = o.office_userid
                WHERE a.req_userid = '$filter_data'
                AND DATE(a.req_dateTime) = '$getDate'
                AND date_format(a.req_dateTime, '15:59') >= '$getTime'
                AND a.req_is_arrived = 0
                ORDER BY a.req_dateTime ASC
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


        function selectAppointmentID($filter_data){
            $this->sql = "
                SELECT * FROM tbl_appointment a
                JOIN tbl_office o 
                ON a.office_id = o.office_id 
                WHERE a.office_id = '$filter_data'
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

        public function addRequestAppointment($dt){
            $this->sql = "
                INSERT INTO tbl_request_appointment(req_appointment_name,req_dateTime,req_desc,req_officeid,req_userid) 
                VALUES ('$dt->appointment_name','$dt->dateTime','$dt->req_desc','$dt->office_id','$dt->req_userid')
            ";
            $this->cn->query($this->sql);
        }


        function countTheTotalOfRequest($filter_data){
            $this->sql = "
                SELECT * FROM tbl_request_appointment
                WHERE DATE(req_dateTime) = '$filter_data'
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

        /*********************************** ADMIN ****************************************/
        public function updateLimitOfVisitor($dt){
            $this->sql = "
                UPDATE tbl_limit
                SET limitOfVisitor = '$dt->limitOfVisitor'
                WHERE id = 1
            ";
            $this->cn->query($this->sql);
        }

        function getLimitOfVisitor($filter_data){
            $this->sql = "
                SELECT limitOfVisitor FROM tbl_limit
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
        
        function getAppointmentWithOffice($filter_data){
            $this->sql = "
                SELECT * FROM tbl_appointment a
                JOIN tbl_office o
                ON a.office_id = o.office_id
                JOIN tbl_office_user ou
                ON o.office_userid = ou.office_user_id
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

        public function addAppointmentWithOffice($dt){
            $this->sql = "
                INSERT INTO tbl_appointment(appointment_name,office_id) 
                VALUES ('$dt->appointment_name','$dt->office_id')
            ";
            $this->cn->query($this->sql);
        }










        //count number of appointment
        function countAppointment($startTime){
            $this->sql = "
                SELECT * FROM tbl_set_appointment 
                WHERE DATE(StartTime) = '$startTime'
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

        function getAdminAppointment($filter_data){
            $this->sql = "
                SELECT * FROM tbl_set_appointment s
                JOIN tbl_office o
                ON s.set_officeid = o.office_id
                JOIN tbl_office_user ou
                ON o.office_userid = ou.office_user_id
                ORDER BY s.StartTime DESC
            ";

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

            return $this->payload;
        }

        public function addAdminAppointment($dt){
            $is_requested = 0;
            $this->sql = "
                INSERT INTO tbl_set_appointment(Subject, StartTime, EndTime, is_requested, set_officeid) 
                VALUES ('$dt->Subject','$dt->StartTime','$dt->StartTime','$is_requested', '$dt->set_officeid')
            ";
            $this->cn->query($this->sql);
        }

        public function updateAdminAppointment($dt){
            $this->sql = "
                UPDATE tbl_set_appointment
                SET Subject = '$dt->Subject',
                StartTime = '$dt->StartTime',  
                Endtime = '$dt->StartTime',
                set_officeid = '$dt->set_officeid'
                WHERE set_id = '$dt->set_id'
            ";
            $this->cn->query($this->sql);
        }



        /*********************************** USERS ****************************************/
        function getAppointmentID($startTime){
            $this->sql = "
                SELECT * FROM tbl_set_appointment s
                INNER JOIN tbl_office o 
                ON s.set_officeid = o.office_id
                INNER JOIN tbl_office_user ou
                ON ou.office_user_id = o.office_userid
                WHERE Date(s.StartTime) = '$startTime' 
                AND s.is_requested = 0 
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
            // return $this->gm->api_result($this->status, $this->payload, $this->code);
        }

        function getAppointment($filter_data){
            $dateToday = date("Y-m-d");
            $this->sql = "
                SELECT * FROM tbl_set_appointment s
                JOIN tbl_office o 
                ON s.set_officeid = o.office_id
                WHERE Date(StartTime) >= '$dateToday'
            ";

            if ($filter_data!=null) {
                $this->sql.= " AND set_id ='$filter_data'";
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

            return $this->payload;
        }

        public function insertAppointment($dt){
            $is_arrived = 0;
            $dateissued = date("Y-m-d H:m:s");
            $this->sql = "
                INSERT INTO tbl_req_appointment(req_dateissued, req_is_arrived, req_userid, req_setid) 
                VALUES ('$dateissued','$is_arrived','$dt->req_userid','$dt->req_setid')
            ";
            $this->cn->query($this->sql);

            $sql2 = "
                UPDATE tbl_set_appointment 
                SET is_requested = 1
                WHERE set_id = '$dt->req_setid'
            ";
            $this->cn->query($sql2);
        }

        function getAppointmentIfExist($req_userid, $startTime){
            $this->sql = "
                SELECT * FROM tbl_req_appointment r 
                JOIN tbl_set_appointment s 
                ON s.set_id = r.req_setid 
                WHERE r.req_userid = '$req_userid' 
                AND DATE(s.StartTime) = '$startTime'
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

        //get all request
        function getAllRequestAppointmentID($filter_data){
            // $newTime = date("Y-m-d H:i:s", strtotime('+7 hours'));
            $dateToday = date("Y-m-d H:i:s");
            $this->sql = "
                SELECT * FROM tbl_set_appointment s
                JOIN tbl_req_appointment r
                ON s.set_id = r.req_setid
                JOIN tbl_office o
                ON o.office_id = s.set_officeid
                JOIN tbl_office_user ou 
                ON ou.office_user_id = o.office_userid
                WHERE r.req_userid = '$filter_data'
                AND (s.StartTime + interval 7 hour) >= '$dateToday'
                ORDER BY s.StartTime ASC
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
            return array(
                "payload" => $this->payload
            );

            //return $this->gm->api_result($this->status, $this->payload, $this->code);
        }

        //get only one request
        function getRequestAppointmentID($filter_data){
            // $newTime = date("Y-m-d H:i:s", strtotime('+7 hours'));
            $dateToday = date("Y-m-d H:i:s");
            $this->sql = "
                SELECT * FROM tbl_set_appointment s
                JOIN tbl_req_appointment r
                ON s.set_id = r.req_setid
                JOIN tbl_office o
                ON o.office_id = s.set_officeid
                JOIN tbl_office_user ou 
                ON ou.office_user_id = o.office_userid
                WHERE r.req_userid = '$filter_data'
                AND (s.StartTime + interval 7 hour) >= '$dateToday'
                ORDER BY s.StartTime ASC
                LIMIT 1
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
            return array(
                "payload" => $this->payload
            );

            //return $this->gm->api_result($this->status, $this->payload, $this->code);
        }

        function getRequestAppointmentHistoryID($filter_data){
            //$newTime = date("Y-m-d H:i:s", strtotime('+7 hours'));
            $dateToday = date("Y-m-d H:i:s");
            $this->sql = "
                SELECT * FROM tbl_set_appointment s
                JOIN tbl_req_appointment r
                ON s.set_id = r.req_setid
                JOIN tbl_office o
                ON o.office_id = s.set_officeid
                JOIN tbl_office_user ou 
                ON ou.office_user_id = o.office_userid
                WHERE r.req_userid = '$filter_data'
                AND (s.StartTime + interval 7 hour) <= '$dateToday'
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

            return array(
                "payload" => $this->payload
            );
            //return $this->gm->api_result($this->status, $this->payload, $this->code);
        }

        /*********************************** OFFICE USERS ****************************************/
        function getOfficeAppointmentID($id){
            $this->sql = "
                SELECT * FROM tbl_set_appointment s
                JOIN tbl_office o 
                ON s.set_officeid = o.office_id
                JOIN tbl_office_user ou
                ON ou.office_user_id = o.office_userid
                WHERE o.office_userid = '$id' 
                ORDER BY s.StartTime DESC
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
            // return $this->gm->api_result($this->status, $this->payload, $this->code);
        }
        
        



      

        
        


    } //class
?>
