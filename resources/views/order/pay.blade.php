@extends('layouts.base')

@section('content')
<div>
    <a href="#" class="btn-do-it">支付</a>
</div>

    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" charset="utf-8">
        wx.config({{ $js->config(array('chooseWXPay')) }});
    </script>
    <script>
        $(function(){

            $(".btn-do-it").click(function(){
                wx.chooseWXPay({
                    timestamp: "{{$config['timestamp']}}", // 支付签名时间戳，注意微信jssdk中的所有使用timestamp字段均为小写。但最新版的支付后台生成签名使用的timeStamp字段名需大写其中的S字符
                    nonceStr: '{{$config['nonceStr']}}', // 支付签名随机串，不长于 32 位
                    package: '{{$config['package']}}', // 统一支付接口返回的prepay_id参数值，提交格式如：prepay_id=***）
                    signType: '{{$config['signType']}}', // 签名方式，默认为'SHA1'，使用新版支付需传入'MD5'
                    paySign: '{{$config['paySign']}}', // 支付签名
                    success: function (res) {
                        // 支付成功后的回调函数
                        if(res.err_msg == "get_brand_wcpay_request：ok" ) {
                            alert('支付成功。');
                            window.location.href="{{url("wechat/pay_ok")}}";
                        }else{
                            //alert(res.errMsg);
                            alert("支付失败，请返回重试。");
                        }
                    },
                    fail: function (res) {
                        alert("支付失败，请返回重试。");
                    }
                });
            });
        });
    </script>
@endsection

