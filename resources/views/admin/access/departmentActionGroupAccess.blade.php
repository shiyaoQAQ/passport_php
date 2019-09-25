@extends('admin.layout')

@section('title', '权限编辑')

@section('content')
<F3:include href="cp/common/header.html" /> 
<F3:include href="cp/common/sidebar.html" /> 
<style type="text/css">
body{
    font:12px/1.8 "宋体";
    overflow-y:scroll
}
html,body{height:100%;margin:0;padding:0;}
.strt-wrap{margin:10px;}
.strt-part{text-align:center;float:left;position:relative;}
.strt-part .line-v{position:relative;height:20px;width:100%;}
.strt-part .line-v span{display:block;background:#ccc;position:absolute;top:0;font-size:0;line-height:1px;width:1px;height:20px;left:50%;}
/*.strt-name{display:inline-block;padding:0 5px;height:24px;line-height:24px;border:1px solid #ccc;margin:0 10px;border-radius:3px;background:#f8f8f8;}*/
.strt-part .line-h{height:1px;display:block;background:#ccc;position:absolute;top:0;font-size:0;}
.strt-part .line-h-l{width:50%;left:0;}
.strt-part .line-h-c{width:100%;left:0;}
.strt-part .line-h-r{width:50%;right:0;}
.strt-block{float:left;}
.btn{
    margin-bottom: 12px;
    /* font:12px/1.8 "宋体"; */
}
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
.strt-name{display:inline-block;padding:0;margin:0 10px;border-radius:3px;background:#f8f8f8;}
.strt-part .table{width: auto;margin: 0;background-color: transparent;}
.strt-part .table>tbody>tr>td{padding: 0}
</style>
<body>
    <div style="margin:0 20px">
        <div>
            <button class="btn">没有访问权限</button>
            <button class="btn btn-success">有权限，所在城市</button>
        </div>
        <ul class="nav nav-tabs">
            <li role="presentation" class="active"><a href="#">权限编辑</a></li>
            <input type="hidden" value="{{$gid}}" name="gid">
        </ul>
        <br> 
        <table class="table table-bordered">
            <tr>
                <td style="width:70px" class="active">组名</td>
                <td>{{$group_info['name']}}({{$group_info['desc']}})</td>
            </tr>
            <tr>
                <td class="active">部门详情</td>
                <td>
                    <div class="strt-wrap">
                        
                    </div>
                    <div class="clear"></div>
                </td>
            </tr>
            <tr>
                <td class="active">部门操作</td>
                <td><button class="btn btn-primary" id="save-department">保存部门设置</button></td>
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
                                        <button class="btn btn-sm action-node" controller="{{$av['controller']}}" title="{{$av['action']}}" 
                                        choose="0" inherit="0" data-limit="0">{{$av['desc']}}<span class="action_desc"></span></button>
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
    loadTree(); 
    renderChooseAction();
    renderChosseDepart();
    $(".strt-wrap").on("click",'.strt-name',chooseNode);
    $(".action-node").on("contextmenu",dbChooseAction);
    $("#save-department").on('click',saveDepartment);
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
function renderChosseDepart(){
    var list = {!!$deaprt_info_json!!};
    if(list != null){
        $.each(list,function(i,depart){
            var node = $(".strt-name[departid="+depart.department_id+"]");
            node.css('background','#4cae4c');
            node.css('color','white');
            node.attr('choose',1);
        });
    }
}
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
function loadTree(){
    $.ajax({
         url : '/cp/longrentdepartment/ajaxdeparttree?pid=0',
         type : 'GET',
         async:false,
         success : function(data){
            $('.strt-wrap').html(data); 
         }
    });
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
function chooseNode(){
    var choose = $(this).attr('choose');
    if(choose != 1){
        $(this).css('background','#4cae4c');
        $(this).css('color','white');
        $(this).attr('choose',1);
    }else{
        $(this).css('background','#f8f8f8');
        $(this).css('color','#000000');
        $(this).attr('choose',0);
    }
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
function saveDepartment(){
    var id_arr = [];
    var choose = [];
    var gid = $('input[name=gid]').val();
    var i      = 0;
    $('.strt-name').each(function(){
        id_arr[i] = $(this).attr('departid');
        choose[i] = $(this).attr('choose');
        i++;
    });
    $.ajax({
        url:'/cp/longrentdepartment/setdepartmentgroup',
        type:'POST',
        dataType:'JSON',
        data:{gid:gid,id_arr:id_arr,choose:choose,_token:$('input[name=_token]').val()},
        success:function(data){
            console.log(data);
            alert(data.msg);
        },
    });
}
function saveAction(){
    var contro_arr = [];
    var action_arr = [];
    var choose_arr = [];
    var inherit_arr = [];
    var limit_arr  = [];
    var i   = 0;
    var gid = $('input[name=gid]').val();
    $(".action-node").each(function(){
        contro_arr[i] = $(this).attr('controller');
        action_arr[i] = $(this).attr('title');
        choose_arr[i] = $(this).attr('choose');
        // inherit_arr[i] = $(this).attr('inherit');
        // limit_arr[i] = $(this).attr('data-limit');
        i++;
    }); 
    $.ajax({
        url:'/cp/longrentdepartment/setgroupaction',
        type:'POST',
        dataType:'JSON',
        data:{gid:gid,controller:contro_arr,action:action_arr,choose:choose_arr,_token:$('input[name=_token]').val()},
        success:function(data){
            alert(data.msg);
        },
    });
}
</script>
@endsection
