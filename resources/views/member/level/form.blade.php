@extends('layouts.base')

@section('content')
<link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div class="rightlist">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="right-titpos">
            <ul class="add-snav">
                <li><a href="#">会员等级设置</a></li>
            </ul>
        </div>
        <!-- 新增加右侧顶部三级菜单结束 -->

        <div  id="member-level" class="main">
            @if(!$levelModel->id)
            <form action="{{ yzWebUrl('member.member-level.store') }}" method="post" class="form-horizontal form" enctype="multipart/form-data">
            @else
            <form action="{{ yzWebUrl('member.member-level.update') }}" method="post" class="form-horizontal form" enctype="multipart/form-data">
                <input type="hidden" name="id" value="{{ $levelModel->id }}">
            @endif
                <div class='panel panel-default'>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>等级权重</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="level[level]" class="form-control" value="{{ $levelModele->level }}"/>
                                <span class='help-block'>等级权重，数字越大越高级</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span> 等级名称</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="level[level_name]" class="form-control" value="{{ $levelModel->level_name }}"/>

                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">升级条件</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='input-group'>
                                @if(empty($shopSet['level_type']))
                                    <span class='input-group-addon'>完成订单金额满</span>
                                    <input type="text" name="level[order_money]" class="form-control" value="{{ $levelModel->order_money }}"/>
                                    <span class='input-group-addon'>元</span>
                                @endif

                                @if($shopSet['level_type'] == 1)
                                    <span class='input-group-addon'>完成订单数量满</span>
                                    <input type="text" name="level[order_count]" class="form-control" value="{{ $levelModel->order_count }}"/>
                                    <span class='input-group-addon'>个</span>
                                @endif

                                @if($shopSet['level_type'] == 2)
                                    <div class="col-sm-12">
                                        <input type='hidden' class='form-control' id='goodsid' name='level[goods_id]' value=""/>
                                        <div class='input-group'>
                                            <div class='input-group-addon' style='border:none;background:#fff;'>
                                                <label class="radio-inline" style='margin-top:-3px;'> 购买指定商品</label>
                                            </div>
                                            <input type='text' class='form-control' id='goods' value="此处功能需要完善" readonly/>
                                            <div class="input-group-btn">
                                                <button type="button" onclick="$('#modal-goods').modal()"
                                                        class="btn btn-default">选择商品
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                </div>
                                <span class='help-block'>会员升级条件，不填写默认为不自动升级, 设置<a href="{{ yzWebUrl('setting.shop.member') }}">【会员升级依据】</a> </span>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">折扣</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="level[discount]" class="form-control" value="{{ $levelModel->discout }}"/>
                                <span class='help-block'>请输入0.1~10之间的数字,值为空代表不设置折扣</span>
                            </div>
                        </div>

                        <div class="form-group"></div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="submit" name="submit" value="提交" class="btn btn-primary"/>
                                <input type="hidden" name="token" value="token"/>
                                <input type="button" name="back" onclick='history.back()' style='margin-left:10px;' value="返回列表" class="btn btn-default" />
                            </div>
                        </div>

                    </div>
                </div>

                </form>
        </div>

        <div id="modal-goods" class="modal fade" tabindex="-1">
            <div class="modal-dialog" style='width: 920px;'>
                <div class="modal-content">
                    <div class="modal-header">
                        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                        <h3>选择商品</h3></div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="input-group">
                                <input type="text" class="form-control" name="keyword" value="" id="search-kwd-goods"
                                       placeholder="请输入商品名称"/>
                                <span class='input-group-btn'><button type="button" class="btn btn-default" onclick="search_goods();">搜索</button></span>
                            </div>
                        </div>
                        <div id="module-menus-goods" style="padding-top:5px;"></div>
                    </div>
                    <div class="modal-footer"><a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a></div>
                </div>
            </div>
        </div>
        <script language='javascript'>

            function search_goods() {
                if ($.trim($('#search-kwd-goods').val()) == '') {
                    Tip.focus('#search-kwd-goods', '请输入关键词');
                    return;
                }
                $("#module-goods").html("正在搜索....")
                $.get('{!! yzWebUrl('') !!}', {
                    keyword: $.trim($('#search-kwd-goods').val())
                }, function (dat) {
                    $('#module-menus-goods').html(dat);
                }
            )
                ;
            }

            function select_good(o) {
                $("#goodsid").val(o.id);
                $("#goods").val("[" + o.id + "]" + o.title);
                $("#modal-goods .close").click();
            }


        </script>
    </div>


@endsection