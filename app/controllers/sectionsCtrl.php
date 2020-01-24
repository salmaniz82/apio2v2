<?php 

if ( !defined('ABSPATH') )
	die('Forbidden Direct Access');


class sectionsCtrl extends appCtrl
{

	public $module;


	public function __construct()
	{
		$this->module = $this->load('module', 'sections');
	}


	public function listAll()
	{

		$data['sections'] = $this->module->joinFlatList();
		$data['status'] = true;
		View::responseJson($data, 200);

	}



	public function catCombo()
	{
		
		$categoryModule = $this->load('module', 'category');
		$data['sections'] = $this->module->joinFlatList();
		$data['cats'] = $categoryModule->flatList();
		View::responseJson($data, 200);

	}


	public function save()
	{

		$keys = array('sectionsEN', 'sectionsAR', 'category_id');
		$dataArray = sanitize($keys);
		$dataArray['user_id'] = $this->jwtUserId();

		if($last_id = $this->module->save($dataArray))
		{
			$data['section'] = $this->module->joinFlatSinglebyId($last_id);
			$statusCode = 200;
			$data['message'] = "New Sections Added";
			$data['last_id'] = $last_id;
			$data['status'] = true;
		}

		else {
			$statusCode = 500;	
			$data['message'] = 'Cannot add new section';
			$data['status'] = false;
		}

		
		View::responseJson($data, 200);

	}

	public function destroy()
	{


		$id = $this->getID();

		if( $this->module->removeItem($id) )
		{

			$data['message'] = "Item Removed";
			$data['status'] = true;
			$statusCode = 200;

		}
		else {

			$data['message'] = "Failed while removing section";
			$data['status'] = false;
			$statusCode = 500;
		}

		View::responseJson($data, $statusCode);

	}


	public function update()
	{

		$id = $this->getID();

		$_POST = Route::$_PUT;


		if($this->module->update($_POST, $id))
		{
			$data['message'] = "Section Updated With Success";	
			$statusCode = 200;

			if($updateRecord = $this->module->joinFlatSinglebyId($id))
			{
				$data['sections'] = $updateRecord;
			}

		}
		else {

			$data['message'] = "Section Updated With Failed";	
			$statusCode = 500;	

		}

		View::responseJson($data, $statusCode);

	}

}