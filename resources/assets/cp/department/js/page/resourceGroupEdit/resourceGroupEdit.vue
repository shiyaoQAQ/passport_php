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
                <p slot="title">资源详情</p>
                <p>
                    <div><Button @click="saveResource" :loading="saveResourceLoading" class="saveResource" type="primary">保存资源</Button></div>
                    <Collapse>
                        <Panel :name="index + ''" :key="index" v-for="(controllerInfo, index) in resourceList">
                            <span class="resourceGroupTitle">{{ controllerInfo.desc }}（{{ controllerInfo.controller }}）</span>
                            <span :key="index" v-for="(resourceInfo, index) in controllerInfo.resource">
                                <Tag color="cyan" v-if="resourceInfo.originIsChecked == 1">{{ resourceInfo.desc }}</Tag>
                            </span>
                            <p slot="content">
                                <span :key="index" v-for="(resourceInfo, index) in controllerInfo.resource">
                                    <Button class="resourceButton" @click="changeResource(resourceInfo)" size="small" type="info" v-if="resourceInfo.isChecked == 1">{{ resourceInfo.desc }}</Button>
                                    <Button class="resourceButton" @click="changeResource(resourceInfo)" size="small" v-else>{{ resourceInfo.desc }}</Button>
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
            // 当前资源组id
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
            saveResourceLoading : false,

            resourceList : [],
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
        // 获取操作列表
        getGroupResourceList() {
            $.ajax({
                url:`/cp/departments/resourceGroup/` + this.groupId + `/resource`,
                type:'GET',
                success: (res) => {
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
        this.getGroupResourceList()
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

        .detailElement {
            margin-bottom : 15px;
        }


        .resourceButton {
            margin:3px;
        }
        
    }
}
</style>

