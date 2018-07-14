import BaseController from '../BaseController';
import Navbar from '../Navbar';

export default class SidebarController extends BaseController{    
    constructor(renderController, renderProperties, data){
        new Navbar();
        if(!renderProperties) {
            super();
            new renderController();
        }else{
            super(data, renderProperties, (template) => {
                new renderController(template);
            });
        }
    }
    renderContent() {
        
    }
}