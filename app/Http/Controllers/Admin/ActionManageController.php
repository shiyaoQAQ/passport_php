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

    public function listActionGroupAction($groupId, Request $request)
    {
        $gid = intval($groupId);

        $actionList = ActionModule::getActionGroupActionList($gid);
        return $this->json(0, 'ok', [
            'action_list' => $actionList
        ]);


        
        return view('admin.access.departmentActionGroupAccess')->with('action_list', $ret)
                                                    ->with('group_info', $groupInfo)        
                                                    ->with('action_info_json', json_encode($actionList['data']))
                                                    ->with('deaprt_info_json', json_encode($departList['data']))
                                                    ->with('gid', $gid);

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
    public function updateActionGroupAction($groupId, Request $requset)
    {
        $gid = intval($groupId);
        if (empty ($gid)) {
            throwWorkError(AccessErrorCode::INVAILD_ACTION_GROUP_2);
        }
        $groupInfo  = CpAccess::getActionGroupInfo($gid);
        if ($groupInfo['code'] != 0 || empty($groupInfo['data'])) {
            throwWorkError(AccessErrorCode::INVAILD_ACTION_GROUP_2);
        }
    }



}
