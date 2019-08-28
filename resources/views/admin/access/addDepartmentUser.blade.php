@extends('admin.layout')

@section('title', '添加账户')

@section('content')
    <style type="text/css">
        body {
            font: 16px/1.8 "宋体";
            overflow-y: scroll
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        .strt-wrap {
            width: 100000px;
            margin: 10px;
        }

        .strt-part {
            text-align: center;
            float: left;
            position: relative;
        }

        .strt-part .line-v {
            position: relative;
            height: 40px;
            width: 100%;
        }

        .strt-part .line-v span {
            display: block;
            background: #ccc;
            position: absolute;
            top: 0;
            font-size: 0;
            line-height: 1px;
            width: 1px;
            height: 40px;
            left: 50%;
        }

        /*.strt-name{display:inline-block;padding:0 5px;height:24px;line-height:24px;border:1px solid #ccc;margin:0 10px;border-radius:3px;background:#f8f8f8;}*/
        .strt-part .line-h {
            height: 1px;
            display: block;
            background: #ccc;
            position: absolute;
            top: 0;
            font-size: 0;
        }

        .strt-part .line-h-l {
            width: 50%;
            left: 0;
        }

        .strt-part .line-h-c {
            width: 100%;
            left: 0;
        }

        .strt-part .line-h-r {
            width: 50%;
            right: 0;
        }

        .strt-block {
            float: left;
        }

        .clear {
            clear: both
        }

        .node-edit {
            margin-right: 10px;
            margin-left: 10px;
        }

        .depart-add-user {
            margin-top: 20px;
            margin-right: 10px;
            margin-left: 10px;
        }

        .depart-user {
            font: 14px/1.8 "宋体";
            margin-top: 20px;
            margin-right: 10px;
            margin-left: 10px;
        }

        .depart-user table td {
            border: 1px solid #ddd
        }

        .depart-user table td, .depart-user table th {
            text-align: center;
        }

        .depart-group {
            font: 14px/1.8 "宋体";
            margin-top: 20px;
            margin-right: 10px;
            margin-left: 10px;
        }

        .btn-all {
            color: #fff;
            background-color: #000;
            border-color: #000;
        }

        .btn-default {
            color: #fff;
            background-color: #E42DFF;
            border-color: #E42DFF;
        }

        .btn-default:hover {
            color: #fff;
            background-color: #E42DFF;
            border-color: #E42DFF;
        }

        .strt-name {
            display: inline-block;
            padding: 0;
            margin: 0 10px;
            border-radius: 3px;
            background: #f8f8f8;
        }

        .strt-part .table {
            width: auto;
            margin: 0;
            background-color: transparent;
        }

        .strt-part .table > tbody > tr > td {
            padding: 0
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div id="addModal">
        <div class="strt-wrap">
        </div>
        <div class="clear"></div>
        {{csrf_field()}}
        <Modal v-model="modal"  :title="'给 '+modalData.title+' 新开账号'">
            <template>
                <i-form ref="modalData" :label-width="80" :rules="ruleValidate" :model="modalData" >
                    <form-item label="手机号"  prop="mobile">
                        <i-input v-model="modalData.mobile" placeholder="请输入企业微信绑定的手机号"></i-input>
                    </form-item>
                    <form-item label="密码"  prop="password">
                        <i-input v-model="modalData.password" placeholder="密码"></i-input>
                    </form-item>
                    <form-item label="姓名"  prop="name">
                        <i-input v-model="modalData.name" placeholder="请输入姓名"></i-input>
                    </form-item>
                    <form-item label="企业邮箱"  prop="email">
                        <i-input v-model="modalData.email" placeholder="请输入企业邮箱"></i-input>
                    </form-item>
                    <form-item label="角色" prop="role">
                        <i-select  name="role"  v-model="modalData.role" placeholder="请选择角色" filterable>
                            @foreach ($cp_role_list as $k => $name)
                                <i-option value="{{$k}}" key="{{$k}}">{{$name}}</i-option>
                            @endforeach
                        </i-select>
                    </form-item>
                </i-form>
            </template>
            <div slot="footer">
                        <i-button type="text" size="large" @click="cancel">取消</i-button>
   	                    <i-button type="primary" size="large" @click="add">提交</i-button>
            </div>
        </Modal>
    </div>
    </html>
    <script type="text/javascript">

        var vm = new Vue({
            el: '#addModal',
            data: {
                modal: false,
                modalData: {},
                filter: {},
                ruleValidate: {
                    name: [
                        {required: true, message: '请填写姓名', trigger: 'blur'}
                    ],
                    email: [
                        {required: true, message: '请填写邮箱', trigger: 'blur'}
                    ],
                    mobile: [
                        { required: true, message: '请填写手机号码', trigger: 'blur' }
                    ],
                    password: [
                        { required: true, message: '请填写密码', trigger: 'blur' }
                    ],
                    role:[
                        { required: true, message: '请选择角色', trigger: 'blur' }
                    ]
                }
            },
            methods: {
                loadTree(id) {
                    $.ajax({
                        url: '/cp/longrentdepartment/ajaxdeparttree?pid=0',
                        type: 'GET',
                        success: function (data) {
                            $('.strt-wrap').html(data);
                            if (id != null) {
                                $('.strt-name').css('border', '1px solid #ccc');
                                $('.strt-name[departid=' + id + ']').css('border', '1px solid #5CACEE');
                            }
                        }
                    });
                },
                add() {
                    var _this = this;
                    var modalData =this.modalData;
                    this.$refs['modalData'].validate((valid) => {
                        if (valid) {
                         
                           
                            $.ajax({
                                url  : '/user/addDepartmentUser',
                                data : modalData,
                                type : 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                async: false,
                                dataType:'json',
                                success : function(response){
                                    if(response.code ==0){
                                        cp.showAlert({content: '添加成功',});
                                        _this.modalData = {};
                                        _this.modal = false;
                                    }else{
                                        cp.alert('添加失败:'+response.msg, '', 'error')
                                        _this.modal = true;
                                    }
                                },
                            });
                        } 
                    })
                
                },
                cancel() {
                    this.modal = false;
                    this.modalData = {};
                    this.$refs['modalData'].resetFields();
                },
                renderUser(id, departname) {
                    if(id ==1 ){
                        return ;
                    }
                    let _this = this;
                    _this.modal = true;
                    _this.modalData.title = departname;
                    _this.modalData.departmentId = id
                }
            },
            created: function () {
                this.modal = false;
            }
        });


        function chooseNode() {
            $('.strt-name').css('border', '1px solid #ccc');
            $(this).css('border', '1px solid #5CACEE');
            //获取部门用户
            var did = $(this).attr('departid');
            var departname = $(this).attr('departname');
            vm.renderUser(did, departname);
        }

        $(function () {
            //排位置
            var strtBlocks = $("div.strt-block");
            strtBlocks.each(function (n) {
                var childs = $(this).children();
                var w = (childs.last().width() - childs.first().width()) / 4;
                if (w > 0) {
                    $(this).css("margin-left", w)
                } else {
                    $(this).css("padding-right", -w * 2);
                }
            });
            vm.loadTree();
            $('.form-control').attr('readonly', 'true');
            $(".strt-wrap").on("click", '.strt-name', chooseNode);
        });
    </script>

@endsection


