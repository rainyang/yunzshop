@extends('layouts.base')

@section('content')

    <div class="w1200 m0a">
        <div class="rightlist">
            <form action="./index.php" method="get" class="form-horizontal" role="form" id="form1">
                <input type="hidden" name="c" value="site" />
                <input type="hidden" name="a" value="entry" />
                <input type="hidden" name="m" value="yun_shop" />
                <input type="hidden" name="do" value="plugin" />
                <input type="hidden" name="p" value="coupon" />
                <input type="hidden" name="method" value="coupon" />
                <input type="hidden" name="op" value="display" />

                <div class="panel panel-info">
                    <div class="panel-heading">筛选</div>
                    <div class="panel-body">

                        <div class="form-group">

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">优惠券名称</label>
                                <div class="col-sm-8 col-lg-9 col-xs-12">
                                    <input type="text" class="form-control"  name="keyword" value="" placeholder='可搜索优惠券名称'/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">分类</label>
                                <div class="col-sm-9 col-xs-12">
                                    <select name='catid' class='form-control'>
                                        <option value=''></option>

                                    </select>

                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">领取中心是否显示</label>
                                <div class="col-sm-8 col-lg-9 col-xs-12">
                                    <select name='gettype' class='form-control'>
                                        <option value=''></option>
                                        <option value='0' >不显示</option>
                                        <option value='1' >显示</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">创建时间</label>
                                <div class="col-sm-7 col-lg-9 col-xs-12">
                                    <div class="col-sm-3">
                                        <label class='radio-inline'>
                                            <input type='radio' value='0' name='searchtime'>不搜索
                                        </label>
                                        <label class='radio-inline'>
                                            <input type='radio' value='1' name='searchtime' >搜索
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"></label>
                                <div class="col-sm-8 col-lg-9 col-xs-12">
                                    <button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
                                    <input type="hidden" name="token" value="{{$var['token']}}" />

                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">总数:<small>排序数字越大越靠前</small></div>
                        <div class="panel-body">
                            <table class="table table-hover table-responsive">
                                <thead class="navbar-inner" >
                                <tr>
                                    <th width="4%">ID</th>
                                    <th width="6%">排序</th>
                                    <th width="16%">优惠券名称</th>
                                    <th width="16%">使用条件/优惠</th>
                                    <th width="10%">已使用/已发出/剩余数量</th>
                                    <th width="10%">领取中心</th>
                                    <th width="10%">创建时间</th>
                                    <th width="18%">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($list as $row)
                                <tr>
                                    <td>{{$row['id']}}</td>
                                    <td>
                                        <input type="text" class="form-control" name="displayorder[{$row['id']}]" value="{{$row['display_order']}}">
                                    </td>

                                    <td>

                                        <br/>{{$row['name']}}
                                    </td>
                                    <td>@if($row['enough']>0)
                                        <label class="label label-danger">满{{$row['enough']}}可用</label>
                                        @else
                                        <label class="label label-warning">不限</label>
                                        @endif

                                        <br/>@if($row['coupon_method']==1)
                                        立减 {{$row['deduct']}} 元
                                        @elseif( $row['coupon_method']==2)
                                        打 {{$row['discount']}} 折
                                        @endif
                                    </td>

                                    <td>
                                        <a href="{{yzWebUrl('coupon.coupon.log', ['id' => $row["id"]])}}">
                                            {{$row['usetotal']}} / {{$row['gettotal']}} / @if($row['total'] == -1) 无限数量 @else {{$row['lasttotal']}} @endif
                                        </a>

                                    <td>@if($row['get_type']==0)
                                        <label class="label label-default">不显示</label>
                                        @else

                                        @if($row['credit']>0 || $row['money']>0)
                                            @if($row['credit']>0)<label class='label label-primary'>{{$row['credit']}} 积分</label><br/>@endif
                                            @if($row['money']>0)<label class='label label-danger'>{{$row['money']}} 现金</label><br/>@endif
                                        @else
                                            <label class='label label-warning'>免费</label>
                                        @endif
                                        @endif
                                    </td>
                                    <td>{!! $row['created_at'] !!}</td>
                                    <td style="position:relative">
                                        <a href="javascript:;" data-url="{{yzWebUrl('coupon.coupon.create')}}"  title="复制连接" class="btn btn-default btn-sm js-clip"><i class="fa fa-link"></i></a>
                                        <a class='btn btn-default btn-sm' href="{{yzWebUrl('coupon.coupon.edit', ['id' => $row["id"]])}}" title="编辑" ><i class='fa fa-edit'></i></a>
                                        <a class='btn btn-default  btn-sm' href="{{yzWebUrl('coupon.coupon.destory', ['id' => $row["id"]])}}" title="删除" onclick="return confirm('确定要删除该优惠券吗？');"><i class='fa fa-remove'></i></a>
                                        <a  class='btn btn-primary  btn-sm' href="{{yzWebUrl('coupon.coupon.send', ['id' => $row["id"]])}}" title="发放优惠券" ><i class='fa fa-send'></i></a>
                                    </td>
                                </tr>
                                @endforeach
                        </tbody>
                        </table>
                        {{$pager}}
                    </div>
                    <div class='panel-footer'>
                        <input name="submit" type="submit" class="btn btn-default" value="提交排序">
                        <a class='btn btn-primary' href="{{yzWebUrl('coupon.coupon.create')}}"><i class='fa fa-plus'></i> 添加优惠券</a>
                    </div>
                </div>
            </form>

    </div>
    </div>
    </div>

@endsection('content')