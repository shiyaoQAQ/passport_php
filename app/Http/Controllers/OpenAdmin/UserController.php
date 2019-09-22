<?php

namespace App\Http\Controllers\OpenAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Admin\Access\CpUserModule;
use App\Modules\Admin\Access\OpenAdminModule;

/**
 * @desc OAuth开放平台Admin用户信息
 */
class UserController extends Controller
{
    /**
     * @desc 获取cpuser信息
     */
    public function getCpUserInfo(Request $request)
    {
        $userInfo = OpenAdminModule::getCpUserInfo();
        return $this->json(0, 'ok', $userInfo);
    }
}
