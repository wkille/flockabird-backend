<?php
	/*
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	*/
	
	// Headers
	header('Access-Control-Allow-Origin: *');
	header('Content-Type: application/json');
	
	include_once '../../config/Database.php';
	include_once '../../models/Post.php';
	
	// Instantiate DB & connect
	$database = new Database();
	$db = $database->connect();
	
	// Instatiate blog post object
	$post = new Post($db);
	
	// Blog post query
	$result = $post->read();
	
	// Get row count
	$num = $result->rowCount();
	
	// Check if any posts
	if($num > 0) {
		// Post array
		$posts_arr = array();
		$posts_arr['data'] = array();
		
		while($row = $result->fetch(PDO::FETCH_ASSOC)) {
			extract($row);
			
			$post_item = array(
				'id' => $id,
				'title' => $title,
				'slug' => $slug,
				'category_id' => $category_id,
				'category_name' => $category_name,
				//'tags' => $tags,
				'description' => $description,
				'body' => html_entity_decode($body),
				'date_posted' => $date_posted,
				'image_url' => $image_url,
				'image_caption' => $image_caption,
				'image_alt' => $image_alt,
				'reading_time' => $reading_time,
				'author' => $author
			);
			
			// Push to "data"
			array_push($posts_arr['data'], $post_item);
		}			
		// Turn to JSON & output
			// echo html_entity_decode(json_encode($posts_arr, JSON_PRETTY_PRINT));
			// echo html_entity_decode(json_encode($posts_arr));
		echo json_encode($posts_arr);
	} else {
		// No posts
		echo json_encode(
			array('message' => 'No posts found')
		);
	}