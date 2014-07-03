<?php namespace Blakmoder\Informer;
class Informer {

	/*
	IDS
	 */
	private $im_id;
	private $faff_id;

	private $tmdb_key;

	/*
	RESULTADOS
	 */
	private $imdb;
	private $faff;
	private $tmdb;
	private $final;
	function __construct($tmdb_key){
		$this->tmdb_key = $tmdb_key;
	}
	public function ignite($im_id, $faff_id, $re=false) {
		$this->im_id = $im_id;
		$this->faff_id = $faff_id;
		$this->final = new \stdClass;
		$this->constructInfoProviders($re);
	}
	public function bestProvide($tv=false) {
		$imdb = $this->getIMDB();
		$this->final->title    = $imdb->Title ?: "";
		$this->final->synopsis = $imdb->Plot ?: "";
		$this->final->director = $imdb->Director ?: "";
		$this->final->actors   = $imdb->Actors ?: "";
		$this->final->poster   = $imdb->Poster ?: "";
		$this->final->genres   = $imdb->Genre ?: "";
		$this->final->year     = $imdb->Year ?: "";
		$this->final->runtime  = (int )$imdb->Runtime ?: 0;
		$this->final->rate     = (float) ($imdb->imdbRating ?: 5);
		$this->final->votes     = (int) (str_replace(',','',$imdb->imdbVotes)) ?: 100;
		$this->final->synopsis_es = "";
		$this->final->backdrop = false;
		if ($this->faff && $this->tmdb) {
			$faff = $this->getFaff();
			$tmdb = $this->getTmdb($tv);
			$images = $this->tmdb->getImages();
			$poster = $images['posters']['370'];
			$backdrop = $images['backdrops']['1000'];
			$this->final->synopsis_es = $faff->synopsis ?: "";
			$this->final->poster = $poster;
			$this->final->backdrop = $backdrop;
			$nrate = (float) ($tmdb->vote_average ?: 5);
			$votes = (int) $tmdb->vote_count ?: 100;
			$this->final->rate += $nrate; /* sumar datos */
			$this->final->votes += $votes;
			$this->final->rate = ($this->final->rate / 2); /* promedio :D */
			/* Experimental */
			$this->final->director_a =  $this->tmdb->enteSearch( $this->final->director ) ?: array();
			$this->final->actors_a   = $this->tmdb->entes($tv) ?: array();
		}
		return $this->final;
	}
	public function getEnte($id) {
		return $this->tmdb->ente($id);
	}
	private function constructInfoProviders($re) {
		$this->imdb = new Imdb();
		$this->faff = false;
		$this->tmdb = false;
		if(!$re){
			$this->faff = new FilmAffinity();
			/* Inyectar API KEY */
			$this->tmdb = new TheMovieDB( $this->tmdb_key );
		}
	}
	private function getIMDB() {
		$assert = $this->imdb->parse( $this->im_id );
		if ($assert) {
			return $this->imdb->getInfo();
		}
		return false;
	}
	private function getFaff() {
		$assert = $this->faff->parse( $this->faff_id );
		if ($assert) {
			return $this->faff->getInfo();
		}

		return false;
	}
	private function getTmdb($tv) {
		$assert = $this->tmdb->parse( $this->im_id, $tv );
		if ($assert) {
			return $this->tmdb->getInfo();
		}
		return false;
	}
}