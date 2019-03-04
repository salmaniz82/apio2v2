<?php class sectionsModule 
{
	
	public $DB;

	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'sections';

	}

	public function joinFlatList()
	{


		$sql = "SELECT s.id, s.sectionsEN, s.sectionsAR, s.category_id, c.cat_name from sections s inner join adj_categories c on c.cat_id = s.category_id";

		$sections = $this->DB->rawSql($sql)->returnData();

		if($sections != null)
		{
			return $sections;
		}
		else {
			return false;
		}

	}


	public function joinFlatSinglebyId($id)
	{


		$sql = "SELECT s.id, s.sectionsEN, s.sectionsAR, s.category_id, c.cat_name from sections s inner join adj_categories c on c.cat_id = s.category_id
		where s.id = $id";

		$sections = $this->DB->rawSql($sql)->returnData();

		if($sections != null)
		{
			return $sections;
		}
		else {
			return false;
		}

	}


	public function save($data)
	{

		if($last_id = $this->DB->insert($data))
		{
			return $last_id;
		}
		else {

			return false;
			
		}	
	}


	public function removeItem($id)
	{

		if($this->DB->delete($id))
		{
			return true;
		}

		else {
			return false;
		}

	}


	public function update($data, $id)
	{


		if($this->DB->update($data, $id))
		{
			return true;
		}
		else {
			return false;
		}

	}

}