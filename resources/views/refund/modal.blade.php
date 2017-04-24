@if($order['has_one_refund_apply']['refund_type'] == 0)
    @include('refund.modal_money')
@elseif($order['has_one_refund_apply']['refund_type'] == 1)
    @include('refund.modal_return')
@elseif($order['has_one_refund_apply']['refund_type'] == 2)
    @include('refund.modal_goods')
@endif