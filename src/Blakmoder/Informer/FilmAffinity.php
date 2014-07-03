<?php namespace Blakmoder\Informer;
class FilmAffinity {

	private $html;
	private $info;

	public function parse( $id ) {
		try {
			$url = sprintf('http://m.filmaffinity.com/es/movie.php?id=%s', $id);
			$this->html = file_get_contents($url);
			$this->info = new \stdClass;
			$this->info->malformed = false;
			$this->info->errors = array();
			return true;
		} catch(Exception $e) {
			return false;
		}
	}
	public function getInfo() {
		$info = $this->pparse();
		if ($info !== false) {
			return $info;
		} else {
			return $this->info->errors;
		}
	}
	private function pparse() {
		$this->setTitle();
		$this->setSynopsis();
		if (!$this->info->malformed) {
			return $this->info;	
		} else {
			return false;
		}
	}
	private function clearText($text) {
		return str_replace("\n        <div>", '', $text);
	}
	private function setTitle() {
		try {
			$title = $this->cut_str($this->html, '<span>T&Iacute;TULO ORIGINAL</span>','</div>');
			$title = $this->clearText($title);
			$this->info->title = $title;
			return true;
		} catch(Exception $e) {
			$this->info->malformed = true;
			$this->info->errors[] = "title";
			return false;
		}
	}
	private function setSynopsis() {
		try {
			$synopsis = $this->cut_str($this->html,'div class="movieSynopsis">','(FILMAFFINITY)');
			$e_synopsis = utf8_encode($synopsis);
			$this->info->synopsis = $e_synopsis; 
			return true;
		} catch(Exception $e) {
			$this->info->malformed = true;
			$this->info->errors[] = "synopsis";
			return false;
		}
	}	
	private function cut_str($string, $start, $end){
		$string = " ".$string;
		$ini    = strpos($string,$start);
		if($ini==0) return "";
		$ini    += strlen($start);
		$len    = strpos($string,$end,$ini)-$ini;
		return substr($string,$ini,$len);
	}
}