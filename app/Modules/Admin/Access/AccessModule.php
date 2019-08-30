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
 * CpAccess类
 * 这里为了快速开发直接继承了原有的const类
 * 新模块不要继承const
 */
class AccessModule
{
    public static function modelReturn($code, $msg = '', $data = null)
    {
        return array(
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        );
    }
}
