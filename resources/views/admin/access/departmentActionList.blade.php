<div style="margin:20px 10px">
    <ul class="nav nav-tabs">
        <li role="presentation" class="active"><a>权限包详情</a></li>
    </ul>
    <table class="table table-bordered">
        @foreach ($group_info as $group)
            <tr class="active actions">
                <td style="width:90px">权限组名</td>
                <td class="set-close" group-id="{{$group['id']}}" status="0"><b>{{$group['name']}}({{$group['desc']}})</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/cp/longrentdepartment/actiongroupaccessdetail?id={{$group['id']}}" class="btn btn-primary">编辑权限</a></td>
            </tr>
            <tr class="action-detail" group-id="{{$group['id']}}" style="display:none">
                <td class="active">权限详情</td>
                <td>
                    <table class="table table-bordered" id="depart_group_list">
                        <tbody>
                            @foreach ($group['actions'] as $k => $action)
                            <tr>
                                <td>{{$action['name']}}({{$k}})</td>
                            </tr>       
                            <tr>
                                <td>
                                    @foreach ($action['actions'] as $ak => $av)
                                        <button class="btn btn-{{$av['color']}}" style="margin-bottom:12px">{{$av['desc']}}<span class="action_desc"></span></button>
                                    @endforeach
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table> 
                </td>
            </tr>
        @endforeach

        <tr class="active actions">
            <td style="width:90px"><b>独立权限</b></td>
            <td class="set-close" status="0"><b>单个权限详情</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <a href="/cp/longrentdepartment/actionaccessdetail?id={{$depart_info['id']}}" class="btn btn-primary">编辑权限</a>
            </td>
        </tr>
        <tr class="action-detail" style="display:none">
            <td class="active">权限详情</td>
            <td>
                <table class="table table-bordered">
                    <tbody>
                    @foreach ($controller_list as $k => $action)
                        <tr>
                            <td>{{$action['name']}}({{$k}})</td>
                        </tr>
                        <tr>
                            <td>
                                @foreach ($action['actions'] as $ak => $av)
                                    <button class="btn btn-success" style="margin-bottom:12px">{{$av['desc']}}<span class="action_desc"></span></button>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
</div>
<script type="text/javascript">
$(function(){
    $(".actions").click(function(event){
        var isshow = $(this).next('.action-detail:first').is(':visible');
        event.stopPropagation();
        if(isshow){
            $(this).next('.action-detail').hide();
        }else{
            $(this).next('.action-detail').show();
        }
    });
    //阻止点击事件，冒泡行为
    $('a').click(function(event){
        event.stopPropagation();
    })
});
</script>
