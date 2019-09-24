<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Admin\Access\CpUserModule;

/**
 * @desc 新组织架构
 */
class DepartmentController extends Controller
{
    /**
     * @desc 组织架构页面
     */
    public function showDepartment(){
        return view('cp.department.index');
    }
}
