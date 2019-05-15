@extends('layouts.base')
@section('title','批量充值')

@section('content')
    <div class="page-heading">
        <h2>批量充值</h2>
    </div>
    <div class="alert alert-info alert-important">
        功能介绍:
        <span style="padding-left: 60px;">1. 使用excel快速导入进行会员充值（积分、余额）, 文件格式<b style="color:red;">[xls]</b></span>
        <span style="padding-left: 60px;">2. 一次导入的数据不要太多,大量数据请分批导入,建议在服务器负载低的时候进行</span>
        <br>
        使用方法: <span style="padding-left: 60px;">1. 下载Excel模板文件并录入信息</span>
        <span style="padding-left: 60px;">2. 选择充值类型</span>
        <span style="padding-left: 60px;">3. 上传Excel导入</span>
        <br>
        格式要求：  Excel第一列必须为会员ID，第二列必须为充值数量

    </div>

    <form id="importform" class="form-horizontal form" action="{{yzWebUrl('finance.batch-excel-recharge.confirm')}}" method="post" enctype="multipart/form-data">

        <div class='form-group'>
            <div class="form-group">
                <label class="col-sm-2 control-label must">充值类型</label>
                <div class="col-sm-5 goodsname"  style="padding-right:0;" >
                    <select class="form-control" name="batch_type">
                        <option value="balance" data-name="充值余额">充值余额</option>
                        <option value="point" data-name="充值积分">充值积分</option>
                        @if($love_open)
                        <option value="love" data-name="充值爱心值">充值爱心值</option>
                        @endif
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label must">EXCEL文件</label>

                <div class="col-sm-5 goodsname"  style="padding-right:0;" >
                    <input type="file" name="batch_recharge" class="form-control" />
                </div>
            </div>

        </div>

        <div class='form-group'>
            <div class="col-sm-12">
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" name="cancelsend" value="yes">确认导入</button>

                    <a class="btn btn-primary" href="{{yzWebUrl('finance.batch-excel-recharge.example')}}" style="margin-right: 10px;" ><i class="fa fa-download" title=""></i> 下载Excel模板文件</a>
                </div>
            </div>
        </div>
        </div>
    </form>
@endsection('content')
