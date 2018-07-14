<?php
	require_once('./RecommendationsEngine.php');
    require_once('./Frameworks/tmdb_api/tmdb-api.php');
    require_once('./config.php');

	class MoviesController {
		private $tmdbApi = null;
		private $recommendationsEngine = null;
		private $moviesProperties = array('genre', 'release_year', 'vote_average', 'original_language');
		private $redis = null;
		private $language = '';
		private $region = '';
		private $prefered_languages = array();

		private function compute_hash_from_string($str){
			$sum = 0;
			for($i = 0; $i < strlen(str); $i++){
				$sum += ord(str[$i]);
			}
			return $sum;
		}

		function __construct($lang = 'en', $region = 'US', $prefered_languages = array(0 => 'en')) {
			$this->language = $lang;
			$this->region = $region;
			$this->prefered_languages = $prefered_languages;
			$this->tmdbApi = new TMDB(TMDB_API_KEY, $this->language);
			$this->recommendationsEngine = new RecommendationsEngine($this->moviesProperties);
			$this->redis = new Redis();
			$this->redis->connect('127.0.0.1', 6379);
		}
		
		private function GetGenresList(){
			return $this->tmdbApi->getMoviesGenresList();
		}

		public function GetPopularMovies(){
			return $this->tmdbApi->popularMovies(1 + rand() % 5, $this->region);
		}

		public function GetNowPlayingMovies(){
			return $this->tmdbApi->nowPlayingMovies(1 + rand() % 5, $this->region);
		}

		public function GetLatestMovies(){
			return $this->tmdbApi->latestMovies();
		}

		public function GetTopRatedMovies(){
			return $this->tmdbApi->topRated(1 + rand() % 5, $this->region);
		}

		public function GetUpcomingMovies(){
			return $this->tmdbApi->upcomingMovies(1 + rand() % 5, $this->region);
		}

		public function GetMovieTrailers($movieID){
			return $this->tmdbApi->movieTrailers($movieID);
		}

		private function DiscoverMovies(){
			$movies = array();
			$criteriasArrays = array();
			$max_page_nr = 10;
			$now_year = date('Y', time());
			$sort_criterias = array('popularity.asc', 'popularity.desc', 'revenue.asc', 'revenue.desc', 'release_date.asc', 'release_date.desc', 'original_tile.asc', 'original_title.desc', 'vote_average.asc', 'vote_average.desc', 'vote_count.asc', 'vote_count.desc', 'primary_release_date.asc', 'primary_release_date.desc');
			for($i = 1; $i <= $max_page_nr; $i++){
				array_push($criteriasArrays, array('sortby' => $sort_criterias[rand() % count($sort_criterias)], 'region' => $this->region,'page' => $i, 'include_video' => 'true', 'release_date_greater_than' => ($now_year - 15) . '-01-01'));
			}
			$movies = $this->tmdbApi->discoverMovies($criteriasArrays, true);
			shuffle($movies);
			return $movies;
		}

		private function PostProcessRecomandations(&$recommendations){
			$average_year = 2006;
			
			//eliminate duplicates
			$n = count($recommendations);
			for($i = 0; $i < $n-1; $i++){
				$flag = false;
				for($j = $i+1; $j < $n; $j++){
					if($recommendations[$i]->GetID() === $recommendations[$j]->GetID()){
						$flag = true;
						break;
					}
				}
				if($flag){
					unset($recommendations[$i]);
				}
			}

			foreach($recommendations as $index => $recommendation){
				$release_year = date('Y', strtotime($recommendation->getReleaseDate()));
				$original_language = $recommendation->getOriginalLanguage();
				if($release_year < $average_year || array_search($original_language, $this->prefered_languages, true) === FALSE){
					unset($recommendations[$index]);
				}
			}
			return array_values($recommendations);
		}

		public function SimulateRecomandations(){
			$recommendations = array();
			$this->recommendationsEngine->AddObjectToDataSet(array('genre' => $this->compute_hash_from_string('Comedy'), 'release_year' => 2005, 'vote_average' => 6.5, 'original_language' => $this->compute_hash_from_string('ro'), 'user_likes_it' => 'yes'));
			$this->recommendationsEngine->AddObjectToDataSet(array('genre' => $this->compute_hash_from_string('Comedy'), 'release_year' => 2003, 'vote_average' => 5.7, 'original_language' => $this->compute_hash_from_string('ja'), 'user_likes_it' => 'no'));    
			$this->recommendationsEngine->AddObjectToDataSet(array('genre' => $this->compute_hash_from_string('Comedy'), 'release_year' => 2008, 'vote_average' => 6.9, 'original_language' => $this->compute_hash_from_string('en'), 'user_likes_it' => 'yes'));  
			$this->recommendationsEngine->AddObjectToDataSet(array('genre' => $this->compute_hash_from_string('Comedy'), 'release_year' => 2010, 'vote_average' => 7.1, 'original_language' => $this->compute_hash_from_string('en'), 'user_likes_it' => 'yes'));  
			$this->recommendationsEngine->AddObjectToDataSet(array('genre' => $this->compute_hash_from_string('Comedy'), 'release_year' => 2012, 'vote_average' => 7.5, 'original_language' => $this->compute_hash_from_string('en'), 'user_likes_it' => 'yes'));  

			$this->recommendationsEngine->AddObjectToDataSet(array('genre' => $this->compute_hash_from_string('Romance'), 'release_year' => 2002, 'vote_average' => 6.1, 'original_language' => $this->compute_hash_from_string('ja'), 'user_likes_it' => 'no'));
			$this->recommendationsEngine->AddObjectToDataSet(array('genre' => $this->compute_hash_from_string('Romance'), 'release_year' => 2008, 'vote_average' => 5.9, 'original_language' => $this->compute_hash_from_string('ja'), 'user_likes_it' => 'no'));    
			$this->recommendationsEngine->AddObjectToDataSet(array('genre' => $this->compute_hash_from_string('Romance'), 'release_year' => 2015, 'vote_average' => 8.3, 'original_language' => $this->compute_hash_from_string('en'), 'user_likes_it' => 'yes'));  
			$this->recommendationsEngine->AddObjectToDataSet(array('genre' => $this->compute_hash_from_string('Romance'), 'release_year' => 2004, 'vote_average' => 7.1, 'original_language' => $this->compute_hash_from_string('en'), 'user_likes_it' => 'yes'));  
			$this->recommendationsEngine->AddObjectToDataSet(array('genre' => $this->compute_hash_from_string('Romance'), 'release_year' => 2012, 'vote_average' => 7.9, 'original_language' => $this->compute_hash_from_string('en'), 'user_likes_it' => 'yes'));  

			$this->recommendationsEngine->AddObjectToDataSet(array('genre' => $this->compute_hash_from_string('Action'), 'release_year' => 2001, 'vote_average' => 6.1, 'original_language' => $this->compute_hash_from_string('fr'), 'user_likes_it' => 'no'));
			$this->recommendationsEngine->AddObjectToDataSet(array('genre' => $this->compute_hash_from_string('Action'), 'release_year' => 2004, 'vote_average' => 6.9, 'original_language' => $this->compute_hash_from_string('en'), 'user_likes_it' => 'yes'));  
			$this->recommendationsEngine->AddObjectToDataSet(array('genre' => $this->compute_hash_from_string('Action'), 'release_year' => 1995, 'vote_average' => 5.7, 'original_language' => $this->compute_hash_from_string('fr'), 'user_likes_it' => 'no'));    
			$this->recommendationsEngine->AddObjectToDataSet(array('genre' => $this->compute_hash_from_string('Action'), 'release_year' => 2011, 'vote_average' => 7.1, 'original_language' => $this->compute_hash_from_string('ro'), 'user_likes_it' => 'yes'));  
			$this->recommendationsEngine->AddObjectToDataSet(array('genre' => $this->compute_hash_from_string('Action'), 'release_year' => 2011, 'vote_average' => 7.1, 'original_language' => $this->compute_hash_from_string('ja'), 'user_likes_it' => 'no'));  

			$genresList = $this->GetGenresList();
			$movies = $this->DiscoverMovies();
			foreach($movies as $movie){
				$vote_average = $movie->getVoteAverage();
				$release_year = date('Y', strtotime($movie->getReleaseDate()));
				$genresIDs = $movie->getGenres();
				foreach($genresList['genres'] as $genreObject){
					foreach($genresIDs as $genreID){
						if($genreID === $genreObject['id']){
							$movie_genre = $genreObject['name'];
							break;
						}
					}
				}
				$nodeArray = array();
				$nodeArray['genre'] = $this->compute_hash_from_string($movie_genre);
				$nodeArray['release_year'] = $release_year;
				$nodeArray['vote_average'] = $movie->getVoteAverage();
				$nodeArray['original_language'] = $this->compute_hash_from_string($movie->getOriginalLanguage());
				$guess = $this->recommendationsEngine->GuessRecomandation($nodeArray, 'user_likes_it');
				if($guess === 'yes'){
					array_push($recommendations, $movie);
				}
			}
			return $this->FindMoviesInfo($this->PostProcessRecomandations($recommendations));
		}

		private function Setredis($key, $value){
            return $this->redis->set($key, $value);
        }

        private function Getredis($key){
            return $this->redis->get($key);
        }

		private function FindMoviesInfo($movies){
			$moviesInfo = array();
			$_temp = array();
			$moviesIds = array();
			foreach($movies as $movie){
				if(($_movie = $this->Getredis('movie_' . $movie->GetID())) !== FALSE){
					array_push($moviesInfo, $_movie);
				}else{
					array_push($moviesIds, $movie->GetID());
				}
			}
			shuffle($moviesIds);
			if(count($moviesIds) > 25){
				$moviesIds = array_slice($moviesIds, 0, 25);
			}
			$_temp = $this->tmdbApi->getMovies($moviesIds);
			foreach($_temp as $_movie){
				$this->Setredis('movie_' . $_movie->GetID(), $_movie);
				array_push($moviesInfo, $_movie);
			}
			if(count($moviesInfo) == 0 && $this->tmdbApi->maximumLimitReached()){
				sleep(2);
				return $this->FindMoviesInfo($movies);
			}
			return $moviesInfo;
		}

		public function GetPopularMoviesWithInfo(){
			return $this->FindMoviesInfo($this->GetPopularMovies());
		}
	}
?>