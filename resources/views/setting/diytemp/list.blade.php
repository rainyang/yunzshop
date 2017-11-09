@extends('layouts.base')
@section('title', '自定义模板管理')
@section('content')
    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">自定义模板管理</a></li>
        </ul>
    </div>
<div class="page-header">
    <div class='panel panel-default'>
    <form action="" method="get" class="form-horizontal form-search" role="form1">
        <input type="hidden" name="c" value="site"/>
        <input type="hidden" name="a" value="entry"/>
        <input type="hidden" name="m" value="yun_shop"/>
        <input type="hidden" name="do" value="temp" id="form_do"/>
        <input type="hidden" name="route" value="{!! yzWebUrl('setting.diy-temp.index') !!}" id="route" />
        <div class="page-toolbar">
             <span class=''>
                 <a class='btn btn-primary btn-sm' href="{!! yzWebUrl('setting.wechat-notice.index') !!}"><i class="fa fa-plus"></i> 微信模板管理</a>
                 <a class='btn btn-primary btn-sm' href="{!! yzWebUrl('setting.diy-temp.add') !!}"><i class="fa fa-plus"></i> 添加新模板</a>
             </span>
             <div class="col-sm-6 pull-right">
                 <div class="input-group">
                     <input type="text" class="input-sm form-control" name='keyword' value="{{$kwd}}" placeholder="请输入关键词"> <span class="input-group-btn">
                     <button class="btn btn-primary" type="submit"> 搜索</button> </span>
                 </div>
             </div>
        </div>
    </form>

    <form action="" method="post">
        @if (count($list) > 0)
        <table class="table table-responsive table-hover">
            <thead>
            <tr>
                <th >模板名称</th>
                <th></th>
                <th style="width: 65px;">操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($list as $row)
            <tr>
                <td>{{$row['title']}}</td>
                <td></td>
                <td>
                    <a class='btn btn-op btn-operation' href="{!! yzWebUrl('setting.diy-temp.ediy', ['id' => $row['id']]) !!}" >
                        <span data-toggle="tooltip" data-placement="top" data-original-title="编辑">
                            <i class='icow icow-bianji2'></i>
                        </span>
                    </a>
                    <a class='btn btn-op btn-operation'  data-toggle='ajaxRemove' href="{!! yzWebUrl('setting.diy-temp.del', ['id' => $row['id']]) !!}" data-confirm="确认删除此模板吗？" >
                        <span data-toggle="tooltip" data-placement="top" data-original-title="删除">
                            <i class='icow icow-shanchu1'></i>
                        </span>
                    </a>
                </td>
            </tr>

            @endforeach

            </tbody>
            <tfoot>
            <tr>
                <td colspan="2" style="text-align: right">
                    {!! $pager !!}
                </td>
            </tr>
            </tfoot>
        </table>
        @else
        <div class='panel panel-default'>
            <div class='panel-body' style='text-align: center;padding:30px;'>
                暂时没有任何群发模板!
            </div>
        </div>
        @endif
    </form>
</div>
@endsection