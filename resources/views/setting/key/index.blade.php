@extends('layouts.base')

@section('content')

    <script type="text/javascript">
        function formcheck(event) {

            if ($(':input[name="upgrade[key]"]').val() == '' || $(':input[name="upgrade[secret]"]').val() == '') {
                if($(':input[name="upgrade[key]"]').val() == '')
                    Tip.focus(':input[name="upgrade[key]"]', 'Key 不能为空');
                else
                    Tip.focus(':input[name="upgrade[secret]"]', '密钥不能为空')
                return false;
            }
            return true
        }
    </script>
<div class="w1200 m0a">
<div class="rightlist">
<!-- 新增加右侧顶部三级菜单 -->
    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">密钥填写</a></li>
        </ul>
    </div>
    <div class="form-group message-box" style="display: none">
        <div class="span4">
            <div class="alert alert-block">
                <a class="close" data-dismiss="alert">×</a>
                <span id="message"></span>
            </div>
        </div>
    </div>
    <!-- 新增加右侧顶部三级菜单结束 -->
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" >
        <div class="panel panel-default">
            <div class='panel-body'>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">Key</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="upgrade[key]" class="form-control" value="{{ $set['key'] }}" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">密钥</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="upgrade[secret]" class="form-control" value="{{ $set['secret'] }}" />
                    </div>
                </div>
                
            <div class="form-group"></div>
            <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                        @if(!$set['secret'] || !$set['key'])
                        <input type="hidden" name="type" value="create" />
                        <input type="submit" name="submit" value="注册站点" class="btn btn-success " onclick="return formcheck(this)" />
                        @else
                            <input type="hidden" name="type" value="cancel" />
                            <input type="submit" name="submit" value="取消站点" class="btn btn-success " onclick="return formcheck(this)" />
                        @endif
                    </div>
            </div>

            </div>
        </div>     
    </form>
</div>
</div>
@endsection
