<?php

namespace App\Http\Controllers\Admin;

// use App\Modules\SalesModule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Admin\Access\Constants\AccessConst;
use App\Modules\Admin\Access\CpAccess;
use App\Modules\Admin\Access\CpUserModule;
use App\Modules\Admin\Access\Models\CpDepartment;
use App\Modules\Admin\Access\Models\CpDepartmentUser;
use App\Modules\Admin\Access\Models\CpDepartmentAction;

/**
 * @desc 访问控制
 */
class AccessController extends Controller
{
    /**
     * @desc 组织架构主页
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function department(Request $request){
        
        $objCpAcc = new CpAccess();
        $data = [
            'accessProjectList' => AccessConst::$accessProjectList,
        ];
    	return view('admin.access.department', $data); ;
    }
    
    /**
     * @desc 获取组织构架树
     */
    public function getTree(){
        $objDepart = new CpDepartment();
        $treeInfo  = $objDepart->getDepartmentTree($did=0);
        // return $this->renderTree($treeInfo);
        return $this->json(0, 'ok', $treeInfo);
    }

    /**
     * @desc 获取组织构架树（权限、资源）
     */
    public function getDepartTree(){
        $objDepart = new CpDepartment();
        $treeInfo  = $objDepart->getDepartmentTree($did=0);
        return $this->renderTree($treeInfo);
    }

    /**
     * @desc 渲染组织架构树
     */
    public function renderTree($list, $isFirst = true){
        $num = sizeof($list);
        foreach ($list as $key => $node) {
            echo "<div class='strt-block'>
                  <div class='strt-part'>";
            if(!$isFirst){
                if($num == 1){
                }elseif($num == 2){
                    echo $key == 1 ? "<span class='line-h line-h-l'></span>" : "<span class='line-h line-h-r'></span>"; 
                    echo "<div class='line-v'><span></span></div>";
                }else{
                    if($key == 0){
                        echo "<span class='line-h line-h-r'></span>";
                    }elseif(($key+1) == $num ){
                        echo "<span class='line-h line-h-l'></span>";
                    }else{
                        echo "<span class='line-h line-h-c'></span>";
                    }
                    echo "<div class='line-v'><span></span></div>";
                }
            }
            //统计每个节点下的人数
            $ret = array('data'=>1,'code'=>0);
            $node['city_name'] = '北京';
            if($ret['code'] == 0 && !empty($ret['data'])){
                $node['count'] = count($ret['data']);
            }else{
                $node['count'] = 0;
            }
            echo "<div class='strt-name' choose='0' departname='{$node['name']}' departid='{$node['id']}' citycode='{$node['city_id']}' mark='{$node['mark']}' email='{$node['email']}'><table class='table table-bordered'><tr><td colspan='2'>{$node['name']}</td></tr><tr><td>{$node['city_name']}</td><td class='user_count'>{$node['count']}</td></tr></table></div>";
            if(isset($node['child']) && !empty($node['child'])){
                echo "<div class='line-v'><span></span></div>";
                self::renderTree($node['child'], false);
            }
            echo "</div>
                </div>"; 
        }
    }

    /**
     * @desc 获取所有的部门信息
     */
    public function getAllDepart() {
        $objDepart = new CpDepartment();
        $pInfo = $objDepart->getAllDepart();
        return $this->json(0,'suc', $pInfo);
    }

    /**
     * @desc 获取父节点信息
     */
    public function getParentDepart(Request $request) {
        $id = $request->input('id');
        $objDepart = new CpDepartment();
        $pInfo = $objDepart->getParentDepart($id);      
        return $this->json(0, 'suc', $pInfo);     
    }

    /**
     * @desc 获取部门用户
     */
    public function getDepartUser(Request $request) {
        $id = $request->input('did');
        if (empty($id)) {
            return $this->json(1, '部门ID为空');
        }
        $userList = CpDepartmentUser::where('department_id', $id)->where('is_deleted',CpDepartmentUser::NOT_DELETED)->get()->toArray();
        if(!empty($userList)) {
            foreach ($userList as &$user) {
                $user['userName']  = CpUserModule::getName($user['uid']);
                $user['adminName'] = CpUserModule::getName($user['admin_uid']);
                $user['cp'] = CpUserModule::getUserInfo($user['uid'], 'mobile_phone');
            }
            unset($user);
        }
        return $this->json(0, 'ok', $userList);
    }

    /**
     * @desc 获取权限组信息
     */
    public function getActionGroup(Request $request) {
        $did = $request->input('did');
        //所有权限节点
        $allProjectAction = CpAccess::getActionList();
        $allAction = [];
        foreach ($allProjectAction as $projectAction) {
            $allAction = array_merge($projectAction ?: [], $allAction);
        }
        //权限组，部门配置
        $departGroup = CpAccess::getActionGroupByDid($did);
        if ($departGroup['code'] == 0 && !empty($departGroup['data'])) {
            $departGroup = $departGroup['data'];
            $gids = array();
            foreach ($departGroup as $one) {
                $gids[] = $one['group_id'];
            }
            //权限组，权限配置
            $groups  = CpAccess::getActionGroupsByIdIn($gids);
            if ($groups['code'] == 0 && !empty($groups['data'])) {
                $groups  = $groups['data'];
                $actions = CpAccess::getActionsByGids($gids);
                $actions = $actions['data'];
                $actionList = array();
                foreach ($actions as $oneAction) {
                    $actionList[$oneAction['gid']][$oneAction['controller']]['name']   = $allAction[$oneAction['controller']]['desc'];
                    $actionList[$oneAction['gid']][$oneAction['controller']]['actions'][] = array(
                        'desc'  => $allAction[$oneAction['controller']]['action'][$oneAction['action']]['desc'],
                        'limit' => $oneAction['data_limit'],
                        'color' => $oneAction['data_limit'] == 1 ? 'info' : 'success',
                    );
                }
                foreach ($groups as &$oneGroup) {
                    if (isset($actionList[$oneGroup['id']])) {
                        $oneGroup['actions'] = $actionList[$oneGroup['id']];
                    }
                }
            }
        }
        //单个权限
        $depart = CpAccess::getDepartInfo($did);
        $tmp = array();
        if ($depart['code'] == 0 && !empty($depart['data'])) {
            $depart = $depart['data'];
            $actionList = CpAccess::getDeaprtActionList($did);
            $actionList = $actionList['data'];
            foreach ($actionList as $oneAction) {
                $tmp[$oneAction['controller']]['name'] = $allAction[$oneAction['controller']]['desc'];
                $tmp[$oneAction['controller']]['actions'][] = array(
                    'desc'  => $allAction[$oneAction['controller']]['action'][$oneAction['action']]['desc'],
                    'limit' => $oneAction['data_limit'],
                    'color' => $oneAction['data_limit'] == 1 ? 'info' : 'success',
                );
            }
        }
        $data['tmp'] = $tmp;
        $data['depart'] = $depart;
        $data['groups'] = $groups;
        // return view('admin.access.departmentActionList')->with('controller_list',$tmp)
        //                                       ->with('depart_info', $depart)
        //                                       ->with('group_info', isset($groups) ? $groups : array());
        return $this->json(0, 'ok', $data);
    }

    public function getDepartResource(Request $request) {
        $allResource  = CpAccess::$resourceList;
        $did = $request->input('did');
        $departGroup = CpAccess::getResourceGroupByDid($did);
        if ($departGroup['code'] == 0 && !empty($departGroup['data'])) {
            $departGroup = $departGroup['data'];
            $gids = array();
            foreach ($departGroup as $one) {
                $gids[] = $one['group_id'];
            }
            $groups  = CpAccess::getResourceGroupsByGroupIdIn($gids);
            if ($groups['code'] == 0 && !empty($groups['data'])) {
                $groups  = $groups['data'];
                $actions = CpAccess::getResourcesByGids($gids);
                $actions = $actions['data'];
                $actionList = array();
                foreach ($actions as $oneAction) {
                    $actionList[$oneAction['gid']][$oneAction['controller']]['name']   = $allResource[$oneAction['controller']]['desc'];
                    $actionList[$oneAction['gid']][$oneAction['controller']]['resource'][] = array(
                        'desc'  => $allResource[$oneAction['controller']]['resource'][$oneAction['resource']],
                        'limit' => $oneAction['data_limit'],
                        'color' => $oneAction['data_limit'] == 1 ? 'info' : 'success',
                    );
                }
                foreach ($groups as &$oneGroup) {
                    if (isset($actionList[$oneGroup['id']])) {
                        $oneGroup['resources'] = $actionList[$oneGroup['id']];
                    }
                }
                // F3::set('group_info',$groups);
            }
        }

        //单独配置的资源
        $tmp = array();
        $depart = CpAccess::getDepartInfo($did);
        if ($depart['code'] == 0 && !empty($depart['data'])) {
            $depart = $depart['data'];
            // F3::set('depart_info', $depart);
            $departResour = CpAccess::getDepartResourceList($did);
            $departResour = $departResour['data'];
            foreach ($departResour as $oneResource) {
                $tmp[$oneResource['controller']]['name'] = $allResource[$oneResource['controller']]['desc'];
                $tmp[$oneResource['controller']]['resource'][] = array(
                    'desc'  => $allResource[$oneResource['controller']]['resource'][$oneResource['resource']],
                );
            }
        }
        // F3::set('resource_list', $tmp);
        // echo Template::serve('cp/longrent_department/department_render_resource.html');      
        // return view('admin.access.departmentResourceRender')->with('resource_list',$tmp)
        //                                       ->with('depart_info', $depart)
        //                                       ->with('group_info', isset($groups) ? $groups : array());   
        $data['depart'] = $depart;
        $data['groups'] = isset($groups) ? $groups : array();
        $data['tmp'] = $tmp;
        return $this->json(0,'ok', $data);
    }

    /**
     * @desc 部门单个权限配置页
     */
    public function actionAccessDetail(Request $request) {
        $did = $request->input('id');
        $project = $request->input('project');
        $depart = CpAccess::getDepartInfo($did);
        if ($depart['code'] != 0 || empty($depart['data'])) {
            die('请重试');
        }
        $depart = $depart['data'];
        $actionList = array_get(CpAccess::getDeaprtActionList($did), 'data');
        $ret = CpAccess::getActionList($project);
        return view('admin.access.departmentActionAccess')
            ->with('action_list', $ret)
            ->with('action_info', $actionList)
            ->with('depart_info', $depart)
            ->with('project', $project)
            ->with('id', $did);
    }

    /**
     * @desc 权限包编辑
     */
    public function actionGroupAccessDetail(Request $request) {
        $gid = $request->input('id');
        $groupInfo  = CpAccess::getActionGroupInfo($gid);
        if ($groupInfo['code'] != 0 || empty($groupInfo['data'])) {
            die('请重试');
        }
        $groupInfo = $groupInfo['data'];
        $actionList = CpAccess::getActionByGroupId($gid);
        $departList = CpAccess::getDepartByGroupId($gid);
        $ret = CpAccess::getActionList($groupInfo['project']);
        return view('admin.access.departmentActionGroupAccess')->with('action_list', $ret)->with('group_info', $groupInfo)        
                                                     ->with('action_info_json', json_encode($actionList['data']))
                                                     ->with('deaprt_info_json', json_encode($departList['data']))
                                                     ->with('gid', $gid);
    }

    /**
     * @desc 设置部门单个权限
     */
    public function setDepartAction(Request $request) {
        $controller = $request->input('controller');
        $action     = $request->input('action');
        $choose     = $request->input('choose');
        // $limit      = $request->input('limit');
        $did        = $request->input('did');
        $project = $request->input('project');
        if (empty ($project)) {
            return $this->json(1,'保存失败，场景错误');
        }
        $data = array();
        foreach ($controller as $key => $name) {
            $tmp = array(
                    'controller' => $name,
                    'action'     => $action[$key],
                    'choose'     => $choose[$key],
                    'limit'      => 0,
            );

            if ($choose[$key] == 1) {
                $data['choose'][] = $tmp;
            } else {
                $data['remove'][] = $tmp;
            }
        }
        $ret = true;
        if (!empty($data['choose'])) {
            $addRet = CpAccess::addDepartAction($data['choose'], $did, $project);
            if ($addRet['code'] != 0) {
                $ret = false;
            }
        }
        if (!empty($data['remove'])) {
            $reRet  = CpAccess::removeDepartAction($data['remove'], $did, $project);
            if ($reRet['code'] != 0) {
                $ret = false;
            }
        }
        if ($ret) {
            return $this->json(0,'保存成功');
        } else {
            return $this->json(1,'保存失败，请重试');
        }
    }


    /**
     * @desc 设置部门的权限组
     */
    public function setDepartActionGroup(Request $request) {
        $gid    = $request->input('gid');
        $ids    = $request->input('id_arr');
        $choose = $request->input('choose');
        $data = array();
        foreach ($ids as $key => $one) {
            if ($choose[$key] == 1) {
                $data['choose'][] = $one;
            } else {
                $data['remove'][] = $one;
            }
        }
        // Logger::info('[longrent_set_department_group] [%s] [%s]', $gid, json_encode($data)); 
        $ret = true;
        if (!empty($data['choose'])) {
            $addRet = CpAccess::addDepartActionGroup($data['choose'], $gid);
            if ($addRet['code'] != 0) {
                $ret = false;
            }
        }
        if (!empty($data['remove'])) {
            $reRet  = CpAccess::removeDepartActionGroup($data['remove'], $gid);
            if ($reRet['code'] != 0) {
                $ret = false;
            }
        }
        if ($ret) {
            return $this->json(0,'保存成功');
        } else {
            return $this->json(1,'保存失败，请重试');
        }
    }

    /**
     * @desc 设置权限组的权限详情
     */
    public function setActionGroupAccess(Request $request) {
        $controller = $request->input('controller');
        $action     = $request->input('action');
        $choose     = $request->input('choose');
        // $inherit    = $request->input('inherit');
        // $limit      = $request->input('limit');
        $gid        = $request->input('gid');
        $data = array();
        foreach ($controller as $key => $name) {
            $tmp = array(
                    'controller' => $name,
                    'action'     => $action[$key],
                    'choose'     => $choose[$key],
                    'inherit'    => 0,
                    'limit'      => 0,
            );

            if ($choose[$key] == 1) {
                $data['choose'][] = $tmp;
            } else {
                $data['remove'][] = $tmp;
            }
        }
        // Logger::info('[longrent_set_group_action] [%s] [%s]', $gid, json_encode($data));
        $ret = true;
        if (!empty($data['choose'])) {
            $addRet = CpAccess::addActionGroupAccess($data['choose'], $gid);
            if ($addRet['code'] != 0) {
                $ret = false;
            }
        }
        if (!empty($data['remove'])) {
            $reRet  = CpAccess::removeActionGroupAccess($data['remove'], $gid);
            if ($reRet['code'] != 0) {
                $ret = false;
            }
        }
        if ($ret) {
            return $this->json(0,'保存成功');
        } else {
            return $this->json(1,'保存失败，请重试');
        }
    }

    /**
     * @desc 添加部门
     */
    public function addDepart(Request $request) {
        $name  = $request->input('name');
        $code  = $request->input('code');
        $pid   = $request->input('pid');
        $mark  = $request->input('mark');
        $email = $request->input('email');
        $ret   = CpAccess::addDepartment($name, $mark, $code, $pid, $email);
        return $this->json($ret['code'],$ret['msg'],$ret['data']);
    }

    /**
     * @desc 获取部门信息
     */
    public function getDepartInfo(Request $request)
    {
        $did = $request->input('did');
        $ret = CpAccess::getDepartInfo($did);
        return $this->json($ret['code'], $ret['msg'], $ret['data']);
    }
    /**
     * @desc 添加部门用户
     */
    public function addDepartUser(Request $request)
    {
        $did = $request->input('did');
        $accout = $request->input('cp_account');
        if (empty($did) || empty($accout)) {
            return $this->json(1, '信息有误');
        }
        $ret = CpAccess::addDepartUser($did, $accout);
        //增加销售信息
        // SalesModule::addSellerInfo();
        return $this->json($ret['code'], $ret['msg'], $ret['data']);
    }

    /**
     * @desc 权限组列表页
     */
    public function actionGroupList()
    {
        $list = CpAccess::getActionGroupList();
        $assign = [
            'accessProjectList' => AccessConst::$accessProjectList,
            'action_group_list' => $list['data'],
        ];
        return view('admin.access.actionGroupList', $assign);
    }

    /**
     * @desc 添加权限组
     */
    public function addActionGroup(Request $request)
    {
        $id   = trim($request->input('id'));
        $data = [
            'name' => trim($request->input('name')),
            'desc' => trim($request->input('desc')),
            'project' => trim($request->input('project')),
        ];
        if (empty($id)) {
            $ret  = CpAccess::addActionGroup($data);
        } else {
            $ret  = CpAccess::updateActionGroup($id, $data);
        }
        return $this->json($ret['code'], $ret['msg'], $ret['data']);      
    }

    /**
     * @desc 更新部门信息
     */
    public function updateDepart(Request $request)
    {
        $name = $request->input('name');
        $code = $request->input('code');
        $id   = $request->input('id');
        $pid  = $request->input('pid');
        $mark = $request->input('mark');
        $email = $request->input('email');
        $data = array(
                'name'      => $name,
                'city_id'   => $code,
                'parent_id' => $pid,
                'mark'      => empty($mark) ? '' : $mark,
                'email'     => empty($email) ? '' : $email,
            );
        $ret  = CpAccess::updateDepartment($id, $data);
        return $this->json($ret['code'],$ret['msg'],$ret['data']);        
    }

    public function delActionGroup(Request $request) {
        $id = intval($request->input('id'));
        $ret  = CpAccess::delActionGroup($id);
        return $this->json($ret['code'], $ret['msg'], $ret['data']);    
    }

    public function delDepartUser(Request $request) {
        $did = $request->input('did');
        $uid = $request->input("uid");
        $ret = CpAccess::delDepartUser($uid, $did);
        return $this->json($ret['code'], $ret['msg'], $ret['data']);        
    }

    /**
     * @desc 资源组列表
     */
    public function resourceGroupList() {
        $list = CpAccess::getResourceGroupList();
        // F3::set('list', $list['data']);
        // echo Template::serve('cp/longrent_department/resource_group_list.html');
        return view('admin.access.resourceGroupList')->with('action_group_list', $list['data']);
    } 

    /**
     * @desc 添加资源组
     */
    public function addResourceGroup(Request $request)
    {
        $id = trim($request->input('id'));
        $name = trim($request->input('name'));
        $desc = trim($request->input('desc'));
        if (empty($id))
            $ret  = CpAccess::addResourceGroup($name, $desc);
        else
            $ret  = CpAccess::updateResourceGroup($id, $name, $desc);
        return $this->json($ret['code'], $ret['msg'], $ret['data']);        
    }

    /**
     * @desc 删除资源组
     */
    public function delResourceGroup(Request $request)
    {
        $id = trim($request->input('id'));
        $ret  = CpAccess::delResourceGroup($id);
        return $this->json($ret['code'], $ret['msg'], $ret['data']);
    }

    /**
     * @desc 资源组配置页
     */
    public function resourceGroupDetail(Request $request)
    {
        $gid = intval($request->input('id'));
        $groupInfo  = CpAccess::getResourceGroupInfo($gid);
        if ($groupInfo['code'] != 0 || empty($groupInfo['data'])) {
            die('请重试');
        }
        // F3::set('group_info', $groupInfo['data']);
        $actionList = CpAccess::getResourceByGroupId($gid);
        // F3::set('action_info_json', json_encode($actionList['data']));
        $departList = CpAccess::getDepartByResourceGroupId($gid);
        // F3::set('deaprt_info_json', json_encode($departList['data']));
        $resourceList = CpAccess::$resourceList;
        return view('admin.access.departmentResourceGroupAccess')->with('resource_list', $resourceList)->with('group_info', $groupInfo['data'])        
                                                     ->with('action_info_json', json_encode($actionList['data']))
                                                     ->with('deaprt_info_json', json_encode($departList['data']))
                                                     ->with('gid', $gid);
    }

    /** 
     * @desc 设置部门单独的资源
     */
    public function setDepartResourceDetail(Request $request)
    {
        $controller = $request->input('controller');
        $resource   = $request->input('resour_arr');
        $choose     = $request->input('choose');
        $did        = $request->input('did');
        $data = array();
        foreach ($controller as $key => $name) {
            $tmp = array(
                    'controller' => $name,
                    'resource'   => $resource[$key],
            );
            if ($choose[$key] == 1) {
                $data['choose'][] = $tmp;
            } else {
                $data['remove'][] = $tmp;
            }
        }

        $ret = true;
        if (!empty($data['choose'])) {
            $addRet = CpAccess::addDepartResource($data['choose'], $did);
            if ($addRet['code'] != 0) {
                $ret = false;
            }
        }
        if (!empty($data['remove'])) {
            $reRet  = CpAccess::removeDepartResource($data['remove'], $did);
            if ($reRet['code'] != 0) {
                $ret = false;
            }
        }
        if ($ret) {
            return $this->json(0,'保存成功');
        } else {
            return $this->json(1,'保存失败，请重试');
        }

    }

    /**
     * @desc 设置部门的资源组
     */
    public function setDepartResourceGroup(Request $request)
    {
        $gid    = $request->input('gid');
        $ids    = $request->input('id_arr');
        $choose = $request->input('choose');
        $data = array();
        foreach ($ids as $key => $one) {
            if ($choose[$key] == 1) {
                $data['choose'][] = $one;
            } else {
                $data['remove'][] = $one;
            }
        }

        $ret = true;
        if (!empty($data['choose'])) {
            $addRet = CpAccess::addDepartResourceGroup($data['choose'], $gid);
            if ($addRet['code'] != 0) {
                $ret = false;
            }
        }
        if (!empty($data['remove'])) {
            $reRet  = CpAccess::removeDepartResourceGroup($data['remove'], $gid);
            if ($reRet['code'] != 0) {
                $ret = false;
            }
        }
        if ($ret) {
            return $this->json(0,'保存成功');
        } else {
            return $this->json(1,'保存失败，请重试');
        }
    }

    /**
     * @desc 设置资源组包含资源
     */
    public function setResourceGroupAccess(Request $request)
    {
        $controller = $request->input('controller');
        $resource   = $request->input('resource');
        $choose     = $request->input('choose');
        $inherit    = $request->input('inherit');
        $limit      = $request->input('limit');
        $gid        = $request->input('gid');
        $data = array();
        foreach ($controller as $key => $name) {
            $tmp = array(
                'controller' => $name,
                'resource'     => $resource[$key],
                'choose'     => $choose[$key],
                'inherit'    => $inherit[$key],
                'limit'      => $limit[$key],
            );

            if ($choose[$key] == 1) {
                $data['choose'][] = $tmp;
            } else {
                $data['remove'][] = $tmp;
            }
        }
        $ret = true;
        if (!empty($data['choose'])) {
            $addRet = CpAccess::addGroupResource($data['choose'], $gid);
            if ($addRet['code'] != 0) {
                $ret = false;
            }
        }
        if (!empty($data['remove'])) {
            $reRet  = CpAccess::removeGroupResource($data['remove'], $gid);
            if ($reRet['code'] != 0) {
                $ret = false;
            }
        }
        if ($ret) {
            return $this->json(0,'保存成功');
        } else {
            return $this->json(1,'保存失败，请重试');
        }
    }

    /**
     * @desc 部门单独资源详情
     */
    public function departResourceDetail(Request $request)
    {
        $did    = $request->input('id');
        $depart = CpAccess::getDepartInfo($did);
        if ($depart['code'] != 0 || empty($depart['data'])) {
            die('请重试');
        }
        $depart = $depart['data'];
        // F3::set('depart_info', $depart);
        $departResour = CpAccess::getDepartResourceList($did);
        // F3::set('choose_resource', json_encode($departResour['data']));
        $resourceList = CpAccess::$resourceList;
        // F3::set('resource_list', $resourceList);
        return view('admin.access.departmentResourceAccessDetail')->with('resource_list', $resourceList)
                                                     ->with('choose_resource', json_encode($departResour['data']))
                                                     ->with('depart_info', $depart)
                                                     ->with('did', $did);
    }

    /**
     * @desc 切换全局权限入口
     */
    public function selectAccess(Request $request)
    {
        $accessKey = trim($request->input('access_key'));
        $accessVal = trim($request->input('access_val'));
        $ret = CpAccess::selectAccess($accessKey, $accessVal); 
        return $this->json($ret['code'], $ret['msg']);
    }

    /**
     * @desc 用户手机号解码
     * @return 
     */
    public function decodeMaskMobile($id)
    {
        if (empty($id) || is_int($id)) {
            return $this->json(800035, '参数错误');
        }
        $mobile = CpAccess::decodeMaskMobile($id);
        return $this->json(0, 'ok', $mobile);
    }

    /**
     * @desc 删除部门
     * @return [type] [description]
     */
    public function delDepart(Request $request)
    {
        $id = intval($request->input('id'));
        if (empty($id)) {
            return $this->json(800035, '参数错误');
        }
        $ret = CpAccess::delDepart($id);        
        if ($ret) {
            return $this->json(0,'删除成功');
        } else {
            return $this->json(1,'删除失败，请重试');
        }
    }

}
