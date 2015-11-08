<?php
class Database {
	
	public function __construct($name) {
		$this->d = new SQLite3($name);
	}
	
	public function query($query) {
		return $this->d->query($query);
	}
	
	public function queryOne($query) {
		$a = $this->query($query);
		return $a->fetchArray(SQLITE3_NUM);
	}
	
	public function queryFirstEach($query) {
		$a = $this->query($query);
		$b = array();
		while ($c = $a->fetchArray(SQLITE3_NUM)) {
			$b[] = $c[0];
		}
		return $b;
	}
	
}
?>