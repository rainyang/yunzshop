@extends('layouts.base')
@section('title', trans('插件管理'))
@section('content')
    <div class="w1200 m0a">
        <script language="javascript" src="{{static_url('js/dist/nestable/jquery.nestable.js')}}"></script>
        <link rel="stylesheet" type="text/css" href="{{static_url('js/dist/nestable/nestable.css')}}"/>

        <!-- 新增加右侧顶部三级菜单 -->
        <section class="content-header">
            <h3 style="display: inline-block;    padding-left: 10px;">
                {{ trans('插件管理') }}
            </h3>
            <a href="{{yzWebUrl('plugin.plugins-market.Controllers.market.show')}}" class="btn btn-success" style="font-size: 13px;float: right;margin-top: 20px;">插件安装/升级</a>
        </section>

        <div style="color:#ff2620">
            （更新插件后，请在插件管理页面，将已更新了的插件禁用后再启用）
        </div>
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
                                   title='{{($plugin->isEnabled() ? '禁用' : '启用')}}'>
                                    @if($plugin->isEnabled())
                                        <i class="fa fa-power-off"></i>
                                    @else
                                        <i class="fa fa-check-circle-o"></i>
                                    @endif

                                </a>
                                {{--<a class='btn btn-default btn-sm'
                                   href="{{yzWebUrl('plugins.manage', ['name'=>$plugin['name'],'action'=>'delete'])}}"
                                   title='删除' onclick="return confirm('确认删除此插件吗？');return false;">
                                    <i class="fa fa-remove"></i>
                                </a>--}}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

@endsection