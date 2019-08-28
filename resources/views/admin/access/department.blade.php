@extends('admin.layout')

@section('title', '组织架构')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style type="text/css">
    body{font:16px/1.8 "宋体";overflow-y:scroll}
    html,body{height:100%;margin:0;padding:0;}
    [v-cloak]{
        display: none !important;
    }
    /* tree */
    .org-tree-container {
        display: inline-block;
        padding: 15px;
        background-color: #fff;
        width: 100vw;
        text-align: center;
        cursor: pointer;
        user-select: none;
    }

    .ivu-card {
        margin-top: 15px;
    }
    .main {
        /* position: fixed;
        left: 0px; */
    }
    .form-inline .form-inline-item{
        display: inline-block;
    }
    .form-inline .form-inline-item .form-inline-item-title{
        display: inline-block;
        border: 1px solid #dddee1;
        border-right: 0;
        background-color: #f3f3f3;
        padding-left: 20px;
        vertical-align: middle;
        height: 32px;
        line-height: 32px;
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
        font-size: 14px;
    }
    .form-inline .form-inline-item .form-inline-item-content{
        width: 150px;
        display: inline-block;
    }
    .form-inline .form-inline-item .form-inline-item-content input{
        border-top-left-radius: 0px;
        border-bottom-left-radius: 0px;
    }

    .depart-user .form-inline-item{
        margin-bottom: 0;
    }
    .depart-user table td, .depart-user table th, .depart-user table{
        border-color: rgba(221, 221, 221, 0.4) !important
    }
    .depart-user table th {
        background-color: #f5f5f5;
    }

    .action_resource_group {
        margin-bottom: 20px;
    }
    .action_resource_group table {
        width: 100%;
    }
    .action_resource_group table th, .action_resource_group table td{
        border: 1px solid #ddd;
        padding: 5px 8px;
    }
    .action_resource_group table .table_tr_title{
        width: 150px;
    }
    .action_resource_group table th {
        background-color: #f5f5f5;
    }
    .action_resource_group table td p {
        margin: 0;
    }
    .action_resource_group table td .ivu-btn {
        margin-right: 10px;
    }
    .action_resource_group .title {
        margin: 0;
        font-weight: 700;
        font-size: 18px;
    }
    .org-tree-node-label-inner-check {
        border: 1px solid #5cacee;
        color: #5cacee;
    }
</style>
<div id="organization" v-cloak>
    <div class="org-tree-container">
        <div class="org-tree" width="100vw" ref="org-tree">
            <org-tree v-for="organization,index in organizationData" :key="index" :data="organization" :collapsable="treeProps.collapsable" :expand="treeProps.expand" :children="treeProps.children" @on-expand="onExpand" @on-node-click="onNodeClick"></org-tree>
        </div>
    </div>
    <div class="main" :style="{top: mainTop + 'px'}" v-if="nodeData.id">
        <Card>
            <div class="node-edit">
                <div class="form-inline">
                    <p class="form-inline-item">
                        <span class="form-inline-item-title">名称：</span><i-input class="form-inline-item-content" v-model="nodeData.name" disabled></i-input>
                    </p>
                    <p class="form-inline-item">
                        <span class="form-inline-item-title">标识：</span><i-input class="form-inline-item-content" v-model="nodeData.mark" disabled></i-input>
                    </p>
                    <p class="form-inline-item">
                        <span class="form-inline-item-title">邮箱：</span><i-input class="form-inline-item-content" v-model="nodeData.email" disabled></i-input>
                    </p>
                    <p class="form-inline-item">
                        <span class="form-inline-item-title">上级部门：</span><i-input class="form-inline-item-content" v-model="nodeData.parent_name" disabled></i-input>
                    </p>
                    <p class="form-inline-item form-inline-option">
                        <i-button type="primary" @click="nodeOptionHandle('edit')">编 辑</i-button>
                        <i-button type="info" @click="nodeOptionHandle('add')">添加子节点</i-button>
                        <i-button type="error" @click="delDepart()">删除</i-button>
                    </p>
                </div>
            </div>
        </Card>
        <Card>
            <div class="form-inline">
                <p class="form-inline-item">
                    <span class="form-inline-item-title">CP账号：</span><i-input class="form-inline-item-content" v-model="cp_account"></i-input>
                </p>
                <p class="form-inline-item form-inline-option">
                    <i-button type="primary" @click="addDepartmentUser">添加用户到部门</i-button>
                    <i-button type="ghost" @click="pageJump('addAdmin')">新增一个管理员</i-button>
                </p>
            </div>
            <div class='depart-user'>
                <table class="table table-bordered table-hover" style="margin-bottom: 0">
                    <tr>
                        <th>ID</th>
                        <th>CP账户</th>
                        <th>姓名</th>
                        <th>添加时间</th>
                        <th>添加人</th>
                        <th>操作</th>
                    </tr>
                    <tr v-for="user in nodeData.departuser">
                        <td>@{{user.uid}}</td>
                        <td>@{{user.cp}}</td>
                        <td>@{{user.userName}}</td>
                        <td>@{{user.ctime}}</td>
                        <td>@{{user.adminName}}</td>
                        <td>
                            <i-button type="error" size="small" @click="delDepartUser(user.uid)">删除</i-button>
                        </td>
                    </tr>
                </table>
            </div>
        </Card>
        <Card>
            <div class='action_resource_group depart-action-group' id="depart_action_group">
                <p class="title">权限包详情</p>
                <table>
                    <template v-for="group,key in nodeData.actionGroup.groups">
                        <tr>
                            <th class="table_tr_title">权限组名</th>
                            <th @click.self="nodeData.isActionGroupShow = nodeData.isActionGroupShow ? false : true">
                                @{{group.name}}（@{{group.desc}}）
                                <i-button type="primary" size="small" @click="pageJump('actionGroup', group.id)">编辑权限</i-button>
                            </th>
                        </tr>
                        <tr v-show="nodeData.isActionGroupShow">
                            <td class="table_tr_title">权限详情</td>
                            <td>
                                <template v-for="groupItem,index in group.actions">
                                    <table>
                                        <tr>
                                            <td>
                                                <p>@{{groupItem.name}}（@{{index}}）</p>
                                                <i-button type="success" size="small" v-for="groupResource,groupResourceIdx in groupItem.actions" 
                                                :key="groupResourceIdx" v-if="groupResource.desc">@{{groupResource.desc}}</i-button>
                                            </td>
                                        </tr>
                                    </table>
                                </template>
                            </td>
                        </tr>
                    </template>
                    <tr >
                        <th class="table_tr_title">独立权限</th>
                        <th @click.self="nodeData.isActionTmpShow = nodeData.isActionTmpShow ? false : true">单个权限详情
                            <i-button 
                                v-for="(projectDesc, project) in accessProjectList" 
                                style="margin-right:10px;" 
                                type="primary" 
                                size="small" 
                                @click="pageJump('actionTmp', project)"
                            >
                                @{{ projectDesc }}权限
                            </i-button>
                        </th>
                    </tr>
                    <tr v-show="nodeData.isActionTmpShow">
                        <td class="table_tr_title">权限详情</td>
                        <td>
                            <template v-for="resource,key in nodeData.actionGroup.tmp">
                                <table>
                                    <tr>
                                        <td>
                                            <p>@{{resource.name}}（@{{key}}）</p>
                                            <i-button type="success" size="small" v-for="resourceItem,index in resource.actions" :key="index" v-if="resourceItem.desc">@{{resourceItem.desc}}</i-button>
                                        </td>
                                    </tr>
                                </table>
                            </template>
                        </td>
                    </tr>
                </table>
            </div>
            <div class='action_resource_group depart-resource' id="depart_resource">
                <p class="title">资源包详情</p>
                <table>
                    <template v-for="group,key in nodeData.departResource.groups">
                        <tr>
                            <th class="table_tr_title">资源组名</th>
                            <th @click.self="nodeData.isResourceGroupShow = nodeData.isResourceGroupShow ? false : true">
                                @{{group.name}}（@{{group.desc}}）
                                <i-button type="primary" size="small" @click="pageJump('resourceGroup', group.id)">编辑资源</i-button>
                            </th>
                        </tr>
                        <tr v-show="nodeData.isResourceGroupShow">
                            <td class="table_tr_title">资源详情</td>
                            <td>
                                <template v-for="groupItem,index in group.resources">
                                    <table>
                                        <tr>
                                            <td>
                                                <p>@{{groupItem.name}}（@{{index}}）</p>
                                                <i-button type="success" size="small" v-for="groupResource,groupResourceIdx in groupItem.resource" :key="groupResourceIdx" v-if="groupResource.desc">@{{groupResource.desc}}</i-button>
                                            </td>
                                        </tr>
                                    </table>
                                </template>
                            </td>
                        </tr>
                    </template>
                    <tr>
                        <th class="table_tr_title">独立资源</th>
                        <th @click.self="nodeData.isResourceTmpShow = nodeData.isResourceTmpShow ? false : true">单个资源详情
                            <i-button type="primary" size="small" @click="pageJump('resourceTmp')">编辑资源</i-button>
                        </th>
                    </tr>
                    <tr v-show="nodeData.isResourceTmpShow">
                        <td class="table_tr_title">资源详情</td>
                        <td>
                            <template v-for="resource,key in nodeData.departResource.tmp">
                                <table>
                                    <tr>
                                        <td>
                                            <p>@{{resource.name}}（@{{key}}）</p>
                                            <i-button type="success" size="small" v-for="resourceItem,index in resource.resource" :key="index" v-if="resourceItem.desc">@{{resourceItem.desc}}</i-button>
                                        </td>
                                    </tr>
                                </table>
                            </template>
                        </td>
                    </tr>
                </table>
            </div>
        </Card>
        <Modal
            v-model="nodeModalData.isShow"
            :title="nodeModalData.title"
            @on-cancel="cancelModal"
            @on-ok="saveModal"
        >   
            <i-form :label-width="100">
                <form-item label="名称：">
                    <i-input v-model="nodeModalData.name" placeholder="请输入"></i-input>
                </form-item>
                <form-item label="标识：">
                    <i-input v-model="nodeModalData.mark" placeholder="请输入"></i-input>
                </form-item>
                <form-item label="邮箱：">
                    <i-input v-model="nodeModalData.email" placeholder="请输入"></i-input>
                </form-item>
                <form-item label="上级部门：">
                    <i-select v-model="nodeModalData.pid" :disabled="nodeModalData.modalType == 'add'">
                        <i-option v-for="depart in nodeModalData.allDepartList" :key="depart.id" :value="depart.id">@{{depart.name}}</i-option>
                    </i-select>
                </form-item>
            </i-form>
        </Modal>
    </div>
</div>

{{csrf_field()}}

<script>
    var vm = new Vue({
        el: '#organization',
        data() {
            return {
                mainTop: 250,
                treeProps: {
                    collapsable: false,
                    expand: 'expand',
                    children: 'child',
                },
                organizationData: [],
                nodeData: {
                    departuser: [],
                    actionGroup: {},
                    departResource: {},
                    id: null,
                    pid: null,
                    parent_name: null,
                    email: null,
                    mark: null,
                    name: null,
                    isActionTmpShow: false,
                    isActionGroupShow: false,
                    isResourceTmpShow: false,
                    isResourceGroupShow: false,
                },
                nodeModalData: {
                    allDepartList: [],
                    isShow: false,
                    modalType: null,
                    title: '',
                    id: null,
                    pid: null,
                    email: null,
                    mark: null,
                    name: null,
                },
                cp_account: null,
                accessProjectList : {!! json_encode($accessProjectList) !!},
            }
        },
        methods: {
            getData(pid = 1, checkId = null) {
                var _this = this;
                $.ajax({
                    url : '/cp/longrentdepartment/ajaxrenderdeparttree?pid=0',
                    type : 'GET',
                    success: function(res){
                        // res.data.forEach(function(v, i) {
                        //     v.expand = true
                        // })
                        _this.addAttr(res.data);
                        // res.data[0].expand = true;
                        _this.organizationData = res.data;
                        _this.expandParent(_this.organizationData, pid, checkId);
                    }
                });
            },
            addAttr(data) {
                var _this = this;
                data.forEach(function (v,i) {
                    v.expand = false;
                    if (v.child) {
                        _this.addAttr(v.child);
                    }
                })
            },
            // 组织架构-相关操作
            onExpand(data) {
                var _this = this;
                if ("expand" in data) {
                    data.expand = !data.expand;
                    if (!data.expand && data.children) {
                        this.collapse(data.children);
                    }
                } else {
                    this.$set(data, "expand", true);
                }
                this.$nextTick(function() {
                    _this.mainTop = Number($('.org-tree-container').height()) + 100;
                })
            },
            collapse(list) {
                var _this = this;
                list.forEach(function(child) {
                    if (child.expand) {
                        child.expand = false;
                    }
                    child.children && _this.collapse(child.children);
                });
            },
            setNodeData(data = {}) {
                this.nodeData.id = data.id;
                this.nodeData.email = data.email;
                this.nodeData.mark = data.mark;
                this.nodeData.name = data.name;
                this.nodeData.pid = Number(data.pid) || Number(data.parent_id);
            },
            setNodeModalData(data = {}) {
                this.nodeModalData.id = data.id || null;
                this.nodeModalData.pid = data.pid || null;
                this.nodeModalData.email = data.email || null;
                this.nodeModalData.mark = data.mark || null;
                this.nodeModalData.name = data.name || null;
            },
            onNodeClick(e, data) {
                this.setNodeData(data);
                this.getdepartuser(data.id)
                this.getActionGroup(data.id)
                this.getDepartResource(data.id)
                this.getParentDepart(data.id)
                $('.org-tree-node-label-inner').removeClass('org-tree-node-label-inner-check')
                e.target.className += ' org-tree-node-label-inner-check'
            },
            // 组织架构-相关操作 - end
            // node节点编辑/添加
            nodeOptionHandle(type) {
                this.getAllDepart();
                if (type == 'edit') {
                    this.nodeModalData.title = '节点编辑';
                    this.nodeModalData.modalType = 'edit';
                    this.setNodeModalData(this.nodeData);
                }else {
                    this.nodeModalData.title = '添加节点';
                    this.nodeModalData.modalType = 'add';
                    this.setNodeModalData({pid: this.nodeData.id});
                }
                this.nodeModalData.isShow = true;
            },
            delDepart() {
                if (!confirm('确认要删除这个节点么？')) {
                    return true;
                }
                _this = this
                $.ajax({
                        url: '/cp/longrentdepartment/ajaxdeletedepart',
                        data: {
                            id:_this.nodeData.id,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        type : 'POST',
                        dataType:'json',
                        success : function(data){
                            alert(data.msg);
                            if (data.code == 0) {
                                console.log() 
                                _this.getData(_this.nodeData.pid, _this.nodeData.pid);
                            }
                        },
                    }); 
            },
            // 获取节点下的用户
            getdepartuser(did) {
                var _this = this;
                $.ajax({
                    url  : '/cp/longrentdepartment/ajaxgetdepartuser',
                    data : {did:did},
                    type : 'GET',
                    success : function(res){
                        _this.nodeData.departuser = res.data
                    },
                });
            },
            // 获取节点下的权限包
            getActionGroup(did) {
                var _this = this;
                $.ajax({
                    url:'/cp/longrentdepartment/ajaxgetactiongroup',
                    data : {did:did},
                    type : 'GET',
                    success : function(data){
                        _this.nodeData.actionGroup = data.data;
                    },
                });
            },
            // 获取节点下的资源包
            getDepartResource(did) {
                var _this = this;
                $.ajax({
                    url:'/cp/longrentdepartment/ajax_get_depart_resource',
                    data : {did:did},
                    type : 'GET',
                    success : function(data){
                        _this.nodeData.departResource = data.data;
                    },
                });
            },
            // 获取父节点信息
            getParentDepart(id) {
                var _this = this;
                $.ajax({
                    url  : '/cp/longrentdepartment/ajaxgetparentdepart',
                    data : {id:id},
                    type : 'GET',
                    async: true,
                    dataType:'json',
                    success : function(data){
                        if(data.code == 0){
                            _this.nodeData.parent_name = data.data.name;
                        }
                    },
                });
            },
            // 获取上级部门列表
            getAllDepart() {
                var _this = this;
                $.ajax({
                    url  : '/cp/longrentdepartment/ajaxgetalldepart',
                    type : 'GET',
                    async: false,
                    dataType:'json',
                    success : function(data){
                        if(data.code == 0){
                            _this.nodeModalData.allDepartList = data.data;
                        }
                    },
                });
            },
            // node节点模态框save
            saveModal() {
                var _this = this;
                if (this.nodeModalData.modalType == 'edit') {
                    $.ajax({
                        url  : '/cp/longrentdepartment/ajaxupdatedepart',
                        data : {
                            id: this.nodeModalData.id,
                            name: this.nodeModalData.name,
                            pid: this.nodeModalData.pid,
                            mark: this.nodeModalData.mark,
                            code: 0,
                            email: this.nodeModalData.email,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        type : 'POST',
                        dataType:'json',
                        success : function(data){
                            alert(data.msg);
                            if(data.code == 0){
                                _this.getData(_this.nodeModalData.pid, _this.nodeModalData.id)
                                _this.setNodeData(_this.nodeModalData);
                                _this.getParentDepart(_this.nodeData.id);
                            }
                        },
                    });
                }else {
                    $.ajax({
                        url: '/cp/longrentdepartment/ajaxadddepart',
                        data: {
                            name: this.nodeModalData.name,
                            pid: this.nodeModalData.pid,
                            mark: this.nodeModalData.mark,
                            email: this.nodeModalData.email,
                            code: 0,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        type : 'POST',
                        dataType:'json',
                        success : function(data){
                            alert(data.msg);
                            if(data.code == 0){
                                _this.getData(_this.nodeModalData.pid, _this.nodeModalData.pid)
                            }
                        },
                    }); 
                }
            },
            // 展开父节点
            expandParent(data, pid, checkId) {
                var _this = this;
                data.forEach(function(v, i) {
                    v.isCheck = false;
                    if (v.id == checkId) {
                        v.isCheck = true;
                    }
                    if (v.id == pid) {
                        _this.$set(v, 'expand', true);
                        _this.expandParent(_this.organizationData, v.parent_id, checkId);
                        new Error("StopForeach");
                    }
                    if (!v.child) {
                        return false;
                    }else {
                        _this.expandParent(v.child, pid, checkId)
                    }
                })
            },
            // node节点模态框cancel
            cancelModal() {
                this.setNodeModalData();
            },
            // 添加用户到部门
            addDepartmentUser() {
                var _this = this;
                if (!this.nodeData.id) {
                    this.$Message.warning({
                        content: '请选择部门',
                    })
                }else if (!this.cp_account) {
                    this.$Message.warning({
                        content: '请输入账号',
                    })
                }else {
                    $.ajax({
                        url:'/cp/longrentdepartment/ajaxadduserbycpaccount',
                        data:{
                            did:this.nodeData.id,
                            cp_account:this.cp_account,
                            _token:$('meta[name="csrf-token"]').attr('content')
                        },
                        dataType:'json',
                        type:"POST",
                        success:function(data){
                            alert(data.msg);
                            if(data.code == 0){
                                _this.getdepartuser(_this.nodeData.id);
                            }
                        }
                    });
                }
            },
            // 页面跳转
            pageJump(type, data) {
                if (type == 'addAdmin') {
                    location.href = '/user/add';
                }else if (type == 'resourceGroup') {
                    window.open('/cp/longrentdepartment/resourcegroupdetail?id=' + data);
                }else if (type == 'resourceTmp') {
                    window.open('/cp/longrentdepartment/depart_resource_detail?id=' + this.nodeData.id)
                }else if (type == 'actionGroup') {
                    window.open('/cp/longrentdepartment/actiongroupaccessdetail?id=' + data);
                }else if (type == 'actionTmp') {
                    // 这里的data指的是project
                    window.open('/cp/longrentdepartment/actionaccessdetail?id=' + this.nodeData.id + '&project=' + data)
                }
            },
            // 删除用户
            delDepartUser(uid) {
                var _this = this;
                $.ajax({
                    url  : '/cp/longrentdepartment/ajaxdeldepartuser',
                    data : {
                        did: this.nodeData.id, 
                        uid: uid,
                        _token:$('meta[name="csrf-token"]').attr('content')
                    },
                    type : 'POST',
                    dataType:'json',
                    success : function(data){
                        alert(data.msg);
                        if(data.code == 0){
                            _this.getdepartuser(_this.nodeData.id);
                        }
                    },
                });
            }
        },
        created() {
            this.getData()
        },
        mounted() {

        }
    })
</script>
<script type="text/javascript">
    // //获取当前节点信息
    // $.ajax({
    //     url  : '/cp/longrentdepartment/ajaxgetdepartinfo',
    //     data : {did:id},
    //     type : 'GET',
    //     async: false,
    //     dataType:'json',
    //     success : function(data){
    //         if(data.code == 0){
    //             $('input[name=node-edit-name]').val(data.data.name);
    //             $('select[name=node-edit-code]').val(data.data.city_id);
    //             $('input[name=node-edit-id]').val(data.data.id);
    //             $('input[name=node-edit-mark]').val(data.data.mark);
    //             $('input[name=node-edit-email]').val(data.data.email);
    //             $('.strt-name[departid='+id+']').find('.user_count').text(data.data.count);
    //         }
    //     },
    // });
    // //获取父节点信息
    // $.ajax({
    //     url  : '/cp/longrentdepartment/ajaxgetparentdepart',
    //     data : {id:id},
    //     type : 'GET',
    //     async: true,
    //     dataType:'json',
    //     success : function(data){
    //         if(data.code == 0){
    //             var opt_html = "<option value='"+ data.data.id +"' >"+ data.data.name +"</option>";
    //             $('select[name=node-edit-pname]').empty();
    //             $('select[name=node-edit-pname]').append(opt_html);
    //             $('input[name=node-edit-pid]').val(data.data.id);
    //         }
    //     },
    // });
</script>
    
@endsection
