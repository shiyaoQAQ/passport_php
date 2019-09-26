<template>
    <div class="page">
        <div class="pageCenter">
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
            did : 0,
            project : '',
            saveActionLoading : false,

            actionList : [],
        }
    },
    components: {
        OrgTree
    },
    methods: {
        // 获取操作列表
        getActionList() {
            $.ajax({
                url:`/cp/departments/actionGroup/` + this.did + `/action`,
                data : {
                    project : this.project,
                },
                type:'GET',
                success: (res) => {
                    this.actionList = res.data.action_list;
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
        this.did = this.$route.params.did
        this.project = this.$route.params.project
        // this.getDepartmentTree()
        this.getGroupActionList()
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
        .actionButton {
            margin:3px;
        }
    }
}
</style>

