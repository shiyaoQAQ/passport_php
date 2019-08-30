<?php

namespace App\Modules\Admin\Oauth\Constants;

class OauthConst
{
    // 授权方式常量
    const GRANT_TYPE_NORMAL = 1;
    const GRANT_TYPE_PASSWORD = 2;
    const GRANT_TYPE_MOBILE = 3;
    public static $grantTypeMap = [
        self::GRANT_TYPE_NORMAL => '普通授权',
        self::GRANT_TYPE_PASSWORD => '密码授权',
        self::GRANT_TYPE_MOBILE => '手机授权',
    ];
    // 封禁情况映射
    public static $nukedMap = [
        0 => '正常',
        1 => '封禁',
    ];

    // 生成secret的salt
    public static $salt = 'passportSALT!@#$zxa';
    // 生成code的salt
    public static $codeSalt = 'pc!@#odeSALT!@#$zxa';
}
