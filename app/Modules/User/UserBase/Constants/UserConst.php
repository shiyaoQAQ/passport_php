<?php

namespace App\Modules\User\UserBase\Constants;

class UserConst
{
    const DEFAULT_RANK = 6;
    const USER_RANK_COMMON = 1;
    const USER_RANK_SILVER = 5;
    const USER_RANK_GOLD = 6;
    const USER_RANK_PLATINUM = 7;
    const USER_RANK_DIAMOND = 8;
    const USER_RANK_UNLIMITED = 10;

    const ROLE_USER  = 'user';
    const ROLE_ADMIN = 'admin';

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

    public static $userColumn = [
        'user_id',
        'city_id',
        'user_name',
        'role',
        'user_rank',
        'mobile_phone',
        'is_allocated',
        'refer',
        'remember_token',
    ];

}
