import Backbone from "backbone";
import SidebarController from "./sidebar/SidebarController.js";
import LandingPageController from "./landing-page/LandingPageController.js";

export default class Router extends Backbone.Router{
    /**
     * 
     * @param {Object} routes - optional
     */
    constructor(routes) {
        super(routes);
        this.routes = routes || {
            "" : "showIndex",
            "/movies" : "showMovies",
            "/news" : "showNews",
            "/friends" : "showFriends",
            "/profile" : "showProfile"
        };
        this.sections = [];
        this._bindRoutes();
    }
    showIndex() {
        this.tabId = "home";
        this.switchToSection();
    }
    showMovies() {
        this.tabId = "movies";
        this.switchToSection();
    }
    showNews() {
        this.tabId = "news";
        this.switchToSection();
    }
    showProfile() {
        this.tabId = "movies";
        this.switchToSection();
    }
    showFriends(){
        this.tabId = "movies";
        this.switchToSection();
    }
    setActiveView(tabId) {
        for(const section of this.sections) {
            if(section.tabId != tabId) {
                $(`#${section.tabId}-view`).hide();
                $(`#${section.tabId}`).removeClass("active");
            } else {
                $(`#${section.tabId}-view`).show();
                $(`#${section.tabId}`).addClass("active");
            }
        }
    }
    switchToSection() {
        this.setActiveView(this.tabId);
        for(const section of this.sections){
            if(section.tabId == this.tabId){
                $(`#${section.id}`).show();
            }else{
                $(`#${section.id}`).hide();
            }
        }
        switch(this.tabId) {
            case "home" :
                new SidebarController(LandingPageController);
                break;
        }
    }
    setUrl(url){
        this.navigate(url, false);
    }
    navigateTo(link){
       this.navigate(link, {trigger:true});
    }
}