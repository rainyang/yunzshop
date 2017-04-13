@extends('layouts.base')
@section('title', trans('插件管理'))
@section('content')
    <div class="w1200 m0a">
        <script language="javascript" src="../addons/sz_yi/static/js/dist/nestable/jquery.nestable.js"></script>
        <link rel="stylesheet" type="text/css" href="../addons/sz_yi/static/js/dist/nestable/nestable.css"/>

        <!-- 新增加右侧顶部三级菜单 -->
        <section class="content-header">
            <h1>
                {{ trans('插件管理') }}
            </h1>
        </section>

        <div class='panel panel-default'>
            <div class='panel-body'>
                <table class="table">
                    <thead>
                    <tr>
                        <th style='width:10%;'>版本</th>
                        <th style='width:10%;'>名称</th>
                        <th style='width:50%;'>描述</th>
                        <th style='width:10%;'>状态</th>
                        <th style='width:20%;'>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($installed as $plugin)
                        <tr>
                            <td>[{{$plugin->version}}]</td>
                            <td class="tip" title="{{$plugin->title}}">
                                {{$plugin->title}}
                            </td>
                            <td style="color:#f39c12" class="tip" title="{{$plugin->description}}">
                                {{$plugin->description}}
                            </td>
                            <td>@if($plugin->isEnabled())
                                    启用
                                @else
                                    禁用
                                @endif
                            </td>
                            <td>
                                <a class='btn btn-default btn-sm'
                                   href="{{yzWebUrl('plugins.manage', ['name'=>$plugin['name'],'action'=>($plugin->isEnabled() ? 'disable' : 'enable')])}}"
                                   title='{{($plugin->isEnabled() ? '禁用' : '启用')}}'><i class="fa fa-edit"></i>
                                </a>
                                <a class='btn btn-default btn-sm'
                                   href="{{yzWebUrl('plugins.manage', ['name'=>$plugin['name'],'action'=>'delete'])}}"
                                   title='删除' onclick="return confirm('确认删除此插件吗？');return false;">
                                    <i class="fa fa-remove"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

@endsection