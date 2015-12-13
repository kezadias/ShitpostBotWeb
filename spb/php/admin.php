<?php
class Admin{
	private $userId;
	private $canReview;
	private $canMakeAdmin;
	
	private $admin;
	
	function __construct($userId, $canReview, $canMakeAdmin){
		$this->userId = $userId;
		$this->canReview = $canReview;
		$this->canMakeAdmin = $canMakeAdmin;
	}
	
	public function getUserId(){
		return $this->userId;
	}
	
	public function canReview(){
		return $this->canReview;
	}
	
	public function canMakeAdmin(){
		return $this->canMakeAdmin;
	}
}
?>