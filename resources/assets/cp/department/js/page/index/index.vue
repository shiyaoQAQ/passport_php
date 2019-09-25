<template>
    <div class="page">
        <div class="tree">
            <div>
                <!-- <orgTree 
                    :data="testTree"
                    :collapsable="departmentTreeConfig.collapsable"
                    :horizontal="departmentTreeConfig.horizontal"
                    @on-expand="departmentOnExpand"
                    @on-node-click="departmentOnClick"
                ></orgTree> -->
                <orgTree 
                    v-for="tree,index in departmentTree"
                    :key="index"
                    :data="tree"
                    :props="departmentTreeConfig.props"
                    :collapsable="departmentTreeConfig.collapsable"
                    :horizontal="departmentTreeConfig.horizontal"
                    @on-expand="departmentOnExpand"
                    @on-node-click="departmentOnClick"
                ></orgTree>
            </div>
        </div>
        <div class="detail">
            <div v-if="department != null">
                <Card class='detailElement'>
                    <p slot="title" style="font-size:20px;">{{ department.name }}</p>
                    <p>
                        <div class="departmentInfo">
                            <span>标识：{{ department.mark }}</span>
                            <span>邮箱：{{ department.email }}</span>
                            <span>上级部门：<span v-if="departmentParent != null">{{ departmentParent.name }}</span></span>
                        </div>
                        <div class="departmentOperateList">
                            <Button @click="editDepart" type="info">编辑</Button>
                            <Button @click="addChildDepart" type="info">添加子节点</Button>
                            <Button @click="delDepart" type="error">删除</Button>
                        </div>
                    </p>
                </Card>
                <Card class='detailElement'>
                    <p class="userInputBlock">
                        <Input class="userInput" type="text" v-model="userInput" placeholder="cp账号">
                            <Icon type="ios-person-outline" slot="prepend"></Icon>
                        </Input>
                        <Button @click="addDepartmentUser" type="info">添加用户到部门</Button>
                        <Button @click="addAdminUser" >新增一个管理员</Button>
                    </p>
                    <p class="userListBlock">
                        <Table :columns="departmentUserColumn" :data="departmentUser"></Table>
                    </p>
                </Card>
                <Card class='detailElement'>
                    <p slot="title">权限详情</p>
                    <p>
                        <h4>独立权限</h4>
                        <Collapse v-if="departmentAction.tmp != null">
                            <Panel :name="index + ''" :key="index" v-for="(projectInfo, project, index) in departmentAction.tmp">
                                {{ projectInfo.projectName }} 
                                <Button type='info' size="small" @click="editTmpAction(project)">编辑{{ projectInfo.projectName}}权限</Button>
                                <p slot="content">
                                    <span class="actionGroup" :key="index" v-for="(controllerInfo, controller, index) in  projectInfo.controllerList" >
                                        <span class="actionGroupTitle">{{ controllerInfo.name }}（{{ controller }}）</span>
                                        <span :key="index" v-for="(actionInfo, index) in controllerInfo.actions">
                                            <Tag color="cyan" v-if="actionInfo.desc">{{ actionInfo.desc }}</Tag>
                                        </span>
                                    </span>
                                </p>
                            </Panel>
                        </Collapse>
                    </p>
                    <p style="margin-top:10px;">
                        <h4>权限包</h4>
                        <Collapse v-if="departmentAction.groups != null">
                            <Panel :name="index + ''" :key="index" v-for="(group, index) in departmentAction.groups">
                                {{ group.name }} （{{ group.project }}:{{ group.desc }}） 
                                <Button type='info' size="small" @click="editGroupAction(group.id)">编辑{{ group.name }}</Button>
                                <p slot="content">
                                    <span class="actionGroup" :key="index" v-for="(controllerInfo, controller, index) in  group.actions" >
                                        <span class="actionGroupTitle">{{ controllerInfo.name }}（{{ controller }}）</span>
                                        <span :key="index" v-for="(actionInfo, index) in controllerInfo.actions">
                                            <Tag color="cyan" v-if="actionInfo.desc">{{ actionInfo.desc }}</Tag>
                                        </span>
                                    </span>
                                </p>
                            </Panel>
                        </Collapse>
                    </p>
                </Card>
                <Card>
                    <p slot="title">资源详情</p>
                    <p>
                        <h4>独立资源</h4>
                        <Collapse v-if="departmentResource.tmp != null">
                            <Panel name="1">
                                独立资源
                                <Button type='info' size="small" @click="editTmpResource()">编辑独立资源</Button>
                                <p slot="content">
                                    <span class="actionGroup" :key="index" v-for="(controllerInfo, controller, index) in  departmentResource.tmp" >
                                        <span class="actionGroupTitle">{{ controllerInfo.name }}（{{ controller }}）</span>
                                        <span :key="index" v-for="(resourceInfo, index) in controllerInfo.resource">
                                            <Tag color="cyan" v-if="resourceInfo.desc">{{ resourceInfo.desc }}</Tag>
                                        </span>
                                    </span>
                                </p>
                            </Panel>
                        </Collapse>
                    </p>
                    <p style="margin-top:10px;">
                        <h4>资源包</h4>
                        <Collapse v-if="departmentResource.groups != null">
                            <Panel :name="index + ''" :key="index" v-for="(group, index) in departmentResource.groups">
                                {{ group.name }} （{{ group.desc }}） 
                                <Button type='info' size="small" @click="editGroupResource(group.id)">编辑{{ group.name }}</Button>
                                <p slot="content">
                                    <span class="actionGroup" :key="index" v-for="(controllerInfo, controller, index) in  group.resources" >
                                        <span class="actionGroupTitle">{{ controllerInfo.name }}（{{ controller }}）</span>
                                        <span :key="index" v-for="(resourceInfo, index) in controllerInfo.resource">
                                            <Tag color="cyan" v-if="resourceInfo.desc">{{ resourceInfo.desc }}</Tag>
                                        </span>
                                    </span>
                                </p>
                            </Panel>
                        </Collapse>
                    </p>
                </Card>
                <!-- <Card>{{ department }}</Card>
                <div></div> -->
                <Modal v-model="addDepartmentModal" @on-ok="saveDepartment" :loading="addDepartmentModalConfig.loading" ok-text="保存">
                    <h3 v-if="this.addDepartmentModalConfig.operate == 'addChild'">添加子节点</h3>
                    <h3 v-else>节点编辑</h3>
                    <ul>
                        <li class="modalLI">
                            <span>名称：</span>
                            <Input style="width:300px" v-model="departmentModalData.name"></Input>
                        </li>
                        <li class="modalLI">
                            <span>标识：</span>
                            <Input style="width:300px" v-model="departmentModalData.mark"></Input>
                        </li>
                        <li class="modalLI">
                            <span>邮箱：</span>
                            <Input style="width:300px" v-model="departmentModalData.email"></Input>
                        </li>
                        <li class="modalLI">
                            <span>上级部门：</span>
                            <i-select 
                                style="width:300px" 
                                v-model="departmentModalData.pid"
                                :disabled="this.addDepartmentModalConfig.operate == 'addChild'"
                                >
                                <i-option v-for="itemDepart in allDepartmentList" :value="itemDepart.id" :key="itemDepart.id">{{ itemDepart.name }} </i-option>
                            </i-select>
                        </li>
                    </ul>
                </Modal>
            </div>
        </div>
    </div>
</template>

<script>
import OrgTree from '../../components/org-tree/index.js'
export default {
    data() {
        return {
            departmentTree: [],
            departmentTreeConfig : {
                props : {
                    label : 'name',
                    children : 'child',
                    expand : 'expand',
                },
                collapsable : true,
                horizontal : true,
            },
            // 当前选定的节点信息
            department : null,
            departmentParent : null,
            departmentUser : [],
            departmentAction : {},
            departmentResource : {},
            // 部门用户管理
            userInput : '',
            departmentUserColumn : [
                {
                    title: 'ID',
                    key: 'uid'
                },
                {
                    title: 'CP账户',
                    key: 'cp'
                },
                {
                    title: '姓名',
                    key: 'userName'
                },
                {
                    title: '添加时间',
                    key: 'ctime'
                },
                {
                    title: '添加人',
                    key: 'adminName'
                },
                {
                    title: '操作',
                    key: 'action',
                    width: 150,
                    align: 'center',
                    render: (h, params) => {
                        return h('div', [
                            h('Button', {
                                props: {
                                    type: 'error',
                                    size: 'small'
                                },
                                on: {
                                    click: () => {
                                        // console.log(params);
                                        this.delDepartUser(params.row.uid)
                                    }
                                }
                            }, '删除')
                        ]);
                    }
                },
            ],
            departmentModalData : {
                name : '',
                mark : '',
                email : '',
                pid : '',
            },
            addDepartmentModal : false,
            addDepartmentModalConfig : {
                loading : true,
                operate : null,
            },
            allDepartmentList : [],
        }
    },
    components: {
        OrgTree
    },
    methods: {
        // 获取组织架构树信息
        getDepartmentTree(pid = 1, checkId = null) {
            this.$Request({
                url:`/cp/departments/tree`,
                method:'GET',
                formData : {},
                success: (res) => {
                    this.addAttr(res.data);
                    this.departmentTree = res.data;
                    this.expandParent(this.departmentTree, pid, checkId);
                }
            })
        },
        addAttr(data) {
            data.forEach((v,i) => {
                v.expand = false;
                if (v.child) {
                    this.addAttr(v.child);
                }
            })
        },
        expandParent(data, pid, checkId) {
            var _this = this;
            data.forEach(function(v, i) {
                v.clickSelect = false;
                if (v.id == checkId) {
                    v.clickSelect = true;
                }
                if (v.id == pid) {
                    _this.$set(v, 'expand', true);
                    _this.expandParent(_this.departmentTree, v.parent_id, checkId);
                    new Error("StopForeach");
                }
                if (!v.child) {
                    return false;
                }else {
                    _this.expandParent(v.child, pid, checkId)
                }
            })
        },
        // 获取所有部门信息 以供编辑部门的时候使用
        getAllDepartmentList() {
            var _this = this
            this.$Request({
                url:`/cp/longrentdepartment/ajaxgetalldepart`,
                method:'GET',
                formData : {},
                success: (res) => {
                    _this.allDepartmentList = res.data;
                }
            })
        },
        // 获取节点的父节点
        getDepartmentParent(data) {
            var _this = this
            let did = data.id
            this.departmentParent = null
            this.$Request({
                url : `/cp/departments/` + did + `/parent`,
                method:'GET',
                formData : {},
                success: (res) => {
                    _this.departmentParent= res.data;
                }
            })
        },
        // 获取节点的用户
        getDepartmentUser(data = null) {
            if (data == null) {
                data = this.department
            }
            var _this = this
            let did = data.id
            this.departmentUser = []
            this.$Request({
                url : `/cp/departments/` + did + `/user`,
                method:'GET',
                formData : {},
                success: (res) => {
                    _this.departmentUser= res.data;
                }
            })
        },
        // 获取节点的操作
        getDepartmentAction(data) {
            var _this = this
            let did = data.id
            this.departmentAction = {}
            this.$Request({
                url : `/cp/departments/` + did + `/action`,
                method:'GET',
                formData : {},
                success: (res) => {
                    _this.departmentAction= res.data;
                }
            })
        },
        // 获取节点的资源
        getDepartmentResource(data) {
            var _this = this
            let did = data.id
            this.departmentResource = {}
            this.$Request({
                url : `/cp/departments/` + did + `/resource`,
                method:'GET',
                formData : {},
                success: (res) => {
                    _this.departmentResource= res.data;
                }
            })
            
        },
        // 编辑部门信息
        editDepart() {
            this.departmentModalData = {
                name : this.department.name,
                mark : this.department.mark,
                email : this.department.email,
                pid : parseInt(this.department.parent_id),
                id : this.department.id
            },
            this.addDepartmentModalConfig.operate = 'edit'
            this.addDepartmentModal = true
        },
        // 增加子部门
        addChildDepart() {
            this.departmentModalData = {
                name : '',
                mark : '',
                email : '',
                pid : this.department.id,
            },
            this.addDepartmentModalConfig.operate = 'addChild'
            this.addDepartmentModal = true
        },
        // 保存部门信息
        saveDepartment() {
            if (this.addDepartmentModalConfig.operate == 'addChild') {
                this.storeChildDepart()
            } else if (this.addDepartmentModalConfig.operate == 'edit') {
                this.updateDepart()
            }
        },
        updateDepart() {
            let _this = this
            this.$Request({
                url  : '/cp/longrentdepartment/ajaxupdatedepart',
                data : {
                    id: this.departmentModalData.id,
                    name: this.departmentModalData.name,
                    pid: this.departmentModalData.pid,
                    mark: this.departmentModalData.mark,
                    code: 0,
                    email: this.departmentModalData.email,
                },
                method : 'POST',
                success : function(data) {
                    if (data.code == 0) {
                        _this.$Message.success(data.msg)
                        _this.getDepartmentTree(_this.departmentModalData.pid, _this.departmentModalData.id)
                        // 更新当前节点信息 这里还是不要请求后台了 提升性能
                        _this.department.name = _this.departmentModalData.name
                        _this.department.mark = _this.departmentModalData.mark
                        _this.department.email = _this.departmentModalData.email
                        if (_this.department.parent_id != _this.departmentModalData.pid + '') {
                            _this.department.parent_id = _this.departmentModalData.pid + ''
                            _this.getDepartmentParent(_this.department)
                        }
                        _this.addDepartmentModalConfig.loading = false
                        _this.addDepartmentModal = false
                        _this.$nextTick(() => { _this.addDepartmentModalConfig.loading = true; })
                    } else {
                        _this.$nextTick(() => { _this.addDepartmentModalConfig.loading = true; })
                    }
                },
            });
        },
        storeChildDepart() {
            let _this = this
            this.$Request({
                url: '/cp/longrentdepartment/ajaxadddepart',
                data: {
                    name: this.departmentModalData.name,
                    pid: this.departmentModalData.pid,
                    mark: this.departmentModalData.mark,
                    email: this.departmentModalData.email,
                    code: 0,
                },
                method : 'POST',
                success : function(data) {
                    if(data.code == 0) {
                        _this.$Message.success(data.msg);
                        _this.getDepartmentTree(_this.departmentModalData.pid, _this.departmentModalData.pid)
                        _this.addDepartmentModalConfig.loading = false
                        _this.addDepartmentModal = false
                        _this.$nextTick(() => { _this.addDepartmentModalConfig.loading = true; })
                    } else {
                        _this.$nextTick(() => { _this.addDepartmentModalConfig.loading = true; })
                    }
                },
            }); 
        },
        // 删除部门
        delDepart() {
            if (!confirm('确认要删除这个部门么？')) {
                return true;
            }
            let _this = this
            this.$Request({
                url: '/cp/longrentdepartment/ajaxdeletedepart',
                data: {
                    id:_this.department.id,
                    // _token: $('meta[name="csrf-token"]').attr('content')
                },
                method : 'POST',
                dataType:'json',
                success : function(data){
                    if (data.code == 0) {
                        _this.$Message.success(data.msg);
                        _this.getDepartmentTree(_this.department.parent_id, _this.department.parent_id);
                        _this.unselectedDepartment()
                    }
                },
            }); 
        },
        // 取消节点选择
        unselectedDepartment() {
            this.department = null
            this.departmentParent = null
            this.departmentUser = []
            this.departmentAction = {}
            this.departmentResource = {}
        },
        // 部门节点展开事件
        departmentOnExpand(e, data) {
            if ("expand" in data) {
                data.expand = !data.expand;
                if (!data.expand && data.children) {
                    this.collapse(data.children);
                }
            } else {
                this.$set(data, "expand", true);
            }
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
        // 部门节点点击事件
        departmentOnClick(e, data) {
            // console.log(data);
            // 幂等性处理
            if (this.department == data) {
                return;
            }
            this.department = data
            // 获取部门相关信息
            this.getDepartmentParent(data)
            this.getDepartmentUser(data)
            this.getDepartmentAction(data)
            this.getDepartmentResource(data)
            $('.org-tree-node-label-inner').removeClass('org-tree-node-label-inner-check')
            e.target.className += ' org-tree-node-label-inner-check'
        },
        // 编辑独立权限
        editTmpAction(project) {
            window.open('/cp/longrentdepartment/actionaccessdetail?id=' + this.department.id + '&project=' + project)
        },
        // 编辑组权限
        editGroupAction(groupid) {
            window.open('/cp/longrentdepartment/actiongroupaccessdetail?id=' + groupid);
        },
        // 编辑独立资源
        editTmpResource() {
            window.open('/cp/longrentdepartment/depart_resource_detail?id=' + this.department.id)
        },
        // 编辑组资源
        editGroupResource(groupid) {
            window.open('/cp/longrentdepartment/resourcegroupdetail?id=' + groupid);
        },
        // 添加管理员
        addAdminUser() {
            window.open('/cp/user/add');
        },
        // 添加用户到部门
        addDepartmentUser() {
            var _this = this;
            if (!this.department.id) {
                this.$Message.warning({
                    content: '请选择部门',
                })
            }else if (!this.userInput) {
                this.$Message.warning({
                    content: '请输入账号',
                })
            }else {
                _this.$Request({
                    url: '/cp/longrentdepartment/ajaxadduserbycpaccount',
                    method:'post',
                    data:{
                        did : _this.department.id,
                        cp_account : _this.userInput,
                        // _token : $('meta[name="csrf-token"]').attr('content')
                    },
                    success: (res) => {
                        if (res.code == 0) {
                            _this.$Message.success('保存成功');
                            _this.getDepartmentUser();
                        }
                    }
                })
            }
        },
        // 删除用户
        delDepartUser(uid) {
            var _this = this;
            if (!confirm('您是否要删除此用户')) {
                return
            }
            _this.$Request({
                url  : '/cp/longrentdepartment/ajaxdeldepartuser',
                data : {
                    did: this.department.id, 
                    uid: uid,
                    // _token:$('meta[name="csrf-token"]').attr('content')
                },
                method : 'POST',
                dataType:'json',
                success : function(data){
                    if (data.code == 0) {
                        _this.$Message.success('删除成功');
                        _this.getDepartmentUser();
                    }
                },
            });
        },
        
    },
    created() {

    },
    mounted() {
        this.getDepartmentTree()
        this.getAllDepartmentList()
    }
}

</script>


<style lang="less" scoped>
.page {
    min-height: 100rem;
    .tree {
        float:left;
        width: 40%;
        background-color: #fff;
    }
    .detail {
        float:right;
        width: 60%;
        padding-left: 15px;

        .departmentInfo {
            span {
                display: inline-block;
                min-width: 120px;
                margin-right: 10px;
            }
        }
        .departmentOperateList {
            margin-top:10px;
            Button {
                margin:5px;
            }
        }

        .detailElement {
            margin-bottom : 15px;
        }

        .userInputBlock {
            overflow:hidden;
            .userInput {
                float: left;
                width: 30%;
            }
            Button {
                float: left;
                margin-left: 10px;
            }
            margin-bottom: 20px;
        }

        .actionGroup {
            padding: 5px;
            display: block;
            .actionGroupTitle {
                display: block;
                font-size: 12px;
            }
        }

        
        
    }
}
</style>

