@extends('layouts.base')
@section('title','批量发货')

@section('content')
<div class="page-heading">
    <h2>批量发货</h2>
</div>
<div class="alert alert-info">
    功能介绍: 使用excel快速导入进行订单发货, 文件格式<b style="color:red;">[xls]</b>
    <span style="padding-left: 60px;">如重复导入数据将以最新导入数据为准，请谨慎使用</span>
    <span style="padding-left: 60px;">数据导入订单状态自动修改为已发货</span>
    <span style="padding-left: 60px;">一次导入的数据不要太多,大量数据请分批导入,建议在服务器负载低的时候进行</span>
    <br>
    使用方法: <span style="padding-left: 60px;">1. 下载Excel模板文件并录入信息</span>
    <span style="padding-left: 60px;">2. 选择快递公司</span>
    <span style="padding-left: 60px;">3. 上传Excel导入</span>
    <br>
        格式要求：  Excel第一列必须为订单编号，第二列必须为快递单号，请确认订单编号与快递单号的备注

</div>

<form id="importform" class="form-horizontal form" action="" method="post" enctype="multipart/form-data">

    <div class='form-group'>
        <div class="form-group">
            <label class="col-sm-2 control-label must">快递公司</label>
            <div class="col-sm-5 goodsname"  style="padding-right:0;" >
                <select class="form-control" name="send[express_code]" id="express">
                    <option value="shunfeng" data-name="顺丰">顺丰</option>
                    <option value="shentong" data-name="申通">申通</option>
                    <option value="yunda" data-name="韵达快运">韵达快运</option>
                    <option value="tiantian" data-name="天天快递">天天快递</option>
                    <option value="yuantong" data-name="圆通速递">圆通速递</option>
                    <option value="zhongtong" data-name="中通速递">中通速递</option>
                    <option value="ems" data-name="ems快递">ems快递</option>
                    <option value="huitongkuaidi" data-name="汇通快运">汇通快运</option>
                    <option value="quanfengkuaidi" data-name="全峰快递">全峰快递</option>
                    <option value="jd" data-name="京东物流">京东物流</option>
                    <option value="guosong" data-name="国送快运">国送快运</option>
                    <option value="baishiwuliu" data-name="百世快运">百世快运</option>
                    <option value="htky" data-name="百世快递">百世快递</option>
                    <option value="zhaijisong" data-name="宅急送">宅急送</option>
                    <option value="aae" data-name="aae全球专递">aae全球专递</option>
                    <option value="anjie" data-name="安捷快递">安捷快递</option>
                    <option value="anxindakuaixi" data-name="安信达快递">安信达快递</option>
                    <option value="biaojikuaidi" data-name="彪记快递">彪记快递</option>
                    <option value="bht" data-name="bht">bht</option>
                    <option value="baifudongfang" data-name="百福东方国际物流">百福东方国际物流</option>
                    <option value="coe" data-name="中国东方（COE）">中国东方（COE）</option>
                    <option value="changyuwuliu" data-name="长宇物流">长宇物流</option>
                    <option value="datianwuliu" data-name="大田物流">大田物流</option>
                    <option value="debangwuliu" data-name="德邦物流">德邦物流</option>
                    <option value="dhl" data-name="dhl">dhl</option>
                    <option value="dpex" data-name="dpex">dpex</option>
                    <option value="dsukuaidi" data-name="d速快递">d速快递</option>
                    <option value="disifang" data-name="递四方">递四方</option>
                    <option value="fedex" data-name="fedex（国外）">fedex（国外）</option>
                    <option value="feikangda" data-name="飞康达物流">飞康达物流</option>
                    <option value="fenghuangkuaidi" data-name="凤凰快递">凤凰快递</option>
                    <option value="feikuaida" data-name="飞快达">飞快达</option>
                    <option value="guotongkuaidi" data-name="国通快递">国通快递</option>
                    <option value="ganzhongnengda" data-name="港中能达物流">港中能达物流</option>
                    <option value="guangdongyouzhengwuliu" data-name="广东邮政物流">广东邮政物流</option>
                    <option value="gongsuda" data-name="共速达">共速达</option>
                    <option value="hengluwuliu" data-name="恒路物流">恒路物流</option>
                    <option value="huaxialongwuliu" data-name="华夏龙物流">华夏龙物流</option>
                    <option value="haihongwangsong" data-name="海红">海红</option>
                    <option value="haiwaihuanqiu" data-name="海外环球">海外环球</option>
                    <option value="jiayiwuliu" data-name="佳怡物流">佳怡物流</option>
                    <option value="jinguangsudikuaijian" data-name="京广速递">京广速递</option>
                    <option value="jixianda" data-name="急先达">急先达</option>
                    <option value="jjwl" data-name="佳吉物流">佳吉物流</option>
                    <option value="jymwl" data-name="加运美物流">加运美物流</option>
                    <option value="jindawuliu" data-name="金大物流">金大物流</option>
                    <option value="jialidatong" data-name="嘉里大通">嘉里大通</option>
                    <option value="jykd" data-name="晋越快递">晋越快递</option>
                    <option value="kuaijiesudi" data-name="快捷速递">快捷速递</option>
                    <option value="lianb" data-name="联邦快递（国内）">联邦快递（国内）</option>
                    <option value="lianhaowuliu" data-name="联昊通物流">联昊通物流</option>
                    <option value="longbanwuliu" data-name="龙邦物流">龙邦物流</option>
                    <option value="lijisong" data-name="立即送">立即送</option>
                    <option value="lejiedi" data-name="乐捷递">乐捷递</option>
                    <option value="minghangkuaidi" data-name="民航快递">民航快递</option>
                    <option value="meiguokuaidi" data-name="美国快递">美国快递</option>
                    <option value="menduimen" data-name="门对门">门对门</option>
                    <option value="ocs" data-name="OCS">OCS</option>
                    <option value="peisihuoyunkuaidi" data-name="配思货运">配思货运</option>
                    <option value="quanchenkuaidi" data-name="全晨快递">全晨快递</option>
                    <option value="quanjitong" data-name="全际通物流">全际通物流</option>
                    <option value="quanritongkuaidi" data-name="全日通快递">全日通快递</option>
                    <option value="quanyikuaidi" data-name="全一快递">全一快递</option>
                    <option value="rufengda" data-name="如风达">如风达</option>
                    <option value="santaisudi" data-name="三态速递">三态速递</option>
                    <option value="shenghuiwuliu" data-name="盛辉物流">盛辉物流</option>
                    <option value="sue" data-name="速尔物流">速尔物流</option>
                    <option value="shengfeng" data-name="盛丰物流">盛丰物流</option>
                    <option value="saiaodi" data-name="赛澳递">赛澳递</option>
                    <option value="tiandihuayu" data-name="天地华宇">天地华宇</option>
                    <option value="tnt" data-name="tnt">tnt</option>
                    <option value="ups" data-name="ups">ups</option>
                    <option value="wanjiawuliu" data-name="万家物流">万家物流</option>
                    <option value="wenjiesudi" data-name="文捷航空速递">文捷航空速递</option>
                    <option value="wuyuan" data-name="伍圆">伍圆</option>
                    <option value="wxwl" data-name="万象物流">万象物流</option>
                    <option value="xinbangwuliu" data-name="新邦物流">新邦物流</option>
                    <option value="xinfengwuliu" data-name="信丰物流">信丰物流</option>
                    <option value="yafengsudi" data-name="亚风速递">亚风速递</option>
                    <option value="yibangwuliu" data-name="一邦速递">一邦速递</option>
                    <option value="youshuwuliu" data-name="优速物流">优速物流</option>
                    <option value="youzhengguonei" data-name="邮政包裹挂号信">邮政包裹挂号信</option>
                    <option value="youzhengguoji" data-name="邮政国际包裹挂号信">邮政国际包裹挂号信</option>
                    <option value="yuanchengwuliu" data-name="远成物流">远成物流</option>
                    <option value="yuanweifeng" data-name="源伟丰快递">源伟丰快递</option>
                    <option value="yuanzhijiecheng" data-name="元智捷诚快递">元智捷诚快递</option>
                    <option value="yuntongkuaidi" data-name="运通快递">运通快递</option>
                    <option value="yuefengwuliu" data-name="越丰物流">越丰物流</option>
                    <option value="yad" data-name="源安达">源安达</option>
                    <option value="yinjiesudi" data-name="银捷速递">银捷速递</option>
                    <option value="zhongtiekuaiyun" data-name="中铁快运">中铁快运</option>
                    <option value="zhongyouwuliu" data-name="中邮物流">中邮物流</option>
                    <option value="zhongxinda" data-name="忠信达">忠信达</option>
                    <option value="zhimakaimen" data-name="芝麻开门">芝麻开门</option>
                </select>
                <input type="hidden" name='send[express_company_name]' id='expresscom' value="顺丰"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label must">EXCEL</label>

            <div class="col-sm-5 goodsname"  style="padding-right:0;" >
                <input type="file" name="send[excelfile]" class="form-control" />
                <span class="help-block">如果遇到数据重复则将进行数据更新</span>
            </div>
        </div>

    </div>

    <div class='form-group'>
        <div class="col-sm-12">
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" name="cancelsend" value="yes">确认导入</button>

                <a class="btn btn-primary" href="{{yzWebUrl('order.batch-send.get-example')}}" style="margin-right: 10px;" ><i class="fa fa-download" title=""></i> 下载Excel模板文件</a>
            </div>
        </div>
    </div>
    </div>
</form>


<script language='javascript'>
    $("#express").change(function () {
        var sel = $(this).find("option:selected").text();
        // var sel = $(this).find("option:selected").attr("data-name");
        $("#expresscom").val(sel);
    });


            $('#express').select2();


</script>
@endsection('content')

