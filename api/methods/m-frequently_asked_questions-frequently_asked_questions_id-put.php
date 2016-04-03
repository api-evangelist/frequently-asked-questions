<?php
$route = '/frequently-asked-questions/:frequently_asked_questions_id/';
$app->put($route, function ($frequently_asked_questions_id) use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$frequently_asked_questions_id = prepareIdIn($frequently_asked_questions_id,$host);
	$frequently_asked_questions_id = mysql_real_escape_string($frequently_asked_questions_id);

	$ReturnObject = array();

 	$request = $app->request();
 	$params = $request->params();

	if(isset($params['title'])){ $title = mysql_real_escape_string($params['title']); } else { $title = date('Y-m-d H:i:s'); }
	if(isset($params['image'])){ $image = mysql_real_escape_string($params['image']); } else { $image = ''; }
	if(isset($params['header'])){ $header = mysql_real_escape_string($params['header']); } else { $header = ''; }
	if(isset($params['footer'])){ $footer = mysql_real_escape_string($params['footer']); } else { $footer = ''; }

  $Query = "SELECT * FROM frequently_asked_questions WHERE ID = " . $frequently_asked_questions_id;
	//echo $Query . "<br />";
	$Database = mysql_query($Query) or die('Query failed: ' . mysql_error());

	if($Database && mysql_num_rows($Database))
		{
		$query = "UPDATE frequently_asked_questions SET ";
		$query .= "title = '" . mysql_real_escape_string($title) . "'";
		$query .= ", image = '" . mysql_real_escape_string($image) . "'";
		$query .= ", header = '" . mysql_real_escape_string($header) . "'";
		$query .= ", footer = '" . mysql_real_escape_string($footer) . "'";
		$query .= " WHERE frequently_asked_questions_id = " . $frequently_asked_questions_id;
		//echo $query . "<br />";
		mysql_query($query) or die('Query failed: ' . mysql_error());
		}

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

	$app->response()->header("Content-Type", "application/json");
	echo stripslashes(format_json(json_encode($ReturnObject)));

	});
?>
