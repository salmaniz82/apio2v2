<?php class contributorModule 
{
	
	public $DB;

	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'contributors';

	}

	

	public function listall()
	{

		$types = $this->DB->listall()->returnData();

		if($types != null)
		{
			return $types;
		}
		else {
			return false;
		}

	}


	public function pluckEntity_id($contributor_id)
	{

		if($entity_id = $this->DB->pluck('entity_id')->Where("contributor_id = '".$contributor_id."'"))
		{
			return $entity_id;
		}

		else {
			return false;
		}

	}


	public function assignContributor($dataPayload)
	{

		if($last_id = $this->DB->insert($dataPayload))
		{
			return $last_id;
		}

		return false;

	}

}