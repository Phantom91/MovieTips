import { Global } from '../Global';
import { Portfolio3Col } from '../third-party/portfolio-3-col';
import { LandingPageView } from "./LandingPageView.js";
import DataService from "../DataService";
import { BaseController } from '../BaseController';

//private methods and properties

export class LandingPageController extends BaseController{
    //getters and setters
    constructor(){
        super();
        this._viewInstance = new LandingPageView();
    }

    renderPopularMovies() {
        DataService.loadDataIntoCollection('/GetPopularMovies', 'Movies', {}, (movies) => {
            this.popularMovies = movies;
            this.renderTemplate({renderOnServer : true, templatePath: 'partials/movies-grid'}, {movies : movies.toJSON()}).then((template) => {
                $('#movies-grid').html(template);
                setTimeout(() => {
                    $('.cbp.col-centered').addClass('hide-loader');
                    Portfolio3Col.init();
                    Global.init();
                }, 0);
            }).catch(err => console.error(err));
        }, (err) => {
            console.error(err);
        });
    }
}