@extends('layouts.admin')

@section('content')
<div class="w1200 m0a">
    <div class="main rightlist">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">菜单栏目</a></li>
            </ul>
        </div>
        <!-- 新增加右侧顶部三级菜单结束 -->

        <form   action="" method="post" class="form-horizontal form" enctype="multipart/form-data" >
            <div class="panel panel-default">
                <div class="panel-body">
                    @if (!empty($menu['id']))
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">上级分类</label>
                        <div class="col-sm-9 col-xs-12 control-label" style="text-align:left;">
                            @if (!empty($menu['id'])) {{$menu['name']}}  @endif
                        </div>
                    </div>
                    @endif

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="menu[sort]" class="form-control" value="0" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>菜单标识</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="menu[item]" class="form-control" value="" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>菜单名称</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="menu[name]" class="form-control" value="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>URL路由或链接地址</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="menu[url]" class="form-control" value="" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>URL参数</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="menu[url_params]" class="form-control" value="" />
                        </div>
                    </div>

                    <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>ICON</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="menu[icon]" class="form-control" value="" />
                            </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">权限控制</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class='radio-inline'>
                                <input type='radio' name='menu[permit]' value='1' checked /> 是
                            </label>
                            <label class='radio-inline'>
                                <input type='radio' name='menu[permit]' value='0'/> 否
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">菜单显示</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class='radio-inline'>
                                <input type='radio' name='menu[menu]' value='1' checked/> 是
                            </label>
                            <label class='radio-inline'>
                                <input type='radio' name='menu[menu]' value='0'/> 否
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">显示状态</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class='radio-inline'>
                                <input type='radio' name='menu[status]' value='1' checked/> 启用
                            </label>
                            <label class='radio-inline'>
                                <input type='radio' name='menu[status]' value='0'/> 禁止
                            </label>
                        </div>
                    </div>

                    <div class="form-group"></div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="hidden" name="menu[parent_id]" class="form-control"
                                   value=" @if (!empty($menu['id'])) {{$menu['id']}} @else 0 @endif" />
                            <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" onclick="return formcheck()" />
                            <input type="button" name="back" onclick="location.href='{{yzWebUrl('menu.index')}}'" style='margin-left:10px;' value="返回列表" class="btn btn-default col-lg-1" />
                        </div>
                    </div>
                </div>
        </form>
    </div>
</div>
@endsection