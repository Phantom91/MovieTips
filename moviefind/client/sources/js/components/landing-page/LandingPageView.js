import Backbone from 'backbone';
import { Scrollbar } from '../third-party/scrollbar';
import { MagnificPopup } from '../third-party/magnific-popup';
import { Swipers } from '../third-party/swipers';
import { Counter } from '../third-party/counter';
import { Parallax } from '../third-party/parallax';
import { Wow } from '../third-party/wow';
import { BaseView } from '../BaseView';
import { LoginFormValidation } from '../login/LoginFormValidation';
import { FacebookLogin } from '../login/FacebookLogin';

export class LandingPageView extends BaseView {
    constructor() {
        super('#landing-page');
        this.createBackboneView({
            afterRenderCallback : () => {
                setTimeout(() => {
                    //Init general components and settings
                    Scrollbar.init();
                    MagnificPopup.init();
                    Swipers.init();
                    Counter.init();
                    Parallax.init();
                    Wow.init();
                }, 0);
            },
            events : {
                'click [data-toggle="modal-login"]' : 'showLoginModal'
            },
            showLoginModal : (evt) => {
                $('#loginModalForm').modal('show');
                LoginFormValidation.init();
                new FacebookLogin($('[data-action="login-with-facebook"]'));
            }
        });
    }
}