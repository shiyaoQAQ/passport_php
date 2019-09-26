<template>
    <div class="page">
        <div class="pageCenter">
            <Card class='detailElement'>
                <p slot="title">
                    <span v-if="departmentDetail!=null">
                        {{ departmentDetail.name }}
                    </span>
                </p>
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
            did : 0,
            project : '',
            saveResourceLoading : false,

            departmentDetail : null,
            resourceList : [],
        }
    },
    components: {
        OrgTree
    },
    methods: {
        // 获取操作列表
        getResourceList() {
            $.ajax({
                url:`/cp/departments/` + this.did + `/tmpResource/resource`,
                data : {
                    project : this.project,
                },
                type:'GET',
                success: (res) => {
                    this.resourceList = res.data.resource_list;
                }
            })
        },
        // 获取部门信息
        getDepartmentDetail() {
            $.ajax({
                url:`/cp/departments/` + this.did + `/detail`,
                type:'GET',
                success: (res) => {
                    this.departmentDetail = res.data;
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
                url  : '/cp/departments/' + this.did + '/tmpResource/resource',
                data : {
                    resourceIncrease : resourceIncrease,
                    resourceReduce : resourceReduce,
                    project : this.project,
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
                        _this.getResourceList()
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
        this.did = this.$route.params.did
        this.project = this.$route.query.project
        // this.getDepartmentTree()
        this.getDepartmentDetail()
        this.getResourceList()
    }
}

</script>


<style lang="less" scoped>
.page {
    // height: calc(100vh - 70px);
    .pageCenter {
        width: 1200px;
        background-color: #fff;
        margin: 0 auto;
        padding: 15px;

        .detailElement {
            margin-bottom : 15px;
        }
        .resourceButton {
            margin:3px;
        }
    }
}
</style>

