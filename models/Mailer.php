<?php 
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    class Mailer {
        protected $gm;
        protected $cn;
        protected $payload;

        public function __construct(\PDO $pdo)
        {
            $this->cn = $pdo;
            $this->gm = new GlobalMethods($pdo);
        }

        //Mailer in Angular Project
        function AngularMailer($dt) {
            try {

                $mail = new PHPMailer(true);
                $mail->isSMTP();                                            // Send using SMTP
                $mail->Host       = 'smtp.gmail.com';                    	// Set the SMTP server to send through
                //   $mail->SMTPDebug  = 2;
                $mail->SMTPAuth   = true;                               	// Enable SMTP authentication
                $mail->Username   = 'jomel.mailer@gmail.com';                // SMTP username
                $mail->Password   = 'jomelMAILER';                 // SMTP password
                $mail->SMTPSecure = 'tls';   
                $mail->SMTPAutoTLS = false;
                //$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
                $mail->Port       = 587;          
                $mail->setFrom('jomel.mailer@gmail.com', 'from jomel mailer');
            
            
                //Recipients
                $email = $dt->user_email;
                $mail->addAddress($email);    // EMAIL ADD OF RECEIVER/RECIPIENTS // Add a recipient
                
               
        
                // Content
                $mail->isHTML(true);		// Set email format to HTML
        
                $mail->Subject = "This is a subject from jomel mailer";
                $mail->Body    = "This is a body from jomel mailer
                <a href='http://localhost:4200/verified/$email'>
                Verify Email</a>
                ";
        
                if($mail->send()) {
                    http_response_code(200);

                    $this->payload = array(
                        "status" => [
                            "remarks" => "success",
                            "message" => "Email has been sent."
                        ],
                        "prepared_by" => "Jomel Isidro",
                        "timestamp"=>date_create()
                    );

                } else {
                    http_response_code(500);

                    $this->payload = array(
                        "status" => [
                            "remarks" => "failed",
                            "message" => "Sending failed."
                        ],
                        "prepared_by" => "Jomel Isidro",
                        "timestamp"=>date_create()
                    );
                }
        
            } 
            catch (Exception $e) {
                http_response_code(500);

                $this->payload = array(
                    "status" => [
                        "remarks" => "failed",
                        "message" => $e->getMessage()
                    ],
                    "prepared_by" => "Jomel Isidro",
                    "timestamp"=>date_create()
                );
            }

            return $this->payload;
        }

        //Mailer in Ionic Project
        function IonicMailer($dt) {
            try {

                $mail = new PHPMailer(true);
                $mail->isSMTP();                                            // Send using SMTP
                $mail->Host       = 'smtp.gmail.com';                    	// Set the SMTP server to send through
                //   $mail->SMTPDebug  = 2;
                $mail->SMTPAuth   = true;                               	// Enable SMTP authentication
                $mail->Username   = 'jomel.mailer@gmail.com';                // SMTP username
                $mail->Password   = 'jomelMAILER';                 // SMTP password
                $mail->SMTPSecure = 'tls';   
                $mail->SMTPAutoTLS = false;
                //$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
                $mail->Port       = 587;          
                $mail->setFrom('jomel.mailer@gmail.com', 'from jomel mailer');
            
            
                //Recipients
                $email = $dt->user_email;
                $mail->addAddress($email);    // EMAIL ADD OF RECEIVER/RECIPIENTS // Add a recipient
                
               
        
                // Content
                $mail->isHTML(true);		// Set email format to HTML
        
                $mail->Subject = "This is a subject from jomel mailer";
                $mail->Body    = "This is a body from jomel mailer
                <a href='http://localhost:8100/verified/$email'>
                Verify Email</a>
                ";
        
                if($mail->send()) {
                    http_response_code(200);

                    $this->payload = array(
                        "status" => [
                            "remarks" => "success",
                            "message" => "Email has been sent."
                        ],
                        "prepared_by" => "Jomel Isidro",
                        "timestamp"=>date_create()
                    );

                } else {
                    http_response_code(500);

                    $this->payload = array(
                        "status" => [
                            "remarks" => "failed",
                            "message" => "Sending failed."
                        ],
                        "prepared_by" => "Jomel Isidro",
                        "timestamp"=>date_create()
                    );
                }
        
            } 
            catch (Exception $e) {
                http_response_code(500);

                $this->payload = array(
                    "status" => [
                        "remarks" => "failed",
                        "message" => "Sending failed."
                    ],
                    "prepared_by" => "Jomel Isidro",
                    "timestamp"=>date_create()
                );
            }

            return $this->payload;
        }

        //Mailer in Ionic Project
        function OfficeMailer($dt) {
            try {

                $mail = new PHPMailer(true);
                $mail->isSMTP();                                            // Send using SMTP
                $mail->Host       = 'smtp.gmail.com';                    	// Set the SMTP server to send through
                //   $mail->SMTPDebug  = 2;
                $mail->SMTPAuth   = true;                               	// Enable SMTP authentication
                $mail->Username   = 'jomel.mailer@gmail.com';                // SMTP username
                $mail->Password   = 'jomelMAILER';                 // SMTP password
                $mail->SMTPSecure = 'tls';   
                $mail->SMTPAutoTLS = false;
                //$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
                $mail->Port       = 587;          
                $mail->setFrom('jomel.mailer@gmail.com', 'from jomel mailer');
            
            
                //Recipients
                $email = $dt->user_email;
                $mail->addAddress($email);    // EMAIL ADD OF RECEIVER/RECIPIENTS // Add a recipient
                
                $subject = $dt->subject;
        
                // Content
                $mail->isHTML(true);		// Set email format to HTML
        
                $mail->Subject = "GC Scheduling";
                $mail->Body    = "New appointment requested for ".$subject;
        
                if($mail->send()) {
                    http_response_code(200);

                    $this->payload = array(
                        "status" => [
                            "remarks" => "success",
                            "message" => "Email has been sent."
                        ],
                        "prepared_by" => "Jomel Isidro",
                        "timestamp"=>date_create()
                    );

                } else {
                    http_response_code(500);

                    $this->payload = array(
                        "status" => [
                            "remarks" => "failed",
                            "message" => "Sending failed."
                        ],
                        "prepared_by" => "Jomel Isidro",
                        "timestamp"=>date_create()
                    );
                }
        
            } 
            catch (Exception $e) {
                http_response_code(500);

                $this->payload = array(
                    "status" => [
                        "remarks" => "failed",
                        "message" => $e->getMessage()
                    ],
                    "prepared_by" => "Jomel Isidro",
                    "timestamp"=>date_create()
                );
            }

            return $this->payload;
        }

    }
?>