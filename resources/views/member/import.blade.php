@extends('layouts.base')

@section('content')
@section('title', trans('批量导入会员'))
<input type="file"onchange="importf(this)" />
<div id="demo">
    <table class="table">
        <tr>
            <td>111</td>
            <td>111</td>
            <td>111</td>
        </tr>

        <tr>
            <td>111</td>
            <td>111</td>
            <td>111</td>
        </tr>
    </table>
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
                url: "{!! yzWebUrl('member.member.member-excel') !!}",
                type: "post",
                data: {data:data},
                cache: false,
                success: function ($data) {
                    alert(111);
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
