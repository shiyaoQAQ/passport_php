<?php

namespace App\Http\Controllers\Admin;

// use App\Modules\SalesModule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Admin\Access\CpAccess;
use App\Modules\Admin\Access\CpUserModule;
use App\Modules\Admin\Access\OauthModule;
use App\Modules\Admin\Oauth\Constants\OauthConst;
use App\Modules\Admin\Oauth\Constants\OauthErrorCode;
use Validator;

/**
 * @desc Oauth鉴权类
 */
class OauthController extends Controller
{
    /**
     * @desc oauth登录页面显示
     */
    public function oauthShow(Request $request)
    {
        // 数据校验
        $validator = Validator::make($request->all(), [
            'client_id' => 'required',
            'redirect_uri' => 'required',
            'response_type' => 'required',
        ], [
            'client_id.required' => '无效的客户端',
            'redirect_uri.required' => '无效的回调域名',
            'response_type.required' => '无效的授权类型',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            throwError($errors->first(), OauthErrorCode::OAUTH_ARGUMENT_INVAILD);
        }
        $clientId = $request->input('client_id');
        $redirectUri = $request->input('redirect_uri');
        $responseType = $request->input('response_type');
        $state = $request->input('state');
        // token级的权限校验 todo
        $scope = $request->input('scope');
        // 校验客户端状态
        OauthModule::checkClient($clientId, $redirectUri);
        $client = OauthModule::getClientInfo($clientId);
        switch ($responseType) {
            case 'code':
                // 目前只支持一种
                break;
            default:
                throwError(OauthErrorCode::INVALID_RESPONSE_TYPE);
                break;
        }
        // 显示页面 待用户确认
        // 如果是内部项目 直接生成code重定向回原有地址
        if ($client->is_trusted) {
            $userId = CpAccess::theUid();
            $code = OauthModule::getAuthorizationCode($clientId, $userId, $scope);
            return redirect($redirectUri . '?' . http_build_query([
                'code' => $code,
            ]));
        }
        // 临时判断 如果没设置企业邮箱 则报错
        CpUserModule::checkMailInZsfucai(CpAccess::theUid());

        $assign = [
            'clientName' => $client->name,
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => $responseType,
            'scope' => $scope,
            'state' => $state,
        ];
        return view('admin.oauth.oauth', $assign);
    }

    /**
     * @desc oauth登录表单提交
     */
    public function oauthAuthorization(Request $request)
    {
        // 数据校验
        $validator = Validator::make($request->all(), [
            'client_id' => 'required',
            'redirect_uri' => 'required',
            'response_type' => 'required',
        ], [
            'client_id.required' => '无效的客户端',
            'redirect_uri.required' => '无效的回调域名',
            'response_type.required' => '无效的授权类型',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            throwError($errors->first(), OauthErrorCode::OAUTH_ARGUMENT_INVAILD);
        }
        $clientId = $request->input('client_id');
        $redirectUri = $request->input('redirect_uri');
        $responseType = $request->input('response_type');
        // token级的权限校验 todo
        $scope = $request->input('scope') ?: '';
        $state = $request->input('state') ?: '';
        // 校验客户端状态
        OauthModule::checkClient($clientId, $redirectUri);
        $client = OauthModule::getClientInfo($clientId);
        // 获取code
        $userId = CpAccess::theUid();
        $code = OauthModule::getAuthorizationCode($clientId, $userId, $scope);
        // 判断原来域名中是否有?
        // $redirectUri = 'http://jkstest2.youcai123.cn/jenkins/securityRealm/finishLogin';
        $realUri = $redirectUri;
        if (strpos($redirectUri, '?') !== false) {
            // 获取参数
            $parseUri = parse_url($redirectUri);
            if (empty(array_get($parseUri, 'query'))) {
                // 这里是判断 http://jkstest2.youcai123.cn/jenkins/securityRealm/finishLogin? 以?结尾的异常case
                $realUri .= '';
            } else {
                $realUri .= '&';
            }
        } else {
            $realUri .= '?';
        }
        $odata = [
            'code' => $code,  
        ];
        if ($state) {
            $odate['state'] = $state;
        }
        $realUri .= http_build_query($odata);
        return redirect($realUri);
    }

    /**
     * @desc 获取oauthToken
     */
    public function getOauthToken(Request $request)
    {
        $grantType = $request->input('grant_type');
        if (empty($grantType)) {
            throwError(OauthErrorCode::GRANT_TYPE_ERROR);
        }
        switch ($grantType) {
            case 'authorization_code':
                $code = $request->input('code');
                $clientId = $request->input('client_id');
                // 同时取签名和秘钥两种校验方式 签名更安全 秘钥更简单
                $signature = $request->input('signature');
                $clientSecret = $request->input('client_secret');
                $body = $request->all();
                unset($body['signature']);
                if (empty($code) || empty($clientId) || (empty($signature) && empty($clientSecret))) {
                    throwError(OauthErrorCode::TOKEN_ARGUMENT_ERROR);
                }
                if ($clientSecret) {
                    $result = OauthModule::getTokenByAuthorizationCodeAndSecret($clientId, $code, $clientSecret, $body);
                } else {
                    $result = OauthModule::getTokenByAuthorizationCodeAndSignature($clientId, $code, $signature, $body);
                }
                break;
            case 'test':
                // 密码获取和手机号获取待开发
                break;
            default:
                throwError(OauthErrorCode::GRANT_TYPE_ERROR);
                break;
        }
        
        return $result;
    }

}
