<?php class moduleTestCtrl extends appCtrl {


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

            $preliminaryModule = $this->load('module','preliminary');

            /*
            $password = 'hello World';
            $last_id = 456;
            echo 'working from here';
            
            $ticketsPayload = array('user_id'=> $last_id, 'ticket' => $password);
            $preliminaryModule->addTickets($ticketsPayload);
            */


            $preliminaryModule->removeTicket(['user_id', '456']);


    }



   public function testphpmailer()
    {
   
        $emailModule = $this->load('module', 'email');
        $mail = $emailModule->getMailer();
        $mail->CharSet = 'utf-8';
        $mail->IsMail();                                      // Set mailer to use SMTP
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

        
        $mail->From = 'no-reply@iskillmetrics.com';
        $mail->FromName = 'Testing From Local';



        $mail->addAddress('salmaniz.82@gmail.com', 'Salman Ahmed');
        
        $mail->isHTML(true); 
                                         // Set email format to HTML
        $mail->Subject = 'Skillmettics Basic Email Testing';

        
        $mail->Body = "<p> Testing From Local </p>";
        $mail->AltBody = 'Please view in rich html email client';


        if(!$mail->send())
        {

            echo $mail->ErrorInfo;
        }

        else {
            echo "sent";
        }


    }




    public function testMailWithConfigs()
    {

        $emailModule = $this->load('module', 'email');
        $mail = $emailModule->getConfigMailer();



        $mail->FromName = 'Config Mail';

        $mail->addAddress('salmaniz.82@gmail.com', 'Salman Ahmed');
        
        $mail->isHTML(true); 
                                         // Set email format to HTML
        $mail->Subject = 'Skillmettics Basic Email Testing';

        /*
        $bodycontents =  file_get_contents("/etemplate?id=33");
                $mail->Body   = $bodycontents;
                $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        */    


        $bodycontents =  $this->file_get_contents_curl(SITE_URL.'etemplate?id=33');

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







    }




}