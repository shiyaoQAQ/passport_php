<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// passport.zsfucai.cn/cp/xxx

Route::group(['middleware' => ['cpauth']], function () {
    // 新组织架构
    Route::get('/departments', 'DepartmentController@showDepartment');
    Route::get('/departments/tree', 'DepartmentController@showDepartmentTree');
    // Route::get('/departments/{did}/parent', 'DepartmentController@getDepartmentParent');
    Route::get('/departments/{did}/parent', 'DepartmentController@getDepartmentParent');
    // Route::get('/departments/{did}/user', 'DepartmentController@getDepartmentUser');
    Route::get('/departments/{did}/user', 'DepartmentController@getDepartmentUser');
    // Route::get('/departments/{did}/action', 'DepartmentController@getDepartmentAction');
    Route::get('/departments/{did}/action', 'DepartmentController@getDepartmentAction');
    // Route::get('/departments/{did}/resource', 'DepartmentController@getDepartmentResource');
    Route::get('/departments/{did}/resource', 'DepartmentController@getDepartmentResource');

    Route::get('/departments/{did}/tmpAction/action', 'ActionManageController@listDepartmentTmpAction');
    Route::put('/departments/{did}/tmpAction/action', 'ActionManageController@updateDepartmentTmpAction');
    Route::get('/departments/actionGroup', 'ActionManageController@ListActionGroup');
    Route::get('/departments/actionGroup/accessProject', 'ActionManageController@ListAccessProject');
    Route::get('/departments/actionGroup/{groupId}/tree', 'ActionManageController@showActionGroupTree');
    Route::get('/departments/actionGroup/{groupId}/action', 'ActionManageController@listActionGroupAction');
    Route::put('/departments/actionGroup/{groupId}/department', 'ActionManageController@updateActionGroupDepartment');
    Route::put('/departments/actionGroup/{groupId}/action', 'ActionManageController@updateActionGroupAction');
    
    Route::get('/departments/{did}/tmpResource/resource', 'ResourceManageController@listDepartmentTmpResource');
    Route::put('/departments/{did}/tmpResource/resource', 'ResourceManageController@updateDepartmentTmpResource');
    Route::get('/departments/resourceGroup', 'ResourceManageController@listResourceGroup');
    Route::get('/departments/resourceGroup/{groupId}/tree', 'ResourceManageController@showResourceGroupTree');
    Route::get('/departments/resourceGroup/{groupId}/resource', 'ResourceManageController@listResourceGroupResource');
    Route::put('/departments/resourceGroup/{groupId}/department', 'ResourceManageController@updateResourceGroupDepartment');
    Route::put('/departments/resourceGroup/{groupId}/resource', 'ResourceManageController@updateResourceGroupResource');

    // 旧组织架构
    Route::get('/department', 'AccessController@department');
    Route::get('/longrentdepartment/ajaxdeparttree', 'AccessController@getDepartTree');
    Route::get('/longrentdepartment/ajaxrenderdeparttree', 'AccessController@getTree');
    Route::get('/longrentdepartment/ajaxgetalldepart', 'AccessController@getAllDepart');
    Route::get('/longrentdepartment/ajaxgetparentdepart', 'AccessController@getParentDepart');
    Route::get('/longrentdepartment/ajaxgetdepartuser', 'AccessController@getDepartUser');
    Route::get('/longrentdepartment/ajaxgetactiongroup', 'AccessController@getActionGroup');
    Route::get('/longrentdepartment/ajax_get_depart_resource', 'AccessController@getDepartResource');

    Route::get('/longrentdepartment/actionaccessdetail', 'AccessController@actionAccessDetail');
    Route::get('/longrentdepartment/actiongroupaccessdetail', 'AccessController@actionGroupAccessDetail');
    Route::post('/longrentdepartment/setdepartaction', 'AccessController@setDepartAction');
    Route::post('/longrentdepartment/setdepartmentgroup', 'AccessController@setDepartActionGroup');
    Route::post('/longrentdepartment/setgroupaction', 'AccessController@setActionGroupAccess');
    Route::post('/longrentdepartment/ajaxadddepart', 'AccessController@addDepart');
    Route::get('/longrentdepartment/ajaxgetdepartinfo', 'AccessController@getDepartInfo');
    Route::post('/longrentdepartment/ajaxadduserbycpaccount', 'AccessController@addDepartUser');
    Route::get('/department/actiongrouplist', 'AccessController@actionGroupList');
    Route::post('/longrentdepartment/ajaxaddactiongroup', 'AccessController@addActionGroup');
    Route::post('/longrentdepartment/ajaxupdatedepart', 'AccessController@updateDepart');
    Route::post('/longrentdepartment/ajaxdeldepartuser', 'AccessController@delDepartUser');
    Route::post('/longrentdepartment/ajaxdelactiongroup', 'AccessController@delActionGroup');
    Route::post('/longrentdepartment/ajax_get_depart_resource', 'AccessController@getDepartResource');
    Route::get('/department/resourcegrouplist', 'AccessController@resourceGroupList');
    Route::post('/longrentdepartment/ajaxaddresourcegroup', 'AccessController@addResourceGroup');
    Route::post('/longrentdepartment/ajaxdelresourcegroup', 'AccessController@delResourceGroup');
    Route::get('/longrentdepartment/resourcegroupdetail', 'AccessController@resourceGroupDetail');
    Route::post('/longrentdepartment/setdepartmentresourcegroup', 'AccessController@setDepartResourceGroup');
    Route::post('/longrentdepartment/setresourcegroup', 'AccessController@setResourceGroupAccess');
    Route::post('/longrentdepartment/ajax_set_depart_resource', 'AccessController@setDepartResourceDetail');
    Route::get('/longrentdepartment/depart_resource_detail', 'AccessController@departResourceDetail');
    Route::post('/longrentdepartment/ajaxdeletedepart', 'AccessController@delDepart');

    Route::post('/access/selectAccess', 'AccessController@selectAccess');
    Route::get('/access/selectAccess', 'AccessController@selectAccess');
    // 首页和登录
    Route::get('/', 'HomeController@welcome');
    Route::get('/home/welcome', 'HomeController@welcome');
    Route::get('/home/logout', 'HomeController@logout');
    // CP用户
    Route::get('/user/add', 'UserController@addUser');
    Route::post('/user/add', 'UserController@doAddUser');
    Route::get('/user/search', 'UserController@search');
    Route::get('/user/password', 'UserController@password');
    Route::post('/user/password', 'UserController@doPassword');
    Route::get('/user/addDepartmentUser', 'UserController@addDepartmentUser');
    Route::post('/user/addDepartmentUser', 'UserController@addDepartmentUserJson');
    Route::delete('/user/dimission', 'UserController@dimission');
    // Oauth配置
    Route::get('/oauth/clients', 'OauthManageController@listClients');
    Route::get('/oauth/clients/json', 'OauthManageController@listJsonClients');
    Route::post('/oauth/clients', 'OauthManageController@storeClients');
    // Oauth本身登录
    Route::get('/oauth/show', 'OauthController@oauthShow');
    Route::get('/oauth/authorize', 'OauthController@oauthShow');
    Route::post('/oauth/authorization', 'OauthController@oauthAuthorization');
    
    Route::get('/layout', 'HomeController@layout');
});
Route::get('/home/testLogin', 'HomeController@testLogin');

Route::get('/home/login', 'HomeController@login');
Route::post('/home/login', 'HomeController@storeLogin');
Route::get('/home/wxcode', 'HomeController@wxCode');
// 通过code获取token接口实质上不需要有任何权限
Route::post('/oauth/token', 'OauthController@getOauthToken');
