<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Admin\Access\ActionModule;
use App\Modules\Admin\Access\Constants\AccessConst;
use App\Modules\Admin\Access\Constants\AccessErrorCode;
use App\Modules\Admin\Access\CpAccess;
use App\Modules\Admin\Access\CpUserModule;
use App\Modules\Admin\Access\Models\CpDepartment;
use App\Modules\Admin\Access\Models\CpDepartmentUser;
use App\Modules\Admin\Access\Models\CpDepartmentAction;
use App\Modules\Admin\Project\ProjectModule;

/**
 * @desc 新权限管理
 */
class ActionManageController extends Controller
{
    /**
     * @desc 获取权限组列表
     *
     * @param Request $request
     * @return void
     */
    public function listActionGroup(Request $request)
    {
        $list = CpAccess::getActionGroupList();
        $result = [
            'action_group_list' => $list['data'] ?: []
        ];
        return $this->json(0, 'ok', $result);
    }

    /**
     * @desc 获取权限组可选项目列表
     *
     * @param Request $request
     * @return void
     */
    public function listAccessProject(Request $request)
    {
        $result = [
            'access_project_list' => ProjectModule::getProjectMap(),
        ];
        return $this->json(0, 'ok', $result);
    }

    /**
     * @desc 获取权限组的详情
     *
     * @param Request $request
     * @return void
     */
    public function showActionGroupDetail($groupId, Request $request)
    {
        $gid = intval($groupId);
        $detail = ActionModule::getActionGroupDetail($gid);
        return $this->json(0, 'ok', $detail);
    }

    /**
     * @desc 获取权限组的部门树
     *
     * @param Request $request
     * @return void
     */
    public function showActionGroupTree($groupId, Request $request)
    {
        $gid = intval($groupId);
        $treeInfo = ActionModule::getActionGroupDepartmentTree($gid);
        return $this->json(0, 'ok', $treeInfo);
    }

    /**
     * @desc 获取权限组的权限列表
     *
     * @param [type] $groupId
     * @param Request $request
     * @return void
     */
    public function listActionGroupAction($groupId, Request $request)
    {
        $gid = intval($groupId);

        $actionList = ActionModule::getActionGroupActionList($gid);
        return $this->json(0, 'ok', [
            'action_list' => $actionList
        ]);
    }

    /**
     * @desc 更新权限组对应部门
     *
     * @param [type] $groupId
     * @param Request $requset
     * @return void
     */
    public function updateActionGroupDepartment($groupId, Request $request)
    {
        $gid = intval($groupId);
        if (empty ($gid)) {
            throwWorkError(AccessErrorCode::INVAILD_ACTION_GROUP);
        }
        $groupInfo  = CpAccess::getActionGroupInfo($gid);
        if ($groupInfo['code'] != 0 || empty($groupInfo['data'])) {
            throwWorkError(AccessErrorCode::INVAILD_ACTION_GROUP);
        }
        // 获取更新资源
        $departmentIncrease = $request->input('departmentIncrease');
        $departmentReduce = $request->input('departmentReduce');

        if (!empty($departmentIncrease)) {
            CpAccess::addDepartActionGroup($departmentIncrease, $gid);
        }
        if (!empty($departmentReduce)) {
            CpAccess::removeDepartActionGroup($departmentReduce, $gid);
        }
    
        return $this->json(0, '更新成功');
    }

    /**
     * @desc 更新权限组对应权限
     *
     * @param [type] $groupId
     * @param Request $requset
     * @return void
     */
    public function updateActionGroupAction($groupId, Request $request)
    {
        $gid = intval($groupId);
        if (empty ($gid)) {
            throwWorkError(AccessErrorCode::INVAILD_ACTION_GROUP_2);
        }
        $groupInfo  = CpAccess::getActionGroupInfo($gid);
        if ($groupInfo['code'] != 0 || empty($groupInfo['data'])) {
            throwWorkError(AccessErrorCode::INVAILD_ACTION_GROUP_2);
        }

        // 获取更新资源
        $actionIncrease = $request->input('actionIncrease');
        $actionReduce = $request->input('actionReduce');
        if (!empty($actionIncrease)) {
            foreach ($actionIncrease as &$item) {
                list($controller, $action) = explode('-', $item);
                $item = [
                    'controller' => $controller,
                    'action'     => $action,
                    'inherit'    => 0,
                    'limit'      => 0,
                ];
            }
            $addRet = CpAccess::addActionGroupAccess($actionIncrease, $gid);
        }
        if (!empty($actionReduce)) {
            foreach ($actionReduce as &$item) {
                list($controller, $action) = explode('-', $item);
                $item = [
                    'controller' => $controller,
                    'action'     => $action,
                    'inherit'    => 0,
                    'limit'      => 0,
                ];
            }
            $reRet  = CpAccess::removeActionGroupAccess($actionReduce, $gid);
        }

        return $this->json(0, '更新成功');
    }

    /**
     * @desc 获取部门的独立权限
     *
     * @param [type] $groupId
     * @param Request $request
     * @return void
     */
    public function listDepartmentTmpAction($did, Request $request)
    {
        $did = intval($did);
        $project = $request->input('project');
        $actionList = ActionModule::getDepartmentTmpActionList($did, $project);
        return $this->json(0, 'ok', [
            'action_list' => $actionList
        ]);
    }

      /**
     * @desc 更新部门的独立权限
     *
     * @param [type] $groupId
     * @param Request $requset
     * @return void
     */
    public function updateDepartmentTmpAction($did, Request $request)
    {
        $did = intval($did);
        $project = $request->input('project');
        if (empty ($did)) {
            throwWorkError(AccessErrorCode::INVAILD_DEPART_2);
        }
        $depart = CpAccess::getDepartInfo($did);
        if ($depart['code'] != 0 || empty($depart['data'])) {
            throwWorkError(AccessErrorCode::INVAILD_DEPART_2);
        }

        // 获取更新资源
        $actionIncrease = $request->input('actionIncrease');
        $actionReduce = $request->input('actionReduce');
        if (!empty($actionIncrease)) {
            foreach ($actionIncrease as &$item) {
                list($controller, $action) = explode('-', $item);
                $item = [
                    'controller' => $controller,
                    'action'     => $action,
                    'inherit'    => 0,
                    'limit'      => 0,
                ];
            }
            $addRet = CpAccess::addDepartAction($actionIncrease, $did, $project);
        }
        if (!empty($actionReduce)) {
            foreach ($actionReduce as &$item) {
                list($controller, $action) = explode('-', $item);
                $item = [
                    'controller' => $controller,
                    'action'     => $action,
                    'inherit'    => 0,
                    'limit'      => 0,
                ];
            }
            $reRet  = CpAccess::removeDepartAction($actionReduce, $did, $project);
        }

        return $this->json(0, '更新成功');
    }
    
}
