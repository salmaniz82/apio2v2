<?php 

class masterdataListController extends appCtrl {


	public $roleName;
	public $userId;
	public $roleId; 

	public $userModule;
	public $roleModule;
	public $permissionsModule;
	public $rolePermissionsModule;

	public $categoryModule;

	public $quizModule;
	public $questionsModule;
	public $batchModule;
	public $mediaModule;
	
	





	public function __construct()
	{

		
		$this->roleName = jwACL::authRole();
		$this->userId = jwACL::authUserId();
		$this->roleId = jwACL::roleId();

		

		$this->userModule = $this->load('module', 'user');
		$this->roleModule = $this->load('module', 'role');
		$this->permissionsModule = $this->load('module', 'permissions');
		$this->rolePermissionsModule = $this->load('module', 'rolepermissions');

		$this->categoryModule = $this->load('module', 'category');
		$this->quizModule = $this->load('module', 'quiz');
		$this->questionsModule = $this->load('module', 'questions');
		
		$this->batcModule = $this->load('module', 'batch');
		$this->mediaModule = $this->load('module', 'media');
		
		
	}


	public function masterDataRouter()
	{


		

		if(!jwACL::isLoggedIn()) 
		{
			return $this->uaReponse();

		}


		if($this->roleName == 'admin')
		{
			return $this->adminDataRouter();
		}

		else if($this->roleName == 'entity')
		{
			return $this->entityMasterData();
		}

		else if($this->roleName == 'candidate' || $this->roleName == 'students')
		{
			return $this->emtpyDefault();
		}

		else if($this->roleName == 'contributor')
		{
			return $this->emtpyDefault();
		}

		else if($this->roleName == 'content developer')
		{
			return $this->emtpyDefault();
		}
		else {


			$data['message'] = "Undefined Role no data available";
			$statusCode = 406;
			return View::responseJson($data, $statusCode);


		}


	}


	public function emtpyDefault()
	{

		$data['message'] = "No Master data available for " . $this->roleName;
		$statusCode = 200;
		return View::responseJson($data, $statusCode);
	}


	public function adminDataRouter()
	{


		/*
		https://api.iskillmetrics.com/quiz
		https://api.iskillmetrics.com/questions
		https://api.iskillmetrics.com/question-section-summary
		https://api.iskillmetrics.com/batches
		https://api.iskillmetrics.com/roles
		https://api.iskillmetrics.com/users
		https://api.iskillmetrics.com/permissions
		https://api.iskillmetrics.com/role-permissions
		https://api.iskillmetrics.com/cats
		https://api.iskillmetrics.com/media
		*/

		/*
			$data['questions'] = $this->questionsDataHandlers();
		*/	
		

		
		$data['topCategories'] = $this->categoryModule->flatRootList();
		$data['quiz'] = $this->quizDataHandler();
		$data['media'] = $this->mediaDataHandler();
		$data['question-summary'] = $this->questionSummaryHandlers();
		$data['batches'] = $this->batchDataHandlers();
		$data['roles'] = $this->roleDataHandlers();
		$data['users'] = $this->usersDataHandlers();
		$data['permissions'] = $this->permissionDataHandlers();
		$data['role-permissions'] = $this->rolePermissionHandler();
		$data['categories'] = $this->categoryHandlers();
		$data['categoryTree'] = $this->categoryTreeHandler();
		$data['media'] = $this->mediaListHandler();


		$masterData['masterdata'] = $data;	
		return View::responseJson($masterData, 200);


	}



	public function candiateMasterData()
	{

	}


	public function entityMasterData()
	{
		$data['topCategories'] = $this->categoryModule->flatRootList();
		$data['users'] = $this->usersDataHandlers();
		$data['roles'] = $this->roleDataHandlers();
		$data['quiz'] = $this->quizDataHandler();
		$data['media'] = $this->mediaDataHandler();
		$data['question-summary'] = $this->questionSummaryHandlers();
		$data['batches'] = $this->batchDataHandlers();
		$data['categories'] = $this->categoryHandlers();
		$data['categoryTree'] = $this->categoryTreeHandler();
		$data['media'] = $this->mediaListHandler();

		$masterData['masterdata'] = $data;
		return View::responseJson($masterData, 200);

	}


	public function contributorMasterData()
	{

	}


	public function contentDeveloperMasterData()
	{

	}



	public function quizDataHandler()
	{

		$user_id = $this->userId;
		$role_id = $this->roleId;



		if($quiz = $this->quizModule->fetchQuizList($user_id, $role_id))
		{

			$data['status'] = true;
			$data['quiz'] = $quiz;
			$data['statusCode'] = 200;

		}

		else {

		$data['status'] = false;
		$data['message'] = "NO Quiz found";
		$data['statusCode'] = 404;

		}

		return $data;

	}



	public function questionsDataHandlers()
	{
		
		if($questions = $this->questionsModule->listall())
		{
			$data['questions'] = $questions;
			$data['statusCode'] = 200;
		}

		else {
		
			
			$data['message'] = "No questions found please add some";
			$data['statusCode'] = 204;
		}

		return $data;

	}

	public function mediaDataHandler()
	{

		if($media = $this->mediaModule->listall())
		{
			$data['media'] = $media;
			$data['statusCode'] = 200;
		}

		else {

			$data['message'] = "Media not found";
			$data['statusCode'] = 500;
		}
		return $data;

	}


	public function questionSummaryHandlers()
	{
		if($summary = $this->questionsModule->summaryCount())
		{
			
			$data['queSum'] = $summary;
			$data['status'] = true;
			$data['statusCode'] = 200;
			
		}
		else {

			$data['status'] = false;
			$data['statusCode'] = 500;

		}

		return $data;

	}



	public function batchDataHandlers()
	{

		$user_id = $this->userId;

		if($batches = $this->batcModule->listAllBatches($user_id))
		{
			$data['batches'] = $batches;

			if($eligQuiz = $this->batcModule->listBatchEligibleQuiz($user_id))
			{
				$data['eligQuiz'] = $eligQuiz;
			}

			else {

				$data['eligQuiz'] = false;

			}

			$data['statusCode'] = 200;
		}
		else {
		
			$data['statusCode'] = 404;
			$data['message'] = "No Batch Created";

		}	
		
		return $data;
	}

	public function roleDataHandlers()
	{
		
		$role_id = $this->roleId;

		if($roles = $this->roleModule->returnAllRoles($role_id))
		{
			$data['roles'] = $roles;
			$data['statusCode'] = 200;
		}
		else {
			$data['message'] = "Role cannot be found";
			$data['statusCode'] = 500;
		}

		return $data;
	}


	public function usersDataHandlers()
	{

		if($data['users'] = $this->userModule->allUsers())
		{
			
			$data['status'] = true;
			$data['statusCode'] = 200;
		}
		else {
			$data['message'] = "No Users Found";
			$data['statusCode'] = 206;
		}

		return $data;

	}


	public function permissionDataHandlers()
	{

		if($rows = $this->permissionsModule->returnAllPermissions())
		{
			$data['permissions'] = $rows;
			$data['statusCode'] = 200;
		}
		else {

			$data['message'] = "Permission not found";
			$data['statusCode'] = 500;
		}

		return $data;

	}


	public function rolePermissionHandler()
	{
		if($rows = $this->rolePermissionsModule->returnAllRolePermissions())
		{
			$data['permissions'] = $rows;
			$data['statusCode'] = 200;
		}
		else {
			$data['message'] = "Permission not found";
			$data['statusCode'] = 500;
		}

		return $data;
	}



	public function categoryHandlers()
	{	
		if($cats = $this->categoryModule->flatJoinList())
		{		
			$data['cats'] = $cats;
			$data['status'] = true;
			$data['statusCode'] = 200;
		}
		else {

			$data['message'] = "No Items Found";
			$data['status'] = true;
			$data['statusCode'] = 500;
		}
		
		return $data;

	}


	public function mediaListHandler()
	{

		if($media = $this->mediaModule->listall())
		{
			$data['media'] = $media;
			$data['statusCode'] = 200;
		}

		else {

			$data['message'] = "Media not found";
			$data['statusCode'] = 500;
		}

		return $data;

	}


	public function categoryTreeHandler()
	{


		if($tree = $this->categoryModule->catTree())
		{
			
			$data['tree'] = $tree;
			$data['statusCode'] = 200;
			$data['status'] = true;
		}
		else {

			$data['statusCode'] = 500;
			$data['status'] = false;
			$data['message'] = 'Error while building tree structure';
		}

		return $data;

	}





}