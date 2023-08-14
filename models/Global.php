<?php
    class GlobalMethods{
        protected $cn;
		
        public function __construct(\PDO $pdo) {
			$this->cn = $pdo;
        }
        
        public function execute_query($sql, $err) {
			$errmsg="";
			$code=0;
			$data = array();
			try {
				if($result = $this->cn->query($sql)->fetchAll()){
					foreach($result as $record) {
						array_push($data, $record);
					}
					$result = null;
					$code = 200;
					return array("code"=>$code,
								 "data"=>$data);
				} else {
					$this->errmsg = $err;
					$this->code = 401;
				}
			} catch (\PDOException $e) {
				$errmsg = $e->getMessage();
				$code = 403;
			}

			return array("code"=>$code, "errmsg"=>"Error: ".$errmsg);
		}

		public function api_result($remarks, $payload, $code){
			http_response_code($code); 
			$status = array(
				"status"=>$remarks
			);
			return array(
                "status"=>$status,
				"payload"=>$payload,
				"preparedBy"=>"Jomel Isidro",
				"timestamp"=>date_create());
		}
    }
?>