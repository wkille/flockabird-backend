<?php
	class User {
		// DB stuff
		private $conn;
		private $table = 'users';
		
		// User properties
		public $id;
		public $name;
		public $email;
		public $password;
		public $auth_token;
		public $token_expiry;
		
		// Constructor with DB
		public function __construct($db) {
			$this->conn = $db;
		}
		
		// Get single user
		public function read_single() {
			// Create query
			$query = 
				'SELECT 
					id,
					name,
					email,
					password,
					auth_token,
					token_expiry
				FROM
					' . $this->table . '
				WHERE 
					email = ?
				LIMIT 0,1';
					
			// Prepare statement
			$stmt = $this->conn->prepare($query);
			
			// Bind ID
			$stmt->bindParam(1, $this->email);
			
			// Execute query
			if($stmt->execute()) {
				$row = $stmt->fetch(PDO::FETCH_ASSOC);			
				// Set properties
				$this->id = $row['id'];
				$this->name = $row['name'];
				$this->password = $row['password'];
				$this->auth_token = $row['auth_token'];
				$this->token_expiry = $row['token_expiry'];
				return [
					'success' => true,
					'errorMsg' => ''
				];
			} else {
				return [
					'success' => false,
					'errorMsg' => 'An unknown error occurred when querying the database.'
				];
			}
		}
		
		// Get single user by auth_token
		public function find_by_token() {
			// Create query
			$query = 
				'SELECT 
					id,
					name,
					email,
					password,
					auth_token,
					token_expiry
				FROM
					' . $this->table . '
				WHERE 
					auth_token = ?
				LIMIT 0,1';
					
			// Prepare statement
			$stmt = $this->conn->prepare($query);
			
			// Bind ID
			$stmt->bindParam(1, $this->auth_token);
			
			// Execute query
			if($stmt->execute()) {
				$row = $stmt->fetch(PDO::FETCH_ASSOC);			
				// Set properties
				$this->id = $row['id'];
				$this->name = $row['name'];
				$this->password = $row['password'];
				$this->auth_token = $row['auth_token'];
				$this->token_expiry = $row['token_expiry'];
				return [
					'success' => true,
					'errorMsg' => ''
				];
			} else {
				return [
					'success' => false,
					'errorMsg' => 'An unknown error occurred when querying the database.'
				];
			}
		}
		
		// Update user
		/*
			To test:
			Send PUT request to http://localhost/php_rest_api_arete/api/user/update.php
			with body like
			{
				"id": "1",
				"name": "Jonathan",
				"email": "jonathan352@yahoo.co.uk",
				"password": "hashed123",
				"auth_token": "hash456",
				"token_expiry": "2021-11-19 11:53:05"
			}
		*/
		public function update() {			
			// Create query
			$query = 'UPDATE ' .
					$this->table . '
				SET 
					name = :name,
					email = :email,
					password = :password,
					auth_token = :auth_token,
					token_expiry = :token_expiry
				WHERE
					id = :id';
					
			// Prepare statement
			$stmt = $this->conn->prepare($query);
				
			// Clean data
			$this->id 				= htmlspecialchars($this->id);
			$this->name 			= htmlspecialchars($this->name);
			$this->email 			= htmlspecialchars($this->email);
			$this->password 		= htmlspecialchars($this->password);
			$this->auth_token 		= htmlspecialchars($this->auth_token);
			$this->token_expiry		= htmlspecialchars($this->token_expiry);
			
			// Bind data
			$stmt->bindParam(':id',				$this->id);
			$stmt->bindParam(':name',			$this->name);
			$stmt->bindParam(':email',			$this->email);
			$stmt->bindParam(':password', 		$this->password);
			$stmt->bindParam(':auth_token',		$this->auth_token);
			$stmt->bindParam(':token_expiry',	$this->token_expiry);
			
			// Execute query
			if($stmt->execute()) {
				return [
					'success' => true,
					'errorMsg' => ''
				];
			}
			
			return [
				'success' => false,
				'errorMsg' => 'An unknown error occurred when trying to update the user in the database.'
			];
		}
		
		// Authenticate user
		public function authenticate() {
			// Create query
			$query = 
				'SELECT 
					id,
					name,
					email,
					password,
					auth_token,
					token_expiry
				FROM
					' . $this->table . '
				WHERE 
					auth_token = ?
				LIMIT 0,1';
					
			// Prepare statement
			$stmt = $this->conn->prepare($query);
			
			// Bind ID
			$stmt->bindParam(1, $this->auth_token);
			
			// Execute query
			if($stmt->execute()) {
				$row = $stmt->fetch(PDO::FETCH_ASSOC);			
				// Set properties
				$this->id = $row['id'] ?? null;
				$this->auth_token = $row['auth_token'] ?? null;
				$this->token_expiry = $row['token_expiry'] ?? null;
				if(!$this->id) {
					return [
						'success' => false,
						'errorMsg' => 'The token is invalid'
					];
				} elseif(strtotime('now') > $this->token_expiry) {
					return [
						'success' => false,
						'errorMsg' => 'The token has expired'
					];
				} else {
					return [
						'success' => true,
						'errorMsg' => '',
						'id' => $this->id
					];
				}
			} else {
				return [
					'success' => false,
					'errorMsg' => 'An unknown error occurred when querying the database.'
				];
			}
		}
	}