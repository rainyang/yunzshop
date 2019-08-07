@extends('layouts.base')

@section('content')
@section('title', trans('商品列表'))
<div class="w1200 ">

    <script type="text/javascript" src="./resource/js/lib/jquery-ui-1.10.3.min.js"></script>
    <link rel="stylesheet" type="text/css" href="{{static_url('css/font-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{static_url('yunshop/goods/goods.css')}}"/>

    <div id="goods-index" class=" rightlist ">
        {{--<div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">商品发布</a></li>

                @section('add_goods')
                    <a class='btn btn-primary' href="{{yzWebUrl('goods.goods.create')}}" style="margin-bottom:5px;"><i class='fa fa-plus'></i> 发布{{$lang['good']}}</a>
                @show

            </ul>
        </div>
--}}
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
                                <!--    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"></label>-->
                                <div class="">
                                    <input class="form-control" placeholder="请输入商品id" name="search[goods_id]" id="" type="text" value="{{$requestSearch['goods_id']}}" ／>
                                </div>
                            </div>
                            <div class="form-group col-xs-12 col-sm-8 col-lg-2">
                                <!--	<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"></label>-->
                                <div class="">
                                    <input class="form-control" placeholder="请输入关键字" name="search[keyword]" id="" type="text" value="{{$requestSearch['keyword']}}" ／>
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
                            <div class="form-group col-xs-12 col-sm-8 col-lg-2">
                                <!--		<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">出售库存</label>-->
                                <div class="">
                                    <select name="search[sell_stock]" class='form-control'>
                                        <option value="">售中库存</option>

                                        <option value="1"
                                                @if($requestSearch['sell_stock'] == '1') selected @endif>{{$lang['yes_stock']}}</option>
                                        <option value="0"
                                                @if($requestSearch['sell_stock'] == '0') selected @endif>{{$lang['no_stock']}}</option>
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
                                    <select class="form-control tpl-category-child" name="search[brand_id]" id="brand">
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
                                       type="text" value="" onclick="value='';">
                            </div>

                            <div class="form-group col-xs-12 col-sm-8 col-lg-5 goods-type">
                                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label goods-type">商品类型</label>

                                <div class="col-xs-12 col-sm-8 col-lg-9">
                                    @foreach($product_attr_list as $product_attr_key => $product_attr_name)
                                        <label for="{$product_attr_key}">
                                            <input type="checkbox"
                                                   @if(@in_array($product_attr_key, $requestSearch['product_attr'])) checked="checked"
                                                   @endif name="search[product_attr][]"
                                                   value="{{$product_attr_key}}" id="{{$product_attr_key}}"/>
                                            {{$product_attr_name}}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <!--<div class="form-group">
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
            <form id="goods-list" action="{!! yzWebUrl($sort_url) !!}" method="post">
                <div class="panel panel-default">
                    <div class="panel-body table-responsive">
                        @section('batch_top')
                            <label class="btn btn-success checkall">全选</label>
                            <label class="btn btn-info batchenable">批量上架</label>
                            <label class="btn batchdisable">批量下架</label>
                            <label class="btn btn-danger batchdel">批量删除</label>
                            <label class="btn btn-warning"><a href="{!! yzWebUrl('goods.goods.import') !!}">商品excel导入</a></label>
                        @show
                        <table class="table table-hover">
                            <thead class="navbar-inner">
                            <tr>
                                <th width="3%">选择</th>
                                <th width="6%">ID</th>
                                <th width="6%">排序</th>
                                <th width="6%">{{$lang['good']}}</th>
                                <th width="31%">&nbsp;</th>
                                <th width="10%">{{$lang['price']}}<br/>{{$lang['repertory']}}</th>

                                <th width="5%">销量</th>

                                <th width="10%">@section('status')状态@show</th>

                                <th width="25%">操作</th>
                            </tr>
                            </thead>
                            <tbody>

                            @section('foreach')
                                @foreach($list as $item)

                                    <tr>
                                        <td width="3%"><input type="checkbox" name="check1" value="{{$item['id']}}">
                                        </td>
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
                                        <td title="{{$item['title']}}" class='tdedit' width="26%"
                                            style="white-space:normal">
                                            <span class=' fa-edit-item' style='cursor:pointer'><i class='fa fa-pencil'
                                                                                                  style="display:none"></i> <span
                                                        class="title">{{$item['title']}}</span> </span>
                                            <div class="input-group goodstitle" style="display:none"
                                                 data-goodsid="{{$item['id']}}">
                                                <input type='text' class='form-control' value="{{$item['title']}}"/>
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn btn-info"
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
                                                        <button type="button" class="btn btn-info"
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
                                                        <button type="button" class="btn btn-info"
                                                                data-goodsid='{{$item['id']}}' data-type="stock"><i
                                                                    class="fa fa-check"></i></button>
                                                    </div>
                                                </div>
                                            @endif


                                        </td>

                                        <td>{{$item['real_sales']}}</td>
                                        <td>
                                            <label data='{{$item['status']}}'
                                                   class='@if($item['status']==1) btn btn-info @else  btn btn-default  @endif'
                                                   onclick="setPutaway(this, {{$item['id']}},'status')">
                                                @if($item['status']==1)
                                                    {{$lang['putaway']}}
                                                @else
                                                    {{$lang['soldout']}}
                                                @endif
                                            </label>
                                        </td>

                                        <td style="position:relative; overflow:visible;" width="25%">
                                            <div class="btn-group">
                                                {{--<div class="td-actions text-right">--}}
                                                {{--<a tabindex="0" class="btn btn-lg btn-danger" role="button" data-toggle="popover" data-trigger="focus" title="推广二维码" data-content="And here's some amazing content. It's very engaging. Right?">
                                                    可消失的弹出框
                                                </a>--}}
                                                @if (in_array($item['id'], $courseGoods_ids))
                                                    <a class="umphp" title="商品二维码"
                                                       data-url="{{yzAppFullUrl('member/coursedetail/'.$item['id'])}}"
                                                       data-goodsid="{{$item['id']}}">
                                                        <div class="img">
                                                            {!! QrCode::size(120)->generate(yzAppFullUrl('member/coursedetail/'.$item['id'])) !!}
                                                        </div>
                                                        <span>推广链接</span>
                                                    </a>
                                                @else
                                                    <a class="umphp" title="商品二维码"
                                                       data-url="{{yzAppFullUrl('goods/'.$item['id'])}}"
                                                       data-goodsid="{{$item['id']}}">
                                                        <div class="img">
                                                            {!! QrCode::size(120)->generate(yzAppFullUrl('goods/'.$item['id'])) !!}
                                                        </div>
                                                        <span>推广链接</span>
                                                    </a>
                                                @endif
                                                <a href="{{$yz_url($copy_url, array('id' => $item['id']))}}"
                                                   title="{{$lang['copyshop']}}" class=""
                                                   style="">复制商品</a>

                                                <a href="{{$yz_url($edit_url, array('id' => $item['id']))}}"
                                                   class="" title="编辑">编辑</a>

                                                <a href="{{$yz_url($delete_url, array('id' => $item['id']))}}"
                                                   onclick="return confirm('{{$delete_msg}}');
                                                           return false;" class="" title="删除">删除</a>

                                                @if (empty($courseGoods_ids))
                                                    <a href="javascript:;"
                                                       data-clipboard-text="{{yzAppFullUrl('goods/'.$item['id'])}}"
                                                       data-url="{{yzAppFullUrl('goods/'.$item['id'])}}"
                                                       title="复制连接" class="js-clip">复制链接</a>
                                                @elseif (in_array($item['id'], $courseGoods_ids))
                                                    <a href="javascript:;"
                                                       data-clipboard-text="{{yzAppFullUrl('member/coursedetail/'.$item['id'])}}"
                                                       data-url="{{yzAppFullUrl('member/coursedetail/'.$item['id'])}}"
                                                       title="复制连接" class="js-clip">复制链接</a>
                                                @else
                                                    <a href="javascript:;"
                                                       data-clipboard-text="{{yzAppFullUrl('goods/'.$item['id'])}}"
                                                       data-url="{{yzAppFullUrl('goods/'.$item['id'])}}"
                                                       title="复制连接" class="js-clip">复制链接</a>

                                                @endif
                                            </div>
                                            <div>
                                                <label data='{{$item['is_new']}}'
                                                       class='btn btn-sm @if($item['is_new']==1) btn-info @else btn-default @endif'
                                                       onclick="setProperty(this,{{$item['id']}},'is_new')">新品</label>

                                                <label data='{{$item['is_hot']}}'
                                                       class='btn btn-sm @if($item['is_hot']==1) btn-info @else btn-default @endif'
                                                       onclick="setProperty(this,{{$item['id']}},'is_hot')">热卖</label>

                                                <label data='{{$item['is_recommand']}}'
                                                       class='btn btn-sm @if($item['is_recommand']==1) btn-info @else btn-default @endif'
                                                       onclick="setProperty(this,{{$item['id']}},'is_recommand')">推荐</label>

                                                <label data='{{$item['is_discount']}}'
                                                       class='btn btn-sm @if($item['is_discount']==1) btn-info @else btn-default @endif'
                                                       onclick="setProperty(this,{{$item['id']}},'is_discount')">促销</label>
                                            </div>
                                            <!-- yitian_add::商品链接二维码 2017-02-07 qq:751818588 -->
                                        </td>
                                    </tr>

                                @endforeach
                            @show
                            @section('release_gods')
                                <!--	<tr>
								<td colspan='10'>
                                    <a class='btn btn-primary' href="{{yzWebUrl('goods.goods.create')}}"><i class='fa fa-plus'></i> 发布{{$lang['good']}}</a>
                                    <input name="submit" type="submit" class="btn btn-default" value="提交排序">
                                    <input type="hidden" name="token" value="{{$var['token']}}" />
								</td>
							</tr>-->
                                @show
                                </tr>
                            </tbody>
                        </table>
                        @section('batch_bottom')
                            <label class="btn btn-success checkall">全选</label>
                            <label class="btn btn-info batchenable">批量上架</label>
                            <label class="btn batchdisable">批量下架</label>
                            <label class="btn btn-danger batchdel">批量删除</label>
                    @show

                    {!!$pager!!}
                    <!--分页-->

                    </div>
                    <div style="margin-left:13px;margin-top:8px">
                        @section('add_goods')
                            <a class='btn btn-success '
                               href="@if($add_url){{yzWebUrl($add_url)}}@else{{yzWebUrl('goods.goods.create')}}@endif"><i
                                        class='fa fa-plus'></i> 发布{{$lang['good']}}</a>
                        @show
                        @section('sub_sort')
                            <input name="submit" type="submit" class="btn btn-default back" value="提交排序">
                        @show
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
</div>
<script>
    $(function () {
        $(".checkall").click(function () {
            //全选
            if ($(this).html() == '全选') {
                $(this).html('全不选');
                $('[name=check1]:checkbox').prop('checked', true);
            } else {
                $(this).html('全选');
                $('[name=check1]:checkbox').prop('checked', false);
            }
        });
        $(".checkrev").click(function () {
            //反选
            $('[name=check1]:checkbox').each(function () {
                this.checked = !this.checked;
            });
        });

        var arr = new Array();
        var url = "{!! yzWebUrl('goods.goods.batchSetProperty') !!}"

        $(".batchenable").click(function () {
            $(this).html('上架中...');
            $("[name=check1]:checkbox:checked").each(function (i) {
                arr[i] = $(this).val();
            });
            $.post(url, {ids: arr, data: 1}
                , function (d) {
                    if (d.result) {
                        $(".batchenable").html('上架成功');
                        setTimeout(location.reload(), 3000);
                    }
                }, "json"
            );
        });

        {{--$(".import").click(function () {--}}
        {{--    alert({{yzWebUrl('goods.goods.import')}});--}}
        {{--    location.href="{{yzWebUrl('goods.goods.import')}}";--}}
        {{--});--}}

        $(".batchdisable").click(function () {
            $(this).html('下架中...');
            $("[name=check1]:checkbox:checked").each(function (i) {
                arr[i] = $(this).val();
            });
            $.post(url, {ids: arr, data: 0}
                , function (d) {
                    if (d.result) {
                        $(".batchdisable").html('下架成功');
                        setTimeout(location.reload(), 3000);
                    }
                }, "json"
            );
        });

        $(".batchdel").click(function () {
            $(this).html('删除中...');
            $("input[type='checkbox']:checked").each(function (i) {
                arr[i] = $(this).val();
            });
            $.post("{!! yzWebUrl('goods.goods.batchDestroy') !!}", {ids: arr}
                , function (d) {
                    if (d.result) {
                        $(".batchdel").html('删除成功');
                        setTimeout(location.reload(), 3000);
                    }
                }, "json"
            );
        })

    });
</script>

<script type="text/javascript">
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
                objData = JSON.parse($data)

                if (objData.status == -1) {
                    alert(objData.msg);
                }
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
                if (type == 'title' && val.length >= '40') {
                    Tip.show(group.find(':input'), '名称不能大于40字符!');
                    return;
                }
                group.prev().show().find('span').html(val);
                group.hide();
                fastChange(goodsid, type, val);
            });
        })
    })

    @section('supplier_js')
    function setPutaway(obj, id, type) {
        $(obj).html($(obj).html() + "...");
        $.post("{!! yzWebUrl('goods.goods.setPutaway') !!}", {id: id, type: type, data: obj.getAttribute("data")}
            , function (d) {
                console.log(d);
                $(obj).html($(obj).html().replace("...", ""));
                $(obj).html(d.data == '1' ? '{{$lang['putaway']}}' : '{{$lang['soldout']}}');
                $(obj).attr("data", d.data);
                if (d.result == 1) {
                    $(obj).toggleClass("btn-info btn-default");
                }
            }
            , "json"
        );
    }
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
                    $(obj).toggleClass("btn-info btn-default");
                }
            }
            , "json"
        );
    }
    @show

    require(['select2'], function () {
        //  $('#brand').select2();
    })
</script>

@endsection('content')