@extends('layouts.base')

@section('content')


    <div class="w1200 m0a">
        <div class="rightlist">

            @include('layouts.tabs')

            <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
                <div class="panel panel-default">
                    <div class='panel-body'>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">订单支付流程</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio radio-inline">
                                    <input type="radio" name="order[paid_process]" value="1"
                                           @if ($set['paid_process']) checked @endif/>
                                    同步
                                </label>
                                <label class="radio radio-inline">
                                    <input type="radio" name="order[paid_process]" value="0"
                                           @if (empty($set['paid_process'])) checked @endif/>
                                    异步
                                </label>
                                <span class="help-block">
                                    获得推广资格的条件和分销商等级升级条件为同一个时，选择同步，否则选择异步（选择同步时会使订单付款变慢）
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">订单完成流程</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio radio-inline">
                                    <input type="radio" name="order[receive_process]" value="1"
                                           @if ($set['receive_process']) checked @endif/>
                                    同步
                                </label>
                                <label class="radio radio-inline">
                                    <input type="radio" name="order[receive_process]" value="0"
                                           @if (empty($set['receive_process'])) checked @endif/>
                                    异步
                                </label>
                                <span class="help-block">
                                    获得推广资格的条件和分销商等级升级条件为同一个时，选择同步，否则选择异步（选择同步时会使订单完成变慢）
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="submit" name="submit" value="提交" class="btn btn-success"
                                       onclick="return formcheck();"/>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
    <script type="text/javascript">
        function formcheck() {

            return true;

        }
    </script>
    @include('public.admin.mylink')
@endsection('content')
