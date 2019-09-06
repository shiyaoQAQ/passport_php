<?php

namespace App\Http\Controllers\Rpc;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Admin\Access\CpUserModule;
use App\Modules\User\UserBase\Constants\UserConst;
use App\Modules\User\UserBase\UserModule;
use Pascal\Core\Rpc\Component\RpcControllerTrait;

/**
 * @desc User信息相关Rpc
 */
class UserRpc extends Controller
{
    use RpcControllerTrait;

    /**
     * @desc 获取用户基础信息
     *
     * @param Request $request
     * @return string rpc结果
     */
    public function getUserInfo(Request $request)
    {
        $uid = $request->input('user_id');
        $argument = [
            'select' => UserConst::$userColumn,
        ];
        $result = UserModule::getUserInfo($uid, $argument);
        return $this->rpcReturn(0, 'ok', $result);
    }

    /**
     * @desc 获取用户基础信息列表
     *
     * @param Request $request
     * @return string rpc结果
     */
    public function getUserInfoByIds(Request $request)
    {
        $uids = $request->input('user_ids');
        $argument = [
            'select' => UserConst::$userColumn,
        ];
        $result = UserModule::getUserInfoByIds($uids, $argument);
        return $this->rpcReturn(0, 'ok', $result);
    }

    /**
     * @desc 通过用户uids获取id name的kv映射
     *
     * @param Request $request
     * @return string rpc结果
     */
    public function getUserNameMap(Request $request)
    {
        $uids = $request->input('user_ids');
        $result = UserModule::getUserNameMap($uids);
        return $this->rpcReturn(0, 'ok', $result);
    }

    /**
     * @desc 通过用户uids获取id mobile的kv映射
     *
     * @param Request $request
     * @return string rpc结果
     */
    public function getUserMobileMap(Request $request)
    {
        $uids = $request->input('user_ids');
        $result = UserModule::getUserMobileMap($uids);
        return $this->rpcReturn(0, 'ok', $result);
    }

    /**
     * @desc 获取用户等级详情
     *
     * @param Request $request
     * @return string rpc结果
     */
    public function getUserRankDetail(Request $request)
    {
        $uid = $request->input('user_id');
        $result = UserModule::getUserRankDetail($uid);
        return $this->rpcReturn(0, 'ok', $result);
    }

    /**
     * @desc 通过手机号获取基础用户
     *
     * @param Request $request
     * @return string rpc结果
     */
    public function getUserByMobile(Request $request)
    {
        $mobile = $request->input('mobile');
        $argument = [
            'select' => UserConst::$userColumn,
        ];
        $result = UserModule::getUserByMobile($mobile, $argument);
        return $this->rpcReturn(0, 'ok', $result);
    }

    /**
     * @desc 通过mobile获取或创建用户
     *
     * @param Request $request
     * @return string rpc结果
     */
    public function createUser(Request $request)
    {
        // $data = $request->input('data');
        $data = $request->only([
            'user_name',
            'mobile',
            'city_id',
            'refer',
            'user_rank',
        ]);
        $result = UserModule::createUser($data);
        return $this->rpcReturn(0, 'ok', $result);
    }


}
