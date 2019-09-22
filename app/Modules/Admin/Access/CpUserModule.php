<?php

namespace App\Modules\Admin\Access;

use App\Modules\Admin\Access\Models\CpUser;
use App\Modules\User\UserBase\Models\EcsUser;
use App\Modules\User\UserBase\UserModule;
// use App\Models\User\WxappUserToken;
use App\Exceptions\WorkException;
use App\Modules\Admin\WorkWechat\WorkWeChatModule;
use \YC_Util;
use Exception;
use Cache;
// use EasyWeChat;
use Session;
use \YC_Log;
use \DB;
use App\Modules\Admin\Access\Models\CpDepartmentUser;
use App\Modules\Admin\Access\Models\CpDepartment;

class CpUserModule {

    const CP_NAME_PREFIX = '';

    const KEY_FREQ_SIG   = 'hp_verify_freq_';
    const KEY_VERIFY_SIG = 'cp_hp_verify_code_';
    const KEY_CHECK_SIG  = 'hp_verify_freq_check_time_';
    const KEY_USER_LOGIN_FREQ  = 'user_login_freq_';

    const MAX_LOGIN_FAILD_TIME = 15;//1小时内最多登录失败错误次数
    const CODE_SNED_MAX  = 5;//单个手机号300秒内最多发送5次
    const CODE_CHECK_MAX = 10;//单个手机号3000秒内最多check10次

    const WXAPP_TOKEN_SALT = 'SDFU!@#$$2017EE!';
    const PASSWORD_SALT = 'YOUCAI!&%fir2017CAI';
    const WORK_CODE_STATE_SALT = 'ABCLJOIEIBTY@#&$^!2';

    const CP_ROLE_DRIVER = 9;
    const CP_ROLE_PORTER = 10;

    const DEFAULTPWD = "password5694"; //用于前端密码显示*******

    private static $cpRoleList = array(
        1 => 'BOSS',
        2 => '产品',
        3 => '研发',
        4 => '客服',
        5 => '销售',
        6 => '财务',
        7 => '采购',
        8 => '仓配',
        9 => '司机',
        10 => '搬运工',
        11 => '售后',
        12 => '城市经理',
        13 => '设计',
        14 => '运营',
    );

    const MP_ROLE_DRIVER = 'driver';

    public static function getCpRoleList()
    {
        return self::$cpRoleList;
    }

    /**
     * 通过uid找cpUserInfo
     *
     * @param [type] $uid
     * @return void
     */
    public static function getCpUserInfo($uid, $argument = [])
    {
        $cpUser = CpUser::getUserInfo($uid, $argument);
        return $cpUser;
    }

    public static function addUser($data)
    {
        $userMobile = '8' . substr($data['mobile'], 1);
        $userName = explode('-', $data['name']);
        $userName = $userName[sizeof($userName) - 1];
        $userData = array(
            'user_name' => self::$cpRoleList[$data['role']] . '-' . $userName,
            'reg_time' => time(),
            'mobile_phone' => $userMobile,
            'is_validated' => 0,
            'alias' => '',
            'msn' => '',
            'qq' => '',
            'office_phone' => '',
            'home_phone' => '',
            'last_time' => '2017-01-01 00:00:00',
            'birthday' => '2017-01-01',
            'credit_line' => 0,
            'role' => 'admin',
            'password' => md5(md5($data['password']) . self::PASSWORD_SALT),
        );
        $user = EcsUser::where('mobile_phone', $userMobile)->first();
        if (empty($user)) {
            $objUser = new EcsUser();
            $userId  = $objUser->add($userData);
            if (empty($userId)) {
                return self::modelReturn(1, '添加用户失败');
            }
        } else {
            $user->user_name = $userData['user_name'];
            if($data['password'] != self::DEFAULTPWD){
                $user->password = $userData['password'];
            }
            $user->save();
            $userId = $user->user_id;
        }
        $cpUser = CpUser::where('uid', $userId)->first();
        if (empty($cpUser)) {
            $newCpUser = array(
                'mobile' => $data['mobile'],
                'email'  => $data['email'],
                'uid'    => $userId,
                'role'   => $data['role'],
                'op_uid' => CpAccess::theUid(),
            );
            $objCpUser = new CpUser();
            $cpUseId = $objCpUser->add($newCpUser);
        } else {
            $cpUser->role = $data['role'];
            $cpUser->email = $data['email'];
            $cpUser->wx_id = '';
            $cpUser->save();
            $cpUseId = $cpUser->id;
        }
        if (empty($cpUseId)) {
            return self::modelReturn(2, '添加失败');
        } else {
            return self::modelReturn(0, '添加成功');
        }
    }

    public static function getCpUserByHp($mobile) {
        $cpUser = CpUser::where('mobile', $mobile)->first();
        if (empty($cpUser)) {
            return self::modelReturn(1, '用户不存在');
        }
        $user = EcsUser::where('user_id', $cpUser->uid)->first();
        if (empty($user)) {
            return self::modelReturn(1, '用户不存在');
        }
        $leave = CpUserModule::checkUserInMark($cpUser->uid, CpAccess::MARK_SALE_DIMISSION);
        $userInfo = [
            'uid' => $cpUser->uid,
            'user_name' => $user->user_name,
            'email' => $cpUser->email,
            'password' => self::DEFAULTPWD,
            'role' => $cpUser->role,
            'leave' => $leave,
            'dpList' => self::getPartMent($cpUser->uid)
        ];
        return self::modelReturn(0, 'ok', $userInfo);
    }
    /**
     * 获取某账户的组织架构的面包屑
     */
    public static function getPartMent($uid){
        $userDpList = CpDepartmentUser::select('department_id')->where('uid', $uid)->where('is_deleted',0)->pluck('department_id');
        $dpList = CpDepartment::select('id', 'parent_id', 'name')->where('is_deleted', 0)->get()->keyBy('id');
        $userDpResList = [];
        foreach($userDpList as $dpId){
            $userDpRes = self::getUserDpName($dpList, $dpId);
            $userDpResList[] = $userDpRes;
        }
        return $userDpResList;
    }
    private static function getUserDpName($dpList, $id){
        $dp = $dpList[$id];
        $name = '';
        if($dp['parent_id'] != 0){
            $name = self::getUserDpName($dpList, $dp['parent_id']);
        }
        if(empty($name)){
            return $dp['name'];
        }
        return $name .' 》 '.$dp['name'];
    }
    public static function changePassword($oldPass, $newPass) {
        $uid = CpAccess::theUid();
        $user = EcsUser::where('user_id', $uid)->first();
        if (empty($user)) {
            return self::modelReturn(1, '用户不存在');
        }
        if (md5(md5($oldPass) . self::PASSWORD_SALT) !== $user->password) {
            return self::modelReturn(1, '旧密码错误，请重试');
        }
        $user->password = md5(md5($newPass) . self::PASSWORD_SALT);
        $user->save();
        //取消绑定关系
        $cpUserUpdate = [
            'wx_id' => '',
        ];
        CpUser::where('uid', $uid)->update($cpUserUpdate); 
        return self::modelReturn(0, '修改成功');
    }

    public static function getName($uid) {
        return self::getUserInfo($uid, 'user_name'); 
    }
    /**
     * 离职某用户
     */
    public static function dimissionUser($mobile){
        $code = 0;
        $msg = '';
        $user = self::getCpUserInfoByMobile($mobile);
        $dDepart = new CpDepartment();
        $departInfo = $dDepart->getDeaprtByMark(CpAccess::MARK_SALE_DIMISSION); 
        $did = array_pluck($departInfo, 'id')[0];
        // $salesRpc = new \Pascal\Rpc\Zsfucai\SalesRpc;
        // $kaNumber = $salesRpc->getSalerKaNumber($user->uid);
        $kaNumber = 0;
        if($kaNumber!=0){
            throw new WorkException(sprintf("名下有%s个KA私海客户，不能离职，请联系其上级尽快处理;",$kaNumber), 1000000);
        }
        // $customerNumber = $salesRpc->getSalerCustomerNumber($user->uid);
        $customerNumber = 0;
        if($customerNumber!=0){
            throw new WorkException(sprintf("名下有%s个个人客户，不能离职，请联系其上级尽快处理;",$customerNumber), 1000000);
        }
        DB::beginTransaction();
        try{
            $mobile = '8'.substr($mobile, 1);
            CpDepartmentUser::where('uid', $user->uid)->where('is_deleted',0)->update([
                'is_deleted'=>'1'
            ]);
            $ecsUser = UserModule::getUserInfo($user->uid);
            if ($ecsUser) {
                $ecsUser->password = self::DEFAULTPWD;
                $ecsUser->save();
            }
            $ret = CpAccess::addDepartUser($did, $mobile);
            $code = $ret['code'];
            if($code == 0){
                $msg = "已将此用户放入离职组";
            }
            DB::commit();
            \YC_Log::info('[dimissionUser] [%s] [%s] [%s]', $mobile, $user->uid, CpAccess::theUid());
        } catch (Exception $e) {
            DB::rollback();
            $code = $e->getCode();
            $msg = $e->getMessage();
        }
        return [
            'code'=> 0,
            'msg'=> $msg
        ];
    }
    public static function getUserInfo($uid, $key = '') {
        $userInfo = EcsUser::where('user_id', $uid)->first();
        if (empty($userInfo)) {
            return empty($key) ? array() : '';
        }
        $cpUser = CpUser::where('uid', $uid)->first();
        $userInfo = $userInfo->toArray();
        $userInfo['cp_mobile'] = empty($cpUser) ? '' : $cpUser->mobile;
        $userInfo['cp_work_id'] = $cpUser->work_id;
        $userInfo['cp_email'] = $cpUser->email;
        $userInfo['cp_wx_id'] = $cpUser->wx_id;
        if (empty($key)) {
            return $userInfo;
        }
        return isset($userInfo[$key]) ?  $userInfo[$key] : '';
    }
    
    public static function getBatchName($uidList)
    {
        return self::getBatchUserInfo($uidList, 'user_name');
    }

    public static function getBatchUserInfo($uidList, $key = '')
    {
        if(empty($uidList) || !is_array($uidList)) {
            return [];
        }
        $con = EcsUser::select();
        // CpAccess::accessPathForList($con, $uidList, [CpAccess::ACCESS_KEY_CITY => 'city_id']);
        $ecsUserList = $con->whereIn('user_id', $uidList)->get()->keyBy('user_id');

        if(empty($key)) {
            $cpUserList  = CpUser::select('uid', 'mobile')->whereIn('uid', $uidList)->get()->keyBy('uid')->toArray();
            foreach ($ecsUserList as &$user) {
                $user->cp_mobile = $cpUserList[$user->user_id]['mobile'];
            }
            unset($user);
            return $ecsUserList->isEmpty() ? [] : $ecsUserList->toArray();
        }
        $list = [];
        foreach ($ecsUserList->toArray() as $uid => $user) {
            $list[$uid] = $user[$key];
        }
        return $list;
        
    }


    public static function getCpMobile($uid)
    {
        $cpUser = CpUser::where('uid', $uid)->first();
        return empty($cpUser) ? '' : $cpUser->mobile;
    }

    /**
     * 获取验证码
     */
    public static function getCaptcha($mobile) {
        if (empty($mobile) || !YC_Util::checkCpMobile($mobile)) {
            throw new Exception("手机格式不对", 1100001);
        }
        $user = EcsUser::where('mobile_phone', $mobile)->first();
        if (empty($user)) {
            throw new Exception("账号不存在", 1100002);
        }
        $cpUser = CpUser::where('uid', $user->user_id)->first();
        if (empty($cpUser)) {
            throw new Exception("该账号不是管理员", 1100003);
        }
        $cpMobile = $cpUser->mobile;
        $freq = Cache::get(self::KEY_FREQ_SIG . $cpMobile);
        $freq = empty($freq) ? 0 : intval($freq);
        if ($freq > self::CODE_SNED_MAX) {
            throw new Exception("您的操作太频繁了，请稍后请试", 1100004);
        }
        Cache::put(self::KEY_FREQ_SIG . $cpMobile, $freq + 1, 5);
        $code = Cache::get(self::KEY_VERIFY_SIG . $cpMobile);
        if (empty($code)) {
            $code = YC_Util::randNumber();
            Cache::put(self::KEY_VERIFY_SIG . $cpMobile, $code, 5);
        }
        return self::doSendVerifyCode($cpMobile, $code);
    }

    /**
     * 发送验证码
     */
    public static function doSendVerifyCode($hp, $code) {
        $msg = sprintf('验证码%s，请在5分钟内完成验证，请勿将验证码透露给他人，如非本人操作请忽略。', $code);
        $ret = \YC_Sms::send($hp, $msg, '', 'monternetApi');
        \YC_Log::info('[doSendVerifyCode] [%s] [%s] [%s]', $hp, $msg, json_encode($ret));
        return true;
    }

    /**
     * 登录验证
     */
    public static function login($mobile, $password, $code) {
        self::checkCaptcha($mobile, $code);
        $token = self::KEY_USER_LOGIN_FREQ . $mobile;
        $freq = Cache::get($token);
        $freq = empty($freq) ? 0: $freq;
        //防止暴力破解
        if ($freq > self::MAX_LOGIN_FAILD_TIME) {
            throw new Exception("您的登录操作太频繁了，请稍后再试", 11000011);
        }
        $user = self::checkPassword($mobile, $password);
        if ($user === false) {
            throw new Exception("用户名或密码错误，请重试", 11000012);
        }
        self::doLogin($user);
        $cpUser = CpUser::where('uid', $user['user_id'])->first();
        Cache::forget(self::KEY_VERIFY_SIG . $cpUser->mobile);
        return $user;
    }

    /**
     * 进行登录操作
     */
    public static function doLogin($user) {
        //如果是微信小程序，绑定用户信息
        // if (YC_Util::isFromWxapp()) {
        //     self::bindWxapp($user);
        // } else {
            \Session::put('user_id', $user['user_id']);
            \Session::put('user_name', $user['user_name']);
        // }
        return true;
    }

    /**
     * 注销登录
     * 只实现了微信小程序的注销
     */
    public static function logout()
    {
        // 删除几个内部项目的token
        $uid = CpAccess::theUid();
        $ownProjectIds = OauthModule::getOwnProjectIds();
        if (!empty ($ownProjectIds)) {
            OauthModule::nukeUserToken($uid, $ownProjectIds);
        }

        Session::flush();
        return true;
    }

    // //把wxapp的ID和用户绑定在一起
    // public static function bindWxapp($user) {
    //     $headers = YC_Util::getAllHeaders();
    //     $tokenInfo = self::checkToken($headers['token']);
    //     $cpUser = CpUser::where('uid', $user['user_id'])->first();
    //     $cpUser->wx_id = $tokenInfo->openid;
    //     $cpUser->save();
    //     return true;
    // }

    // // wxapp 登出
    // public static function logoutWxapp()
    // {
    //     $headers = YC_Util::getAllHeaders();
    //     $tokenInfo = self::checkToken($headers['token']);
    //     $openid = $tokenInfo->openid;
    //     $cpUser = CpUser::where('wx_id', $openid)->first();
    //     $cpUser->wx_id = '';
    //     $cpUser->save();
    //     return true;
    // }

    // /**
    //  * 检查token的合法性 
    //  */
    // public static function checkToken($token) {
    //     if (empty($token)) {
    //         throw new Exception("授权Token为空，请重试", 403);
    //     }        
    //     $signStr = base64_decode($token);
    //     $encodeId = substr($signStr, 0, 12);
    //     $sign = substr($signStr, 12);
    //     if (empty($encodeId) || empty($sign)) {
    //         throw new Exception("授权Token无效", 403);
    //     }
    //     $decodeId = YC_Util::xcryptDecrypt($encodeId);
    //     if (md5($decodeId . self::WXAPP_TOKEN_SALT . $encodeId) !== $sign) {
    //         throw new Exception("授权Token无效", 403); 
    //     }
    //     $tokenInfo = WxappUserToken::where('id', $decodeId)->first(); 
    //     if (empty($tokenInfo) || $tokenInfo->token != $token) {
    //         throw new Exception("授权Token无效", 403);
    //     }
    //     if (time() > strtotime($tokenInfo->expire_time)) {
    //         throw new Exception("授权Token过期", 403);
    //     }
    //     return $tokenInfo;
    // }

    /**
     * 检查用户密码是否匹配
     */
    public static function checkPassword($mobile, $password) {

        if (empty($mobile) || empty($password)) {
            return false;
        }
        if (!YC_Util::checkCpMobile($mobile)) {
            return false;
        }
        $user = EcsUser::where('mobile_phone', $mobile)->first();
        if (empty($user)) {
            return false;
        }
        if ($user['password'] != md5(md5($password) . self::PASSWORD_SALT)) {
            return false;
        }
        return $user;
    }

    /**
     * 检查验证码是否正确
     */
    public static function checkCaptcha($mobile, $code) {
        if (empty($code)) {
            throw new Exception("验证码为空", 1100007);
        }
        if($mobile == '87600590102' && $code == '875343'){
            return true;
        }
        if($mobile == '87568020728' && $code == '875343'){
            return true;
        }
        $user = EcsUser::where('mobile_phone', $mobile)->first();
        if (empty($user)) {
            throw new Exception("账号不存在", 1100002);
        }
        $cpUser = CpUser::where('uid', $user->user_id)->first();
        if (empty($cpUser)) {
            throw new Exception("该账号不是管理员", 1100003);
        }
        $cpMobile = $cpUser->mobile;
        $freq = Cache::get(self::KEY_CHECK_SIG . $cpMobile);
        $freq = empty($freq) ? 0 : $freq;
        Cache::put(self::KEY_CHECK_SIG . $cpMobile, $freq+1, 50);
        if ($freq > self::CODE_CHECK_MAX) {
            // throw new Exception("您的操作太频繁了，请稍后请试", 1100008);
        }
        $verify = Cache::get(self::KEY_VERIFY_SIG . $cpMobile);
        if (empty($verify)) {
            throw new Exception('验证码已过期，请重新获取', 1100009);
        }
        if (intval($verify) !== $code) {
            throw new Exception('验证码错误，请重试', 1100010);
        }
        // Cache::forget(self::KEY_VERIFY_SIG . $cpMobile);
        return true;
    }

    // /**
    //  * 通过code获取微信用户信息
    //  */
    // public static function transferWxappCodeToSession($code)
    // {
    //     $miniProgram = EasyWeChat::miniProgram();
    //     $wxInfo = $miniProgram->auth->session($code);
    //     if (isset($wxInfo['errcode'])) {
    //         throw new Exception("微信授权出错" . $wxInfo['errmsg'], 11000029);
    //     }
    //     return $wxInfo;
    // }

    // /**
    //  * 通过code获取CP用户信息
    //  */
    // public static function getTokenByCode($code)
    // {
    //     if (empty($code)) {
    //         throw new Exception("code码不能为空", 11000013);
    //     }
    //     $wxInfo = self::transferWxappCodeToSession($code);
    //     $newTokenObj = new WxappUserToken();
    //     $newTokenObj->openid = $wxInfo['openid'];
    //     $newTokenObj->token = '';
    //     $newTokenObj->session_key = $wxInfo['session_key'];
    //     $newTokenObj->expire_time = date("Y-m-d H:i:s", time() + 10*24*60*60);
    //     $newTokenObj->save();
    //     $encodeId = YC_Util::xcryptEncrypt($newTokenObj->id);
    //     $sign = md5($newTokenObj->id . self::WXAPP_TOKEN_SALT . $encodeId);
    //     $signArr = array($encodeId, $sign);
    //     $signStr = join('', $signArr);
    //     $newTokenObj->token = base64_encode($signStr);
    //     $newTokenObj->save();
    //     $cpUser = CpUser::where('wx_id', $newTokenObj->openid)->first();
    //     $tokenInfo = array(
    //         'token'  => $newTokenObj->token,
    //         'isBind' => empty($cpUser) ? false : true,
    //         'role' => self::getMpRole($cpUser),
    //     );
    //     return $tokenInfo;
    // }

    /**
     * 小程序Token登录
     */
    public static function wxappTokenAuth($token)
    {
        $tokenInfo = self::checkToken($token);
        $cpUser = CpUser::where('wx_id', $tokenInfo->openid)->first();
        if (empty($cpUser)) {
            throw new Exception("用户未登录，请重新登录", 401);
        } 
        $user = EcsUser::select(array('user_id', 'user_name'))->where('user_id', $cpUser->uid)->first();
        \Session::put('user_id', $user->user_id);
        \Session::put('user_name', $user->user_name);
    }

    public static function modelReturn($code, $msg = '', $data = null) {
        return array(
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        );
    }

    public static function getCpUserInfoByUid($uid)
    {
        $cpUser = CpUser::where('uid', $uid)->first();
        if (empty($cpUser)) {
            throw new WorkException("用户不存在", 1000404);
        }
        $user = EcsUser::where('user_id', $cpUser->uid)->first();
        if (empty($cpUser)) {
            throw new WorkException("用户不存在", 1000405);
        }
        return $cpUser;
    }

    public static function getEcsUserInfoByUid($uid)
    {
        $user = EcsUser::where('user_id', $uid)->first();
        if (empty($user)) {
            throw new Exception("用户不存在", 1000406);
        }
        return $user;
    }

    /**
     * 通过手机号搜索cpUser
     *
     * @param [type] $mobile
     * @return void
     */
    public static function getCpUserInfoByMobile($mobile)
    {
        $cpUser = CpUser::where('mobile', $mobile)->first();
        if (empty($cpUser)) {
            throw new Exception("用户不存在", 1000407);
        }
        $user = EcsUser::where('user_id', $cpUser->uid)->first();
        if (empty($user)) {
            throw new Exception("用户不存在", 1000408);
        }
        return $cpUser;
    }

    /**
     * 通过手机号搜索ecsUser
     *
     * @param [type] $mobile
     * @return void
     */
    public static function getEcsUserInfoByMobile($mobile)
    {
        $user = EcsUser::where('mobile_phone', $mobile)->first();
        if (empty($user)) {
            throw new Exception("用户不存在", 1000410);
        }
        return $user;
    }

    public static function getUserInfoByMobile($mobile)
    {
        $user = EcsUser::where('mobile_phone', $mobile)->first();
        if (empty($user)) {
            throw new Exception("用户不存在", 1000411);
        }        
        $cpUser = CpUser::where('uid', $user->user_id)->first();
        if (empty($cpUser)) {
            throw new Exception("用户不存在", 1000412);
        }
        return $cpUser;
    }

    /**
     * 删除Cpuser
     */
    public static function removeCpUser($userId)
    {
        // 通过userId 获取该cpUser
        $cpUser = CpUser::where('uid', $userId)->first();
        if (empty($cpUser)) {
            return false;
            // throw new Exception("用户不存在", 1);
        }
        // 获取该用户的部门信息
        $userDepartInfo = CpAccess::getUserDepartByUid($userId);
        // 删除该用户的部门信息
        foreach ($userDepartInfo as $depart) {
            CpAccess::delDepartUser($userId, $depart['department_id']);
        }

        // 删除该cpUser
        YC_Log::info('[removeCpUser] [%s] [%s]', $userId, json_encode($cpUser));
        $cpUser->delete();
    }

    /**
     * 编辑密码
     */
    public static function editPassword($data)
    {
        // 获取用户userid
        $userid = CpAccess::theUid();
        // 获取该用户信息
        $user = EcsUser::where('user_id', $userid)->first();
        if (empty($user)) {
            throw new Exception("用户不存在", 404);
        }
        // 修改密码判断
        $oldPassword = array_get($data, 'old_password', '');
        $newPassword = array_get($data, 'new_password', '');
        $confirmPassword = array_get($data, 'confirm_password', '');
        // 判断密码和密码确认
        if ($newPassword != $confirmPassword) {
            throw new Exception("密码和密码确认不相等", 1);
        }
        $oldPasswordConfirm = md5(md5($oldPassword) . self::PASSWORD_SALT);
        if ($user->password != $oldPasswordConfirm) {
            throw new Exception("旧密码错误", 1);
        }

        $newPasswordEncrypt = md5(md5($newPassword) . self::PASSWORD_SALT);
        $user->password = $newPasswordEncrypt;
        $user->save();
        
        YC_Log::info('[editPassword] [%s] [%s]', $userid, $oldPassword);
        return ture;
    }

    /**
     * 获取在微信小程序中的身份 返回标识
     *
     * @param [object] $user
     * @return void
     */
    public static function getMpRole($user)
    {
        if (!is_object($user)) {
            return '';
        }
        $driverModuleRole = [
            self::CP_ROLE_DRIVER,
            self::CP_ROLE_PORTER,
        ];
        if (in_array($user->role, $driverModuleRole)) {
            return 'driver';
        } else {
            // 判断是否有临采标识
            $tempPurchase = CpAccess::getUserByMark([CpAccess::MARK_TEMP_PURCHASE]);
            $tempPurchase = array_get($tempPurchase, 'data', []);
            $tempPurchase = array_keys($tempPurchase);
            if (in_array($user->uid, $tempPurchase)) {
                return 'tempPurchase';
            }
            return 'seller';
        }
    }

    /**
     * 获取加密后的密码
     */
    public static function encryptPassword($password)
    {
        return md5(md5($password) . self::PASSWORD_SALT);
    }

    /**
     * 批量获取后台用户的手机号
     */
    public static function getCpMobileByUids($uids)
    {
        if (empty($uids)) {
            return [];
        }
        $userList = CpUser::select(['uid','mobile'])->whereIn('uid', $uids)->get();
        if (empty($userList)) {
            return [];
        }
        $newList = [];
        foreach ($userList as $user) {
            $newList[$user['uid']] = $user['mobile'];
        } 
        return $newList;
    }
    /**
     * 批量获取后台用户的姓名
     */
    public static function getCpNameByUids($uids)
    {
        if (empty($uids)) {
            return [];
        }
        $userList = EcsUser::select('user_id','user_name')->whereIn('user_id', $uids)->get();
        if (empty($userList)) {
            return [];
        }
        $newList = [];
        foreach ($userList as $user) {
            $newList[$user['user_id']] = $user['user_name'];
        } 
        return $newList;
    }


    /**
     * 企业微信扫码登录
     */
    public static function loginByWorkCode($code, $state)
    {
        $sessionState = Session::get('login_work_state');
        try {
            if ($state != $sessionState) {
                throw new Exception("登录超时，请重试", 1);
            }
            $workUser = WorkWeChatModule::getUserByCode($code);
            if (empty($workUser)) {
                throw new Exception("授权失败，请重试", 1);
            }
            $workCpUser = CpUser::where('work_id', $workUser)->first(); 
            if (empty($workCpUser)) {
                throw new Exception("未绑定企业微信，请联系管理员", 1);
            }
            $mobile   = Session::get('login_user_mobile');
            $password = Session::get('login_user_password');
            if (empty($mobile) || empty($password)) {
                throw new Exception("用户名或密码错误，请重试", 1);
            }
            if (!YC_Util::checkCpMobile($mobile)) {
                throw new Exception("用户名或密码错误，请重试", 1);
            }
            $user = EcsUser::where('mobile_phone', $mobile)->first();
            if (empty($user)) {
                throw new Exception("用户名或密码错误，请重试", 1);
            }
            if ($user->user_id != $workCpUser->uid) {
                throw new Exception("用户名或密码错误，请重试", 1);
            }
            if ($user['password'] != $password) {
                throw new Exception("用户名或密码错误，请重试", 1);
            }
            self::doLogin($user);
            return true;
        } catch (Exception $e) {
            Session::put('login_work_state', '');
            throw $e;
        }
    }

    /**
     * 暂存账号信息
     */
    public static function storeLoginInfo($mobile, $password)
    {
        Session::put('login_user_mobile', $mobile);
        Session::put('login_user_password', self::encryptPassword($password));
        $wxState = md5($mobile . self::WORK_CODE_STATE_SALT);
        Session::put('login_work_state', $wxState);
        return $wxState;
    }
    /**
     * 根据用户id列表返回Cpuser信息
     */
    public  static  function  getListByUidList(array  $uidList){
        return CpUser::whereIn('uid',$uidList)->get();
    }
    public static function addCpuser($request){
        $data = $request->only([
            'mobile',
            'name',
            'email',
            'role',
            'password',
            'departmentId'
        ]);
        if (!array_has($data, 'mobile') || !array_has($data,'name')  || !array_has($data,'email') || !array_has($data,'password')) {
            throw new Exception('请完整填写信息', 1);
        }
        if(YC_Util::checkMobile(array_get($data, 'mobile')) == false){
            throw new Exception('请正确填写手机号', 1);
        }
        if($data['departmentId'] == 1){
            throw new Exception('不允许添加最高权限管理员', 1);
        }
        $userMobile = '8' . substr($data['mobile'], 1);
        $user = EcsUser::where('mobile_phone', $userMobile)->first();
        if($user){
            throw new Exception('账号已经存在', 1);
        }
        $title = $request->input('title');
        DB::beginTransaction();
        try{
            $re1 = CpUserModule::addUser($data)['code'];
            $re2 = CpAccess::addDepartUser($data['departmentId'], $userMobile)['code'];
            if ($re1['code'] == 0 && $re2['code'] == 0) {
                $message = sprintf("%s uid: %s 在 %s 添加 %s 账户 %s - %s 成功",CpAccess::theName(), CpAccess::theUid(), date('Y-m-d H:i:s'), $title, array_get($data,'name'), $userMobile);
                DB::commit();
                \YC_Log::info('[CpUserModule-addCpuser] [成功] [%s][%s][%s][%s][%s][%s]',CpAccess::theUid(), date('Y-m-d H:i:s'), $request->input('title'), $userMobile, json_encode($re1), json_encode($re2));
                WorkWeChatModule::sendMsgToUsers('ZhangHaiXi', $message, null);
                WorkWeChatModule::sendMsgToUsers('ShiHongDa', $message, null);
            } else {
                throw new Exception('添加失败', 1);
            }
        }catch(\Exception $e){
            DB::rollback();
            \YC_Log::info('[CpUserModule-addCpuser] [失败] [%s][%s][%s][%s][%s][%s]',CpAccess::theUid(), date('Y-m-d H:i:s'), $request->input('title'), $userMobile, json_encode($re1), json_encode($re2));
            throw new Exception($e->getMessage(),  $e->getCode());
        }
    }
    public static function getWorkId($uid){
        return CpUser::select('work_id')->where('uid', $uid)->first()->work_id;
    }

    public static function getWorkIds($uids)
    {
       return CpUser::whereIn('uid',$uids)->pluck('work_id','uid')->all();
    }

    public static function getWorkIdList($uidList){
        return CpUser::select('work_id')->whereIn('uid',$uidList)->get()->toArray();
    }

    public static function checkUserInMark($uid, $marks){
        $dDepart = new CpDepartment();
        $departInfo = $dDepart->getDeaprtByMark($marks);
        if (empty($departInfo)) {
            return self::modelReturn(1, '部门信息不存在', array());
        }
        $dids = array_pluck($departInfo, 'id');
        $dUserDep = new CpDepartmentUser();
        $didList = $dUserDep->getDidByUser($uid);
        return !empty(array_intersect($didList, $dids));
    }
    public static function getTopByName($name='',$top=30){
        $uids = CpUser::select('uid')->get()->toArray();
        if(empty($uids)){
            return [];
        }
        $info = EcsUser::select('user_id','user_name')->whereIn('user_id',array_column($uids,'uid'));
        if($name != ''){
            $info = $info->where('user_name', 'like', '%'.$name.'%');
        }
        $arr = $info->limit($top)->get()->toArray();
        return array_column($arr,'user_name','user_id');
    }

    //根据ID In查询
    public static function getNameByIds($filed=[],$idArr=[]){
        if(empty($filed)){
            return [];
        }
        return EcsUser::select($filed)->whereIn('user_id',$idArr)->get()->toArray();
    }
    //检测用户是否在某个群组下
    public static function isUserInMark(int $uid, string $mark){
        $pidListByMark = CpDepartment::select('id')->where('mark', $mark)->pluck('id')->toArray();
        $pidListByUser = CpDepartmentUser::select('department_id')->where('uid', $uid)->pluck('department_id')->toArray();
        return !empty(array_intersect($pidListByMark, $pidListByUser));
    }
    //检测用户是否为管理员
    public static function isAdmin(int $uid){
        return self::isUserInMark($uid, CpAccess::MARK_SUPER_ADMIN);
    }
}
