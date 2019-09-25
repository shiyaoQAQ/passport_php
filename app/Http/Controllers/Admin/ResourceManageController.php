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



}
