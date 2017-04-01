@extends('layouts.base')

@section('content')

        <div class="rightlist">
            <!-- 新增加右侧顶部三级菜单 -->
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="#">财务／余额管理</a></li>
                </ul>
            </div>
            <!-- 新增加右侧顶部三级菜单结束 -->
            <div class="panel panel-info">
                <div class="panel-heading">筛选</div>
                <div class="panel-body">
                    <form action="./index.php" method="get" class="form-horizontal" role="form" id="form1">
                        <input type="hidden" name="c" value="site" />
                        <input type="hidden" name="a" value="entry" />
                        <input type="hidden" name="m" value="sz_yi" />
                        <input type="hidden" name="p" value="list" id="form_p" />
                        <input type="hidden" name="do" value="member" id="form_do" />
                        <div class="form-group">
                            <div class="col-sm-8 col-lg-12 col-xs-12">
                                <div class='input-group'>
                                    <div class='input-group-addon'>会员信息</div>
                                    <input class="form-control" name="keyword" type="text" value="搜索功能未完善" placeholder="订单号/支付单号">

                                    <div class='input-group-addon'>会员等级</div>
                                    <select name="paytype" class="form-control">
                                        <option value="" {if $_GPC['paytype']==''}selected{/if}>不限</option>
                                        @foreach($memberLevel as $level)
                                        <option value="{{ $level['id'] }}" >{{ $level['level_name'] }}</option>
                                        @endforeach
                                    </select>
                                    <div class='input-group-addon'>会员分组</div>
                                    <select name="paytype" class="form-control">
                                        <option value="" {if $_GPC['paytype']==''}selected{/if}>不限</option>
                                        @foreach($memberGroup as $group)
                                        <option value="{{ $group['id'] }}" >{{ $group['group_name'] }}</option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"></label>
                            <div class="col-sm-7 col-lg-9 col-xs-12">
                                <button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
                                <input type="hidden" name="token" value="{$_W['token']}" />
                            </div>
                        </div>


                        <div class="form-group">
                        </div>
                    </form>
                </div>
            </div>
            <div class="clearfix">
                <div class="panel panel-default">
                    <div class="panel-heading">总数：{$total}   </div>
                    <div class="panel-body">
                        <table class="table table-hover" style="overflow:visible;">
                            <thead class="navbar-inner">
                            <tr>
                                <th style='width:8%;text-align: center;'>会员ID</th>
                                <th style='width:8%;text-align: center;'>粉丝</th>
                                <th style='width:12%;'>姓名<br/>手机号码</th>
                                <th style='width:8%;'>等级/分组</th>
                                <th style='width:15%;'>余额</th>
                                <th style='width:8%'>操作</th>
                            </tr>
                            </thead>
                            <tbody>


                            @foreach($memberList as $list)
                            <tr>
                                <td style="text-align: center;">{{ $list->uid }}</td>
                                <td style="text-align: center;">
                                    @if($list->avatar)
                                    <img src='{{ $list->avatar }}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' /><br/>
                                    @endif
                                    {{ $list->nickname or '' }}
                                </td>
                                <td>{{ $list->realname }}<br/>{{ $list->mobile }}</td>
                                <td>
                                    {{ $list->yzMember->level->level_name or '默认会员等级' }}
                                    <br/>
                                    {{ $list->yzMember->group->group_name or '默认会员分组' }}
                                </td>
                                <td>
                                    <label class="label label-danger">余额：{{ $list->credit2 }}</label>
                                </td>


                                <td  style="overflow:visible;">

                                    <div class="btn-group btn-group-sm" >
                                        <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false" href="javascript:;">操作 <span class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-left" role="menu" style='z-index: 9999'>
                                            <li><a href="{php echo $this->createWebUrl('member',array('op'=>'detail','id' => $row['id']));}" title="会员详情"><i class='fa fa-money'></i>充值余额</a></li>
                                        </ul>
                                    </div>


                                </td>

                            </tr>
                            @endforeach
                            </tbody>
                        </table>


                        {!! $pager !!}


                    </div>
                </div>
            </div>

    </div>
    <script language='javascript'>

        function search_members() {
            if( $.trim($('#search-kwd-notice').val())==''){
                Tip.focus('#search-kwd-notice','请输入关键词');
                return;
            }
            $("#module-menus-notice").html("正在搜索....")
            $.get('{php echo $this->createPluginWebUrl('commission/agent')}', {
                keyword: $.trim($('#search-kwd-notice').val()),'op':'query',selfid:"{$id}"
            }, function(dat){
                $('#module-menus-notice').html(dat);
            });
        }
        $(function () {
            $('#export').click(function(){
                $('#form_p').val("exportMember");
                $('#form1').submit();
                $('#form_p').val("list");
            });
        });
        function select_member(o) {
            $("#agentid").val(o.id);
            $("#parentagentavatar").show();
            $("#parentagentavatar").find('img').attr('src',o.avatar);
            $("#parentagent").val( o.nickname+ "/" + o.realname + "/" + o.membermobile );
            $("#modal-module-menus-notice .close").click();
        }

    </script>



@endsection