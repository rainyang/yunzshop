@extends('layouts.base')

@section('content')

    <div class="rightlist">
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action="./index.php" method="get" class="form-horizontal" role="form" id="form1">
                    <input type="hidden" name="c" value="site"/>
                    <input type="hidden" name="a" value="entry"/>
                    <input type="hidden" name="m" value="sz_yi"/>
                    <input type="hidden" name="do" value="finance"/>
                    <input type="hidden" name="p" value="log"/>
                    <input type="hidden" name="type" value="{$_GPC['type']}"/>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">会员信息</label>
                        <div class="col-sm-8 col-lg-9 col-xs-12">
                            <input type="text" class="form-control" name="realname" value="{$_GPC['realname']}"
                                   placeholder='可搜索会员昵称/姓名/手机号/绑定手机号'/>

                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"></label>
                        <div class="col-sm-7 col-lg-9 col-xs-12">
                            <button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
                            <input type="hidden" name="token" value="{$_W['token']}"/>
                            <button type="submit" name="export" value="1" class="btn btn-primary">导出 Excel</button>
                        </div>
                    </div>
                    <div class="form-group">
                    </div>
                </form>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">总数：{{ $tansferList->total() }}</div>
            <div class="panel-body ">
                <table class="table table-hover">
                    <thead class="navbar-inner">
                    <tr>
                        <th style='width:10%;'>编号</th>
                        <th style='width:10%;'>转让人</th>
                        <th style='width:14%;'>被转让人</th>
                        <th style='width:12%;'>转让金额</th>
                        <th style='width:12%;'>转让时间</th>
                        <th style='width:12%;'>状态</th>
                    </tr>
                    </thead>
                    @foreach($tansferList as $list)
                        <tr>
                            <td>{{ $list->id }}</td>
                            <td>{{ $list->transferorInfo->realname or $list->transferorInfo->nickname }}({{ $list->transferor }})</td>
                            <td>{{ $list->recipientInfo->realname or $list->recipientInfo->nickname }}({{ $list->recipient }})</td>
                            <td>{{ $list->money }}</td>
                            <td>{{ $list->created_at }}</td>
                            <td>
                                @if($list->status == 1)
                                    <span class='label label-success'>转让成功</span>
                                @else
                                    <span class='label label-default'>转让失败</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
                {!! $pager !!}
            </div>
        </div>
    </div>


@endsection