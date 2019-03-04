<?php class categoryModule extends appCtrl {

	/*
	http://moinne.com/blog/ronald/mysql/manage-hierarchical-data-with-mysql-stored-procedures/comment-page-1#comment-836
	https://kod34fr33.wordpress.com/2008/05/06/adjacency-list-tree-on-mysql/
	*/


	public $DB;

	public function __construct()
	{
		$this->DB = new Database();
		$this->DB->table = 'adj_categories';
	}

	public function flatList()
	{
		
		$sql = 'select * from adj_categories order by cat_id, pcat_id';
		return $this->DB->rawSql($sql)->returnData();	
	}

	public function flatJoinList()
	{

		$sql = "select t2.cat_id as 'id', t1.cat_name as 'parent', t2.cat_name as 'category' from adj_categories t1
			right join adj_categories t2 on t1.cat_id = t2.pcat_id";
		return $this->DB->rawSql($sql)->returnData();
		
	}

	public function flatJoinById($id)
	{

		$sql = "select t2.cat_id as 'id', t1.cat_name as 'parent', t2.cat_name as 'category' from adj_categories t1
			right join adj_categories t2 on t1.cat_id = t2.pcat_id where t2.cat_id = {$id} limit 1";
		return $this->DB->rawSql($sql)->returnData();
		
	}


	public function directChildrenByName($catName)
	{	
		$sql = "select cat_id,cat_name from adj_categories where pcat_id = adj_getcatid('{$catName}')";
		return $this->DB->rawSql($sql)->returnData();	
	}

	public function directChildrenById($cat_id)
	{
		$sql = "select cat_id,cat_name from adj_categories where pcat_id = $cat_id";
		return $this->DB->rawSql($sql)->returnData();	
	}



	public function allFourLevel()
	{
		$sql = "select cat_id,cat_name from adj_categories where pcat_id = $cat_id";
		return $this->DB->rawSql($sql)->returnData();
	}

	public function findParent()
	{
		$sql ="select adj_getcatname(t1.cat_id) AS Child,adj_getcatname(t1.pcat_id) as Parent
			from adj_categories t1 JOIN adj_categories t2 ON (t1.pcat_id = t2.cat_id)";
		return $this->DB->rawSql($sql)->returnData();
	}


	public function findChildren()
	{
		$sql ="select adj_getcatname(t1.cat_id) AS Child,adj_getcatname(t1.pcat_id) as Parent
			from adj_categories t1 JOIN adj_categories t2 ON (t1.pcat_id = t2.cat_id)";
		return $this->DB->rawSql($sql)->returnData();
	}


	public function getChildrenById($id)
	{
		$sql = "
		select  cat_id,
        cat_name,
        pcat_id 
		from    (select * from adj_categories
        order by pcat_id, cat_id) cats_sorted,
        (select @pv := '{$id}') initialisation
		where find_in_set(pcat_id, @pv)
		and length(@pv := concat(@pv, ',', cat_id))";
		return $this->DB->rawSql($sql)->returnData();
	}

	public function getChildrenByName($catName)
	{
		$sql = "
		select  cat_id,
        cat_name,
        pcat_id 
		from    (select * from adj_categories
        order by pcat_id, cat_id) cats_sorted,
        (select @pv := adj_getcatid('{$catName}')) initialisation
		where find_in_set(pcat_id, @pv)
		and length(@pv := concat(@pv, ',', cat_id))";
		return $this->DB->rawSql($sql)->returnData();
	}



	public function destroyById($id)
	{

		if($res = $this->DB->build('D')->where("cat_id = '".$id."'")->go())
		{
			return $res;
		}

		else {
			return false;
		}
	}


	public function pluckCatId($catName)
	{

		if($catId = $this->DB->pluck('cat_id')->Where("cat_name = '".$catName."'"))
		{
			return $catId;
		}

		else {
			return false;
		}

	}



	public function checkDuplicate($category, $parent_id = NULL)
	{

		
		if($data = $this->DB->build('S')->Colums()->Where("cat_name = '".$category."'")->Where("pcat_id = '".$parent_id."'")->go()->returnData())
		{
			return $data;
		}
		else {
			return false;
		}

	}


	public function addNew($data)
	{

		if($last_id = $this->DB->insert($data))
		{
			return $last_id;	
		}
		else {
			return $this->DB;
		}


	}

}