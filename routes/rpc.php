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
    Route::get('/cpuser/getAdminInfoByToken', 'CpUserRpc@getAdminInfoByToken');
    Route::get('/cpuser/checkAccess', 'CpUserRpc@checkAccess');
    Route::get('/cpuser/renderMenu', 'CpUserRpc@renderMenu');

});
