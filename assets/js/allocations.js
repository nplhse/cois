import Vue from 'vue';

import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';

import axios from 'axios';
import VueAxios from 'vue-axios';

import moment from 'moment'

import App from './pages/Allocations';

// Activate plugins& filters
Vue.use(BootstrapVue);
Vue.use(IconsPlugin);

Vue.use(VueAxios, axios);

Vue.filter('formatDate', function(value) {
    if (value) {
        return moment(String(value)).format('DD.MM.YY hh:mm');
    }
});

new Vue({
    render: (h) => h(App),
}).$mount('#app');
