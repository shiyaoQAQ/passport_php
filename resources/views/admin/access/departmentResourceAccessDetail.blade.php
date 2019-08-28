@extends('admin.layout')

@section('title', '资源组详情编辑')

@section('content')
<F3:include href="cp/common/header.html" /> 
<F3:include href="cp/common/sidebar.html" /> 
<link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css">
<style type="text/css">
body{font:12px/1.8 "宋体";overflow-y:scroll}
html,body{height:100%;margin:0;padding:0;}
.btn{margin-bottom: 12px;font:12px/1.8 "宋体";}
.clear{clear:both;}
</style>
<body>
    <div style="margin:0 20px">
        <br> 
        <ul class="nav nav-tabs">
            <li role="presentation" class="active"><a href="#">部门资源编辑</a></li>
            <input type="hidden" value="{{$did}}" name="did">
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
                            @foreach( $resource_list as $k => $action )
                            <tr>
                                <td><b>{{$action['desc']}}({{$k}})</b></td>
                            </tr>       
                            <tr>
                                <td>
                                    @foreach($action['resource'] as $ak => $av)
                                        <button class="btn action-node" controller="{{$k}}"  choose="0" resource="{{$ak}}">{{$av}}<span class="action_desc"></span></button>
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
        {{csrf_field()}}
</body>
</html>
<script type="text/javascript">
$(function(){
    renderChooseAction();
    $("#save-action").on('click',saveAction);
    $(".action-node").on('click',chooseAction);
});
function renderChooseAction(){
    var list = {!!$choose_resource!!};
    if(list != null){
        $.each(list,function(i,action){
            var node = $(".action-node[controller="+action.controller+"][resource="+action.resource+"]");
            node.addClass('btn-success');
            if(action.data_limit == 1){
                node.addClass('btn-info');
            }
            node.attr('choose',1);
        });
    }
}
function chooseAction(){
    var choose = $(this).attr('choose');
    if(choose != 1){
        $(this).addClass('btn-success');
        $(this).attr('choose',1);
    }else{
        $(this).removeClass('btn-success');
        $(this).attr('choose',0);
    }
}
function saveAction(){
    var contro_arr = [];
    var resour_arr = [];
    var choose_arr = [];
    var limit_arr  = [];
    var i   = 0;
    var did = $('input[name=did]').val();
    $(".action-node").each(function(){
        contro_arr[i] = $(this).attr('controller');
        resour_arr[i] = $(this).attr('resource');
        choose_arr[i] = $(this).attr('choose');
        i++;
    }); 
    $.ajax({
        url:'/cp/longrentdepartment/ajax_set_depart_resource',
        type:'POST',
        dataType:'JSON',
        data:{did:did,controller:contro_arr,resour_arr:resour_arr,choose:choose_arr,_token:$('input[name=_token]').val()},
        success:function(data){
            alert(data.msg);
        },
    });
}
</script>
@endsection