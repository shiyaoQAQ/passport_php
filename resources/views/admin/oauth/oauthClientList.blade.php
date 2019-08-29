@extends('admin.layout')
@section('title', 'Oauth客户端管理')
@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Oauth客户端管理</title>
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
            <h3>Oauth客户端管理</h3>
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
                        <td>密钥</td>
                        <td>回调域名</td>
                        <td>授权方式</td>
                        <td>状态</td>
                        <td>操作</td>
                    </tr>
                    <tr v-for="(v, k) in dataList.data">
                        <td>@{{ v.id }}</td>
                        <td>@{{ v.name }}</td>
                        <td>怎么会告诉你呢</td>
                        <td>@{{ v.redirect }}</td>
                        <td>@{{ v.grant_type_desc }}</td>
                        <td>@{{ v.is_nuked_desc }}</td>
                        <td>
                            <i-button type="primary" style="margin-right:20px;" @click="editItem(v)" size="small">编 辑</i-button>             
                            <!-- <i-button type="default" @click="deleteItem(v)"  size="small">删 除</i-button> -->
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
                        <span>回调域名:</span>
                        <i-input style="width:300px" v-model="modalData.redirect"></i-input>
                    </li>
                    <li class="modalLI">
                        <span>授权类型:</span>
                        <i-select 
                            style="width:300px" 
                            v-model="modalData.grant_type"
                            :disabled="this.modalConfig.operate != 'add'"
                            >
                            <i-option v-for="(desc, index) in grantTypeList" :value="index" :key="index">@{{ desc }} </i-option>
                        </i-select>
                    </li>
                    <li class="modalLI">
                        <span>是否封禁:</span>
                        <i-select 
                            style="width:300px" 
                            v-model="modalData.is_nuked"
                            >
                            <i-option v-for="(desc, index) in nukedList" :value="index" :key="index">@{{ desc }} </i-option>
                        </i-select>
                    </li>
                </ul>
            </Modal>
        </div>
        <div class="driverPage">
            <Page :total="dataList.total" :page-size="dataList.per_page"  :current="dataList.current_page" @on-change="getDataList"></Page>
        </div>
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
            grantTypeList : {!! json_encode($grantTypeList) !!},
            nukedList : {!! json_encode($nukedList) !!},
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
            //获取砂石Oauth客户端管理
            getDataList (page = null) {
                _this = this
                if (page == null) {
                    page = this.dataList.current_page
                }
                _this.filter.page = page

                $.ajax({
                    type: "get",
                    url: "/cp/oauth/clients/json",
                    data: this.filter,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: "json",
                    success: function (response) {
                        if (response.code === 0) {
                            _this.dataList = response.data
                        }
                    }
                })
            },
            //添加
            createItem() {
                this.modalConfig.operate = 'add'
                this.modalData = {
                    'name' : null,
                    'redirect' : null,
                    'grant_type' : null,
                    'is_nuked' : null,
                },
                this.modal = true
            },
            //编辑权限组
            editItem(item) {
                this.modalConfig.operate = 'edit'
                //将该item的数据 绑定到模态框上
                item.is_nuked = Number(item.is_nuked)
                this.modalData = this.copyObject(item)
                this.modal = true
            },
            //校验并存储权限组
            storeItem() {
                _this = this
                // tp_id
                if(!this.modalData.name) {
                    alert('请输入客户端名称');
                    _this.modalConfig.loading = false
                    _this.$nextTick(() => { _this.modalConfig.loading = true; })
                    return false;
                }
                if (!this.modalData.redirect) {
                    alert('请填写回调域名');
                    _this.modalConfig.loading = false
                    _this.$nextTick(() => { _this.modalConfig.loading = true; })
                    return false;
                }
                if (!this.modalData.grant_type) {
                    alert('请选择授权方式');
                    _this.modalConfig.loading = false
                    _this.$nextTick(() => { _this.modalConfig.loading = true; })
                    return false;
                }
                group_id = this.modalData.id
                $.ajax({
                    url : '/cp/oauth/clients',
                    type : "POST",
                    data : this.modalData,
                    dataType : 'JSON',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.code === 0) {
                            cp.alert('保存成功')
                            _this.modalConfig.loading = false
                            _this.$nextTick(() => { _this.modalConfig.loading = true; })
                            _this.modal = false
                            //保存完后更新一次列表
                            _this.getDataList()
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
        },
        mounted : function() {
            this.getDataList(1)
        }
    }) 
</script>
</html>
@endsection
