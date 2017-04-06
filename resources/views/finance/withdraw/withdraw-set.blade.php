@extends('layouts.base')

@section('content')
    <script>
        window.optionchanged = false;
        require(['bootstrap'], function () {
            $('#myTab a').click(function (e) {
                e.preventDefault();
                $(this).tab('show');
            })
        });
    </script>

    <div class="main rightlist">
        <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <div class="panel panel-default panel-center">

                <div class="">
                    <ul class="add-shopnav" id="myTab">
                        <li class="active" ><a href="#tab_balance">余额提现</a></li>

                            @foreach(Config::get('widget.withdraw') as $key=>$value)
                                <li><a href="#{{$key}}">{{$value['title']}}</a></li>
                            @endforeach

                    </ul>
                </div>
                <div class='panel-body'></div>

                <div class='panel-body'>

                    <div class="tab-content">
                        <div class="tab-pane  active" id="tab_balance">
                            {{--余额提现 start--}}
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">开启余额提现</label>
                                <div class="col-sm-9 col-xs-12">
                                    <label class='radio-inline'>
                                        <input type='radio' name='balance[withdraw][status]' value='1' @if($balance['withdraw']['status'] == 1) checked @endif />
                                        开启
                                    </label>
                                    <label class='radio-inline'>
                                        <input type='radio' name='balance[withdraw][status]' value='0' @if($balance['withdraw']['status'] == 0) checked @endif />
                                        关闭
                                    </label>
                                    <span class='help-block'>是否允许用户将余额提出</span>
                                </div>
                            </div>
                            <div id='withdraw' @if(empty($balance['withdraw']['status']))style="display:none"@endif>
                                <div class="form-group">
                                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                    <div class="col-sm-9 col-xs-12">
                                        <div style="float:left; width:15%; height:30px;">
                                            <label class='radio-inline' style="padding-left:0px">提现手续费</label>
                                        </div>
                                        <div style="float:left; width:85%; height:30px;">
                                            <label class='radio-inline' style="width:70%;">
                                                <input class="col-sm-6" style="width:100%;" type="text" name="balance[withdraw][poundage]" value=" "/>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                    <div class="col-sm-9 col-xs-12">
                                        <div style="float:left; width:15%; height:30px;">
                                            <label class='radio-inline' style="padding-left:0px">余额提现限制</label>
                                        </div>
                                        <div style="float:left; width:85%; height:30px;">
                                            <label class='radio-inline' style="width:70%;">
                                                <input class="col-sm-6" style="width:100%;" type="text" name="balance[withdraw][withdrawmoney]" value=" "/>
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
                        <div class="col-sm-9 col-xs-12">
                            <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1"/>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
    <script language="javascript">
        $(function () {
            $(":radio[name='balance[withdraw][status]']").click(function () {
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