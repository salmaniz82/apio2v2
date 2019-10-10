<?php 

if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

require_once(ABSPATH .'bootstrap.php');

$route = new Route();

$routeList = false;



if(SITE_URL == 'http://api.io2v3.dvp/')
{
	sleep(1);		
}



$route->get('/', function() {
	
	$data = "Welcome to IO2 v3 API";
	view::responseJson($data, 200);

});


$route->get('/dashboard', 'dashboardCtrl@router');

$route->get('/dasboard/activity?', 'dashboardCtrl@activity');


$route->get('/routes', function() {

	global $routeList;
	$routeList = true;

});



$route->get('/checktoken', 'jwtauthCtrl@check');

$route->post('/login', 'jwtauthCtrl@login');

$route->post('/register', 'jwtauthCtrl@register');

$route->get('/validate', 'jwtauthCtrl@validateToken');


$route->put('/changepassword/{id}', 'userCtrl@changePassword');

$route->get('/hashpass', 'userCtrl@udpatePasswordHash');






$route->get('/qtest', function() {

	echo SITE_URL;
});

/*

$route->get('/cat/child-by-id/{catID}', 'categoryCtrl@dDirectChildrenById');

$route->get('/cat/child-by-name/{catName}', 'categoryCtrl@dDirectChildrenByName');

$route->get('/cat/4level', 'categoryCtrl@fourlevel');

$route->get('/cat/f-parent', 'categoryCtrl@findParent');

$route->get('/cat/f-children', 'categoryCtrl@findChildren');

$route->get('/cat/children-by-id/{catID}', 'categoryCtrl@allChildrenById');

$route->get('/cat/children-by-name/{catName}', 'categoryCtrl@allChildrenByName');

$route->get('/cat', 'categoryCtrl@flatJoinList');

$route->get('/cat-flat-list', 'categoryCtrl@flatList');

$route->delete('/cat/{id}', 'categoryCtrl@destroy');

$route->post('/cat/add-with-text', 'categoryCtrl@addWithText');

*/


$route->get('/cats', 'categoryCtrl@index');


$route->get('/category-flat-root', 'categoryCtrl@flatRootList');

$route->get('/cats/{id}', 'categoryCtrl@single');

$route->post('/cats', 'categoryCtrl@save');

$route->get('/cat-tree', 'categoryCtrl@catTree');

$route->delete('/cat/{id}', 'categoryCtrl@destroy');

$route->put('/cat/{id}', 'categoryCtrl@update');


/* USERS */

$route->get('/users', 'userCtrl@index');

$route->get('/users/{id}', 'userCtrl@single');

$route->post('/users', 'userCtrl@save');

$route->put('/users', 'userCtrl@update');

$route->put('/users/status-toggle/{id}', 'userCtrl@statusToggle');

$route->delete('/users/{id}', 'userCtrl@destroy');

$route->post('/register-enroll', 'userCtrl@registerEnroll');


/* ROLES*/


$route->get('/roles', 'rolesCtrl@index');

$route->put('/roles-reset-permission/{id}', 'userPermissionsCtrl@resetAllUserPermissionUnderRole');



/* PERMISSIONS */


$route->get('/permissions', 'permissionsCtrl@index');

$route->post('/permissions', 'permissionsCtrl@save');


$route->delete('/permissions/{id}', 'permissionsCtrl@delete');





/* ROLE PERMISSIONS */

$route->get('/role-permissions', 'rolePermissionsCtrl@index');

$route->post('/role-permissions', 'rolePermissionsCtrl@save');

$route->delete('/role-permissions/{id}/{role_id}/{permission_id}', 'rolePermissionsCtrl@delete');

$route->put('/role-permissions', 'rolePermissionsCtrl@statusToggle');


/* ROLE PERMISSIONS */

$route->get('/user-permissions/{id}', 'userPermissionsCtrl@index');

$route->post('/user-permissions', 'userPermissionsCtrl@saveCustomPermission');


$route->get('/user-permissions-concat/{id}', 'userPermissionsCtrl@permissionArrayList');

$route->put('/users-permissons/reset/{user_id}/{role_id}', 'userPermissionsCtrl@resetUserPermission');


$route->put('/users-permissons/status-toggle/{user_id}/{permission_id}', 'userPermissionsCtrl@userPrivatePermissionToggle');



/* CATEGORIES */



/* SECTIONS */


$route->get('/sections', 'sectionsCtrl@listAll');

$route->get('/sections-cat-combo', 'sectionsCtrl@catCombo');

$route->post('/sections', 'sectionsCtrl@save');

$route->delete('/sections/{id}', 'sectionsCtrl@destroy');

$route->put('/sections/{id}', 'sectionsCtrl@update');


$route->get('/quiz-global', 'quizCtrl@globals');

$route->get('/quiz', 'quizCtrl@index');

$route->get('/quiz/{id}', 'quizCtrl@single');

$route->delete('/quiz/{id}', 'quizCtrl@destroy');

$route->post('/quiz', 'quizCtrl@save');

$route->post('/quiz/wizard', 'quizCtrl@saveWithWizard');


$route->put('/quiz-update-datetime/{id}', 'quizCtrl@updateDateTime');




$route->get('/quiz-question-validity/{id}', 'quizCtrl@checkValidityCount');




$route->put('/quiz-enrollment-toggle/{id}', 'quizCtrl@enrollToggle');


$route->put('/quiz-option-toggle/{id}', 'quizCtrl@optionsToggle');


$route->put('/quiz-status-toggle/{id}', 'quizCtrl@statusToggle');



$route->get('/quiz/progress/{id}', 'quizCtrl@quizProgress');


$route->get('/quiz/subjects/{id}', 'subjectCtrl@index');


$route->put('/quiz/distribution/{id}', 'subjectCtrl@updateDistro');


$route->get('/quiz/inspectanswers/{quiz_id}/{attempt_id}', 'answersCtrl@inpspectAnswers');


$route->get('/quiz/scorecard/{quiz_id}/{attempt_id}', 'answersCtrl@scoreCard');



$route->get('/quiz-wizard-preset', 'globalListCtrl@index');

$route->post('/quizwizardsubjects', 'subjectCtrl@wsubjects');


/* STUDENT QUIZ LIST */

$route->get('/std-quiz-list', 'quizCtrl@studentQuizListHandler');


$route->get('/std-quiz-polling?', 'quizCtrl@pollingonfinish');




$route->post('/std-quiz-initiate', 'quizCtrl@studentQuizInitiate');

$route->get('/std-quiz-play/quiz_id/{quiz_id}/attempt_id/{attempt_id}', 'quizCtrl@studentQuizData');


$route->get('/std-quiz-play/dls/{quiz_id}/attempt_id/{attempt_id}', 'quizCtrl@prepareDls');

$route->post('/std-patch-answers', 'answersCtrl@patchAnswers');


$route->get('/quiz-questions-by-quiz-id/{id}', 'quizQuestionsCtrl@getQuestionByQuizId');

$route->post('/quiz-question-allocate/{id}', 'quizQuestionsCtrl@allocateQuestions');

$route->put('/quiz-question-status-toggle/{quiz_id}/{subject_id}', 'quizQuestionsCtrl@qqStatusToggle');

$route->get('/quiz-questions/{id}', 'quizQuestionsCtrl@listMatchQuestions');

$route->get('/newquestion-available/{id}', 'quizQuestionsCtrl@questionSyncCheck');

$route->post('/quiz-question-sycnronize', 'quizQuestionsCtrl@processSynchronize');





$route->get('/questions?', 'questionsCtrl@index');


$route->post('/questions', 'questionsCtrl@save');


$route->get('/question-section-summary', 'questionsCtrl@summaryCount');





$route->get('/enroll/{id}', 'enrollmentCtrl@listEnrolledStudents');


$route->put('/enroll/schedule-datetime/{id}/{quiz_id}', 'enrollmentCtrl@udpateScheduleDatetime');




$route->post('/enroll', 'enrollmentCtrl@saveEnrollment');


$route->put('/enroll/retake/{id}', 'enrollmentCtrl@toggleRetake');







$route->get('/media', 'mediaCtrl@index');

$route->post('/media', 'mediaCtrl@save');






$route->get('/mediabyid/{id}', 'mediaCtrl@singleItemById');




$route->get('/activeAct', 'quizCtrl@currentAct');


$route->post('/recordActivity', 'answersCtrl@activityHandler');







$route->get('/checktimezone', function() {

	function isValidTimezone($timezone) {
  		return in_array($timezone, timezone_identifiers_list());
	}

	var_dump(isValidTimezone('Asia/Karachi'));

	die();

	$db = new Database();

	$dbDate = $db->rawSql("SELECT attempted_at as dt from stdattempts where id = '345'")->returnData();

	echo "mysql date time <br>";

	echo $dbDate[0]['dt'];

	echo "php date time";

	echo Date('Y-m-d H:i:s');


	die();

	prx($_SERVER);

	

	/*
	echo "PHP datetime" . Date('Y-m-d H:i:s');
	$db = new Database();
	$datetimeDB = $db->rawSql("SELECT NOW() AS currentdatetime")->returnData();	
	var_dump($datetimeDB);
		
	git ls-files | xargs wc -l	

	*/

	$timezone_offset_minutes = 300;  // $_GET['timezone_offset_minutes']
	// Convert minutes to seconds
	$timezone_name = timezone_name_from_abbr("", $timezone_offset_minutes*60, false);
	// Asia/Kolkata
	echo $timezone_name;

	/*
		date_default_timezone_set($timezone_name);
	*/

});




$route->get('/batch/details/{id}', 'batchCtrl@batchDetails');

$route->get('/batches', 'batchCtrl@batchList');

$route->post('/batches', 'batchCtrl@save');

$route->get('/batches/{id}', 'batchCtrl@batchItems');

$route->get('/batches/elig/quiz', 'batchCtrl@listEligibleQuiz');


$route->get('/batches/candidate/overview/{id}/{candiateId}', 'batchCtrl@progressOverview');


$route->get('/batches/tagged/canidates/{id}', 'batchCtrl@taggedCanidates');


$route->post('/batches/enrollprocess/{id}', 'batchCtrl@enrollProcedure');



/* TESTING ROUTES */

/*
$route->get('/allocateTest', 'moduletestCtrl@testAllocation');
$route->get('/subAlloTest', 'moduletestCtrl@subjectAllocation');
$route->get('/addTickets', 'moduletestCtrl@testTickets');
$route->get('/single-cat', 'moduletestCtrl@singleCat');
$route->get('/unlockTest', 'moduletestCtrl@unlockTest');
$route->get('/testplay/{id}', 'moduleTestCtrl@testquizplayquestions');
$route->get('/test-encoding', 'moduletestCtrl@testenc');
$route->get('/testmedialink/{id}', 'moduletestCtrl@testMediaLink');
$route->get('/mtest', 'moduletestCtrl@dlstatus');
$route->get('/mtestroute', 'moduletestCtrl@teststartact');
*/


$route->get('/testemail', 'moduleTestCtrl@testphpmailer');

$route->get('/testconfigmail', 'moduleTestCtrl@testMailWithConfigs');


$route->get('/etemplate?', function() {


});


$route->get('/testfgcontents', 'moduleTestCtrl@testGetFileContents');

$route->get('/pages/signup?', 'emailtemplateCtrl@selfRegister');

$route->get('/pages/changepassword?', 'emailtemplateCtrl@changePassword');

$route->get('/pages/registeredEnrolled?', 'emailtemplateCtrl@registerEnroll');

$route->get('/pages/registered?', 'emailtemplateCtrl@registered');

$route->get('/pages/enrolled?', 'emailtemplateCtrl@enrolled');

$route->get('/pages/examresult?', 'emailtemplateCtrl@examResult');

$route->get('/pages/invite-exam?', 'emailtemplateCtrl@inviteExam');

$route->get('/pages/invite-batch?', 'emailtemplateCtrl@inviteBatch');





$route->otherwise( function() {

    $data['message'] = 'Request Not found';
    $data['status'] = false;
    View::responseJson($data, 404);

});

if($routeList)
{
	prx($route->registered);	
}
