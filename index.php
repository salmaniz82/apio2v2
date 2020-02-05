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
	
	$data['route'] = "iSkillMetrics API Root";
	$data['version'] = '1.0.0';
	$data['lastUpdate'] = "API Test Validation Completed";
	$data['dated'] = DATE('Y-m-d h:i:s');
	$data['author'] = "Salman Ahmed";
	$data['copyrights'] = utf8_encode("Copyright iSystematics.com © " . DATE('Y'));


	view::responseJson($data, 200);

});





$route->get('/dashboard', 'dashboardCtrl@router');

$route->get('/dasboard/activity?', 'dashboardCtrl@activity');

$route->put('/dashboard/markactivityinactive/{id}', 'dashboardCtrl@clearEndActivity');

$route->post('/login', 'jwtauthCtrl@login');

$route->post('/register', 'jwtauthCtrl@register');

$route->put('/changepassword/{id}', 'userCtrl@changePassword');





/*

$route->get('/routes', function() {

	global $routeList;
	$routeList = true;

});

$route->post('/routes', function() {

	global $routeList;
	$routeList = true;

});

$route->put('/routes', function() {

	global $routeList;
	$routeList = true;

});

$route->delete('/routes', function() {

	global $routeList;
	$routeList = true;

});

$route->get('/checktoken', 'jwtauthCtrl@check');

$route->get('/validate', 'jwtauthCtrl@validateToken');

$route->get('/masterdatalist', 'masterdataListController@masterDataRouter');

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



/* USERS */

$route->get('/profile', 'profileCtrl@index');

$route->get('/profile/entity-by-slug/{slug}', 'profileCtrl@entityProfilebyslug');

$route->post('/profile/slugavailable', 'profileCtrl@slugAvailable');


$route->post('/profile/logo', 'profileCtrl@updateLogo');


$route->put('/profile', 'profileCtrl@updateInfo');


$route->post('/profile', 'profileCtrl@save');


$route->get('/profile-authlogo', 'profileCtrl@authProfileLogo');




$route->get('/users', 'userCtrl@index');

$route->get('/users/{id}', 'userCtrl@single');

$route->post('/users', 'userCtrl@save');

$route->post('/users-autogenerate', 'userCtrl@autoGenerate');

$route->put('/users', 'userCtrl@update');

$route->put('/users/status-toggle/{id}', 'userCtrl@statusToggle');

$route->delete('/users/{id}', 'userCtrl@destroy');

$route->post('/register-enroll', 'userCtrl@registerEnroll');

$route->get('/my-users', 'userCtrl@entityTaggedUserList');

$route->post('/my-users/upload', 'userCtrl@uploadCandidates');

/* residue */

$route->post('/residue', 'residueCtrl@makeEntrypoint');

$route->put('/residue/doaction/{id}', 'residueCtrl@doaction');



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


$route->get('/cats', 'categoryCtrl@index');


$route->get('/category-flat-root', 'categoryCtrl@flatRootList');

$route->get('/cats/{id}', 'categoryCtrl@single');

$route->post('/cats', 'categoryCtrl@save');

$route->get('/cat-tree', 'categoryCtrl@catTree');

$route->delete('/cat/{id}', 'categoryCtrl@destroy');

$route->put('/cat/{id}', 'categoryCtrl@update');



/* SECTIONS */


/*

$route->get('/sections', 'sectionsCtrl@listAll');

$route->get('/sections-cat-combo', 'sectionsCtrl@catCombo');

$route->post('/sections', 'sectionsCtrl@save');

$route->delete('/sections/{id}', 'sectionsCtrl@destroy');

$route->put('/sections/{id}', 'sectionsCtrl@update');

*/


/* QUIZ */

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

$route->get('/dlsqualification/{id}', 'quizCtrl@isdlsQualified');

$route->put('/quiz-status-toggle/{id}', 'quizCtrl@statusToggle');

$route->get('/quiz/progress/{id}', 'quizCtrl@quizProgress');


/* quiz overview uses same data */

$route->get('/quiz/subjects/{id}', 'subjectCtrl@index');

$route->put('/quiz/distribution/{id}', 'subjectCtrl@updateDistro');

$route->get('/quiz/inspectanswers/{quiz_id}/{attempt_id}', 'answersCtrl@inpspectAnswers');

$route->get('/quiz/scorecard/{quiz_id}/{attempt_id}', 'answersCtrl@scoreCard');

$route->get('/quiz-wizard-preset', 'globalListCtrl@index');

$route->post('/quizwizardsubjects', 'subjectCtrl@wsubjects');


/* STUDENT QUIZ LIST */

$route->get('/std-self-progress/{id}', 'quizCtrl@canidateSelfProgressDetails');

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

$route->get('/question/{id}', 'questionsCtrl@singlequestion');

$route->put('/question/{id}', 'questionsCtrl@update');

$route->post('/questions', 'questionsCtrl@save');


$route->post('/questions-bulk', 'questionsCtrl@uploadCSV');



$route->get('/question-section-summary', 'questionsCtrl@summaryCount');

$route->put('/questions/status-toggle/{id}', 'questionsCtrl@statusToggle');

$route->get('/enroll/{id}', 'enrollmentCtrl@listEnrolledStudents');


$route->put('/enroll-reset/{id}', 'enrollmentCtrl@resetdefault');


$route->delete('/enroll/{id}', 'enrollmentCtrl@removeEnrollment');

$route->put('/enroll/schedule-datetime/{id}/{quiz_id}', 'enrollmentCtrl@udpateScheduleDatetime');

$route->post('/enroll', 'enrollmentCtrl@saveEnrollment');

$route->put('/enroll/retake/{id}', 'enrollmentCtrl@toggleRetake');

$route->get('/media', 'mediaCtrl@index');

$route->post('/media', 'mediaCtrl@save');

$route->get('/mediabyid/{id}', 'mediaCtrl@singleItemById');


/* option images */


$route->post('/queoptionimages/{optionLabel}', 'optionImagesCtrl@saveandreturn');



$route->get('/activeAct', 'quizCtrl@currentAct');

$route->post('/recordActivity', 'answersCtrl@activityHandler');

$route->put('/quiz/progress/recoverviaactivity/{id}', 'answersCtrl@recoverFromActivity');



/* INVITATIONS */



$route->post('/sendinvitation/{id}', 'invitationsCtrl@addInvitation');


$route->get('/invitation-quizzes/{id}', 'quizCtrl@invitationQuizListHandler');



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
$route->get('/checktimezone', 'moduleTestCtrl@checkTimeZoneTesting');
$route->get('/allocateTest', 'moduletestCtrl@testAllocation');
$route->get('/subAlloTest', 'moduletestCtrl@subjectAllocation');
$route->get('/single-cat', 'moduletestCtrl@singleCat');
$route->get('/unlockTest', 'moduletestCtrl@unlockTest');
$route->get('/testplay/{id}', 'moduleTestCtrl@testquizplayquestions');
$route->get('/test-encoding', 'moduletestCtrl@testenc');
$route->get('/testmedialink/{id}', 'moduletestCtrl@testMediaLink');
$route->get('/mtest', 'moduletestCtrl@dlstatus');
$route->get('/mtestroute', 'moduletestCtrl@teststartact');
$route->get('/testconfigmail', 'moduleTestCtrl@testMailWithConfigs');
$route->get('/dlsallocatetest/{id}', 'moduleTestCtrl@dlsAllocateTest');
$route->get('/xattempts/{id}', 'moduleTestCtrl@xattempts');
$route->get('/dlstesting/{id}', 'moduleTestCtrl@dlsSummaryReport');
$route->get('/testtickets/{id}', 'moduleTestCtrl@testTickets');
$route->get('/testmailmodule', 'moduleTestCtrl@emailmoduletest');
$route->post('/postmeta/{id}', 'moduleTestCtrl@postmeta');
$route->get('/dbsingle', 'moduleTestCtrl@dbsingle');
$route->get('/testphpmailer', 'moduleTestCtrl@testphpmailer');
$route->get('/testconfigmail', 'moduleTestCtrl@testConfigEmailStatus');
$route->get('/checkesc', 'moduleTestCtrl@checkESCvalues');

$route->get('/pdftest', 'pdfCtrl@testInstaller');

$route->post('/message', 'moduleTestCtrl@twilloPost');

$route->post('/servicepost', 'moduleTestCtrl@servicePost');

$route->get('/intercept/{id}', 'moduleTestCtrl@attemptIntercept');

*/



$route->post('/markandudpateanswers/{id}', 'answersCtrl@markedAnswerUpdates');




$route->get('/pages/signup?', 'emailTemplateCtrl@selfRegister');

$route->get('/pages/invite-exam/{id}', 'emailTemplateCtrl@inviteExam');

$route->get('/pages/changepassword?', 'emailTemplateCtrl@changePassword');

$route->get('/pages/registeredEnrolled?', 'emailTemplateCtrl@registerEnroll');

$route->get('/pages/registered?', 'emailTemplateCtrl@registered');

$route->get('/pages/enrolled?', 'emailTemplateCtrl@enrolled');

$route->get('/pages/examresult?', 'emailTemplateCtrl@examResult');


$route->get('/pages/invite-batch?', 'emailTemplateCtrl@inviteBatch');



$route->put('/intercept/{id}/{quizId}', 'enrollmentCtrl@defineIntercept');

$route->get('/pages/staticpage', 'pagesCtrl@staticPage');

$route->get('/pages-scoresheet/{quiz_id}/{attempt_id}', 'pagesCtrl@scoresheet');


$route->post('/scorecard-pdf/{quiz_id}/{attempt_id}', 'pdfCtrl@scorecardPDF');


$route->otherwise( function() {

    $data['message'] = 'Request Not found';
    $data['status'] = false;
    View::responseJson($data, 404);

});

if($routeList)
{
	prx($route->registered);	
}
