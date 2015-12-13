<?php
class Database{
	
	private $databaseLocation = 'db/database.db';
	
	private $db;
	
	function __construct(){
		$this->db = new SQLite3($this->databaseLocation);
	}
	
	public function query($query, $values, $types){
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
	
	public function resultHasRows($result){
		$hasRow = $result->fetchArray();
		$result->reset();
		return $hasRow !== false;
	}
	
	public function queryHasRows($query, $values, $types){
		return $this->resultHasRows($this->query($query, $values, $types));
	}
	
	public function addUser($username, $password){
		if(!isset($_SESSION['lastSignUp'])){
			$_SESSION['lastSignUp'] = 0;
		}
		
		$time = time();
		if($time - $_SESSION['lastSignUp'] < 180){
			return ';failed-too-fast:'.($time-$_SESSION['lastSignUp']);
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
	
	private function isValid($str, $minLength=5, $maxLength=15){
		if(strlen($str) < $minLength || strlen($str) > $maxLength){
			return false;
		} else{
			return !preg_match('/[^A-z0-9.\-_]/', $str);
		}
	}
	
	public function addAdmin($userId, $canReview, $canMakeAdmin){
		if(!$this->queryHasRows("SELECT userId FROM Users WHERE userId = ?", array($userId), array(SQLITE3_TEXT))){
			return ';failed-user-doesnt-exist';
		}
		
		if($this->queryHasRows("SELECT userId FROM Admins WHERE userId = ?", array($userId), array(SQLITE3_TEXT))){
			return ';failed-user-already-admin';
		}
		
		$canReview = $canReview ? 'y' : 'n';
		$canMakeAdmin = $canReview ? 'y' : 'n';
		$this->query("INSERT INTO Admins VALUES(?, ?, ?)",
					array($userId, $canReview, $canMakeAdmin),
					array_fill(0, 3, SQLITE3_TEXT));
		return ';success';
	}
	
	public function getUserId(){
		return $_SESSION['login-id'];
	}
	
	public function login($username, $password){
		if(!$this->queryHasRows("SELECT username FROM Users WHERE username = ?", array($username), array(SQLITE3_TEXT))){
			return ';failed';
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
			return ';failed';
		}
	}
	
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
	
	public function getUsername(){
		if(!$this->isLoggedIn()){
			return ';failed-not-logged-in';
		}
		$query = 'SELECT username FROM Users WHERE userId = ?';
		$result = $this->query($query, array($_SESSION['login-id']),
										array(SQLITE3_TEXT))
										->fetchArray();
		return $result['username'];
	}
	
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
	
	private function addRating($tableName, $fieldName, $id, $positive){
		if(!$this->isLoggedIn()){
			return ';failed-not-logged-in';
		}
		
		$userId = $_SESSION['login-id'];
		$positive = $positive ? 'y' : 'n';
		if($this->queryHasRows("SELECT userId FROM $tableName WHERE userId = ? AND $fieldName = ?", array($userId, $id), array(SQLITE3_TEXT, SQLITE3_TEXT))){
			$query = "UPDATE $tableName SET isPositive = ? WHERE userId = ? AND $fieldName = ?";
			$this->query($query, array($userId, $id, $positive),
								array_fill(0, 3, SQLITE3_TEXT));
		} else{
			$query = "INSERT INTO $tableName VALUES(?, ?, ?)";
			$this->query($query, array($userId, $id, $positive),
								array_fill(0, 3, SQLITE3_TEXT));
		}
		return ';success';
	}
	
	public function addTemplateRating($templateId, $positive){
		return addRating('TemplateRatings', 'templateId', $templateId, $positive);
	}
	
	public function addSourceRating($sourceId, $positive){
		return addRating('SourceRatings', 'sourceId', $sourceId, $positive);
	}
	
	public function getRandomSourceImages($count){
		$query = "SELECT sourceId || '.' || filetype AS file FROM SourceImages ORDER BY random() LIMIT ?";
		$result = $this->query($query, array($count),
							array(SQLITE3_INTEGER));
		
		$output = array();
		while($row = $result->fetchArray()){
			array_push($output, $row['file']);
		}
		return $output;
	}
	
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
	
	public function getPositionsForTemplate($templateId){
		$query = "SELECT positions FROM Templates WHERE templateId = ?";
		$result = $this->query($query, array($templateId),
										array(SQLITE3_TEXT))
										->fetchArray();
		return json_decode($result['positions']);
	}
	
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
	
	public function acceptSourceImage($sourceId){
		return $this->reviewImage('SourceImages', 'sourceId', 'a', $sourceId);
	}
	
	public function acceptTemplate($templateId){
		return $this->reviewImage('Templates', 'templateId', 'a', $templateId);
	}
	
	public function denySourceImage($sourceId){
		return $this->reviewImage('SourceImages', 'sourceId', 'd', $sourceId);
	}
	
	public function denyTemplate($templateId){
		return $this->reviewImage('Templates', 'templateId', 'd', $templateId);
	}
	
	public function getTemplates($query, $values, $types){
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
	
	public function getSourceImages($query, $values, $types){
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
	
	public function close(){
		$this->db->close();
	}
	
}
?>