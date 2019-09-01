<?php

namespace App\Http\Middleware;

use Closure;
use App\Modules\Admin\Access\CpAccess;
use App\Modules\Admin\Access\CpUserModule;
use Exception;
use App\Exceptions\WorkException;
/**
 * cp后台用户统一权限控制
 */
class CpUserAuth
{
    public function handle($request, Closure $next)
    {
        $ret = CpAccess::auth();
    	if ($ret['code'] != 0) {
            if(\Request::ajax() || \YC_Util::isFromWxapp()){
                $res = array(
                    'code' => 401,
                    'msg'  => '请先登录后再进行操作',
                );
                echo json_encode($res);
                die;
            } else {
                // 记录当前url
                $currentUrl = url()->full();
                \Session::put('passportCurrentUrl', $currentUrl);
                return redirect('/cp/home/login');
            }
        }
        
        // 正常已登录的case
        // 渲染菜单
        if (!(\Request::ajax() || \YC_Util::isFromWxapp())) {
            $this->initMenu();
        }

        // 校验权限
        $ret = CpAccess::checkPassportAccess();
        if ($ret['code'] != 0) {
            if ($ret['code'] > 10) {
                $code = $ret['code'];
                $msg = $ret['msg'];
            } else {
                $code = 402;
                $msg = '没有权限进行当前操作，请联系管理员开通权限！';
            }
            if(\Request::ajax() || \YC_Util::isFromWxapp()) {
                $res = array(
                    'code' => $code,
                    'msg'  => $msg,
                );
                echo json_encode($res);
                die;
            } else {
                throw new WorkException($msg, $code);
            }
        }
        return $next($request);
    }

    /**
     * 初始化菜单
     */
    public function initMenu()
    {
        $route = \Route::currentRouteAction();
        if (empty($route)) {
            return self::modelReturn(1, '路由不存在');
        }
        list($class, $action) = explode('@', $route);
        $menu = CpAccess::getMenu();
        \View::share('show_access_menu_list', $menu); 
        $showAccessList = CpAccess::getAccessPath($class);
        \View::share('show_access_list', $showAccessList);
    }

}
