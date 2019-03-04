<?php class categoryCtrl extends appCtrl {


	public $module;

	public function __construct()
	{
		$this->module = $this->load('module', 'category');
	}


	public function flatJoinList()
	{

		$cats = $this->module->flatJoinList();


		if($cats != null)
		{
			$data['cats'] = $cats;
			$data['status'] = true;
			$statusCode = 200;

		}
		else {

			$statusCode = 500;
			$data['message'] = "No Records Found";

		}


		View::responseJson($data, $statusCode);

		
	}



	public function flatList()
	{
		
		$cats = $this->module->flatList();

		if($cats != null)
		{
			$data['cats'] = $cats;
			$data['status'] = true;
			$statusCode = 200;

		}
		else {

			$statusCode = 500;
			$data['message'] = "No Records Found";

		}


		View::responseJson($data, $statusCode);


	}

	public function dDirectChildrenByName()
	{	
		$catName = Route::$params['catName'];
		$catName = urldecode($catName);
		$cats = $this->module->directChildrenByName($catName); 
		prx($cats);
	}


	public function dDirectChildrenById()
	{
		$catID = (int) Route::$params['catID'];
		$cats = $this->module->directChildrenById($catID);
		prx($cats);	
	}


	public function fourlevel()
	{


	}

	public function findParent()
	{
		$cats = $this->module->findParent();
		var_dump($cats);

	}

	public function findChildren()
	{

		$cats = $this->module->findChildren();
		var_dump($cats);

	}


	public function allChildrenById()
	{

		$catID = (int) Route::$params['catID'];
		$cats = $this->module->getChildrenById($catID);
		prx($cats);
	}

	public function allChildrenByName()
	{
		
		$catName = Route::$params['catName'];
		$catName = urldecode($catName);
		$cats = $this->module->getChildrenByName($catName);
		prx($cats);
	}


	public function buildTree($dataArr)
	{

		$data;
		$tree = [];
		$bTree = [];
		$depth = [];
		for ($i=0; $i <= sizeof($dataArr) -1; $i++) { 

		}

	}


	public function destroy()
	{

		$id = $this->getID();
        $user_id = $this->jwtUserId();
        $role_id = $this->jwtRoleId();


		if($res = $this->module->destroyById($id) )
		{
                 
            $statusCode = 200;
            $data['message'] = 'Record removed successfully';
            $data['type'] = 'success';
            $data['status'] = true;
      
        }
        else {
		    $statusCode = 404;
		    $data['message'] = 'cannot find record with this id';
            $data['type'] = 'error';
            $data['status'] = false;
        }


	    return  view::responseJson($data, $statusCode);

	}



	public function addWithText()
	{

		$parent = $_POST['parent'];
		$category = $_POST['category'];

		// check if parent id exists

		if($cat_id = $this->module->pluckCatId($parent))
		{

			// check for duplicates

			if(!$this->module->checkDuplicate($category, $cat_id))
			{

				$payload['pcat_id'] = $cat_id;
				$payload['cat_name'] = $category; 

				if($last_id = $this->module->addNew($payload))
				{

					// record added

					if($newCat = $this->module->flatJoinById($last_id))
					{

						$data['message'] = "New Record has been added";
						$data['cat'] = $newCat;
						$statusCode = 200;
					}
					else {

						$data['message'] = "Record added but new record cannot be fetched";
						$data['last_id'] = $last_id;
						$statusCode = 200;

					}


				}

				else {
					$data['message'] = "Failed while adding new category";
					$statusCode = 500;
				}

			}

			else {

				$data['message'] = "Duplicate Entry not allowed";
				$statusCode = 500;

			}


		}

		else {

			$data['message'] = "No parent category found";
			$statusCode = 500;


		}


		View::responseJson($data, $statusCode);

	}


	

	

}