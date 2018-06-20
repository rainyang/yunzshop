@extends('layouts.base')
@section('title', '支付记录详情')
@section('content')

    <div id="app-order-pay" style="padding-top: 1%">
        <el-badge :value="1" class="item">
            <el-popover
                    placement="right"
                    width="400"
                    trigger="click">
                <el-table :data="form.process">
                    <el-table-column width="150" property="name" label="标题"></el-table-column>
                    <el-table-column width="100" property="updated_at" label="更新时间"></el-table-column>
                    <el-table-column width="100" property="status_name" label="状态"></el-table-column>
                </el-table>
                <el-button slot="reference">处理中心</el-button>
            </el-popover>
        </el-badge>
        <el-form ref="form" :model="form" label-width="17%">
            <el-form-item label="id">
                [[form.id]]
            </el-form-item>
            <el-form-item label="支付单号">
                [[form.pay_sn]]
            </el-form-item>
            <el-form-item label="支付状态">
                [[form.status_name]]
            </el-form-item>
            <el-form-item label="支付方式">
                [[form.pay_type_name]]
            </el-form-item>

            <el-form-item label="金额">
                [[form.amount]]
            </el-form-item>
            <el-form-item label="创建时间">
                [[form.created_at]]
            </el-form-item>
            <el-form-item label="支付时间">
                [[form.pay_time]]
            </el-form-item>
            <el-form-item label="退款时间">
                [[form.refund_time]]
            </el-form-item>
        </el-form>


    </div>

@endsection('content')
@section('js')
    <script>
        var app = new Vue({
            el: '#app-order-pay',
            delimiters: ['[[', ']]'],
            data() {
                let orderPay = JSON.parse('{!! $orderPay !!}');

                return {
                    rules: {},
                    form: orderPay,

                }
            },
            mounted: function () {
            },
            methods: {}
        });
    </script>
@endsection('js')
