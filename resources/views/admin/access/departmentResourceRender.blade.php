<div style="margin:20px 10px">
    <ul class="nav nav-tabs">
        <li role="presentation" class="active"><a>资源包详情</a></li>
    </ul>
    <table class="table table-bordered">
        @foreach($group_info as  $group)
            <tr class="active resource">
                <td style="width:90px">资源组名</td>
                <td class="set-close" group-id="{{$group['id']}}" status="0"><b>{{$group['name']}}({{$group['desc']}})</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/cp/longrentdepartment/resourcegroupdetail?id={{$group['id']}}" class="btn btn-primary">编辑资源</a></td>
            </tr>
            <tr class="action-detail" group-id='{{@group.id}}' style="display: none;">
                <td class="active">资源详情</td>
                <td>
                    <table class="table table-bordered" id="depart_group_list">
                        <tbody>
                        @foreach($group['resources'] as $k => $resource)
                            <tr>
                                <td>{{@resource.name}}({{@k}})</td>
                            </tr>
                            <tr>
                                <td>
                                    @foreach($resource['resource'] as $ak => $av)
                                        <button class="btn btn-{{$av['color']}}" style="margin-bottom:12px" controller="{{$av['controller']}}" title="{{$av['resource']}}" choose="0" inherit="0" data-limit="0">{{$av['desc']}}<span class="action_desc"></span></button>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
        @endforeach

        <tr class="active resource">
            <td style="width:90px"><b>独立资源</b></td>
            <td class="set-close" group-id="{{$group['id']}}" status="0"><b>单个资源详情</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/cp/longrentdepartment/depart_resource_detail?id={{$depart_info['id']}}" class="btn btn-primary">编辑资源</a></td>
        </tr>
        <tr class="action-detail" group-id='{{@group.id}}' style="display:none">
            <td class="active">资源详情</td>
            <td>
                <table class="table table-bordered">
                    <tbody>
                    @foreach($resource_list as $k => $action)
                        <tr>
                            <td>{{$action['name']}}({{$k}})</td>
                        </tr>
                        <tr>
                            <td>
                                @foreach($action['resource'] as $ak => $av)
                                    <button class="btn btn-success" title="{{$av['desc']}}" style="margin-bottom:12px">{{$av['desc']}}</button>
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
        $(".resource").click(function(event){
            var isshow = $(this).next('.action-detail:first').is(':visible');
            event.stopPropagation();
            if(isshow){
                $(this).next('.action-detail').hide();
            }else{
                $(this).next('.action-detail').show();
            }
        });
    });
    //阻止点击事件，冒泡行为
    $('a').click(function(event){
        event.stopPropagation();
    })
</script>
