<?php
	/*
		expects a payload like:
		...url...?token=hgierherj78JHG8kjhvk
	*/
	
	/*
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	*/
	
	// Headers
	header('Access-Control-Allow-Origin: *');
	header('Content-Type: application/json');
	
	include_once '../../config/Database.php';
	include_once '../../models/User.php';
	
	// Instantiate DB & connect
	$database = new Database();
	$db = $database->connect();
	
	// Instatiate user object
	$user = new User($db);
	
	// Get token
	if(!isset($_GET['token'])) {
		echo json_encode(
			array(
				'authenticated' => false,
				'message' => 'Token missing from request'
			)
		);
		die;
	} else {
		$user->auth_token = $_GET['token'];
	}
	
	// Check for user in db (also sets properties of $user object if email is registered)
	if($user->find_by_token()['success'] !== true) {
		echo json_encode(
			array(
				'authenticated' => false,
				'message' => $user->find_by_token()['errorMsg']
			)
		);
		die;
	} elseif(!$user->id) {
		echo json_encode(
			array(
				'authenticated' => false,
				'message' => 'The token is invalid'
			)
		);
		die;
	}
	
	// Check if token has expired
	if(strtotime('now') > $user->token_expiry) {
		echo json_encode(
			array(
				'authenticated' => false,
				'message' => 'The token has expired'
			)
		);
		die;
	} else {
		echo json_encode(
			array(
				'authenticated' => true,
				'message' => 'The token is valid',
				//'token' => $user->auth_token,
				'token_expiry' => $user->token_expiry,
				//'name' => $user->name,
				//'email' => $user->email,
				//'password' => $user->password,
				'id' => $user->id
			)
		);
	}
		