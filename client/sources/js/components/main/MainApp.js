import Backbone from 'backbone';
import Utils from '../Utils';
import AppConfig from '../../AppConfig';
import Router from "../Router";

import moment from 'moment';
//inject moment library into the window object
window.moment = moment;

// register helpers to the global scope to make them available for the entire app
window.MainApp = {};
MainApp.Helpers = Utils.Helpers;

class MovieTips {
    constructor(model){
        let _mainView = Backbone.View.extend ({
            // el - stands for element. Every view has an element associated with HTML content, will be rendered. 
            el: '#main-app',
            events : { 
                'click [data-toggle=".container-with-sidebar"]' : 'handleToggleClick'
            },
            // It's the first function called when this view is instantiated.
            initialize: function() {
                MainApp.language = AppConfig.DefaultLanguage;
                Backbone.history = Backbone.history || new Backbone.History( {} );
                let appRouter = new Router();
                // Start Backbone history a necessary step for bookmarkable URL's
                Backbone.history.start({
                    pushState: true,
                    root: "/movietips"
                });
                this.listenTo(this.model, 'change', this.render);
            },
            render: function(){

            },
            handleToggleClick : function(evt){
                let el = $(evt.currentTarget),
                  toggleEl = el.data('toggle');
                $(toggleEl).toggleClass("open-sidebar");
                if(el.hasClass('opened')){
                    $('body').removeClass('sidebar-opened');
                    el.removeClass('opened');
                    el.addClass('closed');
                }else{
                    $('body').addClass('sidebar-opened');
                    el.removeClass('closed');
                    el.addClass('opened');
                }
            }
        });
        return new _mainView(model);
    }
}

new MovieTips();