<?php
class GalleryItem{
	
	private $img;
	private $link;
	private $rating;
	
	function __construct($img, $link, $rating){
		$this->img = $img;
		$this->link = $link;
		$this->rating = $rating;
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

}
?>