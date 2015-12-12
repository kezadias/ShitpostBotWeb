<?php
class SourceImage{
	
	private $sourceId;
	private $userId;
	private $filetype;
	private $reviewState;
	private $timeAdded;
	private $timeReviewed;
	private $reviewedBy;
	
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
	
	public function getRating(){
		if(!isset($this->rating) && isset($GLOBALS['db'])){
			$this->fetchRating($GLOBALS['db']);
		}
		return $this->rating;
	}
	
	public function getImage(){
		$id = $this->getSourceId();
		$type = $this->getFiletype();
		return "img/sourceimages/$id.$type";
	}
	
	public function fetchRating($db){
		$query = "SELECT count(sourceId) AS total FROM SourceRatings WHERE sourceId = ? AND isPositive = ?";
		$positives = $db->query($query, array($this->getSourceId(), 'y'), array_fill(0, 2, SQLITE_TEXT))->fetchArray()['total'];
		$negatives = $db->query($query, array($this->getSourceId(), 'n'), array_fill(0, 2, SQLITE_TEXT))->fetchArray()['total'];
		$this->rating = $positives - $negatives;
	}
	
}
?>