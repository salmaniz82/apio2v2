<?php 
class interceptModule  extends appCtrl {


	public $DB;

	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'subjects';		
	}


	public function getSubjectScoreBeforeSaveWithAttemptId($attempt_id)
	{
		$sql = "SELECT attempt_id, quiz_id, enroll_id, subject_id, max(maxScore) as maxScore, actualScore, rightAnswers, quePerSection, pointPerQuestion FROM (

                        SELECT st.id AS 'attempt_id', en.quiz_id, st.enroll_id, 
                        que.section_id as 'subject_id', sub.points as 'maxScore', 
                        ((sub.points / sub.quePerSection ) * COUNT(sa.isRight) ) as 'actualScore', 
                        COUNT(sa.isRight) as 'rightAnswers', sub.quePerSection as  'quePerSection', (sub.points / sub.quePerSection) as pointPerQuestion  

                        from stdattempts st 

                        INNER JOIN enrollment en on en.id = st.enroll_id 
                        INNER JOIN stdanswers sa on sa.attempt_id = st.id 
                        INNER JOIN questions que on que.id = sa.question_id 
                        INNER JOIN subjects sub on sub.subject_id = que.section_id AND en.quiz_id = sub.quiz_id 
                        where st.id = $attempt_id AND sa.isRight = 1 GROUP BY st.id, en.quiz_id, sub.subject_id, que.section_id, sub.points, sub.quePerSection 
                        
                        UNION 
                        
                        SELECT st.id AS 'attempt_id', en.quiz_id, st.enroll_id, 
                        que.section_id as 'subject_id', sub.points as 'maxScore', 0 as 'actualScore', SUM(sa.isRight) as 'rightAnswers', sub.quePerSection, 
                        (sub.points / sub.quePerSection) as pointPerQuestion 

                        from stdattempts st 

                        INNER JOIN enrollment en on en.id = st.enroll_id 
                        INNER JOIN stdanswers sa on sa.attempt_id = st.id 
                        INNER JOIN questions que on que.id = sa.question_id 
                        INNER JOIN subjects sub on sub.subject_id = que.section_id AND en.quiz_id = sub.quiz_id 
                        where st.id = $attempt_id 
                        GROUP BY 
                        st.id, en.quiz_id, st.enroll_id, que.section_id, sub.points, sub.quePerSection 
                        HAVING SUM(sa.isRight) = 0 
                        
                        ) converge 
                        
                        GROUP BY converge.attempt_id, converge.quiz_id, converge.enroll_id, converge.subject_id, converge.actualScore, converge.quePerSection,
                            converge.rightAnswers, converge.pointPerQuestion";


                        if($data = $this->DB->rawSql($sql)->returnData($sql))
                        {
                        	return $data;
                        }

                        return false;

	}



	public function runPassProcedure($attemptID, $interceptPayload)
	{

		$lastLimit = $interceptPayload['lastLimit'];

		$allSubjectScore = $this->getSubjectScoreBeforeSaveWithAttemptId($attemptID);

		$hasMarkedAnswer = false;

		$iDebug = false;

		?>

				<?php if($iDebug) {?>

				<table border="1">

					<thead>
						<tr>
							<td>s.no</td>
							<td>Limit%</td>
							<td>subject</td>
							<td>maxScore</td>
							<td>obtained</td>
							<td>desired</td>

							<td>ScoreDiff</td>
							<td>tQue</td>
							<td>isRight</td>
							<td>PPQ</td>
							
							<td>QueLImit</td>

							<td>Output</td>
							<td>Final per%</td>
						</tr>

					</thead>


					


					<tbody>

					<?php } ?>
				

	
		<?php 

		$upperLimit = $lastLimit + 10;

		$valueRange = range($upperLimit, $lastLimit);
		
		for ($i=0; $i <= (sizeof($allSubjectScore) -1); $i++) { 



			$lastLimit =  $valueRange[array_rand($valueRange)];


			$obtainedScore = $allSubjectScore[$i]['actualScore'];

			$maxScore = $allSubjectScore[$i]['maxScore'];

			if($obtainedScore != 0)
			{
				$perObtained = ($obtainedScore / $maxScore) * 100;	
			}

			else {
				$perObtained = 0;
			}

			

			if($perObtained < $lastLimit)
			{

				$hasMarkedAnswer = true;

				$desiredCalucatedScore = ($maxScore * $lastLimit) / 100;
				
				$scoreDifference = ($desiredCalucatedScore - $obtainedScore);

				$pointPerQuestion = $allSubjectScore[$i]['pointPerQuestion'];

				$questionLimit = ceil($scoreDifference / $pointPerQuestion);


				?>

						<?php if($iDebug) {?>

							<tr>
						
							<td><?=$i;?></td>
							<td><?=$lastLimit?></td>
							<td><?=$allSubjectScore[$i]['subject_id'];?></td>
							<td><?=$maxScore?></td>
							<td><?=$obtainedScore;?></td>
							<td><?=$desiredCalucatedScore;?></td>
							<td><?=$scoreDifference;?></td>

							<td><?=$allSubjectScore[$i]['quePerSection']?></td>
							<td><?=$allSubjectScore[$i]['rightAnswers']?></td>
							<td><?=$pointPerQuestion?></td>
							<td><?=$questionLimit?></td>

							<td><?= ($pointPerQuestion * ($allSubjectScore[$i]['rightAnswers'] + $questionLimit))  ?></td>

							<td>  <?= ( ($allSubjectScore[$i]['rightAnswers'] + $questionLimit) * $pointPerQuestion ) / $maxScore * 100  ?>  </td>


							</tr>

						<?php } ?>

				<?php

					$this->markAnswerforPassing($attemptID, $allSubjectScore[$i]['subject_id'], $questionLimit);

				}
			
			}

				if($hasMarkedAnswer)
				{
					$this->upgradeAnswerForPassing($attemptID);	
				}	

			?>

				<?php if($iDebug) {?>

				</tbody>	
				</table>

				<?php } ?>

				<?php

	}

	public function runFailProcedure($attemptID, array $interceptPayload)
	{

		$lastLimit = $interceptPayload['lastLimit'];


		$allSubjectScore = $this->getSubjectScoreBeforeSaveWithAttemptId($attemptID);

		$hasMarkedAnswer = false;

		$iDebug = false;

		$totalPassingScore = $interceptPayload['minScore'];


		$totalMaxScore = $interceptPayload['maxScore'];

		$passingPercentage = ($totalPassingScore / $totalMaxScore) * 100;

		$LowerLimit = $lastLimit - 10;


		$valueRange = range($LowerLimit, $lastLimit);


		for ($i=0; $i <= (sizeof($allSubjectScore) -1); $i++) 
		{

			$lastLimit =  $valueRange[array_rand($valueRange)];

			
			$obtainedScore = $allSubjectScore[$i]['actualScore'];


			$maxScore = $allSubjectScore[$i]['maxScore'];


			if($obtainedScore != 0)
			{
				$perObtained = ($obtainedScore / $maxScore) * 100;	
			}

			else {
				$perObtained = 0;
			}


			if($perObtained > $passingPercentage)
			{
				/* if canidate has cross minimum passgin passing for the subject then activate */
				$hasMarkedAnswer = true;

				$desiredCalucatedScore = ($maxScore * $lastLimit) / 100;
			
				$scoreDifference = ($obtainedScore - $desiredCalucatedScore);			

				$pointPerQuestion = $allSubjectScore[$i]['pointPerQuestion'];

				$questionLimit = ceil($scoreDifference / $pointPerQuestion);
				
				$this->markAnswerforFailing($attemptID, $allSubjectScore[$i]['subject_id'], $questionLimit);

			}

		}
			
			if($hasMarkedAnswer)
				$this->downgradeAnswersForFailing($attemptID);
			
	}



	public function markAnswerforPassing($attempId, $sectionId, $limit)
	{

		$sql = "UPDATE stdanswers SET markedStatus='up' 	
		WHERE id IN ( SELECT id FROM (
        	SELECT sta.id FROM stdanswers sta 
            	INNER JOIN questions que on que.id = sta.question_id 
                WHERE 
            	sta.attempt_id = $attempId AND 
            	sta.isRight = 0 AND 
            	que.section_id = $sectionId 
            	
        		ORDER BY id ASC  
        		LIMIT $limit    
    		) tmp
		)";


		$this->DB->rawSql($sql);


	}


	public function markAnswerforFailing($attempId, $sectionId, $limit)
	{

		$sql = "UPDATE stdanswers SET markedStatus='down'
		WHERE id IN ( SELECT id FROM (
        	SELECT sta.id FROM stdanswers sta 
        		INNER JOIN questions que on que.id = sta.question_id 
                WHERE 
                sta.attempt_id = $attempId AND 
                que.section_id = $sectionId AND 
                sta.isRight = 1  AND 
                (que.type_id = 1 or que.type_id = 2)
        		ORDER BY id ASC  
        		LIMIT $limit 
    		) tmp
		)";

		$this->DB->rawSql($sql);

		
	}


	public function upgradeAnswerForPassing($attempt_id)
	{

		
		$sql = "UPDATE stdanswers stda 
		INNER JOIN questions que on que.id = stda.question_id  
		SET stda.answer = que.answer, stda.isRight = 1  
		WHERE stda.attempt_id = $attempt_id AND stda.markedStatus = 'up'";

		if($this->DB->rawSql($sql))
		{
			return $this->DB->connection->affected_rows;
		}

		return false;
	}


	public function downgradeAnswersForFailing($attempt_id)
	{
		

		$sql = "UPDATE stdanswers stda 

		INNER JOIN questions que on que.id = stda.question_id  SET stda.answer = 
		(case 
        when stda.answer = 'a' then 'b' 
        when stda.answer = 'b' then 'c' 
        when stda.answer = 'c' then 'd' 
        when stda.answer = 'd' then 'b' 
        else stda.answer  
        END), stda.isRight = 0  WHERE stda.attempt_id = $attempt_id AND stda.markedStatus = 'down' AND stda.isRight = 1 AND (que.type_id = 1 OR que.type_id = 2)"; 


       	if($this->DB->rawSql($sql))
		{
			return $this->DB->connection->affected_rows;
		}

		return false;

	}


}






	