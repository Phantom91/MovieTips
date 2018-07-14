import * as _ from 'underscore';
import Backbone from 'backbone';
import Utils  from './Utils';
import DataStore from './DataStore';

export default class BaseView{
    /**
     * 
     * @param {String} element - every view has an element associated with HTML content, will be rendered
     * @param {String} template - the template to be rendered by the view
     * 
     */
    constructor(element, template){
        this.element = element;
        this.template = template;
    }
    createBackboneView(prototype) {
        let self = this;
        this._view = Backbone.View.extend ({
            el: this.element,
            
            // It's the first function called when this view is instantiated.
            initialize: function() {
                this.render(); 
                prototype = prototype || {};
                if(!prototype.viewModel){
                    let viewModelProto = Backbone.Model.extend({});
                    this.viewModel = new viewModelProto();     
                }
                if(prototype.useDataStore){
                    Utils.Helpers.bindBackboneModelsFromDom(this, DataStore);
                }else{
                    Utils.Helpers.bindBackboneModelsFromDom(this, prototype.viewModel || this.viewModel);
                }
                if(typeof prototype.initCallback === "function"){
                    prototype.initCallback(this);
                }
            },
            
            // $el - it's a cached jQuery object (el), in which you can use jQuery functions to push content.
            render: function() {
                if(self.template) {
                    this.$el.html(self.template);
                }
                if(typeof prototype.afterRenderCallback === "function") {
                    prototype.afterRenderCallback(self.template || '');
                }
            }
        });
        //extend Backbone view with properties and methods from prototype object
        _.mapObject(prototype, (value, key) => {
            this._view.prototype[key] = prototype[key];
        });
        new this._view();
    }
    destroy(afterDestroyCallback){
        this._view.remove();
        if(typeof afterDestroyCallback === "function"){
            setTimeout(afterDestroyCallback, 0);
        }
    }
}