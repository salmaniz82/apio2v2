<?php class dashboardCtrl extends appCtrl
{

	
	public $enrollModule;
	public $attemptModule;

	
	public function __construct()
	{
		
		$this->enrollModule = $this->load('module', 'enroll');
		$this->attemptModule = $this->load('module', 'attempt');	


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

		if($schedule = $this->enrollModule->weekSchedule($this->jwtUserId(), $this->jwtRoleId()) )
		{
			$data['weekSchedule'] = $schedule;
			
		}

		if($activity = $this->attemptModule->activeMonitoring($this->jwtUserId()))
    	{
    		$data['actvity'] = $activity;  			
    	}

    	$data['message']  = "dashboard";

		return View::responseJson($data, 200);
	}


	public function activity()
	{

		set_time_limit(0);

		$entity_id = $this->jwtUserId();

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


}