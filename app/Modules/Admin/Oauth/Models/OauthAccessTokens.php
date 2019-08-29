<?php

namespace App\Modules\Admin\Oauth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OauthAccessTokens extends Model
{
    protected $table = 'oauth_access_tokens';
    protected $connection = 'passport';

}
