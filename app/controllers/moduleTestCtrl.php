<?php class moduleTestCtrl extends appCtrl {


    public function entityTaggedUserList()
    {
        echo "working";
    }


    public function loadgump()
    {

    	$this->load('external', 'gump.class');
    	$gump = new GUMP();

    }

    public function dateEvaluationTest()
    {

        
        $currentDateTime = $this->Dt_24();

        echo "Current Datetime" . $currentDateTime . "<br>";

        $bookingModule = $this->load('module', 'booking');

        $startDate = '2018-12-31';
        $endDate = '2019-01-05';


        $startTime = "09:00 PM";
        $endTime = "02:00 PM";


        $startTime = (string) $this->convertToMysqlTime($startTime);
        $endTime = (string) $this->convertToMysqlTime($endTime);

        $startPoint = (string) $this->mergeDateTime($startDate, $startTime);
        $endPoint = (string) $this->mergeDateTime($endDate, $endTime);

        echo "start Datetime" . $startPoint . "<br>";

        $dateTimeObj = new Datetime();

        $currentDateTimeObj = $dateTimeObj->createFromFormat('Y-m-d H:i:s', $currentDateTime);
        $startDateTimeObj = $dateTimeObj->createFromFormat('Y-m-d H:i:s', $startPoint);
        $endDateTimeObj = $dateTimeObj->createFromFormat('Y-m-d H:i:s', $endPoint);


        /*
        $interval = $currentDateTimeObj->diff($startDateTimeObj);
        $bookingDuration = (double) $interval->format('%h.%i');
        */


            if($currentDateTimeObj > $startDateTimeObj)
            {
                    
                       echo 'Booking datetime of history are not allowed';
                     
            }

            else {
                echo "all good";
            }
        

        die();

        $interval = $currentDateTimeObj->diff($startDateTimeObj);

        $diff = $interval->m;

        echo "<pre>";
        var_dump($diff);
        echo "</pre>";
        
        die();
        $result  = $bookingModule->validateDuration($startPoint, $endPoint);

        

    }



    public function singleCat()
    {
        $catModule = $this->load('module', 'category');

        $data = $catModule->flatJoinById('86');


        var_dump($data);

    }


    public function incEnroll()
    {
        
        $enrollmentModule = $this->load('module', 'enroll');
        $enrollmentModule->registerAttempt(57);
        View::responseJson($enrollmentModule->DB, 200);

    }


    public function catcheck()
    {


        /*
            {
                "title": "checking STD English 2",
                "category_id": "75",
                "cleanDesp": ["88"],
                "cleanSubDesp":["93"],
                "duration": 10,
                "startDateTime": "2019-03-10 12:24",
                "endDateTime": "2019-03-30 12:28",
                "maxScore": 10,
                "minScore": 8,
                "noques": 10
        }

        */


        $quizid = 45;


        $decipline = $_POST['cleanDesp'];
        $subDecipline = $_POST['cleanSubDesp'];
        

        $categoryModule = $this->load('module', 'category');
        $subDescIds = $categoryModule->verifySubDeciplines($decipline, $subDecipline);

        $dataset['cols'] = array('quiz_id', 'subject_id');

        $keyCounter = 0;

        foreach($subDescIds as $key => $value) {

            $dataset['vals'][$keyCounter] = array($quizid, $value);

            $keyCounter++;            
        }


        prx($dataset);     

        /*
        $subjectModule = $this->load('module', 'subject');
        $subjectModule->saveQuizSubjects($quiz_id, $subDescIds);
        */
        

    }




    public function testddx()
    {
        echo "working test ddx";
    }



    public function testquizplayquestions()
    {

        $quiz_id = $this->getID();

        $quizQuestionModule = $this->load('module', 'quizQuestions');

        if($questions = $quizQuestionModule->listQuizPlayQuestionsDistro($quiz_id))
            {
                
                $data['questions'] = $questions;

            }

            else {


                return var_dump($quizQuestionModule->DB);

            }

           return View::responseJson($data, 200);

    }



    public function testMediaLink()
    {

        $question_id = $this->getID();

        $quizQuestionModule = $this->load('module', 'quizQuestions');
        $media = $quizQuestionModule->getQuestionMedia($question_id);


        if($media)
        {
            $data = $media;
        }

        else {
            $data['message'] = "not found";
        }



        View::responseJson($data, 200);

    }


    public function testenc()
    {


        /*
        $this->module = $this->load('module', 'category');
        $data['catlist'] = $this->module->flatJoinList();

        
        */

        
        $users = array(

           'name' => "salman",
           'email' => 'sa@isystematic.com',
           'role' => 'admin',
           'permission' => array('dashboard', 'users', 'quiz', 'questions')
        );



        

        $data['users'] = $this->encodeData($users);


        return View::responseJson($data, 200);
        
    }


    public function dlstatus()
    {

        $enrollID = 168;

        $enrollmentModule = $this->load('module', 'enroll');
        $dls = $enrollmentModule->quizDLSByEnrollmentId($enrollID);


        

    }


    public function teststartact()
    {
        

        $activityModule = $this->load('module', 'attempt');


        $attempt_id = 419;

        $data = $activityModule->pluckEntityIDFromAttempt_id($attempt_id);    


        var_dump($data);

    }


    public function batchtest()
    {
        
        $batchModule = $this->load('module', 'batch');

        $data = $batchModule->candParticipation(1);


        View::responseJson($data, 200);


    }


    public function testAllocation()
    {


        $quiz_id = 105;
        $entity_id = 80;

        $quizQuestionModule = $this->load('module', 'quizQuestions');

       echo $quizQuestionModule->allocateQuestionsByQuizId($quiz_id, $entity_id);


    }


    public function subjectAllocation()
    {
        
        $quiz_id = 128;
        $entity_id = 80;

        $quizQuestionModule = $this->load('module', 'quizQuestions');


        $data = $quizQuestionModule->synchronizeCheck($quiz_id, $entity_id);


        echo $data;


    }


    public function testTickets()
    {

            $last_id = $this->getID();

            $preliminaryModule = $this->load('module','preliminary');
            
            $password = 'hello World';
            
            $ticketsPayload = array('user_id'=> $last_id, 'ticket' => $password);

            if($preliminaryModule->addTickets($ticketsPayload))
            {
                echo "ticket created";
            }

            else {

                echo  "Failed while creating ticket";

                var_dump($preliminaryModule->DB);

            }

            
    }



   public function testphpmailer()
    {
   
        $emailModule = $this->load('module', 'email');
        $mail = $emailModule->getMailer();

      //  $mail->SMTPDebug = true;
        $mail->CharSet = 'utf-8';
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'mail.iskillmetrics.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'no-reply@iskillmetrics.com';       // SMTP username
        $mail->Password = 'w?C35UF?xMa[';                     // SMTP password

        if(SITE_URL == 'http://api.io2v3.dvp/')
        {
            echo "from local <br>";
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;   // Enable TLS encryption, `ssl` also accepted 
            $mail->Port = 587;                                    // TCP port to connect to 465 for ssl 587 for tsl
        }

        else {
            
            echo "from server <br />";

            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;   // Enable TLS encryption, `ssl` also accepted 
            $mail->Port = 465;                                    // TCP port to connect to 465 for ssl 587 for tsl
        }

        $mail->setFrom('no-reply@iskillmetrics.com', 'iSkillMetrics');


        $mail->addAddress('mani_salmanahmed@hotmail.com', 'Salman Ahmed');
        
        $mail->isHTML(true); 
                                         // Set email format to HTML
        $mail->Subject = 'Skillmettics Basic Email Testing';

        
        $mail->Body = "<p> Testing From Local </p>";
        $mail->AltBody = 'Please view in rich html email client';



        

        try {

            $mail->send();
            return true;
            
        } catch (Exception $e) {

            return false;
            
        }
        


    }




    public function testMailWithConfigs()
    {

        $emailModule = $this->load('module', 'email');

        $mail = $emailModule->getConfigMailer();


        $mail->FromName = 'Test With Configs is working';

        $mail->addAddress('sa@isystematic.com', 'Salman Ahmed');
        
        $mail->isHTML(true); 
                                         // Set email format to HTML
        $mail->Subject = 'Skillmetrics Basic';

        /*
        $bodycontents =  file_get_contents("/etemplate?id=33");
                $mail->Body   = $bodycontents;
                $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        */    


        $mail->Body   = $bodycontents;
        $mail->AltBody = 'body cannot be loaded';


        if(!$mail->send())
        {

            echo $mail->ErrorInfo;
        }

        else {
            echo "sent";
        }


    }







    public function servicePost()
    {

        
        $swModule = $this->load('module', 'servicetest');

        $keys = array('first_name', 'middle_name', 'last_name', 'date_of_birth', 'address', 'hobby');


        $dataPayload = $swModule->DB->sanitize($keys);
        

        if($last_id = $swModule->save($dataPayload))
        {
            $data['last_id'] = $last_id;
            $data['message'] = "record added to database";
            $statusCode = 200;
        }

        else {

            $data['message'] = "cannot add record";
            $statusCode = 500;

        }

        return View::responseJson($data, $statusCode);

        

    }



    public function twilloPost()
    {


        $db = new Database();

        $db->table = 'twillo';


        $keys = array('phoneNumber', 'body');


        $dataPayload = $db->sanitize($keys);


        if($last_id = $db->insert($dataPayload))
        {

            $data['addedRecord'] = $last_id;
            $data['message'] = "message has been added to twillo test";
            $data['result'] = "success";
            $statusCode = 200;

        }

        else {

            
            $data['message'] = "Failed while adding a message to twillo test";
            $statusCode = 500;            


        }

        return View::responseJson($data, $statusCode);

    }


    public function nitroTest()
    {


        $quiz_id = 141;

        $quizQuestionModule = $this->load('module', 'quizQuestions');

        var_dump($quizQuestionModule->isDlsQualifiedNitroMode($quiz_id));


    }



    public function dlsAllocateTest()
    {


        $quiz_id = $this->getID();
        
        $quizModule = $this->load('module', 'quiz');

        

        $quizQuestionModule = $this->load('module', 'quizQuestions');



        $allocatedSummary = $quizQuestionModule->dlsQuizQueAllocatedSummary($quiz_id);



        $output = $quizQuestionModule->dlsQualificationCheck($allocatedSummary);



        View::responseJson($allocatedSummary, 200);

        



    }


    public function xattempts()
    {


        $attempt_id = $this->getID();

        $attemptModule = $this->load('module', 'attempt');

        $usageXTimes = $attemptModule->getXTimesUsed($attempt_id);


        if($usageXTimes === false)
        {
            echo "Invalid Attempt ID";

            return false;
        }

        else if ($usageXTimes != 0)
        {
            echo "Aready Used";

            return false;
        }


        echo "continue as folow";

    }


    public function dlsSummaryReport()
    {


        $attempt_id = $this->getID();


        /*    
        $answerModule = $this->load('module', 'answers');
        $dlsReport = $answerModule->buildDLSSummary($attempt_id);
        $dlsReportModule = $this->load('module', 'dlsreport');
        $res = $dlsReportModule->saveDlsReport($attempt_id);

        $data = $attemptModule->isQuizDLSbyAttempt_id($attempt_id);
        */


        $attemptModule = $this->load('module', 'attempt');


        $dlsReportModule = $this->load('module', 'dlsreport');

        $res = $dlsReportModule->updateScoresheetDlsMatrix($attempt_id);

        var_dump($res);


    }

    public function checkTimeZoneTesting()
    {
        function isValidTimezone($timezone) {
        return in_array($timezone, timezone_identifiers_list());
    }

    var_dump(isValidTimezone('Asia/Karachi'));

    die();

    $db = new Database();

    $dbDate = $db->rawSql("SELECT attempted_at as dt from stdattempts where id = '345'")->returnData();

    echo "mysql date time <br>";

    echo $dbDate[0]['dt'];

    echo "php date time";

    echo Date('Y-m-d H:i:s');


    die();

    prx($_SERVER);

    

    /*
    echo "PHP datetime" . Date('Y-m-d H:i:s');
    $db = new Database();
    $datetimeDB = $db->rawSql("SELECT NOW() AS currentdatetime")->returnData(); 
    var_dump($datetimeDB);
        
    git ls-files | xargs wc -l  

    */

    $timezone_offset_minutes = 300;  // $_GET['timezone_offset_minutes']
    // Convert minutes to seconds
    $timezone_name = timezone_name_from_abbr("", $timezone_offset_minutes*60, false);
    // Asia/Kolkata
    echo $timezone_name;

    /*
        date_default_timezone_set($timezone_name);
    */
    }


    public function jwtAclTesting()
    {
        
        if(!jwACL::isLoggedIn()) 
        {
            return $this->uaReponse();
        }

        echo "jwACL is working Fine Token is validated";


    }

    public function jwPlainTesting()
    {

            if(JwtAuth::validateToken())
            {
                echo "token" . JwtAuth::hasToken();
            }
            else {
                echo "token is not authenticated";
            }

    }


    public function testBulkInsert()
    {

        

    }



    public function emailmoduletest()
    {

        $emailModule = $this->load('module', 'email');


        $last_id = 116;
        
        $newUserDetails = array(
            'user_id' => $last_id,
            'email' => 'salmaniz.82@gmail.com'
        );
        
        if($emailModule->sendRegistrationEmail($newUserDetails))
        {
            echo "sent";
        }

        else {

            echo "not sent";
            
        }
        
    }



    public function postmeta()
    {



        $attempt_id = $this->getID();


        $metaPayload = $_POST['meta'];

        $metaPayload = array(

            'endState'=> 'explicit',
            'timeLeft'=> '20156'

        );


        $attemptModule = $this->load('module', 'attempt');


        if($attemptModule->postUpdateMetaInformation($metaPayload, $attempt_id))
        {
            echo "posted and updated";
        }


        else {
            echo "failed post";
        }



    }



    public function testConfigEmailStatus()
    {


        $emailModule = $this->load('module', 'email');

        $resp = $emailModule->testEmailConfigModule();

        var_dump($resp);


    }



    public function attemptIntercept()
    {
        $attemptId = $this->getID();

        $attemptModule  = $this->load('module', 'attempt');

        $interceptModule = $this->load('module', 'intercept');


        $intercept = $attemptModule->interceptForAttempt($attemptId);

        $intercept = $intercept[0]; 

        
        $interceptModule->runPassProcedure($attemptId, $intercept);

        $interceptModule->runFailProcedure($attemptId, $intercept);

        
        

    }



}