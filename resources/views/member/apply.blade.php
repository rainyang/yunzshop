@extends('layouts.base')

@section('content')

    <link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div class="w1200 m0a">
        <div class="rightlist">
            <!-- 新增加右侧顶部三级菜单 -->
            <div class="right-titpos">
                <ul class="add-snav">
                    <li><a href="javascript:void"> &nbsp;  <i class="fa fa-angle-double-right"></i> &nbsp;会员/会员关系/资格申请</a></li>
                </ul>
            </div>
            <!-- 新增加右侧顶部三级菜单结束 -->
            <div class="panel panel-info"><!--
                <div class="panel-heading">筛选</div>-->
                <div class="panel-body">
                    <form action="./index.php" method="get" class="form-horizontal" role="form" id="form1">
                        <input type="hidden" name="c" value="site" />
                        <input type="hidden" name="a" value="entry" />
                        <input type="hidden" name="m" value="sz_yi" />
                        <input type="hidden" name="do" value="1234" id="form_do" />
                        <input type="hidden" name="route" value="member.member.search" id="route" />
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2 ">
                            <!--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">ID</label>-->
                            <div class="">
                                <input type="text" placeholder="会员ID" class="form-control"  name="mid" value="{{$request['mid']}}"/>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <!-- <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">会员信息</label>-->
                            <div class="">
                                <input type="text" class="form-control"  name="realname" value="{{$request['realname']}}" placeholder="可搜索昵称/姓名/手机号"/>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <!--      <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">是否关注</label>-->
                            <div class="">
                                <select name='followed' class='form-control'>
                                    <option value=''>不限关注</option>
                                <!--
                                    <option value='2'
                                            @if($request['followed']=='2')
                                    selected
                                    @endif
                                        >未关注
                                        -->
                                    </option>
                                    <option value='1'
                                            @if($request['followed']=='1')
                                            selected
                                            @endif
                                    >已关注
                                    </option>
                                    <option value='0'
                                            @if($request['followed']=='0')
                                            selected
                                            @endif
                                    >取消关注
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <!-- <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">会员等级</label>-->
                            <div class="">
                                <select name='level' class='form-control'>
                                    <option value=''>会员等级不限</option>
                                    @foreach($levels as $level)
                                        <option value='{{$level['id']}}'
                                                @if($request['level']==$level['id'])
                                                selected
                                                @endif
                                        >{{$level['level_name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <!--  <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">会员分组</label>-->
                            <div class="">
                                <select name='groupid' class='form-control'>
                                    <option value=''>会员分组不限</option>
                                    @foreach($groups as $group)
                                        <option value='{{$group['id']}}'
                                                @if($request['groupid']==$group['id'])
                                                selected
                                                @endif
                                        >{{$group['group_name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <!--        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">黑名单</label>-->
                            <div class="">
                                <select name='isblack' class='form-control'>
                                    <option value=''>不限黑名单</option>
                                    <option value='0'
                                            @if($request['isblack']=='0')
                                            selected
                                            @endif>否</option>
                                    <option value='1'
                                            @if($request['isblack']=='1')
                                            selected
                                            @endif>是</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-8">

                            <div class="time">

                                <select name='isblack' class='form-control'>
                                    <option value=''>注册时间不限</option>
                                    <option value='0'
                                            @if($request['searchtime']=='0')
                                            selected
                                            @endif>不搜索注册时间</option>
                                    <option value='1'
                                            @if($request['searchtime']=='1')
                                            selected
                                            @endif>搜索注册时间</option>
                                </select>
                            </div>
                            <div class="search-select">
                                时间
                            </div>
                        </div>

                        <div class="form-group col-sm-7 col-lg-4 col-xs-12">
                            <!--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"></label>-->
                            <div class="">
                                <button type="button" name="export" value="1" id="export" class="btn btn-default excel back ">导出 Excel</button>
                                <input type="hidden" name="token" value="{{$var['token']}}" />
                                <button class="btn btn-success "><i class="fa fa-search"></i> 搜索</button>

                            </div>
                        </div>

                    </form>
                </div>
            </div><div class="clearfix">
                <div class="panel panel-default">
                    <div class="panel-heading">总数：{{$total}}   </div>
                    <div class="panel-body">
                        <table class="table table-hover" style="overflow:visible;">
                            <thead class="navbar-inner">
                            <tr>
                                <th style='width:8%;text-align: center;'>ID</th>
                                <th style='width:8%;text-align: center;'>推荐人</th>
                                <th style='width:8%;text-align: center;'>粉丝</th>
                                <th style='width:12%;'>姓名</th>
                                <th style='width:8%;'>电话</th>
                                <th style='width:10%;'>申请时间</th>
                                <th style='width:15%;'>详情</th>
                                <th style='width:8%'>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($list['data'] as $row)
                                <tr>
                                    <td style="text-align: center;">   {{$row['uid']}}</td>

                                        <td style="text-align: center;"
                                            @if(!empty($row['yz_member']['parent_id']))
                                            title='ID: {{$row['yz_member']['parent_id']}}'
                                                @endif
                                        >
                                            @if(empty($row['yz_member']['parent_id']))
                                                @if($row['yz_member']['is_agent']==1)
                                                    <label class='label label-primary'>总店</label>
                                                @else
                                                    <label class='label label-default'>暂无</label>
                                                @endif
                                            @else

                                                @if(!empty($row['yz_member']['agent']['avatar']))
                                                    <img src='{{$row['yz_member']['agent']['avatar']}}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' /><br/>
                                                @endif
                                                @if(empty($row['yz_member']['agent']['nickname']))
                                                    未更新
                                                @else
                                                    {{$row['yz_member']['agent']['nickname']}}
                                                @endif
                                            @endif
                                        </td>

                                    <td style="text-align: center;">
                                        @if(!empty($row['avatar']))
                                            <img src='{{$row['avatar']}}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' /><br/>
                                        @endif
                                        @if(empty($row['nickname']))
                                            未更新
                                        @else
                                            {{$row['nickname']}}
                                        @endif
                                    </td>
                                    <td>{{$row['realname']}}</td>
                                    <td>
                                        {{$row['mobile']}}
                                    </td>
                                    <td>
                                        {{date('Y.m.d',$row['apply_time'])}}</td>
                                    <td><a href="{{yzWebUrl('member.member.detail', ['id'=>$row['uid']])}}">查看会员详情</a></td>
                                    <td  style="overflow:visible;">
                                        <div class="btn-group btn-group-sm" >
                                            <a class="btn btn-default pass" href="javascript:;" data-id="{{$row['uid']}}">通过 <span class="caret"></span></a>
                                        </div>
                                    </td>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {!!$pager!!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script language='javascript'>
        $(function () {
            $('#export').click(function(){
                $('#route').val("member.member.export");
                $('#form1').submit();
                $('#route').val("member.member.index");
            });

            $('.pass').click(function () {
               var id = $(this).data('id');
               var url = '{!! yzWebUrl('member.member-relation.chkApply') !!}';

               if (confirm('确定审核通过吗？')) {
                   $.ajax({
                       url: url,
                       type: 'get',
                       data: {id:id},
                       dataType: 'json',
                       success: function (json) {
                           if (1 == json.result) {
                               window.location.reload();
                           }
                       }
                   });
               }
            });
        });
    </script>
@endsection