<?php
	/*
		expects a payload like:
		{
			"id": "7",
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
	header('Access-Control-Allow-Methods: DELETE');
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
	
	// Set ID of post to be deleted
	$post->id = $data->id;
	
	// Do delete post
	if($post->delete()['success']) {
		echo json_encode(
			array('message' => 'Post deleted')
		);
	} else {
		echo json_encode(
			array(
				'error' => true,
				'message' => $post->delete()['errorMsg']
			)
		);
	}
	