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
                    :data="departmentTree"
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
                            <Button type="info">编辑</Button>
                            <Button type="info">添加子节点</Button>
                            <Button type="error">删除</Button>
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
                            <Panel :name="index + ''" v-for="(projectInfo, project, index) in departmentAction.tmp">
                                {{ projectInfo.projectName }} 
                                <Button type='info' size="small" @click="editTmpAction(project)">编辑{{ projectInfo.projectName}}权限</Button>
                                <p slot="content">
                                    <span class="actionGroup" v-for="(controllerInfo, controller) in  projectInfo.controllerList" >
                                        <span class="actionGroupTitle">{{ controllerInfo.name }}（{{ controller }}）</span>
                                        <span v-for="(actionInfo) in controllerInfo.actions">
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
                            <Panel :name="index + ''" v-for="(group, index) in departmentAction.groups">
                                {{ group.name }} （{{ group.project }}:{{ group.desc }}） 
                                <Button type='info' size="small" @click="editGroupAction(group.id)">编辑{{ group.name }}</Button>
                                <p slot="content">
                                    <span class="actionGroup" v-for="(controllerInfo, controller) in  group.actions" >
                                        <span class="actionGroupTitle">{{ controllerInfo.name }}（{{ controller }}）</span>
                                        <span v-for="(actionInfo) in controllerInfo.actions">
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
                                    <span class="actionGroup" v-for="(controllerInfo, controller) in  departmentResource.tmp" >
                                        <span class="actionGroupTitle">{{ controllerInfo.name }}（{{ controller }}）</span>
                                        <span v-for="(resourceInfo) in controllerInfo.resource">
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
                            <Panel :name="index + ''" v-for="(group, index) in departmentResource.groups">
                                {{ group.name }} （{{ group.desc }}） 
                                <Button type='info' size="small" @click="editGroupResource(group.id)">编辑{{ group.name }}</Button>
                                <p slot="content">
                                    <span class="actionGroup" v-for="(controllerInfo, controller) in  group.resources" >
                                        <span class="actionGroupTitle">{{ controllerInfo.name }}（{{ controller }}）</span>
                                        <span v-for="(resourceInfo) in controllerInfo.resource">
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

            </div>
        </div>
    </div>
</template>

<script>
import OrgTree from '../../components/org-tree/index.js'
export default {
    data() {
        return {
            departmentTree : {},
            departmentTreeConfig : {
                props : {
                    label : 'name',
                    children : 'child',
                },
                collapsable : false,
                horizontal : 'horizontal',
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
                            // h('Button', {
                            //     props: {
                            //         type: 'primary',
                            //         size: 'small'
                            //     },
                            //     style: {
                            //         marginRight: '5px'
                            //     },
                            //     on: {
                            //         click: () => {
                            //             // this.show(params.index)
                            //         }
                            //     }
                            // }, 'View'),
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
            ]
        }
    },
    components: {
        OrgTree
    },
    methods: {
        // 获取组织架构树信息
        getDepartmentTree() {
            var _this = this
            this.$Request({
                url:`/cp/departments/tree`,
                method:'GET',
                formData : {},
                success: (res) => {
                    _this.departmentTree= res.data[0];
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
        // 部门节点展开事件
        departmentOnExpand() {

        },
        // 部门节点点击事件
        departmentOnClick(e, data) {
            // console.log(data);
            // 幂等性处理
            if (this.department == data) {
                return
            }
            this.department = data
            // 获取部门相关信息
            this.getDepartmentParent(data)
            this.getDepartmentUser(data)
            this.getDepartmentAction(data)
            this.getDepartmentResource(data)
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
        }
    },
    created() {

    },
    mounted() {
        this.getDepartmentTree()
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

