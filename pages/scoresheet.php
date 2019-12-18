<?php 
$attempted = $data['attempt'][0];
$quiz = $data['quiz'][0];
$scoreCard = $data['scorecard'];
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Score Sheet</title>
	<link rel="stylesheet" type="text/css" href="<?=SITE_URL?>assets/css/pdf.css">
</head>
<body>


<div class="scorecardWrappper">	

	<img width="150" height="17" src="<?=SITE_URL?>assets/images/iskillmetrics-logo-email.jpg">

	<table width="100%" class="box-border-on">

		<tbody>

			<tr>
				<td colspan="2"><h2>Candidate</h2></td>
			</tr>


			<tr>
				<td>ID No</td>
				<td>1321321321321</td>
			</tr>
			<tr>
				
				<td>Candidate</td>
				<td><?=$attempted['name']?></td>
			</tr>

			<tr>
				
				<td>Email</td>
				<td><?=$attempted['email']?></td>
			</tr>

			<tr>
				<td>Attempted Date</td>
				<td><?=$attempted['formatedDate']?></td>
			</tr>

			<tr>
				<td>Attempted Time</td>
				<td><?=$attempted['formatedTime']?></td>
			</tr>
		</tbody>

	</table>


	<table width="100%" class="box-border-on">

		<tbody>	

			<tr>
				<td colspan="2">
					<h2>Quiz</h2>
				</td>
			</tr>

		<tr>
			
			<td colspan="2"><?=$quiz['title']?></td>
		</tr>

		<tr>
			<td>Code</td>
			<td><?=$quiz['code']?></td>
		</tr>

		<tr>
			<td>Category</td>
			<td><?=$quiz['category']?></td>
		</tr>

		<tr>
			<td>Duration</td>
			<td><?=$quiz['duration']?></td>
		</tr>

		<tr>
			<td>Questions</td>
			<td><?=$quiz['noques']?></td>
		</tr>

		<tr>
			<td>Total Score</td>
			<td><?=$quiz['maxScore']?></td>
		</tr>

		<tr>
			<td>Passing Score</td>
			<td><?=$quiz['minScore']?></td>
		</tr>

		<tr>
			<td>Type</td>
			<td>Static</td>
		</tr>

		</tbody>
		
	</table>




	
	


	
	<table width="100%" class="box-border-on">


		

		<thead>	

			<tr>
				<td colspan="6"><h2>Scorecard</h2></td>
			</tr>

				<tr style="text-align: left">
					<td>S.NO</td>
					<td>Subject</td>
					<td>Questions</td>
					<td>Max Score</td>
					<td>Obtained</td>
					<td>Per %</td>
				</tr>

		</thead>

		<tbody>

			<?php 
			

			for($i=0; $i<sizeof($scoreCard); $i++) {?>

			<tr>
				<td><?= $i +1; ?></td>
				<td><?= $scoreCard[$i]['subjects']?></td>
				<td><?= $scoreCard[$i]['quePerSection']?></td>
				<td><?= $scoreCard[$i]['maxScore']?></td>
				<td><?= $scoreCard[$i]['actualScore']?></td>
				<td><?= $scoreCard[$i]['per']?></td>
			</tr>


		<?php }?>
		</tbody>
		
	</table>



		<table width="100%" class="box-border-on">

		

		<tr>
			<td colspan="5">
				<h2>Overall</h2>		
			</td>
		</tr>

		<tr>
			<td>Obtained</td>
			<td>Per%</td>
			<td>GPA</td>
			<td>Grade</td>
			<td>Status</td>
		</tr>

		<tr>
			<td><?=$data['total']?></td>
			<td><?=$data['overAllPer']?>%</td>
			<td><?=$attempted['grade']?></td>
			<td><?=$attempted['gpa']?></td>
			<td><?php echo ($attempted['resultStatus'] == true) ? 'Pass' : 'Fail'; ?> </td>

		</tr>
		
	</table>


	</div><!-- scorecardWrappper -->


</body>
</html>