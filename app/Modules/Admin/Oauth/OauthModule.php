<?php

namespace App\Modules\Admin\Access;

use App\Modules\Admin\Oauth\Constants\OauthErrorCode;
use App\Modules\Admin\Oauth\Models\OauthAccessTokens;

/**
 * OauthModule类
 * oauth直接功能类
 */
class OauthModule
{
    /**
     * 检查oauthToken返回用户id
     *
     * @param [type] $token
     * @return int user_id
     */
    public static function checkOauthToken($token)
    {
        $token = OauthAccessTokens::getToken($token);
        // 判断过期时间
        if (empty($token)) {
            throwWorkError(OauthErrorCode::TOKEN_NOT_FOUND);
        }
        if ($token->is_nuked) {
            throwWorkError(OauthErrorCode::TOKEN_IS_NUKED);
        }
        if ($token->expires_at < date('Y-m-d H:i:s')) {
            throwWorkError(OauthErrorCode::TOKEN_IS_EXPIRED);
        }
        return $token->user_id;
    }
}

