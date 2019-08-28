@extends('admin.layout')

@section('title', '资源组详情编辑')

@section('content')
<style type="text/css">
    body{font:12px/1.8 "宋体";overflow-y:scroll}
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
    .btn{margin-bottom: 12px;font:12px/1.8 "宋体";}
    .clear{clear:both;}
    .strt-name{display:inline-block;padding:0;margin:0 10px;border-radius:3px;background:#f8f8f8;}
    .strt-part .table{width: auto;margin: 0;background-color: transparent;}
    .strt-part .table>tbody>tr>td{padding: 0}
</style>
<body>
<div style="margin:0 20px">
    <ul class="nav nav-tabs">
        <li role="presentation" class="active"><a href="#">资源编辑</a></li>
        <input type="hidden" value="{{$group_info['id']}}" name="gid">
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
                    {{$depart_tree}}
                </div>
                <div class="clear"></div>
            </td>
        </tr>
        <tr>
            <td class="active">部门操作</td>
            <td><button class="btn btn-primary" id="save-department">保存部门设置</button></td>
        </tr>
        <tr>
            <td class="active">资源详情</td>
            <td>
                <table class="table table-bordered" id="depart_group_list">
                    <tbody>
                    @foreach($resource_list as $k => $resource)
                        <tr>
                            <td><b>{{$resource['desc']}}({{$k}})</b></td>
                        </tr>
                        <tr>
                            <td>
                                @foreach($resource['resource'] as $ak => $av)
                                    <button class="btn action-node" controller="{{$k}}" title="{{$ak}}" choose="0" inherit="0" data-limit="0">{{$av}}<span class="action_desc"></span></button>
                                 @endforeach
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td class="active">资源操作</td>
            <td><button class="btn btn-primary" id="save-action">保存资源设置</button></td>
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
        $("#save-department").on('click',saveDepartment);
        $("#save-action").on('click',saveAction);
        $(".action-node").on('click',chooseAction);
    });
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
                var node = $(".action-node[controller="+action.controller+"][title="+action.resource+"]");
                node.attr('choose',1);
                node.addClass('btn-success');
                if(action.data_limit == 1){
                    node.addClass('btn-info');
                }
                node.attr('data-limit',action.data_limit);
            });
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
            $(this).removeClass('btn-success');
            $(this).attr('choose',0);
            $(this).attr('data-limit',0);
        }
    }
    function saveDepartment(){
        var id_arr = [];
        var choose = [];
        var gid = $('input[name=gid]').val();
        var i = 0;
        $('.strt-name').each(function(){
            id_arr[i] = $(this).attr('departid');
            choose[i] = $(this).attr('choose');
            i++;
        });
        $.ajax({
            url:'/cp/longrentdepartment/setdepartmentresourcegroup',
            type:'POST',
            dataType:'JSON',
            data:{gid:gid,id_arr:id_arr,choose:choose,_token:$('input[name=_token]').val()},
            success:function(data){
                console.log(data);
                alert(data.msg);
            }
        });
    }
    function saveAction(){
        var contro_arr = [];
        var resource_arr = [];
        var choose_arr = [];
        var inherit_arr = [];
        var limit_arr  = [];
        var i   = 0;
        var gid = $('input[name=gid]').val();
        $(".action-node").each(function(){
            contro_arr[i] = $(this).attr('controller');
            resource_arr[i] = $(this).attr('title');
            choose_arr[i] = $(this).attr('choose');
            inherit_arr[i] = $(this).attr('inherit');
            limit_arr[i] = $(this).attr('data-limit');
            i++;
        });
        $.ajax({
            url:'/cp/longrentdepartment/setresourcegroup',
            type:'POST',
            dataType:'JSON',
            data:{gid:gid,controller:contro_arr,resource:resource_arr,choose:choose_arr,inherit:inherit_arr,limit:limit_arr,_token:$('input[name=_token]').val()},
            success:function(data){
                alert(data.msg);
            }
        });
    }
</script>
@endsection