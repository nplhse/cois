import Vue from 'vue';

// Import Bootstrap and BootstrapVue
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';

// Import axios
import axios from 'axios';
import VueAxios from 'vue-axios';

import App from './pages/Hospitals';

Vue.use(BootstrapVue);
Vue.use(IconsPlugin);

Vue.use(VueAxios, axios);

new Vue({
    render: (h) => h(App),
}).$mount('#app');
