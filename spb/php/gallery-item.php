<?php
class GalleryItem{
	
	private $id;
	private $img;
	private $link;
	private $rating;
	private $submitter;
	private $userRating;
	
	function __construct($id, $img, $link, $rating, $submitter, $userRating){
		$this->id = $id;
		$this->img = $img;
		$this->link = $link;
		$this->rating = $rating;
		$this->submitter = $submitter;
		$this->userRating = $userRating;
	}
	
	public function getId(){
		return $this->id;
	}
	
	public function getImg(){
		return $this->img;
	}
	
	public function getLink(){
		return $this->link;
	}
	
	public function getRating(){
		return $this->rating;
	}
	
	public function getSubmitter(){
		return $this->submitter;
	}
	
	public function getUserRating(){
		return $this->userRating;
	}
	
}
?>