@extends('layouts.base')

@section('content')
    <script>
        $(function(){
            $("#myTab li.active>a").css("background","#ccc");
        })
        window.optionchanged = false;
        require(['bootstrap'], function () {
            $('#myTab a').click(function (e) {
                e.preventDefault();
                $(this).tab('show');
                $(this).css("background","#ccc").parent().siblings().children().css("background","none")
            })
        });
    </script>
 <style> .add-snav >li>a {height:46px!important}</style>
<link href="{{static_url('yunshop/balance/balance.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div class="main rightlist">
        <div class="right-titpos">
            <ul class="add-snav" id="myTab">
                <li class="active" ><a href="#tab_balance">余额提现</a></li>

                @foreach(Config::get('widget.withdraw') as $key=>$value)
                <li><a href="#{{$key}}">{{$value['title']}}</a></li>
                @endforeach

            </ul>
        </div>
        <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <div class="panel panel-default">


                <div class='panel-body'></div>

                <div class='panel-body'>

                    <div class="tab-content">
                        <div class="tab-pane  active" id="tab_balance">
                            {{--余额提现 start--}}
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">开启余额提现</label>
                                <div class="col-sm-9 col-xs-12">
                                    <label class='radio-inline'>
                                        <input type='radio' name='withdraw[balance][status]' value='1' @if($set['status'] == 1) checked @endif />
                                        开启
                                    </label>
                                    <label class='radio-inline'>
                                        <input type='radio' name='withdraw[balance][status]' value='0' @if($set['status'] == 0) checked @endif />
                                        关闭
                                    </label>
                                    <span class='help-block'>是否允许用户将余额提出</span>
                                </div>
                            </div>
                            <div id='withdraw' @if(empty($set['status']))style="display:none"@endif>
                                <div class="form-group">
                                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                    <div class="col-sm-9 col-xs-12">
                                        <div class="wechat" >
                                            <label class='radio-inline' style="padding-left:0px">提现到微信</label>
                                        </div>
                                        <div class="switch" >
                                            <label class='radio-inline'>
                                                <input type='radio' name='withdraw[balance][wechat]' value='1' @if($set['wechat'] == 1) checked @endif />
                                                开启
                                            </label>
                                            <label class='radio-inline'>
                                                <input type='radio' name='withdraw[balance][wechat]' value='0' @if($set['wechat'] == 0) checked @endif />
                                                关闭
                                            </label>

                                        </div>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                    <div class="col-sm-9 col-xs-12">
                                        <div class="alipay" >
                                            <label class='radio-inline' >提现到支付宝</label>
                                        </div>
                                        <div class="switch">
                                            <label class='radio-inline'>
                                                <input type='radio' name='withdraw[balance][alipay]' value='1' @if($set['alipay'] == 1) checked @endif />
                                                开启
                                            </label>
                                            <label class='radio-inline'>
                                                <input type='radio' name='withdraw[balance][alipay]' value='0' @if($set['alipay'] == 0) checked @endif />
                                                关闭
                                            </label>

                                        </div>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                    <div class="col-sm-9 col-xs-12">
                                        <div class="alipay" >
                                            <label class='radio-inline' >提现手续费</label>
                                        </div>
                                        <div class="cost" >
                                            <label class='radio-inline'>
                                                <input class="col-sm-6 form-control" type="text" name="withdraw[balance][poundage]" value="{{ $set['poundage'] or '' }}" placeholder="大于0小于1的两位小数"/>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                    <div class="col-sm-9 col-xs-12">
                                        <div class="alipay" >
                                            <label class='radio-inline' style="padding-left:0px">余额提现限制</label>
                                        </div>
                                        <div class="cost" >
                                            <label class='radio-inline'>
                                                <input class="col-sm-6 form-control"  type="text" name="withdraw[balance][withdrawmoney]" value="{{ $set['withdrawmoney'] or '' }}" placeholder="余额提现最小金额值"/>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{--余额提现 end--}}

                        </div>

                        @foreach(Config::get('widget.withdraw') as $key=>$value)
                            <div class="tab-pane" id="{{$key}}">{!! widget($value['class'])!!}</div>
                        @endforeach

                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="success-btn col-sm-9 col-xs-12">
                            <input type="submit" name="submit" value="提交" class="btn btn-success"/>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
    <script language="javascript">
        $(function () {
            $(":radio[name='withdraw[balance][status]']").click(function () {
                if ($(this).val() == 1) {
                    $("#withdraw").show();
                }
                else {
                    $("#withdraw").hide();
                }
            });
        })
    </script>

@endsection