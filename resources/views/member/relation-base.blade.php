@extends('layouts.base')
@section('title', trans('基础设置'))
@section('content')
    <section class="content">

        <form id="setform" action="" method="post" class="form-horizontal form">
            <div class='panel panel-default'>
                <div class='panel-heading'>
                    {{trans('基础设置')}}
                </div>
                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">Banner</label>
                        <div class="col-sm-9 col-xs-12">
                            {!! app\common\helpers\ImageHelper::tplFormFieldImage('base[banner]', $banner)!!}
                            <span class='help-block'>长方型图片</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">内容</label>
                        <div class="col-sm-9 col-xs-12">
                            {!! tpl_ueditor('base[content]', $content) !!}

                        </div>
                    </div>


                    <div class="form-group"></div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9">
                            <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1"
                                   onclick='return formcheck()'/>
                        </div>
                    </div>

                </div>
        </form>
    </section>@endsection