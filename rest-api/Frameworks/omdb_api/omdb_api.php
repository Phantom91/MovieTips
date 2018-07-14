<?php
	if(!class_exists('Zebra_cURL'))
		include_once('./Zebra_cURL/Zebra_cURL.php');
	
	include_once('movie.php');
	
	class OmdbApi{
		
		private $_curl;
		private $_movies;
		private $_lang;
		
		const _API_URL = "http://www.omdbapi.com/";
		
		public function __construct($lang = "en"){
			$this->_curl = new Zebra_cURL();
			$this->_curl->threads = CURL_NTHREADS;
			$this->_movies = [];
			$this->_lang = $lang;
		}
		
		public function handle($result){
			$result->body = json_decode(html_entity_decode($result->body));
			$data = $result->body;
			if($data && $data != ''){
				$this->_movies[] = new Movie($data);
			}
		}
		
		private function _makeRestCall($movies, $type = 'title'){
			$data = [];
			foreach($movies as $movie){
				if($type == 'id'){
					$data[] = self::_API_URL . '?i=' . $movie;
				}
				else if(type == 'title'){
					$data[] = self::_API_URL . '?t=' . $movie;
				}
			}
			$this->_curl->get($data, array($this,'handle'));
		}
		
		public function getMoviesInfoByTitles($titles){
			$this->_makeRestCall($titles, 'title');
			return $this->_books;
		}	
	}	
?>