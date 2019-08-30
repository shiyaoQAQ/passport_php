<?php

namespace App\Modules\Admin\Access;

use App\Modules\Admin\Oauth\Constants\OauthErrorCode;
use App\Modules\Admin\Oauth\Models\OauthAccessTokens;
use App\Modules\Admin\Oauth\Models\OauthClients;

/**
 * OauthModule类
 * oauth直接功能类
 */
class OauthModule
{
    public static function getClientInfo($clientId)
    {
        $client = OauthClients::find($clientId);
        if (empty ($client)) {
            throwError(OauthErrorCode::CLIENT_NOT_FOUND_2);
        }
        return $client;
    }

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

    public static function checkClient($clientId, $redirectUri)
    {
        $client = OauthClients::find($clientId);
        if (empty ($client)) {
            throwError(OauthErrorCode::CLIENT_NOT_FOUND_2);
        }
        // 判断客户端是否被封禁
        if ($client->is_nuked) {
            throwError(OauthErrorCode::CLIENT_IS_NUKED);
        }
        // 校验本次redirectUri的host是否和创建客户端预设的域名一样
        // ？？？？ TODO
        // 都是自己的项目 先不校验的 后面新增外部项目的时候 需要增加域名
    }
}

