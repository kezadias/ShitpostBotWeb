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
	
	private $rating = 0;
	
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
	
	public function getRating(){
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
	
	public function fetchRating($db){
		$query = "SELECT count(templateId) AS total FROM TemplateRatings WHERE templateId = ? AND isPositive = ?";
		$positives = $db->query($query, array($this->templateId, 'y'), array_fill(0, 2, SQLITE_TEXT))->fetchArray()['total'];
		$negatives = $db->query($query, array($this->templateId, 'n'), array_fill(0, 2, SQLITE_TEXT))->fetchArray()['total'];
		$this->rating = $positives - $negatives;
	}
	
}
?>