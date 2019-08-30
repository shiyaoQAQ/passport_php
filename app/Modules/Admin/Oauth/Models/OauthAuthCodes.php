<?php

namespace App\Modules\Admin\Oauth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OauthAuthCodes extends Model
{
    protected $table = 'oauth_auth_codes';
    protected $connection = 'passport';
    protected $guarded = [];
    public $timestamps = false;

    public static function getCodeInfo($code)
    {
        return self::where('code', $code)->first();
    }
}
