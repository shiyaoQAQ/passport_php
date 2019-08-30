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
    /**
     * The URIs that should be excluded from cp auth.
     *
     * @var array
     */
    protected $except = [
        // '/api/login/getCaptcha',
        // '/api/login/checkCaptcha',
        // '/api/login/getToken',
        '/cp/home/login',
        '/cp/home/wxcode',
    ];

    public function handle($request, Closure $next)
    {
        // 白名单
        $arr = parse_url($request->url());
        if(in_array($arr['path'], $this->except)){
            return $next($request);
        }
        // if(\YC_Util::isFromWxapp()){
        //     try {
        //         $this->dealWxapp($request);
        //         // \Session::put('user_id', 10027);
        //     } catch (Exception $e) {
        //         $res = array(
        //             'code' => $e->getCode(),
        //             'msg'  => $e->getMessage(),
        //         );
        //         echo json_encode($res);
        //         die;
        //     }
        // }
        $ret = CpAccess::auth();
    	if ($ret['code'] != 0) {
            if(\Request::ajax() || \YC_Util::isFromWxapp()){
                $res = array(
                    'code' => 401,
                    'msg'  => '请先登录后再进行操作',
                );
                echo json_encode($res);
                die;
            }else{
                return redirect('/cp/home/login');
            }
    	}else{
            $ret = CpAccess::checkAccess();
            if($ret['code'] != 0){
                if(\Request::ajax() || \YC_Util::isFromWxapp()){
                    $res = array(
                        'code' => 402,    
                        'msg'  => '没有权限进行当前操作，请联系管理员开通权限！',
                    );
                    echo json_encode($res);
                    die;
                }else{
                    throw new WorkException('没有权限访问当前页面，请联系管理员开通权限！[' .  $ret['code'] . ']', $ret['code']);
                }
            }
            return $next($request);
    	}
    }

    /**
     * 处理来自微信小程序的Token
     */
    public function dealWxapp($request){
        $token = $request->header('token');
        try {
            CpUserModule::wxappTokenAuth($token);      
        } catch (Exception $e) {
            $code = $e->getCode();            
            if($code !== 401){
                $code = 403;
            }
            throw new Exception($e->getMessage(), $code);
        }
    } 

    /**
     * 格式化返回数据
     */
    public function json($code, $msg = '', $data = null)
    {

    }
}
