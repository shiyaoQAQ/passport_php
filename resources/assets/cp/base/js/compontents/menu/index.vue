<template>
    <div class="menu">
        <Menu mode="horizontal" theme="dark" active-key="1" style="padding-left: 10px">
            <Dropdown
                v-for="(menu, firstName, mIndex) in layoutList.show_access_menu_list"
                :key="mIndex"
                v-if="!menuFold || (menuFold && mIndex < foldNum)">
                <a class="DropdownTitle" href="javascript:void(0)">
                    <Icon :type="menu.logo"></Icon>
                    {{firstName}}
                    <Icon type="ios-arrow-down"></Icon>
                </a>
                <DropdownMenu slot="list">
                    <div v-for="item,path in menu.menu_list" :key="path">
                        <Dropdown 
                            class="child_dropdown"
                            style="width: auto;display: block;"
                            placement="right"
                            v-if="checkData(item)">
                            <DropdownItem>
                                {{path}}
                                <Icon type="ios-arrow-right"></Icon>
                            </DropdownItem>
                            <DropdownMenu slot="list">
                                <DropdownItem v-for="(senName,index) in item" :key="index">
                                    <a :href="index">{{senName}}</a>
                                </DropdownItem>
                            </DropdownMenu>
                        </Dropdown>
                        <DropdownItem v-else>
                            <a :href="path">{{item}}</a>
                        </DropdownItem>
                    </div>
                </DropdownMenu>
            </Dropdown>
            <Dropdown v-if="menuFold">
                <a class="DropdownTitle" href="javascript:void(0)">
                    <Icon type="more"></Icon>
                    更多
                    <Icon type="ios-arrow-down"></Icon>
                </a>
                <DropdownMenu slot="list">
                    <Dropdown 
                    class="child_dropdown"
                    style="width: auto;display: block;"
                    placement="right"
                    v-for="(menu, firstName, mIndex) in layoutList.show_access_menu_list"
                    :key="mIndex"
                    v-if="mIndex >= foldNum">
                        <DropdownItem>
                            {{firstName}}
                            <Icon type="ios-arrow-right"></Icon>
                        </DropdownItem>
                        <DropdownMenu slot="list">
                            <div v-for="item,path in menu.menu_list" :key="path">
                                <Dropdown 
                                    class="child_dropdown"
                                    style="width: auto;display: block;"
                                    placement="right"
                                    v-if="checkData(item)">
                                    <DropdownItem>
                                        {{path}}
                                        <Icon type="ios-arrow-right"></Icon>
                                    </DropdownItem>
                                    <DropdownMenu slot="list">
                                        <DropdownItem v-for="(senName,index) in item" :key="index">
                                            <a :href="index">{{senName}}</a>
                                        </DropdownItem>
                                    </DropdownMenu>
                                </Dropdown>
                                <DropdownItem v-else>
                                    <a :href="path">{{item}}</a>
                                </DropdownItem>
                            </div>
                        </DropdownMenu>
                    </Dropdown>
                    <Dropdown 
                    class="child_dropdown"
                    style="width: auto;display: block;"
                    placement="right">
                        <DropdownItem style="padding: 0;">
                            <!-- <a target="_blank" href="http://{{config('app.url')}}/admin">EcTouch</a> -->
                        </DropdownItem>
                    </Dropdown>
                </DropdownMenu>
            </Dropdown>
            <Submenu
                v-for="(access, accessKey) in layoutList.show_access_list"
                :key="accessKey"
                :name="'access_' + accessKey"
                style="float: right;margin-right: 10px; padding-left: 10px;">
                <template slot="title">
                    {{access.desc}}:{{access.options[access.choose]}}
                </template>
                    <Menu-item
                        v-for="ov,okey in  access.options"
                        :key="okey"
                        :name="accessKey + '-' + okey">
                        <a 
                            class="show_access" 
                            :access-key="accessKey"
                            :access-val="okey">
                            {{ov}}
                        </a>
                    </Menu-item>
            </Submenu>
            <Submenu name="10" style="float: right;margin-right: 10px; padding-left: 10px;">
                <template slot="title">
                    <Icon type="person"></Icon>
                    {{layoutList.user_name}}
                </template>
                    <Menu-item name="10-1"><a :href="layoutList.change_password" target="_blank">修改密码</a></Menu-item>
                    <Menu-item name="10-2"><a :href="layoutList.logout">退出登录</a></Menu-item>
            </Submenu>
        </Menu>
    </div>
</template>

<script>
let Base64 = require('js-base64').Base64;
export default {
    data() {
        return {
            layoutList : {
                'show_access_menu_list' : {},
                'show_access_list' : {},
            },
            menuFold: false,
        }
    },
    methods: {
        layout(){
            this.$Request({
                url: '/cp/layout',
                method:'get',
                data: {
                    controller: Base64.encode(location.href),
                },
                success: (res) => {
                    this.layoutList = res.data
                    this.windowResize()
                }
            })
        },
        checkData: function(data) {
            if (data instanceof Object) {
                return true;                    
            }else {
                return false;  
            }
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
    created(){
        this.layout()
    },
    mounted() {
        window.onresize = () => {
            this.windowResize()
        }
    }
}
</script>

<style lang="less" scoped>
.menu {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 40px;
    z-index: 500;
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
        color: #ccc !important;
        text-decoration: none;
    }
    .ivu-dropdown-item {
        padding: 0
    }
    .ivu-dropdown-item a {
        color: #4d545a !important;
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
		color: rgb(73, 81, 96 ) !important;
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
