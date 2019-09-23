/*
 * @Author: GXY 
 * @Date: 2019-08-23 14:20:43
 * @Describe: 珊瑚家-CP模板文件
 */

window.Vue = require('vue');
import router from './router/router';
import { request } from '../../base/js/utils/util'
import iView from 'iview';
import menu from '../../base/js/compontents/menu';
Vue.component('cp-menu', menu);
Vue.use(iView);
Vue.prototype.$Request = request;

const app = new Vue({
    el: '#app',
    router,
});
