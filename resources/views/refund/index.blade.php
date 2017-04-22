<div class="panel panel-default">
    <div class="panel-heading">
        退款申请
    </div>
    <div class="panel-body">
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">退款状态 :</label>
            <div class="col-sm-9 col-xs-12">
                <p class="form-control-static">{{$order['has_one_refund_apply']['status_name']}}</p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">退款类型 :</label>
            <div class="col-sm-9 col-xs-12">
                <p class="form-control-static">{{$order['has_one_refund_apply']['refund_type_name']}}</p>
            </div>
        </div>

        @if ($order['has_one_refund_apply']['refund_way_type'] != 2)
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">退款金额 :</label>
                <div class="col-sm-9 col-xs-12">
                    <p class="form-control-static">{{$order['has_one_refund_apply']['price']}}</p>
                </div>
            </div>
        @endif

        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                @if ($order['has_one_refund_apply']['refund_way_type'] == 2)
                    换货
                @else
                    退款
                @endif
                原因 :
            </label>
            <div class="col-sm-9 col-xs-12">
                <p class="form-control-static">{{$order['has_one_refund_apply']['reason']}}</p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                @if ($order['has_one_refund_apply']['refund_way_type'] == 2)
                    换货
                @else
                    退款
                @endif
                说明 :
            </label>
            <div class="col-sm-9 col-xs-12">
                <p class="form-control-static">
                    {!! empty($order['has_one_refund_apply']['content'])?'无':$order['has_one_refund_apply']['content'] !!}</p>
            </div>
        </div>
        @if (!empty($order['has_one_refund_apply']['images']))
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">图片凭证 :</label>
                <div class="col-sm-9 col-xs-12">
                    <p class="form-control-static">
                        @foreach ($order['has_one_refund_apply']['images'] as $k1 => $v1)
                            <a target="_blank" href="{{tomedia($v1)}}"><img
                                        style='width:100px;;padding:1px;border:1px solid #ccc'
                                        src="{{tomedia($v1)}}"></a>
                        @endforeach
                    </p>
                </div>
            </div>
        @endif
        @if ($order['has_one_refund_apply']['status'] == 1)
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">退款时间 :</label>
                <div class="col-sm-9 col-xs-12">
                    <div class="form-control-static">
                        {{$order['has_one_refund_apply']['refund_time']}}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">到账时间 :</label>
                <div class="col-sm-9 col-xs-12">
                    <div class="form-control-static" style="color: red">3-15个工作日</div>
                </div>
            </div>
        @endif

        @if ($order['has_one_refund_apply']['status'] == 2)
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">退款操作 :</label>
                <div class="col-sm-9 col-xs-12">
                    <div class="form-control-static" style="color: red">请手动退款</div>
                </div>
            </div>
        @endif

        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
            <div class="col-sm-9 col-xs-12">
                @if ($order['has_one_refund_apply']['status'] == 0)
                    <a class="btn btn-danger btn-sm" href="javascript:;"
                       onclick="$('#modal-refund').find(':input[name=id]').val('{{$order['id']}}')"
                       data-toggle="modal"
                       data-target="#modal-refund">处理{{$order['has_one_refund_apply']['refund_type_name']}}申请</a>
                @elseif ($order['has_one_refund_apply']['status'] == -1 )
                    <span class='label label-default'>已拒绝</span>
                @elseif ($order['has_one_refund_apply']['status'] == -2)
                    <span class='label label-default'>客户取消</span>
                @elseif(in_array($order['has_one_refund_apply']['status'],[4,5]))
                    <span class='label label-danger'>已完成</span>
                @endif
            </div>
        </div>

        @if (!empty($refund['expresssn']))
            <div class="form-group">
                <div class="panel-heading" style="padding-left: 200px;">
                    <br>客户寄出快递信息
                </div>
            </div>

            @if ($refund['status'] == 3 || $refund['status'] == 4)
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否寄出快递 :</label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="form-control-static" style="color: #ef4f4f;">
                            @if ($refund['status'] == 3)
                                等待客户寄出快递
                            @elseif ($refund['status'] == 4)
                                客户已经寄出快递
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if (!empty($refund['expresscom']))
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">快递名称 :</label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="form-control-static">{{$refund['expresscom']}}</div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">快递单号 :</label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="form-control-static">{{$refund['expresssn']}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <button type='button' class='btn btn-default'
                                    onclick='refundexpress_find(this,"{{$order['id']}}",1)'>查看物流
                            </button>
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">填写快递单号时间 :</label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="form-control-static">{{$refund['sendtime']}}</div>
                    </div>
                </div>
            @endif

        @endif

        @if (!empty($refund['rexpresssn']))
            <div class="form-group">
                <div class="panel-heading" style="padding-left: 200px;">
                    <br>店家寄出快递信息
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">快递名称 :</label>
                <div class="col-sm-9 col-xs-12">
                    <div class="form-control-static">
                        @if (empty($refund['rexpresscom']))
                            其他快递
                        @else
                            {{$refund['rexpresscom']}}
                        @endif
                    </div>
                </div>
            </div>


            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">快递单号 :</label>
                <div class="col-sm-9 col-xs-12">
                    <div class="form-control-static">{{$refund['rexpresssn']}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <button type='button' class='btn btn-default'
                                onclick='refundexpress_find(this,"{{$order['id']}}",2)'>查看物流
                        </button>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">确认发货时间 :</label>
                <div class="col-sm-9 col-xs-12">
                    <div class="form-control-static">{{$refund['returntime']}}</div>
                </div>
            </div>
            <div style="width:100%; height:60px;"></div>
        @endif

    </div>
</div>