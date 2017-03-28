<!-- 供货商end -->
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
    <div class="col-sm-9 col-xs-12">
        <input type="text" name="goods[display_order]" id="displayorder" class="form-control" value="{{$goods['display_order']}}" />
        <span class='help-block'>数字大的排名在前,默认排序方式为创建时间</span>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>{{$lang['shopname']}}</label>
    <div class="col-sm-9 col-xs-12">
        <input type="text" name="goods[title]" id="goodsname" class="form-control" value="{{$goods['title']}}" />
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>商品分类</label>
    <div class="col-sm-8 col-xs-12">

        {!!$catetory_menus!!}

    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">品牌</label>
    <div class="col-sm-8 col-xs-12">
        <select name="goods[brand_id]">
            <option value="0">请选择品牌</option>
            @if (!empty($brands))
            @foreach ($brands as $brand)
            <option value="{{$brand['id']}}">{{$brand['name']}}</option>
            @endforeach
            @endif
        </select>
    </div>
</div>

<link href="../addons/sz_yi/static/js/dist/select2/select2.css" rel="stylesheet">
<link href="../addons/sz_yi/static/js/dist/select2/select2-bootstrap.css" rel="stylesheet">

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">商品类型</label>
    <div class="col-sm-9 col-xs-12">
         <div style="float: left" id="ttttype">
            <label for="isshow3" class="radio-inline"><input type="radio" name="goods[type]" value="1" id="isshow3" @if (empty($goods['type']) || $goods['type'] == 1) checked="true" @endif onclick="$('#product').show();$('#type_virtual').hide();$('#divdeposit').hide();" /> 实体商品</label>
            <label for="isshow4" class="radio-inline"><input type="radio" name="goods[type]" value="2" id="isshow4"  @if ($goods['type'] == 2) checked="true" @endif  onclick="$('#product').hide();$('#type_virtual').hide();$('#divdeposit').hide()" /> 虚拟商品</label>
        </div>

    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>商品单位</label>
    <div class="col-sm-6 col-xs-6">
        <input type="text" name="goods[sku]" id="unit" class="form-control" value="{{$goods['sku']}}" />
        <span class="help-block">如: 个/件/包</span>

    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">商品属性</label>
    <div class="col-sm-9 col-xs-12" >
        <label for="isrecommand" class="checkbox-inline">
            <input type="checkbox" name="goods[is_recommand]" value="1" id="isrecommand" @if ($goods['is_recommand'] == 1) checked="true" @endif /> 推荐
        </label>
        <label for="isnew" class="checkbox-inline">
            <input type="checkbox" name="goods[is_new]" value="1" id="isnew" @if ($goods['is_new'] == 1) checked="true" @endif /> 新上
        </label>
        <label for="ishot" class="checkbox-inline">
            <input type="checkbox" name="goods[is_hot]" value="1" id="ishot" @if ($goods['is_hot'] == 1) checked="true" @endif /> 热卖
        </label>
        <label for="isdiscount" class="checkbox-inline">
            <input type="checkbox" name="goods[is_discount]" value="1" id="isdiscount" @if ($goods['is_discount'] == 1) checked="true" @endif /> 促销
        </label>

    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>{{$lang['mainimg']}}</label>
    <div class="col-sm-9 col-xs-12 detail-logo">
        {!! app\common\helpers\ImageHelper::tplFormFieldImage('goods[thumb]', $goods['thumb']) !!}
        <span class="help-block">建议尺寸: 640 * 640 ，或正方型图片 </span>
        @if (!empty($goods['thumb']))
        <a href='{{tomedia($goods['thumb'])}}' target='_blank'>
        <img src="{{tomedia($goods['thumb'])}}" style='width:100px;border:1px solid #ccc;padding:1px' />
         </a>
        @endif
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">其他图片</label>
    <div class="col-sm-9 col-xs-12">

        {!! app\common\helpers\ImageHelper::tplFormFieldMultiImage('goods[thumb_url]',$goods['thumb_url']) !!}
            <span class="help-block">建议尺寸: 640 * 640 ，或正方型图片 </span>
            @if (!empty($goods['piclist']))
                 @foreach ($goods['piclist'] as $p)
                 <a href='{{tomedia($p)}}' target='_blank'>
                   <img src="{{tomedia($p)}}" style='height:100px;border:1px solid #ccc;padding:1px;float:left;margin-right:5px;' />
                 </a>
                 @endforeach
            @endif
    </div>
</div>

<div class="form-group">
    <label class=" col-sm-3 col-md-2 control-label">商品条码</label>
    <div class="col-sm-4 col-xs-12">
        <input type="text" name="goods[product_sn]" id="productsn" class="form-control" value="{{$goods['product_sn']}}" />
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>商品价格</label>
    <div class="col-sm-9 col-xs-12">
        <div class="input-group form-group">
            <span class="input-group-addon">现价</span>
            <input type="text" name="goods[price]" id="product_price" class="form-control" value="{{$goods['price']}}" />
            <span class="input-group-addon">元</span>
        </div>
        <div class="input-group form-group">
            <span class="input-group-addon">原价</span>
            <input type="text" name="goods[market_price]" id="market_price" class="form-control" value="{{$goods['market_price']}}" />
            <span class="input-group-addon">元</span>
        </div>
        <div class="input-group form-group">
            <span class="input-group-addon">成本</span>
            <input type="text" name="goods[cost_price]" id="costprice" class="form-control" value="{{$goods['cost_price']}}" />
            <span class="input-group-addon">元</span>
        </div>
        <span class='help-block'>尽量填写完整，有助于于商品销售的数据分析</span>

    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>库存</label>
    <div class="col-sm-6 col-xs-12">
        <div class="input-group">
            <input type="text" name="goods[stock]" id="total" class="form-control" value="{{$goods['stock']}}" />
            <span class="input-group-addon">件</span>
        </div>
        <span class="help-block">商品的剩余数量, 如启用多规格或为虚拟卡密产品，则此处设置无效，请移至“商品规格”或“虚拟物品插件”中设置</span>
    </div>
</div>


<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">减库存方式</label>
    <div class="col-sm-9 col-xs-12">
        <label for="totalcnf1" class="radio-inline"><input type="radio" name="goods[reduce_stock_method]" value="0" id="totalcnf1" @if (empty($goods) || $goods['reduce_stock_method'] == 0) checked="true" @endif /> 拍下减库存</label>
        &nbsp;&nbsp;&nbsp;
        <label for="totalcnf2" class="radio-inline"><input type="radio" name="goods[reduce_stock_method]" value="1" id="totalcnf2"  @if (!empty($goods) && $goods['reduce_stock_method'] == 1) checked="true" @endif /> 付款减库存</label>
        &nbsp;&nbsp;&nbsp;
        <label for="totalcnf3" class="radio-inline"><input type="radio" name="goods[reduce_stock_method]" value="2" id="totalcnf3"  @if (!empty($goods) && $goods['reduce_stock_method'] == 2) checked="true" @endif /> 永不减库存</label>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{$lang['isputaway']}}</label>
    <div class="col-sm-9 col-xs-12">
        <label for="isshow1" class="radio-inline"><input type="radio" name="goods[status]" value="1" id="isshow1" @if ($goods['status'] == 1) checked="true" @endif /> 是</label>
        &nbsp;&nbsp;&nbsp;
        <label for="isshow2" class="radio-inline"><input type="radio" name="goods[status]" value="0" id="isshow2"  @if ($goods['status'] == 0) checked="true" @endif /> 否</label>
        <span class="help-block"></span>

    </div>
</div>
