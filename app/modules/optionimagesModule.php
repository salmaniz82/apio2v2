<?php 

if ( !defined('ABSPATH') )
	die('Forbidden Direct Access');



class optionimagesModule {


	public $DB;


	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'optionimages';
	}


	public function save($payload)
	{

		if($lastId = $this->DB->insert($payload))
		{
			return $lastId;
		}

		return false;

	}


	public function getLastId()
	{

		$sql = "SELECT (max(id)+1) as lastId from optionimages";
		$lastId = $this->DB->rawSql($sql)->returnData();

		return $lastId;

	}



}