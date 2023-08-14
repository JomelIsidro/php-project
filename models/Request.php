<?php
    class Request
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

        /*********************************** OFFICE REQUEST New Process ****************************************/

        //get reason of office head and display on visitor 
        function selectOfficeRequestReason($filter_data){
            $this->sql = "
                SELECT * FROM tbl_request_appointment a
                JOIN tbl_office o 
                ON a.req_officeid = o.office_id
                JOIN tbl_office_user ou
                ON o.office_userid = ou.office_user_id
                WHERE req_id = '$filter_data'
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


        //need to change the request of visitor by the office
        public function changeRequest($dt){
            $this->sql = "
                UPDATE tbl_request_appointment
                SET req_status = 2, req_reason = '$dt->req_reason', req_dateTime = '$dt->req_dateTime'
                WHERE req_id = '$dt->req_id' 
            ";
            $this->cn->query($this->sql);
        }


        //approved the request of visitor by office head
        public function approveRequest($dt){
            $this->sql = "
                UPDATE tbl_request_appointment
                SET req_status = 1
                WHERE req_id = '$dt->req_id'
            ";
            $this->cn->query($this->sql);
        }

        //display Today's request appointment
        function selectOfficeTodayRequestID($filter_data){
            $getTime = date('H:i'); //get time of today
            $getDate = date('Y-m-d');
            $this->sql = "
                SELECT * FROM tbl_request_appointment a 
                JOIN tbl_office o
                ON a.req_officeid = o.office_id
                JOIN tbl_user u 
                ON u.user_id = a.req_userid
                WHERE DATE(a.req_dateTime) = '$getDate' 
                AND date_format(a.req_dateTime, '15:59') >= '$getTime'
                AND o.office_userid = '$filter_data'
                ORDER BY a.req_dateTime ASC
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
                        'office_name' => $data['data'][$i]['office_name'],
                        'user_fname' => $data['data'][$i]['user_fname'],
                        'user_mname' => $data['data'][$i]['user_mname'],
                        'user_lname' => $data['data'][$i]['user_lname'],
                        'user_extname' => $data['data'][$i]['user_extname'],
                        'user_email' => $data['data'][$i]['user_email'],
                        'req_status' => $data['data'][$i]['req_status']
                    );
                    $value[] = $requests;
                }
                $this->status = $this->sucess_status;
                $this->code = 200;
            } 
            else {
                $value = null;   
            }

            return array(
                "payload" => $value
            );
        }


        //display all request appointment
        function selectOfficeAllRequestID($filter_data){
            $getDateTime = date('Y-m-d H:i'); //get date today
            $this->sql = "
                SELECT * FROM tbl_request_appointment a 
                JOIN tbl_office o
                ON a.req_officeid = o.office_id
                JOIN tbl_user u 
                ON u.user_id = a.req_userid
                WHERE date_format(a.req_dateTime, '%Y-%m-%d 15:59') >= '$getDateTime'
                AND o.office_userid = '$filter_data'
                ORDER BY a.req_dateTime ASC
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
                        'office_name' => $data['data'][$i]['office_name'],
                        'user_fname' => $data['data'][$i]['user_fname'],
                        'user_mname' => $data['data'][$i]['user_mname'],
                        'user_lname' => $data['data'][$i]['user_lname'],
                        'user_extname' => $data['data'][$i]['user_extname'],
                        'user_email' => $data['data'][$i]['user_email'],
                        'req_status' => $data['data'][$i]['req_status']
                    );
                    $value[] = $requests;
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

        /*********************************** ADMIN REQUEST New Process ****************************************/

        //display Today's request appointment
        function selectAdminTodayRequest($filter_data){
            $getTime = date('H:i'); //get time of today
            $getDate = date('Y-m-d');
            $this->sql = "
                SELECT * FROM tbl_request_appointment a 
                JOIN tbl_office o
                ON a.req_officeid = o.office_id
                JOIN tbl_user u 
                ON u.user_id = a.req_userid
                WHERE DATE(a.req_dateTime) = '$getDate' 
                AND date_format(a.req_dateTime, '15:59') >= '$getTime'
                ORDER BY a.req_dateTime ASC
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
                        'office_name' => $data['data'][$i]['office_name'],
                        'user_fname' => $data['data'][$i]['user_fname'],
                        'user_mname' => $data['data'][$i]['user_mname'],
                        'user_lname' => $data['data'][$i]['user_lname'],
                        'user_extname' => $data['data'][$i]['user_extname']
                    );
                    $value[] = $requests;
                }

                $this->status = $this->sucess_status;
                $this->code = 200;
            } 
            else {
                $value = null;   
            }

            return array(
                "payload" => $value
            );
        }

        //display request appointment
        // function selectAllRequest($filter_data){
        //     $getDateTime = date('Y-m-d H:i'); //get date today
        //     $this->sql = "
        //         SELECT * FROM tbl_request_appointment a
        //         JOIN tbl_office o
        //         ON a.req_officeid = o.office_id
        //         JOIN tbl_office_user ou 
        //         ON ou.office_user_id = o.office_userid 
        //         JOIN tbl_user u 
        //         ON u.user_id = a.req_userid
        //         WHERE date_format(a.req_dateTime, '%Y-%m-%d 15:59') >= '$getDateTime'
        //         ORDER BY a.req_dateissued DESC
        //     ";

        //     $data = $this->gm->execute_query($this->sql, "Unauthorized User");

        //     if ($data['code'] == 200) {
        //         $this->payload = $data['data'];
        //         $value = array();

        //         $time = date('H:i');
        //         for($i=0; $i<count($this->payload); $i++) {
        //             $convertDate = strftime("%B %d, %Y",strtotime($data['data'][$i]['req_dateTime']));
        //             $convertDate = $convertDate.' '.$time;
        //             $requests = array(
        //                 'req_id' => $data['data'][$i]['req_id'],
        //                 'req_appointment_name' => $data['data'][$i]['req_appointment_name'],
        //                 'req_dateTime' => $convertDate,
        //                 'req_desc' => $data['data'][$i]['req_desc'],
        //                 'office_name' => $data['data'][$i]['office_name'],
        //                 'user_fname' => $data['data'][$i]['user_fname'],
        //                 'user_mname' => $data['data'][$i]['user_mname'],
        //                 'user_lname' => $data['data'][$i]['user_lname'],
        //                 'user_extname' => $data['data'][$i]['user_extname']
        //             );
        //             $value[] = $requests;
        //         }
        //         $this->status = $this->sucess_status;
        //         $this->code = 200;
        //     } 
        //     else {
        //         $value = null;   
        //     }

        //     return array(
        //         "payload" => $value
        //     );
        // }
        
        function selectAllRequest($filter_data){
            $getDateTime = date('Y-m-d H:i'); //get date today
            $this->sql = "
                SELECT * FROM tbl_request_appointment a
                JOIN tbl_office o
                ON a.req_officeid = o.office_id
                JOIN tbl_office_user ou 
                ON ou.office_user_id = o.office_userid 
                JOIN tbl_user u 
                ON u.user_id = a.req_userid
                WHERE date_format(a.req_dateTime, '%Y-%m-%d 15:59') >= '$getDateTime'
                ORDER BY a.req_dateissued DESC
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
                        'office_name' => $data['data'][$i]['office_name'],
                        'user_fname' => $data['data'][$i]['user_fname'],
                        'user_mname' => $data['data'][$i]['user_mname'],
                        'user_lname' => $data['data'][$i]['user_lname'],
                        'user_extname' => $data['data'][$i]['user_extname']
                    );
                    $value[] = $requests;
                }
                $this->status = $this->sucess_status;
                $this->code = 200;
            } 
            else {
                $value = null;   
            }

            return array(
                "payload" => $value
            );
        }

        /*********************************** OFFICE HEAD ****************************************/
        public function issuedRequest($dt){
            $is_issued = 1;
            $this->sql = "
                UPDATE tbl_req_appointment
                SET is_issued = '$is_issued'
                WHERE req_id = '$dt->req_id'
            ";
            $this->cn->query($this->sql);
        }

        /*********************************** ADMIN ****************************************/
        function getAdminRequest($filter_data){
            $dateToday = date("Y-m-d H:i:s");
            $this->sql = "
                SELECT * FROM tbl_req_appointment r
                JOIN tbl_user u 
                ON r.req_userid = u.user_id 
                JOIN tbl_set_appointment s 
                ON r.req_setid = s.set_id 
                JOIN tbl_office o 
                ON o.office_id = s.set_officeid
                JOIN tbl_office_user ou 
                ON o.office_userid = ou.office_user_id
                WHERE (s.StartTime + interval 7 hour) >= '$dateToday'
                AND s.is_requested = 1
                ORDER BY r.req_dateissued DESC
            ";

            if ($filter_data!=null) {
                $this->sql.= " AND req_id ='$filter_data'";
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
            
        function getRequestHistory($qrid){
            $dateToday = date("Y-m-d H:i:s");
            $this->sql = "
                SELECT * FROM tbl_req_appointment r
                JOIN tbl_set_appointment s 
                ON r.req_setid = s.set_id 
                JOIN tbl_user u 
                ON r.req_userid = u.user_id 
                JOIN tbl_office o 
                ON o.office_id = s.set_officeid 
                JOIN tbl_office_user ou 
                ON ou.office_user_id = o.office_userid
                WHERE (s.StartTime + interval 7 hour) <= '$dateToday'
                ORDER BY s.StartTime DESC
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

        function getRequestHistoryID($filter_data){
            $dateToday = date("Y-m-d H:i:s");
            $this->sql = "
                SELECT * FROM tbl_req_appointment r
                JOIN tbl_set_appointment s 
                ON r.req_setid = s.set_id 
                JOIN tbl_user u 
                ON r.req_userid = u.user_id 
                JOIN tbl_office o 
                ON o.office_id = s.set_officeid
                JOIN tbl_address a 
                ON a.addr_id = u.user_addrid
                JOIN tbl_office_user ou 
                ON ou.office_user_id = o.office_userid
                WHERE (s.StartTime + interval 7 hour) <= '$dateToday'
            ";

            if ($filter_data!=null) {
                $this->sql.= " AND r.req_id ='$filter_data'";
            }

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

        /*********************************** OFFICE USERS ****************************************/
        function getOfficeRequestID($id){
            $dateToday = date("Y-m-d H:i:s");
            $this->sql = "
                SELECT * FROM tbl_set_appointment s
                JOIN tbl_office o 
                ON s.set_officeid = o.office_id
                JOIN tbl_office_user ou
                ON ou.office_user_id = o.office_userid
                JOIN tbl_req_appointment r 
                ON r.req_setid = s.set_id 
                JOIN tbl_user u 
                ON u.user_id = r.req_userid
                WHERE o.office_userid = '$id' 
                AND s.is_requested = 1
                AND (s.StartTime + interval 7 hour) >= '$dateToday'
                AND r.is_issued = 0
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

        function getOfficeRequestHistoryID($id){
            $dateToday = date("Y-m-d H:i:s");
            $this->sql = "
                SELECT * FROM tbl_req_appointment r
                JOIN tbl_set_appointment s 
                ON r.req_setid = s.set_id 
                JOIN tbl_user u 
                ON r.req_userid = u.user_id 
                JOIN tbl_office o 
                ON o.office_id = s.set_officeid 
                JOIN tbl_office_user ou 
                ON ou.office_user_id = o.office_userid
                WHERE (s.StartTime + interval 7 hour) <= '$dateToday'
                AND o.office_userid = '$id' 
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
