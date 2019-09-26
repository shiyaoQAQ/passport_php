<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Admin\Access\Constants\AccessConst;
use App\Modules\Admin\Access\CpAccess;
use App\Modules\Admin\Access\CpUserModule;
use App\Modules\Admin\Access\Models\CpDepartment;
use App\Modules\Admin\Access\Models\CpDepartmentUser;
use App\Modules\Admin\Access\Models\CpDepartmentAction;
use App\Modules\Admin\Access\ResourceModule;
use App\Modules\Admin\Project\ProjectModule;

/**
 * @desc 新资源管理
 */
class ResourceManageController extends Controller
{
    /**
     * @desc 获取资源组列表
     *
     * @param Request $request
     * @return void
     */
    public function listResourceGroup(Request $request)
    {
        $list = CpAccess::getResourceGroupList();
        $result = [
            'resource_group_list' => $list['data'] ?: []
        ];
        return $this->json(0, 'ok', $result);
    }

    /**
     * @desc 获取资源组的详情
     *
     * @param Request $request
     * @return void
     */
    public function showResourceGroupDetail($groupId, Request $request)
    {
        $gid = intval($groupId);
        $detail = ResourceModule::getResourceGroupDetail($gid);
        return $this->json(0, 'ok', $detail);
    }

      /**
     * @desc 获取资源组的部门树
     *
     * @param Request $request
     * @return void
     */
    public function showResourceGroupTree($groupId, Request $request)
    {
        $gid = intval($groupId);
        $treeInfo = ResourceModule::getResourceGroupDepartmentTree($gid);
        return $this->json(0, 'ok', $treeInfo);
    }

    /**
     * @desc 获取资源组的资源列表
     *
     * @param [type] $groupId
     * @param Request $request
     * @return void
     */
    public function listResourceGroupResource($groupId, Request $request)
    {
        $gid = intval($groupId);

        $resourceList = ResourceModule::getResourceGroupResourceList($gid);
        return $this->json(0, 'ok', [
            'resource_list' => $resourceList
        ]);
    }

    /**
     * @desc 更新资源组对应部门
     *
     * @param [type] $groupId
     * @param Request $requset
     * @return void
     */
    public function updateResourceGroupDepartment($groupId, Request $request)
    {
        $gid = intval($groupId);
        if (empty ($gid)) {
            throwWorkError(AccessErrorCode::INVAILD_RESOURCE_GROUP);
        }
        $groupInfo  = CpAccess::getResourceGroupInfo($gid);
        if ($groupInfo['code'] != 0 || empty($groupInfo['data'])) {
            throwWorkError(AccessErrorCode::INVAILD_RESOURCE_GROUP);
        }
        // 获取更新资源
        $departmentIncrease = $request->input('departmentIncrease');
        $departmentReduce = $request->input('departmentReduce');

        if (!empty($departmentIncrease)) {
            CpAccess::addDepartResourceGroup($departmentIncrease, $gid);
        }
        if (!empty($departmentReduce)) {
            CpAccess::removeDepartResourceGroup($departmentReduce, $gid);
        }
    
        return $this->json(0, '更新成功');
    }

    /**
     * @desc 更新资源组对应资源
     *
     * @param [type] $groupId
     * @param Request $requset
     * @return void
     */
    public function updateResourceGroupResource($groupId, Request $request)
    {
        $gid = intval($groupId);
        if (empty ($gid)) {
            throwWorkError(AccessErrorCode::INVAILD_RESOURCE_GROUP_2);
        }
        $groupInfo  = CpAccess::getResourceGroupInfo($gid);
        if ($groupInfo['code'] != 0 || empty($groupInfo['data'])) {
            throwWorkError(AccessErrorCode::INVAILD_RESOURCE_GROUP_2);
        }

        // 获取更新资源
        $resourceIncrease = $request->input('resourceIncrease');
        $resourceReduce = $request->input('resourceReduce');
        if (!empty($resourceIncrease)) {
            foreach ($resourceIncrease as &$item) {
                list($controller, $resource) = explode('@', $item);
                $item = [
                    'controller' => $controller,
                    'resource'     => $resource,
                    'inherit'    => 0,
                    'limit'      => 0,
                ];
            }
            $addRet = CpAccess::addGroupResource($resourceIncrease, $gid);
        }
        if (!empty($resourceReduce)) {
            foreach ($resourceReduce as &$item) {
                list($controller, $resource) = explode('@', $item);
                $item = [
                    'controller' => $controller,
                    'resource'     => $resource,
                    'inherit'    => 0,
                    'limit'      => 0,
                ];
            }
            $reRet  = CpAccess::removeGroupResource($resourceReduce, $gid);
        }

        return $this->json(0, '更新成功');
    }

    /**
     * @desc 获取部门的独立资源
     *
     * @param [type] $groupId
     * @param Request $request
     * @return void
     */
    public function listDepartmentTmpResource($did, Request $request)
    {
        $did = intval($did);
        $project = $request->input('project');
        $resourceList = ResourceModule::getDepartmentTmpResourceList($did, $project);
        return $this->json(0, 'ok', [
            'resource_list' => $resourceList
        ]);
    }

      /**
     * @desc 更新部门的独立资源
     *
     * @param [type] $groupId
     * @param Request $requset
     * @return void
     */
    public function updateDepartmentTmpResource($did, Request $request)
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
        $resourceIncrease = $request->input('resourceIncrease');
        $resourceReduce = $request->input('resourceReduce');
        if (!empty($resourceIncrease)) {
            foreach ($resourceIncrease as &$item) {
                list($controller, $resource) = explode('@', $item);
                $item = [
                    'controller' => $controller,
                    'resource'     => $resource,
                    'inherit'    => 0,
                    'limit'      => 0,
                ];
            }
            $addRet = CpAccess::addDepartResource($resourceIncrease, $did, $project);
        }
        if (!empty($resourceReduce)) {
            foreach ($resourceReduce as &$item) {
                list($controller, $resource) = explode('@', $item);
                $item = [
                    'controller' => $controller,
                    'resource'     => $resource,
                    'inherit'    => 0,
                    'limit'      => 0,
                ];
            }
            $reRet  = CpAccess::removeDepartResource($resourceReduce, $did, $project);
        }

        return $this->json(0, '更新成功');
    }


}
