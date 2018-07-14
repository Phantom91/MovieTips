<?php
	require_once('Controllers\MoviesController.php');
    $moviesController = new MoviesController();
	$popularMovies = $moviesController->GetPopularMoviesWithInfo();
	$exportObject = array();
	$index = 0;
	foreach($popularMovies as $movie){
		$exportObject[$index++] = json_decode($movie->getJSON());
	}
	echo json_encode($exportObject);
?>