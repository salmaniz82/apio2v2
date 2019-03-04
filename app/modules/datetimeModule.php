<?php class dateTimeModule extends appCtrl {

	public function convertToMysqlTime($rawTime)
    {
  	
		if($this->istime24hrs($rawTime))
		{
			return $rawTime;
		}
		else {
			$tempTime = DateTime::createFromFormat( 'H:i A', $rawTime);
			$output = $tempTime->format('H:i:s');
			return $output;		
		}

		
    }

    public function mergeDateTime($inputDate, $inputTime)
    {

    	$date = new DateTime($inputDate);
		$time = new DateTime($inputTime);

		// Solution 1, merge objects to new object:
		$merge = new DateTime($date->format('Y-m-d') .' ' .$time->format('H:i:s'));

		return $merge->format('Y-m-d H:i:s'); // Outputs '2017-03-14 13:37:42'


		/* Solution 2, update date object with time object:
		$date->setTime($time->format('H'), $time->format('i'), $time->format('s'));
		echo $date->format('Y-m-d H:i:s'); // Outputs '2017-03-14 13:37:42'

		*/


    }

    public function getDbCurrentDateTime()
    {
    	$cDtDB = $this->DB->rawSql("SELECT NOW() as 'cd' ")->returnData();
		return $cDtDB[0]['cd'];	
    }

    public function getDbCurrentDate()
    {
    	$db = new Database();
   	   	$cDtDB = $this->DB->rawSql("SELECT DATE(NOW()) AS 'currentDate'")->returnData();
		return $cDtDB[0]['currentDate'];
    }

    public function dateInputCheck($inputDate)
    {

		if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$inputDate)) 
		{
	    		return $inputDate;
		} 
		else {
		   	return date('Y-m-d', strtotime($inputDate));
		}	
		

    }


    public function Dt_24()
    {
    	return Date('Y-m-d H:i:s');
    }


    public function istime24hrs($timeInput)
	{	
		return preg_match("/^([0-1][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $timeInput);
	}


}