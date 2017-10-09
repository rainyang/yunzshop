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

                                <div id="upgrade" class="display" style="display: none;">
                                    <div class="form-group button_title"></div>

                                    <div class="form-group">
                                        <button id="upgradebtn" class="btn btn-success" style="height: 32px">
                                            <i class="fa fa-download"></i> <label> 立即更新 </label>
                                        </button>
                                        <span id="process"></span>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-description"> 最新版本号：</label>
                                        <span class="interval" id="versionNumber" style="color: #ff0d0d">00</span>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-description"> 版本说明：</label><br/>
                                        <div class="interval" id="versionDetail">

                                        </div>
                                    </div>

                                </div>

                        </div>
                    </div>
                </div>
            </div>

        </section><!-- /.content -->
    </div><!-- /.content-wrapper -->

    <script>
        $(function() {
            $.ajax({
                url: '{!! yzWebUrl('update.verifyheck') !!}',
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
                        html+="<li><br/>当前版本：<span style='color: #dd4b39'>" + ret.version +"</span></li>"
                        html+="<li><br/>恭喜您，您现在是最新版本！</li>"
                    }
                    else{
                        if(ret.filecount > 0){
                            html+="<br/><b style='color:#dd4b39'>更新之前请注意数据备份!</b><br/><br/>";
                            html += "更新文件(选中则不更新文件):<br>";
                            var data = ret.files;

                            for(var o in data){
                                html += '<li><label class="checkbox-inline"><input type="checkbox" value="'+data[o].path+'" name="files"> ' + data[o].path+"</label></li>";
                            }
                        }
                    }

                    $("#upgrad_file").html(html);

                    if(ret.filecount>0 || ret.upgrade){
                        $('#versionNumber').html(ret.version);
                        $('#versionDetail').html(ret.log);
                        $('#upgrade').show();

                        $("#upgradebtn").unbind('click').click(function(){
                            if($(this).attr('updating')=='1'){
                                return;
                            }

                            $(this).attr('updating',1).val('正在更新中...');
                            upgrade();
                        });
                    }
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

        //文件更新
        function upgrade(){
            var $check_boxes = $('input[name="files"]:checked');
            var fileIds = new Array();

            $check_boxes.each(function(){
                fileIds.push($(this).val());
            });

            $.ajax({
                url: '{!! yzWebUrl('update.fileDownload') !!}',
                data:{'nofiles': fileIds},
                //traditional :true,
                type:'post',
                dataType:'json',
                success:function(ret){
                    if(ret.result==1)      {
                        $('#process').html("已更新 " + ret.success + "个文件 / 共 " + ret.total +  " 个文件！");
                        //循环更新
                        upgrade();
                    }
                    else if(ret.result==2){
                        $('#process').html("更新完成!");
                        location.reload();
                    }
                    else if(ret.result==3){
                        //跳过计数，3是不更新的
                        upgrade();
                    }
                }
            });
        }
    </script>
@endsection

