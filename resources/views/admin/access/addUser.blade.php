@extends('cp.newlayout')
@section('title', '新增\关闭账户')
@section('head')
    <style>
        .ivu-modal-body td {
        }
        .ivu-form-item-content .ivu-input-wrapper {
            max-width: 300px;
        }
        .ivu-select {
            max-width: 300px;
        }
        .ivu-card {
            margin: auto;
            width: 800px;
        }
        .ivu-form{
            min-height: 450px;;
        }
    </style>
@endsection
@section('content')
    @verbatim
        <div id="editUser" v-cloak>
            <Card>
                <Tabs>
                    <tab-pane label="新增\关闭账户">
                        <i-form :label-width="180" ref="formData" :model="formData" :rules="formRuleValidate">
                            <form-item label="手机号"  prop="mobile">
                                <div style="width:500px">
                                    <i-input v-model="formData.mobile" placeholder="输入下单的手机号"
                                             @on-keyup.enter="seach"></i-input>
                                    <i-button type="primary" @click="seach" style="margin-left: 20px">查找
                                    </i-button>
                                </div>
                            </form-item>
                            <form-item label="密码" prop="password">
                                <i-input type="password" v-model="formData.password" placeholder="请输入密码" ></i-input>
                            </form-item>
                            <form-item label="姓名" prop="name">
                                <i-input v-model="formData.name" placeholder="用户姓名"></i-input>
                            </form-item>
                            <form-item label="邮箱">
                                <i-input v-model="formData.email" placeholder="邮箱"/>
                            </form-item>
                            <form-item label="用户角色" prop="role">
                                <i-select placeholder="请选择用户角色" v-model="formData.role" filterable>
                                    <i-option v-for="(value, key) in roleList" :value="key"
                                              :key="key">{{ value }}</i-option>
                                </i-select>
                            </form-item>
                            <form-item label="当前状态">
                                <Tag v-if="!formData.leave">在职</Tag>
                                <Tag color="error" v-if="formData.leave && formData.uid">离职</Tag>
                            </form-item>
                            <form-item label="所在组织" v-if="formData.dpList.length !=0 ">
                                <Tag type="dot" v-for="value in formData.dpList" :key="value">{{value}}</Tag>
                            </form-item>
                            <form-item style="margin-top:50px">
                                <i-button type="primary" @click="save" :loading="submitLoading" style="margin-left: 20px">确认添加</i-button>
                                <i-button type="error" @click="dimission" style="margin-left: 80px" :disabled="formData.leave || !formData.uid" >确认离职</i-button>
                            </form-item>
                        </i-form>
                    </tab-pane>
                </Tabs>
            </Card>
        </div>
    @endverbatim
@endsection

@section('endbody')
<script>
   var vm = new Vue({
            el: '#editUser',
            data() {
                return {
                    submitLoading:false,
                    formData:{
                        uid:'',
                        mobile:'',
                        password:'',
                        name:'',
                        role:'',
                        email:'',
                        leave:false,
                        dpList:[]
                    },
                    roleList:[],
                    formRuleValidate:{
                        mobile: [
                            {required: true,  message: '请填写手机号', trigger: 'blur'}
                        ],
                        password: [
                            {required: true, message: '请选择密码', trigger: 'blur'}
                        ],
                        name: [
                            {required: true,  message: '请填写姓名', trigger: 'blur'}
                        ],
                        role: [
                            {required: true,  message: '请选择角色', trigger: 'blur'}
                        ],
                    }
                }
            },
            computed: {},
            methods: {
                seach(){
                    _this = this;
                    ajax({
                        url:'/user/search',
                        type:'GET',
                        dataType:'JSON',
                        data:{mobile:_this.formData.mobile},
                        success:function(data){
                            _this.formData.email = data.email
                            _this.formData.uid = data.uid
                            _this.formData.password = data.password
                            _this.formData.name = data.user_name
                            _this.formData.role = data.role
                            _this.formData.leave = data.leave
                            _this.formData.dpList = data.dpList
                        },
                    });
                },
                dimission(){
                    _this = this ;
                    if (confirm('确认将该用户离职吗？')) {
                        ajax({
                                url:'/user/dimission',
                                type:'delete',
                                dataType:'JSON',
                                data:_this.formData,
                                showSuccess:true,
                                success:function(){
                                    _this.seach();
                                }
                            });
                    }
                },
                save(){
                    _this = this;
                    _this.$refs['formData'].validate((valid) => {
                        if (valid) {
                            ajax({
                                url:'/user/add',
                                type:'POST',
                                dataType:'JSON',
                                data:_this.formData,
                                showSuccess:true,
                                success:function(){
                                    _this.seach();
                                }
                            });
                        }
                    });
                }
            },
            created(){
                this.roleList = @json($cp_role_list);
            },
            mounted(){
            }
   });
</script>
@endsection
