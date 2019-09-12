<?php

namespace App\Modules\Admin\Oauth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OauthClients extends Model
{
    protected $table = 'oauth_clients';
    protected $connection = 'passport';
    protected $guarded = [];

    public function getPageList($filter)
    {
        $constructor = self::select(['*']);
        $list = $constructor->paginate(20);
        return $list;
    }

    /**
     * 更新客户端secret
     * 只能对model实例使用
     *
     * @param [type] $secret
     * @return void
     */
    public function updateClientSecret($secret)
    {
        $this->secret = $secret;
        $this->save();
    }

    /**
     * 获取内部项目列表
     *
     * @param array $filter
     * @return void
     */
    public function getOwnProjects($filter = [])
    {
        $constructor = self::select(['*']);
        $constructor->where('is_trusted', 1);
        $list = $constructor->get();
        return $list;
    }


}
