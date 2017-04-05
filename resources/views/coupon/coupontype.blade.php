
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">使用条件</label>
    <div class="col-sm-9 col-xs-12">
        <input type="text" name="coupon[enough]" class="form-control" value="{{$coupon['enough']}}"  />
        <span class='help-block' >满多少可用, 空或0 不限制</span>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">使用时间限制</label>

    <div class="col-sm-5">
        <div class='input-group'>
                        <span class='input-group-addon'>
                             <label class="radio-inline" style='margin-top:-5px;' ><input type="radio" name="coupon[time_limit]" value="0" @if ($coupon['time_limit']==0) checked  @endif>获得后</label>
                        </span>

            <input type='text' class='form-control' name='coupon[time_days]' value="{{$coupon['time_days']}}" />
            <span class='input-group-addon'>天内有效(空为不限时间使用)</span>
        </div>
    </div>

    <div class="col-sm-3">
        <div class='input-group'>
                        <span class='input-group-addon'>
                             <label class="radio-inline" style='margin-top:-5px;' ><input type="radio" name="coupon[time_limit]" value="1" >日期</label>
                        </span>

            <span class='input-group-addon'>内有效</span>
        </div>
    </div>
</div>
@include('coupon.consume')
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">适用范围</label>
    <div class="col-sm-9 col-xs-12">
        <input type="hidden" name="coupon_type" value="0"/>
        <label class="radio-inline " ><input type="radio" name="coupon[use_type]" onclick='showusetype(0)' value="0" @if($coupon['use_type']==0)checked @endif>全类适用</label>
        <label class="radio-inline"><input type="radio" name="coupon[use_type]" onclick='showusetype(1)' value="1" @if($coupon['use_type']==1)checked @endif>指定商品分类</label>
        <label class="radio-inline "><input type="radio" name="coupon[use_type]" onclick='showusetype(2)' value="2" @if($coupon['use_type']==2)checked @endif>指定商品</label>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>

    <div class="col-sm-2 usetype usetype0"  @if($coupon['use_type']!=0)style='display:none' @endif>
        <div class='input-group'>
            <span class='help-block'>如选择此项,则支持商城所有商品使用!</span>
        </div>
    </div>
    <div class="col-sm-7 usetype usetype1"  @if($coupon['use_type']!=1)style='display:none' @endif>
        <div class='input-group'>
            <div id="category" >
                <table class="table">
                    <thead>

                    </thead>
                    <tbody id="param-itemscategory">
                    {{--{loop $coupon[categoryids] $k $v}
                    <tr>
                        <td>
                            <a href="javascript:;" class="fa fa-move" title="拖动调整此显示顺序" ><i class="fa fa-arrows"></i></a>&nbsp;
                            <a href="javascript:;" onclick="deleteParam(this)" style="margin-top:10px;"  title="删除"><i class='fa fa-times'></i></a>
                        </td>
                        <td  colspan="2">
                            <input type="hidden" class="form-control" name="categoryids[]" data-id="{$v}" data-name="categoryids"  value="{$v}" style="width:200px;float:left"  />

                            <input class="form-control" type="text" data-id="{$v}" data-name="categorynames" name="categorynames[]"  value="{{$coupon[categorynames][$k]}}" style="width:200px;float:left">
                                          <span class="input-group-btn">
                                              <button class="btn btn-default nav-link" type="button" data-id="{$v}" >选择分类</button>
                                          </span>


                        </td>

                    </tr>
                    {/loop}--}}
                    </tbody>
                    <tbody>
                    <tr>
                        <td colspan="3">
                            <a href="javascript:;" id='add-param_category' onclick="addParam('category')" style="margin-top:10px;" class="btn btn-primary"  title="添加分类"><i class='fa fa-plus'></i> 添加分类</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-sm-7 usetype usetype2"  @if($coupon['use_type']!=2)style='display:none' @endif>
        <div class='input-group'>

            <div id="goods">
                <table class="table">
                    <tbody id="param-itemsgoods">
                    @if ($coupon['goods_ids'])
                        @foreach ($coupon['goods_ids'] as $k=>$v)
                            <tr>
                                <td>
                                    <a href="javascript:;" class="fa fa-move" title="拖动调整此显示顺序" ><i class="fa fa-arrows"></i></a>&nbsp;
                                    <a href="javascript:;" onclick="deleteParam(this)" style="margin-top:10px;"  title="删除"><i class='fa fa-times'></i></a>
                                </td>
                                <td  colspan="2">
                                    <input type="hidden" class="form-control" name="goods_ids[]" data-id="{$v}" data-name="goods_ids"  value="{$v}" style="width:200px;float:left"  />

                                    <input class="form-control" type="text" data-id="{$v}" data-name="goodsnames" name="goods_names[]"  value="{$coupon[goods_names][$k]}" style="width:200px;float:left">
                                              <span class="input-group-btn">
                                                  <button class="btn btn-default nav-link-goods" type="button" data-id="{$v}" >选择商品</button>
                                              </span>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>

                    <tbody>
                    <tr>
                        <td colspan="3">
                            <a href="javascript:;" id='add-param_goods' onclick="addParam('goods')" style="margin-top:10px;" class="btn btn-primary"  title="添加商品"><i class='fa fa-plus'></i> 添加商品</a>
                        </td>
                    </tr>
                    </tbody>

                </table>

            </div>
        </div>

    </div>　
</div>

<div id="goods" style="display: none">

</div>


<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">领券中心是否可获得</label>
    <div class="col-sm-9 col-xs-12" >
        <label class="radio-inline">
            <input type="radio" name="coupon[get_type]" value="0" @if($coupon['get_type'] == 0)checked="true" @endif  onclick="$('.gettype').hide()"/> 不可以
        </label>
        <label class="radio-inline">
            <input type="radio" name="coupon[get_type]" value="1" @if($coupon['get_type'] == 1)checked="true" @endif onclick="$('.gettype').show()" /> 可以
        </label>
        <span class='help-block'>会员是否可以在领券中心直接领取或购买</span>

    </div>
</div>

<div class="form-group gettype" @if($coupon['get_type']!=1)style="display:none" @endif>
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
    <div class="col-sm-6">
        <div class="input-group">
            <span class="input-group-addon">每个限领</span>
            <input type='text' class='form-control' value="{{$coupon['get_max']}}" name='coupon[get_max]' style="width: 80px" />
            <span class="input-group-addon">张 消耗</span>
            <input style="width: 80px"  type='text' class='form-control' value="{{$coupon['credit']}}" name='coupon[credit]'/>
            <span class="input-group-addon">积分 + 花费</span>
            <input style="width: 80px"  type='text' class='form-control' value="{{$coupon['money']}}" name='coupon[money]'/>
                              <span class="input-group-addon">元&nbsp;&nbsp;
                                  <label class="checkbox-inline" style='margin-top:-8px;'>
                                      <input type="checkbox" name='coupon[usecredit2]' value="1" @if($coupon['usecredit2']==1)checked @endif /> 优先使用余额支付
                                  </label>
                              </span></div>
        <span class="help-block">每人限领，空不限制，领取方式可任意组合，可以单独积分兑换，单独现金兑换，或者积分+现金形式兑换, 如果都为空，则可以免费领取</span>

    </div>

</div>
　
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">发放总数</label>
    <div class="col-sm-9 col-xs-12">
        <input type="text" name="coupon[total]" class="form-control" value="{{$coupon['total']}}"  />
        <span class='help-block' >优惠券总数量，没有不能领取或发放,-1 为不限制张数</span>
    </div>
</div>
　