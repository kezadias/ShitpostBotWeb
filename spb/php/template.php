<?php
class Template{
	
	private $templateId;
	private $userId;
	private $filetype;
	private $overlayFiletype;
	private $positions;
	private $reviewState;
	private $timeAdded;
	private $timeReviewed;
	private $reviewedBy;
	
	private $rating;
	private $submitterName;
	private $reviewerName;
	
	function __construct($templateId, $userId, $filetype, $overlayFiletype, $positions, $reviewState, $timeAdded, $timeReviewed, $reviewedBy){
		$this->templateId = $templateId;
		$this->userId = $userId;
		$this->filetype = $filetype;
		$this->overlayFiletype = $overlayFiletype;
		$this->positions = $positions;
		$this->reviewState = $reviewState;
		$this->timeAdded = $timeAdded;
		$this->timeReviewed = $timeReviewed;
		$this->reviewedBy = $reviewedBy;
	}
	
	public function getTemplateId(){
		return $this->templateId;
	}	

	public function getUserId(){
		return $this->userId;
	}

	public function getFiletype(){
		return $this->filetype;
	}	

	public function getOverlayFiletype(){
		return $this->overlayFiletype;
	}	

	public function getPositions(){
		return $this->positions;
	}	

	public function getReviewState(){
		return $this->positions;
	}	

	public function getTimeAdded(){
		return $this->timeAdded;
	}	

	public function getTimeReviewed(){
		return $this->timeReviewed;
	}	

	public function getReviewedBy(){
		return $this->reviewedBy;
	}
	
	public function getSubmitterName(){
		if(!isset($this->submitterName) && isset($GLOBALS['db'])){
			$this->submitterName = $this->getUsername($this->getUserId());
		}
		return$this->submitterName;
	}
	
	public function getReviewerName(){
		if(!isset($this->reviewerName) && isset($GLOBALS['db'])){
			$this->reviewerName = $this->getUsername($this->getReviewedBy());
		}
		return$this->reviewerName;
	}
	
	private function getUsername($userId){
		$db = $GLOBALS['db'];
		$username = $db->getUsername($userId);
		return $username === false ? null : $username;
	}
	
	public function getRating(){
		if(!isset($this->rating) && isset($GLOBALS['db'])){
			$this->rating = $this->fetchRating($GLOBALS['db']);
		}
		return $this->rating;
	}
	
	public function getImage(){
		$id = $this->getTemplateId();
		$type = $this->getFiletype();
		return "img/template/$id.$type";
	}
	
	public function getOverlayImage(){
		$id = $this->getTemplateId();
		$type = $this->getOverlayFiletype();
		return "img/template/$id-overlay.$type";
	}
	
	private function fetchRating($db){
		$query = "SELECT count(*) AS total FROM TemplateRatings WHERE templateId = ? AND isPositive = ?";
		$id = $this->templateId;
		$positives = $db->scalar($query, array($id, 'y'), array_fill(0, 2, SQLITE3_TEXT));
		$negatives = $db->scalar($query, array($id, 'n'), array_fill(0, 2, SQLITE3_TEXT));
		return $positives - $negatives;
	}
	
}
?>