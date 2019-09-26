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
 * 资源管理模块
 */
class ResourceModule
{
    public static function getResourceGroupDepartmentTree($gid)
    {
        // 取部门信息
        $departList = CpAccess::getDepartByResourceGroupId($gid);
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
     * 获取资源组所选资源列表
     *
     * @param [typeResource $gid
     * @return void
     */
    public static function getResourceGroupResourceList($gid)
    {
        $groupInfo  = CpAccess::getResourceGroupInfo($gid);
        if ($groupInfo['code'] != 0 || empty($groupInfo['data'])) {
            throwWorkError(AccessErrorCode::INVAILD_ACTION_GROUP_3);
        }
        $groupInfo = $groupInfo['data'];
        // 获取所有resource
        // $projectResourceList = CpAccess::getResourceList($groupInfo['project']);
        $projectResourceList = CpAccess::$resourceList;
        $groupResourceList = CpAccess::getResourceByGroupId($gid);
        $groupResourceList = $groupResourceList['data'];
        $groupResourceConList = array_column($groupResourceList, 'con_resource');

        foreach ($projectResourceList as $controllerPath => &$controller) {
            $controller['controller'] = $controllerPath;
            $controller['orderby'] = 0;
            if ($controller['resource']) {
                foreach ($controller['resource'] as $resource => &$resourceDesc) {
                    $conResource = $controllerPath . '-' . $resource;
                    $resourceData = [
                        'controller' => $controllerPath,
                        'resource' => $resource,
                        'desc' => $resourceDesc,
                    ];
                    if (in_array($conResource, $groupResourceConList)) {
                        $resourceData['isChecked'] = 1;
                        $resourceData['originIsChecked'] = 1;
                        $controller['orderby'] = 1;
                    } else {
                        $resourceData['isChecked'] = 0;
                        $resourceData['originIsChecked'] = 0;
                    }
                    $resourceDesc = $resourceData;
                }
                $controller['resource'] = array_values($controller['resource']);
            }
        }
        $projectResourceList = array_values($projectResourceList);
        usort($projectResourceList, function ($a, $b) {
            return $a['orderby'] < $b['orderby'];
        });
        return $projectResourceList;
    }

    public static function getDepartmentTmpResourceList($did, $project)
    {
        $depart = CpAccess::getDepartInfo($did);
        if ($depart['code'] != 0 || empty($depart['data'])) {
            throwWorkError(AccessErrorCode::INVAILD_DEPART);
        }
        $depart = $depart['data'];
        // 获取所有resource
        // $projectResourceList = CpAccess::getResourceList($project);
        $projectResourceList = CpAccess::$resourceList;
        $groupResourceList = CpAccess::getDepartResourceList($did);
        $groupResourceList = array_get($groupResourceList, 'data');
        $groupResourceConList = array_column($groupResourceList, 'con_resource');

        // 计算显示
        foreach ($projectResourceList as $controllerPath => &$controller) {
            $controller['controller'] = $controllerPath;
            $controller['orderby'] = 0;
            if ($controller['resource']) {
                foreach ($controller['resource'] as $resource => &$resourceDesc) {
                    $conResource = $controllerPath . '-' . $resource;
                    $resourceData = [
                        'controller' => $controllerPath,
                        'resource' => $resource,
                        'desc' => $resourceDesc,
                    ];
                    if (in_array($conResource, $groupResourceConList)) {
                        $resourceData['isChecked'] = 1;
                        $resourceData['originIsChecked'] = 1;
                        $controller['orderby'] = 1;
                    } else {
                        $resourceData['isChecked'] = 0;
                        $resourceData['originIsChecked'] = 0;
                    }
                    $resourceDesc = $resourceData;
                }
                $controller['resource'] = array_values($controller['resource']);
            }
        }
        $projectResourceList = array_values($projectResourceList);
        usort($projectResourceList, function ($a, $b) {
            return $a['orderby'] < $b['orderby'];
        });
        return $projectResourceList;
    }
}
