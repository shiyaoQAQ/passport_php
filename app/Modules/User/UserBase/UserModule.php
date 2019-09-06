<?php

namespace App\Modules\User\UserBase;

use App\Modules\User\UserBase\Models\EcsUser;
use App\Modules\User\UserBase\Models\EcsUserRank;

/**
 * 用户Module
 */
class UserModule
{
    /**
     * 获取用户基础信息
     *
     * @param [type] $uid
     * @param [type] $argument
     * @return void
     */
    public static function getUserInfo($uid, $argument = [])
    {
        $user = EcsUser::getUserInfo($uid, $argument);
        return $user;
    }

    /**
     * 获取用户基础信息列表
     *
     * @param [type] $uids
     * @return void
     */
    public static function getUserInfoByIds($uids, $argument = [])
    {
        $users = EcsUser::getUserInfoByIds($uids, $argument);
        return $users;
    }

    /**
     * 获取username
     *
     * @param [type] $uid
     * @return void
     */
    public static function getUserName($uid)
    {
        $user = self::getUserInfo($uid);
        return $user ? $user->user_name : '';
    }

    /**
     * 获取userMobile
     *
     * @param [type] $uid
     * @return void
     */
    public static function getUserMobile($uid)
    {
        $user = self::getUserInfo($uid);
        return $user ? $user->mobile_phone : '';
        // $user = EcsUser::find($uid);
        // if (empty($user)) {
        //     return '';
        // } else {
        //     return $user->mobile_phone;
        // }
    }

    /**
     * 通过用户uids获取id name的kv映射
     */
    public static function getUserNameMap($uids)
    {
        if (!is_array($uids) || empty($uids)) {
            return [];
        }
        return EcsUser::getUserNameMap($uids);
    }

    /**
     * 通过用户uids获取id mobile的kv映射
     */
    public static function getUserMobileMap($uids)
    {
        if (!is_array($uids) || empty($uids)) {
            return [];
        }
        return EcsUser::getUserMobileMap($uids);
    }


    // public static function getUserById($uid)
    // {
    //     $objUser = new EcsUser();
    //     $ret = $objUser->getById($uid);
    //     return self::modelReturn(0, 'suc', $ret);
    // }

    // public static function getUserRank($uid)
    // {
    //     $user = self::getUserInfo($uid);
    //     return EcsUser::where('user_id', $uid)->value('user_rank');
    // }

    /**
     * 获取用户等级详情
     *
     * @param [type] $uid
     * @return void
     */
    public static function getUserRankDetail($uid)
    {
        $user = self::getUserInfo($uid);
        $userRank = EcsUserRank::getUserRank($user->user_rank ?: 0);
        return $userRank;
    }

    /**
     * 获取用户等级名称
     *
     * @param [type] $userRank
     * @return void
     */
    public static function getUserRankName($userRank)
    {
        return array_get(UserModule::$userRankMap, $userRank, '');
    }

    // public static function modelReturn($code, $msg = '', $data = null){
    //     return array(
    //         'code' => $code, 
    //         'msg'  => $msg,
    //         'data' => $data,
    //     );
    // }

    /**
     * 通过手机号获取基础用户
     *
     * @param [type] $mobile
     * @return void
     */
    public static function getUserByMobile($mobile, $argument = [])
    {
        return EcsUser::getByMobile($mobile, $argument);
    }

    // /**
    //  *
    //  * @param $mobile 手机号
    //  * @param $refer 来源
    //  * @param $cityCode 城市ID
    //  * @return 返回这个手机号用户的uid
    //  */
    // public static  function getOrCreateUserId($mobile, $refer, $cityCode = 110100)
    // {
    //     $userData = [
    //         'user_name' => $mobile,
    //         'mobile' => $mobile,
    //         'user_rank' => UserModule::USER_RANK_COMMON,
    //         'city_id' => $cityCode,
    //         'refer' => $refer,
    //     ];
    //     return self::createUser($userData)->user_id;;
    // }

    /**
     * 创建ecsUser用户
     * 通过mobile获取或创建用户
     *
     * @param [type] $data
     * [
     * user_name
     * mobile
     * city_id
     * refer
     * user_rank
     * ]
     * @return EcsUser
     */
    public static function createUser($data)
    {
        $tryUser = EcsUser::where('mobile_phone', $data['mobile'])
            ->first();
        if (!empty($tryUser)) {
            return $tryUser;
        }

        $user = EcsUser::createSimpleUser($data);
        return $user;
    }

    //  /**
    //  * 获取名字列表，根据uid列表
    //  */
    // public static function getNameList(array $uidList):array
    // {
    //     return EcsUser::whereIn('user_id', $uidList)->pluck('user_name', 'user_id')->toArray();
    // }
    // /**
    //  * 检测是否是销售 
    //  */
    // public static function isSeller($uid){
    //     $sellerList = CpAccess::getUserByMark(CpAccess::$saleMark);
    //     $sellerIds = array_keys($sellerList['data']);
    //     return in_array($uid, $sellerIds);
    // }
}
