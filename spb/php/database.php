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
		return $result->fetchArray() !== false;
	}
	
	public function queryHasRows($query, $values, $types){
		return $this->resultHasRows($this->query($query, $values, $types));
	}
	
	public function addUser($username, $password){
		if($this->queryHasRows("SELECT username FROM Users WHERE username = ?", array($username), array(SQLITE3_TEXT))){
			return 'username-taken';
		}
		$userId = uniqid();
		$salt = bin2hex(openssl_random_pseudo_bytes(8));
		$hash = hash('sha256', $salt.$password);
		$this->query("INSERT INTO Users VALUES(?, ?, ?, ?)",
					array($userId, $username, $salt, $hash),
					array_fill(0, 4, SQLITE3_TEXT));
		return 'success';
	}
	
	public function addAdmin($userId, $canReview, $canMakeAdmin){
		if(!$this->queryHasRows("SELECT userId FROM Users WHERE userId = ?", array($userId), array(SQLITE3_TEXT))){
			return 'user-doesnt-exist';
		}
		
		if($this->queryHasRows("SELECT userId FROM Users WHERE userId = ?", array($userId), array(SQLITE3_TEXT))){
			return 'user-already-admin';
		}
		
		$canReview = $canReview ? 'y' : 'n';
		$canMakeAdmin = $canReview ? 'y' : 'n';
		$this->query("INSERT INTO Admins VALUES(?, ?, ?)",
					array($userId, $canReview, $canMakeAdmin),
					array_fill(0, 3, SQLITE3_TEXT));
		return 'success';
	}
	
	public function login($username, $password){
		if(!$this->queryHasRows("SELECT username FROM Users WHERE username = ?", array($username), array(SQLITE3_TEXT))){
			return 'failed';
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
			return 'success';
		}else{
			return 'failed';
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
	
	public function addTemplate($filetype, $pos, $overlayFiletype='NONE'){
		if(!$this->isLoggedIn()){
			return 'failed-not-logged-in';
		}
		$templateId = uniqid();
		$userId = $_SESSION['login-id'];
		$timeAdded = time();
		if($overlayFiletype === 'NONE'){
			$query = 'INSERT INTO Templates(templateId, userId, filetype, positions, timeAdded) VALUES(?, ?, ?, ?, ?, ?)';
			$this->query($query, array($templateId, $userId, $filetype, $pos, $timeAdded),
								array(SQLITE3_TEXT, SQLITE3_TEXT, SQLITE3_TEXT, SQLITE3_TEXT, SQLITE3_INTEGER ));
		}else{
			$query = 'INSERT INTO Templates(templateId, userId, filetype, overlayFiletype, positions, timeAdded) VALUES(?, ?, ?, ?, ?, ?)';
			$this->query($query, array($templateId, $userId, $filetype, $overlayFiletype, $pos, $timeAdded),
								array(SQLITE3_TEXT, SQLITE3_TEXT, SQLITE3_TEXT, SQLITE3_TEXT, SQLITE3_TEXT, SQLITE3_INTEGER ));
		}
		
		return $templateId;
		
	}
	
	public function addSourceImage($filetype){
		if(!$this->isLoggedIn()){
			return 'failed-not-logged-in';
		}
		
		$sourceId = uniqid();
		$userId = $_SESSION['login-id'];
		$timeAdded = time();
		$query = 'INSERT INTO SourceImages(sourceId, userId, filetype, timeAdded) VALUES(?, ?, ?, ?)';
		$this->query($query, array($sourceId, $userId, $filetype, $timeAdded),
							array(SQLITE3_TEXT, SQLITE3_TEXT, SQLITE3_TEXT, SQLITE3_INTEGER ));
		return $sourceId;
	}
	
	private function addRating($tableName, $fieldName, $id, $positive){
		if(!$this->isLoggedIn()){
			return 'failed-not-logged-in';
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
		return 'success';
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
	
	private function acceptImage($tableName, $fieldName, $id){
		if(!$this->isLoggedIn()){
			return 'failed-not-logged-in';
		}
		
		if($this->canUserReviewMemes()){
			$time = time();
			$userId = $_SESSION['login-id']
			$query = "UPDATE $tableName SET acceptedBy = ?, timeAccepted = ? WHERE $fieldName = ?";
			$this->query($query, array($userId, $time, $id),
								array(SQLITE3_TEXT, SQLITE3_INTEGER, SQLITE3_TEXT));
			return 'success';
		} else{
			return 'failed-insufficient-permissions';
		}
	}
	
	public function acceptSourceImage($sourceId){
		return $this->acceptImage('SourceImages', 'sourceId', $sourceId)
	}
	
	public function acceptTemplate($templateId){
		return $this->acceptImage('Templates', 'templateId', $templateId)
	}
	
}
?>