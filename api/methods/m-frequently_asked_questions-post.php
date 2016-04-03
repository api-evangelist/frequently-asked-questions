<?php
$route = '/frequently-asked-questions/';
$app->post($route, function () use ($app){

 	$request = $app->request();
 	$params = $request->params();

	if(isset($params['title'])){ $title = mysql_real_escape_string($params['title']); } else { $title = date('Y-m-d H:i:s'); }
	if(isset($params['image'])){ $image = mysql_real_escape_string($params['image']); } else { $image = ''; }
	if(isset($params['header'])){ $header = mysql_real_escape_string($params['header']); } else { $header = ''; }
	if(isset($params['footer'])){ $footer = mysql_real_escape_string($params['footer']); } else { $footer = ''; }

  $Query = "SELECT * FROM frequently_asked_questions WHERE title = '" . $title . "'";
	//echo $Query . "<br />";
	$Database = mysql_query($Query) or die('Query failed: ' . mysql_error());

	if($Database && mysql_num_rows($Database))
		{
		$Thisfrequently_asked_questions = mysql_fetch_assoc($Database);
		$frequently_asked_questions_id = $Thisfrequently_asked_questions['ID'];
		}
	else
		{
		$Query = "INSERT INTO frequently_asked_questions(title,image,header,footer)";
		$Query .= " VALUES(";
		$Query .= "'" . mysql_real_escape_string($title) . "',";
		$Query .= "'" . mysql_real_escape_string($image) . "',";
		$Query .= "'" . mysql_real_escape_string($header) . "',";
		$Query .= "'" . mysql_real_escape_string($footer) . "'";
		$Query .= ")";
		//echo $Query . "<br />";
		mysql_query($Query) or die('Query failed: ' . mysql_error());
		$frequently_asked_questions_id = mysql_insert_id();
		}

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
