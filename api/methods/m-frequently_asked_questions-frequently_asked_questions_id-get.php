<?php
$route = '/frequently-asked-questions/:frequently_asked_questions_id/';
$app->get($route, function ($frequently_asked_questions_id) use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$frequently_asked_questions_id = prepareIdIn($frequently_asked_questions_id,$host);
	$frequently_asked_questions_id = mysql_real_escape_string($frequently_asked_questions_id);

	$ReturnObject = array();
	$Query = "SELECT * FROM frequently_asked_questions WHERE frequently_asked_questions_id = " . $frequently_asked_questions_id;
	$DatabaseResult = mysql_query($Query) or die('Query failed: ' . mysql_error());

 	while ($Database = mysql_fetch_assoc($DatabaseResult))
 		{

 		$frequently_asked_questions_id = $Database['frequently_asked_questions_id'];
 		$title = $Database['title'];
 		$image = $Database['image'];
 		$header = $Database['header'];
 		$footer = $Database['footer'];

 		$CategoriesQuery = "SELECT * from categories f";
 		$CategoriesQuery .= " WHERE frequently_asked_questions_id = " . $frequently_asked_questions_id;
 		$CategoriesQuery .= " ORDER BY category ASC";
 		$CategoriesResults = mysql_query($CategoriesQuery) or die('Query failed: ' . mysql_error());

 		$frequently_asked_questions_id = prepareIdOut($frequently_asked_questions_id,$host);

 		$F = array();
 		$F['frequently_asked_questions_id'] = $frequently_asked_questions_id;
 		$F['title'] = $title;
 		$F['image'] = $image;
 		$F['header'] = $header;
 		$F['footer'] = $footer;

 		// Categories
 		$F['categories'] = array();
 		while ($Categories = mysql_fetch_assoc($CategoriesResults))
 			{
 			$categories_id = $Categories['catcategories_idegory'];
      $category = $Categories['category'];
 			$C = array();
 			$C['category'] = $category;
 			$C['questions'] = array();

      //Questions
      $QuestionsQuery = "SELECT * from questions f";
      $QuestionsQuery .= " WHERE categories_id = " . $categories_id;
      $QuestionsQuery .= " ORDER BY question ASC";
      $QuestionsResults = mysql_query($QuestionsQuery) or die('Query failed: ' . mysql_error());
      while ($Questions = mysql_fetch_assoc($QuestionsResults))
       {
       $question = $Questions['question'];
       $answer = $Questions['answer'];
  		 $Q = array();
  		 $Q['question'] = $question;
       $Q['answer'] = $answer;
       array_push($C['questions'], $Q);
       }
 			array_push($F['categories'], $C);
 			}

 		$ReturnObject = $F;
 		}

		$app->response()->header("Content-Type", "application/json");
		echo format_json(json_encode($ReturnObject));
	});
?>
