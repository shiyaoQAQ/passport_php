<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Admin\Access\CpUserModule;
use \Exception;
use \Session;
use \YC_Util;
use App\Modules\Admin\Access\CpAccess;
use App\Modules\Admin\Oauth\Constants\OauthErrorCode;
use App\Modules\User\UserBase\UserModule;

/**
 * @desc 系统首页
 */
class HomeController extends Controller
{
    /**
     * @desc 欢迎页面
     */
    public function welcome()
    {
        // if(CpAccess::checkUserInDepartByMark(CpAccess::theUid(), CpAccess::$saleMark)) {
        //     header('location:/sellerProcess/sellerPanels');
        //     exit();
        // }
        return view('admin.home.welcome');
    }

    /**
     * @desc 退出登录
     */
    public function logout(){
        CpUserModule::logout();
        return redirect('/cp/home/login');
    }

    /**
     * @desc 登录页
     */
    public function login()
    {
        $assign = [
            'error_msg' => Session::get('home_login_error_msg'),
            'appid'     => config('wechat.work.default.corp_id'),
            'agentid'   => config('wechat.work.default.agent_id'),
            // 'old_url'   => 'http://' .config('app.url'). '/admin',
        ];
        Session::put('home_login_error_msg', '');
        return view('admin.home.login', $assign);
    }

    /**
     * @desc 存储登陆信息
     */
    public function storeLogin(Request $request)
    {
        // 如果有验证码 直接进行登录 没有则走企业微信路线
        $smsCode = trim($request->input('smscode'));
        $mobile   = trim($request->input('mobile'));
        if (empty($mobile) || YC_Util::checkCpMobile($mobile) == false) {
            throw new WorkException("用户名格式不正确", 1);
        }
        $password = htmlspecialchars($request->input('password')); 
        if (empty($password)) {
            throw new WorkException("密码不能为空", 1);
        }
        if ($smsCode) {
            CpUserModule::login($mobile, $password, $smsCode);
            $result = [
                'type' => 'loginResult',
                'result' => 0,
            ];
        } else {
            $state = CpUserModule::storeLoginInfo($mobile, $password);
            $result = [
                'type' => 'state',
                'result' => $state,
            ];
        }

        return $this->json(0, 'ok', $result);
    }

     /**
     * @desc 登录时发送验证码
     */
    public function loginSendSms(Request $request)
    {
        $mobile   = trim($request->input('mobile'));
        if (empty($mobile) || YC_Util::checkCpMobile($mobile) == false) {
            throw new Exception("用户名格式不正确", 1);
        }
        // 生成验证码
        CpUserModule::getCaptcha($mobile);
        return $this->json(0, 'ok', []);
    }

    /**
     * @desc 登录验证
     */
    public function wxCode(Request $request)
    {
        try {
            $code = htmlspecialchars($request->input('code'));
            if (empty($code)) {
                throw new Exception("企业微信扫码错误", 1);
            }
            $state = htmlspecialchars($request->input('state'));
            if (empty($state)) {
                throw new Exception("企业微信参数错误", 1);
            }
            $login = CpUserModule::loginByWorkCode($code, $state);  
            if ($login == false) {
                throw new Exception("登录失败，请重试", 1);
            }
            $oldUrl = \Session::get('passportCurrentUrl');
            if ($oldUrl) {
                return redirect($oldUrl);    
            }
            return redirect('/cp/home/welcome');
        } catch (Exception $e) {
            $ret = Session::put('home_login_error_msg', sprintf('%s[%s]', $e->getMessage(), $e->getCode()));
            return redirect('/cp/home/login');
        }
    }

    /**
     * @desc 测试环境快速登录接口
     */
    public function testLogin(Request $request)
    {
        if (env('APP_ENV') == 'dev' || env('APP_ENV') == 'testing') {
            \Session::put('user_id', 10027);
            \Session::put('user_name', '测试-侍宏达');
            return redirect('/cp/home/welcome');
        }
    }

    /**
     * @desc 获取导航栏信息
     */
    public function layout(Request $request)
    {
        $url = base64_decode($request->input('controller'));
        // $arr = parse_url($url);
        // if (count(explode('order', $arr['path'])) > 1) {
        //     $controller = 'OrderController';
        // } else {
        // }
        $controller = '';

        $menu = CpAccess::getMenu();
        $showAccessList = CpAccess::getAccessPath($controller);
        return $this->json(0, 'ok', [
            'show_access_menu_list' => $menu, 
            'show_access_list' => $showAccessList, 
            'change_password' => "http://" . config('app.passport_url') . "/cp/user/password",
            'logout' => "http://" . config('app.passport_url') . "/cp/home/logout",
            'user_name' => CpAccess::theName(),
        ]);
    }
}
