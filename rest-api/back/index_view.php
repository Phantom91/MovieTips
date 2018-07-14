<?php 
	require_once('movies_controller.php');
    $movies_controller = new MoviesController();
	$$movieData = $movies_controller->GetPopularMoviesWithInfo();
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
    

    <!-- Body -->
    <body>
                        <!--========== SWIPER SLIDER ==========-->
                        <div class="s-swiper js__swiper-one-item">
                            <!-- Swiper Wrapper -->
                            <div class="swiper-wrapper">
                                <div class="g-fullheight--xs g-bg-position--center swiper-slide" style="background: url('../img/1920x1080/Batman-v-Superman-Final-Trailer-hq.jpg');">
                                    <div class="container g-text-center--xs g-ver-center--xs">
                                        <div class="g-margin-b-20--xs">
                                            <h1 class="g-font-size-20--xs g-font-size-30--sm g-font-size-40--md g-color--white">Enjoy the<br>best movies you can find</h1>
                                        </div>
                                        <a class="cbp-lightbox" href="https://www.youtube.com/watch?v=IwfUnkBfdZ4" title="Intro Video">
                                            <i class="s-icon s-icon--lg s-icon--white-bg g-radius--circle ti-control-play"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="g-fullheight--xs g-bg-position--center swiper-slide" style="background: url('../img/1920x1080/wonder_woman_movie_stills-1920x1200.jpg');">
                                    <div class="container g-text-center--xs g-ver-center--xs">
                                        <div class="g-margin-b-20--xs">
                                            <div class="g-margin-b-20--xs">
                                                <h2 class="g-font-size-20--xs g-font-size-30--sm g-font-size-40--md g-color--white">Prepare to be amazed by our<br>movies recommendations!</h2>
                                            </div>
                                            <a class="cbp-lightbox" href="https://www.youtube.com/watch?v=VSB4wGIdDwo" title="Intro Video">
                                                <i class="s-icon s-icon--lg s-icon--white-bg g-radius--circle ti-control-play"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="g-fullheight--xs g-bg-position--center swiper-slide" style="background: url('../img/1920x1080/Marquee_LeagueUp_alt_598cedc92629e1.10497843.jpg');">
                                    <div class="container g-text-center--xs g-ver-center--xs">
                                        <div class="g-margin-b-20--xs">
                                            <div class="g-margin-b-20--xs">
                                                <h2 class="g-font-size-20--xs g-font-size-30--sm g-font-size-40--md g-color--white">Using MovieFy you can find the newest movies running in cinemas</h2>
                                            </div>
                                            <a class="cbp-lightbox" href="https://www.youtube.com/watch?v=fIHH5-HVS9o" title="Intro Video">
                                                <i class="s-icon s-icon--lg s-icon--white-bg g-radius--circle ti-control-play"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Swiper Wrapper -->

                            <!-- Arrows -->
                            <a href="javascript:void(0);" class="s-swiper__arrow-v1--right s-icon s-icon--md s-icon--white-brd g-radius--circle ti-angle-right js__swiper-btn--next"></a>
                            <a href="javascript:void(0);" class="s-swiper__arrow-v1--left s-icon s-icon--md s-icon--white-brd g-radius--circle ti-angle-left js__swiper-btn--prev"></a>
                            <!-- End Arrows -->
                            
                            <a href="#js__scroll-to-section" class="s-scroll-to-section-v1--bc g-margin-b-15--xs">
                                <span class="g-font-size-18--xs g-color--white ti-angle-double-down"></span>
                                <p class="text-uppercase g-color--white g-letter-spacing--3 g-margin-b-0--xs">Find More</p>
                            </a>
                        </div>
                        <!--========== END SWIPER SLIDER ==========-->

                        
    </body>
    <!-- End Body -->
</html>
