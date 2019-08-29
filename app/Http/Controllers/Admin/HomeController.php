<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Admin\Access\CpUserModule;
use \Exception;
use \Session;
use \YC_Util;
use App\Modules\Admin\Access\CpAccess;
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
     * @desc 临时oauth接口
     */
    public function tempOauth(Request $request)
    {
        $user = UserModule::getUserInfo(CpAccess::theUid());
        $user = empty($user) ? [] : $user->toArray();
        // 生成token
        $timestamp = time();
        $token = \YC_Util::getLaravelToken($user, $timestamp);
        return redirect('http://cp.' . config('app.shanhujia_url') . '/login/tempOauth/check?' . http_build_query([
            'refer' => $request->input('refer'),
            'token' => $token,
            'time' => $timestamp,
            'user_name' => $user['user_name'],
        ]));
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
            'old_url'   => 'http://' .config('app.url'). '/admin',
        ];
        Session::put('home_login_error_msg', '');
        return view('admin.home.login', $assign);
    }

    /**
     * @desc 存储登陆信息
     */
    public function storeLogin(Request $request)
    {
        try {
            $mobile   = trim($request->input('mobile'));
            if (empty($mobile) || YC_Util::checkCpMobile($mobile) == false) {
                throw new Exception("用户名格式不正确", 1);
            }
            $password = htmlspecialchars($request->input('password')); 
            if (empty($password)) {
                throw new Exception("密码不能为空", 1);
            }
            $state = CpUserModule::storeLoginInfo($mobile, $password);
            return $this->json(0, 'ok', $state);
        } catch (Exception $e) {
            return $this->json($e->getCode(), $e->getMessage()); 
        }
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
            return redirect('/cp/home/welcome');
        } catch (Exception $e) {
            $ret = Session::put('home_login_error_msg', sprintf('%s[%s]', $e->getMessage(), $e->getCode()));
            return redirect('/cp/home/login');
        }
    }
}
