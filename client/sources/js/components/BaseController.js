/* jshint expr: true */
import DataService from './DataService';
import AppConfig from '../AppConfig';
import Utils  from './Utils';

export default class BaseController {
    //getters and setters
    static get created() { return _created; }
    static set created(value) { _created = value; }

    /**
     * 
     * @param {Object} data - optional 
     * @param {Object} renderProperties - (renderOnServer | renderOnClient, templatePath)
     * @param {Function} afterRenderCallback
     */
    constructor(data, renderProperties, afterRenderCallback) {
        this.data = data;
        if(renderProperties){
            this.renderTemplate(renderProperties).then((template) => {
                typeof afterRenderCallback === "function" && afterRenderCallback(template);
            })
            .catch(err => console.error(err));
        }
    }
    /**
     * 
     * @param {Object} renderProperties - (renderOnServer | renderOnClient, templatePath)
     * @param {Object} data - optional 
     */
    renderTemplate(renderProperties, data){
        let promise = null;
        if(renderProperties.renderOnServer){
            promise = new Promise((resolve, reject) => {
                DataService.post(`${AppConfig.RenderService}/${renderProperties.templatePath}`, this.data || data || {},
                    (template) => resolve(template), reject, false);
            });
        }else if(renderProperties.renderOnClient){
            promise = new Promise((resolve) => {
                Utils.TemplateManager.renderTemplate(`${AppConfig.TemplatesPath}/${renderProperties.templatePath}`, this.data || data || {}, template => resolve(template));
            });
        }
        return promise;
    }
}