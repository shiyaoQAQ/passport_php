<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  @if(config('app.debug'))
      <title>测|@yield('title')</title>
  @else
      <title>@yield('title')-掌上辅材</title>
  @endif
  <link rel="stylesheet" href="/css/iview2.14.3.css">
  <link rel="stylesheet" href="/css/bootstrap.min.css">
  <link rel="stylesheet" href="/css/flatpickr.min.css">
  <link rel="icon"   href="/images/favicon.ico"  type="image/x-icon"  />
  <link rel="Shortcut Icon" href="/images/favicon.ico"  type="image/x-icon" />
  @if(config('app.debug'))
      <script src="/js/vue.dev@2.5.13.js"></script>
  @else
      <script src="/js/vue.min@2.5.9.js"></script>
      <script src="https://browser.sentry-cdn.com/5.6.2/bundle.min.js" integrity="sha384-H4chu/XQ3ztniOYTpWo+kwec6yx3KQutpNkHiKyeY05XCZwCSap7KSwahg16pzJo" crossorigin="anonymous"></script>
      <script src="https://browser.sentry-cdn.com/5.6.2/vue.min.js" crossorigin="anonymous"></script>
      <script>
          Sentry.init({
              dsn: 'https://2b3d4abe5eed4e0abb313398387e1dc8@sentry.youcai123.cn/3',
              integrations: [new Sentry.Integrations.Vue({Vue, attachProps: true})],
          });
      </script>
  @endif
  <script src="/js/iview@2.9.0.js"></script>
  <script src="/js/iview2.14.3.min.js"></script>
  <script src="/js/jquery.min.js"></script>
  <script src="/js/bootstrap.min.js"></script>
  <script src="/js/flatpickr.min.js"></script>
  <script src="/js/flatpickr.l10n.zh.js"></script>
  <script src="/js/vue-clipboard.min.js"></script>
  <style>
    li {
        list-style: none !important;
    }
    .ivu-dropdown-rel {
        padding: 0 10px;
    }
    .child_dropdown .ivu-dropdown-rel {
        padding: 0;
    }
    .child_dropdown .ivu-dropdown-rel .ivu-dropdown-item{
        padding: 7px 16px;
    }
    .ivu-dropdown-rel .DropdownTitle{
        color: #ccc;
        text-decoration: none;
    }
    .ivu-dropdown-item {
        padding: 0
    }
    .ivu-dropdown-item a {
        color: #4d545a;
        display: inline-block;
        width: 100%;
        padding: 7px 16px;
    }
    .ivu-select-dropdown .ivu-dropdown-menu{
        max-height: 500px;
        overflow-y: auto;
        overflow-x: hidden;
    }
    .ivu-menu-horizontal {
		height: 40px;
		line-height: 40px;
		position: fixed;
		top: 0;
		width: 100%;
    }
    .ivu-menu-item {
      padding: 0 !important;
    }
    .ivu-menu-item a {
		white: 100%;
		height: 100%;
		display: block;
    padding: 7px 16px 8px;
		color: rgb(73, 81, 96 );
    }
    .ivu-icon {
      margin-right: 2px !important;
    }
    .ivu-menu-horizontal .ivu-menu-item, .ivu-menu-horizontal .ivu-menu-submenu {
      padding-right: 0px;
    }
    [v-cloak]{
		display: none !important;
    }
    .layout-content {
      margin-top: 50px;
    }
	.liTitle {
		color: #999;
		padding: 0px 8px;
    }
    .version {
        text-align: center;
        border: 1px solid #ffebcc;
        background-color: #fff5e6;
        border-radius: 6px;
        padding: 8px;
        margin: 0 15px;
        display: none;
    }
    .version i{
        display: inline-block;
        font-style: normal;
        font-weight: 700;
        color: #fff;
        background-color: #f90;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        margin-right: 20px;
    }
    .globalHandle {
        background-color: #337ab7;
        border: 1px solid #337ab7;
        border-radius: 2px;
        color: #fff;
        padding: 0 3px;
        margin-left: 5px;
    }
    .menuList .ivu-menu-drop-list{
        max-height: 500px;
    }
    html,body {
        height: 100%;
    }
    .layout-content,.layout-content-main {
        min-height: 100%;
    }

    /* 活动相关css */
    .activity_ample_label {
        background-color: #4a90e2;
        color: #fff;
        border-radius: 10px;
        padding: 2px 8px;
        margin-right: 5px;
    }
    .activity_ample_tip {
        margin-left: 10px;
    }
    .activity_ample_tip img, .activity_ample_content img{
        width: 20px;
        height: 20px;
        display: inline-block;
        vertical-align: top;
    }
    .activity_ample_content {
        display: block;
        color: #fbb11b;
    }

    /* 组织架构 */
    .org-tree-container .org-tree {
        display: table;
        text-align: center;
        width: 100%;
    }
    .org-tree-container .org-tree:before, .org-tree-container .org-tree:after {
        content: '';
        display: table;
    }
    .org-tree-container .org-tree:after {
        clear: both;
    }

    .org-tree-node, .org-tree-node-children {
        position: relative;
        margin: 0;
        padding: 0;
        list-style-type: none;
        text-align: center;
    }
    .org-tree-node-children:before, .org-tree-node-children:after {
        transition: all .35s;
    }
    .org-tree-node-label {
        position: relative;
        display: inline-block;
    }
    .org-tree-node-label .org-tree-node-label-inner {
        padding: 3px 5px;
        text-align: center;
        border-radius: 3px;
        box-shadow: 0 1px 5px rgba(0, 0, 0, .15);
        min-width: 100px;
        white-space: nowrap;
    }

    .org-tree-node-btn {
        position: absolute;
        top: 100%;
        left: 50%;
        width: 20px;
        height: 20px;
        z-index: 10;
        margin-left: -11px;
        margin-top: 9px;
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 50%;
        box-shadow: 0 0 2px rgba(0, 0, 0, .15);
        cursor: pointer;
        transition: all .35s ease;
    }
    .org-tree-node-btn:hover {
        background-color: #e7e8e9;
        transform: scale(1.15);
    }
    .org-tree-node-btn:before, .org-tree-node-btn:after {
        content: '';
        position: absolute;
    }
    .org-tree-node-btn:before {
        top: 50%;
        left: 4px;
        right: 4px;
        height: 0;
        border-top: 1px solid #ccc;
    }
    .org-tree-node-btn:after {
        top: 4px;
        left: 50%;
        bottom: 4px;
        width: 0;
        border-left: 1px solid #ccc;
    }
    .org-tree-node-btn.expanded:after {
        border: none;
    }

    .org-tree-node {
        padding-top: 20px;
        display: table-cell;
        vertical-align: top;
    }
    .org-tree-node.is-leaf, .org-tree-node.collapsed {
        padding-left: 10px;
        padding-right: 10px;
    }
    .org-tree-node:before, .org-tree-node:after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 50%;
        height: 19px;
    }
    .org-tree-node:after {
        left: 50%;
        border-left: 1px solid #ddd;
    }
    .org-tree-node:not(:first-child):before, .org-tree-node:not(:last-child):after {
        border-top: 1px solid #ddd;
    }

    .collapsable .org-tree-node.collapsed {
        padding-bottom: 30px;
    }
    .collapsable .org-tree-node.collapsed .org-tree-node-label:after {
        content: '';
        position: absolute;
        top: 100%;
        left: 0;
        width: 50%;
        height: 20px;
        border-right: 1px solid #ddd;
    }

    .org-tree > .org-tree-node {
        padding-top: 0;
    }
    .org-tree > .org-tree-node:after {
        border-left: 0;
    }

    .org-tree-node-children {
        padding-top: 20px;
        display: table;
    }
    .org-tree-node-children:before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        width: 0;
        height: 20px;
        border-left: 1px solid #ddd;
    }
    .org-tree-node-children:after {
        content: '';
        display: table;
        clear: both;
    }

    .horizontal .org-tree-node {
        display: table-cell;
        float: none;
        padding-top: 0;
        padding-left: 20px;
    }
    
    .horizontal .org-tree-node.is-leaf, .horizontal .org-tree-node.collapsed {
        padding-top: 10px;
        padding-bottom: 10px;
    }
    .horizontal .org-tree-node:before, .horizontal .org-tree-node:after {
        width: 19px;
        height: 50%;
    }
    .horizontal .org-tree-node:after {
        top: 50%;
        left: 0;
        border-left: 0;
    }
    .horizontal .org-tree-node:only-child:before {
        top: 1px;
        border-bottom: 1px solid #ddd;
    }
    .horizontal .org-tree-node:not(:first-child):before, .horizontal .org-tree-node:not(:last-child):after {
        border-top: 0;
        border-left: 1px solid #ddd;
    }
    .horizontal .org-tree-node:not(:only-child):after {
        border-top: 1px solid #ddd;
    }
    .horizontal .org-tree-node .org-tree-node-inner {
        display: table;
    }
    .horizontal .org-tree-node-label {
        display: table-cell;
        vertical-align: middle;
    }
    .horizontal.collapsable .org-tree-node.collapsed {
        padding-right: 30px;
    }
    .horizontal.collapsable .org-tree-node.collapsed .org-tree-node-label:after {
        top: 0;
        left: 100%;
        width: 20px;
        height: 50%;
        border-right: 0;
        border-bottom: 1px solid #ddd;
    }
    .horizontal .org-tree-node-btn {
        top: 50%;
        left: 100%;
        margin-top: -11px;
        margin-left: 9px;
    }
    .horizontal > .org-tree-node:only-child:before {
        border-bottom: 0;
    }
    .horizontal .org-tree-node-children {
        display: table-cell;
        padding-top: 0;
        padding-left: 20px;
    }
    .horizontal .org-tree-node-children:before {
        top: 50%;
        left: 0;
        width: 20px;
        height: 0;
        border-left: 0;
        border-top: 1px solid #ddd;
    }
    .horizontal .org-tree-node-children:after {
        display: none;
    }
    .horizontal .org-tree-node-children > .org-tree-node {
        display: block;
    }
  </style>
</head>

<body>
  <div id="layout" v-cloak>
    <i-menu mode="horizontal" theme="dark" active-key="1" style="padding-left: 3px">
        <!-- @foreach($show_access_menu_list as $firstName => $meneDetail)
            <Dropdown>
                <a class="DropdownTitle" href="javascript:void(0)">
                    <Icon type="{{$meneDetail['logo']}}"></Icon>
                    {{$firstName}}
                    <Icon type="ios-arrow-down"></Icon>
                </a>
                <dropdown-menu slot="list">
                @foreach($meneDetail['menu_list'] as $path => $senName)
                    @if(is_array($senName))
                        <Dropdown class="child_dropdown" style="width: auto;display: block;" placement="right">
                            <dropdown-item>
                                {{$path}}
                                <Icon type="ios-arrow-right"></Icon>
                            </dropdown-item>
                            <dropdown-menu slot="list">
                                @foreach($senName as $path => $senName)
                                <dropdown-item><a href="{{$path}}">{{$senName}}</a></dropdown-item>
                                @endforeach
                            </dropdown-menu>
                        </Dropdown>
                    @else
                    <dropdown-item>
                        <a href="{{$path}}">{{$senName}}</a>
                    </dropdown-item>
                    @endif
                @endforeach
                </dropdown-menu>
            </Dropdown>
        @endforeach -->

        <!-- @foreach($show_access_list as $accessKey => $accessDetai)
            <Submenu name="access_{{$accessKey}}" style="float: right;margin-right: 10px; padding-left: 10px;">
                <template slot="title">
                    {{$accessDetai['desc']}}:{{$accessDetai['options'][$accessDetai['choose']]}}
                </template>
                    @foreach($accessDetai['options'] as $okey => $ov)
                        <Menu-item name="{{$accessKey}}-{{$okey}}"><a href="" class="show_access" 
                            access-key="{{$accessKey}}" access-val="{{$okey}}"
                            >{{$ov}}</a></Menu-item>
                    @endforeach
            </Submenu>
        @endforeach -->

        <Dropdown v-for="(menu, firstName, mIndex) in showAccessMenuList" v-if="!menuFold || (menuFold && mIndex < foldNum)">
            <a class="DropdownTitle" href="javascript:void(0)">
                <Icon :type="menu.logo"></Icon>
                @{{firstName}}
                <Icon type="ios-arrow-down"></Icon>
            </a>
            <dropdown-menu slot="list">
                <div v-for="item,path in menu.menu_list">
                    <Dropdown 
                        class="child_dropdown"
                        style="width: auto;display: block;"
                        placement="right"
                        v-if="checkData(item)">
                        <dropdown-item>
                            @{{path}}
                            <Icon type="ios-arrow-right"></Icon>
                        </dropdown-item>
                        <dropdown-menu slot="list">
                            <dropdown-item v-for="(senName,index) in item">
                                <a :href="index">@{{senName}}</a>
                            </dropdown-item>
                        </dropdown-menu>
                    </Dropdown>
                    <dropdown-item v-else>
                        <a :href="path">@{{item}}</a>
                    </dropdown-item>
                </div>
            </dropdown-menu>
        </Dropdown>
        <Dropdown v-if="menuFold">
            <a class="DropdownTitle" href="javascript:void(0)">
                <Icon type="more"></Icon>
                更多
                <Icon type="ios-arrow-down"></Icon>
            </a>
            <dropdown-menu slot="list">
                <Dropdown 
                class="child_dropdown"
                style="width: auto;display: block;"
                placement="right"
                v-for="(menu, firstName, mIndex) in showAccessMenuList"
                v-if="mIndex >= foldNum">
                    <dropdown-item>
                        @{{firstName}}
                        <Icon type="ios-arrow-right"></Icon>
                    </dropdown-item>
                    <dropdown-menu slot="list">
                        <div v-for="item,path in menu.menu_list">
                            <Dropdown 
                                class="child_dropdown"
                                style="width: auto;display: block;"
                                placement="right"
                                v-if="checkData(item)">
                                <dropdown-item>
                                    @{{path}}
                                    <Icon type="ios-arrow-right"></Icon>
                                </dropdown-item>
                                <dropdown-menu slot="list">
                                    <dropdown-item v-for="(senName,index) in item">
                                        <a :href="index">@{{senName}}</a>
                                    </dropdown-item>
                                </dropdown-menu>
                            </Dropdown>
                            <dropdown-item v-else>
                                <a :href="path">@{{item}}</a>
                            </dropdown-item>
                        </div>
                    </dropdown-menu>
                </Dropdown>
                <Dropdown 
                class="child_dropdown"
                style="width: auto;display: block;"
                placement="right">
                    <dropdown-item style="padding: 0;">
                        <a target="_blank" href="http://{{config('app.url')}}/admin">EcTouch</a>
                    </dropdown-item>
                </Dropdown>
            </dropdown-menu>
        </Dropdown>
        <!-- <Dropdown v-if="!menuFold">
            <a class="DropdownTitle" href="javascript:void(0)">
                <Icon type="ios-redo"></Icon>
                回ECT
                <Icon type="ios-arrow-down"></Icon>
            </a>
            <dropdown-menu slot="list">
                <dropdown-item>
                    <a target="_blank" href="http://{{config('app.url')}}/admin">EcTouch</a>
                </dropdown-item>
            </dropdown-menu>
        </Dropdown> -->

        <Submenu
            v-for="(access, accessKey) in showAccessList"
            :name="'access_' + accessKey"
            style="float: right;margin-right: 10px; padding-left: 10px;">
            <template slot="title">
                @{{access.desc}}:@{{access.options[access.choose]}}
            </template>
                <Menu-item
                    v-for="ov,okey in  access.options"
                    :name="accessKey + '-' + okey">
                    <a 
                        class="show_access" 
                        :access-key="accessKey"
                        :access-val="okey">
                        @{{ov}}
                    </a>
                </Menu-item>
        </Submenu>
        <Submenu name="10" style="float: right;margin-right: 10px; padding-left: 10px;">
            <template slot="title">
                <Icon type="person"></Icon>
                {{$cp_base_user_name}}
            </template>
                <Menu-item name="10-1"><a href="/cp/user/password" target="_blank">修改密码</a></Menu-item>
                <Menu-item name="10-2"><a href="/cp/home/logout">退出登录</a></Menu-item>
        </Submenu>        
    </i-menu>

</div>
<!-- 组织架构组件 -->
<script>
    // 判断是否叶子节点
    function isLeaf(data, prop) {
        return !(Array.isArray(data[prop]) && data[prop].length > 0)
    }
    function render(h){
        const props = this._props
        return renderNode(h, props.data, this)
    }
    function renderNode(h, data, context) {
        const props = context.$props
        const cls = ['org-tree-node']
        const childNodes = []
        const children = data.child
        if (isLeaf(data, props.children)) {
            cls.push('is-leaf')
        } else if (!data[props.expand]) {
            cls.push('collapsed')
        }
        childNodes.push(renderLabel(h, data, context))
        if (data[props.expand] || props.collapsable) {
            childNodes.push(renderChildren(h, children, context))
        }
        
        return h('div', {
            domProps: {
                className: cls.join(' ')
            }
        }, childNodes)
    }
    // 创建展开折叠按钮
    function renderBtn(h, data, context) {
        const props = context.$props
        const expandHandler = context.$parent['onExpand']
        let cls = ['org-tree-node-btn']

        if (data[props.expand]) {
            cls.push('expanded')
        }

        return h('span', {
            domProps: {
                className: cls.join(' ')
            },
            on: {
                click: e => {
                    e.stopPropagation()
                    expandHandler && expandHandler(data)
                }
            }
        })
    }

    // 创建 label 节点
    function renderLabel (h, data, context) {
        const props = context.$props
        const clickHandler = context.$parent['onNodeClick']
        const childNodes = []
        const cls = ['org-tree-node-label-inner']
        childNodes.push(data.name)
        if (!props.collapsable && !isLeaf(data, props.children)) {
            childNodes.push(renderBtn(h, data, context))
        }

        if (data.isCheck) {
            cls.push('org-tree-node-label-inner-check')
        }

        return h('div', {
            domProps: {
                className: 'org-tree-node-label'
            }
        }, [h('div', {
            domProps: {
                className: cls.join(' ')
            },
            style: {},
            on: {
                click: e => {
                    clickHandler && clickHandler(e, data)
                }
            }
        }, childNodes)])
    }

    // 创建 node 子节点
    function renderChildren (h, list, context) {
        if (Array.isArray(list) && list.length) {
            const children = list.map(item => {
                return renderNode(h, item, context)
            })

            return h('div', {
                domProps: {
                    className: 'org-tree-node-children'
                }
            }, children)
        }
        return ''
    }
    Vue.component('orgTree', {
        props: {
            data: Object,
            collapsable: Boolean,
            expand: String,
            children: String,
        },
        render: render,
        mounted() {
        }
    })
</script>
<script>
    var cp = new Vue({
        el: "#layout",
        data: {
            menuFold: false,
            foldNum: 100,
            chrome_v: '',
            appUrl : '{{ config("app.url") }}',
            showAccessList: {!! json_encode($show_access_list) !!},
            showAccessMenuList: {!! json_encode($show_access_menu_list) !!},
        },
        methods: {
            checkData: function(data) {
                if (data instanceof Object) {
                    return true;                    
                }else {
                    return false;  
                }
            },
            alert: function(content, title="", type="success",successOk = null) {
                // const content = '<p>Content of dialog</p><p>Content of dialog</p>';
                switch (type) {
                    case 'info':
                        this.$Modal.info({
                            title: title,
                            content: content
                        });
                        break;
                    case 'success':
                        this.$Modal.success({
                            title: title,
                            content: content,
                            onOk() {
                                if (successOk != null) {
                                    successOk()
                                }
                            }
                        });
                        break;
                    case 'warning':
                        this.$Modal.warning({
                            title: title,
                            content: content
                        });
                        break;
                    case 'error':
                        this.$Modal.error({
                            title: title,
                            content: content
                        });
                        break;
                }
            },
            showAlert(AlretData) {
                let data = {
                    type     : AlretData.type     ? AlretData.type    : 'success',
                    title    : AlretData.title    ? AlretData.title   : '',
                    content  : AlretData.content  ? AlretData.content : '',
                    okText   : AlretData.okText   ? AlretData.okText  : '',
                    onOkFun  : AlretData.onOk     ? AlretData.onOk    : function () {},
                }
                switch (data.type) {
                    case 'info':
                        this.$Modal.info({
                            title: data.title,
                            content: data.content,
                            onOk() {
                                data.onOkFun()
                            }
                        });
                        break;
                    case 'success':
                        this.$Modal.success({
                            title: data.title,
                            content: data.content,
                            onOk() {
                                data.onOkFun()
                            }
                        });
                        break;
                    case 'warning':
                        this.$Modal.warning({
                            title: data.title,
                            content: data.content,
                            onOk() {
                                data.onOkFun()
                            }
                        });
                        break;
                    case 'error':
                        this.$Modal.error({
                            title: data.title,
                            content: data.content,
                            onOk() {
                                data.onOkFun()
                            }
                        });
                        break;
                }
            },
            confirm: function(content, okCallback=function(){}, cancelCallback=function(){}) {
                this.$Modal.confirm({
                    title: '',
                    content: content,
                    onOk: okCallback,
                    onCancel: cancelCallback,
                });
            },
            windowResize() {
                if (document.documentElement.offsetWidth <= 1440) {
                    this.foldNum = Math.floor((document.documentElement.offsetWidth - 350) / 90);
                    this.menuFold = true;
                }else {
                    this.menuFold = false;
                }
            }
        },
        created() {
            this.windowResize()
        },
        mounted() {
            var _this = this;
            window.onresize = function(){
                _this.windowResize()
            }
        }
    })
    $(function(){
        $('.show_access').click(function(){
            $.ajax({
                url:'/cp/access/selectAccess',
                type:'post', 
                dataType:'json',
                data:{access_key:$(this).attr('access-key'),access_val:$(this).attr('access-val')},
                success(resp){
                    alert(resp.msg);
                    if(resp.code == 0){
                        window.location.reload();
                    }
                },
            });
            return false;
        });
        $(document).on('click','.globalHandle',function (e) {
            e.preventDefault();
            var eventID = $(this).attr('data-id');
            $.ajax({
                url: '',
                type: 'get',
                data: {
                    user_id: eventID
                },
                dataType: 'json',
                success(res) {
                    if (res.code == 0) {
                        $(data.target).prev().text(res.data)
                    }
                }
            })
        })
    });

    var mobilehandle = Vue.component('mobilehandle', {
        template:   `<span>
                        <i style="font-style:normal">@{{mobileData}}</i> 
                        <i-button v-if="isShow" type="primary" size="small" @click="mobileShow">显示</i-button>
                        <i-button v-if="!isShow" type="primary" size="small" v-clipboard:copy="mobileData">复制</i-button>
                    </span>`,
        data() {
            return {
                mobileData: this.mobile,
                mobile_id: this.mobileId,
                isShow: true
            }
        },
        props: {
            mobile: null,
            mobileId: null
        },
        watch: {
            mobile(newVal,oldVal){
                this.isShow = true;
                this.mobileData = newVal;
            },
            mobileId(val){
                this.mobile_id = val;
            }
        },
        methods: {
            mobileShow() {
                var _this = this;
                $.ajax({
                    url: '/cp/access/maskMobile/' + this.mobile_id,
                    type: 'get',
                    data: {},
                    dataType: 'json',
                    success(res) {
                        if (res.code == 0) {
                            _this.isShow = false;
                            _this.mobileData = res.data
                        }
                    }
                })
            },
        },
        mounted() {
        },
    })
    // 活动-优品-标签
    var activity_ample_label = Vue.component('activity-ample-label',{
        template: "<span class='activity_ample_label'>@{{labelData.type_desc}}</span>",
        data() {
            return {
            }
        },
        props: {
            labelData: {}
        },
        mounted() {
        }
    })
    // 活动-优品-标签-单个
    var activity_ample_label_alone = Vue.component('activity-ample-label-alone',{
        template: "<span class='activity_ample_label'>@{{labelName}}</span>",
        data() {
            return {
            }
        },
        props: {
            labelName: {}
        },
        mounted() {
        }
    })
    // 活动-优品-tip活动内容
    var activity_ample_tip = Vue.component('activity-ample-tip',{
        template: '<Tooltip :content="tipData.desc" placement="bottom" transfer><span class="activity_ample_tip"><img src="http://zsfc-static.oss-cn-beijing.aliyuncs.com/data/attached/images/201810/1539312592722095427.png"> @{{tipData.type_desc}}<img src="/images/right.png"></span></Tooltip>',
        props: {
            tipData: {}
        },
    })
    // 活动-优品-活动内容
    var activity_ample_content = Vue.component('activity-ample-content',{
        template: "<span class='activity_ample_content'><img src='http://zsfc-static.oss-cn-beijing.aliyuncs.com/data/attached/images/201810/1539312592722095427.png'> @{{contentData.type_desc}}：@{{contentData.desc}}</span>",
        props: {
            contentData: {}
        }
    })
</script>
<div class="layout-content">
    <div class="layout-content-main">
        <p class="version"><i>!</i><span></span>下载地址：<a href="https://zsfc-static.oss-cn-beijing.aliyuncs.com/data/other/ChromeSetup.exe">点击下载</a></p>
        @yield('content')
    </div>   
</div>
</body>
<script>
    var arr = navigator.userAgent.split(' '); 
    var chromeVersion = '', chrome_v = '';
    if (/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)) {
        $('.version').css('display','none')
    }else {
        for(var i=0;i < arr.length;i++){
            if(/chrome/i.test(arr[i])) {
                chromeVersion = arr[i]
            }
        }
        if(chromeVersion){
            chromeVersion = Number(chromeVersion.split('/')[1].split('.')[0]);
            if (chromeVersion < 60) {
                $('.version').css('display','block')            
                chrome_v = '版本过低 或 您使用的不是谷歌浏览器，请下载最新版本的谷歌浏览器。'
                $('.version span').text(chrome_v)
            }else {
                $('.version').css('display','none')
            }
        }else {
            $('.version').css('display','block')            
            chrome_v = '您使用的浏览器可能会导致部分功能无法正常使用，建议您更换为谷歌浏览器。'
            $('.version span').text(chrome_v)
        }
    }
</script>

</html>
