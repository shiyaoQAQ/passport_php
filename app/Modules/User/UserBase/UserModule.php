<?php

namespace App\Modules;

use App\Modules\Admin\Access\Models\User\EcsUser;

class UserModule
{
    const DEFAULT_RANK = 6;
    const USER_RANK_COMMON = 1;
    const USER_RANK_SILVER = 5;
    const USER_RANK_GOLD = 6;
    const USER_RANK_PLATINUM = 7;
    const USER_RANK_DIAMOND = 8;
    const USER_RANK_UNLIMITED = 10;

    // 充值无优惠客户
    const USER_RANK_PREPAID = 100;
    
    //用户等级配置
    public static $userRankMap = array(
        self::USER_RANK_GOLD => '金卡会员',
        self::USER_RANK_SILVER => '银卡会员',
        self::USER_RANK_PLATINUM => '白金卡会员',
        self::USER_RANK_DIAMOND => '钻石卡会员',
        self::USER_RANK_UNLIMITED => '无限卡会员',
        self::USER_RANK_COMMON => '普通会员',
        self::USER_RANK_PREPAID => '无优惠客户',
    );

    public static function getUserById($uid){
        $objUser = new EcsUser();
        $ret = $objUser->getById($uid);
        return self::modelReturn(0, 'suc', $ret);
    }

    public static function getUserRank($uid){
        return EcsUser::where('user_id', $uid)->value('user_rank');
    }

    public static function getUserRankDetail($uid)
    {
        $userModel = new EcsUser;
        $userRank = $userModel->getUserRankById($uid);
        return $userRank;
    }

    public static function getUserRankName($userRank)
    {
        return array_get(UserModule::$userRankMap, $userRank, '');
    }

    public static function modelReturn($code, $msg = '', $data = null){
        return array(
            'code' => $code, 
            'msg'  => $msg,
            'data' => $data,
        );
    }

    /**
     * 获取username
     *
     * @param [type] $uid
     * @return void
     */
    public static function getUserName($uid)
    {
        $user = EcsUser::find($uid);
        if (empty($user)) {
            return '';
        } else {
            return $user->user_name;
        }
    }
    /**
     * 获取userMobile
     *
     * @param [type] $uid
     * @return void
     */
    public static function getUserMobile($uid)
    {
        $user = EcsUser::find($uid);
        if (empty($user)) {
            return '';
        } else {
            return $user->mobile_phone;
        }
    }

    public static function getUserByMobile($mobile)
    {
        $user = EcsUser::where('mobile_phone', strval($mobile))->first();
        return $user;
    }
   /**
     *
     * @param $mobile 手机号
     * @param $refer 来源
     * @param $cityCode 城市ID
     * @return 返回这个手机号用户的uid
     */
    public static  function getOrCreateUserId($mobile, $refer, $cityCode = 110100)
    {
        $userData = [
            'user_name' => $mobile,
            'mobile' => $mobile,
            'user_rank' => UserModule::USER_RANK_COMMON,
            'city_id' => $cityCode,
            'refer' => $refer,
        ];
        return self::createUser($userData)->user_id;;
    }
    
 
    public static function getUserInfo($userId)
    {
        $user = EcsUser::findOrFail($userId);
        return $user;
    }

    /**
     * 创建ecsUser用户
     *
     * @param [type] $data
     * @return void
     */
    public static function createUser($data)
    {
        $tryUser = EcsUser::where('mobile_phone', $data['mobile'])
            ->first();
        if (!empty($tryUser)) {
            return $tryUser;
        }
        $user = new EcsUser;
        $user->user_name = $data['user_name'];
        $user->reg_time = time();
        $user->mobile_phone = $data['mobile'];
        $user->city_id = $data['city_id'] ?: 0;
        $user->refer = $data['refer'] ?: '';
        $user->is_validated = 0;
        $user->alias = '';
        $user->msn = '';
        $user->qq = '';
        $user->office_phone = '';
        $user->home_phone = '';
        $user->last_time = '2017-01-01 00:00:00';
        $user->birthday = '2017-01-01';
        $user->credit_line = 0;
        $user->user_rank = isset($data['user_rank']) ? $data['user_rank'] : self::DEFAULT_RANK;
        $user->save();
        return $user;
    }

    /**
     * 通过用户uids获取id name的kv映射
     */
    public static function getUserNameMap($userIdList)
    {
        if (!is_array($userIdList)) {
            return [];
        }
        return EcsUser::select('user_name', 'user_id')->whereIn('user_id', $userIdList)
            ->pluck('user_name', 'user_id')->toArray();
    }

    /**
     * 获取手机号列表，根据uid列表
     */
    public static function getMobileList(array $uidList):array
    {
        return EcsUser::whereIn('user_id', $uidList)->pluck('mobile_phone', 'user_id')->toArray();
    }
     /**
     * 获取名字列表，根据uid列表
     */
    public static function getNameList(array $uidList):array
    {
        return EcsUser::whereIn('user_id', $uidList)->pluck('user_name', 'user_id')->toArray();
    }
    // /**
    //  * 检测是否是销售 
    //  */
    // public static function isSeller($uid){
    //     $sellerList = CpAccess::getUserByMark(CpAccess::$saleMark);
    //     $sellerIds = array_keys($sellerList['data']);
    //     return in_array($uid, $sellerIds);
    // }
}
