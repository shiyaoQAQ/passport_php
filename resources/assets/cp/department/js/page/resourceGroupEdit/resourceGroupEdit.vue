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
                    <span v-if="groupDetail!=null">{{ groupDetail.name }}（{{ groupDetail.desc }}）</span>
                </p>
                <Collapse v-model="resourceCollapse" v-if="resourceList.length">
                    <Panel
                        v-for="(controllerInfo, index) in resourceList"
                        :name="controllerInfo.controller"
                        :key="index">
                        <span class="resourceGroupTitle">
                            {{ controllerInfo.desc }}（{{ controllerInfo.controller }}）
                        </span>
                        <p slot="content">
                            <span
                                v-for="(resourceInfo, index) in controllerInfo.resource"
                                :key="index">
                                <Button
                                    class="resourceButton"
                                    @click="changeResource(resourceInfo)"
                                    size="small"
                                    :type="resourceInfo.isChecked == 1 ? 'info' : 'default'"
                                    v-if="resourceInfo.isChecked == 1">
                                    {{ resourceInfo.desc }}
                                </Button>
                            </span>
                        </p>
                    </Panel>
                </Collapse>
            </Card>
            <div class="content_options">
                <Button
                    @click="saveResource"
                    :loading="saveResourceLoading"
                    class="saveResource"
                    type="primary">
                    保存资源
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
            // 当前资源组id
            groupId : 0,
            groupDetail : null,
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
            saveResourceLoading : false,
            resourceList : [],
            resourceCollapse: []
        }
    },
    components: {
        OrgTree
    },
    methods: {
        // 获取组织架构树信息
        getDepartmentTree() {
            $.ajax({
                url:`/cp/departments/resourceGroup/` + this.groupId + `/tree`,
                type:'GET',
                success: (res) => {
                    this.departmentTree = res.data;
                }
            })
        },
        // 获取组信息
        getResourceGroupDetail() {
            $.ajax({
                url:`/cp/departments/resourceGroup/` + this.groupId + `/detail`,
                type:'GET',
                success: (res) => {
                    this.groupDetail = res.data;
                }
            })
        },
        // 获取操作列表
        getGroupResourceList() {
            $.ajax({
                url:`/cp/departments/resourceGroup/` + this.groupId + `/resource`,
                type:'GET',
                success: (res) => {
                    res.data.resource_list.forEach((v) => {
                        this.resourceCollapse.push(v.controller)
                    })
                    this.resourceList = res.data.resource_list;
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
                url  : '/cp/departments/resourceGroup/' + _this.groupId + '/department',
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
        // 切换资源
        changeResource(resourceInfo) {
            resourceInfo.isChecked = 1 - resourceInfo.isChecked
        },
        // 保存资源
        saveResource() {
            // 递归计算变更
            // 部门树变更
            this.saveResourceLoading = true
            let resourceIncrease = []
            let resourceReduce = []
            let _this = this
            this.resourceList.forEach((controller, i) => {
                if (controller.resource) {
                    controller.resource.forEach((resource, ai) => {
                        if (resource.isChecked != resource.originIsChecked) {
                            if (resource.isChecked == 1) {
                                resourceIncrease.push(resource.controller + '@' +  resource.resource)
                            } else {
                                resourceReduce.push(resource.controller + '@' +  resource.resource)
                            }
                        }
                    })
                }
            })
            if (resourceIncrease.length == 0 && resourceReduce.length == 0) {
                _this.$Message.error('您没有任何变更啊')
                _this.saveResourceLoading = false
                return
            }

            // 调用更新接口
            $.ajax({
                url  : '/cp/departments/resourceGroup/' + _this.groupId + '/resource',
                data : {
                    resourceIncrease : resourceIncrease,
                    resourceReduce : resourceReduce,
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
                        _this.getGroupResourceList()
                    } else {
                        _this.$Message.error({
                            title: '',
                            content: '保存失败！错误信息：' + res.msg + res.code,
                        });
                    }
                    _this.saveResourceLoading = false
                },
                error : function(res) {
                    _this.saveResourceLoading = false
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
        this.getResourceGroupDetail()
        this.getGroupResourceList()
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
            background-color: #fff;
        }
    }
    .tree {
        float:left;
        background-color: #fff;
        .content_options {
            width: 100%;
            left: 0px;
        }
    }
    .detail {
        float:right;
        padding-left: 15px;
        .content_options {
            width: calc(100% - 15px);
            left: 15px;
        }
        .resourceButton {
            margin:5px;
        }
    }
}
</style>

