@extends('layouts.base')

@section('content')
    <div class="w1200 m0a">
        <div class="rightlist">
            <form id="dataform" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
                <div class="right-titpos">
                    <ul class="add-snav">
                        <li class="active">
                            <a href="#">
                                会员excel上传
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class='alert alert-danger'>尽量在服务器空闲时间来操作，会占用大量内存与带宽，在获取过程中，请不要进行任何操作!</div>
                        <div class="alert alert-info">
                            功能介绍：可将淘宝助理以及其他途径获取的淘宝商品CSV文件快速上传至商城,节约您的大量时间!
                            <span>使用方法： 1. 将您获取到的CSV文件转存为Excel格式,否则将无法识别</span>
                            <span style="padding-left: 74px;">2. 将配套的图片文件包压缩为Zip格式压缩包并且导入(图片需在压缩包根目录下)</span>
                            <span style="padding-left: 74px;">3. 确认上传即可</span>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label must">EXCEL</label>
                            <div class="col-sm-5"  style="padding-right:0;">
                                <input type="file"onchange="importf(this)" />
                            </div>
                        </div>
                </div>
                <div class="form-group">
                    <div class="modal-footer">
                        <input type="submit" value="确认导入" class="btn btn-primary"/>
                        <a href="{{$excel_url}}"><input type="button" value="Excel示例文件下载" class="btn btn-primary"/></a>
                        <a href="{{$zip_url}}"><input type="button" value="Zip示例文件下载" class="btn btn-primary"/></a>
                    </div>
                </div>
            </form>
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
            // console.log(data);
            $.ajax({
                url: "{!! yzWebUrl('member.member.member-excel') !!}",
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
