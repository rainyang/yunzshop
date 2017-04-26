
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">使用条件</label>
    <div class="col-sm-9 col-xs-12">
        <input type="text" name="coupon[enough]" class="form-control" value="{{isset($coupon['enough']) ? $coupon['enough'] : 0}}"  />
        <span class='help-block' >消费满多少金额才可以使用该优惠券 (设置为空或 0 则不限制消费金额)</span>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">使用时间限制</label>
    <div class="col-sm-9 form-inline">
        <div class='input-group form-group col-sm-6'>
            <span class='input-group-addon'>
                 <label class="radio-inline" style='margin-top:-5px;' >
                     <input type="radio" name="coupon[time_limit]" value="0" checked>获得后
                 </label>
            </span>
            <input type='text' class='form-control' name='coupon[time_days]' value="{{isset($coupon['time_days']) ? $coupon['time_days'] : 0}}" />
            <span class='input-group-addon'>天内有效(空为不限时间使用)</span>
        </div>
        <br>
        <div class='input-group form-group col-sm-3'>
            <span class='input-group-addon'>
                 <label class="radio-inline" style='margin-top:-5px;' >
                     <input type="radio" name="coupon[time_limit]" value="1" @if ($coupon['time_limit']==1) checked  @endif>日期
                 </label>
            </span>
            {!! tpl_form_field_daterange('time', array(
                    'starttime'=>date('Y-m-d', isset($coupon['time_start']) ? $coupon['time_start'] : strtotime('today')),
                    'endtime'=>date('Y-m-d', isset($coupon['time_end']) ? $coupon['time_end'] : strtotime('+7 days')))
            ) !!}
            <span class='input-group-addon'>内有效</span>
        </div>
    </div>
</div>
@include('coupon.consume')
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">适用范围</label>
    <div class="col-sm-9 col-xs-12">
        <label class="radio-inline"><input type="radio" name="usetype" onclick='showusetype(0)' value="0" checked>全类适用</label>
        <label class="radio-inline"><input type="radio" name="usetype" onclick='showusetype(1)' value="1" @if($coupon['use_type']==1)checked @endif>指定商品分类</label>
        <label class="radio-inline"><input type="radio" name="usetype" onclick='showusetype(2)' value="2" @if($coupon['use_type']==2)checked @endif>指定商品</label>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>

    <div class="col-sm-7 usetype usetype0"  @if($coupon['use_type']!=0)style='display:none' @endif>
        <div class='input-group'>
            <span class='help-block'>如选择此项,则支持商城所有商品使用!</span>
        </div>
    </div>
    <div class="col-sm-7 usetype usetype1"  @if($coupon['use_type']!=1)style='display:none' @endif>
        <div class='input-group'>
            <div id="category" >
                <table class="table">
                    <tbody id="param-itemscategory">
                    @if($coupon['category_ids'])
                    @foreach($coupon['category_ids'] as $k=>$v)
                        <tr>
                            <td>
                                <a href="javascript:;" onclick="deleteParam(this)" style="margin-top:10px;"  title="删除"><i class='fa fa-times'></i></a>
                            </td>
                            <td  colspan="2">
                                <input id="categoryid" type="hidden" class="form-control" name="category_ids[]" data-id="{{$v}}" data-name="categoryids"  value="{{$v}}" style="width:200px;float:left"  />
                                <input id="categoryname" class="form-control" type="text" name="category_names[]" data-id="{{$v}}" data-name="categorynames" value="{{$coupon['categorynames'][$k]}}" style="width:200px;float:left">
                                <span class="input-group-btn">
                                    <button class="btn btn-default nav-link" type="button" data-id="{{$v}}" onclick="$('#modal-module-menus-categorys').modal();$(this).parent().parent().addClass('focuscategory')" >选择分类</button>
                                </span>
                            </td>
                        </tr>
                    @endforeach
                    @endif
                    </tbody>
                    <tbody>
                    <tr>
                        <td colspan="3">
                            <a href="javascript:;" id='add-param_category' onclick="addParam('category')"
                               style="margin-top:10px;" class="btn btn-primary"  title="添加分类"><i class='fa fa-plus'></i> 添加分类</a>
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
                                <a href="javascript:;" onclick="deleteParam(this)" style="margin-top:10px;"  title="删除"><i class='fa fa-times'></i></a>
                            </td>
                            <td  colspan="2">
                                <input id="goodid" type="hidden" class="form-control" name="goods_ids[]" data-id="{{$v}}" data-name="goods_ids"  value="{{$v}}" style="width:200px;float:left"  />
                                <input id="goodname" class="form-control" type="text" name="goods_names[]" data-id="{{$v}}" data-name="goodsnames" value="{{$coupon['goods_names'][$k]}}" style="width:200px;float:left">
                                <span class="input-group-btn">
                                    <button class="btn btn-default nav-link-goods" type="button" data-id="{{$v}}" onclick="$('#modal-module-menus-goods').modal();$(this).parent().parent().addClass('focusgood')">选择商品</button>
                                </span>
                            </td>
                        </tr>
                    @endforeach
                    @endif
                    </tbody>

                    <tbody>
                    <tr>
                        <td colspan="3">
                            <a href="javascript:;" id='add-param_goods' onclick="addParam('goods')"
                               style="margin-top:10px;" class="btn btn-primary" title="添加商品"><i class='fa fa-plus'></i> 添加商品</a>
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

<div id="modal-module-menus-categorys" class="modal fade" tabindex="-1"> {{--搜索分类的弹窗--}}
    <div class="modal-dialog" style='width: 920px;'>
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">
                    ×
                </button>
                <h3>选择分类</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="input-group">
                        <input type="text" class="form-control" name="keyword" value=""
                               id="search-kwd-categorys" placeholder="请输入分类名称"/>
                        <span class='input-group-btn'>
                            <button type="button" class="btn btn-default" onclick="search_categorys();">搜索
                            </button>
                        </span>
                    </div>
                </div>
                <div id="module-menus-categorys" style="padding-top:5px;"></div>
            </div>
            <div class="modal-footer"><a href="#" class="btn btn-default"
                                         data-dismiss="modal" aria-hidden="true">关闭</a>
            </div>
        </div>

    </div>
</div>

<div id="modal-module-menus-goods" class="modal fade" tabindex="-1"> {{--搜索商品的弹窗--}}
    <div class="modal-dialog" style='width: 920px;'>
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">
                    ×
                </button>
                <h3>选择商品</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="input-group">
                        <input type="text" class="form-control" name="keyword" value=""
                               id="search-kwd-goods" placeholder="请输入商品名称"/>
                        <span class='input-group-btn'>
                            <button type="button" class="btn btn-default" onclick="search_goods();">搜索
                            </button>
                        </span>
                    </div>
                </div>
                <div id="module-menus-goods" style="padding-top:5px;"></div>
            </div>
            <div class="modal-footer"><a href="#" class="btn btn-default"
                                         data-dismiss="modal" aria-hidden="true">关闭</a>
            </div>
        </div>

    </div>
</div>


<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否可直接领取</label>
    <div class="col-sm-9 col-xs-12" >
        <label class="radio-inline">
            <input type="radio" name="coupon[get_type]" value="0" checked  onclick="$('.gettype').hide()"/> 不可以
        </label>
        <label class="radio-inline">
            <input type="radio" name="coupon[get_type]" value="1" @if($coupon['get_type'] == 1)checked="true" @endif onclick="$('.gettype').show()" /> 可以
        </label>
        <span class='help-block'>会员是否可以在领券中心直接领取或购买</span>

    </div>
</div>

<div class="form-group gettype" @if($coupon['get_type']!=1)style="display:none" @endif>
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
    <div class="col-sm-9 form-inline">

        <div class="input-group form-group col-sm-1">
            <span class="input-group-addon">每个限领</span>
            <input type='text' class='form-control' value="{{isset($coupon['get_max']) ? $coupon['get_max'] : 1}}" name='coupon[get_max]' style="width: 80px" />
            </div>
        <div class="input-group form-group col-sm-1">
            <span class="input-group-addon">张 消耗</span>
            <input style="width: 80px"  type='text' class='form-control' value="{{isset($coupon['credit']) ? $coupon['credit'] : 0}}" name='coupon[credit]'/>
        </div>
        <div class="input-group form-group col-sm-1">
            <span class="input-group-addon">积分 + 花费</span>
                <input style="width: 80px"  type='text' class='form-control' value="{{isset($coupon['money']) ? $coupon['money'] : 0}}" name='coupon[money]'/>
            <span class="input-group-addon">元</span>
        </div>
        <div class="input-group form-group col-sm-3">
            <label class="checkbox-inline" style='margin-top:-8px;'>
                <input type="checkbox" name='coupon[usecredit2]' value="1" @if($coupon['usecredit2']==1)checked @endif /> 优先使用余额支付
            </label>
        </div>
        <span class="help-block">每人限领数量 (-1为不限制数量); 领取方式可任意组合，可以单独积分兑换，单独现金兑换，或者积分+现金形式兑换, 如果都为空，则可以免费领取</span>

    </div>

</div>
　
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">发放总数</label>
    <div class="col-sm-9 col-xs-12">
        <input type="text" name="coupon[total]" class="form-control" value="{{isset($coupon['total']) ? $coupon['total'] : 0}}"  />
        <span class='help-block' >优惠券总数量，没有则不能领取或发放, -1 为不限制数量</span>
    </div>
</div>
　