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


		return View::responseJson($data, 200);
	}


}