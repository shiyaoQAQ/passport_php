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
use App\Modules\Base\Store\StoreModule;
use App\Modules\Base\City\CityModule;
use App\Modules\Admin\Access\Models\Sales\DmSellerOriginationChart;

/**
 * 权限管理模块
 */
class ActionModule
{
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

}
