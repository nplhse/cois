/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// start the Stimulus application
import './bootstrap';

import 'bootstrap';
import { createApp } from 'vue';

// any CSS you import will output into a single css file (app.scss in this case)
import './styles/app.scss';

const app = createApp({
    template: '<p>If you can read this, Vue is working properly.</p>',
}).mount('#app');
