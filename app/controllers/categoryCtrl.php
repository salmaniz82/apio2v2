<?php class categoryCtrl extends appCtrl {

	public $module;


	public function __construct()
	{
		$this->module = $this->load('module', 'category');
	}


	public function catTree()
	{


		if($tree = $this->module->catTree())
		{
			
			$data['tree'] = $tree;
			$statusCode = 200;
			$data['status'] = true;
		}
		else {

			$statusCode = 500;
			$data['status'] = false;
			$data['message'] = 'Error while building tree structure';
		}

		return View::responseJson($data, $statusCode);

	}

	public function index()
	{	
		if($cats = $this->module->flatJoinList())
		{		
			$data['cats'] = $cats;
			$data['status'] = true;
			$statusCode = 200;
		}
		else {

			$data['message'] = "No Items Found";
			$data['status'] = true;
			$statusCode = 500;
		}
		
		return View::responseJson($data, $statusCode);

	}

	public function single()
	{

		$id = $this->getID();
		if($cat = $this->module->flatJoinSingle($id))
		{

			$data['cat'] = $cat;
			$data['status'] = true;
			$statusCode = 200;
		}
		else {

			$data['message'] = "No Items Found";
			$data['status'] = true;
			$statusCode = 500;

		}

		View::responseJson($data, $statusCode);

	}


	public function save()
	{

		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();	
		
		if(!jwACL::has('category-add')) 
			return $this->accessDenied();



		$crossTail = false;

		if(!isset($_POST['pcat_id']))
		{
			$keys = array('name');

		}

		else {
			
			$keys = array('name', 'pcat_id');

			$catNameId = $this->module->pluckCatIdByCategoryName($_POST['name']);

			if($catNameId == $_POST['pcat_id'])
			{
				
				$crossTail = true;
				$data['parentCatId'] = $catNameId;

				$data['crossTail'] = $crossTail;

			}


		}

		$payload = sanitize($keys);


		if((!$this->module->checkDuplicate($payload)) && (!$crossTail))
		{
			
			if($last_id = $this->module->save($payload))
			{


				if($newCat = $this->module->flatJoinSingle($last_id))
				{
					$data['message'] = "New categories Added";
					$data['cat'] = $newCat;
				}
				else {

					$data['message'] = "New categories Added but unable to fetch new data";
					$data['cat'] = $newCat;	
				}

				$statusCode = 200;			
				$data['status'] = true;
				
			}

			else {

				$statusCode = 500;
				$data['message'] = "Failed while adding new categories";
				$data['status'] = false;
				$data['debug'] = $this->module->DB;

			}
			
		}

		else {

			$statusCode = 500;
			$data['message'] = (!$crossTail)  ? "Duplicate Entries not Allowed" : "Parent & Category Cannot be Same";
			$data['status'] = false;
			
		}


		return View::responseJson($data, $statusCode);

	}

	

	public function update()
	{

		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();	
		
		if(!jwACL::has('category-edit')) 
			return $this->accessDenied();


		$id = $this->getID();
		$_POST = Route::$_PUT;


		$keys = array('name', 'pcat_id');

		$dataPayload = sanitize($keys);

		if($dataPayload['pcat_id'] == 0)
		{
			$dataPayload['pcat_id'] = 0;	
		}


		if($cat = $this->module->flatJoinSingle($id))
		{



			if($id == $dataPayload['pcat_id'])
			{

				$data['message'] = 'Category and Parent cannot be same';
				$statusCode = 500;

			}

			else {


				if($this->module->updateCategory($dataPayload, $id))
				{
					
					if($dataPayload['pcat_id'] == 0)
					{
						$this->module->setParentZeroToNull($id);
					}

					$data['message'] = 'category updated';
					$data['cat'] = $this->module->flatJoinSingle($id);
					$statusCode = 200;
				}
				else 
				{

					$data['message'] = 'cannot updated category';
					$statusCode = 500;
					$data['db'] = $this->module->DB;
				}

			}

		}

		else {

			$statusCode = 404;
			$data['status'] = false;
			$data['message'] = 'category is not found';

		}

		View::responseJson($data, $statusCode);

	}


	public function destroy()
	{

		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();	
		
		if(!jwACL::has('category-delete')) 
			return $this->accessDenied();

		$id = $this->getID();


		if(!$this->module->hasChildren($id))
		{
			
			if($this->module->destroyById($id))
			{
				
				if($this->module->destroyCategoryPath($id))
				{
					$data['message'] = "Category Removed";
					
					
				}

				else {

					$data['message'] = "Category Removed but unable to sync path";

				}

				$statusCode = 200;

				
			}
			else {

				$data['message'] = "Error While Removing Data";
				$statusCode = 500;
			}
			
		}
		else {
			$data['message'] = "Category got childrens, hence cannot be removed";
			$statusCode = 500;
		}

		return View::responseJson($data, $statusCode);

	}



	public function flatRootList()
	{
		if($category = $this->module->flatRootList())
		{
			$data['categories'] = $category;
			$statusCode = 200;
		}

		else {

			$data['message'] = "failed to load categoies";
			$statusCode = 500;

		}

		return View::responseJson($data, $statusCode);


	}





}