@extends('layouts.base')

@section('content')

    <script type="text/javascript">
        function formcheck() {
            var thumb = /\.(gif|jpg|jpeg|png|GIF|JPG|PNG)$/;


            if ($(':input[name="category[cat_adv_img]"]').val() != '') {
                if (!thumb.test($(':input[name="category[cat_adv_img]"]').val())) {
                    Tip.focus(':input[name="category[cat_adv_img]"]', '图片类型必须是.gif,jpeg,jpg,png中的一种.');
                    return false;
                }
            }

            return true;

        }
    </script>
<div class="w1200 m0a">
<div class="rightlist">
<!-- 新增加右侧顶部三级菜单 -->
    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">商城设置</a></li>
        </ul>
    </div>
    @include('setting.shop.tabs')
<!-- 新增加右侧顶部三级菜单结束 -->
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" >
        <div class="panel panel-default">
            <div class='panel-body'>  
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">分类级别</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class="radio-inline">
                            <input type="radio" name="category[cat_level]" value="2" @if ($set['cat_level'] == 2 || empty($set['cat_level'])) checked @endif /> 二级
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="category[cat_level]" value="3" @if ($set['cat_level'] == 3) checked @endif/> 三级
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">三级分类显示形式</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class="radio-inline">
                            <input type="radio" name="category[cat_show]" value="0" @if (empty($set['cat_show'])) checked @endif /> 单页
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="category[cat_show]" value="1" @if ($set['cat_show'] == 1) checked @endif/> 多页
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">推荐分类广告</label>
                    <div class="col-sm-9 col-xs-12">
                        {!! app\common\helpers\ImageHelper::tplFormFieldImage('category[cat_adv_img]', $set['cat_adv_img'])!!}
                        <span class='help-block'>分类页面中，推荐分类的广告图，建议尺寸640*320</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">推荐分类广告连接</label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="input-group ">
                            <input class="form-control" type="text" data-id="PAL-00010" placeholder="请填写指向的链接 (请以http://开头, 不填则不显示)" value="{{ $set['cat_adv_url'] }}" name="category[cat_adv_url]">
                            <span class="input-group-btn">
                                <button class="btn btn-default nav-link" type="button" data-id="PAL-00010" >选择链接</button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启自定义分类</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class="radio-inline">
                            <input type="radio" name="category[category_diy]" value="1" @if ($set['category_diy'] == 1) checked @endif /> 是
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="category[category_diy]" value="0" @if ($set['category_diy'] == 0 || empty($set['category_diy'])) checked @endif/> 否
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">自定义分类前缀</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="category[category_diy_name]" class="form-control" value="{{ $set['category_diy_name'] }}" />
                         <span class='help-block'>设置分类2的自定义名称功能，例如品牌，用途等。</span>
                    </div>
                </div>
                   <div class="form-group"></div>
            <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                            <input type="submit" name="submit" value="提交" class="btn btn-primary" onclick="return formcheck();" />
                     </div>
            </div>
                   
            </div>
        </div>
    </form>
</div>
</div>
@include('public.admin.mylink')
@endsection
