<?php

namespace App\Http\Controllers\Rpc;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Admin\Access\CpAccess;
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

    /**
     * 校验接口是否有权限
     *
     * @param Request $request
     * @return void
     */
    public function checkAccess(Request $request)
    {
        $userId = $request->input('user_id');
        $project = $request->input('project');
        $class = $request->input('class');
        $action = $request->input('action');
        $params = $request->input('params');
        $params = json_decode($params, true);
        // 暂存一下user_id 以后有时间慢慢改里面通过传入赋值
        \Session::put('user_id', $userId);

        $checkResult = CpAccess::checkAccess($project, $class, $action, $params);
        return $this->rpcReturn($checkResult['code'], $checkResult['msg'], $checkResult['data']);
    }

    /**
     * 渲染菜单
     *
     * @param Request $request
     * @return void
     */
    public function renderMenu(Request $request)
    {
        $class = $request->input('class');
        $userId = $request->input('user_id');
        \Session::put('user_id', $userId);

        $menu = CpAccess::getMenu();
        $showAccessList = CpAccess::getAccessPath($class);
        return $this->rpcReturn(0, 'ok', [
            'show_access_menu_list' => $menu,
            'show_access_list' => $showAccessList,
        ]);
    }
}
