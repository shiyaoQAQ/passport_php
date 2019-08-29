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


}
