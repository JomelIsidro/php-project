<?php
    class Get
    {
        protected $gm;
        protected $status;
        protected $payload;
        protected $sql;
        protected $code;

        private $sucess_status = "Successfully retrieved requested records";
        private $failed_status = "Failed to retrieve records";

        public function __construct(\PDO $pdo)
        {
            $this->gm = new GlobalMethods($pdo);
        }


        /*****************************  USER ****************************/ 
        //select user request pending
        function selectRequestUserPending($filter_data){
            $datenow = date("Y-m-d H:m:s");
            $this->sql = "
                SELECT * FROM tbl_request
                WHERE StartTime >= '$datenow'
            ";

            if ($filter_data!=null) {
                $this->sql.= " AND req_userid ='$filter_data'";
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

        //select user history
        function selectUserHistory($filter_data){
            $datenow = date("Y-m-d H:m:s");
            $this->sql = "
                SELECT * FROM tbl_request
                WHERE req_status = 1
                AND StartTime < '$datenow'
            ";

            if ($filter_data!=null) {
                $this->sql.= " AND req_userid ='$filter_data'";
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

        //select all admin
        // function selectUserAdmin($filter_data){
        //     $this->sql = "
        //         SELECT * FROM `tbl_user` u
        //         JOIN tbl_account a
        //         ON u.user_id = acc_userid
        //         WHERE acc_role = 1
        //     ";

        //     if ($filter_data!=null) {
        //         $this->sql.= " WHERE user_id = '$filter_data' ";
        //     }
        //     $this->code = 403;

        //     $data = $this->gm->execute_query($this->sql, "Unauthorized User");

        //     if ($data['code'] == 200) {
        //         $this->payload = $data['data'];
        //         $this->status = $this->sucess_status;
        //         $this->code = 200;
        //     } else {
        //         $this->payload = null;
        //         $this->status = $this->failed_status;
        //         $this->code = 403;
        //     }

        //     return $this->gm->api_result($this->status, $this->payload, $this->code);
        // }

        function selectUserAdmin($filter_data){
            $this->sql = "
                SELECT * FROM `tbl_office_user` ou
                JOIN tbl_account a
                ON ou.office_user_id = acc_userid
                WHERE acc_role = 1
            ";

            if ($filter_data!=null) {
                $this->sql.= " WHERE user_id = '$filter_data' ";
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

        //select department or office to place in select option in creating appointment
        function selectDepartment($filter_data){
            $this->sql = "
                SELECT * FROM tbl_department
            ";

            if ($filter_data!=null) {
                $this->sql.= " AND dept_id ='$filter_data'";
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

        //select to fill up assessment
        function fillupAssessment($filter_data){
            $datenow = date("Y-m-d");
            $this->sql = "
                SELECT * FROM `tbl_assessment` a 
                INNER JOIN tbl_user u ON a.user_id = u.user_id 
                WHERE DATE(assessment_issued) = '$datenow'
                AND a.user_id = '$filter_data'
                LIMIT 1
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

            return $this->gm->api_result($this->status, $this->payload, $this->code);
        }

        //select to fill up request
        function fillupRequest($filter_data){
            $datenow = date("Y-m-d");
            $this->sql = "
                SELECT * FROM `tbl_request`  
                WHERE req_userid = '$filter_data'
                AND DATE(req_dateissued) = '$datenow'
                LIMIT 1
            ";

            $this->code = 403;

            $data = $this->gm->execute_query($this->sql, "Unauthorized User");

            if ($data['code'] == 200) {
                $this->payload = $data['data'];
                $this->status = $this->sucess_status;
                $this->code = 200;
            } 
            else {
                $this->payload = null;   
            }
            // else {
            //     $this->payload = null;
            //     $this->status = $this->failed_status;
            //     $this->code = 403;
            // }

            return array(
                "payload" => $this->payload
            );

            //return $this->gm->api_result($this->status, $this->payload, $this->code);
        }

        //select to fill up request in add appointment
        function fillupRequest2($filter_data){
            $datenow = date("Y-m-d");
            $this->sql = "
                SELECT * FROM `tbl_assessment`  
                WHERE user_id = '$filter_data'
                AND DATE(assessment_issued) = '$datenow'
                LIMIT 1
            ";

            $this->code = 403;

            $data = $this->gm->execute_query($this->sql, "Unauthorized User");

            if ($data['code'] == 200) {
                $this->payload = $data['data'][0];
                $this->status = $this->sucess_status;
                $this->code = 200;
            } 
            else {
                $this->payload = null;   
            }
            // else {
            //     $this->payload = null;
            //     $this->status = $this->failed_status;
            //     $this->code = 403;
            // }

            return array(
                "payload" => $this->payload
            );
            //return $this->gm->api_result($this->status, $this->payload, $this->code);
        }

        //select appointment of user by ID
        function selectAppointmentID($filter_data){
            $datenow = date("Y-m-d H:m:s");
            $this->sql = "
                SELECT * FROM tbl_request r
                JOIN tbl_user u ON u.user_id = r.req_userid
                JOIN tbl_address a ON a.addr_id = u.user_addrid
                WHERE StartTime >=  '$datenow' 
            ";

            if ($filter_data!=null) {
                $this->sql.= " AND req_userid ='$filter_data' AND req_status != 3 ORDER BY req_id DESC";
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

        //cannot approved the request if the status is cancelled
        function selectDisapprovedRequestID($filter_data){
            $this->sql = "
                SELECT * FROM tbl_request 
                WHERE req_id = '$filter_data'  
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

            return $this->gm->api_result($this->status, $this->payload, $this->code);
        }


         /*****************************  ADMIN ****************************/ 
        //select from table request
        function selectRequest($filter_data){
            $this->sql = "
                SELECT * FROM tbl_request
                WHERE req_status = 1
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

            return $this->payload;
            //return $this->gm->api_result($this->status, $this->payload, $this->code);
        }

        //select list of upcoming events //change the static date to datenow
        function selectAppointment($filter_data){
            $datenow = date("Y-m-d H:m:s");
            $this->sql = "
                SELECT * FROM tbl_request r
                JOIN tbl_user u ON u.user_id = r.req_userid
                JOIN tbl_address a ON a.addr_id = u.user_addrid
                WHERE StartTime >=  '$datenow'  
                AND req_status = 1  
            ";

            if ($filter_data!=null) {
                $this->sql.= " AND req_userid ='$filter_data'  ORDER BY req_id DESC";
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


        //select list of history events //change the static date to datenow
        function selectHistory($filter_data){
            $datenow = date("Y-m-d H:m:s");
            $this->sql = "
                SELECT * FROM tbl_request r
                JOIN tbl_user u ON u.user_id = r.req_userid
                JOIN tbl_address a ON a.addr_id = u.user_addrid
                JOIN tbl_tracing t ON t.tracing_reqid = r.req_id
                WHERE req_status = 1 
                AND StartTime < '$datenow' 
            ";

            if ($filter_data!=null) {
                $this->sql.= " AND req_userid ='$filter_data'";
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

        //select from tbl request where status is 0
        function selectRequestPending($filter_data){
            $this->sql = "
                SELECT * FROM tbl_request r
                JOIN tbl_user u ON u.user_id = r.req_userid
                JOIN tbl_address a ON a.addr_id = u.user_addrid
                WHERE req_status != 1 
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

        //select null to display no in request admin page
        function selectNullRequest($filter_data){
            $this->sql = "
                SELECT * FROM tbl_request 
                WHERE req_id = 0
            ";

            if ($filter_data!=null) {
                $this->sql.= " WHERE event_id ='$filter_data'";
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

        //select all users except admin itself
        function selectUsers($filter_data){
            $this->sql = "
                SELECT * FROM tbl_user
            ";

            if ($filter_data!=null) {
                $this->sql.= " WHERE user_id != '$filter_data' ";
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

        //select list of history for contact tracing
        function selectContactTracing($filter_data){
            $this->sql = "
                SELECT * FROM tbl_tracing t
                JOIN tbl_request r ON t.tracing_reqid = r.req_id
                JOIN tbl_user u ON u.user_id = r.req_userid
                JOIN tbl_address a ON a.addr_id = u.user_addrid
            ";

            if ($filter_data!=null) {
                $this->sql.= " WHERE req_id ='$filter_data'";
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


         //cannot approved the request if the status is cancelled
        function selectCancelledRequest($filter_data){
            $this->sql = "
                SELECT * FROM tbl_request 
                WHERE req_id = '$filter_data'  
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

            return $this->gm->api_result($this->status, $this->payload, $this->code);
        }

         //select list of department
        function selectAdminDepartment($filter_data){
            $this->sql = "
                SELECT * FROM tbl_department
            ";

            if ($filter_data!=null) {
                $this->sql.= " WHERE dept_id ='$filter_data'";
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


        /*****************************  ADMIN OR USERS ****************************/ 
        //select message
        function selectMessages($from_userid, $to_userid){
            $this->sql = "
                SELECT * FROM tbl_message 
                WHERE (message_fromuserid = $from_userid 
                AND message_touserid = $to_userid) 
                OR (message_fromuserid = $to_userid
                AND message_touserid = $from_userid) 
                ORDER BY message_issued ASC
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

            return $this->gm->api_result($this->status, $this->payload, $this->code);
        }

        //select user history
        function selectAddress($filter_data){
            $this->sql = "
                SELECT * FROM tbl_user
                WHERE user_id = '$filter_data'
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

            return $this->gm->api_result($this->status, $this->payload, $this->code);
        }

        //select from tbl request to update
        function selectRequestUpdate($filter_data){
            $this->sql = "
                SELECT * FROM tbl_request r
                JOIN tbl_department d
                ON r.req_deptid = d.dept_id
                WHERE req_id = '$filter_data'
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

            return $this->gm->api_result($this->status, $this->payload, $this->code);
        }

         //select email if not exist, registered email
         function selectEmail($filter_data){
            $email = $filter_data; 

            $this->sql = "
                SELECT user_email FROM tbl_user 
                WHERE user_email = '$email' LIMIT 1
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


    } // Class Get.php

?>
