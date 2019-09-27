<template>
    <div class="page">
        <!-- 组织架构树 -->
        <div class="tree">
            <div>
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
        <!-- 组织架构树-部门详情 -->
        <div class="detail">
            <div v-if="department != null">
                <Card class='detailElement'>
                    <p slot="title" style="font-size:20px;">{{ department.name }}</p>
                    <p>
                        <div class="departmentInfo">
                            <div class="department_info_item">
                                <Input v-model="department.mark" readonly>
                                    <span slot="prepend">标识：</span>
                                </Input>
                            </div>
                            <div class="department_info_item">
                                <Input v-model="department.email" readonly>
                                    <span slot="prepend">邮箱：</span>
                                </Input>
                            </div>
                            <div class="department_info_item">
                                <Input v-model="departmentParent.name" readonly>
                                    <span slot="prepend">上级部门：</span>
                                </Input>
                            </div>
                        </div>
                        <div class="departmentOperateList">
                            <Button @click="editDepart" type="info">编辑</Button>
                            <Button @click="addChildDepart" type="info">添加子节点</Button>
                            <Button @click="delDepart" type="error">删除</Button>
                        </div>
                    </p>
                </Card>
                <Card class='detailElement'>
                    <div class="userInputBlock">
                        <div class="user_input">
                            <Input type="text" v-model="userInput" placeholder="cp账号">
                                <Icon type="ios-person-outline" slot="prepend"></Icon>
                            </Input>
                        </div>
                        <Button @click="addDepartmentUser" type="info">添加用户到部门</Button>
                        <Button @click="addAdminUser" >新增一个管理员</Button>
                    </div>
                    <p class="userListBlock">
                        <Table :columns="departmentUserColumn" :data="departmentUser"></Table>
                    </p>
                </Card>
                <Card class='detailElement'>
                    <p slot="title">权限详情</p>
                    <div class="tmp" v-if="departmentAction.tmp">
                        <h4>独立权限</h4>
                        <Collapse>
                            <Panel
                                v-for="(projectInfo, project, index) in departmentAction.tmp"
                                :name="index + ''"
                                :key="index" >
                                {{ projectInfo.projectName }} 
                                <Button
                                    type='info'
                                    size="small"
                                    @click="editTmpAction(project)">
                                    编辑{{ projectInfo.projectName}}权限
                                </Button>
                                <div slot="content">
                                    <table>
                                        <tr
                                            class="actionGroup"
                                            v-for="(controllerInfo, controller, index) in  projectInfo.controllerList"
                                            :key="index">
                                            <td>
                                                <p class="group_title">{{ controllerInfo.name }}（{{ controller }}）</p>
                                                <div
                                                    class="group_tag"
                                                    v-for="(actionInfo, index) in controllerInfo.actions"
                                                    :key="index">
                                                    <Tag color="cyan" v-if="actionInfo.desc">{{ actionInfo.desc }}</Tag>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </Panel>
                        </Collapse>
                    </div>
                    <div class="groups" v-if="departmentAction.groups && departmentAction.groups.length">
                        <h4>权限包</h4>
                        <Collapse>
                            <Panel
                                v-for="(group, index) in departmentAction.groups"
                                :name="index + ''"
                                :key="index">
                                {{ group.name }} （{{ group.project }}:{{ group.desc }}） 
                                <Button
                                    type='info'
                                    size="small"
                                    @click="editGroupAction(group.id)">
                                    编辑{{ group.name }}
                                </Button>
                                <div slot="content">
                                    <table>
                                        <tr
                                            class="actionGroup"
                                            v-for="(controllerInfo, controller, index) in group.actions"
                                            :key="index">
                                            <td>
                                                <p class="group_title">{{ controllerInfo.name }}（{{ controller }}）</p>
                                                <div
                                                    class="group_tag"
                                                    v-for="(actionInfo, index) in controllerInfo.actions"
                                                    :key="index">
                                                    <Tag color="cyan" v-if="actionInfo.desc">{{ actionInfo.desc }}</Tag>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </Panel>
                        </Collapse>
                    </div>
                </Card>
                <Card class="detailElement">
                    <p slot="title">资源详情</p>
                    <div class="tmp" v-if="departmentResource.tmp != null">
                        <h4>独立资源</h4>
                        <Collapse>
                            <Panel name="1">
                                独立资源
                                <Button
                                    type='info'
                                    size="small"
                                    @click="editTmpResource()">
                                    编辑独立资源
                                </Button>
                                <div slot="content">
                                    <table>
                                        <tr
                                            class="actionGroup"
                                            v-for="(controllerInfo, controller, index) in  departmentResource.tmp"
                                            :key="index">
                                            <td>
                                                <p class="group_title">{{ controllerInfo.name }}（{{ controller }}）</p>
                                                <div
                                                    class="group_tag"
                                                    v-for="(resourceInfo, index) in controllerInfo.resource"
                                                    :key="index">
                                                    <Tag color="cyan" v-if="resourceInfo.desc">{{ resourceInfo.desc }}</Tag>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </Panel>
                        </Collapse>
                    </div>
                    <div class="groups" v-if="departmentResource.groups && departmentResource.groups.length">
                        <h4>资源包</h4>
                        <Collapse>
                            <Panel
                                v-for="(group, index) in departmentResource.groups"
                                :name="index + ''"
                                :key="index">
                                {{ group.name }} （{{ group.desc }}） 
                                <Button
                                    type='info'
                                    size="small"
                                    @click="editGroupResource(group.id)">
                                    编辑{{ group.name }}
                                </Button>
                                <div slot="content">
                                    <table>
                                        <tr
                                            class="actionGroup"
                                            v-for="(controllerInfo, controller, index) in  group.resources"
                                            :key="index">
                                            <td>
                                                <p class="group_title">{{ controllerInfo.name }}（{{ controller }}）</p>
                                                <div
                                                    class="group_tag"
                                                    v-for="(resourceInfo, index) in controllerInfo.resource"
                                                    :key="index">
                                                    <Tag color="cyan" v-if="resourceInfo.desc">{{ resourceInfo.desc }}</Tag>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </Panel>
                        </Collapse>
                    </div>
                </Card>
            </div>
        </div>
        <!-- 部门编辑-modal -->
        <Modal
            class-name="depart_modal"
            v-model="addDepartmentModal"
            :title="addDepartmentModalConfig.operate == 'addChild' ? '添加子节点' : '节点编辑'"
            :loading="addDepartmentModalConfig.loading"
            ok-text="保存"
            @on-ok="saveDepartment">
            <ul>
                <li class="modal_li">
                    <span class="modal_li_title">名称：</span>
                    <Input style="width:300px" v-model="departmentModalData.name"></Input>
                </li>
                <li class="modal_li">
                    <span class="modal_li_title">标识：</span>
                    <Input style="width:300px" v-model="departmentModalData.mark"></Input>
                </li>
                <li class="modal_li">
                    <span class="modal_li_title">邮箱：</span>
                    <Input style="width:300px" v-model="departmentModalData.email"></Input>
                </li>
                <li class="modal_li">
                    <span class="modal_li_title">上级部门：</span>
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
                    expand : 'isExpand',
                },
                collapsable : true,
                horizontal : true,
            },
            // 当前选定的节点信息
            department : null,
            departmentParent : {},
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
            $.ajax({
                url:`/cp/departments/tree`,
                type:'GET',
                success: (res) => {
                    this.departmentTree = res.data;
                    this.dataFormatExpand(this.departmentTree, pid, checkId);
                }
            })
        },
        // 获取所有部门信息 以供编辑部门的时候使用
        getAllDepartmentList() {
            this.$Request({
                url:`/cp/longrentdepartment/ajaxgetalldepart`,
                type:'GET',
                success: (res) => {
                    this.allDepartmentList = res.data;
                }
            })
        },
        // 获取节点的父节点
        getDepartmentParent(data) {
            this.departmentParent = {}
            this.$Request({
                url : `/cp/departments/${data.id}/parent`,
                type:'GET',
                success: (res) => {
                    this.departmentParent = res.data;
                }
            })
        },
        // 获取节点的用户
        getDepartmentUser(data = null) {
            if (data == null) {
                data = this.department
            }
            this.departmentUser = []
            this.$Request({
                url : `/cp/departments/${data.id}/user`,
                type:'GET',
                success: (res) => {
                    this.departmentUser= res.data;
                }
            })
        },
        // 获取节点的操作
        getDepartmentAction(data) {
            this.departmentAction = {}
            this.$Request({
                url : `/cp/departments/${data.id}/action`,
                type:'GET',
                success: (res) => {
                    this.departmentAction= res.data;
                }
            })
        },
        // 获取节点的资源
        getDepartmentResource(data) {
            this.departmentResource = {}
            this.$Request({
                url : `/cp/departments/${data.id}/resource`,
                type:'GET',
                success: (res) => {
                    this.departmentResource= res.data;
                }
            })
        },
        // 获取部门相关信息
        getDepartHandle(data) {
            this.getDepartmentParent(data)
            this.getDepartmentUser(data)
            this.getDepartmentAction(data)
            this.getDepartmentResource(data)
        },
        // 数据格式处理 ---- 
        dataFormatExpand(data, pid, checkId) {
            data.forEach((v, i) => {
                v.isChecked = 0;
                if (v.id == checkId) {
                    v.isChecked = 1;
                }
                if (v.id == pid) {
                    this.$set(v, 'isExpand', 1);
                    this.dataFormatExpand(this.departmentTree, v.parent_id, checkId);
                    new Error("StopForeach");
                }
                if (!v.child) {
                    return false;
                }else {
                    this.dataFormatExpand(v.child, pid, checkId)
                }
            })
        },
        // 数据格式处理 ---- end
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
            this.$Request({
                url: '/cp/longrentdepartment/ajaxupdatedepart',
                data: {
                    id: this.departmentModalData.id,
                    name: this.departmentModalData.name,
                    pid: this.departmentModalData.pid,
                    mark: this.departmentModalData.mark,
                    code: 0,
                    email: this.departmentModalData.email,
                },
                type: 'POST',
                success: (data) => {
                    if (data.code == 0) {
                        this.$Message.success(data.msg)
                        this.getDepartmentTree(this.departmentModalData.pid, this.departmentModalData.id)
                        // 更新当前节点信息 这里还是不要请求后台了 提升性能
                        this.department.name = this.departmentModalData.name
                        this.department.mark = this.departmentModalData.mark
                        this.department.email = this.departmentModalData.email
                        if (this.department.parent_id != this.departmentModalData.pid + '') {
                            this.department.parent_id = this.departmentModalData.pid + ''
                            this.getDepartmentParent(this.department)
                        }
                        this.addDepartmentModalConfig.loading = false
                        this.addDepartmentModal = false
                        this.$nextTick(() => { this.addDepartmentModalConfig.loading = true; })
                    } else {
                        this.$nextTick(() => { this.addDepartmentModalConfig.loading = true; })
                    }
                },
            });
        },
        storeChildDepart() {
            this.$Request({
                url: '/cp/longrentdepartment/ajaxadddepart',
                data: {
                    name: this.departmentModalData.name,
                    pid: this.departmentModalData.pid,
                    mark: this.departmentModalData.mark,
                    email: this.departmentModalData.email,
                    code: 0,
                },
                type: 'POST',
                success: (data) => {
                    if(data.code == 0) {
                        this.$Message.success(data.msg);
                        this.getDepartmentTree(this.departmentModalData.pid, this.departmentModalData.pid)
                        this.getAllDepartmentList()
                        this.addDepartmentModalConfig.loading = false
                        this.addDepartmentModal = false
                        this.$nextTick(() => { this.addDepartmentModalConfig.loading = true; })
                    } else {
                        this.$nextTick(() => { this.addDepartmentModalConfig.loading = true; })
                    }
                },
            }); 
        },
        // 删除部门
        delDepart() {
            if (!confirm('确认要删除这个部门么？')) {
                return true;
            }
            this.$Request({
                url: '/cp/longrentdepartment/ajaxdeletedepart',
                data: {
                    id: this.department.id,
                },
                type: 'POST',
                dataType:'json',
                success: (data) => {
                    if (data.code == 0) {
                        this.$Message.success(data.msg);
                        this.getDepartmentTree(this.department.parent_id, this.department.parent_id);
                        this.department = this.departmentParent;
                        this.getDepartHandle(this.departmentParent)
                    }
                },
            }); 
        },
        // 编辑独立权限
        editTmpAction(project) {
            // window.open('/cp/longrentdepartment/actionaccessdetail?id=' + this.department.id + '&project=' + project)
            this.$router.push({
                name : "departmentActionEdit",
                params : {
                    did : this.department.id,
                },
                query : {
                    project : project,
                },
            })
        },
        // 编辑组权限
        editGroupAction(groupid) {
            // window.open('/cp/longrentdepartment/actiongroupaccessdetail?id=' + groupid);
            this.$router.push({
                name : "actionGroupEdit",
                params : {
                    groupId : groupid,
                },
            })
        },
        // 编辑独立资源
        editTmpResource() {
            this.$router.push({
                name : "departmentResourceEdit",
                params : {
                    did : this.department.id,
                },
            })
            // window.open('/cp/longrentdepartment/depart_resource_detail?id=' + this.department.id)
        },
        // 编辑组资源
        editGroupResource(groupid) {
            // window.open('/cp/longrentdepartment/resourcegroupdetail?id=' + groupid);
            this.$router.push({
                name : "resourceGroupEdit",
                params : {
                    groupId : groupid,
                },
            })
        },
        // 添加管理员
        addAdminUser() {
            window.open('/cp/user/add');
        },
        // 添加用户到部门
        addDepartmentUser() {
            if (!this.department.id) {
                this.$Message.warning({
                    content: '请选择部门',
                })
            }else if (!this.userInput) {
                this.$Message.warning({
                    content: '请输入账号',
                })
            }else {
                this.$Request({
                    url: '/cp/longrentdepartment/ajaxadduserbycpaccount',
                    type:'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        did : this.department.id,
                        cp_account : this.userInput,
                    },
                    success: (res) => {
                        if (res.code == 0) {
                            this.$Message.success('保存成功');
                            this.getDepartmentUser();
                        }
                    }
                })
            }
        },
        // 删除用户
        delDepartUser(uid) {
            if (!confirm('您是否要删除此用户')) {
                return
            }
            this.$Request({
                url  : '/cp/longrentdepartment/ajaxdeldepartuser',
                data : {
                    did: this.department.id, 
                    uid: uid,
                },
                type : 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:'json',
                success: (data) =>{
                    if (data.code == 0) {
                        this.$Message.success('删除成功');
                        this.getDepartmentUser();
                    }
                },
            });
        },
        // tree-handle ----
        // 部门节点展开事件
        departmentOnExpand(e, data) {
            if ("isExpand" in data) {
                data.isExpand = data.isExpand ? 0 : 1;
                if (!data.isExpand && data.children) {
                    this.collapse(data.children);
                }
            } else {
                this.$set(data, "isExpand", 1);
            }
        },
        collapse(list) {
            list.forEach((child) => {
                if (child.isExpand) {
                    child.isExpand = 0;
                }
                child.children && this.collapse(child.children);
            });
        },
        // 部门节点点击事件
        departmentOnClick(e, data) {
            // 幂等性处理
            if (this.department == data) {
                return;
            }
            this.department = data
            this.dataFormatExpand(this.departmentTree, data.parent_id, data.id)
            // 获取部门相关信息
            this.getDepartHandle(data)
        },
        // tree-handle ---- end
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
    height: calc(100vh - 70px);
    .tree {
        float:left;
        width: 50%;
        background-color: #fff;
        overflow: scroll;
        height: 100%;
    }
    .detail {
        float:right;
        width: 50%;
        padding-left: 15px;
        overflow: scroll;
        height: 100%;
        .departmentInfo {
            .department_info_item {
                display: inline-block;
            }
            .ivu-input-wrapper {
                width: 200px;
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
            .tmp, .groups {
                table {
                    width: 100%;
                    border-collapse: collapse;
                    td {
                        padding: 5px;
                        border-color: #eee;
                        text-align: left;
                        .group_tag {
                            display: inline-block;
                            margin-right: 5px;
                            margin-bottom: 3px;
                        }
                    }
                }
            }
            .tmp {
                margin-bottom: 10px;
            }
        }

        .userInputBlock {
            .user_input {
                width: 200px;
                display: inline-block;
                vertical-align: top;
            }
            Button {
                margin-left: 10px;
                vertical-align: top;
            }
            margin-bottom: 20px;
        }
    }
}
.depart_modal {
    ul {
        .modal_li {
            margin-bottom: 10px;
            .modal_li_title {
                display: inline-block;
                width: 100px;
                text-align: right;
            }
        }
    }
}
</style>

