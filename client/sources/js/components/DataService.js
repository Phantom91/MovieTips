import "isomorphic-fetch";
import * as _ from 'underscore';
import * as es6Promise from 'es6-promise';
import { Model, Collection } from 'backbone';
import AppConfig from '../AppConfig';

es6Promise.polyfill();

const DataService = {
    cache : {},
    //perform a get AJAX call
    get : function(url, cacheData, cacheKey, successCallback, errCallback) {
        if (cacheData && !cacheKey) {
            cacheKey = 'data';
        }
        let self = this;
        if (!cacheData || cacheData && !this.cache[cacheKey]) {
            fetch(url, {
                method : 'get'
            }).then(response => {
                if(response.ok) {
                    return response;
                }
                throw new Error('Internal server error');
            })
            .then(response => {
                let data = null;
                try {
                    data = response.json();
                    if (cacheData) {
                        self.cache[cacheKey] = data;
                    }
                    if(typeof successCallback === 'function'){
                        successCallback(data);
                    }
                }
                catch(err){
                    if(typeof errCallback === 'function'){
                        errCallback(err);
                    }else{
                        console.error(err);
                    }
                }
            })
            .catch(err => {
                if(typeof errCallback === 'function'){
                    errCallback(err);
                }else{
                    console.error(err);
                }
            });
        }
        else {
            if(typeof successCallback === 'function'){
                successCallback(this.cache[cacheKey]);
            }
        }
    },
    post : function(url, requestData, successCallback, errCallback, expectJSON = true) {
        fetch(url, {
            method : 'post',
            body : JSON.stringify(requestData),
            headers : {
                'Content-Type': 'application/json'
            }
        }).then(response => {
            if(response.ok) {
                return response;
            }
            throw new Error('Internal server error');
        })
        .then(response => {
            let data = null;
            try {
                if(expectJSON){
                    data = response.json();
                }else{
                    data = response.text();
                }
                if(typeof successCallback === 'function'){
                    successCallback(data);
                }
            }
            catch(err){
                if(typeof errCallback === 'function'){
                    errCallback(err);
                }else{
                    console.error(err);
                }
            }
        })
        .catch(err => {
            if(typeof errCallback === 'function'){
                errCallback(err);
            }else{
                console.error(err);
            }
        });
    },
    //load data from the server and put inside a backbone collection
    loadDataIntoCollection : function(path, collectionName, data, successCallback, errCallback){
        let model = Model.extend({
            initialize: function () {
                $.ajaxPrefilter( function( options ) {
                    options.crossDomain = {
                        crossDomain: true
                    };
                });
            }
        });
        let collection = Collection.extend({
            url : AppConfig.API_ENDPOINT + path,
            model: model,
            data : data || {},
            name : collectionName
        });
        let newCollection = new collection();
        newCollection.fetch({
            success : successCallback,
            error : errCallback
        });
        return newCollection;
    },
    //load data from the server and put inside multiple backbone collections
    loadDataIntoCollections : function(data, successCallback, errCallback){
        let collectionsPromisesBuffer = [],
            collectionsData = {};
        _.each(data, (collectionData) => {
            let model = Model.extend({ 
                initialize: function () {
                    $.ajaxPrefilter( function(options) {
                        options.crossDomain = {
                            crossDomain: true
                        };
                    });
                }}
            );
            let options = {
                model: model,
                name : collectionData.name,
                data : collectionData.data || {}
            };
            if(collectionData.url){
                options.url = collectionData.url;
            }
            else if(collectionData.path){
                options.url = AppConfig.API_ENDPOINT + collectionData.path;
            }
            let collection = Collection.extend(options);
            let newCollection = new collection();
            collectionsPromisesBuffer.push(
                newCollection.fetch({
                    success : function(result){
                        collectionsData[collectionData.name] = result;
                    },
                    error : errCallback,
                    data : newCollection.data || {}
                })
            );
        });
        $.when(...collectionsPromisesBuffer).done(() => {
            successCallback(collectionsData);
        });
    }
};

export default DataService;