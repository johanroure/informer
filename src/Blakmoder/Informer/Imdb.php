<?php namespace Blakmoder\Informer;
class Imdb {

	private $cache;

	public function parse($imid) {
		try {
			$url = sprintf('http://www.omdbapi.com/?i=%s',$imid);
			$page = file_get_contents($url);
			$this->cache = json_decode($page);
			if ($this->cache->Response != "True") {
				return false;
			}
			return true;
		} catch(Exception $e) {
			return false;
		}
	}
	public function getInfo() {
		return $this->cache;
	}
}