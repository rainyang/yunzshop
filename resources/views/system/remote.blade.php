<form action="" method="post" class="form-horizontal form">
    {{ csrf_field() }}
    <div class="panel-default panel-center">
        <div class="form-group">
            <div class="col-sm-12">
                <input type="radio" name="type" id="type-0" value="0" onclick="$('.remote-qiniu').hide();$('.remote-alioss').hide();$('.remote-ftp').hide();$('.remote-close').show();$('.remote-cos').hide();" @if (!$remote['type'] || $remote['type'] == '0') checked="checked" @endif>
                <label class="radio-inline" for="type-0">
                    关闭
                </label>

                <input type="radio" name="type" id="type-2" value="2" onclick="$('.remote-alioss').show();$('.remote-ftp').hide();$('.remote-close').hide();$('.remote-cos').hide();" @if (!$remote['type'] && $remote['type'] == '2') checked="checked" @endif>
                <label class="radio-inline" for="type-2">
                    阿里云OSS <span class="label label-success">推荐，快速稳定</span>
                </label>

                <input type="radio" name="type" id="type-4" value="4" onclick="$('.remote-alioss').hide();$('.remote-ftp').hide();$('.remote-close').hide();$('.remote-cos').show();" @if (!$remote['type'] && $remote['type'] == '4') checked="checked" @endif>
                <label class="radio-inline" for="type-4">
                    腾讯云存储 <span class="label label-success">推荐，快速稳定</span>
                </label>

                <span class="help-block"></span>
            </div>
        </div>

        <div class="remote-alioss" @if(empty($remote['type']) || $remote['type'] != '2') style="display:none;" @endif>
        <div class="form-group">
            <label class="col-sm-2 control-label">Access Key ID</label>
            <div class="col-sm-9">
                <input type="text" name="alioss[key]" class="form-control" value="{{$remote['alioss']['key']}}" placeholder="" />
                <span class="help-block">
                        Access Key ID是您访问阿里云API的密钥，具有该账户完全的权限，请您妥善保管。
                    </span>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">Access Key Secret</label>
            <div class="col-sm-9">
                <input type="text" name="alioss[secret]" class="form-control encrypt" value="{{$remote['alioss']['secret']}}" placeholder="" />
                <span class="help-block">
                        Access Key Secret是您访问阿里云API的密钥，具有该账户完全的权限，请您妥善保管。(填写完Access Key ID 和 Access Key Secret 后请选择bucket)
                    </span>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">内网上传</label>
            <div class="col-sm-9">
                <input type="radio" name="alioss[internal]" id="type-12" value="1" @if($remote['alioss']['internal'] == 1) checked @endif>
                <label class="radio-inline" for="type-12">
                    是
                </label>
                <input type="radio" name="alioss[internal]" id="type-13" value="0" @if($remote['alioss']['internal'] != 1) checked @endif>
                <label class="radio-inline" for="type-13">
                    否
                </label>
                <span class="help-block">
                            如果此站点使用的是阿里云ecs服务器，并且服务器与bucket在同一地区（如：同在华北一区），您可以选择通过内网上传的方式上传附件，以加快上传速度、节省带宽。
                        </span>
            </div>
        </div>
        <div class="form-group" id="bucket" >
        <label class="col-sm-2 control-label">Bucket选择</label>
        <div class="col-sm-9">
            <select name="alioss[bucket]" class="form-control">
            </select>
            <span class="help-block">
                        完善Access Key ID和Access Key Secret资料后可以选择存在的Bucket(请保证bucket为可公共读取的)，否则请手动输入。
                    </span>
        </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">自定义URL</label>
            <div class="col-sm-9">
                <input type="text" name="alioss[url]" class="form-control" @if(!strexists($remote['alioss']['url'],'aliyuncs.com') && $_W['setting']['remote']['type'] == 2) value="{{$remote['alioss']['url']}}" @endif  placeholder="默认URL不需要填写"/>
                <span class="help-block">
                            阿里云oss支持用户自定义访问域名，如果自定义了URL则用自定义的URL，如果未自定义，则用系统生成出来的URL。注：自定义url开头加http://或https://结尾不加 ‘/’例：http://abc.com
                        </span>
            </div>
        </div>
        <div class="form-group">
            <div class="">
                <button style="float:right;" name="submit" class="btn btn-primary" value="submit">保存配置</button>
                <button style="float:right;" name="button" type="button" class="btn btn-info js-checkremoteoss" value="check">测试配置（无需保存）</button>
                @if($_W['setting']['remote_complete_info']['type'] && $local_attachment)
                <a style="float:right;" name="button" class="btn btn-info one-key" href="javascript:;">一键上传</a>
                @endif
                <input type="hidden" name="token" value="{$_W['token']}" />
            </div>
        </div>
        </div>

        <div class="remote-cos" @if(!$remote['type'] || $remote['type'] != '4') style="display:none;" @endif >
        <div class="form-group">
            <label class="col-sm-2 control-label">APPID</label>
            <div class="col-sm-9">
                <input type="text" name="cos[appid]" class="form-control" value="{{$remote['cos']['appid']}}" placeholder="" />
                <span class="help-block">APPID 是您项目的唯一ID</span>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">SecretID</label>
            <div class="col-sm-9">
                <input type="text" name="cos[secretid]" class="form-control" value="{{$remote['cos']['secretid']}}" placeholder="" />
                <span class="help-block">SecretID 是您项目的安全秘钥，具有该账户完全的权限，请妥善保管</span>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">SecretKEY</label>
            <div class="col-sm-9">
                <input type="text" name="cos[secretkey]" class="form-control encrypt" value="{{$remote['cos']['secretkey']}}" placeholder="" />
                <span class="help-block">SecretKEY 是您项目的安全秘钥，具有该账户完全的权限，请妥善保管</span>
            </div>
        </div>
        <div class="form-group" id="cosbucket">
            <label class="col-sm-2 control-label">Bucket</label>
            <div class="col-sm-9">
                <input type="text" name="cos[bucket]" class="form-control" value="{{$remote['cos']['bucket']}}" placeholder="" />
                <span class="help-block">请保证bucket为可公共读取的</span>
            </div>
        </div>
        <div class="form-group" id="cos_local">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">bucket所在区域</label>
            <div class="col-sm-9 col-xs-12">
                <select class="form-control" name="cos[local]">
                    <option value="" @if($remote['cos']['local'] == '') selected @endif >无</option>
                    <option value="tj" @if($remote['cos']['local'] == 'tj') selected @endif >华北</option>
                    <option value="sh" @if($remote['cos']['local'] == 'sh') selected @endif >华东</option>
                    <option value="gz" @if($remote['cos']['local'] == 'gz') selected @endif >华南</option>
                    <option value="cd" @if($remote['cos']['local'] == 'cd') selected @endif >西南</option>
                    <option value="bj" @if($remote['cos']['local'] == 'bj') selected @endif >北京</option>
                    <option value="sgp" @if($remote['cos']['local'] == 'sgp') selected @endif >新加坡</option>
                    <option value="hk" @if($remote['cos']['local'] == 'hk') selected @endif >香港</option>
                    <option value="ca" @if($remote['cos']['local'] == 'ca') selected @endif >多伦多</option>
                    <option value="ger" @if($remote['cos']['local'] == 'ger') selected @endif >法兰克福</option>
                </select>
                <span class="help-block">选择bucket对应的区域，如果没有选择无</span>
            </div>
        </div>
        <div class="form-group" >
            <label class="col-sm-2 control-label">Url</label>
            <div class="col-sm-9">
                <input type="text" name="cos[url]" class="form-control" value="{{$remote['cos']['url']}}" placeholder="" />
                <span class="help-block">腾讯云支持用户自定义访问域名。注：url开头加http://或https://结尾不加 ‘/’例：http://abc.com</span>
            </div>
        </div>
        <div class="form-group">
            <div>
                <button style="float:right;" name="submit" class="btn btn-primary" value="submit">保存配置</button>
                <button style="float:right;" name="button" type="button" class="btn btn-info js-checkremotecos" value="check">测试配置（无需保存）</button>
                @if($_W['setting']['remote_complete_info']['type'] && $local_attachment)
                <a style="float:right;" name="button" class="btn btn-info one-key" href="javascript:;">一键上传</a>
                @endif
                <input type="hidden" name="token" value="{$_W['token']}" />
            </div>
        </div>
        </div>
        <div class="remote-close" @if(!$remote['type']) style="display:none;" @endif >
        <div class="form-group">
            <div class="">
                <button style="float:right;" name="submit" class="btn btn-primary" value="submit">保存配置</button>
                <input type="hidden" name="token" value="{$_W['token']}" />
            </div>
        </div>
        </div>
        <div class="modal fade" id="name" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="we7-modal-dialog modal-dialog we7-form">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <div class="modal-title">上传文件</div>
                    </div>
                    <div class="modal-body">
                        正在上传....
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>


<script type="text/javascript">
    $(function() {
        $('.encrypt').val(function() {
            return util.encrypt($(this).val());
        });
    });
    $('.js-checkremoteftp').on('click', function(){
        var ssl =  parseInt($(':radio[name="ftp[ssl]"]:checked').val());
        var pasv = parseInt($(':radio[name="ftp[pasv]"]:checked').val());
        var param = {
            'ssl' : ssl,
            'host' : $.trim($(':text[name="ftp[host]"]').val()),
            'username'  : $.trim($(':text[name="ftp[username]"]').val()),
            'password' : $.trim($(':text[name="ftp[password]"]').val()),
            'pasv' : pasv,
            'dir': $.trim($(':text[name="ftp[dir]"]').val()),
            'url': $.trim($(':text[name="ftp[url]"]').val()),
            'port' : parseInt($(':text[name="ftp[port]"]').val()),
            'overtime' : parseInt($(':text[name="ftp[overtime]"]').val())
        };
        $.post("{php echo url('system/attachment/ftp')}", param, function(data){
            var data = $.parseJSON(data);
            if(data.message.errno == 0) {
                util.message(data.message.message);
                return false;
            }
            if(data.message.errno < 0) {
                util.message(data.message.message);
                return false;
            }
        });
    });
    $('.one-key').click(function() {
        upload_remote();
        return false;
    });
    var upload_remote = function() {
        $('#name').modal('show');
        $.post("{php echo url('system/attachment/upload_remote')}", {}, function(data) {
            var data = $.parseJSON(data);
            if (data.message.errno == 2) {
                upload_remote();
            }
            if (data.message.errno == 0) {
                util.message('上传完毕', location.reload(), 'success');
            }
            if (data.message.errno == 1) {
                util.message(data.message.message, '', 'success');
            }
        });
    }
    $('.js-checkremoteoss').on('click', function(){
        var bucket = $.trim($('select[name="alioss[bucket]"]').val());
        if (bucket == '') {
            bucket = $.trim($(':text[name="alioss[bucket]"]').val());
        }
        var param = {
            'key' : $.trim($(':text[name="alioss[key]"]').val()),
            'secret' : $.trim($(':text[name="alioss[secret]"]').val()),
            'url'  : $.trim($(':text[name="custom[url]"]').val()),
            'bucket' : bucket,
            'internal' : $('[name="alioss[internal]"]:checked').val()
        };
        $.post("{php echo url('system/attachment/oss')}", param, function(data) {
            var data = $.parseJSON(data);
            if(data.message.errno == 0) {
                util.message('配置成功');
                return false;
            }
            if(data.message.errno < 0) {
                util.message(data.message.message);
                return false;
            }
        });
    });
    $('.js-checkremoteqiniu').on('click', function(){
        var key = $.trim($(':text[name="qiniu[accesskey]"]').val());
        if (key == '') {
            util.message('请填写Accesskey');
            return false;
        }
        var secret = $.trim($(':text[name="qiniu[secretkey]"]').val());
        if (secret == '') {
            util.message('请填写Secretkey');
            return false;
        }
        var param = {
            'accesskey' : $.trim($(':text[name="qiniu[accesskey]"]').val()),
            'secretkey' : $.trim($(':text[name="qiniu[secretkey]"]').val()),
            'url'  : $.trim($(':text[name="qiniu[url]"]').val()),
            'bucket' :  $.trim($(':text[name="qiniu[bucket]"]').val())
        };
        $.post("{php echo url('system/attachment/qiniu')}",param, function(data) {
            var data = $.parseJSON(data);
            if(data.message.errno == 0) {
                util.message('配置成功');
                return false;
            }
            if(data.message.errno < 0) {
                util.message(data.message.message);
                return false;
            }
        });
    });
    $('.js-checkremotecos').on('click', function(){
        var appid = $.trim($(':text[name="cos[appid]"]').val());
        if (appid == '') {
            util.message('请填写APPID');
            return false;
        }
        var secretid = $.trim($(':text[name="cos[secretid]"]').val());
        if (secretid == '') {
            util.message('请填写secretid');
            return false;
        }
        var secretkey = $.trim($(':text[name="cos[secretkey]"]').val());
        if (secretkey == '') {
            util.message('请填写Secretkey');
            return false;
        }
        var bucket = $.trim($(':text[name="cos[bucket]"]').val());
        if (bucket == '') {
            util.message('请填写bucket');
            return false;
        }
        var url = $.trim($(':text[name="cos[url]"]').val());
        var local = $('[name="cos[local]"]').val();
        var param = {
            'appid' : appid,
            'secretid' : secretid,
            'secretkey'  : secretkey,
            'bucket' :  bucket,
            'url' : url,
            'local' : local
        };
        $.post("{php echo url('system/attachment/cos')}",param, function(data) {
            var data = $.parseJSON(data);
            if(data.message.errno == 0) {
                util.message('配置成功');
                return false;
            }
            if(data.message.errno < 0) {
                util.message(data.message.message);
                return false;
            }
        });
    });
    var alibucket = '{{$_W['setting']['remote']['alioss']['bucket']}}';
    var buck =  function() {
        var key = $(':text[name="alioss[key]"]').val();
        var secret = $(':text[name="alioss[secret]"]').val();
        if (secret.indexOf('*') > 0) {
            secret = '{{$_W['setting']['remote']['alioss']['secret']}}';
        }
        if (key == '' || secret == '') {
            $('#bucket').hide();
            return false;
        }
        $.post("{php echo url('system/attachment/buckets')}", {'key' : key, 'secret' : secret}, function(data) {
            try {
                var data = $.parseJSON(data);
            } catch (error) {
                util.message('Access Key ID 或 Access Key Secret 填写错误，请重新填写。', '', 'error');
                $('#bucket').hide();
                $('select[name="alioss[bucket]"]').val('');
                return false;
            }

            if (data.message.errno < 0 ) {
                return false;
            } else {
                $('#bucket').show();
                var bucket = $('select[name="alioss[bucket]"]');
                bucket.empty();
                var buckets = eval(data.message.message);
                for (var i in buckets) {
                    var selected = alibucket == buckets[i]['name'] || alibucket ==  buckets[i]['name'] + '@@' + buckets[i]['location'] ? 'selected' : '';
                    bucket.append('<option value="' + buckets[i]['name'] + '@@' + buckets[i]['location'] + '"' + selected + '>'+buckets[i]['loca_name'] + '</option>');
                }
            }
        });
    };
    buck();
    $(':text[name="alioss[secret]"]').blur(function() {buck();});
    $('form').submit(function() {

    });
</script>

