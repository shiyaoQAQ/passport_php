@extends('admin.layout')

@section('title', '修改密码')

@section('content')
<ul class="nav nav-tabs" style="width:100%; padding: 0 10px;">
    <li role="presentation" class="active"><a href="#">修改密码</a></li>
</ul>
<br>
<table class="table table-bordered" style="width:750px;margin-left: 10px" >
        <tr>
            <td style="width:250px" class="active">旧密码</td>
            <td>
                <div class="input-group">
                  <input type="password" class="form-control" placeholder="输入旧密码" aria-describedby="basic-addon1" name="old_pass">
                </div>
            </td>
        </tr>
        <tr>
            <td style="width:250px" class="active">新密码</td>
            <td>
                <div class="input-group">
                  <input type="password" class="form-control" placeholder="输入新密码" aria-describedby="basic-addon1" name="new_pass">
                </div>
            </td>
        </tr>        
        <tr>
            <td style="width:250px" class="active">确认密码</td>
            <td colspan="3">
                <div class="input-group">
                  <input type="password" class="form-control" placeholder="确认密码" aria-describedby="basic-addon1" name="con_pass">
                </div>
            </td>
        </tr>
        <tr>
            <td style="width:250px" class="active">操作</td>
            <td><input type="button" value="确认" id='add_user' class="btn btn-info"/></td>
        </tr>
</table>
{{csrf_field()}}
<script type="text/javascript">
    $(function(){
        $('#add_user').click(function(){
            var old_pass = $('input[name=old_pass]').val();          
            var new_pass = $('input[name=new_pass]').val();
            var con_pass  = $('input[name=con_pass]').val();          
            if(old_pass.trim().length == 0){
                alert('请输入旧密码');
                return false;
            }
            if(new_pass.trim().length == 0){
                alert('请输入新密码');
                return false;                
            }
            if(con_pass.trim().length == 0){
                alert('请输入确认密码');
                return false;                
            }
            if(con_pass != new_pass){
                alert('两次新密码输入不一样');
                return false;                
            }
            $.ajax({
                url:'/cp/user/password',
                type:'POST',
                dataType:'JSON',
                data:{old_pass:old_pass,new_pass:new_pass,con_pass:con_pass,_token:$('input[name=_token]').val()},
                success:function(data){
                    alert(data.msg);
                },
            });
        });
    });
</script>
@endsection
