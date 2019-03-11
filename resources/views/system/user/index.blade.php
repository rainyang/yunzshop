@extends('layouts.base')

@section('content')
@section('title', trans('用户列表'))
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
                                    <!--	<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"></label>-->
                                    <div class="">
                                        <input class="form-control" placeholder="请输入用户名/手机号" name="search[keyword]" id=""
                                               type="text" value="{{$requestSearch['keyword']}}" ／>
                                    </div>
                                </div>


                                <div class="form-group col-xs-12 col-sm-12 col-md-4 col-lg">

                                    <div class="time">

                                        <select name='search[searchtime]' class='form-control'>
                                            <option value='0'
                                                    @if($request['search']['searchtime']=='0')
                                                    selected
                                                    @endif>注册时间不限
                                            </option>
                                            <option value='1'
                                                    @if($request['search']['searchtime']=='1')
                                                    selected
                                                    @endif>搜索注册时间
                                            </option>
                                        </select>
                                    </div>
                                    <div class="search-select">
                                        {!! app\common\helpers\DateRange::tplFormFieldDateRange('search[times]', [
                                        'starttime'=>date('Y-m-d H:i', $starttime),
                                        'endtime'=>date('Y-m-d H:i',$endtime),
                                        'start'=>0,
                                        'end'=>0
                                        ], true) !!}
                                    </div>
                                </div>

                                <div class="form-group col-xs-12 col-sm-8 col-lg-2">
                                    <!--		<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">状态</label>-->
                                    <div class="">
                                        <select name="search[status]" class='form-control'>
                                            <option value="">状态不限</option>
                                            <option value="0"
                                                    @if($requestSearch['status'] == '1') selected @endif>有效</option>
                                            <option value="1"
                                                    @if($requestSearch['status'] == '0') selected @endif>已过期</option>
                                            <option value="2"
                                                    @if($requestSearch['status'] == '0') selected @endif>已禁用</option>
                                        </select>
                                    </div>
                                </div>

                            @show
                            <div class="form-group col-xs-8 col-sm-8 col-lg-1">
                                <!--	<label class="col-sm-9"></label>-->

                                <button class="btn btn-block btn-success"><i class="fa fa-search"></i> 搜索</button>
                            </div>

                            <a class="btn btn-info " href="/index.php/admin/user/add"><i class="fa fa-plus"></i> 添加用户</a>

                        </form>
                    </div>
                </div>
                <form id="goods-list" action="" method="post">
                    <div class="panel panel-default">
                        <div class="panel-body table-responsive">
                            <table class="table table-hover">
                                <thead class="navbar-inner">
                                <tr>
                                    <th width="22%">ID</th>
                                    <th width="22%">用户名</th>
                                    <th width="22%">注册时间</th>
                                    <th width="22%">到期时间</th>
                                    <th width="22%">状态</th>
                                    <th width="35%">操作</th>
                                </tr>
                                </thead>
                                <tbody>

                                @section('foreach')
                                @foreach($users as $item)
                                    <tr>
                                        <td>{{$item['id']}}</td>
                                        <td>{{$item['username']}}</td>
                                        <td>{{$item['create_at']}}</td>
                                        <td>{{$item['effective_time']}}</td>
                                        <td>
                                            @if($item['status']=='0') 有效
                                                @elseif($item['status'] == '1') 已过期
                                                @elseif($item['status'] == '2') 已禁用
                                            @endif
                                        </td>
                                        <td>
                                            <a class="btn btn-info" href="/index.php/admin/user/application_list"><i class="fa fa-plus"></i> 平台列表</a>
                                            <a class="btn btn-danger" href="/index.php/admin/user/change?id={{$item['id']}}"><i class="fa fa-plus"></i> 修改密码</a>
                                            @if($item['status']=='0')
                                                <a class="btn batchdisable" href="/index.php/admin/user/status?status=2&id={{$item['id']}}"><i class="fa fa-plus"></i> 禁用</a>
                                            @elseif($item['status']=='2')
                                                <a class="btn btn-info" href="/index.php/admin/user/status?status=0&id={{$item['id']}}"><i class="fa fa-plus"></i> 启用</a>
                                            @elseif($item['status']=='1')
                                                <a class="btn batchdisable"><i class="fa fa-plus"></i>已过期</a>
                                            @endif
                                                {{ csrf_field() }}
                                            <a class="btn btn-info " href="/index.php/admin/user/edit?id={{$item['id']}}"><i class="fa fa-plus"></i> 编辑</a>
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

                            {!!$pager!!}
                                    <!--分页-->
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
<script>
    $(function(){
        $(".checkall").click(function(){
            //全选
            if($(this).html() == '全选') {
                $(this).html('全不选');
                $('[name=check1]:checkbox').prop('checked',true);
            } else {
                $(this).html('全选');
                $('[name=check1]:checkbox').prop('checked',false);
            }
        });
        $(".checkrev").click(function(){
            //反选
            $('[name=check1]:checkbox').each(function(){
                this.checked=!this.checked;
            });
        });

        var arr = new Array();
        var url = "{!! yzWebUrl('goods.goods.batchSetProperty') !!}"

        $(".batchenable").click(function () {
            $(this).html('上架中...');
            $("[name=check1]:checkbox:checked").each(function(i){
                arr[i] = $(this).val();
            });
            $.post(url, {ids: arr, data: 1}
                , function (d) {
                    if (d.result) {
                        $(".batchenable").html('上架成功');
                        setTimeout(location.reload(), 3000);
                    }
                } , "json"
            );
        });
        // $(".batchdisable").click(function () {
        //     $(this).html('下架中...');
        //     $("[name=check1]:checkbox:checked").each(function(i){
        //         arr[i] = $(this).val();
        //     });
        //     $.post(url, {ids: arr, data: 0}
        //         , function (d) {
        //             if (d.result) {
        //                 $(".batchdisable").html('下架成功');
        //                 setTimeout(location.reload(), 3000);
        //             }
        //         } , "json"
        //     );
        // });

        $(".batchdel").click(function () {
            $(this).html('删除中...');
            $("input[type='checkbox']:checked").each(function(i){
                arr[i] = $(this).val();
            });
            $.post("{!! yzWebUrl('goods.goods.batchDestroy') !!}", {ids: arr}
                , function (d) {
                    if (d.result) {
                        $(".batchdel").html('删除成功');
                        setTimeout(location.reload(), 3000);
                    }
                } , "json"
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
        @show
        require(['select2'], function () {
          //  $('#brand').select2();
        })
    </script>

@endsection('content')