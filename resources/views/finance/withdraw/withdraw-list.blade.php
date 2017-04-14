@extends('layouts.base')

@section('content')

    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">提现记录</a></li>
        </ul>
    </div>

    <form action="" method="post" class="form-horizontal">
        <div class="panel panel-info">
            <div class="panel-body">
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
                                <option value='{{$type['class']}}' @if($search['type']==$type['class']) selected @endif>{{$type['type_name']}}</option>
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
                        {!! app\common\helpers\DateRange::tplFormFieldDateRange('search[times]', ['starttime'=>date('Y-m-d H:i', $search['starttime']), 'endtime'=>date('Y-m-d H:i',$search['endtime'])], true);!!}
                    </div>

                </div>

                <div class="form-group col-xs-12 col-sm-2 col-lg-1">

                    <input type="submit" class="btn btn-block btn-success" value="搜索">

                </div>

            </div>
        </div>
    </form>

    <div class='panel panel-default'>
        <div class='panel-body'>
            <table class="table">
                <thead>
                <tr>
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
                        <td title="{{$row->withdraw_sn}}" class="tip">{{$row->withdraw_sn}}</td>
                        <td><img src="{{tomedia($row->hasOneMember['avatar'])}}"
                                 style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                            </br>
                            {{$row->hasOneMember['nickname']}}</td>
                        <td>{{$row->hasOneMember['realname']}}</br>{{$row->hasOneMember['mobile']}}</td>
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

    </div>
    </div>
@endsection