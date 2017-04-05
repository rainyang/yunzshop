@extends('layouts.base')

@section('content')
    <div class="w1200 m0a">
        <div class="rightlist">
            <form id="setform"  action="" method="post" class="form-horizontal form">
                <input type="hidden" name="route" value="member.memberrelation.save" />
                <div class='panel panel-default'>
                    <div class='panel-heading'>
                        会员关系设置
                    </div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">启用关系链</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio-inline"><input type="radio"  name="setdata[status]" value="1" @if($set['status'] ==1)
                                    checked="checked"
 @endif/> 开启</label>
                                <label class="radio-inline"><input type="radio"  name="setdata[status]" value="0" {if $set['status'] ==0} checked="checked"{/if} /> 关闭</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">获得发展下线权利条件</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio-inline"><input type="radio"  name="setdata[become]" value="0" {if $set['become'] ==0} checked="checked"{/if} /> 无条件</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-6">
                                <label class="radio-inline"><input type="radio"  name="setdata[become]" value="1" {if $set['become'] ==1} checked="checked"{/if} /> 申请</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-6">
                                <div class='input-group' style='border:none;margin-left:-12px;'>
                                    <div class='input-group-addon'  style='border:none;background:#fff;'><label class="radio-inline" style='margin-top:-3px;'><input type="radio"  name="setdata[become]" value="2" {if $set['become'] == 2} checked="checked"{/if} /> 消费达到</label></div>
                                    <input type='text' class='form-control' name='setdata[become_ordercount]' value="{$set['become_ordercount']}" />
                                    <div class='input-group-addon'  style='border:none;background:#fff;'>次</div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-6">
                                <div class='input-group' style='border:none;margin-left:-12px;'>
                                    <div class='input-group-addon'  style='border:none;background:#fff;'><label class="radio-inline" style='margin-top:-3px;'><input type="radio"  name="setdata[become]" value="3" {if $set['become'] == 3} checked="checked"{/if} /> 消费达到</label></div>
                                    <input type='text' class='form-control' name='setdata[become_moneycount]' value="{$set['become_moneycount']}" />
                                    <div class='input-group-addon'  style='border:none;background:#fff;'>元</div>
                                </div>
                            </div>
                        </div>
                        <!-- Author:Y.yang Date:2016-04-08 Content:购买指定商品成为分销商 -->
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-6">
                                <input type='hidden' class='form-control' id='goods_id' name='setdata[become_goods_id]' value="{$set['become_goods_id']}" />
                                <div class='input-group' style='border:none;margin-left:-12px;'>
                                    <div class='input-group-addon'  style='border:none;background:#fff;'><label class="radio-inline" style='margin-top:-3px;'><input type="radio"  name="setdata[become]" value="4" {if $set['become'] == 4} checked="checked"{/if} /> 购买商品</label></div>
                                    <input type='text' class='form-control' id='goods' value="{if !empty($goods)}[{$goods['id']}]{$goods['title']}{/if}" readonly />
                                    <div class="input-group-btn">
                                        <button type="button" onclick="$('#modal-goods').modal()" class="btn btn-default" >选择商品</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END -->
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio-inline"><input type="radio"  name="setdata[become_order]" value="0" {if $set['become_order'] ==0} checked="checked"{/if} /> 付款后</label>
                                <label class="radio-inline"><input type="radio"  name="setdata[become_order]" value="1" {if $set['become_order'] ==1} checked="checked"{/if} /> 完成后</label>
                                <span class="help-block">消费条件统计的方式</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">成为下线条件</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio-inline"><input type="radio"  name="setdata[become_child]" value="0" {if $set['become_child'] ==0} checked="checked"{/if} /> 首次点击分享连接</label>
                                <label class="radio-inline"><input type="radio"  name="setdata[become_child]" value="1" {if $set['become_child'] ==1} checked="checked"{/if} /> 首次下单</label>
                                <label class="radio-inline"><input type="radio"  name="setdata[become_child]" value="2" {if $set['become_child'] ==2} checked="checked"{/if} /> 首次付款</label>
                                <span class='help-block'>首次点击分享连接： 可以自由设置分销商条件</span>
                                <span class='help-block'>首次下单/首次付款： 无条件不可用</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">发展下线必须完善资料</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio-inline"><input type="radio"  name="setdata[become_info]" value="0" {if $set['become_info'] ==0} checked="checked"{/if} /> 需要</label>
                                <label class="radio-inline"><input type="radio"  name="setdata[become_info]" value="1" {if $set['become_info'] ==1} checked="checked"{/if} /> 不需要</label>
                                <span class="help-block">分销商在分销或提现时是否必须完善资料</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">发展下线是否需要审核</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio-inline"><input type="radio"  name="setdata[become_check]" value="0" {if $set['become_check'] ==0} checked="checked"{/if} /> 需要</label>
                                <label class="radio-inline"><input type="radio"  name="setdata[become_check]" value="1" {if $set['become_check'] ==1} checked="checked"{/if} /> 不需要</label>
                                <span class="help-block">以上条件达到后，是否需要审核才能成为真正的分销商</span>
                            </div>
                        </div>
                        <div class="form-group"></div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9">
                                <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" onclick='return formcheck()' />
                                <input type="hidden" name="token" value="{$var['token']}" />
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <!-- Author:Y.yang Date:2016-04-08 Content:购买指定商品成为分销商，（选择商品的输入框和JS） -->
            <div id="modal-goods"  class="modal fade" tabindex="-1">
                <div class="modal-dialog" style='width: 920px;'>
                    <div class="modal-content">
                        <div class="modal-header"><button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button><h3>选择商品</h3></div>
                        <div class="modal-body" >
                            <div class="row">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="keyword" value="" id="search-kwd-goods" placeholder="请输入商品名称" />
                                    <span class='input-group-btn'><button type="button" class="btn btn-default" onclick="search_goods();">搜索</button></span>
                                </div>
                            </div>
                            <div id="module-menus-goods" style="padding-top:5px;"></div>
                        </div>
                        <div class="modal-footer"><a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script language='javascript'>
        function search_goods() {
            if( $.trim($('#search-kwd-goods').val())==''){
                Tip.focus('#search-kwd-goods','请输入关键词');
                return;
            }
            $("#module-goods").html("正在搜索....")
            $.get('{php echo $this->createWebUrl('shop/query')}', {
                keyword: $.trim($('#search-kwd-goods').val())
            }, function(dat){
                $('#module-menus-goods').html(dat);
            });
        }
        function select_good(o) {
            $("#goods_id").val(o.id);
            $("#goods").val( "[" + o.id + "]" + o.title);
            $("#modal-goods .close").click();
        }
        function formcheck(){
            var become_child =$(":radio[name='setdata[become_child]']:checked").val();
            if( become_child=='1'  || become_child=='2' ){
                if( $(":radio[name='setdata[become]']:checked").val() =='0'){
                    alert('成为下线条件选择了首次下单/首次付款，成为分销商条件不能选择无条件!')   ;
                    return false;
                }
            }
            return true;
        }
        function credit_avoid_audit() {
            if ($("input[name='setdata[credit_avoid_audit]']:checked").val() == 1) {
                $(".form-closewithdrawcheck").show();
            } else {
                $(".form-closewithdrawcheck").hide();
            }
        }
    </script>
@endsection