<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>掌上辅材</title>
    <link rel="stylesheet" href="/css/iview@2.9.0.css">
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    @if(config('app.debug'))
        <script src="/js/vue.dev@2.5.13.js"></script>
    @else
        <script src="/js/vue.min@2.5.9.js"></script>
    @endif
    <script src="/js/iview@2.9.0.js"></script>
    <script src="/js/jquery.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="https://rescdn.qqmail.com/node/ww/wwopenmng/js/sso/wwLogin-1.0.0.js"></script>
    <style>
        [v-cloak]{
            display: none !important;
        }
        html,body {
            width: 100%;
            height: 100%;
        }
        #layout{
            width: 100%;
            height: 100%;
            position: relative;
            background: url('http://zsfc-static.oss-cn-beijing.aliyuncs.com/data/attached/images/201808/1535186186395860874.jpg') no-repeat;
            background-size: 100% 100%;
        }
        .logo img{
            width: 120px;
            height: 120px;
            margin: 100px 0 0 100px;
        }
        .login {
            position: absolute;
            top: 20%;
            right: 100px;
            background-color: rgba(0,0,0,0.5);
            border-radius: 10px;
        }
        .loginTitle{
            text-align: center;
            color: #fff;
            font-size: 25px;
            margin-bottom: 30px;
        }
        .login .loginBox {
            display: inline-block;
            padding: 30px;
        }
        .login .loginBox .error{
            background-color: #fdece8;
            text-align: center;
            padding: 5px 8px;
            border: 1px solid #fbd9d0;
        }
        .login .loginBox .ivu-input-group .ivu-input{
            height: 40px;
            width: 230px;
            font-size: 14px;
        }
        .login .loginBox .ivu-form-item{
            margin-bottom: 30px;
        }
        .login .loginBox .ivu-input-group-prepend {
            background-color: #fff;
        }
        .login .loginBox .ivu-input-group-prepend img{
            width: 25px;
            height: 25px;
        }
        .login .loginBox .submitBtn{
            width: 170px;
            height: 40px;
            font-size: 15px;
            background-color: #009944;
            border: none;
            color: #fff;
        }
        .login .loginBox .smsSubmitBtn{
            width: 140px;
            height: 40px;
            font-size: 15px;
            background-color: #009944;
            border: none;
            color: #fff;
            margin: 30px 5px 30px 5px;
            
        }

        .login .loginBox .submitBtnBack{
            width: 85px;
            height: 40px;
            font-size: 15px;
            background-color: #F4A460;
            border: none;
            color: #fff;
        }

        .login .loginEWM iframe{
            height: 300px;
        }
        .login .loginEWM .loginTitle{
            margin-top: 30px;
            margin-bottom: 0;
        }
        .login .loginEWM .loginHint{
            margin-top: 0px;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div id="layout" v-cloak>
        <div class="logo">
            <img src="http://zsfc-static.oss-cn-beijing.aliyuncs.com/data/attached/images/201808/1535166914193219757.png" alt="">
        </div>
        <div class="login">
            <!-- <Tabs>
                <tab-pane label="扫码登录" name="wxcode"> -->
                    <i-form class="loginBox" v-if="loginStep == 0">
                        <p class="loginTitle">登录</p>
                        <p class="error" v-show="errorMsg.length > 0">
                            @{{errorMsg}}
                        </p>
                        <form-item label="">
                            <i-input v-model="mobile" placeholder="账号（账号与小程序账号相同）">
                                <img src="/images/login_name.png" slot="prepend" alt="">
                            </i-input>
                        </form-item>
                        <form-item label="">
                            <i-input v-model="password" type="password" placeholder="密码（密码与小程序密码相同）">
                                <img src="/images/login_pwd.png" slot="prepend" alt="">
                            </i-input>
                        </form-item>
                        <form-item style="text-align:center;margin-bottom: 0;">
                            <i-button class="submitBtn" shape="circle" @click="onBlur">登录</i-button>
                        </form-item>
                    </i-form>
                    <div class="loginEWM" v-show="loginStep == 1">
                        <p class="loginTitle">扫码确认登录</p>
                        <div id="work_code">
                        </div>
                        <div class="loginTitle loginHint" @click="clickSmsButton">
                            <!-- (如果尚未开通企业微信，请联系HR) -->
                            无法扫码，使用<a>验证码</a>登录
                        </div>
                    </div>
                    <i-form class="loginBox" v-if="loginStep == 2">
                        <p class="loginTitle">验证码登录</p>
                        <p class="error" v-show="errorMsg.length > 0">
                            @{{errorMsg}}
                        </p>
                        <form-item label="">
                            <i-input v-model="smscode" placeholder="验证码">
                                <img src="/images/login_pwd.png" slot="prepend" alt="">
                            </i-input>
                            <form-item style="text-align:center;margin-bottom: 0;">
                                <i-button class="smsSubmitBtn" shape="circle" :disabled="smsButtonDisabled" @click="getSmsCode">
                                    获取验证码
                                    <span v-if="smsButtonTime > 0">(@{{ smsButtonTime }})</span>
                                </i-button>
                                <i-button class="smsSubmitBtn" shape="circle" @click="onBlur">登录</i-button>
                            </form-item>
                        </form-item>
                    </i-form>
                </tab-pane>
                <!-- <tab-pane label="短信登录" name="sms"></tab-pane> -->
                        
            <!-- </Tabs> -->
            
        </div>

    </div>
</body>
<script type="text/javascript">
    var vm = new Vue({
        el: "#layout",
        data : {
            mobile : '',
            password : '',
            smscode : '',
            smsButtonDisabled : false,
            smsButtonTime : 0,
            errorMsg : '{{$error_msg}}',
            loginStep: 0,
        },
        methods:{
            onBlur(){
                if (this.mobile.length > 0 && this.password.length > 0) {
                    this.doLogin()
                } else {
                    this.errorMsg = '请填写用户名和密码'
                }
            },
            showCode(state){
                window.WwLogin({
                        "id" : "work_code",  
                        "appid" : "{{$appid}}",
                        "agentid" : "{{$agentid}}",
                        "redirect_uri" :"https://{{config('app.url')}}/cp/home/wxcode",
                        "state" : state,
                        "href" : "https://{{config('app.url')}}/css/cp_login.css",
                })
            },
            doLogin(){
                let _this = this
                $.ajax({
                    type: "POST",
                    url: '/cp/home/login',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        mobile:this.mobile,
                        password:this.password,
                        smscode: this.smscode,
                    },
                    dataType: "json",
                    success: function (resp) {
                        if (resp.code === 0) {
                            if (resp.data.type == 'loginResult') {
                                window.location.href = '/cp/home/welcome'
                            } else if (resp.data.type == 'state') {
                                _this.loginStep = 1;
                                _this.showCode(resp.data.result);
                            }
                        } else {
                            // 密码错误则跳到第一步
                            if (resp.code == 11000012) {
                                _this.loginStep = 0
                                _this.smscode = ''
                            }
                            _this.errorMsg = resp.msg + '[' + resp.code + ']'
                        }
                    },
                    error(){
                        _this.errorMsg = '页面失效，刷新后重试'
                    }
                })
            },
            clickSmsButton () {
                let _this = this
                _this.loginStep = 2
            },
            getSmsCode () {
                // 点击后 按钮不可用
                let _this =  this
                this.smsButtonDisabled = true
                this.smsButtonTime = 60
                let buttonInterval = setInterval(function () {
                    if (--_this.smsButtonTime == 0) {
                        clearInterval(buttonInterval)
                        _this.smsButtonDisabled = false
                    }
                }, 1000)

                // 发送验证码
                $.ajax({
                    type: "POST",
                    url: '/cp/home/login/sendSms',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        mobile:this.mobile,
                        password:this.password,
                    },
                    dataType: "json",
                    success: function (resp) {
                        if (resp.code === 0) {
                            // _this.loginStep = 1;

                        } else {
                            _this.errorMsg = resp.msg + '[' + resp.code + ']'
                        }
                    },
                    error(){
                        _this.errorMsg = '页面失效，刷新后重试'
                    }
                })
            },
        },
        mounted(){
        },
    })
</script>
</html>
