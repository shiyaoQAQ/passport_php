@extends('admin.layout')

@section('title', '单个权限编辑')

@section('content')
<style type="text/css">
body{font:12px/1.8 "宋体";overflow-y:scroll}
html,body{height:100%;margin:0;padding:0;}
.btn{margin-bottom: 12px;font:12px/1.8 "宋体";}
.clear{clear:both;}
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
</style>
<body>
    <div style="margin:0 20px">
        <div>
            <button class="btn">没有访问权限</button>
            <button class="btn btn-success">有权限，所在城市</button>
<!--             <button class="btn btn-info">有权限，仅下属</button>
            <button class="btn btn-primary">有权限，仅自己</button>
            <button class="btn btn-warning">有权限，所在城市+下属</button>
            <button class="btn btn-all">有权限，所在城市+自己</button>
            <button class="btn btn-danger">有权限，下属+自己</button>
            <button class="btn btn-default">有权限，所在城市+下属+自己</button> -->
        </div>
        <!-- <div>权限显示说明：（查询的数据情况，需要具体的代码实现支持，如同一个节点在多个城市，配置不同的权限，显示叠加的人总和）</div> -->
        <ul class="nav nav-tabs">
            <li role="presentation" class="active"><a href="#">部门权限编辑</a></li>
            <input type="hidden" value="{{$id}}" name="did">
        </ul>
        <br> 
        <table class="table table-bordered">
            <tr>
                <td style="width:70px" class="active">部门名称</td>
                <td>{{$depart_info['name']}}</td>
            </tr>
            <tr>
                <td class="active">权限详情</td>
                <td>
                    <table class="table table-bordered" id="depart_group_list">
                        <tbody>
                            @foreach ($action_list as $k => $action)
                            <tr>
                                <td><b>{{$action['desc']}}({{$k}})</b></td>
                            </tr>       
                            <tr>
                                <td>
                                    @foreach ($action['action'] as $ak => $av)
                                        <button class="btn action-node" controller="{{$av['controller']}}" title="{{$av['action']}}" choose="0" inherit="0" data-limit="0">{{$av['desc']}}<span class="action_desc"></span></button>
                                    @endforeach
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table> 
                </td>
            </tr>
            <tr>
                <td class="active">权限操作</td>
                <td><button class="btn btn-primary" id="save-action">保存权限设置</button></td>
            </tr>
        </table>
    </div>
<div id="preview-box" style="display:none; position: absolute; top: 40%; left:25%; padding: 2px; border: 1px solid #E8E9F7; background-color: white; z-index:1002; overflow: auto;">
    <input type="hidden" name="choose_title" id="choose_title" value="">
    <input type="hidden" name="choose_controller" id="choose_controller" value="">
    <table class="table table-bordered">
        <tr>
            <td>所在城市数据</td>
            <td><input type="checkbox" name="set_data_limit0" value="0"></td>
        </tr>
        <tr>
            <td>仅下属数据</td>
            <td><input type="checkbox" name="set_data_limit1" value="1"></td>
        </tr>
        <tr>
            <td>仅自己数据</td>
            <td><input type="checkbox" name="set_data_limit2" value="2"></td>
        </tr>
        <tr>
            <td>操作</td>
            <td><button class="btn btn-primary btn-xs" id="action-set">确定</button></td>
        </tr>
    </table>
</div>
{{csrf_field()}}
</body>
</html>
<script type="text/javascript">
$(function(){
    renderChooseAction();
    $(".action-node").on("contextmenu",dbChooseAction);
    $("#save-action").on('click',saveAction);
    $(".action-node").on('click',chooseAction);
    //右键确认后
    $('#action-set').click(function(){
        $("#preview-box").hide();
        var title = $('#choose_title').val();
        var controller = $('#choose_controller').val();
        var obj_action = $('button[controller='+controller+'][title='+title+']');
        var limit0 = $('input[name=set_data_limit0]:checked').length;
        var limit1 = $('input[name=set_data_limit1]:checked').length;
        var limit2 = $('input[name=set_data_limit2]:checked').length;
        if(limit0==1 && limit1==0 && limit2==0){
            obj_action.attr('data-limit',0);
            changebtncolor(obj_action,0);
        }else if(limit0==0 && limit1==1 && limit2==0){
            obj_action.attr('data-limit',1);
            changebtncolor(obj_action,1);
        }else if(limit0==0 && limit1==0 && limit2==1){
            obj_action.attr('data-limit',2);
            changebtncolor(obj_action,2);
        }else if(limit0==1 && limit1==1 && limit2==0){
            obj_action.attr('data-limit',3);
            changebtncolor(obj_action,3);
        }else if(limit0==1 && limit1==0 && limit2==1){
            obj_action.attr('data-limit',4);
            changebtncolor(obj_action,4);
        }else if(limit0==0 && limit1==1 && limit2==1){
            obj_action.attr('data-limit',5);
            changebtncolor(obj_action,5);
        }else if(limit0==1 && limit1==1 && limit2==1){
            obj_action.attr('data-limit',6);
            changebtncolor(obj_action,6);
        }else {
            alert("修改时，至少选择一个");
        }
    });
});
function escapeJquery(srcString) {
    // 转义之后的结果
    var escapseResult = srcString;

    // javascript正则表达式中的特殊字符
    var jsSpecialChars = ["\\", "^", "$", "*", "?", ".", "+", "(", ")", "[",
        "]", "|", "{", "}"];

    // jquery中的特殊字符,不是正则表达式中的特殊字符
    var jquerySpecialChars = ["~", "`", "@", "#", "%", "&", "=", "'", "\"",
        ":", ";", "<", ">", ",", "/"];

    for (var i = 0; i < jsSpecialChars.length; i++) {
        escapseResult = escapseResult.replace(new RegExp("\\"
                + jsSpecialChars[i], "g"), "\\"
                + jsSpecialChars[i]);
    }

    for (var i = 0; i < jquerySpecialChars.length; i++) {
        escapseResult = escapseResult.replace(new RegExp(jquerySpecialChars[i],
                "g"), "\\" + jquerySpecialChars[i]);
    }

    return escapseResult;
}
//页面初始化显示
function renderChooseAction(){
    var list = {!!$action_info_json!!};
    if(list != null){
        $.each(list,function(i,action){
            var node = $(".action-node[controller="+escapeJquery(action.controller)+"][title="+action.action+"]");
            changebtncolor(node,action.data_limit)
            node.attr('choose',1);
            node.attr('data-limit',action.data_limit);
        });
    }
}
//节点颜色
function changebtncolor(node ,data_limit) {
    if(data_limit == 0){
        node.addClass('btn-success');
        node.removeClass('btn-all');
        node.removeClass('btn-primary');
        node.removeClass('btn-info');
        node.removeClass('btn-warning');
        node.removeClass('btn-danger');
        node.removeClass('btn-default');
    }else if(data_limit == 1){
        node.addClass('btn-info');
        node.removeClass('btn-all');
        node.removeClass('btn-primary');
        node.removeClass('btn-success');
        node.removeClass('btn-warning');
        node.removeClass('btn-danger');
        node.removeClass('btn-danger');
    }else if(data_limit == 2){
        node.addClass('btn-primary');
        node.removeClass('btn-all');
        node.removeClass('btn-success');
        node.removeClass('btn-info');
        node.removeClass('btn-warning');
        node.removeClass('btn-danger');
        node.removeClass('btn-default');
    }else if(data_limit == 3){
        node.addClass('btn-warning');
        node.removeClass('btn-all');
        node.removeClass('btn-primary');
        node.removeClass('btn-info');
        node.removeClass('btn-success');
        node.removeClass('btn-danger');
        node.removeClass('btn-default');
    }else if(data_limit == 4){
        node.addClass('btn-all');
        node.removeClass('btn-danger');
        node.removeClass('btn-primary');
        node.removeClass('btn-info');
        node.removeClass('btn-warning');
        node.removeClass('btn-success');
        node.removeClass('btn-default');
    }else if(data_limit == 5){
        node.addClass('btn-danger');
        node.removeClass('btn-success');
        node.removeClass('btn-primary');
        node.removeClass('btn-info');
        node.removeClass('btn-warning');
        node.removeClass('btn-all');
        node.removeClass('btn-default');
    }else if(data_limit == 6){
        node.addClass('btn-default');
        node.removeClass('btn-all');
        node.removeClass('btn-success');
        node.removeClass('btn-primary');
        node.removeClass('btn-info');
        node.removeClass('btn-warning');
        node.removeClass('btn-danger');
    }
}
//节点右键选中显示
function dbChooseAction(){
    if($(this).attr('choose') != 1){
        return false;
    }
    $('#choose_title').val($(this).attr('title'));
    $('#choose_controller').val($(this).attr('controller'));
    if($(this).attr('data-limit') == 0){
        $('input[name=set_data_limit0]').attr('checked',true);
        $('input[name=set_data_limit1]').attr('checked',false);
        $('input[name=set_data_limit2]').attr('checked',false);
    }else if($(this).attr('data-limit') == 1){
        $('input[name=set_data_limit0]').attr('checked',false);
        $('input[name=set_data_limit1]').attr('checked',true);
        $('input[name=set_data_limit2]').attr('checked',false);
    }else if($(this).attr('data-limit') == 2){
        $('input[name=set_data_limit0]').attr('checked',false);
        $('input[name=set_data_limit1]').attr('checked',false);
        $('input[name=set_data_limit2]').attr('checked',true);
    }else if($(this).attr('data-limit') == 3){
        $('input[name=set_data_limit0]').attr('checked',true);
        $('input[name=set_data_limit1]').attr('checked',true);
        $('input[name=set_data_limit2]').attr('checked',false);
    }else if($(this).attr('data-limit') == 4){
        $('input[name=set_data_limit0]').attr('checked',true);
        $('input[name=set_data_limit1]').attr('checked',false);
        $('input[name=set_data_limit2]').attr('checked',true);
    }else if($(this).attr('data-limit') == 5){
        $('input[name=set_data_limit0]').attr('checked',false);
        $('input[name=set_data_limit1]').attr('checked',true);
        $('input[name=set_data_limit2]').attr('checked',true);
    }else if($(this).attr('data-limit') == 6){
        $('input[name=set_data_limit0]').attr('checked',true);
        $('input[name=set_data_limit1]').attr('checked',true);
        $('input[name=set_data_limit2]').attr('checked',true);
    }
    $("#preview-box").css('top', $(this).offset().top+36);
    $("#preview-box").css('left', $(this).offset().left);
    $("#preview-box").slideDown('fast');
    return false;
}
//节点选中样式显示
function chooseAction(){
    var choose = $(this).attr('choose');
    if(choose != 1){
        $(this).addClass('btn-success');
        $(this).attr('choose',1);
    }else{
        $(this).removeClass('btn-all');
        $(this).removeClass('btn-info');
        $(this).removeClass('btn-warning');
        $(this).removeClass('btn-success');
        $(this).removeClass('btn-danger');
        $(this).removeClass('btn-primary');
        $(this).removeClass('btn-default');
        $(this).attr('choose',0);
        $(this).attr('data-limit',0);
    }
}
function saveAction(){
    var contro_arr = [];
    var action_arr = [];
    var choose_arr = [];
    var limit_arr  = [];
    var i   = 0;
    var did = $('input[name=did]').val();
    $(".action-node").each(function(){
        contro_arr[i] = $(this).attr('controller');
        action_arr[i] = $(this).attr('title');
        choose_arr[i] = $(this).attr('choose');
        // limit_arr[i] = $(this).attr('data-limit');
        i++;
    }); 
    $.ajax({
        url:'/cp/longrentdepartment/setdepartaction',
        type:'POST',
        dataType:'JSON',
        data:{did:did,controller:contro_arr,action:action_arr,choose:choose_arr,_token:$('input[name=_token]').val()},
        success:function(data){
            alert(data.msg);
        },
    });
}
</script>
@endsection