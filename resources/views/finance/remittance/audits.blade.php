@extends('layouts.base')
@section('title', '转账审核列表')
@section('content')
    <div id="app-remittance-audits" xmlns:v-bind="http://www.w3.org/1999/xhtml">
        <ToolBar>
        </ToolBar>
        <div style="float: right">
            <el-input
                    placeholder="搜索"
                    size="small"
                    style="width: 140px"
                    v-model="searchParams.keywords"
                    clearable>
            </el-input>


            <el-select v-model="searchParams.status_id" size="small" clearable placeholder="请选择状态"
                       style="width: 120px">
                <el-option
                        v-for="(v,k) in all_status"
                        :key="k"
                        :label="v"
                        :value="k">
                </el-option>
            </el-select>
            <el-button type="success" icon="el-icon-search" size="small" @click="refresh = !refresh"></el-button>
        </div>
        <el-table
                :data="list"
                style="width: 100%"
                :row-class-name="tableRowClassName">
            <el-table-column
                    align="center"
                    label="支付单号">
                <template slot-scope="scope">
                    <a v-bind:href="'{{ yzWebUrl('orderPay.detail', array('order_pay_id' => '')) }}'+[[scope.row.remittance_record.order_pay.id]]"
                       target="_blank">[[scope.row.remittance_record.order_pay.pay_sn]]</a>
                </template>
            </el-table-column>

            <el-table-column
                    align="center"
                    prop="remittance_record.order_pay.amount"
                    label="金额">
            </el-table-column>
            <el-table-column
                    align="center"
                    prop="member.nickname"
                    label="用户">
                <template slot-scope="scope">
                    <a v-bind:href="'{{ yzWebUrl('member.member.detail', array('id' => '')) }}'+[[scope.row.remittance_record.order_pay.uid]]"
                       target="_blank"><img v-bind:src="scope.row.member.avatar_image"
                                            style='width:30px;height:30px;padding:1px;border:1px solid #ccc'><br/>[[scope.row.member.nickname]]</a>
                </template>
            </el-table-column>

            <el-table-column
                    align="center"
                    prop="status_name"
                    label="状态">
            </el-table-column>

            <el-table-column
                    align="center"
                    prop="created_at"
                    label="创建时间">
            </el-table-column>
            <el-table-column
                    align="center"
                    fixed="right"
                    label="操作"
                    width="100">
                <template slot-scope="scope">
                    <a v-bind:href="'{{ yzWebUrl('remittanceAudit.detail', array('id' => '')) }}'+[[scope.row.id]]"
                       target="_blank">查看</a>

                </template>
            </el-table-column>
        </el-table>

    </div>
    <style>
        .el-table .warning-row {
            background: oldlace;
        }

        .el-table .success-row {
            background: #f0f9eb;
        }
    </style>
@endsection('content')
@section('js')
    <script>
        var app = new Vue({
            el: '#app-remittance-audits',
            delimiters: ['[[', ']]'],

            data() {
                let data = JSON.parse('{!! $data !!}');
                console.log(remittanceAudits);
                return {
                    list: data.remittanceAudits,
                    searchParams:{
                        ...data.searchParams,
                        "keywords":"",
                        "status_id":""
                    }
                }
            },
            mounted: function () {
            },
            methods: {
                tableRowClassName({row, rowIndex}) {
                    if (row.state == 'completed') {
                        return 'success-row';
                    } else if (row.state == 'closed') {
                        return 'warning-row';

                    }
                    return '';
                }
            }
        });
    </script>
@endsection('js')
