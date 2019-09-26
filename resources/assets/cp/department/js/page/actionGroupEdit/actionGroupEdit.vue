<template>
    <div class="page">
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

            </div>
        </div>
    </div>
</template>

<script>
import OrgTree from '../../components/org-tree/index.js'
export default {
    data() {
        return {
            // 当前权限组id
            groupId : 0,

            departmentTree: [],
            departmentTreeConfig : {
                props : {
                    label : 'name',
                    children : 'child',
                    expand : 'isExpand',
                },
                collapsable : false,
                horizontal : true,
            },
        }
    },
    components: {
        OrgTree
    },
    methods: {
        // 获取组织架构树信息
        getDepartmentTree() {
            this.$Request({
                url:`/cp/departments/tree`,
                method:'GET',
                success: (res) => {
                    this.departmentTree = res.data;
                }
            })
        },
        // expandParent(data, pid, checkId) {
        //     var _this = this;
        //     data.forEach(function(v, i) {
        //         v.clickSelect = false;
        //         if (v.id == checkId) {
        //             console.log(v.id, checkId);
                    
        //             v.clickSelect = true;
        //         }
        //         if (v.id == pid) {
        //             _this.$set(v, 'expand', true);
        //             _this.expandParent(_this.departmentTree, v.parent_id, checkId);
        //             new Error("StopForeach");
        //         }
        //         if (!v.child) {
        //             return false;
        //         }else {
        //             _this.expandParent(v.child, pid, checkId)
        //         }
        //     })
        // },
        
        
    },
    created() {

    },
    mounted() {
        this.groupId = this.$route.params.groupId
        this.getDepartmentTree()
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

