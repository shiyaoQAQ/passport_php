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

// passport.zsfucai.cn/rpc/xxxx

// rpcauth
Route::group(['middleware' => ['rpcauth']], function () {
    Route::any('/cpaccess/getAdminInfoByToken', 'CpAccessRpc@getAdminInfoByToken');
    Route::any('/cpaccess/checkAccess', 'CpAccessRpc@checkAccess');
    Route::any('/cpaccess/renderMenu', 'CpAccessRpc@renderMenu');

    Route::any('/cpaccess/addDepartUser', 'CpAccessRpc@addDepartUser');
    Route::any('/cpaccess/getSaleGroupWithName', 'CpAccessRpc@getSaleGroupWithName');
    Route::any('/cpaccess/getSaleGroupUsers', 'CpAccessRpc@getSaleGroupUsers');
    Route::any('/cpaccess/getUserByMark', 'CpAccessRpc@getUserByMark');
    Route::any('/cpaccess/getUserChildUser', 'CpAccessRpc@getUserChildUser');
    Route::any('/cpaccess/getUserByResource', 'CpAccessRpc@getUserByResource');
    Route::any('/cpaccess/getDepartByResource', 'CpAccessRpc@getDepartByResource');
    Route::any('/cpaccess/getUserChildDepart', 'CpAccessRpc@getUserChildDepart');
    Route::any('/cpaccess/selectAccess', 'CpAccessRpc@selectAccess');
    Route::any('/cpaccess/getAccessVal', 'CpAccessRpc@getAccessVal');
    Route::any('/cpaccess/getAccessDetail', 'CpAccessRpc@getAccessDetail');
    Route::any('/cpaccess/getAccessList', 'CpAccessRpc@getAccessList');
    Route::any('/cpaccess/hasAccess', 'CpAccessRpc@hasAccess');
    Route::any('/cpaccess/hasResource', 'CpAccessRpc@hasResource');
    Route::any('/cpaccess/getUserResouceList', 'CpAccessRpc@getUserResouceList');
    Route::any('/cpaccess/getAccess', 'CpAccessRpc@getAccess');
    Route::any('/cpaccess/delDepartUser', 'CpAccessRpc@delDepartUser');
    Route::any('/cpaccess/getDepartResourceList', 'CpAccessRpc@getDepartResourceList');
    Route::any('/cpaccess/getUserDepartByUid', 'CpAccessRpc@getUserDepartByUid');
    Route::any('/cpaccess/getDepartmentByMark', 'CpAccessRpc@getDepartmentByMark');
    Route::any('/cpaccess/getNewSaleGroupWithName', 'CpAccessRpc@getNewSaleGroupWithName');
    Route::any('/cpaccess/checkUserInDepartByMark', 'CpAccessRpc@checkUserInDepartByMark');
    Route::any('/cpaccess/getUserAccessMark', 'CpAccessRpc@getUserAccessMark');
    Route::any('/cpaccess/getCityServiceMap', 'CpAccessRpc@getCityServiceMap');

    Route::any('/user/getUserInfo', 'UserRpc@getUserInfo');
    Route::any('/user/getUserInfoByIds', 'UserRpc@getUserInfoByIds');
    Route::any('/user/getUserNameMap', 'UserRpc@getUserNameMap');
    Route::any('/user/getUserMobileMap', 'UserRpc@getUserMobileMap');
    Route::any('/user/getUserRankDetail', 'UserRpc@getUserRankDetail');
    Route::any('/user/getUserByMobile', 'UserRpc@getUserByMobile');
    Route::any('/user/createUser', 'UserRpc@createUser');
});
