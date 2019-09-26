<?php

namespace App\Modules\Admin\Access;

use App\Modules\Admin\Access\Models\CpDepartment;
use App\Modules\Admin\Access\Models\CpDepartmentUser;
use App\Modules\Admin\Access\Models\CpDepartmentAction;
use App\Modules\Admin\Access\Models\CpActionGroup;
use App\Modules\Admin\Access\Models\CpActionGroupAccess;
use App\Modules\Admin\Access\Models\CpResourceGroup;
use App\Modules\Admin\Access\Models\CpResourceGroupAccess;
use App\Modules\Admin\Access\Models\CpDepartmentResource;
use App\Modules\User\UserBase\Models\EcsUser;
use App\Modules\Admin\Access\Models\CpUser;
use \Session;
use \YC_Util;
// use App\Modules\Ka\KaModule;
use App\Exceptions\WorkException;
use App\Modules\Admin\Access\Constants\AccessConst;
use App\Modules\Admin\Access\Constants\AccessErrorCode;
use App\Modules\Base\Store\StoreModule;
use App\Modules\Base\City\CityModule;
use App\Modules\Admin\Access\Models\Sales\DmSellerOriginationChart;

/**
 * 权限管理模块
 */
class ActionModule
{
    public static function getActionGroupDetail($gid)
    {
        $group = CpActionGroup::find($gid);
        if (empty($group)) {
            throwWorkError(AccessErrorCode::INVAILD_ACTION_GROUP_4);
        }
        return $group;
    }

    public static function getActionGroupDepartmentTree($gid)
    {
        // 取部门信息
        $departList = CpAccess::getDepartByGroupId($gid);
        $departList = array_get($departList, 'data') ?: [];
        $departmentIds = array_column($departList, 'department_id');
        // 生成部门树
        $objDepart = new CpDepartment();
        $treeInfo  = $objDepart->getDepartmentTree(0);
        // 遍历部门树 将在节点里的是否选择 数据置为1
        self::operateTree($treeInfo, function (&$node) use ($departmentIds) {
            if (in_array($node['id'], $departmentIds)) {
                $node['isChecked'] = 1;
                $node['originIsChecked'] = 1;
            } else {
                $node['isChecked'] = 0;
                $node['originIsChecked'] = 0;
            }
        });

        return $treeInfo;
    }

    /**
     * 遍历树 执行callback
     *
     * @param [type] $treeInfo
     * @param [type] $callback
     * @return void
     */
    public static function operateTree(array &$nodeList, $callback)
    {
        foreach ($nodeList as &$node) {
            // 对当前节点执行callback
            $callback($node);
            if (array_get($node, 'child')) {
                self::operateTree($node['child'], $callback);
            }
        }
    }

    /**
     * 获取权限组所选权限列表
     *
     * @param [type] $gid
     * @return void
     */
    public static function getActionGroupActionList($gid)
    {
        $groupInfo  = CpAccess::getActionGroupInfo($gid);
        if ($groupInfo['code'] != 0 || empty($groupInfo['data'])) {
            throwWorkError(AccessErrorCode::INVAILD_ACTION_GROUP_3);
        }
        $groupInfo = $groupInfo['data'];
        // 获取所有action
        $projectActionList = CpAccess::getActionList($groupInfo['project']);
        $groupActionList = CpAccess::getActionByGroupId($gid);
        $groupActionList = $groupActionList['data'];
        $groupActionConList = array_column($groupActionList, 'con_action');

        foreach ($projectActionList as $controllerPath => &$controller) {
            $controller['controller'] = $controllerPath;
            $controller['orderby'] = 0;
            if ($controller['action']) {
                foreach ($controller['action'] as &$action) {
                    $conAction = $action['controller'] . '-' . $action['action'];
                    if (in_array($conAction, $groupActionConList)) {
                        $action['isChecked'] = 1;
                        $action['originIsChecked'] = 1;
                        $controller['orderby'] = 1;
                    } else {
                        $action['isChecked'] = 0;
                        $action['originIsChecked'] = 0;
                    }
                }
                $controller['action'] = array_values($controller['action']);
            }
        }
        $projectActionList = array_values($projectActionList);
        usort($projectActionList, function ($a, $b) {
            return $a['orderby'] < $b['orderby'];
        });
        return $projectActionList;
    }

    public static function getDepartmentTmpActionList($did, $project)
    {
        $depart = CpAccess::getDepartInfo($did);
        if ($depart['code'] != 0 || empty($depart['data'])) {
            throwWorkError(AccessErrorCode::INVAILD_DEPART);
        }
        $depart = $depart['data'];
        // 获取所有action
        $projectActionList = CpAccess::getActionList($project);
        $groupActionList = CpAccess::getDeaprtActionList($did, $project);
        $groupActionList = array_get($groupActionList, 'data');
        $groupActionConList = array_column($groupActionList, 'con_action');

        // 计算显示
        foreach ($projectActionList as $controllerPath => &$controller) {
            $controller['controller'] = $controllerPath;
            $controller['orderby'] = 0;
            if ($controller['action']) {
                foreach ($controller['action'] as &$action) {
                    $conAction = $action['controller'] . '-' . $action['action'];
                    if (in_array($conAction, $groupActionConList)) {
                        $action['isChecked'] = 1;
                        $action['originIsChecked'] = 1;
                        $controller['orderby'] = 1;
                    } else {
                        $action['isChecked'] = 0;
                        $action['originIsChecked'] = 0;
                    }
                }
                $controller['action'] = array_values($controller['action']);
            }
        }
        $projectActionList = array_values($projectActionList);
        usort($projectActionList, function ($a, $b) {
            return $a['orderby'] < $b['orderby'];
        });
        return $projectActionList;
    }
}
