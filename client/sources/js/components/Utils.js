import _ from 'underscore';
import Backbone from 'backbone';

let UtilsClass = (() => {
    let _events = {},
        instance = null;
    _.extend(_events, Backbone.Events);
    let _UtilsClassPrototype = () => {
        //create a local object containing helpers functions for the app
        return {
            TemplateManager : {
                templates : {},
                loadTemplateFromFile : function(file, data, options, callback){
                    if(!options){
                        options = {};
                    }
                    if(this.templates[file]){
                        if(typeof callback === "function") {
                            callback(this.templates[file], data, options);
                        }
                    }else{
                        if(file){
                            $.get(`${file}.ejs`, (template) => {
                                this.templates[file] = template;
                                if(typeof callback === "function") {
                                    callback(template, data, options);
                                }
                            }).fail((err) => {
                                console.error(err);
                            });
                        }
                    }
                },
                loadPartial : function(file, data){
                    return new Promise((resolve, reject) => {
                        if(this.templates[file]){
                            resolve(this.templates[file]);
                        }else{
                            $.get(`${file}.ejs`, (template) => {
                                resolve(template);
                            }).fail((err) => {
                                reject(err);
                            });
                        }
                    });
                },
                renderTemplate : function(file, data, callback) {
                    let self = this;
                    let _renderTemplate = function(template, _callback){
                        let htmlFromTemplate = _.template(template).call(self, data);
                        if(typeof _callback === "function") {
                            _callback(htmlFromTemplate);
                        }
                    };
                    let _renderPartial = function(template, partial, token, data){
                        let compiledPartial = _.template(partial).call(self, data);
                        return template.replace(`<%- @include${token}-%>`, compiledPartial);
                    };
                    //load partials based on the tokens extracted from the template
                    let _loadPartials = function(template, tokens){
                        let partials = [],
                            promises = [];
                        _.each(tokens, (token) => {
                            if(token.indexOf('-%>') !== -1){
                                let includeTokens = token.split('-%>');
                                let expression = includeTokens[0].substring(includeTokens[0].indexOf('('), token.indexOf(')')).replace(/\(|\)/g, '');
                                let params = expression.split(',');
                                params = params.map(value => value.trim());
                                params = params.map(value => value.replace(/"/g, ''));
                                if(params[1].includes('this.')){
                                    let dataTokens = params[1].split('.');
                                    dataTokens[1] = dataTokens[1].replace(/(\s*\'\s*})|(\s*\"\s*})|(\s*}*)/g, '');
                                    let newExpression = `<%= JSON.stringify(${dataTokens[1]}) %>`;
                                    let parsedParam = _.template(newExpression).call(self, data);
                                    if(!parsedParam.includes('{')){
                                        parsedParam = JSON.parse(parsedParam);
                                    }
                                    params[1] = params[1].replace(`this.${dataTokens[1]}`, parsedParam);
                                }
                                let f = eval;
                                f(`MainApp.passData=${params[1]}`);
                                partials.push({file : params[0], data : MainApp.passData, token : includeTokens[0]});
                            }
                        });
                        _.each(partials, (partial) => {
                            promises.push(self.loadPartial(partial.file, partial.data));
                        });
                        let newTemplate = template;
                        //compile all the partials after they are loaded
                        Promise.all(promises).then((templates) => {
                            _.each(partials, (partial, index) => {
                                newTemplate = _renderPartial(newTemplate, templates[index], partial.token, partial.data);
                            });
                            _renderTemplate(newTemplate, callback);
                        });
                    };
                    this.loadTemplateFromFile(file, data, {}, (template) => {
                        let searchForIncludeKeyword = template.indexOf('<%- @include');
                        if(searchForIncludeKeyword !== -1){
                            let tokens = template.split('<%- @include');
                            _loadPartials(template, tokens);
                        }else{
                            _renderTemplate(template, callback);
                        }
                    });
                },
                // options : Object
                renderSubTemplate : function(options, elementID, data, callback) {
                    if(options.template){
                        $(`#${elementID}`).html(options.template);
                        return;
                    }
                    let template = null;
                    if(options.isInlined) {
                        template = $( `script[template-id='${elementID}']`).html();
                    }
                    if(!template) {
                        this.renderTemplate(options.file, data, (html) => {
                            $(`#${elementID}`).html(html);
                            if(typeof callback === "function") {
                                callback(data);
                            }
                        });
                    } else {
                        let htmlFromTemplate = _.template(template).call(this, data);
                        $(`#${elementID}`).html(htmlFromTemplate);
                        if(typeof callback === "function") {
                            callback(data);
                        }
                    }
                }
            },
            Helpers : {
                getIEVersion : () => {
                    let sAgent = window.navigator.userAgent;
                    let Idx = sAgent.indexOf("MSIE");
                
                    // If IE, return version number.
                    if (Idx > 0) {
                        return parseInt(sAgent.substring(Idx+ 5, sAgent.indexOf(".", Idx)));
                    }
                    // Condition Check IF IE 11 and or MS Edge
                    else if ( !!navigator.userAgent.match(/Trident\/7\./) || window.navigator.userAgent.indexOf("Edge") > -1 )
                    {
                        return 11;
                    } else {
                        return 0; //It is not IE
                    }
                },
                capitalizeFirstLetter : (string) => {
                    return `${string.charAt(0).toUpperCase()}${string.slice(1)}`;
                },
                renderModal : (modalId) => {
                    if($(`body #modals > #${modalId}`).length == 0){
                        $('body #modals').append($(`#${modalId}`).detach());
                    }
                    $(`#${modalId}`).modal({backdrop: 'static', keyboard: true});
                },
                hexToRgba(hex, opacity){
                    hex = hex.replace('#','');
                    let r = parseInt(hex.substring(0,2), 16),
                        g = parseInt(hex.substring(2,4), 16),
                        b = parseInt(hex.substring(4,6), 16);
                    return 'rgba(' +r+','+g+','+b+','+opacity/100+')';
                },
                registerHelper : function(name, fn){
                    MainApp.Helpers[name] = fn;
                },
                //bind backbone properties to events described in DOM
                bindBackboneModelsFromDom(scope, model) {
                    let modelsFromDom = $('[data-backbone-model]');
                    let _updateModel = (modelId, modelValue, modelChange, validationMethod) => {
                        if(validationMethod && typeof scope[validationMethod] === "function" && scope[validationMethod](modelValue)) {
                            model.set(modelId, modelValue);
                            if(modelChange){
                                scope[modelChange](modelValue);
                            }
                        }else if(!validationMethod){
                            model.set(modelId, modelValue);
                            if(modelChange){
                                scope[modelChange](modelValue);
                            }
                        }
                    };
                    $.each(modelsFromDom, (index, elem) => {
                        let modelId = $(elem).attr('data-backbone-model'),   
                            watchEvent = $(elem).attr('data-watch-event') || 'change',
                            debounceTimer = parseInt($(elem).attr('data-debounce')) || 0,
                            validationMethod = $(elem).attr('validation-method') || null,
                            modelChange = $(elem).attr('data-model-change') || null;
                        if(debounceTimer){
                            $(elem).on(watchEvent, _.debounce((evt) => {
                                _updateModel(modelId, evt.currentTarget.value, modelChange, validationMethod);
                            }, debounceTimer));
                        }else{
                            $(elem).on(watchEvent, evt => _updateModel(modelId, evt.currentTarget.value, modelChange, validationMethod));
                        }
                    });
                }
            },
            //Events bus for the whole app
            Events : {
                subscribe(evt, handler){
                    _events.on(evt, handler);
                },
                unsubscribe(evt, handler){
                    _events.off(evt, handler);
                },
                trigger(evt, data){
                    _events.trigger(evt, data);
                }
            }
        };
    };
    return {
        getInstance: function(){
            if (instance == null) {
                instance = new _UtilsClassPrototype();
                // Hide the constructor so the returned objected can't be new'd...
                instance.constructor = null;
            }
            return instance;
        }
    };        
})();

const Utils = UtilsClass.getInstance();

export default Utils;