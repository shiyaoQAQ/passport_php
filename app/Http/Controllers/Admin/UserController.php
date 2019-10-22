<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Admin\Access\CpUserModule;

/**
 * @desc 用户控制系统
 */
class UserController extends Controller
{
    /**
     * @desc 添加用户页面
     */
    public function addUser()
    {
        $cpRoleList = CpUserModule::getCpRoleList();
        return view('admin.access/addUser')->with('cp_role_list', $cpRoleList);
    }

    /**
     * @desc 确认添加用户
     */
    public function doAddUser(Request $request)
    {
        $mobile = $request->input('mobile');
        $name   = $request->input('name');
        $email  = $request->input('email') ?? '';
        $role   = $request->input('role');
        $password = $request->input('password'); 
        $mobile = '1'.substr($mobile, 1);
        if (empty($mobile) || empty($name) || empty($role) || empty($password)) {
            return $this->returnAjax(1, '请完整填写信息');
        }
        if(\YC_Util::checkMobile($mobile) == false){
            return $this->returnAjax(1, '请正确填写手机号');
        }

        $user = array(
            'mobile' => $mobile,
            'name'   => $name,
            'email'  => $email,
            'role'   => $role,
            'password' => $password,
        );
        $ret = CpUserModule::addUser($user);
        return $this->returnAjax($ret['code'], $ret['msg'], $ret['data']);
    }

    /**
     * @desc 查找用户
     */
    public function search(Request $request)
    {
        $mobile = $request->input('mobile');
        $mobile = '1'.substr($mobile, 1);
        $user = CpUserModule::getCpUserByHp($mobile);
        return $this->returnAjax($user['code'], $user['msg'], $user['data']);
    }

    /**
     * @desc 进入修改密码页面
     */
    public function password(){
        return view('admin.access.password');
    }

    /**
     * @desc 确认修改密码
     */
    public function doPassword(Request $request){
        $oldPass = trim($request->input('old_pass'));
        $newPass = trim($request->input('new_pass'));
        $conPass = trim($request->input('con_pass'));
        if($newPass != $conPass){
            return $this->returnAjax(1, '两次新密码输入不一样');
        }
        if(strlen($newPass) <= 8){
            return $this->returnAjax(1, '新密码长度不能小于8位');
        }
        $ret = CpUserModule::changePassword($oldPass, $newPass);
        return $this->returnAjax($ret['code'], $ret['msg']);
    }

    /**
     * @desc 离职用户
     */
    public function dimission(Request $request){
        $mobile = $request->input("mobile");
        $mobile = '1'.substr($mobile, 1);
        $ret = CpUserModule::dimissionUser($mobile);
        return $this->returnAjax($ret['code'], $ret['msg'], $ret['data']);
    }

    protected function returnAjax($code, $msg = '', $data = null){
        $data = array(
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        );
        return $data;
    }
   /**
     * @desc 新增管理员-页面
     */
    public function addDepartmentUser(){
        $cpRoleList = CpUserModule::getCpRoleList();
        return view('admin.access.addDepartmentUser')->with('cp_role_list', $cpRoleList);
    }

    /**
     * @desc 新增管理员-请求
     */
    public function addDepartmentUserJson(Request $request){
        try{
            CpuserModule::addCpuser($request);
            return $this->returnAjax(0, '添加成功');
        }catch (\Exception $e) {
            return $this->returnAjax(intval($e->getCode()) ?: 1000000, $e->getMessage());
        }
    }
}
