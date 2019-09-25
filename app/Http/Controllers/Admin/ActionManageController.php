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


}
