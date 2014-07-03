<?php namespace Blakmoder\Informer;
/*
https://api.themoviedb.org/3/search/person?api_key=97258df5b255c40496eed87d6fb26ba3&query=leonardo+dicaprio
-----> https://api.themoviedb.org/3/person/6193?api_key=97258df5b255c40496eed87d6fb26ba3
 */
class TheMovieDB {

	const BASE_IMG_URL = "https://image.tmdb.org/t/p/w";
	private $img_sizes = array('150','300','370','600','1000');
	private $api_key;
	private $cache;
	private $acache;

	function __construct ( $api_key ) {
		$this->api_key = $api_key;
	}
	public function parse( $id, $tv=false ) {
		$url = sprintf(
			"https://api.themoviedb.org/3/find/%s?api_key=%s&external_source=imdb_id", 
			$id, $this->api_key);
		try {
			$type = $tv ? 'tv_results' : 'movie_results';
			$data = file_get_contents($url);
			$this->cache = json_decode($data);
			$this->cache = $this->cache->$type;
			$this->cache = $this->cache[0];
			return true;
		} catch(Exception $e){
			return false;
		}
	}
	private function sizesArray($poster, $backdrop) {
		$posters = array();
		$backdrops = array();
		foreach ($this->img_sizes as $size) {
			$base = self::BASE_IMG_URL . $size;
			$posters[$size] =  $base . $poster;
			$backdrops[$size] = $base . $backdrop;
		}
		return array('posters' => $posters, 'backdrops' => $backdrops);
	} 
	public function getImages() {
		try {
			$object = $this->cache;
			$backdrop = $object->backdrop_path;
			$poster = $object->poster_path;
			$images = $this->sizesArray($poster, $backdrop);
			return $images;
		} catch(Exception $e){
			return false;
		}
	}
	public function getInfo() {
		return $this->cache;
	}
	public function entes( $tv=false ) {
		$type = $tv ? 'serie' : 'movie';
		$id = $this->cache->id;
		$url = sprintf(
			"https://api.themoviedb.org/3/%s/%s/credits?api_key=%s", 
			$type, $id, $this->api_key);
		try {
			$data = file_get_contents($url);
			$this->acache = json_decode($data);
			$this->acache = $this->acache->cast;
			return $this->acache;
		} catch(Exception $e){
			return false;
		}
	}
	public function enteSearch( $name="" ) {
		$id = $this->cache->id;
		$url = sprintf(
			"https://api.themoviedb.org/3/search/person?query=%s&api_key=%s", 
			$this->urlify($name), $this->api_key);
		try {
			$data = file_get_contents($url);
			$temp = json_decode($data);
			$ente = isset($temp->results[0]) ? $temp->results[0] : false;
			return $ente;
		} catch(Exception $e){
			return false;
		}
	}
	public function ente( $id ) {
		$url = sprintf(
			"https://api.themoviedb.org/3/person/%s?api_key=%s", 
			$id, $this->api_key);
		try {
			$data = file_get_contents($url);
			$ente = json_decode($data);
			return $ente;
		} catch(Exception $e){
			return false;
		}
	}
	private function urlify( $text ) {
		return urlencode($text);
	}
}