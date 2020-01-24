<?php 

if ( !defined('ABSPATH') )
	die('Forbidden Direct Access');


class pagesCtrl extends appCtrl {


	public function staticPage()
	{

		return View::render('testingpage');


	}


	public function scoresheet()
	{


		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();
	
		$attemptModule = $this->load('module', 'attempt');

		$answerModule = $this->load('module', 'answers');

		$quizModule = $this->load('module', 'quiz');

		$quiz_id = (int) Route::$params['quiz_id'];
		$attempt_id = (int) Route::$params['attempt_id'];



		if($attemptDetail = $attemptModule->getAttemptDetails($attempt_id))
		{
			$data['attempt'] = $attemptDetail;
		}



		if($quizModule = $quizModule->getQuizById($quiz_id))
		{
			$data['quiz'] = $quizModule;
		}


		if($scoreCard = $answerModule->scoreCardBreakDown($quiz_id, $attempt_id))
		{
			
			$data['scorecard'] = $scoreCard;

			$data['total'] = number_format(array_sum(array_column($scoreCard, 'actualScore')), 2);
			$data['maxTotal'] = array_sum(array_column($scoreCard, 'maxScore'));
			$data['queTotal'] = array_sum(array_column($scoreCard, 'quePerSection'));
			$data['overAllPer'] = number_format(($data['total'] / $data['maxTotal']) * 100, 2);
			
		}

		
		View::render('scoresheet', $data);

		
	}






}