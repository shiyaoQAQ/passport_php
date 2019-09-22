<?php

namespace App\Http\Middleware;

use Closure;
use App\Modules\Admin\Access\CpAccess;
use App\Modules\Admin\Access\CpUserModule;
use Exception;
use App\Exceptions\WorkException;
use App\Modules\Admin\Access\OauthModule;

/**
 * OAuthToken鉴权校验
 */
class CpOauthTokenAuth
{
    public function handle($request, Closure $next)
    {
        $accessToken = $request->input('access_token');
        $adminId = OauthModule::checkOauthToken($accessToken);
        
        CpAccess::setTheUid($adminId);
        return $next($request);
    }


}
