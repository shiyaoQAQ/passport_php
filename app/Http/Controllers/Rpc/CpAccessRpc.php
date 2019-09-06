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
 * @desc CpAccess信息相关Rpc
 */
class CpAccessRpc extends Controller
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
        $result = [
            'show_access_menu_list' => $menu,
            'show_access_list' => $showAccessList,
        ];
        return $this->rpcReturn(0, 'ok', $result);
    }

    /**
     * 增加部门用户
     *
     * @param Request $request
     * @return void
     */
    public function addDepartUser(Request $request)
    {
        $did = $request->input('did');
        $mobile = $request->input('mobile');
        $result = CpAccess::addDepartUser($did, $mobile);
        return $this->rpcReturn($result['code'], $result['msg'], $result['data']);
    }

    public function getSaleGroupWithName(Request $request)
    {
        // raw
        $result = CpAccess::getSaleGroupWithName();
        return $this->rpcReturn(0, 'ok', $result);
    }

    public function getSaleGroupUsers(Request $request)
    {
        // raw
        $result = CpAccess::getSaleGroupUsers();
        return $this->rpcReturn(0, 'ok', $result);
    }

    public function getUserByMark(Request $request)
    {
        $marks = $request->input('marks');
        $result = CpAccess::getUserByMark($marks);
        return $this->rpcReturn($result['code'], $result['msg'], $result['data']);
    }

    public function getUserChildUser(Request $request)
    {
        $uid = $request->input('uid');
        $mark = $request->input('mark') ?: false;
        $result = CpAccess::getUserChildUser($uid, $mark);
        return $this->rpcReturn($result['code'], $result['msg'], $result['data']);
    }

    public function getUserByResource(Request $request)
    {
        $controller = $request->input('controller');
        $resource = $request->input('resource');
        $result = CpAccess::getUserByResource($controller, $resource);
        return $this->rpcReturn($result['code'], $result['msg'], $result['data']);
    }

    public function getDepartByResource(Request $request)
    {
        $controller = $request->input('controller');
        $resource = $request->input('resource');
        $result = CpAccess::getDepartByResource($controller, $resource);
        return $this->rpcReturn($result['code'], $result['msg'], $result['data']);
    }

    public function getUserChildDepart(Request $request)
    {
        $uid = $request->input('uid');
        $mark = $request->input('mark') ?: false;
        $result = CpAccess::getUserChildDepart($uid, $mark);
        return $this->rpcReturn($result['code'], $result['msg'], $result['data']);
    }

    public function selectAccess(Request $request)
    {
        // 设置一下userid
        $userId = $request->input('user_id') ?: 0;
        CpAccess::setTheUid($userId);

        $accessKey = $request->input('accessKey');
        $accessVal = $request->input('accessVal');
        $result = CpAccess::selectAccess($accessKey, $accessVal);
        return $this->rpcReturn($result['code'], $result['msg'], $result['data']);
    }

    public function getAccessVal(Request $request)
    {
        // 设置一下userid
        $userId = $request->input('user_id') ?: 0;
        CpAccess::setTheUid($userId);

        $key = $request->input('key');
        $accessList = $request->input('accessList') ?: [];
        $result = CpAccess::getAccessVal($key, $accessList);
        return $this->rpcReturn(0, 'ok', $result);
    }

    public function getAccessDetail(Request $request)
    {
        // 设置一下userid
        $userId = $request->input('user_id') ?: 0;
        CpAccess::setTheUid($userId);

        $key = $request->input('key');
        $mapCode = $request->input('mapCode') ?: [];
        $result = CpAccess::getAccessDetail($key, $mapCode);
        return $this->rpcReturn(0, 'ok', $result);
    }

    public function getAccessList(Request $request)
    {
        // 设置一下userid
        $userId = $request->input('user_id') ?: 0;
        CpAccess::setTheUid($userId);

        $key = $request->input('key');
        $mapCode = $request->input('mapCode') ?: [];
        $result = CpAccess::getAccessList($key, $mapCode);
        return $this->rpcReturn(0, 'ok', $result);
    }

    public function hasAccess(Request $request)
    {
        $uid = $request->input('uid');
        $class = $request->input('class');
        $action = $request->input('action');
        $result = CpAccess::hasAccess($uid, $class, $action);
        return $this->rpcReturn($result['code'], $result['msg'], $result['data']);
    }

    public function hasResource(Request $request)
    {
        $uid = $request->input('uid');
        $controller = $request->input('controller');
        $resource = $request->input('resource');
        $result = CpAccess::hasResource($uid, $controller, $resource);
        return $this->rpcReturn($result['code'], $result['msg'], $result['data']);
    }

    public function getUserResouceList(Request $request)
    {
        $uid = $request->input('uid');
        $result = CpAccess::getUserResouceList($uid);
        return $this->rpcReturn($result['code'], $result['msg'], $result['data']);
    }

    public function getAccess(Request $request)
    {
        $uid = $request->input('uid');
        $project = $request->input('project') ?: [];
        $result = CpAccess::getAccess($uid, $project);
        return $this->rpcReturn($result['code'], $result['msg'], $result['data']);
    }

    public function delDepartUser(Request $request)
    {
        $uid = $request->input('uid');
        $did = $request->input('did');
        $result = CpAccess::delDepartUser($uid, $did);
        return $this->rpcReturn($result['code'], $result['msg'], $result['data']);
    }

    public function getDepartResourceList(Request $request)
    {
        $did = $request->input('did');
        $result = CpAccess::getDepartResourceList($did);
        return $this->rpcReturn($result['code'], $result['msg'], $result['data']);
    }

    public function getUserDepartByUid(Request $request)
    {
        $uid = $request->input('uid');
        $result = CpAccess::getUserDepartByUid($uid);
        return $this->rpcReturn(0, 'ok', $result);
    }

    public function getDepartmentByMark(Request $request)
    {
        $mark = $request->input('mark');
        $result = CpAccess::getDepartmentByMark($mark);
        return $this->rpcReturn(0, 'ok', $result);
    }

    public function getNewSaleGroupWithName(Request $request)
    {
        $result = CpAccess::getNewSaleGroupWithName();
        return $this->rpcReturn(0, 'ok', $result);
    }

    public function checkUserInDepartByMark(Request $request)
    {
        $userId = $request->input('userId');
        $mark = $request->input('mark');
        $result = CpAccess::checkUserInDepartByMark($userId, $mark);
        return $this->rpcReturn(0, 'ok', $result);
    }

    public function getUserAccessMark(Request $request)
    {
        $userId = $request->input('userId');
        $controller = $request->input('controller');
        $result = CpAccess::getUserAccessMark($userId, $controller);
        return $this->rpcReturn($result['code'], $result['msg'], $result['data']);
    }

    public function getCityServiceMap(Request $request)
    {
        $cityList = $request->input('cityList');
        $result = CpAccess::getCityServiceMap($cityList);
        return $this->rpcReturn(0, 'ok', $result);
    }
}
