import { Scrollbar } from '../third-party/scrollbar';
import { MagnificPopup } from '../third-party/magnific-popup';
import { Swipers } from '../third-party/swipers';
import { Counter } from '../third-party/counter';
import { Parallax } from '../third-party/parallax';
import { Wow } from '../third-party/wow';

import BaseView from '../BaseView';

export default class LandingPageView extends BaseView {
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
            }
        });
    }
}