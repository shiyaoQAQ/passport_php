<?php

namespace App\Modules\Admin\Access;

use App\Modules\Admin\Oauth\Constants\OauthConst;
use App\Modules\Admin\Oauth\Constants\OauthErrorCode;
use App\Modules\Admin\Oauth\Models\OauthAccessTokens;
use App\Modules\Admin\Oauth\Models\OauthAuthCodes;
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

    /**
     * 获取client用来换token的code
     */
    public static function getAuthorizationCode($clientId, $userId, $scope)
    {
        $appid = md5($clientId);
        $salt = md5(OauthConst::$codeSalt);
        $random = md5(rand(1, 100000));
        $encrypt = md5(substr($appid, 0, 24) . substr($salt, 0, 4) . substr($random, 0, 4));
        $code = strtolower($encrypt);
        // 创建code
        OauthAuthCodes::create([
            'code' => $code,
            'user_id' => $userId,
            'client_id' => $clientId,
            'scopes' => json_encode($scope ?: ''),
            'is_nuked' => 0,
            'expires_at' => date('Y-m-d H:i:s', time() + 300),
        ]);
        return $code;
    }

    /**
     * 获取token接口
     * 禁止外部直接访问获取权限接口
     *
     * @param [type] $clientId
     * @param [type] $userId
     * @param [type] $scope
     * @return void
     */
    private static function getAccessToken($clientId, $userId, $scope)
    {
        $tmp = $clientId . $userId . time() . rand(1, 100000);
        $token = hash('sha1', md5($tmp));
        $expiresIn = date('Y-m-d H:i:s', time() + 3600 * 20);
        OauthAccessTokens::create([
            'token' => $token,
            'user_id' => $userId,
            'client_id' => $clientId,
            'scopes' => $scope ?: '',
            'is_nuked' => 0,
            'expires_at' => $expiresIn,
        ]);
        return [
            'access_token' => $token,
            'refresh_token' => '',
            'expires_in' => $expiresIn,
        ];
    }

    public static function getTokenByAuthorizationCode($clientId, $code, $signature, $body)
    {
        $client = OauthClients::find($clientId);
        if (empty ($client)) {
            throwError(OauthErrorCode::CLIENT_NOT_FOUND_3);
        }

        // 取code
        $codeInfo = OauthAuthCodes::getCodeInfo($code);
        if (empty ($codeInfo)) {
            throwError(OauthErrorCode::CODE_NOT_FOUND);
        }
        if ($codeInfo->is_nuked) {
            throwError(OauthErrorCode::CODE_IS_NUKED);
        }
        if ($codeInfo->expires_at < date('Y-m-d H:i:s')) {
            throwError(OauthErrorCode::CODE_IS_EXPIRED);
        }

        // 校验签名
        \YC_Util::checkSignature($signature, $client->id, $client->secret, $body);
        
        // 颁发token
        $token = self::getAccessToken($codeInfo->client_id, $codeInfo->user_id, $codeInfo->scopes);
        return $token;
    }


}

