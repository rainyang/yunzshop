@extends('layouts.base')

@section('title', trans('系统升级'))

@section('css')
    <link href="{{static_url('resource/css/upgrade.css')}}" rel="stylesheet">
@endsection

@section('content')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper" style="margin-left: 0px">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1 style="display: inline-block; font-size: 16px">
                系统升级
            </h1>
        </section>
        <!-- Main content -->
        <section class="content">

            <div class="modal" id="showMiddleModal"
                 data-backdrop="false" data-keyboard="false"
                 role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <h5 class="center-block">正在加载中...</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box">

                <div class="box-body">
                    <div class="alert"></div>

                    <div class="row">
                        <div class="col-md-6">
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-6 sidebar scrollbox  left-menu-border content-height-scroll" >
                            <ul class="nav nav-sidebar" id="upgrad_file">
                            </ul>
                        </div>
                        <div class="col-md-6 main content-height-scroll">
                            @foreach($data as $k => $item)
                                <div id="detail{{$k}}" data-status="{{$item['versionStatus']}}" data-size="{{$item['size']}}" data-name="{{$item['name']}}" data-index="{{$k}}" class="display" style="@if($k==0) display: block; @else display: none;@endif">
                                    <div class="form-group button_title">
                                        <h4 style="text-align: center">{{$item['title']}}</h4>
                                        <p style="text-align: right; font-size: 12px"> ——— {{$item['author']}}</p>
                                    </div>

                                    <div class="form-group">
                                        @if($item['versionStatus'] == 'new')
                                            <button onclick="isUpdated('{{$item['latestVersion']}}')" class="btn btn-info" style="height: 32px">
                                                <i class="fa fa-download"> </i> <label> 升级 </label>
                                            </button>
                                        @elseif($item['versionStatus'] == 'installed')
                                            <button onclick="" disabled class="btn btn-success" style="height: 32px">
                                                <i class="fa fa-download"> </i> <label> 安装 </label>
                                            </button>
                                        @elseif($item['versionStatus'] == 'preview')
                                            <button onclick="" class="btn btn-success" style="height: 32px">
                                                <i class="fa fa-download"> </i> <label> 预览 </label>
                                            </button>
                                        @else
                                            <button onclick="isDownload()" class="btn btn-success" style="height: 32px">
                                                <i class="fa fa-download"></i> <label> 安装 </label>
                                            </button>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label class="font-description"> 插件详情：</label><br/>
                                        <div class="interval">{{$item['description']}}</div>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-description"> 版本号：</label>
                                        <span class="interval" id="versionNumber{{$k}}">{{$item['version']}}</span>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-description"> 版本说明：</label><br/>
                                        <div class="interval" id="versionDetail{{$k}}">
                                            @foreach($item['versionList'] as $ver)
                                                @if($ver['version'] == $item['version'])
                                                    <span data-version="{{$item['version']}}">{!! $ver['description'] !!}</span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-description"> 大小：</label>
                                        <span class="interval" id="size{{$k}}">{{$item['size']}}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </section><!-- /.content -->
    </div><!-- /.content-wrapper -->

    <script>
        $(function() {
            $.ajax({
                url: '{!! yzWebUrl('update.check') !!}',
                type: 'get',
                dataType: 'json',
                beforeSend: function(){
                    var html = '<li><br/>正在检查更新文件</li>';
                    $("#upgrad_file").html(html);
                }
            }).done(function (ret) {
                console.log("Downloading finished");
                console.log(ret);

                if (1 == ret.result) {
                    var html = "";

                    if(ret.filecount<=0 && !ret.upgrade){
                        html+="恭喜您，您现在是最新版本！"
                    }
                    else{
                        if(ret.filecount > 0){
                            html+="<br/><b style='color:red'>更新之前请注意数据备份!</b><br/><br/>";
                            html += "更新文件(选中则不更新文件):<br>";
                            var data = ret.files;

                            for(var o in data){
                                html += '<li><label class="checkbox-inline"><input type="checkbox" value="'+data[o].path+'" name="files"> ' + data[o].path+"</label></li>";
                            }
                        }
                    }

                    $("#upgrad_file").html(html);
                }

            }).fail(function (message) {
                console.log('update.start-download:', message)
            });

            $("#updateVersion").click(function () {
                var $btn = $(this);
                console.log($btn);
                $btn.button('loading');
                $.ajax({
                    url: '{!! yzWebUrl('update.start-download') !!}',
                    type: 'POST',
                    dataType: 'json'
                })
                    .done(function (json) {

                        console.log("Downloading finished");
                        console.log(json);
                        $btn.button('reset');
                    })
                    .fail(function (message) {
                        console.log('update.start-download:', message)
                    });

            });
        });
    </script>
@endsection

