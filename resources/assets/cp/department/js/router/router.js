/*
 * @Author: GXY 
 * @Date: 2019-08-23 14:21:04 
 * @Describe: router
 */

import Vue from 'vue';
import Router from 'vue-router';
import App from '../app.vue';
import Index from '../page/index/index.vue';
import actionGroupList from '../page/actionGroupList/actionGroupList.vue';
import actionGroupEdit from '../page/actionGroupEdit/actionGroupEdit.vue';
import departmentActionEdit from '../page/departmentActionEdit/departmentActionEdit.vue';
import resourceGroupList from '../page/resourceGroupList/resourceGroupList.vue';

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
                // 组织架构页
                {
                    path : '/index',
                    name : 'index',
                    component : Index,
                },
                // 权限组列表
                {
                    path : '/actionGroup',
                    name : 'actionGroupList',
                    component : actionGroupList,
                },
                // 权限组编辑
                {
                    path : '/actionGroup/:groupId/edit',
                    name : 'actionGroupEdit',
                    component : actionGroupEdit,
                },
                // 部门独立权限编辑
                {
                    path : '/department/:did/action/edit',
                    name : 'departmentActionEdit',
                    component : departmentActionEdit,
                },
                // 资源组列表
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
