@extends('layouts.base')

@section('content')
@section('title', trans('商品详情'))

<div class="panel panel-info">
    <div class="panel-heading">
        <span>当前位置：</span>
        <a href="#">
            <span>EXCEL导入商品</span>
        </a>
    </div>
</div>
<div class="alert alert-info alert-important">
    <span>功能介绍:</span>
    <span style="padding-left: 60px;">1. 使用excel快速导入商品, 文件格式<b style="color:red;">[xls]</b></span>
    <span style="padding-left: 60px;">2. 一次导入的数据不要太多,大量数据请分批导入,建议在服务器负载低的时候进行</span>
    <br>
    <span>使用方法:</span>
    <span style="padding-left: 60px;">1. 下载Excel模板文件并录入信息</span>
    <span style="padding-left: 60px;">4. 上传Excel导入</span>
    <br>
    <span>格式要求： Excel第一列可以为会员ID或者手机号(根据选择的上传第一列值得类型决定)，第二列必须为充值数量</span>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label must">EXCEL文件</label>
    <div class="col-sm-5 goodsname" style="padding-right:0;">
        <input type="file" onchange="importf(this)" class="form-control"/>
    </div>
</div>
    <script>
        /*
        FileReader共有4种读取方法：
        1.readAsArrayBuffer(file)：将文件读取为ArrayBuffer。
        2.readAsBinaryString(file)：将文件读取为二进制字符串
        3.readAsDataURL(file)：将文件读取为Data URL
        4.readAsText(file, [encoding])：将文件读取为文本，encoding缺省值为'UTF-8'
                     */
        var wb;//读取完成的数据
        var rABS = false; //是否将文件读取为二进制字符串

        function importf(obj) {//导入
            if(!obj.files) {
                return;
            }
            var f = obj.files[0];
            var reader = new FileReader();
            reader.onload = function(e) {
                var data = e.target.result;
                if(rABS) {
                    wb = XLSX.read(btoa(fixdata(data)), {//手动转化
                        type: 'base64'
                    });
                } else {
                    wb = XLSX.read(data, {
                        type: 'binary'
                    });
                }
                //wb.SheetNames[0]是获取Sheets中第一个Sheet的名字
                //wb.Sheets[Sheet名]获取第一个Sheet的数据
                var data = XLSX.utils.sheet_to_row_object_array(wb.Sheets[wb.SheetNames[0]]);
                console.log(data);
                $.ajax({
                    url: "{!! yzWebUrl('goods.goods.a') !!}",
                    type: "post",
                    data: {data:data},
                    cache: false,
                    success: function (result) {
                        alert(result.msg);
                        window.location.reload();
                    }
                })
            };

            if(rABS) {
                reader.readAsArrayBuffer(f);
            } else {
                reader.readAsBinaryString(f);
            }
        }

        function fixdata(data) { //文件流转BinaryString
            var o = "",
                l = 0,
                w = 10240;
            for(; l < data.byteLength / w; ++l) o += String.fromCharCode.apply(null, new Uint8Array(data.slice(l * w, l * w + w)));
            o += String.fromCharCode.apply(null, new Uint8Array(data.slice(l * w)));
            return o;
        }
    </script>

@endsection('content')
