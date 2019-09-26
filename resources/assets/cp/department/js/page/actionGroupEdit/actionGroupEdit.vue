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
                    <h4>独立权限</h4>
                    <Collapse>
                        <Panel :name="index + ''" :key="index" v-for="(projectInfo, project, index) in actionList">
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

            actionList : [],

            // 权限变更
            actionIncrease : [],
            actionReduce : [],
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
            // console.log(departmentIncrease);
            // console.log(departmentReduce);
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
        operateTree(treeNodeList, callbackFunc) {
            treeNodeList.forEach((v, i) => {
                callbackFunc(v)
                if (v.child) {
                    this.operateTree(v.child, callbackFunc)
                }
            })
        },

        
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

