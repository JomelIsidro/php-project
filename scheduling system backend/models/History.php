<?php
    class History
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

        /***************************** VISITOR  ****************************/
        //get single to view records of arrived visitors
        function getVisitorHistoryID($filter_data){
            $getDateTime = date('Y-m-d H'); //get date today
            $this->sql = "
                SELECT * FROM tbl_request_appointment r
                JOIN tbl_office o 
                ON o.office_id = r.req_officeid 
                JOIN tbl_office_user ou 
                ON o.office_userid = ou.office_user_id  
                WHERE r.req_userid = '$filter_data'
                AND (date_format(r.req_dateTime, '%Y-%m-%d 16') <= '$getDateTime'
                OR r.req_is_arrived = 1)
                ORDER BY r.req_dateTime DESC
            ";

            $data = $this->gm->execute_query($this->sql, "Unauthorized User");

            if ($data['code'] == 200) {
                $this->payload = $data['data'];
                $this->status = $this->sucess_status;
                $this->code = 200;
            } else {
                $this->payload = null;
            }

            return array(
                "payload" => $this->payload
            );
            //return $this->gm->api_result($this->status, $this->payload, $this->code);
        }





        /***************************** OFFICE  ****************************/
        //get lists of arrived visitors
        function getOfficeArrivedHistory($filter_data){
            $dateToday = date("Y-m-d H:i:s");
            $this->sql = "
                SELECT * FROM tbl_request_appointment r
                JOIN tbl_user u 
                ON r.req_userid = u.user_id
                JOIN tbl_office o
                ON r.req_officeid = o.office_id
                JOIN tbl_office_user ou 
                ON ou.office_user_id = o.office_userid
                WHERE r.req_is_arrived = 1
                AND o.office_userid = '$filter_data' 
                ORDER BY r.req_dateTime DESC
            ";

            $data = $this->gm->execute_query($this->sql, "Unauthorized User");

            if ($data['code'] == 200) {
                $this->payload = $data['data'];
                $value = array();

                $time = date('H:i');
                for($i=0; $i<count($this->payload); $i++) {
                    // $convertDate = strftime("%B %d, %Y",strtotime($data['data'][$i]['req_dateTime']));
                    // $convertDate = $convertDate.' '.$time;
                    $requests = array(
                        'req_id' => $data['data'][$i]['req_id'],
                        'req_appointment_name' => $data['data'][$i]['req_appointment_name'],
                        // 'req_dateTime' => $convertDate,
                        'req_dateTime' => $data['data'][$i]['req_dateTime'],
                        'req_desc' => $data['data'][$i]['req_desc'],
                        'req_is_arrived' => $data['data'][$i]['req_is_arrived'],
                        'office_name' => $data['data'][$i]['office_name'],
                        'user_fname' => $data['data'][$i]['user_fname'],
                        'user_mname' => $data['data'][$i]['user_mname'],
                        'user_lname' => $data['data'][$i]['user_lname'],
                        'user_extname' => $data['data'][$i]['user_extname'],
                        'is_issued' => $data['data'][$i]['is_issued'],
                        'user_age' => $data['data'][$i]['user_age'],
                        'user_phonenumber' => $data['data'][$i]['user_phonenumber'],
                        'user_email' => $data['data'][$i]['user_email'],
                        'office_name' => $data['data'][$i]['office_name'],
                        'office_user_fname' => $data['data'][$i]['office_user_fname'],
                        'office_user_mname' => $data['data'][$i]['office_user_mname'],
                        'office_user_lname' => $data['data'][$i]['office_user_lname'],
                        'office_user_extname' => $data['data'][$i]['office_user_extname'],
                        'office_user_email' => $data['data'][$i]['office_user_email'],
                    );
                    $value[] = $requests;
                }
                $this->status = $this->sucess_status;
                $this->code = 200;
            } else {
                $value = null;
                $this->status = $this->failed_status;
                $this->code = 403;
            }

            return array(
                "payload" => $value
            );

        }

        //get lists of not arrived visitors
        function getOfficeNotArrivedHistory($filter_data){
            $getDateTime = date('Y-m-d H'); //get date today
            $this->sql = "
                SELECT * FROM tbl_request_appointment r
                JOIN tbl_user u 
                ON r.req_userid = u.user_id
                JOIN tbl_office o
                ON r.req_officeid = o.office_id
                JOIN tbl_office_user ou 
                ON ou.office_user_id = o.office_userid
                WHERE r.req_is_arrived = 0 AND date_format(r.req_dateTime, '%Y-%m-%d 16') <= '$getDateTime'
                AND o.office_userid = '$filter_data' 
                ORDER BY r.req_dateTime DESC
            ";

            $data = $this->gm->execute_query($this->sql, "Unauthorized User");

            if ($data['code'] == 200) {
                $this->payload = $data['data'];
                $value = array();

                $time = date('H:i');
                for($i=0; $i<count($this->payload); $i++) {
                    // $convertDate = strftime("%B %d, %Y",strtotime($data['data'][$i]['req_dateTime']));
                    // $convertDate = $convertDate.' '.$time;
                    $requests = array(
                        'req_id' => $data['data'][$i]['req_id'],
                        'req_appointment_name' => $data['data'][$i]['req_appointment_name'],
                        // 'req_dateTime' => $convertDate,
                        'req_dateTime' => $data['data'][$i]['req_dateTime'],
                        'req_desc' => $data['data'][$i]['req_desc'],
                        'req_is_arrived' => $data['data'][$i]['req_is_arrived'],
                        'office_name' => $data['data'][$i]['office_name'],
                        'user_fname' => $data['data'][$i]['user_fname'],
                        'user_mname' => $data['data'][$i]['user_mname'],
                        'user_lname' => $data['data'][$i]['user_lname'],
                        'user_extname' => $data['data'][$i]['user_extname'],
                        'is_issued' => $data['data'][$i]['is_issued'],
                        'user_age' => $data['data'][$i]['user_age'],
                        'user_phonenumber' => $data['data'][$i]['user_phonenumber'],
                        'user_email' => $data['data'][$i]['user_email'],
                        'office_name' => $data['data'][$i]['office_name'],
                        'office_user_fname' => $data['data'][$i]['office_user_fname'],
                        'office_user_mname' => $data['data'][$i]['office_user_mname'],
                        'office_user_lname' => $data['data'][$i]['office_user_lname'],
                        'office_user_extname' => $data['data'][$i]['office_user_extname'],
                        'office_user_email' => $data['data'][$i]['office_user_email'],
                    );
                    $value[] = $requests;
                }
                $this->status = $this->sucess_status;
                $this->code = 200;
            } else {
                $value = null;
                $this->status = $this->failed_status;
                $this->code = 403;
            }

            return array(
                "payload" => $value
            );
        }

       


        /***************************** ADMIN  ****************************/
        //get lists of arrived visitors
        function getAdminArrivedHistory($filter_data){
            $dateToday = date("Y-m-d H:i:s");
            $this->sql = "
                SELECT * FROM tbl_request_appointment r
                JOIN tbl_user u 
                ON r.req_userid = u.user_id
                JOIN tbl_office o
                ON r.req_officeid = o.office_id
                JOIN tbl_office_user ou 
                ON ou.office_user_id = o.office_userid
                WHERE r.req_is_arrived = 1
                ORDER BY r.req_dateTime DESC
            ";

            $data = $this->gm->execute_query($this->sql, "Unauthorized User");

            if ($data['code'] == 200) {
                $this->payload = $data['data'];
                $value = array();

                $time = date('H:i');
                for($i=0; $i<count($this->payload); $i++) {
                    // $convertDate = strftime("%B %d, %Y",strtotime($data['data'][$i]['req_dateTime']));
                    // $convertDate = $convertDate.' '.$time;
                    $requests = array(
                        'req_id' => $data['data'][$i]['req_id'],
                        'req_appointment_name' => $data['data'][$i]['req_appointment_name'],
                        // 'req_dateTime' => $convertDate,
                        'req_dateTime' => $data['data'][$i]['req_dateTime'],
                        'req_desc' => $data['data'][$i]['req_desc'],
                        'req_is_arrived' => $data['data'][$i]['req_is_arrived'],
                        'office_name' => $data['data'][$i]['office_name'],
                        'user_fname' => $data['data'][$i]['user_fname'],
                        'user_mname' => $data['data'][$i]['user_mname'],
                        'user_lname' => $data['data'][$i]['user_lname'],
                        'user_extname' => $data['data'][$i]['user_extname'],
                        'is_issued' => $data['data'][$i]['is_issued'],
                        'user_age' => $data['data'][$i]['user_age'],
                        'user_phonenumber' => $data['data'][$i]['user_phonenumber'],
                        'user_email' => $data['data'][$i]['user_email'],
                        'office_name' => $data['data'][$i]['office_name'],
                        'office_user_fname' => $data['data'][$i]['office_user_fname'],
                        'office_user_mname' => $data['data'][$i]['office_user_mname'],
                        'office_user_lname' => $data['data'][$i]['office_user_lname'],
                        'office_user_extname' => $data['data'][$i]['office_user_extname'],
                        'office_user_email' => $data['data'][$i]['office_user_email'],
                    );
                    $value[] = $requests;
                }
                $this->status = $this->sucess_status;
                $this->code = 200;
            } else {
                $value = null;
                $this->status = $this->failed_status;
                $this->code = 403;
            }

            return array(
                "payload" => $value
            );
        }

        //get lists of not arrived visitors
        function getAdminNotArrivedHistory($filter_data){
            $getDateTime = date('Y-m-d H'); //get date today

            $value = array();

            $this->sql = "
                SELECT * FROM tbl_request_appointment r
                JOIN tbl_user u 
                ON r.req_userid = u.user_id
                JOIN tbl_office o
                ON r.req_officeid = o.office_id
                JOIN tbl_office_user ou 
                ON ou.office_user_id = o.office_userid
                WHERE date_format(r.req_dateTime, '%Y-%m-%d 16') <= '$getDateTime'
                AND r.req_is_arrived = 0
                ORDER BY r.req_dateTime DESC
            ";

            $data = $this->gm->execute_query($this->sql, "Unauthorized User");

            if ($data['code'] == 200) {
                $this->payload = $data['data'];
                

                $time = date('H:i');
                for($i=0; $i<count($this->payload); $i++) {
                    // $convertDate = strftime("%B %d, %Y",strtotime($data['data'][$i]['req_dateTime']));
                    // $convertDate = $convertDate.' '.$time;
                    $requests = array(
                        'req_id' => $data['data'][$i]['req_id'],
                        'req_appointment_name' => $data['data'][$i]['req_appointment_name'],
                        // 'req_dateTime' => $convertDate,
                        'req_dateTime' => $data['data'][$i]['req_dateTime'],
                        'req_desc' => $data['data'][$i]['req_desc'],
                        'req_is_arrived' => $data['data'][$i]['req_is_arrived'],
                        'office_name' => $data['data'][$i]['office_name'],
                        'user_fname' => $data['data'][$i]['user_fname'],
                        'user_mname' => $data['data'][$i]['user_mname'],
                        'user_lname' => $data['data'][$i]['user_lname'],
                        'user_extname' => $data['data'][$i]['user_extname'],
                        'is_issued' => $data['data'][$i]['is_issued'],
                        'user_age' => $data['data'][$i]['user_age'],
                        'user_phonenumber' => $data['data'][$i]['user_phonenumber'],
                        'user_email' => $data['data'][$i]['user_email'],
                        'office_name' => $data['data'][$i]['office_name'],
                        'office_user_fname' => $data['data'][$i]['office_user_fname'],
                        'office_user_mname' => $data['data'][$i]['office_user_mname'],
                        'office_user_lname' => $data['data'][$i]['office_user_lname'],
                        'office_user_extname' => $data['data'][$i]['office_user_extname'],
                        'office_user_email' => $data['data'][$i]['office_user_email'],
                    );
                    $value[] = $requests;
                }
                $this->status = $this->sucess_status;
                $this->code = 200;
            } else {
                $value = null;
                $this->status = $this->failed_status;
                $this->code = 403;
            }

            return array(
                "payload" => $value
            );
        }

   
        /***************************** ADMIN & OFFICE  ****************************/
        //get single to view records of arrived visitors
        function viewArrivedHistoryID($filter_data){
            $dateToday = date("Y-m-d H:i:s");
            $this->sql = "
                SELECT * FROM tbl_request_appointment r
                JOIN tbl_user u 
                ON r.req_userid = u.user_id
                JOIN tbl_office o
                ON r.req_officeid = o.office_id
                JOIN tbl_office_user ou 
                ON ou.office_user_id = o.office_userid
                JOIN tbl_address a
                ON u.user_addrid = a.addr_id
                WHERE r.req_is_arrived = 1
                AND r.req_id = '$filter_data'
                ORDER BY r.req_dateTime DESC
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

        //get single to view records of not arrived visitors
        function viewNotArrivedHistoryID($filter_data){
            $getDateTime = date('Y-m-d H'); //get date today
            $this->sql = "
                SELECT * FROM tbl_request_appointment r
                JOIN tbl_user u 
                ON r.req_userid = u.user_id
                JOIN tbl_office o
                ON r.req_officeid = o.office_id
                JOIN tbl_office_user ou 
                ON ou.office_user_id = o.office_userid
                JOIN tbl_address a
                ON u.user_addrid = a.addr_id
                WHERE r.req_is_arrived = 0 AND date_format(r.req_dateTime, '%Y-%m-%d 16') <= '$getDateTime'
                AND r.req_id = '$filter_data'
                ORDER BY r.req_dateTime DESC
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
       
     

    } //class
?>
