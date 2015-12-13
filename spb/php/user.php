<?php
class User{
	private $userId;
	private $username;
	
	private $admin;
	
	function __construct($userId, $username){
		$this->userId = $userId;
		$this->username = $username;
	}
	
	public function getUserId(){
		return $this->userId;
	}
	
	public function getUsername(){
		return $this->username;
	}
	
	public function getAdmin(){
		if(!isset($this->admin) && isset($GLOBALS['db'])){
			$this->admin = $this->fetchAdmin($GLOBALS['db']);
		}
		return $this->admin;
	}
	
	private function fetchAdmin($db){
		$admins = $db->getAdmins('SELECT * FROM Admins WHERE userId = ?', array($this->getUserId()), array(SQLITE3_TEXT));
		if(count($admins) > 0){
			return $admins[0];
		}
		return null;
	}
}
?>