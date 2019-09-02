<?php

namespace App\Modules\Admin\Oauth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OauthAccessTokens extends Model
{
    protected $table = 'oauth_access_tokens';
    protected $connection = 'passport';
    protected $guarded = [];

    public static function getToken($token)
    {
        return self::where('token', $token)->first();
    }

    /**
     * 封禁用户token
     *
     * @param [type] $uid
     * @param [type] $clientId
     * @return void
     */
    public static function nukeUserToken($uid, $clientId)
    {
        $tokenList = self::where('user_id', $uid)
            ->where('client_id', $clientId)
            ->where('expires_at', '>', date('Y-m-d H:i:s'))
            ->where('is_nuked', 0)
            ->get();
        $tokenList = $tokenList->isEmpty() ? [] : $tokenList->toArray();
        // 简单记录一下tokenId和操作人
        $tokens = array_column($tokenList, 'token');
        $ids = array_column($tokenList, 'id');
        \YC_Log::info('[passport OauthAccessTokens nukeUserToken][%s]', json_encode($tokens));
        // 将所有token失效
        self::whereIn('id', $ids)->update([
            'is_nuked' => 1
        ]);
    }

}
