/*
 * @Author: GXY 
 * @Date: 2019-08-23 14:21:04 
 * @Describe: router
 */

import Vue from 'vue';
import Router from 'vue-router';
import App from '../app.vue'

Vue.use(Router);

export default new Router({
    // mode : 'history',
    routes: [
        {
            path: '/',
            redirect: '/app'
        },        
        {
            path: '/app',
            name: 'app',
            component: App,
            // children: [],
            // redirect: ''
        },
        {
            path: '*',
            redirect: '/'
        }
    ]
})
