<?php

/**
 * TMDB API v3 PHP class - wrapper to API version 3 of 'themoviedb.org
 * API Documentation: http://help.themoviedb.org/kb/api/about-3
 * Documentation and usage in README file
 *
 * @pakage TMDB_V3_API_PHP
 * @author adangq <adangq@gmail.com>
 * @copyright 2012 pixelead0
 * @date 2012-02-12
 * @link http://www.github.com/pixelead
 * @version 0.0.2
 * @license BSD http://www.opensource.org/licenses/bsd-license.php
 *
 *
 * Portions of this file are based on pieces of TMDb PHP API class - API 'themoviedb.org'
 * @Copyright Jonas De Smet - Glamorous | https://github.com/glamorous/TMDb-PHP-API
 * Licensed under BSD (http://www.opensource.org/licenses/bsd-license.php)
 * @date 10.12.2010
 * @version 0.9.10
 * @author Jonas De Smet - Glamorous
 * @link {https://github.com/glamorous/TMDb-PHP-API}
 *
 * Mostly code cleaning and documentation
 * @Copyright Alvaro Octal | https://github.com/Alvaroctal/TMDB-PHP-API
 * Licensed under BSD (http://www.opensource.org/licenses/bsd-license.php)
 * @date 09/01/2015
 * @version 0.0.2.1
 * @author Alvaro Octal
 * @link {https://github.com/Alvaroctal/TMDB-PHP-API}
 *
 * 	Function List
 *   	public function  __construct($apikey,$lang='en')
 *   	public function setLang($lang="en")
 *   	public function getLang()
 *   	public function setImageURL($config)
 *   	public function getImageURL($size="original")
 *   	public function movieTitles($idMovie)
 *   	public function movieTrans($idMovie)
 *   	public function movieTrailer($idMovie,$source="")
 *   	public function movieDetail($idMovie)
 *   	public function moviePoster($idMovie)
 *   	public function movieCast($idMovie)
 *   	public function movieInfo($idMovie,$option="",$print=false)
 *   	public function searchMovie($movieTitle)
 *   	public function getConfig()
 *   	public function latestMovie()
 *   	public function nowPlayingMovies($page=1)
 *
 *   	private function _getDataArray($action,$text,$lang="")
 *   	private function setApikey($apikey)
 *   	private function getApikey()
 *
 *
 * 	URL LIST:
 *   	configuration		http://api.themoviedb.org/3/configuration
 * 		Image				http://cf2.imgobject.com/t/p/original/IMAGEN.jpg #### echar un ojo ####
 * 		Search Movie		http://api.themoviedb.org/3/search/movie
 * 		Search Person		http://api.themoviedb.org/3/search/person
 * 		Movie Info			http://api.themoviedb.org/3/movie/11
 * 		Casts				http://api.themoviedb.org/3/movie/11/casts
 * 		Posters				http://api.themoviedb.org/3/movie/11/images
 * 		Trailers			http://api.themoviedb.org/3/movie/11/trailers
 * 		translations		http://api.themoviedb.org/3/movie/11/translations
 * 		Alternative titles 	http://api.themoviedb.org/3/movie/11/alternative_titles
 *
 * 		// Collection Info 	http://api.themoviedb.org/3/collection/11
 * 		// Person images		http://api.themoviedb.org/3/person/287/images
 */

 /* Improved by Darius Mihai Popescu to support multiple queries at once and more*/

include("data/Movie.php");
include("data/TVShow.php");
include("data/Season.php");
include("data/Episode.php");
include("data/Person.php");
include("data/Role.php");
include("data/roles/MovieRole.php");
include("data/roles/TVShowRole.php");
include("data/Collection.php");
include("Frameworks/Zebra_cURL/Zebra_cURL.php");

class TMDB{

	#@var string url of API TMDB
	const _API_URL_ = "http://api.themoviedb.org/3/";

	#@var string Version of this class
	const VERSION = '0.0.2.1';

	#@var string API KEY
	private $_apikey;

	#@var string Default language
	private $_lang;

	#@var array of TMDB config
    private $_config;

	#@var boolean for testing
	private $_debug;

	private $_multi_curl;

	private $_movies_data;

	private $_actor_ids;

	private $_maximum_limit_reached;

	/**
	 * 	Construct Class
	 *
	 * 	@param string $apikey The API key token
	 * 	@param string $lang The languaje to work with, default is english
	 */
	public function __construct($apikey, $lang = 'en', $debug = false, $delay = 0) {

		// Sets the API key
		$this->setApikey($apikey);

		// Setting Language
		$this->setLang($lang);

		// Set the debug mode
		$this->_debug = $debug;

		$this->_multi_curl = new Zebra_cURL();
		$this->_multi_curl->threads = CURL_NTHREADS;
		$this->_multi_curl->pause_interval = $delay;
		$this->_maximum_limit_reached = false;

		$this->_actor_ids = [];
	}

	//------------------------------------------------------------------------------
	// Api Key
	//------------------------------------------------------------------------------

	/**
	 * 	Set the API key
	 *
	 * 	@param string $apikey
	 * 	@return void
	 */
	private function setApikey($apikey) {
		$this->_apikey = (string) $apikey;
	}

	/**
	 * 	Get the API key
	 *
	 * 	@return string
	 */
	private function getApikey() {
		return $this->_apikey;
	}

	//------------------------------------------------------------------------------
	// Language
	//------------------------------------------------------------------------------

	/**
	 *  Set the language
	 *	By default english
	 *
	 * 	@param string $lang
	 */
	public function setLang($lang = 'en') {
		$this->_lang = $lang;
	}

	/**
	 * 	Get the language
	 *
	 * 	@return string
	 */
	public function getLang() {
		return $this->_lang;
	}

	public function maximumLimitReached(){
		$_is_reached = $this->_maximum_limit_reached;
		$this->_maximum_limit_reached = false;
		return $_is_reached;
	}

	//------------------------------------------------------------------------------
	// Config
	//------------------------------------------------------------------------------

	/**
	 * 	Loads the configuration of the API
	 *
	 * 	@return boolean
	 */
	private function _loadConfig() {
		$this->_config = $this->_call('configuration', '');

		return ! empty($this->_config);
	}

	/**
	 * 	Get Configuration of the API (Revisar)
	 *
	 * 	@return array
	 */
	public function getConfig(){
		return $this->_config;
	}

	//------------------------------------------------------------------------------
	// Get Variables
	//------------------------------------------------------------------------------

	/**
	 *	Get the URL images
	 * 	You can specify the width, by default original
	 *
	 * 	@param String $size A String like 'w185' where you specify the image width
	 * 	@return string
	 */
	public function getImageURL($size = 'original') {
		return $this->_config['images']['base_url'] . $size;
	}

	/**
	 * 	Get Movie Info
	 * 	Gets part of the info of the Movie, mostly used for the lazy load
	 *
	 * 	@param int $idMovie The Movie id
	 *  @param string $option The request option
	 * 	@param string $append_request additional request
	 * 	@return array
	 *	@deprecated
	 */
	public function getMovieInfo($idMovie, $option = '', $append_request = ''){
		$option = (empty($option)) ? '' : '/' . $option;
		$params = 'movie/' . $idMovie . $option;
		$movieInfo = new Movie($this->_call($params, $append_request));

		return $movieInfo;
	}

	//------------------------------------------------------------------------------
	// Get Lists of Movies
	//------------------------------------------------------------------------------

	/**
	* 	Get latest Movie
	 *
	 * 	@return Movie
	 */
	public function getLatestMovie() {
		return new Movie($this->_call('movie/latest',''));
	}

	public function _getData($result){
		if($result->info['http_code'] == '429'){
			$this->_maximum_limit_reached = true;
			return;
		}
		$result->body = json_decode(html_entity_decode($result->body));
		if(array_key_exists('status_code', $result->body) && $result->body->status_code == '34'){
			return;
		}
		if(isset($result->body->results)){
			$results = $result->body->results;
			foreach($results as $movie){
				array_push($this->_movies_data, new Movie((array)$movie));
			}
		}else{
			array_push($this->_movies_data, new Movie((array)$result->body));
		}
	}

	public function extractIds($result){
		$result = json_decode(html_entity_decode($result->body));
		if(isset($result->results) && isset($result->results[0]->id)){
			 $this->_actor_ids[$result->results[0]->name] = $result->results[0]->id;
		}
	}

	/*public function getMoviesData($ids){
		$movies_urls = [];
		foreach($ids as $id){
			$movies_urls[] = self::_API_URL_ . 'movie/' . $id . '?api_key='. $this->getApikey() . '&language='. $this->getLang() . "&append_to_response=similar,reviews,trailers,casts,translations";
		}
		$this->_multi_curl->get($movies_urls, array($this,'_getData'));
		return $this->_movies_data;
	}

	public function getMoviesByQueries($queries){
		$this->_multi_curl->get($queries, array($this,'_getDataForQueries'));
		return $this->_movies_data;
	}

	public function getActorsIdsByNames($names){
		$actors_queries = [];
		foreach($names as $name){
			$actors_queries[] = self::_API_URL_ . 'search/person?api_key=' . $this->getApikey() . '&query=' . urlencode($name);
		}
		$this->_multi_curl->get($actors_queries, array($this,'extractIds'));
		return $this->_actor_ids;
	}*/

	//------------------------------------------------------------------------------
	// Get Lists of Persons
	//------------------------------------------------------------------------------

	/**
	 * 	Get latest Person
	 * 	Get latest Person
	 *
	 * 	@return Person
	 */
	public function getLatestPerson() {
		return new Person($this->_call('person/latest',''));
	}

	/**
	 * 	Get Popular Persons
	 *
	 * 	@return Person[]
	 */
	public function getPopularPersons($page = 1) {
		$persons = array();

		$result = $this->_call('person/popular','page='. $page);

		foreach($result['results'] as $data){
			$persons[] = new Person($data);
		}

		return $persons;
	}

	private function _user_agent()
	 {

			// browser version: 9 or 10
			$version = rand(9, 10);

			// windows version; here are the meanings:
			// Windows NT 6.2   ->  Windows 8                                       //  can have IE10
			// Windows NT 6.1   ->  Windows 7                                       //  can have IE9 or IE10
			// Windows NT 6.0   ->  Windows Vista                                   //  can have IE9
			$major_version = 6;

			$minor_version =

					// for IE9 Windows can have "0", "1" or "2" as minor version number
					$version == 8 || $version == 9 ? rand(0, 2) :

					// for IE10 Windows will have "2" as major version number
					2;

			// add some extra information
			$extras = rand(0, 3);

			// return the random user agent string
			return 'Mozilla/5.0 (compatible; MSIE ' . $version . '.0; Windows NT ' . $major_version . '.' . $minor_version . ($extras == 1 ? '; WOW64' : ($extras == 2 ? '; Win64; IA64' : ($extras == 3 ? '; Win64; x64' : ''))) . ')';

	 }

	//------------------------------------------------------------------------------
	// API Call
	//------------------------------------------------------------------------------

	/**
	 * 	Makes the call to the API and retrieves the data as a JSON
	 *
	 * 	@param string $action	API specific function name for in the URL
	 * 	@param string $appendToResponse	The extra append of the request
	 * 	@return string
	 */
	private function _call($action, $appendToResponse){
		$url = self::_API_URL_.$action .'?api_key='. $this->getApikey() .'&language='. $this->getLang() .'&'.$appendToResponse;

		if ($this->_debug) {
			echo '<pre><a href="' . $url . '">check request</a></pre>';
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$error_number = curl_errno($ch);
		$error_message = curl_error($ch);
		//var_dump($error_number);
		//var_dump($error_message);

		$results = json_decode(curl_exec($ch), true);
		if(array_key_exists('status_code', $results) && $results['status_code'] == "34"){
			sleep(10);
			return $this->_call($action, $appendToResponse);
		}

		curl_close($ch);

		return (array) $results;
	}

	/**
	 * 	Makes multiple calls to the API and retrieves the data as an array of objects using the _getData helper function
	 *
	 * 	@param Array of objects with two properties :
	*	action -> API specific function name for in the URL
	 * 	appendToResponse -> The extra append of the request
	 * 	@return none
	 */
	private function _call_multi_queries($actions){
		$urls = array();
		$this->_movies_data = array();
		foreach($actions as $data){
			$url = self::_API_URL_. $data->action .'?api_key='. $this->getApikey() .'&language='. $this->getLang() .'&'.$data->appendToResponse;
			array_push($urls, $url);
		}
		$this->_multi_curl->get($urls, array($this,'_getData'));
		return $this->_movies_data;
	}

	//------------------------------------------------------------------------------
	// Get Data Objects
	//------------------------------------------------------------------------------

	/**
	 * 	Get a Movie
	 *
	 * 	@param int $idMovie The Movie id
	 * 	@param string $appendToResponse The extra append of the request, by default all
	 * 	@return Movie
	 */
	public function getMovie($idMovie, $appendToResponse = 'append_to_response=trailers,casts,translations'){
		return new Movie($this->_call('movie/' . $idMovie, $appendToResponse));
	}
	
	
	/**
	 * 	Get infos about movies
	 *
	 * 	@param array[] array of Movie ids
	 * 	@return Movie[]
	*/
	public function getMovies($movieIds){
		$queries = array();
		foreach($movieIds as $movieID){
			$query = new stdClass();
			$query->action = 'movie/' . $movieID;
			$query->appendToResponse = 'append_to_response=trailers,casts';
			array_push($queries, $query);
		}
		return $this->_call_multi_queries($queries);
	}

	/**
	 * 	Get a TVShow
	 *
	 * 	@param int $idTVShow The TVShow id
	 * 	@param string $appendToResponse The extra append of the request, by default all
	 * 	@return TVShow
	 */
	public function getTVShow($idTVShow, $appendToResponse = 'append_to_response=trailers,images,casts,translations,keywords'){
		return new TVShow($this->_call('tv/' . $idTVShow, $appendToResponse));
	}

	/**
	 * 	Get a Season
	 *
	 *  @param int $idTVShow The TVShow id
	 *  @param int $numSeason The Season number
	 * 	@param string $appendToResponse The extra append of the request, by default all
	 * 	@return Season
	 */
	public function getSeason($idTVShow, $numSeason, $appendToResponse = 'append_to_response=trailers,images,casts,translations'){
		return new Season($this->_call('tv/'. $idTVShow .'/season/' . $numSeason, $appendToResponse), $idTVShow);
	}

	/**
	 * 	Get a Season by Number
	 *
	 *  @param int $idTVShow The TVShow id
	 *  @param int $numSeason The Season number
	 * 	@param string $appendToResponse The extra append of the request, by default all
	 * 	@return Season
	 */
	/*public function getSeasonByNumber($idTVShow, $numSeason, $appendToResponse = 'append_to_response=trailers,images,casts,translations'){
		return new Season($this->_call('tv/'. $idTVShow .'/season/' . $numSeason, $appendToResponse));
	}*/

	/**
	 * 	Get a Episode
	 *
	 *  @param int $idEpisode The Episode id
	 * 	@param string $appendToResponse The extra append of the request, by default all
	 * 	@return Episode
	 */
	/*public function getEpisode($idEpisode, $appendToResponse = 'append_to_response=trailers,images,casts,translations'){
		return new Episode($this->_call('tv/season/episode/' . $idEpisode, $appendToResponse));
	}*/

	/**
	 * 	Get an Episode
	 *
	 *  @param int $idTVShow The TVShow id
	 *  @param int $numSeason The Season number
	 *  @param int $numEpisode the Episode number
	 * 	@param string $appendToResponse The extra append of the request, by default all
	 * 	@return Episode
	 */
	public function getEpisode($idTVShow, $numSeason, $numEpisode, $appendToResponse = 'append_to_response=trailers,images,casts,translations'){
		return new Episode($this->_call('tv/'. $idTVShow .'/season/'. $numSeason .'/episode/'. $numEpisode, $appendToResponse), $idTVShow);
	}

	/**
	 * 	Get a Person
	 *
	 * 	@param int $idPerson The Person id
	 * 	@param string $appendToResponse The extra append of the request, by default all
	 * 	@return Person
	 */
	public function getPerson($idPerson, $appendToResponse = 'append_to_response=tv_credits,movie_credits'){
		return new Person($this->_call('person/' . $idPerson, $appendToResponse));
	}

	/**
	 * 	Get a Collection
	 *
	 * 	@param int $idCollection The Person id
	 * 	@param string $appendToResponse The extra append of the request, by default all
	 * 	@return Collection
	 */
	public function getCollection($idCollection, $appendToResponse = 'append_to_response=images'){
		return new Collection($this->_call('collection/' . $idCollection, $appendToResponse));
	}

	//------------------------------------------------------------------------------
	// Searches
	//------------------------------------------------------------------------------

	/**
	 *  Search Movie
	 *
	 * 	@param string $movieTitle The title of a Movie
	 * 	@return Movie[]
	 */
	public function searchMovie($movieTitle){

		$movies = array();

		$result = $this->_call('search/movie', 'query='. urlencode($movieTitle));

		foreach($result['results'] as $data){
			$movies[] = new Movie($data);
		}

		return $movies;
	}

	/**
	 *  Search TVShow
	 *
	 * 	@param string $tvShowTitle The title of a TVShow
	 * 	@return TVShow[]
	 */
	public function searchTVShow($tvShowTitle){

		$tvShows = array();

		$result = $this->_call('search/tv', 'query='. urlencode($tvShowTitle));

		foreach($result['results'] as $data){
			$tvShows[] = new TVShow($data);
		}

		return $tvShows;
	}

	/**
	 *  Search Person
	 *
	 * 	@param string $personName The name of the Person
	 * 	@return Person[]
	 */
	public function searchPerson($personName){

		$persons = array();

		$result = $this->_call('search/person', 'query='. urlencode($personName));

		foreach($result['results'] as $data){
			$persons[] = new Person($data);
		}

		return $persons;
	}

	/**
	 *  Search Collection
	 *
	 * 	@param string $collectionName The name of the Collection
	 * 	@return Collection[]
	 */
	public function searchCollection($collectionName){

		$collections = array();

		$result = $this->_call('search/collection', 'query='. urlencode($collectionName));

		foreach($result['results'] as $data){
			$collections[] = new Collection($data);
		}

		return $collections;
	}

	/**
	 *  Get the list of official genres for movies
	 *
	 * 	@param none
	 * 	@return Array[]
	 */

	public function getMoviesGenresList(){
	 	return $this->_call('genre/movie/list', '');
	}

	/**
	 *  Discover movies by different criterias and with the posibily of multiple queries by once
	 *
	 * 	@param Array[] or Array[Array[]]
	 *  @param Boolean
	 * 	@return Movie[]
	 */

	public function discoverMovies($criterias, $multi_queries = false){
		function _formQueryBasedOnCondition($query, $condition, $value){
			switch($condition){
				case 'region' :
					$query .= 'region=' . $value;
					break;
				case 'sortby' : 
					$query .= 'sort_by=' . $value;
					break;
				case 'certification_country' :
					$query .= 'certification_country=' . $value;	
					break;
				case 'certification' :
					$query .= 'certification=' . $value;	
					break;
				case 'certification_less_than' :
					$query .= 'certification.lte=' . $value;	
					break;
				case 'include_adult' :
					$query .= 'include_adult=' . $value;		
					break;
				case 'include_video' :
					$query .= 'include_video=' . $value;	
					break;
				case 'page' :
					$query .= 'page=' . $value;
					break;
				case 'primary_release_year' :
					$query .= 'primary_release_year=' . $value;
					break;
				case 'primary_release_date_greater_than' :  
					$query .= 'primary_release_date.gte=' . $value;
					break;
				case 'primary_release_date_less_than' :  
					$query .= 'primary_release_date.lte=' . $value;
					break;		
				case 'release_date_greater_than' :  
					$query .= 'release_date.gte=' . $value;
					break;
				case 'release_date_less_than' :  
					$query .= 'release_date.lte=' . $value;	
					break;
				case 'vote_count_greater_than' :
					$query .= 'vote_count.gte=' . $value;
					break;
				case 'vote_count_less_than' :
					$query .= 'vote_count.lte=' . $value;	
					break;
				case 'vote_average_greater_than' :
					$query .= 'vote_average.gte=' . $value;
					break;
				case 'vote_average_less_than' :
					$query .= 'vote_average.lte=' . $value;
					break;
				case 'with_cast' :
					$query .= 'with_cast=' . $value;
					break;
				case 'with_crew' :
					$query .= 'with_crew=' . $value;	
					break;
				case 'with_companies' :
					$query .= 'with_companies=' . $value;
					break;
				case 'with_genres' :
					$query .= 'with_genres=' . $value;
					break;
				case 'with_keywords' :
					$query .= 'with_keywords=' . $value;
					break;
				case 'with_people' :
					$query .= 'with_people=' . $value;
					break;
				case 'year' :
					$query .= 'year=' . $value;	
					break;
				case 'without_genres' :
					$query .= 'without_genres=' . $value;	
					break;
				case 'with_runtime_greater_than' :
					$query .= 'with_runtime.gte=' . $value;	
					break;
				case 'with_runtime_less_than' :
					$query .= 'with_runtime.lte=' . $value;	
					break;
				case 'with_release_type' :
					$query .= 'with_release_type=' . $value;	
					break;
				case 'with_original_language' :
					$query .= 'with_original_language=' . $value;	
					break;	
				case 'without_keywords' :
					$query .= 'without_keywords=' . $value;	
					break;	
				default :
					break;
			}
			return $query;
		}
		if($multi_queries){
			$actions = array();
			foreach($criterias as $criteriasArray){
				$entry = new stdClass();
				$entry->action = 'discover/movie';
				$query = '';
				foreach(array_keys($criteriasArray) as $key){
					if($query != ''){
						$query .= '&amp';
					}
					$query .= _formQueryBasedOnCondition($innerQuery, $key, $criteriasArray[$key]);
				}
				$entry->appendToResponse = $query;
				array_push($actions, $entry);
			}
			return $this->_call_multi_queries($actions);
		}else{
			$movies = array();
			$query = '';
			foreach(array_keys($criterias) as $key){
				if($query != ''){
					$query .= '&amp';
				}
				$query = _formQueryBasedOnCondition($query, $key, $criterias[$key]); 
			}
			$result = $this->_call('discover/movie', 'query='. urlencode($query));

			foreach($result['results'] as $data){
				$movies[] = new Movie($data);
			}
			return $movies;
		}
	}

	/**
	 *  Latest movies
	 *
	 * 	@param none
	 * 	@return Movie[]
	 */
	public function latestMovies() {

		$movies = array();

		$result = $this->_call('movie/latest', '');

		foreach($result['results'] as $data){
			$movies[] = new Movie($data);
		}

		return $movies;
	}

	/**
	 *  Most popular movies
	 *
	 * 	@param integer $page optional
	 * 	@param string $region optional
	 * 	@return Movie[]
	 */
	public function popularMovies($page = 1, $region = 'US') {

		$movies = array();

		$result = $this->_call('movie/popular', 'page='. $page . '&region=' . $region);

		foreach($result['results'] as $data){
			$movies[] = new Movie($data);
		}

		return $movies;
	}

	/**
	 *  Similar movies
	 *
	 * 	@param integer movie_id
	 *	@param integer page optional
	 * 	@return Movie[]
	 */
	public function similarMovies($movie_id, $page=1) {

		$movies = array();

		$result = $this->_call('movie/' . $movie_id . '/similar', 'page='. $page);

		foreach($result['results'] as $data){
			$movies[] = new Movie($data);
		}

		return $movies;
	}

	/**
	 *  Movies recomandations
	 *
	 * 	@param integer movie_id
	 *	@param integer page optional
	 * 	@return Movie[]
	 */
	public function moviesRecomandations($movie_id, $page=1) {

		$movies = array();

		$result = $this->_call('movie/' . $movie_id . '/recommendations', 'page='. $page);

		foreach($result['results'] as $data){
			$movies[] = new Movie($data);
		}

		return $movies;
	}

	/**
	 *  Now Playing Movies
	 *
	 * 	@param integer $page optional
	 * 	@param string $region optional
	 * 	@return Movie[]
	 */
	public function nowPlayingMovies($page = 1, $region = 'US') {

		$movies = array();

		$result = $this->_call('movie/now_playing', 'page='. $page . '&region=' . $region);

		foreach($result['results'] as $data){
			$movies[] = new Movie($data);
		}

		return $movies;
	}

	/**
	 * Get the top rated movies on TMDb
	 *
	 * 	@param integer $page optional
	 * 	@param string $region optional
	 * 	@return Movie[]
	 */
	public function topRatedMovies($page = 1, $region = 'US') {

		$movies = array();

		$result = $this->_call('movie/top_rated', 'page='. $page . '&region=' . $region);

		foreach($result['results'] as $data){
			$movies[] = new Movie($data);
		}

		return $movies;
	}

	/**
	 * Get upcoming movies from TMDb
	 *
	 * 	@param integer $page optional
	 * 	@param string $region optional
	 * 	@return Movie[]
	 */
	public function upcomingMovies($page = 1, $region = 'US') {

		$movies = array();

		$result = $this->_call('movie/upcoming', 'page='. $page . '&region=' . $region);

		foreach($result['results'] as $data){
			$movies[] = new Movie($data);
		}

		return $movies;
	}
	
	/**
	 *  Movie trailers
	 *
	 * 	@param integer movie_id
	 * 	@return Array[]
	*/
	public function movieTrailers($movie_id) {

		$movies = array();

		$result = $this->_call('movie/' . $movie_id . '/videos', '');

		return $results;
	}
}
?>