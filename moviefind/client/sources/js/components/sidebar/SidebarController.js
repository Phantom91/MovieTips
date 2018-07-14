import { BaseController } from '../BaseController';
import { Navbar } from '../Navbar';

export class SidebarController extends BaseController{    
    constructor(renderController, renderProperties, data){
        new Navbar();
        super(data, renderProperties, (template) => {
            new renderController(template);
        });
    }
}