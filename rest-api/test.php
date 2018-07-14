<?php
    require_once('movies_controller.php');

    $movies_controller = new MoviesController();

    echo '<pre>';
        print_r($movies_controller->SimulateRecomandations());
    echo '</pre>';
?>