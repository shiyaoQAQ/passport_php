<?php

namespace App\Http\Controllers\Rpc;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Admin\Access\CpUserModule;
use App\Modules\Admin\Access\OauthModule;
use App\Modules\User\UserBase\UserModule;
use Pascal\Core\Rpc\Component\RpcControllerTrait;

/**
 * @desc CpUser信息相关Rpc
 */
class CpUserRpc extends Controller
{
    use RpcControllerTrait;

    /**
     * 根据token获取userInfo
     *
     * @param Request $request
     * @return void
     */
    public function getAdminInfoByToken(Request $request)
    {
        $token = $request->input('token');
        try {
            $adminId = OauthModule::checkOauthToken($token);
        } catch (\Exception $e) {
            return $this->rpcReturn(401, $e->getMessage());
        }
        // 获取用户信息 只取ecsuser不用取cpuser
        $userInfo = UserModule::getUserInfo($adminId);
        return $this->rpcReturn(0, 'ok', [
            'user' => $userInfo,
        ]);
    }
}
