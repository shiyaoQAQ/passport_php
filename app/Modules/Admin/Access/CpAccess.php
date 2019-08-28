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
class CpAccess extends \App\Modules\Admin\Access\Constants\AccessConst
{
    public static function getCreateUserType($uid){
        if(empty($uid)){
            return '';
        }
        if(in_array($uid, array(10, 11))){
            return 'kfxd';
        }
        return 'xsxd';
    }

    private static function travleDir($path)
    {
        $dir = app_path($path);
        // dd($dir);
        $list = array();
        if (!$dh = opendir($dir)) {
            return $list;
        }
        while($file = readdir($dh))
        {
            if ('.' == $file || '..' == $file) {
                continue;
            }
            $class = 'App\\' . str_replace('/', '\\', $path)  . str_replace('.php', '', $file);
            if (false !== strpos($class, '.')) {
                continue;
            }
            $list[$class] = $dir.'/'.$file;
        }
        closedir($dh);
        return $list;
    }

    public static function getOwnAction()
    {
        $ctl = self::travleDir('Http/Controllers/Admin/');
        $breakAction = array('__construct', 'middleware', 'getMiddleware', 'callAction', '__call', 'authorize', 'authorizeForUser', 'authorizeResource', 
            'dispatchNow', 'validateWith', 'validate', 'validateWithBag', 'login', 'returnAjax', 'json', 'returnMsg');
        $list = array();
        foreach ($ctl as $k => $v) {
            $reflection = new \ReflectionClass($k);
            $cDesc = self::_getDescByDocComment($reflection);
            $list[$k]['desc'] = $cDesc;
            $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
            foreach ($methods as $method) {
                $mName = $method->name;
                if (in_array($mName,$breakAction)) continue;
                $mDesc = self::_getDescByDocComment($method);
                if (empty($mDesc)) {
                    $mDesc = $mName;
                }
                $list[$k]['action'][$mName] = array('action'=>$mName,'desc'=>$mDesc,'controller'=>$k);
            }
        }
        return $list;
    }

    public static function getActionList($project = null)
    {
        $zsfucai = app('redis')->get(AccessConst::REDIS_ZSFUCAI_ACCESS_LIST);
        $zsfucai = json_decode($zsfucai, true);
        $shanhujia = app('redis')->get(AccessConst::REDIS_SHANHUJIA_ACCESS_LIST);
        $shanhujia = json_decode($shanhujia, true);
        $result = [
            'zsfucai' => $zsfucai,
            'shanhujia' => $shanhujia,
            'passport' => self::getOwnAction(),
        ];
        if (is_null($project)) {
            // 分模块返回
            return $result;
        } else {
            return array_get($result, $project ?: 0) ?: [];
        }
    }

    private static function _getDescByDocComment($obj)
    {
        $doc  = $obj->getDocComment();
        $mdoc = preg_match("/@desc.+/", $doc, $m);
        if (empty($m)) {
            return '';
        }
        $desc = $m[0];
        $desc = explode(' ', $desc);
        if (empty($desc)) {
            return '';
        }
        $mDesc = $desc[1];
        return $mDesc;
    }

    /**
     * 获取部门树关系结构
     */
    public static function getDepartTreeData($did)
    {
        $objDepart = new CpDepartment();
        $treeInfo  = $objDepart->getDepartmentTree($did);
        return $treeInfo;
    }

    /**
     * 获取所有的部门信息
     */
    public static function getAllDepart()
    {
        $objDepart = new CpDepartment();
    }


    /**
     * 获取部门所有权限组
     */
    public static function getActionGroupByDid($did)
    {
        $dDG = new CpDepartmentAction();
        $ret = $dDG->getGroupByDid($did);
        return self::modelReturn(0, '', $ret);
    }
    
    /**
     * 获取部门信息
     */
    public static function getDepartInfo($did)
    {
        if (empty($did)) {
            return self::modelReturn(800013,'部门ID为空');
        }
        $dDepart = new CpDepartment();
        $ret = $dDepart->getById($did);
        return self::modelReturn(0, '获取成功',$ret);
    }

    /**
     * 获取部门单个权限详情
     */
    public static function getDeaprtActionList($did)
    {
        $dDG = new CpDepartmentAction(); 
        $ret = $dDG->getDeaprtActionList($did);
        return self::modelReturn(0, '', $ret);
    }

    public static function addDepartAction($datas, $did, $project)
    {
        if (empty($datas)) {
            return self::modelReturn(800030, 'action列表为空');
        }
        $dDG = new CpDepartmentAction();
        $ret = $dDG->addActions($datas, $did, $project);
        return $ret ? self::modelReturn(0, '移除成功') : self::modelReturn(800031,'移除失败');
    }

    public static function removeDepartAction($datas, $did, $project)
    {
        if (empty($datas)) {
            return self::modelReturn(800030, 'action列表为空');
        }
        $dDG = new CpDepartmentAction();
        $ret = $dDG->removeActions($datas, $did, $project);
        return $ret ? self::modelReturn(0, '添加成功') : self::modelReturn(800032,'添加失败');
    }

    /**
     * 获取权限组详情
     */
    public static function getActionGroupInfo($gid)
    {
        $ret = CpActionGroup::find($gid);
        if (empty($ret)) {
            return self::modelReturn(1,'权限组不存在');
        }
        $ret = $ret->toArray();
        return self::modelReturn(0, '', $ret);
    }

    /**
     * 获取权限组权限详情
     */
    public static function getActionByGroupId($gid)
    {
        $dGA = new CpActionGroupAccess();
        $ret = $dGA->getActionByGroupId($gid);
        return self::modelReturn(0, '', $ret);
    }

    /**
     * 通过权限组ID获取该权限组所绑定的所有部门
     */
    public static function getDepartByGroupId($gid)
    {
        $dDG = new CpDepartmentAction(); 
        $ret = $dDG->getDepartByGroupId($gid);
        return self::modelReturn(0, '', $ret);
    }

    /**
     * 移除部门的权限组
     */
    public static function removeDepartActionGroup($dids, $gid)
    {
        if (empty($dids)) {
            return self::modelReturn(800030, 'action列表为空');
        }
        $dDG = new CpDepartmentAction();
        $ret = $dDG->removeGroups($dids, $gid);
        return $ret ? self::modelReturn(0, '移除成功') : self::modelReturn(800031, '移除失败');
    }

    /**
     * 添加部门的权限组
     */
    public static function addDepartActionGroup($dids, $gid)
    {
        if (empty($dids)) {
            returnself::modelReturn(800030, 'action列表为空');
        }
        $dDG = new CpDepartmentAction();
        $groupInfo = array_get(CpAccess::getActionGroupInfo($gid), 'data');
        $ret = $dDG->addGroups($dids, $gid, $groupInfo['project']);
        return $ret ? self::modelReturn(0, '添加成功') : self::modelReturn(800032, '添加失败');
    }

    /**
     * 移除权限组的权限
     */
    public static function removeActionGroupAccess($actionList, $gid)
    {
        if (empty($actionList)) {
            return self::modelReturn(800025,'action列表为空');
        }
        $dGA = new CpActionGroupAccess();
        $ret = $dGA->removeActions($actionList, $gid);
        return $ret ? self::modelReturn(0, '移除成功') : self::modelReturn(800026, '移除失败');
    }

    /**
     * 添加权限组权限
     */
    public static function addActionGroupAccess($actions, $gid)
    {
        if (empty($actions)) {
            return self::modelReturn(800025,'action列表为空');
        }
        $dGA = new CpActionGroupAccess();
        $ret = $dGA->addActions($actions, $gid);
        return $ret ? self::modelReturn(0, '添加成功') : self::modelReturn(800027, '添加失败');
    }

    /**
     * 通过权限组ID批量获取权限组
     */
    public static function getActionGroupsByIdIn($gids)
    {
        $dAG = new CpActionGroup();
        $ret = $dAG->getByIds($gids);
        return self::modelReturn(0, '', $ret);
    }

    /**
     * 通过权限组ID批量获取权限
     * @param  [type] $gids [description]
     * @return [type]       [description]
     */
    public static function getActionsByGids($gids)
    {
        $dGA = new CpActionGroupAccess();
        $ret = $dGA->getActionsByGids($gids);
        return self::modelReturn(0, '', $ret);
    }

    /**
     * 添加部门
     */
    public static function addDepartment($name, $mark, $cityId, $parentId = 0, $email)
    {
        if (empty($name)) {
            return self::modelReturn(800007,'部门名称不能为空');
        }
        $uid     = 1;//todo
        $dDepart = new CpDepartment();
        $ret     = $dDepart->add($name, $mark, $cityId, $parentId, $uid, $email);
        return $ret ? self::modelReturn(0, '添加成功') : self::modelReturn(800008,'添加失败');
    }

    /**
     * 添加部门用户
     */
    public static function addDepartUser($did, $accout) {
        $userInfo = EcsUser::where('mobile_phone', $accout)->first();
        if (empty($userInfo)) {
            return self::modelReturn(1, '用户不存在');
        }
        $userInfo = $userInfo->toArray(); 
        $userId = $userInfo['user_id'];
        $objDepartUser = new CpDepartmentUser();
        $exsitInfo = $objDepartUser->get($did, $userId);
        if (!empty($exsitInfo)) {
            return self::modelReturn(1, '用户已经存在当前部门，请勿重复添加');
        }
        $ret = $objDepartUser->add($did, $userId, self::theUid());
        return $ret ? self::modelReturn(0, '添加成功') : self::modelReturn(800008,'添加失败');
    }

    /**
     * 获取权限组列表
     */
    public static function getActionGroupList()
    {
        $dAG = new CpActionGroup();
        $ret = $dAG->getList();
        return self::modelReturn(0, '', $ret);
    }

    /**
     * 添加权限组
     */
    public static function addActionGroup($data)
    {
        if (empty($data['name'])) {
            return self::modelReturn(800028,'名称不能为空');
        }
        if (empty($data['project'])) {
            return self::modelReturn(800028,'请选择所属项目');
        }
        $dAG = new CpActionGroup();
        $ret = $dAG->add($data);
        return $ret ? self::modelReturn(0, '添加成功') : self::modelReturn(800029,'添加失败');
    }

    /**
     * 更新权限组
     */
    public static function updateActionGroup($id, $data)
    {
        if (empty($id)) {
            return self::modelReturn(800029,'ID不能为空');
        }
        if (empty($data['name'])) {
            return self::modelReturn(800028,'名称不能为空');
        }
        if (empty($data['project'])) {
            return self::modelReturn(800028,'请选择所属项目');
        }
        $dAG = new CpActionGroup();
        $ret = $dAG->updateGroup($id, $data);
        return $ret ? self::modelReturn(0, '更新成功') : self::modelReturn(800029,'更新失败');
    }

    /**
     * 更新部门信息
     */
    public static function updateDepartment($id, $data)
    {
        if (empty($data)) {
            return self::modelReturn(800010,'城市更新数据为空');
        }
        $data['operator_id'] = 1;
        $ischild = self::checkIschildDepartment($id,$data['parent_id']);
        if ($data['parent_id']!==0 && $ischild['data']===true) {
            return self::modelReturn(800010,'不能配置为当前节点的子节点！');
        }
        if ($id == $data['parent_id']) {
            return self::modelReturn(800010,'不能配置为当前节点的自身！');
        }
        $dDepart = new CpDepartment();
        $ret     = $dDepart->updateDepart($id, $data);
        return $ret ? self::modelReturn(0, '更新成功') : self::modelReturn(800011,'更新失败');
    }

    //判断后一个是不是，前一个节点的子节点
    public static function checkIschildDepartment($id, $cid)
    {
        if ($cid==0) {
            return self::modelReturn(800036, '不是其子节点', false);
        }
        $dDepart  = new CpDepartment();
        $rets     = $dDepart->getAllChildNode($id);
        if (empty($rets)) {
            return self::modelReturn(800036, '不是其子节点', false);
        }
        $tmp = array();
        foreach ($rets as $ret) {
            $tmp[$ret['id']] = $ret['id'];
        }
        if (in_array($cid,$tmp)) {
            return self::modelReturn(0, '是其子节点', true);
        }
        return self::modelReturn(800036, '不是其子节点', false);
    }

    public static function getSaleGroupWithName()
    {
        $groupUserList = self::getSaleGroupUsers();
        $dids = array_keys($groupUserList);
        $dDepart = new CpDepartment();
        $departList = $dDepart->getByIds($dids);
        $departList = \YC_Util::resetArrIndex($departList, 'id');
        foreach ($groupUserList as $did => &$userList) {
            $userList = [
                'depart_name' => $departList[$did]['name'] ?? '未知',
                'user_list'   => $userList,
            ];
        }
        unset($userList);
        return $groupUserList;
    }

    /**
    * 获取某城市下所有销售，返回结构：array(array(第一组销售ids),array(第二组销售ids)...),每组销售主管在前
    * @param unknown $cityCode
    * @return multitype:unknown string |Ambigous <multitype:multitype: , NULL, unknown>
    */
    public static function getSaleGroupUsers()
    {
        $dDepart = new CpDepartment();
        $departInfo = $dDepart->getDeaprtByMark(self::MARK_SALE_LEADER);
        if (empty($departInfo)) {
            return self::modelReturn(800024, '部门信息不存在');
        }
        $departIds = self::getDepartByResource('cityList', CityModule::getCurrentCity());
        $dids = array();
        $groupDidList = [];
        foreach ($departInfo as $departDetail) {
            if(!in_array($departDetail['id'], $departIds['data'])) {
                continue;
            }
            $dids[] = $departDetail['id'];
            $chidlDepart = $dDepart->getChildDepart($departDetail['id']);
            $chidlDepartIds = \YC_Util::filterArrayInfo($chidlDepart, 'id');
            $groupDidList[$departDetail['id']] = $chidlDepartIds;
            $groupDidList[$departDetail['id']][] = $departDetail['id'];
        }
        $dUserDep = new CpDepartmentUser();
        $groupUserList = [];
        foreach ($groupDidList as $groupLeaderId => $groupIds) {
            $userList = $dUserDep->getUserByDidIn($groupIds);
            if (empty($userList)) {
                continue;
            }
            foreach ($userList as $oneDepartUser) {
                $groupUserList[$groupLeaderId][$oneDepartUser['uid']] = CpUserModule::getName($oneDepartUser['uid']);
            }
        }
        return $groupUserList;
        die;

        $userList = $dUserDep->getUserByDidIn($dids);
        if (empty($userList)) {
            return self::modelReturn(800025,'部门用户信息不存在');
        }
        $salesList = array();
        $salesListLeader = array();
        foreach ($userList as $oneDepartUser) {
            $salesList[$oneDepartUser['department_id']][$oneDepartUser['uid']] = CpUserModule::getName($oneDepartUser['uid']);
            $salesListLeader[$oneDepartUser['department_id']] = $oneDepartUser['uid'];
        }
        foreach ($salesListLeader as $k => $userLeader) {
            $users = self::getUserChildUser($userLeader, array(self::MARK_SALE));
            if ($users['code'] == 0 && !empty($users['data'])) {
                $uids = array_keys($users['data']);
                foreach ($uids as $uid) {
                        $sellers[$uid] = CpUserModule::getName($uid);
                }
                $salesList[$k] = $salesList[$k] + $sellers;
                unset($sellers);
            }
        }
        return $salesList;
    }

    /**
    * 通过标识获取用户
    */
    public static function getUserByMark(array $marks)
    {
        $dDepart = new CpDepartment();
        $departInfo = $dDepart->getDeaprtByMark($marks);
        if (empty($departInfo)) {
            return self::modelReturn(1, '部门信息不存在', array());
        }
        $dids = \YC_Util::filterArrayInfo($departInfo, 'id');
        $dUserDep = new CpDepartmentUser();
        $userList = $dUserDep->getUserByDidIn($dids);
        if (empty($userList)) {
            return self::modelReturn(1, '部门用户不存在', array());
        }
        $retList = array();
        // $cityList = self::getAccessDetail(self::ACCESS_KEY_CITY);
        $uidList = [];
        foreach ($userList as $user) {
            $uidList[] = $user['uid'];
        }
        $retList = CpUserModule::getBatchName($uidList);
        // foreach ($userList as $user) {
        //     $retList[$user['uid']] = CpUserModule::getName($user['uid']);
        // }
        return self::modelReturn(0, 'suc', $retList);
    }

    /**
     * 获取用户的下级用户
     */
    public static function getUserChildUser($uid, $mark = false) {
        $chidlDepart = self::getUserChildDepart($uid, $mark);
        if ($chidlDepart['code'] != 0) {
            return $chidlDepart;
        }
        $dids = $chidlDepart['data'];
        $dUserDep = new CpDepartmentUser();
        $userList = $dUserDep->getUserByDidIn($dids);
        if (empty($userList)) {
            return self::modelReturn(0, '', array());
        }
        $userIds = array();
        foreach ($userList as $userDetail) {
            $userIds[$userDetail['uid']] = $userDetail['uid'];
        }
        return self::modelReturn(0, '',$userIds);
    }

    /**
     * 通过资源获取用户
     */
    public static function getUserByResource($controller, $resource) {
        $departIds = self::getDepartByResource($controller, $resource);
        if ($departIds['code'] != 0) {
            return $departIds;
        }
        $departIds = $departIds['data'];
        $dUserDep = new CpDepartmentUser();
        $userInfo = $dUserDep->getUserByDidIn($departIds);
        $uids = \YC_Util::extractList($userInfo, 'uid');
        $uids = array_unique($uids);
        return self::modelReturn(0, '', $uids);
    }

    public static function getDepartByResource($controller, $resource)
    {
        if (empty($controller) || empty($resource)) {
            return self::modelReturn(1,'入参不能为空');
        }
        $ddR     = new CpDepartmentResource();
        //获取单独资源配置
        $oneDepart = $ddR->getByResource($controller, $resource);
        $departIds = array();
        if (!empty($oneDepart)) {
            $departIds = \YC_Util::extractList($oneDepart, 'department_id');
        }
        //获取资源组配置
        $dResourceGroupAcc = new CpResourceGroupAccess();
        $resourceGroupList = $dResourceGroupAcc->getGidByResource($controller, $resource);
        if (!empty($resourceGroupList)) {
            $resourceGroupIds = \YC_Util::extractList($resourceGroupList, 'gid');
            $groupDepart = $ddR->getDepartByGroupIdIn($resourceGroupIds);
            if (!empty($groupDepart)) {
                $groupDepartIds = \YC_Util::extractList($groupDepart, 'department_id');
                $departIds = array_merge($departIds, $groupDepartIds);
            }
        }
        if (empty($departIds)) {
            return self::modelReturn(0, '', array());
        }
        return self::modelReturn(0, '', $departIds);
    }

    public static function getUserChildDepart($uid, $mark = false)
    {
        if (empty($uid)) {
            return self::modelReturn(800019,'用户ID为空');
        }
        $dUserDep = new CpDepartmentUser();
        $dDepart  = new CpDepartment();
        $userDepartInfo = $dUserDep->getUserDepartByUid($uid);
        if (empty($userDepartInfo)) {
            return self::modelReturn(0, '',array()); 
        }
        $allChild = array();
        foreach ($userDepartInfo as $userDepartDetail) {
            $childInfo = $dDepart->getAllChildNode($userDepartDetail['department_id']);    
            $allChild  = array_merge($allChild, $childInfo);
        }
        $allChildId = array();
        foreach ($allChild as $childNode) {
            if ( !$mark || in_array($childNode['mark'], $mark) ) {
                $allChildId[$childNode['id']] = $childNode['id'];
            }
        }
        return self::modelReturn(0, '', $allChildId);
    }    

    /**
     * 检验来自己ECT的检验
     * @return [type] [description]
     */
    public static function fromEctCheck($token, $time) {
        $ret = \YC_Util::checkLaravelToken($token); 
        if ($ret == false) {
            throw new WorkException('验证失败', 1000001);
        }
        if ($time < time()-300 || $time > time()+300) {
            throw new WorkException('验证失败', 1000002);
        }
        $objEcsUser = new EcsUser();
        $userInfo = $objEcsUser->getById($ret);
        if (empty($userInfo)) {
            throw new WorkException('用户不存在', 1000001);
        }
        $cpUser = CpUser::where('uid', $userInfo['user_id'])->first();
        if (empty($cpUser)) {
            throw new WorkException('新系统用户不存在', 1000001);
        }
        $md5Str = substr(base64_decode($token), 12);
        $checkStr = $userInfo['user_name'] . $cpUser->mobile . $userInfo['user_id'] . \YC_Util::$laravelTokenKey . $time;
        if ($md5Str != md5($checkStr)) {
            throw new WorkException('验证失败', 1000003);
        }
        \Session::put('user_id', $userInfo['user_id']);
        \Session::put('user_name', $userInfo['user_name']);
        return self::modelReturn(0, '验证成功');
    }

    public static function theUid()
    {
        $pdaUserId = \Session::get('sort_uid') ?? 0;
        $userId = \Session::get('user_id') ?? 0;
        if(!empty($pdaUserId)){
            return $pdaUserId;
        }
        return $userId;
    }

    public static function theName()
    {
        $userId = \Session::get('user_name') ?? 0;
        return $userId;
    }

    /**
     * 检验用户是否合法
     */
    public static function auth() {
        $userId = \Session::get('user_id');

        if (empty($userId)) {
            return self::modelReturn(1,'验证失败');
        }
        \View::share('cp_base_user_name', \Session::get('user_name'));
        return self::modelReturn(0, 'suc');
    }

    /**
     * 检查用户是否有修改返利支付状态的权限
     */
    public static function hasRebatePayStatusAccess()
    {
        if (self::theUid() != self::REBATE_PAY_STATUS_USER) {
            return false;
        }
        return true;
    }

    /**
     * 检查用户是否有企业可读的权限
     */
    public static function hasCompCusomterAccess($companyId)
    {
        if (empty($companyId)) {
            return true;
        }

        $uid = self::theUid();
        if ($uid == self::REBATE_PAY_STATUS_USER) {
            return true;
        }
        $accessId = self::$compSalerMap[$companyId];
        if ($uid != $accessId) {
            //获取当前销售所属的子用户
            // $salerList = self::getUserChildUser($uid, CpAccess::$saleMark);
            // $uids = [];
            // if ($salerList['code'] == 0 && !empty($salerList['data'])) {
            //     $uids = array_keys($salerList['data']);
            // } 

            // if (! in_array($accessId, $uids)) {
            //     return false;
            // } else {
            //     return true;                
            // }
            return false;
        }
        return true;
    }


    /**
     * 检查用户是否有访问权限
     */
    public static function checkAccess() {
        $route = \Route::currentRouteAction();
        if (empty($route)) {
            return self::modelReturn(1, '路由不存在');
        }
        list($class, $action) = explode('@', $route);
        $actionList = self::getAccess(self::theUid());
        if ($actionList['code'] != 0 || empty($actionList['data'])) {
            return self::modelReturn(2, '没有权限');
        }
        $actionList = $actionList['data'];
        if(!isset($actionList['all']['all']) && !isset($actionList[$class][$action])){
            return self::modelReturn(3, '没有权限');
        }
        self::initMenu($actionList);
        $ret = self::initAccessPath($class);  
        if($ret['code'] != 0){
            return $ret;
        }
        self::checkAccessPath($class);
        return self::modelReturn(0, 'suc');
    }

    public static function initMenu($actionList)
    {

        $allMenuList = config('menu.cp_menu'); 
        $actionMd5 = md5(json_encode($actionList) . json_encode($allMenuList));
        $actionMd5Key  = "action_list_md5_key";
        $actionMenuKey = 'action_list_menu_key';
        $sessinActionMd5 = Session::get($actionMd5Key);
        $sessionMenuList = Session::get($actionMenuKey);
        //如果缓存里有，且用户的权限摘要没有变，就取缓存里的菜单
        if (!empty($sessinActionMd5) && $sessinActionMd5 == $actionMd5 && !empty($sessionMenuList)) {
            $actionMenu = json_decode($sessionMenuList, true);
        } else {
            if (isset($actionList['all']['all'])) {
                $actionMenu = $allMenuList;
            } else {
                $actionMenu = [];
                $routeList = app()->routes->getRoutes();
                $routeMap = [];
                foreach ($routeList as $route){
                    if ($route->methods[0] == 'POST') {
                        continue;
                    }
                    $routeMap['/' . $route->uri] = $route->getActionName();
                }
                unset($route);
                foreach ($allMenuList as $firMenuNmae => $menuDetail) {
                    foreach ($menuDetail['menu_list'] as $path => $secMenuName) {
                        if(is_array($secMenuName)){
                            $thirdList = [];
                            foreach ($secMenuName as $itemPath => $itemName ) {
                                $route = $routeMap[$itemPath];
                                if ($route == 'Closure') {
                                    continue;
                                }
                                list($class, $action) = explode('@', $route);
                                if (isset($actionList[$class][$action])) {
                                    $thirdList[$itemPath] = $itemName;
                                }
                            }
                            if(!empty($thirdList)){
                                $actionMenu[$firMenuNmae]['logo'] = $menuDetail['logo'];
                                $actionMenu[$firMenuNmae]['menu_list'][$path] = $thirdList;
                            }
                        }else{
                            if (!isset($routeMap[$path])) {
                                continue;
                            }
                            $route = $routeMap[$path];
                            if ($route == 'Closure') {
                                continue;
                            }
                            list($class, $action) = explode('@', $route);
                            if (isset($actionList[$class][$action])) {
                                $actionMenu[$firMenuNmae]['logo'] = $menuDetail['logo'];
                                $actionMenu[$firMenuNmae]['menu_list'][$path] = $secMenuName;
                            }
                        }
                    }

                }                
            }
            Session::put($actionMd5Key, $actionMd5); 
            Session::put($actionMenuKey, json_encode($actionMenu));
        }
        \View::share('show_access_menu_list', $actionMenu);
    }

    //初始化全局控制权限相关(目前仓库使用)
    public static function initAccessPath($class)
    {
        $allAccessPath = self::$allAccessPath;
        $showAccessList = [];
        $theUid = self::theUid();
        $userResourceList = self::getUserResouceList($theUid);
        $userResourceList = $userResourceList['data'] ?? [];
        foreach ($allAccessPath as $accessKey => $accessPath) {
            if(!in_array($class, $accessPath['controllers'])){
                continue;
            }
            $showAccessList[$accessKey]['desc'] = $accessPath['desc'];
            $resourceList = self::$resourceList[$accessPath['resource']]['resource'];
            if(empty($resourceList)){
                continue;
            }
            foreach ($resourceList as $rKey => $rDesc) {
                if(isset($userResourceList[$accessPath['resource']][$rKey])){
                    $showAccessList[$accessKey]['options'][$rKey] = $rDesc;
                }
            }
            //如果都为空，没有权限
            if(empty($showAccessList[$accessKey]['options'])){
                return self::modelReturn(4, '没有权限'); 
            }
            $accessList = array_keys($showAccessList[$accessKey]['options']);
            \Session::put('access_path_all_' . $accessKey, json_encode($accessList));
            $showAccessList[$accessKey]['options']['all'] = '全部';
            $showAccessList[$accessKey]['options'] = array_reverse($showAccessList[$accessKey]['options'], true);
            $showAccessList[$accessKey]['parent_access'] = $accessPath['parent_access'] ?? '';
            //取当前选项
            $nowChoose = self::getAccessVal($accessKey, $showAccessList[$accessKey]['options']);
            //权限更新后重置已选项
            if($nowChoose != self::ACCESS_VAL_ALL && !in_array($nowChoose, $accessList)){
                self::selectAccess($accessKey, self::ACCESS_VAL_ALL);
                $nowChoose = self::ACCESS_VAL_ALL;
            }
            $showAccessList[$accessKey]['choose'] = $nowChoose;
        }
        foreach ($showAccessList as $accessKey => &$access) {
            if (!empty($access['parent_access'])) {
                $choose = count(self::getAccessDetail($access['parent_access'])) == 1 ? self::getAccessDetail($access['parent_access'])[0] : 'all';
                $access['options'] = self::initParentAccessPath($access['parent_access'], $choose, $access['options']);
                $access['options']['all'] = '全部';
                \Session::put('access_path_all_' . $accessKey, json_encode(array_keys($access['options'])));
            }
        }
        unset($access);
        //有城市与仓库两个筛选条件的页面，根据城市显示相应城市的仓库
        // if (!empty($showAccessList['city']) && $showAccessList['city']['choose'] != self::ACCESS_VAL_ALL) {
        //     $accessInfo = [];
        //     $storeName = [];
        //     $storeInfo = array_get(StoreModule::$cityStoreList, $showAccessList['city']['choose'] ?: 0, []);
        //     dd($storeInfo);
        //     foreach ($storeInfo as $stores) {
        //         $storeAccess[] = $stores['access'];
        //     }
        //     $storeAccess['all'] = self::ACCESS_VAL_ALL;
        //     if (!empty($showAccessList['express']['options'])) {
        //             foreach ($showAccessList['express']['options'] as $expressk => $store) {
        //             if (in_array($expressk, $storeAccess)) {
        //                 $accessInfo[$expressk] = $store;
        //             }
        //         }
        //         $showAccessList['express']['options'] = $accessInfo;
        //     }
        //     //权限更新后重置已选项
        //     if (!empty($showAccessList['express']) && $showAccessList['express']['choose'] != self::ACCESS_VAL_ALL) {
        //         $currentChoose = self::getAccessVal('express', $showAccessList['express']['options']);
        //         if ($currentChoose != self::ACCESS_VAL_ALL && !in_array($currentChoose, array_keys($showAccessList['express']['options']))) {
        //             self::selectAccess('express', self::ACCESS_VAL_ALL);
        //             $currentChoose = self::ACCESS_VAL_ALL;
        //         }
        //         $showAccessList['express']['choose'] = $currentChoose;
        //     }
        // }
        \View::share('show_access_list', $showAccessList);
        return self::modelReturn(0, 'ok');
    }

    public static function initParentAccessPath($access, $choose, $accessList)
    {
        $allAccessPath = self::$allAccessPath;
        $showAccessList = [];
        $userResourceList = self::getUserResouceList(self::theUid());
        $userResourceList = $userResourceList['data'] ?? [];
        if (!isset($allAccessPath[$access])) {
            return $accessList;
        }
        if (!isset(self::$accessRelaConf[$access])) {
            return $accessList;
        }
        $conf = self::$accessRelaConf[$access];
        $havAccessList = [];
        if ($choose == 'all') {
            foreach ($conf as $acc) {
                $havAccessList = array_merge($havAccessList, $acc ?? []);
            }
        } else {
            $havAccessList = $conf[$choose] ?? [];
        }

        foreach ($accessList as $k => $access) {
            if (!in_array($k, $havAccessList)) {
                unset($accessList[$k]);
            }
        }
        return $accessList;
    }

    //检查是否有当前资源权限
    public static function checkAccessPath($class)
    {
        $keyCodeMap = [
            self::ACCESS_KEY_EXPRESS => StoreModule::$resourceToStore,
        ];
        //灰度测试
        // if (!in_array(self::theUid(), [11111])) {
        //     return true;
        // }
        $request = \App::make('request');
        $params  = array_merge($request->route()->parameters() ?? [] , $request->all() ?? []);
        $callbackData = [];
        foreach (self::$allAccessPath as $accessKey => $oneAccess) {
            if(!isset($oneAccess['rules'][$class])){
                continue;
            }
            $accessConf = $oneAccess['rules'][$class];
            if (!isset($accessConf['check_key']) || empty($accessConf['check_key'])) {
                continue;
            }
            foreach ($params as $paramKey => $paramVal) {
                if (is_array($paramVal)) {
                    continue;
                }
                $paramKey = strtolower($paramKey);
                if (!in_array($paramKey, $accessConf['check_key'])) {
                    continue;
                }
                //不重复调用第三方接口
                $callbackMd5 = md5($accessConf['callback']['class'] . $accessConf['callback']['function'] . $paramVal);
                if (isset($callbackData[$callbackMd5])) {
                    $backData = $callbackData[$callbackMd5];
                } else {
                    $backData = call_user_func(array($accessConf['callback']['class'], $accessConf['callback']['function']), $paramVal); 
                    $callbackData[$callbackMd5] = $backData;
                }
                if (!isset($backData[$accessConf['callback']['data_key']]) || empty($backData[$accessConf['callback']['data_key']])) {
                    continue;
                }
                // $accessDetailList = self::getAccessDetail($accessKey);
                $accessDetailList = self::getAccessDetail($accessKey, $keyCodeMap[$accessKey] ?? []);
                if (!in_array($backData[$accessConf['callback']['data_key']], $accessDetailList)) {
                    $errorMsg = sprintf("没有当前%s权限，请切换%s或联系管理员开通权限", $oneAccess['desc'], $oneAccess['desc']);
                    throw new WorkException($errorMsg, 20000);
                }
            } 
        }
        unset($callbackData);
        return true;
    }

    //切换全局权限
    public static function selectAccess($accessKey, $accessVal)
    {
        $uid = self::theUid();
        if(!isset(self::$allAccessPath[$accessKey])){
            return self::modelReturn(1, '全局参数不存在');
        }
        $accessPath = self::$allAccessPath[$accessKey];
        $resourceList = self::$resourceList[$accessPath['resource']]['resource'];
        if(!isset($resourceList[$accessVal]) && $accessVal != 'all'){
            return self::modelReturn(2, '全局参数不存在');
        }
        \Session::put('access_path_' . $accessKey, $accessVal);
        return self::modelReturn(0, $accessPath['desc'] . '切换成功');
    }

    public static function getAccessVal($key, $accessList = []){
        $nowChoose = \Session::get('access_path_' . $key);
        if(empty($nowChoose)){
            $nowChoose = 'all';
            if ($key == self::ACCESS_KEY_CITY && in_array(CityModule::ADCODE_BJ, array_keys($accessList))) {
                $nowChoose = CityModule::ADCODE_BJ;
                self::selectAccess($key, $nowChoose);
            }
        }
        return $nowChoose;
    }

    //把全部权限转换成具体的权限
    public static function getAccessDetail($key, $mapCode = [])
    {
        $accessVal = self::getAccessVal($key);
        if($accessVal == self::ACCESS_VAL_ALL){
            $allAccess = \Session::get('access_path_all_' . $key);
            $accessList = json_decode($allAccess, true);
        }else{
            $accessList = [$accessVal];
        }
        if(empty($mapCode)){
            return $accessList;
        }
        $mapList = [];
        foreach ($accessList as $acc) {
            $mapList[] = isset($mapCode[$acc]) ? $mapCode[$acc] : $acc;
        }
        return $mapList;
    }

    // 获取资源列表
    public static function getAccessList($key, $mapCode = []) 
    {
        $allAccess = \Session::get('access_path_all_' . $key);
        $accessList = json_decode($allAccess, true);
        // 对应映射
        if(empty($mapCode)){
            return $accessList;
        }
        $mapList = [];
        foreach ($accessList as $acc) {
            $mapList[] = isset($mapCode[$acc]) ? $mapCode[$acc] : $acc;
        }
        return $mapList;
    }

    //在菜单上提供
    // public static $allAccessPath1 = array(
    //     'express' => array(
    //         'desc'     => '仓库',
    //         'resource' => 'expressStore',
    //         'controllers' => array(
    //             'App\Http\Controllers\Cp\Express',
    //             'App\Http\Controllers\Cp\ExpressBase',
    //             'App\Http\Controllers\Cp',
    //         ),
    //     ),
    // );    

    // //判断全局权限
    // public static function hasKeyAccess()
    // {
    //     $accessConfigs = self::$allAccessPath;
    // }

    public static function hasAccess($uid, $class, $action) {
        $actionList = self::getAccess($uid); 
        if ($actionList['code'] != 0 || empty($actionList['data'])) {
            return self::modelReturn(1, '没有权限');
        }
        $actionList = $actionList['data'];
        return (isset($actionList['all']['all']) || isset($actionList[$class][$action])) ? 
                 self::modelReturn(0, 'suc') : self::modelReturn(1, '没有权限');        
    } 

    public static function hasResource($uid, $controller, $resource) {
        $resourceList = self::getUserResouceList($uid);
        if ($resourceList['code'] != 0) {
            return false;
        }
        $resourceList = $resourceList['data'];
        return isset($resourceList[$controller][$resource]) ? 
                 self::modelReturn(0, 'suc') : self::modelReturn(1, '没有权限');
    }    

    public static function getUserResouceList($uid) {
       //获取用户部门
        $dUserDep = new CpDepartmentUser();
        $userDepartInfo = $dUserDep->getUserDepartByUid($uid); 
        if (empty($userDepartInfo)) {
            return self::modelReturn(0, '', array());
        }
        $dids = \YC_Util::extractList($userDepartInfo, 'department_id');
        $dDR = new CpDepartmentResource();
        //获取单独的资源列表
        $resourceList = $dDR->getResourceByDids($dids);
        $ret = array();
        foreach ($resourceList as $oneResource) {
            $ret[$oneResource['controller']][$oneResource['resource']] =  self::$resourceList[$oneResource['controller']]['resource'][$oneResource['resource']];
        }
        //查询资源组
        $dDRG = new CpDepartmentResource();
        $rgroups = $dDRG->getResourceGroupsByDids($dids);
        $groups = array();
        if (!empty($rgroups)) {
            foreach ($rgroups as $rgroup) {
                $groups[$rgroup['group_id']] = $rgroup['group_id'];
            }
            //查询组资源
            $dRG = new CpResourceGroupAccess();
            $resourcegroups = $dRG->getResourcesByGids($groups);
            foreach ($resourcegroups as $one) {
                $ret[$one['controller']][$one['resource']] =  self::$resourceList[$one['controller']]['resource'][$one['resource']];
            }
        }
        return self::modelReturn(0, '', $ret);
    }    

    /**
     * 获取访问权限
     */
    public static function getAccess($uid) {
        //获取用户部门
        $dUserDep = new CpDepartmentUser();
        $userDepartInfo = $dUserDep->getUserDepartByUid($uid); 
        if (empty($userDepartInfo)) {
            return self::modelReturn(0, '', array());
        }
        $dids = \YC_Util::extractList($userDepartInfo, 'department_id');
        //获取部门权限关系表
        $dDG = new CpDepartmentAction();
        $departActionList = $dDG->getByDids($dids);
        if(empty($departActionList)) {
            return self::modelReturn(0, '', array());
        }
        //开始处理权限
        $groupIds = array();
        $actionList = array();
        foreach ($departActionList as $key => $actionDetail) {
            if($actionDetail['action_type'] == CpDepartmentAction::TYPE_GROUP) {
                $groupIds[] = $actionDetail['group_id'];
            }else{
                $actionList[$actionDetail['controller']][$actionDetail['action']] = $actionDetail;
                unset($departActionList[$key]);
            }
        }
        //获取权限组里的权限
        $dGA = new CpActionGroupAccess();
        $groupActionList = $dGA->getActionsByGids($groupIds);
        foreach ($departActionList as $actionDetail) {
            foreach ($groupActionList as $one) {
                if($one['gid'] == $actionDetail['group_id']) {
                    $tmp = array(
                            'department_id' => $actionDetail['department_id'],
                            'controller'    => $one['controller'], 
                            'action'        => $one['action'],
                            'data_limit'    => $one['data_limit'],
                        );
                    $actionList[$tmp['controller']][$tmp['action']] = $tmp;
                }
            }
        }
        return self::modelReturn(0, '',$actionList);
    }    

    public static function delDepartUser($uid, $did) {
        if (empty($did)) {
            return self::modelReturn(800013,'部门ID为空');
        }
        if (empty($uid)) {
            return self::modelReturn(800014,'用户ID为空');
        }
        $department = self::getDepartInfo($did);
        if ($department['code'] != 0) {
            return $department;
        }
        $department = $department['data'];
        //删除销售
        if (!empty($department['mark']) && in_array($department['mark'], self::$saleWithDissionMark)) {
            $userDepartList = self::getUserDepartByUid($uid); 
            if (sizeof($userDepartList) <= 1) {
                if (SalesModule::hasCustomer($uid)) {
                    return self::modelReturn(800031,'此销售私海有客户，如果删除账号请记得先重新分配其私海客户');
                }
                // 先干掉 后面加回来
                // $kaCompany = KaModule::hasKaCompay($uid);
                $kaCompany = null;
                if (!empty($kaCompany)) {
                    return self::modelReturn(800032,'此销售有KA客户['. $kaCompany->company_name .']，如果删除账号请记得先重新分配其KA客户');
                }
            }
        }
        $admiUid = self::theUid();
        $dUserDep = new CpDepartmentUser();
        $ret = $dUserDep->del($did, $uid, $admiUid);
        return $ret ? self::modelReturn(0, '删除成功') : self::modelReturn(800015,'用户删除失败');
    }
    public static function delActionGroup($id) {
        if (empty($id)) {
            return self::modelReturn(800028,'ID不能为空');
        }
        $dAG = new CpActionGroup();
        $ret = $dAG->del($id);
        //删除权限组的部门配置
        $dDG = new CpDepartmentAction();
        $redids = $dDG->getDepartByGroupId($id);
        $dids = array();
        if (!empty($redids) && is_array($redids)) {
            foreach ($redids as $item) {
                $dids[$item['department_id']] = $item['department_id'];
            }
            $dDG->removeGroups($dids, $id);
        }
        //删除权限组的资源配置
        $dGA = new CpActionGroupAccess();
        $dGA->del($id);
        return $ret ? self::modelReturn(0, '删除成功') : self::modelReturn(800029,'删除失败');        
    }

    //通过部门获取其资源组
    public static function getResourceGroupByDid($did) {
        $dCpDeaprtRes = new CpDepartmentResource();
        $ret = $dCpDeaprtRes->getGroupByDid($did);
        return self::modelReturn(0, '', $ret);
    }

    //通过资源组组id查询资源组信息
    public static function getResourceGroupsByGroupIdIn($gids) {
        $dCpDeaprtRes = new CpResourceGroup();
        $ret = $dCpDeaprtRes->getByIds($gids);
        return self::modelReturn(0, '', $ret);
    }

    //通过资源组组id查询资源组对应的详细资源信息
    public static function getResourcesByGids($gids) {
        $dGA = new CpResourceGroupAccess();
        $ret = $dGA->getResourcesByGids($gids);
        return self::modelReturn(0, '', $ret);
    }    

    //获取部门单独资源详情
    public static function getDepartResourceList($did) {
        $dDR = new CpDepartmentResource(); 
        $ret = $dDR->getDeaprtResourceList($did);
        return self::modelReturn(0, '', $ret);
    }    

    //资源组列表
    public static function getResourceGroupList() {
        $dAG = new CpResourceGroup();
        $ret = $dAG->getList();
        return self::modelReturn(0, '', $ret);
    }    

    //添加资源组
    public static function addResourceGroup($name, $desc) {
        if (empty($name)) {
            return self::modelReturn(800028,'名称不能为空');
        }
        $dAG = new CpResourceGroup();
        $ret = $dAG->add($name, $desc);
        return $ret ? self::modelReturn(0, '添加成功') : self::modelReturn(800029,'添加失败');
    }
    //更新资源组
    public static function updateResourceGroup($id, $name, $desc) {
        if (empty($id)) {
            return self::modelReturn(800029,'ID不能为空');
        }
        if (empty($name)) {
            return self::modelReturn(800028,'名称不能为空');
        }
        $dAG = new CpResourceGroup();
        $ret = $dAG->updateGroup($id, $name, $desc);
        return $ret ? self::modelReturn(0, '更新成功') : self::modelReturn(800029,'更新失败');
    }
    //删除资源组
    public static function delResourceGroup($id) {
        if (empty($id)) {
            return self::modelReturn(800028,'ID不能为空');
        }
        $dAG = new CpResourceGroup();
        $ret = $dAG->del($id);

        //删除资源组的部门配置
        $dDG = new CpDepartmentResource();
        $redids = $dDG->getDepartByGroupId($id);
        $dids = array();
        if (!empty($redids) && is_array($redids)) {
            foreach ($redids as $item) {
                $dids[$item['department_id']] = $item['department_id'];
            }
            $dDG->removeGroups($dids, $id);
        }
        //删除资源组的资源配置
        $dGA = new CpResourceGroupAccess();
        $dGA->del($id);
        return $ret ? self::modelReturn(0, '删除成功') : self::modelReturn(800029,'删除失败');
    }

    //查询资源组信息
    public static function getResourceGroupInfo($gid) {
        $dAG = new CpResourceGroup();
        $ret = $dAG->get($gid);
        return self::modelReturn(0, '', $ret);
    }

    //查询资源组资源详情
    public static function getResourceByGroupId($gid) {
        $dGA = new CpResourceGroupAccess();
        $ret = $dGA->getResourceByGroupId($gid);
        return self::modelReturn(0, '', $ret);
    }

    //通过资源组组ID查询部门
    public static function getDepartByResourceGroupId($gid) {
        $dDG = new CpDepartmentResource();
        $ret = $dDG->getDepartByGroupId($gid);
        return self::modelReturn(0, '', $ret);
    }

    //删除部门组
    public static function removeDepartResourceGroup($dids, $gid) {
        if (empty($dids)) {
            return self::modelReturn(800030,'部门不能为空');
        }
        $dDG = new CpDepartmentResource();
        $ret = $dDG->removeGroups($dids, $gid);
        return $ret ? self::modelReturn(0, '移除成功') : self::modelReturn(800031,'移除失败');
    }
    //添加部门组
    public static function addDepartResourceGroup($dids, $gid) {
        if (empty($dids)) {
            return self::modelReturn(800030,'部门不能为空');
        }
        $dDG = new CpDepartmentResource();
        $ret = $dDG->addGroups($dids, $gid);
        return $ret ? self::modelReturn(0, '添加成功') : self::modelReturn(800032,'添加失败');
    }

    public static function removeGroupResource($actionList, $gid) {
        if (empty($actionList)) {
            return self::modelReturn(800025,'资源列表为空');
        }
        $dGA = new CpResourceGroupAccess();
        $ret = $dGA->removeResources($actionList, $gid);
        return $ret ? self::modelReturn(0, '移除成功') : self::modelReturn(800026,'移除失败');
    }

    public static function addGroupResource($actions, $gid) {
        if (empty($actions)) {
            return self::modelReturn(800025,'资源列表为空');
        }
        $dGA = new CpResourceGroupAccess();
        $ret = $dGA->addResources($actions, $gid);
        return $ret ? self::modelReturn(0, '添加成功') : self::modelReturn(800027,'添加失败');
    }

    public static function addDepartResource($datas, $did) {
        if (empty($datas)) {
            return self::modelReturn(800033,'资源列表为空');
        }
        $dDR = new CpDepartmentResource(); 
        $ret = $dDR->addResources($datas, $did);
        return $ret ? self::modelReturn(0, '移除成功') : self::modelReturn(800034,'移除失败');
    }

    public static function removeDepartResource($datas, $did) {
        if (empty($datas)) {
            return self::modelReturn(800033,'资源列表为空');
        }
        $dDR = new CpDepartmentResource(); 
        $ret = $dDR->removeResources($datas, $did);
        return $ret ? self::modelReturn(0, '添加成功') : self::modelReturn(800035,'添加失败');
    }

    public static function getDepartByMark($mark)
    {
        $dDepart = new CpDepartment();
        $departInfo = $dDepart->getDeaprtByMark($mark);
        return $departInfo;
    }

    public static function delDepart($id)
    {
        $hasUser = CpDepartmentUser::where('department_id', $id)->where('is_deleted', 0)->first();
        if (!empty($hasUser)) {
            throw new WorkException("当前节点还有用户，不能删除，请先移除节点用户", 800038);
        }
        //判断是否还有子部门
        $hasChild = CpDepartment::where('is_deleted', 0)->where('parent_id', $id)->first();
        if (!empty($hasChild)) {
            throw new WorkException("当前节点还有子节点，不能删除，请先移除子节点", 800036);
        }
        $ret = CpDepartment::where('id', $id)->update(['is_deleted' => 1]);
        if (!$ret) {
            throw new WorkException("删除失败，请重试", 800037);
        }
        return true;
    }

    public static function getUserDepartByUid($uid)
    {
        $dUserDep = new CpDepartmentUser();
        $userDepartInfo = $dUserDep->getUserDepartByUid($uid);
        return $userDepartInfo;
    }

    // 根据标识增加部门人员
    public static function addDepartUserByMark($mobile, $mark)
    {
        $account = '8' . substr($mobile, 1);
        // 获取第一个部门
        $departs = self::getDepartByMark($mark);
        if (empty($departs)) {
            return self::modelReturn(800024, '部门信息不存在');
        }
        $departId = array_get($departs, '0.id', 0);
        if (empty($departId)) {
            return self::modelReturn(800024, '部门信息不存在');
        }
        return self::addDepartUser($departId, $account);
    }

    public static function modelReturn($code, $msg = '', $data = null) {
        return array(
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        );
    }
    /**
     * 根据标识获取部门
     * @param $mark
     * @return array
     */
    public static function getDepartmentByMark($mark)
    {
        $department = new  CpDepartment();
        return $department->getDeaprtByMark($mark);
    }

    /**
     * 根据部门获取用户
     * @param $departmentId
     * @return array
     */
    public static function getUserByDepartment($departmentId)
    {

        $groupUserList=[];
        $dUserDep = new CpDepartmentUser();
        $userList = $dUserDep->getUserByDidIn($departmentId);
        if(!empty($userList)){
            foreach ($userList as $oneDepartUser) {
                $groupUserList[$oneDepartUser['uid']] = CpUserModule::getName($oneDepartUser['uid']);
            }
        }
        return $groupUserList;
    }

    /**
     * 根据资源获取部门Id列表
     * @param $resource 资源名称
     * @return array
     */
    public static function getDepartmentIdByResource($resource)
    {
        return CpDepartmentResource::where('resource',$resource)->pluck('department_id')->toArray();
    }

    /**
     * 根据标识和资源组获取部门
     * @param $mark    标识
     * @param $resource    资源名称
     * @return array
     */
    public static function getDepartmentUserByResource($mark, $resource)
    {
        $department = new  CpDepartment();
        $departmentList = $department->getDeaprtByMark($mark);
        $departmentIdList= self::getDepartmentIdByResource($resource);
        $res = [];
        foreach ($departmentList as $departmentItem) {
            if( in_array($departmentItem['id'],$departmentIdList)){
                $res[]=[
                    "id"=>$departmentItem['id'],
                    "name"=>$departmentItem['name']
                ];
            }
        }
        return $res;
    }

    public static function getResourceByDids($dids)
    {
        $dDR = new CpDepartmentResource();
        //获取单独的资源列表
        $resourceList = $dDR->getResourceByDids($dids);
        $result = [];
        foreach ($resourceList as $resource) {
            $result[$resource['department_id']][] = $resource;
        }
        return $result;
    }

    /**
     * 获取一段时间的两层销售部门分组数据 ，只会还原销售，不会还原部门
     *
     * @return array[group => 组织架构, saleTimeGroup => 销售每个时段属于哪个组]
     * @param $startTime "传入开始时间，Y-H-d H:i:s类型"
     * @param $endTime "传入结束时间，Y-H-d H:i:s类型"
     */
    public static function getStackSaleGroupForTime($startTime, $endTime) : array{
        $saleTimeGroup = [];
        $saleGroupNow = self::getStackSaleGroup();
        $saleGroup = DmSellerOriginationChart::whereBetween('date', [$startTime, $endTime])->get();
        $saleGroup = $saleGroup->groupBy('group_id');
        foreach($saleGroup as $key => $sale){
            $saleList = $sale->groupBy('seller_id');
            foreach($saleList as $sellerId => $saleItem){
                $dateList = $saleItem->pluck('date');
                $saleList->put($sellerId, $dateList);
                foreach($dateList as $date){
                    $saleTimeGroup[$sellerId][$date]= $key;
                }
            }
            $saleGroup->put($key, $saleList);
        }
        $uNNameUserList = [];
        foreach($saleGroupNow as $departmentKey => &$department){
            foreach($department['children'] as $groupKey => &$group){
                $groupId = $group['value'];
                $saleList = $saleGroup->get($groupId);
                $saleListNow = &$group['user_list'];
                if($saleList == NUll){
                    //月初第一天，组织架构脚本跑以前
                    foreach($saleListNow as $saleId => $saleName){
                        $date = date('Y-m-1');
                        $name = $saleListNow[$saleId];
                        $saleListNow[$saleId] =[
                            'name' => $name,
                            'date' => [$date],
                        ];
                        $saleTimeGroup[$saleId][$date]= $groupId;
                    }
                }else{
                    foreach($saleList as $saleId => $saleDate){
                        if(isset($saleListNow[$saleId])){
                            $name = $saleListNow[$saleId];
                            $saleListNow[$saleId] =[
                                'name' => $name,
                                'date' => $saleDate->toArray(),
                            ];
                        }else{
                            $saleListNow[$saleId]=[
                                'name' => '缺失',
                                'date' => $saleDate->toArray(),
                            ];
                            //缺失姓名的账户可能没有删除，将地址缓存，在外面再查一遍
                            $uNNameUserList[$saleId][] = &$saleListNow[$saleId];
                        }
                    }
                }
                //再过滤遍，格式化新添加未缓存销售
                foreach($saleListNow as $saleId => $saleName){
                    $date = date('Y-m-d');
                    $name = $saleListNow[$saleId];
                    if(!is_array($name)){
                        $saleListNow[$saleId] =[
                            'name' => $name,
                            'date' => [$date],
                        ];
                    }
                    $saleTimeGroup[$saleId][$date]= $groupId;
                }
                unset($saleListNow);
            }
            unset($group);
        }
        unset($department);
        $userNameArray = EcsUser::whereIn('user_id', array_keys($uNNameUserList))->pluck('user_name', 'user_id')->toArray();
        foreach($uNNameUserList as $userId => $pointList){
            $name = $userNameArray[$userId];
            foreach($pointList as &$point){
                $point['name'] = $name;
            }
            unset($point);
        }
        return [
                'group' => $saleGroupNow,
                'saleTimeGroup' => $saleTimeGroup
               ];
    }
    /**
     * 获取两层的销售部门分组数据
     *
     * @return void
     */
    public static function getStackSaleGroup()
    {
        $groups = self::getSaleGroupWithName();
        foreach ($groups as $key => &$group) {
            $group['value'] = $key;
            $group['label'] = $group['depart_name'];
            unset($group['depart_name']);
        }
        unset($group);
        $saleDepart = array_get(self::$resourceList, 'cpUserRole.resource', []);   
        $result = [];
        foreach ($saleDepart as $r => $rDesc) {
            // 先获取对应的资源对应的部门
            $departs = [];
            // 获取持有该资源的部门
            $rDeparts = array_get(self::getDepartByResource('cpUserRole', $r), 'data', []);
            foreach ($rDeparts as $departId) {
                if (array_has($groups, $departId)) {
                    $departs[] = array_get($groups, $departId) ?: [];
                }
            }

            $result[] = [
                'value' => $r,
                'label' => $rDesc,
                'children' => $departs,
            ];
        }
        return $result;
    }
    /**
     * 获取两层的销售部门分组数据,带全部
     *
     * @return array
     */
    public static function getStackSaleGroupAndAll()
    {
        $saleGroup = self::getStackSaleGroup();
        $allSaleGroup = [];
        foreach($saleGroup as $k => &$sale){
            $childrenGroup = &$sale['children'];
            $allUserList = [];
            foreach($childrenGroup as &$children){
                $userList = &$children['user_list'];
                $userList[0] = '全部' ;
                $allUserList += $userList;
            }
            array_unshift($childrenGroup, [
                "value" => 0 ,
                "label" => "全部" ,
                'user_list' => $allUserList ,
            ]);
            $allSaleGroup += $childrenGroup;
            $allSaleGroup[0]['user_list'] += $childrenGroup[0]['user_list'];
            unset($children);
            if($sale['value'] == 'sale-tj-group'){
                unset($saleGroup[$k]);
            }
        }
        unset($sale);
        array_unshift($saleGroup, [
            'value' => '0',
            'label' => '全部' ,
            'children' => $allSaleGroup]);
        return $saleGroup;
    }

    public static function getCredentialCreateResource()
    {
        $uid = CpAccess::theUid();
        $resourceList = array_get(self::$resourceList, 'credentialCreate.resource');
        $licenceList = [];
        foreach($resourceList as $resourceKey => $resource) {
            $licence = CpAccess::hasResource($uid, 'credentialCreate', $resourceKey);
            if($licence['code'] == 0 && $licence['msg'] == 'suc') {
                $licenceList[] = [
                    'key' => $resourceKey,
                    'name' => $resource
                ];
            }
            unset($licence);
        }
        return $licenceList;
    }

    //给各种列表提供全局权限控制
    public static function accessPathForList($ormObj, $queryData, $keyList)
    {   
        $domain = explode ('.', \Request::server('HTTP_HOST'));
        if($domain[0] == 'ka'){
            return ;
        }
        $keyCodeMap = [
            self::ACCESS_KEY_EXPRESS => StoreModule::$resourceToStore,
        ];        
        if (empty($keyList) || empty($ormObj)) {
            return true;
        }

        foreach ($keyList as $accessKey => $ormKey) {
            $accessDetailList = self::getAccessDetail($accessKey, $keyCodeMap[$accessKey] ?? []);
            if (isset($queryData[$ormKey])) {
                $queryInfo = is_array($queryData[$ormKey]) ? $queryData[$ormKey] : [$queryData[$ormKey]];
                $accessDetailList = array_intersect($accessDetailList, $queryInfo);            
            } elseif (self::ACCESS_VAL_ALL == self::getAccessVal($accessKey)) {
                $accessDetailList[] = 0;
            }
            if (empty($accessDetailList)) {
                $accessDesc = self::$allAccessPath[$accessKey]['desc'];
                $errorMsg = sprintf("没有当前%s权限，请切换%s或联系管理员开通权限", $accessDesc, $accessDesc);
                throw new WorkException($errorMsg, 20000001);
            }
            $ormObj->whereIn($ormKey, $accessDetailList);
        }
        return true;
    }

    public static function getNewSaleGroupWithName()
    {
        $dDepart = new CpDepartment();
        $departList = $dDepart->getDeaprtByMark(self::$salesMark);
        if (empty($departList)) {
            return self::modelReturn(800024, '部门信息不存在');
        }
        $saleTreeList = [];
        // 一级
        foreach($departList as $departInfo) {
            if($departInfo['mark'] == self::MARK_SALELEADER) {
                $saleTreeList[$departInfo['id']] = [
                    'depart_name' => $departInfo['name'],
                    'user_list'   => [],
                ];
            }
        }
        unset($depart);
        // 二级
        foreach($saleTreeList as $departId => &$depart) {
            foreach($departList as $departInfo) {
                if($departInfo['mark'] == self::MARK_SALE_LEADER && $departInfo['parent_id'] == $departId) {
                    $depart['user_list'][$departInfo['id']] = [
                        'depart_name' => $departInfo['name'],
                        'user_list' => [],
                    ]; 
                }
            }
        }
        unset($depart);
        // 三级
        foreach($saleTreeList as $departId => &$depart) {
            foreach($depart['user_list'] as $groupId => &$group) {
                foreach($departList as $departInfo) {
                    if($departInfo['mark'] == self::MARK_SALE && $departInfo['parent_id'] == $groupId) {
                        $group['user_list'][$departInfo['id']] = [
                            'depart_name' => $departInfo['name'],
                            'user_list' => [],
                        ]; 
                    }
                }
            } 
        }
        unset($depart, $group);
        $departIds = [];
        foreach($departList as $departInfo) {
            if($departInfo['mark'] == self::MARK_SALE || $departInfo['mark'] == self::MARK_SALE_LEADER) {
                $departIds[] = $departInfo['id'];
            }
        }
        $dUserDep = new CpDepartmentUser;
        $saleList = $dUserDep->getUserByDidIn(array_unique($departIds));
        foreach($saleTreeList as $departId => &$depart) {
            foreach($depart['user_list'] as $groupId => &$group) {
                $userList = [];
                foreach($saleList as $user) {
                    if(in_array($user['department_id'], array_merge(array_keys($group['user_list']), [$groupId]))) {
                        $userList[$user['uid']] = CpUserModule::getName($user['uid']);
                    }
                }
                $group['user_list'] = $userList;
            }
        }
        return $saleTreeList;
    }

    // 获取父节点字段
    public static function getFatherInfoById($id, $index = '') 
    {
        $id = intval($id);
        $departModel = new CpDepartment;
        $depart = $departModel->getParentDepart($id);
        if(!empty($index)) {
            return $depart[$index];
        }
        return $depart;
    }

    public static function getSalerGroupDepartInfo($salerUid)
    {
        $departInfo = [];
        $salerNode = CpDepartmentUser::where('uid', $salerUid)->first();
        // if(empty($salerNode)) {
        //     throw new WorkException('该用户不在组织架构', 404);
        // }
        $departNode = CpDepartment::find($salerNode->department_id);

        if(strcmp($departNode->mark, self::MARK_SALE_LEADER) == 0) {
            $departInfo['group'] = $departNode->name;
        }else if(strcmp($departNode->mark, self::MARK_SALE) == 0){
            $departNode = CpDepartment::find($departNode->parent_id);
            $departInfo['group'] = $departNode->name;
        } else{
            $departInfo['group'] = '离职';
        }
        $departNode = CpDepartment::find($departNode->parent_id);
        $departInfo['depart'] = $departNode->name;

        return $departInfo;
    }

    /**
    * 通过部门mark和用户user_id判断该用户是否在该部门
    * @param $userIds [string] 用户id
    * @param $marks [string] 或者 [array] 部门marks
    */
    public static function checkUserInDepartByMark($userId, $mark) 
    {
        if(!is_array($mark)) {
            $mark = [$mark];
        }
        $department = self::getDepartmentByMark($mark);
        if(empty($department)) {
            return false;
        }
        $departmentUserModel = new CpDepartmentUser;
        $userDepart = $departmentUserModel->getUserDepartByUid($userId);
        // $userDepart = array_column($userDepart, 'department_id');

        return empty(array_intersect(array_column($department, 'id'), array_column($userDepart, 'department_id'))) ? false : true;
    }

    /**
    * 获取用户拥有资源 拥有多个资源报错
    * @param userId [int] 客户ID
    * @param constroller [string] 资源名称
    * @return [int] 资源ID
    */
    public static function getUserAccessMark($userId, $controller) 
    {
        $resourceList = array_get(self::$resourceList, $controller . '.resource', []);
        if(empty($resourceList)) {
            return self::modelReturn(404, '资源不存在');
        }
        $userAccess = 0;
        foreach($resourceList as $resourceKey => $resourceValue) {
            $uidList = self::getUserByResource($controller, $resourceKey);
            if($uidList['code'] == 0) {
                $uidList = $uidList['data'];
            }
            if(in_array($userId, $uidList)) {
                if(!empty($userAccess)) {
                    return self::modelReturn(1, '用户拥有多个资源');
                }
                $userAccess = $resourceKey;
            }
        }
        
        return self::modelReturn(0, '', $userAccess);
    }
}
