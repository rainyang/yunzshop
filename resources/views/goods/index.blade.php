@extends('layouts.base')

@section('content')

    <div class="w1200 ">


        <script type="text/javascript" src="./resource/js/lib/jquery-ui-1.10.3.min.js"></script>
        <link rel="stylesheet" type="text/css" href="{{static_url('css/font-awesome.min.css')}}">
        <link rel="stylesheet" type="text/css" href="{{static_url('yunshop/goods/goods.css')}}"/>

        <div id="goods-index" class=" rightlist ">
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="#">商品发布</a></li>

                    @section('add_goods')
                        <a class='btn btn-primary' href="{{yzWebUrl('goods.goods.create')}}" style="margin-bottom:5px;"><i class='fa fa-plus'></i> 发布{{$lang['good']}}</a>
                    @show

                </ul>
            </div>

            <div class="right-addbox"><!-- 此处是右侧内容新包一层div -->

                <div class="panel panel-info">
                    <div class="panel-body">
                        <form action="" method="post" class="form-horizontal" role="form">
                            <input type="hidden" name="c" value="site"/>
                            <input type="hidden" name="a" value="entry"/>
                            <input type="hidden" name="m" value="yun_shop"/>
                            <input type="hidden" name="do" value="shop"/>
                            <input type="hidden" name="p" value="goods"/>
                            <input type="hidden" name="op" value="display"/>
                            @section('search')
                                <div class="form-group col-xs-12 col-sm-8 col-lg-2">
                                    <!--	<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"></label>-->
                                    <div class="">
                                        <input class="form-control" placeholder="请输入关键字" name="search[keyword]" id=""
                                               type="text" value="{{$requestSearch['keyword']}}" onclick="value='';" ／>
                                    </div>
                                </div>
                                <div class="form-group col-xs-12 col-sm-8 col-lg-2">
                                    <!--		<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">状态</label>-->
                                    <div class="">
                                        <select name="search[status]" class='form-control'>
                                            <option value="">状态不限</option>

                                            <option value="1"
                                                    @if($requestSearch['status'] == '1') selected @endif>{{$lang['putaway']}}</option>
                                            <option value="0"
                                                    @if($requestSearch['status'] == '0') selected @endif>{{$lang['soldout']}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-xs-12 col-sm-8 col-lg-5">
                                    <!--<label class="col-xs-12 col-sm-3 col-md-2 control-label">商品分类</label>-->
                                    <div class="col-sm-12 col-xs-12">
                                        {!!$catetory_menus!!}
                                    </div>
                                </div>

                                <div class="form-group col-xs-12 col-sm-8 col-lg-3">
                                    <!--<label class="col-xs-12 col-sm-3 col-md-2 control-label">品牌</label>-->
                                    <div class="col-sm-8 col-xs-12">
                                        <select name="search[brand_id]" id="brand">
                                            <option value="">请选择品牌</option>
                                            @if(!empty($brands))
                                                @foreach($brands as $brand)
                                                    <option value="{{$brand['id']}}"
                                                            @if($requestSearch['brand_id'] == $brand['id']) selected @endif>{{$brand['name']}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class='form-input col-xs-12 col-sm-8 col-lg-6'>
                                    <p class="input-group-addon price">价格区间</p>
                                    <input class="form-control price" name="search[min_price]" id="minprice" type="text"
                                           value="" onclick="value='';" ／>
                                    <p class="line">—</p>
                                    <input class="form-control price" name="search[max_price]" id="max_price"
                                           type="text" value="" onclick="value='';" ／>
                                </div>

                                <div class="form-group col-xs-12 col-sm-8 col-lg-5 goods-type">
                                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label goods-type">商品类型</label>

                                    <div class="col-xs-12 col-sm-8 col-lg-9">
                                        @foreach($product_attr_list as $product_attr_key => $product_attr_name)
                                            <label for="{$product_attr_key}">
                                                <input type="checkbox"
                                                       @if(@in_array($product_attr_key, $product_attr)) checked="checked"
                                                       @endif name="search[product_attr][]"
                                                       value="{{$product_attr_key}}" id="{{$product_attr_key}}"/>
                                                {{$product_attr_name}}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>







                                <!--
                                                        <div class="form-group">
                                                            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">价格区间</label>
                                                            <div class="col-xs-6 col-sm-4 col-lg-4">
                                                                <input class="form-control" name="search[min_price]" id="minprice" type="text" value="" onclick="value='';" ／>
                                                            </div>
                                                            <div class="col-xs-6 col-sm-4 col-lg-4">
                                                                <input class="form-control" name="search[max_price]" id="max_price" type="text" value="" onclick="value='';" ／>
                                                            </div>
                                                        </div>-->
                            @show
                            <div class="form-group col-xs-8 col-sm-8 col-lg-1">
                                <!--	<label class="col-sm-9"></label>-->

                                <button class="btn btn-block btn-success"><i class="fa fa-search"></i> 搜索</button>
                            </div>

                        </form>
                    </div>
                </div>

                <style type="text/css">

                </style>
                <form id="goods-list" action="{!! yzWebUrl('goods.goods.displayorder') !!}" method="post">
                    <div class="panel panel-default">
                        <div class="panel-body table-responsive">
                            <table class="table table-hover">
                                <thead class="navbar-inner">
                                <tr>
                                    <th width="6%">ID</th>
                                    <th width="6%">排序</th>
                                    <th width="6%">{{$lang['good']}}</th>
                                    <th width="26%">&nbsp;</th>
                                    <th width="16%">{{$lang['price']}}<br/>{{$lang['repertory']}}</th>

                                    <th width="10%">销量</th>

                                    <th width="10%">@section('status')状态@show</th>

                                    <th width="20%">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($list as $item)

                                    <tr>
                                        <td width="6%">{{$item['id']}}</td>
                                        <td width="6%">
                                            <input type="text" class="form-control"
                                                   name="display_order[{{$item['id']}}]"
                                                   value="{{$item['display_order']}}">
                                        </td>
                                        <td width="6%" title="{{$item['title']}}">
                                            <img src="{{tomedia($item['thumb'])}}"
                                                 style="width:40px;height:40px;padding:1px;border:1px solid #ccc;"/>
                                        </td>
                                        <td title="{{$item['title']}}" class='tdedit' width="26%">
                                            <span class=' fa-edit-item' style='cursor:pointer'><i class='fa fa-pencil'
                                                                                                  style="display:none"></i> <span
                                                        class="title">{{$item['title']}}</span> </span>
                                            <div class="input-group goodstitle" style="display:none"
                                                 data-goodsid="{{$item['id']}}">
                                                <input type='text' class='form-control' value="{{$item['title']}}"/>
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn btn-default"
                                                            data-goodsid='{{$item['id']}}' data-type="title"><i
                                                                class="fa fa-check"></i></button>
                                                </div>
                                            </div>
                                        </td>
                                        <td class='tdedit' width="16%">
                                            @if($item['has_option']==1)
                                                <span class='tip' title='多规格不支持快速修改'>{{$item['price']}}</span>
                                            @else
                                                <span class=' fa-edit-item' style='cursor:pointer'><i
                                                            class='fa fa-pencil' style="display:none"></i> <span
                                                            class="title">{{$item['price']}}</span> </span>
                                                <div class="input-group" style="display:none"
                                                     data-goodsid="{{$item['id']}}">
                                                    <input type='text' class='form-control' value="{{$item['price']}}"/>
                                                    <div class="input-group-btn">
                                                        <button type="button" class="btn btn-default"
                                                                data-goodsid='{{$item['id']}}' data-type="price"><i
                                                                    class="fa fa-check"></i></button>
                                                    </div>
                                                </div>
                                            @endif
                                            <br/>
                                            @if($item['has_option']==1)
                                                <span class='tip' title='多规格不支持快速修改'>{{$item['stock']}}</span>
                                            @else
                                                <span class=' fa-edit-item' style='cursor:pointer'><i
                                                            class='fa fa-pencil' style="display:none"></i> <span
                                                            class="title">{{$item['stock']}}</span> </span>
                                                <div class="input-group" style="display:none"
                                                     data-goodsid="{{$item['id']}}">
                                                    <input type='text' class='form-control' value="{{$item['stock']}}"/>
                                                    <div class="input-group-btn">
                                                        <button type="button" class="btn btn-default"
                                                                data-goodsid='{{$item['id']}}' data-type="stock"><i
                                                                    class="fa fa-check"></i></button>
                                                    </div>
                                                </div>
                                            @endif


                                        </td>

                                        <td>{{$item['real_sales']}}</td>
                                        <td>

                                            <label data='{{$item['status']}}'
                                                   class='label  label-default @if($item['status']==1) label-info @endif'
                                                   onclick="setProperty(this, {{$item['id']}},'status')">
                                                @if($item['status']==1)
                                                    {{$lang['putaway']}}
                                                @else
                                                    {{$lang['soldout']}}
                                                @endif
                                            </label>

                                            @section('putaway')
                                            @show

                                        </td>

                                        <td style="position:relative; overflow:visible;" width="20%">
                                            <!-- yitian_add::商品链接二维码 2017-02-07 qq:751818588 -->
                                            <a class="btn btn-sm btn-default umphp" title="商品二维码"
                                               data-url="<?php echo yzAppUrl('shop/detail', array('id' => $item['id']));?>"
                                               data-goodsid="{{$item['id']}}">
                                                <div class="img">
                                                    {!! QrCode::size(120)->generate(Request::url()) !!}
                                                </div>
                                                <i class="fa fa-qrcode"></i>
                                            </a>

                                            <a href="{{yzWebUrl('goods.goods.copy', array('id' => $item['id']))}}"
                                               title="{{$lang['copyshop']}}" class="btn btn-default btn-smjs-clip"
                                               style="font-size: 13px;"><i class="fa fa-article"></i></a>

                                            <a href="{{yzWebUrl($edit_url, array('id' => $item['id']))}}"
                                               class="btn btn-sm btn-default" title="编辑"><i class="fa fa-edit"></i></a>

                                            <a href="{{yzWebUrl($delete_url, array('id' => $item['id']))}}"
                                               onclick="return confirm('{{$delete_msg}}');
                                                       return false;" class="btn btn-default  btn-sm" title="删除"><i
                                                        class="fa fa-trash"></i></a>

                                            <a href="javascript:;"
                                               data-url="{{yzAppUrl('shop/detail', array('id' => $item['id']))}}"
                                               title="复制连接" class="btn btn-default btn-sm js-clip"><i
                                                        class="fa fa-link"></i></a>
                                        </td>
                                    </tr>
                                    <tr class="goods-state">
                                        <td colspan="10">
                                            <label class="empty"></label>

                                            @if($item['goods_sn'])
                                                <label data='{{$item['isnew']}}'
                                                       class='label label-default text-default'>商品编号：</label><span
                                                        style="font-size:14px;color:#7B7B7B; margin-right:20px;">{{$item['goods_sn']}}</span>
                                            @endif
                                            <label data='{{$item['is_new']}}'
                                                   class='label label-default text-default @if($item['is_new']==1)label-info text-pinfo @endif'
                                                   onclick="setProperty(this,{{$item['id']}},'is_new')">新品</label>-

                                            <label data='{{$item['is_hot']}}'
                                                   class='label label-default text-default @if($item['is_hot']==1)label-info text-pinfo @endif'
                                                   onclick="setProperty(this,{{$item['id']}},'is_hot')">热卖</label>-

                                            <label data='{{$item['is_recommand']}}'
                                                   class='label label-default text-default @if($item['is_recommand']==1)label-info text-pinfo @endif'
                                                   onclick="setProperty(this,{{$item['id']}},'is_recommand')">推荐</label>-

                                            <label data='{{$item['is_discount']}}'
                                                   class='label label-default text-default @if($item['is_discount']==1)label-info text-pinfo @endif'
                                                   onclick="setProperty(this,{{$item['id']}},'is_discount')">促销</label>-


                                        </td>
                                    </tr>

                                    @endforeach

                                    @section('release_gods')
                                            <!--	<tr>
								<td colspan='10'>
									@section('add_goods')
                                            <a class='btn btn-primary' href="{{yzWebUrl('goods.goods.create')}}"><i class='fa fa-plus'></i> 发布{{$lang['good']}}</a>
									@show
                                            <input name="submit" type="submit" class="btn btn-default" value="提交排序">
                                            <input type="hidden" name="token" value="{{$var['token']}}" />

								</td>
							</tr>-->
                                    @show
                                    </tr>
                                </tbody>
                            </table>


                            {!!$pager!!}
                                    <!--分页-->


                        </div>
                        <div class='panel-footer'>
                            <a class='btn btn-success ' href="{{yzWebUrl('goods.goods.create')}}"><i
                                        class='fa fa-plus'></i> 发布{{$lang['good']}}</a>
                            <input name="submit" type="submit" class="btn btn-default back" value="提交排序">
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>

    <script type="text/javascript">
        $('.js-clip').each(function () {
            util.clip(this, $(this).attr('data-url'));
        });
        //鼠标划过显示商品链接二维码
        $('.umphp').hover(function () {
                    var url = $(this).attr('data-url');
                    $(this).addClass("selected");
                },
                function () {
                    $(this).removeClass("selected");
                })
        function fastChange(id, type, value) {
            $.ajax({
                url: "{!! yzWebUrl('goods.goods.change') !!}",
                type: "post",
                data: {id: id, type: type, value: value},
                cache: false,
                success: function ($data) {
                    //console.log($data);
                    location.reload();
                }
            })
        }
        $(function () {
            $("form").keypress(function (e) {
                if (e.which == 13) {
                    return false;
                }
            });

            $('.tdedit input').keydown(function (event) {
                if (event.keyCode == 13) {
                    var group = $(this).closest('.input-group');
                    var type = group.find('button').data('type');
                    var goodsid = group.find('button').data('goodsid');
                    var val = $.trim($(this).val());
                    if (type == 'title' && val == '') {
                        return;
                    }
                    group.prev().show().find('span').html(val);
                    group.hide();
                    fastChange(goodsid, type, val);
                }
            })
            $('.tdedit').mouseover(function () {
                $(this).find('.fa-pencil').show();
            }).mouseout(function () {
                $(this).find('.fa-pencil').hide();
            });
            $('.fa-edit-item').click(function () {
                var group = $(this).closest('span').hide().next();

                group.show().find('button').unbind('click').click(function () {
                    var type = $(this).data('type');
                    var goodsid = $(this).data('goodsid');
                    var val = $.trim(group.find(':input').val());
                    if (type == 'title' && val == '') {
                        Tip.show(group.find(':input'), '请输入名称!');
                        return;
                    }
                    group.prev().show().find('span').html(val);
                    group.hide();
                    fastChange(goodsid, type, val);
                });
            })
        })
        function setProperty(obj, id, type) {
            $(obj).html($(obj).html() + "...");
            $.post("{!! yzWebUrl('goods.goods.setProperty') !!}", {id: id, type: type, data: obj.getAttribute("data")}
                    , function (d) {
                        console.log(d);
                        $(obj).html($(obj).html().replace("...", ""));
                        if (type == 'type') {
                            $(obj).html(d.data == '1' ? '实体物品' : '虚拟物品');
                        }
                        if (type == 'status') {
                            $(obj).html(d.data == '1' ? '{{$lang['putaway']}}' : '{{$lang['soldout']}}');
                        }
                        $(obj).attr("data", d.data);
                        if (d.result == 1) {
                            $(obj).toggleClass("label-info text-pinfo");
                        }
                    }
                    , "json"
            );
        }
        require(['select2'], function () {
            $('#brand').select2();
        })
    </script>

@endsection('content')