<?php
class SourceImage{
	
	private $sourceId;
	private $userId;
	private $filetype;
	private $reviewState;
	private $timeAdded;
	private $timeReviewed;
	private $reviewedBy;
	
	private $username;
	private $rating = 0;
	
	function __construct($sourceId, $userId, $filetype, $reviewState, $timeAdded, $timeReviewed, $reviewedBy){
		$this->sourceId = $sourceId;
		$this->userId = $userId;
		$this->filetype = $filetype;
		$this->reviewState = $reviewState;
		$this->timeAdded = $timeAdded;
		$this->timeReviewed = $timeReviewed;
		$this->reviewedBy = $reviewedBy;
	}
	
	public function getSourceId(){
		return $this->sourceId;
	}

	public function getUserId(){
		return $this->userId;
	}

	public function getFiletype(){
		return $this->filetype;
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
		$id = $this->getSourceId();
		$type = $this->getFiletype();
		return "img/sourceimages/$id.$type";
	}
	
	public function fetchRating($db){
		$query = "SELECT count(*) AS total FROM SourceRatings WHERE sourceId = ? AND isPositive = ?";
		$id = $this->sourceId;
		$positives = $db->scalar($query, array($id, 'y'), array_fill(0, 2, SQLITE3_TEXT));
		$negatives = $db->scalar($query, array($id, 'n'), array_fill(0, 2, SQLITE3_TEXT));
		return $positives - $negatives;
	}
	
}
?>