<template>
    <div class="page">
        <div class="pageCenter">
            <h2>资源组管理 <Button type="info" @click="createItem">添 加</Button></h2>
            <div class="resourceGroupList">
                <Table highlight-row :columns="resourceGroupColumn" :data="resourceGroupList"></Table>
                <!-- <table class="">
                    <tr>
                        <td>ID</td>
                        <td>名称</td>
                        <td>描述</td>
                        <td>所属项目</td>
                        <td>创建时间</td>
                        <td>操作</td>
                    </tr>
                    <tr v-for="(v, k) in resource_group_list">
                        <td>@{{ v.id }}</td>
                        <td>@{{ v.name }}</td>
                        <td>@{{ v.desc }}</td>
                        <td>@{{ v.project }}</td>
                        <td>@{{ v.ctime }}</td>
                        <td>
                            <i-button type="primary" style="margin-right:20px;" @click="editItem(v)" size="small">编 辑</i-button>             
                            <i-button type="primary" style="margin-right:20px;" @click="jumpPage('resourcegroupdetail', v)" size="small">资源编辑</i-button>             
                            <i-button type="default" @click="deleteItem(v)"  size="small">删 除</i-button>
                        </td>
                    </tr>
                </table> -->
            </div>

            <Modal v-model="modal" @on-ok="storeItem" :loading="modalConfig.loading" ok-text="保存">
                <h3 v-if="this.modalConfig.operate == 'add'">添加</h3>
                <h3 v-else>编辑</h3>
                <ul>
                    <li  class="modalLI">
                        <span>名称:</span>
                        <i-input style="width:300px" v-model="modalData.name"></i-input>
                    </li>
                    <li  class="modalLI">
                        <span>描述:</span>
                        <i-input style="width:300px" v-model="modalData.desc"></i-input>
                    </li>
                    <!-- 
                    <li  class="modalLI">
                        <span>所属项目:</span>
                        <i-select 
                            style="width:300px" 
                            v-model="modalData.project"
                            :disabled="this.modalConfig.operate != 'add'"
                            >
                            <i-option v-for="(desc, index) in accessProjectList" :value="index" :key="index">{{ desc }} </i-option>
                        </i-select>
                    </li>
                    -->
                </ul>
            </Modal>
        </div>
        <!-- <div class="driverPage">
            <Page :total="dataList.total" :page-size="dataList.per_page"  :current="dataList.current_page" @on-change="getDataList"></Page>
        </div> -->
    </div>
</template>

<script>
export default {
    data() {
        return {
            modal: false,
            // filter: {
            // },
            dataList : {},
            modalData : {
            },
            // accessProjectList : {!! json_encode($accessProjectList) !!},
            // resource_group_list : {!! json_encode($resource_group_list) !!},
            // accessProjectList : [],
            resourceGroupList : [],
            // 表格字段
            resourceGroupColumn : [
                {
                    title: 'ID',
                    width : 80,
                    key: 'id',
                },
                {
                    title: '名称',
                    key: 'name'
                },
                {
                    title: '描述',
                    key: 'desc'
                },
                {
                    title: '创建时间',
                    key: 'ctime'
                },
                {
                    title: '操作',
                    key: 'resource',
                    width: 240,
                    align: 'center',
                    render: (h, params) => {
                        return h('div', [
                            h('Button', {
                                props: {
                                    type: 'info',
                                    size: 'small',
                                },
                                style : {
                                    margin :'0 5px 0 5px',
                                },
                                on: {
                                    click: () => {
                                        this.editItem(params.row)
                                    }
                                }
                            }, '编辑'),
                            h('Button', {
                                props: {
                                    type: 'info',
                                    size: 'small',
                                },
                                style : {
                                    margin :'0 5px 0 5px',
                                },
                                on: {
                                    click: () => {
                                        this.jumpPage('resourcegroupdetail', params.row)
                                    }
                                }
                            }, '资源编辑'),
                            h('Button', {
                                props: {
                                    type: 'error',
                                    size: 'small',
                                },
                                style : {
                                    margin :'0 5px 0 5px',
                                },
                                on: {
                                    click: () => {
                                        // console.log(params);
                                        this.deleteItem(params.row)
                                    }
                                }
                            }, '删除'),
                        ]);
                    }
                },
            ],
            //搜索时使用的资源组列表
            searchList : [],
            modalConfig : {
                loading : true,
                operate : null,
                searchLoading : false,
            },
            thirdCityInfo : [],
        }
    },
    methods: {
        // 获取数据
        getDataList () {
            let _this = this
            // if (page == null) {
            //     page = this.dataList.current_page
            // }
            // _this.filter.page = page

            $.ajax({
                type: "get",
                url: "/cp/departments/resourceGroup",
                data: this.filter,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                success: function (response) {
                    if (response.code === 0) {
                        _this.resourceGroupList = response.data.resource_group_list
                    }
                }
            })
        },
        // 获取页面初始化数据
        getInitData () {
            let _this = this
            // // 获取可选项目列表
            // $.ajax({
            //     type: "get",
            //     url: "/cp/departments/resourceGroup/accessProject",
            //     data: this.filter,
            //     headers: {
            //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //     },
            //     dataType: "json",
            //     success: function (response) {
            //         if (response.code === 0) {
            //             _this.accessProjectList = response.data.access_project_list
            //         }
            //     }
            // })
        },
        //添加资源组
        createItem() {
            this.modalConfig.operate = 'add'
            this.modalData = {
                name : '',
                desc : '',
                project : null,
            },
            this.modal = true
        },
        //编辑资源组
        editItem(item) {
            this.modalConfig.operate = 'edit'
            //将该item的数据 绑定到模态框上
            this.modalData = this.copyObject(item)
            this.modal = true
        },
        //校验并存储资源组
        storeItem() {
            let _this = this
            // tp_id
            if(this.modalData.name.length == 0){
                alert('请输入资源组名称');
                _this.modalConfig.loading = false
                _this.$nextTick(() => { _this.modalConfig.loading = true; })
                return false;
            }
            // if(!this.modalData.project){
            //     alert('请选择所属项目');
            //     _this.modalConfig.loading = false
            //     _this.$nextTick(() => { _this.modalConfig.loading = true; })
            //     return false;
            // }
            let group_id = this.modalData.id
            $.ajax({
                url : '/cp/longrentdepartment/ajaxaddresourcegroup',
                type : "POST",
                data : this.modalData,
                dataType : 'JSON',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.code === 0) {
                        _this.modalConfig.loading = false
                        _this.$nextTick(() => { _this.modalConfig.loading = true; })
                        _this.modal = false
                        //保存完后更新一次列表
                        _this.$Message.success({
                            title: '',
                            content: '保存成功',
                        });
                        _this.getDataList()
                    } else {
                        _this.$Message.error({
                            title: '',
                            content: '保存失败！错误信息：' + response.msg + response.code,
                        });
                        _this.modalConfig.loading = false
                        _this.$nextTick(() => { _this.modalConfig.loading = true; })
                    }
                },
                error: function () {
                    _this.$Message.error({
                            title: '',
                            content: '网络错误，保存失败',
                        })
                    _this.modalConfig.loading = false
                    _this.$nextTick(() => { _this.modalConfig.loading = true; })
                }
            });
        },
        // 删除资源组
        deleteItem (item) {
            if (!confirm('真的要执行删除操作吗？')) {
                return false
            }
            let _this = this
            //后台交互
            $.ajax({
                url:'/cp/longrentdepartment/ajaxdelresourcegroup',
                type:"POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: item.id,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                success: function (response) {
                    if (response.code == 0){
                        //保存完后更新一次列表
                        _this.$Message.success({
                            title: '',
                            content: response.msg,
                        });
                        _this.getDataList()
                    } else {
                        _this.$Message.error({
                            title: '',
                            content: '删除失败！错误信息：' + response.msg + response.code,
                        });
                    }
                },
                error: function () {
                    _this.$Message.error({
                        title: '',
                        content: '网络错误，保存失败',
                    })
                }
            })
        },
        //复制对象
        copyObject(obj){
            if(typeof obj != 'object'){
                return obj;
            }
            var newobj = {};
            for ( var attr in obj) {
                newobj[attr] = this.copyObject(obj[attr]);
            }
            return newobj;
        },
        // 页面跳转
        jumpPage(pa, v) {
            if (pa == 'resourcegroupdetail') {
                // window.location = '/cp/longrentdepartment/resourcegroupdetail?id=' + v.id
                this.$router.push({
                    name : "resourceGroupEdit",
                    params : {
                        groupId : v.id,
                    },
                })
            }
        }
    },
    created() {

    },
    mounted() {
        this.getDataList()
        this.getInitData()
    }
}

</script>


<style lang="less" scoped>
.page {
    h2 {
        margin-bottom: 15px;
    }
    .pageCenter {
        width: 1000px;
        background-color: #fff;
        margin: 0 auto;
        padding: 15px;
        .resourceGroupList {
            margin-bottom: 50px;
            // .ivu-btn-info {
            //     margin: 0px 10px 0px 10px;
            // }
        }
    }
}
.ivu-modal-body{
    .modalLI {
        margin-top: 20px;
        span:first-child{
            display: inline-block;
            width: 100px;
            text-align: right;
        }
    }
}
</style>



