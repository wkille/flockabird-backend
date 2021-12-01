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
	include_once '../../models/Category.php';
	
	// Instantiate DB & connect
	$database = new Database();
	$db = $database->connect();
	
	// Instatiate category object
	$category = new Category($db);
	
	// Category read query
	$result = $category->read();
	
	// Get row count
	$num = $result->rowCount();
	
	// Check if any categories
	if($num > 0) {
		// Category array
		$cat_arr = array();
		$cat_arr['data'] = array();
		
		while($row = $result->fetch(PDO::FETCH_ASSOC)) {
			extract($row);
			
			$cat_item = array(
				'id' => $id,
				'name' => $name,
				'slug' => $slug
			);
			
			// Push to "data"
			array_push($cat_arr['data'], $cat_item);
		}			
		// Turn to JSON & output
		echo json_encode($cat_arr);
	} else {
		// No categories
		echo json_encode(
			array('message' => 'No categories found')
		);
	}