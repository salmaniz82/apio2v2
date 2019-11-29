<?php 
class taggedusersModule {


	public $DB;


	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'taggedusers';
	}


	public function linkusertoentity($userId, $entityId)
	{

		$sql = "INSERT IGNORE INTO taggedusers (user_id, entity_id) VALUES ($userId, $entityId)";
		$res = $this->DB->rawSql($sql);

		return $res;

	}



	public function unlinkuserFromEntity($userId, $entityId)
	{
		
		
	}






}