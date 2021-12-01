<?php
	/*
		expects a payload like:
		{
			"id": "9",
			"title": "blablablahhh",
			"slug": "boost-boost",
			"category_id": 1,
			"description": "Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto accusantium praesentium eius, ut atque fuga culpa, similique sequi cum eos quis dolorum.",
			"body": "Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto accusantium praesentium eius, ut atque fuga culpa, similique sequi cum eos quis dolorum. Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto accusantium praesentium eius, ut atque fuga culpa, similique sequi cum eos quis dolorum. Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto accusantium praesentium eius, ut atque fuga culpa, similique sequi cum eos quis dolorum. Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto accusantium praesentium eius, ut atque fuga culpa, similique sequi cum eos quis dolorum.",
			"image_url":  "url",
			"image_caption": "cap",
			"image_alt": "alt",	
			"reading_time": "6 min",
			"author": "Jonathan",
			"token": "kjhgkergerhgiJHBkjhrguhr784hjh"
		}
	*/
	
	/*
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	*/
	
	// Headers
	header('Access-Control-Allow-Origin: *');
	header('Content-Type: application/json');
	header('Access-Control-Allow-Methods: PUT');
	header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
	
	include_once '../../config/Database.php';
	include_once '../../models/Post.php';
	include_once '../../models/User.php';
	
	// Instantiate DB & connect
	$database = new Database();
	$db = $database->connect();
	
	// Instatiate blog post object
	$post = new Post($db);
	
	// Get raw posted data
	$data = json_decode(file_get_contents("php://input"));
	
	// Instatiate user object
	$user = new User($db);
	
	// Get token
	if(!$data->token) {
		echo json_encode(
			array(
				'authenticated' => false,
				'message' => 'Token missing from request'
			)
		);
		die;
	} else {
		$user->auth_token = $data->token;
	}
	
	// Break out if not authorised
	if(!$user->authenticate()['success']) {
		echo json_encode(
			array(
				'success' => false,
				'message' => $user->authenticate()['errorMsg']
			)
		);
		die;
	}
	
	// Set ID of post to be updated
	$post->id 				= $data->id;
	
	$post->title 			= $data->title;
	$post->slug 			= $data->slug;
	$post->category_id 		= $data->category_id;
	//$post->tags 			= $data->tags;
	$post->description 		= $data->description;
	$post->body 			= $data->body;
	$post->image_url 		= $data->image_url;
	$post->image_caption 	= $data->image_caption;
	$post->image_alt 		= $data->image_alt;
	$post->reading_time 	= $data->reading_time;
	$post->author 			= $data->author;
	$post->date_posted		= $data->date_posted;
	
	// Do update post
	$runUpdate = $post->update();
	if($runUpdate['success']) {
		echo json_encode(
			array('message' => 'Post updated')
		);
	} else {
		echo json_encode(
			array(
				'error' => true,
				'message' => $runUpdate['errorMsg']
			)
		);
	}
	