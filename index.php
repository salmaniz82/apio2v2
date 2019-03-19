<?php 

if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

require_once(ABSPATH .'bootstrap.php');

$route = new Route();

$routeList = false;


sleep(1);


$route->get('/', function() {
	$data = "Welcome to IO2 v3 API";
	view::responseJson($data, 200);
});


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



$route->get('/single-cat', 'moduletestCtrl@singleCat');

$route->get('/unlockTest', 'moduletestCtrl@unlockTest');


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

$route->post('/quiz', 'quizCtrl@save');

$route->get('/quiz-question-validity/{id}', 'quizCtrl@checkValidityCount');




$route->put('/quiz-enrollment-toggle/{id}', 'quizCtrl@enrollToggle');


$route->get('/quiz/progress/{id}', 'quizCtrl@quizProgress');

$route->get('/quiz/subjects/{id}', 'subjectCtrl@index');


$route->put('/quiz/distribution/{id}', 'subjectCtrl@updateDistro');


$route->get('/quiz/inspectanswers/{quiz_id}/{attempt_id}', 'answersCtrl@inpspectAnswers');

$route->get('/quiz/scorecard/{quiz_id}/{attempt_id}', 'answersCtrl@scoreCard');







/* STUDENT QUIZ LIST */

$route->get('/std-quiz-list', 'quizCtrl@studentQuizListHandler');

$route->post('/std-quiz-initiate', 'quizCtrl@studentQuizInitiate');

$route->get('/std-quiz-play/quiz_id/{quiz_id}/attempt_id/{attempt_id}', 'quizCtrl@studentQuizData');

$route->post('/std-patch-answers', 'answersCtrl@patchAnswers');


$route->get('/quiz-questions-by-quiz-id/{id}', 'quizQuestionsCtrl@getQuestionByQuizId');

$route->post('/quiz-question-allocate/{id}', 'quizQuestionsCtrl@allocateQuestions');

$route->put('/quiz-question-status-toggle/{quiz_id}/{subject_id}', 'quizQuestionsCtrl@qqStatusToggle');

$route->get('/quiz-questions/{id}', 'quizQuestionsCtrl@listMatchQuestions');

$route->get('/newquestion-available/{id}', 'quizQuestionsCtrl@questionSyncCheck');

$route->post('/quiz-question-sycnronize', 'quizQuestionsCtrl@processSynchronize');





$route->get('/questions', 'questionsCtrl@index');


$route->post('/questions', 'questionsCtrl@save');


$route->get('/question-section-summary', 'questionsCtrl@summaryCount');



$route->get('/enroll/{id}', 'enrollmentCtrl@listEnrolledStudents');

$route->post('/enroll', 'enrollmentCtrl@saveEnrollment');


$route->put('/enroll/retake/{id}', 'enrollmentCtrl@toggleRetake');




$route->get('/testQuizPlay/{id}', 'moduletestCtrl@testQuizPlayQuestion');












$route->get('/demo', 'userCtrl@demopage');








$route->otherwise( function() {

    $data['message'] = 'Request Not found';
    $data['status'] = false;
    View::responseJson($data, 404);

});

if($routeList)
{
	prx($route->registered);	
}
