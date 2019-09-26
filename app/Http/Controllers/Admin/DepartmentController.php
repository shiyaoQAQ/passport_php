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
use App\Modules\Admin\Project\ProjectModule;

/**
 * @desc 新组织架构
 */
class DepartmentController extends Controller
{
    /**
     * @desc 组织架构页面
     */
    public function showDepartment()
    {
        return view('cp.department.index');
    }

    public function showDepartmentTree()
    {
        $objDepart = new CpDepartment();
        $treeInfo  = $objDepart->getDepartmentTree(0);
        // 第一个节点默认展开
        $treeInfo[0]['isExpand'] = 1;
        return $this->json(0, 'ok', $treeInfo);
    }

    /**
     * @desc 获取父节点信息
     */
    public function getDepartmentParent($did)
    {
        $did = intval($did);
        $objDepart = new CpDepartment();
        $pInfo = $objDepart->getParentDepart($did);
        return $this->json(0, 'suc', $pInfo);     
    }

    /**
     * @desc 获取部门用户
     */
    public function getDepartmentUser($did)
    {
        $id = intval($did);
        if (empty($id)) {
            return $this->json(1, '部门ID为空');
        }
        $userList = CpDepartmentUser::where('department_id', $id)->where('is_deleted',CpDepartmentUser::NOT_DELETED)->get()->toArray();
        if(!empty($userList)) {
            foreach ($userList as &$user) {
                $user['userName']  = CpUserModule::getName($user['uid']);
                $user['adminName'] = CpUserModule::getName($user['admin_uid']);
                $user['cp'] = CpUserModule::getUserInfo($user['uid'], 'mobile_phone');
            }
            unset($user);
        }
        return $this->json(0, 'ok', $userList);
    }

    /**
     * @desc 获取部门权限信息
     */
    public function getDepartmentAction($did)
    {
        $did = intval($did);
        //所有权限节点
        $allAction = CpAccess::getActionList();
        //权限组，部门配置
        $departGroup = CpAccess::getActionGroupByDid($did);
        $departGroup = array_get($departGroup, 'data') ?: [];
        $groups = [];
        if ($departGroup) {
            $gids = array_column($departGroup, 'group_id');
            //权限组，权限配置
            $groups  = CpAccess::getActionGroupsByIdIn($gids);
            $groups = array_get($groups, 'data') ?: [];
            $groupsMap = array_column($groups, null, 'id');
            if ($groups) {
                $actions = CpAccess::getActionsByGids($gids);
                $actions = $actions['data'];
                $actionList = array();
                foreach ($actions as $oneAction) {
                    $gid = $oneAction['gid'];
                    $controller = $oneAction['controller'];
                    $project = $groupsMap[$gid]['project'];
                    if (!isset($actionList[$gid][$controller])) {
                        $actionList[$gid][$controller] = [
                            'project' => $groupsMap[$gid]['project'],
                            'name' =>  $allAction[$project][$controller]['desc'],
                            'actions' => [],
                        ];
                    }
                    $actionList[$gid][$controller]['actions'][] = [
                        'desc'  => $allAction[$project][$controller]['action'][$oneAction['action']]['desc'],
                        'limit' => $oneAction['data_limit'],
                        'color' => $oneAction['data_limit'] == 1 ? 'info' : 'success',
                    ];
                }
                foreach ($groups as &$oneGroup) {
                    if (isset($actionList[$oneGroup['id']])) {
                        $oneGroup['actions'] = $actionList[$oneGroup['id']];
                    }
                }
            }
        }
        //单个权限
        $depart = CpAccess::getDepartInfo($did);
        $depart = array_get($depart, 'data');
        $tmp = array();
        if ($depart) {
            $actionList = CpAccess::getDeaprtActionList($did);
            $actionList = $actionList['data'];
            foreach ($actionList as $oneAction) {
                $project = $oneAction['project'];
                $controller = $oneAction['controller'];
                if (!isset($tmp[$project][$controller])) {
                    $tmp[$project][$controller] = [
                        'name' => $allAction[$project][$controller]['desc'],
                        'controller' => $controller,
                        'actions' => [],
                    ];
                }
                $tmp[$project][$controller]['actions'][] = array(
                    'desc'  => $allAction[$project][$controller]['action'][$oneAction['action']]['desc'],
                    'limit' => $oneAction['data_limit'],
                    'color' => $oneAction['data_limit'] == 1 ? 'info' : 'success',
                );
            }
            // 组装数据project数据
            $projectMap = ProjectModule::getProjectMap();
            foreach ($projectMap as $project => $projectName) {
                if (isset($tmp[$project])) {
                    $tmp[$project] = [
                        'projectName' => $projectName,
                        'controllerList' => $tmp[$project],
                    ];
                } else {
                    $tmp[$project] = [
                        'projectName' => $projectName,
                        'controllerList' => [],
                    ];
                }
            }
        }
        $data['tmp'] = $tmp;
        $data['depart'] = $depart;
        $data['groups'] = $groups;
        // return view('admin.access.departmentActionList')->with('controller_list',$tmp)
        //                                       ->with('depart_info', $depart)
        //                                       ->with('group_info', isset($groups) ? $groups : array());
        return $this->json(0, 'ok', $data);
    }

    /**
     * 获取部门资源信息
     *
     * @param [type] $did
     * @param Request $request
     * @return void
     */
    public function getDepartmentResource($did)
    {
        $did = intval($did);
        $allResource  = CpAccess::$resourceList;
        $departGroup = CpAccess::getResourceGroupByDid($did);
        if ($departGroup['code'] == 0 && !empty($departGroup['data'])) {
            $departGroup = $departGroup['data'];
            $gids = array();
            foreach ($departGroup as $one) {
                $gids[] = $one['group_id'];
            }
            $groups  = CpAccess::getResourceGroupsByGroupIdIn($gids);
            if ($groups['code'] == 0 && !empty($groups['data'])) {
                $groups  = $groups['data'];
                $actions = CpAccess::getResourcesByGids($gids);
                $actions = $actions['data'];
                $actionList = array();
                foreach ($actions as $oneAction) {
                    $actionList[$oneAction['gid']][$oneAction['controller']]['name']   = $allResource[$oneAction['controller']]['desc'];
                    $actionList[$oneAction['gid']][$oneAction['controller']]['resource'][] = array(
                        'desc'  => $allResource[$oneAction['controller']]['resource'][$oneAction['resource']],
                        'limit' => $oneAction['data_limit'],
                        'color' => $oneAction['data_limit'] == 1 ? 'info' : 'success',
                    );
                }
                foreach ($groups as &$oneGroup) {
                    if (isset($actionList[$oneGroup['id']])) {
                        $oneGroup['resources'] = $actionList[$oneGroup['id']];
                    }
                }
            }
        }

        //单独配置的资源
        $tmp = array();
        $depart = CpAccess::getDepartInfo($did);
        if ($depart['code'] == 0 && !empty($depart['data'])) {
            $depart = $depart['data'];
            $departResour = CpAccess::getDepartResourceList($did);
            $departResour = $departResour['data'];
            foreach ($departResour as $oneResource) {
                $tmp[$oneResource['controller']]['name'] = $allResource[$oneResource['controller']]['desc'];
                $tmp[$oneResource['controller']]['resource'][] = array(
                    'desc'  => $allResource[$oneResource['controller']]['resource'][$oneResource['resource']],
                );
            }
        }
        // echo Template::serve('cp/longrent_department/department_render_resource.html');      
        // return view('admin.access.departmentResourceRender')->with('resource_list',$tmp)
        //                                       ->with('depart_info', $depart)
        //                                       ->with('group_info', isset($groups) ? $groups : array());   
        $data['depart'] = $depart;
        $data['groups'] = isset($groups) ? $groups : array();
        $data['tmp'] = $tmp;
        return $this->json(0,'ok', $data);
    }

}
