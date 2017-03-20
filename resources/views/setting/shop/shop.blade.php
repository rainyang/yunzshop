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
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">商城名称</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="shop[name]" class="form-control" value="{{ $set['name']}}" />

                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">商城LOGO</label>
                    <div class="col-sm-9 col-xs-12">
                        {!! app\common\helpers\ImageHelper::tplFormFieldImage('shop[logo]', $set['logo'])!!}
                        <span class='help-block'>正方型图片</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">店招</label>
                    <div class="col-sm-9 col-xs-12">
                        {!! app\common\helpers\ImageHelper::tplFormFieldImage('shop[img]', $set['img']) !!}
                        <span class='help-block'>商城首页店招，建议尺寸640*450</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">商城海报</label>
                    <div class="col-sm-9 col-xs-12">
                        {!! app\common\helpers\ImageHelper::tplFormFieldImage('shop[signimg]', $set['signimg']) !!}
                        <span class='help-block'>推广海报，建议尺寸640*640</span>

                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">美恰客服</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="shop[cservice]" class="form-control" value="{{ $set['cservice']}}" />
                        <span class='help-block'>请到 <a href='https://meiqia.com/' target='_blank'>美恰</a> 获取聊天连接地址<br>如:https://eco-api.meiqia.com/dist/standalone.html?eid=9669

                    </div>
                </div>
                 <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">全局统计代码</label>
                    <div class="col-sm-9 col-xs-12">
			            <textarea name="shop[diycode]" class="form-control richtext" cols="70" rows="5">{{ $set['diycode']}}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">版权信息</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="shop[copyright]" class="form-control" value="{{ $set['copyright']}}" />
                        <span class='help-block'>版权所有 © 后面文字字样</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">余额字样</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="shop[credit]" class="form-control" value="{{ $set['credit']}}" />
                        <span class='help-block'>商城内余额字样的自定义功能</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">积分字样</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="shop[credit1]" class="form-control" value="{{ $set['credit1']}}" />
                        <span class='help-block'>商城内积分字样的自定义功能</span>
                    </div>
                </div>   
  
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
@endsection
