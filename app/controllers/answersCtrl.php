<?php 
class answersCtrl extends appCtrl 
{

	public $module;

	public function __construct()
	{
		$this->module = $this->load('module', 'answers');

	}


	public function recoverFromActivity()
	{

		$attempt_id = $this->getID();

		/*

		1. copy answers from activity
		2. insert to questions

		3. toggleActive
		4. markAnswer with correct & incorrect
		5. calcuate score and insert into score sheet
		6. get caculated score
		7. set / udpate score on attempts table
		8. update question counter
		9. 


		*/


		if($this->module->recoverAnswersFromActivity($attempt_id))
		{

			$attemptModule = $this->load('module', 'attempt');

			$attemptModule->toggleActive($attempt_id, "0");

			$this->module->markAnswers($attempt_id);

			$this->module->saveCalculatedSubjectsScore($attempt_id);

			$queCounter = $this->module->udpateQuestionCounter($attempt_id);

			$score = $this->module->getCalcuatedScoreSum($attempt_id);

			$this->module->setBasicScore($attempt_id, $score);



			if($progress = $this->module->singleProgressByAtemptId($attempt_id))
			{
				$data['progress'] = $progress;

				$statusCode = 200;
			}

			else {
				$data['message'] = "Unable to fetch Progress";
				$statusCode = 500;
			}


			return View::responseJson($data, $statusCode);

		}


	}


	public function patchAnswers()
	{

		
		$payload = $_POST['answers'];

		$user_id = $this->jwtUserId();

		$attempt_id = $payload[0]['attempt_id'];
		$dataset['cols'] = array('attempt_id', 'question_id', 'answer');
		$dataset['vals'] = $payload;


		$attemptModule = $this->load('module', 'attempt');

		$attemptModule->toggleActive($attempt_id, "0");

		if($this->module->patchBulkAnswers($dataset))
		{
			
			$this->module->markAnswers($attempt_id);

			$this->module->saveCalculatedSubjectsScore($attempt_id);

			/*				
			if($attemptModule->isQuizDLSbyAttempt_id($attempt_id))
			{
				
				$dlsReportModule = $this->load('module', 'dlsreport');

				$dlsReportModule->saveDlsReport($attempt_id);

				$dlsReportModule->updateScoresheetDlsMatrix($attempt_id);
			}
			*/
			

			$queCounter = $this->module->udpateQuestionCounter($attempt_id);

			$score = $this->module->getCalcuatedScoreSum($attempt_id);

			$this->module->setBasicScore($attempt_id, $score);


			$data['message'] = "Answers Were Saved";
			
			$data['score'] = $score;
			
			$data['consumed'] = $queCounter;

			$statusCode = 200;

			$data['status'] = true;


			$activityModule = $this->load('module', 'activity');

			$entity_id = $activityModule->pluckEntityIDFromAttempt_id($attempt_id);

			$FileName = ABSPATH."pooling/activities/activity_"."{$entity_id}".".json";

        	$dataFilePath = $FileName;

        	$data_source_file = fopen($dataFilePath, "w");

			$dataPayload['message'] = "quiz finished";	        	

        	fwrite($data_source_file, json_encode($dataPayload));

			fclose($data_source_file);

			$studentFilePath = ABSPATH."pooling/stdquiz/candidate_"."{$user_id}".".json";

			$stdSourceFile = fopen($studentFilePath, "w");

			fwrite($stdSourceFile, json_encode($dataPayload));

			fclose($stdSourceFile);				

		}


		else 
		{
			$data['message'] = "Answer were not saved to database";
			$statusCode = 400;
			$data['status'] = false;
		}

		return View::responseJson($data, $statusCode);

	}


	public function inpspectAnswers()
	{


			$quiz_id = (int) Route::$params['quiz_id'];
			$attempt_id = (int) Route::$params['attempt_id'];
			
			if($answers = $this->module->inspectAnswerByAttemptId($attempt_id))
			{
				$data['answers'] = $answers;
				$data['meta'] = [
					'correct' => 0,
					'incorrect'=> 0 
				];


				foreach ($answers as $key => $value) {

					if($value['isCorrect'] == 1)
					{
						$data['meta']['correct']++; 	
					}
					else {
						$data['meta']['incorrect']++;
					}
					
				}


				$data['qzAttemptSubjects'] = $this->module->quizAttemptQuestionSubjects($attempt_id);
				$data['status'] = true;
				$statusCode = 200;

			}

			else {
				$data['message'] = "NO Attempted Answer found";
				$data['status'] = false;
				$statusCode = 500;
			}


			View::responseJson($data, $statusCode);

	}



	public function scoreCard()
	{

		$quiz_id = (int) Route::$params['quiz_id'];
		$attempt_id = (int) Route::$params['attempt_id'];

		if($scoreCard = $this->module->scoreCardBreakDown($quiz_id, $attempt_id))
		{
			$data['scorecard'] = $scoreCard;
			$data['total'] = number_format(array_sum(array_column($scoreCard, 'actualScore')), 2);
			$data['maxTotal'] = array_sum(array_column($scoreCard, 'maxScore'));
			$data['queTotal'] = array_sum(array_column($scoreCard, 'quePerSection'));
			$data['overAllPer'] = number_format(($data['total'] / $data['maxTotal']) * 100, 2);
			$data['status'] = true;
			$statusCode = 200;
		}

		else {
			$statusCode = 500;
			$data['message'] = "Unable to fetch scorecard for this quiz attempt";
			$data['status'] = false;
		}


		return View::responseJson($data, $statusCode);


	}



	public function activityHandler()
	{


		$activityModule = $this->load('module', 'activity');

        $keys = array('attempt_id', 'question_id', 'questionIndex', 'answer', 'atype');

        $attempt_id = $_POST['attempt_id'];

        $entity_id = $activityModule->pluckEntityIDFromAttempt_id($attempt_id);

        $dataPayload = $activityModule->DB->sanitize($keys);
        

        if($activityModule->activityHandler($dataPayload))
        {
        	// write to a file

        	$FileName = ABSPATH."pooling/activities/activity_"."{$entity_id}".".json";

        	$dataFilePath = $FileName;

        	$data_source_file = fopen($dataFilePath, "w");

        	fwrite($data_source_file, json_encode($dataPayload));

			fclose($data_source_file);

        }

        $data['message'] = 'done';
        return View::responseJson($data, 204);

	}


}