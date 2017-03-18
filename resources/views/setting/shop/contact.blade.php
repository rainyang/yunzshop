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
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">客服电话</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="contact[phone]" class="form-control" value="{{ $set['phone'] }}" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">所在地址</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="contact[address]" class="form-control" value="{{ $set['address'] }}" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">商城简介</label>
                    <div class="col-sm-9 col-xs-12">
                        <textarea name="contact[description]" class="form-control richtext" cols="70">{{ $set['description'] }}</textarea>
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
@endsection
