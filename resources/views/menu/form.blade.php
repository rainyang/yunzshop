@extends('layouts.admin')

@section('content')
    {!! BootForm::open(['model'=>$model,'url'=>yzWebFullUrl(request()->get('route')),'method'=>'POST']) !!}
    {!! $model->id ? BootForm::hidden('id',$model->id) : '' !!}
    <div class="panel panel-default">
        <div class="panel-body">

            {!! BootForm::select('menu[parent_id]','上级',$parentMenu,$model->parent_id) !!}
            {!! BootForm::text('menu[item]','* 标识',$model->item,['help_text'=>'标识唯一也是做为权限判断标识']) !!}
            {!! BootForm::text('menu[name]','* 菜单名称',$model->name) !!}
            {!! BootForm::text('menu[url]','URL路由或链接地址',$model->url,['help_text'=>'填写路由 menu.add 或 http(s)://xxxx']) !!}
            {!! BootForm::text('menu[url_params]','URL参数',$model->url_params) !!}
            {!! BootForm::text('menu[icon]','ICON',$model->icon) !!}
            {!! BootForm::text('menu[sort]','排序',$model->sort) !!}
            {!! BootForm::radios('menu[permit]','权限控制',[1=>'是',0=>'否'],(int)$model->permit,true) !!}
            {!! BootForm::radios('menu[menu]','菜单显示',[1=>'是',0=>'否'],(int)$model->menu,true) !!}
            {!! BootForm::radios('menu[status]','显示状态',[1=>'启用',0=>'禁止'],(int)$model->status,true) !!}

            {!! BootForm::submit('提交') !!}

        </div>
    </div>
   {!! BootForm::close() !!}

@endsection