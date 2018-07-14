
import BaseView from './BaseView';
import LoginFormValidation from './login/LoginFormValidation';
import FacebookLogin from './login/FacebookLogin';

export default class Navbar extends BaseView {
    constructor() {
        super('#navbar');
        this.createBackboneView({
            events : {
                'click [data-toggle="modal-login"]' : 'showLoginModal'
            },
            showLoginModal : (evt) => {
                $('#loginModalForm').modal('show');
                LoginFormValidation.init();
                //new FacebookLogin($('[data-action="login-with-facebook"]'));
            }
        });
    }
}