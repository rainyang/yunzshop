@extends('layouts.base')

@section('content')
@section('title', trans('添加退货地址'))
<div class="w1200 m0a">
<div class="main rightlist">
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" onsubmit='return formcheck()'>
    <input type="hidden" name="id" value="{{$dispatch['id']}}" />

    <div class="panel panel-default">
        <div class="panel-body">
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>配送方式名称</label>
                <div class="col-sm-9 col-xs-12">
                    <input type="text" id='dispatchname' name="dispatchname" class="form-control" value="{{$dispatch['dispatchname']}}" />
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>配送方式名称</label>
                <div class="col-sm-9 col-xs-12">
                    <input type="text" id='dispatchname' name="dispatchname" class="form-control" value="{{$dispatch['dispatchname']}}" />
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">地址</label>
                <div class="col-xs-6">
                    {!! app\common\helpers\AddressHelper::tplLinkedAddress(['agent[province_id]','agent[city_id]','agent[district_id]','agent[street_id]'], [])!!}
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否为默认快递模板</label>

                <div class="col-sm-9 col-xs-12">
                    <label class='radio-inline'>
                        <input type='radio' name='isdefault' id="isdefault1" value='1' @if ($dispatch['isdefault'] == 1) checked @endif /> 是
                    </label>
                    <label class='radio-inline'>
                        <input type='radio' name='isdefault' id="isdefault0" value='0' @if ($dispatch['isdefault'] == 0) checked @endif /> 否
                    </label>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
            <div class="col-sm-9 col-xs-12">
                <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" onclick="return formcheck()" />
                <input type="hidden" name="token" value="{{$var['token']}}" />
                <input type="button" name="back" onclick='history.back()' style='margin-left:10px;' value="返回列表" class="btn btn-default col-lg-1" />
            </div>
        </div>
    </div>
    </form>
</div>
</div>
<style type='text/css'>
    .province { float:left; position:relative;width:150px; height:35px; line-height:35px;border:1px solid #fff;}
    .province:hover { border:1px solid #f7e4a5;border-bottom:1px solid #fffec6; background:#fffec6;}
    .province .cityall { margin-top:10px;}
    .province ul { list-style: outside none none;position:absolute;padding:0;background:#fffec6;border:1px solid #f7e4a5;display:none;
        width:auto; width:300px; z-index:999999;left:-1px;top:32px;}
    .province ul li  { float:left;min-width:60px;margin-left:20px; height:30px;line-height:30px; }
</style>
<div id="modal-areas"  class="modal fade" tabindex="-1">
    <div class="modal-dialog" style='width: 920px;'>
        <div class="modal-content">
            <div class="modal-header"><button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button><h3>选择区域</h3></div>
            <div class="modal-body" style='height:280px;;' >

                @foreach($areas['address']['province'] as $value)
                    @if ($value['@attributes']['name'] == '请选择省份')
                        @continue
                    @endif
                    <div class='province'>
                        <label class='checkbox-inline' style='margin-left:20px;'>
                            <input type='checkbox' class='cityall' /> {{$value['@attributes']['name']}}
                            <span class="citycount" style='color:#ff6600'></span>
                        </label>

                        @if (!empty($value['city']['0']))
                        <ul>
                            @foreach ($value['city'] as $c)
                            <li>
                                <label class='checkbox-inline'>
                                    <input type='checkbox' class='city' style='margin-top:8px;' city="{{$c['@attributes']['name']}}" /> {{$c['@attributes']['name']}}
                                </label>
                            </li>
                            @endforeach
                        </ul>
                        @else
                        <ul>
                            <li>
                                <label class='checkbox-inline'>
                                    <input type='checkbox' class='city' style='margin-top:8px;' city="{{$value['city']['@attributes']['name']}}" /> {{$value['city']['@attributes']['name']}}
                                </label>
                            </li>
                        </ul>
                        @endif
                    </div>
                @endforeach

            </div>
            <div class="modal-footer">
                <a href="javascript:;" id='btnSubmitArea' class="btn btn-primary" data-dismiss="modal" aria-hidden="true">确定</a>
                <a href="javascript:;" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a>
            </div>
        </div>
    </div>
</div>
</div>
<script language='javascript'>
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
        show_type({{$dispatch['calculatetype']}});

        $(':radio[name=calculatetype]').click(function(){
            var val = $(this).val();
            show_type(val);
        })
        $(':radio[name=dispatchtype]').click(function(){
            var val = $(this).val();
            $(".dispatch0,.dispatch1").hide();
            $(".dispatch" + val ).show();
        })

        $("select[name=express]").change(function(){
            var obj = $(this);
            var sel = obj.find("option:selected");
            $(":input[name=expressname]").val(sel.data("name"));
        });

        $('.province').mouseover(function(){
            $(this).find('ul').show();
        }).mouseout(function(){
            $(this).find('ul').hide();
        });

        $('.cityall').click(function(){
            var checked = $(this).get(0).checked;
            var citys = $(this).parent().parent().find('.city');
            citys.each(function(){
                $(this).get(0).checked = checked;
            });
            var count = 0;
            if(checked){
                count =  $(this).parent().parent().find('.city:checked').length;
            }
            if(count>0){
                $(this).next().html("(" + count + ")")    ;
            }
            else{
                $(this).next().html("");
            }
        });
        $('.city').click(function(){
            var checked = $(this).get(0).checked;
            var cityall = $(this).parent().parent().parent().parent().find('.cityall');

            if(checked){
                cityall.get(0).checked = true;
            }
            var count = cityall.parent().parent().find('.city:checked').length;
            if(count>0){
                cityall.next().html("(" + count + ")")    ;
            }
            else{
                cityall.next().html("");
            }
        });

    });
    function getCurrents(withOutRandom){
        var citys = "";
        $('.citys').each(function(){
            var crandom = $(this).prev().val();
            if(withOutRandom && crandom==withOutRandom){
                return true;
            }
            citys+=$(this).val();
        });
        return citys;
    }
    var current = '';
    function addArea(btn){
        $(btn).button('loading');
        $.ajax({
            url:"{php echo $this->createWebUrl('shop/dispatch',array('op'=>'tpl'))}",
            dataType:'json',
            success:function(json){
                $(btn).button('reset');
                current = json.random;

                $('#tbody-areas').append(json.html);
                $('#tbody-areas tr').last().hide();
                clearSelects();

                $("#modal-areas").modal();
                var currents = getCurrents();
                currents = currents.split(';');
                var citystrs = "";
                $('.city').each(function(){
                    var parentdisabled =false;
                    for(var i in currents){
                        if(currents[i]!='' && currents[i]==$(this).attr('city')){
                            $(this).attr('disabled',true);
                            $(this).parent().parent().parent().parent().find('.cityall').attr('disabled',true);
                        }
                    }

                });
                $('#btnSubmitArea').unbind('click').click(function(){
                    $('.city:checked').each(function(){
                        citystrs+= $(this).attr('city') +";";
                    });
                    $('.' + current + ' .cityshtml').html(citystrs);
                    $('.' + current + ' .citys').val(citystrs);
                    $('#tbody-areas tr').last().show();
                })
                var calculatetype1 = $('input[name="calculatetype"]:checked ').val();
                show_type(calculatetype1);
            }
        })
    }
    function clearSelects(){
        $('.city').attr('checked',false).removeAttr('disabled');
        $('.cityall').attr('checked',false).removeAttr('disabled');
        $('.citycount').html('');
    }
    function editArea(btn){
        current = $(btn).attr('random');
        clearSelects();
        var old_citys = $(btn).prev().val().split(';');

        $('.city').each(function(){
            var parentcheck = false;
            for(var i in old_citys){
                if(old_citys[i]==$(this).attr('city')){
                    parentcheck = true;
                    $(this).get(0).checked = true;
                    break;
                }
            }
            if(parentcheck){
                $(this).parent().parent().parent().parent().find('.cityall').get(0).checked=  true;
            }
        });

        $("#modal-areas").modal();
        var citystrs = '';
        $('#btnSubmitArea').unbind('click').click(function(){
            $('.city:checked').each(function(){
                citystrs+= $(this).attr('city') +";";
            });
            $('.' + current + ' .cityshtml').html(citystrs);
            $('.' + current + ' .citys').val(citystrs);


        })
        var currents = getCurrents(current);
        currents = currents.split(';');
        var citys = "";
        $('.city').each(function(){
            var parentdisabled =false;
            for(var i in currents){
                if(currents[i]!='' && currents[i]==$(this).attr('city')){
                    $(this).attr('disabled',true);
                    $(this).parent().parent().parent().parent().find('.cityall').attr('disabled',true);
                }
            }

        });
    }
    function formcheck() {
        if ($("#dispatchname").isEmpty()) {
            Tip.focus("dispatchname", "请填写配送方式名称!", "top");
            return false;
        }
        return true;
    }
</script>
@endsection