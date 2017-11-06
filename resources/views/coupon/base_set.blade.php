@extends('layouts.base')
@section('title', '优惠券设置')
@section('content')

    <div class="rightlist">
        @include('layouts.tabs')

        <form action="{{ yzWebUrl('coupon.base-set.store') }}" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <div class='panel panel-default form-horizontal form'>


                <div class='panel-heading'>基础设置</div>

                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠券转让：</label>
                        <div class="col-sm-4 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="coupon[transfer]" value="1" @if ($coupon['transfer'] == 1) checked="checked" @endif />
                                开启
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="coupon[transfer]" value="0" @if ($coupon['transfer'] == 0) checked="checked" @endif />
                                关闭
                            </label>
                            <div class="help-block">
                                优惠券转让：会员之间可以转让自己拥有的优惠券。
                            </div>
                        </div>
                    </div>
                </div>

                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">抵扣奖励积分：</label>
                        <div class="col-sm-4 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="coupon[award_point]" value="1" @if ($coupon['award_point'] == 1) checked="checked" @endif />
                                开启
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="coupon[award_point]" value="0" @if ($coupon['award_point'] == 0) checked="checked" @endif />
                                关闭
                            </label>
                            <div class="help-block">
                                优惠券抵扣金额奖励等值积分，如优惠券抵扣 10元则奖励 10积分。
                            </div>
                        </div>
                    </div>
                </div>





                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9">
                        <input type="submit" name="submit" value="保存设置" class="btn btn-primary col-lg-1" onclick='return formcheck()'/>
                    </div>
                </div>

            </div>
        </form>
    </div>


@endsection

