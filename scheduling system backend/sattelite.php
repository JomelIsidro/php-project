<?php
    require_once("./config/Database.php");
    require_once("./models/Auth.php");
    require_once("./models/Post.php");
    require_once("./models/Global.php");
    require_once("./models/Get.php");
    require_once("./models/Mailer.php");
    require_once("./models/Appointment.php");
    require_once("./models/Qrcode.php");
    require_once("./models/Assessment.php");
    require_once("./models/Visitor.php");
    require_once("./models/Office.php");
    require_once("./models/OfficeUser.php");
    require_once("./models/Request.php");
    require_once("./models/History.php");
    require_once("./models/Account.php");
    require_once('./vendor/autoload.php');

    require_once("./models/Offices.php");

    $auth = new Auth($pdo);
    $post = new Post($pdo);
    $get = new Get($pdo);
    $mail = new Mailer($pdo);
    $appointment = new Appointment($pdo);
    $assessment = new Assessment($pdo);
    $qrcode = new Qrcode($pdo);
    $visitor = new Visitor($pdo);
    $office = new Office($pdo);
    $office_user = new OfficeUser($pdo);
    $request = new Request($pdo);
    $history = new History($pdo);
    $account = new Account($pdo);

    $offices = new Offices($pdo);

    if(isset($_REQUEST['request'])){
        $req=explode('/', rtrim($_REQUEST['request'], '/'));
    }
    else{
        $req = array("errorcatcher");
    }

    $authHeader = "";
    $authUser = "";
    $headers= apache_request_headers();
    foreach($headers as $header=>$value){
        if($header == "Authorization"){
            $authHeader = $value;
        }
        if($header == "X-Auth-User"){
            $authUser = $value;
        }
    }

    switch($_SERVER['REQUEST_METHOD']){
        case 'POST':
            switch($req[0]) {

                /***************************** Class VISITOR ASSESSMENT   ****************************/
                case 'checkRequest':
                    if(count($req)>1){
                        echo json_encode($assessment->checkRequest($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($assessment->checkRequest(null), JSON_PRETTY_PRINT);
                    }
                break;

                case 'checkTodayAssessment':
                    if(count($req)>1){
                        echo json_encode($assessment->checkTodayAssessment($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($assessment->checkTodayAssessment(null), JSON_PRETTY_PRINT);
                    }
                break;

                case 'checkTodayRequest':
                    if(count($req)>1){
                        echo json_encode($assessment->checkTodayRequest($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($assessment->checkTodayRequest(null), JSON_PRETTY_PRINT);
                    }
                break;

                case 'checkFutureRequest':
                    if(count($req)>1){
                        echo json_encode($assessment->checkFutureRequest($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($assessment->checkFutureRequest(null), JSON_PRETTY_PRINT);
                    }
                break;
            

                /***************************** Class VISITOR QRCODE   ****************************/
                case 'selectQRCode':
                    if(count($req)>1){
                        echo json_encode($qrcode->selectQRCode($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($qrcode->selectQRCode(null), JSON_PRETTY_PRINT);
                    }
                break;

                /***************************** Class OFFICE HISTORY   ****************************/
                case 'getVisitorHistoryID':
                    if(count($req)>1){
                        echo json_encode($history->getVisitorHistoryID($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($history->getVisitorHistoryID(null), JSON_PRETTY_PRINT);
                    }
                break;


                /***************************** Class OFFICE HISTORY   ****************************/
                case 'getOfficeArrivedHistory':
                    if(count($req)>1){
                        echo json_encode($history->getOfficeArrivedHistory($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($history->getOfficeArrivedHistory(null), JSON_PRETTY_PRINT);
                    }
                break;

                case 'getOfficeNotArrivedHistory':
                    if(count($req)>1){
                        echo json_encode($history->getOfficeNotArrivedHistory($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($history->getOfficeNotArrivedHistory(null), JSON_PRETTY_PRINT);
                    }
                break;

                /***************************** Class ADMIN HISTORY   ****************************/
                case 'getAdminArrivedHistory':
                    if(count($req)>1){
                        echo json_encode($history->getAdminArrivedHistory($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($history->getAdminArrivedHistory(null), JSON_PRETTY_PRINT);
                    }
                break;

                case 'getAdminNotArrivedHistory':
                    if(count($req)>1){
                        echo json_encode($history->getAdminNotArrivedHistory($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($history->getAdminNotArrivedHistory(null), JSON_PRETTY_PRINT);
                    }
                break;

                /***************************** Class ADMIN AND OFFICE HISTORY   ****************************/
                case 'viewArrivedHistoryID':
                    if(count($req)>1){
                        echo json_encode($history->viewArrivedHistoryID($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($history->viewArrivedHistoryID(null), JSON_PRETTY_PRINT);
                    }
                break;

                case 'viewNotArrivedHistoryID':
                    if(count($req)>1){
                        echo json_encode($history->viewNotArrivedHistoryID($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($history->viewNotArrivedHistoryID(null), JSON_PRETTY_PRINT);
                    }
                break;
    

                /***************************** Class VISITOR   ****************************/
                //this is the validation of holidays in requesting an appointment
                case 'validateHolidays':
                    if(count($req)>1){
                        echo json_encode($appointment->validateHolidays($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($appointment->validateHolidays(null), JSON_PRETTY_PRINT);
                    }
                break;

                /***************************** Class ADMIN   ****************************/
                case 'selectHolidays':
                    if(count($req)>1){
                        echo json_encode($appointment->selectHolidays($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($appointment->selectHolidays(null), JSON_PRETTY_PRINT);
                    }
                break;

                case 'addHolidays':
                    $d = json_decode(file_get_contents("php://input"));
                    $appointment->addHolidays($d);
                    echo json_encode("New Occasion Added!", JSON_PRETTY_PRINT);
                break;


                /***************************** Class VISITOR   ****************************/
                case 'approvedChangeRequest':
                    $d = json_decode(file_get_contents("php://input"));
                    $visitor->approvedChangeRequest($d);
                    echo json_encode("Successfully Approved Changes!", JSON_PRETTY_PRINT);
                break;


                /***************************** Class OFFICE  ****************************/
                case 'selectOfficeRequestReason':
                    if(count($req)>1){
                        echo json_encode($request->selectOfficeRequestReason($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($request->selectOfficeRequestReason(null), JSON_PRETTY_PRINT);
                    }
                break;
                
                
                case 'changeRequest':
                    $d = json_decode(file_get_contents("php://input"));
                    $request->changeRequest($d);
                    echo json_encode("Reason has been changed!", JSON_PRETTY_PRINT);
                break;

                case 'approveRequest':
                    $d = json_decode(file_get_contents("php://input"));
                    $request->approveRequest($d);
                    echo json_encode("Request has been approved!", JSON_PRETTY_PRINT);
                break;

                case 'selectAppointmentOfOfficeID':
                    if(count($req)>1){
                        echo json_encode($office->selectAppointmentOfOfficeID($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($office->selectAppointmentOfOfficeID(null), JSON_PRETTY_PRINT);
                    }
                break;

                case 'selectOfficeAllRequestID':
                    if(count($req)>1){
                        echo json_encode($request->selectOfficeAllRequestID($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($request->selectOfficeAllRequestID(null), JSON_PRETTY_PRINT);
                    }
                break;

                case 'selectOfficeTodayRequestID':
                    if(count($req)>1){
                        echo json_encode($request->selectOfficeTodayRequestID($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($request->selectOfficeTodayRequestID(null), JSON_PRETTY_PRINT);
                    }
                break;


                /***************************** Class ADMIN REQUEST  ****************************/
                case 'selectAllRequest':
                    if(count($req)>1){
                        echo json_encode($request->selectAllRequest($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($request->selectAllRequest(null), JSON_PRETTY_PRINT);
                    }
                break;

                case 'selectAdminTodayRequest':
                    if(count($req)>1){
                        echo json_encode($request->selectAdminTodayRequest($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($request->selectAdminTodayRequest(null), JSON_PRETTY_PRINT);
                    }
                break;


                /***************************** Class VISITOR Appointment  ****************************/
                case 'selectAllRequestAppointmentID':
                    if(count($req)>1){
                        echo json_encode($appointment->selectAllRequestAppointmentID($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($appointment->selectAllRequestAppointmentID(null), JSON_PRETTY_PRINT);
                    }
                break;
                
                case 'selectTodayRequestAppointmentID':
                    if(count($req)>1){
                        echo json_encode($appointment->selectTodayRequestAppointmentID($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($appointment->selectTodayRequestAppointmentID(null), JSON_PRETTY_PRINT);
                    }
                break;

                case 'selectAppointmentID':
                    if(count($req)>1){
                        echo json_encode($appointment->selectAppointmentID($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($appointment->selectAppointmentID(null), JSON_PRETTY_PRINT);
                    }
                break;

                case 'addRequestAppointment':
                    $d = json_decode(file_get_contents("php://input"));
                    $appointment->addRequestAppointment($d);
                    echo json_encode("Request Submitted!", JSON_PRETTY_PRINT);
                break;

                case 'countTheTotalOfRequest':
                    if(count($req)>1){
                        echo json_encode($appointment->countTheTotalOfRequest($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($appointment->countTheTotalOfRequest(null), JSON_PRETTY_PRINT);
                    }
                break;
                

                /***************************** Class ADMIN Appointment  ****************************/
                case 'updateLimitOfVisitor':
                    $d = json_decode(file_get_contents("php://input"));
                    $appointment->updateLimitOfVisitor($d);
                    echo json_encode("Updated!", JSON_PRETTY_PRINT);
                break;

                case 'getLimitOfVisitor':
                    if(count($req)>1){
                        echo json_encode($appointment->getLimitOfVisitor($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($appointment->getLimitOfVisitor(null), JSON_PRETTY_PRINT);
                    }
                break;

                case 'getAppointmentWithOffice':
                    if(count($req)>1){
                        echo json_encode($appointment->getAppointmentWithOffice($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($appointment->getAppointmentWithOffice(null), JSON_PRETTY_PRINT);
                    }
                break;

                case 'addAppointmentWithOffice':
                    $d = json_decode(file_get_contents("php://input"));
                    $appointment->addAppointmentWithOffice($d);
                    echo json_encode("Inserted Successfully!", JSON_PRETTY_PRINT);
                break;





                /***************************** Class ADMIN Offices  ****************************/
                case 'getOffices':
                    if(count($req)>1){
                        echo json_encode($offices->getOffices($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($offices->getOffices(null), JSON_PRETTY_PRINT);
                    }
                break;

                case 'addOffices':
                    $d = json_decode(file_get_contents("php://input"));
                    $offices->addOffices($d);
                    echo json_encode("Inserted Successfully!", JSON_PRETTY_PRINT);
                break;


                /***************************** Class Account  ****************************/
                case 'updateAdminOfficeAccount':
                    $d = json_decode(file_get_contents("php://input"));
                    $account->updateAdminOfficeAccount($d);
                    echo json_encode("Updated Successfully!", JSON_PRETTY_PRINT);
                break;

                case 'getOfficeUserAccountID':
                    if(count($req)>1){
                        echo json_encode($account->getOfficeUserAccountID($req[1]), JSON_PRETTY_PRINT);
                    }
                break;

                case 'changeAdminOfficePassword':
                    $d = json_decode(file_get_contents("php://input"));
                    echo json_encode($account->changeAdminOfficePassword($d));
                break;

                /***************************** Class Office Mailer  ****************************/
                case 'officeMailer':
                    $dt = json_decode(file_get_contents("php://input"));
                    echo json_encode($mail->OfficeMailer($dt));
                break;


                /***************************** Class Office Request ****************************/
                case 'getOfficeRequestID':
                    if(count($req)>1){
                        echo json_encode($request->getOfficeRequestID($req[1]), JSON_PRETTY_PRINT);
                    }
                break;

                case 'getOfficeRequestHistoryID':
                    if(count($req)>1){
                        echo json_encode($request->getOfficeRequestHistoryID($req[1]), JSON_PRETTY_PRINT);
                    }
                break;

                case 'issuedRequest':
                    $d = json_decode(file_get_contents("php://input"));
                    $request->issuedRequest($d);
                    echo json_encode("Request has been Issued!", JSON_PRETTY_PRINT);
                break;



                /***************************** Class Office User Appointment ****************************/
                case 'getOfficeAppointmentID':
                    if(count($req)>1){
                        echo json_encode($appointment->getOfficeAppointmentID($req[1]), JSON_PRETTY_PRINT);
                    } 
                break;

                 /***************************** Class Office User ****************************/
                 case 'getOfficeUser':
                    if(count($req)>1){
                        echo json_encode($office_user->getOfficeUser($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($office_user->getOfficeUser(null), JSON_PRETTY_PRINT);
                    }
                break;

                case 'addOfficeUser':
                    $d = json_decode(file_get_contents("php://input"));
                    //echo json_encode($office_user->addOfficeUser($d));
                    $office_user->addOfficeUser($d);
                    echo json_encode("Inserted Successfully!", JSON_PRETTY_PRINT);
                break;

                /***************************** Class Request ****************************/
                case 'getAdminRequest':
                    if(count($req)>1){
                        echo json_encode($request->getAdminRequest($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($request->getAdminRequest(null), JSON_PRETTY_PRINT);
                    }
                break;

                case 'getRequestHistory':
                    if(count($req)>1){
                        echo json_encode($request->getRequestHistory($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($request->getRequestHistory(null), JSON_PRETTY_PRINT);
                    }
                break;

                case 'getRequestHistoryID':
                    if(count($req)>1){
                        echo json_encode($request->getRequestHistoryID($req[1]), JSON_PRETTY_PRINT);
                    }
                break;

                /***************************** Class Office ****************************/
                case 'getOffice':
                    if(count($req)>1){
                        echo json_encode($office->getOffice($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($office->getOffice(null), JSON_PRETTY_PRINT);
                    }
                break;

                case 'getOfficeUserID':
                    if(count($req)>1){
                        echo json_encode($office->getOfficeUserID($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($office->getOfficeUserID(null), JSON_PRETTY_PRINT);
                    }
                break;

                case 'addOffice':
                    $d = json_decode(file_get_contents("php://input"));
                    $office->addOffice($d);
                    echo json_encode("Inserted Successfully!", JSON_PRETTY_PRINT);
                break;

                case 'updateOffice':
                    $d = json_decode(file_get_contents("php://input"));
                    $office->updateOffice($d);
                    echo json_encode("Updated Successfully!", JSON_PRETTY_PRINT);
                break;

                /***************************** Class Visitor ****************************/
                case 'getTodayVisitors':
                    if(count($req)>1){
                        echo json_encode($visitor->getTodayVisitors($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($visitor->getTodayVisitors(null), JSON_PRETTY_PRINT);
                    }
                break;

                case 'getTodayArrivedVisitors':
                    if(count($req)>1){
                        echo json_encode($visitor->getTodayArrivedVisitors($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($visitor->getTodayArrivedVisitors(null), JSON_PRETTY_PRINT);
                    }
                break;

                case 'getVisitorID':
                    if(count($req)>1){
                        echo json_encode($visitor->getVisitorID($req[1]), JSON_PRETTY_PRINT);
                    }
                break;

                /***************************** Class Assessment ****************************/
                case 'assessmentID':
                    if(count($req)>1){
                        echo json_encode($assessment->getAssessmentID($req[1]), JSON_PRETTY_PRINT);
                    }
                break;

                case 'dontDisplayAssessment':
                    if(count($req)>1){
                        echo json_encode($assessment->dontDisplayAssessment($req[1]), JSON_PRETTY_PRINT);
                    }
                break;

                case 'displayAssessment':
                    if(count($req)>1){
                        echo json_encode($assessment->displayAssessment($req[1]), JSON_PRETTY_PRINT);
                    }
                break;

                case 'noRequest':
                    if(count($req)>1){
                        echo json_encode($assessment->noRequest($req[1]), JSON_PRETTY_PRINT);
                    }
                break;

                /***************************** Class Qrcode ****************************/
                case 'getQrID':
                    if(count($req)>1){
                        echo json_encode($qrcode->getQrID($req[1]), JSON_PRETTY_PRINT);
                    }
                break;

                case 'getQrCode':
                    if(count($req)>1){
                        echo json_encode($qrcode->getQrCode($req[1]), JSON_PRETTY_PRINT);
                    }else {
                        echo json_encode($qrcode->getQrCode(null), JSON_PRETTY_PRINT);
                    }
                break;

                case 'scanQrCode':
					$dt = json_decode(file_get_contents("php://input"));
                    $qrcode->scanQrCode($dt);
                    echo json_encode("Updated Successfully!", JSON_PRETTY_PRINT);
                break;

                /***************************** Class Admin Appointment ****************************/
                case 'countAppointment':
                    if(count($req)>1){
                        echo json_encode($appointment->countAppointment($req[1]), JSON_PRETTY_PRINT);
                    } 
                break;

                case 'getAdminAppointment':
                    if(count($req)>1){
                        echo json_encode($appointment->getAdminAppointment($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($appointment->getAdminAppointment(null), JSON_PRETTY_PRINT);
                    }
                break;

                case 'addAdminAppointment':
                    $d = json_decode(file_get_contents("php://input"));
                    $appointment->addAdminAppointment($d);
                    echo json_encode("Inserted Successfully!", JSON_PRETTY_PRINT);
                break;

                case 'updateAdminAppointment':
                    $d = json_decode(file_get_contents("php://input"));
                    $appointment->updateAdminAppointment($d);
                    echo json_encode("Updated Successfully!", JSON_PRETTY_PRINT);
                break;

                /***************************** Class User Appointment ****************************/
                case 'getAppointmentID':
                    if(count($req)>1){
                        echo json_encode($appointment->getAppointmentID($req[1]), JSON_PRETTY_PRINT);
                    } 
                break;

                case 'insertAppointment':
                    $d = json_decode(file_get_contents("php://input"));
                    $appointment->insertAppointment($d);
                    echo json_encode("Inserted Successfully!", JSON_PRETTY_PRINT);
                break;

                case 'getAppointmentIfExist':
                    if(count($req)>1){
                        echo json_encode($appointment->getAppointmentIfExist($req[1], $req[2]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($appointment->getAppointmentIfExist(null), JSON_PRETTY_PRINT);
                    }
                break;

                case 'getRequestAppointmentID':
                    if(count($req)>1){
                        echo json_encode($appointment->getRequestAppointmentID($req[1]), JSON_PRETTY_PRINT);
                    }
                break;

                case 'getAllRequestAppointmentID':
                    if(count($req)>1){
                        echo json_encode($appointment->getAllRequestAppointmentID($req[1]), JSON_PRETTY_PRINT);
                    }
                break;

                case 'getRequestAppointmentHistoryID':
                    if(count($req)>1){
                        echo json_encode($appointment->getRequestAppointmentHistoryID($req[1]), JSON_PRETTY_PRINT);
                    }
                break;


                /***************************** Class Mailer ****************************/ 
                //mailer in angular
                case 'angularmailer':
                    $dt = json_decode(file_get_contents("php://input"));
                    echo json_encode($mail->AngularMailer($dt));
                break;

                //mailer in ionic
                case 'ionicmailer':
                    $dt = json_decode(file_get_contents("php://input"));
                    echo json_encode($mail->IonicMailer($dt));
                break;

                /***************************** Class Auth ****************************/ 
                //register admin
                case 'registerAdmin':
                    $dt = json_decode(file_get_contents("php://input"));
                    echo json_encode($auth->register_admin($dt));
                break;

                //register user
                case 'registerUser':
                    $dt = json_decode(file_get_contents("php://input"));
                    echo json_encode($auth->register_user($dt));
                break;

                //login
                case 'login':
                    $d = json_decode(file_get_contents("php://input"));
                    echo json_encode($auth->login($d));
                break;

                //google login
                case 'googleLogin':
                    $d = json_decode(file_get_contents("php://input"));
                    echo json_encode($auth->google_login($d));
                break;

                /***************************** Class Get USER ****************************/
                 //get query
                 case 'requestUserPending':
                    if(count($req)>1){
                        echo json_encode($get->selectRequestUserPending($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($get->selectRequestUserPending(null), JSON_PRETTY_PRINT);
                    }
                break;

                //get query
                case 'userHistory':
                    if(count($req)>1){
                        echo json_encode($get->selectUserHistory($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($get->selectUserHistory(null), JSON_PRETTY_PRINT);
                    }
                break;

                //get query
                case 'department':
                    if(count($req)>1){
                        echo json_encode($get->selectDepartment($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($get->selectDepartment(null), JSON_PRETTY_PRINT);
                    }
                break;

                //get query with ID
                case 'fillupAssessment':
                    if(count($req)>1){
                        echo json_encode($get->fillupAssessment($req[1]), JSON_PRETTY_PRINT);
                    } 
                break;

                //get query with ID
                case 'fillupRequest':
                    if(count($req)>1){
                        echo json_encode($get->fillupRequest($req[1]), JSON_PRETTY_PRINT);
                    } 
                break;

                //get query with ID
                case 'fillupRequest2':
                    if(count($req)>1){
                        echo json_encode($get->fillupRequest2($req[1]), JSON_PRETTY_PRINT);
                    } 
                break;

                case 'userEmail':
                    if(count($req)>1){
                        echo json_encode($get->selectEmail($req[1]), JSON_PRETTY_PRINT);
                    } 
                break;

                //get query with userID
                case 'appointmentID':
                    if(count($req)>1){
                        echo json_encode($get->selectAppointmentID($req[1]), JSON_PRETTY_PRINT);
                    } 
                break;

                /************ Messages **************/
                //get account of all admin
                case 'userAdmin':
                    if(count($req)>1){
                        echo json_encode($get->selectUserAdmin($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($get->selectUserAdmin(null), JSON_PRETTY_PRINT);
                    }
                break;

                //get request to update
                case 'getRequestToUpdate':
                    if(count($req)>1){
                        echo json_encode($get->selectRequestUpdate($req[1]), JSON_PRETTY_PRINT);
                    } 
                break;

                //get query with userID
                case 'disapprovedRequestID':
                    if(count($req)>1){
                        echo json_encode($get->selectDisapprovedRequestID($req[1]), JSON_PRETTY_PRINT);
                    } 
                break;


                /***************************** Class Get ADMIN ****************************/ 
                //get query
                case 'request':
                    if(count($req)>1){
                        echo json_encode($get->selectRequest($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($get->selectRequest(null), JSON_PRETTY_PRINT);
                    }
                break;

                //get query
                case 'appointment':
                    if(count($req)>1){
                        echo json_encode($get->selectAppointment($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($get->selectAppointment(null), JSON_PRETTY_PRINT);
                    }
                break;

                //get query with userID
                case 'cancelledRequestID':
                    if(count($req)>1){
                        echo json_encode($get->selectCancelledRequest($req[1]), JSON_PRETTY_PRINT);
                    } 
                break;

                //get query
                case 'history':
                    if(count($req)>1){
                        echo json_encode($get->selectHistory($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($get->selectHistory(null), JSON_PRETTY_PRINT);
                    }
                break;

                //get query with userID
                case 'historyID':
                    if(count($req)>1){
                        echo json_encode($get->selectHistory($req[1]), JSON_PRETTY_PRINT);
                    } 
                break;

                //get query
                case 'requestPending':
                    if(count($req)>1){
                        echo json_encode($get->selectRequestPending($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($get->selectRequestPending(null), JSON_PRETTY_PRINT);
                    }
                break;

                //get query
                case 'nullrequest':
                    if(count($req)>1){
                        echo json_encode($get->selectNullRequest($req[1]), JSON_PRETTY_PRINT);
                    } 
                    else {
                        echo json_encode($get->selectNullRequest(null), JSON_PRETTY_PRINT);
                    }
                break;

                //get query
                case 'contactTracing':
                    if(count($req)>1){
                        echo json_encode($get->selectContactTracing($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($get->selectContactTracing(null), JSON_PRETTY_PRINT);
                    }
                break;

                //get query
                case 'adminDepartment':
                    if(count($req)>1){
                        echo json_encode($get->selectAdminDepartment($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($get->selectAdminDepartment(null), JSON_PRETTY_PRINT);
                    }
                break;

                /******ADMIN MESSAGES*********/
                //get query
                case 'users':
                    if(count($req)>1){
                        echo json_encode($get->selectUsers($req[1]), JSON_PRETTY_PRINT);
                    } 
                break;

                 /***************************** Class Get USER OR ADMIN ****************************/

                //get query
                case 'messages':
                    if(count($req)>1){
                        echo json_encode($get->selectMessages($req[1], $req[2]), JSON_PRETTY_PRINT);
                    } 
                break;

                case 'address':
                    if(count($req)>1){
                        echo json_encode($get->selectAddress($req[1]), JSON_PRETTY_PRINT);
                    } 
                break;

                /***************************** Class POST USER ****************************/
              

                case 'insertRequest':
                    $d = json_decode(file_get_contents("php://input"));
                    $post->insertRequest($d);
                    echo json_encode("Inserted Successfully!", JSON_PRETTY_PRINT);
                break;

                //delete query
                case 'cancelRequest':
					$dt = json_decode(file_get_contents("php://input"));
                    $post->cancelRequest($dt);
                break;

                case 'insertAssessment':
                    $d = json_decode(file_get_contents("php://input"));
                    $post->insertAssessment($d);
                    echo json_encode("Inserted Successfully!", JSON_PRETTY_PRINT);
                break;

                //update query - edit request
                case 'updateRequest':
					$dt = json_decode(file_get_contents("php://input"));
                    $post->updateRequest($dt);
                break;

                //update query - verified email
                case 'verifiedUser':
					$dt = json_decode(file_get_contents("php://input"));
                    $post->verified_user($dt);
                break;

                 /***************************** Class POST USER OR ADMIN ****************************/
                case 'insertMessage':
                    $d = json_decode(file_get_contents("php://input"));
                    $post->insertMessage($d);
                    echo json_encode("Inserted Successfully!", JSON_PRETTY_PRINT);
                break;

                /***************************** Class Post ADMIN ****************************/
                //update status - approved request
                case 'approvedRequest':
					$dt = json_decode(file_get_contents("php://input"));
                    $post->approvedRequestStatus($dt);
                    echo json_encode($get->selectRequestPending(null), JSON_PRETTY_PRINT);
                break;

                //update status - disapproved request
                case 'disapprovedRequest':
					$dt = json_decode(file_get_contents("php://input"));
                    $post->disapprovedRequestStatus($dt);
                break;

                //delete query
                case 'deleteRequest':
					$dt = json_decode(file_get_contents("php://input"));
                    $post->deleteRequest($dt);
                    echo json_encode($get->selectRequestPending(null), JSON_PRETTY_PRINT);
                break;

                case 'insertQR':
                    $d = json_decode(file_get_contents("php://input"));
                    echo json_encode($post->insertQR($d));
                    //$post->insertQR($d);
                    //echo json_encode("Inserted Successfully!", JSON_PRETTY_PRINT);
                break;

                case 'insertDepartment':
                    $d = json_decode(file_get_contents("php://input"));
                    $post->insertDepartment($d);
                    echo json_encode("Inserted Successfully!", JSON_PRETTY_PRINT);
                break;

                //update query
                case 'updateDepartment':
					$dt = json_decode(file_get_contents("php://input"));
                    $post->updateDepartment($dt);
                break;

                //delete query this is not working
                case 'deleteDepartment':
					$dt = json_decode(file_get_contents("php://input"));
                    $post->deleteDepartment($dt);
                break;

                default:
                    http_response_code(400);
                    echo "Bad Request";
                    break;
            }
        break;

        //get data from database to display in calendar
        case 'GET':
            switch($req[0]){
                case 'getRequest':
                    if(count($req)>1){
                        echo json_encode($get->selectRequest($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($get->selectRequest(null), JSON_PRETTY_PRINT);
                    }
                break;

                case 'getAppointment':
                    if(count($req)>1){
                        echo json_encode($appointment->getAppointment($req[1]), JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode($appointment->getAppointment(null), JSON_PRETTY_PRINT);
                    }
                break;
    
                default:
                    http_response_code(400);
                    echo "Bad Request";
                    break;
            }
        break;
    
        default:
            http_response_code(400);
            echo "Bad Request";
            break;
    }

?>