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
    Route::get('/cpaccess/getAdminInfoByToken', 'CpAccessRpc@getAdminInfoByToken');
    Route::get('/cpaccess/checkAccess', 'CpAccessRpc@checkAccess');
    Route::get('/cpaccess/renderMenu', 'CpAccessRpc@renderMenu');

    Route::get('/cpaccess/addDepartUser', 'CpAccessRpc@addDepartUser');
    Route::get('/cpaccess/getSaleGroupWithName', 'CpAccessRpc@getSaleGroupWithName');
    Route::get('/cpaccess/getSaleGroupUsers', 'CpAccessRpc@getSaleGroupUsers');
    Route::get('/cpaccess/getUserByMark', 'CpAccessRpc@getUserByMark');
    Route::get('/cpaccess/getUserChildUser', 'CpAccessRpc@getUserChildUser');
    Route::get('/cpaccess/getUserByResource', 'CpAccessRpc@getUserByResource');
    Route::get('/cpaccess/getDepartByResource', 'CpAccessRpc@getDepartByResource');
    Route::get('/cpaccess/getUserChildDepart', 'CpAccessRpc@getUserChildDepart');
    Route::get('/cpaccess/selectAccess', 'CpAccessRpc@selectAccess');
    Route::get('/cpaccess/getAccessVal', 'CpAccessRpc@getAccessVal');
    Route::get('/cpaccess/getAccessDetail', 'CpAccessRpc@getAccessDetail');
    Route::get('/cpaccess/getAccessList', 'CpAccessRpc@getAccessList');
    Route::get('/cpaccess/hasAccess', 'CpAccessRpc@hasAccess');
    Route::get('/cpaccess/hasResource', 'CpAccessRpc@hasResource');
    Route::get('/cpaccess/getUserResouceList', 'CpAccessRpc@getUserResouceList');
    Route::get('/cpaccess/getAccess', 'CpAccessRpc@getAccess');
});
