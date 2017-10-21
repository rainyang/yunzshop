@extends('layouts.base')

@section('content')
@section('title', trans('提现列表'))
<div class="right-titpos">
    <ul class="add-snav">
        <li class="active"><a href="#">提现记录</a></li>
    </ul>
</div>

{{--<form action="" method="post" class="form-horizontal">--}}
<form action="" method="post" class="form-horizontal"
      id="form1">
    <div class="panel panel-info">
        <div class="panel-body">
            <input type="hidden" name="search[status]" value="{{$search['status']}}">

            <div class="form-group col-xs-12 col-sm-8 col-lg-11">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">会员信息</label>
                <div class="col-xs-12 col-sm-8 col-lg-9">
                    <input class="form-control" name="search[member]" id="" type="text"
                           value="{{$search['member']}}" placeholder="昵称/姓名/手机">
                </div>
            </div>

            <div class="form-group col-xs-12 col-sm-8 col-lg-11">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">提现编号</label>
                <div class="col-xs-12 col-sm-8 col-lg-9">
                    <input class="form-control" name="search[withdraw_sn]" id="" type="text"
                           value="{{$search['withdraw_sn']}}" placeholder="提现编号">
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">类型</label>
                <div class="col-xs-12 col-sm-8 col-lg-9">
                    <select name='search[type]' class='form-control'>
                        <option value='' @if($search['type']=='') selected @endif>全部</option>
                        @foreach($types as $type)
                            <option value='{{$type['class']}}'
                                    @if($search['type']==$type['class']) selected @endif>{{$type['title']}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">提现时间</label>
                <div class="col-sm-2">
                    <select name='search[searchtime]' class='form-control'>
                        <option value='' @if(empty($search['searchtime'])) selected @endif>不搜索</option>
                        <option value='1' @if($search['searchtime']==1) selected @endif >搜索</option>
                    </select>
                </div>
                <div class="col-sm-7 col-lg-7 col-xs-12">
                    {!! app\common\helpers\DateRange::tplFormFieldDateRange('search[times]', [
                                                        'starttime'=>date('Y-m-d H:i', $search['times']['start']),
                                                        'endtime'=>date('Y-m-d H:i', $search['times']['end']),
                                                        'start'=>date('Y-m-d H:i', $search['times']['start']),
                                                        'end'=>date('Y-m-d H:i', $search['times']['end'])
                                                        ], true) !!}
                </div>

            </div>

            <div class="form-group col-xs-12 col-sm-4">
                <div style="width: 110px;margin: auto;">
                    <input type="button" class="btn btn-success" id="export" value="导出">
                    <input type="button" class="btn btn-success pull-right" id="search" value="搜索">
                </div>

            </div>

        </div>
    </div>
</form>

@if (1 == YunShop::request()->search['status'])
<div class='panel panel-default'>
    <div class='panel-body' style="padding-bottom: 15px">
        <input type="hidden" name="pay_way" value="2">
        <input type="button" class="btn btn-success" id="batch_alipay" value="支付宝批量打款">
        <label style="color:#ff0000;">批量打款所选的订单收入类型必须保持一致，收入类型分为余额提现和收入提现</label>
    </div>
</div>
@endif
<div class='panel panel-default'>
    <div class='panel-body'>
        <table class="table">
            <thead>
            <tr>
                <th style='width:5%;'><input id="all" type="checkbox" value="0"> 全选</th>
                <th style='width:20%;'>提现编号</th>
                <th style='width:10%;'>粉丝</th>
                <th style='width:10%;'>姓名</br>手机</th>
                <th style='width:10%;'>收入类型</th>
                <th style='width:10%;'>提现方式</th>
                <th style='width:10%;'>申请金额</th>
                <th style='width:15%;'>申请时间</th>
                <th style='width:10%;'>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($list as $row)
                <tr>
                    <td><input type="checkbox" name="chk_withdraw" value="{{$row->id}}"></td>
                    <td title="{{$row->withdraw_sn}}" class="tip">{{$row->withdraw_sn}}</td>
                    <td><img src="{{tomedia($row->hasOneMember['avatar'])}}"
                             style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                        <br/>
                        {{$row->hasOneMember['nickname']}}</td>
                    <td>{{$row->hasOneMember['realname']}}<br/>{{$row->hasOneMember['mobile']}}</td>
                    <td>{{$row->type_name}}</td>
                    <td>{{$row->pay_way_name}}</td>
                    <td>{{$row->amounts}}</td>
                    <td>{{$row->created_at}}</td>
                    <td>
                        @if($row->type == 'balance')
                            <a class='btn btn-default'
                               href="{{yzWebUrl('finance.balance-withdraw.detail', ['id' => $row->id])}}"
                               title='详情'>详情</a>
                        @else
                            <a class='btn btn-default'
                               href="{{yzWebUrl('finance.withdraw.info', ['id' => $row->id])}}" title='详情'>详情</a>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {!! $pager !!}

    </div>
</div>


<script language='javascript'>
    $(function () {
        $('#export').click(function () {
            $('#form1').attr('action', '{!! yzWebUrl('finance.withdraw.export') !!}');
            $('#form1').submit();
        });
        $('#search').click(function () {
            $('#form1').attr('action', '{!! yzWebUrl('finance.withdraw') !!}');
            $('#form1').submit();
        });

        $('#all').change(function() {
            $(this).parents('.table').find('input[type="checkbox"]').prop('checked',$(this).prop('checked'));
        });

        $(document).on('click', '#batch_alipay', function () {
            var total    = 0;
            var balance  = 0;
            var error    = 0;
            var ids     = []

            $('input[type="checkbox"]').each(function() {
                if ($(this).prop('checked') && $(this).val() != 0){
                    total++;
                    ids.push($(this).val());

                    if ($(this).parent().siblings().eq(4).text() != '提现到支付宝') {
                        error++;
                    }

                    if ($(this).parent().siblings().eq(3).text() == '余额提现') {
                        balance++;
                    }
                }
            });

            if (error > 0) {
                alert('提现方式错误');
                return false;
            }

            if (balance == 0 || balance == total) {
                var myform = $('<form class="batch_alipay" method="post"><input type="hidden" name="ids" value="' + ids + '" /></form>');

                $(document.body).append(myform);

                if (balance == 0) {  //收入提现
                    myform.attr('action', '{!! yzWebUrl("finance.withdraw.batchAlipay") !!}');
                } else { //余额提现
                    myform.attr('action', '{!! yzWebUrl("finance.balance-withdraw.batchAlipay") !!}');
                }

                myform.submit();
            } else {
                alert('订单收入类型不一致');
                return false;
            }
        });

    });
</script>
@endsection