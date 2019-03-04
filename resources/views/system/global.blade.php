<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="panel-default panel-center">
        @if(!empty($upload_max_filesize) && !empty($post_max_size))
            <div class="form-group">
                <label class="col-sm-2 control-label">PHP 环境说明</label>
                <div class="col-sm-10">
                    <div class="form-control-static">1. 当前 PHP 环境允许最大单个上传文件大小为: {{$upload_max_filesize}}</div>
                    <div class="form-control-static">2. 当前 PHP 环境允许最大 POST 表单大小为: {{$post_max_size}}</div>
                </div>
            </div>
        @endif

        <div>附件缩略设置</div>
        <hr>
        <div class="form-group">
            <label class="col-sm-2 control-label">缩略设置</label>
            <div class="col-sm-10">
                <input type="radio" name="upload[thumb]" id="radio_image_thumb_0" value="0" @if(!$upload->thumb || $upload->thumb=='0') checked @endif />
                <label for="radio_image_thumb_0" class="radio-inline">
                    不启用缩略
                </label>
                <input type="radio" name="upload[thumb]" id="radio_image_thumb_1" value="1" @if($upload->thumb=='1') checked @endif />
                <label for="radio_image_thumb_1" class="radio-inline">
                    启用缩略
                </label>
                <div class="help-block"></div>
            </div>
        </div>
        <div class="form-group upload-image-thumb-width-height" @if(empty($upload->thumb)) style="display:none;" @endif >
            <label class="col-sm-2 control-label"></label>
            <div class="col-sm-3">
                <div class="input-group">
                    <span class="input-group-addon">宽</span>
                    <input name="upload[thumb_width]" value="{{$upload->thumb_width}}" type="text" class="form-control">
                    <span class="input-group-addon">px</span>
                </div>
                <span class="help-block">缩略后图片 <b>最大宽度</b></span>
            </div>
        </div>

        <div>图片附件设置</div>
        <hr>
        <div class="form-group">
            <label class="col-sm-2 control-label">支持文件后缀</label>
            <div class="col-sm-5">
                <textarea name="upload[image_extentions]" class="form-control" rows="4">{{$upload->image_extentions}}</textarea>
                <div class="help-block">填写图片后缀名称, 如: jpg, 换行输入, 一行一个后缀.</div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">支持文件大小</label>
            <div class="col-sm-5">
                <div class="input-group">
                    <input name="upload[image_limit]" value="{{$upload->image_limit}}" type="text" class="form-control">
                    <span class="input-group-addon">KB</span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">图片压缩</label>
            <div class="col-sm-5">
                <div class="input-group">
                    <input name="upload[zip_percentage]" value="{{$upload->zip_percentage}}" type="number" step="1" min="10"
                           max="100" class="form-control" placeholder="请输入原图的压缩比(100%表示不压缩)">
                    <span class="input-group-addon">%</span>

                </div>
                <div class="help-block">100不压缩 值越大越清晰</div>
            </div>
        </div>

        <div>音频视频附件设置</div>
        <hr>
        <div class="form-group">
            <label class="col-sm-2 control-label">支持文件后缀</label>
            <div class="col-sm-5">
                <textarea name="upload[audio_extentions]" class="form-control" rows="4">{{$upload->audio_extentions}}</textarea>
                <div class="help-block">填写音频视频后缀名称, 如: mp3, 换行输入, 一行一个后缀.</div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">支持文件大小</label>
            <div class="col-sm-5">
                <div class="input-group">
                    <input name="upload[audio_limit]" value="{{$upload->audio_limit}}" type="text" class="form-control">
                    <span class="input-group-addon">KB</span>
                </div>
            </div>
        </div>

    </div>
    <div class="form-group col-sm-12 mrleft40 border-t">
        <input style="float:right;" type="submit" name="submit" value="提交" class="btn btn-success"
               onclick="return formcheck()"/>
    </div>
</form>

<script type="text/javascript">
    $(function(){
        $('input[name="upload[image][thumb]"]').click(function(){
            if($(this).val() == 1){
                $('.upload-image-thumb-width-height').css('display', '');
            } else {
                $('.upload-image-thumb-width-height').css('display', 'none');
            }
        });
    });
</script>