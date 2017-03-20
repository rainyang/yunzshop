@extends('layouts.base')

@section('content')
<div class="w1200 m0a">
<div class="rightlist">
<!-- 新增加右侧顶部三级菜单 -->
    @include('setting.shop.tabs')
<!-- 新增加右侧顶部三级菜单结束 -->

    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" >
        <div class="panel panel-default">
            <div class='panel-body'>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员默认头像</label>
                    <div class="col-sm-9 col-xs-12">
                        {!! app\common\helpers\ImageHelper::tplFormFieldImage('member[headimg]', $set['headimg'])!!}
                        <span class='help-block'>会员默认头像（会员自定义头像>微信头像>商城默认头像）</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">默认会员级别名称</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="member[level_name]" class="form-control" value="{{ empty($set['level_name'])?'普通会员':$set['level_name']}}"  />
                        <span class="help-block">会员默认等级名称，不填写默认“普通会员”</span>
                    </div>
                </div>
	
				
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员等级说明连接</label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="input-group ">
                            <input class="form-control" type="text" data-id="PAL-00010" placeholder="请填写指向的链接 (请以http://开头, 不填则不显示)" value="{{$set['level_url']}}" name="member[level_url]">
                            <span class="input-group-btn">
                                <button class="btn btn-default nav-link" type="button" data-id="PAL-00010" >选择链接</button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员等级升级依据</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class="radio radio-inline">
                              <input type="radio" name="member[level_type]" value="0" @if (empty($set['level_type'])) checked @endif/> 已完成的订单金额
                        </label>
                        <label class="radio radio-inline">
                              <input type="radio" name="member[level_type]" value="1" @if ($set['level_type'] == 1) checked @endif/> 已完成的订单数量
                        </label>
                        <label class="radio radio-inline">
                              <input type="radio" name="member[level_type]" value="2" @if ($set['level_type'] == 2) checked @endif/> 购买指定商品
                        </label>

                        <span class="help-block">默认为完成订单金额</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员等级时间限制</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class='radio-inline'><input type='radio' name='member[term]' value='0' @if ($set['term'] == 0) checked @endif /> 关闭</label>
                        <label class='radio-inline'><input type='radio' name='member[term]' value='1' @if ($set['term'] == 1) checked @endif/> 开启</label>
                    </div>
                </div>

                <div class="form-group"  >
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员等级到期时间</label>
                    <div class="col-sm-8">
                        <div class="input-group col-xs-2">
                            <input type="text" name="member[term_time]" class="form-control" value="{{ $set['term_time'] }}"  />
                            <div class="input-group-addon ">
                                <select name="member[term_unit]">
                                    <option value ="1" @if ($set['term_unit'] ==1) selected @endif>--天--</option>
                                    <option value ="2" @if ($set['term_unit'] ==2) selected @endif>--周--</option>
                                    <option value ="3" @if ($set['term_unit'] ==3) selected @endif>--月--</option>
                                    <option value ="4" @if ($set['term_unit'] ==4) selected @endif>--年--</option>
                                </select>
                            </div>
                        </div>
                        <span class='help-block'>会员等级到期时间</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">强制绑定手机</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class="radio radio-inline">
                              <input type="radio" name="member[is_bind_mobile]" value="0" @if (empty($set['is_bind_mobile'])) checked @endif/> 否
                        </label>
                       <label class="radio radio-inline">
                              <input type="radio" name="member[is_bind_mobile]" value="1" @if ($set['is_bind_mobile'] == 1) checked @endif/> 是
                        </label>
                        <span class="help-block">进入商城是否强制绑定手机号</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员中心显示推荐人</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class="radio radio-inline">
                              <input type="radio" name="member[is_referrer]" value="0" @if (empty($set['is_referrer'])) checked @endif/> 否
                        </label>
                       <label class="radio radio-inline">
                              <input type="radio" name="member[is_referrer]" value="1" @if ($set['is_referrer'] == 1) checked @endif/> 是
                        </label>
                        <span class="help-block">会员中心显示推荐人</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">绑定手机</label>
                    <div class="col-sm-9 col-xs-12">
                        <a class="btn btn-warning" href="{php echo $this->createWebUrl('member/query', array('op' => 'delbindmobile'))}" data-original-title="" title="">清除绑定记录</a>
                        <span class="help-block">公众号被封后可使用此功能清除手机号绑定记录，让会员重新绑定找回被封公众号会员信息</span>
                    </div>
                </div>

                       <div class="form-group"></div>
            <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                            <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1"  />
                     </div>
            </div>
                       
            </div>
        </div>     
    </form>
</div>
</div>
{{--@include('setting.mylink')--}}
@endsection('content')
