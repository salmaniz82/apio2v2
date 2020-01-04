<?php class questionsCtrl extends appCtrl
{

	public $module;


	public function __construct()
	{
		$this->module = $this->load('module', 'questions');
	}


	public function index()
	{
		
		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();


		
		if($questions = $this->module->listall())
		{
			$data['questions'] = $questions;
			$statusCode = 200;
		}

		else {
		
			$data['debug'] = $this->module->DB;
			$data['message'] = "No questions found please add some";
			$statusCode = 204;
		}


		return View::responseJson($data, $statusCode);


	}


	public function save()
	{

		
		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();


		$keys = array('category_id', 'section_id', 'level_id', 'type_id', 'queDesc', 'optionA', 'optionB', 'optionC', 'optionD', 'answer');
		$dataPayload = $this->module->DB->sanitize($keys);
		$dataPayload['user_id'] = $this->jwtUserId();
		$dataPayload['status'] = 1;


		if(jwACL::authRole() == 'contributor')
		{
			$contributorModule = $this->load('module', 'contributor');
			$entity_id = $contributorModule->pluckEntity_id($this->jwtUserId());
			$dataPayload['entity_id'] = $entity_id;
			$dataPayload['scope'] = 'private';
		}

		else if(jwACL::authRole() == 'entity')
		{
			
			$dataPayload['entity_id'] = $this->jwtUserId();

			if(!isset($_POST['quiz_id']))
			{
				$dataPayload['scope'] = 'private';		
			}


		}


		if(isset($_POST['quiz_id']))
		{
			$dataPayload['quiz_id'] = $_POST['quiz_id'];

			$dataPayload['scope'] = 'linked';
		}


		if($last_Id = $this->module->store($dataPayload))
		{
			$data['last_id'] = $last_Id;
			$statusCode = 200;

			$data['message'] = "New Question Added Successfully";


			if(isset($_POST['mediaIds']))
			{

				$mediaPost = $_POST['mediaIds'];

				$mediaPayload = [];

				
				$questionMediaModule = $this->load('module', 'questionsMedia');
				if($questionMediaModule->saveQuestionMedia($last_Id, $mediaPost))
				{
					$data['message'] = "New Question Added Successfully with media";
					$statusCode = 200;
				}
				else {
					$data['message'] = "Saved but failed to attach media to question";
					$statusCode = 406;
				}

			}


			/*
				if that stored with quiz id that need to be auto synced to question table
			*/

				if(isset($_POST['quiz_id']))
				{
					$quiz_id = $_POST['quiz_id'];
					$quizQuestionModule = $this->load('module', 'quizQuestions');


					$PrivateQuestionData = array(
						'quiz_id' => $quiz_id,
						'question_id' => $last_Id
					);



					if($quizQuestionModule->autoSyncPrivateQuizQuestions($PrivateQuestionData))
					{
							$data['message'] = "New Question Added and Synced";
					}

					else {
						 $data['message'] = "New Question But not sync to questions";	
						 
					}
				}
				
		}
		else {
			$data['res'] = $last_Id;
			$statusCode = 500;
			$data['message'] = "Failed to Add new Question";
			$data['db'] = $this->module->DB->connection;

		}

		return View::responseJson($data, $statusCode);

	}


	public function summaryCount()
	{
		if($summary = $this->module->summaryCount())
		{
			
			$data['queSum'] = $summary;
			$data['status'] = true;
			$statusCode = 200;
			
		}
		else {

			$data['status'] = false;
			$statusCode = 500;

		}

		return View::responseJson($data, $statusCode);

	}


	public function statusToggle()
	{

		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();	


		/*
		bind permission
		!admin check ownership
		*/


		$_POST = Route::$_PUT;
		$id = $this->getID();

		$statusValue = $_POST['status'];

		if($this->module->statusToggle($statusValue, $id))
		{

			$stsText = ($statusValue == 1) ? 'enabled' : 'disabled';
			$data['message'] = "Question status : " . $stsText;
			$data['status'] = true;
			$statusCode = 200;

		}

		else {

			$data['message'] = "Failed while updating status";
			$data['status'] = true;
			$statusCode = 500;

		}

		return View::responseJson($data, $statusCode);

	}



	public function singlequestion()
	{

		$queID = $this->getID();
		$user_id = jwACL::authUserId();
		$role = jwACL::authRole();

		if($question = $this->module->getsingle($user_id, $role, $queID))
		{
			$data['question'] = $question[0];
			$statusCode = 200;	
		}
		else {

			$data['message'] = 'Question data not found';
			$statusCode = 500;	

		}

		return View::responseJson($data, $statusCode);

	}


	public function update()
	{

		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();

		$queID = $this->getID();

		$user_id = jwACL::authUserId();

		$role = jwACL::authRole();

		$_POST = Route::$_PUT;


		$questionType = $_POST['type_id'];

		$keys = array('queDesc', 'answer');

		if($questionType == 1 || $questionType == 3)
		{
			array_push($keys, 'optionA', 'optionB', 'optionC', 'optionD');
		}

		else if($questionType == 2)
		{
			array_push($keys, 'optionA', 'optionB');	
		}


		$dataPayload = $this->module->DB->sanitize($keys);

		if($this->module->updateQuestionBasic($dataPayload, $queID))
		{

			$data['message'] = "Question updated successfully";
			$data['payload'] = $dataPayload;

			$statusCode = 200;

		}

		else {

			$data['message'] = "Failed updating question";
			$statusCode = 500;
		}

		return View::responseJson($data, $statusCode);

	}



	public function uploadCSV()
	{


		if(!jwACL::isLoggedIn()) 
			return $this->uaReponse();



		if(jwACL::authRole() == 'contributor')
		{
			$contributorModule = $this->load('module', 'contributor');
			$entity_id = $contributorModule->pluckEntity_id($this->jwtUserId());
			$scope = 'private';
		}

		else if(jwACL::authRole() == 'entity')
		{
			
			$entity_id = $this->jwtUserId();
			$scope = 'private';
			
		}

		else if(jwACL::authRole() == 'content developer')
		{
			$entity_id = null;
			$scope = 'public';
		}




		$category_id = $_POST['category_id'];

		$section_id = $_POST['section_id'];

		$user_id = $user_id = jwACL::authUserId();

		$status = 1;

		$fileRowsLimit = 300;

		
		$mimes = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv', 'application/octet-stream');


		if(!isset($_FILES['file']))
		{
			
			$data['message'] = "No File Provided cannot proceed further";
			$statusCode = 406;
			return View::responseJson($data, $statusCode);
		}

		else if(!in_array($_FILES["file"]["type"], $mimes) )
		{


			$data['message'] = "File type is not supported";
			$statusCode = 406;
			return View::responseJson($data, $statusCode);
			
		}

		else {


				$target_dir = "uploads/datafiles/";

				$filename = sanitizeFilename(basename($_FILES["file"]["name"]));

				$target_file = $target_dir.$filename;
				
		}


		if(file_exists($target_file))
		{
			unlink($target_file);	
		}

	
			if(!move_uploaded_file($_FILES["file"]["tmp_name"], $target_file))
			{

				$data['message'] = "unable to upload file";
				return View::responseJson($data, 500);
			}

			$csv = array_map('str_getcsv', file($target_file));

			$fileRowsCount = sizeof($csv) - 1;


			if($fileRowsCount > $fileRowsLimit)
			{
				$data['message'] = $fileRowsLimit . " rows of data is allowed for upload provied with " . $fileRowsCount;
				$statusCode = 406;
				return View::responseJson($data, $statusCode);

				die();
			}




			$requiredColumns = array('question', 'a', 'b', 'c', 'd', 'answer', 'type', 'difficulty');


			

			$difficulty = array('easy', 'medium', 'difficult');

			$type = array('radio', 'true/false', 'checkbox', 'text');

			$optionalColumns = array('difficulty', 'type');



			foreach ($requiredColumns as $value) 
			{	

					if(!$idx = array_search($value, $csv[0]))
					{
						$data['message'] = "Required column ". $value . " not provided in file ";
						return View::responseJson($data, 406);
					}
			}


			$finalArray;



			$hasDifficulty = false;
			$hasType = false;


			$userIndex = array_push($csv[0], 'user_id');
			$userIndex = array_push($csv[0], 'entity_id');
			$userIndex = array_push($csv[0], 'scope');
			
			$categoryIndex = array_push($csv[0], 'category_id');
			$sectionIndex = array_push($csv[0], 'section_id');
			$statusIndex = array_push($csv[0], 'status');


			for($i=0; $i<sizeof($csv); $i++)
			{

				if($i==0)
				{

					foreach ($optionalColumns as $val) {	

					
					if($idx = array_search($val, $csv[$i]))
					{

						if($csv[$i][$idx] == 'difficulty')
						{
							$hasDifficulty = true;
							$diffIndex = $idx;
						} 

						else if($csv[$i][$idx] == 'type') {

							$hasType = true;

							$typeIndex = $idx;

						}
						
						
					}

				}



				}

					if($i > 0 && $hasDifficulty)
					{


						array_push($csv[$i], $user_id);
						array_push($csv[$i], $entity_id);
						array_push($csv[$i], $scope);
						array_push($csv[$i], $category_id);
						array_push($csv[$i], $section_id);
						array_push($csv[$i], $status);

						

						$replacedIndexDifficulty = array_search($csv[$i][$diffIndex], $difficulty);
			

						if($replacedIndexDifficulty === false)
						{					

							$csv[$i][$diffIndex] = 0;
													
						}

						else {


							$csv[$i][$diffIndex] = $replacedIndexDifficulty + 1;

						}


					}


					if($i > 0 && $hasType)
					{
						

						$replacedTypeIndex = array_search($csv[$i][$typeIndex], $type);
			
						if($replacedTypeIndex === false)
						{					

							$csv[$i][$typeIndex] = 0;
													
						}

						else {

							$csv[$i][$typeIndex] = $replacedTypeIndex + 1;

						}

					}

		}


			foreach($csv[0] as &$val)
			{

	    		if($val == 'question')
	    			$val = 'queDesc';	
	    		
	    		if($val == 'a')
	    			$val = 'optionA';


	    		if($val == 'b')
	    			$val = 'optionB';


	    		if($val == 'c')
	    			$val = 'optionC';


	    		if($val == 'd')
	    			$val = 'optionD';

	    		if($val == 'answer')
	    			$val = 'answer';

	    		if($val == 'difficulty')
	    			$val = 'level_id';

	    		if($val == 'type')
	    			$val = 'type_id';


			}

			/*

			$indexCounter = 0;

			

			*/


			/*
			get arrays index of all non matching values in the first rows and then unset all that are not required 
			*/


			$finalArray = array('queDesc', 'optionA', 'optionB', 'optionC', 'optionD', 'answer', 'type_id', 'level_id', 'category_id', 'section_id', 'user_id', 'scope', 'status', 'entity_id');

			$unmatchedArrayKeys = [];

			for($nk = 0; $nk < sizeof($csv[0]); $nk++ )
			{

			
				if(array_search($csv[0][$nk], $finalArray) === false)
				{

					array_push($unmatchedArrayKeys, $nk);

				};
		

			}


			foreach(array_keys($csv) as $key) {

				foreach ($unmatchedArrayKeys as $ukey => $removeIndex) {

					unset($csv[$key][$removeIndex]);
				
				}

			}



			/*

			must need to have index of all possibel values that I am going to be validating

			array('queDesc', 'optionA', 'optionB', 'optionC', 'optionD', 'answer', 'type_id', 'level_id', 'category_id', 'section_id', 'user_id', 'scope', 'status', 'entity_id')

			*/


			$quefIndex = array_search('queDesc', $csv[0]);
			$afIndex = array_search('optionA', $csv[0]);
			$bfIndex = array_search('optionB', $csv[0]);
			$cfIndex = array_search('optionC', $csv[0]);
			$dfIndex = array_search('optionD', $csv[0]);
			$answerfIndex = array_search('answer', $csv[0]);
			$typefIndex = array_search('type_id', $csv[0]);
			$levelfIndex = array_search('level_id', $csv[0]);
			$catfIndex = array_search('category_id', $csv[0]);
			$subjectfIndex = array_search('section_id', $csv[0]);
			$scopefIndex = array_search('scope', $csv[0]);
			$statusfIndex = array_search('status', $csv[0]);


			
			$keyFindex = 1;


			$colSize = sizeof($csv[0]);

			$radioAnwers = array('a', 'b', 'c', 'd');
			$booleanAnswers = array('a', 'b');

			foreach ($csv as $key => $value) {




				if($key > 0) 
				{

					if(sizeof($csv[$key]) != $colSize)
					{

						$data['message'] = " Mismatched column and data size at row". $keyFindex;
						return View::responseJson($data, 406);

					}


					$typeID = $csv[$key][$typefIndex];

					$queDesc = $csv[$key][$quefIndex];
					$optionA = $csv[$key][$afIndex];
					$optionB = $csv[$key][$bfIndex];
					$optionC = $csv[$key][$cfIndex];
					$optionD = $csv[$key][$dfIndex];
					$answer = strtolower($csv[$key][$answerfIndex]);

					$levelID = $csv[$key][$levelfIndex];


					if(strlen($optionA) > 255) 
					{
						$data['message'] = "Option A value exceeds 255 character limit at line" . $keyFindex;
					}

					if(strlen($optionB) > 255) 
					{
						$data['message'] = "Option A value exceeds 255 character limit at line" . $keyFindex;
					}

					if(strlen($optionC) > 255) 
					{
						$data['message'] = "Option A value exceeds 255 character limit at line" . $keyFindex;
					}

					if(strlen($optionD) > 255) 
					{
						$data['message'] = "Option A value exceeds 255 character limit at line" . $keyFindex;
					}


					if($answer == "")
					{
						$data['message'] = "Empty answer is not accetable at line " . $keyFindex;

						return View::responseJson($data, 406);

					}



					if($typeID == 1)
					{
						if(!in_array($answer, array('a', 'b')))
						{
							$data['message'] = "Answer is not acceptable for true/false at line " . $keyFindex;
							return View::responseJson($data, 406);

						}
					}



					if($typeID == 0 || $typeID == 2)
					{				

						/* options must not be empty */
						/* answer must not be empty */


						if (strpos($answer, ',') !== false && $typeID == 2 ) {

							/* multipe of abcd is provided */

							$chosenOptions = explode(',', $answer);

							foreach ($chosenOptions as $brokenAnswers) {


								if(array_search($brokenAnswers, $radioAnwers) === false)
								{
									
									$data['brokenanswer'] = $brokenAnswers;
									$data['message'] = "Answer is not accetable for type checkbox at line " . $keyFindex;
									return View::responseJson($data, 406);
								}
								
							}

						}

						else if(!in_array($answer, $radioAnwers)) {

							$data['message'] = "Provided answer is not accetable for type " . $type[$typeID] . " at line " . $keyFindex;
							return View::responseJson($data, 406);

						}



						if($optionA == null || $optionA == "" || strlen($optionA) < 0 )
						{

							$data['message'] = "Value missing option A at line " . $keyFindex;

							return View::responseJson($data, 406);

						}

						if($optionB == null || $optionB == "" || strlen($optionB) < 0 )
						{

							$data['message'] = "Value missing option B at line " . $keyFindex;
							return View::responseJson($data, 406);

						}

						if($optionC == null || $optionC == "" || strlen($optionC) < 0 )
						{

							$data['message'] = "Value missing option C at line " . $keyFindex;
							return View::responseJson($data, 406);

						}

						if($optionD == null || $optionD == "" || strlen($optionD) < 0 )
						{
							
							$data['message'] = "Value missing option B at line " . $keyFindex;
							return View::responseJson($data, 406);

						}

					}

					++$keyFindex;
	
				}
				
			}



			$csvColValue = array_values($csv[0]);

			$cols = array_shift($csv);




			$dataset['cols'] = $cols;
			$dataset['vals'] = $csv;


			$statusCode = 200;


			$data['length'] = sizeof($dataset['vals']);

			
			if($this->module->bulkQuestionUpload($dataset))
			{

				$data['message'] = sizeof($csv) . " Question uploaded successfully";
				$data['status'] = true;
				$statusCode = 200;
			}

			else {

				$data['message'] = "Fail while uploading questions";
				$data['status'] = false;
				$data['debug'] = $this->module->DB;
				$statusCode = 500;

			}

			return View::responseJson($data, $statusCode);

			/*

			level and type id is not subsituted on the file 


			*/


	}

}