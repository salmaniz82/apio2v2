<?php class mediaModule 
{
	
	public $DB;

	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'media';

	}

	

	public function listall()
	{


		$sql = "SELECT m.id, m.category_id, cat.name as 'category', m.title, m.filepathurl, m.type, m.size , m.user_id from media m
		INNER JOIN categories cat on cat.id = m.category_id";

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


		$sql = $sql = "SELECT m.id, m.category_id, cat.name as 'category', m.title, m.filepathurl, m.type, m.size , m.user_id from media m
		INNER JOIN categories cat on cat.id = m.category_id where m.id = $id";


		if($data = $this->DB->getbyId($id)->returnData())
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


	public function update($data, $id)
	{




	}

}