<?php

namespace App\Http\Middleware;

use Closure;
use App\Modules\Admin\Access\CpAccess;
use App\Modules\Admin\Access\CpUserModule;
use Exception;
use App\Exceptions\WorkException;
use App\Modules\Admin\Access\OauthModule;
use YC_Log;

/**
 * OAuthToken鉴权校验
 */
class CpOauthTokenAuth
{
    public function handle($request, Closure $next)
    {
        YC_Log::info("[supersetdebug][%s][%s]", json_encode($request->input()), json_encode($request->header()));
        $accessToken = $request->input('access_token');
        $adminId = OauthModule::checkOauthToken($accessToken);
        
        CpAccess::setTheUid($adminId);
        return $next($request);
    }


}
