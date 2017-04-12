@extends('layouts.base')

@section('content')

    <div class="main rightlist">
        <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <div class="panel panel-default">
                <div class="panel-heading">
                    积分抵扣设置
                </div>

                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">积分抵扣</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="radio-inline">
                                <input type="radio" name="set[point_deduct]" value='1' @if ($set['point_deduct'] == 1) checked @endif /> 开启
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="set[point_deduct]" value='0' @if (empty($set['point_deduct'])) checked @endif /> 关闭
                            </label>
                            <span class='help-block'>开启积分抵扣, 商品最多抵扣的数目需要在商品【营销设置】中单独设置, 否则同意设置</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">积分抵扣比例</label>
                        <div class="col-sm-5">
                            <div class='input-group'>
                                <input type="hidden" name="set[point]" value="1" class="form-control" />
                                <span class='input-group-addon'>1个积分 抵扣</span>
                                <input type="text" name="set[money]"  value="{{$set['money']}}" class="form-control" />
                                <span class='input-group-addon'>元</span>
                            </div>
                            <span class='help-block'>积分抵扣比例设置</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">商品最高抵扣</label>
                        <div class="col-sm-5">
                            <div class='input-group'>
                                <span class='input-group-addon'>最多可抵扣</span>
                                <input type="text" name="set[money_max]"  value="{{$set['money_max']}}" class="form-control" />
                                <span class='input-group-addon'>元</span>
                            </div>
                            <span class='help-block'>商品最高抵扣比例</span>
                        </div>
                    </div>
                </div>

                <div class="panel-heading">
                    积分赠送设置
                </div>
                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">购买商品赠送积分</label>
                        <div class="col-sm-5">
                            <div class='input-group'>
                                <span class='input-group-addon'>购买商品赠送</span>
                                <input type="text" name="set[give_point]"  value="{{$set['give_point']}}" class="form-control" />
                                <span class='input-group-addon'>积分</span>
                            </div>
                            <span class='help-block'>购买<span style="color:red; font-weight:bold">单个</span>商品赠送积分,商品【营销设置】中单独设置</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">消费赠送</label>
                        <div class="col-sm-4">
                            <span class="help-block">两项都填写才能生效 <span style="color:red; font-weight:bold">且阶梯优先级最大</span></span>
                            <div class='input-group'>
                                <span class="input-group-addon">单笔订单满</span>
                                <input type="text" name="set[enough_money]"  value="{{$set['enough_money']}}" class="form-control wid100" />
                                <span class='input-group-addon'>元 赠送</span>
                                <input type="text" name="set[enough_point]"  value="{{$set['enough_point']}}" class="form-control wid100" />
                                <span class='input-group-addon'>积分</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-4">
                            <div class='recharge-items'>
                                @foreach ($set['enoughs'] as $item)
                                <div class="input-group recharge-item" style="margin-top:5px">
                                    <span class="input-group-addon">单笔订单满</span>
                                    <input type="text" class="form-control  wid100" name='enough[]' value='{{$item['enough']}}' />
                                    <span class="input-group-addon">元 赠送</span>
                                    <input type="text" class="form-control wid100"  name='give[]' value='{{$item['give']}}' />
                                    <span class="input-group-addon">积分</span>
                                    <div class='input-group-btn'>
                                        <button class='btn btn-danger' type='button' onclick="removeConsumeItem(this)"><i class='fa fa-remove'></i></button>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div style="margin-top:5px">
                                <button type='button' class="btn btn-default" onclick='addConsumeItem()' style="margin-bottom:5px"><i class='fa fa-plus'></i> 增加项</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1"/>
                    </div>
                </div>
            </div>

        </form>
    </div>
    <script language='javascript'>

        function addConsumeItem(){
            var html= '<div class="input-group recharge-item"  style="margin-top:5px">';
            html+='<span class="input-group-addon">单笔订单满</span>';
            html+='<input type="text" class="form-control wid100" name="enough[]"  />';
            html+='<span class="input-group-addon">元 赠送</span>';
            html+='<input type="text" class="form-control wid100"  name="give[]"  />';
            html+='<span class="input-group-addon">积分</span>';
            html+='<div class="input-group-btn"><button class="btn btn-danger" onclick="removeRechargeItem(this)"><i class="fa fa-remove"></i></button></div>';
            html+='</div>';
            $('.recharge-items').append(html);
        }
        function removeConsumeItem(obj){
            $(obj).closest('.recharge-item').remove();
        }


    </script>
    <script language="javascript">
        $(function () {
            $(":radio[name='set[recharge]']").click(function () {
                if ($(this).val() == 1) {
                    $("#recharge").show();
                }
                else {
                    $("#recharge").hide();
                }
            });
            $(":radio[name='set[withdraw][status]']").click(function () {
                if ($(this).val() == 1) {
                    $("#withdraw").show();
                }
                else {
                    $("#withdraw").hide();
                }
            });
        })
    </script>


@endsection