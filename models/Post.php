<?php
	class Post {
		// DB stuff
		private $conn;
		private $table = 'posts';
		
		// Post Properties
		public $id;
		public $category_id;
		public $category_name;
		public $tag_ids = '';
		public $title;
		public $body;
		public $description;
		public $author;
		public $slug;
		public $image_url;
		public $image_caption;
		public $image_alt;
		public $reading_time;
		public $date_posted;
		
		// Constructor with DB
		public function __construct($db) {
			$this->conn = $db;
		}
		
		// Get posts
		public function read() {
			// Create query
			$query = 'SELECT 
					c.name as category_name,
					p.id,
					p.category_id,
					p.title,
					p.body,
					p.description,
					p.author,
					p.slug,
					p.image_url,
					p.image_caption,
					p.image_alt,
					p.reading_time,
					p.date_posted
				FROM
					' . $this->table . ' p
				LEFT JOIN
					categories c ON p.category_id = c.id
				ORDER BY 
					p.date_posted DESC';
					
			// Prepare statement
			$stmt = $this->conn->prepare($query);
			
			// Execute statement
			$stmt->execute();
			
			return $stmt;
		}
		
		// Get single post
		public function read_single() {
			// Create query
			$query = 'SELECT 
					c.name as category_name,
					p.id,
					p.category_id,
					p.title,
					p.body,
					p.description,
					p.author,
					p.slug,
					p.image_url,
					p.image_caption,
					p.image_alt,
					p.reading_time,
					p.date_posted
				FROM
					' . $this->table . ' p
				LEFT JOIN
					categories c ON p.category_id = c.id
				WHERE 
					p.id = ?
				LIMIT 0,1';
					
			// Prepare statement
			$stmt = $this->conn->prepare($query);
			
			// Bind ID
			$stmt->bindParam(1, $this->id);
			
			// Execute query
			$stmt->execute();
			
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			
			// Set properties
			$this->title = $row['title'];
			$this->body = $row['body'];
			$this->author = $row['author'];
			$this->category_id = $row['category_id'];
			$this->category_name = $row['category_name'];
			$this->description = $row['description'];
			$this->slug = $row['slug'];
			$this->image_url = $row['image_url'];
			$this->image_caption = $row['image_caption'];
			$this->image_alt = $row['image_alt'];
			$this->reading_time = $row['reading_time'];
			$this->date_posted = $row['date_posted'];
		}
		
		// Create post
		/*
			To test:
			Send POST request to http://localhost/php_rest_api_arete/api/post/create.php
			with body like
			{
				"title": "blablablahhh_2",
				"slug": "boost-boost",
				"category_id": 1,
				"description": "Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto accusantium praesentium eius, ut atque fuga culpa, similique sequi cum eos quis dolorum.",
				"body": "Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto accusantium praesentium eius, ut atque fuga culpa, similique sequi cum eos quis dolorum. Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto accusantium praesentium eius, ut atque fuga culpa, similique sequi cum eos quis dolorum. Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto accusantium praesentium eius, ut atque fuga culpa, similique sequi cum eos quis dolorum. Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto accusantium praesentium eius, ut atque fuga culpa, similique sequi cum eos quis dolorum.",
				"image_url":  "url",
				"image_caption": "cap",
				"image_alt": "alt",	
				"reading_time": "6 min",
				"author": "Jonathan"
			}
		*/
		public function create() {
			
			// Create query
			$query = 'INSERT INTO ' .
					$this->table . '
				SET 
					title = :title,
					slug = :slug,
					category_id = :category_id,
					description = :description,
					body = :body,
					image_url = :image_url,
					image_caption = :image_caption,
					image_alt = :image_alt,
					reading_time = :reading_time,
					author = :author';
					
			// Prepare statement
			$stmt = $this->conn->prepare($query);
				
			// Clean data
			$this->title 			= htmlspecialchars($this->title);
			$this->slug 			= htmlspecialchars($this->slug);
			$this->category_id 		= htmlspecialchars($this->category_id);
			// $this->tag_ids 			= htmlspecialchars($this->tag_ids);
			$this->description 		= htmlspecialchars($this->description);
			$this->body 			= htmlspecialchars($this->body);
			$this->image_url 		= htmlspecialchars($this->image_url);
			$this->image_caption 	= htmlspecialchars($this->image_caption);
			$this->image_alt 		= htmlspecialchars($this->image_alt);
			$this->reading_time 	= htmlspecialchars($this->reading_time);
			$this->author 			= htmlspecialchars($this->author);
			
			// Bind data
			$stmt->bindParam(':title',			 $this->title);
			$stmt->bindParam(':slug',			 $this->slug);
			$stmt->bindParam(':category_id',	 $this->category_id);
			// $stmt->bindParam(':tag_ids',		 $this->tag_ids);
			$stmt->bindParam(':description',	 $this->description);
			$stmt->bindParam(':body',			 $this->body);
			$stmt->bindParam(':image_url',		 $this->image_url);
			$stmt->bindParam(':image_caption',	 $this->image_caption);
			$stmt->bindParam(':image_alt',		 $this->image_alt);
			$stmt->bindParam(':reading_time',	 $this->reading_time);
			$stmt->bindParam(':author',			 $this->author);
			
			// Execute query
			if($stmt->execute()) {
				return [
					'success' => true,
					'errorMsg' => '',
					'id' => $this->conn->lastInsertId()
				];
			}
			
			return [
				'success' => false,
				'errorMsg' => 'An unknown error occurred when trying to create the post in the database.'
			];
		}
		
		// Update post
		/*
			To test:
			Send PUT request to http://localhost/php_rest_api_arete/api/post/update.php
			with body like
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
				"date_posted": "2021-11-26 19:38:06"
			}
		*/
		public function update() {
			
			// Create query
			$query = 'UPDATE ' .
					$this->table . '
				SET 
					title = :title,
					slug = :slug,
					category_id = :category_id,
					description = :description,
					body = :body,
					image_url = :image_url,
					image_caption = :image_caption,
					image_alt = :image_alt,
					reading_time = :reading_time,
					author = :author,
					date_posted = :date_posted
				WHERE
					id = :id';
					
			// Prepare statement
			$stmt = $this->conn->prepare($query);
				
			// Clean data
			$this->title 			= htmlspecialchars($this->title);
			$this->slug 			= htmlspecialchars($this->slug);
			$this->category_id 		= htmlspecialchars($this->category_id);
			//$this->tag_ids 			= htmlspecialchars($this->tag_ids);
			$this->description 		= htmlspecialchars($this->description);
			$this->body 			= htmlspecialchars($this->body);
			$this->image_url 		= htmlspecialchars($this->image_url);
			$this->image_caption 	= htmlspecialchars($this->image_caption);
			$this->image_alt 		= htmlspecialchars($this->image_alt);
			$this->reading_time 	= htmlspecialchars($this->reading_time);
			$this->author 			= htmlspecialchars($this->author);
			$this->date_posted		= htmlspecialchars($this->date_posted);
			$this->id 				= htmlspecialchars($this->id);
			
			// Bind data
			$stmt->bindParam(':title',			 $this->title);
			$stmt->bindParam(':slug',			 $this->slug);
			$stmt->bindParam(':category_id',	 $this->category_id);
			//$stmt->bindParam(':tag_ids',		 $this->tag_ids);
			$stmt->bindParam(':description',	 $this->description);
			$stmt->bindParam(':body',			 $this->body);
			$stmt->bindParam(':image_url',		 $this->image_url);
			$stmt->bindParam(':image_caption',	 $this->image_caption);
			$stmt->bindParam(':image_alt',		 $this->image_alt);
			$stmt->bindParam(':reading_time',	 $this->reading_time);
			$stmt->bindParam(':author',			 $this->author);
			$stmt->bindParam(':date_posted',	 $this->date_posted);
			$stmt->bindParam(':id',				 $this->id);
			
			// Execute query
			if($stmt->execute()) {
				return [
					'success' => true,
					'errorMsg' => ''
				];
			}
			
			return [
				'success' => false,
				'errorMsg' => 'An unknown error occurred when trying to update the post in the database.'
			];
		}
		
		// Delete post
		/*
			To test:
			Send delete request to http://localhost/php_rest_api_arete/api/post/delete.php
			with body like
			{
				"id": "7",
				"token": "kjhgkergerhgiJHBkjhrguhr784hjh"
			}
		*/
		public function delete() {
			
			// Create query
			$query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
			
			// Prepare statement
			$stmt = $this->conn->prepare($query);
			
			// Clean data
			$this->id = htmlspecialchars($this->id);
			
			// Bind data
			$stmt->bindParam(':id', $this->id);
			
			// Execute query
			if($stmt->execute()) {
				return [
					'success' => true,
					'errorMsg' => ''
				];
			}
			
			return [
				'success' => false,
				'errorMsg' => 'An unknown error occurred when trying to delete from the database.'
			];
		}
	}