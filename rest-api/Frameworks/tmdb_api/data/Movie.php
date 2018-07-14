<?php
/**
 * 	This class handles all the data you can get from a Movie
 *
 * 	@author Alvaro Octal | <a href="https://twitter.com/Alvaro_Octal">Twitter</a>
 * 	@version 0.1
 * 	@date 09/01/2015
 * 	@link https://github.com/Alvaroctal/TMDB-PHP-API
 * 	@copyright Licensed under BSD (http://www.opensource.org/licenses/bsd-license.php)
 */

class Movie{

	//------------------------------------------------------------------------------
	// Class Variables
	//------------------------------------------------------------------------------

	private $_data;
	private $_tmdb;

	/**
	 * 	Construct Class
	 *
	 * 	@param array $data An array with the data of the Movie
	 */
	public function __construct($data) {
		$this->_data = $data;
	}
	
	public function isEmpty(){
		return count($this->_data) == 0;
	}

	//------------------------------------------------------------------------------
	// Get Variables
	//------------------------------------------------------------------------------

	/** 
	 * 	Get the Movie's id
	 *
	 * 	@return int
	 */
	public function getID() {
		return $this->_data['id'];
	}

	/** 
	 * 	Get the Movie's title
	 *
	 * 	@return string
	 */
	public function getTitle() {
		return $this->_data['title'];
	}

	/** 
	 * 	Get the Movie's tagline
	 *
	 * 	@return string
	 */
	public function getTagline() {
		return $this->_data['tagline'];
	}

	/** 
	 * 	Get the Movie's Poster
	 *
	 * 	@return string
	 */
	public function getPoster() {
		return $this->_data['poster_path'];
	}
	

	/** 
	 * 	Get the Movie's vote average
	 *
	 * 	@return int
	 */
	public function getVoteAverage() {
		return $this->_data['vote_average'];
	}
	
	public function getGenres(){
		return $this->_data['genre_ids'];
	}
	
	
	public function getReleaseDate(){
		return $this->_data['release_date'];
	}

	/** 
	 * 	Get the Movie's vote count
	 *
	 * 	@return int
	 */
	public function getVoteCount() {
		return $this->_data['vote_count'];
	}

	/** 
	 * 	Get the Movie's trailers
	 *
	 * 	@return array
	 */
	private function getTrailers() {

		if (empty($this->_data['trailers']) && isset($this->_tmdb)){
			$this->loadTrailer();
		}

		return (array)$this->_data['trailers'];
	}

	/** 
	 * 	Get the Movie's trailer
	 *
	 * 	@return string
	 */
	public function getTrailer() {
		$data = $this->getTrailers();
		if(!isset($data['youtube'][0])){
			return '';
		}
		$trailer = (array)($data['youtube'][0]);
		if(isset($trailer['source'])){
			return $trailer['source'];
		}
	}

	/**
	 *  Get Generic.<br>
	 *  Get a item of the array, you should not get used to use this, better use specific get's.
	 *
	 * 	@param string $item The item of the $data array you want
	 * 	@return array
	 */
	public function get($item = ''){
		return (empty($item)) ? $this->_data : $this->_data[$item];
	}
	
	public function getCasting(){
		return $this->_data['casts'];
	}

	//------------------------------------------------------------------------------
	// Load Variables
	//------------------------------------------------------------------------------

	/**
	 * 	Load the images of the Movie
	 *	Used in a Lazy load technique
	 */
	public function loadImages(){
		$this->_data['images'] = $this->_tmdb->getMovieInfo($this->getID(), 'images', false);
	}

	/**
	 * 	Load the trailer of the Movie
	 *	Used in a Lazy load technique
	 */
	public function loadTrailer() {
		$this->_data['trailers'] = $this->_tmdb->getMovieInfo($this->getID(), 'trailers', false);
	}

	/**
	 * 	Load the casting of the Movie
	 *	Used in a Lazy load technique
	 */
	public function loadCasting(){
		$this->_data['casts'] = $this->_tmdb->getMovieInfo($this->getID(), 'casts', false);
	}

	/**
	 * 	Load the translations of the Movie
	 *	Used in a Lazy load technique
	 */
	public function loadTranslations(){
		$this->_data['translations'] = $this->_tmdb->getMovieInfo($this->getID(), 'translations', false);
	}
	
	public function getImdbId(){
		return $this->_data['imdb_id'];
	}
	
	public function getProductionCountry(){
		if(isset($this->_data['production_countries']))
			if(isset($this->_data['production_countries'][0]))
				return $this->_data['production_countries'][0]->{'iso_3166_1'};
		return '';
	}
	
	public function getReviews(){
		return $this->_data['reviews'];
	}

	//------------------------------------------------------------------------------
	// Import an API instance
	//------------------------------------------------------------------------------

	/**
	 *	Set an instance of the API
	 *
	 *	@param TMDB $tmdb An instance of the api, necessary for the lazy load
	 */
	public function setAPI($tmdb){
		$this->_tmdb = $tmdb;
	}

	//------------------------------------------------------------------------------
	// Export
	//------------------------------------------------------------------------------

	/** 
	 * 	Get the JSON representation of the Movie
	 *
	 * 	@return string
	 */
	public function getJSON() {
		return json_encode($this->_data, JSON_PRETTY_PRINT);
	}
	
	public function getSimilarMoviesIds(){
		$ids = [];
		if(count($this->_data['similar']) > 0){
			foreach($this->_data['similar']->results as $similar_movie){
				$ids[] = $similar_movie->id;
			}
		}
		return $ids;
	}
	
	public function getMovieGenres(){
		return $this->_data['genres'];
	}
	
	public function getMovieLanguages(){
		return $this->_data['spoken_languages'];
	}

	public function getOriginalLanguage(){
		return $this->_data['original_language'];
	}
}
?>