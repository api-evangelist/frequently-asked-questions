<?php
$route = '/frequently-asked-questions/';
$app->get($route, function ()  use ($app,$contentType,$githuborg,$githubrepo){

	$ReturnObject = array();
	//$ReturnObject["contentType"] = $contentType;

	if($contentType == 'application/apis+json')
		{
		$app->response()->header("Content-Type", "application/json");

		$apis_json_url = "http://" . $githuborg . ".github.io/" . $githubrepo . "/apis.json";
		$apis_json = file_get_contents($apis_json_url);
		echo stripslashes(format_json($apis_json));
		}
	else
		{

	 	$request = $app->request();
	 	$params = $request->params();

		if(isset($params['query'])){ $query = trim(mysql_real_escape_string($params['query'])); } else { $query = '';}
		if(isset($params['page'])){ $page = trim(mysql_real_escape_string($params['page'])); } else { $page = 0;}
		if(isset($params['count'])){ $count = trim(mysql_real_escape_string($params['count'])); } else { $count = 50;}
		if(isset($params['sort'])){ $sort = trim(mysql_real_escape_string($params['sort'])); } else { $sort = 'title';}
		if(isset($params['order'])){ $order = trim(mysql_real_escape_string($params['order'])); } else { $order = 'ASC';}

		// Pull from MySQL
		if($query!='')
			{
			$Query = "SELECT * FROM frequently_asked_questions WHERE title LIKE '%" . $query . "%' OR header LIKE '%" . $query . "%' OR footer LIKE '%" . $query . "%'";
			}
		else
			{
			$Query = "SELECT * FROM frequently_asked_questions";
			}
			$Query .= " ORDER BY " . $sort . " " . $order . " LIMIT " . $page . "," . $count;
			//echo $Query . "<br />";
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
			}
	});
?>
