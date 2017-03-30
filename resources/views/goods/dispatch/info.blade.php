@extends('layouts.base')

@section('content')

<div class="rightlist">
    <!-- 新增加右侧顶部三级菜单 -->
    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">配送方式设置</a></li>
        </ul>
    </div>
    <!-- 新增加右侧顶部三级菜单结束 -->
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" onsubmit='return formcheck()'>

    <div class="panel panel-default">
        <div class="panel-body">
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
                <div class="col-sm-9 col-xs-12">
                    <input type="text" name="dispatch[display_order]" class="form-control" value="{{ $dispatch->display_order }}" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>配送方式名称</label>
                <div class="col-sm-9 col-xs-12">
                    <input type="text" id='dispatchname' name="dispatch[dispatch_name]" class="form-control" value="{{   $dispatch->dispatch_name }}" />

                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否为默认快递模板</label>

                <div class="col-sm-9 col-xs-12">
                    <label class='radio-inline'>
                        <input type='radio' name='dispatch[is_default]' id="isdefault1" value='1' @if ( $dispatch->isdefault == 1 )checked @endif /> 是
                    </label>
                    <label class='radio-inline'>
                        <input type='radio' name='dispatch[is_default]' id="isdefault0" value='0' @if ( $dispatch->isdefault == 0 )checked @endif /> 否
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">计费方式</label>

                <div class="col-sm-9 col-xs-12">
                    <label class='radio-inline'>
                        <input type='radio' name='dispatch[calculate_type]' value='0' @if ( $dispatch->calculate_type == 0 )checked @endif /> 按重量计费
                    </label>
                    <label class='radio-inline'>
                        <input type='radio' name='dispatch[calculate_type]' value='1' @if ( $dispatch->calculate_type == 1 ) checked @endif /> 按件计费
                    </label>
                </div>
            </div>

            <!--<div class="form-group dispatch0" style='display:none'>
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">物流公司</label>
                <div class="col-sm-9 col-xs-12">
                    <input type='hidden' name='expressname' value='{$dispatch['expressname']}'/>
                    <select name='express' class="form-control input-medium">
                        <option value="" data-name="其他快递">其他快递</option>
                        <option value="shunfeng" data-name="顺丰">顺丰</option>
                        <option value="shentong" data-name="申通">申通</option>
                        <option value="yunda" data-name="韵达快运">韵达快运</option>
                        <option value="tiantian" data-name="天天快递">天天快递</option>
                        <option value="yuantong" data-name="圆通速递">圆通速递</option>
                        <option value="zhongtong" data-name="中通速递">中通速递</option>
                        <option value="ems" data-name="ems快递">ems快递</option>
                        <option value="huitongkuaidi" data-name="汇通快运">汇通快运</option>
                        <option value="quanfengkuaidi" data-name="全峰快递">全峰快递</option>
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
                    <span class="help-block">如果您选择了常用快递，则客户可以订单中查询快递信息，如果缺少您想要的快递，您可以联系我们! </span>
                </div>
            </div>-->

            <div class="form-group dispatch0" >
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">配送区域</label>
                <div class="col-sm-9 col-xs-12">

                    <table>
                        <thead>
                        <tr>
                            <th style="height:40px;width:400px;">运送到</th>
                            <th class="show_h" style="width:120px;">首重(克)</th>
                            <th class="show_h" style="width:120px;">首费(元)</th>
                            <th class="show_h" style="width:120px;">续重(克)</th>
                            <th class="show_h" style="width:120px;">续费(元)</th>


                            <th class="show_n" style="width:120px;">首件(个)</th>
                            <th class="show_n" style="width:120px;">运费(元)</th>
                            <th class="show_n" style="width:120px;">续件(个)</th>
                            <th class="show_n" style="width:120px;">续费(元)</th>
                            <th style="width:120px;">管理</th>
                        </tr>
                        </thead>
                        <tbody id='tbody-areas'>
                        <tr>
                            <input type="hidden" id="selectedareas" value="{{ $dispatch->areas }}" class="form-control" name="dispatch[areas]">
                            <td style="padding:10px;" id="areas">{{ $dispatch->areas }}</td>
                            <td class="show_h text-center">
                                <input type="number" value="{{ $dispatch->first_weight }}" class="form-control" name="dispatch[first_weight]" style="width:100px;"></td>
                            <td class="show_h text-center">
                                <input type="text" value="{{ $dispatch->first_weight_price }}" class="form-control" name="dispatch[first_weight_price]"  style="width:100px;"></td>
                            <td class="show_h text-center">
                                <input type="number" value="{{ $dispatch->another_weight }}" class="form-control" name="dispatch[another_weight]"  style="width:100px;"></td>
                            <td class="show_h text-center">
                                <input type="text" value="{{ $dispatch->another_weight_price }}" class="form-control" name="dispatch[another_weight_price]"  style="width:100px;"></td>
                            <td class="show_h"></td>

                            <td class="show_n text-center">
                                <input type="number" value="{{ $dispatch->first_piece }}" class="form-control" name="dispatch[first_piece]" style="width:100px;"></td>
                            <td class="show_n text-center">
                                <input type="text" value="{{ $dispatch->first_piece_price }}" class="form-control" name="dispatch[first_piece_price]"  style="width:100px;"></td>
                            <td class="show_n text-center">
                                <input type="number" value="{{ $dispatch->another_piece }}" class="form-control" name="dispatch[another_piece]"  style="width:100px;"></td>
                            <td class="show_n text-center">
                                <input type="text" value="{{ $dispatch->another_piece_price }}" class="form-control" name="dispatch[another_piece_price]"  style="width:100px;"></td>

                            <td class="show_n"></td>
                        </tr>
                        </tbody>
                    </table>
                    <a class='btn btn-default' href="javascript:;" onclick='selectAreas()'><span class="fa fa-plus"></span> 新增配送区域</a>
                    <span class='help-block show_h' @if ( $dispatch->dispatch_type == 1 ) style='display:block' @endif>根据重量来计算运费，当物品不足《首重重量》时，按照《首重费用》计算，超过部分按照《续重重量》和《续重费用》乘积来计算</span>
                    <span class='help-block show_n' @if ( $dispatch->dispatch_type == 0 ) style='display:block' @endif>根据件数来计算运费，当物品不足《首件数量》时，按照《首件费用》计算，超过部分按照《续件数量》和《续件费用》乘积来计算</span>

                </div>

            </div>
        </div>



        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否显示</label>
            <div class="col-sm-9 col-xs-12">
                <label class='radio-inline'>
                    <input type='radio' name='dispatch[enabled]' value='1' @if ( $dispatch->enabled == 1 ) checked @endif /> 是
                </label>
                <label class='radio-inline'>
                    <input type='radio' name='dispatch[enabled]' value='0' @if ( $dispatch->enabled == 0 ) checked @endif /> 否
                </label>
            </div>
        </div>
        <div class="form-group"></div>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
            <div class="col-sm-9 col-xs-12">
                <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" onclick="return formcheck()" />
                <input type="button" name="back" onclick='history.back()' style='margin-left:10px;'value="返回列表" class="btn btn-default col-lg-1" />
            </div>
        </div>


    </div>
</div>

</form>
</div>
<script>
    function show_type(flag){
        if (flag == 1) {
            $('.weight').css("display", "none");
            $('.fnum').css("display", "");
            $('.show_h').hide();
            $('.show_n').show();
        } else {
            $('.weight').css("display", "");
            $('.fnum').css("display", "none");
            $('.show_h').show();
            $('.show_n').hide();
        }
    }
    $(function(){
        show_type({{ $dispatch['calculate_type'] }});

        $(':radio[name="dispatch[calculate_type]"]').click(function(){
            var val = $(this).val();
            show_type(val);
        })
        $(':radio[name="dispatch[dispatch_type]"]').click(function(){
            var val = $(this).val();
            $(".dispatch0,.dispatch1").hide();
            $(".dispatch" + val ).show();
        })

    });
</script>
@include('area.selectprovinces')
@endsection