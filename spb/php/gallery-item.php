<?php
class GalleryItem{
	
	private $img;
	private $link;
	
	function __construct($img, $link){
		$this->img = $img;
		$this->link = $link;
	}
	
	public function getImg(){
		return $this->img;
	}
	
	public function getLink(){
		return $this->link;
	}

}
?>