<?php
    class Post
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


        /*****************************  USER ****************************/ 
        //update status and reason request
        public function cancelRequest($dt){
            $this->sql = "
                UPDATE tbl_request 
                SET req_status = '3', req_reason = '$dt->req_reason'
                WHERE req_id = '$dt->req_id'
            ";
            $this->cn->query($this->sql);
        }

        //update request
        public function updateRequest($dt){
            $datenow = date("Y-m-d H:m:s");
            $this->sql = "
                UPDATE tbl_request 
                SET Subject = '$dt->Subject', req_concern = '$dt->req_concern', req_status = '2', 
                StartTime = '$dt->StartTime', EndTime = '$dt->EndTime', req_deptid = '$dt->req_deptid', req_reason = '$dt->req_reason'
                WHERE req_id = '$dt->req_id'
            ";
            $this->cn->query($this->sql);
        }

        //update tbl_user - verified user
        public function verified_user($dt){
            $this->sql = "
                UPDATE tbl_user
                SET user_is_verified = 1
                WHERE user_email = '$dt->user_email'
            ";
            $this->cn->query($this->sql);
        }

        //insert assessment
        public function insertAssessment($dt){
            
            $this->sql = "
                INSERT INTO tbl_assessment(status, user_id) 
                VALUES ('$dt->status','$dt->user_id')
            ";
            $this->cn->query($this->sql);
        }


         /*****************************  ADMIN ****************************/ 
        //update request
        public function approvedRequestStatus($dt){
            $datenow = date("Y-m-d H:m:s");
            $this->sql = "
                UPDATE tbl_request 
                SET req_status = 1
                WHERE req_id='$dt->req_id'
                AND StartTime >=  '$datenow'
            ";
            $this->cn->query($this->sql);
        }

        //update request
        public function disapprovedRequestStatus($dt){
            $datenow = date("Y-m-d H:m:s");
            $this->sql = "
                UPDATE tbl_request 
                SET req_status = 4, req_reason = '$dt->req_reason'
                WHERE req_id='$dt->req_id'
                AND StartTime >=  '$datenow'
            ";
            $this->cn->query($this->sql);
        }

        //insert tracing qrcode
        public function insertQR($dtqr){
            $this->code = 403;

            $this->sql = "
                SELECT req_id
                FROM tbl_request
                WHERE req_id = '$dtqr->tracing_reqid' LIMIT 1
            ";

            $dt = $this->gm->execute_query($this->sql, "Unauthorized User");

            if ($dt['code'] == 200) {
                $this->payload = $dt['data'];
                $this->status = $this->sucess_status;
                $this->code = 200;

                $this->sql = "
                    INSERT INTO tbl_tracing(tracing_reqid, tracing_temp) 
                    VALUES ('$dtqr->tracing_reqid', '$dtqr->tracing_temp')
                ";
                $this->cn->query($this->sql);
            } else {
                $this->payload = null;
                $this->status = $this->failed_status;
                $this->code = 403;
            }

            return $this->gm->api_result($this->status, $this->payload, $this->code);
        }

        //delete request 
        public function deleteRequest($dt){
            $datenow = date("Y-m-d H:m:s");
            $this->sql = "
                DELETE FROM tbl_request
                WHERE req_id = '$dt->req_id'
                AND StartTime < '$datenow'
            ";
            $this->cn->query($this->sql);
        }

        //update department
        public function updateDepartment($dt){
            $this->sql = "
                UPDATE tbl_department 
                SET dept_name = '$dt->dept_name', dept_head = '$dt->dept_head'
                WHERE dept_id = '$dt->dept_id'
            ";
            $this->cn->query($this->sql);
        }

        //delete department 
        public function deleteDepartment($dt){
            $this->sql = "
                DELETE FROM tbl_department
                WHERE dept_id = '$dt->dept_id'
            ";
            $this->cn->query($this->sql);
        }

        //insert department
        public function insertDepartment($dt){
            $this->sql = "
                INSERT INTO tbl_department(dept_name, dept_head) 
                VALUES ('$dt->dept_name','$dt->dept_head')
            ";
            $this->cn->query($this->sql);
        }

         /*****************************  ADMIN OR USER ****************************/ 
        //insert message
        public function insertMessage($dt){
            $status = 0; //it means message is not seen
            $this->sql = "
                INSERT INTO tbl_message(message, message_fromuserid, message_touserid, message_seen) 
                VALUES ('$dt->message','$dt->from_userid','$dt->to_userid','$status')
            ";
            $this->cn->query($this->sql);
        }

        //insert request
        public function insertRequest($dt){
            $status = 0; //it means request is not yet approved

            if($dt->user_addrid == 0) {
                $sql2 = "
                    INSERT INTO tbl_address(addr_housenumber, addr_street, addr_brgy, addr_city, addr_province) 
                    VALUES ('$dt->addr_housenumber','$dt->addr_street','$dt->addr_brgy','$dt->addr_city', '$dt->addr_province')
                ";
                $this->cn->query($sql2);

                $this->sql = "SELECT addr_id FROM `tbl_address` ORDER BY addr_id DESC LIMIT 1 ";
                $data = $this->gm->execute_query($this->sql, "Unauthorized User");
                $user_addrid = $data['data'][0]['addr_id']; 

                $sql3 = "
                    UPDATE tbl_user 
                    SET user_addrid = $user_addrid
                    WHERE user_id = $dt->req_userid
                ";
                $this->cn->query($sql3);

                $this->sql = "
                    INSERT INTO tbl_request(Subject, req_concern, StartTime, EndTime, req_status, req_userid, req_deptid, req_concern_person) 
                    VALUES ('$dt->Subject','$dt->req_concern','$dt->StartTime','$dt->EndTime', '$status', '$dt->req_userid', '$dt->req_deptid', '$dt->req_concern_person')
                ";
                $this->cn->query($this->sql);
            }
            if($dt->user_addrid >= 1) {
                $this->sql = "
                    INSERT INTO tbl_request(Subject, req_concern, StartTime, EndTime, req_status, req_userid, req_deptid, req_concern_person) 
                    VALUES ('$dt->Subject','$dt->req_concern','$dt->StartTime','$dt->EndTime', '$status', '$dt->req_userid', '$dt->req_deptid', '$dt->req_concern_person')
                ";
                $this->cn->query($this->sql);
            }
            
        }
        
        


    } //class
?>
