/*
 * @Author: GXY 
 * @Date: 2019-08-23 14:21:04 
 * @Describe: router
 */

import Vue from 'vue';
import Router from 'vue-router';
import App from '../app.vue';
import Index from '../page/index/index.vue';
import actionGroupList from '../page/actionGroupList/index.vue';
import resourceGroupList from '../page/resourceGroupList/index.vue';

Vue.use(Router);

export default new Router({
    // mode : 'history',
    routes: [
        {
            path: '/',
            redirect: '/index'
        },        
        {
            path: '/app',
            name: 'app',
            component: App,
            children: [
                {
                    path : '/index',
                    name : 'index',
                    component : Index,
                },
                {
                    path : '/actionGroup',
                    name : 'actionGroupList',
                    component : actionGroupList,
                },
                {
                    path : '/resourceGroup',
                    name : 'resourceGroupList',
                    component : resourceGroupList,
                },
            ],
            // redirect: ''
        },
        {
            path: '*',
            redirect: '/'
        }
    ]
})
