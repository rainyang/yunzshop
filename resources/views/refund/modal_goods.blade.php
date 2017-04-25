@extends('refund.modal_base')
@section('operation_pass')

    @if($order['has_one_refund_apply']['status'] < 3)
        <label class='radio-inline'>
            <input type='radio' value='3' class="refund-action" data-action="{{yzWebUrl('refund.operation.pass')}}"
                   name='refund_status' @if( $order['has_one_refund_apply']['status']==3 ||
                                        $refund['status']==4) checked @endif>通过申请(需客户寄回商品)
        </label>
    @endif
@endsection
@section('operation_resend')

<label class='radio-inline'>
    <input type='radio' value='5' class="refund-action" name='refund_status'
           data-action="{{yzWebUrl('refund.operation.resend')}}"
           @if($order['has_one_refund_apply']['status'] < 5) checked @endif>
    确认发货 @if($order['has_one_refund_apply']['status'] < 3)(无需客户寄回商品，商家直接发换货商品)@endif
</label>

@if($order['has_one_refund_apply']['status'] < 5)
    <label class='radio-inline'>
        <input type='radio' value='10' class="refund-action"
               data-action="{{yzWebUrl('refund.operation.close')}}"
               name='refund_status'>关闭申请(换货完成)
    </label>
@endif
@endsection
