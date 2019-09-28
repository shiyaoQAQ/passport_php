<template>
    <div class="page">
        <Card class='detail_element'>
            <div slot="title" class="content_header">
                <p class="content_header_text">{{ departmentDetail.name }}</p>
                <Button 
                    @click="saveAction"
                    :loading="saveActionLoading"
                    class="saveAction"
                    type="primary">
                    保存资源
                </Button>
            </div>
            <Collapse
                v-model="actionCollapse"
                v-if="actionList.length"
                class="content">
                <Panel
                    v-for="(controllerInfo, index) in actionList"
                    :name="controllerInfo.controller"
                    :key="index">
                    <span class="actionGroupTitle">{{ controllerInfo.desc }}（{{ controllerInfo.controller }}）</span>
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
    </div>
</template>

<script>
import OrgTree from '../../components/org-tree/index.js'
export default {
    data() {
        return {
            // 当前权限组id
            did : 0,
            project : '',
            saveActionLoading : false,

            departmentDetail : {},
            actionList : [],
            actionCollapse: []
        }
    },
    components: {
        OrgTree
    },
    methods: {
        // 获取操作列表
        getActionList() {
            $.ajax({
                url:`/cp/departments/` + this.did + `/tmpAction/action`,
                data : {
                    project : this.project,
                },
                type:'GET',
                success: (res) => {
                    res.data.action_list.forEach((v) => {
                        this.actionCollapse.push(v.controller)
                    })
                    this.actionList = res.data.action_list;
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
                url  : '/cp/departments/' + this.did + '/tmpAction/action',
                data : {
                    actionIncrease : actionIncrease,
                    actionReduce : actionReduce,
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
                        _this.getActionList()
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
        this.did = this.$route.params.did
        this.project = this.$route.query.project
        // this.getDepartmentTree()
        this.getDepartmentDetail()
        this.getActionList()
    }
}

</script>


<style lang="less" scoped>
.page {
    .detail_element {
        width: 1000px;
        height: calc(100vh - 70px);
        margin: 0 auto;
        .content_header {
            .content_header_text {
                font-weight: 700;
                width: auto;
                display: inline-block;
                vertical-align: middle;
                margin-right: 10px;
            }
        }
        .content {
            height: calc(100vh - 165px);
            overflow-y: scroll;
            .actionButton {
                margin: 5px;
            }
        }
    }
}
</style>
