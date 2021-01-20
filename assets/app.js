/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// start the Stimulus application
import './bootstrap';

import bootstrap from 'bootstrap';

// any CSS you import will output into a single css file (app.scss in this case)
import './styles/app.scss';

new Vue({
    template: '<h1>Hello Vue! Is this cooler?</h1>',
}).mount('#app');
