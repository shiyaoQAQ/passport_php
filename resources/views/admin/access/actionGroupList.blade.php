@extends('admin.layout')
@section('title', '权限组管理')
@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>权限组管理</title>
    <style>
        *{
            margin: 0;
            padding: 0;
        }
        .ivu-btn-primary {
            background-color: #337ab7;
            border: none;
        }
        .ivu-btn-primary.active, .ivu-btn-primary:active {
            background-color: #204d74;
            border: none;
        }
        .ivu-btn-primary:hover {
            background-color: #204d74;
            border: none;
        }
        .fasten {
            display: inline-block;
            width: 100px;
            text-align: right;
        }
        h3 {
            margin-top: 0;
        }
        #driverList {
            padding: 0px 20px;
            overflow: hidden;
        }
        #driverList .driverSearch .searchUL{
            float: left;
            width: 50%;
            margin-bottom: 40px;
        }
        #driverList .driverSearch .searchUL .searchLI {
            margin-top: 10px;
        }

        .driver_list table{
            width: 100%;
            border: 1px solid #c5c5c5;
        }
        .driver_list table th,.driver_list table td{
            text-align: center;
            border: 1px solid #c5c5c5;
            padding: 8px 10px;
        }

        .ivu-modal-body .modalLI {
            margin-top: 20px;
        }
        .ivu-modal-body .modalLI span:first-child{
            display: inline-block;
            width: 100px;
            text-align: right;
        }
        .driverPage {
            margin:20px;
        }

        /* vue闪烁 */
        [v-cloak]{
            display: none !important;
        }
    </style>
</head>
<body>
    <div id="driverList" v-cloak>
        <div class="driverSearch">
            <h3>权限组管理</h3>
            <ul class="searchUL">
                <li class="searchLI">
                    <!-- <span class='fasten'>联运方：</span>
                    <i-select style="width:180px" v-model="filter.tp_id" clearable placeholder="全部" >
                        <i-option v-for="(desc, index) in thirdPartyList" :value="index" :key="index" >@{{ desc }} </i-option>
                    </i-select> -->
                </li>
            </ul>
            <ul class="searchUL">
                <!-- <li class="searchLI">
                  
                </li> -->
                <li class="searchLI">
                    <span class='fasten'></span>
                    <!-- <i-button type="primary" @click="getDataList(1)" style="margin-right:20px;">搜 索</i-button> -->
                    <i-button type="primary" @click="createItem">添 加</i-button>
                </li>             
            </ul>
            <div class="driver_list">
                <table>
                    <tr>
                        <td>ID</td>
                        <td>名称</td>
                        <td>描述</td>
                        <td>所属项目</td>
                        <td>创建时间</td>
                        <td>操作</td>
                    </tr>
                    <tr v-for="(v, k) in action_group_list">
                        <td>@{{ v.id }}</td>
                        <td>@{{ v.name }}</td>
                        <td>@{{ v.desc }}</td>
                        <td>@{{ v.project }}</td>
                        <td>@{{ v.ctime }}</td>
                        <td>
                            <i-button type="primary" style="margin-right:20px;" @click="editItem(v)" size="small">编 辑</i-button>             
                            <i-button type="primary" style="margin-right:20px;" @click="jumpPage('actiongroupaccessdetail', v)" size="small">权限编辑</i-button>             
                            <i-button type="default" @click="deleteItem(v)"  size="small">删 除</i-button>
                            <!-- <a class="btn btn-primary btn-edit" @click="editItem(v)" href="#">编辑</a>
                            <a class="btn btn-danger btn-del" @click="removeItem(v)" href="#">删除</a>
                            <a class="btn btn-info" :href="'/cp/longrentdepartment/actiongroupaccessdetail?id=' + v.id">权限编辑</a> -->
                        </td>
                    </tr>
                </table>
            </div>

            <Modal v-model="modal" @on-ok="storeItem" :loading="modalConfig.loading" ok-text="保存">
                <h3 v-if="this.modalConfig.operate == 'add'">添加</h3>
                <h3 v-else>编辑</h3>
                <ul>
                    <li class="modalLI">
                        <span>名称:</span>
                        <i-input style="width:300px" v-model="modalData.name"></i-input>
                    </li>
                    <li class="modalLI">
                        <span>描述:</span>
                        <i-input style="width:300px" v-model="modalData.desc"></i-input>
                    </li>
                    <li class="modalLI">
                        <span>所属项目:</span>
                        <i-select 
                            style="width:300px" 
                            v-model="modalData.project"
                            :disabled="this.modalConfig.operate != 'add'"
                            >
                            <i-option v-for="(desc, index) in accessProjectList" :value="index" :key="index">@{{ desc }} </i-option>
                        </i-select>
                    </li>
                </ul>
            </Modal>
        </div>
        <!-- <div class="driverPage">
            <Page :total="dataList.total" :page-size="dataList.per_page"  :current="dataList.current_page" @on-change="getDataList"></Page>
        </div> -->
    </div>
</body>
<script>
    var vm = new Vue({
        el: '#driverList',
        data: {
            modal: false,
            filter: {
            },
            dataList : {},
            modalData : {
            },
            accessProjectList : {!! json_encode($accessProjectList) !!},
            action_group_list : {!! json_encode($action_group_list) !!},
            //搜索时使用的权限组列表
            searchList : [],
            modalConfig : {
                loading : true,
                operate : null,
                searchLoading : false,
            },
            thirdCityInfo : [],
        },
        methods: {
            //获取砂石权限组管理
            getDataList (page = null) {
                // _this = this
                // if (page == null) {
                //     page = this.dataList.current_page
                // }
                // _this.filter.page = page

                // $.ajax({
                //     type: "get",
                //     url: "/goods/thirdParty/json",
                //     data: this.filter,
                //     headers: {
                //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                //     },
                //     dataType: "json",
                //     success: function (response) {
                //         if (response.code === 0) {
                //             _this.dataList = response.data
                //         }
                //     }
                // })
            },
            //添加权限组
            createItem() {
                this.modalConfig.operate = 'add'
                this.modalData = {
                    name : '',
                    desc : '',
                    project : null,
                },
                this.modal = true
            },
            //编辑权限组
            editItem(item) {
                this.modalConfig.operate = 'edit'
                //将该item的数据 绑定到模态框上
                this.modalData = this.copyObject(item)
                this.modal = true
            },
            //校验并存储权限组
            storeItem() {
                _this = this
                // tp_id
                if(this.modalData.name.length == 0){
                    alert('请输入权限组名称');
                    _this.modalConfig.loading = false
                    _this.$nextTick(() => { _this.modalConfig.loading = true; })
                    return false;
                }
                if(!this.modalData.project){
                    alert('请选择所属项目');
                    _this.modalConfig.loading = false
                    _this.$nextTick(() => { _this.modalConfig.loading = true; })
                    return false;
                }
                group_id = this.modalData.id
                $.ajax({
                    url : '/cp/longrentdepartment/ajaxaddactiongroup',
                    type : "POST",
                    data : this.modalData,
                    dataType : 'JSON',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.code === 0) {
                            // cp.alert('保存成功')
                            _this.modalConfig.loading = false
                            _this.$nextTick(() => { _this.modalConfig.loading = true; })
                            _this.modal = false
                            //保存完后更新一次列表
                            // _this.getDataList()
                            alert('保存成功')
                            window.location.reload();
                        } else {
                            cp.alert('保存失败！错误信息：' + response.msg + response.code, '', 'error')
                            _this.modalConfig.loading = false
                            _this.$nextTick(() => { _this.modalConfig.loading = true; })
                        }
                    },
                    error: function () {
                        cp.alert('网络错误，保存失败', '', 'error')
                        _this.modalConfig.loading = false
                        _this.$nextTick(() => { _this.modalConfig.loading = true; })
                    }
                });
            },
            // 删除权限组
            deleteItem (item) {
                if (!confirm('真的要执行删除操作吗？')) {
                    return false
                }
                _this = this
                //后台交互
                $.ajax({
                    url:'/cp/longrentdepartment/ajaxdelactiongroup',
                    type:"POST",
                    data: {
                        id: item.id,
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: "json",
                    success: function (response) {
                        // if (response.code === 0) {
                        //     cp.alert('删除成功')
                        //     //删除完后更新一次列表
                        //     _this.getDataList()
                        // } else {
                        //     cp.alert('删除失败！错误信息：' + response.msg + response.code, '', 'error')
                        // }
                        alert(response.msg);
                        if(response.code == 0){
                            window.location.reload();
                        }
                    },
                    error: function () {
                        cp.alert('网络错误，删除失败', '', 'error')
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
                
                if (pa == 'actiongroupaccessdetail') {
                    window.location = '/cp/longrentdepartment/actiongroupaccessdetail?id=' + v.id
                }
            }
        },
        mounted : function() {
            // this.getDataList(1)
        }
    }) 
</script>
</html>
@endsection
