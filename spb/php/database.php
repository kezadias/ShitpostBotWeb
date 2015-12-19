<?php

//if the server doesn't have the hash_equals method, we add it
//this is used in the login method, and is used instead of hash == hash to defend against timing attacks
if(!function_exists('hash_equals')) {
	function hash_equals($str1, $str2) {
			if(strlen($str1) != strlen($str2)) {
			return false;
		} else {
			$res = $str1 ^ $str2;
			$ret = 0;
			for($i = strlen($res) - 1; $i >= 0; $i--) $ret |= ord($res[$i]);
			return !$ret;
		}
	}
}

class Database{
	
	private $databaseLocation = 'db/database.db';

	//the sqlite3 instance
	private $db;
	
	function __construct(){
		$this->db = new SQLite3($this->databaseLocation);
		$this->db->busyTimeout(5000); //if this value is not set, the timeout is 0 seconds by default. this often causes locking errors
	}

	/**
	 * makes and executes a prepared statement, using ?s as the parameters.
	 * @param $query string value of query
	 * @param array $values the values that replace all the ?s in the query. must be in the same order as they appear in the query
	 * @param array $types the data type of each value. must match up in the same order as the values.
	 * @return SQLite3Result result
	 */
	public function query($query, $values = array(), $types = array()){
		try{
			$statement = $this->db->prepare($query);
			for($i = 0; $i < count($values); $i++){
				$value = $values[$i];
				$type = $types[$i];
				$statement->bindValue($i+1, $value, $type);
			}
			return $statement->execute();
		} catch(Exception $e){
			die($e->getMessage());
		}
	}

	/**
	 * Does what query does, but returns the value of the first column from the first row
	 * if there is no first row, it returns false
	 * @param $query string value of query
	 * @param array $values the values that replace all the ?s in the query. must be in the same order as they appear in the query
	 * @param array $types the data type of each value. must match up in the same order as the values.
	 * @return the first value of the first column from the first row, false if there is no first row.
	 */
	public function scalar($query, $values = array(), $types = array()){
		$result = $this->query($query, $values, $types);
		if($this->resultHasRows($result)){
			$row = $result->fetchArray();
			return $row[key($row)];
		}
		return false;
	}

	/**
	 * Does what query does, but returns an assoc array of all the values
	 * ONLY USE THIS WHEN YOU NEED ALL VALUES, AS IT WILL READ ALL VALUES
	 * @param $query string value of query
	 * @param array $values the values that replace all the ?s in the query. must be in the same order as they appear in the query
	 * @param array $types the data type of each value. must match up in the same order as the values.
	 * @return array assoc array of all the values returned by the query
	 */
	public function queryAsAssoc($query, $values = array(), $types = array()){
		$result = $this->query($query, $values, $types);
		$data = array();
		while($row = $result->fetchArray(SQLITE3_ASSOC)){
			array_push($data, $row);
		}
		return $data;
	}

	/**
	 * @param $result SQLite3Result
	 * @return bool true if there is at least 1 row, false otherwise
	 */
	public function resultHasRows($result){
		$hasRow = $result->fetchArray();
		$result->reset();
		return $hasRow !== false;
	}

	/**
	 * Does what resultHasRows does, but does the query for you as well
	 * @param $query string value of query
	 * @param array $values the values that replace all the ?s in the query. must be in the same order as they appear in the query
	 * @param array $types the data type of each value. must match up in the same order as the values.
	 * @return bool true if there is at least 1 row, false otherwise
	 */
	public function queryHasRows($query, $values = array(), $types = array()){
		return $this->resultHasRows($this->query($query, $values, $types));
	}

	/**
	 * Adds a user to the database
	 * @param $username
	 * @param $password
	 * @return string error message, returns ';success' if there was no error. This is passed onto javascript on the client,
	 * 	where it is translated to a human-readable error
	 */
	public function addUser($username, $password){
		if(!isset($_SESSION['lastSignUp'])){
			$_SESSION['lastSignUp'] = 0;
		}
		
		$time = time();
		if($time - $_SESSION['lastSignUp'] < 180){
			return ';failed-too-fast';
		}
		
		if(!$this->isValid($username)){
			return ';failed-username-invalid';
		}
		
		if(!$this->isValid($password, 7, 30)){
			return ';failed-password-invalid';
		}
		
		if($this->queryHasRows("SELECT username FROM Users WHERE username = ?", array($username), array(SQLITE3_TEXT))){
			return ';failed-username-taken';
		}
		
		$userId = uniqid();
		$salt = bin2hex(openssl_random_pseudo_bytes(8));
		$hash = hash('sha256', $salt.$password);
		$this->query("INSERT INTO Users VALUES(?, ?, ?, ?)",
					array($userId, $username, $salt, $hash),
					array_fill(0, 4, SQLITE3_TEXT));
		$_SESSION['lastSignUp'] = $time;
		return ';success';
	}

	/**
	 * Detects if the username/password is valid
	 * Checks for length, as well as if it's A-Z, a-z, ., -, or _.
	 * Used only for addUser.
	 * @param $str input string
	 * @param int $minLength
	 * @param int $maxLength
	 * @return true if valid, false if not.
	 */
	private function isValid($str, $minLength=5, $maxLength=15){
		if(strlen($str) < $minLength || strlen($str) > $maxLength){
			return false;
		} else{
			return !preg_match('/[^A-z0-9.\-_]/', $str);
		}
	}

	/**
	 * adds an admin with the specified permissions
	 * @param $userId the user id from the Users table. this is NOT the username.
	 * @param $canReview true if the user can review source images and templates.
	 * @param $canMakeAdmin true if the user can manage the admins. there should only be one of these,
	 * 	as they could remove all admins if they choose.
	 * @return string error message, returns ';success' if there was no error. This is passed onto javascript on the client,
	 * 	where it is translated to a human-readable error
	 */
	public function addAdmin($userId, $canReview, $canMakeAdmin){
		if(!$this->queryHasRows("SELECT userId FROM Users WHERE userId = ?", array($userId), array(SQLITE3_TEXT))){
			return ';failed-user-doesnt-exist';
		}
		
		if($this->queryHasRows("SELECT userId FROM Admins WHERE userId = ?", array($userId), array(SQLITE3_TEXT))){
			return ';failed-user-already-admin';
		}
		
		$canReview = $canReview ? 'y' : 'n';
		$canMakeAdmin = $canMakeAdmin ? 'y' : 'n';
		$this->query("INSERT INTO Admins VALUES(?, ?, ?)",
					array($userId, $canReview, $canMakeAdmin),
					array_fill(0, 3, SQLITE3_TEXT));
		return ';success';
	}

	/**
	 * @return the user ID of the currently logged in user.
	 */
	public function getUserId(){
		return $_SESSION['login-id'];
	}

	/**
	 * Sets the session login-id to the specified user if the username and password matches with what's in the db.
	 * @param $username
	 * @param $password
	 * @return string error message, returns ';success' if there was no error. This is passed onto javascript on the client,
	 * 	where it is translated to a human-readable error
	 */
	public function login($username, $password){
		if(!isset($_SESSION['lastLogin'])){
			$_SESSION['lastLogin'] = 0;
		}
		
		$time = time();
		if($time - $_SESSION['lastLogin'] < 2){
			return ';failed-too-fast:'.($time-$_SESSION['lastLogin']);
		}
		
		$_SESSION['lastLogin'] = $time;
		if(!$this->queryHasRows("SELECT username FROM Users WHERE username = ?", array($username), array(SQLITE3_TEXT))){
			return ';failed-invalid-credentials';
		}
		
		$res = $this->query("SELECT userId, salt, password FROM Users WHERE username = ?",
					array($username),
					array(SQLITE3_TEXT))
					->fetchArray();
		$userId = $res['userId'];
		$salt = $res['salt'];
		$storedHash = $res['password'];
		$userHash = hash('sha256', $salt.$password);
		if(hash_equals($storedHash, $userHash)){
			$_SESSION['login-id'] = $userId;
			return ';success';
		}else{
			return ';failed-invalid-credentials';
		}
	}

	/**
	 * Checks if the login-id is set at all, if it's not empty, and if the userId exists at all.
	 * 	if all criteria is met, than the user is logged in
	 * It also sets the login-id to empty if the user-id doesn't exist, to make future calls of this method faster.
	 * @return bool true if the user is logged in, false otherwise.
	 */
	public function isLoggedIn(){
		if(isset($_SESSION['login-id'])){
			if($_SESSION['login-id'] !== ''){
				$userId = $_SESSION['login-id'];
				if($this->queryHasRows("SELECT userId FROM Users WHERE userId = ?", array($userId), array(SQLITE3_TEXT))){
					return true;
				} else{
					$_SESSION['login-id'] = '';
				}
			}
		}
		return false;
	}

	/**
	 * Adds a template to the DB. the uploaded template must match the templateId in filename, and the filetype must also match.
	 * The overlay must be id-overlay.filetype.
	 * @param $templateId the id of the template
	 * @param $pos the position JSON taken from the designer.
	 * @param $filetype the filetype of the template image
	 * @param string $overlayFiletype the filetype of the overlay. always PNG, but exists here for future-proofing.
	 * @return string error message, returns ';success' if there was no error. This is passed onto javascript on the client,
	 * 	where it is translated to a human-readable error
	 */
	public function addTemplate($templateId, $pos, $filetype, $overlayFiletype='NONE'){
		if(!$this->isLoggedIn()){
			return ';failed-not-logged-in';
		}
		$userId = $_SESSION['login-id'];
		$timeAdded = time();
		if($overlayFiletype === 'NONE'){
			$query = 'INSERT INTO Templates(templateId, userId, filetype, positions, timeAdded) VALUES(?, ?, ?, ?, ?)';
			$this->query($query, array($templateId, $userId, $filetype, $pos, $timeAdded),
								array(SQLITE3_TEXT, SQLITE3_TEXT, SQLITE3_TEXT, SQLITE3_TEXT, SQLITE3_INTEGER ));
		}else{
			$query = 'INSERT INTO Templates(templateId, userId, filetype, overlayFiletype, positions, timeAdded) VALUES(?, ?, ?, ?, ?, ?)';
			$this->query($query, array($templateId, $userId, $filetype, $overlayFiletype, $pos, $timeAdded),
								array(SQLITE3_TEXT, SQLITE3_TEXT, SQLITE3_TEXT, SQLITE3_TEXT, SQLITE3_TEXT, SQLITE3_INTEGER ));
		}
		
		return ';success';
		
	}

	/**
	 * Adds a source image to the DB. as with addTemplate, the template ID must match the filename,
	 * 	and the filetype must match the image's filetype.
	 * @param $sourceId the id of the source image
	 * @param $filetype the filetype of the source image
	 * @return string error message, returns ';success' if there was no error. This is passed onto javascript on the client,
	 * 	where it is translated to a human-readable error
	 */
	public function addSourceImage($sourceId, $filetype){
		if(!$this->isLoggedIn()){
			return ';failed-not-logged-in';
		}
		
		$userId = $_SESSION['login-id'];
		$timeAdded = time();
		$query = 'INSERT INTO SourceImages(sourceId, userId, filetype, timeAdded) VALUES(?, ?, ?, ?)';
		$this->query($query, array($sourceId, $userId, $filetype, $timeAdded),
							array(SQLITE3_TEXT, SQLITE3_TEXT, SQLITE3_TEXT, SQLITE3_INTEGER ));
		return ';success';
	}

	/**
	 * Generic method for adding a rating to either the TemplateRatings or SourceRatings table.
	 * @param $tableName string the name of the ratings table
	 * @param $fieldName string the id field name
	 * @param $id string the actual id value
	 * @param $positive string is positive? 'y' or 'n'.
	 * @return string error message, returns ';success' if there was no error. This is passed onto javascript on the client,
	 * 	where it is translated to a human-readable error
	 */
	public function addRating($tableName, $fieldName, $id, $positive){
		if(!$this->isLoggedIn()){
			return ';failed-not-logged-in';
		}
		
		$userId = $_SESSION['login-id'];
		if($this->queryHasRows("SELECT $fieldName FROM $tableName WHERE userId = ? AND $fieldName = ?", array($userId, $id), array(SQLITE3_TEXT, SQLITE3_TEXT))){
			$query = "UPDATE $tableName SET isPositive = ? WHERE userId = ? AND $fieldName = ?";
			$this->query($query, array($positive, $userId, $id),
								array_fill(0, 3, SQLITE3_TEXT));
		} else{
			$query = "INSERT INTO $tableName VALUES(?, ?, ?)";
			$this->query($query, array($userId, $id, $positive),
								array_fill(0, 3, SQLITE3_TEXT));
		}
		return ';success';
	}

	/**
	 * Fetches a random selection of a specified amount of accepted source images from the source images table.
	 * Will be used for the bot.
	 * @param $count int amount of source images to fetch
	 * @return array returns all the filenames of the source images
	 */
	public function getRandomAcceptedSourceImages($count){
		$query = "SELECT sourceId || '.' || filetype AS file FROM SourceImages WHERE reviewState = 'a' ORDER BY random() LIMIT ?";
		$result = $this->query($query, array($count),
							array(SQLITE3_INTEGER));
		
		$output = array();
		while($row = $result->fetchArray()){
			array_push($output, $row['file']);
		}
		return $output;
	}

	/**
	 * Detects if the currently logged in user is allowed to review content.
	 * @return bool true if they can
	 */
	public function canUserReviewMemes(){
		if(!$this->isLoggedIn()){
			return false;
		}
		$userId = $_SESSION['login-id'];
		$query = 'SELECT canReview FROM Admins WHERE userId = ?';
		$result = $this->query($query, array($userId), array(SQLITE3_TEXT));
		if($this->resultHasRows($result)){
			return $result->fetchArray()['canReview'] === 'y';
		} else{
			return false;
		}
	}

	/**
	 * Generic method for reviewing content
	 * @param $tableName string the name of the table
	 * @param $idFieldName string the id field name
	 * @param $id string the string actual id value
	 * @param $state 'a' (accept), 'd' (deny), or 'p' (pending)
	 * @return string error message, returns ';success' if there was no error. This is passed onto javascript on the client,
	 * 	where it is translated to a human-readable error
	 */
	private function reviewImage($tableName, $idFieldName, $state, $id){
		if(!$this->isLoggedIn()){
			return ';failed-not-logged-in';
		}
		
		if($this->canUserReviewMemes()){
			$time = time();
			$userId = $_SESSION['login-id'];
			$query = "UPDATE $tableName SET reviewedBy = ?, timeReviewed = ?, reviewState = ? WHERE $idFieldName = ?";
			$this->query($query, array($userId, $time, $state, $id),
								array(SQLITE3_TEXT, SQLITE3_INTEGER, SQLITE3_TEXT, SQLITE3_TEXT));
			return ';success';
		} else{
			return ';failed-insufficient-permissions';
		}
	}

	/*
	 * accepts source image of specified id.
	 */
	public function acceptSourceImage($sourceId){
		return $this->reviewImage('SourceImages', 'sourceId', 'a', $sourceId);
	}

	/*
	 * accepts template of specified id.
	 */
	public function acceptTemplate($templateId){
		return $this->reviewImage('Templates', 'templateId', 'a', $templateId);
	}

	/*
	 * denies source image of specified id.
	 */
	public function denySourceImage($sourceId){
		return $this->reviewImage('SourceImages', 'sourceId', 'd', $sourceId);
	}

	/*
	 * denies template of specified id.
	 */
	public function denyTemplate($templateId){
		return $this->reviewImage('Templates', 'templateId', 'd', $templateId);
	}

	/*
	 * makes source image of specified id pending.
	 */
	public function makeSourceImagePending($sourceId){
		return $this->reviewImage('SourceImages', 'sourceId', 'p', $sourceId);
	}

	/*
	 * makes template of specified id pending.
	 */
	public function makeTemplatePending($templateId){
		return $this->reviewImage('Templates', 'templateId', 'p', $templateId);
	}

	/**
	 * Fetches all templates returned from a query. You must make your query select Templates.* for this to work properly.
	 * @param $query string value of query
	 * @param array $values the values that replace all the ?s in the query. must be in the same order as they appear in the query
	 * @param array $types the data type of each value. must match up in the same order as the values.
	 * @return array array of templates
	 */
	public function getTemplates($query, $values = array(), $types = array()){
		$result = $this->query($query, $values, $types);
		$templates = array();
		while($row = $result->fetchArray()){
			array_push($templates, 
				new Template(
					$row['templateId'], 
					$row['userId'], 
					$row['filetype'], 
					$row['overlayFiletype'],  
					$row['positions'],  
					$row['reviewState'],  
					$row['timeAdded'],  
					$row['timeReviewed'],   
					$row['reviewedBy']
				)
			);
		}
		return $templates;
	}

	/**
	 * Fetches all source images returned from a query. You must make your query select SourceImages.* for this to work properly.
	 * @param $query string value of query
	 * @param array $values the values that replace all the ?s in the query. must be in the same order as they appear in the query
	 * @param array $types the data type of each value. must match up in the same order as the values.
	 * @return array array of source images
	 */
	public function getSourceImages($query, $values = array(), $types = array()){
		$result = $this->query($query, $values, $types);
		$sourceImages = array();
		while($row = $result->fetchArray()){
			array_push($sourceImages, 
				new SourceImage(
					$row['sourceId'], 
					$row['userId'], 
					$row['filetype'],
					$row['reviewState'], 
					$row['timeAdded'], 
					$row['timeReviewed'], 
					$row['reviewedBy']
				)
			);
		}
		return $sourceImages;
	}

	/**
	 * Fetches all users returned from a query. You must make your query select Users.* for this to work properly.
	 * 	DOES NOT INCLUDE PASSWORD INFO. ONLY INCLUDES USERID AND USERNAME
	 * @param $query string value of query
	 * @param array $values the values that replace all the ?s in the query. must be in the same order as they appear in the query
	 * @param array $types the data type of each value. must match up in the same order as the values.
	 * @return array array of users
	 */
	public function getUsers($query, $values = array(), $types = array()){
		$result = $this->query($query, $values, $types);
		$users = array();
		while($row = $result->fetchArray()){
			array_push($users, new User($row['userId'], $row['username']));
		}
		return $users;
	}

	/**
	 * Fetches all admins returned from a query. You must make your query select Admins.* for this to work properly.
	 * @param $query string value of query
	 * @param array $values the values that replace all the ?s in the query. must be in the same order as they appear in the query
	 * @param array $types the data type of each value. must match up in the same order as the values.
	 * @return array array of admins
	 */
	public function getAdmins($query, $values = array(), $types = array()){
		$result = $this->query($query, $values, $types);
		$admins = array();
		while($row = $result->fetchArray()){
			array_push($admins, new Admin($row['userId'], $row['canReview'] === 'y', $row['canMakeAdmin'] === 'y'));
		}
		return $admins;
	}

	/**
	 * fetches the username associated with the specified userid
	 * @param $userId
	 * @return string username, false if no username was found.
	 */
	public function getUsername($userId){
		return $this->scalar('SELECT username FROM Users WHERE userId = ?', array($userId), array(SQLITE3_TEXT));
	}

	/**
	 * Frees the db resource. Must be done every time you're done with the database. this is usually after twig->render
	 */
	public function close(){
		$this->db->close();
	}
	
}
?>