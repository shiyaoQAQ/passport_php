<?php

namespace App\Modules\Admin\Access;

use App\Modules\Admin\Oauth\Constants\OauthConst;
use App\Modules\Admin\Oauth\Constants\OauthErrorCode;
use App\Modules\Admin\Oauth\Models\OauthClients;

/**
 * OauthManagerModule类
 * oauth对应的客户端token等管理类
 */
class OauthManagerModule
{
    /**
     * 获取客户端列表
     *
     * @param [type] $filter
     * @return array
     */
    public static function getClientList($filter)
    {
        $oc = new OauthClients;
        $list = $oc->getPageList($filter);
        $list = $list->toArray();
        // 处理数据
        foreach ($list['data'] as &$item) {
            $item['grant_type_desc'] = array_get(OauthConst::$grantTypeMap, $item['grant_type'] ?: 0, '');
            $item['is_nuked_desc'] = array_get(OauthConst::$nukedMap, $item['is_nuked'] ?: 0, '');
        }
        unset($item);
        return $list;
    }

    /**
     * 通过AppId生成AppSecret
     */
    public static function getAppSecret($appid)
    {
        $appid = md5($appid);
        $salt = md5(OauthConst::$salt);
        $random = md5(rand(1, 100000));
        $encrypt = md5(substr($appid, 0, 24) . substr($salt, 0, 4) . substr($random, 0, 4));
        return strtoupper($encrypt);
    }

    /**
     * 添加客户端
     *
     * @param [type] $data
     * @return void
     */
    public static function storeClient($data)
    {
        $data['secret'] = '';
        $client = OauthClients::create($data);
        $secret = self::getAppSecret($client->id);
        $client->updateClientSecret($secret);
    }

    /**
     * 更新客户端数据
     */
    public static function updateClient($id, $data)
    {
        try {
            $client = OauthClients::findOrFail($id);
        } catch (\Exception $e) {
            throwError(OauthErrorCode::CLIENT_NOT_FOUND);
        }
        $data = array_only($data, [
            'name',
            'redirect',
            'is_nuked',
        ]);
        $client->update($data);
    }

}

