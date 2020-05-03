@extends('admin.layout')
@section('title', '快速安全登录')
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        .form_content {
            position: relative;
        }
        .form_content .formOauth{
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translateX(-50%) translateY(-50%);
            text-align: center;
        }
    </style>
    <div id="editUser" v-cloak>
        <Card>
            <Tabs>
                <tab-pane label="快速安全登录">
                    <i-form class="form_content" :label-width="0" ref="formData" :model="formData">
                        <form id="formOauth" class="formOauth" method="POST" action="/cp/oauth/authorization" accept-charset="UTF-8">
                            {{ csrf_field() }}
                            <input type="hidden" name="client_id" value="{{ $client_id }}" >
                            <input type="hidden" name="redirect_uri" value="{{ $redirect_uri }}" >
                            <input type="hidden" name="response_type" value="{{ $response_type }}" >
                            <input type="hidden" name="scope" value="{{ $scope }}" >
                            <input type="hidden" name="state" value="{{ $state }}" >
                            <form-item label="">
                                <h5>您好，{{ $cp_base_user_name }}</h5>
                                <h5>{{ $clientName }}，请求您授权登录</h5>
                            </form-item>
                            <form-item style="margin-top:30px">
                                <i-button type="primary" @click="submitForm()">授权登录</i-button>
                            </form-item>
                        </form>
                    </i-form>
                </tab-pane>
            </Tabs>
        </Card>
    </div>
<script>
    var vm = new Vue({
        el: '#editUser',
        data() {
            return {
                formData:{
                },
            }
        },
        computed: {},
        methods: {
            submitForm() {
                $('#formOauth').submit()
            }
        },
        created(){
        },
        mounted(){
        }
   });
</script>

@endsection
