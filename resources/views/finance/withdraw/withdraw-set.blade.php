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
                            {{--余额提现--}}
                            余额提现

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

@endsection