<template>
    <div class="page">
        <div class="tree">
            <div class="content_options">
                <Button
                    @click="saveDepartment"
                    :loading="saveDepartmentLoading"
                    class="saveDepartment"
                    type="primary">
                    保存部门
                </Button>
            </div>
            <div class="content">
                <orgTree 
                    v-for="tree,index in departmentTree"
                    :key="index"
                    :data="tree"
                    :props="departmentTreeConfig.props"
                    :collapsable="departmentTreeConfig.collapsable"
                    :horizontal="departmentTreeConfig.horizontal"
                    @on-node-click="departmentOnClick">
                </orgTree>
            </div>
        </div>
        <div class="detail">
            <Card class='content detailElement'>
                <p slot="title">
                    <span v-if="groupDetail != null">{{ groupDetail.name }}（{{ groupDetail.desc }}）</span>
                </p>
                <Collapse v-model="actionCollapse" v-if="actionList.length">
                    <Panel
                        v-for="(controllerInfo, index) in actionList"
                        :name="controllerInfo.controller"
                        :key="index">
                        <span class="actionGroupTitle">
                            {{ controllerInfo.desc }}（{{ controllerInfo.controller }}）
                        </span>
                        <p slot="content">
                            <span
                                v-for="(actionInfo, index) in controllerInfo.action"
                                :key="index">
                                <Button
                                    class="actionButton"
                                    @click="changeAction(actionInfo)"
                                    size="small"
                                    :type="actionInfo.isChecked == 1 ? 'info' : 'default'">
                                    {{ actionInfo.desc }}
                                </Button>
                            </span>
                        </p>
                    </Panel>
                </Collapse>
            </Card>
            <div class="content_options">
                <Button
                    @click="saveAction"
                    :loading="saveActionLoading"
                    class="saveAction"
                    type="primary">
                    保存权限
                </Button>
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
            groupId: 0,
            groupDetail: null,
            departmentTree: [],
            departmentTreeConfig: {
                props: {
                    label: 'name',
                    children: 'child',
                    expand: 'isExpand',
                },
                collapsable: false,
                horizontal: true,
            },
            saveDepartmentLoading: false,
            saveActionLoading: false,
            actionList: [],
            actionCollapse: []
        }
    },
    components: {
        OrgTree
    },
    methods: {
        // 获取组织架构树信息
        getDepartmentTree() {
            $.ajax({
                url:`/cp/departments/actionGroup/` + this.groupId + `/tree`,
                type:'GET',
                success: (res) => {
                    this.departmentTree = res.data;
                }
            })
        },
        // 获取组信息
        getActionGroupDetail() {
            $.ajax({
                url:`/cp/departments/actionGroup/` + this.groupId + `/detail`,
                type:'GET',
                success: (res) => {
                    this.groupDetail = res.data;
                }
            })
        },
        // 获取操作列表
        getGroupActionList() {
            $.ajax({
                url:`/cp/departments/actionGroup/` + this.groupId + `/action`,
                type:'GET',
                success: (res) => {
                    res.data.action_list.forEach((v, i) => {
                        this.actionCollapse.push(v.controller);
                    })
                    this.actionList = res.data.action_list;
                }
            })
        },
        departmentOnClick (e, data) {
            // 进行选择或反选
            data.isChecked = 1 - data.isChecked            
        },
        saveDepartment () {
            // 递归计算变更
            // 部门树变更
            this.saveDepartmentLoading = true
            let departmentIncrease = []
            let departmentReduce = []
            let _this = this
            this.operateTree(this.departmentTree, function (node) {
                if (node.isChecked != node.originIsChecked) {
                    if (node.isChecked == 1) {
                        departmentIncrease.push(node.id)
                    } else {
                        departmentReduce.push(node.id)
                    }
                }
            })
            if (departmentIncrease.length == 0 && departmentReduce.length == 0) {
                _this.$Message.error('您没有任何变更啊')
                _this.saveDepartmentLoading = false
                return
            }

            // 调用更新接口
            $.ajax({
                url  : '/cp/departments/actionGroup/' + _this.groupId + '/department',
                data : {
                    departmentIncrease : departmentIncrease,
                    departmentReduce : departmentReduce,
                },
                type : 'PUT',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success : function(res) {
                    if (res.code == 0) {
                        _this.$Message.success({
                            title: '',
                            content: res.msg,
                        });
                        _this.getDepartmentTree()
                    } else {
                        _this.$Message.error({
                            title: '',
                            content: '保存失败！错误信息：' + res.msg + res.code,
                        });
                    }
                    _this.saveDepartmentLoading = false
                },
                error : function(res) {
                    _this.saveDepartmentLoading = false
                    _this.$Message.error({
                        title: '',
                        content: '网络错误',
                    });
                }
            });
        },
        // 树递归方法
        operateTree(treeNodeList, callbackFunc) {
            treeNodeList.forEach((v, i) => {
                callbackFunc(v)
                if (v.child) {
                    this.operateTree(v.child, callbackFunc)
                }
            })
        },
        // 切换权限
        changeAction(actionInfo) {
            actionInfo.isChecked = 1 - actionInfo.isChecked
        },
        // 保存权限
        saveAction() {
            // 递归计算变更
            // 部门树变更
            this.saveActionLoading = true
            let actionIncrease = []
            let actionReduce = []
            let _this = this
            this.actionList.forEach((controller, i) => {
                if (controller.action) {
                    controller.action.forEach((action, ai) => {
                        if (action.isChecked != action.originIsChecked) {
                            if (action.isChecked == 1) {
                                actionIncrease.push(action.controller + '-' +  action.action)
                            } else {
                                actionReduce.push(action.controller + '-' +  action.action)
                            }
                        }
                    })
                }
            })
            if (actionIncrease.length == 0 && actionReduce.length == 0) {
                _this.$Message.error('您没有任何变更啊')
                _this.saveActionLoading = false
                return
            }

            // 调用更新接口
            $.ajax({
                url  : '/cp/departments/actionGroup/' + _this.groupId + '/action',
                data : {
                    actionIncrease : actionIncrease,
                    actionReduce : actionReduce,
                },
                type : 'PUT',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success : function(res) {
                    if (res.code == 0) {
                        _this.$Message.success({
                            title: '',
                            content: res.msg,
                        });
                        _this.getGroupActionList()
                    } else {
                        _this.$Message.error({
                            title: '',
                            content: '保存失败！错误信息：' + res.msg + res.code,
                        });
                    }
                    _this.saveActionLoading = false
                },
                error : function(res) {
                    _this.saveActionLoading = false
                    _this.$Message.error({
                        title: '',
                        content: '网络错误',
                    });
                }
            });
        }

        
    },
    created() {

    },
    mounted() {
        this.groupId = this.$route.params.groupId
        this.getDepartmentTree()
        this.getActionGroupDetail()
        this.getGroupActionList()
    }
}

</script>


<style lang="less" scoped>
.page {
    height: calc(100vh - 70px);
    .tree, .detail {
        width: 50%;
        position: relative;
        height: 100%;
        .content_options {
            height: 50px;
            line-height: 50px;
            background-color: #fff;
            background: linear-gradient(rgba(255, 255, 255, 0.38), #fff);
            position: absolute;
            bottom: 16px;
            z-index: 1000;
            text-align: center;
            Button {
                margin: 10px 10px;
            }
        }
        .content {
            overflow: scroll;
            height: 100%;
            padding-bottom: 50px;
        }
    }
    .tree {
        float:left;
        background-color: #fff;
        .content_options {
            width: 100%;
            left: 0px;
        }
        // position:relative;
        // .saveDepartment {
        //     position: absolute;
        // }
    }
    .detail {
        float:right;
        padding-left: 15px;
        .content_options {
            width: calc(100% - 15px);
            left: 15px;
        }
        .actionButton {
            margin:5px;
        }
        // .departmentInfo {
        //     span {
        //         display: inline-block;
        //         min-width: 120px;
        //         margin-right: 10px;
        //     }
        // }
        // .departmentOperateList {
        //     margin-top:10px;
        //     Button {
        //         margin:5px;
        //     }
        // }



        // .userInputBlock {
        //     overflow:hidden;
        //     .userInput {
        //         float: left;
        //         width: 30%;
        //     }
        //     Button {
        //         float: left;
        //         margin-left: 10px;
        //     }
        //     margin-bottom: 20px;
        // }

        // .actionGroup {
        //     padding: 5px;
        //     display: block;
        //     .actionGroupTitle {
        //         display: block;
        //         font-size: 12px;
        //     }
        // }
    }
}
</style>

