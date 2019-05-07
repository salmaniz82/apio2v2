<?php


Event::on('quiz-inintiate', function() {

	echo "this should fire when load fired <br />";

});


Event::on('quiz-submission', function() {

	echo "this should fire when load fired 2 <br />";

});


Event::on('before-list-quiz-questions', function() {

	echo "update notification <br />";

});


Event::on('new-user-creation', function() {

	echo "You can now trigger new email for the user <br />";

});






?>