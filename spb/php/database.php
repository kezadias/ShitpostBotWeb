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
	
}
?>