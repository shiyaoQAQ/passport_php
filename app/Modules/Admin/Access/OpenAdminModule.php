<?php

namespace App\Modules\Admin\Access;

use App\Modules\Admin\Access\Constants\AccessErrorCode;
use App\Modules\User\UserBase\UserModule;

/**
 * 开放平台Oauth获取用户登录信息类
 */
class OpenAdminModule
{
    /**
     * 开放平台获取登录用户信息接口
     *
     */
    public static function getCpUserInfo()
    {
        $userId = CpAccess::theUid();
        if (empty($userId)) {
            throwWorkError(AccessErrorCode::INVAILD_USER);
        }
        // User表信息
        $ecsUser = UserModule::getUserInfo($userId, [
            'user_id',
            'user_name',
        ]);
        $cpUser = CpUserModule::getCpUserInfo($userId, [
            'select' => [
                'mobile',
                'sex',
                'email',
                'role',
            ],
        ]);
        if (empty($ecsUser) || empty($cpUser)) {
            throwWorkError(AccessErrorCode::INVAILD_USER_2);
        }
        $user = [
            'user_id' => $ecsUser->user_id,
            'user_name' => $ecsUser->user_name,
            'mobile' => $cpUser->mobile,
            'sex' => $cpUser->sex,
            'email' => $cpUser->email,
            'role' => $cpUser->role,
            'role_desc' => CpUserModule::getCpRoleDesc($cpUser->role),
        ];

        return $user;
    }
}
