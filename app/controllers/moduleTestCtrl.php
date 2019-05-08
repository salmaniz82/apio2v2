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





    public function testQuizPlayQuestion()
    {

        $quiz_id = $this->getID();

        $quizQuestionModule = $this->load('module', 'quizQuestions');

        if($questions = $quizQuestionModule->listQuizPlayQuestionsDistro($quiz_id))
            {
                
                $data['questions'] = $questions;

            }

           View::responseJson($data, 200);

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




    public function retakeFilter()
    {


        $quiz_id = 85;

        $quizQuestionModule = $this->load('module', 'quizQuestions');
        $questions = $quizQuestionModule->listQuizPlayQuestionsDistro($quiz_id);
        prx($questions);        



    }









    



}