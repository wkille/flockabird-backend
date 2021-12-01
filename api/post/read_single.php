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
	
	// Get ID
	$post->id = isset($_GET['id']) ? $_GET['id'] : die();
	
	// Get post
	$post->read_single();
	
	// Create array
	$post_arr = array(
		'id' => $post->id,
		'title' => $post->title,
		'slug' => $post->slug,
		'category_id' => $post->category_id,
		'category_name' => $post->category_name,
		//'tags' => $post->tags,
		'description' => $post->description,
		'body' => html_entity_decode($post->body),
		'date_posted' => $post->date_posted,
		'image_url' => $post->image_url,
		'image_caption' => $post->image_caption,
		'image_alt' => $post->image_alt,
		'reading_time' => $post->reading_time,
		'author' => $post->author
	);
	
	// Make JSON
	echo json_encode($post_arr);