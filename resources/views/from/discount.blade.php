@extends('layouts.base')
@section('title', '折扣设置')
@section('content')

    <div class="w1200 m0a">
        @include('layouts.tabs')
        <form action="{{ yzWebUrl('from.div-from.store') }}" method="post"
              class="form-horizontal form" enctype="multipart/form-data">

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">折扣类型</label>
                <div class="col-sm-6 col-xs-6">
                    <label class="radio-inline">
                        <input type="radio" name="widgets[discount][level_discount_type]" value="1" @if($discount[0]['level_discount_type']) checked @endif />
                        会员等级
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">折扣方式</label>
                <div class="col-sm-6 col-xs-6">
                    <div class='input-group'>
                        <label class="radio-inline">
                            <input type="radio" name="widgets[discount][discount_method]" value="1" @if($discount[0]['discount_method'] == '1') checked @endif /> 折扣
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="widgets[discount][discount_method]" value="2" @if($discount[0]['discount_method'] == '2') checked @endif />
                            固定金额
                        </label>
                    </div>
                </div>
            </div>

            <div id="ismember">
                @foreach ($levels as $level)
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-6 col-xs-6">
                            <div class='input-group col-md-6'>
                                <div class='input-group-addon'>{{  $level['level_name'] }}</div>

                                <input type='text' name='widgets[discount][discount_value][{{$level["id"] }}]'
                                       class="form-control discounts_value"
                                       value="@if (!empty($discountValue)){{ $discountValue[$level["id"]] }}@endif"/>
                                <div class='input-group-addon waytxt'> @if ( !empty($discount) && $discount[0]['discount_method'] == 1)
                                        折 @else 元 @endif</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9">
                        <input type="submit" name="submit" value="保存设置"
                               class="btn btn-primary col-lg-1" onclick='return formcheck()'/>
                    </div>
                </div>

            </div>
        </form>
    </div>

@endsection


<script language='javascript'>
    $('input[name="widgets[discount][level_discount_type]"]').click(function () {
        var discounttype = $('input:radio[name="widgets[discount][level_discount_type]"]:checked').val();
        if (discounttype == 1) {
            $('#ismember').show();
        } else {
            $('#ismember').hide();
        }
    });
    $('input[name="widgets[discount][discount_method]"]').click(function () {
        var discountway = $('input:radio[name="widgets[discount][discount_method]"]:checked').val();
        if (discountway == 1) {
            $('.waytxt').html('折');
        } else {
            $('.waytxt').html('元');
        }
    });


    $('.chkall').click(function () {
        var checked = $(this).get(0).checked;
        if (checked) {
            $(this).closest('div').find(':checkbox[class!="chkall"]').removeAttr('checked');
        }
    });
    $('.chksingle').click(function () {
        $(this).closest('div').find(':checkbox[class="chkall"]').removeAttr('checked');
    })

</script>

