<?php
$route = '/frequently-asked-questions/:frequently_asked_questions_id/';
$app->delete($route, function ($frequently_asked_questions_id) use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$frequently_asked_questions_id = prepareIdIn($frequently_asked_questions_id,$host);
	$frequently_asked_questions_id = mysql_real_escape_string($frequently_asked_questions_id);

	$Add = 1;
	$ReturnObject = array();

 	$request = $app->request();
 	$_POST = $request->params();

	$query = "DELETE FROM frequently_asked_questions WHERE frequently_asked_questions_id = " . $frequently_asked_questions_id;
	//echo $query . "<br />";
	mysql_query($query) or die('Query failed: ' . mysql_error());

	});
?>
