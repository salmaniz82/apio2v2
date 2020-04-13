<?php 

if ( !defined('ABSPATH') )
	die('Forbidden Direct Access');


class mediaModule 
{
	
	public $DB;

	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'media';

	}

	

	public function listall($user_id, $role_id = null)
	{


		if($role_id == null || $role_id != 1)
		{

			$sql = "SELECT m.id, m.category_id, cat.name as 'category', m.title, m.filepathurl, m.type, format_bytes(m.size) as 'size', m.user_id from media m 
			INNER JOIN categories cat on cat.id = m.category_id WHERE m.user_id = $user_id";

		}

		else {

			$sql = "SELECT m.id, m.category_id, cat.name as 'category', m.title, m.filepathurl, m.type, format_bytes(m.size) as 'size', m.user_id from media m 
			INNER JOIN categories cat on cat.id = m.category_id";

		}
	

		$media = $this->DB->rawsql($sql)->returnData();

		if($media != null)
		{
			return $media;
		}
		else {
			return false;
		}

	}


	public function getbyItem($id)
	{


		$sql = $sql = "SELECT m.id, m.category_id, cat.name as 'category', m.title, m.filepathurl, m.type, format_bytes(m.size) as 'size' , m.user_id from media m
		INNER JOIN categories cat on cat.id = m.category_id where m.id = $id";


		if($data = $this->DB->rawsql($sql)->returnData())
		{
			return $data;
		}

		return false;


	}


	public function save($dataPayload)
	{


		if($last_id = $this->DB->insert($dataPayload))
		{
			return $last_id;
		}

		return false;


	}


	

}