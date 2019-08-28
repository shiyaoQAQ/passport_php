@extends('admin.layout')

@section('title', '添加管理员')

@section('content')
<style type="text/css">
</style>
<body>
    <div style="padding:0 10px;">
        <ul class="nav nav-tabs">
            <li role="presentation" class="active"><a href="/cp/longrentdepartment/action_group_list">权限组管理</a></li>
        </ul>
        <br> 
        <button class="btn btn-primary" id="new-group">新增</button>
        <br>
        <br>
        <table class="table table-bordered table-hover">
            <tr class="active">
                <td>ID</td>
                <td>名称</td>
                <td>描述</td>
                <td>创建时间</td>
                <td>操作</td>
            </tr>
            @foreach ($action_group_list as $k => $v)
                <tr>
                    <td>{{$v['id']}}</td>
                    <td class="action_group_name">{{$v['name']}}</td>
                    <td class="action_group_desc">{{$v['desc']}}</td>
                    <td>{{$v['ctime']}}</td>
                    <td>
                        <a class="btn btn-primary btn-edit" data="{{$v['id']}}" href="#">编辑</a>
                        <a class="btn btn-danger btn-del" data="{{$v['id']}}" href="#">删除</a>
                        <a class="btn btn-info" href="/cp/longrentdepartment/actiongroupaccessdetail?id={{$v['id']}}">权限编辑</a>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
<div id="add-box" style="display:none; position: absolute; top: 40%; left:50%;transform: translateX(-50%); padding: 2px;width: 500px; border: 8px solid #E8E9F7; background-color: white; z-index:1002; overflow: auto;">
    <input type="hidden" name="choose_title" id="choose_title" value="">
    <table class="table table-bordered">
            <tr>
                <td>名称</td>
                <td><input type="text" name="add-name"></td>
            </tr>
            <tr>
                <td>描述</td>
                <td><input type="text" name="add-desc"></td>
            </tr>
            <tr>
                <td>操作</td>
                <td>
                    <input type="hidden" name="group_id">
                    <button class="btn btn-success" id="add-save">确认</button>
                    <button class="btn btn-danger" id="add-cancel">取消</button>
                </td>
            </tr>
    </table>
</div>
{{csrf_field()}}
</body>
</html>
<script type="text/javascript">
$(function(){
    $('#new-group').click(function(){
        $('[name=add-name]').val('');
        $('[name=add-desc]').val('');
        $('[name=group_id]').val('');
        $("#add-box").css('top', $(this).offset().top+36);
        $("#add-box").slideDown('fast');
    });
    $('#add-cancel').click(function(){
        $('#add-box').hide();
    });
    $('#add-save').click(function(){
        var name = $('input[name=add-name]').val();
        if(name.length == 0){
            alert('请输入权限组名称');
            return false;
        }
        var desc = $('input[name=add-desc]').val();
        /*if(desc.length == 0){
            alert('请输入权限组描述');
            return false;
        }*/
        var group_id = $('[name=group_id]').val();
        $.ajax({
            url:'/cp/longrentdepartment/ajaxaddactiongroup',
            type:"POST",
            data:{name:name,desc:desc,id:group_id,_token:$('input[name=_token]').val()},
            dataType:'JSON',
            success:function(data){
                alert(data.msg);
                if(data.code == 0){
                    $('#add-box').hide();
                    $('[name=group_id]').val('');
                    window.location.reload();
                }
            },
        });

    });
    //编辑权限组
    $('.btn-edit').click(function(){
        var name = $(this).parent().parent().find('.action_group_name').text();
        var desc = $(this).parent().parent().find('.action_group_desc').text();
        $('[name=add-name]').val(name);
        $('[name=add-desc]').val(desc);
        $('[name=group_id]').val($(this).attr('data'));
        $("#add-box").css('top', $(this).offset().top-80);
        $("#add-box").slideDown('fast');
    });

    //删除权限组
    $('.btn-del').click(function(){
        var r=confirm("确认删除权限组？");
        if (r==true){
            var group_id = $(this).attr('data');
            $.ajax({
                url:'/cp/longrentdepartment/ajaxdelactiongroup',
                type:"POST",
                data:{id:group_id,_token:$('input[name=_token]').val()},
                dataType:'JSON',
                success:function(data){
                    alert(data.msg);
                    if(data.code == 0){
                        window.location.reload();
                    }
                }
            });
        }
    });
});
</script>
@endsection