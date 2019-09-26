<template>
    <div class="page">
        <div class="tree">
            <div>
                <Button @click="saveDepartment" :loading="saveDepartmentLoading" class="saveDepartment" type="primary">保存部门</Button>
            </div>
            <div>
                <orgTree 
                    v-for="tree,index in departmentTree"
                    :key="index"
                    :data="tree"
                    :props="departmentTreeConfig.props"
                    :collapsable="departmentTreeConfig.collapsable"
                    :horizontal="departmentTreeConfig.horizontal"
                    @on-node-click="departmentOnClick"
                ></orgTree>
            </div>
            
        </div>
        <div class="detail">
            
            <Card class='detailElement'>
                <p slot="title">权限详情</p>
                <p>
                    <div><Button @click="saveAction" :loading="saveActionLoading" class="saveAction" type="primary">保存权限</Button></div>
                    <Collapse>
                        <Panel :name="index + ''" :key="index" v-for="(controllerInfo, index) in actionList">
                            <span class="actionGroupTitle">{{ controllerInfo.desc }}（{{ controllerInfo.controller }}）</span>
                            <span :key="index" v-for="(actionInfo, index) in controllerInfo.action">
                                <Tag color="cyan" v-if="actionInfo.originIsChecked == 1">{{ actionInfo.desc }}</Tag>
                            </span>
                            <p slot="content">
                                <span :key="index" v-for="(actionInfo, index) in controllerInfo.action">
                                    <Button class="actionButton" @click="changeAction(actionInfo)" size="small" type="info" v-if="actionInfo.isChecked == 1">{{ actionInfo.desc }}</Button>
                                    <Button class="actionButton" @click="changeAction(actionInfo)" size="small" v-else>{{ actionInfo.desc }}</Button>
                                </span>
                            </p>
                        </Panel>
                    </Collapse>
                </p>
            </Card>
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
            
            saveDepartmentLoading : false,
            saveActionLoading : false,

            actionList : [],
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
        // 获取操作列表
        getGroupActionList() {
            $.ajax({
                url:`/cp/departments/actionGroup/` + this.groupId + `/action`,
                type:'GET',
                success: (res) => {
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
        this.getGroupActionList()
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
        // position:relative;
        // .saveDepartment {
        //     position: absolute;
        // }
    }
    .detail {
        float:right;
        width: 50%;
        padding-left: 15px;
        overflow: scroll;
        height: 100%;
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

        .detailElement {
            margin-bottom : 15px;
        }

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

        .actionButton {
            margin:3px;
        }
        
    }
}
</style>

