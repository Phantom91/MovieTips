<?php
	class Book{
		
		public function __construct($data){
			$this->_data = $data;
		}
		
		public function GetTitle(){
			return $this->_data->Title;
		}
		
		public function GetActors(){
			return explode(",", $this->_data->Actors);
		}
		
		public function GetYear(){
			return $this->_data->Year;
		}
		
		public function GetPoster(){
			return $this->_data->Poster;
		}
		
		public function GetRating(){
			return $this->_data->imdbRating;
		}
		
		public function GetGenres(){
			return explode(",", $this->_data->Genres);
		}
		
		public function GetImdbId(){
			return $this->_data->imdbID;
		}
		
		public function GetProductionCountry(){
			return $this->_data->Country;
		}
		
		public function GetWriter(){
			return $this->Writer;
		}
		
		public function GetDirector(){
			return $this->Director;
		}
	}

?>