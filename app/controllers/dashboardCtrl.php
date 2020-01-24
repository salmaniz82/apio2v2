<?php 

if ( !defined('ABSPATH') )
	die('Forbidden Direct Access');


class dashboardCtrl extends appCtrl
{

	
	public $enrollModule;
	public $attemptModule;
	public $profileModule;

	
	public function __construct()
	{
		
		$this->enrollModule = $this->load('module', 'enroll');
		$this->attemptModule = $this->load('module', 'attempt');	
		$this->profileModule = $this->load('module', 'profile');


	}


	public function router()
	{	
		
		if(jwACL::authRole() == 'admin')
		{
			return $this->adminDashboard();
		}

		else if(jwACL::authRole() == 'entity')
		{
			return $this->entityDashboard();
		}

		else if(jwACL::authRole() == 'students' || jwACL::authRole() == 'canidate')
		{
			return $this->candidateDashboard();
		}

		else if(jwACL::authRole() == 'contributor')
		{
			return $this->contributorDashoardDashboard();
		}
		else if(jwACL::authRole() == 'content developer')
		{
			return $this->contentDeveloperDashboard();
		}

		return $this->accessDenied();

	}

	

	public function candidateDashboard()
	{

	}


	public function contributorDashoard()
	{

	}


	public function contentDeveloperDashboard()
	{

	}


	public function adminDashboard()
	{

	}

	public function entityDashboard()
	{

		if($schedule = $this->enrollModule->allPendingEnrollments($this->jwtUserId(), $this->jwtRoleId()) )
		{
			$data['weekSchedule'] = $schedule;
			
		}

		if($activity = $this->attemptModule->activeMonitoring($this->jwtUserId()))
    	{
    		$data['actvity'] = $activity;  			
    	}

    	if($recentFinished = $this->attemptModule->returnXamountOfRecentQuizResults($this->jwtUserId(), 20))
    	{
    		$data['recentFinished']	= $recentFinished;
    	}

    	if($topPerformer = $this->attemptModule->returnTopPerformer($this->jwtUserId(), 20))
    	{
    		$data['topPerformer'] = $topPerformer;
    	}


    	$data['logo'] = $this->profileModule->autoProfileLogo($this->jwtUserId());


    	$data['message']  = "dashboard";

		return View::responseJson($data, 200);
	}


	public function activity()
	{

		set_time_limit(0);

		


		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();

		if(!jwACL::has('activity-monitor')) 
			return $this->accessDenied();	


		$authenticatedRole = jwACL::authRole();


		if($authenticatedRole == 'entity')
    	{
    		$entity_id = jwACL::authUserId();
    	} 

    	else if($authenticatedRole == 'proctor') {

    		
    		$entity_id = JwtAuth::$user['created_by'];

    	}



		$activity = $this->attemptModule->activeMonitoring($entity_id);

		$dataFilePath = ABSPATH."pooling/activities/activity_{$entity_id}.json";

		if(!file_exists($dataFilePath))
		{

			$handle = fopen($dataFilePath, 'w');
			fclose($handle);

		}

		//$data_source_file = fopen($dataFilePath, "w");

			while (true) {

	    	// if ajax request has send a timestamp, then $last_ajax_call = timestamp, else $last_ajax_call = null

	    	$last_ajax_call = ( isset($_GET['timestamp']) && $_GET['timestamp'] != 0 ) ? (int)$_GET['timestamp'] : null;

	    	// PHP caches file data, like requesting the size of a file, by default. clearstatcache() clears that cache

	    	clearstatcache();

	    	// get timestamp of when file has been changed the last time

	   	 	$last_change_in_data_file = filemtime($dataFilePath);

	    	// if no timestamp delivered via ajax or data.txt has been changed SINCE last ajax timestamp

	    	if ($last_ajax_call == null || $last_change_in_data_file > $last_ajax_call) {

	        // content of file has changed 

	    		$activity = $this->attemptModule->activeMonitoring($entity_id);        

	        	$data['activity'] = $activity;

		        $data['timestamp'] = $last_change_in_data_file;

		        $data['user_id'] = $this->jwtUserId();

		        $statusCode = 200;

	    	    View::responseJson($data, $statusCode);

	    	    break;

	    	} 
	    	else 
	    	{
	        // wait for 1 sec (not very sexy as this blocks the PHP/Apache process, but that's how it goes)
	        sleep( 1 );
	        continue;
	    	}
		}

	}


		public function clearEndActivity()
		{


			if(!jwACL::isLoggedIn()) 
				return $this->uaReponse();

			
			$attemptID = $this->getID();

			$attemptModule = $this->load('module', 'attempt');


			$payload = array(

				'is_active'=> 0
			);


			if($attemptModule->clearActivity($payload, $attemptID))
			{
				$data['message'] = "Marked as inactive";
				$statusCode = 200;
			}

			else {

				$data['message'] = "Failed to mark as inactive";
				$statusCode = 500;

			}

			return View::responseJson($data, $statusCode);	

		}





	}


/*

SET @entity_id = 80;

SELECT sta.id as attemptID, en.id as enrollID, qz.id, qz.code, qz.title, qz.maxScore, qz.minScore, c.name as candidate, c.email as candidateEmail,

sta.score as obtainedScore 

from quiz qz 

INNER JOIN enrollment en on en.quiz_id = qz.id 

INNER JOIN users c on c.id = en.student_id 

INNER JOIN stdattempts as sta on sta.enroll_id = en.id 

where 
user_id = @entity_id AND 
sta.score IS NOT NULL AND 
sta.is_active = 0 AND 
sta.id IN (SELECT max(stax.id) as id from stdattempts stax INNER JOIN enrollment enx on enx.id = stax.enroll_id INNER JOIN quiz qzx on qzx.id = enx.quiz_id WHERE qzx.user_id = @entity_id AND stax.score IS NOT NULL and stax.is_active = 0 group by enroll_id);




*/