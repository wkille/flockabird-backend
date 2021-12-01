<?php
	
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	
	
	// Headers
	header('Access-Control-Allow-Origin: *');
	header('Content-Type: application/json');
	header('Access-Control-Allow-Methods: PUT');
	header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
	
	include_once '../../config/Database.php';
	include_once '../../models/User.php';
	
	// Instantiate DB & connect
	$database = new Database();
	$db = $database->connect();
	
	// Instatiate user object
	$user = new User($db);
	
	// Get raw posted data
	$data = json_decode(file_get_contents("php://input"));
	
	// Check email and password received
	if(empty($data->email) || empty($data->password)) {
		echo json_encode(
			array(
				'error' => true,
				'message' => 'Login request missing email and/or password'
			)
		);
		die;
	}
	
	// Set email of user
	$user->email = $data->email;
	
	// Check for user in db (also sets properties of $user object if email is registered)
	if($user->read_single()['success'] !== true) {
		echo json_encode(
			array(
				'error' => true,
				'message' => $user->read_single()['errorMsg']
			)
		);
		die;
	} elseif(!$user->id) {
		echo json_encode(
			array(
				'error' => true,
				'message' => 'The email is not registered'
			)
		);
		die;
	}
	
	// Salt and hash received password
	// test pw: hashed123
	// create a new hash for db:
	/*
	$pw = 'hashed123';
	$pepper = 'qmshxy7349fgm3ud78jksio';
	$pwd_peppered = hash_hmac("sha256", $pw, $pepper);
	$pwd_hashed = password_hash($pwd_peppered, PASSWORD_ARGON2ID);
	echo json_encode(
		array(
			'pw' => $pw,
			'hash' => $pwd_hashed
		)
	);
	die;
	*/
	
	$pepper = 'qmshxy7349fgm3ud78jksio';
	$received_pw_peppered = hash_hmac("sha256", $data->password, $pepper);
	if (!password_verify($received_pw_peppered, $user->password)) {
		echo json_encode(
			array(
				'error' => true,
				'message' => 'Incorrect password'
			)
		);
		die;
	} else {
		// generate new token
		$user->auth_token = bin2hex(random_bytes(16));
		
		// generate new expiry
		$user->token_expiry = strtotime('tomorrow 3am');
		
		// update user with new token and expiry
		if($user->update()['success'] !== true) {
			echo json_encode(
				array(
					'error' => true,
					'message' => $user->update()['errorMsg']
				)
			);
			die;
		} else {
			echo json_encode(
				array(
					'error' => false,
					'message' => 'New token issued',
					'id' => $user->id,
					'auth_token' => $user->auth_token,
					'token_expiry' => $user->token_expiry
				)
			);
			die;
		}
	}
	
