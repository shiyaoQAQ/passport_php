<?php

namespace App\Http\Controllers\Admin;

// use App\Modules\SalesModule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Admin\Access\OauthManagerModule;
use App\Modules\Admin\Oauth\Constants\OauthConst;
use App\Modules\Admin\Oauth\Constants\OauthErrorCode;
use Validator;

/**
 * @desc Oauth管理类
 */
class OauthManageController extends Controller
{
    /**
     * @desc oauth客户端列表
     */
    public function listClients()
    {
        $assign = [
            'grantTypeList' => OauthConst::$grantTypeMap,
            'nukedList' => OauthConst::$nukedMap,
        ];
        return view('admin.oauth.oauthClientList', $assign);
    }

    /**
     * @desc oauth客户端列表数据
     */
    public function listJsonClients(Request $request)
    {
        $filter = $request->all();
        $list = OauthManagerModule::getClientList($filter);
        return $this->json(0, 'ok', $list);
    }

    /**
     * @desc 创建oauth客户端
     */
    public function storeClients(Request $request)
    {
        // 数据校验
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'redirect' => 'required',
            'grant_type' => 'required',
        ], [
            'name.required' => '客户端名称不能为空',
            'redirect.required' => '请填写客户端回调地址',
            'grant_type.required' => '请选择授权类型',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            throwWorkError($errors->first(), OauthErrorCode::ARGUMENT_INVALID);
        }
        $data = [
            'name' => htmlspecialchars($request->input('name')),
            'redirect' => htmlspecialchars($request->input('redirect')),
            'grant_type' => intval($request->input('grant_type')),
            'is_nuked' => intval($request->input('is_nuked')),
        ];
        if ($id = $request->input('id')) {
            OauthManagerModule::updateClient($id, $data);
        } else {
            OauthManagerModule::storeClient($data);
        }
        return $this->json(0, 'ok');
    }

}
