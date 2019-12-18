<?php 
class quizQuestionsModule extends appCtrl {


	public $DB;


	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'quizquestions';		
	}


	public function quizQuestionsByQuizId($quiz_id)
	{
		$sql = "SELECT qz.id as quiz_id, que.id as question_id from quiz qz
				INNER JOIN questions que on qz.category_id = que.category_id 
				WHERE qz.id = $quiz_id AND que.status = 1 AND (que.quiz_id = $quiz_id OR que.quiz_id IS NULL)";

		if($questions = $this->DB->rawSql($sql)->returnData())
		{
			return $questions;
		}

			return false;
	}



	public function allocateQuestionsByQuizId($quiz_id, $entity_id)
	{



		$factorList = $this->listMaxFactor($quiz_id);	


		$sql = "INSERT INTO quizquestions (quiz_id, question_id)

			SELECT quiz_id, question_id FROM ( ";

			for($i = 0; $i<sizeof($factorList); $i++)
			{

				$subject_id = $factorList[$i]['subject_id'];
				$maxFactor = $factorList[$i]['maxFactor'];

			$sql .= " ( SELECT qz.id as quiz_id, que.id as question_id from quiz qz
			INNER JOIN questions que on qz.category_id = que.category_id 
			WHERE qz.id = $quiz_id AND que.status = 1 AND (que.quiz_id = $quiz_id OR que.quiz_id IS NULL) 
			AND (que.entity_id = $entity_id OR que.entity_id IS NULL)
			AND que.consumed <= qz.threshold  
			AND que.section_id = $subject_id

			ORDER BY que.level_id ASC LIMIT $maxFactor ) ";


			if($i + 1 < sizeof($factorList))
			{
				$sql .= " UNION "; 
			}

			}

			$sql .= " ) coverge";


			


		if($this->DB->rawSql($sql))
		{
			return $this->DB->connection->affected_rows;
		}

			return false;

			
	}


	public function reSyncQuestions($quiz_id)
	{


		$sql = "INSERT IGNORE INTO quizquestions (quiz_id, question_id)
			SELECT qz.id as quiz_id, que.id as question_id from quiz qz
			INNER JOIN questions que on qz.category_id = que.category_id 
			WHERE qz.id = $quiz_id AND que.status = 1 
			AND que.consumed <= qz.threshold 
			AND (que.quiz_id = $quiz_id OR que.quiz_id IS NULL)";
			$this->DB->rawSql($sql);
			return $this->DB->connection->affected_rows;
		
	}


	public function synchronizeCheck($quiz_id, $entity_id = 0)
	{
		/*
			pick only non matching rows from questions which are not in quizquestions
			{
 	   			"queIDs": "99,100,203",
    			"noQues": "3",
    			"status": true,
    			"message": "3 New Questions Available"
			}
		*/

		$rows = $this->calclauteSynPerSubject($quiz_id);
		/*
		$sql = "SELECT GROUP_CONCAT(que.id) as question_id, count(que.id) as quecount from quiz qz
				INNER JOIN questions que on qz.category_id = que.category_id 
				WHERE qz.id = $quiz_id AND que.status = 1 AND (que.quiz_id = $quiz_id OR que.quiz_id IS NULL) 
				AND (que.entity_id = $entity_id OR que.entity_id IS NULL) 
				AND que.consumed <= qz.threshold 
				AND que.section_id IN (SELECT subject_id from subjects where quiz_id = $quiz_id) 
    			AND que.id NOT IN (SELECT question_id from quizquestions where quiz_id = $quiz_id)";
    	*/


        $newSql = " SELECT GROUP_CONCAT(question_id) as question_id, count(question_id) as quecount from ( ";

        for($i = 0; $i<sizeof($rows); $i++)
        { 


            $subject_id = $rows[$i]['section_id'];
            $pullLimit = $rows[$i]['pullLimit'];
            $limit = ($pullLimit < 0) ? 0 : $pullLimit;

            $newSql .= " ( 
                SELECT que.id as question_id from quiz qz 
                INNER JOIN questions que on que.category_id = qz.category_id 
                WHERE qz.id = $quiz_id AND que.status = 1 
                AND (que.quiz_id = $quiz_id OR que.quiz_id IS NULL) 
                AND (que.entity_id = $entity_id OR que.entity_id IS NULL) 
                AND que.consumed <= qz.threshold 
                AND que.section_id = $subject_id  
                AND que.id NOT IN (SELECT question_id from quizquestions where quiz_id = $quiz_id) LIMIT $limit 
                ) ";

                if($i + 1 < sizeof($rows))
                {
                    $newSql .= " UNION "; 
                }

        }

	        $newSql .= " ) converge";

	        

    		$data = $this->DB->rawSql($newSql)->returnData();

			if($data[0]['quecount'] != 0)
			{
				return $data;	
			}

				return false;
	
	}


	public function listMatchQuestions($quiz_id)
	{
		$sql = "SELECT qq.id, qq.status as 'qqStatus', que.id as questionId, que.queDesc, que.optionA, que.optionB, que.optionC, que.optionD,
			que.consumed as 'hits', SUBSTRING(fnStripTags(que.queDesc), 1,350) as excerptDesc, 
			(case when CHAR_LENGTH(fnStripTags(que.queDesc)) > 350 then true else false end) as hasExcerpt, 
			cat.name as category, sub.name as 'subject', que.section_id as 'subject_id',   
			lvl.levelEN, lvl.levelAR, 
			typ.typeEN, 
			que.answer,

			que.scope as 'scope' 

			from quizquestions qq 
			INNER JOIN questions que on que.id = qq.question_id 
			INNER JOIN categories cat on cat.id = que.category_id 
			INNER JOIN categories sub on sub.id = que.section_id 
			INNER JOIN level lvl on que.level_id = lvl.id 
			INNER JOIN type typ on typ.id = que.type_id 
			WHERE qq.quiz_id = $quiz_id ORDER BY qq.status DESC, que.consumed DESC";

			if($questions = $this->DB->rawSql($sql)->returnData())
			{
				return $questions;
			}

				return false;
	}


	public function SynchronizeHandler($quiz_id, $queIDs)
	{


		$sql = "INSERT INTO quizquestions (quiz_id, question_id)
			SELECT $quiz_id as quiz_id, que.id as question_id from questions que 
			WHERE que.id IN($queIDs)";


			if($this->DB->rawSql($sql))
			{
				return $this->DB->connection->affected_rows;
			}

				return false;

	}


	public function newSyncAddedQuestions($quiz_id, $queIDs)
	{


		$sql = "SELECT qq.id, qq.status as 'qqStatus', que.id as questionId, que.queDesc, que.optionA, que.optionB, que.optionC, que.optionD,
				que.consumed as 'hits', 
			cat.name as category, 
			lvl.levelEN, lvl.levelAR, 
			typ.typeEN, 
			que.answer,

			que.scope as 'scope' 


			from quizquestions qq 
			INNER JOIN questions que on que.id = qq.question_id 
			INNER JOIN categories cat on cat.id = que.category_id 
			INNER JOIN level lvl on que.level_id = lvl.id 
			INNER JOIN type typ on typ.id = que.type_id 
			WHERE qq.quiz_id = $quiz_id AND qq.question_id IN ($queIDs)";

			if($questions = $this->DB->rawSql($sql)->returnData())
			{
				return $questions;
			}

				return false;

	}

	public function autoSyncPrivateQuizQuestions($dataPayload)
	{

			if($last_id = $this->DB->insert($dataPayload))
			{
				return $last_id;
			}
			
			return false;

	}


	public function appliedQuizSections($quiz_id)
	{

		/*
		$sql = "SELECT subject_id, quePerSection, points from subjects WHERE quiz_id = $quiz_id AND quePerSection > 0 AND points > 0";
		*/
				
			$sql = "SELECT
			    que.section_id AS 'subject_id',
			    sec.name AS 'subjects',
			    sub.quePerSection AS 'quePerSection',
			    sub.points AS 'points',
			    COUNT(*) AS 'subQueAllocated'
			FROM
			    quizquestions qq
			INNER JOIN questions que ON
			    que.id = qq.question_id
			INNER JOIN categories cat ON
			    cat.id = que.category_id
			INNER JOIN categories sec ON
			    sec.id = que.section_id
			INNER JOIN subjects sub ON
			    sub.subject_id = que.section_id
			WHERE
			    qq.quiz_id = $quiz_id AND sub.quiz_id = $quiz_id AND qq.status = 1 AND que.section_id IN(
			    SELECT
			        subject_id
			    FROM
			        subjects
			    WHERE
			        quiz_id = $quiz_id AND quePerSection > 0 AND points > 0
			)
			GROUP BY
			    que.section_id,
			    sec.name,
			    sub.quePerSection,
			    sub.points";
		

		if($subjects = $this->DB->rawSql($sql)->returnData())
		{
			return $subjects;
		}

			return false;

	}



	public function listQuizPlayQuestions($quiz_id, $reqQues)
	{

			$sql = "SELECT qq.id, qq.status as 'qqStatus', que.id as questionId, que.type_id, que.queDesc, que.optionA, que.optionB, que.optionC, que.optionD,
			cat.name as category, 
            sec.name as 'subDecipline',
			lvl.levelEN, lvl.levelAR, 
			typ.typeEN 
			
			from quizquestions qq 
			INNER JOIN questions que on que.id = qq.question_id 
			INNER JOIN categories cat on cat.id = que.category_id 
            INNER JOIN categories sec on que.section_id = sec.id 
			INNER JOIN level lvl on que.level_id = lvl.id 
			INNER JOIN type typ on typ.id = que.type_id 
			WHERE qq.quiz_id = $quiz_id AND qq.status = 1 
			ORDER BY sec.name, RAND() LIMIT $reqQues";

			if($questions = $this->DB->rawSql($sql)->returnData())
			{
				return $questions;
			}

				return false;

	}


	public function listQuizPlayQuestionsDistro($quiz_id)
	{

			$subjects = $this->appliedQuizSections($quiz_id);
			$studentId = jwACL::authUserId();
			$questionsArray = [];
			$counter = 1;


			foreach ($subjects as $key => $subj) {

			$subject_id = (int) $subj['subject_id'];

			$queFromSection = (int) $subj['quePerSection'];

			$subQueAllocated = (int) $subj['subQueAllocated'];


			if($foundUSedQueIds = $this->fetchQuestionsIdsOnRetake($studentId, $quiz_id, $subject_id) )
			{

				$countQuesIds = (int) sizeof($foundUSedQueIds);
				$availablePoolSize = (int) ($subQueAllocated - $countQuesIds);

				if( ($queFromSection == $subQueAllocated) || ($countQuesIds == $subQueAllocated)  )
				{
					// all exhausted or alloacted just as required
					$idsToFilerOut = 0;
					
				}
				else if ($availablePoolSize >= $queFromSection ) 
				{
					//all the room all can be stripped out
					$idsToFilerOut = $foundUSedQueIds;
				}

				else if ( $availablePoolSize < $queFromSection )
				{
					// little room available 
					$noOfCanBeStripped  =  $subQueAllocated - $queFromSection;
					shuffle($foundUSedQueIds);
					$idsToFilerOut = array_slice($foundUSedQueIds, 0, $noOfCanBeStripped);
				}



			}
			else {
				$idsToFilerOut = 0;
			}


			if($idsToFilerOut != 0)
			{			
				sort($idsToFilerOut);
				$idsToFilerOut = "'" . implode("','", $idsToFilerOut) . "'";
			}



			$sql = "SELECT qq.id, qq.status as 'qqStatus', que.id as questionId, que.type_id, que.queDesc, que.optionA, que.optionB, que.optionC, que.optionD,
			cat.name as category, 
            sec.name as 'subDecipline',
			lvl.levelEN, lvl.levelAR, 
			typ.typeEN 
			
			from quizquestions qq 
			INNER JOIN questions que on que.id = qq.question_id 
			INNER JOIN categories cat on cat.id = que.category_id 
            INNER JOIN categories sec on que.section_id = sec.id 
			INNER JOIN level lvl on que.level_id = lvl.id 
			INNER JOIN type typ on typ.id = que.type_id 
			WHERE que.id NOT IN ({$idsToFilerOut}) AND 
			qq.quiz_id = $quiz_id AND 
			qq.status = 1 AND 
			que.section_id = $subject_id  
			ORDER BY RAND() LIMIT $queFromSection";



			if($questions = $this->DB->rawSql($sql)->returnData())
			{
				foreach ($questions as $key => $item) {				
					array_push($questionsArray, $item);	
				}
			}

			}
			
			
			return $questionsArray;
			
	}



	public function quizAllocatedQuestionsSubjects($quiz_id)
	{


		$sql = "SELECT que.section_id as 'subject_id', sec.name as 'subjects' from quizquestions qq 
		inner join questions que on que.id = qq.question_id inner join categories cat on cat.id = que.category_id 
		inner join categories sec on sec.id = que.section_id 
		inner join subjects sub on sub.subject_id = que.section_id 
		where qq.quiz_id = $quiz_id AND sub.quiz_id = $quiz_id 
		AND que.section_id IN (SELECT subject_id from subjects where quiz_id = $quiz_id) GROUP BY sec.id";


		if($qqSubjects = $this->DB->rawSql($sql)->returnData())
		{
			return $qqSubjects;
		}

		return false;


	}


	public function statusToggle($dataPayload, $qqid)
	{


		if($this->DB->update($dataPayload, $qqid))
		{
			return true;
		}

		return false;
			

	}



	public function getQuestionMedia($question_id)
	{

		$sql = "SELECT m.type, qm.qmlabel as 'title', m.filepathurl from media m
		INNER JOIN quemedia qm on m.id = qm.media_id where qm.question_id = $question_id";
		return $this->DB->rawSql($sql)->returnData();

	}


	public function thresholdValidation($quizId)
	{
		$sql  = "SELECT count(qq.id) as 'expired',
			qz.threshold  
			from quizquestions qq 
			INNER JOIN questions que on que.id = qq.question_id 
            INNER JOIN quiz qz on qz.id = qq.quiz_id 
			WHERE qq.status = 1 AND qq.quiz_id = $quizId  
            AND que.consumed > qz.threshold";

            if($row = $this->DB->rawSql($sql)->returnData())
            {
            	return $row[0];	
            }

	}

	public function globalThresholdCount($quizId)
	{
		$globalThreshold = GLOBAL_Threshold;

		$sql  = "SELECT count(qq.id) as 'expired'
			
			from quizquestions qq 
			INNER JOIN questions que on que.id = qq.question_id 
            INNER JOIN quiz qz on qz.id = qq.quiz_id 
			WHERE qq.status = 1 AND qq.quiz_id = $quizId  
            AND que.consumed > $globalThreshold";

            if($row = $this->DB->rawSql($sql)->returnData())
            {
            	return $row[0]['expired'];
            }

            return 0;
	}

	public function globalThresholdByQuizId($quiz_id)
	{

		/*
		disable questions global status when crossed global threshold limit
		fire prerior to on question listings
		- before allocation
		- before listings
		- before synchronization check

		*/
		$globalThreshold = GLOBAL_Threshold;
		$sql = "UPDATE questions que INNER JOIN quiz qz on que.quiz_id = qz.id 
		SET que.status = 0 
		WHERE que.quiz_id = $quiz_id AND que.consumed > $globalThreshold";
		$this->DB->rawSql($sql);
		return $this->DB->connection->affected_rows;
	}


	public function fetchQuestionsIdsOnRetake($studentId, $quizId, $subjectId)
	{
		
		$sql = "SELECT MAX(sa.question_id) as 'question_id' from stdattempts sta 
				INNER JOIN enrollment en on en.id = sta.enroll_id
				INNER JOIN stdanswers sa on sa.attempt_id = sta.id 
				INNER JOIN questions que on que.id = sa.question_id 
				WHERE 
					en.student_id = $studentId AND 
    				en.quiz_id = $quizId AND 
    				en.attempts > 0 AND
    				que.section_id = $subjectId  
    			GROUP BY sa.question_id 
				order by sa.question_id DESC";
			if($row = $this->DB->rawSql($sql)->returnData())
			{
				$idList = array();

				foreach ($row as $key => $value) {

					array_push($idList, $value['question_id']);
					
				}

				return $idList;

				
			}

			return false;

	}



	public function listQuizPlayQuestionsDLS($quiz_id, $studentId)
	{

			$subjects = $this->appliedQuizSections($quiz_id);
			//$studentId = jwACL::authUserId();
			$questionsArray = [];

			$levels = array('easy'=> 1, 'medium'=> 2, 'difficult'=> 3);

			$collections = [];

			foreach ($subjects as $key => $subj) {

			$collections[$subj['subjects']]['composite'] = array('easy' => [], 'medium'=> [], 'difficult'=> []);				

			$collections[$subj['subjects']]['meta'] = array('easy' => 0, 'medium'=> 0, 'difficult'=> 0);
			

			$subject_id = (int) $subj['subject_id'];
			
			$queFromSection = (int) $subj['quePerSection'];

			$subQueAllocated = (int) $subj['subQueAllocated'];


			if($foundUSedQueIds = $this->fetchQuestionsIdsOnRetake($studentId, $quiz_id, $subject_id) )
			{

				$countQuesIds = (int) sizeof($foundUSedQueIds);
				$availablePoolSize = (int) ($subQueAllocated - $countQuesIds);

				if( ($queFromSection == $subQueAllocated) || ($countQuesIds == $subQueAllocated)  )
				{
					// all exhausted or alloacted just as required
					$idsToFilerOut = 0;
						
				}
				else if ($availablePoolSize >= $queFromSection ) 
				{
					//all the room all can be stripped out
					$idsToFilerOut = $foundUSedQueIds;
				}

				else if ( $availablePoolSize < $queFromSection )
				{
					// little room available 
					$noOfCanBeStripped  =  $subQueAllocated - $queFromSection;
					shuffle($foundUSedQueIds);
					$idsToFilerOut = array_slice($foundUSedQueIds, 0, $noOfCanBeStripped);
				}



			}
			else {
				$idsToFilerOut = 0;
			}


			if($idsToFilerOut != 0)
			{			
				sort($idsToFilerOut);
				$idsToFilerOut = "'" . implode("','", $idsToFilerOut) . "'";
			}



			// disable usedx times filter out questions
			$idsToFilerOut = 0;


			// here we need to repeat it 3 times for each level and store that array based on level 

			foreach ($levels as $levelLabel => $levelKeyDB) 
			{


				$levelKeyDB;

				$sql = "SELECT qq.id, qq.status as 'qqStatus', que.id as questionId, que.type_id, que.queDesc, que.optionA, que.optionB, que.optionC, que.optionD, que.answer as stamp,  
				cat.name as category, 
            	sec.name as 'subDecipline',
				lvl.levelEN, lvl.levelAR, 
				typ.typeEN 
			
				from quizquestions qq 
				INNER JOIN questions que on que.id = qq.question_id 
				INNER JOIN categories cat on cat.id = que.category_id 
            	INNER JOIN categories sec on que.section_id = sec.id 
				INNER JOIN level lvl on que.level_id = lvl.id 
				INNER JOIN type typ on typ.id = que.type_id 
				WHERE que.id NOT IN ({$idsToFilerOut}) AND 
				que.level_id = $levelKeyDB AND 
				qq.quiz_id = $quiz_id AND 
				qq.status = 1 AND 
				que.section_id = $subject_id  
				ORDER BY RAND() LIMIT $queFromSection";

				if($questions = $this->DB->rawSql($sql)->returnData())
				{
				
					 $questionCount = $this->DB->noRows;

					for($i=0; $i<sizeof($questions); $i++)
					{


						$question_id = $questions[$i]['questionId'];
							
						if($media = $this->getQuestionMedia($question_id))
						{					
							
							$questions[$i]['media'] = $media;
						}

					}



					if($levelKeyDB == 1)
					{					
						$collections[$subj['subjects']]['composite']['easy'] = $questions;
						$collections[$subj['subjects']]['meta']['easy'] = $questionCount;
					}

					else if($levelKeyDB == 2)
					{

						
						$collections[$subj['subjects']]['composite']['medium'] = $questions;
						$collections[$subj['subjects']]['meta']['medium'] = $questionCount;
					}

					else if ($levelKeyDB == 3) {

						$collections[$subj['subjects']]['composite']['difficult'] = $questions;
						$collections[$subj['subjects']]['meta']['difficult'] = $questionCount;

					}


				}

				}

			}	
		
			return ['collections' => $collections, 'distribution' => $subjects];
			
	}



	public function listMaxFactor($quiz_id)
	{

		$sql = "SELECT round(qz.maxAllocation / qz.noques * sub.quePerSection) as maxFactor, sub.subject_id from subjects sub 
			INNER JOIN quiz qz on qz.id = sub.quiz_id 
			where quiz_id = $quiz_id";

			return $this->DB->rawSql($sql)->returnData();

	}



	public function calclauteSynPerSubject($quiz_id)
	{


		$sql  = "SELECT que.section_id, cat.name, COUNT(qq.id) as allocated, ROUND(qz.maxAllocation / qz.noques * sub.quePerSection) as limitPerSubject, qz.noques, qz.maxAllocation, sub.quePerSection, 
			ROUND(qz.maxAllocation / qz.noques * sub.quePerSection - COUNT(qq.id)) as pullLimit 
			from quizquestions qq 
			INNER JOIN quiz qz on qz.id = qq.quiz_id 
			INNER JOIN questions que on qq.question_id = que.id 
			INNER JOIN categories cat on cat.id = que.section_id 
			INNER JOIN subjects sub on sub.quiz_id = qz.id AND sub.subject_id = que.section_id 
			where qq.quiz_id = $quiz_id AND qq.status = 1
			GROUP BY que.section_id";
			return $this->DB->rawSql($sql)->returnData();

	}



	public function dlsQuizQueAllocatedSummary($quiz_id)
	{


		if(SITE_URL == 'http://api.io2v3.dvp/')
		{

			


			$sql = "SELECT sub.name as 'subject', que.section_id as 'subject_id',  
			lvl.levelEN, count(que.section_id) AS 'queLevelCount', qsub.quePerSection,
            (SELECT count(id) from subjects where quiz_id = $quiz_id) as noSubjects, 
			(case when count(que.section_id) >= (SELECT quePerSection from subjects where quiz_id = $quiz_id AND subject_id = que.section_id) then true else false end) as isDlsStatus  
			from quizquestions qq 
			INNER JOIN questions que on que.id = qq.question_id 
			INNER JOIN categories cat on cat.id = que.category_id 
			INNER JOIN categories sub on sub.id = que.section_id 
			INNER JOIN level lvl on que.level_id = lvl.id 
			INNER JOIN type typ on typ.id = que.type_id 
			INNER JOIN quiz qz on qz.id = qq.quiz_id 
			INNER JOIN subjects qsub on que.section_id = qsub.subject_id  
			WHERE qq.quiz_id = $quiz_id  AND qsub.quiz_id = $quiz_id 
			GROUP BY sub.name, lvl.levelEN, que.section_id
			ORDER BY que.section_id, lvl.levelEN";

		}


		else {

			$sql = "SELECT sub.name as 'subject', que.section_id as 'subject_id',  
			lvl.levelEN, count(que.section_id) AS 'queLevelCount', qsub.quePerSection,
            (SELECT count(id) from subjects where quiz_id = $quiz_id) as noSubjects, 
			(case when count(que.section_id) >= (SELECT quePerSection from subjects where quiz_id = $quiz_id AND subject_id = que.section_id) then true else false end) as isDlsStatus  
			from quizquestions qq 
			INNER JOIN questions que on que.id = qq.question_id 
			INNER JOIN categories cat on cat.id = que.category_id 
			INNER JOIN categories sub on sub.id = que.section_id 
			INNER JOIN level lvl on que.level_id = lvl.id 
			INNER JOIN type typ on typ.id = que.type_id 
			INNER JOIN quiz qz on qz.id = qq.quiz_id 
			INNER JOIN subjects qsub on que.section_id = qsub.subject_id  
			WHERE qq.quiz_id = $quiz_id  AND qsub.quiz_id = $quiz_id 
			GROUP BY sub.name, lvl.levelEN, que.section_id, qsub.quePerSection 
			ORDER BY que.section_id, lvl.levelEN";

		}



			if($dlsSummary = $this->DB->rawSql($sql)->returnData())
			{
				
				return $dlsSummary;
			}

			return false;

	}


	public function dlsQualificationCheck($allocatedSummary)
	{

		$output = [];
        $noOfSubjetcs = (int) $allocatedSummary[0]['noSubjects'];
        $rowsCount = sizeof($allocatedSummary);



        if($rowsCount % 3 != 0 &&  $rowsCount != ($noOfSubjetcs * 3))
        {

            // all sections have rows no missing subject difficuty

            $output = array(

                'message' => 'All 3 difficulty levels must have alleast equal required no. of questions for each subject',
                'status' => false
            );

            return  $output;

        }


        $dlsStatusCount = 0;

        foreach ($allocatedSummary as $key => $item) {


            if($item['isDlsStatus'] == 0)
            {

            	$dlsStatusCount += 1;

                $missingCount = $item['quePerSection'] - $item['queLevelCount'];
                $message = $item['subject'] . " required " . $missingCount . " more Questions with " . $item['levelEN'] . " level";

                $errorMessage = array(

                    'message' => $message,
                    'status' => false
                );

                $output[] = $errorMessage;

            }


           }



           if($dlsStatusCount === 0)
           {
           		return true;
           }

           return $output;


	}




	public function isDlsQualifiedNitroMode($quiz_id)
	{




		if( !$allocatedSummary = $this->dlsQuizQueAllocatedSummary($quiz_id) )
		{

			return false;

		}


		$output = [];
        $noOfSubjetcs = (int) $allocatedSummary[0]['noSubjects'];
        $rowsCount = sizeof($allocatedSummary);



        if($rowsCount % 3 != 0 &&  $rowsCount != ($noOfSubjetcs * 3))
        {

            return false;

        }



	       foreach ($allocatedSummary as $key => $item) 
	       {

		        if($item['isDlsStatus'] == 0)
		        {
		          	return false;

		        }

	        }


	        return true;


	}



	public function allocateDLSQuestionsByQuizId($quiz_id, $entity_id)
	{


		/*
		- get maxXFactor by xAllocation / noques  
		- get subjectsId and their quePerSection
		- sectionLimit = quePerSection * maxXFactor 
		*/

		/*

		$sql = "INSERT INTO quizquestions (quiz_id, question_id)
			SELECT qz.id as quiz_id, que.id as question_id from quiz qz
			INNER JOIN questions que on qz.category_id = que.category_id 
			WHERE qz.id = $quiz_id AND que.status = 1 AND (que.quiz_id = $quiz_id OR que.quiz_id IS NULL) 
			AND (que.entity_id = $entity_id OR que.entity_id IS NULL)
			AND que.consumed <= qz.threshold  
			AND que.section_id IN (SELECT subject_id from subjects where quiz_id = $quiz_id) LIMIT $maxAllocation";

			*/

		$factorList = $this->listMaxFactor($quiz_id);	

		$levels = [1,2,3];


		$sql = "INSERT INTO quizquestions (quiz_id, question_id) 


		SELECT quiz_id, question_id FROM ( ";

			for($i = 0; $i<sizeof($factorList); $i++)
			{

				$subject_id = $factorList[$i]['subject_id'];
				$maxFactor = $factorList[$i]['maxFactor'];
			
			$levelIteration = 1;	
				
	        for($t = 0; $t<sizeof($levels);  $t++) {

	        	$sql .= " ( SELECT qz.id as quiz_id, que.id as question_id from quiz qz
			INNER JOIN questions que on qz.category_id = que.category_id 
			WHERE qz.id = $quiz_id AND que.status = 1 AND (que.quiz_id = $quiz_id OR que.quiz_id IS NULL) 
			AND (que.entity_id = $entity_id OR que.entity_id IS NULL)
			AND que.consumed <= qz.threshold  
			AND que.section_id = $subject_id 
			AND que.level_id = $levels[$t]   

			LIMIT $maxFactor ) ";

			

			if($t + 1 < sizeof($levels) ) 
			{

				$sql .= " UNION "; 

			}

            
            
            
	       	}



			if($i + 1 < sizeof($factorList) )
			{
				$sql .= " UNION "; 
			}

			}

			$sql .= " ) coverge";


			


		if($this->DB->rawSql($sql))
		{
			return $this->DB->connection->affected_rows;
		}

			return false;

			
	}



}


/*
calculate available room for each subject as per setLimit 
SELECT que.section_id, cat.name, COUNT(qq.id) as allocated, ROUND(qz.maxAllocation / qz.noques * sub.quePerSection) as limitPerSubject, qz.noques, qz.maxAllocation, sub.quePerSection, 
ROUND(qz.maxAllocation / qz.noques * sub.quePerSection - COUNT(qq.id)) as pullLimit 
from quizquestions qq 
INNER JOIN quiz qz on qz.id = qq.quiz_id 
INNER JOIN questions que on qq.question_id = que.id 
INNER JOIN categories cat on cat.id = que.section_id 
INNER JOIN subjects sub on sub.quiz_id = qz.id AND sub.subject_id = que.section_id 
where qq.quiz_id = 126 AND qq.status = 1
GROUP BY que.section_id;
*/