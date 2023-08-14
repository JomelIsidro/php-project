<?php
    class Offices
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

        /***************************** ADMIN  ****************************/
        //get list of offices
        function getOffices($filter_data){
            $this->sql = "
                SELECT * FROM tbl_offices 
            ";

            if ($filter_data!=null) {
                $this->sql.= " WHERE offices_id ='$filter_data'";
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

        //add offices
        public function addOffices($dt){
            $this->sql = "
                INSERT INTO tbl_offices(offices_name) 
                VALUES ('$dt->offices_name')
            ";
            $this->cn->query($this->sql);
        }

       
     

    } //class
?>
