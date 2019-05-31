<?php 
class answersCtrl extends appCtrl 
{

	public $module;

	public function __construct()
	{
		$this->module = $this->load('module', 'answers');

	}


	public function patchAnswers()
	{

		
		$payload = $_POST['answers'];

		$attempt_id = $payload[0]['attempt_id'];
		$dataset['cols'] = array('attempt_id', 'question_id', 'answer');
		$dataset['vals'] = $payload;


		$attemptModule = $this->load('module', 'attempt');

		$attemptModule->toggleActive($attempt_id, "0");

		if($this->module->patchBulkAnswers($dataset))
		{
			
			$correct = $this->module->markRightAnswers($attempt_id);
			$inCorrect = $this->module->markIncorrectAnswers($attempt_id);


			$this->module->saveCalculatedSubjectsScore($attempt_id);

			$queCounter = $this->module->udpateQuestionCounter($attempt_id);

			$score = $this->module->getCalcuatedScoreSum($attempt_id);
			$this->module->setBasicScore($attempt_id, $score);

			$data['message'] = "Answers Were Saved";
			$data['correct'] = $correct;
			$data['wrong'] = $inCorrect;
			$data['score'] = $score;
			$data['consumed'] = $queCounter;
			$statusCode = 200;
			$data['status'] = true;
		}

		else 
		{
			$data['message'] = "Cannot Save Amswers";
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
			$data['total'] = array_sum(array_column($scoreCard, 'actualScore'));
			$data['maxTotal'] = array_sum(array_column($scoreCard, 'maxScore'));
			$data['queTotal'] = array_sum(array_column($scoreCard, 'quePerSection'));
			$data['overAllPer'] = ($data['total'] / $data['maxTotal']) * 100;
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

        $dataPayload = $activityModule->DB->sanitize($keys);

        $activityModule->activityHandler($dataPayload);

        $data['message'] = "no contents needed";

        return View::responseJson($data, 204);

	}


}