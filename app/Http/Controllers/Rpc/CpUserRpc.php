<?php

namespace App\Http\Controllers\Rpc;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Admin\Access\CpUserModule;
use App\Modules\User\UserBase\UserModule;
use Pascal\Core\Rpc\Component\RpcControllerTrait;

/**
 * @desc CpUser信息相关Rpc
 */
class CpUserRpc extends Controller
{
    use RpcControllerTrait;

    public function test(Request $request)
    {

        return $this->rpcReturn(0, 'ok', [
            // 'user' => $userInfo,
        ]);
    }

}
